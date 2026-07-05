<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lucky_draw_winners', function (Blueprint $table) {
            $table->id();
            $table->string('category');                              // matches lucky_draw_settings.category_key
            $table->integer('batch_no');
            $table->string('source');                               // 'offline' | 'online'
            $table->unsignedBigInteger('order_id')->nullable();     // orders.order_id  (offline)
            $table->unsignedBigInteger('uorder_id')->nullable();    // uorder.orderid   (online)
            $table->string('winner_name');
            $table->string('winner_mobile');
            $table->decimal('prize_amount', 10, 2)->default(1000.00);
            $table->unsignedBigInteger('drawn_by')->nullable();     // ausers.user_id — audit trail
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lucky_draw_winners');
    }
};
