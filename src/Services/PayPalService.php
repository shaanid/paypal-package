<?php

namespace Shaanid\PayPal\Services;

use Srmklive\PayPal\Services\PayPal as PayPalClient;
use Illuminate\Support\Facades\Log;
use Throwable;
use Exception;

/**
 * Class PayPalService
 * @package Shaanid\PayPal\Services
 */
class PayPalService
{
    /**
     * @var PayPalClient
     */
    protected $provider;

    /**
     * PayPalService constructor.
     *
     * @throws Throwable
     */
    public function __construct()
    {
        $this->provider = new PayPalClient;
        $this->provider->setApiCredentials(config('paypal'));
        $this->provider->getAccessToken();
    }

    /**
     * Create a new PayPal order.
     *
     * @param float|string $amount
     * @param string|null $currency
     * @param string|null $returnUrl
     * @param string|null $cancelUrl
     * @return array
     * @throws Exception
     */
    public function createOrder($amount = '10.00', $currency = null, $returnUrl = null, $cancelUrl = null): array
    {
        $currency = $currency ?: config('paypal.currency', 'USD');

        $data = [
            "intent" => "CAPTURE",
            "purchase_units" => [
                [
                    "amount" => [
                        "currency_code" => $currency,
                        "value" => (string) $amount
                    ]
                ]
            ],
            "application_context" => [
                "return_url" => $returnUrl ?: route('paypal.success'),
                "cancel_url" => $cancelUrl ?: route('paypal.cancel'),
            ]
        ];

        try {
            Log::info("PayPal: Creating order for amount {$amount} {$currency}");
            $response = $this->provider->createOrder($data);

            if (isset($response['error'])) {
                Log::error("PayPal: Order creation error - " . json_encode($response['error']));
                throw new Exception($response['error']['message'] ?? 'Failed to create PayPal order.');
            }

            Log::debug("PayPal: Order created successfully with ID: " . ($response['id'] ?? 'N/A'));
            return $response;
        } catch (Throwable $e) {
            Log::critical("PayPal: Order Creation Exception - " . $e->getMessage());
            throw new Exception("PayPal Order Creation Error: " . $e->getMessage());
        }
    }

    /**
     * Capture the payment for an existing PayPal order.
     *
     * @param string $token
     * @return array
     * @throws Exception
     */
    public function captureOrder(string $token): array
    {
        try {
            Log::info("PayPal: Capturing order with token {$token}");
            $response = $this->provider->capturePaymentOrder($token);

            if (isset($response['error'])) {
                Log::error("PayPal: Order capture error - " . json_encode($response['error']));
                throw new Exception($response['error']['message'] ?? 'Failed to capture PayPal order.');
            }

            Log::info("PayPal: Order captured successfully with status: " . ($response['status'] ?? 'UNKNOWN'));
            return $response;
        } catch (Throwable $e) {
            Log::critical("PayPal: Order Capture Exception - " . $e->getMessage());
            throw new Exception("PayPal Order Capture Error: " . $e->getMessage());
        }
    }

    /**
     * Get the approval link from the PayPal order response.
     *
     * @param array $response
     * @return string|null
     */
    public function getApprovalLink(array $response): ?string
    {
        if (!isset($response['links'])) {
            return null;
        }

        foreach ($response['links'] as $link) {
            if ($link['rel'] === 'approve') {
                return $link['href'];
            }
        }

        return null;
    }
}
