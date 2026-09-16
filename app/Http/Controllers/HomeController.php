<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use App\Models\Category;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $heroBanners = Banner::active()->where('type', 'hero_slide')->get();
        $promoBanners = Banner::active()->where('type', 'promo_strip')->get();
        $dealBanners = Banner::active()->where('type', 'deal_banner')->get();

        $featuredCategories = Category::active()->featured()->withCount('products')->get();
        $allCategories = Category::active()->with('subcategories')->get();

        $featuredProducts = Product::active()
            ->featured()
            ->with(['images', 'category'])
            ->take(8)
            ->get();

        $newArrivals = Product::active()
            ->newArrival()
            ->with(['images', 'category'])
            ->take(8)
            ->get();

        $bestSellers = Product::active()
            ->bestseller()
            ->with(['images', 'category'])
            ->take(8)
            ->get();

        $testimonials = Review::approved()
            ->where('rating', '>=', 4)
            ->with(['user', 'product'])
            ->take(6)
            ->get();

        return view('home', compact(
            'heroBanners',
            'promoBanners',
            'dealBanners',
            'featuredCategories',
            'allCategories',
            'featuredProducts',
            'newArrivals',
            'bestSellers',
            'testimonials'
        ));
    }
}