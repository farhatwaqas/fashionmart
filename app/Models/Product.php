<?php

namespace App\Models;

use App\Enums\ProductStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'sku',
        'legacy_id',
        'description',
        'short_description',
        'price',
        'old_price',
        'quantity',
        'featured',
        'hot_selling',
        'recommended',
        'status',
        'meta_title',
        'meta_description',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'old_price' => 'decimal:2',
            'quantity' => 'integer',
            'featured' => 'boolean',
            'hot_selling' => 'boolean',
            'recommended' => 'boolean',
            'status' => ProductStatus::class,
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Product $product): void {
            if (empty($product->slug)) {
                $product->slug = static::uniqueSlug($product->name);
            }
        });
    }

    public static function uniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $base = Str::slug($name) ?: 'product';
        $slug = $base;
        $i = 2;

        while (static::query()
            ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
            ->where('slug', $slug)
            ->exists()) {
            $slug = "{$base}-{$i}";
            $i++;
        }

        return $slug;
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order');
    }

    public function coverImage(): HasOne
    {
        return $this->hasOne(ProductImage::class)->where('is_cover', true)->orderBy('sort_order');
    }

    public function scopeActive($query)
    {
        return $query->where('status', ProductStatus::Active);
    }

    public function scopeFeatured($query)
    {
        return $query->where('featured', true);
    }

    public function scopeHotSelling($query)
    {
        return $query->where('hot_selling', true);
    }

    public function scopeRecommended($query)
    {
        return $query->where('recommended', true);
    }

    public function scopeLowStock($query, int $threshold = 5)
    {
        return $query->where('quantity', '<=', $threshold)->where('quantity', '>=', 0);
    }

    public function isOnSale(): bool
    {
        return $this->old_price !== null && $this->old_price > $this->price;
    }

    public function formattedPrice(): string
    {
        return 'PKR '.number_format((float) $this->price, 0);
    }

    public function formattedOldPrice(): ?string
    {
        if ($this->old_price === null) {
            return null;
        }

        return 'PKR '.number_format((float) $this->old_price, 0);
    }

    public function coverUrl(): string
    {
        $cover = $this->relationLoaded('coverImage')
            ? $this->coverImage
            : ($this->relationLoaded('images') ? $this->images->firstWhere('is_cover', true) ?? $this->images->first() : $this->coverImage);

        if (! $cover && $this->relationLoaded('images')) {
            $cover = $this->images->first();
        }

        return $cover?->url() ?? asset('images/placeholder-product.svg');
    }

    public function inStock(): bool
    {
        return $this->quantity > 0;
    }
}
