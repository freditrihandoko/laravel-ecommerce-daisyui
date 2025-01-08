<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Discount extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'description',
        'discount_type',
        'discount_value',
        'start_date',
        'end_date',
        'minimum_order_value',
        'maximum_discount_amount',
        'usage_limit',
        'usage_count'
    ];

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
    ];

    // Mengecek apakah diskon valid
    public function isValid($subtotal): bool
    {
        $now = now();
        return $this->start_date <= $now && $this->end_date >= $now
            && $subtotal >= $this->minimum_order_value
            && ($this->usage_limit === null || $this->usage_count < $this->usage_limit);
    }

    // Menghitung diskon berdasarkan tipe
    public function calculateDiscount($subtotal): float
    {
        if ($this->discount_type === 'percentage') {
            $discount = ($subtotal * $this->discount_value) / 100;
        } else {
            $discount = $this->discount_value;
        }

        return min($discount, $this->maximum_discount_amount);
    }
}
