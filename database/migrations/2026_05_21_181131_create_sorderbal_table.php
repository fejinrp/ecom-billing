<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sorderbal', function (Blueprint $table) {
            $table->id('order_idbal');
            $table->integer('order_id');
            $table->string('gtotal');
            $table->string('pamount');
            $table->string('bamount');
            $table->date('pdate');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sorderbal');
    }
};