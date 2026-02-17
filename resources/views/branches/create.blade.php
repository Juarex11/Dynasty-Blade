@extends('layouts.app')

@section('title', 'Nuevo Local | Dynasty')

@section('content')

<div class="max-w-2xl mx-auto">

    {{-- Header --}}
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('branches.index') }}" class="w-9 h-9 flex items-center justify-center rounded-xl border border-gray-200 hover:border-fuchsia-300 hover:bg-fuchsia-50 text-gray-500 hover:text-fuchsia-600 transition-all">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Nuevo Local</h1>
            <p class="text-sm text-gray-500">Completa los datos de la sede</p>
        </div>
    </div>

    <form action="{{ route('branches.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
        @csrf

        {{-- Sección: Información básica --}}
        <div class="bg-white rounded-2xl border border-gray-200 p-6 space-y-4">
            <h2 class="font-semibold text-gray-900 text-base border-b border-gray-100 pb-3">Información básica</h2>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Nombre del local <span class="text-pink-500">*</span></label>
                <input type="text" name="name" value="{{ old('name') }}" required
                    class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-fuchsia-300 focus:border-transparent @error('name') border-red-400 @enderror"
                    placeholder="Ej: Dynasty Miraflores">
                @error('name')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Teléfono</label>
                    <input type="text" name="phone" value="{{ old('phone') }}"
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-fuchsia-300 focus:border-transparent"
                        placeholder="+51 999 999 999">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">WhatsApp</label>
                    <input type="text" name="whatsapp" value="{{ old('whatsapp') }}"
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-fuchsia-300 focus:border-transparent"
                        placeholder="+51 999 999 999">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Email</label>
                <input type="email" name="email" value="{{ old('email') }}"
                    class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-fuchsia-300 focus:border-transparent"
                    placeholder="local@dynastysalon.com">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Descripción</label>
                <textarea name="description" rows="3"
                    class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-fuchsia-300 focus:border-transparent resize-none"
                    placeholder="Describe brevemente este local...">{{ old('description') }}</textarea>
            </div>
        </div>

        {{-- Sección: Dirección --}}
        <div class="bg-white rounded-2xl border border-gray-200 p-6 space-y-4">
            <h2 class="font-semibold text-gray-900 text-base border-b border-gray-100 pb-3">Dirección</h2>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Dirección <span class="text-pink-500">*</span></label>
                <input type="text" name="address" value="{{ old('address') }}" required
                    class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-fuchsia-300 focus:border-transparent @error('address') border-red-400 @enderror"
                    placeholder="Av. Larco 345">
                @error('address')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Distrito</label>
                    <input type="text" name="district" value="{{ old('district') }}"
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-fuchsia-300 focus:border-transparent"
                        placeholder="Miraflores">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Ciudad <span class="text-pink-500">*</span></label>
                    <input type="text" name="city" value="{{ old('city') ?? 'Lima' }}" required
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-fuchsia-300 focus:border-transparent"
                        placeholder="Lima">
                </div>
            </div>
        </div>

        {{-- Sección: Imagen --}}
        <div class="bg-white rounded-2xl border border-gray-200 p-6 space-y-4">
            <h2 class="font-semibold text-gray-900 text-base border-b border-gray-100 pb-3">Imagen del local</h2>

            <div class="border-2 border-dashed border-gray-200 rounded-xl p-6 text-center hover:border-fuchsia-300 transition-colors cursor-pointer" onclick="document.getElementById('branch-image').click()">
                <svg class="w-10 h-10 text-gray-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <p class="text-sm text-gray-500">Haz click para subir una foto del local</p>
                <p class="text-xs text-gray-400 mt-1">JPG, PNG — máx. 2MB</p>
                <input type="file" name="image" id="branch-image" accept="image/*" class="hidden">
            </div>
        </div>

        {{-- Estado --}}
        <div class="bg-white rounded-2xl border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="font-medium text-gray-900 text-sm">Local activo</p>
                    <p class="text-xs text-gray-400 mt-0.5">Si está inactivo no aparecerá en las citas</p>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" value="1" checked
                        class="sr-only peer">
                    <div class="w-11 h-6 bg-gray-200 peer-focus:ring-2 peer-focus:ring-fuchsia-300 rounded-full peer peer-checked:after:translate-x-full after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-gradient-to-r peer-checked:from-fuchsia-500 peer-checked:to-pink-600"></div>
                </label>
            </div>
        </div>

        {{-- Botones --}}
        <div class="flex gap-3 pb-6">
            <a href="{{ route('branches.index') }}"
               class="flex-1 text-center py-3 border border-gray-200 text-gray-600 font-semibold rounded-xl hover:bg-gray-50 transition-colors">
                Cancelar
            </a>
            <button type="submit"
                class="flex-1 bg-gradient-to-r from-fuchsia-500 to-pink-600 hover:from-fuchsia-600 hover:to-pink-700 text-white font-semibold py-3 rounded-xl shadow-lg shadow-fuchsia-500/30 transition-all duration-200">
                Crear Local
            </button>
        </div>
    </form>
</div>

@endsection