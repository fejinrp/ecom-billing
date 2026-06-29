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
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('user_id');
            $table->date('date');
            $table->dateTime('punch_in')->nullable();
            $table->dateTime('punch_out')->nullable();
            $table->decimal('punch_in_lat', 10, 8)->nullable();
            $table->decimal('punch_in_long', 11, 8)->nullable();
            $table->decimal('punch_out_lat', 10, 8)->nullable();
            $table->decimal('punch_out_long', 11, 8)->nullable();
            $table->string('punch_in_device')->nullable();
            $table->string('punch_out_device')->nullable();
            $table->string('punch_in_ip')->nullable();
            $table->string('punch_out_ip')->nullable();
            $table->string('status')->default('Present');
            $table->text('notes')->nullable();
            $table->string('total_hours')->nullable();
            $table->string('overtime_hours')->nullable();
            $table->integer('overtime_minutes')->default(0);
            $table->integer('late_minutes')->default(0);
            $table->integer('early_exit_minutes')->default(0);
            $table->decimal('earned_salary', 10, 2)->default(0.00);
            $table->decimal('basic_earned', 10, 2)->default(0.00);
            $table->decimal('overtime_earned', 10, 2)->default(0.00);
            $table->timestamps();

            // Foreign key relation mapping to ausers.user_id
            $table->foreign('user_id')->references('user_id')->on('ausers')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
