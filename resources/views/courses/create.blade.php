@extends('layouts.app')

@section('title', 'Nuevo Curso | Dynasty')

@section('content')

<div class="max-w-2xl mx-auto">

    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('courses.index') }}" class="w-9 h-9 flex items-center justify-center rounded-xl border border-gray-200 hover:border-violet-300 hover:bg-violet-50 text-gray-500 hover:text-violet-600 transition-all">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Nuevo Curso</h1>
            <p class="text-sm text-gray-500">Completa la información del curso</p>
        </div>
    </div>

    @if($errors->any())
    <div class="mb-5 bg-red-50 border border-red-200 rounded-xl p-4">
        <ul class="text-sm text-red-600 space-y-1 list-disc list-inside">
            @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('courses.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
        @csrf

        {{-- Información básica --}}
        <div class="bg-white rounded-2xl border border-gray-200 p-6 space-y-4">
            <h2 class="font-semibold text-gray-900 text-base border-b border-gray-100 pb-3">Información básica</h2>

            <div class="grid grid-cols-2 gap-4">
                {{-- Categoría con modal --}}
                <div class="col-span-2 sm:col-span-1">
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Categoría <span class="text-pink-500">*</span></label>
                    <div class="flex gap-2">
                        <select name="course_category_id" id="course_category_id" required
                            class="flex-1 px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-violet-300 bg-white @error('course_category_id') border-red-400 @enderror">
                            <option value="">Seleccionar...</option>
                            @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('course_category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                            @endforeach
                        </select>
                        <button type="button" onclick="openCatModal()" title="Nueva categoría"
                            class="w-10 h-10 shrink-0 flex items-center justify-center rounded-xl border border-gray-200 hover:border-violet-300 hover:bg-violet-50 text-gray-400 hover:text-violet-600 transition-all">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        </button>
                    </div>
                    @if($categories->count())
                    <div class="mt-2 flex flex-wrap gap-1.5" id="cat-chips">
                        @foreach($categories as $cat)
                        <button type="button" onclick="selectCat({{ $cat->id }}, '{{ addslashes($cat->name) }}')"
                            class="cat-chip px-2.5 py-1 rounded-lg text-xs font-medium border transition-all {{ old('course_category_id') == $cat->id ? 'border-violet-400 bg-violet-50 text-violet-700' : 'border-gray-200 bg-gray-50 text-gray-600 hover:border-violet-300 hover:bg-violet-50 hover:text-violet-700' }}"
                            data-id="{{ $cat->id }}">
                            {{ $cat->name }}
                        </button>
                        @endforeach
                    </div>
                    @endif
                </div>

                <div class="col-span-2 sm:col-span-1">
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Nombre <span class="text-pink-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-violet-300 @error('name') border-red-400 @enderror"
                        placeholder="Ej: Colorimetría Avanzada">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Descripción corta</label>
                <input type="text" name="short_description" value="{{ old('short_description') }}" maxlength="300"
                    class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-violet-300"
                    placeholder="Breve descripción del curso (máx. 300 caracteres)">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Descripción completa</label>
                <textarea name="description" rows="4"
                    class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-violet-300 resize-none"
                    placeholder="Descripción detallada del curso, contenido, beneficios...">{{ old('description') }}</textarea>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Imagen de portada</label>
                <div class="border-2 border-dashed border-gray-200 rounded-xl p-5 text-center hover:border-violet-300 transition-colors cursor-pointer" onclick="document.getElementById('cover-img').click()">
                    <svg class="w-8 h-8 text-gray-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <p class="text-sm text-gray-500" id="cover-label">Seleccionar imagen</p>
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
                    <input type="number" name="price" value="{{ old('price', 0) }}" required min="0" step="0.01"
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-violet-300"
                        placeholder="0 = Gratis">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Precio máximo (S/.) <span class="text-gray-400 font-normal">(opcional)</span></label>
                    <input type="number" name="price_max" value="{{ old('price_max') }}" min="0" step="0.01"
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-violet-300"
                        placeholder="Para rango de precios">
                </div>
            </div>

            <div class="grid grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Modalidad <span class="text-pink-500">*</span></label>
                    <select name="modality" required class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-violet-300 bg-white">
                        <option value="presencial" {{ old('modality') === 'presencial' ? 'selected' : '' }}>Presencial</option>
                        <option value="online"     {{ old('modality') === 'online'     ? 'selected' : '' }}>Online</option>
                        <option value="mixto"      {{ old('modality') === 'mixto'      ? 'selected' : '' }}>Mixto</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Nivel <span class="text-pink-500">*</span></label>
                    <select name="level" required class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-violet-300 bg-white">
                        <option value="basico"      {{ old('level') === 'basico'      ? 'selected' : '' }}>Básico</option>
                        <option value="intermedio"  {{ old('level') === 'intermedio'  ? 'selected' : '' }}>Intermedio</option>
                        <option value="avanzado"    {{ old('level') === 'avanzado'    ? 'selected' : '' }}>Avanzado</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Máx. estudiantes</label>
                    <input type="number" name="max_students" value="{{ old('max_students') }}" min="1"
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-violet-300"
                        placeholder="Sin límite">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Instructor externo <span class="text-gray-400 font-normal">(nombre libre)</span></label>
                <input type="text" name="instructor" value="{{ old('instructor') }}"
                    class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-violet-300"
                    placeholder="Nombre del instructor externo">
            </div>
        </div>

        {{-- Sedes donde se dictará --}}
        <div class="bg-white rounded-2xl border border-gray-200 p-6 space-y-4">
            <h2 class="font-semibold text-gray-900 text-base border-b border-gray-100 pb-3">Disponible en sedes</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                @foreach($branches as $branch)
                <label class="flex items-center gap-3 p-3 border border-gray-200 rounded-xl cursor-pointer hover:border-violet-300 hover:bg-violet-50 transition-all has-[:checked]:border-violet-400 has-[:checked]:bg-violet-50">
                    <input type="checkbox" name="branch_ids[]" value="{{ $branch->id }}"
                        {{ in_array($branch->id, old('branch_ids', [])) ? 'checked' : '' }}
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
            @if($employees->isEmpty())
            <p class="text-sm text-gray-400">No hay empleados activos registrados.</p>
            @else
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                @foreach($employees as $emp)
                <label class="flex items-center gap-2.5 p-2.5 border border-gray-100 rounded-xl cursor-pointer hover:border-violet-200 hover:bg-violet-50 transition-all has-[:checked]:border-violet-300 has-[:checked]:bg-violet-50">
                    <input type="checkbox" name="instructor_ids[]" value="{{ $emp->id }}"
                        {{ in_array($emp->id, old('instructor_ids', [])) ? 'checked' : '' }}
                        class="w-4 h-4 text-violet-600 rounded border-gray-300">
                    <div class="flex items-center gap-2 min-w-0">
                        <div class="w-7 h-7 rounded-full overflow-hidden shrink-0">
                            @if($emp->photo)
                            <img src="{{ asset('storage/' . $emp->photo) }}" class="w-full h-full object-cover">
                            @else
                            <div class="w-full h-full bg-violet-100 flex items-center justify-center text-violet-600 text-xs font-bold">{{ strtoupper(substr($emp->first_name, 0, 1)) }}</div>
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
                ['is_active',      'Curso activo',    'Visible y disponible para inscripciones', true],
                ['is_featured',    'Destacado',       'Aparece primero en los listados',         false],
                ['has_certificate','Tiene certificado','Se emite certificado al completarlo',    false],
            ] as [$field, $label, $desc, $default])
            <div class="flex items-center justify-between py-1">
                <div>
                    <p class="text-sm font-medium text-gray-900">{{ $label }}</p>
                    <p class="text-xs text-gray-400">{{ $desc }}</p>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="hidden" name="{{ $field }}" value="0">
                    <input type="checkbox" name="{{ $field }}" value="1"
                        {{ old($field, $default) ? 'checked' : '' }}
                        class="sr-only peer">
                    <div class="w-11 h-6 bg-gray-200 peer-focus:ring-2 peer-focus:ring-violet-300 rounded-full peer peer-checked:after:translate-x-full after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-gradient-to-r peer-checked:from-violet-500 peer-checked:to-purple-600"></div>
                </label>
            </div>
            @endforeach
        </div>

        <div class="flex gap-3 pb-6">
            <a href="{{ route('courses.index') }}" class="flex-1 text-center py-3 border border-gray-200 text-gray-600 font-semibold rounded-xl hover:bg-gray-50 transition-colors">Cancelar</a>
            <button type="submit" class="flex-1 bg-gradient-to-r from-violet-500 to-purple-600 hover:from-violet-600 hover:to-purple-700 text-white font-semibold py-3 rounded-xl shadow-lg shadow-violet-500/30 transition-all">Crear Curso</button>
        </div>
    </form>
</div>

{{-- MODAL: Nueva Categoría --}}
<div id="cat-modal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" onclick="closeCatModal()"></div>
    <div class="absolute inset-x-4 top-1/2 -translate-y-1/2 max-w-md mx-auto bg-white rounded-2xl shadow-2xl p-6">
        <div class="flex items-center justify-between mb-5">
            <div>
                <h3 class="text-lg font-bold text-gray-900">Nueva Categoría</h3>
                <p class="text-sm text-gray-500">Se creará y seleccionará automáticamente</p>
            </div>
            <button type="button" onclick="closeCatModal()" class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div id="modal-error" class="hidden mb-4 bg-red-50 border border-red-200 rounded-xl p-3">
            <p class="text-sm text-red-600" id="modal-error-text"></p>
        </div>
        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Nombre <span class="text-pink-500">*</span></label>
                <input type="text" id="modal-name" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-violet-300" placeholder="Ej: Técnicas de Color">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Descripción <span class="text-gray-400 font-normal">(opcional)</span></label>
                <input type="text" id="modal-desc" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-violet-300" placeholder="Breve descripción...">
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Color</label>
                    <div class="flex items-center gap-2">
                        <input type="color" id="modal-color" value="#8b5cf6" class="w-10 h-10 rounded-lg border border-gray-200 cursor-pointer p-1">
                        <span class="text-sm text-gray-500">Identifica la categoría</span>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Orden</label>
                    <input type="number" id="modal-sort" value="0" min="0" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-violet-300">
                </div>
            </div>
        </div>
        <div class="flex gap-3 mt-6">
            <button type="button" onclick="closeCatModal()" class="flex-1 py-2.5 border border-gray-200 text-gray-600 font-semibold rounded-xl hover:bg-gray-50 transition-colors text-sm">Cancelar</button>
            <button type="button" onclick="saveCategory()" id="modal-save-btn" class="flex-1 bg-gradient-to-r from-violet-500 to-purple-600 hover:from-violet-600 hover:to-purple-700 text-white font-semibold py-2.5 rounded-xl shadow-lg shadow-violet-500/30 transition-all text-sm">Crear y seleccionar</button>
        </div>
    </div>
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

function openCatModal() {
    document.getElementById('cat-modal').classList.remove('hidden');
    document.getElementById('modal-name').focus();
    document.getElementById('modal-error').classList.add('hidden');
}
function closeCatModal() {
    document.getElementById('cat-modal').classList.add('hidden');
    document.getElementById('modal-name').value = '';
    document.getElementById('modal-desc').value = '';
    document.getElementById('modal-sort').value = '0';
    document.getElementById('modal-color').value = '#8b5cf6';
}
async function saveCategory() {
    const name = document.getElementById('modal-name').value.trim();
    if (!name) { showModalErr('El nombre es obligatorio.'); return; }
    const btn = document.getElementById('modal-save-btn');
    btn.disabled = true; btn.textContent = 'Guardando...';
    try {
        const res = await fetch('{{ route('course-categories.store-ajax') }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
            body: JSON.stringify({ name, description: document.getElementById('modal-desc').value.trim() || null, color: document.getElementById('modal-color').value, sort_order: parseInt(document.getElementById('modal-sort').value) || 0 }),
        });
        const json = await res.json();
        if (!res.ok) { showModalErr(json.errors?.name?.[0] ?? json.message ?? 'Error.'); return; }
        const opt = new Option(json.name, json.id, true, true);
        document.getElementById('course_category_id').appendChild(opt);
        const chips = document.getElementById('cat-chips');
        if (chips) {
            const chip = document.createElement('button');
            chip.type = 'button';
            chip.className = 'cat-chip px-2.5 py-1 rounded-lg text-xs font-medium border transition-all border-gray-200 bg-gray-50 text-gray-600';
            chip.dataset.id = json.id;
            chip.textContent = json.name;
            chip.onclick = () => selectCat(json.id, json.name);
            chips.appendChild(chip);
        }
        selectCat(json.id, json.name);
        closeCatModal();
    } catch(e) { showModalErr('Error de conexión.'); }
    finally { btn.disabled = false; btn.textContent = 'Crear y seleccionar'; }
}
function showModalErr(msg) {
    document.getElementById('modal-error-text').textContent = msg;
    document.getElementById('modal-error').classList.remove('hidden');
}
document.addEventListener('keydown', e => { if (e.key === 'Escape') closeCatModal(); });
document.getElementById('modal-name')?.addEventListener('keydown', e => { if (e.key === 'Enter') { e.preventDefault(); saveCategory(); } });
</script>

@endsection