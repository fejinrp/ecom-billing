<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_item', function (Blueprint $table) {
            $table->id('item_id');
            $table->integer('order_id');
            $table->integer('product_id');
            $table->string('hsnsan');
            $table->integer('gst');
            $table->integer('qty');
            $table->decimal('rate', 12,2);
            $table->string('unit');
            $table->decimal('total', 12,2);
            $table->integer('status');
            $table->string('slno')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_item');
    }
};