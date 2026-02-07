<?php

namespace Shaanid\PayPal\DTOs;

use Illuminate\Support\Collection;

class PaymentData
{
    public function __construct(
        public readonly string $amount,
        public readonly string $currency,
        public readonly ?int $userId = null,
    ) {
    }

    /**
     * Create from validated request data.
     */
    public static function fromArray(array $data, ?int $userId = null): self
    {
        return new self(
            amount: $data['amount'] ?? '10.00',
            currency: $data['currency'] ?? config('paypal.currency', 'USD'),
            userId: $userId
        );
    }

    /**
     * Convert the DTO to a Laravel collection with snake_case keys.
     */
    public function toCollection(): Collection
    {
        return collect([
            'amount' => $this->amount,
            'currency' => $this->currency,
            'user_id' => $this->userId,
        ]);
    }
}
