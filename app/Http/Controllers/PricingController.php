<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\PricingService;
use Illuminate\Http\Request;

class PricingController extends Controller
{
    public function quote(Request $request, PricingService $pricing)
    {
        $data = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'group_quantity' => ['required', 'integer', 'min:0', 'max:10000'],
            'personal_quantity' => ['nullable', 'integer', 'min:0', 'max:500'],
        ]);

        $product = Product::query()->findOrFail($data['product_id']);
        $personal = (int) ($data['personal_quantity'] ?? 1);

        return response()->json($pricing->quote($product, (int) $data['group_quantity'], $personal));
    }
}
