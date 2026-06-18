<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('product_batches', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id');
            $table->string('batch_number');
            $table->date('mfg_date')->nullable();
            $table->date('expiry_date')->nullable();
            $table->integer('initial_qty');
            $table->integer('current_qty');
            $table->integer('warranty_months')->default(0);
            $table->decimal('prate', 10, 2)->nullable();
            $table->decimal('srate', 10, 2)->nullable();
            $table->decimal('mrp', 10, 2)->nullable();
            $table->decimal('cprice', 10, 2)->nullable();
            $table->decimal('dprice', 10, 2)->nullable();
            $table->decimal('sdprice', 10, 2)->nullable();
            $table->integer('status')->default(1); // 1 = Active, 0 = Inactive
            $table->timestamps();
        });

        // 2. Create delivery_notes table
        Schema::create('delivery_notes', function (Blueprint $table) {
            $table->id();
            $table->string('dn_number')->unique();
            $table->string('type'); // 'inward' or 'outward'
            $table->unsignedBigInteger('porder_id')->nullable(); // Reference to purchases
            $table->unsignedBigInteger('order_id')->nullable();  // Reference to POS orders
            $table->integer('status')->default(1); // 1 = Draft, 2 = Shipped, 3 = Delivered/Received
            $table->string('carrier_info')->nullable();
            $table->date('dn_date');
            $table->timestamps();
        });

        // 3. Create delivery_note_items table
        Schema::create('delivery_note_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('delivery_note_id');
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('batch_id')->nullable();
            $table->integer('qty_shipped');
            $table->integer('qty_received');
            $table->integer('qty_damaged')->default(0);
            $table->timestamps();
        });

        // 4. Modify existing tables
        Schema::table('products', function (Blueprint $table) {
            $table->integer('warranty_months')->default(0)->after('pcode');
        });

        Schema::table('order_item', function (Blueprint $table) {
            $table->unsignedBigInteger('batch_id')->nullable()->after('prod_id');
            $table->date('warranty_expiry_date')->nullable()->after('qty');
        });

        Schema::table('eorder_item', function (Blueprint $table) {
            $table->unsignedBigInteger('batch_id')->nullable()->after('prod_id');
            $table->date('warranty_expiry_date')->nullable()->after('qty');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('eorder_item', function (Blueprint $table) {
            $table->dropColumn(['batch_id', 'warranty_expiry_date']);
        });

        Schema::table('order_item', function (Blueprint $table) {
            $table->dropColumn(['batch_id', 'warranty_expiry_date']);
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('warranty_months');
        });

        Schema::dropIfExists('delivery_note_items');
        Schema::dropIfExists('delivery_notes');
        Schema::dropIfExists('product_batches');
    }
};
