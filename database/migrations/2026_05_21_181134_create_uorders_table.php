<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('uorders', function (Blueprint $table) {
            $table->id('id');
            $table->integer('userId')->nullable();
            $table->integer('productId')->nullable();
            $table->integer('quantity')->nullable();
            $table->decimal('cprice', 10,2)->nullable();
            $table->integer('hsnsan');
            $table->decimal('srate', 10,2)->nullable();
            $table->integer('gst');
            $table->string('unit');
            $table->integer('orderid');
            $table->decimal('price', 10,2)->default(0.00);
            $table->string('slno')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('uorders');
    }
};