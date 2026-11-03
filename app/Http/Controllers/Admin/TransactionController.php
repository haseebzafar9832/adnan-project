<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Services\GroupService;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $payments = Payment::query()
            ->with(['user', 'order.group'])
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->string('status')))
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('admin.transactions.index', compact('payments'));
    }

    public function update(Request $request, Payment $payment, GroupService $groups)
    {
        $data = $request->validate([
            'action' => ['required', 'in:confirm,reject,refund'],
        ]);

        match ($data['action']) {
            'confirm' => $groups->confirmPayment($payment),
            'reject' => $groups->rejectPayment($payment),
            'refund' => $groups->refund($payment),
        };

        return back()->with('success', 'Transaction updated.');
    }
}
