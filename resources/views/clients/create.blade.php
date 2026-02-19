@extends('layouts.app')

@section('title', 'Nuevo Cliente | Dynasty')

@section('content')

<div class="max-w-2xl mx-auto">

    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('clients.index') }}" class="w-9 h-9 flex items-center justify-center rounded-xl border border-gray-200 hover:border-violet-300 hover:bg-violet-50 text-gray-500 hover:text-violet-600 transition-all">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Nuevo Cliente</h1>
            <p class="text-sm text-gray-500">Registra la información del cliente</p>
        </div>
    </div>

    @if($errors->any())
    <div class="mb-5 bg-red-50 border border-red-200 rounded-xl p-4">
        <ul class="text-sm text-red-600 space-y-1 list-disc list-inside">
            @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('clients.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
        @csrf

        {{-- Datos personales --}}
        <div class="bg-white rounded-2xl border border-gray-200 p-6 space-y-4">
            <h2 class="font-semibold text-gray-900 text-base border-b border-gray-100 pb-3">Datos personales</h2>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Nombre <span class="text-pink-500">*</span></label>
                    <input type="text" name="first_name" value="{{ old('first_name') }}" required
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-violet-300 @error('first_name') border-red-400 @enderror"
                        placeholder="Nombre">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Apellido <span class="text-pink-500">*</span></label>
                    <input type="text" name="last_name" value="{{ old('last_name') }}" required
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-violet-300"
                        placeholder="Apellido">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">DNI</label>
                    <input type="text" name="dni" value="{{ old('dni') }}" maxlength="15"
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-violet-300 @error('dni') border-red-400 @enderror"
                        placeholder="12345678">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Fecha de nacimiento</label>
                    <input type="date" name="birthdate" value="{{ old('birthdate') }}" max="{{ date('Y-m-d') }}"
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-violet-300">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Teléfono / WhatsApp</label>
                    <input type="text" name="phone" value="{{ old('phone') }}"
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-violet-300"
                        placeholder="9XX XXX XXX">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}"
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-violet-300 @error('email') border-red-400 @enderror"
                        placeholder="correo@ejemplo.com">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Género</label>
                <div class="flex gap-2 flex-wrap">
                    @foreach(['masculino'=>'Masculino','femenino'=>'Femenino','otro'=>'Otro','no_especifica'=>'Prefiero no decir'] as $val=>$lbl)
                    <label class="flex items-center gap-1.5 px-3 py-2 border border-gray-200 rounded-xl cursor-pointer hover:border-violet-300 transition-colors has-[:checked]:border-violet-400 has-[:checked]:bg-violet-50">
                        <input type="radio" name="gender" value="{{ $val }}" {{ old('gender') === $val ? 'checked' : '' }} class="text-violet-600">
                        <span class="text-sm text-gray-700">{{ $lbl }}</span>
                    </label>
                    @endforeach
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Foto</label>
                <div class="border-2 border-dashed border-gray-200 rounded-xl p-4 text-center hover:border-violet-300 transition-colors cursor-pointer"
                     onclick="document.getElementById('photo-inp').click()">
                    <svg class="w-7 h-7 text-gray-300 mx-auto mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    <p class="text-sm text-gray-400" id="photo-label">Seleccionar foto</p>
                    <input type="file" name="photo" id="photo-inp" accept="image/*" class="hidden"
                           onchange="document.getElementById('photo-label').textContent = this.files[0]?.name ?? 'Seleccionar foto'">
                </div>
            </div>
        </div>

        {{-- Ubicación (API Perú) --}}
        <div class="bg-white rounded-2xl border border-gray-200 p-6 space-y-4">
            <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                <h2 class="font-semibold text-gray-900 text-base">Ubicación</h2>
                <span class="text-xs text-gray-400 flex items-center gap-1">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    API Regiones Perú
                </span>
            </div>

            <div class="grid grid-cols-3 gap-3">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Departamento</label>
                    <select id="sel-department" name="department"
                        class="w-full px-3 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-violet-300 bg-white">
                        <option value="">Cargando...</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Provincia</label>
                    <select id="sel-province" name="province" disabled
                        class="w-full px-3 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-violet-300 bg-white disabled:opacity-50">
                        <option value="">Seleccionar...</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Distrito</label>
                    <select id="sel-district" name="district" disabled
                        class="w-full px-3 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-violet-300 bg-white disabled:opacity-50">
                        <option value="">Seleccionar...</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Dirección</label>
                <input type="text" name="address" value="{{ old('address') }}"
                    class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-violet-300"
                    placeholder="Av. / Calle / Jr. ...">
            </div>
        </div>

        {{-- Marketing --}}
        <div class="bg-white rounded-2xl border border-gray-200 p-6 space-y-4">
            <h2 class="font-semibold text-gray-900 text-base border-b border-gray-100 pb-3">Marketing & perfil</h2>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">¿Cómo nos conoció?</label>
                    <select name="acquisition_source" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-violet-300 bg-white">
                        <option value="">Seleccionar...</option>
                        @foreach(['instagram'=>'📸 Instagram','facebook'=>'👥 Facebook','tiktok'=>'🎵 TikTok','google'=>'🔍 Google','referido'=>'🤝 Referido','walk_in'=>'🚶 Walk-in','whatsapp'=>'💬 WhatsApp','otro'=>'Otro'] as $v=>$l)
                        <option value="{{ $v }}" {{ old('acquisition_source') === $v ? 'selected' : '' }}>{{ $l }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">¿Quién refirió? <span class="text-gray-400 font-normal">(nombre)</span></label>
                    <input type="text" name="referred_by" value="{{ old('referred_by') }}"
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-violet-300"
                        placeholder="Nombre del referidor">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Tipo de cabello</label>
                <div class="flex gap-2 flex-wrap">
                    @foreach(['liso'=>'Liso','ondulado'=>'Ondulado','rizado'=>'Rizado','muy_rizado'=>'Muy rizado','otro'=>'Otro'] as $v=>$l)
                    <label class="flex items-center gap-1.5 px-3 py-2 border border-gray-200 rounded-xl cursor-pointer hover:border-violet-300 transition-colors has-[:checked]:border-violet-400 has-[:checked]:bg-violet-50 text-sm">
                        <input type="radio" name="hair_type" value="{{ $v }}" {{ old('hair_type') === $v ? 'checked' : '' }} class="text-violet-600">
                        {{ $l }}
                    </label>
                    @endforeach
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Servicios de interés</label>
                <div class="flex flex-wrap gap-2">
                    @foreach(['Corte','Color / Tinte','Mechas / Balayage','Alisado','Ondulado / Permanente','Tratamientos','Peinado','Manicure','Depilación','Maquillaje'] as $svc)
                    <label class="flex items-center gap-1.5 px-2.5 py-1.5 border border-gray-200 rounded-lg cursor-pointer hover:border-violet-300 transition-colors has-[:checked]:border-violet-400 has-[:checked]:bg-violet-50 text-xs">
                        <input type="checkbox" name="services_interest[]" value="{{ $svc }}"
                            {{ in_array($svc, old('services_interest', [])) ? 'checked' : '' }}
                            class="text-violet-600 w-3.5 h-3.5">
                        {{ $svc }}
                    </label>
                    @endforeach
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Etiquetas <span class="text-gray-400 font-normal">(separadas por coma)</span></label>
                <input type="text" name="tags" value="{{ old('tags') }}"
                    class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-violet-300"
                    placeholder="novia, vip, cabello dañado...">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Notas internas</label>
                <textarea name="notes" rows="3"
                    class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-violet-300 resize-none"
                    placeholder="Preferencias, alergias, notas de atención...">{{ old('notes') }}</textarea>
            </div>
        </div>

        {{-- Inscripción a curso --}}
        @if($openings->isNotEmpty())
        <div class="bg-white rounded-2xl border border-gray-200 p-6 space-y-4">
            <div class="flex items-center gap-2 border-b border-gray-100 pb-3">
                <svg class="w-4 h-4 text-violet-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                </svg>
                <h2 class="font-semibold text-gray-900 text-base">Inscribir a un curso <span class="text-gray-400 font-normal text-sm">(opcional)</span></h2>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Apertura de curso disponible</label>
                <select name="enroll_opening_id" id="enroll-opening"
                    class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-violet-300 bg-white">
                    <option value="">No inscribir ahora</option>
                    @foreach($openings as $opening)
                    <option value="{{ $opening->id }}"
                        data-price="{{ $opening->effective_price ?? '' }}"
                        {{ old('enroll_opening_id') == $opening->id ? 'selected' : '' }}>
                        {{ $opening->display_name }} — {{ $opening->start_date->format('d/m/Y') }}
                        @if($opening->branch) · {{ $opening->branch->name }}@endif
                    </option>
                    @endforeach
                </select>
            </div>

            <div id="enroll-details" class="{{ old('enroll_opening_id') ? '' : 'hidden' }} grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Precio pagado (S/.)</label>
                    <input type="number" name="enroll_price_paid" id="enroll-price" value="{{ old('enroll_price_paid') }}" min="0" step="0.01"
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-violet-300"
                        placeholder="0.00">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Estado de pago</label>
                    <select name="enroll_payment_status" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-violet-300 bg-white">
                        <option value="pendiente" {{ old('enroll_payment_status') === 'pendiente' ? 'selected':'' }}>Pendiente</option>
                        <option value="pagado"    {{ old('enroll_payment_status') === 'pagado'    ? 'selected':'' }}>Pagado</option>
                        <option value="parcial"   {{ old('enroll_payment_status') === 'parcial'   ? 'selected':'' }}>Pago parcial</option>
                        <option value="becado"    {{ old('enroll_payment_status') === 'becado'    ? 'selected':'' }}>Becado</option>
                    </select>
                </div>
            </div>
        </div>
        @endif

        <div class="flex gap-3 pb-6">
            <a href="{{ route('clients.index') }}" class="flex-1 text-center py-3 border border-gray-200 text-gray-600 font-semibold rounded-xl hover:bg-gray-50 transition-colors">Cancelar</a>
            <button type="submit" class="flex-1 bg-gradient-to-r from-violet-500 to-purple-600 hover:from-violet-600 hover:to-purple-700 text-white font-semibold py-3 rounded-xl shadow-lg shadow-violet-500/30 transition-all">Registrar Cliente</button>
        </div>
    </form>
</div>

<script>
const UBIGEO = {
    departamentos: @json(json_decode(file_get_contents(public_path('ubigeo/departamentos.json')))),
    provincias:    @json(json_decode(file_get_contents(public_path('ubigeo/provincias.json')))),
    distritos:     @json(json_decode(file_get_contents(public_path('ubigeo/distritos.json'))))
};

const selDept = document.getElementById('sel-department');
const selProv = document.getElementById('sel-province');
const selDist = document.getElementById('sel-district');

const oldDept = @json(old('department', ''));
const oldProv = @json(old('province', ''));
const oldDist = @json(old('district', ''));

function loadDepartments() {
    selDept.innerHTML = '<option value="">Seleccionar departamento</option>';
    UBIGEO.departamentos.forEach(d => {
        const opt = new Option(d.nombre_ubigeo, d.nombre_ubigeo);
        opt.dataset.id = d.id_ubigeo;
        if (d.nombre_ubigeo === oldDept) opt.selected = true;
        selDept.appendChild(opt);
    });
    if (oldDept) {
        const dept = UBIGEO.departamentos.find(d => d.nombre_ubigeo === oldDept);
        if (dept) loadProvinces(dept.id_ubigeo);
    }
}

function loadProvinces(deptId) {
    selProv.innerHTML = '<option value="">Seleccionar provincia</option>';
    selDist.innerHTML = '<option value="">Seleccionar...</option>';
    selProv.disabled = selDist.disabled = true;
    if (!deptId) return;

    const list = UBIGEO.provincias[deptId] ?? [];
    list.forEach(p => {
        const opt = new Option(p.nombre_ubigeo, p.nombre_ubigeo);
        opt.dataset.id = p.id_ubigeo;
        if (p.nombre_ubigeo === oldProv) opt.selected = true;
        selProv.appendChild(opt);
    });
    selProv.disabled = false;

    if (oldProv) {
        const prov = list.find(p => p.nombre_ubigeo === oldProv);
        if (prov) loadDistricts(prov.id_ubigeo);
    }
}

function loadDistricts(provId) {
    selDist.innerHTML = '<option value="">Seleccionar distrito</option>';
    selDist.disabled = true;
    if (!provId) return;

    const list = UBIGEO.distritos[provId] ?? [];
    list.forEach(d => {
        const opt = new Option(d.nombre_ubigeo, d.nombre_ubigeo);
        if (d.nombre_ubigeo === oldDist) opt.selected = true;
        selDist.appendChild(opt);
    });
    selDist.disabled = false;
}

selDept.addEventListener('change', function () {
    const opt = this.options[this.selectedIndex];
    loadProvinces(opt?.dataset.id ?? null);
});

selProv.addEventListener('change', function () {
    const opt = this.options[this.selectedIndex];
    loadDistricts(opt?.dataset.id ?? null);
});

loadDepartments();

// ─── Inscripción a curso ──────────────────────────────────────────────────────
const enrollSelect  = document.getElementById('enroll-opening');
const enrollDetails = document.getElementById('enroll-details');
const enrollPrice   = document.getElementById('enroll-price');

if (enrollSelect) {
    enrollSelect.addEventListener('change', function () {
        const opt = this.options[this.selectedIndex];
        if (this.value) {
            enrollDetails.classList.remove('hidden');
            if (opt.dataset.price) enrollPrice.value = opt.dataset.price;
        } else {
            enrollDetails.classList.add('hidden');
        }
    });
}
</script>
@endsection