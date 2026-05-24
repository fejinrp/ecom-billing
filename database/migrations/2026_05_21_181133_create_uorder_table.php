<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('uorder', function (Blueprint $table) {
            $table->id('orderid');
            $table->integer('userid');
            $table->string('utype');
            $table->string('paymethod')->nullable();
            $table->decimal('total', 10,2)->nullable()->default(0.00);
            $table->decimal('gamount', 10,2);
            $table->decimal('tship', 10,2);
            $table->decimal('pamount', 10,2);
            $table->decimal('bamount', 10,2);
            $table->decimal('discount', 10,2);
            $table->decimal('gsta', 10,2);
            $table->string('ostatus')->nullable();
            $table->integer('morderid');
            $table->decimal('install', 10,2);
            $table->string('gsttin')->nullable();
            $table->decimal('mcoin', 10,2)->default(0.00);
            $table->decimal('bcoin', 10,2)->default(0.00);
            $table->decimal('tcoin', 10,2)->default(0.00);
            $table->decimal('pcoin', 10,2)->default(0.00);
            $table->string('username')->nullable();
            $table->timestamp('orderdate')->nullable()->default('current_timestamp');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('uorder');
    }
};