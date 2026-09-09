<x-guest-layout>
    <form id="register-form" method="POST" action="{{ route('register') }}" enctype="multipart/form-data" x-data="{ role: '{{ old('role', 'user') }}', geoMessage: '' }">
        @csrf

        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="role" :value="__('Tipo de cuenta')" />
            <select id="role" name="role" x-model="role" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                <option value="user">Usuario</option>
                <option value="technician">Tecnico</option>
                <option value="provider">Proveedor</option>
            </select>
            <x-input-error :messages="$errors->get('role')" class="mt-2" />
        </div>

        <div x-show="role === 'technician'" x-cloak class="mt-4 space-y-4">
            <div>
                <x-input-label for="specialty" :value="__('Especialidad')" />
                <x-text-input id="specialty" class="block mt-1 w-full" type="text" name="specialty" :value="old('specialty')" />
                <x-input-error :messages="$errors->get('specialty')" class="mt-2" />
            </div>
        </div>

        <div x-show="role === 'provider'" x-cloak class="mt-4 space-y-4">
            <div>
                <x-input-label for="spare_parts_type" :value="__('Tipo de repuestos que vende')" />
                <select id="spare_parts_type" name="spare_parts_type" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                    <option value="">Selecciona una opcion</option>
                    <option value="washing" @selected(old('spare_parts_type') === 'washing')>Lavadoras</option>
                    <option value="refrigerators" @selected(old('spare_parts_type') === 'refrigerators')>Refrigeradoras</option>
                    <option value="refrigeration_systems" @selected(old('spare_parts_type') === 'refrigeration_systems')>Sistemas de refrigeracion</option>
                    <option value="mixed" @selected(old('spare_parts_type') === 'mixed')>Mixto</option>
                </select>
                <x-input-error :messages="$errors->get('spare_parts_type')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="provider_photos" :value="__('Fotos del negocio o productos (maximo 4)')" />
                <input id="provider_photos" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" type="file" name="provider_photos[]" accept=".jpg,.jpeg,.png,.webp" multiple />
                <div id="register-provider-photos-preview" class="grid grid-cols-2 sm:grid-cols-4 gap-2 mt-2"></div>
                <x-input-error :messages="$errors->get('provider_photos')" class="mt-2" />
                <x-input-error :messages="$errors->get('provider_photos.*')" class="mt-2" />
            </div>
        </div>

        <div x-show="role !== 'user'" x-cloak class="mt-4 space-y-4">

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <x-input-label for="country" :value="__('Pais')" />
                    <x-text-input id="country" class="block mt-1 w-full" type="text" name="country" :value="old('country')" />
                    <x-input-error :messages="$errors->get('country')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="state" :value="__('Estado / Departamento')" />
                    <x-text-input id="state" class="block mt-1 w-full" type="text" name="state" :value="old('state')" />
                    <x-input-error :messages="$errors->get('state')" class="mt-2" />
                </div>
            </div>

            <div>
                <x-input-label for="years_experience" :value="__('Anios de experiencia')" />
                <x-text-input id="years_experience" class="block mt-1 w-full" type="number" min="0" max="70" name="years_experience" :value="old('years_experience')" />
                <x-input-error :messages="$errors->get('years_experience')" class="mt-2" />
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <x-input-label for="phone" :value="__('Telefono')" />
                    <x-text-input id="phone" class="block mt-1 w-full" type="text" name="phone" :value="old('phone')" placeholder="+503 7000 0000" />
                    <x-input-error :messages="$errors->get('phone')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="whatsapp" :value="__('WhatsApp')" />
                    <x-text-input id="whatsapp" class="block mt-1 w-full" type="text" name="whatsapp" :value="old('whatsapp')" placeholder="+503 7000 0000" />
                    <x-input-error :messages="$errors->get('whatsapp')" class="mt-2" />
                </div>
            </div>

            <div>
                <x-input-label for="studies" :value="__('Estudios')" />
                <x-text-input id="studies" class="block mt-1 w-full" type="text" name="studies" :value="old('studies')" />
                <x-input-error :messages="$errors->get('studies')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="diplomas" :value="__('Diplomas o certificaciones')" />
                <x-text-input id="diplomas" class="block mt-1 w-full" type="text" name="diplomas" :value="old('diplomas')" />
                <x-input-error :messages="$errors->get('diplomas')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="diploma_file" :value="__('Subir diploma (PDF o imagen)')" />
                <input id="diploma_file" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" type="file" name="diploma_file" accept=".pdf,.jpg,.jpeg,.png,.webp" />
                <x-input-error :messages="$errors->get('diploma_file')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="address" :value="__('Direccion o zona de trabajo')" />
                <x-text-input id="address" class="block mt-1 w-full" type="text" name="address" :value="old('address')" />
                <x-input-error :messages="$errors->get('address')" class="mt-2" />
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-4">
                <div class="rounded-md border border-gray-200 p-3 bg-gray-50 lg:col-span-8">
                    <div class="flex items-center justify-between gap-3">
                        <div>
                            <span class="text-sm text-gray-700 font-semibold">Ubicacion para mostrarte en el directorio</span>
                            <p class="text-xs text-gray-500 mt-1">Usa el buscador de texto para mayor precision.</p>
                        </div>
                        <button
                            type="button"
                            title="Requiere GPS activo en tu dispositivo. Si no tienes GPS, el resultado puede ser incorrecto."
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
                                        window.updateRegistrationMapFromInputs();
                                        geoMessage = 'GPS detectado. Verifica el pin en el mapa.';
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
                        <input id="register-location-search" type="text" class="w-full rounded-md border-indigo-400 focus:border-indigo-600 focus:ring-indigo-500 ring-1 ring-indigo-300" placeholder="Escribe tu ciudad o pais (ej. Ciudad de Guatemala, GT)" />
                        <button
                            type="button"
                            class="inline-flex items-center justify-center px-3 py-2 text-xs font-semibold rounded-md border border-gray-300 text-gray-700 hover:bg-gray-100"
                            onclick="window.searchRegistrationLocation()"
                        >
                            Buscar en mapa
                        </button>
                    </div>
                    <p id="register-geo-message" class="text-xs text-gray-600 mt-2" x-text="geoMessage"></p>
                    <div id="register-map" class="mt-3 h-56 md:h-72 lg:h-[460px] w-full rounded-md border border-gray-200"></div>
                    <p class="text-xs text-gray-500 mt-2">Tambien puedes hacer clic en el mapa para guardar tu ubicacion exacta.</p>
                </div>

                <aside class="hidden lg:flex lg:col-span-4 rounded-md border border-dashed border-gray-300 bg-white p-4 items-center justify-center text-center">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Espacio publicitario</p>
                        <p class="mt-2 text-sm text-gray-700">Area reservada para banner de anunciantes o promociones.</p>
                        <div class="mt-4 h-64 w-full rounded-md bg-gray-100 border border-gray-200"></div>
                    </div>
                </aside>
            </div>
        </div>

        <input id="latitude" type="hidden" name="latitude" value="{{ old('latitude') }}">
        <input id="longitude" type="hidden" name="longitude" value="{{ old('longitude') }}">

        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />

            <x-text-input id="password_confirmation" class="block mt-1 w-full"
                            type="password"
                            name="password_confirmation" required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('login') }}">
                {{ __('Already registered?') }}
            </a>

            <x-primary-button class="ms-4">
                {{ __('Register') }}
            </x-primary-button>
        </div>
    </form>

    @push('scripts')
    <script>
        /* ─── CDN fallback: si el bundle Vite aún no exportó window.L ─── */
        (function () {
            if (typeof window.L !== 'undefined') return;
            if (document.getElementById('leaflet-cdn-script')) return;
            const link = document.createElement('link');
            link.rel = 'stylesheet';
            link.id = 'leaflet-cdn-style';
            link.href = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css';
            document.head.appendChild(link);
            const s = document.createElement('script');
            s.id = 'leaflet-cdn-script';
            s.src = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js';
            document.head.appendChild(s);
        })();

        /* ─── Estado ─── */
        let registerMap = null;
        let registerMarker = null;

        /* ─── Mensajes ─── */
        function setRegisterGeoMessage(msg) {
            const el = document.getElementById('register-geo-message');
            if (el) el.textContent = msg;
            const form = document.getElementById('register-form');
            if (form?.__x?.$data) form.__x.$data.geoMessage = msg;
        }

        /* ─── Actualiza inputs ocultos y pin en mapa ─── */
        function setRegisterLocation(lat, lng, zoom = 12) {
            document.getElementById('latitude').value  = Number(lat).toFixed(7);
            document.getElementById('longitude').value = Number(lng).toFixed(7);
            if (!registerMap) return;
            const pt = [Number(lat), Number(lng)];
            if (registerMarker) registerMarker.setLatLng(pt);
            else registerMarker = window.L.marker(pt).addTo(registerMap);
            registerMap.setView(pt, zoom);
        }

        /* ─── Coloca pin según los inputs ocultos (después de init) ─── */
        window.updateRegistrationMapFromInputs = function () {
            if (!registerMap) return;
            const lat = parseFloat(document.getElementById('latitude').value);
            const lng = parseFloat(document.getElementById('longitude').value);
            if (isNaN(lat) || isNaN(lng)) return;
            const pt = [lat, lng];
            if (registerMarker) registerMarker.setLatLng(pt);
            else registerMarker = window.L.marker(pt).addTo(registerMap);
            registerMap.setView(pt, 12);
        };

        /* ─── Crea (o recrea) el mapa Leaflet en el contenedor ─── */
        function createRegisterMap() {
            const container = document.getElementById('register-map');
            if (!container) return;
            if (typeof window.L === 'undefined') return;
            /* Solo inicializar cuando el contenedor es visible y tiene dimensiones */
            if (container.offsetWidth === 0 || container.offsetHeight === 0) return;

            /* Destruir instancia previa si quedó corrupta */
            if (registerMap) {
                registerMap.remove();
                registerMap = null;
                registerMarker = null;
            }

            registerMap = window.L.map('register-map').setView([13.7, -89.2], 7);
            window.L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19, attribution: '&copy; OpenStreetMap contributors',
            }).addTo(registerMap);
            registerMap.on('click', e => setRegisterLocation(e.latlng.lat.toFixed(7), e.latlng.lng.toFixed(7)));

            /* Si ya hay coordenadas guardadas (ej. error de validación), mostrarlas */
            const lat = parseFloat(document.getElementById('latitude')?.value);
            const lng = parseFloat(document.getElementById('longitude')?.value);
            if (!isNaN(lat) && !isNaN(lng)) {
                registerMarker = window.L.marker([lat, lng]).addTo(registerMap);
                registerMap.setView([lat, lng], 12);
            }

            /* Forzar recalculo de tamaño tras render */
            setTimeout(() => registerMap && registerMap.invalidateSize(), 150);
        }

        /* ─── Poll hasta que el contenedor sea visible Y window.L exista ─── */
        function waitForRegisterMap(maxMs = 5000) {
            const start = Date.now();
            (function poll() {
                const container = document.getElementById('register-map');
                if (container && container.offsetWidth > 0 && container.offsetHeight > 0 && typeof window.L !== 'undefined') {
                    createRegisterMap();
                    return;
                }
                if (Date.now() - start < maxMs) setTimeout(poll, 120);
                else setRegisterGeoMessage('No se pudo cargar el mapa. Recarga la pagina con Ctrl+F5.');
            })();
        }

        /* ─── Geocoding Nominatim ─── */
        async function geocodeLocation(query) {
            const r = await fetch(`https://nominatim.openstreetmap.org/search?format=json&addressdetails=1&limit=1&q=${encodeURIComponent(query)}`);
            if (!r.ok) throw new Error('Error en el servicio de mapas.');
            const data = await r.json();
            return Array.isArray(data) && data.length ? data[0] : null;
        }

        /* ─── Buscar ubicacion por texto ─── */
        window.searchRegistrationLocation = async function () {
            const query = document.getElementById('register-location-search')?.value?.trim();
            if (!query) return;

            /* Si el rol es "user", cambiarlo a "provider" para mostrar sección */
            const roleSelect = document.getElementById('role');
            if (roleSelect && roleSelect.value === 'user') {
                roleSelect.value = 'provider';
                roleSelect.dispatchEvent(new Event('change'));
            }

            setRegisterGeoMessage('Buscando ubicacion...');

            /* Esperar a que el mapa esté listo (container visible + window.L) */
            await new Promise(resolve => {
                const start = Date.now();
                (function waitMap() {
                    const container = document.getElementById('register-map');
                    const ready = container && container.offsetWidth > 0 && container.offsetHeight > 0 && typeof window.L !== 'undefined';
                    if (ready) { createRegisterMap(); resolve(); return; }
                    if (Date.now() - start < 4000) setTimeout(waitMap, 120);
                    else resolve(); /* continuar aunque no haya mapa — coordenadas igual se guardan */
                })();
            });

            try {
                const result = await geocodeLocation(query);
                if (!result) {
                    setRegisterGeoMessage('No encontramos esa ubicacion. Prueba con mas detalle.');
                    return;
                }

                setRegisterLocation(result.lat, result.lon, 13);

                const address = result.address || {};
                const countryInput  = document.getElementById('country');
                const stateInput    = document.getElementById('state');
                const addressInput  = document.getElementById('address');
                if (countryInput && !countryInput.value) countryInput.value  = address.country || '';
                if (stateInput   && !stateInput.value)   stateInput.value    = address.state || address.region || address.county || '';
                if (addressInput && !addressInput.value) addressInput.value  = result.display_name || '';

                setRegisterGeoMessage('Ubicacion encontrada. Verifica el pin y guarda.');
            } catch {
                setRegisterGeoMessage('No se pudo buscar la ubicacion en este momento.');
            }
        };

        /* ─── Preview de fotos ─── */
        function renderImagePreviews(inputId, previewId) {
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
        function bootRegisterMap() {
            /* Fotos */
            const photosInput = document.getElementById('provider_photos');
            if (photosInput) photosInput.addEventListener('change', () => renderImagePreviews('provider_photos', 'register-provider-photos-preview'));

            const roleSelect = document.getElementById('role');
            if (!roleSelect) return;

            /* Cuando el usuario cambia el rol: destruir mapa anterior y esperar container visible */
            roleSelect.addEventListener('change', () => {
                if (registerMap) { registerMap.remove(); registerMap = null; registerMarker = null; }
                waitForRegisterMap();
            });

            /* Si la página cargó con rol != user (ej. error de validación devolvió el form) */
            if (roleSelect.value !== 'user') {
                waitForRegisterMap();
            }
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', bootRegisterMap, { once: true });
        } else {
            bootRegisterMap();
        }
    </script>
@endpush
</x-guest-layout>
