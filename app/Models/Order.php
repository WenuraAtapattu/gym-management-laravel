<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Shipping;
use App\Models\OrderItem;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($order) {
            if (empty($order->order_number)) {
                $order->order_number = 'ORD-' . now()->format('Ymd') . '-' . strtoupper(Str::random(6));
            }
        });
    }

    public const STATUS_PENDING = 'pending';
    public const STATUS_PROCESSING = 'processing';
    public const STATUS_SHIPPED = 'shipped';
    public const STATUS_DELIVERED = 'delivered';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_CANCELLED = 'cancelled';
    public const STATUS_REFUNDED = 'refunded';

    // Payment status constants
    public const PAYMENT_STATUS_PENDING = 'pending';
    public const PAYMENT_STATUS_AUTHORIZED = 'authorized';
    public const PAYMENT_STATUS_PAID = 'paid';
    public const PAYMENT_STATUS_PARTIALLY_REFUNDED = 'partially_refunded';
    public const PAYMENT_STATUS_REFUNDED = 'refunded';
    public const PAYMENT_STATUS_VOIDED = 'voided';
    public const PAYMENT_STATUS_FAILED = 'failed';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'order_number',
        'status',
        'shipping_id',
        'user_id',
        'order_date',
        'amount',
        'total_amount',
        'total',
        'subtotal',
        'tax_amount',
        'shipping_cost',
        'discount_amount',
        'payment_status',
        'shipping_address',
        'created_at',
        'updated_at'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'order_date' => 'datetime',
        'amount' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'shipping_address' => 'json',
        'billing_address' => 'json'
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'formatted_subtotal',
        'formatted_tax',
        'formatted_shipping',
        'formatted_discount',
        'formatted_total',
        'status_label',
        'payment_status_label',
    ];

    /**
     * Get the shipping for the order.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function shipping(): BelongsTo
    {
        return $this->belongsTo(Shipping::class);
    }

    /**
     * Get the user that owns the order.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the user who created the order.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated the order.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Get the order items for the order.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Get the order items for the order (alias for orderItems).
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Get the payments for the order.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Scope a query to only include pending orders.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /**
     * Scope a query to only include processing orders.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeProcessing(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_PROCESSING);
    }

    /**
     * Scope a query to only include shipped orders.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeShipped(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_SHIPPED);
    }

    /**
     * Scope a query to only include delivered orders.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeDelivered(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_DELIVERED);
    }

    /**
     * Scope a query to only include completed orders.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCompleted(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }

    /**
     * Scope a query to only include cancelled orders.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCancelled(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_CANCELLED);
    }

    /**
     * Scope a query to only include refunded orders.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeRefunded(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_REFUNDED);
    }

    /**
     * Scope a query to only include orders with a specific payment status.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $status
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePaymentStatus(Builder $query, string $status): Builder
    {
        return $query->where('payment_status', $status);
    }

    /**
     * Scope a query to only include orders for a specific user.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  int  $userId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForUser(Builder $query, int $userId): Builder
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope a query to only include orders within a date range.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string|\DateTime  $from
     * @param  string|\DateTime  $to
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeDateRange(Builder $query, $from, $to): Builder
    {
        return $query->whereBetween('created_at', [
            $from instanceof \DateTime ? $from->format('Y-m-d') : $from,
            $to instanceof \DateTime ? $to->format('Y-m-d') : $to,
        ]);
    }

    /**
     * Get the formatted subtotal attribute.
     *
     * @return string
     */
    public function getFormattedSubtotalAttribute(): string
    {
        return '$' . number_format((float) ($this->subtotal ?? 0), 2);
    }

    /**
     * Get the formatted tax attribute.
     *
     * @return string
     */
    public function getFormattedTaxAttribute(): string
    {
        return '$' . number_format((float) ($this->tax_amount ?? 0), 2);
    }

    /**
     * Get the formatted shipping cost attribute.
     *
     * @return string
     */
    public function getFormattedShippingAttribute(): string
    {
        return '$' . number_format((float) ($this->shipping_cost ?? 0), 2);
    }

    /**
     * Get the formatted discount amount attribute.
     *
     * @return string
     */
    public function getFormattedDiscountAttribute(): string
    {
        return '$' . number_format((float) ($this->discount_amount ?? 0), 2);
    }

    /**
     * Get the formatted total attribute.
     *
     * @return string
     */
    public function getFormattedTotalAttribute(): string
    {
        return '$' . number_format((float) ($this->total_amount ?? $this->total ?? 0), 2);
    }

    /**
     * Get the human-readable status label.
     *
     * @return string
     */
    public function getStatusLabelAttribute(): string
    {
        $labels = [
            self::STATUS_PENDING => 'Pending',
            self::STATUS_PROCESSING => 'Processing',
            self::STATUS_SHIPPED => 'Shipped',
            self::STATUS_DELIVERED => 'Delivered',
            self::STATUS_COMPLETED => 'Completed',
            self::STATUS_CANCELLED => 'Cancelled',
            self::STATUS_REFUNDED => 'Refunded',
        ];

        return $labels[$this->status] ?? ucfirst($this->status);
    }

    /**
     * Get the human-readable payment status label.
     *
     * @return string
     */
    public function getPaymentStatusLabelAttribute(): string
    {
        $labels = [
            self::PAYMENT_STATUS_PENDING => 'Pending',
            self::PAYMENT_STATUS_AUTHORIZED => 'Authorized',
            self::PAYMENT_STATUS_PAID => 'Paid',
            self::PAYMENT_STATUS_PARTIALLY_REFUNDED => 'Partially Refunded',
            self::PAYMENT_STATUS_REFUNDED => 'Refunded',
            self::PAYMENT_STATUS_VOIDED => 'Voided',
            self::PAYMENT_STATUS_FAILED => 'Failed',
        ];

        return $labels[$this->payment_status] ?? ucfirst(str_replace('_', ' ', $this->payment_status));
    }


    /**
     * Check if the order is paid.
     */
    public function isPaid()
    {
        return $this->payment_status === 'paid';
    }

    /**
     * Check if the order is pending.
     */
    public function isPending()
    {
        return $this->status === 'pending';
    }

    /**
     * Check if the order is completed.
     */
    public function isCompleted()
    {
        return $this->status === 'completed';
    }

    /**
     * Check if the order is cancelled.
     */
    public function isCancelled()
    {
        return $this->status === 'cancelled';
    }

    /**
     * Calculate the total amount for the order.
     */
    public function calculateTotal()
    {
        $subtotal = $this->subtotal ?? 0;
        $tax = $this->tax_amount ?? 0;
        $shipping = $this->shipping_cost ?? 0;
        $discount = $this->discount_amount ?? 0;
        
        $this->total_amount = $subtotal + $tax + $shipping - $discount;
        $this->save();
        return $this->total_amount;
    }
}
