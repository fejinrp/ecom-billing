<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id('order_id');
            $table->date('order_date');
            $table->string('client_name')->nullable();
            $table->string('client_contact')->nullable();
            $table->decimal('sub_total', 10,2);
            $table->decimal('total_amount', 10,2);
            $table->decimal('discount', 10,2);
            $table->decimal('grand_total', 10,2);
            $table->decimal('paid', 10,2);
            $table->decimal('due', 10,2);
            $table->integer('payment_type');
            $table->integer('payment_status');
            $table->integer('payment_place');
            $table->decimal('gstn', 10,2);
            $table->integer('order_status')->default(0);
            $table->integer('user_id');
            $table->string('paymentname');
            $table->integer('morder_id');
            $table->string('mobile');
            $table->string('gsttin');
            $table->integer('section');
            $table->decimal('instamt', 10,2);
            $table->decimal('shipamt', 10,2);
            $table->decimal('mcoin', 10,2)->default(0.00);
            $table->decimal('bcoin', 10,2)->default(0.00);
            $table->decimal('tcoin', 10,2)->default(0.00);
            $table->decimal('pcoin', 10,2)->default(0.00);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};