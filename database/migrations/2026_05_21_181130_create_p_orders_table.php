<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('p_orders', function (Blueprint $table) {
            $table->id('porder_id');
            $table->date('porder_date');
            $table->string('s_name');
            $table->string('s_contact');
            $table->integer('staffname');
            $table->decimal('sub_total', 13,2);
            $table->decimal('vat', 13,2)->default(0.00);
            $table->decimal('t_amount', 13,2);
            $table->decimal('discount', 13,2);
            $table->decimal('g_total', 13,2);
            $table->decimal('ppaid', 12,2);
            $table->decimal('pbal', 12,2);
            $table->decimal('gstn', 13,2)->default(0.00);
            $table->integer('porder_status');
            $table->integer('morder_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('p_orders');
    }
};