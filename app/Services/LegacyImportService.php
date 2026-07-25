<?php

namespace App\Services;

use App\Enums\ProductStatus;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class LegacyImportService
{
    public function __construct(
        protected ProductImageService $imageService
    ) {}

    /**
     * Import categories & products from the legacy JSON store.
     *
     * @return array{categories: int, products: int, images: int}
     */
    public function import(string $legacyRoot): array
    {
        $categoriesFile = $legacyRoot.DIRECTORY_SEPARATOR.'data'.DIRECTORY_SEPARATOR.'categories.json';
        $productsFile = $legacyRoot.DIRECTORY_SEPARATOR.'data'.DIRECTORY_SEPARATOR.'products.json';
        $uploadsDir = $legacyRoot.DIRECTORY_SEPARATOR.'public'.DIRECTORY_SEPARATOR.'uploads';

        if (! File::exists($categoriesFile) || ! File::exists($productsFile)) {
            throw new \RuntimeException('Legacy JSON files not found under '.$legacyRoot);
        }

        $categoriesJson = json_decode(File::get($categoriesFile), true) ?: [];
        $productsJson = json_decode(File::get($productsFile), true) ?: [];

        $stats = ['categories' => 0, 'products' => 0, 'images' => 0];

        return DB::transaction(function () use ($categoriesJson, $productsJson, $uploadsDir, &$stats) {
            $categoryMap = [];

            foreach ($categoriesJson as $index => $row) {
                $legacyId = (string) ($row['id'] ?? '');
                $name = trim((string) ($row['label'] ?? $legacyId));

                if ($legacyId === '' || $name === '') {
                    continue;
                }

                $category = Category::query()->updateOrCreate(
                    ['legacy_id' => $legacyId],
                    [
                        'name' => $name,
                        'slug' => Category::uniqueSlug($name),
                        'sort_order' => $index + 1,
                        'is_active' => true,
                        'meta_title' => $name.' | Fashion Corner',
                    ]
                );

                // Keep slug stable on re-import
                if ($category->wasRecentlyCreated === false && empty($category->slug)) {
                    $category->update(['slug' => Category::uniqueSlug($name, $category->id)]);
                }

                $categoryMap[$legacyId] = $category->id;
                $stats['categories']++;
            }

            foreach ($productsJson as $row) {
                $legacyId = (string) ($row['id'] ?? '');
                $name = trim((string) ($row['name'] ?? ''));
                $categoryLegacy = (string) ($row['category'] ?? '');

                if ($legacyId === '' || $name === '' || ! isset($categoryMap[$categoryLegacy])) {
                    continue;
                }

                $price = $this->parsePrice($row['price'] ?? 0);
                $oldPrice = isset($row['originalPrice']) ? $this->parsePrice($row['originalPrice']) : null;

                $existing = Product::query()->where('legacy_id', $legacyId)->first();

                $attributes = [
                    'category_id' => $categoryMap[$categoryLegacy],
                    'name' => $name,
                    'sku' => strtoupper(Str::slug($legacyId, '-')),
                    'description' => $row['description'] ?? null,
                    'short_description' => $row['short_description'] ?? null,
                    'price' => $price,
                    'old_price' => $oldPrice,
                    'quantity' => (int) ($row['quantity'] ?? 50),
                    'featured' => (bool) ($row['featured'] ?? false),
                    'hot_selling' => (bool) ($row['hotSelling'] ?? false),
                    'recommended' => (bool) ($row['youMayAlsoLike'] ?? false),
                    'status' => ProductStatus::Active,
                    'meta_title' => $name.' | Fashion Corner',
                    'meta_description' => Str::limit(strip_tags((string) ($row['description'] ?? $name)), 160),
                ];

                if (! $existing) {
                    $attributes['slug'] = Product::uniqueSlug($name);
                    $attributes['legacy_id'] = $legacyId;
                    $product = Product::query()->create($attributes);
                } else {
                    $existing->update($attributes);
                    $product = $existing->fresh();
                }

                $stats['products']++;

                $images = array_values(array_filter((array) ($row['images'] ?? [])));
                if ($images === []) {
                    continue;
                }

                // Clear existing imported images only when re-importing this product's images
                foreach ($product->images as $existing) {
                    $this->imageService->deleteImage($existing);
                }

                foreach (array_slice($images, 0, ProductImageService::MAX_IMAGES) as $index => $imagePath) {
                    $absolute = $this->resolveImagePath($imagePath, $uploadsDir);
                    if (! $absolute) {
                        continue;
                    }

                    $created = $this->imageService->importFromPath(
                        $product,
                        $absolute,
                        $index + 1,
                        $index === 0
                    );

                    if ($created) {
                        $stats['images']++;
                    }
                }
            }

            return $stats;
        });
    }

    protected function parsePrice(mixed $value): float
    {
        return (float) preg_replace('/[^\d.]/', '', (string) $value);
    }

    protected function resolveImagePath(string $imagePath, string $uploadsDir): ?string
    {
        if (str_starts_with($imagePath, '/uploads/')) {
            $file = $uploadsDir.DIRECTORY_SEPARATOR.basename($imagePath);

            return is_file($file) ? $file : null;
        }

        if (str_starts_with($imagePath, 'http://') || str_starts_with($imagePath, 'https://')) {
            return null; // remote URLs skipped during offline import
        }

        if (is_file($imagePath)) {
            return $imagePath;
        }

        $candidate = $uploadsDir.DIRECTORY_SEPARATOR.ltrim($imagePath, '/\\');

        return is_file($candidate) ? $candidate : null;
    }
}
