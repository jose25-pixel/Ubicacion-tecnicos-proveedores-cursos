<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TechnicianVerificationController extends Controller
{
    private const SECONDS_PER_QUESTION = 45;

    /**
     * @return array<int, array<string, mixed>>
     */
    private function questions(): array
    {
        return [
            [
                'id' => 'q1',
                'question' => 'Que accion es correcta antes de revisar una lavadora que no enciende?',
                'options' => [
                    'a' => 'Desconectar la energia y verificar voltaje de entrada',
                    'b' => 'Cambiar motor inmediatamente',
                    'c' => 'Golpear el panel para reiniciar',
                    'd' => 'Llenar agua para probar',
                ],
                'correct' => 'a',
            ],
            [
                'id' => 'q2',
                'question' => 'Un error de drenaje frecuente suele iniciar revisando:',
                'options' => [
                    'a' => 'Filtro, manguera y bomba de desague',
                    'b' => 'Color del gabinete',
                    'c' => 'Control remoto',
                    'd' => 'Tapa superior',
                ],
                'correct' => 'a',
            ],
            [
                'id' => 'q3',
                'question' => 'Que herramienta es clave para diagnostico electrico basico?',
                'options' => [
                    'a' => 'Martillo',
                    'b' => 'Multimetro',
                    'c' => 'Pistola de calor',
                    'd' => 'Lija',
                ],
                'correct' => 'b',
            ],
            [
                'id' => 'q4',
                'question' => 'Para proteger al cliente y al tecnico, siempre debes:',
                'options' => [
                    'a' => 'Trabajar sin orden de servicio',
                    'b' => 'Documentar diagnostico y repuestos',
                    'c' => 'Evitar explicar el problema',
                    'd' => 'No dar garantia',
                ],
                'correct' => 'b',
            ],
            [
                'id' => 'q5',
                'question' => 'Si la lavadora vibra excesivamente, que se revisa primero?',
                'options' => [
                    'a' => 'Nivelacion y distribucion de carga',
                    'b' => 'Color del detergente',
                    'c' => 'Puerta del cuarto',
                    'd' => 'WiFi del cliente',
                ],
                'correct' => 'a',
            ],
            [
                'id' => 'q6',
                'question' => 'Buena practica al cambiar una tarjeta electronica:',
                'options' => [
                    'a' => 'No tomar foto del cableado',
                    'b' => 'Manipular con energia conectada',
                    'c' => 'Etiquetar conectores antes de desmontar',
                    'd' => 'Quitar sensores para ahorrar tiempo',
                ],
                'correct' => 'c',
            ],
            [
                'id' => 'q7',
                'question' => 'Que indica continuidad correcta en un cable?',
                'options' => [
                    'a' => 'Resistencia infinita',
                    'b' => 'Circuito abierto',
                    'c' => 'Lectura baja o pitido en multimetro',
                    'd' => 'Chispa al tocar',
                ],
                'correct' => 'c',
            ],
            [
                'id' => 'q8',
                'question' => 'El mantenimiento preventivo ayuda principalmente a:',
                'options' => [
                    'a' => 'Aumentar fallas',
                    'b' => 'Reducir averias y extender vida util',
                    'c' => 'Anular toda garantia',
                    'd' => 'Eliminar consumo electrico',
                ],
                'correct' => 'b',
            ],
            [
                'id' => 'q9',
                'question' => 'Cuando no hay centrifugado, revisar primero:',
                'options' => [
                    'a' => 'Seguro de tapa/sensor de puerta',
                    'b' => 'Etiquetas externas',
                    'c' => 'Pintura del panel',
                    'd' => 'Manual sin abrir equipo',
                ],
                'correct' => 'a',
            ],
            [
                'id' => 'q10',
                'question' => 'Trato profesional con el cliente implica:',
                'options' => [
                    'a' => 'Cobrar sin detallar',
                    'b' => 'Comunicar tiempos, costos y garantia',
                    'c' => 'No responder mensajes',
                    'd' => 'Cambiar repuestos sin autorizacion',
                ],
                'correct' => 'b',
            ],
        ];
    }

    public function create(Request $request): View
    {
        $user = $request->user();
        abort_unless($user->role === 'technician', 403);

        $questions = $this->questions();
        $totalSeconds = count($questions) * self::SECONDS_PER_QUESTION;

        if (! $user->isTechnicianVerified() &&
            ($user->technician_verification_locked_until === null || $user->technician_verification_locked_until->isPast())) {
            session(['technician_quiz_started_at' => now()->toIso8601String()]);
        }

        return view('verification.technician', [
            'questions' => $questions,
            'questionSeconds' => self::SECONDS_PER_QUESTION,
            'totalSeconds' => $totalSeconds,
            'user' => $user,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();
        abort_unless($user->role === 'technician', 403);

        if ($user->isTechnicianVerified()) {
            return redirect()
                ->route('technician.verification.create')
                ->with('verification_result', [
                    'score' => (int) ($user->technician_verification_score ?? 0),
                    'passed' => true,
                    'correct' => null,
                    'total' => count($this->questions()),
                    'message' => 'Tu cuenta ya esta verificada.',
                ]);
        }

        if ($user->technician_verification_locked_until !== null && $user->technician_verification_locked_until->isFuture()) {
            return redirect()
                ->route('technician.verification.create')
                ->withErrors([
                    'verification_lock' => 'Debes esperar hasta '.$user->technician_verification_locked_until->format('d/m/Y H:i').' para volver a intentar.',
                ]);
        }

        $questions = $this->questions();
        $totalSeconds = count($questions) * self::SECONDS_PER_QUESTION;
        $rules = [];
        foreach ($questions as $question) {
            $rules['answers.'.$question['id']] = ['nullable', 'in:a,b,c,d'];
        }
        $rules['elapsed_seconds'] = ['nullable', 'integer', 'min:0'];
        $rules['timed_out'] = ['nullable', 'in:0,1'];

        $validated = $request->validate($rules);
        $answers = $validated['answers'] ?? [];

        $startedAtIso = session('technician_quiz_started_at');
        $serverElapsed = 0;
        if (is_string($startedAtIso)) {
            $started = Carbon::parse($startedAtIso);
            $serverElapsed = max(0, $started->diffInSeconds(now()));
        }

        $clientElapsed = (int) ($validated['elapsed_seconds'] ?? 0);
        $timedOut = ($validated['timed_out'] ?? '0') === '1'
            || $clientElapsed > $totalSeconds
            || $serverElapsed > ($totalSeconds + 5);

        if ($timedOut) {
            $user->technician_verification_score = 0;
            $user->technician_verification_attempts = (int) $user->technician_verification_attempts + 1;
            $user->technician_verified_at = null;
            $user->technician_verification_locked_until = now()->addDay();
            $user->save();

            return redirect()
                ->route('technician.verification.create')
                ->withErrors([
                    'verification_lock' => 'Se agoto el tiempo. Debes esperar 24 horas para intentar de nuevo.',
                ]);
        }

        $correct = 0;
        foreach ($questions as $question) {
            if (($answers[$question['id']] ?? null) === $question['correct']) {
                $correct++;
            }
        }

        $score = (int) round(($correct / count($questions)) * 100);
        $passed = $score >= 70;

        $user->technician_verification_score = $score;
        $user->technician_verification_attempts = (int) $user->technician_verification_attempts + 1;
        $user->technician_verified_at = $passed ? now() : null;
        $user->technician_verification_locked_until = null;
        $user->save();

        return redirect()
            ->route('technician.verification.create')
            ->with('verification_result', [
                'score' => $score,
                'passed' => $passed,
                'correct' => $correct,
                'total' => count($questions),
            ]);
    }
}
