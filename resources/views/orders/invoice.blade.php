<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice_{{ $order->order_number }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            .no-print { display: none !important; }
            body { font-size: 12px; background: #fff !important; color: #000 !important; }
            .print-border { border-color: #e2e8f0 !important; }
        }
    </style>
</head>
<body class="bg-slate-100 text-slate-800 p-4 sm:p-8">

    <!-- Print / Action Toolbar (hidden during print) -->
    <div class="max-w-3xl mx-auto mb-6 flex justify-between items-center no-print">
        <a href="{{ url()->previous() }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-slate-600 hover:text-slate-900 bg-white px-4 py-2 rounded-xl border border-slate-200 shadow-sm">
            &larr; Back
        </a>
        <button onclick="window.print()" class="inline-flex items-center gap-2 text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-700 px-5 py-2 rounded-xl shadow-md transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
            <span>Print / Save PDF</span>
        </button>
    </div>

    <!-- Official Invoice Document Container -->
    <div class="max-w-3xl mx-auto bg-white p-8 sm:p-12 rounded-3xl border border-slate-200/80 shadow-lg print:border-none print:shadow-none">

        <!-- Header -->
        <div class="flex justify-between items-start pb-8 border-b border-slate-200">
            <div>
                <div class="flex items-center gap-2 mb-2">
                    <span class="text-2xl font-black tracking-tight text-slate-900">AURA<span class="text-indigo-600">CART</span></span>
                </div>
                <p class="text-xs text-slate-500">{{ $store['name'] }}</p>
                <p class="text-xs text-slate-500">{{ $store['address'] }}</p>
                <p class="text-xs text-slate-500 font-mono mt-1">GSTIN: {{ $store['gst'] }}</p>
                <p class="text-xs text-slate-500">Email: {{ $store['email'] }} | Tel: {{ $store['phone'] }}</p>
            </div>

            <div class="text-right">
                <span class="inline-block px-3 py-1 bg-slate-900 text-white rounded-lg text-xs font-bold uppercase tracking-wider mb-2">
                    Tax Invoice
                </span>
                <p class="text-xs font-mono font-bold text-slate-900">#{{ $order->order_number }}</p>
                <p class="text-xs text-slate-500 mt-1">Date: {{ $order->created_at->format('d M Y') }}</p>
                <p class="text-xs text-slate-500">Status: <strong class="text-emerald-600 uppercase">{{ $order->payment_status }}</strong></p>
            </div>
        </div>

        <!-- Billed To / Shipping Address -->
        <div class="grid grid-cols-2 gap-8 py-6 border-b border-slate-200 text-xs">
            <div>
                <span class="font-bold text-slate-400 uppercase text-[10px] tracking-wider block mb-1">Billed To</span>
                <p class="font-bold text-slate-900">{{ $order->shipping_address['full_name'] ?? 'Customer' }}</p>
                <p class="text-slate-600">{{ $order->shipping_address['email'] ?? '' }}</p>
                <p class="text-slate-600">{{ $order->shipping_address['phone'] ?? '' }}</p>
            </div>
            <div>
                <span class="font-bold text-slate-400 uppercase text-[10px] tracking-wider block mb-1">Ship To</span>
                <p class="text-slate-700 leading-relaxed">
                    {{ $order->shipping_address['address_line1'] ?? '' }}, {{ $order->shipping_address['address_line2'] ?? '' }}<br>
                    {{ $order->shipping_address['city'] ?? '' }}, {{ $order->shipping_address['state'] ?? '' }} - {{ $order->shipping_address['pincode'] ?? '' }}<br>
                    {{ $order->shipping_address['country'] ?? 'India' }}
                </p>
            </div>
        </div>

        <!-- Line Items Table -->
        <div class="py-6">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-slate-200 text-[10px] font-bold uppercase text-slate-400">
                        <th class="py-2.5">Item Description</th>
                        <th class="py-2.5 text-center">SKU</th>
                        <th class="py-2.5 text-right">Unit Price</th>
                        <th class="py-2.5 text-center">Qty</th>
                        <th class="py-2.5 text-right">Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($order->items as $item)
                        <tr>
                            <td class="py-3 pr-2">
                                <p class="font-bold text-slate-900">{{ $item->product_name }}</p>
                                @if($item->variant_name)
                                    <p class="text-[10px] text-indigo-600">{{ $item->variant_name }}</p>
                                @endif
                            </td>
                            <td class="py-3 text-center font-mono text-[10px] text-slate-500">{{ $item->sku }}</td>
                            <td class="py-3 text-right">₹{{ number_format($item->unit_price, 2) }}</td>
                            <td class="py-3 text-center font-bold">{{ $item->quantity }}</td>
                            <td class="py-3 text-right font-bold text-slate-900">₹{{ number_format($item->subtotal, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Financial Summary -->
        <div class="border-t border-slate-200 pt-4 flex justify-end">
            <div class="w-64 space-y-2 text-xs">
                <div class="flex justify-between text-slate-600">
                    <span>Subtotal:</span>
                    <span class="font-bold text-slate-900">₹{{ number_format($order->subtotal, 2) }}</span>
                </div>
                @if($order->discount_amount > 0)
                    <div class="flex justify-between text-emerald-600 font-medium">
                        <span>Coupon ({{ $order->coupon_code }}):</span>
                        <span>-₹{{ number_format($order->discount_amount, 2) }}</span>
                    </div>
                @endif
                <div class="flex justify-between text-slate-600">
                    <span>Shipping Charges:</span>
                    <span>{{ $order->shipping_amount == 0 ? 'FREE' : '₹' . number_format($order->shipping_amount, 2) }}</span>
                </div>
                <div class="flex justify-between text-slate-600">
                    <span>GST (18% Included):</span>
                    <span>₹{{ number_format($order->tax_amount, 2) }}</span>
                </div>
                <div class="flex justify-between pt-2 border-t border-slate-200 text-sm font-black text-slate-900">
                    <span>Grand Total:</span>
                    <span>₹{{ number_format($order->grand_total, 2) }}</span>
                </div>
            </div>
        </div>

        <!-- Footer / Payment Mode -->
        <div class="mt-12 pt-6 border-t border-slate-100 flex justify-between items-center text-[10px] text-slate-400">
            <div>
                <p>Payment Mode: <strong class="uppercase text-slate-700">{{ $order->payment_method }}</strong></p>
                <p class="mt-0.5">Computer generated invoice. No signature required.</p>
            </div>
            <p>Thank you for choosing AuraCart!</p>
        </div>

    </div>
</body>
</html>
