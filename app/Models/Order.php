<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'address', 'subtotal', 'discount_amount', 'total_amount', 'weight', 'shipping_cost', 'discount_id', 'status_id', 'shipping_method_id', 'payment_method_id'];

    protected $casts = [
        'address' => 'json', // Cast the 'address' field as JSON
    ];


    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function discount(): BelongsTo
    {
        return $this->belongsTo(Discount::class);
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(OrderStatus::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function shippingInformation(): HasOne
    {
        return $this->hasOne(ShippingInformation::class);
    }

    public function paymentInformation(): HasOne
    {
        return $this->hasOne(PaymentInformation::class);
    }


    public function shippingAddress(): BelongsTo
    {
        return $this->belongsTo(Address::class, 'address_id');
    }

    public function shippingMethod(): BelongsTo
    {
        return $this->belongsTo(ShippingMethod::class);
    }

    public function paymentMethod(): BelongsTo
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    // Add accessor for address data
    public function getAddressAttribute($value)
    {
        return json_decode($value, true);
    }

    public function scopeApprovedOrders($query)
    {
        return $query->where('status_id', '>=', 2) // Menampilkan status 2 (Approved) dan lebih besar (Packing dan Shipped)
            ->whereHas('paymentInformation', function ($q) {
                $q->where('payment_status', 'Approved');
            });
    }
}
