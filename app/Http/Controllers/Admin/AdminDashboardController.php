<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $totalSales = (float) Order::where('payment_status', 'paid')->sum('grand_total');
        $totalOrders = Order::count();
        $totalCustomers = User::where('role', 'customer')->count();
        $totalProducts = Product::count();
        $pendingOrders = Order::whereIn('order_status', ['pending', 'confirmed', 'processing'])->count();
        $lowStockProducts = Product::whereColumn('stock_quantity', '<=', 'low_stock_threshold')->count();

        // 7-day sales chart data
        $dates = [];
        $salesData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $dates[] = $date->format('M d');
            $salesData[] = (float) Order::where('payment_status', 'paid')
                ->whereDate('created_at', $date)
                ->sum('grand_total');
        }

        // Order status breakdown
        $statusCounts = Order::select('order_status', DB::raw('count(*) as count'))
            ->groupBy('order_status')
            ->pluck('count', 'order_status')
            ->toArray();

        $recentOrders = Order::with('user')->latest()->take(5)->get();
        $recentCustomers = User::where('role', 'customer')->latest()->take(5)->get();
        $lowStockItems = Product::with('category')
            ->whereColumn('stock_quantity', '<=', 'low_stock_threshold')
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'totalSales',
            'totalOrders',
            'totalCustomers',
            'totalProducts',
            'pendingOrders',
            'lowStockProducts',
            'dates',
            'salesData',
            'statusCounts',
            'recentOrders',
            'recentCustomers',
            'lowStockItems'
        ));
    }
}