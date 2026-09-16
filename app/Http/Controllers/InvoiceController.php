<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InvoiceController extends Controller
{
    public function show(string $orderNumber)
    {
        $order = Order::where('order_number', $orderNumber)->with(['items', 'payments', 'user'])->firstOrFail();

        // Customer can only view their own order invoice; admin can view any
        if (Auth::check() && !Auth::user()->isAdmin() && $order->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to invoice.');
        }

        $store = [
            'name' => Setting::get('store_name', 'AuraCart Luxury E-Commerce'),
            'email' => Setting::get('store_email', 'support@auracart.com'),
            'phone' => Setting::get('store_phone', '+91 98765 43210'),
            'address' => Setting::get('store_address', '101 Horizon Towers, Bengaluru, Karnataka, 560001, India'),
            'gst' => Setting::get('store_gst', '29ABCDE1234F1Z5'),
        ];

        return view('orders.invoice', compact('order', 'store'));
    }
}