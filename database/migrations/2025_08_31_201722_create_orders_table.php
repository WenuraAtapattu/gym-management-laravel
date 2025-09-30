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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('order_number', 50)->unique();
            
            // Order status tracking
            $table->enum('status', [
                'pending', 
                'processing', 
                'shipped', 
                'delivered', 
                'completed', 
                'cancelled',
                'refunded'
            ])->default('pending');
            
            // Financial information
            $table->decimal('subtotal', 10, 2);
            $table->decimal('tax_amount', 10, 2)->default(0);
            $table->decimal('shipping_cost', 10, 2)->default(0);
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->string('discount_code')->nullable();
            $table->decimal('total', 10, 2);
            
            // Payment information
            $table->enum('payment_status', [
                'pending',
                'authorized',
                'paid',
                'partially_refunded',
                'refunded',
                'voided',
                'failed'
            ])->default('pending');
            $table->string('payment_method')->nullable();
            $table->string('transaction_id')->nullable();
            
            // Shipping information
            $table->string('shipping_method')->nullable();
            $table->string('tracking_number')->nullable();
            
            // Customer information
            $table->string('customer_email');
            $table->string('customer_phone')->nullable();
            
            // Billing and shipping addresses (could be normalized into a separate table for production)
            $table->json('billing_address')->nullable();
            $table->json('shipping_address')->nullable();
            
            // Order metadata
            $table->text('customer_notes')->nullable();
            $table->text('admin_notes')->nullable();
            $table->timestamp('shipped_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->string('cancellation_reason')->nullable();
            
            // Audit fields
            $table->timestamps();
            $table->softDeletes();
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
