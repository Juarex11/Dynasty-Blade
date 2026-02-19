@extends('layouts.app')

@section('title', $course->name . ' | Dynasty')

@section('content')

{{-- Header --}}
<div class="flex items-start gap-3 mb-6">
    <a href="{{ route('courses.index') }}"
       class="w-9 h-9 flex items-center justify-center rounded-xl border border-gray-200 hover:border-violet-300 hover:bg-violet-50 text-gray-500 hover:text-violet-600 transition-all shrink-0 mt-0.5">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
    </a>
    <div class="flex-1 min-w-0">
        <div class="flex flex-wrap items-center gap-2 mb-1">
            <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold text-white"
                  style="background-color: {{ $course->category->color ?? '#8b5cf6' }}">
                {{ $course->category->name }}
            </span>
            @php $lc = $course->level_color @endphp
            <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold
                {{ $lc === 'green' ? 'bg-green-100 text-green-700' : ($lc === 'amber' ? 'bg-amber-100 text-amber-700' : 'bg-red-100 text-red-700') }}">
                {{ $course->level_label }}
            </span>
            @if($course->is_featured)
            <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-100 text-amber-700 flex items-center gap-1">
                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                Destacado
            </span>
            @endif
            @if(!$course->is_active)
            <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-500">Inactivo</span>
            @endif
        </div>
        <h1 class="text-2xl font-bold text-gray-900 truncate">{{ $course->name }}</h1>
    </div>
    <a href="{{ route('courses.edit', $course) }}"
       class="shrink-0 inline-flex items-center gap-2 bg-gradient-to-r from-violet-500 to-purple-600 hover:from-violet-600 hover:to-purple-700 text-white font-semibold px-4 py-2.5 rounded-xl shadow-lg shadow-violet-500/30 transition-all text-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
        </svg>
        Editar
    </a>
</div>

@if(session('success'))
<div class="mb-5 bg-green-50 border border-green-200 rounded-xl p-4 flex items-center gap-3">
    <svg class="w-5 h-5 text-green-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
    </svg>
    <p class="text-sm font-medium text-green-700">{{ session('success') }}</p>
</div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

    {{-- Columna principal --}}
    <div class="lg:col-span-2 space-y-5">

        {{-- Cover + descripción --}}
        <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden">
            @if($course->cover_image)
            <div class="h-52 overflow-hidden">
                <img src="{{ asset('storage/' . $course->cover_image) }}" alt="{{ $course->name }}"
                     class="w-full h-full object-cover">
            </div>
            @else
            <div class="h-32 flex items-center justify-center"
                 style="background: linear-gradient(135deg, {{ $course->category->color ?? '#8b5cf6' }}20, {{ $course->category->color ?? '#8b5cf6' }}08)">
                <svg class="w-14 h-14 opacity-20" fill="none" stroke="{{ $course->category->color ?? '#8b5cf6' }}" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                </svg>
            </div>
            @endif

            <div class="p-6">
                @if($course->short_description)
                <p class="text-gray-600 font-medium mb-3">{{ $course->short_description }}</p>
                @endif
                @if($course->description)
                <div class="prose prose-sm text-gray-600 max-w-none">
                    {!! nl2br(e($course->description)) !!}
                </div>
                @else
                <p class="text-gray-400 text-sm italic">Sin descripción detallada.</p>
                @endif
            </div>
        </div>

        {{-- Instructores del equipo --}}
        <div class="bg-white rounded-2xl border border-gray-200 p-6">
            <h2 class="font-semibold text-gray-900 mb-4 flex items-center gap-2">
                <svg class="w-4 h-4 text-violet-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                Instructores del equipo
                <span class="ml-auto text-xs font-normal text-gray-400">{{ $course->instructors->count() }} registrados</span>
            </h2>
            @if($course->instructors->isEmpty())
            <p class="text-sm text-gray-400 italic">Sin instructores asignados del equipo.</p>
            @if($course->instructor)
            <div class="mt-2 flex items-center gap-2 p-3 bg-gray-50 rounded-xl">
                <div class="w-8 h-8 rounded-full bg-violet-100 flex items-center justify-center text-violet-600 text-xs font-bold shrink-0">
                    {{ strtoupper(substr($course->instructor, 0, 1)) }}
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-800">{{ $course->instructor }}</p>
                    <p class="text-xs text-gray-400">Instructor externo</p>
                </div>
            </div>
            @endif
            @else
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                @foreach($course->instructors as $emp)
                <div class="flex items-center gap-2.5 p-2.5 bg-violet-50 border border-violet-100 rounded-xl">
                    <div class="w-8 h-8 rounded-full overflow-hidden shrink-0">
                        @if($emp->photo)
                        <img src="{{ asset('storage/' . $emp->photo) }}" class="w-full h-full object-cover">
                        @else
                        <div class="w-full h-full bg-violet-200 flex items-center justify-center text-violet-700 text-xs font-bold">
                            {{ strtoupper(substr($emp->first_name, 0, 1)) }}
                        </div>
                        @endif
                    </div>
                    <div class="min-w-0">
                        <p class="text-sm font-medium text-gray-800 truncate">{{ $emp->full_name }}</p>
                        <p class="text-xs text-gray-500 truncate">{{ $emp->position }}</p>
                    </div>
                </div>
                @endforeach
            </div>
            @if($course->instructor)
            <div class="mt-2 flex items-center gap-2.5 p-2.5 bg-gray-50 border border-gray-100 rounded-xl">
                <div class="w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center text-gray-600 text-xs font-bold shrink-0">
                    {{ strtoupper(substr($course->instructor, 0, 1)) }}
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-700">{{ $course->instructor }}</p>
                    <p class="text-xs text-gray-400">Instructor externo</p>
                </div>
            </div>
            @endif
            @endif
        </div>

        {{-- Estudiantes inscritos --}}
        <div class="bg-white rounded-2xl border border-gray-200 p-6">
            <h2 class="font-semibold text-gray-900 mb-4 flex items-center gap-2">
                <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
                Estudiantes inscritos
                <span class="ml-auto text-xs font-normal text-gray-400">{{ $course->students->count() }}{{ $course->max_students ? ' / ' . $course->max_students : '' }}</span>
            </h2>
            @if($course->students->isEmpty())
            <p class="text-sm text-gray-400 italic">Sin estudiantes inscritos.</p>
            @else
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                @foreach($course->students as $emp)
                @php
                    $status = $emp->pivot->status ?? 'inscrito';
                    $statusColors = [
                        'inscrito'   => 'bg-blue-50 border-blue-100 text-blue-600',
                        'en_curso'   => 'bg-amber-50 border-amber-100 text-amber-600',
                        'completado' => 'bg-green-50 border-green-100 text-green-600',
                        'abandonado' => 'bg-red-50 border-red-100 text-red-400',
                    ];
                    $sc = $statusColors[$status] ?? 'bg-gray-50 border-gray-100 text-gray-500';
                @endphp
                <div class="flex items-center gap-2.5 p-2.5 border rounded-xl {{ str_contains($sc, 'bg-') ? explode(' ', $sc)[0] : 'bg-gray-50' }} {{ explode(' ', $sc)[1] ?? 'border-gray-100' }}">
                    <div class="w-8 h-8 rounded-full overflow-hidden shrink-0">
                        @if($emp->photo)
                        <img src="{{ asset('storage/' . $emp->photo) }}" class="w-full h-full object-cover">
                        @else
                        <div class="w-full h-full bg-green-100 flex items-center justify-center text-green-700 text-xs font-bold">
                            {{ strtoupper(substr($emp->first_name, 0, 1)) }}
                        </div>
                        @endif
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-sm font-medium text-gray-800 truncate">{{ $emp->full_name }}</p>
                        <p class="text-xs text-gray-400 truncate">{{ $emp->position }}</p>
                    </div>
                    <span class="text-xs font-medium {{ explode(' ', $sc)[2] ?? 'text-gray-500' }} shrink-0 capitalize">
                        {{ str_replace('_', ' ', $status) }}
                    </span>
                </div>
                @endforeach
            </div>
            @endif
        </div>
    </div>

    {{-- Columna lateral --}}
    <div class="space-y-5">

        {{-- Stats --}}
        <div class="bg-white rounded-2xl border border-gray-200 p-5">
            <h2 class="font-semibold text-gray-900 mb-4 text-sm">Resumen</h2>
            <div class="space-y-3">
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-500">Precio</span>
                    <span class="font-semibold text-sm" style="color: {{ $course->category->color ?? '#8b5cf6' }}">{{ $course->price_display }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-500">Duración</span>
                    <span class="font-medium text-sm text-gray-800">{{ $course->duration_display }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-500">Modalidad</span>
                    <span class="font-medium text-sm text-gray-800 capitalize">{{ $course->modality }}</span>
                </div>
                @if($course->max_students)
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-500">Cupos</span>
                    <span class="font-medium text-sm text-gray-800">{{ $course->students->count() }} / {{ $course->max_students }}</span>
                </div>
                @endif
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-500">Certificado</span>
                    <span class="font-medium text-sm {{ $course->has_certificate ? 'text-green-600' : 'text-gray-400' }}">
                        {{ $course->has_certificate ? 'Sí' : 'No' }}
                    </span>
                </div>
            </div>
        </div>

        {{-- Sedes --}}
        <div class="bg-white rounded-2xl border border-gray-200 p-5">
            <h2 class="font-semibold text-gray-900 mb-4 text-sm flex items-center gap-2">
                <svg class="w-4 h-4 text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                Sedes
            </h2>
            @if($course->branches->isEmpty())
            <p class="text-sm text-gray-400 italic">Sin sedes asignadas.</p>
            @else
            <div class="space-y-2">
                @foreach($course->branches as $branch)
                <div class="flex items-center gap-2 p-2.5 bg-gray-50 rounded-xl">
                    <div class="w-2 h-2 rounded-full shrink-0" style="background-color: {{ $course->category->color ?? '#8b5cf6' }}"></div>
                    <div class="min-w-0">
                        <p class="text-sm font-medium text-gray-800 truncate">{{ $branch->name }}</p>
                        @if($branch->district ?? $branch->city)
                        <p class="text-xs text-gray-400">{{ $branch->district ?? $branch->city }}</p>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
            @endif
        </div>

        {{-- Acciones --}}
        <div class="bg-white rounded-2xl border border-gray-200 p-5 space-y-2">
            <a href="{{ route('courses.edit', $course) }}"
               class="w-full flex items-center justify-center gap-2 py-2.5 border border-violet-200 text-violet-600 hover:bg-violet-50 font-medium rounded-xl transition-colors text-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Editar curso
            </a>
            <form action="{{ route('courses.destroy', $course) }}" method="POST"
                  onsubmit="return confirm('¿Eliminar este curso? Esta acción no se puede deshacer.')">
                @csrf @method('DELETE')
                <button type="submit"
                        class="w-full flex items-center justify-center gap-2 py-2.5 border border-red-200 text-red-500 hover:bg-red-50 font-medium rounded-xl transition-colors text-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                    Eliminar curso
                </button>
            </form>
        </div>
    </div>
</div>

@endsection