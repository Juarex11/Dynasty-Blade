@extends('layouts.app')

@section('title', 'Aperturas de Curso | Dynasty')

@section('content')

<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Aperturas de Curso</h1>
        <p class="text-sm text-gray-500 mt-0.5">Ediciones y grupos activos</p>
    </div>
    <a href="{{ route('course-openings.create') }}"
       class="inline-flex items-center gap-2 bg-gradient-to-r from-violet-500 to-purple-600 hover:from-violet-600 hover:to-purple-700 text-white font-semibold px-4 py-2.5 rounded-xl shadow-lg shadow-violet-500/30 transition-all text-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Nueva Apertura
    </a>
</div>

@if(session('success'))
<div class="mb-5 bg-green-50 border border-green-200 rounded-xl p-4 flex items-center gap-3">
    <svg class="w-5 h-5 text-green-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
    <p class="text-sm font-medium text-green-700">{{ session('success') }}</p>
</div>
@endif

{{-- Filtros --}}
<form method="GET" class="mb-6 bg-white border border-gray-200 rounded-2xl p-4">
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
        <div class="col-span-2 sm:col-span-1 relative">
            <svg class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z"/></svg>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Buscar apertura..."
                class="w-full pl-9 pr-4 py-2 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-violet-300">
        </div>
        <select name="status" class="px-3 py-2 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-violet-300 bg-white">
            <option value="">Todos los estados</option>
            @foreach(['borrador'=>'Borrador','publicado'=>'Publicado','en_curso'=>'En curso','finalizado'=>'Finalizado','cancelado'=>'Cancelado'] as $v=>$l)
            <option value="{{ $v }}" {{ request('status') === $v ? 'selected' : '' }}>{{ $l }}</option>
            @endforeach
        </select>
        <select name="course" class="px-3 py-2 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-violet-300 bg-white">
            <option value="">Todos los cursos</option>
            @foreach($courses as $c)
            <option value="{{ $c->id }}" {{ request('course') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
            @endforeach
        </select>
        <div class="flex gap-2">
            <select name="branch" class="flex-1 px-3 py-2 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-violet-300 bg-white">
                <option value="">Todas las sedes</option>
                @foreach($branches as $b)
                <option value="{{ $b->id }}" {{ request('branch') == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
                @endforeach
            </select>
            <button type="submit" class="px-4 py-2 bg-violet-600 hover:bg-violet-700 text-white rounded-xl text-sm font-medium transition-colors">Filtrar</button>
        </div>
    </div>
</form>

@if($openings->isEmpty())
<div class="bg-white rounded-2xl border border-gray-200 p-14 text-center">
    <div class="w-14 h-14 bg-violet-50 rounded-2xl flex items-center justify-center mx-auto mb-4">
        <svg class="w-7 h-7 text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
    </div>
    <p class="text-gray-500 font-medium">No hay aperturas registradas</p>
    <a href="{{ route('course-openings.create') }}" class="text-violet-600 hover:underline text-sm mt-1 inline-block">Crear primera apertura</a>
</div>
@else
<div class="space-y-3">
    @foreach($openings as $opening)
    @php
        $sc = $opening->status_color;
        $statusBg = ['gray'=>'bg-gray-100 text-gray-500','blue'=>'bg-blue-100 text-blue-600','amber'=>'bg-amber-100 text-amber-600','green'=>'bg-green-100 text-green-600','red'=>'bg-red-100 text-red-500'];
        $sb = $statusBg[$sc] ?? 'bg-gray-100 text-gray-500';
        $max = $opening->max_students ?? $opening->course->max_students;
        $pct = $max && $max > 0 ? min(100, round($opening->enrollments_count / $max * 100)) : null;
    @endphp
    <div class="bg-white rounded-2xl border border-gray-200 p-4 hover:border-violet-200 transition-colors">
        <div class="flex flex-col sm:flex-row sm:items-center gap-3">
            {{-- Info principal --}}
            <div class="flex-1 min-w-0">
                <div class="flex items-center gap-2 mb-1">
                    <span class="px-2 py-0.5 rounded-full text-xs font-semibold {{ $sb }}">{{ $opening->status_label }}</span>
                    @if($opening->code)
                    <span class="text-xs text-gray-400 font-mono">{{ $opening->code }}</span>
                    @endif
                    @if($opening->promo_label && $opening->promo_until?->isFuture())
                    <span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-pink-100 text-pink-600">🏷 {{ $opening->promo_label }}</span>
                    @endif
                </div>
                <h3 class="font-bold text-gray-900 truncate">{{ $opening->display_name }}</h3>
                <p class="text-xs text-gray-500 mt-0.5">
                    {{ $opening->course->name }}
                    @if($opening->branch) · {{ $opening->branch->name }} @endif
                </p>
            </div>

            {{-- Fechas y horario --}}
            <div class="hidden md:block text-center shrink-0 min-w-[120px]">
                <p class="text-sm font-semibold text-gray-800">{{ $opening->start_date->format('d/m/Y') }}</p>
                @if($opening->end_date)<p class="text-xs text-gray-400">hasta {{ $opening->end_date->format('d/m/Y') }}</p>@endif
                @if($opening->time_start)<p class="text-xs text-violet-600 mt-0.5">{{ substr($opening->time_start,0,5) }}{{ $opening->time_end ? ' – '.substr($opening->time_end,0,5) : '' }}</p>@endif
                @if($opening->days_label !== '—')<p class="text-xs text-gray-400">{{ $opening->days_label }}</p>@endif
            </div>

            {{-- Ocupación --}}
            <div class="hidden lg:block shrink-0 min-w-[100px]">
                @if($pct !== null)
                <div class="text-center">
                    <p class="text-sm font-bold {{ $pct >= 90 ? 'text-red-500' : ($pct >= 70 ? 'text-amber-500' : 'text-green-600') }}">
                        {{ $opening->enrollments_count }} / {{ $max }}
                    </p>
                    <div class="mt-1 h-1.5 bg-gray-100 rounded-full overflow-hidden">
                        <div class="h-full rounded-full {{ $pct >= 90 ? 'bg-red-400' : ($pct >= 70 ? 'bg-amber-400' : 'bg-green-400') }}"
                             style="width: {{ $pct }}%"></div>
                    </div>
                    <p class="text-xs text-gray-400 mt-0.5">{{ $pct }}% lleno</p>
                </div>
                @else
                <p class="text-sm text-gray-500 text-center">{{ $opening->enrollments_count }} inscritos</p>
                @endif
            </div>

            {{-- Precio --}}
            <div class="hidden sm:block shrink-0 text-right min-w-[80px]">
                @if($opening->effective_price !== null)
                <p class="text-sm font-bold text-violet-600">S/. {{ number_format($opening->effective_price,2) }}</p>
                @if($opening->price_promo && $opening->promo_until?->isFuture())
                <p class="text-xs text-gray-400 line-through">S/. {{ number_format($opening->price,2) }}</p>
                @endif
                @else
                <p class="text-xs text-gray-400">Sin precio</p>
                @endif
            </div>

            {{-- Acciones --}}
            <div class="flex gap-2 shrink-0">
                <a href="{{ route('course-openings.show', $opening) }}" class="text-violet-600 hover:underline text-xs font-medium">Ver</a>
                <a href="{{ route('course-openings.edit', $opening) }}" class="text-gray-400 hover:text-gray-600 text-xs">Editar</a>
            </div>
        </div>
    </div>
    @endforeach
</div>

@if($openings->hasPages())
<div class="mt-4">{{ $openings->withQueryString()->links() }}</div>
@endif
@endif

@endsection