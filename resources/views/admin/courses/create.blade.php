<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Publicar Curso</h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <form method="POST" action="{{ route('admin.courses.store') }}" class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm space-y-4">
                @csrf

                <div>
                    <label class="block text-sm font-semibold text-slate-700">Titulo</label>
                    <input name="title" value="{{ old('title') }}" class="mt-1 w-full rounded-md border-slate-300 focus:border-sky-500 focus:ring-sky-500" required>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700">Slug (opcional)</label>
                    <input name="slug" value="{{ old('slug') }}" class="mt-1 w-full rounded-md border-slate-300 focus:border-sky-500 focus:ring-sky-500" placeholder="curso-reparacion-lavadoras">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700">Resumen</label>
                    <textarea name="summary" rows="3" class="mt-1 w-full rounded-md border-slate-300 focus:border-sky-500 focus:ring-sky-500">{{ old('summary') }}</textarea>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700">Descripcion</label>
                    <textarea name="description" rows="5" class="mt-1 w-full rounded-md border-slate-300 focus:border-sky-500 focus:ring-sky-500">{{ old('description') }}</textarea>
                </div>

                <div class="grid sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700">Precio</label>
                        <input type="number" step="0.01" min="0" name="price" value="{{ old('price', '0.00') }}" class="mt-1 w-full rounded-md border-slate-300 focus:border-sky-500 focus:ring-sky-500" required>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700">Moneda</label>
                        <input name="currency" value="{{ old('currency', 'USD') }}" maxlength="3" class="mt-1 w-full rounded-md border-slate-300 focus:border-sky-500 focus:ring-sky-500" required>
                    </div>
                </div>

                <label class="inline-flex items-center gap-2 text-sm text-slate-700">
                    <input type="checkbox" name="is_published" value="1" {{ old('is_published') ? 'checked' : '' }} class="rounded border-slate-300 text-sky-600 focus:ring-sky-500">
                    Publicar inmediatamente
                </label>

                @if ($errors->any())
                    <div class="rounded-md border border-rose-200 bg-rose-50 p-3 text-sm text-rose-900">
                        {{ $errors->first() }}
                    </div>
                @endif

                <div class="flex items-center gap-3">
                    <button class="px-4 py-2 rounded-md bg-sky-600 text-white text-sm font-semibold hover:bg-sky-500">Guardar curso</button>
                    <a href="{{ route('admin.courses.index') }}" class="px-4 py-2 rounded-md border border-slate-300 text-sm font-semibold text-slate-700 hover:bg-slate-100">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
