<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        /**
         * 1) USERS – bổ sung cột SSO nếu thiếu (KHÔNG dùng change() để tránh lỗi SQLite)
         */
        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $t) {
                if (!Schema::hasColumn('users', 'facebook_user_id')) {
                    $t->string('facebook_user_id')->nullable()->unique()->after('id');
                }
                if (!Schema::hasColumn('users', 'avatar')) {
                    $t->string('avatar')->nullable()->after('email');
                }
                if (!Schema::hasColumn('users', 'facebook_token')) {
                    $t->text('facebook_token')->nullable()->after('avatar');
                }
                // GHI CHÚ:
                // - Cột password: giữ nguyên (nếu đang NOT NULL). Controller đã set password ngẫu nhiên khi SSO.
                // - Khi chuyển MySQL: sẽ có migration riêng để đổi password -> nullable (dùng doctrine/dbal).
            });
        }

        /**
         * 2) PAGES – tenant gốc (tạo nếu chưa có)
         */
        if (!Schema::hasTable('pages')) {
            Schema::create('pages', function (Blueprint $t) {
                $t->id();
                $t->string('meta_page_id')->unique();
                $t->string('name');
                $t->json('meta')->nullable();
                $t->timestamps();
            });
        }

        /**
         * 3) PAGE_USER – RBAC theo Page (tạo nếu chưa có)
         */
        if (!Schema::hasTable('page_user')) {
            Schema::create('page_user', function (Blueprint $t) {
                $t->unsignedBigInteger('page_id');
                $t->unsignedBigInteger('user_id');
                $t->string('role');
                $t->timestamps();

                $t->primary(['page_id', 'user_id']);
                $t->index(['user_id', 'role']);
                // Có thể thêm FK khi dùng MySQL/SQLite FK enabled:
                // $t->foreign('page_id')->references('id')->on('pages')->onDelete('cascade');
                // $t->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            });
        }

        /**
         * 4) PAGE_TOKENS – lưu token theo Page (tạo nếu chưa có)
         *    Dùng string cho 'status' để tương thích SQLite (tránh enum).
         */
        if (!Schema::hasTable('page_tokens')) {
            Schema::create('page_tokens', function (Blueprint $t) {
                $t->id();
                $t->unsignedBigInteger('page_id')->index();
                $t->longText('access_token');
                $t->json('scopes')->nullable();
                $t->unsignedBigInteger('issued_by_user_id')->nullable();
                $t->string('status')->default('active'); // active|invalid|expired
                $t->timestamp('expires_at')->nullable();
                $t->timestamps();

                // FK (bật sau nếu cần):
                // $t->foreign('page_id')->references('id')->on('pages')->onDelete('cascade');
            });
        }

        /**
         * 5) SETTINGS – config-driven (tạo nếu chưa có)
         */
        if (!Schema::hasTable('settings')) {
            Schema::create('settings', function (Blueprint $t) {
                $t->id();
                $t->string('key')->index();
                $t->json('value_json');
                $t->string('type', 20);       // boolean|number|json|duration|enum...
                $t->string('scope_type', 20); // global|plan|workspace|page|user
                $t->unsignedBigInteger('scope_id')->nullable();
                $t->unsignedInteger('version')->default(1);
                $t->boolean('is_active')->default(true);
                $t->unsignedBigInteger('created_by')->nullable();
                $t->timestamps();
                $t->index(['scope_type', 'scope_id', 'key', 'is_active']);
            });
        }
    }

    public function down(): void
    {
        // Giữ schema khi rollback để tránh mất dữ liệu dev.
        // Nếu thật sự cần rollback, có thể drop theo nhu cầu:
        // Schema::dropIfExists('settings');
        // Schema::dropIfExists('page_tokens');
        // Schema::dropIfExists('page_user');
        // Schema::dropIfExists('pages');

        // Với cột trên users, không rollback để an toàn dữ liệu.
    }
};
