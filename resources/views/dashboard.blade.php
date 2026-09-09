<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 space-y-3">
                    <p>{{ __('Bienvenido a tu panel.') }}</p>
                    <p><strong>Tipo de cuenta:</strong> {{ auth()->user()->role === 'technician' ? 'Tecnico' : (auth()->user()->role === 'provider' ? 'Proveedor' : 'Usuario') }}</p>
                    <p><strong>Cursos activos:</strong> {{ auth()->user()->courseEnrollments()->where('status', 'active')->count() }}</p>

                    @if (auth()->user()->role === 'technician')
                        <p>
                            <strong>Estado tecnico:</strong>
                            @if (auth()->user()->isTechnicianVerified())
                                <span class="text-emerald-700 font-semibold">Verificado</span>
                            @else
                                <span class="text-amber-700 font-semibold">Pendiente de verificacion</span>
                            @endif
                        </p>
                        <a href="{{ route('technician.verification.create') }}" class="inline-flex items-center px-4 py-2 rounded-md bg-emerald-600 text-white text-sm font-semibold hover:bg-emerald-500">
                            {{ __('Presentar Examen Tecnico') }}
                        </a>
                    @endif

                    <a href="{{ route('directory.index') }}" class="inline-flex items-center px-4 py-2 rounded-md bg-indigo-600 text-white text-sm font-semibold hover:bg-indigo-500">
                        {{ __('Ir al Directorio') }}
                    </a>

                    <a href="{{ route('courses.index') }}" class="inline-flex items-center px-4 py-2 rounded-md bg-sky-600 text-white text-sm font-semibold hover:bg-sky-500">
                        {{ __('Ver Catalogo de Cursos') }}
                    </a>

                    <a href="{{ route('courses.my') }}" class="inline-flex items-center px-4 py-2 rounded-md bg-slate-900 text-white text-sm font-semibold hover:bg-slate-700">
                        {{ __('Mis Cursos') }}
                    </a>

                    @if (auth()->user()->role === 'admin')
                        <a href="{{ route('admin.courses.index') }}" class="inline-flex items-center px-4 py-2 rounded-md bg-emerald-600 text-white text-sm font-semibold hover:bg-emerald-500">
                            {{ __('Publicar Cursos (Admin)') }}
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
