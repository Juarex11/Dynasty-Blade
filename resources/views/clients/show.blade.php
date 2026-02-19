@extends('layouts.app')

@section('title', $client->full_name . ' | Dynasty')

@section('content')

<div class="flex items-start gap-3 mb-6">
    <a href="{{ route('clients.index') }}"
       class="w-9 h-9 flex items-center justify-center rounded-xl border border-gray-200 hover:border-violet-300 hover:bg-violet-50 text-gray-500 hover:text-violet-600 transition-all shrink-0 mt-0.5">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
    </a>
    <div class="flex-1 min-w-0">
        <div class="flex flex-wrap items-center gap-2 mb-1">
            @php
            $typeColors = ['nuevo'=>'bg-blue-100 text-blue-700','recurrente'=>'bg-violet-100 text-violet-700','vip'=>'bg-amber-100 text-amber-700','unico'=>'bg-red-100 text-red-600','inactivo'=>'bg-gray-100 text-gray-500'];
            $tc = $typeColors[$client->client_type] ?? 'bg-gray-100 text-gray-500';
            @endphp
            <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $tc }}">{{ $client->client_type_label }}</span>
            @if(!$client->is_active)
            <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-400">Inactivo</span>
            @endif
        </div>
        <h1 class="text-2xl font-bold text-gray-900">{{ $client->full_name }}</h1>
        @if($client->age)<p class="text-sm text-gray-400 mt-0.5">{{ $client->age }} años</p>@endif
    </div>
    <div class="flex gap-2 shrink-0">
        {{-- Registrar visita --}}
        <form action="{{ route('clients.visit', $client) }}" method="POST">
            @csrf
            <button type="submit"
                class="inline-flex items-center gap-1.5 border border-green-200 text-green-600 hover:bg-green-50 font-medium px-3 py-2 rounded-xl transition-colors text-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Visita
            </button>
        </form>
        <a href="{{ route('clients.edit', $client) }}"
           class="inline-flex items-center gap-2 bg-gradient-to-r from-violet-500 to-purple-600 hover:from-violet-600 hover:to-purple-700 text-white font-semibold px-4 py-2 rounded-xl shadow-lg shadow-violet-500/30 transition-all text-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
            Editar
        </a>
    </div>
</div>

@if(session('success'))
<div class="mb-5 bg-green-50 border border-green-200 rounded-xl p-4 flex items-center gap-3">
    <svg class="w-5 h-5 text-green-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
    <p class="text-sm font-medium text-green-700">{{ session('success') }}</p>
</div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

    {{-- Columna principal --}}
    <div class="lg:col-span-2 space-y-5">

        {{-- Info de contacto y ubicación --}}
        <div class="bg-white rounded-2xl border border-gray-200 p-6">
            <div class="flex gap-5 items-start">
                {{-- Avatar --}}
                <div class="w-20 h-20 rounded-2xl overflow-hidden shrink-0">
                    @if($client->photo)
                    <img src="{{ asset('storage/'.$client->photo) }}" class="w-full h-full object-cover">
                    @else
                    <div class="w-full h-full bg-gradient-to-br from-violet-400 to-purple-600 flex items-center justify-center text-white text-2xl font-bold">
                        {{ strtoupper(substr($client->first_name,0,1)) }}
                    </div>
                    @endif
                </div>

                <div class="flex-1 grid grid-cols-2 gap-x-6 gap-y-3 text-sm">
                    @if($client->phone)
                    <div>
                        <p class="text-xs text-gray-400 mb-0.5">Teléfono</p>
                        <a href="https://wa.me/51{{ preg_replace('/\D/','',$client->phone) }}" target="_blank"
                           class="font-medium text-green-600 hover:underline flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                            {{ $client->phone }}
                        </a>
                    </div>
                    @endif
                    @if($client->email)
                    <div>
                        <p class="text-xs text-gray-400 mb-0.5">Email</p>
                        <p class="font-medium text-gray-800 truncate">{{ $client->email }}</p>
                    </div>
                    @endif
                    @if($client->dni)
                    <div>
                        <p class="text-xs text-gray-400 mb-0.5">DNI</p>
                        <p class="font-medium text-gray-800">{{ $client->dni }}</p>
                    </div>
                    @endif
                    @if($client->gender)
                    <div>
                        <p class="text-xs text-gray-400 mb-0.5">Género</p>
                        <p class="font-medium text-gray-800 capitalize">{{ str_replace('_',' ',$client->gender) }}</p>
                    </div>
                    @endif
                    @if($client->birthdate)
                    <div>
                        <p class="text-xs text-gray-400 mb-0.5">Cumpleaños</p>
                        <p class="font-medium text-gray-800">🎂 {{ $client->birthdate->format('d/m/Y') }}</p>
                    </div>
                    @endif
                    @if($client->district || $client->department)
                    <div>
                        <p class="text-xs text-gray-400 mb-0.5">Ubicación</p>
                        <p class="font-medium text-gray-800">
                            {{ collect([$client->district, $client->province, $client->department])->filter()->join(', ') }}
                        </p>
                    </div>
                    @endif
                    @if($client->address)
                    <div class="col-span-2">
                        <p class="text-xs text-gray-400 mb-0.5">Dirección</p>
                        <p class="font-medium text-gray-800">{{ $client->address }}</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Perfil de marketing --}}
        <div class="bg-white rounded-2xl border border-gray-200 p-6">
            <h2 class="font-semibold text-gray-900 mb-4 flex items-center gap-2">
                <svg class="w-4 h-4 text-violet-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                Perfil de marketing
            </h2>
            <div class="grid grid-cols-2 gap-4 text-sm">
                <div>
                    <p class="text-xs text-gray-400 mb-1">Fuente de captación</p>
                    <p class="font-medium text-gray-800">{{ $client->acquisition_label }}</p>
                    @if($client->referred_by)
                    <p class="text-xs text-gray-400 mt-0.5">Referido por: {{ $client->referred_by }}</p>
                    @endif
                </div>
                <div>
                    <p class="text-xs text-gray-400 mb-1">Tipo de cabello</p>
                    <p class="font-medium text-gray-800 capitalize">{{ str_replace('_',' ',$client->hair_type ?? '—') }}</p>
                </div>
                @if($client->services_interest)
                <div class="col-span-2">
                    <p class="text-xs text-gray-400 mb-2">Servicios de interés</p>
                    <div class="flex flex-wrap gap-1.5">
                        @foreach($client->services_interest as $svc)
                        <span class="px-2.5 py-1 bg-violet-50 border border-violet-100 text-violet-700 text-xs rounded-lg font-medium">{{ $svc }}</span>
                        @endforeach
                    </div>
                </div>
                @endif
                @if($client->tags)
                <div class="col-span-2">
                    <p class="text-xs text-gray-400 mb-2">Etiquetas</p>
                    <div class="flex flex-wrap gap-1.5">
                        @foreach(explode(',', $client->tags) as $tag)
                        <span class="px-2.5 py-1 bg-gray-100 text-gray-600 text-xs rounded-lg">{{ trim($tag) }}</span>
                        @endforeach
                    </div>
                </div>
                @endif
                @if($client->notes)
                <div class="col-span-2">
                    <p class="text-xs text-gray-400 mb-1">Notas internas</p>
                    <p class="text-sm text-gray-700 bg-gray-50 rounded-xl p-3">{{ $client->notes }}</p>
                </div>
                @endif
            </div>
        </div>

        {{-- Cursos inscritos --}}
        <div class="bg-white rounded-2xl border border-gray-200 p-6">
            <h2 class="font-semibold text-gray-900 mb-4 flex items-center gap-2">
                <svg class="w-4 h-4 text-violet-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                Cursos
                <span class="ml-auto text-xs font-normal text-gray-400">{{ $client->courseOpenings->count() }} inscripciones</span>
            </h2>
            @forelse($client->courseOpenings as $opening)
            @php
                $st = $opening->pivot->status ?? 'inscrito';
                $stColors = ['inscrito'=>'bg-blue-100 text-blue-700','en_curso'=>'bg-amber-100 text-amber-700','completado'=>'bg-green-100 text-green-700','abandonado'=>'bg-red-100 text-red-500','retirado'=>'bg-gray-100 text-gray-500'];
                $sc = $stColors[$st] ?? 'bg-gray-100 text-gray-500';
            @endphp
            <div class="flex items-center justify-between p-3 border border-gray-100 rounded-xl mb-2">
                <div>
                    <p class="text-sm font-medium text-gray-800">{{ $opening->display_name }}</p>
                    <p class="text-xs text-gray-400">{{ $opening->start_date->format('d/m/Y') }} · {{ $opening->course->name }}</p>
                </div>
                <div class="text-right">
                    <span class="px-2 py-0.5 rounded-full text-xs font-semibold {{ $sc }} block mb-1">{{ ucfirst(str_replace('_',' ',$st)) }}</span>
                    @if($opening->pivot->price_paid !== null)
                    <p class="text-xs text-gray-500">S/. {{ number_format($opening->pivot->price_paid,2) }}</p>
                    @endif
                </div>
            </div>
            @empty
            <p class="text-sm text-gray-400 italic">Sin cursos inscritos.</p>
            @endforelse
        </div>
    </div>

    {{-- Sidebar --}}
    <div class="space-y-5">
        {{-- Métricas de visita --}}
        <div class="bg-white rounded-2xl border border-gray-200 p-5">
            <h2 class="font-semibold text-gray-900 mb-4 text-sm">Actividad</h2>
            <div class="space-y-3">
                <div class="text-center py-3 bg-violet-50 rounded-xl">
                    <p class="text-3xl font-bold text-violet-600">{{ $client->visit_count }}</p>
                    <p class="text-xs text-gray-500 mt-0.5">Visitas totales</p>
                </div>
                @if($client->first_visit_at)
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500">Primera visita</span>
                    <span class="font-medium text-gray-700">{{ $client->first_visit_at->format('d/m/Y') }}</span>
                </div>
                @endif
                @if($client->last_visit_at)
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500">Última visita</span>
                    <span class="font-medium text-gray-700">{{ $client->last_visit_at->diffForHumans() }}</span>
                </div>
                @endif
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500">Registrado</span>
                    <span class="font-medium text-gray-700">{{ $client->created_at->format('d/m/Y') }}</span>
                </div>
            </div>
        </div>

        {{-- Acciones --}}
        <div class="bg-white rounded-2xl border border-gray-200 p-5 space-y-2">
            <form action="{{ route('clients.visit', $client) }}" method="POST">
                @csrf
                <button type="submit" class="w-full flex items-center justify-center gap-2 py-2.5 border border-green-200 text-green-600 hover:bg-green-50 font-medium rounded-xl transition-colors text-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Registrar visita
                </button>
            </form>
            <a href="{{ route('clients.edit', $client) }}"
               class="w-full flex items-center justify-center gap-2 py-2.5 border border-violet-200 text-violet-600 hover:bg-violet-50 font-medium rounded-xl transition-colors text-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                Editar cliente
            </a>
            <form action="{{ route('clients.destroy', $client) }}" method="POST"
                  onsubmit="return confirm('¿Eliminar este cliente?')">
                @csrf @method('DELETE')
                <button type="submit" class="w-full flex items-center justify-center gap-2 py-2.5 border border-red-200 text-red-500 hover:bg-red-50 font-medium rounded-xl transition-colors text-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    Eliminar cliente
                </button>
            </form>
        </div>
    </div>
</div>

@endsection