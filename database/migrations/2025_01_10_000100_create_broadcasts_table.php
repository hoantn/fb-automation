<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('broadcasts')) {
            Schema::create('broadcasts', function (Blueprint $t) {
                $t->id();
                $t->unsignedBigInteger('page_id')->index();
                $t->text('content');
                $t->unsignedInteger('total')->default(0);
                $t->unsignedInteger('sent')->default(0);
                $t->unsignedInteger('failed')->default(0);
                $t->string('status', 20)->default('queued');
                $t->timestamps();
            });
        }
    }
    public function down(): void { Schema::dropIfExists('broadcasts'); }
};
