<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tempreport', function (Blueprint $table) {
            $table->integer('pro_id');
            $table->string('pname');
            $table->integer('qty');
            $table->decimal('amount', 12,2);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tempreport');
    }
};