<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('p_price', function (Blueprint $table) {
            $table->id('price_id');
            $table->integer('product_id');
            $table->integer('brand_id');
            $table->integer('cat_id');
            $table->integer('tsheet');
            $table->integer('psheet');
            $table->integer('quantity');
            $table->string('iunit');
            $table->decimal('p_gst', 13,2);
            $table->decimal('p_pur', 13,2);
            $table->decimal('p_mrp', 13,2);
            $table->decimal('p_sel', 13,2);
            $table->integer('active');
            $table->integer('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('p_price');
    }
};