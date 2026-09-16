<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InventoryLog;
use App\Models\Product;
use App\Services\InventoryService;
use Illuminate\Http\Request;

class AdminInventoryController extends Controller
{
    protected InventoryService $inventoryService;

    public function __construct(InventoryService $inventoryService)
    {
        $this->inventoryService = $inventoryService;
    }

    public function index(Request $request)
    {
        $query = Product::with(['category', 'variants']);

        if ($request->filled('stock_status')) {
            if ($request->stock_status === 'out') {
                $query->where('stock_quantity', '<=', 0);
            } elseif ($request->stock_status === 'low') {
                $query->whereColumn('stock_quantity', '<=', 'low_stock_threshold')->where('stock_quantity', '>', 0);
            } elseif ($request->stock_status === 'in') {
                $query->whereColumn('stock_quantity', '>', 'low_stock_threshold');
            }
        }

        $products = $query->latest()->paginate(15)->withQueryString();
        $recentLogs = InventoryLog::with(['product', 'user'])->latest()->take(10)->get();

        return view('admin.inventory.index', compact('products', 'recentLogs'));
    }

    public function adjust(Request $request, int $id)
    {
        $request->validate([
            'quantity_change' => 'required|integer',
            'type' => 'required|in:restock,damage,manual_adjustment',
            'note' => 'nullable|string|max:255',
            'variant_id' => 'nullable|integer|exists:product_variants,id',
        ]);

        $result = $this->inventoryService->adjustStock(
            $id,
            (int) $request->quantity_change,
            $request->type,
            $request->note,
            $request->variant_id
        );

        return redirect()->back()->with('success', "Stock updated successfully! New stock: {$result['new_stock']}");
    }
}