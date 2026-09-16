<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::active()->with(['category', 'brand', 'images']);

        if ($request->filled('q')) {
            $term = '%' . $request->q . '%';
            $query->where('name', 'like', $term)->orWhere('sku', 'like', $term);
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        $products = $query->paginate($request->get('per_page', 12));

        return response()->json([
            'success' => true,
            'data' => $products,
        ]);
    }

    public function show(int $id)
    {
        $product = Product::active()
            ->with(['category', 'brand', 'images', 'variants', 'approvedReviews.user'])
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $product,
        ]);
    }
}