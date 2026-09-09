<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Perfil | {{ $member->name }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50 text-slate-900 antialiased">
    <header class="bg-white border-b border-slate-200">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <a href="{{ route('directory.index') }}" class="text-sm font-semibold text-slate-700 hover:text-slate-900">Volver al directorio</a>
            <a href="{{ url('/') }}" class="font-bold tracking-tight text-lg">RepuestosApp</a>
        </div>
    </header>

    <main class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8 grid lg:grid-cols-2 gap-6">
        <section class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
            <h1 class="text-2xl font-black tracking-tight">{{ $member->name }}</h1>
            <p class="mt-1 text-sm text-slate-700">
                {{ $member->role === 'technician' ? 'Tecnico' : 'Proveedor' }} |
                {{ $member->role === 'provider' ? ($member->spare_parts_type ?: 'Sin tipo de repuesto') : ($member->specialty ?: 'Sin especialidad') }}
            </p>

            @if ($member->role === 'technician')
                <p class="mt-2 inline-flex items-center rounded-full text-xs font-semibold px-3 py-1 {{ $member->technician_verified_at ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">
                    {{ $member->technician_verified_at ? 'Tecnico verificado' : 'Tecnico sin verificar' }}
                </p>
            @endif

            <div class="mt-4 space-y-2 text-sm text-slate-700">
                <p><strong>Pais:</strong> {{ $member->country ?: 'N/D' }}</p>
                <p><strong>Estado:</strong> {{ $member->state ?: 'N/D' }}</p>
                <p><strong>Direccion:</strong> {{ $member->address ?: 'N/D' }}</p>
                <p><strong>Experiencia:</strong> {{ $member->years_experience !== null ? $member->years_experience . ' anios' : 'N/D' }}</p>
                <p><strong>Telefono:</strong> {{ $member->phone ?: 'N/D' }}</p>
                <p><strong>WhatsApp:</strong> {{ $member->whatsapp ?: 'N/D' }}</p>
                <p><strong>Estudios:</strong> {{ $member->studies ?: 'N/D' }}</p>
                <p><strong>Diplomas:</strong> {{ $member->diplomas ?: 'N/D' }}</p>
            </div>

            @if ($member->role === 'provider' && $member->providerPhotos->isNotEmpty())
                <div class="mt-4">
                    <p class="text-sm font-semibold text-slate-800 mb-2">Productos y negocio</p>
                    <div class="grid grid-cols-2 gap-2">
                        @foreach ($member->providerPhotos as $photo)
                            <img src="{{ asset('storage/'.$photo->path) }}" alt="Foto de producto" class="h-28 w-full object-cover rounded-md border border-slate-200" />
                        @endforeach
                    </div>
                </div>
            @endif

            @if ($member->diploma_file_path)
                <a href="{{ asset('storage/'.$member->diploma_file_path) }}" target="_blank" class="inline-flex mt-4 text-sm text-indigo-600 hover:text-indigo-500 underline">
                    Ver diploma adjunto
                </a>
            @endif

            @if ($member->latitude !== null && $member->longitude !== null)
                <a href="https://www.google.com/maps?q={{ $member->latitude }},{{ $member->longitude }}" target="_blank" class="inline-flex mt-4 ms-3 text-sm text-sky-700 hover:text-sky-600 underline">
                    Ver en Google Maps
                </a>
                <a href="https://www.google.com/maps/dir/?api=1&destination={{ $member->latitude }},{{ $member->longitude }}" target="_blank" class="inline-flex mt-4 ms-3 text-sm text-indigo-700 hover:text-indigo-600 underline">
                    Trazar ruta
                </a>
            @endif
        </section>

        <section class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="text-lg font-bold">Ubicacion</h2>
            @if ($member->latitude !== null && $member->longitude !== null)
                <div id="member-map" class="mt-3 h-[420px] rounded-lg border border-slate-200"></div>
            @else
                <p class="mt-3 text-sm text-amber-700">Este perfil aun no tiene ubicacion registrada.</p>
            @endif
        </section>
    </main>

    <script>
        window.addEventListener('load', () => {
            const lat = {{ $member->latitude ?? 'null' }};
            const lng = {{ $member->longitude ?? 'null' }};
            if (lat === null || lng === null || typeof window.L === 'undefined') {
                return;
            }

            const map = window.L.map('member-map').setView([lat, lng], 13);
            window.L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; OpenStreetMap',
            }).addTo(map);
            window.L.marker([lat, lng]).addTo(map).bindPopup('Ubicacion de {{ addslashes($member->name) }}').openPopup();
        });
    </script>
</body>
</html>
