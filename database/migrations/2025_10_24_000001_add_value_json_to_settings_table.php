<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('settings') && !Schema::hasColumn('settings', 'value_json')) {
            Schema::table('settings', function (Blueprint $table) {
                $table->text('value_json')->nullable()->after('key');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('settings') && Schema::hasColumn('settings', 'value_json')) {
            Schema::table('settings', function (Blueprint $table) {
                $table->dropColumn('value_json');
            });
        }
    }
};
