@extends('layouts.app')

@section('title', 'Editar Local | Dynasty')

@section('content')

<div class="max-w-2xl mx-auto">

    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('branches.show', $branch) }}" class="w-9 h-9 flex items-center justify-center rounded-xl border border-gray-200 hover:border-fuchsia-300 hover:bg-fuchsia-50 text-gray-500 hover:text-fuchsia-600 transition-all">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Editar Local</h1>
            <p class="text-sm text-gray-500">{{ $branch->name }}</p>
        </div>
    </div>

    <form action="{{ route('branches.update', $branch) }}" method="POST" enctype="multipart/form-data" class="space-y-5">
        @csrf @method('PUT')

        <div class="bg-white rounded-2xl border border-gray-200 p-6 space-y-4">
            <h2 class="font-semibold text-gray-900 text-base border-b border-gray-100 pb-3">Información básica</h2>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Nombre del local <span class="text-pink-500">*</span></label>
                <input type="text" name="name" value="{{ old('name', $branch->name) }}" required
                    class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-fuchsia-300 focus:border-transparent">
                @error('name')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Teléfono</label>
                    <input type="text" name="phone" value="{{ old('phone', $branch->phone) }}"
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-fuchsia-300 focus:border-transparent">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">WhatsApp</label>
                    <input type="text" name="whatsapp" value="{{ old('whatsapp', $branch->whatsapp) }}"
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-fuchsia-300 focus:border-transparent">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Email</label>
                <input type="email" name="email" value="{{ old('email', $branch->email) }}"
                    class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-fuchsia-300 focus:border-transparent">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Descripción</label>
                <textarea name="description" rows="3"
                    class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-fuchsia-300 focus:border-transparent resize-none">{{ old('description', $branch->description) }}</textarea>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-gray-200 p-6 space-y-4">
            <h2 class="font-semibold text-gray-900 text-base border-b border-gray-100 pb-3">Dirección</h2>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Dirección <span class="text-pink-500">*</span></label>
                <input type="text" name="address" value="{{ old('address', $branch->address) }}" required
                    class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-fuchsia-300 focus:border-transparent">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Distrito</label>
                    <input type="text" name="district" value="{{ old('district', $branch->district) }}"
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-fuchsia-300 focus:border-transparent">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Ciudad <span class="text-pink-500">*</span></label>
                    <input type="text" name="city" value="{{ old('city', $branch->city) }}" required
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-fuchsia-300 focus:border-transparent">
                </div>
            </div>
        </div>

        {{-- Imagen actual --}}
        <div class="bg-white rounded-2xl border border-gray-200 p-6 space-y-4">
            <h2 class="font-semibold text-gray-900 text-base border-b border-gray-100 pb-3">Imagen del local</h2>

            @if($branch->image)
                <div class="relative w-full h-40 rounded-xl overflow-hidden mb-3">
                    <img src="{{ asset('storage/' . $branch->image) }}" alt="{{ $branch->name }}" class="w-full h-full object-cover">
                    <div class="absolute inset-0 bg-black/20 flex items-center justify-center opacity-0 hover:opacity-100 transition-opacity">
                        <span class="text-white text-sm font-medium">Reemplazar imagen</span>
                    </div>
                </div>
            @endif

            <div class="border-2 border-dashed border-gray-200 rounded-xl p-5 text-center hover:border-fuchsia-300 transition-colors cursor-pointer" onclick="document.getElementById('branch-image').click()">
                <p class="text-sm text-gray-500">{{ $branch->image ? 'Subir nueva imagen (reemplaza la actual)' : 'Haz click para subir una foto' }}</p>
                <p class="text-xs text-gray-400 mt-1">JPG, PNG — máx. 2MB</p>
                <input type="file" name="image" id="branch-image" accept="image/*" class="hidden">
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="font-medium text-gray-900 text-sm">Local activo</p>
                    <p class="text-xs text-gray-400 mt-0.5">Si está inactivo no aparecerá en las citas</p>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" value="1" {{ $branch->is_active ? 'checked' : '' }}
                        class="sr-only peer">
                    <div class="w-11 h-6 bg-gray-200 peer-focus:ring-2 peer-focus:ring-fuchsia-300 rounded-full peer peer-checked:after:translate-x-full after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-gradient-to-r peer-checked:from-fuchsia-500 peer-checked:to-pink-600"></div>
                </label>
            </div>
        </div>

        <div class="flex gap-3 pb-6">
            <a href="{{ route('branches.show', $branch) }}"
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