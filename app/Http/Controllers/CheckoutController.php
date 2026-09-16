<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\Order;
use App\Services\CartService;
use App\Services\OrderService;
use App\Services\Payment\PaymentGatewayFactory;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckoutController extends Controller
{
    protected CartService $cartService;
    protected OrderService $orderService;

    public function __construct(CartService $cartService, OrderService $orderService)
    {
        $this->cartService = $cartService;
        $this->orderService = $orderService;
    }

    public function index()
    {
        $summary = $this->cartService->getSummary();
        if ($summary['cart']->items->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        $user = Auth::user();
        $savedAddresses = $user ? $user->addresses : collect();
        $defaultAddress = $user ? $user->defaultShippingAddress : null;

        return view('checkout.index', compact('summary', 'savedAddresses', 'defaultAddress'));
    }

    public function process(Request $request)
    {
        $request->validate([
            'payment_method' => 'required|in:cod,razorpay,stripe',
            'full_name' => 'required|string|max:100',
            'email' => 'required|email|max:100',
            'phone' => 'required|string|max:20',
            'address_line1' => 'required|string|max:255',
            'address_line2' => 'nullable|string|max:255',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'pincode' => 'required|string|max:20',
            'country' => 'required|string|max:100',
            'save_address' => 'nullable|boolean',
        ]);

        $shippingAddress = [
            'full_name' => $request->full_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'address_line1' => $request->address_line1,
            'address_line2' => $request->address_line2,
            'city' => $request->city,
            'state' => $request->state,
            'pincode' => $request->pincode,
            'country' => $request->country,
        ];

        if (Auth::check() && $request->boolean('save_address')) {
            Address::create([
                'user_id' => Auth::id(),
                'full_name' => $request->full_name,
                'phone' => $request->phone,
                'email' => $request->email,
                'address_line1' => $request->address_line1,
                'address_line2' => $request->address_line2,
                'city' => $request->city,
                'state' => $request->state,
                'pincode' => $request->pincode,
                'country' => $request->country,
                'type' => 'shipping',
                'is_default' => Auth::user()->addresses()->count() === 0,
            ]);
        }

        try {
            $result = $this->orderService->createOrder([
                'payment_method' => $request->payment_method,
                'shipping_address' => $shippingAddress,
                'customer_notes' => $request->customer_notes,
            ]);

            $order = $result['order'];
            $paymentResult = $result['payment_result'];

            // Handle Payment Gateway responses
            if ($request->payment_method === 'cod') {
                return redirect()->route('checkout.success', $order->order_number)
                    ->with('success', 'Thank you! Your order has been placed successfully.');
            }

            // For Razorpay / Stripe, render intermediate payment gateway view or handle client modal
            return view('checkout.gateway-pay', [
                'order' => $order,
                'payment_method' => $request->payment_method,
                'payload' => $paymentResult,
            ]);

        } catch (Exception $e) {
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function verifyPayment(Request $request, string $orderNumber)
    {
        $order = Order::where('order_number', $orderNumber)->firstOrFail();
        $gateway = PaymentGatewayFactory::make($order->payment_method);
        $result = $gateway->verify($request, $order);

        if ($result['success']) {
            return redirect()->route('checkout.success', $order->order_number)
                ->with('success', 'Payment successful! Your order has been confirmed.');
        }

        return redirect()->route('customer.orders.show', $order->order_number)
            ->with('error', 'Payment verification failed or was cancelled.');
    }

    public function success(string $orderNumber)
    {
        $order = Order::where('order_number', $orderNumber)->with(['items.product', 'payments'])->firstOrFail();
        return view('checkout.success', compact('order'));
    }
}