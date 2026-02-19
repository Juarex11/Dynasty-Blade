@extends('layouts.app')

@section('title', $courseOpening->display_name . ' | Dynasty')

@section('content')

<div class="flex items-start gap-3 mb-6">
    <a href="{{ route('course-openings.index') }}"
       class="w-9 h-9 flex items-center justify-center rounded-xl border border-gray-200 hover:border-violet-300 hover:bg-violet-50 text-gray-500 hover:text-violet-600 transition-all shrink-0 mt-0.5">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
    </a>
    <div class="flex-1 min-w-0">
        @php
        $sc = $courseOpening->status_color;
        $statusBg = ['gray'=>'bg-gray-100 text-gray-500','blue'=>'bg-blue-100 text-blue-600','amber'=>'bg-amber-100 text-amber-600','green'=>'bg-green-100 text-green-600','red'=>'bg-red-100 text-red-500'];
        $sb = $statusBg[$sc] ?? 'bg-gray-100 text-gray-500';
        @endphp
        <div class="flex flex-wrap items-center gap-2 mb-1">
            <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $sb }}">{{ $courseOpening->status_label }}</span>
            @if($courseOpening->promo_label && $courseOpening->promo_until?->isFuture())
            <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-pink-100 text-pink-600">🏷 {{ $courseOpening->promo_label }}</span>
            @endif
            @if($courseOpening->code)
            <span class="text-xs text-gray-400 font-mono">{{ $courseOpening->code }}</span>
            @endif
        </div>
        <h1 class="text-2xl font-bold text-gray-900 truncate">{{ $courseOpening->display_name }}</h1>
        <p class="text-sm text-gray-500 mt-0.5">{{ $courseOpening->course->name }}</p>
    </div>
    <a href="{{ route('course-openings.edit', $courseOpening) }}"
       class="shrink-0 inline-flex items-center gap-2 bg-gradient-to-r from-violet-500 to-purple-600 hover:from-violet-600 hover:to-purple-700 text-white font-semibold px-4 py-2 rounded-xl shadow-lg shadow-violet-500/30 transition-all text-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
        Editar
    </a>
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

        {{-- Resumen de la apertura --}}
        <div class="bg-white rounded-2xl border border-gray-200 p-6">
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-center">
                @php
                $max = $courseOpening->max_students ?? $courseOpening->course->max_students;
                $enrolled = $courseOpening->enrollments->count();
                $pct = $max && $max > 0 ? min(100, round($enrolled / $max * 100)) : null;
                @endphp
                <div class="p-3 bg-violet-50 rounded-xl">
                    <p class="text-2xl font-bold text-violet-600">{{ $enrolled }}</p>
                    <p class="text-xs text-gray-500 mt-0.5">Inscritos{{ $max ? ' / '.$max : '' }}</p>
                    @if($pct !== null)
                    <div class="mt-1.5 h-1.5 bg-violet-100 rounded-full overflow-hidden">
                        <div class="h-full bg-violet-500 rounded-full" style="width:{{ $pct }}%"></div>
                    </div>
                    @endif
                </div>
                <div class="p-3 bg-blue-50 rounded-xl">
                    <p class="text-2xl font-bold text-blue-600">{{ $courseOpening->sessions->count() }}</p>
                    <p class="text-xs text-gray-500 mt-0.5">Sesiones</p>
                </div>
                <div class="p-3 bg-green-50 rounded-xl">
                    <p class="text-2xl font-bold text-green-600">{{ $courseOpening->sessions->where('status','realizada')->count() }}</p>
                    <p class="text-xs text-gray-500 mt-0.5">Realizadas</p>
                </div>
                <div class="p-3 bg-amber-50 rounded-xl">
                    <p class="text-2xl font-bold text-amber-600">{{ $attendanceRate !== null ? $attendanceRate.'%' : '—' }}</p>
                    <p class="text-xs text-gray-500 mt-0.5">Asistencia</p>
                </div>
            </div>
        </div>

        {{-- Sesiones y asistencia --}}
        <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden">
            <div class="flex items-center justify-between p-5 border-b border-gray-100">
                <h2 class="font-semibold text-gray-900 flex items-center gap-2">
                    <svg class="w-4 h-4 text-violet-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    Sesiones
                </h2>
                <span class="text-xs text-gray-400">{{ $courseOpening->total_sessions }} programadas</span>
            </div>

            @if($courseOpening->sessions->isEmpty())
            <div class="p-8 text-center text-gray-400 text-sm italic">Sin sesiones generadas aún.</div>
            @else
            <div class="divide-y divide-gray-50">
                @foreach($courseOpening->sessions as $session)
                @php
                $stBg = ['programada'=>'bg-gray-100 text-gray-500','realizada'=>'bg-green-100 text-green-600','cancelada'=>'bg-red-100 text-red-500','postergada'=>'bg-amber-100 text-amber-600'];
                $ssb = $stBg[$session->status] ?? 'bg-gray-100 text-gray-500';
                $presents = $session->attendances->whereIn('status',['presente','tardanza'])->count();
                $total    = $session->attendances->count();
                @endphp
                <div class="p-4">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-violet-100 flex items-center justify-center text-violet-600 text-xs font-bold shrink-0">
                            {{ $session->session_number }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="font-medium text-sm text-gray-800">{{ $session->date->format('d/m/Y') }}</span>
                                @if($session->time_start)
                                <span class="text-xs text-gray-400">{{ substr($session->time_start,0,5) }}{{ $session->time_end ? ' – '.substr($session->time_end,0,5) : '' }}</span>
                                @endif
                                <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $ssb }}">{{ $session->status_label }}</span>
                            </div>
                            @if($session->topic)
                            <p class="text-xs text-gray-500 mt-0.5">📋 {{ $session->topic }}</p>
                            @endif
                        </div>
                        <div class="text-right shrink-0">
                            @if($total > 0)
                            <p class="text-sm font-semibold {{ $presents === $total ? 'text-green-600' : ($presents === 0 ? 'text-red-500' : 'text-amber-600') }}">
                                {{ $presents }}/{{ $total }}
                            </p>
                            <p class="text-xs text-gray-400">presentes</p>
                            @endif
                        </div>
                        @if($session->status === 'programada' || $session->status === 'realizada')
                        <a href="#session-{{ $session->id }}"
                           onclick="toggleSession({{ $session->id }})"
                           class="shrink-0 text-xs text-violet-600 hover:underline font-medium">
                            Asistencia
                        </a>
                        @endif
                    </div>

                    {{-- Panel de asistencia --}}
                    <div id="session-{{ $session->id }}" class="hidden mt-4">
                        <form action="{{ route('course-openings.attendance', $session) }}" method="POST">
                            @csrf
                            <div class="grid grid-cols-2 gap-2 mb-3">
                                <div>
                                    <label class="block text-xs text-gray-500 mb-1">Tema de la sesión</label>
                                    <input type="text" name="topic" value="{{ $session->topic }}"
                                        class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-violet-300"
                                        placeholder="Tema tratado">
                                </div>
                                <div>
                                    <label class="block text-xs text-gray-500 mb-1">Estado de la sesión</label>
                                    <select name="session_status" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-violet-300 bg-white">
                                        @foreach(['programada'=>'Programada','realizada'=>'Realizada','cancelada'=>'Cancelada','postergada'=>'Postergada'] as $v=>$l)
                                        <option value="{{ $v }}" {{ $session->status === $v ? 'selected' : '' }}>{{ $l }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="border border-gray-100 rounded-xl overflow-hidden">
                                <table class="w-full text-xs">
                                    <thead>
                                        <tr class="bg-gray-50">
                                            <th class="text-left px-3 py-2 font-medium text-gray-500">Estudiante</th>
                                            <th class="text-center px-2 py-2 font-medium text-gray-500">Asistencia</th>
                                            <th class="text-left px-2 py-2 font-medium text-gray-500">Observación</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-50">
                                        @foreach($courseOpening->enrollments as $i => $enrollment)
                                        @php
                                        $att = $session->attendances->firstWhere('course_opening_student_id', $enrollment->id);
                                        @endphp
                                        <tr>
                                            <input type="hidden" name="attendance[{{ $i }}][id]" value="{{ $enrollment->id }}">
                                            <td class="px-3 py-2 font-medium text-gray-700">{{ $enrollment->person_name }}</td>
                                            <td class="px-2 py-2">
                                                <select name="attendance[{{ $i }}][status]"
                                                    class="w-full px-2 py-1 border border-gray-200 rounded-lg text-xs bg-white focus:ring-1 focus:ring-violet-300">
                                                    @foreach(['presente'=>'✅ Presente','tardanza'=>'⏰ Tardanza','ausente'=>'❌ Ausente','justificado'=>'📋 Justificado'] as $v=>$l)
                                                    <option value="{{ $v }}" {{ ($att?->status ?? 'presente') === $v ? 'selected' : '' }}>{{ $l }}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td class="px-2 py-2">
                                                <input type="text" name="attendance[{{ $i }}][observation]" value="{{ $att?->observation }}"
                                                    class="w-full px-2 py-1 border border-gray-200 rounded-lg text-xs focus:ring-1 focus:ring-violet-300"
                                                    placeholder="Opcional">
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="flex justify-end mt-2">
                                <button type="submit" class="px-4 py-2 bg-violet-600 hover:bg-violet-700 text-white text-xs font-semibold rounded-lg transition-colors">
                                    Guardar asistencia
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                @endforeach
            </div>
            @endif
        </div>

        {{-- Estudiantes inscritos --}}
        <div class="bg-white rounded-2xl border border-gray-200 p-6">
            <h2 class="font-semibold text-gray-900 mb-4 flex items-center gap-2">
                <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                Estudiantes inscritos
                <span class="ml-auto text-xs font-normal text-gray-400">{{ $courseOpening->enrollments->count() }}</span>
            </h2>
            @if($courseOpening->enrollments->isEmpty())
            <p class="text-sm text-gray-400 italic">Sin estudiantes inscritos.</p>
            @else
            <div class="space-y-2">
                @foreach($courseOpening->enrollments as $enrollment)
                @php
                $stColors = ['inscrito'=>'bg-blue-100 text-blue-700','en_curso'=>'bg-amber-100 text-amber-700','completado'=>'bg-green-100 text-green-700','abandonado'=>'bg-red-100 text-red-500','retirado'=>'bg-gray-100 text-gray-500'];
                $sc2 = $stColors[$enrollment->status] ?? 'bg-gray-100 text-gray-500';
                $payColors = ['pagado'=>'text-green-600','pendiente'=>'text-amber-600','parcial'=>'text-orange-500','becado'=>'text-violet-600'];
                $pc = $payColors[$enrollment->payment_status] ?? 'text-gray-500';
                @endphp
                <div class="flex items-center gap-3 p-3 border border-gray-100 rounded-xl">
                    <div class="w-8 h-8 rounded-full overflow-hidden shrink-0">
                        @php $person = $enrollment->employee ?? $enrollment->client; @endphp
                        @if($person?->photo)
                        <img src="{{ asset('storage/'.$person->photo) }}" class="w-full h-full object-cover">
                        @else
                        <div class="w-full h-full {{ $enrollment->person_type === 'employee' ? 'bg-violet-100 text-violet-600' : 'bg-blue-100 text-blue-600' }} flex items-center justify-center text-xs font-bold">
                            {{ strtoupper(substr($enrollment->person_name, 0, 1)) }}
                        </div>
                        @endif
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-800 truncate">{{ $enrollment->person_name }}</p>
                        <p class="text-xs text-gray-400">{{ $enrollment->person_type === 'employee' ? 'Empleado' : 'Cliente' }} · Inscrito {{ $enrollment->enrolled_at?->format('d/m/Y') }}</p>
                    </div>
                    <div class="text-right shrink-0">
                        <span class="px-2 py-0.5 rounded-full text-xs font-semibold {{ $sc2 }} block mb-1">{{ ucfirst(str_replace('_',' ',$enrollment->status)) }}</span>
                        @if($enrollment->price_paid !== null)
                        <p class="text-xs {{ $pc }} font-medium">S/. {{ number_format($enrollment->price_paid,2) }} · {{ ucfirst($enrollment->payment_status) }}</p>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
            @endif
        </div>
    </div>

    {{-- Sidebar --}}
    <div class="space-y-5">
        {{-- Info del curso --}}
        <div class="bg-white rounded-2xl border border-gray-200 p-5">
            <h2 class="font-semibold text-gray-900 mb-4 text-sm">Detalles</h2>
            <div class="space-y-2.5 text-sm">
                <div class="flex justify-between">
                    <span class="text-gray-500">Inicio</span>
                    <span class="font-medium text-gray-800">{{ $courseOpening->start_date->format('d/m/Y') }}</span>
                </div>
                @if($courseOpening->end_date)
                <div class="flex justify-between">
                    <span class="text-gray-500">Fin</span>
                    <span class="font-medium text-gray-800">{{ $courseOpening->end_date->format('d/m/Y') }}</span>
                </div>
                @endif
                @if($courseOpening->time_start)
                <div class="flex justify-between">
                    <span class="text-gray-500">Horario</span>
                    <span class="font-medium text-gray-800">{{ substr($courseOpening->time_start,0,5) }}{{ $courseOpening->time_end ? ' – '.substr($courseOpening->time_end,0,5) : '' }}</span>
                </div>
                @endif
                @if($courseOpening->days_label !== '—')
                <div class="flex justify-between">
                    <span class="text-gray-500">Días</span>
                    <span class="font-medium text-gray-800">{{ $courseOpening->days_label }}</span>
                </div>
                @endif
                @if($courseOpening->branch)
                <div class="flex justify-between">
                    <span class="text-gray-500">Sede</span>
                    <span class="font-medium text-gray-800">{{ $courseOpening->branch->name }}</span>
                </div>
                @endif
                @if($courseOpening->effective_price !== null)
                <div class="flex justify-between">
                    <span class="text-gray-500">Precio</span>
                    <span class="font-bold text-violet-600">S/. {{ number_format($courseOpening->effective_price,2) }}</span>
                </div>
                @endif
            </div>
        </div>

        {{-- Instructores --}}
        <div class="bg-white rounded-2xl border border-gray-200 p-5">
            <h2 class="font-semibold text-gray-900 mb-3 text-sm">Instructores</h2>
            @forelse($courseOpening->instructors as $emp)
            <div class="flex items-center gap-2 mb-2">
                <div class="w-7 h-7 rounded-full overflow-hidden shrink-0">
                    @if($emp->photo)
                    <img src="{{ asset('storage/'.$emp->photo) }}" class="w-full h-full object-cover">
                    @else
                    <div class="w-full h-full bg-violet-100 flex items-center justify-center text-violet-600 text-xs font-bold">{{ strtoupper(substr($emp->first_name,0,1)) }}</div>
                    @endif
                </div>
                <div class="min-w-0">
                    <p class="text-sm font-medium text-gray-800 truncate">{{ $emp->full_name }}</p>
                    <p class="text-xs text-gray-400">{{ $emp->position }}</p>
                </div>
            </div>
            @empty
            <p class="text-xs text-gray-400 italic">Sin instructores asignados.</p>
            @endforelse
        </div>

        {{-- Acciones --}}
        <div class="bg-white rounded-2xl border border-gray-200 p-5 space-y-2">
            <a href="{{ route('course-openings.edit', $courseOpening) }}"
               class="w-full flex items-center justify-center gap-2 py-2.5 border border-violet-200 text-violet-600 hover:bg-violet-50 font-medium rounded-xl transition-colors text-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                Editar apertura
            </a>
            <form action="{{ route('course-openings.destroy', $courseOpening) }}" method="POST"
                  onsubmit="return confirm('¿Eliminar esta apertura? Se perderán sesiones y asistencias.')">
                @csrf @method('DELETE')
                <button type="submit" class="w-full flex items-center justify-center gap-2 py-2.5 border border-red-200 text-red-500 hover:bg-red-50 font-medium rounded-xl transition-colors text-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    Eliminar apertura
                </button>
            </form>
        </div>
    </div>
</div>

<script>
function toggleSession(id) {
    const el = document.getElementById('session-' + id);
    el.classList.toggle('hidden');
}
</script>

@endsection@extends('layouts.app')

@section('title', $courseOpening->display_name . ' | Dynasty')

@section('content')

<div class="flex items-start gap-3 mb-6">
    <a href="{{ route('course-openings.index') }}"
       class="w-9 h-9 flex items-center justify-center rounded-xl border border-gray-200 hover:border-violet-300 hover:bg-violet-50 text-gray-500 hover:text-violet-600 transition-all shrink-0 mt-0.5">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
    </a>
    <div class="flex-1 min-w-0">
        @php
        $sc = $courseOpening->status_color;
        $statusBg = ['gray'=>'bg-gray-100 text-gray-500','blue'=>'bg-blue-100 text-blue-600','amber'=>'bg-amber-100 text-amber-600','green'=>'bg-green-100 text-green-600','red'=>'bg-red-100 text-red-500'];
        $sb = $statusBg[$sc] ?? 'bg-gray-100 text-gray-500';
        @endphp
        <div class="flex flex-wrap items-center gap-2 mb-1">
            <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $sb }}">{{ $courseOpening->status_label }}</span>
            @if($courseOpening->promo_label && $courseOpening->promo_until?->isFuture())
            <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-pink-100 text-pink-600">🏷 {{ $courseOpening->promo_label }}</span>
            @endif
            @if($courseOpening->code)
            <span class="text-xs text-gray-400 font-mono">{{ $courseOpening->code }}</span>
            @endif
        </div>
        <h1 class="text-2xl font-bold text-gray-900 truncate">{{ $courseOpening->display_name }}</h1>
        <p class="text-sm text-gray-500 mt-0.5">{{ $courseOpening->course->name }}</p>
    </div>
    <a href="{{ route('course-openings.edit', $courseOpening) }}"
       class="shrink-0 inline-flex items-center gap-2 bg-gradient-to-r from-violet-500 to-purple-600 hover:from-violet-600 hover:to-purple-700 text-white font-semibold px-4 py-2 rounded-xl shadow-lg shadow-violet-500/30 transition-all text-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
        Editar
    </a>
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

        {{-- Resumen de la apertura --}}
        <div class="bg-white rounded-2xl border border-gray-200 p-6">
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-center">
                @php
                $max = $courseOpening->max_students ?? $courseOpening->course->max_students;
                $enrolled = $courseOpening->enrollments->count();
                $pct = $max && $max > 0 ? min(100, round($enrolled / $max * 100)) : null;
                @endphp
                <div class="p-3 bg-violet-50 rounded-xl">
                    <p class="text-2xl font-bold text-violet-600">{{ $enrolled }}</p>
                    <p class="text-xs text-gray-500 mt-0.5">Inscritos{{ $max ? ' / '.$max : '' }}</p>
                    @if($pct !== null)
                    <div class="mt-1.5 h-1.5 bg-violet-100 rounded-full overflow-hidden">
                        <div class="h-full bg-violet-500 rounded-full" style="width:{{ $pct }}%"></div>
                    </div>
                    @endif
                </div>
                <div class="p-3 bg-blue-50 rounded-xl">
                    <p class="text-2xl font-bold text-blue-600">{{ $courseOpening->sessions->count() }}</p>
                    <p class="text-xs text-gray-500 mt-0.5">Sesiones</p>
                </div>
                <div class="p-3 bg-green-50 rounded-xl">
                    <p class="text-2xl font-bold text-green-600">{{ $courseOpening->sessions->where('status','realizada')->count() }}</p>
                    <p class="text-xs text-gray-500 mt-0.5">Realizadas</p>
                </div>
                <div class="p-3 bg-amber-50 rounded-xl">
                    <p class="text-2xl font-bold text-amber-600">{{ $attendanceRate !== null ? $attendanceRate.'%' : '—' }}</p>
                    <p class="text-xs text-gray-500 mt-0.5">Asistencia</p>
                </div>
            </div>
        </div>

        {{-- Sesiones y asistencia --}}
        <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden">
            <div class="flex items-center justify-between p-5 border-b border-gray-100">
                <h2 class="font-semibold text-gray-900 flex items-center gap-2">
                    <svg class="w-4 h-4 text-violet-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    Sesiones
                </h2>
                <span class="text-xs text-gray-400">{{ $courseOpening->total_sessions }} programadas</span>
            </div>

            @if($courseOpening->sessions->isEmpty())
            <div class="p-8 text-center text-gray-400 text-sm italic">Sin sesiones generadas aún.</div>
            @else
            <div class="divide-y divide-gray-50">
                @foreach($courseOpening->sessions as $session)
                @php
                $stBg = ['programada'=>'bg-gray-100 text-gray-500','realizada'=>'bg-green-100 text-green-600','cancelada'=>'bg-red-100 text-red-500','postergada'=>'bg-amber-100 text-amber-600'];
                $ssb = $stBg[$session->status] ?? 'bg-gray-100 text-gray-500';
                $presents = $session->attendances->whereIn('status',['presente','tardanza'])->count();
                $total    = $session->attendances->count();
                @endphp
                <div class="p-4">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-violet-100 flex items-center justify-center text-violet-600 text-xs font-bold shrink-0">
                            {{ $session->session_number }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="font-medium text-sm text-gray-800">{{ $session->date->format('d/m/Y') }}</span>
                                @if($session->time_start)
                                <span class="text-xs text-gray-400">{{ substr($session->time_start,0,5) }}{{ $session->time_end ? ' – '.substr($session->time_end,0,5) : '' }}</span>
                                @endif
                                <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $ssb }}">{{ $session->status_label }}</span>
                            </div>
                            @if($session->topic)
                            <p class="text-xs text-gray-500 mt-0.5">📋 {{ $session->topic }}</p>
                            @endif
                        </div>
                        <div class="text-right shrink-0">
                            @if($total > 0)
                            <p class="text-sm font-semibold {{ $presents === $total ? 'text-green-600' : ($presents === 0 ? 'text-red-500' : 'text-amber-600') }}">
                                {{ $presents }}/{{ $total }}
                            </p>
                            <p class="text-xs text-gray-400">presentes</p>
                            @endif
                        </div>
                        @if($session->status === 'programada' || $session->status === 'realizada')
                        <a href="#session-{{ $session->id }}"
                           onclick="toggleSession({{ $session->id }})"
                           class="shrink-0 text-xs text-violet-600 hover:underline font-medium">
                            Asistencia
                        </a>
                        @endif
                    </div>

                    {{-- Panel de asistencia --}}
                    <div id="session-{{ $session->id }}" class="hidden mt-4">
                        <form action="{{ route('course-openings.attendance', $session) }}" method="POST">
                            @csrf
                            <div class="grid grid-cols-2 gap-2 mb-3">
                                <div>
                                    <label class="block text-xs text-gray-500 mb-1">Tema de la sesión</label>
                                    <input type="text" name="topic" value="{{ $session->topic }}"
                                        class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-violet-300"
                                        placeholder="Tema tratado">
                                </div>
                                <div>
                                    <label class="block text-xs text-gray-500 mb-1">Estado de la sesión</label>
                                    <select name="session_status" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-violet-300 bg-white">
                                        @foreach(['programada'=>'Programada','realizada'=>'Realizada','cancelada'=>'Cancelada','postergada'=>'Postergada'] as $v=>$l)
                                        <option value="{{ $v }}" {{ $session->status === $v ? 'selected' : '' }}>{{ $l }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="border border-gray-100 rounded-xl overflow-hidden">
                                <table class="w-full text-xs">
                                    <thead>
                                        <tr class="bg-gray-50">
                                            <th class="text-left px-3 py-2 font-medium text-gray-500">Estudiante</th>
                                            <th class="text-center px-2 py-2 font-medium text-gray-500">Asistencia</th>
                                            <th class="text-left px-2 py-2 font-medium text-gray-500">Observación</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-50">
                                        @foreach($courseOpening->enrollments as $i => $enrollment)
                                        @php
                                        $att = $session->attendances->firstWhere('course_opening_student_id', $enrollment->id);
                                        @endphp
                                        <tr>
                                            <input type="hidden" name="attendance[{{ $i }}][id]" value="{{ $enrollment->id }}">
                                            <td class="px-3 py-2 font-medium text-gray-700">{{ $enrollment->person_name }}</td>
                                            <td class="px-2 py-2">
                                                <select name="attendance[{{ $i }}][status]"
                                                    class="w-full px-2 py-1 border border-gray-200 rounded-lg text-xs bg-white focus:ring-1 focus:ring-violet-300">
                                                    @foreach(['presente'=>'✅ Presente','tardanza'=>'⏰ Tardanza','ausente'=>'❌ Ausente','justificado'=>'📋 Justificado'] as $v=>$l)
                                                    <option value="{{ $v }}" {{ ($att?->status ?? 'presente') === $v ? 'selected' : '' }}>{{ $l }}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td class="px-2 py-2">
                                                <input type="text" name="attendance[{{ $i }}][observation]" value="{{ $att?->observation }}"
                                                    class="w-full px-2 py-1 border border-gray-200 rounded-lg text-xs focus:ring-1 focus:ring-violet-300"
                                                    placeholder="Opcional">
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="flex justify-end mt-2">
                                <button type="submit" class="px-4 py-2 bg-violet-600 hover:bg-violet-700 text-white text-xs font-semibold rounded-lg transition-colors">
                                    Guardar asistencia
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                @endforeach
            </div>
            @endif
        </div>

        {{-- Estudiantes inscritos --}}
        <div class="bg-white rounded-2xl border border-gray-200 p-6">
            <h2 class="font-semibold text-gray-900 mb-4 flex items-center gap-2">
                <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                Estudiantes inscritos
                <span class="ml-auto text-xs font-normal text-gray-400">{{ $courseOpening->enrollments->count() }}</span>
            </h2>
            @if($courseOpening->enrollments->isEmpty())
            <p class="text-sm text-gray-400 italic">Sin estudiantes inscritos.</p>
            @else
            <div class="space-y-2">
                @foreach($courseOpening->enrollments as $enrollment)
                @php
                $stColors = ['inscrito'=>'bg-blue-100 text-blue-700','en_curso'=>'bg-amber-100 text-amber-700','completado'=>'bg-green-100 text-green-700','abandonado'=>'bg-red-100 text-red-500','retirado'=>'bg-gray-100 text-gray-500'];
                $sc2 = $stColors[$enrollment->status] ?? 'bg-gray-100 text-gray-500';
                $payColors = ['pagado'=>'text-green-600','pendiente'=>'text-amber-600','parcial'=>'text-orange-500','becado'=>'text-violet-600'];
                $pc = $payColors[$enrollment->payment_status] ?? 'text-gray-500';
                @endphp
                <div class="flex items-center gap-3 p-3 border border-gray-100 rounded-xl">
                    <div class="w-8 h-8 rounded-full overflow-hidden shrink-0">
                        @php $person = $enrollment->employee ?? $enrollment->client; @endphp
                        @if($person?->photo)
                        <img src="{{ asset('storage/'.$person->photo) }}" class="w-full h-full object-cover">
                        @else
                        <div class="w-full h-full {{ $enrollment->person_type === 'employee' ? 'bg-violet-100 text-violet-600' : 'bg-blue-100 text-blue-600' }} flex items-center justify-center text-xs font-bold">
                            {{ strtoupper(substr($enrollment->person_name, 0, 1)) }}
                        </div>
                        @endif
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-800 truncate">{{ $enrollment->person_name }}</p>
                        <p class="text-xs text-gray-400">{{ $enrollment->person_type === 'employee' ? 'Empleado' : 'Cliente' }} · Inscrito {{ $enrollment->enrolled_at?->format('d/m/Y') }}</p>
                    </div>
                    <div class="text-right shrink-0">
                        <span class="px-2 py-0.5 rounded-full text-xs font-semibold {{ $sc2 }} block mb-1">{{ ucfirst(str_replace('_',' ',$enrollment->status)) }}</span>
                        @if($enrollment->price_paid !== null)
                        <p class="text-xs {{ $pc }} font-medium">S/. {{ number_format($enrollment->price_paid,2) }} · {{ ucfirst($enrollment->payment_status) }}</p>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
            @endif
        </div>
    </div>

    {{-- Sidebar --}}
    <div class="space-y-5">
        {{-- Info del curso --}}
        <div class="bg-white rounded-2xl border border-gray-200 p-5">
            <h2 class="font-semibold text-gray-900 mb-4 text-sm">Detalles</h2>
            <div class="space-y-2.5 text-sm">
                <div class="flex justify-between">
                    <span class="text-gray-500">Inicio</span>
                    <span class="font-medium text-gray-800">{{ $courseOpening->start_date->format('d/m/Y') }}</span>
                </div>
                @if($courseOpening->end_date)
                <div class="flex justify-between">
                    <span class="text-gray-500">Fin</span>
                    <span class="font-medium text-gray-800">{{ $courseOpening->end_date->format('d/m/Y') }}</span>
                </div>
                @endif
                @if($courseOpening->time_start)
                <div class="flex justify-between">
                    <span class="text-gray-500">Horario</span>
                    <span class="font-medium text-gray-800">{{ substr($courseOpening->time_start,0,5) }}{{ $courseOpening->time_end ? ' – '.substr($courseOpening->time_end,0,5) : '' }}</span>
                </div>
                @endif
                @if($courseOpening->days_label !== '—')
                <div class="flex justify-between">
                    <span class="text-gray-500">Días</span>
                    <span class="font-medium text-gray-800">{{ $courseOpening->days_label }}</span>
                </div>
                @endif
                @if($courseOpening->branch)
                <div class="flex justify-between">
                    <span class="text-gray-500">Sede</span>
                    <span class="font-medium text-gray-800">{{ $courseOpening->branch->name }}</span>
                </div>
                @endif
                @if($courseOpening->effective_price !== null)
                <div class="flex justify-between">
                    <span class="text-gray-500">Precio</span>
                    <span class="font-bold text-violet-600">S/. {{ number_format($courseOpening->effective_price,2) }}</span>
                </div>
                @endif
            </div>
        </div>

        {{-- Instructores --}}
        <div class="bg-white rounded-2xl border border-gray-200 p-5">
            <h2 class="font-semibold text-gray-900 mb-3 text-sm">Instructores</h2>
            @forelse($courseOpening->instructors as $emp)
            <div class="flex items-center gap-2 mb-2">
                <div class="w-7 h-7 rounded-full overflow-hidden shrink-0">
                    @if($emp->photo)
                    <img src="{{ asset('storage/'.$emp->photo) }}" class="w-full h-full object-cover">
                    @else
                    <div class="w-full h-full bg-violet-100 flex items-center justify-center text-violet-600 text-xs font-bold">{{ strtoupper(substr($emp->first_name,0,1)) }}</div>
                    @endif
                </div>
                <div class="min-w-0">
                    <p class="text-sm font-medium text-gray-800 truncate">{{ $emp->full_name }}</p>
                    <p class="text-xs text-gray-400">{{ $emp->position }}</p>
                </div>
            </div>
            @empty
            <p class="text-xs text-gray-400 italic">Sin instructores asignados.</p>
            @endforelse
        </div>

        {{-- Acciones --}}
        <div class="bg-white rounded-2xl border border-gray-200 p-5 space-y-2">
            <a href="{{ route('course-openings.edit', $courseOpening) }}"
               class="w-full flex items-center justify-center gap-2 py-2.5 border border-violet-200 text-violet-600 hover:bg-violet-50 font-medium rounded-xl transition-colors text-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                Editar apertura
            </a>
            <form action="{{ route('course-openings.destroy', $courseOpening) }}" method="POST"
                  onsubmit="return confirm('¿Eliminar esta apertura? Se perderán sesiones y asistencias.')">
                @csrf @method('DELETE')
                <button type="submit" class="w-full flex items-center justify-center gap-2 py-2.5 border border-red-200 text-red-500 hover:bg-red-50 font-medium rounded-xl transition-colors text-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    Eliminar apertura
                </button>
            </form>
        </div>
    </div>
</div>

<script>
function toggleSession(id) {
    const el = document.getElementById('session-' + id);
    el.classList.toggle('hidden');
}
</script>

@endsection