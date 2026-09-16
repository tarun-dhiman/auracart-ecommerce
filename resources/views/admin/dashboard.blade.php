@extends('layouts.admin')

@section('title', 'Executive Dashboard — AuraCart Control Hub')
@section('header_title', 'Analytics & Store Operations')

@section('content')
<div class="space-y-8">

    <!-- KPI Metric Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4">
        <div class="bg-slate-900/90 border border-slate-800 p-5 rounded-3xl space-y-2">
            <div class="flex items-center justify-between text-slate-400">
                <span class="text-[11px] font-bold uppercase tracking-wider">Revenue</span>
                <i data-lucide="indian-rupee" class="w-4 h-4 text-emerald-400"></i>
            </div>
            <p class="text-xl sm:text-2xl font-black text-white">₹{{ number_format($totalSales, 0) }}</p>
            <p class="text-[10px] text-emerald-400 font-semibold">+18.4% vs last period</p>
        </div>

        <div class="bg-slate-900/90 border border-slate-800 p-5 rounded-3xl space-y-2">
            <div class="flex items-center justify-between text-slate-400">
                <span class="text-[11px] font-bold uppercase tracking-wider">Total Orders</span>
                <i data-lucide="shopping-cart" class="w-4 h-4 text-indigo-400"></i>
            </div>
            <p class="text-xl sm:text-2xl font-black text-white">{{ $totalOrders }}</p>
            <p class="text-[10px] text-indigo-400 font-semibold">Processed orders</p>
        </div>

        <div class="bg-slate-900/90 border border-slate-800 p-5 rounded-3xl space-y-2">
            <div class="flex items-center justify-between text-slate-400">
                <span class="text-[11px] font-bold uppercase tracking-wider">Customers</span>
                <i data-lucide="users" class="w-4 h-4 text-purple-400"></i>
            </div>
            <p class="text-xl sm:text-2xl font-black text-white">{{ $totalCustomers }}</p>
            <p class="text-[10px] text-purple-400 font-semibold">Registered users</p>
        </div>

        <div class="bg-slate-900/90 border border-slate-800 p-5 rounded-3xl space-y-2">
            <div class="flex items-center justify-between text-slate-400">
                <span class="text-[11px] font-bold uppercase tracking-wider">Products</span>
                <i data-lucide="package" class="w-4 h-4 text-blue-400"></i>
            </div>
            <p class="text-xl sm:text-2xl font-black text-white">{{ $totalProducts }}</p>
            <p class="text-[10px] text-blue-400 font-semibold">In active catalog</p>
        </div>

        <div class="bg-slate-900/90 border border-slate-800 p-5 rounded-3xl space-y-2">
            <div class="flex items-center justify-between text-slate-400">
                <span class="text-[11px] font-bold uppercase tracking-wider">Pending Orders</span>
                <i data-lucide="clock" class="w-4 h-4 text-amber-400"></i>
            </div>
            <p class="text-xl sm:text-2xl font-black text-amber-400">{{ $pendingOrders }}</p>
            <p class="text-[10px] text-amber-300 font-semibold">Requires fulfillment</p>
        </div>

        <div class="bg-slate-900/90 border border-slate-800 p-5 rounded-3xl space-y-2">
            <div class="flex items-center justify-between text-slate-400">
                <span class="text-[11px] font-bold uppercase tracking-wider">Low Stock</span>
                <i data-lucide="alert-triangle" class="w-4 h-4 text-rose-400"></i>
            </div>
            <p class="text-xl sm:text-2xl font-black text-rose-400">{{ $lowStockProducts }}</p>
            <p class="text-[10px] text-rose-400 font-semibold">Reorder alert</p>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Revenue Trend Line Chart -->
        <div class="lg:col-span-2 bg-slate-900/90 border border-slate-800 rounded-3xl p-6 sm:p-8 space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-800">
                <div>
                    <h3 class="text-sm font-bold text-white">Sales & Revenue Trend (Last 7 Days)</h3>
                    <p class="text-xs text-slate-400">Daily processed financial order volume</p>
                </div>
                <span class="px-2.5 py-1 rounded-full bg-emerald-500/10 text-emerald-400 font-mono text-xs font-bold">
                    Daily Analytics
                </span>
            </div>
            <div class="h-64 sm:h-72">
                <canvas id="salesChart"></canvas>
            </div>
        </div>

        <!-- Order Status Distribution Donut Chart -->
        <div class="lg:col-span-1 bg-slate-900/90 border border-slate-800 rounded-3xl p-6 sm:p-8 space-y-4">
            <div class="pb-3 border-b border-slate-800">
                <h3 class="text-sm font-bold text-white">Order Status Distribution</h3>
                <p class="text-xs text-slate-400">Current fulfillment pipelines</p>
            </div>
            <div class="h-64 flex items-center justify-center">
                <canvas id="statusChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Tables Grid (Recent Orders & Low Stock Warning) -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">

        <!-- Recent Orders (2 cols) -->
        <div class="lg:col-span-2 bg-slate-900/90 border border-slate-800 rounded-3xl p-6 sm:p-8 space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-800">
                <div>
                    <h3 class="text-sm font-bold text-white">Recent Customer Orders</h3>
                    <p class="text-xs text-slate-400">Latest transactions requiring fulfillment</p>
                </div>
                <a href="{{ route('admin.orders.index') }}" class="text-xs font-bold text-indigo-400 hover:text-indigo-300">
                    View All Orders &rarr;
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="border-b border-slate-800 text-[10px] font-bold uppercase text-slate-500">
                            <th class="py-3">Order Number</th>
                            <th class="py-3">Customer</th>
                            <th class="py-3">Status</th>
                            <th class="py-3">Total</th>
                            <th class="py-3 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/60">
                        @foreach($recentOrders as $ro)
                            <tr class="hover:bg-slate-800/40 transition">
                                <td class="py-3 font-mono font-bold text-white">{{ $ro->order_number }}</td>
                                <td class="py-3 text-slate-300">{{ $ro->shipping_address['full_name'] ?? ($ro->user->name ?? 'Guest') }}</td>
                                <td class="py-3">
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold {{ $ro->status_badge_class }}">
                                        {{ ucfirst($ro->order_status) }}
                                    </span>
                                </td>
                                <td class="py-3 font-bold text-white">₹{{ number_format($ro->grand_total, 2) }}</td>
                                <td class="py-3 text-right">
                                    <a href="{{ route('admin.orders.show', $ro->order_number) }}" class="px-3 py-1.5 bg-slate-800 hover:bg-indigo-600 text-slate-200 hover:text-white font-bold rounded-lg text-[11px] transition">
                                        Manage
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Low Stock Alerts (1 col) -->
        <div class="lg:col-span-1 bg-slate-900/90 border border-slate-800 rounded-3xl p-6 sm:p-8 space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-800">
                <div>
                    <h3 class="text-sm font-bold text-white">Low Stock Warning</h3>
                    <p class="text-xs text-slate-400">Inventory critically depleted</p>
                </div>
                <a href="{{ route('admin.inventory.index') }}" class="text-xs font-bold text-rose-400 hover:text-rose-300">
                    Restock &rarr;
                </a>
            </div>

            @if($lowStockItems->isEmpty())
                <div class="text-center py-8 text-slate-500 text-xs">
                    <i data-lucide="check-circle-2" class="w-8 h-8 text-emerald-400 mx-auto mb-2"></i>
                    <p>All catalog products are healthy in stock.</p>
                </div>
            @else
                <div class="space-y-3">
                    @foreach($lowStockItems as $lsi)
                        <div class="p-3 bg-slate-800/60 rounded-2xl border border-slate-700/60 flex items-center justify-between text-xs">
                            <div class="min-w-0 pr-2">
                                <h4 class="font-bold text-white truncate">{{ $lsi->name }}</h4>
                                <p class="text-[10px] text-slate-400">{{ $lsi->category->name ?? '' }}</p>
                            </div>
                            <span class="px-2.5 py-1 rounded-xl text-[10px] font-black {{ $lsi->stock_quantity <= 0 ? 'bg-rose-500/20 text-rose-400 border border-rose-500/30' : 'bg-amber-500/20 text-amber-400 border border-amber-500/30' }}">
                                {{ $lsi->stock_quantity }} Left
                            </span>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

    </div>

</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        // Sales Trend Chart
        const salesCtx = document.getElementById('salesChart').getContext('2d');
        new Chart(salesCtx, {
            type: 'line',
            data: {
                labels: {!! json_encode($dates) !!},
                datasets: [{
                    label: 'Revenue (₹)',
                    data: {!! json_encode($salesData) !!},
                    borderColor: '#6366f1',
                    backgroundColor: 'rgba(99, 102, 241, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#818cf8',
                    pointRadius: 4,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    x: { grid: { color: 'rgba(255,255,255,0.05)' }, ticks: { color: '#94a3b8' } },
                    y: { grid: { color: 'rgba(255,255,255,0.05)' }, ticks: { color: '#94a3b8' } }
                }
            }
        });

        // Status Breakdown Donut
        const statusCtx = document.getElementById('statusChart').getContext('2d');
        const statusData = {!! json_encode($statusCounts) !!};
        new Chart(statusCtx, {
            type: 'doughnut',
            data: {
                labels: Object.keys(statusData).map(s => s.replace('_', ' ').toUpperCase()),
                datasets: [{
                    data: Object.values(statusData),
                    backgroundColor: ['#f59e0b', '#3b82f6', '#6366f1', '#a855f7', '#10b981', '#f43f5e'],
                    borderWidth: 0,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom', labels: { color: '#94a3b8', font: { size: 10 } } }
                }
            }
        });
    });
</script>
@endpush
