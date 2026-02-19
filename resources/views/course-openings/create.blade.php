@extends('layouts.app')

@section('title', 'Nueva Apertura | Dynasty')

@section('content')

<div class="max-w-3xl mx-auto">

    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('course-openings.index') }}"
           class="w-9 h-9 flex items-center justify-center rounded-xl border border-gray-200 hover:border-violet-300 hover:bg-violet-50 text-gray-500 hover:text-violet-600 transition-all">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Nueva Apertura de Curso</h1>
            <p class="text-sm text-gray-500">Crea una edición / grupo del curso</p>
        </div>
    </div>

    @if($errors->any())
    <div class="mb-5 bg-red-50 border border-red-200 rounded-xl p-4">
        <ul class="text-sm text-red-600 space-y-1 list-disc list-inside">
            @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('course-openings.store') }}" method="POST" class="space-y-5">
        @csrf

        {{-- Curso base --}}
        <div class="bg-white rounded-2xl border border-gray-200 p-6 space-y-4">
            <h2 class="font-semibold text-gray-900 text-base border-b border-gray-100 pb-3">Información general</h2>

            <div class="grid grid-cols-2 gap-4">
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Curso base <span class="text-pink-500">*</span></label>
                    <select name="course_id" id="course-select" required
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-violet-300 bg-white @error('course_id') border-red-400 @enderror">
                        <option value="">Seleccionar curso...</option>
                        @foreach($courses as $c)
                        <option value="{{ $c->id }}"
                            data-max="{{ $c->max_students ?? '' }}"
                            data-price="{{ $c->price ?? '' }}"
                            data-duration="{{ $c->duration_hours ?? '' }}"
                            {{ old('course_id', $selected?->id) == $c->id ? 'selected' : '' }}>
                            {{ $c->name }}
                        </option>
                        @endforeach
                    </select>
                    <p class="text-xs text-gray-400 mt-1">Al seleccionar el curso se precargará la información base</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Nombre de la edición <span class="text-gray-400 font-normal">(opcional)</span></label>
                    <input type="text" name="name" value="{{ old('name') }}"
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-violet-300"
                        placeholder="Ej: Grupo Mañana — Junio 2025">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Código interno</label>
                    <input type="text" name="code" value="{{ old('code') }}" maxlength="30"
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-violet-300"
                        placeholder="COL-2025-01">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Sede</label>
                    <select name="branch_id" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-violet-300 bg-white">
                        <option value="">Sin sede específica</option>
                        @foreach($branches as $b)
                        <option value="{{ $b->id }}" {{ old('branch_id') == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Estado <span class="text-pink-500">*</span></label>
                    <select name="status" required class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-violet-300 bg-white">
                        @foreach(['borrador'=>'Borrador','publicado'=>'Publicado','en_curso'=>'En curso','finalizado'=>'Finalizado','cancelado'=>'Cancelado'] as $v=>$l)
                        <option value="{{ $v }}" {{ old('status','borrador') === $v ? 'selected':'' }}>{{ $l }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        {{-- Horario y fechas --}}
        <div class="bg-white rounded-2xl border border-gray-200 p-6 space-y-4">
            <h2 class="font-semibold text-gray-900 text-base border-b border-gray-100 pb-3">Horario y fechas</h2>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Fecha inicio <span class="text-pink-500">*</span></label>
                    <input type="date" name="start_date" value="{{ old('start_date') }}" required
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-violet-300 @error('start_date') border-red-400 @enderror">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Fecha fin</label>
                    <input type="date" name="end_date" value="{{ old('end_date') }}"
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-violet-300">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Hora inicio</label>
                    <input type="time" name="time_start" value="{{ old('time_start') }}"
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-violet-300">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Hora fin</label>
                    <input type="time" name="time_end" value="{{ old('time_end') }}"
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-violet-300">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Días de la semana</label>
                <div class="flex gap-2 flex-wrap">
                    @php $days = [1=>'Lun',2=>'Mar',3=>'Mié',4=>'Jue',5=>'Vie',6=>'Sáb',7=>'Dom']; @endphp
                    @foreach($days as $num => $label)
                    <label class="w-12 h-12 flex flex-col items-center justify-center border border-gray-200 rounded-xl cursor-pointer hover:border-violet-300 transition-colors has-[:checked]:border-violet-500 has-[:checked]:bg-violet-50 text-xs font-medium text-gray-600 has-[:checked]:text-violet-700">
                        <input type="checkbox" name="days_of_week[]" value="{{ $num }}"
                            {{ in_array($num, old('days_of_week',[])) ? 'checked' : '' }}
                            class="hidden">
                        {{ $label }}
                    </label>
                    @endforeach
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Total de sesiones <span class="text-pink-500">*</span></label>
                    <input type="number" name="total_sessions" value="{{ old('total_sessions', 1) }}" required min="1"
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-violet-300">
                </div>
                <div class="flex items-end">
                    <label class="flex items-center gap-2 p-3 border border-gray-200 rounded-xl cursor-pointer hover:border-violet-300 hover:bg-violet-50 transition-colors w-full has-[:checked]:border-violet-400 has-[:checked]:bg-violet-50">
                        <input type="checkbox" name="generate_sessions" value="1" {{ old('generate_sessions') ? 'checked':'' }} class="text-violet-600 w-4 h-4">
                        <div>
                            <p class="text-sm font-medium text-gray-800">Generar sesiones automáticas</p>
                            <p class="text-xs text-gray-400">Basado en días seleccionados y fechas</p>
                        </div>
                    </label>
                </div>
            </div>
        </div>

        {{-- Capacidad y precio --}}
        <div class="bg-white rounded-2xl border border-gray-200 p-6 space-y-4">
            <h2 class="font-semibold text-gray-900 text-base border-b border-gray-100 pb-3">Capacidad y precio</h2>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Máximo de estudiantes</label>
                    <input type="number" name="max_students" id="max-students" value="{{ old('max_students') }}" min="1"
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-violet-300"
                        placeholder="Sin límite (hereda del curso)">
                    <p class="text-xs text-gray-400 mt-1" id="course-max-hint"></p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Precio (S/.) de esta apertura</label>
                    <input type="number" name="price" id="opening-price" value="{{ old('price') }}" min="0" step="0.01"
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-violet-300"
                        placeholder="Hereda del curso">
                </div>
            </div>

            {{-- Promoción --}}
            <div class="border border-dashed border-gray-200 rounded-xl p-4 space-y-3">
                <p class="text-sm font-medium text-gray-700 flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-pink-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                    Promoción <span class="font-normal text-gray-400">(opcional)</span>
                </p>
                <div class="grid grid-cols-3 gap-3">
                    <div>
                        <label class="block text-xs text-gray-500 mb-1">Precio promo (S/.)</label>
                        <input type="number" name="price_promo" value="{{ old('price_promo') }}" min="0" step="0.01"
                            class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-violet-300"
                            placeholder="0.00">
                    </div>
                    <div>
                        <label class="block text-xs text-gray-500 mb-1">Válido hasta</label>
                        <input type="date" name="promo_until" value="{{ old('promo_until') }}"
                            class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-violet-300">
                    </div>
                    <div>
                        <label class="block text-xs text-gray-500 mb-1">Etiqueta</label>
                        <input type="text" name="promo_label" value="{{ old('promo_label') }}" maxlength="80"
                            class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-violet-300"
                            placeholder="Ej: Early bird 20%">
                    </div>
                </div>
            </div>
        </div>

        {{-- Instructores --}}
        <div class="bg-white rounded-2xl border border-gray-200 p-6 space-y-4">
            <div>
                <h2 class="font-semibold text-gray-900 text-base">Instructores</h2>
                <p class="text-xs text-gray-400 mt-0.5">Empleados que dictarán esta apertura</p>
            </div>
            @if($employees->isEmpty())
            <p class="text-sm text-gray-400">No hay empleados activos.</p>
            @else
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2" id="instructor-list">
                @foreach($employees as $emp)
                <label class="flex items-center gap-2.5 p-2.5 border border-gray-100 rounded-xl cursor-pointer hover:border-violet-200 hover:bg-violet-50 transition-all has-[:checked]:border-violet-300 has-[:checked]:bg-violet-50">
                    <input type="checkbox" name="instructor_ids[]" value="{{ $emp->id }}"
                        {{ in_array($emp->id, old('instructor_ids',[])) ? 'checked' : '' }}
                        class="w-4 h-4 text-violet-600 rounded border-gray-300">
                    <div class="flex items-center gap-2 min-w-0">
                        <div class="w-7 h-7 rounded-full overflow-hidden shrink-0">
                            @if($emp->photo)
                            <img src="{{ asset('storage/'.$emp->photo) }}" class="w-full h-full object-cover">
                            @else
                            <div class="w-full h-full bg-violet-100 flex items-center justify-center text-violet-600 text-xs font-bold">{{ strtoupper(substr($emp->first_name,0,1)) }}</div>
                            @endif
                        </div>
                        <div class="min-w-0">
                            <p class="text-sm font-medium text-gray-800 truncate">{{ $emp->full_name }}</p>
                            <p class="text-xs text-gray-400 truncate">{{ $emp->position }}</p>
                        </div>
                    </div>
                </label>
                @endforeach
            </div>
            @endif
        </div>

        {{-- Estudiantes del equipo --}}
        <div class="bg-white rounded-2xl border border-gray-200 p-6 space-y-4">
            <div>
                <h2 class="font-semibold text-gray-900 text-base">Estudiantes — Empleados internos</h2>
                <p class="text-xs text-gray-400 mt-0.5">Empleados inscritos como estudiantes en esta apertura</p>
            </div>
            @if($employees->isEmpty())
            <p class="text-sm text-gray-400">No hay empleados activos.</p>
            @else
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                @foreach($employees as $emp)
                <label class="flex items-center gap-2.5 p-2.5 border border-gray-100 rounded-xl cursor-pointer hover:border-green-200 hover:bg-green-50 transition-all has-[:checked]:border-green-300 has-[:checked]:bg-green-50">
                    <input type="checkbox" name="employee_student_ids[]" value="{{ $emp->id }}"
                        {{ in_array($emp->id, old('employee_student_ids',[])) ? 'checked' : '' }}
                        class="w-4 h-4 text-green-600 rounded border-gray-300">
                    <div class="flex items-center gap-2 min-w-0">
                        <div class="w-7 h-7 rounded-full overflow-hidden shrink-0">
                            @if($emp->photo)
                            <img src="{{ asset('storage/'.$emp->photo) }}" class="w-full h-full object-cover">
                            @else
                            <div class="w-full h-full bg-green-100 flex items-center justify-center text-green-700 text-xs font-bold">{{ strtoupper(substr($emp->first_name,0,1)) }}</div>
                            @endif
                        </div>
                        <div class="min-w-0">
                            <p class="text-sm font-medium text-gray-800 truncate">{{ $emp->full_name }}</p>
                            <p class="text-xs text-gray-400 truncate">{{ $emp->position }}</p>
                        </div>
                    </div>
                </label>
                @endforeach
            </div>
            @endif
        </div>

        {{-- Clientes externos --}}
        @if($clients->isNotEmpty())
        <div class="bg-white rounded-2xl border border-gray-200 p-6 space-y-4">
            <div>
                <h2 class="font-semibold text-gray-900 text-base">Estudiantes — Clientes externos</h2>
                <p class="text-xs text-gray-400 mt-0.5">Clientes inscritos en esta apertura</p>
            </div>
            <div class="relative">
                <svg class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z"/></svg>
                <input type="text" id="client-search" placeholder="Buscar cliente..." class="w-full pl-9 pr-4 py-2 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-violet-300 mb-3">
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 max-h-64 overflow-y-auto pr-1" id="client-list">
                @foreach($clients as $cli)
                <label class="flex items-center gap-2.5 p-2.5 border border-gray-100 rounded-xl cursor-pointer hover:border-blue-200 hover:bg-blue-50 transition-all has-[:checked]:border-blue-300 has-[:checked]:bg-blue-50 client-item"
                       data-name="{{ strtolower($cli->full_name) }}">
                    <input type="checkbox" name="client_ids[]" value="{{ $cli->id }}"
                        {{ in_array($cli->id, old('client_ids',[])) ? 'checked' : '' }}
                        class="w-4 h-4 text-blue-600 rounded border-gray-300">
                    <div class="min-w-0">
                        <p class="text-sm font-medium text-gray-800 truncate">{{ $cli->full_name }}</p>
                        <p class="text-xs text-gray-400 truncate">{{ $cli->phone ?? $cli->email ?? 'Sin contacto' }}</p>
                    </div>
                </label>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Precio por estudiante al inscribir --}}
        <div class="bg-white rounded-2xl border border-gray-200 p-6 space-y-4">
            <h2 class="font-semibold text-gray-900 text-base border-b border-gray-100 pb-3">Inscripción masiva — configuración de pago</h2>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Precio pagado inicial (S/.)</label>
                    <input type="number" name="student_price_paid" value="{{ old('student_price_paid') }}" min="0" step="0.01"
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-violet-300"
                        placeholder="Aplica a todos los estudiantes arriba">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Estado de pago inicial</label>
                    <select name="student_payment_status" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-violet-300 bg-white">
                        <option value="pendiente" {{ old('student_payment_status','pendiente') === 'pendiente' ? 'selected':'' }}>Pendiente</option>
                        <option value="pagado"    {{ old('student_payment_status') === 'pagado' ? 'selected':'' }}>Pagado</option>
                        <option value="parcial"   {{ old('student_payment_status') === 'parcial' ? 'selected':'' }}>Pago parcial</option>
                        <option value="becado"    {{ old('student_payment_status') === 'becado' ? 'selected':'' }}>Becado</option>
                    </select>
                </div>
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Notas internas</label>
            <textarea name="notes" rows="3"
                class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-violet-300 resize-none bg-white"
                placeholder="Observaciones, acuerdos, detalles de la apertura...">{{ old('notes') }}</textarea>
        </div>

        <div class="flex gap-3 pb-6">
            <a href="{{ route('course-openings.index') }}" class="flex-1 text-center py-3 border border-gray-200 text-gray-600 font-semibold rounded-xl hover:bg-gray-50 transition-colors">Cancelar</a>
            <button type="submit" class="flex-1 bg-gradient-to-r from-violet-500 to-purple-600 hover:from-violet-600 hover:to-purple-700 text-white font-semibold py-3 rounded-xl shadow-lg shadow-violet-500/30 transition-all">Crear Apertura</button>
        </div>
    </form>
</div>

<script>
// Precargar datos del curso seleccionado
document.getElementById('course-select')?.addEventListener('change', function() {
    const opt = this.options[this.selectedIndex];
    const maxHint = document.getElementById('course-max-hint');
    const priceInp = document.getElementById('opening-price');
    const maxInp   = document.getElementById('max-students');
    if (opt.dataset.max) {
        maxHint.textContent = `El curso base tiene máx. ${opt.dataset.max} estudiantes`;
        if (!maxInp.value) maxInp.value = opt.dataset.max;
    } else {
        maxHint.textContent = 'El curso base no tiene límite definido';
    }
    if (opt.dataset.price && !priceInp.value) priceInp.value = opt.dataset.price;
});

// Búsqueda de clientes
document.getElementById('client-search')?.addEventListener('input', function() {
    const q = this.value.toLowerCase();
    document.querySelectorAll('.client-item').forEach(el => {
        el.style.display = el.dataset.name.includes(q) ? '' : 'none';
    });
});
</script>

@endsection