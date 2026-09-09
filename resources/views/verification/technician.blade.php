<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Verificacion Tecnica') }}
        </h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-4">
            <div class="bg-white border border-gray-200 shadow-sm sm:rounded-lg p-5">
                <p class="text-gray-800"><strong>Estado actual:</strong>
                    @if ($user->isTechnicianVerified())
                        <span class="text-emerald-700 font-semibold">Tecnico verificado</span>
                    @else
                        <span class="text-amber-700 font-semibold">Pendiente de verificacion</span>
                    @endif
                </p>
                <p class="text-sm text-gray-600 mt-1">Intentos: {{ (int) $user->technician_verification_attempts }} | Ultimo puntaje: {{ $user->technician_verification_score ?? 'N/D' }}</p>
                @if ($user->technician_verification_locked_until && $user->technician_verification_locked_until->isFuture())
                    <p class="text-sm text-amber-700 mt-2">Bloqueado hasta: {{ $user->technician_verification_locked_until->format('d/m/Y H:i') }}</p>
                @endif
            </div>

            @if ($errors->has('verification_lock'))
                <div class="rounded-lg p-4 border bg-amber-50 border-amber-200 text-amber-900">
                    <p class="font-semibold">{{ $errors->first('verification_lock') }}</p>
                </div>
            @endif

            @if (session('verification_result'))
                @php($result = session('verification_result'))
                <div class="rounded-lg p-4 border {{ $result['passed'] ? 'bg-emerald-50 border-emerald-200 text-emerald-900' : 'bg-amber-50 border-amber-200 text-amber-900' }}">
                    <p class="font-semibold">Resultado del examen: {{ $result['score'] }}%</p>
                    <p class="text-sm mt-1">
                        @if (!empty($result['message']))
                            {{ $result['message'] }}
                        @else
                            Respuestas correctas: {{ $result['correct'] }} de {{ $result['total'] }}. {{ $result['passed'] ? 'Aprobado.' : 'Necesitas 70% para aprobar.' }}
                        @endif
                    </p>
                </div>
            @endif

            @if (! $user->isTechnicianVerified() && !($user->technician_verification_locked_until && $user->technician_verification_locked_until->isFuture()))
                <form id="quiz-form" method="POST" action="{{ route('technician.verification.store') }}" class="bg-white border border-gray-200 shadow-sm sm:rounded-lg p-5 space-y-6">
                    @csrf

                    <div class="rounded-md border border-indigo-200 bg-indigo-50 p-3 flex items-center justify-between">
                        <p class="text-sm text-indigo-900 font-semibold">Tiempo por pregunta: {{ $questionSeconds }} segundos</p>
                        <p class="text-sm text-indigo-900">Pregunta <span id="current-question">1</span>/{{ count($questions) }} | Restante: <span id="question-timer">{{ $questionSeconds }}</span>s</p>
                    </div>

                    <input type="hidden" name="elapsed_seconds" id="elapsed-seconds" value="0">
                    <input type="hidden" name="timed_out" id="timed-out" value="0">

                    @foreach ($questions as $index => $question)
                        <div class="quiz-question {{ $index === 0 ? '' : 'hidden' }}" data-index="{{ $index }}">
                            <p class="font-semibold text-gray-900">{{ $index + 1 }}. {{ $question['question'] }}</p>
                            <div class="mt-2 space-y-2">
                                @foreach ($question['options'] as $key => $option)
                                    <label class="flex items-start gap-2 text-sm text-gray-700">
                                        <input type="radio" name="answers[{{ $question['id'] }}]" value="{{ $key }}" {{ old('answers.'.$question['id']) === $key ? 'checked' : '' }} class="mt-1">
                                        <span>{{ strtoupper($key) }}. {{ $option }}</span>
                                    </label>
                                @endforeach
                            </div>
                            <x-input-error class="mt-2" :messages="$errors->get('answers.'.$question['id'])" />
                        </div>
                    @endforeach

                    <div class="flex items-center justify-between gap-3">
                        <button type="button" id="next-question" class="inline-flex items-center px-4 py-2 rounded-md bg-gray-700 text-white text-sm font-semibold hover:bg-gray-600">Siguiente</button>
                        <x-primary-button>
                            {{ __('Enviar examen') }}
                        </x-primary-button>
                    </div>
                </form>
            @endif
        </div>
    </div>
</x-app-layout>

@push('scripts')
    <script>
        window.addEventListener('load', () => {
            const form = document.getElementById('quiz-form');
            if (!form) {
                return;
            }

            const questions = Array.from(document.querySelectorAll('.quiz-question'));
            const timerLabel = document.getElementById('question-timer');
            const currentLabel = document.getElementById('current-question');
            const nextButton = document.getElementById('next-question');
            const elapsedInput = document.getElementById('elapsed-seconds');
            const timedOutInput = document.getElementById('timed-out');

            let currentIndex = 0;
            let secondsLeft = {{ (int) $questionSeconds }};
            let elapsed = 0;
            const perQuestionSeconds = {{ (int) $questionSeconds }};

            function showQuestion(index) {
                questions.forEach((question, i) => {
                    question.classList.toggle('hidden', i !== index);
                });
                currentLabel.textContent = (index + 1).toString();
            }

            function jumpToNextQuestion() {
                if (currentIndex < questions.length - 1) {
                    currentIndex += 1;
                    secondsLeft = perQuestionSeconds;
                    timerLabel.textContent = secondsLeft.toString();
                    showQuestion(currentIndex);
                }
            }

            nextButton.addEventListener('click', jumpToNextQuestion);

            const interval = setInterval(() => {
                elapsed += 1;
                elapsedInput.value = elapsed.toString();
                secondsLeft -= 1;
                timerLabel.textContent = secondsLeft.toString();

                if (secondsLeft <= 0) {
                    timedOutInput.value = '1';
                    form.submit();
                }
            }, 1000);

            form.addEventListener('submit', () => {
                clearInterval(interval);
            });

            showQuestion(currentIndex);
        });
    </script>
@endpush
