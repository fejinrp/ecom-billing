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
        if (!Schema::hasTable('settings')) {
            Schema::create('settings', function (Blueprint $table) {
                $table->id();
                $table->string('key')->unique();
                $table->text('value')->nullable();
                $table->text('description')->nullable();
                $table->timestamps();
            });

            // Seed default settings for office/attendance
            DB::table('settings')->insert([
                [
                    'key' => 'daily_work_duration',
                    'value' => '8',
                    'description' => 'Standard daily work hours duration'
                ],
                [
                    'key' => 'office_start_time',
                    'value' => '10:00',
                    'description' => 'Standard office start shift time'
                ],
                [
                    'key' => 'office_end_time',
                    'value' => '18:00',
                    'description' => 'Standard office close shift time'
                ]
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
