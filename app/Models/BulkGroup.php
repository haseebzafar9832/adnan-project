<?php

namespace App\Models;

use App\Services\PricingService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BulkGroup extends Model
{
    protected $fillable = [
        'product_id',
        'user_id',
        'title',
        'description',
        'target_quantity',
        'current_quantity',
        'deadline',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'deadline' => 'datetime',
        ];
    }

    private ?array $quoteCache = null;

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function members(): HasMany
    {
        return $this->hasMany(GroupMember::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(GroupMessage::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function isJoinable(): bool
    {
        return in_array($this->status, ['open', 'reached'], true)
            && $this->deadline
            && $this->deadline->isFuture();
    }

    public function progressPercent(): int
    {
        if ($this->target_quantity < 1) {
            return 0;
        }

        return (int) min(100, round(($this->current_quantity / $this->target_quantity) * 100));
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            'open' => 'Open',
            'reached' => 'Target reached',
            'closed' => 'Closed',
            'fulfilled' => 'Fulfilled',
            'cancelled' => 'Cancelled',
            default => ucfirst((string) $this->status),
        };
    }

    public function liveQuote(): array
    {
        if ($this->quoteCache === null) {
            $this->quoteCache = app(PricingService::class)->quote(
                $this->product,
                (int) $this->current_quantity
            );
        }

        return $this->quoteCache;
    }
}
