<?php

namespace App\Services\Payment;

use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\Request;

class CodPaymentGateway implements PaymentGatewayInterface
{
    public function process(Order $order, array $data = []): array
    {
        Payment::create([
            'order_id' => $order->id,
            'gateway' => 'cod',
            'transaction_id' => 'COD-' . strtoupper(uniqid()),
            'amount' => $order->grand_total,
            'currency' => 'INR',
            'status' => 'pending',
            'payload' => ['instruction' => 'Cash to be collected upon delivery'],
        ]);

        $order->update([
            'payment_method' => 'cod',
            'payment_status' => 'pending',
            'order_status' => 'confirmed',
        ]);

        return [
            'success' => true,
            'gateway' => 'cod',
            'message' => 'Order placed successfully with Cash on Delivery.',
        ];
    }

    public function verify(Request $request, Order $order): array
    {
        return ['success' => true, 'message' => 'COD requires no online verification.'];
    }

    public function refund(Order $order, float $amount, ?string $reason = null): array
    {
        return ['success' => true, 'message' => 'Cash refund logged.'];
    }
}