<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'membership_id',
        'amount',
        'payment_date',
        'payment_method',
        'status',
        'notes',
        'transaction_reference'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'payment_date' => 'date',
        'amount' => 'decimal:2',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
        'payment_date',
    ];

    /**
     * The member who made this payment.
     */
    public function member(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * The membership that this payment belongs to.
     */
    public function membership(): BelongsTo
    {
        return $this->belongsTo(Membership::class);
    }

    /**
     * Get the formatted amount attribute.
     */
    public function getFormattedAmountAttribute(): string
    {
        return '₦' . number_format($this->amount, 2);
    }

    /**
     * Get the payment method label attribute.
     */
    public function getPaymentMethodLabelAttribute(): string
    {
        return [
            'cash' => 'Cash',
            'credit_card' => 'Credit Card',
            'debit_card' => 'Debit Card',
            'bank_transfer' => 'Bank Transfer',
            'other' => 'Other'
        ][$this->payment_method] ?? ucfirst($this->payment_method);
    }

    /**
     * Get the status badge attribute.
     */
    public function getStatusBadgeAttribute(): string
    {
        $statuses = [
            'pending' => 'bg-yellow-100 text-yellow-800',
            'completed' => 'bg-green-100 text-green-800',
            'failed' => 'bg-red-100 text-red-800',
            'refunded' => 'bg-blue-100 text-blue-800',
        ];

        $class = $statuses[$this->status] ?? 'bg-gray-100 text-gray-800';
        
        return '<span class="inline-flex px-2 text-xs font-semibold leading-5 rounded-full' . $class . '">' 
             . ucfirst($this->status) 
             . '</span>';
    }
}
