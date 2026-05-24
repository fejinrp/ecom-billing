<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('uorderbal', function (Blueprint $table) {
            $table->id('balid');
            $table->integer('orderid');
            $table->decimal('gtotal', 10,2);
            $table->decimal('pamount', 10,2)->default(0.00);
            $table->decimal('bamount', 10,2)->default(0.00);
            $table->string('ptype')->nullable();
            $table->timestamp('pdate')->default('current_timestamp');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('uorderbal');
    }
};