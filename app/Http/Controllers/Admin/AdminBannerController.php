<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\Request;

class AdminBannerController extends Controller
{
    public function index()
    {
        $banners = Banner::orderBy('sort_order')->get();
        return view('admin.banners.index', compact('banners'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|in:hero_slide,promo_strip,deal_banner',
            'title' => 'required|string|max:150',
            'subtitle' => 'nullable|string|max:255',
            'button_text' => 'nullable|string|max:50',
            'link_url' => 'nullable|string|max:255',
            'image' => 'required|image|max:5120',
            'sort_order' => 'nullable|integer',
        ]);

        $path = $request->file('image')->store('banners', 'public');

        Banner::create([
            'type' => $request->type,
            'title' => $request->title,
            'subtitle' => $request->subtitle,
            'button_text' => $request->button_text,
            'link_url' => $request->link_url ?: '/products',
            'image_path' => $path,
            'sort_order' => $request->get('sort_order', 0),
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('admin.banners.index')->with('success', 'Banner created successfully.');
    }

    public function destroy(int $id)
    {
        $banner = Banner::findOrFail($id);
        $banner->delete();
        return redirect()->route('admin.banners.index')->with('success', 'Banner deleted.');
    }
}