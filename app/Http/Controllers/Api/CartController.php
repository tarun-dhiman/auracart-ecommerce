<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
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
        return response()->json([
            'success' => true,
            'data' => $this->cartService->getSummary(),
        ]);
    }

    public function addItem(Request $request)
    {
        $request->validate([
            'product_id' => 'required|integer|exists:products,id',
            'quantity' => 'nullable|integer|min:1',
            'variant_id' => 'nullable|integer|exists:product_variants,id',
        ]);

        $res = $this->cartService->addItem($request->product_id, $request->get('quantity', 1), $request->variant_id);
        return response()->json($res, $res['success'] ? 200 : 422);
    }
}