@extends('layouts.app')

@section('title', 'Nuevo Empleado | Dynasty')

@section('content')

<div class="max-w-2xl mx-auto">

    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('employees.index') }}" class="w-9 h-9 flex items-center justify-center rounded-xl border border-gray-200 hover:border-fuchsia-300 hover:bg-fuchsia-50 text-gray-500 hover:text-fuchsia-600 transition-all">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Nuevo Empleado</h1>
            <p class="text-sm text-gray-500">Registra un nuevo miembro del equipo</p>
        </div>
    </div>

    @if($errors->any())
    <div class="mb-5 bg-red-50 border border-red-200 rounded-xl p-4">
        <p class="text-sm font-semibold text-red-700 mb-2">Corrige los siguientes errores:</p>
        <ul class="text-sm text-red-600 space-y-1 list-disc list-inside">
            @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('employees.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
        @csrf

        {{-- Datos personales --}}
        <div class="bg-white rounded-2xl border border-gray-200 p-6 space-y-4">
            <h2 class="font-semibold text-gray-900 text-base border-b border-gray-100 pb-3">Datos personales</h2>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Nombre <span class="text-pink-500">*</span></label>
                    <input type="text" name="first_name" value="{{ old('first_name') }}" required
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-fuchsia-300 @error('first_name') border-red-400 @enderror"
                        placeholder="Ana">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Apellido <span class="text-pink-500">*</span></label>
                    <input type="text" name="last_name" value="{{ old('last_name') }}" required
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-fuchsia-300 @error('last_name') border-red-400 @enderror"
                        placeholder="García">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">DNI</label>
                    <input type="text" name="dni" value="{{ old('dni') }}"
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-fuchsia-300"
                        placeholder="12345678">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Fecha de nacimiento</label>
                    <input type="date" name="birth_date" value="{{ old('birth_date') }}"
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-fuchsia-300">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Teléfono</label>
                    <input type="text" name="phone" value="{{ old('phone') }}"
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-fuchsia-300"
                        placeholder="+51 999 999 999">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}"
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-fuchsia-300"
                        placeholder="empleado@email.com">
                </div>
            </div>

            {{-- Foto --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Foto</label>
                <div class="border-2 border-dashed border-gray-200 rounded-xl p-5 text-center hover:border-fuchsia-300 transition-colors cursor-pointer" onclick="document.getElementById('emp-photo').click()">
                    <svg class="w-8 h-8 text-gray-300 mx-auto mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    <p class="text-sm text-gray-500">Foto de perfil</p>
                    <input type="file" name="photo" id="emp-photo" accept="image/*" class="hidden">
                </div>
            </div>
        </div>

        {{-- Datos laborales --}}
        <div class="bg-white rounded-2xl border border-gray-200 p-6 space-y-4">
            <h2 class="font-semibold text-gray-900 text-base border-b border-gray-100 pb-3">Datos laborales</h2>

            <div class="grid grid-cols-2 gap-4">
                <div class="col-span-2 sm:col-span-1">
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Cargo / Posición <span class="text-pink-500">*</span></label>
                    <input type="text" name="position" value="{{ old('position') }}" required
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-fuchsia-300 @error('position') border-red-400 @enderror"
                        placeholder="Estilista, Colorista, Esteticista...">
                </div>
                <div class="col-span-2 sm:col-span-1">
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Tipo de contrato <span class="text-pink-500">*</span></label>
                    <select name="employment_type" required
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-fuchsia-300 bg-white">
                        <option value="full_time" {{ old('employment_type') === 'full_time' ? 'selected' : '' }}>Tiempo completo</option>
                        <option value="part_time" {{ old('employment_type') === 'part_time' ? 'selected' : '' }}>Medio tiempo</option>
                        <option value="freelance" {{ old('employment_type') === 'freelance' ? 'selected' : '' }}>Freelance</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Fecha de ingreso <span class="text-pink-500">*</span></label>
                    <input type="date" name="hire_date" value="{{ old('hire_date') }}" required
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-fuchsia-300 @error('hire_date') border-red-400 @enderror">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">% Comisión</label>
                    <input type="number" name="commission_rate" value="{{ old('commission_rate') }}" min="0" max="100" step="0.5"
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-fuchsia-300"
                        placeholder="Ej: 15">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Presentación / Bio</label>
                <textarea name="bio" rows="3"
                    class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-fuchsia-300 resize-none"
                    placeholder="Pequeña presentación del profesional...">{{ old('bio') }}</textarea>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Instagram</label>
                    <div class="flex">
                        <span class="px-3 bg-gray-50 border border-r-0 border-gray-200 rounded-l-xl text-gray-400 text-sm flex items-center">@</span>
                        <input type="text" name="instagram" value="{{ old('instagram') }}"
                            class="flex-1 px-3 py-2.5 border border-gray-200 rounded-r-xl text-sm focus:outline-none focus:ring-2 focus:ring-fuchsia-300"
                            placeholder="usuario">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">TikTok</label>
                    <div class="flex">
                        <span class="px-3 bg-gray-50 border border-r-0 border-gray-200 rounded-l-xl text-gray-400 text-sm flex items-center">@</span>
                        <input type="text" name="tiktok" value="{{ old('tiktok') }}"
                            class="flex-1 px-3 py-2.5 border border-gray-200 rounded-r-xl text-sm focus:outline-none focus:ring-2 focus:ring-fuchsia-300"
                            placeholder="usuario">
                    </div>
                </div>
            </div>
        </div>

        {{-- Sedes --}}
        <div class="bg-white rounded-2xl border border-gray-200 p-6 space-y-4">
            <h2 class="font-semibold text-gray-900 text-base border-b border-gray-100 pb-3">Sedes donde trabaja <span class="text-pink-500">*</span></h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                @foreach($branches as $branch)
                <label class="flex items-center gap-3 p-3 border border-gray-200 rounded-xl cursor-pointer hover:border-fuchsia-300 hover:bg-fuchsia-50 transition-all has-[:checked]:border-fuchsia-400 has-[:checked]:bg-fuchsia-50">
                    <input type="checkbox" name="branch_ids[]" value="{{ $branch->id }}"
                        {{ in_array($branch->id, old('branch_ids', [])) ? 'checked' : '' }}
                        class="w-4 h-4 text-fuchsia-600 rounded border-gray-300 branch-checkbox"
                        data-branch-id="{{ $branch->id }}" data-branch-name="{{ $branch->name }}">
                    <span class="text-sm font-medium text-gray-800">{{ $branch->name }}</span>
                </label>
                @endforeach
            </div>

            <div id="primary-branch-selector" class="hidden">
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Sede principal <span class="text-pink-500">*</span></label>
                <select name="primary_branch" id="primary-branch-select"
                    class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-fuchsia-300 bg-white">
                    <option value="">Seleccionar sede principal...</option>
                </select>
            </div>
        </div>

        {{-- Servicios --}}
        <div class="bg-white rounded-2xl border border-gray-200 p-6 space-y-4">
            <h2 class="font-semibold text-gray-900 text-base border-b border-gray-100 pb-3">Servicios que realiza</h2>

            @php $servicesByCategory = $services->groupBy(fn($s) => $s->category->name); @endphp
            <div class="space-y-3">
                @foreach($servicesByCategory as $catName => $catServices)
                <div>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">{{ $catName }}</p>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                        @foreach($catServices as $service)
                        <label class="flex items-center gap-2.5 p-2.5 border border-gray-100 rounded-xl cursor-pointer hover:border-fuchsia-200 hover:bg-fuchsia-50 transition-all has-[:checked]:border-fuchsia-300 has-[:checked]:bg-fuchsia-50">
                            <input type="checkbox" name="service_ids[]" value="{{ $service->id }}"
                                {{ in_array($service->id, old('service_ids', [])) ? 'checked' : '' }}
                                class="w-4 h-4 text-fuchsia-600 rounded border-gray-300">
                            <div class="min-w-0">
                                <p class="text-sm font-medium text-gray-800 truncate">{{ $service->name }}</p>
                                <p class="text-xs text-gray-400">{{ $service->price_display }}</p>
                            </div>
                        </label>
                        @endforeach
                    </div>
                </div>
                @endforeach
            </div>
        </div>

{{-- Cursos --}}
<div class="bg-white rounded-2xl border border-gray-200 p-6 space-y-4">
    <div class="flex items-center justify-between border-b border-gray-100 pb-3">
        <div>
            <h2 class="font-semibold text-gray-900 text-base">Cursos</h2>
            <p class="text-xs text-gray-400 mt-0.5">Selecciona el rol del empleado en cada curso</p>
        </div>
        <a href="{{ route('courses.index') }}" class="text-xs text-violet-600 hover:underline">Ver todos →</a>
    </div>

    @if($courses->isEmpty())
        <p class="text-sm text-gray-400 text-center py-4">No hay cursos activos registrados.
            <a href="{{ route('courses.create') }}" class="text-violet-600 hover:underline">Crear curso</a>
        </p>
    @else
        @php $coursesByCategory = $courses->groupBy(fn($c) => $c->category->name); @endphp

        {{-- ── Para el formulario de CREAR empleado ── --}}
        {{-- old values para create --}}
        @php
            $selectedInstructorCourses = old('instructor_course_ids', []);
            $selectedStudentCourses    = old('student_course_ids', []);
            // Para edit: descomentar estas líneas y comentar las de arriba
            // $selectedInstructorCourses = old('instructor_course_ids', $employee->teachingCourses->pluck('id')->toArray());
            // $selectedStudentCourses    = old('student_course_ids', $employee->enrolledCourses->pluck('id')->toArray());
        @endphp

        <div class="space-y-4">
            @foreach($coursesByCategory as $catName => $catCourses)
            <div>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">{{ $catName }}</p>
                <div class="space-y-2">
                    @foreach($catCourses as $course)
                    <div class="p-3 border border-gray-100 rounded-xl hover:border-violet-100 transition-colors">
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-800 truncate">{{ $course->name }}</p>
                                <p class="text-xs text-gray-400">{{ $course->price_display }} · {{ $course->duration_display }} · {{ $course->level_label }}</p>
                            </div>
                            {{-- Toggle de rol --}}
                            <div class="flex items-center gap-3 shrink-0">
                                <label class="flex items-center gap-1.5 cursor-pointer">
                                    <input type="checkbox" name="instructor_course_ids[]" value="{{ $course->id }}"
                                        {{ in_array($course->id, $selectedInstructorCourses) ? 'checked' : '' }}
                                        class="w-4 h-4 text-violet-600 rounded border-gray-300 course-instructor-check" data-course="{{ $course->id }}">
                                    <span class="text-xs font-medium text-violet-700">Instructor</span>
                                </label>
                                <label class="flex items-center gap-1.5 cursor-pointer">
                                    <input type="checkbox" name="student_course_ids[]" value="{{ $course->id }}"
                                        {{ in_array($course->id, $selectedStudentCourses) ? 'checked' : '' }}
                                        class="w-4 h-4 text-green-600 rounded border-gray-300 course-student-check" data-course="{{ $course->id }}">
                                    <span class="text-xs font-medium text-green-700">Estudiante</span>
                                </label>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endforeach
        </div>
    @endif
</div>

        {{-- Acceso al sistema --}}
        <div class="bg-white rounded-2xl border border-gray-200 p-6 space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="font-semibold text-gray-900 text-base">Acceso al sistema</h2>
                    <p class="text-xs text-gray-400 mt-0.5">Crea un usuario para que este empleado pueda ingresar</p>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="hidden" name="has_system_access" value="0">
                    <input type="checkbox" name="has_system_access" value="1" id="has-access-toggle"
                        {{ old('has_system_access') ? 'checked' : '' }}
                        class="sr-only peer">
                    <div class="w-11 h-6 bg-gray-200 peer-focus:ring-2 peer-focus:ring-fuchsia-300 rounded-full peer peer-checked:after:translate-x-full after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-gradient-to-r peer-checked:from-fuchsia-500 peer-checked:to-pink-600"></div>
                </label>
            </div>

            <div id="access-fields" class="{{ old('has_system_access') ? '' : 'hidden' }} space-y-4 border-t border-gray-100 pt-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Nombre de usuario <span class="text-pink-500">*</span></label>
                    <input type="text" name="user_name" value="{{ old('user_name') }}"
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-fuchsia-300"
                        placeholder="Nombre completo">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Email de acceso <span class="text-pink-500">*</span></label>
                    <input type="email" name="user_email" value="{{ old('user_email') }}"
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-fuchsia-300"
                        placeholder="acceso@email.com">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Contraseña <span class="text-pink-500">*</span></label>
                    <input type="password" name="user_password"
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-fuchsia-300"
                        placeholder="Mínimo 8 caracteres">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Rol en el sistema</label>
                    <select name="user_role"
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-fuchsia-300 bg-white">
                        <option value="employee">Empleado</option>
                        <option value="receptionist">Recepcionista</option>
                        <option value="manager">Manager</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="flex gap-3 pb-6">
            <a href="{{ route('employees.index') }}"
               class="flex-1 text-center py-3 border border-gray-200 text-gray-600 font-semibold rounded-xl hover:bg-gray-50 transition-colors">
                Cancelar
            </a>
            <button type="submit"
                class="flex-1 bg-gradient-to-r from-fuchsia-500 to-pink-600 hover:from-fuchsia-600 hover:to-pink-700 text-white font-semibold py-3 rounded-xl shadow-lg shadow-fuchsia-500/30 transition-all duration-200">
                Crear Empleado
            </button>
        </div>
    </form>
</div>

<script>
// Toggle acceso al sistema
document.getElementById('has-access-toggle')?.addEventListener('change', function () {
    document.getElementById('access-fields').classList.toggle('hidden', !this.checked);
});

// Sede principal dinámica
const checkboxes = document.querySelectorAll('.branch-checkbox');
const primarySelector = document.getElementById('primary-branch-selector');
const primarySelect = document.getElementById('primary-branch-select');

function updatePrimaryBranches() {
    const checked = [...checkboxes].filter(c => c.checked);
    primarySelector.classList.toggle('hidden', checked.length === 0);

    const currentValue = primarySelect.value;
    primarySelect.innerHTML = '<option value="">Seleccionar sede principal...</option>';
    checked.forEach(c => {
        const opt = document.createElement('option');
        opt.value = c.dataset.branchId;
        opt.textContent = c.dataset.branchName;
        if (c.dataset.branchId === currentValue) opt.selected = true;
        primarySelect.appendChild(opt);
    });

    // Si solo hay una sede marcada, seleccionarla automáticamente
    if (checked.length === 1) {
        primarySelect.value = checked[0].dataset.branchId;
    }
}

checkboxes.forEach(c => c.addEventListener('change', updatePrimaryBranches));
updatePrimaryBranches();

// Evitar que el mismo curso sea instructor y estudiante a la vez
document.querySelectorAll('.course-instructor-check').forEach(function(check) {
    check.addEventListener('change', function() {
        if (this.checked) {
            const studentCheck = document.querySelector(`.course-student-check[data-course="${this.dataset.course}"]`);
            if (studentCheck) studentCheck.checked = false;
        }
    });
});
document.querySelectorAll('.course-student-check').forEach(function(check) {
    check.addEventListener('change', function() {
        if (this.checked) {
            const instrCheck = document.querySelector(`.course-instructor-check[data-course="${this.dataset.course}"]`);
            if (instrCheck) instrCheck.checked = false;
        }
    });
});
</script>

@endsection