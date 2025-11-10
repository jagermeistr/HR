<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            // Make payroll_id nullable if it's required
            $table->unsignedBigInteger('payroll_id')->nullable()->change();
            
            // Add M-Pesa fields
            $table->string('transaction_id')->nullable()->after('payment_method');
            $table->string('mpesa_receipt_number')->nullable()->after('transaction_id');
            $table->json('mpesa_response')->nullable()->after('mpesa_receipt_number');
            $table->enum('payment_status', ['pending', 'processing', 'completed', 'failed'])->default('pending')->after('mpesa_response');
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn(['transaction_id', 'mpesa_receipt_number', 'mpesa_response', 'payment_status']);
            // Revert payroll_id change if needed
            $table->unsignedBigInteger('payroll_id')->nullable(false)->change();
        });
    }
};