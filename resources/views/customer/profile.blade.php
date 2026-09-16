@extends('customer.layout')

@section('title', 'Profile & Security — AuraCart')

@section('customer_content')
<div class="space-y-8">

    <!-- Profile Information Form -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm p-6 sm:p-8 space-y-6">
        <div class="pb-4 border-b border-slate-100">
            <h2 class="text-base font-bold text-slate-900">Personal Information</h2>
            <p class="text-xs text-slate-500">Update your account name, contact details and email.</p>
        </div>

        <form method="POST" action="{{ route('customer.profile.update') }}" class="space-y-4 max-w-xl">
            @csrf
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Full Name</label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}" required class="w-full text-xs px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:border-indigo-500 focus:bg-white focus:outline-none">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Email Address</label>
                <input type="email" name="email" value="{{ old('email', $user->email) }}" required class="w-full text-xs px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:border-indigo-500 focus:bg-white focus:outline-none">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Mobile Phone</label>
                <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" class="w-full text-xs px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:border-indigo-500 focus:bg-white focus:outline-none">
            </div>

            <button type="submit" class="px-6 py-2.5 bg-slate-900 hover:bg-indigo-600 text-white font-bold text-xs rounded-xl transition">
                Save Profile Changes
            </button>
        </form>
    </div>

    <!-- Password Change Form -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm p-6 sm:p-8 space-y-6">
        <div class="pb-4 border-b border-slate-100">
            <h2 class="text-base font-bold text-slate-900">Security & Password</h2>
            <p class="text-xs text-slate-500">Ensure your account is using a long, random password to stay secure.</p>
        </div>

        <form method="POST" action="{{ route('customer.password.update') }}" class="space-y-4 max-w-xl">
            @csrf
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Current Password</label>
                <input type="password" name="current_password" required class="w-full text-xs px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:border-indigo-500 focus:bg-white focus:outline-none">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">New Password</label>
                    <input type="password" name="password" required class="w-full text-xs px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:border-indigo-500 focus:bg-white focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Confirm New Password</label>
                    <input type="password" name="password_confirmation" required class="w-full text-xs px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:border-indigo-500 focus:bg-white focus:outline-none">
                </div>
            </div>

            <button type="submit" class="px-6 py-2.5 bg-slate-900 hover:bg-indigo-600 text-white font-bold text-xs rounded-xl transition">
                Update Password
            </button>
        </form>
    </div>

</div>
@endsection
