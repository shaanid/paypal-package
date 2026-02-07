<?php

namespace App\Actions\PayPal;

use Exception;
use Illuminate\Support\Collection;
use App\Models\Transaction;
use App\Services\PayPal\PayPalService;
use Illuminate\Support\Str;
use Throwable;

class CreatePayPalOrderAction
{
    public function __construct(private readonly PayPalService $payPalService) {}

    /**
     * @param Collection $collection
     * @return string Approval URL
     * @throws Throwable
     */
    public function execute(Collection $collection): string
    {
        $amount = $collection->get('amount', '10.00');
        $currency = $collection->get('currency', config('paypal.currency', 'USD'));
        $userId = $collection->get('user_id');

        /* Create internal transaction record */
        $transaction = Transaction::create([
            'reference_id' => 'ORD-' . strtoupper(Str::random(10)),
            'amount' => $amount,
            'currency' => $currency,
            'status' => 'PENDING',
            'user_id' => $userId,
        ]);

        try {
            /* Request order from PayPal */
            $response = $this->payPalService->createOrder($amount, $currency);

            $approvalLink = $this->payPalService->getApprovalLink($response);

            if (!$approvalLink || !isset($response['id'])) {
                $transaction->update(['status' => 'FAILED']);
                throw new Exception('Could not retrieve PayPal approval link.');
            }

            /* Update transaction with PayPal's ID */
            $transaction->update([
                'transaction_id' => $response['id'],
                'payload' => $response
            ]);

            return $approvalLink;

        } catch (Throwable $e) {
            $transaction->update(['status' => 'FAILED']);
            throw $e;
        }
    }
}
