<?php

namespace App\Contracts;

interface StripeRefundClientInterface
{
    public function createRefund(array $payload): array;
}
