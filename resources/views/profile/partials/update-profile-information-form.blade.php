<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Profile Information') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __("Update your account's profile information and email address.") }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form id="profile-form" method="post" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="mt-6 space-y-6" x-data="{ role: '{{ old('role', $user->role ?? 'user') }}', geoMessage: '' }">
        @csrf
        @method('patch')

        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div>
                    <p class="text-sm mt-2 text-gray-800">
                        {{ __('Your email address is unverified.') }}

                        <button form="send-verification" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            {{ __('Click here to re-send the verification email.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-sm text-green-600">
                            {{ __('A new verification link has been sent to your email address.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div>
            <x-input-label for="role" :value="__('Tipo de cuenta')" />
            <select id="role" name="role" x-model="role" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                <option value="user">Usuario</option>
                <option value="technician">Tecnico</option>
                <option value="provider">Proveedor</option>
                @if (($user->role ?? null) === 'admin')
                    <option value="admin">Admin</option>
                @endif
            </select>
            <x-input-error :messages="$errors->get('role')" class="mt-2" />
        </div>

        <div x-show="role === 'technician'" x-cloak class="space-y-4">
            <div>
                <x-input-label for="specialty" :value="__('Especialidad')" />
                <x-text-input id="specialty" class="block mt-1 w-full" type="text" name="specialty" :value="old('specialty', $user->specialty)" />
                <x-input-error :messages="$errors->get('specialty')" class="mt-2" />
            </div>
        </div>

        <div x-show="role === 'provider'" x-cloak class="space-y-4">
            <div>
                <x-input-label for="spare_parts_type" :value="__('Tipo de repuestos que vende')" />
                <select id="spare_parts_type" name="spare_parts_type" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                    <option value="">Selecciona una opcion</option>
                    <option value="washing" @selected(old('spare_parts_type', $user->spare_parts_type) === 'washing')>Lavadoras</option>
                    <option value="refrigerators" @selected(old('spare_parts_type', $user->spare_parts_type) === 'refrigerators')>Refrigeradoras</option>
                    <option value="refrigeration_systems" @selected(old('spare_parts_type', $user->spare_parts_type) === 'refrigeration_systems')>Sistemas de refrigeracion</option>
                    <option value="mixed" @selected(old('spare_parts_type', $user->spare_parts_type) === 'mixed')>Mixto</option>
                </select>
                <x-input-error :messages="$errors->get('spare_parts_type')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="provider_photos" :value="__('Fotos del negocio o productos (maximo 4)')" />
                <input id="provider_photos" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" type="file" name="provider_photos[]" accept=".jpg,.jpeg,.png,.webp" multiple />
                <x-input-error :messages="$errors->get('provider_photos')" class="mt-2" />
                <x-input-error :messages="$errors->get('provider_photos.*')" class="mt-2" />
                @if ($user->providerPhotos->isNotEmpty())
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 mt-2">
                        @foreach ($user->providerPhotos as $photo)
                            <img src="{{ asset('storage/'.$photo->path) }}" alt="Foto de producto" class="h-20 w-full object-cover rounded-md border border-gray-200" />
                        @endforeach
                    </div>
                @endif
                <div id="profile-provider-photos-preview" class="grid grid-cols-2 sm:grid-cols-4 gap-2 mt-2"></div>
            </div>
        </div>

        <div x-show="role !== 'user'" x-cloak class="space-y-4">

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <x-input-label for="country" :value="__('Pais')" />
                    <x-text-input id="country" class="block mt-1 w-full" type="text" name="country" :value="old('country', $user->country)" />
                    <x-input-error :messages="$errors->get('country')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="state" :value="__('Estado / Departamento')" />
                    <x-text-input id="state" class="block mt-1 w-full" type="text" name="state" :value="old('state', $user->state)" />
                    <x-input-error :messages="$errors->get('state')" class="mt-2" />
                </div>
            </div>

            <div>
                <x-input-label for="years_experience" :value="__('Anios de experiencia')" />
                <x-text-input id="years_experience" class="block mt-1 w-full" type="number" min="0" max="70" name="years_experience" :value="old('years_experience', $user->years_experience)" />
                <x-input-error :messages="$errors->get('years_experience')" class="mt-2" />
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <x-input-label for="phone" :value="__('Telefono')" />
                    <x-text-input id="phone" class="block mt-1 w-full" type="text" name="phone" :value="old('phone', $user->phone)" placeholder="+503 7000 0000" />
                    <x-input-error :messages="$errors->get('phone')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="whatsapp" :value="__('WhatsApp')" />
                    <x-text-input id="whatsapp" class="block mt-1 w-full" type="text" name="whatsapp" :value="old('whatsapp', $user->whatsapp)" placeholder="+503 7000 0000" />
                    <x-input-error :messages="$errors->get('whatsapp')" class="mt-2" />
                </div>
            </div>

            <div>
                <x-input-label for="studies" :value="__('Estudios')" />
                <x-text-input id="studies" class="block mt-1 w-full" type="text" name="studies" :value="old('studies', $user->studies)" />
                <x-input-error :messages="$errors->get('studies')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="diplomas" :value="__('Diplomas o certificaciones')" />
                <x-text-input id="diplomas" class="block mt-1 w-full" type="text" name="diplomas" :value="old('diplomas', $user->diplomas)" />
                <x-input-error :messages="$errors->get('diplomas')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="diploma_file" :value="__('Subir diploma (PDF o imagen)')" />
                <input id="diploma_file" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" type="file" name="diploma_file" accept=".pdf,.jpg,.jpeg,.png,.webp" />
                <x-input-error :messages="$errors->get('diploma_file')" class="mt-2" />
                @if ($user->diploma_file_path)
                    <a href="{{ asset('storage/'.$user->diploma_file_path) }}" target="_blank" class="inline-flex mt-2 text-sm text-indigo-600 hover:text-indigo-500 underline">
                        Ver diploma subido
                    </a>
                @endif
            </div>

            <div>
                <x-input-label for="address" :value="__('Direccion o zona de trabajo')" />
                <x-text-input id="address" class="block mt-1 w-full" type="text" name="address" :value="old('address', $user->address)" />
                <x-input-error :messages="$errors->get('address')" class="mt-2" />
            </div>

            <div class="rounded-md border border-gray-200 p-3 bg-gray-50">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <span class="text-sm text-gray-700 font-semibold">Ubicacion para aparecer cerca de tus clientes</span>
                        <p class="text-xs text-gray-500 mt-1">Usa el buscador de texto para establecer ubicacion correcta.</p>
                    </div>
                    <button
                        type="button"
                        title="Requiere GPS activo. Si tu equipo no tiene GPS el resultado puede ser incorrecto."
                        class="inline-flex items-center px-3 py-2 text-xs font-semibold rounded-md bg-gray-500 text-white hover:bg-gray-600"
                        @click="
                            if (!navigator.geolocation) {
                                geoMessage = 'Tu navegador no soporta geolocalizacion.';
                                return;
                            }

                            geoMessage = 'Obteniendo GPS del dispositivo...';
                            navigator.geolocation.getCurrentPosition(
                                (position) => {
                                    document.getElementById('latitude').value = position.coords.latitude.toFixed(7);
                                    document.getElementById('longitude').value = position.coords.longitude.toFixed(7);
                                    window.updateProfileMapFromInputs();
                                    geoMessage = 'GPS detectado. Verifica el pin en el mapa y guarda.';
                                },
                                () => {
                                    geoMessage = 'GPS no disponible. Usa el buscador de texto.';
                                },
                                { enableHighAccuracy: true, timeout: 8000 }
                            );
                        "
                    >
                        Usar GPS
                    </button>
                </div>
                <div class="mt-3 flex flex-col sm:flex-row gap-2">
                    <input id="profile-location-search" type="text" class="w-full rounded-md border-indigo-400 focus:border-indigo-600 focus:ring-indigo-500 ring-1 ring-indigo-300" placeholder="Escribe tu ciudad o pais (ej. Ciudad de Guatemala, GT)" />
                    <button
                        type="button"
                        class="inline-flex items-center justify-center px-3 py-2 text-xs font-semibold rounded-md border border-gray-300 text-gray-700 hover:bg-gray-100"
                        onclick="window.searchProfileLocation()"
                    >
                        Buscar en mapa
                    </button>
                </div>
                <p id="profile-geo-message" class="text-xs text-gray-600 mt-2" x-text="geoMessage"></p>
                <div id="profile-map" class="mt-3 h-56 w-full rounded-md border border-gray-200"></div>
                <p class="text-xs text-gray-500 mt-2">Tambien puedes hacer clic en el mapa para ajustar tu ubicacion exacta.</p>
            </div>

            @if (($user->role ?? 'user') === 'technician')
                <div class="rounded-md border border-indigo-200 p-3 bg-indigo-50">
                    <p class="text-sm text-indigo-900 font-semibold">Validacion de tecnico</p>
                    <p class="text-xs text-indigo-800 mt-1">
                        Estado:
                        @if ($user->isTechnicianVerified())
                            Tecnico verificado
                        @else
                            Pendiente
                        @endif
                        | Puntaje: {{ $user->technician_verification_score ?? 'N/D' }}
                    </p>
                    <a href="{{ route('technician.verification.create') }}" class="inline-flex mt-2 px-3 py-2 text-xs font-semibold rounded-md bg-indigo-600 text-white hover:bg-indigo-500">
                        Presentar examen tecnico
                    </a>
                </div>
            @endif
        </div>

        <input id="latitude" type="hidden" name="latitude" value="{{ old('latitude', $user->latitude) }}">
        <input id="longitude" type="hidden" name="longitude" value="{{ old('longitude', $user->longitude) }}">

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Save') }}</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600"
                >{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>
</section>

@push('scripts')
    <script>
        /* ─── CDN fallback: carga Leaflet desde CDN si el bundle Vite no exportó window.L ─── */
        (function () {
            if (typeof window.L !== 'undefined') return;
            if (document.getElementById('leaflet-cdn-script')) return;
            const link = document.createElement('link');
            link.rel = 'stylesheet'; link.id = 'leaflet-cdn-style';
            link.href = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css';
            document.head.appendChild(link);
            const s = document.createElement('script');
            s.id = 'leaflet-cdn-script';
            s.src = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js';
            document.head.appendChild(s);
        })();

        /* ─── Estado ─── */
        let profileMap = null;
        let profileMarker = null;

        /* ─── Mensajes ─── */
        function setProfileGeoMessage(msg) {
            const el = document.getElementById('profile-geo-message');
            if (el) el.textContent = msg;
            const form = document.getElementById('profile-form');
            if (form?.__x?.$data) form.__x.$data.geoMessage = msg;
        }

        /* ─── Actualiza inputs y pin ─── */
        function setProfileLocation(lat, lng, zoom = 12) {
            document.getElementById('latitude').value  = Number(lat).toFixed(7);
            document.getElementById('longitude').value = Number(lng).toFixed(7);
            if (!profileMap) return;
            const pt = [Number(lat), Number(lng)];
            if (profileMarker) profileMarker.setLatLng(pt);
            else profileMarker = window.L.marker(pt).addTo(profileMap);
            profileMap.setView(pt, zoom);
        }

        /* ─── Muestra coordenadas ya guardadas en el mapa ─── */
        window.updateProfileMapFromInputs = function () {
            if (!profileMap) return;
            const lat = parseFloat(document.getElementById('latitude').value);
            const lng = parseFloat(document.getElementById('longitude').value);
            if (isNaN(lat) || isNaN(lng)) return;
            const pt = [lat, lng];
            if (profileMarker) profileMarker.setLatLng(pt);
            else profileMarker = window.L.marker(pt).addTo(profileMap);
            profileMap.setView(pt, 12);
        };

        /* ─── Crea (o recrea) el mapa ─── */
        function createProfileMap() {
            const container = document.getElementById('profile-map');
            if (!container) return;
            if (typeof window.L === 'undefined') return;
            /* Solo cuando el contenedor tiene dimensiones reales */
            if (container.offsetWidth === 0 || container.offsetHeight === 0) return;

            if (profileMap) {
                profileMap.remove();
                profileMap = null;
                profileMarker = null;
            }

            profileMap = window.L.map('profile-map').setView([13.7, -89.2], 7);
            window.L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19, attribution: '&copy; OpenStreetMap contributors',
            }).addTo(profileMap);
            profileMap.on('click', e => setProfileLocation(e.latlng.lat.toFixed(7), e.latlng.lng.toFixed(7)));

            /* Mostrar coordenadas guardadas en la BD */
            const lat = parseFloat(document.getElementById('latitude')?.value);
            const lng = parseFloat(document.getElementById('longitude')?.value);
            if (!isNaN(lat) && !isNaN(lng)) {
                profileMarker = window.L.marker([lat, lng]).addTo(profileMap);
                profileMap.setView([lat, lng], 12);
            }

            setTimeout(() => profileMap && profileMap.invalidateSize(), 150);
        }

        /* ─── Poll hasta que el contenedor sea visible Y window.L exista ─── */
        function waitForProfileMap(maxMs = 5000) {
            const start = Date.now();
            (function poll() {
                const container = document.getElementById('profile-map');
                if (container && container.offsetWidth > 0 && container.offsetHeight > 0 && typeof window.L !== 'undefined') {
                    createProfileMap();
                    return;
                }
                if (Date.now() - start < maxMs) setTimeout(poll, 120);
                else setProfileGeoMessage('No se pudo cargar el mapa. Recarga la pagina con Ctrl+F5.');
            })();
        }

        /* ─── Geocoding Nominatim ─── */
        async function geocodeProfileLocation(query) {
            const r = await fetch(`https://nominatim.openstreetmap.org/search?format=json&addressdetails=1&limit=1&q=${encodeURIComponent(query)}`);
            if (!r.ok) throw new Error('Error en el servicio de mapas.');
            const data = await r.json();
            return Array.isArray(data) && data.length ? data[0] : null;
        }

        /* ─── Buscar ubicacion por texto ─── */
        window.searchProfileLocation = async function () {
            const query = document.getElementById('profile-location-search')?.value?.trim();
            if (!query) return;

            setProfileGeoMessage('Buscando ubicacion...');

            /* Esperar a que el mapa esté listo */
            await new Promise(resolve => {
                const start = Date.now();
                (function waitMap() {
                    const container = document.getElementById('profile-map');
                    const ready = container && container.offsetWidth > 0 && container.offsetHeight > 0 && typeof window.L !== 'undefined';
                    if (ready) { createProfileMap(); resolve(); return; }
                    if (Date.now() - start < 4000) setTimeout(waitMap, 120);
                    else resolve();
                })();
            });

            try {
                const result = await geocodeProfileLocation(query);
                if (!result) {
                    setProfileGeoMessage('No encontramos esa ubicacion. Prueba con mas detalle.');
                    return;
                }

                setProfileLocation(result.lat, result.lon, 13);

                const address = result.address || {};
                const countryInput = document.getElementById('country');
                const stateInput   = document.getElementById('state');
                const addressInput = document.getElementById('address');
                if (countryInput) countryInput.value = address.country || countryInput.value;
                if (stateInput)   stateInput.value   = address.state || address.region || address.county || stateInput.value;
                if (addressInput) addressInput.value = result.display_name || addressInput.value;

                setProfileGeoMessage('Ubicacion encontrada. Verifica el pin y guarda el perfil.');
            } catch {
                setProfileGeoMessage('No se pudo buscar la ubicacion en este momento.');
            }
        };

        /* ─── Preview de fotos ─── */
        function renderProfileImagePreviews(inputId, previewId) {
            const input     = document.getElementById(inputId);
            const container = document.getElementById(previewId);
            if (!input || !container) return;
            container.innerHTML = '';
            Array.from(input.files || []).slice(0, 4).forEach(file => {
                if (!file.type.startsWith('image/')) return;
                const url = URL.createObjectURL(file);
                const img = document.createElement('img');
                img.src = url; img.alt = 'Vista previa';
                img.className = 'h-20 w-full object-cover rounded-md border border-gray-200';
                img.onload = () => URL.revokeObjectURL(url);
                container.appendChild(img);
            });
        }

        /* ─── Boot ─── */
        function bootProfileMap() {
            const photosInput = document.getElementById('provider_photos');
            if (photosInput) photosInput.addEventListener('change', () => renderProfileImagePreviews('provider_photos', 'profile-provider-photos-preview'));

            const roleSelect = document.getElementById('role');
            if (roleSelect) {
                roleSelect.addEventListener('change', () => {
                    if (profileMap) { profileMap.remove(); profileMap = null; profileMarker = null; }
                    waitForProfileMap();
                });
            }

            /* El perfil carga con rol ya definido en BD — iniciar mapa de inmediato */
            waitForProfileMap();
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', bootProfileMap, { once: true });
        } else {
            bootProfileMap();
        }
    </script>
@endpush
