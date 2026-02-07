<?php

namespace Shaanid\PayPal\Http\Controllers;

use Illuminate\Routing\Controller;
use Shaanid\PayPal\Http\Requests\ProcessPaymentRequest;
use Shaanid\PayPal\Actions\CreatePayPalOrderAction;
use Shaanid\PayPal\Actions\CompletePayPalPaymentAction;
use Shaanid\PayPal\Actions\CancelPayPalPaymentAction;
use Shaanid\PayPal\DTOs\PaymentData;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Throwable;

class PayPalController extends Controller
{
    /**
     * Display the payment initial page.
     */
    public function index(): View
    {
        return view('paypal::index');
    }

    /**
     * Initiate the PayPal transaction process using the Create Action.
     */
    public function process(ProcessPaymentRequest $request, CreatePayPalOrderAction $action): RedirectResponse
    {
        try {
            $paymentData = PaymentData::fromArray($request->validated(), auth()->id());

            $approvalLink = $action->execute($paymentData->toCollection());

            return redirect()->away($approvalLink);

        } catch (Throwable $th) {
            return redirect()->route('paypal.index')->with('error', $th->getMessage() ?: 'An error occurred during payment initialization.');
        }
    }

    /**
     * Handle the successful PayPal callback using the Complete Action.
     */
    public function success(Request $request, CompletePayPalPaymentAction $action): RedirectResponse
    {
        try {
            $action->execute(collect($request->all()));

            return redirect()->route('paypal.index')->with('success', 'Transaction completed successfully.');

        } catch (Throwable $th) {
            return redirect()->route('paypal.index')->with('error', $th->getMessage() ?: 'An error occurred during payment confirmation.');
        }
    }

    /**
     * Handle the canceled PayPal callback using the Cancel Action.
     */
    public function cancel(Request $request, CancelPayPalPaymentAction $action): RedirectResponse
    {
        $action->execute(collect($request->all()));

        return redirect()->route('paypal.index')->with('error', 'Payment was canceled by the user.');
    }
}
