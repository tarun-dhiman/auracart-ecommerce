<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\Order;
use App\Services\InventoryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class CustomerDashboardController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();
        $totalOrders = $user->orders()->count();
        $pendingOrders = $user->orders()->whereIn('order_status', ['pending', 'confirmed', 'processing', 'shipped', 'out_for_delivery'])->count();
        $completedOrders = $user->orders()->where('order_status', 'delivered')->count();
        $wishlistCount = $user->wishlistItems()->count();

        $recentOrders = $user->orders()->with('items.product')->take(5)->get();

        return view('customer.dashboard', compact(
            'user',
            'totalOrders',
            'pendingOrders',
            'completedOrders',
            'wishlistCount',
            'recentOrders'
        ));
    }

    public function orders(Request $request)
    {
        $user = Auth::user();
        $query = $user->orders()->with('items.product.images');

        if ($request->filled('status')) {
            $query->where('order_status', $request->status);
        }

        $orders = $query->paginate(10)->withQueryString();
        return view('customer.orders.index', compact('orders'));
    }

    public function orderDetails(string $orderNumber)
    {
        $user = Auth::user();
        $order = $user->orders()->where('order_number', $orderNumber)
            ->with(['items.product.images', 'payments'])
            ->firstOrFail();

        return view('customer.orders.show', compact('order'));
    }

    public function cancelOrder(Request $request, string $orderNumber, InventoryService $inventoryService)
    {
        $user = Auth::user();
        $order = $user->orders()->where('order_number', $orderNumber)->firstOrFail();

        if (!$order->canBeCancelled()) {
            return redirect()->back()->with('error', 'This order cannot be cancelled at its current status.');
        }

        $order->update([
            'order_status' => 'cancelled',
            'customer_notes' => $request->get('reason', 'Cancelled by customer.'),
        ]);

        $inventoryService->restoreForOrder($order, 'Cancelled by customer');

        return redirect()->back()->with('success', 'Order #' . $order->order_number . ' has been cancelled.');
    }

    public function profile()
    {
        $user = Auth::user();
        return view('customer.profile', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        $request->validate([
            'name' => 'required|string|max:100',
            'phone' => 'nullable|string|max:20',
            'email' => 'required|email|max:100|unique:users,email,' . $user->id,
        ]);

        $user->update($request->only('name', 'email', 'phone'));

        return redirect()->back()->with('success', 'Profile updated successfully.');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|current_password',
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        $user = Auth::user();
        $user->update(['password' => Hash::make($request->password)]);

        return redirect()->back()->with('success', 'Password changed successfully.');
    }

    public function addresses()
    {
        $user = Auth::user();
        $addresses = $user->addresses()->latest()->get();
        return view('customer.addresses', compact('addresses'));
    }

    public function storeAddress(Request $request)
    {
        $request->validate([
            'full_name' => 'required|string|max:100',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:100',
            'address_line1' => 'required|string|max:255',
            'address_line2' => 'nullable|string|max:255',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'pincode' => 'required|string|max:20',
            'country' => 'required|string|max:100',
            'is_default' => 'nullable|boolean',
        ]);

        $user = Auth::user();
        $isDefault = $request->boolean('is_default') || $user->addresses()->count() === 0;

        if ($isDefault) {
            $user->addresses()->update(['is_default' => false]);
        }

        $user->addresses()->create(array_merge(
            $request->all(),
            ['is_default' => $isDefault]
        ));

        return redirect()->back()->with('success', 'Address added successfully.');
    }

    public function deleteAddress(int $id)
    {
        $user = Auth::user();
        $user->addresses()->where('id', $id)->delete();
        return redirect()->back()->with('success', 'Address deleted.');
    }

    public function setDefaultAddress(int $id)
    {
        $user = Auth::user();
        $user->addresses()->update(['is_default' => false]);
        $user->addresses()->where('id', $id)->update(['is_default' => true]);
        return redirect()->back()->with('success', 'Default address set.');
    }
}