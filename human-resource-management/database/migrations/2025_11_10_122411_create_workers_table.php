<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('workers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('phone')->unique();
            $table->decimal('salary', 10, 2);
            $table->string('mpesa_receipt_number')->nullable();
            $table->enum('payment_status', ['pending', 'processing', 'paid', 'failed'])->default('pending');
            $table->json('payment_response')->nullable();
            $table->timestamp('last_payment_date')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('workers');
    }
};