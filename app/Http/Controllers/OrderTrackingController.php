<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class OrderTrackingController extends Controller
{
    public function index()
    {
        return view('orders.track');
    }

    public function track(Request $request)
    {
        $request->validate([
            'order_number' => 'required|string',
            'contact' => 'required|string',
        ]);

        $order = Order::where('order_number', trim($request->order_number))
            ->with(['items.product.images'])
            ->first();

        if (!$order) {
            return redirect()->back()->with('error', 'No order found with that order number.');
        }

        $email = $order->shipping_address['email'] ?? '';
        $phone = $order->shipping_address['phone'] ?? '';
        $contactInput = trim($request->contact);

        if ($contactInput !== $email && $contactInput !== $phone && (!auth()->check() || auth()->id() !== $order->user_id)) {
            return redirect()->back()->with('error', 'Order details did not match the provided email or phone number.');
        }

        return view('orders.track-result', compact('order'));
    }
}