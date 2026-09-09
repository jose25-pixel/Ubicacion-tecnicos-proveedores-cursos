<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Mis cursos</h2>
            <a href="{{ route('courses.index') }}" class="inline-flex items-center px-3 py-2 rounded-md bg-slate-900 text-white text-xs font-semibold hover:bg-slate-700">Explorar cursos</a>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid md:grid-cols-2 xl:grid-cols-3 gap-5">
                @forelse ($enrollments as $enrollment)
                    <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                        <p class="text-xs uppercase tracking-wider text-slate-500">Inscrito</p>
                        <h3 class="mt-2 text-lg font-black tracking-tight text-slate-900">{{ $enrollment->course->title }}</h3>
                        <p class="mt-2 text-sm text-slate-600">{{ $enrollment->course->summary ?: 'Continua avanzando en tu formacion tecnica.' }}</p>
                        <div class="mt-4 text-xs text-slate-500 space-y-1">
                            <p>Inicio: {{ optional($enrollment->started_at)->format('d/m/Y H:i') ?: 'N/D' }}</p>
                            <p>Estado: <span class="font-semibold text-emerald-700">{{ ucfirst($enrollment->status) }}</span></p>
                        </div>

                        @if ($enrollment->lastWatchedLesson)
                            <p class="mt-3 text-xs text-slate-600">Ultima leccion: {{ $enrollment->lastWatchedLesson->title }}</p>
                        @endif

                        <a href="{{ route('courses.show', $enrollment->course) }}" class="mt-4 inline-flex items-center px-3 py-2 rounded-md bg-sky-600 text-white text-sm font-semibold hover:bg-sky-500">
                            Continuar curso
                        </a>
                    </article>
                @empty
                    <div class="md:col-span-2 xl:col-span-3 rounded-xl border border-amber-200 bg-amber-50 p-6 text-amber-900">
                        Aun no tienes cursos activos. Explora el catalogo y compra tu primer curso.
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>
