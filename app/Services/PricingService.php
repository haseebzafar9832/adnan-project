<?php

namespace App\Services;

use App\Models\DiscountTier;
use App\Models\Product;
use Illuminate\Support\Collection;

class PricingService
{
    /** @var array<string, Collection<int, DiscountTier>> */
    private array $cache = [];

    public function tiersFor(Product $product): Collection
    {
        $key = 'product:'.$product->id;

        if (! isset($this->cache[$key])) {
            $specific = DiscountTier::query()
                ->where('product_id', $product->id)
                ->orderBy('min_quantity')
                ->get();

            $this->cache[$key] = $specific->isNotEmpty() ? $specific : $this->globalTiers();
        }

        return $this->cache[$key];
    }

    public function globalTiers(): Collection
    {
        return $this->cache['global'] ??= DiscountTier::query()
            ->whereNull('product_id')
            ->orderBy('min_quantity')
            ->get();
    }

    public function quote(Product $product, int $totalQuantity, int $personalQuantity = 1): array
    {
        $totalQuantity = max(0, $totalQuantity);
        $personalQuantity = max(0, $personalQuantity);
        $tiers = $this->tiersFor($product);
        $applied = null;
        $next = null;

        foreach ($tiers as $tier) {
            if ($totalQuantity >= $tier->min_quantity) {
                $applied = $tier;
            } elseif ($next === null) {
                $next = $tier;
            }
        }

        $percent = $applied ? (float) $applied->discount_percent : 0.0;
        $base = (float) $product->base_price;
        $unit = round($base * (1 - ($percent / 100)), 2);

        return [
            'base_price' => $base,
            'unit_price' => $unit,
            'discount_percent' => $percent,
            'tier_name' => $applied?->name,
            'applied_tier_id' => $applied?->id,
            'unit' => $product->unit,
            'total_quantity' => $totalQuantity,
            'personal_quantity' => $personalQuantity,
            'line_total' => round($unit * $personalQuantity, 2),
            'savings' => round(($base - $unit) * $personalQuantity, 2),
            'next_tier' => $next ? [
                'name' => $next->name,
                'min_quantity' => (int) $next->min_quantity,
                'discount_percent' => (float) $next->discount_percent,
                'units_needed' => max(0, $next->min_quantity - $totalQuantity),
                'unit_price' => round($base * (1 - ((float) $next->discount_percent / 100)), 2),
            ] : null,
        ];
    }
}
