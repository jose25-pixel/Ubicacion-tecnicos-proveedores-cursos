<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Panel Admin
        </h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm">
                    <p class="text-xs uppercase text-gray-500">Total usuarios</p>
                    <p class="text-3xl font-black text-gray-900 mt-1">{{ $totalUsers }}</p>
                </div>
                <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm">
                    <p class="text-xs uppercase text-gray-500">Usuarios comunes</p>
                    <p class="text-3xl font-black text-gray-900 mt-1">{{ $totalRegularUsers }}</p>
                </div>
                <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm">
                    <p class="text-xs uppercase text-gray-500">Tecnicos</p>
                    <p class="text-3xl font-black text-gray-900 mt-1">{{ $totalTechnicians }}</p>
                </div>
                <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm">
                    <p class="text-xs uppercase text-gray-500">Proveedores</p>
                    <p class="text-3xl font-black text-gray-900 mt-1">{{ $totalProviders }}</p>
                </div>
            </div>

            <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm overflow-x-auto">
                <h3 class="text-lg font-bold text-gray-900 mb-3">Usuarios por pais</h3>
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="text-left border-b border-gray-200">
                            <th class="py-2 pe-4">Pais</th>
                            <th class="py-2 pe-4">Usuarios</th>
                            <th class="py-2 pe-4">Tecnicos</th>
                            <th class="py-2 pe-4">Proveedores</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($usersByCountry as $item)
                            <tr class="border-b border-gray-100">
                                <td class="py-2 pe-4">{{ $item->country }}</td>
                                <td class="py-2 pe-4">{{ $item->users_count }}</td>
                                <td class="py-2 pe-4">{{ $item->technicians_count }}</td>
                                <td class="py-2 pe-4">{{ $item->providers_count }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-3 text-gray-500">Sin datos de pais.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="grid lg:grid-cols-2 gap-6">
                <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm overflow-x-auto">
                    <h3 class="text-lg font-bold text-gray-900 mb-3">Perfiles mas vistos</h3>
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr class="text-left border-b border-gray-200">
                                <th class="py-2 pe-4">Perfil</th>
                                <th class="py-2 pe-4">Tipo</th>
                                <th class="py-2 pe-4">Pais/Estado</th>
                                <th class="py-2 pe-4">Vistas</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($mostViewedProfiles as $item)
                                <tr class="border-b border-gray-100">
                                    <td class="py-2 pe-4">{{ $item->name }}</td>
                                    <td class="py-2 pe-4">{{ $item->role === 'technician' ? 'Tecnico' : 'Proveedor' }}</td>
                                    <td class="py-2 pe-4">{{ ($item->country ?: 'N/D') . ' / ' . ($item->state ?: 'N/D') }}</td>
                                    <td class="py-2 pe-4 font-semibold">{{ $item->total_views }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="py-3 text-gray-500">Aun no hay vistas registradas.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm overflow-x-auto">
                    <h3 class="text-lg font-bold text-gray-900 mb-3">Vistas ultimos 7 dias</h3>
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr class="text-left border-b border-gray-200">
                                <th class="py-2 pe-4">Dia</th>
                                <th class="py-2 pe-4">Vistas</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($viewsLast7Days as $item)
                                <tr class="border-b border-gray-100">
                                    <td class="py-2 pe-4">{{ \Carbon\Carbon::parse($item->view_day)->format('d/m/Y') }}</td>
                                    <td class="py-2 pe-4 font-semibold">{{ $item->total_views }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" class="py-3 text-gray-500">Sin vistas registradas.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
