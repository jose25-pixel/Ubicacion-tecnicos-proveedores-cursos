<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'RepuestosApp') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-100 text-slate-900 antialiased">
    <header x-data="{ menu: false }" class="bg-white/90 backdrop-blur border-b border-slate-200 sticky top-0 z-40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="h-16 flex items-center justify-between">
                <a href="{{ url('/') }}" class="font-bold tracking-tight text-lg">RepuestosApp</a>

                <nav class="hidden md:flex items-center gap-8">
                    <a href="{{ route('directory.index') }}" class="text-sm font-medium text-slate-700 hover:text-slate-900">Directorio</a>
                    <a href="{{ route('courses.index') }}" class="text-sm font-medium text-slate-700 hover:text-slate-900">Cursos</a>
                    <div class="relative" x-data="{ open: false }" @mouseenter="open = true" @mouseleave="open = false">
                        <button type="button" class="inline-flex items-center text-sm font-medium text-slate-700 hover:text-slate-900">
                            Lavadoras
                            <svg class="w-4 h-4 ms-1" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.94a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                            </svg>
                        </button>
                        <div x-show="open" x-transition class="absolute left-0 mt-2 w-72 rounded-lg bg-white border border-slate-200 shadow-xl p-4" style="display:none;">
                            <p class="text-xs uppercase text-slate-500 tracking-wider mb-3">Marcas y modelos</p>
                            <ul class="space-y-2 text-sm text-slate-700">
                                <li><span class="font-semibold">Whirlpool:</span> Xpert System, 8MWTWCO31W</li>
                                <li><span class="font-semibold">Mabe:</span> LMA74215WBAB0, Aqua Saver Green</li>
                                <li><span class="font-semibold">Samsung:</span> WA19CG6745BV, WA13T5260BY</li>
                                <li><span class="font-semibold">LG:</span> WT13DSBP, WT21WT6HKA</li>
                            </ul>
                        </div>
                    </div>
                </nav>

                <div class="hidden md:flex items-center gap-3">
                    @auth
                        <a href="{{ route('courses.my') }}" class="px-4 py-2 text-sm font-semibold rounded-md border border-slate-300 text-slate-700 hover:bg-slate-100">Mis cursos</a>
                        <a href="{{ route('dashboard') }}" class="px-4 py-2 text-sm font-semibold rounded-md bg-slate-900 text-white hover:bg-slate-700">Panel</a>
                    @else
                        <a href="{{ route('login') }}" class="px-4 py-2 text-sm font-semibold rounded-md border border-slate-300 text-slate-700 hover:bg-slate-100">Ingresar</a>
                        <a href="{{ route('register') }}" class="px-4 py-2 text-sm font-semibold rounded-md bg-sky-600 text-white hover:bg-sky-500">Crear cuenta</a>
                    @endauth
                </div>

                <button @click="menu = !menu" class="md:hidden inline-flex items-center justify-center p-2 rounded-md border border-slate-300">
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
            </div>

            <div x-show="menu" x-transition class="md:hidden pb-4 space-y-2" style="display:none;">
                <a href="{{ route('directory.index') }}" class="block text-sm font-medium text-slate-700">Directorio</a>
                <a href="{{ route('courses.index') }}" class="block text-sm font-medium text-slate-700">Cursos</a>
                <div class="text-xs uppercase text-slate-500 pt-2">Lavadoras</div>
                <p class="text-sm text-slate-600">Whirlpool: Xpert System, 8MWTWCO31W</p>
                <p class="text-sm text-slate-600">Mabe: LMA74215WBAB0, Aqua Saver Green</p>
                <p class="text-sm text-slate-600">Samsung: WA19CG6745BV, WA13T5260BY</p>
                <p class="text-sm text-slate-600">LG: WT13DSBP, WT21WT6HKA</p>
            </div>
        </div>
    </header>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <section class="rounded-3xl bg-gradient-to-br from-slate-900 via-slate-800 to-slate-700 text-white p-8 sm:p-10 shadow-xl">
            <div class="grid lg:grid-cols-2 gap-8 items-center">
                <div>
                    <span class="inline-flex items-center rounded-full bg-white/15 text-slate-100 text-xs font-semibold px-3 py-1 border border-white/20">Marketplace + Formacion Tecnica</span>
                    <h1 class="mt-5 text-4xl sm:text-5xl font-black tracking-tight">RepuestosApp Zeta: Directorio y cursos en un solo lugar</h1>
                    <p class="mt-5 text-slate-200 text-lg">Conecta clientes, tecnicos y proveedores; publica tu perfil, muestra tus repuestos y aprende con cursos de pago integrados con PayPal.</p>
                    <div class="mt-8 flex flex-wrap gap-3">
                        <a href="{{ route('register') }}" class="px-5 py-3 rounded-md bg-sky-500 hover:bg-sky-400 text-white text-sm font-semibold">Publicar mi perfil</a>
                        <a href="{{ route('directory.index') }}" class="px-5 py-3 rounded-md border border-white/40 hover:bg-white/10 text-sm font-semibold">Explorar directorio</a>
                        <a href="{{ route('courses.index') }}" class="px-5 py-3 rounded-md bg-white text-slate-900 hover:bg-slate-200 text-sm font-semibold">Ver cursos</a>
                        <button
                            type="button"
                            class="px-5 py-3 rounded-md bg-slate-900/60 hover:bg-slate-900 text-white text-sm font-semibold border border-white/20"
                            onclick="goToNearby()"
                        >
                            Ver cercanos a mi
                        </button>
                    </div>
                    <p id="nearby-message" class="mt-3 text-xs text-slate-300"></p>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div class="rounded-2xl bg-white/10 border border-white/15 p-4">
                        <p class="text-xs uppercase tracking-wider text-slate-300">Tecnicos</p>
                        <p class="mt-2 text-3xl font-black">{{ number_format($stats['technicians']) }}</p>
                    </div>
                    <div class="rounded-2xl bg-white/10 border border-white/15 p-4">
                        <p class="text-xs uppercase tracking-wider text-slate-300">Proveedores</p>
                        <p class="mt-2 text-3xl font-black">{{ number_format($stats['providers']) }}</p>
                    </div>
                    <div class="rounded-2xl bg-white/10 border border-white/15 p-4">
                        <p class="text-xs uppercase tracking-wider text-slate-300">Tecnicos verificados</p>
                        <p class="mt-2 text-3xl font-black">{{ number_format($stats['verified_technicians']) }}</p>
                    </div>
                    <div class="rounded-2xl bg-white/10 border border-white/15 p-4">
                        <p class="text-xs uppercase tracking-wider text-slate-300">Cursos activos</p>
                        <p class="mt-2 text-3xl font-black">{{ number_format($stats['published_courses']) }}</p>
                    </div>
                </div>
            </div>
        </section>

        <section class="mt-8 grid lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm" x-data="providerCarousel()" x-init="init()">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h2 class="text-xl font-black tracking-tight">Proveedores destacados</h2>
                        <p class="text-sm text-slate-600">Carousel con perfiles reales de repuestos y negocio.</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <button type="button" @click="prev()" class="h-8 w-8 rounded-full border border-slate-300 text-slate-700 hover:bg-slate-100">&larr;</button>
                        <button type="button" @click="next()" class="h-8 w-8 rounded-full border border-slate-300 text-slate-700 hover:bg-slate-100">&rarr;</button>
                    </div>
                </div>

                @if ($featuredProviders->isNotEmpty())
                    <div x-ref="track" class="flex gap-4 overflow-x-auto scroll-smooth snap-x snap-mandatory pb-2">
                        @foreach ($featuredProviders as $provider)
                            <a href="{{ route('directory.show', $provider) }}" class="snap-start shrink-0 w-[270px] rounded-xl border border-slate-200 bg-slate-50 overflow-hidden">
                                @if ($provider->providerPhotos->isNotEmpty())
                                    <img src="{{ asset('storage/'.$provider->providerPhotos->first()->path) }}" alt="Foto de {{ $provider->name }}" class="h-36 w-full object-cover" />
                                @else
                                    <div class="h-36 w-full bg-slate-200"></div>
                                @endif
                                <div class="p-4">
                                    <p class="font-bold text-slate-900">{{ $provider->name }}</p>
                                    <p class="text-xs text-slate-500 mt-1">{{ $provider->state ?: 'Zona no definida' }}, {{ $provider->country ?: 'Pais no definido' }}</p>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @else
                    <div class="rounded-lg border border-amber-200 bg-amber-50 p-4 text-amber-900 text-sm">
                        Aun no hay proveedores con fotos publicadas.
                    </div>
                @endif
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <h2 class="font-bold text-xl">Datos recomendados para tecnicos y proveedores</h2>
                <ul class="mt-4 space-y-2 text-sm text-slate-700">
                    <li>Nombre completo</li>
                    <li>Especialidad y marcas que atiendes</li>
                    <li>Pais, estado/departamento y zona</li>
                    <li>Anios de experiencia</li>
                    <li>Estudios, diplomas o certificaciones</li>
                    <li>Ubicacion geografica para resultados cercanos</li>
                </ul>
            </div>
        </section>

        <section class="mt-8">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-2xl font-black tracking-tight">Cursos recomendados</h2>
                <a href="{{ route('courses.index') }}" class="text-sm font-semibold text-sky-700 hover:text-sky-600">Ver todos</a>
            </div>

            <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4">
                @forelse ($featuredCourses as $course)
                    <article class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                        <p class="text-xs uppercase tracking-wider text-slate-500">Curso</p>
                        <h3 class="mt-1 font-black tracking-tight text-slate-900">{{ $course->title }}</h3>
                        <p class="mt-2 text-sm text-slate-600 min-h-[42px]">{{ $course->summary ?: 'Formacion tecnica para mejorar tu nivel profesional.' }}</p>
                        <div class="mt-3 flex items-center justify-between text-xs">
                            <span class="text-emerald-700 font-semibold">${{ number_format((float) $course->price, 2) }} {{ $course->currency }}</span>
                            <a href="{{ route('courses.show', $course) }}" class="text-sky-700 font-semibold hover:text-sky-600">Ver curso</a>
                        </div>
                    </article>
                @empty
                    <div class="sm:col-span-2 lg:col-span-4 rounded-xl border border-amber-200 bg-amber-50 p-5 text-amber-900">
                        Aun no hay cursos publicados para mostrar.
                    </div>
                @endforelse
            </div>
        </section>
    </main>

    <script>
        function providerCarousel() {
            return {
                timer: null,
                init() {
                    this.timer = setInterval(() => this.next(), 4500);
                },
                next() {
                    if (!this.$refs.track) {
                        return;
                    }
                    this.$refs.track.scrollBy({ left: 290, behavior: 'smooth' });
                },
                prev() {
                    if (!this.$refs.track) {
                        return;
                    }
                    this.$refs.track.scrollBy({ left: -290, behavior: 'smooth' });
                }
            };
        }

        function goToNearby() {
            const msg = document.getElementById('nearby-message');

            if (!navigator.geolocation) {
                msg.textContent = 'Tu navegador no soporta geolocalizacion. Entra al directorio y agrega tu ubicacion manual.';
                return;
            }

            msg.textContent = 'Detectando tu ubicacion...';
            navigator.geolocation.getCurrentPosition(
                (position) => {
                    const lat = position.coords.latitude.toFixed(7);
                    const lng = position.coords.longitude.toFixed(7);
                    window.location.href = `{{ route('directory.index') }}?lat=${lat}&lng=${lng}&radius=50`;
                },
                () => {
                    msg.textContent = 'No se pudo obtener tu ubicacion. Puedes buscar por pais o estado en el directorio.';
                }
            );
        }
    </script>
</body>
</html>
