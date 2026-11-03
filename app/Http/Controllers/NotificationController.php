<?php

namespace App\Http\Controllers;

use App\Models\AppNotification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $notifications = $request->user()->alerts()->latest()->paginate(12);

        return view('notifications.index', compact('notifications'));
    }

    public function count(Request $request)
    {
        $count = $request->user()->alerts()->whereNull('read_at')->count();

        return response()->json(['count' => $count]);
    }

    public function readAll(Request $request)
    {
        $request->user()->alerts()->whereNull('read_at')->update(['read_at' => now()]);

        return back()->with('success', 'All notifications marked as read.');
    }

    public function open(Request $request, AppNotification $notification)
    {
        abort_unless($notification->user_id === $request->user()->id, 403);

        if ($notification->read_at === null) {
            $notification->forceFill(['read_at' => now()])->save();
        }

        $link = (string) $notification->link;

        if ($link !== '' && (str_starts_with($link, '/') || str_starts_with($link, url('/')))) {
            return redirect($link);
        }

        return redirect()->route('notifications.index');
    }
}
