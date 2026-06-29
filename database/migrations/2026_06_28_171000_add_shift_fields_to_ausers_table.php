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
        Schema::table('ausers', function (Blueprint $table) {
            $table->decimal('hourly_rate', 10, 2)->default(0.00)->after('ustatus');
            $table->time('shift_start')->nullable()->after('hourly_rate');
            $table->time('shift_end')->nullable()->after('shift_start');
            $table->string('mac_address')->nullable()->after('shift_end');
            $table->text('permissions')->nullable()->after('mac_address');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ausers', function (Blueprint $table) {
            $table->dropColumn(['hourly_rate', 'shift_start', 'shift_end', 'mac_address', 'permissions']);
        });
    }
};
