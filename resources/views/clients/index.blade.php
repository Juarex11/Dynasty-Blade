@extends('layouts.app')

@section('title', 'Clientes | Dynasty')

@section('content')

<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Clientes</h1>
        <p class="text-sm text-gray-500 mt-0.5">Gestión y segmentación de clientes</p>
    </div>
    <div class="flex items-center gap-2">
        <a href="{{ route('clients.export') }}"
           class="inline-flex items-center gap-2 border border-green-300 bg-green-50 hover:bg-green-100 text-green-700 font-semibold px-4 py-2.5 rounded-xl transition-all text-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            Exportar Excel
        </a>
        <a href="{{ route('clients.create') }}"
           class="inline-flex items-center gap-2 bg-gradient-to-r from-violet-500 to-purple-600 hover:from-violet-600 hover:to-purple-700 text-white font-semibold px-4 py-2.5 rounded-xl shadow-lg shadow-violet-500/30 transition-all text-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Nuevo Cliente
        </a>
    </div>
</div>

@if(session('success'))
<div class="mb-5 bg-green-50 border border-green-200 rounded-xl p-4 flex items-center gap-3">
    <svg class="w-5 h-5 text-green-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
    <p class="text-sm font-medium text-green-700">{{ session('success') }}</p>
</div>
@endif

{{-- Stats --}}
<div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-8 gap-3 mb-5">
    @php
    $statCards = [
        ['key'=>'',           'label'=>'Total',       'color'=>'violet', 'val'=>$stats['total'],      'mode'=>false],
        ['key'=>'frecuente',  'label'=>'Frecuentes',  'color'=>'violet', 'val'=>$stats['frecuente'],  'mode'=>true],
        ['key'=>'ocasional',  'label'=>'Ocasionales', 'color'=>'blue',   'val'=>$stats['ocasional'],  'mode'=>true],
        ['key'=>'vip',        'label'=>'VIP',         'color'=>'amber',  'val'=>$stats['vip'],        'mode'=>false],
        ['key'=>'recurrente', 'label'=>'Recurrentes', 'color'=>'purple', 'val'=>$stats['recurrente'], 'mode'=>false],
        ['key'=>'nuevo',      'label'=>'Nuevos',      'color'=>'sky',    'val'=>$stats['nuevo'],      'mode'=>false],
        ['key'=>'inactivo',   'label'=>'Inactivos',   'color'=>'gray',   'val'=>$stats['inactivo'],   'mode'=>false],
        ['key'=>'unico',      'label'=>'Únicos',      'color'=>'red',    'val'=>$stats['unico'],      'mode'=>false],
    ];
    $colorMap = [
        'violet'=>['text'=>'text-violet-600'], 'blue'  =>['text'=>'text-blue-600'],
        'amber' =>['text'=>'text-amber-600'], 'purple'=>['text'=>'text-purple-600'],
        'sky'   =>['text'=>'text-sky-600'],   'gray'  =>['text'=>'text-gray-500'],
        'red'   =>['text'=>'text-red-600'],
    ];
    @endphp
    @foreach($statCards as $card)
    @php $c = $colorMap[$card['color']]; @endphp
    <div class="bg-white border border-gray-200 rounded-2xl p-3 hover:border-violet-200 transition-colors cursor-pointer text-center"
         onclick="{{ $card['mode'] ? "setFilter('mode','{$card['key']}')" : "setFilter('type','{$card['key']}')" }}">
        <p class="text-2xl font-bold {{ $c['text'] }}">{{ $card['val'] }}</p>
        <p class="text-xs text-gray-500 mt-0.5">{{ $card['label'] }}</p>
    </div>
    @endforeach
</div>

{{-- Mini reportes --}}
<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-5">

    {{-- Modo --}}
    <div class="bg-white border border-gray-200 rounded-2xl p-4">
        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-3">Modo de cliente</p>
        @php
        $tot = $stats['total'] > 0 ? $stats['total'] : 1;
        $pctF = round($stats['frecuente'] / $tot * 100);
        $pctO = round($stats['ocasional'] / $tot * 100);
        @endphp
        <div class="space-y-2.5">
            <div>
                <div class="flex justify-between text-xs mb-1">
                    <span class="font-semibold text-violet-600">Frecuentes</span>
                    <span class="text-gray-500">{{ $stats['frecuente'] }} · {{ $pctF }}%</span>
                </div>
                <div class="h-2 bg-violet-100 rounded-full overflow-hidden">
                    <div class="h-full bg-violet-500 rounded-full transition-all" style="width:{{ $pctF }}%"></div>
                </div>
            </div>
            <div>
                <div class="flex justify-between text-xs mb-1">
                    <span class="font-semibold text-blue-600">Ocasionales</span>
                    <span class="text-gray-500">{{ $stats['ocasional'] }} · {{ $pctO }}%</span>
                </div>
                <div class="h-2 bg-blue-100 rounded-full overflow-hidden">
                    <div class="h-full bg-blue-500 rounded-full transition-all" style="width:{{ $pctO }}%"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Top fuentes --}}
    <div class="bg-white border border-gray-200 rounded-2xl p-4">
        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-3">Top Fuentes de Captación</p>
        @php $sourceLabels = ['instagram'=>'📸 Instagram','facebook'=>'👥 Facebook','tiktok'=>'🎵 TikTok','google'=>'🔍 Google','referido'=>'🤝 Referido','walk_in'=>'🚶 Walk-in','whatsapp'=>'💬 WhatsApp','otro'=>'Otro']; @endphp
        <div class="space-y-1.5">
            @forelse($topSources->take(5) as $source => $count)
            @php $pctS = $stats['total'] > 0 ? round($count / $stats['total'] * 100) : 0; @endphp
            <div class="flex items-center gap-2">
                <span class="text-xs text-gray-700 flex-1 truncate">{{ $sourceLabels[$source] ?? $source }}</span>
                <div class="w-16 h-1.5 bg-gray-100 rounded-full overflow-hidden">
                    <div class="h-full bg-violet-400 rounded-full" style="width:{{ min(100,$pctS) }}%"></div>
                </div>
                <span class="text-xs font-semibold text-violet-600 w-6 text-right">{{ $count }}</span>
            </div>
            @empty
            <p class="text-xs text-gray-400 italic">Sin datos</p>
            @endforelse
        </div>
    </div>

    {{-- Top distritos --}}
    <div class="bg-white border border-gray-200 rounded-2xl p-4">
        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-3">Top Distritos</p>
        <div class="space-y-1.5">
            @forelse($topDistricts->take(5) as $district => $count)
            @php $pctD = $stats['total'] > 0 ? round($count / $stats['total'] * 100) : 0; @endphp
            <div class="flex items-center gap-2">
                <span class="text-xs text-gray-700 flex-1 truncate">{{ $district }}</span>
                <div class="w-16 h-1.5 bg-gray-100 rounded-full overflow-hidden">
                    <div class="h-full bg-green-400 rounded-full" style="width:{{ min(100,$pctD) }}%"></div>
                </div>
                <span class="text-xs font-semibold text-green-600 w-6 text-right">{{ $count }}</span>
            </div>
            @empty
            <p class="text-xs text-gray-400 italic">Sin datos</p>
            @endforelse
        </div>
    </div>
</div>

{{-- Filtros --}}
<div class="bg-white border border-gray-200 rounded-2xl p-4 mb-5">
    <form method="GET" id="filter-form">
        <div class="grid grid-cols-2 sm:grid-cols-5 gap-3">
            <div class="col-span-2 relative">
                <svg class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z"/></svg>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Buscar por nombre, DNI, teléfono..."
                    class="w-full pl-9 pr-4 py-2 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-violet-300">
            </div>
            <select name="mode" id="filter-mode" class="px-3 py-2 border border-gray-200 rounded-xl text-sm bg-white focus:outline-none focus:ring-2 focus:ring-violet-300">
                <option value="">Todos los modos</option>
                <option value="frecuente" {{ request('mode') === 'frecuente' ? 'selected' : '' }}>Frecuentes</option>
                <option value="ocasional" {{ request('mode') === 'ocasional' ? 'selected' : '' }}>Ocasionales</option>
            </select>
            <select name="type" id="filter-type" class="px-3 py-2 border border-gray-200 rounded-xl text-sm bg-white focus:outline-none focus:ring-2 focus:ring-violet-300">
                <option value="">Todos los tipos</option>
                <option value="nuevo"      {{ request('type') === 'nuevo'      ? 'selected' : '' }}>Nuevos</option>
                <option value="recurrente" {{ request('type') === 'recurrente' ? 'selected' : '' }}>Recurrentes</option>
                <option value="vip"        {{ request('type') === 'vip'        ? 'selected' : '' }}>VIP</option>
                <option value="unico"      {{ request('type') === 'unico'      ? 'selected' : '' }}>Únicos</option>
                <option value="inactivo"   {{ request('type') === 'inactivo'   ? 'selected' : '' }}>Inactivos</option>
            </select>
            <div class="flex gap-2">
                <select name="source" class="flex-1 px-3 py-2 border border-gray-200 rounded-xl text-sm bg-white focus:outline-none focus:ring-2 focus:ring-violet-300">
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

{{-- Tabla --}}
@if($clients->isEmpty())
<div class="bg-white rounded-2xl border border-gray-200 p-14 text-center">
    <div class="w-14 h-14 bg-violet-50 rounded-2xl flex items-center justify-center mx-auto mb-4">
        <svg class="w-7 h-7 text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0"/></svg>
    </div>
    <p class="text-gray-500 font-medium">No hay clientes registrados</p>
    <a href="{{ route('clients.create') }}" class="text-violet-600 hover:underline text-sm mt-1 inline-block">Registrar primer cliente</a>
</div>
@else
<div class="bg-white rounded-2xl border border-gray-200 overflow-hidden">
    <div class="flex items-center justify-between px-5 py-3 border-b border-gray-100">
        <p class="text-sm text-gray-500">{{ $clients->total() }} clientes encontrados</p>
        <a href="{{ route('clients.export') }}" class="inline-flex items-center gap-1.5 text-xs text-green-700 font-medium hover:underline">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            Descargar Excel
        </a>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-gray-100">
                    <th class="text-left px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wide">Cliente</th>
                    <th class="text-center px-4 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wide">Modo</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wide hidden sm:table-cell">Ubicación</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wide hidden md:table-cell">Contacto</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wide hidden lg:table-cell">
    Registro
</th>

                    <th class="text-center px-4 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wide">Tipo</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wide hidden lg:table-cell">Fuente</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @foreach($clients as $client)
                @php
                    $typeColors = ['nuevo'=>'bg-blue-100 text-blue-700','recurrente'=>'bg-violet-100 text-violet-700','vip'=>'bg-amber-100 text-amber-700','unico'=>'bg-red-100 text-red-600','inactivo'=>'bg-gray-100 text-gray-500'];
                    $tc = $typeColors[$client->client_type] ?? 'bg-gray-100 text-gray-500';
                    $modeColor = $client->client_mode === 'frecuente' ? 'bg-violet-50 text-violet-600 border border-violet-200' : 'bg-blue-50 text-blue-600 border border-blue-200';
                @endphp
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-5 py-3">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-full overflow-hidden shrink-0">
                                @if($client->photo)
                                <img src="{{ asset('storage/'.$client->photo) }}" class="w-full h-full object-cover">
                                @else
                                <div class="w-full h-full {{ $client->client_mode === 'frecuente' ? 'bg-violet-100 text-violet-600' : 'bg-blue-100 text-blue-600' }} flex items-center justify-center text-sm font-bold">
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
                    <td class="px-4 py-3 text-center">
                        <span class="px-2 py-0.5 rounded-full text-xs font-semibold {{ $modeColor }}">{{ $client->client_mode_label }}</span>
                    </td>
                    <td class="px-4 py-3 hidden sm:table-cell">
                        <p class="text-gray-700">{{ $client->district ?? '—' }}</p>
                        <p class="text-xs text-gray-400">{{ $client->department }}</p>
                    </td>
                    <td class="px-4 py-3 hidden md:table-cell">
                        <p class="text-gray-700">{{ $client->phone ?? '—' }}</p>
                        <p class="text-xs text-gray-400 truncate max-w-[140px]">{{ $client->email ?? '' }}</p>
                    </td><td class="px-4 py-3 hidden lg:table-cell">
    <p class="text-gray-700">{{ $client->created_at?->format('d/m/Y') }}</p>
</td>

                    <td class="px-4 py-3 text-center">
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $tc }}">{{ $client->client_type_label }}</span>
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