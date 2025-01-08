<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ShippingInformation extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'shipping_method',
        'shipping_cost',
        'tracking_number',
        'carrier',
        'estimated_delivery_date'
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
