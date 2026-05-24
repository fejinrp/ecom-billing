<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kkpincode', function (Blueprint $table) {
            $table->id('pinid');
            $table->integer('pincode');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kkpincode');
    }
};