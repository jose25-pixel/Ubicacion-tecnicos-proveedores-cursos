<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Cursos (Admin)</h2>
            <a href="{{ route('admin.courses.create') }}" class="inline-flex items-center px-4 py-2 rounded-md bg-sky-600 text-white text-sm font-semibold hover:bg-sky-500">Publicar nuevo curso</a>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left text-slate-500 border-b">
                            <th class="py-2">Titulo</th>
                            <th class="py-2">Precio</th>
                            <th class="py-2">Estado</th>
                            <th class="py-2">Publicado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($courses as $course)
                            <tr class="border-b border-slate-100">
                                <td class="py-3 font-semibold text-slate-800">{{ $course->title }}</td>
                                <td class="py-3">${{ number_format((float) $course->price, 2) }} {{ $course->currency }}</td>
                                <td class="py-3">
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold {{ $course->is_published ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-200 text-slate-700' }}">
                                        {{ $course->is_published ? 'Publicado' : 'Borrador' }}
                                    </span>
                                </td>
                                <td class="py-3">{{ optional($course->published_at)->format('d/m/Y H:i') ?: 'N/D' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-6 text-amber-800">No hay cursos creados aun.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="mt-6">
                    {{ $courses->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
