@extends('layouts.app')

@section('title', 'Clientes | Dynasty')

@section('content')

<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Clientes</h1>
        <p class="text-sm text-gray-500 mt-0.5">Gestión y segmentación de clientes</p>
    </div>
    <a href="{{ route('clients.create') }}"
       class="inline-flex items-center gap-2 bg-gradient-to-r from-violet-500 to-purple-600 hover:from-violet-600 hover:to-purple-700 text-white font-semibold px-4 py-2.5 rounded-xl shadow-lg shadow-violet-500/30 transition-all text-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Nuevo Cliente
    </a>
</div>

@if(session('success'))
<div class="mb-5 bg-green-50 border border-green-200 rounded-xl p-4 flex items-center gap-3">
    <svg class="w-5 h-5 text-green-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
    <p class="text-sm font-medium text-green-700">{{ session('success') }}</p>
</div>
@endif

{{-- Stats de segmentación --}}
<div class="grid grid-cols-3 sm:grid-cols-6 gap-3 mb-6">
    @php
    $typeConfig = [
        'total'      => ['label'=>'Total',      'color'=>'gray',   'icon'=>'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0'],
        'nuevo'      => ['label'=>'Nuevos',     'color'=>'blue',   'icon'=>'M12 4v16m8-8H4'],
        'recurrente' => ['label'=>'Recurrentes','color'=>'violet', 'icon'=>'M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15'],
        'vip'        => ['label'=>'VIP',        'color'=>'amber',  'icon'=>'M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z'],
        'unico'      => ['label'=>'Únicos',     'color'=>'red',    'icon'=>'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z'],
        'inactivo'   => ['label'=>'Inactivos',  'color'=>'gray',   'icon'=>'M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636'],
    ];
    $colorMap = [
        'gray'  => 'bg-gray-50 text-gray-600 border-gray-200',
        'blue'  => 'bg-blue-50 text-blue-600 border-blue-200',
        'violet'=> 'bg-violet-50 text-violet-600 border-violet-200',
        'amber' => 'bg-amber-50 text-amber-600 border-amber-200',
        'red'   => 'bg-red-50 text-red-600 border-red-200',
    ];
    @endphp
    @foreach($typeConfig as $key => $cfg)
    @php $c = $colorMap[$cfg['color']]; @endphp
    <div class="bg-white border border-gray-200 rounded-2xl p-3 text-center hover:border-violet-200 transition-colors cursor-pointer"
         onclick="setFilter('type','{{ $key === 'total' ? '' : $key }}')">
        <div class="w-8 h-8 rounded-xl {{ explode(' ',$c)[0] }} flex items-center justify-center mx-auto mb-1.5">
            <svg class="w-4 h-4 {{ explode(' ',$c)[1] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $cfg['icon'] }}"/>
            </svg>
        </div>
        <p class="text-lg font-bold text-gray-900">{{ $stats[$key] }}</p>
        <p class="text-xs text-gray-500">{{ $cfg['label'] }}</p>
    </div>
    @endforeach
</div>

<div class="grid grid-cols-1 lg:grid-cols-4 gap-5 mb-6">
    {{-- Filtros --}}
    <div class="lg:col-span-3">
        <form method="GET" id="filter-form" class="bg-white border border-gray-200 rounded-2xl p-4">
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                <div class="col-span-2 relative">
                    <svg class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z"/>
                    </svg>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Buscar por nombre, DNI, teléfono..."
                        class="w-full pl-9 pr-4 py-2 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-violet-300">
                </div>
                <select name="type" id="filter-type" class="px-3 py-2 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-violet-300 bg-white">
                    <option value="">Todos los tipos</option>
                    <option value="nuevo"      {{ request('type') === 'nuevo'      ? 'selected' : '' }}>Nuevos</option>
                    <option value="recurrente" {{ request('type') === 'recurrente' ? 'selected' : '' }}>Recurrentes</option>
                    <option value="vip"        {{ request('type') === 'vip'        ? 'selected' : '' }}>VIP</option>
                    <option value="unico"      {{ request('type') === 'unico'      ? 'selected' : '' }}>Únicos</option>
                    <option value="inactivo"   {{ request('type') === 'inactivo'   ? 'selected' : '' }}>Inactivos</option>
                </select>
                <div class="flex gap-2">
                    <select name="source" class="flex-1 px-3 py-2 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-violet-300 bg-white">
                        <option value="">Todas las fuentes</option>
                        @foreach(['instagram'=>'Instagram','facebook'=>'Facebook','tiktok'=>'TikTok','google'=>'Google','referido'=>'Referido','walk_in'=>'Walk-in','whatsapp'=>'WhatsApp','otro'=>'Otro'] as $v=>$l)
                        <option value="{{ $v }}" {{ request('source') === $v ? 'selected' : '' }}>{{ $l }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="px-4 py-2 bg-violet-600 hover:bg-violet-700 text-white rounded-xl text-sm font-medium transition-colors">Filtrar</button>
                </div>
            </div>
        </form>
    </div>

    {{-- Top distritos mini --}}
    <div class="bg-white border border-gray-200 rounded-2xl p-4">
        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3">Top Distritos</p>
        <div class="space-y-1.5">
            @forelse($topDistricts as $district => $count)
            <div class="flex items-center gap-2">
                <span class="text-xs text-gray-700 flex-1 truncate">{{ $district }}</span>
                <span class="text-xs font-semibold text-violet-600">{{ $count }}</span>
            </div>
            @empty
            <p class="text-xs text-gray-400 italic">Sin datos</p>
            @endforelse
        </div>
    </div>
</div>

{{-- Tabla de clientes --}}
@if($clients->isEmpty())
<div class="bg-white rounded-2xl border border-gray-200 p-14 text-center">
    <div class="w-14 h-14 bg-violet-50 rounded-2xl flex items-center justify-center mx-auto mb-4">
        <svg class="w-7 h-7 text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0"/>
        </svg>
    </div>
    <p class="text-gray-500 font-medium">No hay clientes registrados</p>
    <a href="{{ route('clients.create') }}" class="text-violet-600 hover:underline text-sm mt-1 inline-block">Registrar primer cliente</a>
</div>
@else
<div class="bg-white rounded-2xl border border-gray-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-gray-100">
                    <th class="text-left px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wide">Cliente</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wide hidden sm:table-cell">Ubicación</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wide hidden md:table-cell">Contacto</th>
                    <th class="text-center px-4 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wide">Tipo</th>
                    <th class="text-center px-4 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wide hidden lg:table-cell">Visitas</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wide hidden lg:table-cell">Fuente</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @foreach($clients as $client)
                @php
                    $typeColors = [
                        'nuevo'     =>'bg-blue-100 text-blue-700',
                        'recurrente'=>'bg-violet-100 text-violet-700',
                        'vip'       =>'bg-amber-100 text-amber-700',
                        'unico'     =>'bg-red-100 text-red-600',
                        'inactivo'  =>'bg-gray-100 text-gray-500',
                    ];
                    $tc = $typeColors[$client->client_type] ?? 'bg-gray-100 text-gray-500';
                @endphp
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-5 py-3">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-full overflow-hidden shrink-0">
                                @if($client->photo)
                                <img src="{{ asset('storage/'.$client->photo) }}" class="w-full h-full object-cover">
                                @else
                                <div class="w-full h-full bg-violet-100 flex items-center justify-center text-violet-600 text-sm font-bold">
                                    {{ strtoupper(substr($client->first_name,0,1)) }}
                                </div>
                                @endif
                            </div>
                            <div class="min-w-0">
                                <p class="font-medium text-gray-900 truncate">{{ $client->full_name }}</p>
                                <p class="text-xs text-gray-400">{{ $client->age ? $client->age.' años' : ($client->dni ?? '—') }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-4 py-3 hidden sm:table-cell">
                        <p class="text-gray-700">{{ $client->district ?? '—' }}</p>
                        <p class="text-xs text-gray-400">{{ $client->department }}</p>
                    </td>
                    <td class="px-4 py-3 hidden md:table-cell">
                        <p class="text-gray-700">{{ $client->phone ?? '—' }}</p>
                        <p class="text-xs text-gray-400 truncate max-w-[140px]">{{ $client->email ?? '' }}</p>
                    </td>
                    <td class="px-4 py-3 text-center">
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $tc }}">
                            {{ $client->client_type_label }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-center hidden lg:table-cell">
                        <span class="font-semibold text-gray-800">{{ $client->visit_count }}</span>
                        @if($client->last_visit_at)
                        <p class="text-xs text-gray-400">{{ $client->last_visit_at->diffForHumans() }}</p>
                        @endif
                    </td>
                    <td class="px-4 py-3 hidden lg:table-cell">
                        <span class="text-xs text-gray-500">{{ $client->acquisition_label }}</span>
                    </td>
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-2 justify-end">
                            <a href="{{ route('clients.show', $client) }}" class="text-violet-600 hover:underline text-xs font-medium">Ver</a>
                            <a href="{{ route('clients.edit', $client) }}" class="text-gray-400 hover:text-gray-600 text-xs">Editar</a>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@if($clients->hasPages())
<div class="mt-4">{{ $clients->withQueryString()->links() }}</div>
@endif
@endif

<script>
function setFilter(name, value) {
    document.getElementById('filter-' + name).value = value;
    document.getElementById('filter-form').submit();
}
</script>

@endsection