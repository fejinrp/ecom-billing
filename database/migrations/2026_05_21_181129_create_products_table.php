<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id('id');
            $table->integer('brandid');
            $table->integer('catid')->nullable();
            $table->integer('subcatid');
            $table->string('productname')->nullable();
            $table->integer('unit');
            $table->string('productdes');
            $table->integer('pqty')->nullable();
            $table->integer('tqty')->nullable();
            $table->string('pfrom')->nullable();
            $table->decimal('prate', 10,2);
            $table->decimal('srate', 10,2);
            $table->decimal('mrp', 10,2);
            $table->integer('gst');
            $table->decimal('cprice', 10,2);
            $table->decimal('dprice', 10,2);
            $table->integer('status');
            $table->string('pimagef')->nullable();
            $table->string('pimages')->nullable();
            $table->string('pimaget')->nullable();
            $table->string('hsnsac');
            $table->string('slno')->nullable();
            $table->string('postingdate')->nullable();
            $table->string('updationdate')->nullable();
            $table->decimal('sdprice', 10,2);
            $table->string('pcode')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};