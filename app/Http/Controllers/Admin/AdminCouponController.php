<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use Illuminate\Http\Request;

class AdminCouponController extends Controller
{
    public function index()
    {
        $coupons = Coupon::withCount('usages')->latest()->paginate(15);
        return view('admin.coupons.index', compact('coupons'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|string|max:50|unique:coupons,code',
            'type' => 'required|in:percentage,fixed',
            'value' => 'required|numeric|min:0',
            'min_order_value' => 'nullable|numeric|min:0',
            'max_discount' => 'nullable|numeric|min:0',
            'start_date' => 'nullable|date',
            'expiry_date' => 'nullable|date|after_or_equal:start_date',
            'usage_limit' => 'nullable|integer|min:1',
            'usage_per_user' => 'nullable|integer|min:1',
        ]);

        Coupon::create([
            'code' => strtoupper(trim($request->code)),
            'type' => $request->type,
            'value' => $request->value,
            'min_order_value' => $request->get('min_order_value', 0),
            'max_discount' => $request->max_discount,
            'start_date' => $request->start_date,
            'expiry_date' => $request->expiry_date,
            'usage_limit' => $request->usage_limit,
            'usage_per_user' => $request->get('usage_per_user', 1),
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('admin.coupons.index')->with('success', 'Coupon created successfully.');
    }

    public function toggle(int $id)
    {
        $coupon = Coupon::findOrFail($id);
        $coupon->is_active = !$coupon->is_active;
        $coupon->save();

        return redirect()->back()->with('success', 'Coupon status updated.');
    }

    public function destroy(int $id)
    {
        $coupon = Coupon::findOrFail($id);
        $coupon->delete();
        return redirect()->route('admin.coupons.index')->with('success', 'Coupon deleted.');
    }
}