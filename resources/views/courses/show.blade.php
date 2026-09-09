<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $course->title }} | Cursos</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-100 text-slate-900 antialiased">
    <header class="bg-white border-b border-slate-200">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <a href="{{ route('courses.index') }}" class="text-sm font-semibold text-slate-700 hover:text-slate-900">Volver a cursos</a>
            @auth
                <a href="{{ route('courses.my') }}" class="px-3 py-2 rounded-md bg-slate-900 text-white text-xs font-semibold">Mis cursos</a>
            @else
                <a href="{{ route('login') }}" class="px-3 py-2 rounded-md border border-slate-300 text-slate-700 text-xs font-semibold">Ingresar</a>
            @endauth
        </div>
    </header>

    <main class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-10 grid lg:grid-cols-3 gap-6">
        <section class="lg:col-span-2 rounded-2xl bg-white border border-slate-200 p-6 shadow-sm">
            <p class="text-xs uppercase tracking-[0.2em] text-slate-500">Curso premium</p>
            <h1 class="mt-2 text-3xl font-black tracking-tight">{{ $course->title }}</h1>
            <p class="mt-3 text-slate-600">{{ $course->description ?: $course->summary }}</p>

            @php
                $firstPlayable = $course->lessons->first(fn ($lesson) => $hasAccess || $lesson->is_preview);
            @endphp

            @if ($firstPlayable)
                <div class="mt-6 rounded-xl overflow-hidden border border-slate-200">
                    @if ($firstPlayable->embed_id)
                        <iframe
                            class="w-full aspect-video"
                            src="https://www.youtube.com/embed/{{ $firstPlayable->embed_id }}"
                            title="Video del curso"
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                            referrerpolicy="strict-origin-when-cross-origin"
                            allowfullscreen
                        ></iframe>
                    @else
                        <div class="aspect-video flex items-center justify-center bg-slate-100 text-sm text-slate-600">
                            No se pudo cargar vista previa de este video.
                        </div>
                    @endif
                </div>
            @endif

            <h2 class="mt-7 text-lg font-bold">Lecciones</h2>
            <div class="mt-3 space-y-2">
                @forelse ($course->lessons as $lesson)
                    <div class="rounded-lg border border-slate-200 p-3 flex items-center justify-between gap-3">
                        <div>
                            <p class="font-semibold text-sm text-slate-800">{{ $lesson->sort_order }}. {{ $lesson->title }}</p>
                            <p class="text-xs text-slate-500">{{ $lesson->is_preview ? 'Vista previa' : 'Solo alumnos inscritos' }}</p>
                        </div>
                        @if ($hasAccess || $lesson->is_preview)
                            <span class="text-xs font-semibold rounded-full px-2 py-1 bg-emerald-100 text-emerald-800">Disponible</span>
                        @else
                            <span class="text-xs font-semibold rounded-full px-2 py-1 bg-amber-100 text-amber-800">Bloqueada</span>
                        @endif
                    </div>
                @empty
                    <p class="text-sm text-slate-500">Aun no hay lecciones publicadas en este curso.</p>
                @endforelse
            </div>
        </section>

        <aside class="rounded-2xl bg-white border border-slate-200 p-6 shadow-sm h-fit">
            <p class="text-xs uppercase tracking-wider text-slate-500">Acceso</p>
            <p class="mt-1 text-2xl font-black text-slate-900">
                {{ (float) $course->price > 0 ? '$'.number_format((float) $course->price, 2).' '.$course->currency : 'Gratis' }}
            </p>

            @auth
                @if ($hasAccess)
                    <div class="mt-4 rounded-lg bg-emerald-50 border border-emerald-200 p-3 text-emerald-800 text-sm font-semibold">
                        Ya tienes acceso activo a este curso.
                    </div>
                    <a href="{{ route('courses.my') }}" class="mt-4 inline-flex items-center px-4 py-2 rounded-md bg-slate-900 text-white text-sm font-semibold hover:bg-slate-700">
                        Ir a mis cursos
                    </a>
                @elseif ((float) $course->price > 0)
                    <form method="POST" action="{{ route('courses.checkout.paypal.create', $course) }}" class="mt-4">
                        @csrf
                        <button type="submit" class="w-full inline-flex items-center justify-center px-4 py-3 rounded-md bg-sky-600 text-white text-sm font-semibold hover:bg-sky-500">
                            Comprar con PayPal
                        </button>
                    </form>
                    <p class="mt-3 text-xs text-slate-500">Al pagar, el acceso se activa automaticamente cuando PayPal confirma la captura.</p>
                @else
                    <div class="mt-4 rounded-lg bg-slate-100 border border-slate-200 p-3 text-slate-700 text-sm font-semibold">
                        Curso gratuito. Se habilitara sin pago.
                    </div>
                @endif
            @else
                <a href="{{ route('login') }}" class="mt-4 inline-flex items-center justify-center w-full px-4 py-3 rounded-md bg-slate-900 text-white text-sm font-semibold hover:bg-slate-700">
                    Inicia sesion para comprar
                </a>
                <a href="{{ route('register') }}" class="mt-3 inline-flex items-center justify-center w-full px-4 py-3 rounded-md border border-slate-300 text-slate-700 text-sm font-semibold hover:bg-slate-100">
                    Crear cuenta
                </a>
            @endauth
        </aside>
    </main>
</body>
</html>
