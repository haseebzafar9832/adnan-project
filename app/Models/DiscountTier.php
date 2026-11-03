<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DiscountTier extends Model
{
    protected $fillable = [
        'product_id',
        'name',
        'min_quantity',
        'discount_percent',
    ];

    protected function casts(): array
    {
        return [
            'discount_percent' => 'decimal:2',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function appliesTo(): string
    {
        return $this->product_id ? ($this->product->name ?? 'This product') : 'All products';
    }
}
