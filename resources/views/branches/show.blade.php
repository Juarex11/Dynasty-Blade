@extends('layouts.app')

@section('title', $branch->name . ' | Dynasty')

@section('content')

{{-- Header --}}
<div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4 mb-6">
    <div class="flex items-center gap-3">
        <a href="{{ route('branches.index') }}" class="w-9 h-9 flex items-center justify-center rounded-xl border border-gray-200 hover:border-fuchsia-300 hover:bg-fuchsia-50 text-gray-500 hover:text-fuchsia-600 transition-all flex-shrink-0">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div>
            <div class="flex items-center gap-2">
                <h1 class="text-2xl font-bold text-gray-900">{{ $branch->name }}</h1>
                <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold
                    {{ $branch->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                    {{ $branch->is_active ? 'Activo' : 'Inactivo' }}
                </span>
            </div>
            <p class="text-sm text-gray-500 mt-0.5">{{ $branch->full_address }}</p>
        </div>
    </div>
    <a href="{{ route('branches.edit', $branch) }}"
       class="inline-flex items-center gap-2 border border-gray-200 text-gray-700 hover:border-fuchsia-300 hover:text-fuchsia-600 font-medium px-4 py-2 rounded-xl transition-all text-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
        </svg>
        Editar
    </a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

    {{-- Columna izquierda: info + contacto --}}
    <div class="lg:col-span-1 space-y-5">

        {{-- Imagen --}}
        @if($branch->image)
        <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden">
            <img src="{{ asset('storage/' . $branch->image) }}" alt="{{ $branch->name }}" class="w-full h-48 object-cover">
        </div>
        @endif

        {{-- Contacto --}}
        <div class="bg-white rounded-2xl border border-gray-200 p-5 space-y-3">
            <h3 class="font-semibold text-gray-900">Contacto</h3>
            @if($branch->phone)
            <div class="flex items-center gap-3 text-sm text-gray-600">
                <svg class="w-4 h-4 text-fuchsia-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                </svg>
                {{ $branch->phone }}
            </div>
            @endif
            @if($branch->whatsapp)
            <div class="flex items-center gap-3 text-sm text-gray-600">
                <svg class="w-4 h-4 text-green-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                </svg>
                {{ $branch->whatsapp }}
            </div>
            @endif
            @if($branch->email)
            <div class="flex items-center gap-3 text-sm text-gray-600">
                <svg class="w-4 h-4 text-pink-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
                {{ $branch->email }}
            </div>
            @endif
        </div>

        {{-- Descripción --}}
        @if($branch->description)
        <div class="bg-white rounded-2xl border border-gray-200 p-5">
            <h3 class="font-semibold text-gray-900 mb-2">Descripción</h3>
            <p class="text-sm text-gray-600 leading-relaxed">{{ $branch->description }}</p>
        </div>
        @endif
    </div>

    {{-- Columna derecha: empleados + servicios --}}
    <div class="lg:col-span-2 space-y-5">

        {{-- Empleados --}}
        <div class="bg-white rounded-2xl border border-gray-200 p-5">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-semibold text-gray-900">Empleados ({{ $branch->employees->count() }})</h3>
                <a href="{{ route('employees.create') }}" class="text-xs text-fuchsia-600 hover:text-fuchsia-700 font-medium">+ Agregar</a>
            </div>

            @if($branch->employees->isEmpty())
                <p class="text-sm text-gray-400 text-center py-4">No hay empleados asignados a este local</p>
            @else
                <div class="space-y-2">
                    @foreach($branch->employees as $employee)
                    <div class="flex items-center gap-3 p-3 rounded-xl hover:bg-gray-50 transition-colors">
                        <div class="w-9 h-9 rounded-full overflow-hidden flex-shrink-0">
                            @if($employee->photo)
                                <img src="{{ asset('storage/' . $employee->photo) }}" alt="{{ $employee->full_name }}" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full bg-gradient-to-br from-fuchsia-100 to-pink-100 flex items-center justify-center text-fuchsia-600 font-bold text-sm">
                                    {{ strtoupper(substr($employee->first_name, 0, 1)) }}
                                </div>
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 truncate">{{ $employee->full_name }}</p>
                            <p class="text-xs text-gray-400">{{ $employee->position }}</p>
                        </div>
                        <a href="{{ route('employees.show', $employee) }}" class="text-xs text-gray-400 hover:text-fuchsia-600">Ver →</a>
                    </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Servicios --}}
        <div class="bg-white rounded-2xl border border-gray-200 p-5">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-semibold text-gray-900">Servicios ({{ $branch->services->count() }})</h3>
                <a href="{{ route('services.create') }}" class="text-xs text-fuchsia-600 hover:text-fuchsia-700 font-medium">+ Agregar</a>
            </div>

            @if($branch->services->isEmpty())
                <p class="text-sm text-gray-400 text-center py-4">No hay servicios asignados a este local</p>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    @foreach($branch->services as $service)
                    <div class="flex items-center gap-3 p-3 border border-gray-100 rounded-xl hover:border-fuchsia-200 transition-colors">
                        <div class="w-10 h-10 rounded-xl overflow-hidden flex-shrink-0 bg-fuchsia-50">
                            @if($service->cover_image)
                                <img src="{{ asset('storage/' . $service->cover_image) }}" alt="{{ $service->name }}" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex items-center justify-center">
                                    <svg class="w-5 h-5 text-fuchsia-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 truncate">{{ $service->name }}</p>
                            <p class="text-xs text-fuchsia-600 font-semibold">{{ $service->price_display }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>

@endsection