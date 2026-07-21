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
        // 1. Alter columns to make catid and scatid nullable
        Schema::table('brands', function (Blueprint $table) {
            $table->integer('catid')->nullable()->change();
            $table->integer('scatid')->nullable()->change();
        });

        // 2. Perform DB deduplication of existing brand records
        try {
            DB::transaction(function () {
                // Find duplicate brand names (case-insensitive)
                $duplicates = DB::select("
                    SELECT LOWER(TRIM(brand_name)) as lower_name, MIN(brand_id) as keep_id, COUNT(*) as cnt
                    FROM brands
                    GROUP BY LOWER(TRIM(brand_name))
                    HAVING cnt > 1
                ");

                foreach ($duplicates as $dup) {
                    $keepId = $dup->keep_id;
                    $lowerName = $dup->lower_name;

                    // Get all brand_ids for this duplicate name except the keepId
                    $discardIds = DB::table('brands')
                        ->whereRaw("LOWER(TRIM(brand_name)) = ?", [$lowerName])
                        ->where('brand_id', '!=', $keepId)
                        ->pluck('brand_id')
                        ->toArray();

                    if (!empty($discardIds)) {
                        // Re-point products to the master brand_id
                        DB::table('products')
                            ->whereIn('brandid', $discardIds)
                            ->update(['brandid' => $keepId]);

                        // Delete redundant duplicate brand rows
                        DB::table('brands')->whereIn('brand_id', $discardIds)->delete();
                    }

                    // Set catid and scatid to NULL for master record so it acts as a global brand
                    DB::table('brands')->where('brand_id', $keepId)->update([
                        'catid' => null,
                        'scatid' => null
                    ]);
                }
            });
        } catch (\Exception $e) {
            // Ignore if running in environment where products table or brands table isn't populated
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('brands', function (Blueprint $table) {
            $table->integer('catid')->nullable(false)->change();
            $table->integer('scatid')->nullable(false)->change();
        });
    }
};
