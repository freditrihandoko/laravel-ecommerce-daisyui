<?php

namespace App\Models;

use App\Models\Category;
use App\Models\OrderItem;
use App\Models\ProductReview;
use App\Models\ProductVariant;
use Spatie\MediaLibrary\HasMedia;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Product extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(200)
            ->height(300)
            ->sharpen(10);

        $this->addMediaConversion('large')
            ->width(800)
            ->height(1200);
    }

    protected $fillable = [
        'name',
        'slug',
        'description',
        'sku',
        'weight',
        'price',
        'discount_price',
        // 'stock',
        'category_id',
        'product_type',
        'is_active'
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(ProductReview::class);
    }

    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function stocks()
    {
        return $this->hasMany(ProductStock::class);
    }

    public function stockHistory() //kayaknya double 
    {
        return $this->hasMany(ProductStock::class);
    }

    public function currentStock()
    {
        return $this->hasMany(ProductStock::class, 'product_id')
            ->sum('quantity');
    }

    public function scopeSearch($query, $term)
    {
        return $query->where(function ($query) use ($term) {
            $query->where('name', 'like', "%{$term}%")
                ->orWhere('description', 'like', "%{$term}%")
                ->orWhere('sku', 'like', "%{$term}%");
        });
    }

    public function isSingleProduct(): bool
    {
        return $this->product_type === 'single';
    }

    public function isVariantProduct(): bool
    {
        return $this->product_type === 'variant';
    }
}
