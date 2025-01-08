<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Address extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'label',
        'name',
        'address_line_1',
        'address_line_2',
        'kelurahan',
        'kecamatan',
        'kota_kab',
        'provinsi',
        'kelurahan_id',
        'kecamatan_id',
        'kabupaten_id',
        'provinsi_id',
        'country',
        'zip_code',
        'phone',
        'is_default'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // public function orders(): BelongsTo
    // {
    //     return $this->belongsTo(Order::class);
    // }
}
