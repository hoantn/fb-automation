<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('messages')) {
            Schema::create('messages', function (Blueprint $t) {
                $t->id();
                $t->unsignedBigInteger('page_id')->index();
                $t->unsignedBigInteger('customer_id')->index();
                $t->string('direction', 10)->index(); // in|out
                $t->string('mid')->nullable()->index();
                $t->text('text')->nullable();
                $t->json('attachments')->nullable();
                $t->string('status', 20)->nullable();
                $t->timestamp('sent_at')->nullable();
                $t->json('raw')->nullable();
                $t->timestamps();
            });
        }
    }
    public function down(): void { Schema::dropIfExists('messages'); }
};
