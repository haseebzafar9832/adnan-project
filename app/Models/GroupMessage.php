<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GroupMessage extends Model
{
    protected $fillable = [
        'bulk_group_id',
        'user_id',
        'body',
    ];

    public function group(): BelongsTo
    {
        return $this->belongsTo(BulkGroup::class, 'bulk_group_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
