<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'order_number',
        'user_id',
        'subtotal',
        'discount_amount',
        'coupon_code',
        'tax_amount',
        'shipping_amount',
        'grand_total',
        'payment_method',
        'payment_status',
        'order_status',
        'shipping_address',
        'billing_address',
        'tracking_number',
        'carrier_name',
        'customer_notes',
        'admin_notes',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'shipping_amount' => 'decimal:2',
        'grand_total' => 'decimal:2',
        'shipping_address' => 'array',
        'billing_address' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function canBeCancelled(): bool
    {
        return in_array($this->order_status, ['pending', 'confirmed']);
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->order_status) {
            'pending' => 'bg-amber-100 text-amber-800 border-amber-200',
            'confirmed' => 'bg-blue-100 text-blue-800 border-blue-200',
            'processing' => 'bg-indigo-100 text-indigo-800 border-indigo-200',
            'shipped' => 'bg-purple-100 text-purple-800 border-purple-200',
            'out_for_delivery' => 'bg-teal-100 text-teal-800 border-teal-200',
            'delivered' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
            'cancelled', 'refunded', 'returned' => 'bg-rose-100 text-rose-800 border-rose-200',
            default => 'bg-slate-100 text-slate-800 border-slate-200',
        };
    }
}
