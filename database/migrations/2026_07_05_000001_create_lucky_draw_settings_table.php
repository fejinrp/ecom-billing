<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lucky_draw_settings', function (Blueprint $table) {
            $table->id();
            $table->string('category_key')->unique();   // e.g. 'bronze', 'premium'
            $table->string('category_label');           // e.g. 'Bronze', 'Premium'
            $table->decimal('min_amount', 10, 2)->default(0);     // inclusive lower bound
            $table->decimal('max_amount', 10, 2)->nullable();     // null = no upper bound
            $table->integer('batch_size');              // entries needed before a draw can happen
            $table->decimal('prize_amount', 10, 2)->default(1000.00);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lucky_draw_settings');
    }
};
