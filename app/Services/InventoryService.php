<?php

namespace App\Services;

use App\Models\InventoryLog;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\Auth;

class InventoryService
{
    public function decrementForOrder(Order $order): void
    {
        foreach ($order->items as $item) {
            if ($item->variant_id) {
                $variant = ProductVariant::lockForUpdate()->find($item->variant_id);
                if ($variant) {
                    $before = $variant->stock_quantity;
                    $variant->decrement('stock_quantity', $item->quantity);
                    $after = $variant->fresh()->stock_quantity;

                    InventoryLog::create([
                        'product_id' => $item->product_id,
                        'variant_id' => $item->variant_id,
                        'user_id' => $order->user_id,
                        'type' => 'order_sale',
                        'quantity_change' => -$item->quantity,
                        'quantity_before' => $before,
                        'quantity_after' => $after,
                        'note' => "Order #{$order->order_number} placed.",
                    ]);
                }
            }

            $product = Product::lockForUpdate()->find($item->product_id);
            if ($product) {
                $before = $product->stock_quantity;
                $product->decrement('stock_quantity', $item->quantity);
                $after = $product->fresh()->stock_quantity;

                InventoryLog::create([
                    'product_id' => $item->product_id,
                    'variant_id' => $item->variant_id,
                    'user_id' => $order->user_id,
                    'type' => 'order_sale',
                    'quantity_change' => -$item->quantity,
                    'quantity_before' => $before,
                    'quantity_after' => $after,
                    'note' => "Order #{$order->order_number} placed.",
                ]);
            }
        }
    }

    public function restoreForOrder(Order $order, string $reason = 'Order cancelled'): void
    {
        foreach ($order->items as $item) {
            if ($item->variant_id) {
                $variant = ProductVariant::lockForUpdate()->find($item->variant_id);
                if ($variant) {
                    $before = $variant->stock_quantity;
                    $variant->increment('stock_quantity', $item->quantity);
                    $after = $variant->fresh()->stock_quantity;

                    InventoryLog::create([
                        'product_id' => $item->product_id,
                        'variant_id' => $item->variant_id,
                        'user_id' => Auth::id(),
                        'type' => 'order_cancel',
                        'quantity_change' => $item->quantity,
                        'quantity_before' => $before,
                        'quantity_after' => $after,
                        'note' => "{$reason} for Order #{$order->order_number}",
                    ]);
                }
            }

            $product = Product::lockForUpdate()->find($item->product_id);
            if ($product) {
                $before = $product->stock_quantity;
                $product->increment('stock_quantity', $item->quantity);
                $after = $product->fresh()->stock_quantity;

                InventoryLog::create([
                    'product_id' => $item->product_id,
                    'variant_id' => $item->variant_id,
                    'user_id' => Auth::id(),
                    'type' => 'order_cancel',
                    'quantity_change' => $item->quantity,
                    'quantity_before' => $before,
                    'quantity_after' => $after,
                    'note' => "{$reason} for Order #{$order->order_number}",
                ]);
            }
        }
    }

    public function adjustStock(int $productId, int $quantityChange, string $type = 'manual_adjustment', ?string $note = null, ?int $variantId = null): array
    {
        $product = Product::findOrFail($productId);

        if ($variantId) {
            $variant = ProductVariant::findOrFail($variantId);
            $before = $variant->stock_quantity;
            $newQuantity = max(0, $before + $quantityChange);
            $actualChange = $newQuantity - $before;
            $variant->update(['stock_quantity' => $newQuantity]);

            InventoryLog::create([
                'product_id' => $productId,
                'variant_id' => $variantId,
                'user_id' => Auth::id(),
                'type' => $type,
                'quantity_change' => $actualChange,
                'quantity_before' => $before,
                'quantity_after' => $newQuantity,
                'note' => $note ?? ucfirst(str_replace('_', ' ', $type)),
            ]);

            return ['success' => true, 'new_stock' => $newQuantity];
        }

        $before = $product->stock_quantity;
        $newQuantity = max(0, $before + $quantityChange);
        $actualChange = $newQuantity - $before;
        $product->update(['stock_quantity' => $newQuantity]);

        InventoryLog::create([
            'product_id' => $productId,
            'variant_id' => null,
            'user_id' => Auth::id(),
            'type' => $type,
            'quantity_change' => $actualChange,
            'quantity_before' => $before,
            'quantity_after' => $newQuantity,
            'note' => $note ?? ucfirst(str_replace('_', ' ', $type)),
        ]);

        return ['success' => true, 'new_stock' => $newQuantity];
    }
}