<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PayPalService
{
    protected $clientId;
    protected $secret;
    protected $mode;
    protected $baseUrl;

    public function __construct()
    {
        $this->clientId = env('PAYPAL_CLIENT_ID');
        $this->secret = env('PAYPAL_SECRET');
        $this->mode = env('PAYPAL_MODE', 'sandbox');

        $this->baseUrl = $this->mode === 'sandbox' 
            ? 'https://api-m.sandbox.paypal.com' 
            : 'https://api-m.paypal.com';
    }

    /**
     * Obtiene el Access Token de PayPal
     */
    public function getAccessToken()
    {
        $response = Http::asForm()->withBasicAuth($this->clientId, $this->secret)
            ->post("{$this->baseUrl}/v1/oauth2/token", [
                'grant_type' => 'client_credentials'
            ]);

        if ($response->successful()) {
            return $response->json()['access_token'];
        }

        Log::error('PayPal Auth Error: ' . $response->body());
        throw new \Exception('No se pudo autenticar con PayPal. Revisa tus credenciales.');
    }

    /**
     * Crea una orden en PayPal
     */
    public function createOrder($amount, $currency = 'USD', $referenceId = null)
    {
        $accessToken = $this->getAccessToken();

        $response = Http::withToken($accessToken)
            ->post("{$this->baseUrl}/v2/checkout/orders", [
                'intent' => 'CAPTURE',
                'purchase_units' => [
                    [
                        'reference_id' => (string) $referenceId,
                        'amount' => [
                            'currency_code' => $currency,
                            'value' => number_format($amount, 2, '.', '')
                        ]
                    ]
                ]
            ]);

        if ($response->successful()) {
            return $response->json();
        }

        Log::error('PayPal Create Order Error: ' . $response->body());
        throw new \Exception('Error al crear la orden en PayPal');
    }

    /**
     * Captura el pago de una orden previamente aprobada
     */
    public function capturePayment($orderId)
    {
        $accessToken = $this->getAccessToken();

        $response = Http::withHeaders([
            'Content-Type' => 'application/json'
        ])->withToken($accessToken)
            ->send('POST', "{$this->baseUrl}/v2/checkout/orders/{$orderId}/capture", [
                'body' => '{}'
            ]);

        if ($response->successful()) {
            return $response->json();
        }

        Log::error('PayPal Capture Error: ' . $response->body());
        throw new \Exception('Error al capturar el pago en PayPal');
    }
    
    /**
     * Verifica la firma del Webhook
     */
    public function verifyWebhookSignature($headers, $body)
    {
        $accessToken = $this->getAccessToken();
        
        $webhookId = env('PAYPAL_WEBHOOK_ID');
        if (!$webhookId) {
            Log::warning('No PAYPAL_WEBHOOK_ID configured. Assuming invalid signature.');
            return false;
        }

        $payload = [
            'auth_algo' => $headers['paypal-auth-algo'][0] ?? '',
            'cert_url' => $headers['paypal-cert-url'][0] ?? '',
            'transmission_id' => $headers['paypal-transmission-id'][0] ?? '',
            'transmission_sig' => $headers['paypal-transmission-sig'][0] ?? '',
            'transmission_time' => $headers['paypal-transmission-time'][0] ?? '',
            'webhook_id' => $webhookId,
            'webhook_event' => json_decode($body, true)
        ];

        $response = Http::withToken($accessToken)
            ->post("{$this->baseUrl}/v1/notifications/verify-webhook-signature", $payload);

        if ($response->successful()) {
            $data = $response->json();
            return $data['verification_status'] === 'SUCCESS';
        }

        Log::error('PayPal Webhook Verification Error: ' . $response->body());
        return false;
    }
}
