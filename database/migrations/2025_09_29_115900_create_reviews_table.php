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
        if (!Schema::hasTable('reviews')) {
            Schema::create('reviews', function (Blueprint $table) {
                $table->id();
                $table->morphs('reviewable'); // This creates reviewable_id and reviewable_type columns
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->unsignedTinyInteger('rating');
                $table->string('title');
                $table->text('comment')->nullable();
                $table->text('content')->nullable(); // For backward compatibility
                $table->boolean('is_approved')->default(true);
                $table->string('guest_name')->nullable();
                $table->string('guest_email')->nullable();
                $table->timestamps();
                
                // Indexes for better performance
                $table->index(['reviewable_id', 'reviewable_type']);
                $table->index('is_approved');
                $table->index('user_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
