<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ausers', function (Blueprint $table) {
            $table->id('user_id');
            $table->string('username');
            $table->string('password');
            $table->string('email');
            $table->string('mobile')->nullable();
            $table->integer('section');
            $table->integer('ustatus');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ausers');
    }
};