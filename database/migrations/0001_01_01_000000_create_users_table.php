<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('uname')->nullable();
            $table->string('email')->nullable();
            $table->string('contactno')->nullable();
            $table->string('password')->nullable();
            $table->string('usertype')->nullable();
            $table->integer('ustatus');
            $table->longText('shippingaddress')->nullable();
            $table->string('shippingstate')->nullable();
            $table->string('shippingcity')->nullable();
            $table->string('shippingpincode')->nullable();
            $table->longText('billingaddress')->nullable();
            $table->string('billingstate')->nullable();
            $table->string('billingcity')->nullable();
            $table->string('billingpincode')->nullable();
            $table->timestamp('regdate')->default('current_timestamp');
            $table->string('updationdate')->nullable();
            $table->string('gsttin')->nullable();
            $table->decimal('mcoin', 10,2)->default(0.00);
            $table->decimal('mcoinp', 10,2)->default(0.00);
            $table->decimal('mcoinb', 10,2)->default(0.00);
            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};