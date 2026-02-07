<?php

namespace App\Actions\PayPal;

use App\Models\Transaction;
use App\Services\PayPal\PayPalService;
use App\Events\PayPal\PaymentCompleted;
use App\Events\PayPal\PaymentFailed;
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

                // event(new PaymentCompleted($transaction));

                return $transaction;
            }

            $transaction->update(['status' => 'FAILED']);
            // event(new PaymentFailed($transaction));

            throw new Exception('Payment capture failed or was not completed.');

        } catch (Throwable $e) {
            $transaction->update(['status' => 'FAILED']);
            throw $e;
        }
    }
}
