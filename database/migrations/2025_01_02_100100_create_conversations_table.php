<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('conversations')) {
            Schema::create('conversations', function (Blueprint $t) {
                $t->id();
                $t->unsignedBigInteger('page_id')->index();
                $t->unsignedBigInteger('customer_id')->index();
                $t->timestamp('last_message_at')->nullable();
                $t->timestamps();
                $t->unique(['page_id','customer_id']);
            });
        }
    }
    public function down(): void { Schema::dropIfExists('conversations'); }
};
