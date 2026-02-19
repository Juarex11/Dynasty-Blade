@extends('layouts.app')

@section('title', 'Editar Curso | Dynasty')

@section('content')

<div class="max-w-2xl mx-auto">

    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('courses.show', $course) }}"
           class="w-9 h-9 flex items-center justify-center rounded-xl border border-gray-200 hover:border-violet-300 hover:bg-violet-50 text-gray-500 hover:text-violet-600 transition-all">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Editar Curso</h1>
            <p class="text-sm text-gray-500 truncate max-w-xs">{{ $course->name }}</p>
        </div>
    </div>

    @if($errors->any())
    <div class="mb-5 bg-red-50 border border-red-200 rounded-xl p-4">
        <ul class="text-sm text-red-600 space-y-1 list-disc list-inside">
            @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('courses.update', $course) }}" method="POST" enctype="multipart/form-data" class="space-y-5">
        @csrf
        @method('PUT')

        {{-- Información básica --}}
        <div class="bg-white rounded-2xl border border-gray-200 p-6 space-y-4">
            <h2 class="font-semibold text-gray-900 text-base border-b border-gray-100 pb-3">Información básica</h2>

            <div class="grid grid-cols-2 gap-4">
                <div class="col-span-2 sm:col-span-1">
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Categoría <span class="text-pink-500">*</span></label>
                    <select name="course_category_id" id="course_category_id" required
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-violet-300 bg-white @error('course_category_id') border-red-400 @enderror">
                        <option value="">Seleccionar...</option>
                        @foreach($categories as $cat)
                        <option value="{{ $cat->id }}"
                            {{ old('course_category_id', $course->course_category_id) == $cat->id ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                        @endforeach
                    </select>
                    @if($categories->count())
                    <div class="mt-2 flex flex-wrap gap-1.5" id="cat-chips">
                        @foreach($categories as $cat)
                        <button type="button" onclick="selectCat({{ $cat->id }}, '{{ addslashes($cat->name) }}')"
                            class="cat-chip px-2.5 py-1 rounded-lg text-xs font-medium border transition-all
                                {{ old('course_category_id', $course->course_category_id) == $cat->id
                                    ? 'border-violet-400 bg-violet-50 text-violet-700'
                                    : 'border-gray-200 bg-gray-50 text-gray-600 hover:border-violet-300 hover:bg-violet-50 hover:text-violet-700' }}"
                            data-id="{{ $cat->id }}">
                            {{ $cat->name }}
                        </button>
                        @endforeach
                    </div>
                    @endif
                </div>

                <div class="col-span-2 sm:col-span-1">
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Nombre <span class="text-pink-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $course->name) }}" required
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-violet-300 @error('name') border-red-400 @enderror"
                        placeholder="Ej: Colorimetría Avanzada">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Descripción corta</label>
                <input type="text" name="short_description" value="{{ old('short_description', $course->short_description) }}" maxlength="300"
                    class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-violet-300"
                    placeholder="Breve descripción del curso (máx. 300 caracteres)">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Descripción completa</label>
                <textarea name="description" rows="4"
                    class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-violet-300 resize-none"
                    placeholder="Descripción detallada del curso...">{{ old('description', $course->description) }}</textarea>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Imagen de portada</label>
                @if($course->cover_image)
                <div class="mb-3 flex items-center gap-3 p-3 bg-gray-50 rounded-xl border border-gray-200">
                    <img src="{{ asset('storage/' . $course->cover_image) }}" alt="Portada actual"
                         class="w-16 h-12 object-cover rounded-lg shrink-0">
                    <div class="min-w-0">
                        <p class="text-sm font-medium text-gray-700">Imagen actual</p>
                        <p class="text-xs text-gray-400">Selecciona una nueva para reemplazarla</p>
                    </div>
                </div>
                @endif
                <div class="border-2 border-dashed border-gray-200 rounded-xl p-5 text-center hover:border-violet-300 transition-colors cursor-pointer"
                     onclick="document.getElementById('cover-img').click()">
                    <svg class="w-8 h-8 text-gray-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <p class="text-sm text-gray-500" id="cover-label">{{ $course->cover_image ? 'Cambiar imagen' : 'Seleccionar imagen' }}</p>
                    <input type="file" name="cover_image" id="cover-img" accept="image/*" class="hidden"
                        onchange="document.getElementById('cover-label').textContent = this.files[0]?.name ?? 'Seleccionar imagen'">
                </div>
            </div>
        </div>

        {{-- Precio y modalidad --}}
        <div class="bg-white rounded-2xl border border-gray-200 p-6 space-y-4">
            <h2 class="font-semibold text-gray-900 text-base border-b border-gray-100 pb-3">Precio y modalidad</h2>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Precio (S/.) <span class="text-pink-500">*</span></label>
                    <input type="number" name="price" value="{{ old('price', $course->price) }}" required min="0" step="0.01"
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-violet-300"
                        placeholder="0 = Gratis">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Precio máximo (S/.) <span class="text-gray-400 font-normal">(opcional)</span></label>
                    <input type="number" name="price_max" value="{{ old('price_max', $course->price_max) }}" min="0" step="0.01"
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-violet-300"
                        placeholder="Para rango de precios">
                </div>
            </div>

            <div class="grid grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Modalidad <span class="text-pink-500">*</span></label>
                    <select name="modality" required class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-violet-300 bg-white">
                        @foreach(['presencial' => 'Presencial', 'online' => 'Online', 'mixto' => 'Mixto'] as $val => $label)
                        <option value="{{ $val }}" {{ old('modality', $course->modality) === $val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Nivel <span class="text-pink-500">*</span></label>
                    <select name="level" required class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-violet-300 bg-white">
                        @foreach(['basico' => 'Básico', 'intermedio' => 'Intermedio', 'avanzado' => 'Avanzado'] as $val => $label)
                        <option value="{{ $val }}" {{ old('level', $course->level) === $val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Máx. estudiantes</label>
                    <input type="number" name="max_students" value="{{ old('max_students', $course->max_students) }}" min="1"
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-violet-300"
                        placeholder="Sin límite">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Instructor externo <span class="text-gray-400 font-normal">(nombre libre)</span></label>
                <input type="text" name="instructor" value="{{ old('instructor', $course->instructor) }}"
                    class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-violet-300"
                    placeholder="Nombre del instructor externo">
            </div>
        </div>

        {{-- Sedes --}}
        <div class="bg-white rounded-2xl border border-gray-200 p-6 space-y-4">
            <h2 class="font-semibold text-gray-900 text-base border-b border-gray-100 pb-3">Disponible en sedes</h2>
            @php $selectedBranches = old('branch_ids', $course->branches->pluck('id')->toArray()) @endphp
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                @foreach($branches as $branch)
                <label class="flex items-center gap-3 p-3 border border-gray-200 rounded-xl cursor-pointer hover:border-violet-300 hover:bg-violet-50 transition-all has-[:checked]:border-violet-400 has-[:checked]:bg-violet-50">
                    <input type="checkbox" name="branch_ids[]" value="{{ $branch->id }}"
                        {{ in_array($branch->id, $selectedBranches) ? 'checked' : '' }}
                        class="w-4 h-4 text-violet-600 rounded border-gray-300 focus:ring-violet-400">
                    <div>
                        <p class="text-sm font-medium text-gray-800">{{ $branch->name }}</p>
                        <p class="text-xs text-gray-400">{{ $branch->district ?? $branch->city }}</p>
                    </div>
                </label>
                @endforeach
            </div>
        </div>

        {{-- Instructores del equipo --}}
        <div class="bg-white rounded-2xl border border-gray-200 p-6 space-y-4">
            <div>
                <h2 class="font-semibold text-gray-900 text-base">Instructores del equipo</h2>
                <p class="text-xs text-gray-400 mt-0.5">Empleados que dictan este curso</p>
            </div>
            @php
                $selectedInstructors = old('instructor_ids',
                    $course->employees->where('pivot.role', 'instructor')->pluck('id')->toArray()
                );
            @endphp
            @if($employees->isEmpty())
            <p class="text-sm text-gray-400">No hay empleados activos registrados.</p>
            @else
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                @foreach($employees as $emp)
                <label class="flex items-center gap-2.5 p-2.5 border border-gray-100 rounded-xl cursor-pointer hover:border-violet-200 hover:bg-violet-50 transition-all has-[:checked]:border-violet-300 has-[:checked]:bg-violet-50">
                    <input type="checkbox" name="instructor_ids[]" value="{{ $emp->id }}"
                        {{ in_array($emp->id, $selectedInstructors) ? 'checked' : '' }}
                        class="w-4 h-4 text-violet-600 rounded border-gray-300">
                    <div class="flex items-center gap-2 min-w-0">
                        <div class="w-7 h-7 rounded-full overflow-hidden shrink-0">
                            @if($emp->photo)
                            <img src="{{ asset('storage/' . $emp->photo) }}" class="w-full h-full object-cover">
                            @else
                            <div class="w-full h-full bg-violet-100 flex items-center justify-center text-violet-600 text-xs font-bold">
                                {{ strtoupper(substr($emp->first_name, 0, 1)) }}
                            </div>
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

        {{-- Configuración --}}
        <div class="bg-white rounded-2xl border border-gray-200 p-6 space-y-3">
            <h2 class="font-semibold text-gray-900 text-base border-b border-gray-100 pb-3">Configuración</h2>
            @foreach([
                ['is_active',      'Curso activo',     'Visible y disponible para inscripciones',  $course->is_active],
                ['is_featured',    'Destacado',         'Aparece primero en los listados',          $course->is_featured],
                ['has_certificate','Tiene certificado', 'Se emite certificado al completarlo',      $course->has_certificate],
            ] as [$field, $label, $desc, $current])
            <div class="flex items-center justify-between py-1">
                <div>
                    <p class="text-sm font-medium text-gray-900">{{ $label }}</p>
                    <p class="text-xs text-gray-400">{{ $desc }}</p>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="hidden" name="{{ $field }}" value="0">
                    <input type="checkbox" name="{{ $field }}" value="1"
                        {{ old($field, $current) ? 'checked' : '' }}
                        class="sr-only peer">
                    <div class="w-11 h-6 bg-gray-200 peer-focus:ring-2 peer-focus:ring-violet-300 rounded-full peer peer-checked:after:translate-x-full after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-gradient-to-r peer-checked:from-violet-500 peer-checked:to-purple-600"></div>
                </label>
            </div>
            @endforeach
        </div>

        <div class="flex gap-3 pb-6">
            <a href="{{ route('courses.show', $course) }}"
               class="flex-1 text-center py-3 border border-gray-200 text-gray-600 font-semibold rounded-xl hover:bg-gray-50 transition-colors">
               Cancelar
            </a>
            <button type="submit"
                class="flex-1 bg-gradient-to-r from-violet-500 to-purple-600 hover:from-violet-600 hover:to-purple-700 text-white font-semibold py-3 rounded-xl shadow-lg shadow-violet-500/30 transition-all">
                Guardar cambios
            </button>
        </div>
    </form>
</div>

<script>
function selectCat(id, name) {
    document.getElementById('course_category_id').value = id;
    document.querySelectorAll('.cat-chip').forEach(c => {
        const sel = c.dataset.id == id;
        c.classList.toggle('border-violet-400', sel);
        c.classList.toggle('bg-violet-50', sel);
        c.classList.toggle('text-violet-700', sel);
        c.classList.toggle('border-gray-200', !sel);
        c.classList.toggle('bg-gray-50', !sel);
        c.classList.toggle('text-gray-600', !sel);
    });
}
const sel = document.getElementById('course_category_id');
if (sel.value) selectCat(sel.value, '');
sel.addEventListener('change', () => selectCat(sel.value, ''));
</script>

@endsection