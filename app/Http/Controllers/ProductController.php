<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\Subcategory;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::active()->with(['category', 'brand', 'images', 'variants']);

        // Search
        if ($request->filled('q')) {
            $term = '%' . $request->q . '%';
            $query->where(function ($q) use ($term) {
                $q->where('name', 'like', $term)
                  ->orWhere('sku', 'like', $term)
                  ->orWhere('short_description', 'like', $term)
                  ->orWhere('description', 'like', $term)
                  ->orWhereHas('category', fn($c) => $c->where('name', 'like', $term))
                  ->orWhereHas('brand', fn($b) => $b->where('name', 'like', $term));
            });
        }

        // Category filter
        if ($request->filled('category')) {
            $cat = Category::where('slug', $request->category)->first();
            if ($cat) {
                $query->where('category_id', $cat->id);
            }
        }

        // Subcategory filter
        if ($request->filled('subcategory')) {
            $sub = Subcategory::where('slug', $request->subcategory)->first();
            if ($sub) {
                $query->where('subcategory_id', $sub->id);
            }
        }

        // Brand filter
        if ($request->filled('brand')) {
            $brands = is_array($request->brand) ? $request->brand : [$request->brand];
            $brandIds = Brand::whereIn('slug', $brands)->pluck('id');
            $query->whereIn('brand_id', $brandIds);
        }

        // Price filter
        if ($request->filled('min_price')) {
            $query->where(function ($q) use ($request) {
                $q->where('sale_price', '>=', $request->min_price)
                  ->orWhere(fn($sq) => $sq->whereNull('sale_price')->where('price', '>=', $request->min_price));
            });
        }
        if ($request->filled('max_price')) {
            $query->where(function ($q) use ($request) {
                $q->where('sale_price', '<=', $request->max_price)
                  ->orWhere(fn($sq) => $sq->whereNull('sale_price')->where('price', '<=', $request->max_price));
            });
        }

        // Availability filter
        if ($request->filled('in_stock') && $request->in_stock == '1') {
            $query->where('stock_quantity', '>', 0);
        }

        // Sorting
        $sort = $request->get('sort', 'newest');
        match ($sort) {
            'price_asc' => $query->orderByRaw('COALESCE(sale_price, price) ASC'),
            'price_desc' => $query->orderByRaw('COALESCE(sale_price, price) DESC'),
            'popular' => $query->orderByDesc('views_count'),
            'bestseller' => $query->where('is_bestseller', true)->latest(),
            default => $query->latest(),
        };

        $products = $query->paginate(12)->withQueryString();

        $categories = Category::active()->withCount('products')->get();
        $brands = Brand::active()->withCount('products')->get();

        return view('products.index', compact('products', 'categories', 'brands', 'sort'));
    }

    public function show(string $slug)
    {
        $product = Product::active()
            ->where('slug', $slug)
            ->with(['category', 'subcategory', 'brand', 'images', 'variants', 'approvedReviews.user'])
            ->firstOrFail();

        $product->increment('views_count');

        // Related products in same category
        $relatedProducts = Product::active()
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->take(4)
            ->get();

        // Recently viewed tracking via session
        $recentIds = session()->get('recently_viewed', []);
        if (!in_array($product->id, $recentIds)) {
            array_unshift($recentIds, $product->id);
            $recentIds = array_slice($recentIds, 0, 6);
            session()->put('recently_viewed', $recentIds);
        }

        $recentlyViewed = Product::active()
            ->whereIn('id', array_filter($recentIds, fn($id) => $id != $product->id))
            ->take(4)
            ->get();

        return view('products.show', compact('product', 'relatedProducts', 'recentlyViewed'));
    }

    public function quickView(int $id)
    {
        $product = Product::active()
            ->with(['category', 'brand', 'images', 'variants'])
            ->findOrFail($id);

        return response()->json([
            'id' => $product->id,
            'name' => $product->name,
            'category' => $product->category->name ?? '',
            'price' => (float) $product->price,
            'sale_price' => $product->sale_price ? (float) $product->sale_price : null,
            'effective_price' => $product->effective_price,
            'discount_percentage' => $product->discount_percentage,
            'stock_quantity' => $product->stock_quantity,
            'is_in_stock' => $product->is_in_stock,
            'short_description' => $product->short_description,
            'primary_image' => $product->primary_image_url,
            'images' => $product->images->map(fn($img) => $img->url),
            'variants' => $product->variants,
            'url' => route('products.show', $product->slug),
        ]);
    }

    public function searchSuggestions(Request $request)
    {
        $q = trim($request->get('q', ''));
        if (strlen($q) < 2) {
            return response()->json(['products' => [], 'categories' => []]);
        }

        $products = Product::active()
            ->where('name', 'like', "%{$q}%")
            ->orWhere('sku', 'like', "%{$q}%")
            ->take(5)
            ->get(['id', 'name', 'slug', 'price', 'sale_price']);

        $categories = Category::active()
            ->where('name', 'like', "%{$q}%")
            ->take(3)
            ->get(['id', 'name', 'slug']);

        return response()->json([
            'products' => $products->map(fn($p) => [
                'name' => $p->name,
                'url' => route('products.show', $p->slug),
                'price' => '₹' . number_format($p->effective_price, 2),
                'image' => $p->primary_image_url,
            ]),
            'categories' => $categories->map(fn($c) => [
                'name' => $c->name,
                'url' => route('products.index', ['category' => $c->slug]),
            ]),
        ]);
    }
}