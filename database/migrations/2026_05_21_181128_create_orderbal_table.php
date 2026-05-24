<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orderbal', function (Blueprint $table) {
            $table->id('order_idbal');
            $table->integer('order_id');
            $table->decimal('gtotal', 10,2);
            $table->decimal('pamount', 10,2);
            $table->decimal('bamount', 10,2);
            $table->date('pdate');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orderbal');
    }
};