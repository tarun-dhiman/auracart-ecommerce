<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Wishlist;
use App\Services\CartService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WishlistController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $wishlistItems = $user->wishlistItems()->with('product.images')->latest()->paginate(12);
        return view('customer.wishlist', compact('wishlistItems'));
    }

    public function toggle(Request $request, int $productId)
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Please log in to add items to your wishlist.',
                'redirect' => route('login'),
            ], 401);
        }

        $user = Auth::user();
        $exists = Wishlist::where('user_id', $user->id)->where('product_id', $productId)->first();

        if ($exists) {
            $exists->delete();
            $status = 'removed';
            $message = 'Removed from your wishlist.';
        } else {
            Wishlist::create(['user_id' => $user->id, 'product_id' => $productId]);
            $status = 'added';
            $message = 'Saved to your wishlist!';
        }

        $count = Wishlist::where('user_id', $user->id)->count();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'status' => $status,
                'message' => $message,
                'wishlist_count' => $count,
            ]);
        }

        return redirect()->back()->with('success', $message);
    }

    public function moveToCart(Request $request, int $productId, CartService $cartService)
    {
        $user = Auth::user();
        Wishlist::where('user_id', $user->id)->where('product_id', $productId)->delete();

        $result = $cartService->addItem($productId, 1);

        return redirect()->back()->with($result['success'] ? 'success' : 'error', $result['message']);
    }
}