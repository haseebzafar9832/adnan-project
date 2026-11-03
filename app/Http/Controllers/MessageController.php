<?php

namespace App\Http\Controllers;

use App\Models\BulkGroup;
use App\Models\GroupMember;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function poll(Request $request, BulkGroup $group)
    {
        $this->memberOrAbort($request, $group);

        $messages = $group->messages()
            ->with('user')
            ->where('id', '>', $request->integer('after'))
            ->orderBy('id')
            ->limit(50)
            ->get()
            ->map(fn ($message) => $this->payload($request, $message));

        return response()->json(['messages' => $messages]);
    }

    public function store(Request $request, BulkGroup $group)
    {
        $this->memberOrAbort($request, $group);

        $data = $request->validate([
            'body' => ['required', 'string', 'max:1000'],
        ]);

        $message = $group->messages()->create([
            'user_id' => $request->user()->id,
            'body' => trim($data['body']),
        ]);
        $message->load('user');

        if ($request->ajax()) {
            return response()->json(['message' => $this->payload($request, $message)]);
        }

        return back();
    }

    private function memberOrAbort(Request $request, BulkGroup $group): void
    {
        $exists = GroupMember::query()
            ->where('bulk_group_id', $group->id)
            ->where('user_id', $request->user()->id)
            ->exists();

        abort_unless($exists, 403);
    }

    private function payload(Request $request, $message): array
    {
        return [
            'id' => $message->id,
            'body' => $message->body,
            'name' => $message->user->name,
            'initials' => $message->user->initials(),
            'tone' => $message->user->tone(),
            'mine' => $message->user_id === $request->user()->id,
            'time' => $message->created_at->diffForHumans(),
        ];
    }
}
