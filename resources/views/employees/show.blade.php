@extends('layouts.app')

@section('title', $employee->full_name . ' | Dynasty')

@section('content')

<div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4 mb-6">
    <div class="flex items-center gap-3">
        <a href="{{ route('employees.index') }}" class="w-9 h-9 flex items-center justify-center rounded-xl border border-gray-200 hover:border-fuchsia-300 hover:bg-fuchsia-50 text-gray-500 hover:text-fuchsia-600 transition-all flex-shrink-0">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 rounded-2xl overflow-hidden flex-shrink-0">
                @if($employee->photo)
                    <img src="{{ asset('storage/' . $employee->photo) }}" alt="{{ $employee->full_name }}" class="w-full h-full object-cover">
                @else
                    <div class="w-full h-full bg-gradient-to-br from-fuchsia-400 to-pink-500 flex items-center justify-center text-white font-bold text-xl">
                        {{ strtoupper(substr($employee->first_name, 0, 1)) }}
                    </div>
                @endif
            </div>
            <div>
                <div class="flex items-center gap-2 flex-wrap">
                    <h1 class="text-2xl font-bold text-gray-900">{{ $employee->full_name }}</h1>
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold
                        {{ $employee->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                        {{ $employee->is_active ? 'Activo' : 'Inactivo' }}
                    </span>
                </div>
                <p class="text-sm text-gray-500">{{ $employee->position }}</p>
            </div>
        </div>
    </div>
    <div class="flex gap-2">
        <a href="{{ route('employees.schedules', $employee) }}"
           class="inline-flex items-center gap-2 border border-gray-200 text-gray-700 hover:border-pink-300 hover:text-pink-600 font-medium px-4 py-2 rounded-xl transition-all text-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            Horarios
        </a>
        <a href="{{ route('employees.edit', $employee) }}"
           class="inline-flex items-center gap-2 border border-gray-200 text-gray-700 hover:border-fuchsia-300 hover:text-fuchsia-600 font-medium px-4 py-2 rounded-xl transition-all text-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
            </svg>
            Editar
        </a>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

    {{-- Columna izquierda --}}
    <div class="space-y-5">

        {{-- Info personal --}}
        <div class="bg-white rounded-2xl border border-gray-200 p-5 space-y-3">
            <h3 class="font-semibold text-gray-900">Información</h3>
@if($employee->birth_date)
<div>
    <p class="text-xs text-gray-400">Fecha de nacimiento</p>
    <p class="text-sm font-medium text-gray-700">
        {{ $employee->birth_date->format('d/m/Y') }}
        <span class="text-gray-500">
            ({{ $employee->birth_date->age }} años)
        </span>
    </p>
</div>
@endif
            @if($employee->phone)
            <div class="flex items-center gap-3 text-sm text-gray-600">
                <svg class="w-4 h-4 text-fuchsia-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                </svg>
                {{ $employee->phone }}
            </div>
            @endif
            @if($employee->email)
            <div class="flex items-center gap-3 text-sm text-gray-600">
                <svg class="w-4 h-4 text-pink-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
                {{ $employee->email }}
            </div>
            @endif


            <div class="grid grid-cols-2 gap-3 pt-2 border-t border-gray-100">
                <div>
                    <p class="text-xs text-gray-400">Ingreso</p>
                    <p class="text-sm font-medium text-gray-700">{{ $employee->hire_date->format('d/m/Y') }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400">Antigüedad</p>
                    <p class="text-sm font-medium text-gray-700">{{ $employee->tenure }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400">Contrato</p>
                    <p class="text-sm font-medium text-gray-700">{{ ['full_time' => 'Tiempo completo', 'part_time' => 'Medio tiempo', 'freelance' => 'Freelance'][$employee->employment_type] }}</p>
                </div>
                @if($employee->commission_rate)
                <div>
                    <p class="text-xs text-gray-400">Comisión</p>
                    <p class="text-sm font-medium text-gray-700">{{ $employee->commission_rate }}%</p>
                </div>
                @endif
            </div>

            {{-- Redes sociales --}}
            @if($employee->instagram || $employee->tiktok)
            <div class="flex gap-3 pt-2 border-t border-gray-100">
                @if($employee->instagram)
                <a href="https://instagram.com/{{ $employee->instagram }}" target="_blank"
                   class="flex items-center gap-1.5 text-xs text-gray-500 hover:text-pink-600 transition-colors">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                    </svg>
                    {{ $employee->instagram }}
                </a>
                @endif
                @if($employee->tiktok)
                <span class="text-xs text-gray-500 flex items-center gap-1">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M19.59 6.69a4.83 4.83 0 01-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 01-2.88 2.5 2.89 2.89 0 01-2.89-2.89 2.89 2.89 0 012.89-2.89c.28 0 .54.04.79.1V9.01a6.27 6.27 0 00-.79-.05 6.34 6.34 0 00-6.34 6.34 6.34 6.34 0 006.34 6.34 6.34 6.34 0 006.33-6.34V9.74a8.16 8.16 0 004.77 1.52V7.81a4.85 4.85 0 01-1-.12z"/>
                    </svg>
                    {{ $employee->tiktok }}
                </span>
                @endif
            </div>
            @endif
        </div>

        {{-- Sedes --}}
        <div class="bg-white rounded-2xl border border-gray-200 p-5">
            <h3 class="font-semibold text-gray-900 mb-3">Sedes ({{ $employee->branches->count() }})</h3>
            <div class="space-y-2">
                @foreach($employee->branches as $branch)
                <a href="{{ route('branches.show', $branch) }}" class="flex items-center justify-between text-sm hover:text-fuchsia-600 transition-colors py-1">
                    <span class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full {{ $branch->pivot->is_primary ? 'bg-fuchsia-500' : 'bg-gray-300' }}"></span>
                        {{ $branch->name }}
                    </span>
                    @if($branch->pivot->is_primary)
                    <span class="text-xs text-fuchsia-600 font-semibold">Principal</span>
                    @endif
                </a>
                @endforeach
            </div>
        </div>

        {{-- Acceso sistema --}}
        @if($employee->user)
        <div class="bg-fuchsia-50 rounded-2xl border border-fuchsia-200 p-5">
            <h3 class="font-semibold text-fuchsia-900 mb-2">Acceso al sistema</h3>
            <p class="text-sm text-fuchsia-700">{{ $employee->user->email }}</p>
            <span class="inline-block mt-1 px-2 py-0.5 bg-fuchsia-100 text-fuchsia-700 text-xs font-semibold rounded-full">
                {{ $employee->user->role_name }}
            </span>
        </div>
        @endif
    </div>

    {{-- Columna derecha --}}
    <div class="lg:col-span-2 space-y-5">

        {{-- Bio --}}
        @if($employee->bio)
        <div class="bg-white rounded-2xl border border-gray-200 p-5">
            <h3 class="font-semibold text-gray-900 mb-2">Presentación</h3>
            <p class="text-sm text-gray-600 leading-relaxed">{{ $employee->bio }}</p>
        </div>
        @endif

        {{-- Servicios --}}
        <div class="bg-white rounded-2xl border border-gray-200 p-5">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-semibold text-gray-900">Especialidades ({{ $employee->services->count() }})</h3>
            </div>
            @if($employee->services->isEmpty())
                <p class="text-sm text-gray-400 text-center py-4">Sin servicios asignados</p>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    @foreach($employee->services as $service)
                    <a href="{{ route('services.show', $service) }}" class="flex items-center gap-3 p-3 border border-gray-100 rounded-xl hover:border-fuchsia-200 hover:bg-fuchsia-50 transition-all group">
                        <div class="w-10 h-10 rounded-xl overflow-hidden flex-shrink-0 bg-fuchsia-50">
                            @if($service->cover_image)
                                <img src="{{ asset('storage/' . $service->cover_image) }}" alt="" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex items-center justify-center">
                                    <svg class="w-5 h-5 text-fuchsia-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 truncate group-hover:text-fuchsia-700">{{ $service->name }}</p>
                            <div class="flex items-center gap-2">
                                <span class="text-xs text-fuchsia-600">{{ $service->price_display }}</span>
                                <span class="text-xs text-gray-300">·</span>
                                <span class="text-xs text-gray-400 capitalize">{{ $service->pivot->skill_level ?? 'mid' }}</span>
                            </div>
                        </div>
                    </a>
                    @endforeach
                </div>
            @endif
        </div>
{{-- Cursos como Instructor --}}
@php $instructorCourses = $employee->courses->where('pivot.role', 'instructor'); @endphp
@if($instructorCourses->isNotEmpty())
<div class="bg-white rounded-2xl border border-gray-200 p-5">
    <div class="flex items-center justify-between mb-4">
        <h3 class="font-semibold text-gray-900">Cursos como Instructor ({{ $instructorCourses->count() }})</h3>
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
        @foreach($instructorCourses as $course)
        <a href="{{ route('courses.show', $course) }}"
           class="flex items-center gap-3 p-3 border border-gray-100 rounded-xl hover:border-fuchsia-200 hover:bg-fuchsia-50 transition-all group">
            <div class="w-10 h-10 rounded-xl overflow-hidden flex-shrink-0 bg-fuchsia-50 flex items-center justify-center">
                @if($course->cover_image)
                    <img src="{{ asset('storage/' . $course->cover_image) }}" alt="" class="w-full h-full object-cover">
                @else
                    <svg class="w-5 h-5 text-fuchsia-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                @endif
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-medium text-gray-900 truncate group-hover:text-fuchsia-700">
                    {{ $course->name }}
                </p>
                <div class="flex items-center gap-2">
                    @if($course->category)
                        <span class="text-xs text-fuchsia-600">{{ $course->category->name }}</span>
                    @endif
                    <span class="text-xs text-gray-300">·</span>
                    <span class="inline-flex items-center gap-1 text-xs font-semibold text-fuchsia-700 bg-fuchsia-50 px-1.5 py-0.5 rounded-full border border-fuchsia-100">
                        Instructor
                    </span>
                </div>
            </div>
        </a>
        @endforeach
    </div>
</div>
@endif
        {{-- Horarios --}}
        <div class="bg-white rounded-2xl border border-gray-200 p-5">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-semibold text-gray-900">Horarios</h3>
                <a href="{{ route('employees.schedules', $employee) }}" class="text-xs text-fuchsia-600 hover:text-fuchsia-700 font-medium">Editar →</a>
            </div>
            @if($employee->schedules->isEmpty())
                <p class="text-sm text-gray-400 text-center py-4">Sin horarios configurados</p>
            @else
                @php $schedulesByBranch = $employee->schedules->groupBy('branch_id'); @endphp
                @foreach($schedulesByBranch as $branchId => $schedules)
                @php $branchName = $schedules->first()->branch->name ?? 'Sede'; @endphp
                <div class="mb-4">
                    <p class="text-xs font-bold text-gray-400 uppercase mb-2">{{ $branchName }}</p>
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                        @foreach($schedules->sortBy('day_of_week') as $schedule)
                        <div class="flex items-center justify-between p-2 rounded-lg {{ $schedule->is_working ? 'bg-fuchsia-50 border border-fuchsia-100' : 'bg-gray-50 border border-gray-100' }}">
                            <span class="text-xs font-semibold {{ $schedule->is_working ? 'text-fuchsia-700' : 'text-gray-400' }}">
                                {{ substr($schedule->day_name, 0, 3) }}
                            </span>
                            @if($schedule->is_working)
                            <span class="text-xs text-gray-600">{{ $schedule->hours_display }}</span>
                            @else
                            <span class="text-xs text-gray-400">Libre</span>
                            @endif
                        </div>
                        @endforeach
                    </div>
                </div>
                @endforeach
            @endif
        </div>
    </div>
</div>

@endsection