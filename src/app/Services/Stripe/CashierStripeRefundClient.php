<?php

namespace App\Services\Stripe;

use App\Contracts\StripeRefundClientInterface;
use Laravel\Cashier\Cashier;

class CashierStripeRefundClient implements StripeRefundClientInterface
{
    public function createRefund(array $payload): array
    {
        $refund = Cashier::stripe()->refunds->create($payload);

        return $refund->toArray();
    }
}
