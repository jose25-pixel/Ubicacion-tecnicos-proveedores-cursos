<?php

namespace App\Http\Controllers;

use App\Models\CourseEnrollment;
use App\Models\CourseOrder;
use App\Models\PaypalWebhookEvent;
use App\Services\PaypalGateway;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaypalWebhookController extends Controller
{
    public function handle(Request $request, PaypalGateway $paypal): JsonResponse
    {
        $payload = $request->json()->all();
        $eventId = (string) data_get($payload, 'id');
        $eventType = (string) data_get($payload, 'event_type');

        if ($eventId === '' || $eventType === '') {
            return response()->json(['message' => 'Evento PayPal invalido.'], 422);
        }

        $webhookEvent = PaypalWebhookEvent::firstOrCreate(
            ['event_id' => $eventId],
            [
                'event_type' => $eventType,
                'resource_type' => data_get($payload, 'resource_type'),
                'transmission_id' => $request->header('Paypal-Transmission-Id'),
                'transmission_time' => $request->header('Paypal-Transmission-Time'),
                'payload' => $payload,
                'is_signature_valid' => false,
            ]
        );

        if (! $webhookEvent->wasRecentlyCreated) {
            return response()->json(['message' => 'Evento ya procesado.'], 200);
        }

        $isValidSignature = $paypal->verifyWebhookSignature([
            'paypal-auth-algo' => $request->header('Paypal-Auth-Algo'),
            'paypal-cert-url' => $request->header('Paypal-Cert-Url'),
            'paypal-transmission-id' => $request->header('Paypal-Transmission-Id'),
            'paypal-transmission-sig' => $request->header('Paypal-Transmission-Sig'),
            'paypal-transmission-time' => $request->header('Paypal-Transmission-Time'),
        ], $payload);

        $webhookEvent->update([
            'is_signature_valid' => $isValidSignature,
        ]);

        if (! $isValidSignature) {
            return response()->json(['message' => 'Firma PayPal invalida.'], 400);
        }

        if ($eventType === 'PAYMENT.CAPTURE.COMPLETED') {
            $this->handleCaptureCompleted($payload, $webhookEvent);
        }

        $webhookEvent->update([
            'processed_at' => now(),
        ]);

        return response()->json(['message' => 'OK'], 200);
    }

    private function handleCaptureCompleted(array $payload, PaypalWebhookEvent $webhookEvent): void
    {
        $providerOrderId = (string) data_get($payload, 'resource.supplementary_data.related_ids.order_id');
        $providerCaptureId = (string) data_get($payload, 'resource.id');

        if ($providerOrderId === '' || $providerCaptureId === '') {
            return;
        }

        $order = CourseOrder::query()
            ->with('items')
            ->where('provider', 'paypal')
            ->where('provider_order_id', $providerOrderId)
            ->first();

        if (! $order) {
            return;
        }

        DB::transaction(function () use ($order, $providerCaptureId, $payload, $webhookEvent) {
            $order->update([
                'status' => 'paid',
                'provider_capture_id' => $providerCaptureId,
                'payer_email' => data_get($payload, 'resource.payer.email_address'),
                'payer_id' => data_get($payload, 'resource.payer.payer_id'),
                'paid_at' => now(),
            ]);

            if ($order->user_id !== null) {
                foreach ($order->items as $item) {
                    CourseEnrollment::updateOrCreate(
                        [
                            'user_id' => $order->user_id,
                            'course_id' => $item->course_id,
                        ],
                        [
                            'course_order_id' => $order->id,
                            'status' => 'active',
                            'started_at' => now(),
                            'expires_at' => null,
                        ]
                    );
                }
            }

            $webhookEvent->update([
                'course_order_id' => $order->id,
            ]);
        });
    }
}
