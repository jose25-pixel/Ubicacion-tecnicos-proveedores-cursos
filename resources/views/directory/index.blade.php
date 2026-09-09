<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Directorio | {{ config('app.name', 'RepuestosApp') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50 text-slate-900 antialiased">
    <header class="bg-white border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <a href="{{ url('/') }}" class="font-bold tracking-tight text-lg">RepuestosApp</a>
            <div class="flex items-center gap-3">
                @auth
                    <a href="{{ route('dashboard') }}" class="px-4 py-2 text-sm font-semibold rounded-md bg-slate-900 text-white hover:bg-slate-700">Panel</a>
                @else
                    <a href="{{ route('login') }}" class="px-4 py-2 text-sm font-semibold rounded-md border border-slate-300 text-slate-700 hover:bg-slate-100">Ingresar</a>
                    <a href="{{ route('register') }}" class="px-4 py-2 text-sm font-semibold rounded-md bg-sky-600 text-white hover:bg-sky-500">Registrar perfil</a>
                @endauth
            </div>
        </div>
    </header>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <div class="flex items-center justify-between gap-4 mb-6">
            <div>
                <h1 class="text-3xl font-black tracking-tight">Directorio de Tecnicos y Proveedores</h1>
                <p class="text-slate-600 mt-1">Busca por zona, especialidad o usa tu ubicacion para ver opciones cercanas.</p>
            </div>
            <button type="button" onclick="setNearby()" class="px-4 py-2 rounded-md bg-slate-900 text-white text-sm font-semibold hover:bg-slate-700">Activar ubicacion</button>
        </div>

        <form method="GET" action="{{ route('directory.index') }}" class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
            <div class="grid lg:grid-cols-6 md:grid-cols-2 gap-3">
                <div class="lg:col-span-2">
                    <label for="q" class="block text-xs font-semibold uppercase tracking-wider text-slate-500">Buscar</label>
                    <input id="q" name="q" type="text" value="{{ $filters['q'] }}" placeholder="Nombre, especialidad, pais o estado" class="mt-1 w-full rounded-md border-slate-300 focus:border-sky-500 focus:ring-sky-500">
                </div>

                <div>
                    <label for="role" class="block text-xs font-semibold uppercase tracking-wider text-slate-500">Tipo</label>
                    <select id="role" name="role" class="mt-1 w-full rounded-md border-slate-300 focus:border-sky-500 focus:ring-sky-500">
                        <option value="">Todos</option>
                        <option value="technician" @selected($filters['role'] === 'technician')>Tecnicos</option>
                        <option value="provider" @selected($filters['role'] === 'provider')>Proveedores</option>
                    </select>
                </div>

                <div>
                    <label for="radius" class="block text-xs font-semibold uppercase tracking-wider text-slate-500">Radio km</label>
                    <input id="radius" name="radius" type="number" min="1" max="500" value="{{ (int) $filters['radius'] }}" class="mt-1 w-full rounded-md border-slate-300 focus:border-sky-500 focus:ring-sky-500">
                </div>

                <div class="flex items-end">
                    <label class="inline-flex items-center gap-2 text-sm text-slate-700">
                        <input type="checkbox" name="verified_only" value="1" {{ $filters['verified_only'] ? 'checked' : '' }} class="rounded border-slate-300 text-sky-600 focus:ring-sky-500">
                        Solo tecnicos verificados
                    </label>
                </div>

                <div>
                    <label for="lat" class="block text-xs font-semibold uppercase tracking-wider text-slate-500">Latitud</label>
                    <input id="lat" name="lat" type="text" value="{{ $filters['lat'] }}" placeholder="13.7000000" class="mt-1 w-full rounded-md border-slate-300 focus:border-sky-500 focus:ring-sky-500">
                </div>

                <div>
                    <label for="lng" class="block text-xs font-semibold uppercase tracking-wider text-slate-500">Longitud</label>
                    <input id="lng" name="lng" type="text" value="{{ $filters['lng'] }}" placeholder="-89.2000000" class="mt-1 w-full rounded-md border-slate-300 focus:border-sky-500 focus:ring-sky-500">
                </div>
            </div>

            <div class="mt-4 flex items-center gap-3">
                <button type="submit" class="px-4 py-2 rounded-md bg-sky-600 text-white text-sm font-semibold hover:bg-sky-500">Aplicar filtros</button>
                <a href="{{ route('directory.index') }}" class="px-4 py-2 rounded-md border border-slate-300 text-sm font-semibold text-slate-700 hover:bg-slate-100">Limpiar</a>
                <p id="geo-status" class="text-xs text-slate-500"></p>
            </div>

            <div id="directory-map" class="mt-4 h-64 rounded-lg border border-slate-200"></div>
            <p class="text-xs text-slate-500 mt-2">Tip: puedes hacer clic en el mapa para fijar latitud y longitud.</p>
        </form>

        <section class="mt-6 grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @forelse ($members as $member)
                <article class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                    @if ($member->role === 'provider' && $member->providerPhotos->isNotEmpty())
                        <img src="{{ asset('storage/'.$member->providerPhotos->first()->path) }}" alt="Producto del proveedor" class="mb-3 h-36 w-full object-cover rounded-lg border border-slate-200" />
                    @endif

                    <div class="flex items-center justify-between">
                        <h2 class="font-bold text-lg">{{ $member->name }}</h2>
                        <span class="text-xs font-semibold uppercase tracking-wider px-2 py-1 rounded bg-slate-100 text-slate-600">
                            {{ $member->role === 'technician' ? 'Tecnico' : 'Proveedor' }}
                        </span>
                    </div>
                    <p class="mt-2 text-sm text-slate-700">
                        {{ $member->role === 'provider'
                            ? ($member->spare_parts_type ? __('Tipo: ').$member->spare_parts_type : 'Sin tipo de repuesto')
                            : ($member->specialty ?: 'Sin especialidad registrada') }}
                    </p>
                    <div class="mt-3 space-y-1 text-sm text-slate-600">
                        <p><strong>Zona:</strong> {{ $member->state ?: 'N/D' }}, {{ $member->country ?: 'N/D' }}</p>
                        <p><strong>Experiencia:</strong> {{ $member->years_experience !== null ? $member->years_experience . ' anios' : 'N/D' }}</p>
                        <p><strong>Telefono:</strong> {{ $member->phone ?: 'N/D' }}</p>
                        <p><strong>WhatsApp:</strong> {{ $member->whatsapp ?: 'N/D' }}</p>
                        <p><strong>Estudios:</strong> {{ $member->studies ?: 'N/D' }}</p>
                        <p><strong>Diplomas:</strong> {{ $member->diplomas ?: 'N/D' }}</p>
                    </div>

                    @if ($member->role === 'technician')
                        <p class="mt-2 inline-flex items-center rounded-full text-xs font-semibold px-3 py-1 {{ $member->technician_verified_at ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">
                            {{ $member->technician_verified_at ? 'Tecnico verificado' : 'Tecnico sin verificar' }}
                        </p>
                    @endif

                    @if ($member->diploma_file_path)
                        <p class="mt-2">
                            <a href="{{ asset('storage/'.$member->diploma_file_path) }}" target="_blank" class="text-sm text-indigo-600 hover:text-indigo-500 underline">Ver diploma adjunto</a>
                        </p>
                    @endif

                    <p class="mt-3">
                        @auth
                            <a href="{{ route('directory.show', $member) }}" class="inline-flex items-center px-3 py-2 rounded-md bg-slate-900 text-white text-xs font-semibold hover:bg-slate-700">
                                Ver perfil
                            </a>
                            @if ($member->latitude !== null && $member->longitude !== null)
                                <a
                                    href="{{ ($filters['lat'] !== null && $filters['lng'] !== null)
                                        ? 'https://www.google.com/maps/dir/?api=1&origin='.$filters['lat'].','.$filters['lng'].'&destination='.$member->latitude.','.$member->longitude
                                        : 'https://www.google.com/maps?q='.$member->latitude.','.$member->longitude }}"
                                    target="_blank"
                                    class="inline-flex items-center ms-2 px-3 py-2 rounded-md bg-sky-600 text-white text-xs font-semibold hover:bg-sky-500"
                                >
                                    Ver ubicacion
                                </a>
                            @endif
                        @else
                            <a href="{{ route('register') }}" class="inline-flex items-center px-3 py-2 rounded-md bg-slate-900 text-white text-xs font-semibold hover:bg-slate-700">
                                Ver perfil
                            </a>
                            <a href="{{ route('register') }}" class="inline-flex items-center ms-2 px-3 py-2 rounded-md bg-sky-600 text-white text-xs font-semibold hover:bg-sky-500">
                                Ver ubicacion
                            </a>
                            <p class="mt-2 text-xs text-amber-700">Registrate para ver perfil completo y trazar ruta.</p>
                        @endauth
                    </p>

                    @if (isset($member->distance_km))
                        <p class="mt-3 inline-flex items-center rounded-full bg-emerald-100 text-emerald-700 text-xs font-semibold px-3 py-1">
                            A {{ number_format($member->distance_km, 1) }} km de ti
                        </p>
                    @endif
                </article>
            @empty
                <div class="sm:col-span-2 lg:col-span-3 rounded-xl border border-amber-200 bg-amber-50 p-5 text-amber-900">
                    No hay resultados con esos filtros. Prueba otro radio o quita la ubicacion.
                </div>
            @endforelse
        </section>

        <div class="mt-6">
            {{ $members->links() }}
        </div>
    </main>

    <script>
        let map;
        let marker;

        function initMap() {
            if (typeof window.L === 'undefined') {
                return;
            }

            map = window.L.map('directory-map').setView([13.7, -89.2], 7);
            window.L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; OpenStreetMap',
            }).addTo(map);

            const latInput = document.getElementById('lat');
            const lngInput = document.getElementById('lng');

            if (latInput.value && lngInput.value) {
                const lat = parseFloat(latInput.value);
                const lng = parseFloat(lngInput.value);
                marker = L.marker([lat, lng]).addTo(map);
                map.setView([lat, lng], 11);
            }

            map.on('click', (e) => {
                const lat = e.latlng.lat.toFixed(7);
                const lng = e.latlng.lng.toFixed(7);
                latInput.value = lat;
                lngInput.value = lng;

                if (marker) {
                    marker.setLatLng(e.latlng);
                } else {
                    marker = window.L.marker(e.latlng).addTo(map);
                }
            });
        }

        function setNearby() {
            const status = document.getElementById('geo-status');
            if (!navigator.geolocation) {
                status.textContent = 'Tu navegador no soporta geolocalizacion.';
                return;
            }

            status.textContent = 'Buscando ubicacion...';
            navigator.geolocation.getCurrentPosition(
                (position) => {
                    document.getElementById('lat').value = position.coords.latitude.toFixed(7);
                    document.getElementById('lng').value = position.coords.longitude.toFixed(7);
                    if (map) {
                        const point = [position.coords.latitude, position.coords.longitude];
                        if (marker) {
                            marker.setLatLng(point);
                        } else {
                            marker = window.L.marker(point).addTo(map);
                        }
                        map.setView(point, 12);
                    }
                    status.textContent = 'Ubicacion lista. Cargando resultados cercanos...';
                    const form = document.querySelector('form[action="{{ route('directory.index') }}"]');
                    if (form) {
                        form.submit();
                    }
                },
                () => {
                    status.textContent = 'No se pudo obtener la ubicacion. Puedes escribir latitud/longitud manualmente.';
                }
            );
        }

        window.addEventListener('load', initMap);

    </script>
</body>
</html>
