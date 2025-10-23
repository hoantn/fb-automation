<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Với SQLite, chỉ cần ADD COLUMN (nếu chưa tồn tại).
        if (!Schema::hasColumn('settings', 'value')) {
            Schema::table('settings', function (Blueprint $t) {
                $t->text('value')->nullable();
            });
        }
    }

    public function down(): void
    {
        // SQLite không drop column được dễ; để trống cho an toàn
        // (nếu cần rollback thật, tạo bảng tạm & copy – không cần thiết ở môi trường dev).
    }
};
