<?php

namespace App\Services\Payment;

use InvalidArgumentException;

class PaymentGatewayFactory
{
    public static function make(string $gateway): PaymentGatewayInterface
    {
        return match (strtolower($gateway)) {
            'cod', 'cash_on_delivery' => new CodPaymentGateway(),
            'razorpay' => new RazorpayPaymentGateway(),
            'stripe' => new StripePaymentGateway(),
            default => throw new InvalidArgumentException("Unsupported payment gateway: {$gateway}"),
        };
    }
}