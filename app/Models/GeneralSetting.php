<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GeneralSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'website_name',
        'slogan',
        'description',
        'contact_email',
        'contact_phone',
        'address',
        'logo',
        'favicon'
    ];
}
