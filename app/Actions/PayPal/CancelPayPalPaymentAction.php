<?php

namespace App\Actions\PayPal;

use Illuminate\Support\Collection;
use App\Models\Transaction;

class CancelPayPalPaymentAction
{
    /**
     * @param Collection $collection
     * @return void
     */
    public function execute(Collection $collection): void
    {
        $token = $collection->get('token');

        if (!$token) {
            return;
        }

        $transaction = Transaction::where('transaction_id', $token)->first();

        if ($transaction) {
            $transaction->update(['status' => 'CANCELED']);
        }
    }
}
