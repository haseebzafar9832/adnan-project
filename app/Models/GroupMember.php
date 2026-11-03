<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GroupMember extends Model
{
    protected $fillable = [
        'bulk_group_id',
        'user_id',
        'quantity',
        'payment_status',
        'locked_unit_price',
        'discount_percent',
    ];

    protected function casts(): array
    {
        return [
            'locked_unit_price' => 'decimal:2',
            'discount_percent' => 'decimal:2',
        ];
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(BulkGroup::class, 'bulk_group_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class)->latest();
    }

    public function paymentLabel(): string
    {
        return match ($this->payment_status) {
            'paid' => 'Paid',
            'refunded' => 'Refunded',
            default => 'Pending',
        };
    }
}
