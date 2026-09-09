<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Cursos | {{ config('app.name', 'RepuestosApp') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-100 text-slate-900 antialiased">
    <header class="bg-white border-b border-slate-200 sticky top-0 z-30">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <a href="{{ url('/') }}" class="font-black text-lg tracking-tight">Zeta Cursos</a>
            <nav class="flex items-center gap-3">
                <a href="{{ route('directory.index') }}" class="text-sm font-semibold text-slate-700 hover:text-slate-900">Directorio</a>
                @auth
                    <a href="{{ route('courses.my') }}" class="px-3 py-2 rounded-md bg-slate-900 text-white text-xs font-semibold">Mis cursos</a>
                @endauth
            </nav>
        </div>
    </header>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <div class="rounded-2xl bg-gradient-to-r from-slate-900 via-slate-800 to-slate-700 text-white p-8">
            <p class="text-xs uppercase tracking-[0.25em] text-slate-300">Formacion tecnica</p>
            <h1 class="mt-3 text-3xl sm:text-4xl font-black tracking-tight">Cursos pagados para tecnicos y proveedores</h1>
            <p class="mt-3 text-slate-200 max-w-2xl">Aprende reparacion, diagnostico y ventas de repuestos con clases en video y acceso desde tu panel.</p>
        </div>

        <section class="mt-8 grid sm:grid-cols-2 lg:grid-cols-3 gap-5">
            @forelse ($courses as $course)
                <article class="rounded-2xl bg-white border border-slate-200 p-5 shadow-sm hover:shadow-md transition">
                    <div class="flex items-center justify-between gap-3">
                        <p class="text-xs uppercase tracking-wider text-slate-500">Curso</p>
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold {{ (float) $course->price > 0 ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-200 text-slate-700' }}">
                            {{ (float) $course->price > 0 ? '$'.number_format((float) $course->price, 2).' '.$course->currency : 'Gratis' }}
                        </span>
                    </div>
                    <h2 class="mt-2 text-xl font-black tracking-tight text-slate-900">{{ $course->title }}</h2>
                    <p class="mt-2 text-sm text-slate-600 min-h-[56px]">{{ $course->summary ?: 'Curso tecnico para fortalecer tus conocimientos y mejorar tu servicio.' }}</p>
                    <div class="mt-4 flex items-center justify-between text-xs text-slate-500">
                        <span>{{ $course->lessons_count }} lecciones</span>
                        <span>Publicado {{ optional($course->published_at)->diffForHumans() ?: 'recientemente' }}</span>
                    </div>
                    <a href="{{ route('courses.show', $course) }}" class="mt-4 inline-flex items-center px-3 py-2 rounded-md bg-sky-600 text-white text-sm font-semibold hover:bg-sky-500">
                        Ver curso
                    </a>
                </article>
            @empty
                <div class="sm:col-span-2 lg:col-span-3 rounded-xl border border-amber-200 bg-amber-50 p-6 text-amber-900">
                    Aun no hay cursos publicados.
                </div>
            @endforelse
        </section>

        <div class="mt-8">
            {{ $courses->links() }}
        </div>
    </main>
</body>
</html>
