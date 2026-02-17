@extends('layouts.app')

@section('title', (isset($courseCategory) ? 'Editar' : 'Nueva') . ' Categoría de Curso | Dynasty')

@section('content')

<div class="max-w-xl mx-auto">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('course-categories.index') }}" class="w-9 h-9 flex items-center justify-center rounded-xl border border-gray-200 hover:border-violet-300 hover:bg-violet-50 text-gray-500 hover:text-violet-600 transition-all">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ isset($courseCategory) ? 'Editar Categoría' : 'Nueva Categoría' }}</h1>
            <p class="text-sm text-gray-500">{{ isset($courseCategory) ? $courseCategory->name : 'Completa la información' }}</p>
        </div>
    </div>

    @if($errors->any())
    <div class="mb-5 bg-red-50 border border-red-200 rounded-xl p-4">
        <ul class="text-sm text-red-600 space-y-1 list-disc list-inside">
            @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
        </ul>
    </div>
    @endif

    <form action="{{ isset($courseCategory) ? route('course-categories.update', $courseCategory) : route('course-categories.store') }}"
          method="POST" class="space-y-5">
        @csrf
        @isset($courseCategory) @method('PUT') @endisset

        <div class="bg-white rounded-2xl border border-gray-200 p-6 space-y-4">
            <h2 class="font-semibold text-gray-900 text-base border-b border-gray-100 pb-3">Información</h2>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Nombre <span class="text-pink-500">*</span></label>
                <input type="text" name="name" value="{{ old('name', $courseCategory->name ?? '') }}" required autofocus
                    class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-violet-300 @error('name') border-red-400 @enderror"
                    placeholder="Ej: Técnicas de Color, Barbería, Estética...">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Descripción</label>
                <textarea name="description" rows="3"
                    class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-violet-300 resize-none"
                    placeholder="Descripción de la categoría..." maxlength="500">{{ old('description', $courseCategory->description ?? '') }}</textarea>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Color</label>
                    <div class="flex items-center gap-3">
                        <input type="color" name="color" value="{{ old('color', $courseCategory->color ?? '#8b5cf6') }}"
                            class="w-12 h-10 rounded-xl border border-gray-200 cursor-pointer p-1">
                        <span class="text-sm text-gray-500">Color identificador</span>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Orden</label>
                    <input type="number" name="sort_order" value="{{ old('sort_order', $courseCategory->sort_order ?? 0) }}" min="0"
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-violet-300">
                </div>
            </div>

            <div class="flex items-center justify-between py-1">
                <div>
                    <p class="text-sm font-medium text-gray-900">Categoría activa</p>
                    <p class="text-xs text-gray-400">Visible al crear cursos</p>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" value="1"
                        {{ old('is_active', $courseCategory->is_active ?? true) ? 'checked' : '' }}
                        class="sr-only peer">
                    <div class="w-11 h-6 bg-gray-200 peer-focus:ring-2 peer-focus:ring-violet-300 rounded-full peer peer-checked:after:translate-x-full after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-gradient-to-r peer-checked:from-violet-500 peer-checked:to-purple-600"></div>
                </label>
            </div>
        </div>

        <div class="flex gap-3 pb-6">
            <a href="{{ route('course-categories.index') }}" class="flex-1 text-center py-3 border border-gray-200 text-gray-600 font-semibold rounded-xl hover:bg-gray-50 transition-colors">Cancelar</a>
            <button type="submit" class="flex-1 bg-gradient-to-r from-violet-500 to-purple-600 hover:from-violet-600 hover:to-purple-700 text-white font-semibold py-3 rounded-xl shadow-lg shadow-violet-500/30 transition-all">
                {{ isset($courseCategory) ? 'Guardar cambios' : 'Crear Categoría' }}
            </button>
        </div>
    </form>
</div>

@endsection