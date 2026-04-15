<?php

namespace App\Contracts;

interface StripeCheckoutClientInterface
{
    /**
     * @param array<string, mixed> $payload
     * @return array<string, mixed>
     */
    public function createSession(array $payload): array;
}
