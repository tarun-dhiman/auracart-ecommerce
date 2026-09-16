<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function store(Request $request, int $productId)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'title' => 'nullable|string|max:150',
            'comment' => 'required|string|min:10|max:1000',
        ]);

        $user = Auth::user();
        $product = Product::findOrFail($productId);

        // Check verified purchase
        $isVerified = Order::where('user_id', $user->id)
            ->where('order_status', 'delivered')
            ->whereHas('items', fn($q) => $q->where('product_id', $product->id))
            ->exists();

        Review::create([
            'product_id' => $product->id,
            'user_id' => $user->id,
            'rating' => $request->rating,
            'title' => $request->title,
            'comment' => $request->comment,
            'is_verified_purchase' => $isVerified,
            'status' => 'approved', // Auto-publish with admin moderation capability
        ]);

        return redirect()->back()->with('success', 'Thank you! Your review has been submitted.');
    }
}