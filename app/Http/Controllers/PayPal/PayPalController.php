<?php

namespace App\Http\Controllers\PayPal;

use App\Http\Controllers\Controller;
use App\Services\PayPal\PayPalService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Throwable;

/**
 * Class PayPalController
 * @package App\Http\Controllers\PayPal
 */
class PayPalController extends Controller
{
    /**
     * @var PayPalService
     */
    protected $payPalService;

    /**
     * PayPalController constructor.
     *
     * @param PayPalService $payPalService
     */
    public function __construct(PayPalService $payPalService)
    {
        $this->payPalService = $payPalService;
    }

    /**
     * Display the payment initial page.
     *
     * @return View
     */
    public function index(): View
    {
        return view('PayPal.index');
    }

    /**
     * Initiate the PayPal transaction process.
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function process(Request $request): RedirectResponse
    {
        try {
            // Amount could be passed from request in a real scenario
            $amount = '10.00';
            $response = $this->payPalService->createOrder($amount);

            $approvalLink = $this->payPalService->getApprovalLink($response);

            if ($approvalLink) {
                return redirect()->away($approvalLink);
            }

            return redirect()
                ->route('paypal.index')
                ->with('error', 'Could not initiate PayPal payment links.');

        } catch (Throwable $th) {
            report($th);
            return redirect()
                ->route('paypal.index')
                ->with('error', $th->getMessage() ?: 'An error occurred during the payment initialization.');
        }
    }

    /**
     * Handle the successful PayPal transaction callback.
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function success(Request $request): RedirectResponse
    {
        try {
            $token = $request->get('token');
            if (!$token) {
                return redirect()->route('paypal.index')->with('error', 'Invalid transaction token.');
            }

            $response = $this->payPalService->captureOrder($token);

            if (isset($response['status']) && $response['status'] === 'COMPLETED') {
                return redirect()->route('paypal.index')->with('success', 'Transaction completed successfully.');
            }

            return redirect()->route('paypal.index')->with('error', 'Payment confirmation failed.');

        } catch (Throwable $th) {
            report($th);
            return redirect()->route('paypal.index')->with('error', $th->getMessage() ?: 'An error occurred during payment confirmation.');
        }
    }

    /**
     * Handle the canceled PayPal transaction callback.
     *
     * @return RedirectResponse
     */
    public function cancel(): RedirectResponse
    {
        return redirect()->route('paypal.index')->with('error', 'Payment was canceled by the user.');
    }
}
