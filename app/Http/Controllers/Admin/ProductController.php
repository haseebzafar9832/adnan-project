<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Support\Slugs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $products = Product::query()
            ->with('category')
            ->when($request->filled('q'), function ($query) use ($request) {
                $term = '%'.trim((string) $request->query('q')).'%';
                $query->where('name', 'like', $term);
            })
            ->orderBy('name')
            ->paginate(12)
            ->withQueryString();

        return view('admin.products.index', compact('products'));
    }

    public function create()
    {
        return view('admin.products.form', [
            'product' => new Product(['unit' => 'piece', 'is_active' => true]),
            'categories' => Category::query()->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['slug'] = Slugs::unique(Product::class, $data['name']);
        $data['image'] = $this->storeImage($request);
        Product::query()->create($data);

        return redirect()->route('admin.products.index')->with('success', 'Product added.');
    }

    public function edit(Product $product)
    {
        return view('admin.products.form', [
            'product' => $product,
            'categories' => Category::query()->orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Product $product)
    {
        $data = $this->validated($request);
        $data['slug'] = Slugs::unique(Product::class, $data['name'], $product->id);
        $image = $this->storeImage($request);

        if ($image) {
            $this->deleteStoredImage($product->image);
            $data['image'] = $image;
        }

        $product->update($data);

        return redirect()->route('admin.products.index')->with('success', 'Product updated.');
    }

    public function destroy(Product $product)
    {
        if ($product->groups()->exists()) {
            $product->update(['is_active' => false]);

            return back()->with('success', 'This product is used by groups, so it was hidden instead of deleted.');
        }

        $this->deleteStoredImage($product->image);

        $product->delete();

        return back()->with('success', 'Product deleted.');
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:140'],
            'summary' => ['required', 'string', 'max:180'],
            'description' => ['required', 'string', 'max:2000'],
            'unit' => ['required', 'string', 'max:30'],
            'base_price' => ['required', 'numeric', 'min:1', 'max:10000000'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'is_active' => ['required', 'boolean'],
        ]);

        unset($data['image']);
        $data['is_active'] = $request->boolean('is_active');

        return $data;
    }

    private function storeImage(Request $request): ?string
    {
        if (! $request->hasFile('image')) {
            return null;
        }

        return $request->file('image')->store('products', 'public');
    }

    private function deleteStoredImage(?string $path): void
    {
        if ($path && ! str_starts_with($path, 'images/')) {
            Storage::disk('public')->delete($path);
        }
    }
}
