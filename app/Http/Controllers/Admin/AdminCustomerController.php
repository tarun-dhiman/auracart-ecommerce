<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class AdminCustomerController extends Controller
{
    public function index(Request $request)
    {
        $query = User::where('role', 'customer')->withCount('orders');

        if ($request->filled('q')) {
            $term = '%' . $request->q . '%';
            $query->where(function ($q) use ($term) {
                $q->where('name', 'like', $term)
                  ->orWhere('email', 'like', $term)
                  ->orWhere('phone', 'like', $term);
            });
        }

        $customers = $query->latest()->paginate(15)->withQueryString();
        return view('admin.customers.index', compact('customers'));
    }

    public function show(int $id)
    {
        $customer = User::where('role', 'customer')->with(['orders.items', 'addresses'])->findOrFail($id);
        $totalSpent = $customer->orders()->where('payment_status', 'paid')->sum('grand_total');

        return view('admin.customers.show', compact('customer', 'totalSpent'));
    }

    public function toggleBlock(int $id)
    {
        $customer = User::where('role', 'customer')->findOrFail($id);
        $customer->is_blocked = !$customer->is_blocked;
        $customer->save();

        $msg = $customer->is_blocked ? 'Customer has been blocked.' : 'Customer account unblocked.';
        return redirect()->back()->with('success', $msg);
    }
}