@extends('layouts.app')

@section('title', 'Nuevo Servicio | Dynasty')

@section('content')

<div class="max-w-2xl mx-auto">

    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('services.index') }}" class="w-9 h-9 flex items-center justify-center rounded-xl border border-gray-200 hover:border-fuchsia-300 hover:bg-fuchsia-50 text-gray-500 hover:text-fuchsia-600 transition-all">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Nuevo Servicio</h1>
            <p class="text-sm text-gray-500">Completa la información del servicio</p>
        </div>
    </div>

    @if($errors->any())
    <div class="mb-5 bg-red-50 border border-red-200 rounded-xl p-4">
        <p class="text-sm font-semibold text-red-700 mb-2">Por favor corrige los siguientes errores:</p>
        <ul class="text-sm text-red-600 space-y-1 list-disc list-inside">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('services.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
        @csrf

        {{-- Info básica --}}
        <div class="bg-white rounded-2xl border border-gray-200 p-6 space-y-4">
            <h2 class="font-semibold text-gray-900 text-base border-b border-gray-100 pb-3">Información básica</h2>

            <div class="grid grid-cols-2 gap-4">
                {{-- Categoría con botón para crear nueva --}}
                <div class="col-span-2 sm:col-span-1">
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Categoría <span class="text-pink-500">*</span></label>
                    <div class="flex gap-2">
                        <select name="service_category_id" id="service_category_id" required
                            class="flex-1 px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-fuchsia-300 bg-white @error('service_category_id') border-red-400 @enderror">
                            <option value="">Seleccionar...</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ old('service_category_id') == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->name }}
                                </option>
                            @endforeach
                        </select>
                        <button type="button" onclick="openCategoryModal()"
                            title="Crear nueva categoría"
                            class="w-10 h-10 shrink-0 flex items-center justify-center rounded-xl border border-gray-200 hover:border-fuchsia-300 hover:bg-fuchsia-50 text-gray-400 hover:text-fuchsia-600 transition-all">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                        </button>
                    </div>
                    {{-- Lista rápida de categorías existentes --}}
                    @if($categories->count() > 0)
                    <div class="mt-2 flex flex-wrap gap-1.5" id="category-chips">
                        @foreach($categories as $cat)
                        <button type="button"
                            onclick="selectCategory({{ $cat->id }}, '{{ addslashes($cat->name) }}')"
                            class="category-chip px-2.5 py-1 rounded-lg text-xs font-medium border transition-all
                                {{ old('service_category_id') == $cat->id
                                    ? 'border-fuchsia-400 bg-fuchsia-50 text-fuchsia-700'
                                    : 'border-gray-200 bg-gray-50 text-gray-600 hover:border-fuchsia-300 hover:bg-fuchsia-50 hover:text-fuchsia-700' }}"
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
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-fuchsia-300 @error('name') border-red-400 @enderror"
                        placeholder="Ej: Corte de cabello">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Descripción corta</label>
                <input type="text" name="short_description" value="{{ old('short_description') }}"
                    class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-fuchsia-300"
                    placeholder="Breve descripción que verá el cliente (máx. 300 caracteres)" maxlength="300">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Descripción completa</label>
                <textarea name="description" rows="4"
                    class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-fuchsia-300 resize-none"
                    placeholder="Descripción detallada del servicio...">{{ old('description') }}</textarea>
            </div>
        </div>

        {{-- Precio y duración --}}
        <div class="bg-white rounded-2xl border border-gray-200 p-6 space-y-4">
            <h2 class="font-semibold text-gray-900 text-base border-b border-gray-100 pb-3">Precio y duración</h2>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Precio base (S/.) <span class="text-pink-500">*</span></label>
                    <input type="number" name="price" value="{{ old('price') }}" required min="0" step="0.01"
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-fuchsia-300 @error('price') border-red-400 @enderror"
                        placeholder="50.00">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Precio máximo (S/.) <span class="text-gray-400 font-normal">(opcional)</span></label>
                    <input type="number" name="price_max" value="{{ old('price_max') }}" min="0" step="0.01"
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-fuchsia-300"
                        placeholder="Para rango de precios">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Duración (minutos) <span class="text-pink-500">*</span></label>
                    <input type="number" name="duration_minutes" value="{{ old('duration_minutes', 60) }}" required min="5" step="5"
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-fuchsia-300 @error('duration_minutes') border-red-400 @enderror">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Buffer (minutos)</label>
                    <input type="number" name="buffer_minutes" value="{{ old('buffer_minutes', 0) }}" min="0" step="5"
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-fuchsia-300"
                        placeholder="Tiempo de limpieza/preparación">
                </div>
            </div>
        </div>

        {{-- Sedes --}}
        <div class="bg-white rounded-2xl border border-gray-200 p-6 space-y-4">
            <h2 class="font-semibold text-gray-900 text-base border-b border-gray-100 pb-3">Disponible en sedes</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                @foreach($branches as $branch)
                <label class="flex items-center gap-3 p-3 border border-gray-200 rounded-xl cursor-pointer hover:border-fuchsia-300 hover:bg-fuchsia-50 transition-all has-[:checked]:border-fuchsia-400 has-[:checked]:bg-fuchsia-50">
                    <input type="checkbox" name="branch_ids[]" value="{{ $branch->id }}"
                        {{ in_array($branch->id, old('branch_ids', [])) ? 'checked' : '' }}
                        class="w-4 h-4 text-fuchsia-600 rounded border-gray-300 focus:ring-fuchsia-400">
                    <div>
                        <p class="text-sm font-medium text-gray-800">{{ $branch->name }}</p>
                        <p class="text-xs text-gray-400">{{ $branch->district ?? $branch->city }}</p>
                    </div>
                </label>
                @endforeach
            </div>
        </div>

        {{-- Imágenes --}}
        <div class="bg-white rounded-2xl border border-gray-200 p-6 space-y-4">
            <h2 class="font-semibold text-gray-900 text-base border-b border-gray-100 pb-3">Imágenes</h2>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Imagen principal (cover)</label>
                <div class="border-2 border-dashed border-gray-200 rounded-xl p-5 text-center hover:border-fuchsia-300 transition-colors cursor-pointer" onclick="document.getElementById('cover-image').click()">
                    <svg class="w-8 h-8 text-gray-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <p class="text-sm text-gray-500" id="cover-label">Imagen principal del servicio</p>
                    <input type="file" name="cover_image" id="cover-image" accept="image/*" class="hidden"
                        onchange="document.getElementById('cover-label').textContent = this.files[0]?.name ?? 'Imagen principal del servicio'">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Galería de imágenes <span class="text-gray-400 font-normal">(hasta 10)</span></label>
                <div class="border-2 border-dashed border-gray-200 rounded-xl p-5 text-center hover:border-fuchsia-300 transition-colors cursor-pointer" onclick="document.getElementById('gallery-images').click()">
                    <svg class="w-8 h-8 text-gray-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <p class="text-sm text-gray-500" id="gallery-label">Seleccionar múltiples imágenes</p>
                    <input type="file" name="images[]" id="gallery-images" accept="image/*" multiple class="hidden"
                        onchange="document.getElementById('gallery-label').textContent = this.files.length + ' imagen(es) seleccionada(s)'">
                </div>
            </div>
        </div>

        {{-- Configuración --}}
        <div class="bg-white rounded-2xl border border-gray-200 p-6 space-y-4">
            <h2 class="font-semibold text-gray-900 text-base border-b border-gray-100 pb-3">Configuración</h2>

            <div class="space-y-3">
                @foreach([
                    ['is_active', 'Servicio activo', 'Visible para los clientes'],
                    ['is_featured', 'Destacado', 'Aparece primero en los listados'],
                    ['online_booking', 'Reserva online', 'Permite reservas por internet'],
                    ['requires_deposit', 'Requiere seña', 'El cliente debe pagar una seña al reservar'],
                ] as [$name, $label, $desc])
                <div class="flex items-center justify-between py-1">
                    <div>
                        <p class="text-sm font-medium text-gray-900">{{ $label }}</p>
                        <p class="text-xs text-gray-400">{{ $desc }}</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="hidden" name="{{ $name }}" value="0">
                        <input type="checkbox" name="{{ $name }}" value="1"
                            {{ old($name, in_array($name, ['is_active', 'online_booking'])) ? 'checked' : '' }}
                            class="sr-only peer" id="toggle-{{ $name }}">
                        <div class="w-11 h-6 bg-gray-200 peer-focus:ring-2 peer-focus:ring-fuchsia-300 rounded-full peer peer-checked:after:translate-x-full after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-gradient-to-r peer-checked:from-fuchsia-500 peer-checked:to-pink-600"></div>
                    </label>
                </div>
                @endforeach
            </div>

            <div id="deposit-field" class="hidden">
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Monto de seña (S/.)</label>
                <input type="number" name="deposit_amount" value="{{ old('deposit_amount') }}" min="0" step="0.01"
                    class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-fuchsia-300"
                    placeholder="20.00">
            </div>
        </div>

        <div class="flex gap-3 pb-6">
            <a href="{{ route('services.index') }}"
               class="flex-1 text-center py-3 border border-gray-200 text-gray-600 font-semibold rounded-xl hover:bg-gray-50 transition-colors">
                Cancelar
            </a>
            <button type="submit"
                class="flex-1 bg-gradient-to-r from-fuchsia-500 to-pink-600 hover:from-fuchsia-600 hover:to-pink-700 text-white font-semibold py-3 rounded-xl shadow-lg shadow-fuchsia-500/30 transition-all duration-200">
                Crear Servicio
            </button>
        </div>
    </form>
</div>

{{-- ─── MODAL: Nueva Categoría ──────────────────────────────────────────── --}}
<div id="category-modal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" onclick="closeCategoryModal()"></div>

    <div class="absolute inset-x-4 top-1/2 -translate-y-1/2 max-w-md mx-auto bg-white rounded-2xl shadow-2xl p-6">
        
        {{-- Tabs --}}
        <div class="flex gap-1 mb-5 bg-gray-100 rounded-xl p-1">
            <button type="button" onclick="switchTab('create')" id="tab-create"
                class="flex-1 py-2 text-sm font-semibold rounded-lg transition-all bg-white text-gray-900 shadow-sm">
                Nueva categoría
            </button>
            <button type="button" onclick="switchTab('manage')" id="tab-manage"
                class="flex-1 py-2 text-sm font-semibold rounded-lg transition-all text-gray-500 hover:text-gray-700">
                Gestionar
            </button>
        </div>

        <button type="button" onclick="closeCategoryModal()"
            class="absolute top-5 right-5 w-8 h-8 flex items-center justify-center rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-all">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>

        {{-- Tab: Crear --}}
        <div id="tab-panel-create">
            <p class="text-sm text-gray-500 mb-4">Se creará y seleccionará automáticamente</p>

            <div id="modal-error" class="hidden mb-4 bg-red-50 border border-red-200 rounded-xl p-3">
                <p class="text-sm text-red-600" id="modal-error-text"></p>
            </div>

            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Nombre <span class="text-pink-500">*</span></label>
                    <input type="text" id="modal-cat-name" autofocus
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-fuchsia-300"
                        placeholder="Ej: Cabello, Uñas, Pestañas...">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Descripción <span class="text-gray-400 font-normal">(opcional)</span></label>
                    <input type="text" id="modal-cat-description"
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-fuchsia-300"
                        placeholder="Breve descripción...">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Color</label>
                        <div class="flex items-center gap-2">
                            <input type="color" id="modal-cat-color" value="#d946ef"
                                class="w-10 h-10 rounded-lg border border-gray-200 cursor-pointer p-1">
                            <span class="text-sm text-gray-500">Identifica la categoría</span>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Orden</label>
                        <input type="number" id="modal-cat-sort" value="0" min="0"
                            class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-fuchsia-300">
                    </div>
                </div>
            </div>

            <div class="flex gap-3 mt-6">
                <button type="button" onclick="closeCategoryModal()"
                    class="flex-1 py-2.5 border border-gray-200 text-gray-600 font-semibold rounded-xl hover:bg-gray-50 transition-colors text-sm">
                    Cancelar
                </button>
                <button type="button" onclick="saveCategory()" id="modal-save-btn"
                    class="flex-1 bg-gradient-to-r from-fuchsia-500 to-pink-600 hover:from-fuchsia-600 hover:to-pink-700 text-white font-semibold py-2.5 rounded-xl shadow-lg shadow-fuchsia-500/30 transition-all text-sm">
                    Crear y seleccionar
                </button>
            </div>
        </div>

        {{-- Tab: Gestionar --}}
        <div id="tab-panel-manage" class="hidden">
            <p class="text-sm text-gray-500 mb-4">Elimina categorías que no tengan servicios asociados.</p>

            <div id="manage-error" class="hidden mb-3 bg-red-50 border border-red-200 rounded-xl p-3">
                <p class="text-sm text-red-600" id="manage-error-text"></p>
            </div>

            <div id="categories-list" class="space-y-2 max-h-72 overflow-y-auto pr-1">
                @foreach($categories as $cat)
                <div class="flex items-center justify-between p-3 border border-gray-100 rounded-xl hover:bg-gray-50 transition-all" id="cat-row-{{ $cat->id }}">
                    <div class="flex items-center gap-2.5">
                        <span class="w-3 h-3 rounded-full shrink-0" style="background-color: {{ $cat->color ?? '#d1d5db' }}"></span>
                        <span class="text-sm font-medium text-gray-800">{{ $cat->name }}</span>
                        @if($cat->services_count ?? $cat->services()->count() > 0)
                            <span class="text-xs text-gray-400">({{ $cat->services()->count() }} servicios)</span>
                        @endif
                    </div>
                    <button type="button"
                        onclick="deleteCategory({{ $cat->id }}, '{{ addslashes($cat->name) }}')"
                        class="w-7 h-7 flex items-center justify-center rounded-lg text-gray-400 hover:text-red-500 hover:bg-red-50 transition-all"
                        title="Eliminar categoría">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    </button>
                </div>
                @endforeach

                @if($categories->count() === 0)
                <p class="text-sm text-gray-400 text-center py-6">No hay categorías creadas aún.</p>
                @endif
            </div>

            <button type="button" onclick="closeCategoryModal()"
                class="w-full mt-5 py-2.5 border border-gray-200 text-gray-600 font-semibold rounded-xl hover:bg-gray-50 transition-colors text-sm">
                Cerrar
            </button>
        </div>
    </div>
</div>

<script>
    // ─── Seña toggle ───────────────────────────────────────────────────────────
    const depositCheck = document.getElementById('toggle-requires_deposit');
    const depositField = document.getElementById('deposit-field');
    depositCheck?.addEventListener('change', () => {
        depositField.classList.toggle('hidden', !depositCheck.checked);
    });
    if (depositCheck?.checked) depositField.classList.remove('hidden');

    // ─── Chips de categoría ────────────────────────────────────────────────────
    function selectCategory(id, name) {
        document.getElementById('service_category_id').value = id;
        document.querySelectorAll('.category-chip').forEach(chip => {
            const isSelected = chip.dataset.id == id;
            chip.classList.toggle('border-fuchsia-400', isSelected);
            chip.classList.toggle('bg-fuchsia-50', isSelected);
            chip.classList.toggle('text-fuchsia-700', isSelected);
            chip.classList.toggle('border-gray-200', !isSelected);
            chip.classList.toggle('bg-gray-50', !isSelected);
            chip.classList.toggle('text-gray-600', !isSelected);
        });
    }

    const selectEl = document.getElementById('service_category_id');
    if (selectEl.value) selectCategory(selectEl.value, '');
    selectEl.addEventListener('change', () => selectCategory(selectEl.value, ''));

    // ─── Tabs del modal ────────────────────────────────────────────────────────
    function switchTab(tab) {
        const panels = { create: 'tab-panel-create', manage: 'tab-panel-manage' };
        const tabs   = { create: 'tab-create',        manage: 'tab-manage'        };

        Object.entries(panels).forEach(([key, panelId]) => {
            const isActive = key === tab;
            document.getElementById(panelId).classList.toggle('hidden', !isActive);
            const tabBtn = document.getElementById(tabs[key]);
            tabBtn.classList.toggle('bg-white', isActive);
            tabBtn.classList.toggle('text-gray-900', isActive);
            tabBtn.classList.toggle('shadow-sm', isActive);
            tabBtn.classList.toggle('text-gray-500', !isActive);
        });

        // Limpiar errores al cambiar pestaña
        document.getElementById('modal-error').classList.add('hidden');
        document.getElementById('manage-error').classList.add('hidden');
    }

    // ─── Modal categoría ───────────────────────────────────────────────────────
    function openCategoryModal() {
        document.getElementById('category-modal').classList.remove('hidden');
        switchTab('create');
        document.getElementById('modal-cat-name').focus();
    }

    function closeCategoryModal() {
        document.getElementById('category-modal').classList.add('hidden');
        document.getElementById('modal-cat-name').value = '';
        document.getElementById('modal-cat-description').value = '';
        document.getElementById('modal-cat-sort').value = '0';
        document.getElementById('modal-cat-color').value = '#d946ef';
    }

    async function saveCategory() {
        const name = document.getElementById('modal-cat-name').value.trim();
        if (!name) { showModalError('El nombre de la categoría es obligatorio.'); return; }

        const btn = document.getElementById('modal-save-btn');
        btn.disabled = true;
        btn.textContent = 'Guardando...';

        try {
            const response = await fetch('{{ route('service-categories.store-ajax') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({
                    name:        name,
                    description: document.getElementById('modal-cat-description').value.trim() || null,
                    color:       document.getElementById('modal-cat-color').value,
                    sort_order:  parseInt(document.getElementById('modal-cat-sort').value) || 0,
                }),
            });

            const json = await response.json();
            if (!response.ok) {
                showModalError(json.errors?.name?.[0] ?? json.message ?? 'Error al crear la categoría.');
                return;
            }

            // Agregar al select principal
            const select = document.getElementById('service_category_id');
            const option = new Option(json.name, json.id, true, true);
            select.appendChild(option);

            // Agregar chip
            const chipsContainer = document.getElementById('category-chips');
            if (chipsContainer) {
                const chip = document.createElement('button');
                chip.type = 'button';
                chip.className = 'category-chip px-2.5 py-1 rounded-lg text-xs font-medium border transition-all border-gray-200 bg-gray-50 text-gray-600 hover:border-fuchsia-300 hover:bg-fuchsia-50 hover:text-fuchsia-700';
                chip.dataset.id = json.id;
                chip.textContent = json.name;
                chip.onclick = () => selectCategory(json.id, json.name);
                chipsContainer.appendChild(chip);
            }

            // Agregar fila en pestaña Gestionar
            const list = document.getElementById('categories-list');
            const row = document.createElement('div');
            row.id = `cat-row-${json.id}`;
            row.className = 'flex items-center justify-between p-3 border border-gray-100 rounded-xl hover:bg-gray-50 transition-all';
            row.innerHTML = `
                <div class="flex items-center gap-2.5">
                    <span class="w-3 h-3 rounded-full shrink-0" style="background-color: ${json.color ?? '#d1d5db'}"></span>
                    <span class="text-sm font-medium text-gray-800">${json.name}</span>
                </div>
                <button type="button" onclick="deleteCategory(${json.id}, '${json.name.replace(/'/g, "\\'")}')"
                    class="w-7 h-7 flex items-center justify-center rounded-lg text-gray-400 hover:text-red-500 hover:bg-red-50 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                </button>`;
            list.appendChild(row);

            selectCategory(json.id, json.name);
            closeCategoryModal();

        } catch (e) {
            showModalError('Error de conexión. Intenta nuevamente.');
        } finally {
            btn.disabled = false;
            btn.textContent = 'Crear y seleccionar';
        }
    }

    // ─── Eliminar categoría ────────────────────────────────────────────────────
    async function deleteCategory(id, name) {
        if (!confirm(`¿Eliminar la categoría "${name}"?\n\nSolo se puede eliminar si no tiene servicios asociados.`)) return;

        try {
            const response = await fetch(`/service-categories/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                },
            });

            const json = await response.json().catch(() => ({}));

            if (!response.ok) {
                showManageError(json.message ?? 'No se pudo eliminar la categoría.');
                return;
            }

            // Quitar fila del listado del modal
            document.getElementById(`cat-row-${id}`)?.remove();

            // Quitar del select principal
            const select = document.getElementById('service_category_id');
            const opt = select.querySelector(`option[value="${id}"]`);
            if (opt) {
                if (select.value == id) select.value = '';
                opt.remove();
            }

            // Quitar chip
            document.querySelector(`.category-chip[data-id="${id}"]`)?.remove();

        } catch (e) {
            showManageError('Error de conexión. Intenta nuevamente.');
        }
    }

    function showModalError(msg) {
        const el = document.getElementById('modal-error');
        document.getElementById('modal-error-text').textContent = msg;
        el.classList.remove('hidden');
    }

    function showManageError(msg) {
        const el = document.getElementById('manage-error');
        document.getElementById('manage-error-text').textContent = msg;
        el.classList.remove('hidden');
    }

    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') closeCategoryModal();
    });

    document.getElementById('modal-cat-name')?.addEventListener('keydown', e => {
        if (e.key === 'Enter') { e.preventDefault(); saveCategory(); }
    });
</script>
@endsection