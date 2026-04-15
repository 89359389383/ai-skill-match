<?php

namespace App\Contracts;

interface StripeTransferClientInterface
{
    /**
     * @param array<string, mixed> $payload
     * @return array<string, mixed>
     */
    public function createTransfer(array $payload): array;
}
