<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('memberships', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['basic', 'premium', 'vip', 'student', 'senior', 'family']);
            $table->date('start_date');
            $table->date('end_date');
            $table->enum('status', ['active', 'expired', 'cancelled', 'on_hold'])->default('active');
            $table->decimal('price', 10, 2);
            $table->enum('payment_status', ['paid', 'pending', 'overdue', 'refunded'])->default('pending');
            $table->text('notes')->nullable();
            $table->timestamps();
            
            // Indexes
            $table->index(['user_id', 'status']);
            $table->index('end_date');
        });
    }

    public function down()
    {
        Schema::dropIfExists('memberships');
    }
};
