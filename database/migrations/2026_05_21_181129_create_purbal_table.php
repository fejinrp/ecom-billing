<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('purbal', function (Blueprint $table) {
            $table->id('bal_id');
            $table->integer('porder_id');
            $table->decimal('gtotal', 11,2);
            $table->decimal('paid', 11,2);
            $table->decimal('bal', 11,2);
            $table->date('pdate');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purbal');
    }
};