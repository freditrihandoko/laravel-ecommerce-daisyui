<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;

class PaymentInformation extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'transaction_id',
        'payment_status',
        'amount_paid',
        'payment_proof',
        'payment_date'
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
