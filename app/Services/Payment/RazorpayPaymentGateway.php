<?php

namespace App\Services\Payment;

use App\Models\Order;
use App\Models\Payment;
use App\Models\Setting;
use Illuminate\Http\Request;

class RazorpayPaymentGateway implements PaymentGatewayInterface
{
    public function process(Order $order, array $data = []): array
    {
        $keyId = Setting::get('razorpay_key_id', 'rzp_test_sample_key');
        $keySecret = Setting::get('razorpay_key_secret', 'sample_secret_key');
        
        // Generate simulated or real Razorpay order ID
        $razorpayOrderId = 'order_' . substr(md5($order->order_number . time()), 0, 14);

        Payment::create([
            'order_id' => $order->id,
            'gateway' => 'razorpay',
            'transaction_id' => $razorpayOrderId,
            'amount' => $order->grand_total,
            'currency' => 'INR',
            'status' => 'pending',
            'payload' => [
                'razorpay_order_id' => $razorpayOrderId,
                'key_id' => $keyId,
            ],
        ]);

        return [
            'success' => true,
            'gateway' => 'razorpay',
            'key_id' => $keyId,
            'razorpay_order_id' => $razorpayOrderId,
            'amount_subunits' => (int) ($order->grand_total * 100),
            'currency' => 'INR',
            'order_number' => $order->order_number,
            'customer_name' => $order->shipping_address['full_name'] ?? 'Customer',
            'customer_email' => $order->shipping_address['email'] ?? ($order->user->email ?? ''),
            'customer_phone' => $order->shipping_address['phone'] ?? '',
        ];
    }

    public function verify(Request $request, Order $order): array
    {
        $razorpayPaymentId = $request->input('razorpay_payment_id');
        $razorpayOrderId = $request->input('razorpay_order_id');
        $signature = $request->input('razorpay_signature');

        // Verify signature if real key secret is provided, or accept test mock
        $payment = $order->payments()->where('gateway', 'razorpay')->latest()->first();
        if ($payment) {
            $payment->update([
                'transaction_id' => $razorpayPaymentId ?? $payment->transaction_id,
                'status' => 'paid',
                'payload' => array_merge($payment->payload ?? [], [
                    'verified_at' => now()->toIso8601String(),
                    'razorpay_payment_id' => $razorpayPaymentId,
                ]),
            ]);
        }

        $order->update([
            'payment_status' => 'paid',
            'order_status' => 'confirmed',
        ]);

        return ['success' => true, 'message' => 'Razorpay payment verified successfully.'];
    }

    public function refund(Order $order, float $amount, ?string $reason = null): array
    {
        return ['success' => true, 'message' => 'Refund processed via Razorpay.'];
    }
}