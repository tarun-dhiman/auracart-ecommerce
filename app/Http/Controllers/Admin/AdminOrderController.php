<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\InventoryService;
use Illuminate\Http\Request;

class AdminOrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with('user');

        if ($request->filled('q')) {
            $term = '%' . $request->q . '%';
            $query->where('order_number', 'like', $term)
                  ->orWhere('shipping_address->full_name', 'like', $term)
                  ->orWhere('shipping_address->email', 'like', $term)
                  ->orWhere('shipping_address->phone', 'like', $term);
        }

        if ($request->filled('status')) {
            $query->where('order_status', $request->status);
        }

        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        $orders = $query->latest()->paginate(15)->withQueryString();

        return view('admin.orders.index', compact('orders'));
    }

    public function show(string $orderNumber)
    {
        $order = Order::where('order_number', $orderNumber)
            ->with(['items.product.images', 'payments', 'user'])
            ->firstOrFail();

        return view('admin.orders.show', compact('order'));
    }

    public function updateStatus(Request $request, string $orderNumber, InventoryService $inventoryService)
    {
        $order = Order::where('order_number', $orderNumber)->firstOrFail();

        $request->validate([
            'order_status' => 'required|in:pending,confirmed,processing,shipped,out_for_delivery,delivered,cancelled,returned,refunded',
            'payment_status' => 'required|in:pending,paid,failed,refunded',
            'tracking_number' => 'nullable|string|max:100',
            'carrier_name' => 'nullable|string|max:100',
            'admin_notes' => 'nullable|string',
        ]);

        $oldStatus = $order->order_status;
        $newStatus = $request->order_status;

        // If newly cancelled or refunded, restore stock
        if (in_array($newStatus, ['cancelled', 'returned', 'refunded']) && !in_array($oldStatus, ['cancelled', 'returned', 'refunded'])) {
            $inventoryService->restoreForOrder($order, "Admin changed status to {$newStatus}");
        }

        $order->update([
            'order_status' => $newStatus,
            'payment_status' => $request->payment_status,
            'tracking_number' => $request->tracking_number,
            'carrier_name' => $request->carrier_name,
            'admin_notes' => $request->admin_notes,
        ]);

        return redirect()->back()->with('success', 'Order #' . $order->order_number . ' updated successfully.');
    }
}