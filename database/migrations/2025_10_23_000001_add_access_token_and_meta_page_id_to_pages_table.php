<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pages', function (Blueprint $table) {
            if (!Schema::hasColumn('pages', 'meta_page_id')) {
                $table->string('meta_page_id')->nullable()->index();
            }
            if (!Schema::hasColumn('pages', 'access_token')) {
                $table->text('access_token')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('pages', function (Blueprint $table) {
            if (Schema::hasColumn('pages', 'access_token')) {
                $table->dropColumn('access_token');
            }
            if (Schema::hasColumn('pages', 'meta_page_id')) {
                $table->dropColumn('meta_page_id');
            }
        });
    }
};
