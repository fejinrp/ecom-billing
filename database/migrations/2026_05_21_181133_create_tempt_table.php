<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tempt', function (Blueprint $table) {
            $table->id('tid');
            $table->integer('porder_id');
            $table->integer('pid');
            $table->decimal('qty', 12,2);
            $table->decimal('rate', 12,2);
            $table->decimal('total', 12,2);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tempt');
    }
};