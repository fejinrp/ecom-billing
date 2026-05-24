<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mtlstock', function (Blueprint $table) {
            $table->integer('mtlid');
            $table->timestamp('stockdate')->nullable();
            $table->decimal('cashhand', 10,2);
            $table->decimal('openstock', 10,2);
            $table->decimal('closestock', 10,2);
            $table->decimal('todaystock', 10,2);
            $table->integer('uname');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mtlstock');
    }
};