@extends('layouts.app')

@section('title', $service->name . ' | Dynasty')

@section('content')

<div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4 mb-6">
    <div class="flex items-center gap-3">
        <a href="{{ route('services.index') }}" class="w-9 h-9 flex items-center justify-center rounded-xl border border-gray-200 hover:border-fuchsia-300 hover:bg-fuchsia-50 text-gray-500 hover:text-fuchsia-600 transition-all flex-shrink-0">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div>
            <div class="flex items-center gap-2 flex-wrap">
                <h1 class="text-2xl font-bold text-gray-900">{{ $service->name }}</h1>
                {{-- Badge de categoría con color dinámico --}}
                <a href="{{ route('services.index', ['category' => $service->category->id]) }}"
                   class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold transition-all hover:opacity-80"
                   style="background-color: {{ $service->category->color ?? '#d946ef' }}1a; color: {{ $service->category->color ?? '#d946ef' }}; border: 1px solid {{ $service->category->color ?? '#d946ef' }}40">
                    <span class="w-1.5 h-1.5 rounded-full" style="background-color: {{ $service->category->color ?? '#d946ef' }}"></span>
                    {{ $service->category->name }}
                </a>
                @if(!$service->is_active)
                <span class="px-2 py-0.5 bg-gray-100 text-gray-500 text-xs font-semibold rounded-full">Inactivo</span>
                @endif
                @if($service->is_featured)
                <span class="px-2 py-0.5 bg-amber-100 text-amber-700 text-xs font-semibold rounded-full">⭐ Destacado</span>
                @endif
            </div>
            @if($service->short_description)
            <p class="text-sm text-gray-500 mt-0.5">{{ $service->short_description }}</p>
            @endif
        </div>
    </div>
    <div class="flex items-center gap-2 shrink-0">
        
        <a href="{{ route('services.edit', $service) }}"
           class="inline-flex items-center gap-2 border border-gray-200 text-gray-700 hover:border-fuchsia-300 hover:text-fuchsia-600 font-medium px-4 py-2 rounded-xl transition-all text-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
            </svg>
            Editar
        </a>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

    {{-- Columna izquierda --}}
    <div class="space-y-5">

        {{-- Cover --}}
        @if($service->cover_image)
        <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden">
            <img src="{{ asset('storage/' . $service->cover_image) }}" alt="{{ $service->name }}" class="w-full h-48 object-cover">
        </div>
        @endif

        {{-- Precio y duración --}}
        <div class="bg-white rounded-2xl border border-gray-200 p-5 grid grid-cols-2 gap-4">
            <div class="text-center p-3 bg-fuchsia-50 rounded-xl">
                <p class="text-xl font-bold text-fuchsia-700">{{ $service->price_display }}</p>
                <p class="text-xs text-gray-400 mt-0.5">Precio</p>
            </div>
            <div class="text-center p-3 bg-pink-50 rounded-xl">
                <p class="text-xl font-bold text-pink-700">{{ $service->duration_display }}</p>
                <p class="text-xs text-gray-400 mt-0.5">Duración</p>
            </div>
            @if($service->buffer_minutes)
            <div class="col-span-2 text-center p-3 bg-gray-50 rounded-xl">
                <p class="text-sm font-semibold text-gray-600">+{{ $service->buffer_minutes }} min buffer</p>
                <p class="text-xs text-gray-400 mt-0.5">Preparación/limpieza</p>
            </div>
            @endif
            @if($service->requires_deposit && $service->deposit_amount)
            <div class="col-span-2 text-center p-3 bg-amber-50 rounded-xl">
                <p class="text-sm font-semibold text-amber-700">Seña: S/. {{ number_format($service->deposit_amount, 0) }}</p>
                <p class="text-xs text-gray-400 mt-0.5">Requerida al reservar</p>
            </div>
            @endif
        </div>

        {{-- Información de categoría --}}
        <div class="bg-white rounded-2xl border border-gray-200 p-5">
            <div class="flex items-center justify-between mb-3">
                <h3 class="font-semibold text-gray-900">Categoría</h3>
               
            </div>
            <div class="flex items-center gap-3 p-3 rounded-xl border"
                 style="border-color: {{ $service->category->color ?? '#d946ef' }}30; background-color: {{ $service->category->color ?? '#d946ef' }}0d">
                <div class="w-9 h-9 rounded-xl flex items-center justify-center shrink-0"
                     style="background-color: {{ $service->category->color ?? '#d946ef' }}20">
                    <div class="w-3 h-3 rounded-full" style="background-color: {{ $service->category->color ?? '#d946ef' }}"></div>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-gray-900">{{ $service->category->name }}</p>
                    @if($service->category->description)
                    <p class="text-xs text-gray-400 truncate">{{ $service->category->description }}</p>
                    @endif
                </div>
            </div>
            {{-- Otros servicios de esta categoría --}}
            @php
                $siblings = $service->category->services()
                    ->where('id', '!=', $service->id)
                    ->active()
                    ->limit(4)
                    ->get();
            @endphp
            @if($siblings->count())
            <div class="mt-3 pt-3 border-t border-gray-100">
                <p class="text-xs text-gray-400 font-medium mb-2 uppercase tracking-wide">Más en esta categoría</p>
                <div class="space-y-1.5">
                    @foreach($siblings as $sibling)
                    <a href="{{ route('services.show', $sibling) }}"
                       class="flex items-center justify-between text-sm text-gray-700 hover:text-fuchsia-600 transition-colors py-0.5">
                        <span class="truncate">{{ $sibling->name }}</span>
                        <span class="text-xs text-gray-400 shrink-0 ml-2">{{ $sibling->price_display }}</span>
                    </a>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        {{-- Sedes --}}
        <div class="bg-white rounded-2xl border border-gray-200 p-5">
            <h3 class="font-semibold text-gray-900 mb-3">Disponible en</h3>
            @if($service->branches->isEmpty())
                <p class="text-sm text-gray-400">Sin sedes asignadas</p>
            @else
                <div class="space-y-2">
                    @foreach($service->branches as $branch)
                    <a href="{{ route('branches.show', $branch) }}" class="flex items-center gap-2 text-sm text-gray-700 hover:text-fuchsia-600 transition-colors">
                        <span class="w-2 h-2 rounded-full {{ $branch->is_active ? 'bg-green-400' : 'bg-gray-300' }} flex-shrink-0"></span>
                        {{ $branch->name }}
                    </a>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    {{-- Columna derecha --}}
    <div class="lg:col-span-2 space-y-5">

        {{-- Descripción --}}
        @if($service->description)
        <div class="bg-white rounded-2xl border border-gray-200 p-5">
            <h3 class="font-semibold text-gray-900 mb-3">Descripción</h3>
            <p class="text-sm text-gray-600 leading-relaxed">{{ $service->description }}</p>
        </div>
        @endif

        {{-- Galería --}}
        @if($service->images->count())
        <div class="bg-white rounded-2xl border border-gray-200 p-5">
            <h3 class="font-semibold text-gray-900 mb-3">Galería ({{ $service->images->count() }})</h3>
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                @foreach($service->images as $image)
                <div class="relative group rounded-xl overflow-hidden h-32 bg-gray-100">
                    <img src="{{ asset('storage/' . $image->path) }}" alt="{{ $image->alt ?? $service->name }}" class="w-full h-full object-cover">
                    @if($image->is_primary)
                    <span class="absolute top-2 left-2 px-2 py-0.5 bg-fuchsia-600 text-white text-xs font-bold rounded-full">Principal</span>
                    @endif
                    <div class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center gap-2">
                        @if(!$image->is_primary)
                        <form action="{{ route('services.images.primary', $image) }}" method="POST">
                            @csrf @method('PATCH')
                            <button type="submit" class="text-white text-xs bg-fuchsia-600 px-2 py-1 rounded-lg">Principal</button>
                        </form>
                        @endif
                        <form action="{{ route('services.images.destroy', $image) }}" method="POST" onsubmit="return confirm('¿Eliminar imagen?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-white text-xs bg-red-500 px-2 py-1 rounded-lg">Eliminar</button>
                        </form>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Especialistas --}}
        <div class="bg-white rounded-2xl border border-gray-200 p-5">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-semibold text-gray-900">Especialistas ({{ $service->employees->count() }})</h3>
                <a href="{{ route('employees.index') }}" class="text-xs text-fuchsia-600 hover:text-fuchsia-700 font-medium">Ver todos →</a>
            </div>
            @if($service->employees->isEmpty())
                <p class="text-sm text-gray-400 text-center py-4">No hay especialistas asignados a este servicio</p>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    @foreach($service->employees as $employee)
                    <a href="{{ route('employees.show', $employee) }}"
                       class="flex items-center gap-3 p-3 border border-gray-100 rounded-xl hover:border-fuchsia-200 hover:bg-fuchsia-50 transition-all">
                        <div class="w-10 h-10 rounded-full overflow-hidden flex-shrink-0">
                            @if($employee->photo)
                                <img src="{{ asset('storage/' . $employee->photo) }}" alt="{{ $employee->full_name }}" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full bg-gradient-to-br from-fuchsia-100 to-pink-100 flex items-center justify-center text-fuchsia-600 font-bold">
                                    {{ strtoupper(substr($employee->first_name, 0, 1)) }}
                                </div>
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 truncate">{{ $employee->full_name }}</p>
                            <p class="text-xs text-gray-400">{{ ucfirst($employee->pivot->skill_level ?? 'mid') }}</p>
                        </div>
                    </a>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>

@endsection