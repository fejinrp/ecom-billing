<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ordertrackhistory', function (Blueprint $table) {
            $table->id('id');
            $table->integer('orderId')->nullable();
            $table->string('status')->nullable();
            $table->text('remark')->nullable();
            $table->timestamp('postingDate')->default('current_timestamp');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ordertrackhistory');
    }
};