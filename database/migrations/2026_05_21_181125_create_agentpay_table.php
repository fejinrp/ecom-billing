<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('agentpay', function (Blueprint $table) {
            $table->integer('payid');
            $table->integer('acode');
            $table->date('pdate');
            $table->decimal('pamount', 10,2);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('agentpay');
    }
};