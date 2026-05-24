<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('userlog', function (Blueprint $table) {
            $table->id('id');
            $table->string('usermobile')->nullable();
            $table->binary('userip')->nullable();
            $table->timestamp('loginTime')->nullable()->default('current_timestamp');
            $table->string('logout')->nullable();
            $table->integer('status')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('userlog');
    }
};