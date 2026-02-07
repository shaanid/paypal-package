<?php

namespace Shaanid\PayPal\Actions;

use Shaanid\PayPal\Models\Transaction;
use Shaanid\PayPal\Services\PayPalService;
use Exception;
use Illuminate\Support\Collection;
use Throwable;

class CompletePayPalPaymentAction
{
    public function __construct(
        private readonly PayPalService $payPalService
    ) {
    }

    /**
     * @param Collection $collection
     * @return Transaction
     * @throws Throwable
     */
    public function execute(Collection $collection): Transaction
    {
        try {
            $token = $collection->get('token');

            if (!$token) {
                throw new Exception('PayPal token is required for completion.');
            }

            $transaction = Transaction::where('transaction_id', $token)->firstOrFail();

            $response = $this->payPalService->captureOrder($token);

            if (isset($response['status']) && $response['status'] === 'COMPLETED') {
                $transaction->update([
                    'status' => 'COMPLETED',
                    'payload' => array_merge($transaction->payload ?? [], ['capture' => $response])
                ]);

                return $transaction;
            }

            $transaction->update(['status' => 'FAILED']);

            throw new Exception('Payment capture failed or was not completed.');

        } catch (Throwable $e) {
            if (isset($transaction)) {
                $transaction->update(['status' => 'FAILED']);
            }
            throw $e;
        }
    }
}
