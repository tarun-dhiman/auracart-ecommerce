<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'type',
        'value',
        'min_order_value',
        'max_discount',
        'start_date',
        'expiry_date',
        'usage_limit',
        'usage_per_user',
        'times_used',
        'is_active',
    ];

    protected $casts = [
        'value' => 'decimal:2',
        'min_order_value' => 'decimal:2',
        'max_discount' => 'decimal:2',
        'start_date' => 'datetime',
        'expiry_date' => 'datetime',
        'usage_limit' => 'integer',
        'usage_per_user' => 'integer',
        'times_used' => 'integer',
        'is_active' => 'boolean',
    ];

    public function usages()
    {
        return $this->hasMany(CouponUsage::class);
    }

    public function isValidForAmount(float $amount, ?User $user = null): array
    {
        if (!$this->is_active) {
            return ['valid' => false, 'message' => 'This coupon is no longer active.'];
        }

        $now = Carbon::now();
        if ($this->start_date && $now->lt($this->start_date)) {
            return ['valid' => false, 'message' => 'This coupon is not yet valid.'];
        }

        if ($this->expiry_date && $now->gt($this->expiry_date)) {
            return ['valid' => false, 'message' => 'This coupon has expired.'];
        }

        if ($this->usage_limit !== null && $this->times_used >= $this->usage_limit) {
            return ['valid' => false, 'message' => 'Coupon usage limit has been reached.'];
        }

        if ($amount < $this->min_order_value) {
            return [
                'valid' => false,
                'message' => "Minimum order value of ₹{$this->min_order_value} required for this coupon.",
            ];
        }

        if ($user) {
            $userUsageCount = $this->usages()->where('user_id', $user->id)->count();
            if ($userUsageCount >= $this->usage_per_user) {
                return ['valid' => false, 'message' => 'You have already used this coupon maximum times.'];
            }
        }

        return ['valid' => true, 'message' => 'Coupon applied successfully!'];
    }

    public function calculateDiscount(float $amount): float
    {
        $discount = 0;
        if ($this->type === 'percentage') {
            $discount = ($amount * $this->value) / 100;
            if ($this->max_discount !== null && $discount > $this->max_discount) {
                $discount = (float) $this->max_discount;
            }
        } else {
            $discount = min((float) $this->value, $amount);
        }

        return round($discount, 2);
    }
}
