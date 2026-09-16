<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Coupon;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class CartService
{
    protected function getSessionId(): string
    {
        if (!Session::has('cart_session_id')) {
            Session::put('cart_session_id', Session::getId() . '_' . uniqid());
        }
        return Session::get('cart_session_id');
    }

    public function getCart(): Cart
    {
        $user = Auth::user();
        if ($user) {
            $cart = Cart::firstOrCreate(['user_id' => $user->id]);
        } else {
            $sessionId = $this->getSessionId();
            $cart = Cart::firstOrCreate(['session_id' => $sessionId]);
        }

        return $cart->load(['items.product.images', 'items.variant']);
    }

    public function addItem(int $productId, int $quantity = 1, ?int $variantId = null): array
    {
        $product = Product::active()->find($productId);
        if (!$product) {
            return ['success' => false, 'message' => 'Product not found or unavailable.'];
        }

        $availableStock = $product->stock_quantity;
        $price = $product->effective_price;

        if ($variantId) {
            $variant = ProductVariant::where('product_id', $productId)->find($variantId);
            if ($variant) {
                $availableStock = $variant->stock_quantity;
                if ($variant->price !== null) {
                    $price = (float) $variant->price;
                }
            }
        }

        if ($availableStock < $quantity) {
            return [
                'success' => false,
                'message' => "Only {$availableStock} items currently in stock for this selection.",
            ];
        }

        $cart = $this->getCart();
        $item = $cart->items()
            ->where('product_id', $productId)
            ->where('variant_id', $variantId)
            ->first();

        if ($item) {
            $newQuantity = $item->quantity + $quantity;
            if ($availableStock < $newQuantity) {
                return [
                    'success' => false,
                    'message' => "Cannot add more. Only {$availableStock} items available.",
                ];
            }
            $item->quantity = $newQuantity;
            $item->price = $price;
            $item->save();
        } else {
            $cart->items()->create([
                'product_id' => $productId,
                'variant_id' => $variantId,
                'quantity' => $quantity,
                'price' => $price,
            ]);
        }

        return ['success' => true, 'message' => 'Product added to cart successfully!'];
    }

    public function updateQuantity(int $itemId, int $quantity): array
    {
        if ($quantity <= 0) {
            return $this->removeItem($itemId);
        }

        $cart = $this->getCart();
        $item = $cart->items()->find($itemId);
        if (!$item) {
            return ['success' => false, 'message' => 'Cart item not found.'];
        }

        $availableStock = $item->variant ? $item->variant->stock_quantity : $item->product->stock_quantity;
        if ($quantity > $availableStock) {
            return [
                'success' => false,
                'message' => "Cannot exceed available stock of {$availableStock}.",
            ];
        }

        $item->quantity = $quantity;
        $item->save();

        return ['success' => true, 'message' => 'Cart updated successfully!'];
    }

    public function removeItem(int $itemId): array
    {
        $cart = $this->getCart();
        $item = $cart->items()->find($itemId);
        if ($item) {
            $item->delete();
            return ['success' => true, 'message' => 'Item removed from cart.'];
        }
        return ['success' => false, 'message' => 'Item not found.'];
    }

    public function clearCart(): void
    {
        $cart = $this->getCart();
        $cart->items()->delete();
        $cart->coupon_code = null;
        $cart->save();
    }

    public function applyCoupon(string $code): array
    {
        $coupon = Coupon::where('code', strtoupper(trim($code)))->first();
        if (!$coupon) {
            return ['success' => false, 'message' => 'Invalid coupon code.'];
        }

        $cart = $this->getCart();
        $subtotal = $this->calculateSubtotal($cart);
        $user = Auth::user();

        $validation = $coupon->isValidForAmount($subtotal, $user);
        if (!$validation['valid']) {
            return ['success' => false, 'message' => $validation['message']];
        }

        $cart->coupon_code = $coupon->code;
        $cart->save();

        return ['success' => true, 'message' => 'Coupon code applied successfully!'];
    }

    public function removeCoupon(): array
    {
        $cart = $this->getCart();
        $cart->coupon_code = null;
        $cart->save();

        return ['success' => true, 'message' => 'Coupon removed.'];
    }

    public function calculateSubtotal(Cart $cart): float
    {
        return round($cart->items->sum(fn($item) => $item->price * $item->quantity), 2);
    }

    public function getSummary(): array
    {
        $cart = $this->getCart();
        $subtotal = $this->calculateSubtotal($cart);
        $discount = 0.0;
        $appliedCoupon = null;

        if ($cart->coupon_code) {
            $coupon = Coupon::where('code', $cart->coupon_code)->first();
            if ($coupon) {
                $validation = $coupon->isValidForAmount($subtotal, Auth::user());
                if ($validation['valid']) {
                    $discount = $coupon->calculateDiscount($subtotal);
                    $appliedCoupon = $coupon;
                } else {
                    $cart->coupon_code = null;
                    $cart->save();
                }
            }
        }

        $taxRate = (float) Setting::get('tax_rate', 18.0); // 18% GST standard
        $freeShippingThreshold = (float) Setting::get('free_shipping_threshold', 999.0);
        $flatShipping = (float) Setting::get('shipping_fee', 99.0);

        $afterDiscount = max(0, $subtotal - $discount);
        $shipping = ($afterDiscount >= $freeShippingThreshold || $subtotal == 0) ? 0.0 : $flatShipping;
        $tax = round(($afterDiscount * $taxRate) / 100, 2);
        $grandTotal = round($afterDiscount + $tax + $shipping, 2);

        return [
            'cart' => $cart,
            'items_count' => $cart->items->sum('quantity'),
            'subtotal' => $subtotal,
            'discount' => $discount,
            'coupon' => $appliedCoupon,
            'shipping' => $shipping,
            'free_shipping_threshold' => $freeShippingThreshold,
            'tax_rate' => $taxRate,
            'tax' => $tax,
            'grand_total' => $grandTotal,
        ];
    }

    public function syncSessionCartToDatabase(User $user): void
    {
        $sessionId = Session::get('cart_session_id');
        if (!$sessionId) {
            return;
        }

        $guestCart = Cart::where('session_id', $sessionId)->first();
        if (!$guestCart || $guestCart->items->isEmpty()) {
            return;
        }

        $userCart = Cart::firstOrCreate(['user_id' => $user->id]);

        foreach ($guestCart->items as $guestItem) {
            $existingItem = $userCart->items()
                ->where('product_id', $guestItem->product_id)
                ->where('variant_id', $guestItem->variant_id)
                ->first();

            if ($existingItem) {
                $existingItem->quantity += $guestItem->quantity;
                $existingItem->save();
            } else {
                $guestItem->cart_id = $userCart->id;
                $guestItem->save();
            }
        }

        if ($guestCart->coupon_code && !$userCart->coupon_code) {
            $userCart->coupon_code = $guestCart->coupon_code;
            $userCart->save();
        }

        $guestCart->delete();
        Session::forget('cart_session_id');
    }
}