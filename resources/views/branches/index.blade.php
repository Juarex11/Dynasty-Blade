@extends('layouts.app')

@section('title', 'Locales | Dynasty')

@section('content')

{{-- Header --}}
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Locales</h1>
        <p class="text-sm text-gray-500 mt-0.5">Gestiona las sedes de Dynasty</p>
    </div>
    <a href="{{ route('branches.create') }}"
       class="inline-flex items-center gap-2 bg-gradient-to-r from-fuchsia-500 to-pink-600 hover:from-fuchsia-600 hover:to-pink-700 text-white font-semibold px-5 py-2.5 rounded-xl shadow-lg shadow-fuchsia-500/30 transition-all duration-200">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Nuevo Local
    </a>
</div>

{{-- Flash --}}
@if(session('success'))
    <div class="mb-4 flex items-center gap-3 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl text-sm">
        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
        </svg>
        {{ session('success') }}
    </div>
@endif

{{-- Grid de locales --}}
@if($branches->isEmpty())
    <div class="bg-white rounded-2xl border border-gray-200 p-12 text-center">
        <div class="w-16 h-16 bg-fuchsia-50 rounded-2xl flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-fuchsia-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
            </svg>
        </div>
        <p class="text-gray-500 font-medium">No hay locales registrados</p>
        <a href="{{ route('branches.create') }}" class="mt-3 inline-block text-fuchsia-600 hover:text-fuchsia-700 font-semibold text-sm">
            + Agregar el primer local
        </a>
    </div>
@else
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
        @foreach($branches as $branch)
        <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden hover:shadow-md hover:border-fuchsia-200 transition-all duration-200 group">

{{-- Imagen --}}
<div class="h-32 bg-gradient-to-br from-fuchsia-50 to-pink-50 overflow-hidden relative">
    @if($branch->image)
        <img src="{{ asset('storage/' . $branch->image) }}" alt="{{ $branch->name }}"
             class="w-full h-full object-cover object-center group-hover:scale-105 transition-transform duration-300">
    @else
        <div class="w-full h-full flex items-center justify-center">
            <svg class="w-14 h-14 text-fuchsia-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
            </svg>
        </div>
    @endif
    {{-- Badge estado --}}
    <span class="absolute top-3 right-3 px-2.5 py-1 rounded-full text-xs font-semibold
        {{ $branch->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
        {{ $branch->is_active ? 'Activo' : 'Inactivo' }}
    </span>
</div>


            {{-- Info --}}
            <div class="p-5">
                <h3 class="font-bold text-gray-900 text-lg mb-1">{{ $branch->name }}</h3>
                <p class="text-sm text-gray-500 flex items-center gap-1.5 mb-3">
                    <svg class="w-4 h-4 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    {{ $branch->full_address }}
                </p>

                {{-- Stats --}}
                <div class="flex gap-4 mb-4">
                    <div class="text-center">
                        <p class="text-xl font-bold text-fuchsia-600">{{ $branch->employees_count }}</p>
                        <p class="text-xs text-gray-400">Empleados</p>
                    </div>
                    <div class="w-px bg-gray-100"></div>
                    <div class="text-center">
                        <p class="text-xl font-bold text-pink-600">{{ $branch->services_count }}</p>
                        <p class="text-xs text-gray-400">Servicios</p>
                    </div>
                    @if($branch->phone)
                    <div class="w-px bg-gray-100"></div>
                    <div class="text-center overflow-hidden">
                        <p class="text-sm font-semibold text-gray-700 truncate">{{ $branch->phone }}</p>
                        <p class="text-xs text-gray-400">Teléfono</p>
                    </div>
                    @endif
                </div>

                {{-- Acciones --}}
                <div class="flex items-center gap-2 pt-3 border-t border-gray-100">
                    <a href="{{ route('branches.show', $branch) }}"
                       class="flex-1 text-center text-sm font-medium text-fuchsia-600 hover:text-fuchsia-700 py-1.5 rounded-lg hover:bg-fuchsia-50 transition-colors">
                        Ver detalle
                    </a>
                    <a href="{{ route('branches.edit', $branch) }}"
                       class="flex-1 text-center text-sm font-medium text-gray-600 hover:text-gray-700 py-1.5 rounded-lg hover:bg-gray-50 transition-colors">
                        Editar
                    </a>
                    <form action="{{ route('branches.toggle', $branch) }}" method="POST" class="flex-1">
                        @csrf @method('PATCH')
                        <button type="submit"
                            class="w-full text-center text-sm font-medium py-1.5 rounded-lg transition-colors
                            {{ $branch->is_active ? 'text-red-500 hover:bg-red-50' : 'text-green-600 hover:bg-green-50' }}">
                            {{ $branch->is_active ? 'Desactivar' : 'Activar' }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @endforeach
    </div>
@endif

@endsection