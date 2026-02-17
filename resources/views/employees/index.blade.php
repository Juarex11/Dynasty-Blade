@extends('layouts.app')

@section('title', 'Empleados | Dynasty')

@section('content')

<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Empleados</h1>
        <p class="text-sm text-gray-500 mt-0.5">Especialistas y equipo Dynasty</p>
    </div>
    <a href="{{ route('employees.create') }}"
       class="inline-flex items-center gap-2 bg-gradient-to-r from-fuchsia-500 to-pink-600 hover:from-fuchsia-600 hover:to-pink-700 text-white font-semibold px-5 py-2.5 rounded-xl shadow-lg shadow-fuchsia-500/30 transition-all duration-200">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Nuevo Empleado
    </a>
</div>

{{-- Filtros --}}
<form method="GET" class="bg-white rounded-2xl border border-gray-200 p-4 mb-5 flex flex-wrap gap-3 items-end">
    <div class="flex-1 min-w-48">
        <label class="block text-xs font-medium text-gray-500 mb-1.5">Buscar</label>
        <input type="text" name="search" value="{{ request('search') }}"
            class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-fuchsia-200"
            placeholder="Nombre, cargo...">
    </div>
    <div class="min-w-40">
        <label class="block text-xs font-medium text-gray-500 mb-1.5">Sede</label>
        <select name="branch" class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-fuchsia-200 bg-white">
            <option value="">Todas</option>
            @foreach($branches as $branch)
                <option value="{{ $branch->id }}" {{ request('branch') == $branch->id ? 'selected' : '' }}>{{ $branch->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="min-w-36">
        <label class="block text-xs font-medium text-gray-500 mb-1.5">Estado</label>
        <select name="status" class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-fuchsia-200 bg-white">
            <option value="">Todos</option>
            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Activos</option>
            <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactivos</option>
        </select>
    </div>
    <button type="submit" class="px-5 py-2 bg-fuchsia-50 text-fuchsia-700 font-medium rounded-xl hover:bg-fuchsia-100 transition-colors text-sm">
        Filtrar
    </button>
    @if(request()->hasAny(['search','branch','status']))
    <a href="{{ route('employees.index') }}" class="px-4 py-2 text-gray-500 hover:text-gray-700 text-sm">Limpiar</a>
    @endif
</form>

@if(session('success'))
<div class="mb-4 flex items-center gap-3 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl text-sm">
    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
    </svg>
    {{ session('success') }}
</div>
@endif

@if($employees->isEmpty())
    <div class="bg-white rounded-2xl border border-gray-200 p-12 text-center">
        <div class="w-16 h-16 bg-fuchsia-50 rounded-2xl flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-fuchsia-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
        </div>
        <p class="text-gray-500 font-medium">No se encontraron empleados</p>
        <a href="{{ route('employees.create') }}" class="mt-3 inline-block text-fuchsia-600 hover:text-fuchsia-700 font-semibold text-sm">
            + Agregar primer empleado
        </a>
    </div>
@else
    {{-- Tabla --}}
    <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-100 bg-gray-50/50">
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Empleado</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider hidden sm:table-cell">Cargo</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider hidden md:table-cell">Sedes</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider hidden lg:table-cell">Servicios</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider hidden lg:table-cell">Ingreso</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Estado</th>
                        <th class="px-6 py-4 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($employees as $employee)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full overflow-hidden flex-shrink-0">
                                    @if($employee->photo)
                                        <img src="{{ asset('storage/' . $employee->photo) }}" alt="{{ $employee->full_name }}" class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full bg-gradient-to-br from-fuchsia-100 to-pink-100 flex items-center justify-center text-fuchsia-600 font-bold text-sm">
                                            {{ strtoupper(substr($employee->first_name, 0, 1)) }}
                                        </div>
                                    @endif
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-gray-900">{{ $employee->full_name }}</p>
                                    @if($employee->user)
                                    <span class="text-xs text-fuchsia-600 flex items-center gap-1">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                        </svg>
                                        Acceso al sistema
                                    </span>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 hidden sm:table-cell">
                            <p class="text-sm text-gray-700">{{ $employee->position }}</p>
                            <p class="text-xs text-gray-400">{{ ['full_time' => 'Tiempo completo', 'part_time' => 'Medio tiempo', 'freelance' => 'Freelance'][$employee->employment_type] }}</p>
                        </td>
                        <td class="px-6 py-4 hidden md:table-cell">
                            <div class="flex flex-wrap gap-1">
                                @foreach($employee->branches->take(2) as $branch)
                                <span class="px-2 py-0.5 bg-gray-100 text-gray-600 text-xs rounded-full">{{ $branch->name }}</span>
                                @endforeach
                                @if($employee->branches->count() > 2)
                                <span class="px-2 py-0.5 bg-gray-100 text-gray-500 text-xs rounded-full">+{{ $employee->branches->count() - 2 }}</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 hidden lg:table-cell">
                            <span class="text-sm text-gray-600">{{ $employee->services_count }} servicios</span>
                        </td>
                        <td class="px-6 py-4 hidden lg:table-cell">
                            <p class="text-sm text-gray-600">{{ $employee->hire_date->format('d/m/Y') }}</p>
                            <p class="text-xs text-gray-400">{{ $employee->tenure }}</p>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold
                                {{ $employee->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                                {{ $employee->is_active ? 'Activo' : 'Inactivo' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-1">
                                <a href="{{ route('employees.show', $employee) }}"
                                   class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-500 hover:text-fuchsia-600 hover:bg-fuchsia-50 transition-all"
                                   title="Ver perfil">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </a>
                                <a href="{{ route('employees.edit', $employee) }}"
                                   class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-500 hover:text-fuchsia-600 hover:bg-fuchsia-50 transition-all"
                                   title="Editar">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </a>
                                <a href="{{ route('employees.schedules', $employee) }}"
                                   class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-500 hover:text-pink-600 hover:bg-pink-50 transition-all"
                                   title="Horarios">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    @if($employees->hasPages())
    <div class="mt-5">{{ $employees->links() }}</div>
    @endif
@endif

@endsection