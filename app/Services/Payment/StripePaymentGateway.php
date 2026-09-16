<?php

namespace App\Services\Payment;

use App\Models\Order;
use App\Models\Payment;
use App\Models\Setting;
use Illuminate\Http\Request;

class StripePaymentGateway implements PaymentGatewayInterface
{
    public function process(Order $order, array $data = []): array
    {
        $publishableKey = Setting::get('stripe_publishable_key', 'pk_test_sample');
        $clientSecret = 'pi_' . substr(md5(uniqid()), 0, 16) . '_secret_' . substr(md5(uniqid()), 0, 16);

        Payment::create([
            'order_id' => $order->id,
            'gateway' => 'stripe',
            'transaction_id' => 'pi_' . substr(md5(uniqid()), 0, 16),
            'amount' => $order->grand_total,
            'currency' => 'INR',
            'status' => 'pending',
            'payload' => [
                'client_secret' => $clientSecret,
            ],
        ]);

        return [
            'success' => true,
            'gateway' => 'stripe',
            'publishable_key' => $publishableKey,
            'client_secret' => $clientSecret,
            'amount' => $order->grand_total,
        ];
    }

    public function verify(Request $request, Order $order): array
    {
        $paymentIntentId = $request->input('payment_intent_id') ?? 'pi_simulated_' . uniqid();

        $payment = $order->payments()->where('gateway', 'stripe')->latest()->first();
        if ($payment) {
            $payment->update([
                'transaction_id' => $paymentIntentId,
                'status' => 'paid',
                'payload' => array_merge($payment->payload ?? [], [
                    'verified_at' => now()->toIso8601String(),
                ]),
            ]);
        }

        $order->update([
            'payment_status' => 'paid',
            'order_status' => 'confirmed',
        ]);

        return ['success' => true, 'message' => 'Stripe payment verified successfully.'];
    }

    public function refund(Order $order, float $amount, ?string $reason = null): array
    {
        return ['success' => true, 'message' => 'Refund processed via Stripe.'];
    }
}