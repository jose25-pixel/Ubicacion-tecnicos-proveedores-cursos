<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Models\CourseOrder;
use App\Services\PaypalGateway;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CourseCheckoutController extends Controller
{
    public function createPaypalOrder(Request $request, Course $course, PaypalGateway $paypal): RedirectResponse
    {
        abort_unless($course->is_published, 404);

        if ((float) $course->price <= 0) {
            return redirect()->route('dashboard')->with('status', 'Este curso no requiere pago.');
        }

        $alreadyEnrolled = CourseEnrollment::query()
            ->where('user_id', $request->user()->id)
            ->where('course_id', $course->id)
            ->where('status', 'active')
            ->exists();

        if ($alreadyEnrolled) {
            return redirect()->route('dashboard')->with('status', 'Ya tienes acceso a este curso.');
        }

        $order = DB::transaction(function () use ($request, $course) {
            $order = CourseOrder::create([
                'user_id' => $request->user()->id,
                'status' => 'pending',
                'subtotal' => $course->price,
                'tax' => 0,
                'total' => $course->price,
                'currency' => $course->currency,
                'provider' => 'paypal',
            ]);

            $order->items()->create([
                'course_id' => $course->id,
                'unit_price' => $course->price,
                'quantity' => 1,
                'line_total' => $course->price,
                'meta' => [
                    'course_title' => $course->title,
                ],
            ]);

            return $order;
        });

        try {
            $paypalOrder = $paypal->createOrder(
                $order,
                $course,
                route('courses.checkout.paypal.return', $order),
                route('courses.checkout.paypal.cancel', $order)
            );

            $approveLink = collect((array) data_get($paypalOrder, 'links'))
                ->firstWhere('rel', 'approve')['href'] ?? null;

            if (! is_string($approveLink) || $approveLink === '') {
                throw new \RuntimeException('PayPal no devolvio enlace de aprobacion.');
            }

            $order->update([
                'provider_order_id' => data_get($paypalOrder, 'id'),
                'status' => 'created',
                'meta' => [
                    'paypal_order' => $paypalOrder,
                ],
            ]);

            return redirect()->away($approveLink);
        } catch (\Throwable $e) {
            $order->update([
                'status' => 'failed',
                'failed_at' => now(),
                'meta' => [
                    'error' => $e->getMessage(),
                ],
            ]);

            return redirect()->route('dashboard')
                ->withErrors(['paypal' => 'No se pudo iniciar el pago PayPal. Intenta de nuevo.']);
        }
    }

    public function paypalReturn(CourseOrder $order): RedirectResponse
    {
        abort_if($order->user_id !== auth()->id(), 403);

        if ($order->status === 'created') {
            $order->update([
                'status' => 'approved',
                'approved_at' => now(),
            ]);
        }

        return redirect()->route('dashboard')
            ->with('status', 'Pago aprobado en PayPal. Estamos confirmando tu acceso.');
    }

    public function paypalCancel(CourseOrder $order): RedirectResponse
    {
        abort_if($order->user_id !== auth()->id(), 403);

        if (! in_array($order->status, ['paid', 'cancelled'], true)) {
            $order->update([
                'status' => 'cancelled',
                'cancelled_at' => now(),
            ]);
        }

        return redirect()->route('dashboard')
            ->withErrors(['paypal' => 'Pago cancelado por el usuario.']);
    }
}
