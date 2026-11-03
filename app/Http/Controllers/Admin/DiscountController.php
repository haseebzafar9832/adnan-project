<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DiscountTier;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class DiscountController extends Controller
{
    public function index()
    {
        $tiers = DiscountTier::query()->with('product')->orderBy('product_id')->orderBy('min_quantity')->get();
        $products = Product::query()->orderBy('name')->get();

        return view('admin.discounts.index', compact('tiers', 'products'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:80'],
            'product_id' => ['nullable', 'exists:products,id'],
            'min_quantity' => ['required', 'integer', 'min:2', 'max:100000'],
            'discount_percent' => ['required', 'numeric', 'min:1', 'max:80'],
        ]);

        $productId = $request->input('product_id') ?: null;
        $query = DiscountTier::query()->where('min_quantity', $data['min_quantity']);
        $productId ? $query->where('product_id', $productId) : $query->whereNull('product_id');

        if ($query->exists()) {
            throw ValidationException::withMessages([
                'min_quantity' => 'A tier with this quantity already exists for that scope.',
            ]);
        }

        DiscountTier::query()->create([
            'name' => $data['name'],
            'product_id' => $productId,
            'min_quantity' => $data['min_quantity'],
            'discount_percent' => $data['discount_percent'],
        ]);

        return back()->with('success', 'Discount tier saved.');
    }

    public function destroy(DiscountTier $discount)
    {
        $discount->delete();

        return back()->with('success', 'Discount tier removed.');
    }
}
