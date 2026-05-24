<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('qorders', function (Blueprint $table) {
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
            $table->integer('qtype');
            $table->string('signa')->nullable();
            $table->decimal('discount', 10,2)->default(0.00);
            $table->decimal('gtotal', 10,2)->default(0.00);
            $table->integer('qstate')->default(1);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('qorders');
    }
};