<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('p_item', function (Blueprint $table) {
            $table->id('pitem_id');
            $table->integer('porder_id');
            $table->integer('prod_id');
            $table->decimal('rate', 10,2);
            $table->string('punit');
            $table->integer('tqty');
            $table->integer('pqty');
            $table->integer('qty');
            $table->decimal('tamount', 12,2);
            $table->integer('bqty')->default(0);
            $table->integer('status');
            $table->string('slno')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('p_item');
    }
};