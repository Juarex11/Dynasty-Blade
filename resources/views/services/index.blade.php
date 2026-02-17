@extends('layouts.app')

@section('title', 'Servicios | Dynasty')

@section('content')

{{-- Header --}}
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Servicios</h1>
        <p class="text-sm text-gray-500 mt-0.5">Todos los servicios de Dynasty</p>
    </div>
    <a href="{{ route('services.create') }}"
       class="inline-flex items-center gap-2 bg-gradient-to-r from-fuchsia-500 to-pink-600 hover:from-fuchsia-600 hover:to-pink-700 text-white font-semibold px-5 py-2.5 rounded-xl shadow-lg shadow-fuchsia-500/30 transition-all duration-200">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Nuevo Servicio
    </a>
</div>

{{-- Filtros --}}
<form method="GET" class="bg-white rounded-2xl border border-gray-200 p-4 mb-5 flex flex-wrap gap-3 items-end">
    <div class="flex-1 min-w-48">
        <label class="block text-xs font-medium text-gray-500 mb-1.5">Buscar</label>
        <input type="text" name="search" value="{{ request('search') }}"
            class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-fuchsia-200"
            placeholder="Nombre del servicio...">
    </div>
    <div class="min-w-40">
        <label class="block text-xs font-medium text-gray-500 mb-1.5">Categoría</label>
        <select name="category" class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-fuchsia-200 bg-white">
            <option value="">Todas</option>
            @foreach($categories as $cat)
                <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="min-w-40">
        <label class="block text-xs font-medium text-gray-500 mb-1.5">Sede</label>
        <select name="branch" class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-fuchsia-200 bg-white">
            <option value="">Todas las sedes</option>
            @foreach($branches as $branch)
                <option value="{{ $branch->id }}" {{ request('branch') == $branch->id ? 'selected' : '' }}>{{ $branch->name }}</option>
            @endforeach
        </select>
    </div>
    <button type="submit" class="px-5 py-2 bg-fuchsia-50 text-fuchsia-700 font-medium rounded-xl hover:bg-fuchsia-100 transition-colors text-sm">
        Filtrar
    </button>
    @if(request()->hasAny(['search','category','branch']))
    <a href="{{ route('services.index') }}" class="px-4 py-2 text-gray-500 hover:text-gray-700 text-sm">Limpiar</a>
    @endif
</form>

{{-- Flash --}}
@if(session('success'))
<div class="mb-4 flex items-center gap-3 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl text-sm">
    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
    </svg>
    {{ session('success') }}
</div>
@endif

{{-- Grid de servicios --}}
@if($services->isEmpty())
    <div class="bg-white rounded-2xl border border-gray-200 p-12 text-center">
        <div class="w-16 h-16 bg-fuchsia-50 rounded-2xl flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-fuchsia-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
            </svg>
        </div>
        <p class="text-gray-500 font-medium">No se encontraron servicios</p>
        <a href="{{ route('services.create') }}" class="mt-3 inline-block text-fuchsia-600 hover:text-fuchsia-700 font-semibold text-sm">
            + Crear el primer servicio
        </a>
    </div>
@else
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-5">
        @foreach($services as $service)
        <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden hover:shadow-md hover:border-fuchsia-200 transition-all duration-200 group flex flex-col">

            {{-- Cover --}}
            <div class="h-40 bg-gradient-to-br from-fuchsia-50 to-pink-50 overflow-hidden relative">
                @if($service->cover_image)
                    <img src="{{ asset('storage/' . $service->cover_image) }}" alt="{{ $service->name }}"
                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                @else
                    <div class="w-full h-full flex items-center justify-center">
                        <svg class="w-12 h-12 text-fuchsia-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                    </div>
                @endif
                <div class="absolute top-3 left-3 flex gap-1.5">
                    <span class="px-2 py-0.5 bg-white/90 backdrop-blur-sm text-xs font-semibold text-fuchsia-700 rounded-full">
                        {{ $service->category->name }}
                    </span>
                    @if($service->is_featured)
                    <span class="px-2 py-0.5 bg-yellow-400/90 text-xs font-semibold text-yellow-900 rounded-full">
                        ⭐ Destacado
                    </span>
                    @endif
                </div>
                @if(!$service->is_active)
                <div class="absolute top-3 right-3">
                    <span class="px-2 py-0.5 bg-gray-800/70 text-xs font-semibold text-white rounded-full">Inactivo</span>
                </div>
                @endif
            </div>

            {{-- Info --}}
            <div class="p-5 flex-1 flex flex-col">
                <h3 class="font-bold text-gray-900 mb-1">{{ $service->name }}</h3>
                @if($service->short_description)
                <p class="text-xs text-gray-500 mb-3 line-clamp-2">{{ $service->short_description }}</p>
                @endif

                <div class="flex items-center gap-3 mt-auto mb-4">
                    <div class="flex-1 text-center bg-fuchsia-50 rounded-xl py-2">
                        <p class="text-base font-bold text-fuchsia-700">{{ $service->price_display }}</p>
                        <p class="text-xs text-gray-400">Precio</p>
                    </div>
                    <div class="flex-1 text-center bg-pink-50 rounded-xl py-2">
                        <p class="text-base font-bold text-pink-700">{{ $service->duration_display }}</p>
                        <p class="text-xs text-gray-400">Duración</p>
                    </div>
                    <div class="flex-1 text-center bg-gray-50 rounded-xl py-2">
                        <p class="text-base font-bold text-gray-700">{{ $service->employees_count }}</p>
                        <p class="text-xs text-gray-400">Espec.</p>
                    </div>
                </div>

                {{-- Sedes --}}
                @if($service->branches->count())
                <div class="flex flex-wrap gap-1 mb-4">
                    @foreach($service->branches->take(3) as $branch)
                    <span class="px-2 py-0.5 bg-gray-100 text-gray-600 text-xs rounded-full">{{ $branch->name }}</span>
                    @endforeach
                    @if($service->branches->count() > 3)
                    <span class="px-2 py-0.5 bg-gray-100 text-gray-500 text-xs rounded-full">+{{ $service->branches->count() - 3 }}</span>
                    @endif
                </div>
                @endif

                {{-- Acciones --}}
                <div class="flex items-center gap-2 pt-3 border-t border-gray-100">
                    <a href="{{ route('services.show', $service) }}"
                       class="flex-1 text-center text-sm font-medium text-fuchsia-600 hover:text-fuchsia-700 py-1.5 rounded-lg hover:bg-fuchsia-50 transition-colors">
                        Ver
                    </a>
                    <a href="{{ route('services.edit', $service) }}"
                       class="flex-1 text-center text-sm font-medium text-gray-600 hover:text-gray-700 py-1.5 rounded-lg hover:bg-gray-50 transition-colors">
                        Editar
                    </a>
                    <form action="{{ route('services.destroy', $service) }}" method="POST" class="flex-1"
                          onsubmit="return confirm('¿Eliminar este servicio?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="w-full text-center text-sm font-medium text-red-500 hover:text-red-600 py-1.5 rounded-lg hover:bg-red-50 transition-colors">
                            Eliminar
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Paginación --}}
    @if($services->hasPages())
    <div class="mt-6">
        {{ $services->links() }}
    </div>
    @endif
@endif

@endsection