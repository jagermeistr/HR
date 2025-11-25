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
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade'); // ADD THIS LINE
            $table->foreignId(column: 'designation_id')->constrained()->onDelete(action: 'cascade');
            $table->string(column: 'name');
            $table->string(column: 'email')->unique();
            $table->string(column: 'phone');
            $table->string(column: 'address');          
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
