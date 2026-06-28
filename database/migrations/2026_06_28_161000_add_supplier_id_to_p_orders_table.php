<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('p_orders', function (Blueprint $table) {
            $table->unsignedBigInteger('supplier_id')->nullable()->after('porder_id');
        });

        // Seed existing suppliers from p_orders
        $existingPurchases = DB::table('p_orders')
            ->select('s_name', 's_contact')
            ->where('s_name', '<>', '')
            ->distinct()
            ->get();

        foreach ($existingPurchases as $purchase) {
            // Trim and uppercase the name
            $name = strtoupper(trim($purchase->s_name));
            
            // Check if supplier already exists
            $supplierId = DB::table('suppliers')->where('name', $name)->value('id');

            if (!$supplierId) {
                $supplierId = DB::table('suppliers')->insertGetId([
                    'name' => $name,
                    'address' => $purchase->s_contact,
                    'status' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // Update matching purchase orders
            DB::table('p_orders')
                ->where('s_name', $purchase->s_name)
                ->update(['supplier_id' => $supplierId]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('p_orders', function (Blueprint $table) {
            $table->dropColumn('supplier_id');
        });
    }
};
