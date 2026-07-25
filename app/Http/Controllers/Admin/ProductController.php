<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ProductStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ProductRequest;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Services\ProductImageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function __construct(
        protected ProductImageService $imageService
    ) {}

    public function index(Request $request): View
    {
        $this->authorize('viewAny', Product::class);

        $products = Product::query()
            ->with(['category', 'coverImage'])
            ->withCount('images')
            ->when($request->filled('q'), function ($q) use ($request) {
                $term = '%'.$request->string('q').'%';
                $q->where(function ($inner) use ($term) {
                    $inner->where('name', 'like', $term)
                        ->orWhere('sku', 'like', $term)
                        ->orWhere('slug', 'like', $term);
                });
            })
            ->when($request->filled('category_id'), fn ($q) => $q->where('category_id', $request->integer('category_id')))
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.products.index', [
            'products' => $products,
            'categories' => Category::query()->orderBy('name')->get(),
            'statuses' => ProductStatus::cases(),
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', Product::class);

        return view('admin.products.create', [
            'categories' => Category::query()->orderBy('name')->get(),
            'statuses' => ProductStatus::cases(),
        ]);
    }

    public function store(ProductRequest $request): RedirectResponse
    {
        $this->authorize('create', Product::class);

        $data = $request->validated();
        unset($data['images']);

        if (empty($data['slug'])) {
            $data['slug'] = Product::uniqueSlug($data['name']);
        }

        $product = Product::query()->create($data);

        if ($request->hasFile('images')) {
            $this->imageService->storeUploads($product, $request->file('images'));
        }

        return redirect()
            ->route('admin.products.edit', $product)
            ->with('success', 'Product created successfully.');
    }

    public function edit(Product $product): View
    {
        $this->authorize('update', $product);

        $product->load('images');

        return view('admin.products.edit', [
            'product' => $product,
            'categories' => Category::query()->orderBy('name')->get(),
            'statuses' => ProductStatus::cases(),
        ]);
    }

    public function update(ProductRequest $request, Product $product): RedirectResponse
    {
        $this->authorize('update', $product);

        $data = $request->validated();
        unset($data['images']);

        if (empty($data['slug'])) {
            $data['slug'] = Product::uniqueSlug($data['name'], $product->id);
        }

        $product->update($data);

        if ($request->hasFile('images')) {
            $this->imageService->storeUploads($product, $request->file('images'));
        }

        return redirect()
            ->route('admin.products.edit', $product)
            ->with('success', 'Product updated successfully.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        $this->authorize('delete', $product);

        foreach ($product->images as $image) {
            $this->imageService->deleteImage($image);
        }

        $product->delete();

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Product deleted.');
    }

    public function destroyImage(Product $product, ProductImage $image): RedirectResponse
    {
        $this->authorize('update', $product);
        abort_unless($image->product_id === $product->id, 404);

        $this->imageService->deleteImage($image);

        return back()->with('success', 'Image removed.');
    }

    public function reorderImages(Request $request, Product $product): RedirectResponse
    {
        $this->authorize('update', $product);

        $validated = $request->validate([
            'order' => ['required', 'array'],
            'order.*' => ['integer', 'exists:product_images,id'],
        ]);

        $this->imageService->reorder($product, $validated['order']);

        return back()->with('success', 'Image order updated.');
    }
}
