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
        Schema::create('contracts', function (Blueprint $table) {
            $table->id();
            $table->foreignId(column: 'employee_id')->constrained()->onDelete(action: 'cascade');
            $table->foreignId(column: 'designation_id')->constrained()->onDelete(action: 'cascade');
            $table->date(column: 'start_date');
            $table->date(column: 'end_date');
            $table->enum(column: 'rate_type', allowed: ['daily', 'monthly']);
            $table->decimal(column: 'rate', total:10, places: 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contracts');
    }
};
