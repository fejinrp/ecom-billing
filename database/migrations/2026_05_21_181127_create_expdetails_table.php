<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('expdetails', function (Blueprint $table) {
            $table->id('exp_id');
            $table->date('exp_date');
            $table->string('exp_name');
            $table->string('exp_amount');
            $table->string('sname');
            $table->integer('mexp_id');
            $table->integer('estatus');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('expdetails');
    }
};