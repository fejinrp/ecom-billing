<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Offline sales (walk-in customers)
        Schema::table('orders', function (Blueprint $table) {
            $table->integer('lucky_draw_batch_no')->nullable()->after('order_status');
            $table->string('lucky_draw_cat')->nullable()->after('lucky_draw_batch_no');
        });

        // Online orders (registered storefront users)
        Schema::table('uorder', function (Blueprint $table) {
            $table->integer('lucky_draw_batch_no')->nullable()->after('ostatus');
            $table->string('lucky_draw_cat')->nullable()->after('lucky_draw_batch_no');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['lucky_draw_batch_no', 'lucky_draw_cat']);
        });

        Schema::table('uorder', function (Blueprint $table) {
            $table->dropColumn(['lucky_draw_batch_no', 'lucky_draw_cat']);
        });
    }
};
