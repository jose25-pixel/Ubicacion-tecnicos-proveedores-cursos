<?php

namespace App\Services;

use App\Models\Course;
use App\Models\CourseOrder;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class PaypalGateway
{
    public function createOrder(CourseOrder $order, Course $course, string $returnUrl, string $cancelUrl): array
    {
        $accessToken = $this->getAccessToken();
        $baseUrl = $this->getBaseUrl();

        $payload = [
            'intent' => 'CAPTURE',
            'purchase_units' => [[
                'reference_id' => 'course-order-'.$order->id,
                'custom_id' => (string) $order->id,
                'description' => 'Compra de curso: '.$course->title,
                'amount' => [
                    'currency_code' => $order->currency,
                    'value' => number_format((float) $order->total, 2, '.', ''),
                ],
            ]],
            'application_context' => [
                'brand_name' => config('app.name'),
                'user_action' => 'PAY_NOW',
                'return_url' => $returnUrl,
                'cancel_url' => $cancelUrl,
            ],
        ];

        $response = Http::acceptJson()
            ->withToken($accessToken)
            ->timeout(20)
            ->post($baseUrl.'/v2/checkout/orders', $payload);

        if ($response->failed()) {
            throw new RuntimeException('No se pudo crear la orden en PayPal: '.$response->body());
        }

        return $response->json();
    }

    public function verifyWebhookSignature(array $headers, array $payload): bool
    {
        $webhookId = (string) config('services.paypal.webhook_id');
        if ($webhookId === '') {
            return app()->environment('local');
        }

        $accessToken = $this->getAccessToken();
        $baseUrl = $this->getBaseUrl();

        $verificationPayload = [
            'auth_algo' => $headers['paypal-auth-algo'] ?? null,
            'cert_url' => $headers['paypal-cert-url'] ?? null,
            'transmission_id' => $headers['paypal-transmission-id'] ?? null,
            'transmission_sig' => $headers['paypal-transmission-sig'] ?? null,
            'transmission_time' => $headers['paypal-transmission-time'] ?? null,
            'webhook_id' => $webhookId,
            'webhook_event' => $payload,
        ];

        if (in_array(null, $verificationPayload, true)) {
            return false;
        }

        $response = Http::acceptJson()
            ->withToken($accessToken)
            ->timeout(20)
            ->post($baseUrl.'/v1/notifications/verify-webhook-signature', $verificationPayload);

        if ($response->failed()) {
            return false;
        }

        return data_get($response->json(), 'verification_status') === 'SUCCESS';
    }

    private function getAccessToken(): string
    {
        $baseUrl = $this->getBaseUrl();
        $clientId = (string) config('services.paypal.client_id');
        $clientSecret = (string) config('services.paypal.client_secret');

        if ($clientId === '' || $clientSecret === '') {
            throw new RuntimeException('Faltan credenciales de PayPal en config/services.php y .env.');
        }

        $response = Http::asForm()
            ->withBasicAuth($clientId, $clientSecret)
            ->acceptJson()
            ->timeout(20)
            ->post($baseUrl.'/v1/oauth2/token', [
                'grant_type' => 'client_credentials',
            ]);

        if ($response->failed()) {
            throw new RuntimeException('No se pudo obtener access token de PayPal: '.$response->body());
        }

        $token = (string) data_get($response->json(), 'access_token');
        if ($token === '') {
            throw new RuntimeException('PayPal no devolvio access token.');
        }

        return $token;
    }

    private function getBaseUrl(): string
    {
        $mode = (string) config('services.paypal.mode', 'sandbox');

        return $mode === 'live'
            ? 'https://api-m.paypal.com'
            : 'https://api-m.sandbox.paypal.com';
    }
}
