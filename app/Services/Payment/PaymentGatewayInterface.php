<?php

namespace App\Services\Payment;

use App\Models\Order;
use Illuminate\Http\Request;

interface PaymentGatewayInterface
{
    /**
     * Initialize or process payment for order.
     * Returns array with status, redirect_url, gateway_order_id or instructions.
     */
    public function process(Order $order, array $data = []): array;

    /**
     * Verify payment response / webhook from provider.
     */
    public function verify(Request $request, Order $order): array;

    /**
     * Process refund if applicable.
     */
    public function refund(Order $order, float $amount, ?string $reason = null): array;
}