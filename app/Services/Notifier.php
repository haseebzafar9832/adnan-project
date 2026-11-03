<?php

namespace App\Services;

use App\Models\AppNotification;
use App\Models\User;

class Notifier
{
    public function to(?User $user, string $title, string $body, string $type = 'info', ?string $link = null): void
    {
        if (! $user) {
            return;
        }

        AppNotification::query()->create([
            'user_id' => $user->id,
            'title' => $title,
            'body' => $body,
            'type' => $type,
            'link' => $link,
        ]);
    }
}
