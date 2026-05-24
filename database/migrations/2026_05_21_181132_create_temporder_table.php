<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('temporder', function (Blueprint $table) {
            $table->integer('pid');
            $table->integer('go_id');
            $table->decimal('qty', 12,2);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('temporder');
    }
};