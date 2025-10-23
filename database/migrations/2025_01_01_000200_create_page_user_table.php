<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('page_user', function (Blueprint $t) {
            $t->unsignedBigInteger('page_id');
            $t->unsignedBigInteger('user_id');
            $t->string('role');
            $t->timestamps();
            $t->primary(['page_id','user_id']);
            $t->index(['user_id','role']);
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('page_user');
    }
};
