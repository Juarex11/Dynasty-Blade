@extends('layouts.app')

@section('title', 'Editar Servicio | Dynasty')

@section('content')

<div class="max-w-2xl mx-auto">

    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('services.show', $service) }}" class="w-9 h-9 flex items-center justify-center rounded-xl border border-gray-200 hover:border-fuchsia-300 hover:bg-fuchsia-50 text-gray-500 hover:text-fuchsia-600 transition-all">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Editar Servicio</h1>
            <p class="text-sm text-gray-500">{{ $service->name }}</p>
        </div>
    </div>

    @if($errors->any())
    <div class="mb-5 bg-red-50 border border-red-200 rounded-xl p-4">
        <ul class="text-sm text-red-600 space-y-1 list-disc list-inside">
            @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('services.update', $service) }}" method="POST" enctype="multipart/form-data" class="space-y-5">
        @csrf @method('PUT')

        <div class="bg-white rounded-2xl border border-gray-200 p-6 space-y-4">
            <h2 class="font-semibold text-gray-900 text-base border-b border-gray-100 pb-3">Información básica</h2>

            <div class="grid grid-cols-2 gap-4">
                <div class="col-span-2 sm:col-span-1">
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Categoría <span class="text-pink-500">*</span></label>
                    <select name="service_category_id" required class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-fuchsia-300 bg-white">
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('service_category_id', $service->service_category_id) == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-span-2 sm:col-span-1">
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Nombre <span class="text-pink-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $service->name) }}" required
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-fuchsia-300">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Descripción corta</label>
                <input type="text" name="short_description" value="{{ old('short_description', $service->short_description) }}"
                    class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-fuchsia-300" maxlength="300">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Descripción completa</label>
                <textarea name="description" rows="4" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-fuchsia-300 resize-none">{{ old('description', $service->description) }}</textarea>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-gray-200 p-6 space-y-4">
            <h2 class="font-semibold text-gray-900 text-base border-b border-gray-100 pb-3">Precio y duración</h2>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Precio base (S/.) <span class="text-pink-500">*</span></label>
                    <input type="number" name="price" value="{{ old('price', $service->price) }}" required min="0" step="0.01"
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-fuchsia-300">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Precio máximo (S/.)</label>
                    <input type="number" name="price_max" value="{{ old('price_max', $service->price_max) }}" min="0" step="0.01"
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-fuchsia-300">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Duración (minutos) <span class="text-pink-500">*</span></label>
                    <input type="number" name="duration_minutes" value="{{ old('duration_minutes', $service->duration_minutes) }}" required min="5" step="5"
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-fuchsia-300">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Buffer (minutos)</label>
                    <input type="number" name="buffer_minutes" value="{{ old('buffer_minutes', $service->buffer_minutes) }}" min="0" step="5"
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-fuchsia-300">
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-gray-200 p-6 space-y-4">
            <h2 class="font-semibold text-gray-900 text-base border-b border-gray-100 pb-3">Disponible en sedes</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                @foreach($branches as $branch)
                <label class="flex items-center gap-3 p-3 border border-gray-200 rounded-xl cursor-pointer hover:border-fuchsia-300 hover:bg-fuchsia-50 transition-all has-[:checked]:border-fuchsia-400 has-[:checked]:bg-fuchsia-50">
                    <input type="checkbox" name="branch_ids[]" value="{{ $branch->id }}"
                        {{ in_array($branch->id, old('branch_ids', $service->branches->pluck('id')->toArray())) ? 'checked' : '' }}
                        class="w-4 h-4 text-fuchsia-600 rounded border-gray-300">
                    <span class="text-sm font-medium text-gray-800">{{ $branch->name }}</span>
                </label>
                @endforeach
            </div>
        </div>

        {{-- Imagen cover --}}
        <div class="bg-white rounded-2xl border border-gray-200 p-6 space-y-4">
            <h2 class="font-semibold text-gray-900 text-base border-b border-gray-100 pb-3">Imagen principal</h2>
            @if($service->cover_image)
            <div class="relative h-36 rounded-xl overflow-hidden mb-2">
                <img src="{{ asset('storage/' . $service->cover_image) }}" alt="{{ $service->name }}" class="w-full h-full object-cover">
            </div>
            @endif
            <div class="border-2 border-dashed border-gray-200 rounded-xl p-5 text-center hover:border-fuchsia-300 transition-colors cursor-pointer" onclick="document.getElementById('cover-img').click()">
                <p class="text-sm text-gray-500">{{ $service->cover_image ? 'Reemplazar imagen' : 'Subir imagen' }}</p>
                <input type="file" name="cover_image" id="cover-img" accept="image/*" class="hidden">
            </div>
        </div>

        {{-- Configuración --}}
        <div class="bg-white rounded-2xl border border-gray-200 p-6 space-y-3">
            <h2 class="font-semibold text-gray-900 text-base border-b border-gray-100 pb-3">Configuración</h2>
            @foreach([
                ['is_active', 'Servicio activo', 'Visible para los clientes'],
                ['is_featured', 'Destacado', 'Aparece primero en los listados'],
                ['online_booking', 'Reserva online', 'Permite reservas por internet'],
                ['requires_deposit', 'Requiere seña', 'El cliente debe pagar una seña'],
            ] as [$name, $label, $desc])
            <div class="flex items-center justify-between py-1">
                <div>
                    <p class="text-sm font-medium text-gray-900">{{ $label }}</p>
                    <p class="text-xs text-gray-400">{{ $desc }}</p>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="hidden" name="{{ $name }}" value="0">
                    <input type="checkbox" name="{{ $name }}" value="1"
                        {{ old($name, $service->$name) ? 'checked' : '' }}
                        class="sr-only peer">
                    <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:after:translate-x-full after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-gradient-to-r peer-checked:from-fuchsia-500 peer-checked:to-pink-600"></div>
                </label>
            </div>
            @endforeach
        </div>

        <div class="flex gap-3 pb-6">
            <a href="{{ route('services.show', $service) }}"
               class="flex-1 text-center py-3 border border-gray-200 text-gray-600 font-semibold rounded-xl hover:bg-gray-50 transition-colors">
                Cancelar
            </a>
            <button type="submit"
                class="flex-1 bg-gradient-to-r from-fuchsia-500 to-pink-600 hover:from-fuchsia-600 hover:to-pink-700 text-white font-semibold py-3 rounded-xl shadow-lg shadow-fuchsia-500/30 transition-all duration-200">
                Guardar cambios
            </button>
        </div>
    </form>
</div>

@endsection