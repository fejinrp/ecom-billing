<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('expname', function (Blueprint $table) {
            $table->id('exp_id');
            $table->string('exp_name');
            $table->string('estatus');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('expname');
    }
};