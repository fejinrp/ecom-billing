<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('retproduct', function (Blueprint $table) {
            $table->id('ret_id');
            $table->date('ret_date');
            $table->integer('order_itemid');
            $table->decimal('ret_qty', 11,2);
            $table->decimal('ret_amount', 12,2);
            $table->integer('order_id');
            $table->integer('product_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('retproduct');
    }
};