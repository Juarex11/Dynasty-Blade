{{-- Partials: sessions list with inline attendance --}}
{{-- Usado dentro de course-openings/show.blade.php --}}

<div class="bg-white rounded-2xl border border-gray-200 overflow-hidden">
    <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
        <h2 class="font-semibold text-gray-900 flex items-center gap-2 text-sm">
            <svg class="w-4 h-4 text-violet-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            Sesiones y Asistencias
        </h2>
        <div class="flex items-center gap-3">
            <span class="text-xs text-gray-400">
                {{ $courseOpening->sessions->where('status','realizada')->count() }}
                / {{ $courseOpening->sessions->count() }} realizadas
            </span>
            @if($courseOpening->sessions->isNotEmpty())
            {{-- Progreso visual --}}
            @php
            $pct = $courseOpening->sessions->count() > 0
                ? round($courseOpening->sessions->where('status','realizada')->count() / $courseOpening->sessions->count() * 100)
                : 0;
            @endphp
            <div class="w-20 h-1.5 bg-gray-100 rounded-full overflow-hidden">
                <div class="h-full bg-violet-500 rounded-full transition-all" style="width:{{ $pct }}%"></div>
            </div>
            @endif
        </div>
    </div>

    @if($courseOpening->sessions->isEmpty())
        <div class="p-10 text-center">
            <div class="w-12 h-12 bg-gray-50 rounded-2xl flex items-center justify-center mx-auto mb-3">
                <svg class="w-6 h-6 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </div>
            <p class="text-sm text-gray-400 italic">Sin sesiones generadas.</p>
            <a href="{{ route('course-openings.edit', $courseOpening) }}"
               class="mt-3 inline-flex items-center gap-1 text-xs text-violet-600 hover:underline font-medium">
                Generar sesiones desde la edición →
            </a>
        </div>
    @else

    {{-- Resumen compacto de asistencia general --}}
    @if($attendanceRate !== null)
    <div class="px-6 py-3 bg-gray-50 border-b border-gray-100 flex items-center gap-4 text-xs text-gray-500">
        <div class="flex items-center gap-1.5">
            <span class="w-2 h-2 rounded-full bg-green-400 inline-block"></span>
            Asistencia promedio: <strong class="text-gray-700">{{ $attendanceRate }}%</strong>
        </div>
        <div class="flex items-center gap-1.5">
            <span class="w-2 h-2 rounded-full bg-amber-400 inline-block"></span>
            Sesiones realizadas: <strong class="text-gray-700">{{ $courseOpening->sessions->where('status','realizada')->count() }}</strong>
        </div>
        <div class="flex items-center gap-1.5">
            <span class="w-2 h-2 rounded-full bg-gray-300 inline-block"></span>
            Pendientes: <strong class="text-gray-700">{{ $courseOpening->sessions->where('status','programada')->count() }}</strong>
        </div>
    </div>
    @endif

    <div class="divide-y divide-gray-50">
        @foreach($courseOpening->sessions as $session)
        @php
            $stColors = [
                'programada'  => ['bg' => 'bg-gray-100',   'text' => 'text-gray-500',  'dot' => 'bg-gray-300'],
                'realizada'   => ['bg' => 'bg-green-100',  'text' => 'text-green-600', 'dot' => 'bg-green-400'],
                'cancelada'   => ['bg' => 'bg-red-100',    'text' => 'text-red-500',   'dot' => 'bg-red-400'],
                'postergada'  => ['bg' => 'bg-amber-100',  'text' => 'text-amber-600', 'dot' => 'bg-amber-400'],
            ];
            $sc = $stColors[$session->status] ?? $stColors['programada'];

            $presents   = $session->attendances->whereIn('status', ['presente','tardanza'])->count();
            $absents    = $session->attendances->where('status', 'ausente')->count();
            $justified  = $session->attendances->where('status', 'justificado')->count();
            $totalAtt   = $session->attendances->count();
            $attPct     = $totalAtt > 0 ? round($presents / $totalAtt * 100) : null;

            $isEditable = in_array($session->status, ['programada','realizada']);
            $panelId    = "sess-panel-{$session->id}";
        @endphp

        <div class="session-row" id="row-{{ $session->id }}">
            {{-- Fila resumen --}}
            <div class="flex items-center gap-3 px-5 py-3.5 hover:bg-gray-50/80 transition-colors">

                {{-- Número --}}
                <div class="w-8 h-8 rounded-xl bg-violet-50 border border-violet-100 flex items-center justify-center text-violet-600 text-xs font-bold shrink-0">
                    {{ $session->session_number }}
                </div>

                {{-- Info --}}
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="font-medium text-sm text-gray-800">
                            {{ $session->date->translatedFormat('D d/m/Y') }}
                        </span>
                        @if($session->time_start)
                        <span class="text-xs text-gray-400 tabular-nums">
                            {{ substr($session->time_start,0,5) }}{{ $session->time_end ? '–'.substr($session->time_end,0,5) : '' }}
                        </span>
                        @endif
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium {{ $sc['bg'] }} {{ $sc['text'] }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ $sc['dot'] }}"></span>
                            {{ ucfirst($session->status) }}
                        </span>
                    </div>
                    @if($session->topic)
                    <p class="text-xs text-gray-400 mt-0.5 truncate">📋 {{ $session->topic }}</p>
                    @endif
                </div>

                {{-- Asistencia mini --}}
                @if($totalAtt > 0)
                <div class="hidden sm:flex items-center gap-3 shrink-0">
                    <div class="flex items-center gap-1 text-xs">
                        <span class="w-2 h-2 rounded-full bg-green-400"></span>
                        <span class="text-gray-600 font-medium">{{ $presents }}</span>
                    </div>
                    <div class="flex items-center gap-1 text-xs">
                        <span class="w-2 h-2 rounded-full bg-red-400"></span>
                        <span class="text-gray-500">{{ $absents }}</span>
                    </div>
                    @if($justified > 0)
                    <div class="flex items-center gap-1 text-xs">
                        <span class="w-2 h-2 rounded-full bg-blue-400"></span>
                        <span class="text-gray-500">{{ $justified }}</span>
                    </div>
                    @endif
                    @if($attPct !== null)
                    <div class="w-16 h-1.5 bg-gray-100 rounded-full overflow-hidden">
                        <div class="h-full rounded-full {{ $attPct >= 75 ? 'bg-green-400' : ($attPct >= 50 ? 'bg-amber-400' : 'bg-red-400') }}"
                             style="width:{{ $attPct }}%"></div>
                    </div>
                    <span class="text-xs text-gray-400 tabular-nums w-7">{{ $attPct }}%</span>
                    @endif
                </div>
                @endif

                {{-- Toggle --}}
                @if($isEditable || $totalAtt > 0)
                <button type="button" onclick="toggleSessionPanel('{{ $panelId }}')"
                    class="shrink-0 flex items-center gap-1 text-xs font-medium text-violet-600 hover:text-violet-800 px-2.5 py-1.5 rounded-lg hover:bg-violet-50 transition-colors">
                    <span class="toggle-label-{{ $session->id }}">
                        {{ $isEditable ? 'Asistencia' : 'Ver' }}
                    </span>
                    <svg class="w-3.5 h-3.5 toggle-icon-{{ $session->id }} transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                @endif
            </div>

            {{-- Panel expandible de asistencia --}}
            <div id="{{ $panelId }}" class="hidden border-t border-gray-100">
                <form action="{{ route('course-openings.attendance', $session) }}" method="POST" class="p-5">
                    @csrf

                    {{-- Controles superiores --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">Tema / Contenido</label>
                            <input type="text" name="topic" value="{{ $session->topic }}"
                                class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-violet-300 focus:border-transparent"
                                placeholder="Tema tratado en esta sesión">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">Estado de la sesión</label>
                            <select name="session_status"
                                class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm bg-white focus:outline-none focus:ring-2 focus:ring-violet-300 focus:border-transparent">
                                @foreach(['programada'=>'📅 Programada','realizada'=>'✅ Realizada','cancelada'=>'❌ Cancelada','postergada'=>'⏸ Postergada'] as $v=>$l)
                                <option value="{{ $v }}" {{ $session->status === $v ? 'selected' : '' }}>{{ $l }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Accesos rápidos --}}
                    @if($courseOpening->enrollments->isNotEmpty())
                    <div class="flex items-center gap-2 mb-3">
                        <span class="text-xs text-gray-400">Marcar todos:</span>
                        <button type="button" onclick="markAll('{{ $session->id }}', 'presente')"
                            class="text-xs px-2.5 py-1 bg-green-50 text-green-600 hover:bg-green-100 rounded-lg font-medium transition-colors">
                            ✅ Todos presentes
                        </button>
                        <button type="button" onclick="markAll('{{ $session->id }}', 'ausente')"
                            class="text-xs px-2.5 py-1 bg-red-50 text-red-500 hover:bg-red-100 rounded-lg font-medium transition-colors">
                            ❌ Todos ausentes
                        </button>
                    </div>
                    @endif

                    {{-- Tabla de estudiantes --}}
                    @if($courseOpening->enrollments->isEmpty())
                    <p class="text-xs text-gray-400 italic py-3">Sin estudiantes inscritos en esta apertura.</p>
                    @else
                    <div class="rounded-xl border border-gray-200 overflow-hidden">
                        <table class="w-full text-xs" id="att-table-{{ $session->id }}">
                            <thead>
                                <tr class="bg-gray-50 border-b border-gray-100">
                                    <th class="text-left px-4 py-2.5 font-semibold text-gray-500">Estudiante</th>
                                    <th class="text-center px-3 py-2.5 font-semibold text-gray-500 w-44">Asistencia</th>
                                    <th class="text-left px-3 py-2.5 font-semibold text-gray-500">Observación</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @foreach($courseOpening->enrollments as $i => $enrollment)
                                @php
                                    $att = $session->attendances->firstWhere('course_opening_student_id', $enrollment->id);
                                    $currentStatus = $att?->status ?? 'presente';
                                @endphp
                                <tr class="hover:bg-gray-50/50 transition-colors att-row-{{ $session->id }}"
                                    data-enrollment="{{ $enrollment->id }}">
                                    <input type="hidden" name="attendance[{{ $i }}][id]" value="{{ $enrollment->id }}">
                                    <td class="px-4 py-3">
                                        <div class="flex items-center gap-2">
                                            @php $person = $enrollment->employee ?? $enrollment->client; @endphp
                                            @if($person?->photo)
                                                <img src="{{ asset('storage/'.$person->photo) }}" class="w-6 h-6 rounded-full object-cover shrink-0">
                                            @else
                                                <div class="w-6 h-6 rounded-full {{ $enrollment->person_type === 'employee' ? 'bg-violet-100 text-violet-600' : 'bg-blue-100 text-blue-600' }} flex items-center justify-center text-xs font-bold shrink-0">
                                                    {{ strtoupper(substr($enrollment->person_name, 0, 1)) }}
                                                </div>
                                            @endif
                                            <span class="font-medium text-gray-700">{{ $enrollment->person_name }}</span>
                                        </div>
                                    </td>
                                    <td class="px-3 py-3">
                                        {{-- Toggle visual de asistencia --}}
                                        <div class="flex items-center justify-center gap-1" id="att-btns-{{ $session->id }}-{{ $i }}">
                                            @foreach(['presente'=>['✅','bg-green-100 text-green-700 border-green-300','Presente'],'tardanza'=>['⏰','bg-amber-100 text-amber-700 border-amber-300','Tardanza'],'ausente'=>['❌','bg-red-100 text-red-600 border-red-300','Ausente'],'justificado'=>['📋','bg-blue-100 text-blue-600 border-blue-300','Justificado']] as $attVal=>[$icon,$activeClass,$label])
                                            <button type="button"
                                                onclick="setAttendance('{{ $session->id }}', {{ $i }}, '{{ $attVal }}')"
                                                title="{{ $label }}"
                                                class="att-btn w-8 h-8 rounded-lg border text-sm transition-all flex items-center justify-center
                                                    {{ $currentStatus === $attVal ? $activeClass.' border-opacity-100' : 'border-gray-200 hover:bg-gray-100 text-gray-400' }}"
                                                data-att-val="{{ $attVal }}" data-sess="{{ $session->id }}" data-idx="{{ $i }}">
                                                {{ $icon }}
                                            </button>
                                            @endforeach
                                            <input type="hidden"
                                                name="attendance[{{ $i }}][status]"
                                                id="att-status-{{ $session->id }}-{{ $i }}"
                                                value="{{ $currentStatus }}">
                                        </div>
                                    </td>
                                    <td class="px-3 py-3">
                                        <input type="text"
                                            name="attendance[{{ $i }}][observation]"
                                            value="{{ $att?->observation }}"
                                            class="w-full px-2.5 py-1.5 border border-gray-200 rounded-lg text-xs focus:ring-1 focus:ring-violet-300 focus:outline-none"
                                            placeholder="Observación (opcional)">
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @endif

                    <div class="flex justify-end mt-4">
                        <button type="submit"
                            class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-violet-500 to-purple-600 hover:from-violet-600 hover:to-purple-700 text-white text-sm font-semibold rounded-xl shadow-md shadow-violet-500/25 transition-all">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
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

<script>
function toggleSessionPanel(panelId) {
    const panel = document.getElementById(panelId);
    panel.classList.toggle('hidden');
    // Rotar ícono
    const sessId = panelId.replace('sess-panel-', '');
    const icons = document.querySelectorAll('.toggle-icon-' + sessId);
    icons.forEach(ic => ic.classList.toggle('rotate-180'));
}

function setAttendance(sessId, idx, val) {
    // Actualizar campo hidden
    document.getElementById(`att-status-${sessId}-${idx}`).value = val;

    // Actualizar botones visuales
    const btns = document.querySelectorAll(`[data-sess="${sessId}"][data-idx="${idx}"]`);
    const colorMap = {
        'presente':    'bg-green-100 text-green-700 border-green-300',
        'tardanza':    'bg-amber-100 text-amber-700 border-amber-300',
        'ausente':     'bg-red-100 text-red-600 border-red-300',
        'justificado': 'bg-blue-100 text-blue-600 border-blue-300',
    };

    btns.forEach(btn => {
        const bVal = btn.getAttribute('data-att-val');
        btn.className = `att-btn w-8 h-8 rounded-lg border text-sm transition-all flex items-center justify-center ${
            bVal === val
                ? colorMap[val] + ' border-opacity-100'
                : 'border-gray-200 hover:bg-gray-100 text-gray-400'
        }`;
    });
}

function markAll(sessId, val) {
    const rows = document.querySelectorAll(`.att-row-${sessId}`);
    rows.forEach((row, idx) => {
        setAttendance(sessId, idx, val);
    });
}
</script>