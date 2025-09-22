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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId(column: 'payroll_id')->constrained()->onDelete(action: 'cascade');
            $table->foreignId(column: 'employee_id')->constrained()->onDelete(action: 'cascade');
            $table->decimal(column: 'amount', total:10, places: 2);
            $table->date(column: 'payment_date')->nullable();
            $table->string(column: 'payment_method')->nullable();
            $table->string(column: 'reference')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
