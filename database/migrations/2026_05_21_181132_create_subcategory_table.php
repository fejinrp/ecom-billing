<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subcategory', function (Blueprint $table) {
            $table->id('id');
            $table->integer('catid');
            $table->string('subcategoryname')->nullable();
            $table->string('creationdate')->nullable();
            $table->integer('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subcategory');
    }
};