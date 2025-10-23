<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('page_tokens', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('page_id')->index();
            $t->longText('access_token');
            $t->json('scopes')->nullable();
            $t->unsignedBigInteger('issued_by_user_id')->nullable();
            $t->enum('status', ['active','invalid','expired'])->default('active');
            $t->timestamp('expires_at')->nullable();
            $t->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('page_tokens');
    }
};
