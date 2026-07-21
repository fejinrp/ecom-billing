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
        Schema::table('subcategory', function (Blueprint $table) {
            $table->unsignedBigInteger('parent_subcategory_id')->nullable()->after('catid');
            $table->foreign('parent_subcategory_id')
                  ->references('id')
                  ->on('subcategory')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subcategory', function (Blueprint $table) {
            $table->dropForeign(['parent_subcategory_id']);
            $table->dropColumn('parent_subcategory_id');
        });
    }
};
