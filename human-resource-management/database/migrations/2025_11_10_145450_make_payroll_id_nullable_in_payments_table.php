<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            // Make payroll_id nullable
            $table->unsignedBigInteger('payroll_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            // Revert back to not nullable if needed
            $table->unsignedBigInteger('payroll_id')->nullable(false)->change();
        });
    }
};