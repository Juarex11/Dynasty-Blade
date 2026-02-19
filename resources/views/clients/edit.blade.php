@extends('layouts.app')

@section('title', 'Editar ' . $client->full_name . ' | Dynasty')

@section('content')

<div class="max-w-2xl mx-auto">

    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('clients.show', $client) }}" class="w-9 h-9 flex items-center justify-center rounded-xl border border-gray-200 hover:border-violet-300 hover:bg-violet-50 text-gray-500 hover:text-violet-600 transition-all">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Editar cliente</h1>
            <p class="text-sm text-gray-500">{{ $client->full_name }}</p>
        </div>
    </div>

    @if($errors->any())
    <div class="mb-5 bg-red-50 border border-red-200 rounded-xl p-4">
        <ul class="text-sm text-red-600 space-y-1 list-disc list-inside">
            @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('clients.update', $client) }}" method="POST" enctype="multipart/form-data" class="space-y-5">
        @csrf
        @method('PUT')

        {{-- ── Tipo de cliente ─────────────────────────────────────────────── --}}
        <div class="bg-white rounded-2xl border border-gray-200 p-6">
            <h2 class="font-semibold text-gray-900 text-base border-b border-gray-100 pb-3 mb-4">Tipo de cliente</h2>
            <div class="grid grid-cols-2 gap-3">
                <label id="mode-frecuente-label"
                    class="flex flex-col items-center gap-2 p-4 border-2 rounded-xl cursor-pointer transition-all
                           {{ old('client_mode', $client->client_mode) === 'frecuente' ? 'border-violet-400 bg-violet-50' : 'border-gray-200 hover:border-violet-200' }}">
                    <input type="radio" name="client_mode" value="frecuente"
                        {{ old('client_mode', $client->client_mode) === 'frecuente' ? 'checked' : '' }}
                        class="sr-only" onchange="toggleMode('frecuente')">
                    <div class="w-10 h-10 rounded-xl bg-violet-100 flex items-center justify-center">
                        <svg class="w-5 h-5 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                    </div>
                    <div class="text-center">
                        <p class="font-semibold text-gray-900 text-sm">Frecuente</p>
                        <p class="text-xs text-gray-400 mt-0.5">Registra perfil completo, historial y credenciales</p>
                    </div>
                </label>

                <label id="mode-ocasional-label"
                    class="flex flex-col items-center gap-2 p-4 border-2 rounded-xl cursor-pointer transition-all
                           {{ old('client_mode', $client->client_mode) === 'ocasional' ? 'border-blue-400 bg-blue-50' : 'border-gray-200 hover:border-blue-200' }}">
                    <input type="radio" name="client_mode" value="ocasional"
                        {{ old('client_mode', $client->client_mode) === 'ocasional' ? 'checked' : '' }}
                        class="sr-only" onchange="toggleMode('ocasional')">
                    <div class="w-10 h-10 rounded-xl bg-blue-100 flex items-center justify-center">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    </div>
                    <div class="text-center">
                        <p class="font-semibold text-gray-900 text-sm">Ocasional</p>
                        <p class="text-xs text-gray-400 mt-0.5">Solo datos básicos, sin perfil detallado</p>
                    </div>
                </label>
            </div>
        </div>

        {{-- ── Datos básicos (siempre visibles) ───────────────────────────── --}}
        <div class="bg-white rounded-2xl border border-gray-200 p-6 space-y-4">
            <h2 class="font-semibold text-gray-900 text-base border-b border-gray-100 pb-3">Datos básicos</h2>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Nombre <span class="text-pink-500">*</span></label>
                    <input type="text" name="first_name" value="{{ old('first_name', $client->first_name) }}" required
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-violet-300 @error('first_name') border-red-400 @enderror">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Apellido <span class="text-pink-500">*</span></label>
                    <input type="text" name="last_name" value="{{ old('last_name', $client->last_name) }}" required
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-violet-300">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Teléfono / WhatsApp</label>
                    <input type="text" name="phone" value="{{ old('phone', $client->phone) }}"
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-violet-300"
                        placeholder="9XX XXX XXX">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">DNI</label>
                    <input type="text" name="dni" value="{{ old('dni', $client->dni) }}" maxlength="15"
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-violet-300 @error('dni') border-red-400 @enderror"
                        placeholder="12345678">
                </div>
            </div>

            {{-- Estado activo --}}
            <div class="flex items-center gap-3 pt-1">
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" value="1" class="sr-only peer"
                        {{ old('is_active', $client->is_active) ? 'checked' : '' }}>
                    <div class="w-10 h-6 bg-gray-200 peer-focus:ring-2 peer-focus:ring-violet-300 rounded-full peer peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-violet-500"></div>
                </label>
                <span class="text-sm text-gray-700 font-medium">Cliente activo</span>
            </div>
        </div>

        {{-- ── Sección frecuente: Datos personales completos ──────────────── --}}
        <div id="section-frecuente" class="{{ old('client_mode', $client->client_mode) === 'ocasional' ? 'hidden' : '' }} space-y-5">

            {{-- Datos personales extendidos --}}
            <div class="bg-white rounded-2xl border border-gray-200 p-6 space-y-4">
                <h2 class="font-semibold text-gray-900 text-base border-b border-gray-100 pb-3">Datos personales</h2>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Email</label>
                        <input type="email" name="email" value="{{ old('email', $client->email) }}"
                            class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-violet-300 @error('email') border-red-400 @enderror"
                            placeholder="correo@ejemplo.com">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Fecha de nacimiento</label>
                        <input type="date" name="birthdate" value="{{ old('birthdate', $client->birthdate?->format('Y-m-d')) }}" max="{{ date('Y-m-d') }}"
                            class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-violet-300">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Género</label>
                    <div class="flex gap-2 flex-wrap">
                        @foreach(['masculino'=>'Masculino','femenino'=>'Femenino','otro'=>'Otro','no_especifica'=>'Prefiero no decir'] as $val=>$lbl)
                        <label class="flex items-center gap-1.5 px-3 py-2 border border-gray-200 rounded-xl cursor-pointer hover:border-violet-300 transition-colors has-[:checked]:border-violet-400 has-[:checked]:bg-violet-50">
                            <input type="radio" name="gender" value="{{ $val }}" {{ old('gender', $client->gender) === $val ? 'checked' : '' }} class="text-violet-600">
                            <span class="text-sm text-gray-700">{{ $lbl }}</span>
                        </label>
                        @endforeach
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Foto</label>
                    @if($client->photo)
                    <div class="flex items-center gap-3 mb-2">
                        <img src="{{ asset('storage/'.$client->photo) }}" class="w-12 h-12 rounded-xl object-cover border border-gray-200">
                        <p class="text-xs text-gray-500">Foto actual · Sube una nueva para reemplazarla</p>
                    </div>
                    @endif
                    <div class="border-2 border-dashed border-gray-200 rounded-xl p-4 text-center hover:border-violet-300 transition-colors cursor-pointer"
                         onclick="document.getElementById('photo-inp').click()">
                        <p class="text-sm text-gray-400" id="photo-label">Seleccionar nueva foto</p>
                        <input type="file" name="photo" id="photo-inp" accept="image/*" class="hidden"
                               onchange="document.getElementById('photo-label').textContent = this.files[0]?.name ?? 'Seleccionar nueva foto'">
                    </div>
                </div>
            </div>

            {{-- Ubicación --}}
            <div class="bg-white rounded-2xl border border-gray-200 p-6 space-y-4">
                <h2 class="font-semibold text-gray-900 text-base border-b border-gray-100 pb-3">Ubicación</h2>

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
                    <input type="text" name="address" value="{{ old('address', $client->address) }}"
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
                            <option value="{{ $v }}" {{ old('acquisition_source', $client->acquisition_source) === $v ? 'selected' : '' }}>{{ $l }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">¿Quién refirió?</label>
                        <input type="text" name="referred_by" value="{{ old('referred_by', $client->referred_by) }}"
                            class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-violet-300"
                            placeholder="Nombre del referidor">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Tipo de cabello</label>
                    <div class="flex gap-2 flex-wrap">
                        @foreach(['liso'=>'Liso','ondulado'=>'Ondulado','rizado'=>'Rizado','muy_rizado'=>'Muy rizado','otro'=>'Otro'] as $v=>$l)
                        <label class="flex items-center gap-1.5 px-3 py-2 border border-gray-200 rounded-xl cursor-pointer hover:border-violet-300 transition-colors has-[:checked]:border-violet-400 has-[:checked]:bg-violet-50 text-sm">
                            <input type="radio" name="hair_type" value="{{ $v }}" {{ old('hair_type', $client->hair_type) === $v ? 'checked' : '' }} class="text-violet-600">
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
                                {{ in_array($svc, old('services_interest', $client->services_interest ?? [])) ? 'checked' : '' }}
                                class="text-violet-600 w-3.5 h-3.5">
                            {{ $svc }}
                        </label>
                        @endforeach
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Etiquetas <span class="text-gray-400 font-normal">(separadas por coma)</span></label>
                    <input type="text" name="tags" value="{{ old('tags', $client->tags) }}"
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-violet-300"
                        placeholder="novia, vip, cabello dañado...">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Notas internas</label>
                    <textarea name="notes" rows="3"
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-violet-300 resize-none"
                        placeholder="Preferencias, alergias, notas de atención...">{{ old('notes', $client->notes) }}</textarea>
                </div>
            </div>

            {{-- Credenciales de acceso --}}
            <div class="bg-white rounded-2xl border border-gray-200 p-6 space-y-4">
                <div class="flex items-center gap-2 border-b border-gray-100 pb-3">
                    <svg class="w-4 h-4 text-violet-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
                    <h2 class="font-semibold text-gray-900 text-base">Credenciales de acceso <span class="text-gray-400 font-normal text-sm">(opcional)</span></h2>
                </div>
                <p class="text-xs text-gray-400">Para uso futuro en la app del cliente. Dejar en blanco para no cambiar.</p>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Usuario</label>
                        <input type="text" name="username" value="{{ old('username', $client->username) }}" autocomplete="off"
                            class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-violet-300 @error('username') border-red-400 @enderror"
                            placeholder="usuario123">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">
                            Contraseña
                            @if($client->password)
                            <span class="text-xs text-green-600 font-normal ml-1">✓ Ya tiene contraseña</span>
                            @endif
                        </label>
                        <input type="password" name="password" autocomplete="new-password"
                            class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-violet-300"
                            placeholder="{{ $client->password ? 'Dejar vacío para no cambiar' : 'Mínimo 6 caracteres' }}">
                    </div>
                </div>
            </div>

        </div>
        {{-- ── Fin sección frecuente ───────────────────────────────────────── --}}

        {{-- Botones --}}
        <div class="flex gap-3 pb-6">
            <a href="{{ route('clients.show', $client) }}" class="flex-1 text-center py-3 border border-gray-200 text-gray-600 font-semibold rounded-xl hover:bg-gray-50 transition-colors">Cancelar</a>
            <button type="submit" class="flex-1 bg-gradient-to-r from-violet-500 to-purple-600 hover:from-violet-600 hover:to-purple-700 text-white font-semibold py-3 rounded-xl shadow-lg shadow-violet-500/30 transition-all">Guardar cambios</button>
        </div>
    </form>
</div>

<script>
// ── Toggle modo frecuente / ocasional ─────────────────────────────────────────
function toggleMode(mode) {
    const seccion    = document.getElementById('section-frecuente');
    const lblFrec    = document.getElementById('mode-frecuente-label');
    const lblOcas    = document.getElementById('mode-ocasional-label');

    if (mode === 'frecuente') {
        seccion.classList.remove('hidden');
        lblFrec.classList.add('border-violet-400','bg-violet-50');
        lblFrec.classList.remove('border-gray-200');
        lblOcas.classList.remove('border-blue-400','bg-blue-50');
        lblOcas.classList.add('border-gray-200');
    } else {
        seccion.classList.add('hidden');
        lblOcas.classList.add('border-blue-400','bg-blue-50');
        lblOcas.classList.remove('border-gray-200');
        lblFrec.classList.remove('border-violet-400','bg-violet-50');
        lblFrec.classList.add('border-gray-200');
    }
}

// ── Ubigeo ────────────────────────────────────────────────────────────────────
const UBIGEO = {
    departamentos: @json(json_decode(file_get_contents(public_path('ubigeo/departamentos.json')))),
    provincias:    @json(json_decode(file_get_contents(public_path('ubigeo/provincias.json')))),
    distritos:     @json(json_decode(file_get_contents(public_path('ubigeo/distritos.json'))))
};

const selDept = document.getElementById('sel-department');
const selProv = document.getElementById('sel-province');
const selDist = document.getElementById('sel-district');

const oldDept = @json(old('department', $client->department ?? ''));
const oldProv = @json(old('province',   $client->province   ?? ''));
const oldDist = @json(old('district',   $client->district   ?? ''));

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
    loadProvinces(this.options[this.selectedIndex]?.dataset.id ?? null);
});
selProv.addEventListener('change', function () {
    loadDistricts(this.options[this.selectedIndex]?.dataset.id ?? null);
});

loadDepartments();
</script>

@endsection