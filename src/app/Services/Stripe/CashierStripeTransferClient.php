<?php

namespace App\Services\Stripe;

use App\Contracts\StripeTransferClientInterface;
use Laravel\Cashier\Cashier;

class CashierStripeTransferClient implements StripeTransferClientInterface
{
    public function createTransfer(array $payload): array
    {
        $transfer = Cashier::stripe()->transfers->create($payload);

        return $transfer->toArray();
    }
}
