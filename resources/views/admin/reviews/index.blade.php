@extends('layouts.admin')

@section('title', 'Product Reviews Moderation - AuraCart Admin')
@section('page_title', 'Reviews Moderation')

@section('content')
<div class="space-y-6">
    <!-- Header & Filter Tabs -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-5 rounded-2xl border border-slate-100 shadow-sm">
        <div>
            <h2 class="text-xl font-bold text-slate-800 tracking-tight">Customer Product Reviews</h2>
            <p class="text-sm text-slate-500 mt-0.5">Moderate customer feedback, ratings, and testimonials before they appear publicly</p>
        </div>

        <!-- Filter Status Tabs -->
        <div class="flex items-center gap-1.5 p-1 bg-slate-100 rounded-xl">
            <a href="{{ route('admin.reviews.index') }}"
                class="px-3.5 py-1.5 rounded-lg text-xs font-bold transition-all {{ !request('status') ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-600 hover:text-slate-900' }}">
                All Reviews
            </a>
            <a href="{{ route('admin.reviews.index', ['status' => 'pending']) }}"
                class="px-3.5 py-1.5 rounded-lg text-xs font-bold transition-all {{ request('status') === 'pending' ? 'bg-white text-amber-700 shadow-sm' : 'text-slate-600 hover:text-amber-700' }}">
                Pending
            </a>
            <a href="{{ route('admin.reviews.index', ['status' => 'approved']) }}"
                class="px-3.5 py-1.5 rounded-lg text-xs font-bold transition-all {{ request('status') === 'approved' ? 'bg-white text-emerald-700 shadow-sm' : 'text-slate-600 hover:text-emerald-700' }}">
                Approved
            </a>
            <a href="{{ route('admin.reviews.index', ['status' => 'rejected']) }}"
                class="px-3.5 py-1.5 rounded-lg text-xs font-bold transition-all {{ request('status') === 'rejected' ? 'bg-white text-rose-700 shadow-sm' : 'text-slate-600 hover:text-rose-700' }}">
                Rejected
            </a>
        </div>
    </div>

    <!-- Reviews Table -->
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/80 text-[11px] font-bold uppercase tracking-wider text-slate-500 border-b border-slate-100">
                        <th class="py-3.5 px-6">Product</th>
                        <th class="py-3.5 px-6">Customer</th>
                        <th class="py-3.5 px-6">Rating & Feedback</th>
                        <th class="py-3.5 px-6">Date</th>
                        <th class="py-3.5 px-6 text-center">Status</th>
                        <th class="py-3.5 px-6 text-right">Moderation Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    @forelse($reviews as $review)
                        <tr class="hover:bg-slate-50/50 transition-colors group">
                            <!-- Product Info -->
                            <td class="py-4 px-6 max-w-xs">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-lg bg-slate-100 overflow-hidden shrink-0 border border-slate-200">
                                        @if($review->product && $review->product->primary_image)
                                            <img src="{{ $review->product->primary_image->image_url }}" alt="" class="w-full h-full object-cover">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center text-slate-300">
                                                <i data-lucide="package" class="w-5 h-5"></i>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="truncate">
                                        <div class="font-bold text-slate-800 text-xs truncate">
                                            {{ $review->product ? $review->product->name : 'Deleted Product' }}
                                        </div>
                                        <div class="text-[11px] text-slate-400 font-mono">
                                            SKU: {{ $review->product ? $review->product->sku : 'N/A' }}
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <!-- Customer Info -->
                            <td class="py-4 px-6">
                                <div class="font-bold text-slate-800 text-xs">{{ $review->user ? $review->user->name : 'Anonymous' }}</div>
                                <div class="text-[11px] text-slate-400">{{ $review->user ? $review->user->email : '' }}</div>
                                @if($review->is_verified_buyer)
                                    <span class="inline-flex items-center gap-1 text-[10px] text-emerald-600 font-bold mt-0.5">
                                        <i data-lucide="check-circle" class="w-3 h-3"></i> Verified Buyer
                                    </span>
                                @endif
                            </td>

                            <!-- Rating & Content -->
                            <td class="py-4 px-6 max-w-md">
                                <div class="flex items-center gap-1 mb-1">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i data-lucide="star" class="w-3.5 h-3.5 {{ $i <= $review->rating ? 'text-amber-500 fill-amber-500' : 'text-slate-200 fill-slate-200' }}"></i>
                                    @endfor
                                    <span class="text-xs font-bold text-slate-700 ml-1">({{ $review->rating }}/5)</span>
                                </div>
                                @if($review->title)
                                    <div class="text-xs font-bold text-slate-800 mb-0.5">{{ $review->title }}</div>
                                @endif
                                <p class="text-xs text-slate-600 leading-relaxed line-clamp-2">
                                    "{{ $review->comment }}"
                                </p>
                            </td>

                            <!-- Date -->
                            <td class="py-4 px-6 text-xs text-slate-500 whitespace-nowrap">
                                {{ $review->created_at->format('M d, Y') }}
                                <div class="text-[11px] text-slate-400">{{ $review->created_at->diffForHumans() }}</div>
                            </td>

                            <!-- Status Badge -->
                            <td class="py-4 px-6 text-center whitespace-nowrap">
                                @if($review->status === 'approved')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Approved
                                    </span>
                                @elseif($review->status === 'pending')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> Pending
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span> Rejected
                                    </span>
                                @endif
                            </td>

                            <!-- Moderation Actions -->
                            <td class="py-4 px-6 text-right whitespace-nowrap">
                                <div class="flex items-center justify-end gap-1">
                                    @if($review->status !== 'approved')
                                        <form method="POST" action="{{ route('admin.reviews.status', $review->id) }}">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="status" value="approved">
                                            <button type="submit" class="p-1.5 text-emerald-600 hover:bg-emerald-50 rounded-lg transition-colors" title="Approve Review">
                                                <i data-lucide="check" class="w-4 h-4"></i>
                                            </button>
                                        </form>
                                    @endif

                                    @if($review->status !== 'rejected')
                                        <form method="POST" action="{{ route('admin.reviews.status', $review->id) }}">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="status" value="rejected">
                                            <button type="submit" class="p-1.5 text-amber-600 hover:bg-amber-50 rounded-lg transition-colors" title="Reject Review">
                                                <i data-lucide="x" class="w-4 h-4"></i>
                                            </button>
                                        </form>
                                    @endif

                                    <form method="POST" action="{{ route('admin.reviews.destroy', $review->id) }}" onsubmit="return confirm('Permanently delete this customer review?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 text-rose-500 hover:bg-rose-50 rounded-lg transition-colors" title="Delete Review">
                                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-slate-400">
                                <i data-lucide="message-square" class="w-10 h-10 mx-auto mb-2 text-slate-300 stroke-1"></i>
                                <p class="text-base font-semibold text-slate-600">No reviews found</p>
                                <p class="text-xs text-slate-400 mt-1">There are no reviews matching this filter</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($reviews->hasPages())
            <div class="p-4 border-t border-slate-100 bg-slate-50/50">
                {{ $reviews->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
