<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasColumn('settings', 'value')) {
            Schema::table('settings', function (Blueprint $t) {
                $t->text('value')->nullable(); // không bắt buộc
            });
        }
        // Không đụng value_json (để nguyên constraint hiện có)
    }
    public function down(): void {}
};
