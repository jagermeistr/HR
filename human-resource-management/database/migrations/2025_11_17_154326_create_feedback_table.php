<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('feedback', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sender_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('receiver_id')->constrained('users')->cascadeOnDelete();
            $table->text('message');
            $table->enum('type', ['positive', 'constructive', 'general']);
            $table->boolean('is_anonymous')->default(false);
            $table->enum('status', ['draft', 'sent', 'archived'])->default('draft');
            $table->timestamp('sent_at')->nullable();
            $table->softDeletes();
            $table->timestamps();
            
            $table->index(['receiver_id', 'status']);
            $table->index(['sender_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('feedback');
    }
};