<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $t) {
            $t->id();
            $t->string('key')->index();
            $t->json('value_json');
            $t->string('type', 20);
            $t->string('scope_type', 20);
            $t->unsignedBigInteger('scope_id')->nullable();
            $t->unsignedInteger('version')->default(1);
            $t->boolean('is_active')->default(true);
            $t->unsignedBigInteger('created_by')->nullable();
            $t->timestamps();
            $t->index(['scope_type','scope_id','key','is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
