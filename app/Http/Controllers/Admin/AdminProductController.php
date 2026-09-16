<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use App\Models\Subcategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AdminProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with(['category', 'brand', 'images']);

        if ($request->filled('q')) {
            $term = '%' . $request->q . '%';
            $query->where('name', 'like', $term)
                  ->orWhere('sku', 'like', $term);
        }

        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status == 'active');
        }

        if ($request->filled('stock')) {
            if ($request->stock === 'low') {
                $query->whereColumn('stock_quantity', '<=', 'low_stock_threshold');
            } elseif ($request->stock === 'out') {
                $query->where('stock_quantity', '<=', 0);
            }
        }

        $products = $query->latest()->paginate(15)->withQueryString();
        $categories = Category::active()->get();

        return view('admin.products.index', compact('products', 'categories'));
    }

    public function create()
    {
        $categories = Category::active()->with('subcategories')->get();
        $brands = Brand::active()->get();
        return view('admin.products.create', compact('categories', 'brands'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:200',
            'sku' => 'nullable|string|max:50|unique:products,sku',
            'category_id' => 'required|exists:categories,id',
            'subcategory_id' => 'nullable|exists:subcategories,id',
            'brand_id' => 'nullable|exists:brands,id',
            'price' => 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0|lt:price',
            'stock_quantity' => 'required|integer|min:0',
            'low_stock_threshold' => 'nullable|integer|min:0',
            'short_description' => 'nullable|string|max:500',
            'description' => 'nullable|string',
            'tax_rate' => 'nullable|numeric|min:0|max:100',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        $slug = Str::slug($request->name);
        if (Product::where('slug', $slug)->exists()) {
            $slug .= '-' . Str::lower(Str::random(5));
        }

        $product = Product::create([
            'name' => $request->name,
            'slug' => $slug,
            'sku' => $request->sku ?: 'SKU-' . strtoupper(Str::random(8)),
            'category_id' => $request->category_id,
            'subcategory_id' => $request->subcategory_id,
            'brand_id' => $request->brand_id,
            'price' => $request->price,
            'sale_price' => $request->sale_price,
            'stock_quantity' => $request->stock_quantity,
            'low_stock_threshold' => $request->get('low_stock_threshold', 5),
            'short_description' => $request->short_description,
            'description' => $request->description,
            'weight' => $request->weight,
            'dimensions' => $request->dimensions,
            'tax_rate' => $request->get('tax_rate', 18.0),
            'is_featured' => $request->boolean('is_featured'),
            'is_bestseller' => $request->boolean('is_bestseller'),
            'is_new' => $request->boolean('is_new', true),
            'is_active' => $request->boolean('is_active', true),
            'tags' => $request->tags ? array_map('trim', explode(',', $request->tags)) : null,
        ]);

        // Process images
        if ($request->hasFile('images')) {
            $isPrimary = true;
            foreach ($request->file('images') as $file) {
                $path = $file->store('products', 'public');
                ProductImage::create([
                    'product_id' => $product->id,
                    'image_path' => $path,
                    'is_primary' => $isPrimary,
                ]);
                $isPrimary = false;
            }
        }

        // Process variants if provided
        if ($request->filled('variant_names')) {
            foreach ($request->variant_names as $index => $name) {
                if (!empty($name)) {
                    ProductVariant::create([
                        'product_id' => $product->id,
                        'variant_name' => $name,
                        'sku' => $request->variant_skus[$index] ?? ($product->sku . "-V{$index}"),
                        'price' => !empty($request->variant_prices[$index]) ? $request->variant_prices[$index] : null,
                        'stock_quantity' => $request->variant_stocks[$index] ?? 0,
                    ]);
                }
            }
        }

        return redirect()->route('admin.products.index')
            ->with('success', "Product '{$product->name}' created successfully! It is now live on the storefront.");
    }

    public function edit(int $id)
    {
        $product = Product::with(['images', 'variants'])->findOrFail($id);
        $categories = Category::active()->with('subcategories')->get();
        $brands = Brand::active()->get();

        return view('admin.products.edit', compact('product', 'categories', 'brands'));
    }

    public function update(Request $request, int $id)
    {
        $product = Product::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:200',
            'sku' => 'nullable|string|max:50|unique:products,sku,' . $product->id,
            'category_id' => 'required|exists:categories,id',
            'subcategory_id' => 'nullable|exists:subcategories,id',
            'brand_id' => 'nullable|exists:brands,id',
            'price' => 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0|lt:price',
            'stock_quantity' => 'required|integer|min:0',
            'low_stock_threshold' => 'nullable|integer|min:0',
            'short_description' => 'nullable|string|max:500',
            'description' => 'nullable|string',
            'tax_rate' => 'nullable|numeric|min:0|max:100',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        $product->update([
            'name' => $request->name,
            'sku' => $request->sku ?: $product->sku,
            'category_id' => $request->category_id,
            'subcategory_id' => $request->subcategory_id,
            'brand_id' => $request->brand_id,
            'price' => $request->price,
            'sale_price' => $request->sale_price,
            'stock_quantity' => $request->stock_quantity,
            'low_stock_threshold' => $request->get('low_stock_threshold', 5),
            'short_description' => $request->short_description,
            'description' => $request->description,
            'weight' => $request->weight,
            'dimensions' => $request->dimensions,
            'tax_rate' => $request->get('tax_rate', 18.0),
            'is_featured' => $request->boolean('is_featured'),
            'is_bestseller' => $request->boolean('is_bestseller'),
            'is_new' => $request->boolean('is_new'),
            'is_active' => $request->boolean('is_active'),
            'tags' => $request->tags ? array_map('trim', explode(',', $request->tags)) : null,
        ]);

        // Upload additional images
        if ($request->hasFile('images')) {
            $hasExistingPrimary = $product->images()->where('is_primary', true)->exists();
            foreach ($request->file('images') as $file) {
                $path = $file->store('products', 'public');
                ProductImage::create([
                    'product_id' => $product->id,
                    'image_path' => $path,
                    'is_primary' => !$hasExistingPrimary,
                ]);
                $hasExistingPrimary = true;
            }
        }

        return redirect()->route('admin.products.index')
            ->with('success', "Product '{$product->name}' updated! Changes immediately reflect on the website.");
    }

    public function toggleStatus(int $id)
    {
        $product = Product::findOrFail($id);
        $product->is_active = !$product->is_active;
        $product->save();

        $statusText = $product->is_active ? 'Active (published)' : 'Inactive (hidden)';
        return redirect()->back()->with('success', "Product is now {$statusText}.");
    }

    public function destroy(int $id)
    {
        $product = Product::findOrFail($id);
        $product->delete();

        return redirect()->route('admin.products.index')
            ->with('success', "Product '{$product->name}' deleted successfully.");
    }

    public function deleteImage(int $imageId)
    {
        $image = ProductImage::findOrFail($imageId);
        if (Storage::disk('public')->exists($image->image_path)) {
            Storage::disk('public')->delete($image->image_path);
        }
        $image->delete();

        return redirect()->back()->with('success', 'Image removed.');
    }
}