@extends('layouts.app')

@section('title', 'Editar Empleado | Dynasty')

@section('content')

<div class="max-w-2xl mx-auto">

    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('employees.show', $employee) }}" class="w-9 h-9 flex items-center justify-center rounded-xl border border-gray-200 hover:border-fuchsia-300 hover:bg-fuchsia-50 text-gray-500 hover:text-fuchsia-600 transition-all">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Editar Empleado</h1>
            <p class="text-sm text-gray-500">{{ $employee->full_name }}</p>
        </div>
    </div>

    @if($errors->any())
    <div class="mb-5 bg-red-50 border border-red-200 rounded-xl p-4">
        <ul class="text-sm text-red-600 space-y-1 list-disc list-inside">
            @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('employees.update', $employee) }}" method="POST" enctype="multipart/form-data" class="space-y-5">
        @csrf @method('PUT')

        {{-- Datos personales --}}
        <div class="bg-white rounded-2xl border border-gray-200 p-6 space-y-4">
            <h2 class="font-semibold text-gray-900 text-base border-b border-gray-100 pb-3">Datos personales</h2>

            {{-- Foto actual --}}
            @if($employee->photo)
            <div class="flex items-center gap-4">
                <div class="w-16 h-16 rounded-2xl overflow-hidden flex-shrink-0">
                    <img src="{{ asset('storage/' . $employee->photo) }}" alt="{{ $employee->full_name }}" class="w-full h-full object-cover">
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-700">Foto actual</p>
                    <label class="text-xs text-fuchsia-600 cursor-pointer hover:text-fuchsia-700" for="emp-photo">Cambiar foto</label>
                </div>
            </div>
            @endif

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Nombre <span class="text-pink-500">*</span></label>
                    <input type="text" name="first_name" value="{{ old('first_name', $employee->first_name) }}" required
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-fuchsia-300">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Apellido <span class="text-pink-500">*</span></label>
                    <input type="text" name="last_name" value="{{ old('last_name', $employee->last_name) }}" required
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-fuchsia-300">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Teléfono</label>
                    <input type="text" name="phone" value="{{ old('phone', $employee->phone) }}"
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-fuchsia-300">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Email</label>
                    <input type="email" name="email" value="{{ old('email', $employee->email) }}"
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-fuchsia-300">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ $employee->photo ? 'Cambiar foto' : 'Foto de perfil' }}</label>
                <div class="border-2 border-dashed border-gray-200 rounded-xl p-4 text-center hover:border-fuchsia-300 transition-colors cursor-pointer" onclick="document.getElementById('emp-photo').click()">
                    <p class="text-sm text-gray-500">Seleccionar nueva foto</p>
                    <input type="file" name="photo" id="emp-photo" accept="image/*" class="hidden">
                </div>
            </div>
        </div>

        {{-- Datos laborales --}}
        <div class="bg-white rounded-2xl border border-gray-200 p-6 space-y-4">
            <h2 class="font-semibold text-gray-900 text-base border-b border-gray-100 pb-3">Datos laborales</h2>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Cargo <span class="text-pink-500">*</span></label>
                    <input type="text" name="position" value="{{ old('position', $employee->position) }}" required
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-fuchsia-300">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Tipo de contrato</label>
                    <select name="employment_type" required class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-fuchsia-300 bg-white">
                        <option value="full_time" {{ old('employment_type', $employee->employment_type) === 'full_time' ? 'selected' : '' }}>Tiempo completo</option>
                        <option value="part_time" {{ old('employment_type', $employee->employment_type) === 'part_time' ? 'selected' : '' }}>Medio tiempo</option>
                        <option value="freelance" {{ old('employment_type', $employee->employment_type) === 'freelance' ? 'selected' : '' }}>Freelance</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Fecha de ingreso <span class="text-pink-500">*</span></label>
                    <input type="date" name="hire_date" value="{{ old('hire_date', $employee->hire_date->format('Y-m-d')) }}" required
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-fuchsia-300">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">% Comisión</label>
                    <input type="number" name="commission_rate" value="{{ old('commission_rate', $employee->commission_rate) }}" min="0" max="100" step="0.5"
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-fuchsia-300">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Bio / Presentación</label>
                <textarea name="bio" rows="3" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-fuchsia-300 resize-none">{{ old('bio', $employee->bio) }}</textarea>
            </div>
        </div>

        {{-- Sedes --}}
        <div class="bg-white rounded-2xl border border-gray-200 p-6 space-y-4">
            <h2 class="font-semibold text-gray-900 text-base border-b border-gray-100 pb-3">Sedes donde trabaja <span class="text-pink-500">*</span></h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                @foreach($branches as $branch)
                <label class="flex items-center gap-3 p-3 border border-gray-200 rounded-xl cursor-pointer hover:border-fuchsia-300 hover:bg-fuchsia-50 transition-all has-[:checked]:border-fuchsia-400 has-[:checked]:bg-fuchsia-50">
                    <input type="checkbox" name="branch_ids[]" value="{{ $branch->id }}"
                        {{ in_array($branch->id, old('branch_ids', $employee->branches->pluck('id')->toArray())) ? 'checked' : '' }}
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
                    <option value="">Seleccionar...</option>
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
                                {{ in_array($service->id, old('service_ids', $employee->services->pluck('id')->toArray())) ? 'checked' : '' }}
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

        {{-- Estado --}}
        <div class="bg-white rounded-2xl border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="font-medium text-gray-900 text-sm">Empleado activo</p>
                    <p class="text-xs text-gray-400 mt-0.5">Si está inactivo no aparecerá disponible para citas</p>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $employee->is_active) ? 'checked' : '' }} class="sr-only peer">
                    <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:after:translate-x-full after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-gradient-to-r peer-checked:from-fuchsia-500 peer-checked:to-pink-600"></div>
                </label>
            </div>
        </div>

        <div class="flex gap-3 pb-6">
            <a href="{{ route('employees.show', $employee) }}"
               class="flex-1 text-center py-3 border border-gray-200 text-gray-600 font-semibold rounded-xl hover:bg-gray-50 transition-colors">
                Cancelar
            </a>
            <button type="submit"
                class="flex-1 bg-gradient-to-r from-fuchsia-500 to-pink-600 hover:from-fuchsia-600 hover:to-pink-700 text-white font-semibold py-3 rounded-xl shadow-lg shadow-fuchsia-500/30 transition-all duration-200">
                Guardar cambios
            </button>
        </div>
    </form>
</div>

<script>
const checkboxes = document.querySelectorAll('.branch-checkbox');
const primarySelector = document.getElementById('primary-branch-selector');
const primarySelect = document.getElementById('primary-branch-select');
const currentPrimary = "{{ optional($employee->branches->where('pivot.is_primary', true)->first())->id }}";

function updatePrimaryBranches() {
    const checked = [...checkboxes].filter(c => c.checked);
    primarySelector.classList.toggle('hidden', checked.length === 0);
    const currentValue = primarySelect.value || currentPrimary;
    primarySelect.innerHTML = '<option value="">Seleccionar...</option>';
    checked.forEach(c => {
        const opt = document.createElement('option');
        opt.value = c.dataset.branchId;
        opt.textContent = c.dataset.branchName;
        if (c.dataset.branchId === currentValue) opt.selected = true;
        primarySelect.appendChild(opt);
    });
    if (checked.length === 1) primarySelect.value = checked[0].dataset.branchId;
}

checkboxes.forEach(c => c.addEventListener('change', updatePrimaryBranches));
updatePrimaryBranches();
</script>

@endsection