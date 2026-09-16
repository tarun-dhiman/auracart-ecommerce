<?php

namespace App\Http\Controllers;

use App\Services\CartService;
use Illuminate\Http\Request;

class CartController extends Controller
{
    protected CartService $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    public function index()
    {
        $summary = $this->cartService->getSummary();
        return view('cart.index', compact('summary'));
    }

    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|integer|exists:products,id',
            'quantity' => 'nullable|integer|min:1',
            'variant_id' => 'nullable|integer|exists:product_variants,id',
        ]);

        $result = $this->cartService->addItem(
            $request->product_id,
            $request->get('quantity', 1),
            $request->variant_id
        );

        if ($request->wantsJson() || $request->ajax()) {
            $summary = $this->cartService->getSummary();
            return response()->json([
                'success' => $result['success'],
                'message' => $result['message'],
                'cart_count' => $summary['items_count'],
                'subtotal' => $summary['subtotal'],
            ], $result['success'] ? 200 : 422);
        }

        return redirect()->back()->with($result['success'] ? 'success' : 'error', $result['message']);
    }

    public function update(Request $request, int $id)
    {
        $request->validate([
            'quantity' => 'required|integer|min:0',
        ]);

        $result = $this->cartService->updateQuantity($id, $request->quantity);

        if ($request->wantsJson() || $request->ajax()) {
            $summary = $this->cartService->getSummary();
            return response()->json([
                'success' => $result['success'],
                'message' => $result['message'],
                'summary' => $summary,
            ], $result['success'] ? 200 : 422);
        }

        return redirect()->route('cart.index')->with($result['success'] ? 'success' : 'error', $result['message']);
    }

    public function remove(Request $request, int $id)
    {
        $result = $this->cartService->removeItem($id);

        if ($request->wantsJson() || $request->ajax()) {
            $summary = $this->cartService->getSummary();
            return response()->json([
                'success' => $result['success'],
                'message' => $result['message'],
                'cart_count' => $summary['items_count'],
                'summary' => $summary,
            ]);
        }

        return redirect()->route('cart.index')->with('success', 'Item removed from cart.');
    }

    public function applyCoupon(Request $request)
    {
        $request->validate(['code' => 'required|string|max:50']);
        $result = $this->cartService->applyCoupon($request->code);

        if ($request->wantsJson() || $request->ajax()) {
            $summary = $this->cartService->getSummary();
            return response()->json([
                'success' => $result['success'],
                'message' => $result['message'],
                'summary' => $summary,
            ], $result['success'] ? 200 : 422);
        }

        return redirect()->back()->with($result['success'] ? 'success' : 'error', $result['message']);
    }

    public function removeCoupon(Request $request)
    {
        $result = $this->cartService->removeCoupon();

        if ($request->wantsJson() || $request->ajax()) {
            $summary = $this->cartService->getSummary();
            return response()->json([
                'success' => true,
                'message' => 'Coupon removed.',
                'summary' => $summary,
            ]);
        }

        return redirect()->back()->with('success', 'Coupon removed.');
    }

    public function drawer()
    {
        $summary = $this->cartService->getSummary();
        return response()->json([
            'items_count' => $summary['items_count'],
            'subtotal' => $summary['subtotal'],
            'grand_total' => $summary['grand_total'],
            'items' => $summary['cart']->items->map(fn($item) => [
                'id' => $item->id,
                'name' => $item->product->name,
                'variant' => $item->variant ? $item->variant->variant_name : null,
                'price' => '₹' . number_format($item->price, 2),
                'quantity' => $item->quantity,
                'image' => $item->product->primary_image_url,
                'url' => route('products.show', $item->product->slug),
            ]),
        ]);
    }
}