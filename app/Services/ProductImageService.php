<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Laravel\Facades\Image;

class ProductImageService
{
    public const MAX_IMAGES = 20;

    public function storeUploads(Product $product, array $files): void
    {
        $currentCount = $product->images()->count();
        $remaining = self::MAX_IMAGES - $currentCount;

        if ($remaining <= 0) {
            return;
        }

        $sort = (int) $product->images()->max('sort_order');
        $hasCover = $product->images()->where('is_cover', true)->exists();

        foreach (array_slice($files, 0, $remaining) as $file) {
            if (! $file instanceof UploadedFile || ! $file->isValid()) {
                continue;
            }

            $sort++;
            $paths = $this->processAndStore($file, $product->id);

            ProductImage::query()->create([
                'product_id' => $product->id,
                'path' => $paths['path'],
                'thumbnail_path' => $paths['thumbnail'],
                'alt' => $product->name,
                'sort_order' => $sort,
                'is_cover' => ! $hasCover,
            ]);

            $hasCover = true;
        }
    }

    /**
     * Import an existing file from disk (legacy migration).
     */
    public function importFromPath(Product $product, string $absolutePath, int $sortOrder, bool $isCover): ?ProductImage
    {
        if (! is_file($absolutePath)) {
            return null;
        }

        $extension = strtolower(pathinfo($absolutePath, PATHINFO_EXTENSION) ?: 'jpg');
        $basename = Str::slug($product->slug).'-'.Str::random(8);
        $directory = 'products/'.$product->id;
        $path = "{$directory}/{$basename}.{$extension}";
        $thumbPath = "{$directory}/thumbs/{$basename}.{$extension}";

        Storage::disk('public')->makeDirectory($directory.'/thumbs');

        $image = Image::read($absolutePath);
        $image->scaleDown(width: 1600);
        Storage::disk('public')->put($path, (string) $image->encodeByExtension($extension, quality: 85));

        $thumb = Image::read($absolutePath);
        $thumb->cover(400, 500);
        Storage::disk('public')->put($thumbPath, (string) $thumb->encodeByExtension($extension, quality: 80));

        return ProductImage::query()->create([
            'product_id' => $product->id,
            'path' => $path,
            'thumbnail_path' => $thumbPath,
            'alt' => $product->name,
            'sort_order' => $sortOrder,
            'is_cover' => $isCover,
        ]);
    }

    public function deleteImage(ProductImage $image): void
    {
        $productId = $image->product_id;
        $wasCover = $image->is_cover;

        Storage::disk('public')->delete(array_filter([$image->path, $image->thumbnail_path]));
        $image->delete();

        if ($wasCover) {
            $next = ProductImage::query()
                ->where('product_id', $productId)
                ->orderBy('sort_order')
                ->first();

            if ($next) {
                $next->update(['is_cover' => true]);
            }
        }
    }

    public function reorder(Product $product, array $orderedIds): void
    {
        DB::transaction(function () use ($product, $orderedIds): void {
            foreach ($orderedIds as $index => $id) {
                ProductImage::query()
                    ->where('product_id', $product->id)
                    ->where('id', $id)
                    ->update([
                        'sort_order' => $index + 1,
                        'is_cover' => $index === 0,
                    ]);
            }
        });
    }

    /**
     * @return array{path: string, thumbnail: string}
     */
    protected function processAndStore(UploadedFile $file, int $productId): array
    {
        $extension = strtolower($file->getClientOriginalExtension() ?: 'jpg');
        if ($extension === 'jpeg') {
            $extension = 'jpg';
        }

        $basename = Str::random(16);
        $directory = 'products/'.$productId;
        $path = "{$directory}/{$basename}.{$extension}";
        $thumbPath = "{$directory}/thumbs/{$basename}.{$extension}";

        Storage::disk('public')->makeDirectory($directory.'/thumbs');

        $image = Image::read($file->getRealPath());
        $image->scaleDown(width: 1600);
        Storage::disk('public')->put($path, (string) $image->encodeByExtension($extension, quality: 85));

        $thumb = Image::read($file->getRealPath());
        $thumb->cover(400, 500);
        Storage::disk('public')->put($thumbPath, (string) $thumb->encodeByExtension($extension, quality: 80));

        return ['path' => $path, 'thumbnail' => $thumbPath];
    }
}
