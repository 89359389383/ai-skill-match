<?php

namespace App\Services\Stripe;

use App\Contracts\StripeCheckoutClientInterface;
use Laravel\Cashier\Cashier;

class CashierStripeCheckoutClient implements StripeCheckoutClientInterface
{
    public function createSession(array $payload): array
    {
        $session = Cashier::stripe()->checkout->sessions->create($payload);

        return $session->toArray();
    }
}
