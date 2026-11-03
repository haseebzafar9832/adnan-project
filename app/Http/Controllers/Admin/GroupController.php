<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BulkGroup;
use App\Services\GroupService;
use Illuminate\Http\Request;

class GroupController extends Controller
{
    public function index(Request $request)
    {
        $groups = BulkGroup::query()
            ->with(['product', 'creator'])
            ->withCount('members')
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->string('status')))
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('admin.groups.index', compact('groups'));
    }

    public function close(BulkGroup $group, GroupService $groups)
    {
        $groups->close($group);

        return back()->with('success', 'Group closed.');
    }

    public function cancel(BulkGroup $group, GroupService $groups)
    {
        $groups->cancel($group);

        return back()->with('success', 'Group cancelled.');
    }
}
