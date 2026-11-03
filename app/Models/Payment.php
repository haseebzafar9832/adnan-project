<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    protected $fillable = [
        'order_id',
        'user_id',
        'amount',
        'method',
        'reference',
        'payer_hint',
        'status',
        'paid_at',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'paid_at' => 'datetime',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function methodLabel(): string
    {
        return match ($this->method) {
            'card' => 'Debit / Credit card',
            'jazzcash' => 'JazzCash',
            'easypaisa' => 'Easypaisa',
            'bank' => 'Bank transfer',
            default => ucfirst((string) $this->method),
        };
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            'pending' => 'Pending',
            'paid' => 'Paid',
            'refunded' => 'Refunded',
            'failed' => 'Failed',
            default => ucfirst((string) $this->status),
        };
    }
}
