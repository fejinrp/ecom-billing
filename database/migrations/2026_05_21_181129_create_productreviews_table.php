<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('productreviews', function (Blueprint $table) {
            $table->id('id');
            $table->integer('productId')->nullable();
            $table->integer('quality')->nullable();
            $table->integer('price')->nullable();
            $table->integer('value')->nullable();
            $table->string('name')->nullable();
            $table->string('summary')->nullable();
            $table->longText('review')->nullable();
            $table->timestamp('reviewDate')->default('current_timestamp');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('productreviews');
    }
};