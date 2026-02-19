@extends('layouts.app')

@section('title', $course->name . ' | Dynasty')

@section('content')

<div class="max-w-3xl mx-auto space-y-5">

    {{-- Header --}}
    <div class="flex items-start gap-4">
        <a href="{{ route('courses.index') }}"
           class="w-9 h-9 shrink-0 flex items-center justify-center rounded-xl border border-gray-200 hover:border-violet-300 hover:bg-violet-50 text-gray-500 hover:text-violet-600 transition-all mt-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div class="flex-1 min-w-0">
            <div class="flex flex-wrap items-center gap-2 mb-1">
                @if($course->category)
                <span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-violet-100 text-violet-700">
                    {{ $course->category->name }}
                </span>
                @endif
                <span class="px-2 py-0.5 rounded-full text-xs font-semibold
                    {{ $course->level === 'basico' ? 'bg-green-100 text-green-700' : ($course->level === 'intermedio' ? 'bg-amber-100 text-amber-700' : 'bg-red-100 text-red-500') }}">
                    {{ $course->level_label }}
                </span>
                <span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-700">
                    {{ $course->modality_label }}
                </span>
                @if(!$course->is_active)
                <span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-500">Inactivo</span>
                @endif
                @if($course->is_featured)
                <span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-700">⭐ Destacado</span>
                @endif
            </div>
            <h1 class="text-2xl font-bold text-gray-900">{{ $course->name }}</h1>
            @if($course->short_description)
            <p class="text-sm text-gray-500 mt-1">{{ $course->short_description }}</p>
            @endif
        </div>
        <div class="flex gap-2 shrink-0">
            <a href="{{ route('courses.edit', $course) }}"
               class="inline-flex items-center gap-1.5 px-4 py-2 border border-gray-200 text-gray-600 font-semibold rounded-xl hover:bg-gray-50 transition-colors text-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Editar
            </a>
            <form action="{{ route('courses.destroy', $course) }}" method="POST"
                  onsubmit="return confirm('¿Eliminar este curso? Esta acción no se puede deshacer.')">
                @csrf @method('DELETE')
                <button type="submit"
                    class="inline-flex items-center gap-1.5 px-4 py-2 border border-red-200 text-red-500 font-semibold rounded-xl hover:bg-red-50 transition-colors text-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                    Eliminar
                </button>
            </form>
        </div>
    </div>

    @if(session('success'))
    <div class="bg-green-50 border border-green-200 rounded-xl p-4 flex items-center gap-3">
        <svg class="w-5 h-5 text-green-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
        </svg>
        <p class="text-sm font-medium text-green-700">{{ session('success') }}</p>
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

        {{-- Columna principal --}}
        <div class="lg:col-span-2 space-y-5">

            {{-- Imagen de portada --}}
            @if($course->cover_image)
            <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden">
                <img src="{{ asset('storage/' . $course->cover_image) }}" alt="{{ $course->name }}"
                     class="w-full h-52 object-cover">
            </div>
            @endif

            {{-- Descripción --}}
            @if($course->description)
            <div class="bg-white rounded-2xl border border-gray-200 p-6">
                <h2 class="font-semibold text-gray-900 mb-3 flex items-center gap-2">
                    <svg class="w-4 h-4 text-violet-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Descripción
                </h2>
                <p class="text-sm text-gray-600 leading-relaxed whitespace-pre-line">{{ $course->description }}</p>
            </div>
            @endif

            {{-- Instructores del equipo --}}
            <div class="bg-white rounded-2xl border border-gray-200 p-6">
                <h2 class="font-semibold text-gray-900 mb-4 flex items-center gap-2">
                    <svg class="w-4 h-4 text-violet-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    Instructores del equipo
                    <span class="ml-auto text-xs font-normal text-gray-400">{{ $course->instructors->count() }} instructor(es)</span>
                </h2>
                @if($course->instructors->isEmpty())
                <p class="text-sm text-gray-400 italic">Sin instructores asignados.</p>
                @else
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                    @foreach($course->instructors as $emp)
                    <div class="flex items-center gap-3 p-3 bg-violet-50 border border-violet-100 rounded-xl">
                        <div class="w-9 h-9 rounded-full overflow-hidden shrink-0">
                            @if($emp->photo)
                            <img src="{{ asset('storage/' . $emp->photo) }}" class="w-full h-full object-cover">
                            @else
                            <div class="w-full h-full bg-violet-200 flex items-center justify-center text-violet-700 text-sm font-bold">
                                {{ strtoupper(substr($emp->first_name, 0, 1)) }}
                            </div>
                            @endif
                        </div>
                        <div class="min-w-0">
                            <p class="text-sm font-semibold text-gray-800 truncate">{{ $emp->full_name }}</p>
                            <p class="text-xs text-gray-500 truncate">{{ $emp->position }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>

            {{-- Sedes --}}
            @if($course->branches->isNotEmpty())
            <div class="bg-white rounded-2xl border border-gray-200 p-6">
                <h2 class="font-semibold text-gray-900 mb-4 flex items-center gap-2">
                    <svg class="w-4 h-4 text-violet-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    Disponible en sedes
                </h2>
                <div class="flex flex-wrap gap-2">
                    @foreach($course->branches as $branch)
                    <span class="px-3 py-1.5 bg-gray-50 border border-gray-200 rounded-xl text-sm text-gray-700 font-medium">
                        {{ $branch->name }}
                        @if($branch->district ?? $branch->city)
                        <span class="text-gray-400 font-normal ml-1">· {{ $branch->district ?? $branch->city }}</span>
                        @endif
                    </span>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Aperturas relacionadas --}}
            @if(isset($course->openings) && $course->openings?->isNotEmpty())
            <div class="bg-white rounded-2xl border border-gray-200 p-6">
                <h2 class="font-semibold text-gray-900 mb-4 flex items-center gap-2">
                    <svg class="w-4 h-4 text-violet-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    Aperturas programadas
                </h2>
                <div class="space-y-2">
                    @foreach($course->openings as $opening)
                    @php
                        $sc = $opening->status_color;
                        $sb = ['gray'=>'bg-gray-100 text-gray-500','blue'=>'bg-blue-100 text-blue-600','amber'=>'bg-amber-100 text-amber-600','green'=>'bg-green-100 text-green-600','red'=>'bg-red-100 text-red-500'][$sc] ?? 'bg-gray-100 text-gray-500';
                    @endphp
                    <a href="{{ route('course-openings.show', $opening) }}"
                       class="flex items-center gap-3 p-3 border border-gray-200 rounded-xl hover:border-violet-300 hover:bg-violet-50 transition-all">
                        <span class="px-2 py-0.5 rounded-full text-xs font-semibold {{ $sb }} shrink-0">{{ $opening->status_label }}</span>
                        <span class="text-sm font-medium text-gray-800 flex-1 truncate">{{ $opening->display_name }}</span>
                        <span class="text-xs text-gray-400 shrink-0">{{ $opening->start_date->format('d/m/Y') }}</span>
                    </a>
                    @endforeach
                </div>
            </div>
            @endif

        </div>

        {{-- Columna lateral --}}
        <div class="space-y-5">

            {{-- Precio --}}
            <div class="bg-white rounded-2xl border border-gray-200 p-5">
                <p class="text-xs text-gray-400 uppercase tracking-wide mb-1">Precio</p>
                <p class="text-2xl font-bold text-violet-600">{{ $course->price_display }}</p>
                @if($course->has_certificate)
                <div class="mt-3 flex items-center gap-1.5 text-xs text-green-600 font-medium">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                    </svg>
                    Incluye certificado
                </div>
                @endif
            </div>

            {{-- Detalles --}}
            <div class="bg-white rounded-2xl border border-gray-200 p-5 space-y-3">
                <p class="text-xs text-gray-400 uppercase tracking-wide mb-2">Detalles</p>

                <div class="flex items-center gap-2.5 text-sm">
                    <svg class="w-4 h-4 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17H3a2 2 0 01-2-2V5a2 2 0 012-2h14a2 2 0 012 2v10a2 2 0 01-2 2h-2"/>
                    </svg>
                    <span class="text-gray-500">Modalidad:</span>
                    <span class="font-medium text-gray-800">{{ $course->modality_label }}</span>
                </div>

                <div class="flex items-center gap-2.5 text-sm">
                    <svg class="w-4 h-4 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                    <span class="text-gray-500">Nivel:</span>
                    <span class="font-medium text-gray-800">{{ $course->level_label }}</span>
                </div>

                @if($course->max_students)
                <div class="flex items-center gap-2.5 text-sm">
                    <svg class="w-4 h-4 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    <span class="text-gray-500">Máx. estudiantes:</span>
                    <span class="font-medium text-gray-800">{{ $course->max_students }}</span>
                </div>
                @endif

                @if($course->instructor)
                <div class="flex items-center gap-2.5 text-sm">
                    <svg class="w-4 h-4 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    <span class="text-gray-500">Instructor externo:</span>
                    <span class="font-medium text-gray-800 truncate">{{ $course->instructor }}</span>
                </div>
                @endif
            </div>

            {{-- Acción rápida: crear apertura --}}
            <a href="{{ route('course-openings.create', ['course_id' => $course->id]) }}"
               class="flex items-center justify-center gap-2 w-full bg-gradient-to-r from-violet-500 to-purple-600 hover:from-violet-600 hover:to-purple-700 text-white font-semibold py-3 rounded-xl shadow-lg shadow-violet-500/30 transition-all text-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Nueva apertura de este curso
            </a>

        </div>
    </div>

</div>

@endsection