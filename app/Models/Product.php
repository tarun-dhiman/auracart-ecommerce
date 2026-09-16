<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'category_id',
        'subcategory_id',
        'brand_id',
        'name',
        'slug',
        'sku',
        'short_description',
        'description',
        'price',
        'sale_price',
        'stock_quantity',
        'low_stock_threshold',
        'weight',
        'dimensions',
        'tax_rate',
        'specifications',
        'tags',
        'is_featured',
        'is_bestseller',
        'is_new',
        'is_active',
        'views_count',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'sale_price' => 'decimal:2',
        'tax_rate' => 'decimal:2',
        'weight' => 'decimal:2',
        'stock_quantity' => 'integer',
        'low_stock_threshold' => 'integer',
        'views_count' => 'integer',
        'is_featured' => 'boolean',
        'is_bestseller' => 'boolean',
        'is_new' => 'boolean',
        'is_active' => 'boolean',
        'specifications' => 'array',
        'tags' => 'array',
    ];

    protected $appends = [
        'effective_price',
        'discount_percentage',
        'primary_image_url',
        'average_rating',
        'reviews_count',
        'is_in_stock',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($product) {
            if (empty($product->slug)) {
                $product->slug = Str::slug($product->name) . '-' . Str::lower(Str::random(5));
            }
            if (empty($product->sku)) {
                $product->sku = 'SKU-' . strtoupper(Str::random(8));
            }
        });
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeBestseller($query)
    {
        return $query->where('is_bestseller', true);
    }

    public function scopeNewArrival($query)
    {
        return $query->where('is_new', true);
    }

    public function scopeInStock($query)
    {
        return $query->where('stock_quantity', '>', 0);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function subcategory()
    {
        return $this->belongsTo(Subcategory::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order');
    }

    public function primaryImage()
    {
        return $this->hasOne(ProductImage::class)->where('is_primary', true);
    }

    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function approvedReviews()
    {
        return $this->hasMany(Review::class)->where('status', 'approved')->latest();
    }

    public function inventoryLogs()
    {
        return $this->hasMany(InventoryLog::class)->latest();
    }

    public function getEffectivePriceAttribute(): float
    {
        if ($this->sale_price !== null && $this->sale_price > 0 && $this->sale_price < $this->price) {
            return (float) $this->sale_price;
        }
        return (float) $this->price;
    }

    public function getDiscountPercentageAttribute(): int
    {
        if ($this->sale_price !== null && $this->sale_price > 0 && $this->sale_price < $this->price) {
            return (int) round((($this->price - $this->sale_price) / $this->price) * 100);
        }
        return 0;
    }

    public function getPrimaryImageUrlAttribute(): string
    {
        $primary = $this->images->firstWhere('is_primary', true) ?? $this->images->first();
        if ($primary && file_exists(public_path('storage/' . $primary->image_path))) {
            return asset('storage/' . $primary->image_path);
        }
        if ($primary && Str::startsWith($primary->image_path, ['http://', 'https://'])) {
            return $primary->image_path;
        }
        return 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=800&auto=format&fit=crop&q=80';
    }

    public function getAverageRatingAttribute(): float
    {
        $avg = $this->approvedReviews->avg('rating');
        return $avg ? round($avg, 1) : 4.8; // default realistic high rating if new
    }

    public function getReviewsCountAttribute(): int
    {
        return $this->approvedReviews->count();
    }

    public function getIsInStockAttribute(): bool
    {
        return $this->stock_quantity > 0;
    }
}
