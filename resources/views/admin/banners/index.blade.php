@extends('layouts.admin')

@section('title', 'Hero & Promotional Banners - AuraCart Admin')
@section('page_title', 'Banners & Campaigns')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Existing Banners (2 Cols) -->
    <div class="lg:col-span-2 space-y-6">
        <div class="bg-white rounded-2xl border border-slate-100 p-6 shadow-sm">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-base font-bold text-slate-800">Active Storefront Banners</h2>
                    <p class="text-xs text-slate-400 mt-0.5">Manage hero sliders, promotional strips, and campaign visuals displayed on customer storefront</p>
                </div>
                <span class="px-3 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-700">
                    {{ $banners->count() }} Live Banners
                </span>
            </div>

            <div class="space-y-4">
                @forelse($banners as $banner)
                    <div class="group p-4 rounded-2xl border border-slate-100 hover:border-amber-200 bg-slate-50/50 hover:bg-white transition-all flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                        <div class="flex items-center gap-4">
                            <div class="w-24 h-16 rounded-xl bg-slate-200 overflow-hidden shrink-0 border border-slate-200/80">
                                @if(Str::startsWith($banner->image_path, 'http'))
                                    <img src="{{ $banner->image_path }}" alt="{{ $banner->title }}" class="w-full h-full object-cover">
                                @else
                                    <img src="{{ asset('storage/' . $banner->image_path) }}" alt="{{ $banner->title }}" class="w-full h-full object-cover">
                                @endif
                            </div>
                            <div>
                                <div class="flex items-center gap-2">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider
                                        {{ $banner->type === 'hero_slide' ? 'bg-indigo-50 text-indigo-700 border border-indigo-200/60' : '' }}
                                        {{ $banner->type === 'promo_strip' ? 'bg-amber-50 text-amber-700 border border-amber-200/60' : '' }}
                                        {{ $banner->type === 'deal_banner' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200/60' : '' }}">
                                        {{ str_replace('_', ' ', $banner->type) }}
                                    </span>
                                    <span class="text-xs text-slate-400 font-mono">Order: #{{ $banner->sort_order }}</span>
                                </div>
                                <h3 class="font-bold text-slate-800 text-sm mt-1">{{ $banner->title }}</h3>
                                @if($banner->subtitle)
                                    <p class="text-xs text-slate-500 line-clamp-1">{{ $banner->subtitle }}</p>
                                @endif
                                <div class="text-[11px] text-amber-600 font-medium mt-1 flex items-center gap-1">
                                    <i data-lucide="link" class="w-3 h-3"></i> {{ $banner->link_url }}
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center gap-2 self-end sm:self-center">
                            @if($banner->is_active)
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Live
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-slate-100 text-slate-500">
                                    <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span> Inactive
                                </span>
                            @endif

                            <form method="POST" action="{{ route('admin.banners.destroy', $banner->id) }}" onsubmit="return confirm('Delete this banner?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-2 text-rose-400 hover:text-rose-600 hover:bg-rose-50 rounded-xl transition-colors" title="Delete Banner">
                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="py-12 text-center text-slate-400">
                        <i data-lucide="image" class="w-10 h-10 mx-auto mb-2 text-slate-300 stroke-1"></i>
                        <p class="text-sm font-semibold text-slate-600">No promotional banners active</p>
                        <p class="text-xs text-slate-400 mt-1">Upload a hero banner or promotional strip using the form on the right</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Create Banner Form (1 Col) -->
    <div class="lg:col-span-1">
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 sticky top-24">
            <h2 class="text-base font-bold text-slate-800 flex items-center gap-2 mb-1">
                <i data-lucide="plus-circle" class="w-5 h-5 text-amber-600"></i> New Banner
            </h2>
            <p class="text-xs text-slate-400 mb-6">Upload a visual campaign banner</p>

            <form method="POST" action="{{ route('admin.banners.store') }}" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">Banner Type *</label>
                    <select name="type" required class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium text-slate-800 focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-600">
                        <option value="hero_slide">Hero Slide (Home Top Slider)</option>
                        <option value="promo_strip">Promotional Strip</option>
                        <option value="deal_banner">Special Deal Banner</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">Title / Heading *</label>
                    <input type="text" name="title" value="{{ old('title') }}" required placeholder="e.g. Pure Artisanal Crafted Essentials"
                        class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-600">
                    @error('title') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">Subtitle / Description</label>
                    <input type="text" name="subtitle" value="{{ old('subtitle') }}" placeholder="e.g. Elevate your lifestyle with handcrafted organic curation"
                        class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-600">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">Button Text</label>
                        <input type="text" name="button_text" value="{{ old('button_text', 'Shop The Collection') }}" placeholder="Shop Now"
                            class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-600">
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">Sort Order</label>
                        <input type="number" name="sort_order" value="{{ old('sort_order', 1) }}"
                            class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-600">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">Target Link</label>
                    <input type="text" name="link_url" value="{{ old('link_url', '/products') }}" placeholder="/products"
                        class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-600">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">Banner Image *</label>
                    <input type="file" name="image" required accept="image/*"
                        class="w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-amber-50 file:text-amber-700 hover:file:bg-amber-100">
                    <p class="text-[11px] text-slate-400 mt-1">Recommended: 1920x800 for hero slides, max 5MB</p>
                    @error('image') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="flex items-center gap-2 pt-1">
                    <input type="checkbox" id="banner_active" name="is_active" value="1" checked class="w-4 h-4 rounded text-amber-600 focus:ring-amber-500 border-slate-300">
                    <label for="banner_active" class="text-sm font-medium text-slate-700">Display on Storefront</label>
                </div>

                <button type="submit" class="w-full py-3 bg-gradient-to-r from-amber-600 to-amber-700 hover:from-amber-700 hover:to-amber-800 text-white font-bold rounded-xl text-sm shadow-md shadow-amber-900/10 transition-all flex items-center justify-center gap-2">
                    <i data-lucide="upload-cloud" class="w-4 h-4"></i> Upload & Publish Banner
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
