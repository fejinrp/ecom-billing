<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('eorders', function (Blueprint $table) {
            $table->id('order_id');
            $table->date('order_date');
            $table->string('client_name');
            $table->string('client_contact');
            $table->decimal('sub_total', 10,2);
            $table->decimal('grand_total', 10,2);
            $table->decimal('gstn', 10,2);
            $table->integer('user_id');
            $table->integer('morder_id');
            $table->string('mobile');
            $table->string('gsttin');
            $table->decimal('instamt', 10,2);
            $table->integer('shipamt');
            $table->integer('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('eorders');
    }
};