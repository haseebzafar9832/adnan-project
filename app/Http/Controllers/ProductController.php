<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Services\PricingService;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::query()->active()->orderBy('name')->get();
        $products = Product::query()
            ->with('category')
            ->active()
            ->whereHas('category', fn ($query) => $query->active())
            ->when($request->filled('category'), function ($query) use ($request) {
                $query->whereHas('category', fn ($category) => $category->where('slug', $request->string('category')));
            })
            ->when($request->filled('q'), function ($query) use ($request) {
                $term = '%'.trim((string) $request->query('q')).'%';
                $query->where(function ($inner) use ($term) {
                    $inner->where('name', 'like', $term)->orWhere('summary', 'like', $term);
                });
            })
            ->orderBy('name')
            ->paginate(9)
            ->withQueryString();

        if ($request->ajax()) {
            return view('products._grid', compact('products'));
        }

        return view('products.index', compact('products', 'categories'));
    }

    public function show(Product $product, PricingService $pricing)
    {
        abort_unless($product->is_active && $product->category?->is_active, 404);

        $product->load('category');
        $tiers = $pricing->tiersFor($product);
        $groups = $product->groups()
            ->with('creator')
            ->withCount('members')
            ->whereIn('status', ['open', 'reached'])
            ->latest()
            ->get();

        return view('products.show', compact('product', 'tiers', 'groups'));
    }
}
