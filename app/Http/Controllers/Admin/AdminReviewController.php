<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;

class AdminReviewController extends Controller
{
    public function index(Request $request)
    {
        $query = Review::with(['product', 'user']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $reviews = $query->latest()->paginate(15)->withQueryString();
        return view('admin.reviews.index', compact('reviews'));
    }

    public function updateStatus(Request $request, int $id)
    {
        $request->validate(['status' => 'required|in:approved,rejected,pending']);
        $review = Review::findOrFail($id);
        $review->update(['status' => $request->status]);

        return redirect()->back()->with('success', "Review marked as {$request->status}.");
    }

    public function destroy(int $id)
    {
        $review = Review::findOrFail($id);
        $review->delete();
        return redirect()->back()->with('success', 'Review deleted.');
    }
}