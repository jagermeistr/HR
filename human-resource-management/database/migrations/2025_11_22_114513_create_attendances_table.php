<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->onDelete('cascade');
            $table->date('date');
            $table->time('check_in')->nullable();
            $table->time('check_out')->nullable();
            $table->decimal('hours_worked', 5, 2)->default(0);
            $table->enum('status', ['present', 'absent', 'late', 'half_day'])->default('present');
            $table->text('notes')->nullable();
            $table->boolean('overtime')->default(false);
            $table->decimal('overtime_hours', 5, 2)->default(0);
            $table->boolean('is_late')->default(false);
            $table->timestamps();
            
            $table->unique(['employee_id', 'date']);
        });

        // Create company settings table
        Schema::create('company_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->time('late_threshold')->default('09:15:00'); // Default 15 minutes late
            $table->decimal('regular_hours', 5, 2)->default(8.00); // Regular hours before overtime
            $table->integer('burnout_threshold')->default(40); // Weekly hours for burnout
            $table->time('work_start_time')->default('09:00:00');
            $table->time('work_end_time')->default('17:00:00');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendances');
        Schema::dropIfExists('company_settings');
    }
};