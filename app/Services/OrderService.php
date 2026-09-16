<?php

namespace App\Services;

use App\Models\CouponUsage;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Services\Payment\PaymentGatewayFactory;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderService
{
    protected CartService $cartService;
    protected InventoryService $inventoryService;

    public function __construct(CartService $cartService, InventoryService $inventoryService)
    {
        $this->cartService = $cartService;
        $this->inventoryService = $inventoryService;
    }

    public function generateOrderNumber(): string
    {
        $year = date('Y');
        $latestOrder = Order::withTrashed()
            ->where('order_number', 'like', "ORD-{$year}-%")
            ->orderByDesc('id')
            ->first();

        $nextSequence = 1;
        if ($latestOrder) {
            $parts = explode('-', $latestOrder->order_number);
            if (isset($parts[2])) {
                $nextSequence = ((int) $parts[2]) + 1;
            }
        }

        return sprintf("ORD-%s-%06d", $year, $nextSequence);
    }

    public function createOrder(array $data): array
    {
        $summary = $this->cartService->getSummary();
        $cart = $summary['cart'];

        if ($cart->items->isEmpty()) {
            throw new Exception("Your cart is empty. Please add items before checking out.");
        }

        return DB::transaction(function () use ($data, $summary, $cart) {
            $user = Auth::user();

            // 1. Verify Stock for every item using lock
            foreach ($cart->items as $item) {
                if ($item->variant_id) {
                    $variant = ProductVariant::lockForUpdate()->find($item->variant_id);
                    if (!$variant || $variant->stock_quantity < $item->quantity) {
                        throw new Exception("Variant for '{$item->product->name}' is out of stock or insufficient.");
                    }
                } else {
                    $product = Product::lockForUpdate()->find($item->product_id);
                    if (!$product || $product->stock_quantity < $item->quantity) {
                        throw new Exception("Product '{$item->product->name}' is out of stock or insufficient.");
                    }
                }
            }

            // 2. Generate Order Record
            $orderNumber = $this->generateOrderNumber();
            $order = Order::create([
                'order_number' => $orderNumber,
                'user_id' => $user ? $user->id : null,
                'subtotal' => $summary['subtotal'],
                'discount_amount' => $summary['discount'],
                'coupon_code' => $cart->coupon_code,
                'tax_amount' => $summary['tax'],
                'shipping_amount' => $summary['shipping'],
                'grand_total' => $summary['grand_total'],
                'payment_method' => $data['payment_method'],
                'payment_status' => 'pending',
                'order_status' => 'pending',
                'shipping_address' => $data['shipping_address'],
                'billing_address' => $data['billing_address'] ?? $data['shipping_address'],
                'customer_notes' => $data['customer_notes'] ?? null,
            ]);

            // 3. Create Order Items
            foreach ($cart->items as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'variant_id' => $item->variant_id,
                    'product_name' => $item->product->name,
                    'sku' => $item->variant ? ($item->variant->sku ?? $item->product->sku) : $item->product->sku,
                    'variant_name' => $item->variant ? $item->variant->variant_name : null,
                    'unit_price' => $item->price,
                    'quantity' => $item->quantity,
                    'subtotal' => $item->subtotal,
                ]);
            }

            // 4. Record Coupon Usage
            if ($summary['coupon'] && $user) {
                CouponUsage::create([
                    'coupon_id' => $summary['coupon']->id,
                    'user_id' => $user->id,
                    'order_id' => $order->id,
                ]);
                $summary['coupon']->increment('times_used');
            }

            // 5. Decrement Stock
            $this->inventoryService->decrementForOrder($order);

            // 6. Process Payment Gateway
            $gateway = PaymentGatewayFactory::make($data['payment_method']);
            $paymentResult = $gateway->process($order, $data);

            // 7. Clear the cart
            $this->cartService->clearCart();

            return [
                'success' => true,
                'order' => $order->load('items'),
                'payment_result' => $paymentResult,
            ];
        });
    }
}