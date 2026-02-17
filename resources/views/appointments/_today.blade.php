{{-- resources/views/appointments/_today.blade.php --}}
@php
    $todayAppts = $appointments[now()->format('Y-m-d')] ?? collect();

    $statusClasses = [
        'pending'   => 'bg-amber-100 text-amber-700',
        'confirmed' => 'bg-blue-100 text-blue-700',
        'completed' => 'bg-green-100 text-green-700',
        'cancelled' => 'bg-red-100 text-red-700',
    ];
    $statusLabels = [
        'pending'   => 'Pendiente',
        'confirmed' => 'Confirmada',
        'completed' => 'Completada',
        'cancelled' => 'Cancelada',
    ];
@endphp

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden flex-shrink-0">

    {{-- Header --}}
    <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
        <div>
            <h4 class="text-sm font-bold text-gray-900">Agenda de Hoy</h4>
            <p class="text-xs text-gray-400 mt-0.5">{{ now()->translatedFormat('d \d\e F') }}</p>
        </div>
        <span class="text-xs font-bold px-2.5 py-1 rounded-full
            {{ $todayAppts->count() > 0 ? 'bg-fuchsia-100 text-fuchsia-700' : 'bg-gray-100 text-gray-500' }}">
            {{ $todayAppts->count() }} {{ $todayAppts->count() === 1 ? 'cita' : 'citas' }}
        </span>
    </div>

    {{-- List --}}
    <div class="overflow-y-auto" style="max-height: 320px;">
        @if($todayAppts->isEmpty())
            <div class="p-6 flex flex-col items-center text-center">
                <div class="w-12 h-12 bg-gray-50 rounded-xl flex items-center justify-center mb-3">
                    <svg class="w-6 h-6 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                <p class="text-xs text-gray-500 font-medium">Sin citas para hoy</p>
                <button onclick="openModalWithDate('{{ now()->format('Y-m-d') }}')"
                    class="mt-2 text-xs text-fuchsia-600 hover:text-fuchsia-700 font-semibold">
                    + Agregar una
                </button>
            </div>
        @else
            <div class="divide-y divide-gray-50">
                @foreach($todayAppts as $appt)
                    <button
                        onclick="selectAppointment(
                            {{ $appt->id }},
                            '{{ addslashes($appt->client_name) }}',
                            '{{ addslashes($appt->service) }}',
                            '{{ addslashes($appt->stylist ?? '') }}',
                            '{{ $appt->date->format('Y-m-d') }}',
                            '{{ substr($appt->start_time,0,5) }}',
                            '{{ substr($appt->end_time,0,5) }}',
                            '{{ $appt->status }}',
                            '{{ addslashes($appt->notes ?? '') }}',
                            '{{ $appt->color }}',
                            '{{ addslashes($appt->client_phone ?? '') }}',
                            '{{ addslashes($appt->client_email ?? '') }}'
                        )"
                        class="w-full text-left px-4 py-3 hover:bg-gray-50 transition-colors flex items-center gap-3 group"
                    >
                        {{-- Color bar --}}
                        <div class="w-1 h-12 rounded-full flex-shrink-0" style="background-color: {{ $appt->color }}"></div>

                        {{-- Time block --}}
                        <div class="flex-shrink-0 text-center w-12">
                            <p class="text-xs font-bold text-gray-800">{{ substr($appt->start_time,0,5) }}</p>
                            <div class="w-px h-3 bg-gray-300 mx-auto my-0.5"></div>
                            <p class="text-xs text-gray-400">{{ substr($appt->end_time,0,5) }}</p>
                        </div>

                        {{-- Info --}}
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between gap-1 mb-0.5">
                                <p class="text-sm font-bold text-gray-800 truncate">{{ $appt->client_name }}</p>
                                <span class="text-[10px] font-bold px-2 py-0.5 rounded-full flex-shrink-0
                                    {{ $statusClasses[$appt->status] ?? 'bg-gray-100 text-gray-600' }}">
                                    {{ $statusLabels[$appt->status] ?? '' }}
                                </span>
                            </div>
                            <p class="text-xs text-gray-500 truncate">{{ $appt->service }}</p>
                            @if($appt->stylist)
                                <p class="text-xs text-gray-400 mt-0.5 flex items-center gap-1">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                    {{ $appt->stylist }}
                                </p>
                            @endif
                        </div>
                    </button>
                @endforeach
            </div>
        @endif
    </div>
</div>