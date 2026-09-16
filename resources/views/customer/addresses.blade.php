@extends('customer.layout')

@section('title', 'Saved Addresses — AuraCart')

@section('customer_content')
<div class="space-y-6" x-data="{ showModal: false }">

    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm p-6 sm:p-8 space-y-6">
        <div class="flex items-center justify-between pb-4 border-b border-slate-100">
            <div>
                <h2 class="text-base font-bold text-slate-900">Address Book</h2>
                <p class="text-xs text-slate-500">Manage your shipping destinations for swift checkout.</p>
            </div>
            <button type="button" @click="showModal = true" class="px-4 py-2 bg-slate-900 hover:bg-indigo-600 text-white font-bold text-xs rounded-xl transition flex items-center gap-1.5">
                <i data-lucide="plus" class="w-4 h-4"></i>
                <span>Add Address</span>
            </button>
        </div>

        @if($addresses->isEmpty())
            <div class="text-center py-12 text-slate-500 text-xs">
                <i data-lucide="map-pin-off" class="w-10 h-10 text-slate-300 mx-auto mb-2"></i>
                <p>No addresses saved yet. Add your default address for one-click checkout.</p>
            </div>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                @foreach($addresses as $addr)
                    <div class="p-5 rounded-2xl border {{ $addr->is_default ? 'border-indigo-600 bg-indigo-50/30' : 'border-slate-200' }} flex flex-col justify-between space-y-3">
                        <div class="flex items-start justify-between">
                            <span class="text-xs font-bold text-slate-900">{{ $addr->full_name }}</span>
                            @if($addr->is_default)
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-indigo-600 text-white">Default</span>
                            @endif
                        </div>
                        <p class="text-xs text-slate-600 leading-relaxed">{{ $addr->formatted_address }}</p>
                        <p class="text-xs text-slate-500">Phone: {{ $addr->phone }}</p>

                        <div class="pt-3 border-t border-slate-100 flex items-center justify-between text-xs">
                            @if(!$addr->is_default)
                                <form method="POST" action="{{ route('customer.addresses.default', $addr->id) }}">
                                    @csrf
                                    <button type="submit" class="text-indigo-600 hover:underline font-semibold text-[11px]">Set as Default</button>
                                </form>
                            @else
                                <span></span>
                            @endif

                            <form method="POST" action="{{ route('customer.addresses.delete', $addr->id) }}" onsubmit="return confirm('Delete this address?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-rose-500 hover:text-rose-700 font-semibold text-[11px]">Delete</button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <!-- Add Address Modal -->
    <div x-show="showModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" @click="showModal = false"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div class="inline-block align-bottom bg-white rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-slate-100 p-6 sm:p-8">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-4">
                    <h3 class="text-sm font-bold text-slate-900">Add New Shipping Address</h3>
                    <button type="button" @click="showModal = false" class="text-slate-400 hover:text-slate-600"><i data-lucide="x" class="w-5 h-5"></i></button>
                </div>

                <form method="POST" action="{{ route('customer.addresses.store') }}" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Full Name *</label>
                        <input type="text" name="full_name" required class="w-full text-xs px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:border-indigo-500 focus:outline-none">
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Phone Number *</label>
                            <input type="text" name="phone" required class="w-full text-xs px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:border-indigo-500 focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Email</label>
                            <input type="email" name="email" class="w-full text-xs px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:border-indigo-500 focus:outline-none">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Address Line 1 *</label>
                        <input type="text" name="address_line1" required placeholder="House / Flat / Block" class="w-full text-xs px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:border-indigo-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Address Line 2 (Area/Landmark)</label>
                        <input type="text" name="address_line2" class="w-full text-xs px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:border-indigo-500 focus:outline-none">
                    </div>
                    <div class="grid grid-cols-3 gap-2">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">City *</label>
                            <input type="text" name="city" required class="w-full text-xs px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:border-indigo-500 focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">State *</label>
                            <input type="text" name="state" required class="w-full text-xs px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:border-indigo-500 focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Pincode *</label>
                            <input type="text" name="pincode" required class="w-full text-xs px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:border-indigo-500 focus:outline-none">
                        </div>
                    </div>
                    <div>
                        <label class="flex items-center gap-2 text-xs text-slate-700 cursor-pointer pt-2">
                            <input type="checkbox" name="is_default" value="1" class="text-indigo-600 focus:ring-indigo-500 rounded border-slate-300">
                            <span>Set as default shipping address</span>
                        </label>
                    </div>
                    <button type="submit" class="w-full py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-xl shadow-md transition">
                        Save Address
                    </button>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection
