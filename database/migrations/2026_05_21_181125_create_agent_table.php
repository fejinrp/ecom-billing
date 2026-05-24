<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('agent', function (Blueprint $table) {
            $table->integer('acode');
            $table->string('aname');
            $table->string('aplace');
            $table->string('amobile');
            $table->date('adate');
            $table->integer('astatus');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('agent');
    }
};