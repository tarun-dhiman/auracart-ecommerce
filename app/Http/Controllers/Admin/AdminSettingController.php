<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class AdminSettingController extends Controller
{
    public function index()
    {
        $settings = [
            'store_name' => Setting::get('store_name', 'AuraCart Luxury E-Commerce'),
            'store_email' => Setting::get('store_email', 'support@auracart.com'),
            'store_phone' => Setting::get('store_phone', '+91 98765 43210'),
            'store_address' => Setting::get('store_address', '101 Horizon Towers, Bengaluru, Karnataka, 560001, India'),
            'store_gst' => Setting::get('store_gst', '29ABCDE1234F1Z5'),
            'currency_symbol' => Setting::get('currency_symbol', '₹'),
            'tax_rate' => Setting::get('tax_rate', '18.0'),
            'shipping_fee' => Setting::get('shipping_fee', '99.0'),
            'free_shipping_threshold' => Setting::get('free_shipping_threshold', '999.0'),
            'enable_cod' => Setting::get('enable_cod', '1'),
            'enable_razorpay' => Setting::get('enable_razorpay', '1'),
            'razorpay_key_id' => Setting::get('razorpay_key_id', 'rzp_test_sample_key'),
            'razorpay_key_secret' => Setting::get('razorpay_key_secret', 'sample_secret_key'),
            'enable_stripe' => Setting::get('enable_stripe', '1'),
            'stripe_publishable_key' => Setting::get('stripe_publishable_key', 'pk_test_sample_key'),
            'stripe_secret_key' => Setting::get('stripe_secret_key', 'sk_test_sample_secret'),
        ];

        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $keys = [
            'store_name', 'store_email', 'store_phone', 'store_address', 'store_gst',
            'currency_symbol', 'tax_rate', 'shipping_fee', 'free_shipping_threshold',
            'enable_cod', 'enable_razorpay', 'razorpay_key_id', 'razorpay_key_secret',
            'enable_stripe', 'stripe_publishable_key', 'stripe_secret_key'
        ];

        foreach ($keys as $key) {
            if ($request->has($key)) {
                Setting::set($key, $request->input($key));
            }
        }

        return redirect()->back()->with('success', 'Store settings updated successfully.');
    }
}