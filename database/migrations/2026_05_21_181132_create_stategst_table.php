<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stategst', function (Blueprint $table) {
            $table->integer('sid');
            $table->string('sname');
            $table->integer('scode');
            $table->integer('status')->default(0);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stategst');
    }
};