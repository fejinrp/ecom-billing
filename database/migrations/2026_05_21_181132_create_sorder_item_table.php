<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sorder_item', function (Blueprint $table) {
            $table->id('item_id');
            $table->integer('order_id');
            $table->longText('sname');
            $table->integer('qty');
            $table->decimal('rate', 12,2);
            $table->decimal('total', 12,2);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sorder_item');
    }
};