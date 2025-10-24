<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('page_user')) {
            Schema::create('page_user', function (Blueprint $table) {
                $table->id();
                $table->foreignId('page_id')->constrained('pages')->cascadeOnDelete();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->string('role')->default('owner');
                $table->timestamps();
                $table->unique(['page_id', 'user_id']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('page_user');
    }
};
