<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('settings')) {
            Schema::create('settings', function (Blueprint $table) {
                $table->id();
                $table->string('key')->unique();
                $table->string('type')->nullable();
                $table->text('value')->nullable();
                if (DB::getDriverName() === 'sqlite') {
                    $table->text('value_json')->nullable();
                } else {
                    $table->json('value_json')->nullable();
                }
                $table->timestamps();
            });
            return;
        }

        $columns = Schema::getColumnListing('settings');
        sort($columns);
        $target = ['created_at','id','key','type','updated_at','value','value_json'];
        sort($target);
        $needRebuild = ($columns !== $target);

        if ($needRebuild) {
            Schema::dropIfExists('settings_new');
            Schema::create('settings_new', function (Blueprint $table) {
                $table->id();
                $table->string('key')->unique();
                $table->string('type')->nullable();
                $table->text('value')->nullable();
                if (DB::getDriverName() === 'sqlite') {
                    $table->text('value_json')->nullable();
                } else {
                    $table->json('value_json')->nullable();
                }
                $table->timestamps();
            });

            $oldCols = Schema::getColumnListing('settings');
            $keyCol   = in_array('key', $oldCols)   ? 'key'   : (in_array('setting_key', $oldCols) ? 'setting_key' : null);
            $valCol   = in_array('value', $oldCols) ? 'value' : null;
            $typeCol  = in_array('type', $oldCols)  ? 'type'  : (in_array('scope_type', $oldCols) ? 'scope_type' : null);
            $jsonCol  = in_array('value_json', $oldCols) ? 'value_json' : null;

            $rows = DB::table('settings')->get();
            foreach ($rows as $r) {
                $payload = [
                    'key'        => $keyCol ? ($r->{$keyCol} ?? null) : null,
                    'type'       => $typeCol ? ($r->{$typeCol} ?? null) : null,
                    'value'      => $valCol ? ($r->{$valCol} ?? null) : null,
                    'value_json' => $jsonCol ? ($r->{$jsonCol} ?? null) : null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
                if ($payload['key']) {
                    DB::table('settings_new')->updateOrInsert(['key' => $payload['key']], $payload);
                }
            }

            Schema::dropIfExists('settings_backup');
            Schema::rename('settings', 'settings_backup');
            Schema::rename('settings_new', 'settings');
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
        if (Schema::hasTable('settings_backup')) {
            Schema::rename('settings_backup', 'settings');
        }
    }
};
