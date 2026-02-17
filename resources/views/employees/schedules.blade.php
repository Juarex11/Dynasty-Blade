@extends('layouts.app')

@section('title', 'Horarios - ' . $employee->full_name . ' | Dynasty')

@section('content')

<div class="max-w-3xl mx-auto">

    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('employees.show', $employee) }}" class="w-9 h-9 flex items-center justify-center rounded-xl border border-gray-200 hover:border-fuchsia-300 hover:bg-fuchsia-50 text-gray-500 hover:text-fuchsia-600 transition-all">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Horarios</h1>
            <p class="text-sm text-gray-500">{{ $employee->full_name }} · {{ $employee->position }}</p>
        </div>
    </div>

    @if(session('success'))
    <div class="mb-5 flex items-center gap-3 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl text-sm">
        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
        </svg>
        {{ session('success') }}
    </div>
    @endif

    @if($branches->isEmpty())
    <div class="bg-white rounded-2xl border border-gray-200 p-10 text-center">
        <p class="text-gray-500">Este empleado no tiene sedes asignadas.</p>
        <a href="{{ route('employees.edit', $employee) }}" class="mt-2 inline-block text-fuchsia-600 hover:text-fuchsia-700 font-medium text-sm">
            Asignar sedes →
        </a>
    </div>
    @else

    <form action="{{ route('employees.schedules.update', $employee) }}" method="POST">
        @csrf

        @php
        $days = \App\Models\EmployeeSchedule::DAY_NAMES;
        $existingSchedules = $employee->schedules->keyBy(fn($s) => $s->branch_id . '-' . $s->day_of_week);
        @endphp

        <div class="space-y-5">
            @foreach($branches as $branch)
            <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 bg-gradient-to-r from-fuchsia-50 to-pink-50 border-b border-gray-100">
                    <h3 class="font-semibold text-gray-900">{{ $branch->name }}</h3>
                    <p class="text-xs text-gray-400 mt-0.5">{{ $branch->full_address }}</p>
                </div>

                <div class="p-4 space-y-2">
                    @foreach($days as $dayNum => $dayName)
                    @php $schedule = $existingSchedules->get($branch->id . '-' . $dayNum); @endphp
                    <div class="day-row grid items-center gap-3 py-2 border-b border-gray-50 last:border-0"
                         style="grid-template-columns: 100px 60px 1fr;">

                        {{-- Nombre del día --}}
                        <span class="text-sm font-medium text-gray-700">{{ $dayName }}</span>

                        {{-- Toggle trabaja/libre --}}
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="hidden"
                                name="schedules[{{ $branch->id }}_{{ $dayNum }}][is_working]" value="0">
                            <input type="checkbox"
                                name="schedules[{{ $branch->id }}_{{ $dayNum }}][is_working]"
                                value="1"
                                {{ ($schedule && $schedule->is_working) ? 'checked' : '' }}
                                class="sr-only peer schedule-toggle"
                                data-target="hours-{{ $branch->id }}-{{ $dayNum }}">
                            <input type="hidden" name="schedules[{{ $branch->id }}_{{ $dayNum }}][branch_id]" value="{{ $branch->id }}">
                            <input type="hidden" name="schedules[{{ $branch->id }}_{{ $dayNum }}][day_of_week]" value="{{ $dayNum }}">
                            <div class="w-10 h-5 bg-gray-200 rounded-full peer peer-checked:after:translate-x-full after:absolute after:top-[1px] after:left-[1px] after:bg-white after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-gradient-to-r peer-checked:from-fuchsia-500 peer-checked:to-pink-600"></div>
                        </label>

                        {{-- Horas --}}
                        <div id="hours-{{ $branch->id }}-{{ $dayNum }}"
                             class="{{ ($schedule && $schedule->is_working) ? '' : 'opacity-40 pointer-events-none' }} flex items-center gap-2">
                            <input type="time"
                                name="schedules[{{ $branch->id }}_{{ $dayNum }}][start_time]"
                                value="{{ $schedule?->start_time ?? '09:00' }}"
                                class="px-3 py-1.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-fuchsia-200 w-28">
                            <span class="text-gray-400 text-sm">–</span>
                            <input type="time"
                                name="schedules[{{ $branch->id }}_{{ $dayNum }}][end_time]"
                                value="{{ $schedule?->end_time ?? '18:00' }}"
                                class="px-3 py-1.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-fuchsia-200 w-28">
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endforeach
        </div>

        <div class="flex gap-3 mt-5 pb-6">
            <a href="{{ route('employees.show', $employee) }}"
               class="flex-1 text-center py-3 border border-gray-200 text-gray-600 font-semibold rounded-xl hover:bg-gray-50 transition-colors">
                Cancelar
            </a>
            <button type="submit"
                class="flex-1 bg-gradient-to-r from-fuchsia-500 to-pink-600 hover:from-fuchsia-600 hover:to-pink-700 text-white font-semibold py-3 rounded-xl shadow-lg shadow-fuchsia-500/30 transition-all duration-200">
                Guardar Horarios
            </button>
        </div>
    </form>
    @endif
</div>

<script>
document.querySelectorAll('.schedule-toggle').forEach(toggle => {
    toggle.addEventListener('change', function () {
        const target = document.getElementById(this.dataset.target);
        if (target) {
            target.classList.toggle('opacity-40', !this.checked);
            target.classList.toggle('pointer-events-none', !this.checked);
        }
    });
});
</script>

@endsection