<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('customers')) {
            Schema::create('customers', function (Blueprint $t) {
                $t->id();
                $t->unsignedBigInteger('page_id')->index();
                $t->string('psid')->index();
                $t->string('name')->nullable();
                $t->string('avatar')->nullable();
                $t->timestamp('last_interaction_at')->nullable();
                $t->json('meta')->nullable();
                $t->timestamps();
                $t->unique(['page_id','psid']);
            });
        }
    }
    public function down(): void { Schema::dropIfExists('customers'); }
};
