{{-- resources/views/appointments/_calendar.blade.php --}}
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 flex flex-col h-full overflow-hidden">

    {{-- Header --}}
    <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 flex-shrink-0">
        <div class="flex items-center gap-2">
            <a href="{{ route('appointments.index', ['month' => $prevMonth->month, 'year' => $prevMonth->year]) }}"
               class="w-9 h-9 flex items-center justify-center rounded-xl hover:bg-gray-100 transition-colors">
                <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>

            <h2 class="text-lg font-bold text-gray-900 capitalize min-w-[170px] text-center">
                {{ $startOfMonth->translatedFormat('F Y') }}
            </h2>

            <a href="{{ route('appointments.index', ['month' => $nextMonth->month, 'year' => $nextMonth->year]) }}"
               class="w-9 h-9 flex items-center justify-center rounded-xl hover:bg-gray-100 transition-colors">
                <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </a>

            <a href="{{ route('appointments.index', ['month' => now()->month, 'year' => now()->year]) }}"
               class="ml-1 px-3 py-1.5 text-xs font-semibold text-fuchsia-600 bg-fuchsia-50 hover:bg-fuchsia-100 rounded-lg transition-colors">
                Hoy
            </a>
        </div>

        <button onclick="openModal()"
            class="flex items-center gap-2 px-4 py-2.5 bg-gradient-to-r from-fuchsia-500 to-pink-600 text-white text-sm font-semibold rounded-xl hover:from-fuchsia-600 hover:to-pink-700 transition-all shadow-lg shadow-fuchsia-500/30">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            <span class="hidden sm:inline">Nueva Cita</span>
        </button>
    </div>

    {{-- Day headers --}}
    <div class="grid grid-cols-7 border-b border-gray-100 bg-gray-50/60 flex-shrink-0">
        @foreach(['Lun','Mar','Mié','Jue','Vie','Sáb','Dom'] as $d)
            <div class="py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ $d }}</div>
        @endforeach
    </div>

    {{-- Grid --}}
    @php
        $firstDay    = $startOfMonth->dayOfWeek;
        $firstDay    = $firstDay === 0 ? 6 : $firstDay - 1;
        $daysInMonth = $startOfMonth->daysInMonth;
        $today       = now()->format('Y-m-d');
    @endphp

    <div class="grid grid-cols-7 divide-x divide-y divide-gray-100 flex-1 overflow-auto">

        {{-- Empty leading cells --}}
        @for($i = 0; $i < $firstDay; $i++)
            <div class="bg-gray-50/40 min-h-[90px]"></div>
        @endfor

        {{-- Day cells --}}
        @for($day = 1; $day <= $daysInMonth; $day++)
            @php
                $date    = \Carbon\Carbon::createFromDate($year, $month, $day)->format('Y-m-d');
                $isToday = $date === $today;
                $dayAppts = $appointments[$date] ?? collect();
            @endphp

            <div
                class="min-h-[90px] p-1.5 transition-colors cursor-pointer group
                    {{ $isToday ? 'bg-fuchsia-50/30' : 'hover:bg-gray-50' }}"
                onclick="openModalWithDate('{{ $date }}')"
            >
                {{-- Day number --}}
                <div class="flex items-center justify-between mb-1 px-0.5">
                    <span class="text-sm font-semibold w-7 h-7 flex items-center justify-center rounded-full transition-all
                        {{ $isToday
                            ? 'bg-gradient-to-br from-fuchsia-500 to-pink-600 text-white shadow-md shadow-fuchsia-500/40'
                            : 'text-gray-600 group-hover:bg-fuchsia-100 group-hover:text-fuchsia-700' }}">
                        {{ $day }}
                    </span>
                    @if($dayAppts->count() > 0)
                        <span class="text-[10px] font-bold text-fuchsia-400">{{ $dayAppts->count() }}</span>
                    @endif
                </div>

                {{-- Appointments --}}
                <div class="space-y-0.5">
                    @foreach($dayAppts->take(3) as $appt)
                        <button
                            onclick="event.stopPropagation();
                                selectAppointment(
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
                            class="w-full text-left px-2 py-0.5 rounded-md text-xs font-medium text-white truncate
                                hover:brightness-110 hover:scale-[1.01] transition-all block"
                            style="background-color: {{ $appt->color }}"
                        >
                            {{ substr($appt->start_time,0,5) }} {{ $appt->client_name }}
                        </button>
                    @endforeach

                    @if($dayAppts->count() > 3)
                        <p class="text-[10px] text-gray-400 pl-1">+{{ $dayAppts->count() - 3 }} más</p>
                    @endif
                </div>
            </div>
        @endfor

        {{-- Empty trailing cells --}}
        @php
            $lastDay   = \Carbon\Carbon::createFromDate($year, $month, $daysInMonth)->dayOfWeek;
            $lastDay   = $lastDay === 0 ? 6 : $lastDay - 1;
            $remaining = 6 - $lastDay;
        @endphp
        @for($i = 0; $i < $remaining; $i++)
            <div class="bg-gray-50/40 min-h-[90px]"></div>
        @endfor

    </div>
</div>