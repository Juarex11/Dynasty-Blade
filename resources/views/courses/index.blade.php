@extends('layouts.app')

@section('title', 'Cursos | Dynasty')

@section('content')

<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Cursos</h1>
        <p class="text-sm text-gray-500 mt-0.5">Formación y capacitación del equipo</p>
    </div>
    <div class="flex items-center gap-2">
        <a href="{{ route('course-categories.index') }}"
           class="inline-flex items-center gap-2 border border-gray-200 text-gray-600 hover:border-violet-300 hover:text-violet-600 font-medium px-4 py-2.5 rounded-xl transition-all text-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
            </svg>
            Categorías
        </a>
        <a href="{{ route('courses.create') }}"
           class="inline-flex items-center gap-2 bg-gradient-to-r from-violet-500 to-purple-600 hover:from-violet-600 hover:to-purple-700 text-white font-semibold px-4 py-2.5 rounded-xl shadow-lg shadow-violet-500/30 transition-all text-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Nuevo Curso
        </a>
    </div>
</div>

@if(session('success'))
<div class="mb-5 bg-green-50 border border-green-200 rounded-xl p-4 flex items-center gap-3">
    <svg class="w-5 h-5 text-green-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
    </svg>
    <p class="text-sm font-medium text-green-700">{{ session('success') }}</p>
</div>
@endif

{{-- Filtros --}}
<form method="GET" class="mb-6 bg-white border border-gray-200 rounded-2xl p-4">
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
        <div class="col-span-2 sm:col-span-1 relative">
            <svg class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z"/>
            </svg>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Buscar curso..."
                class="w-full pl-9 pr-4 py-2 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-violet-300">
        </div>
        <select name="category" class="px-3 py-2 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-violet-300 bg-white">
            <option value="">Todas las categorías</option>
            @foreach($categories as $cat)
            <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
            @endforeach
        </select>
        <select name="level" class="px-3 py-2 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-violet-300 bg-white">
            <option value="">Todos los niveles</option>
            <option value="basico"     {{ request('level') === 'basico'     ? 'selected' : '' }}>Básico</option>
            <option value="intermedio" {{ request('level') === 'intermedio' ? 'selected' : '' }}>Intermedio</option>
            <option value="avanzado"   {{ request('level') === 'avanzado'   ? 'selected' : '' }}>Avanzado</option>
        </select>
        <div class="flex gap-2">
            <select name="branch" class="flex-1 px-3 py-2 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-violet-300 bg-white">
                <option value="">Todas las sedes</option>
                @foreach($branches as $b)
                <option value="{{ $b->id }}" {{ request('branch') == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
                @endforeach
            </select>
            <button type="submit" class="px-4 py-2 bg-violet-600 hover:bg-violet-700 text-white rounded-xl text-sm font-medium transition-colors">Filtrar</button>
        </div>
    </div>
</form>

{{-- Grid de cursos --}}
@if($courses->isEmpty())
<div class="bg-white rounded-2xl border border-gray-200 p-14 text-center">
    <div class="w-14 h-14 bg-violet-50 rounded-2xl flex items-center justify-center mx-auto mb-4">
        <svg class="w-7 h-7 text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
        </svg>
    </div>
    <p class="text-gray-500 font-medium">No hay cursos registrados</p>
    <a href="{{ route('courses.create') }}" class="text-violet-600 hover:underline text-sm mt-1 inline-block">Crear primer curso</a>
</div>
@else
<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
    @foreach($courses as $course)
    @php
        // Suma de enrolled_count de todas las aperturas de este curso
        $totalOpeningStudents = $course->openings_enrolled_count ?? 0;
    @endphp
    <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden hover:shadow-md hover:border-violet-200 transition-all group">

        {{-- Cover --}}
        <div class="h-36 overflow-hidden relative"
             style="background: linear-gradient(135deg, {{ $course->category->color ?? '#8b5cf6' }}20, {{ $course->category->color ?? '#8b5cf6' }}10)">
            @if($course->cover_image)
            <img src="{{ asset('storage/' . $course->cover_image) }}" alt="{{ $course->name }}"
                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
            @else
            <div class="w-full h-full flex items-center justify-center">
                <svg class="w-12 h-12 opacity-20" fill="none" stroke="{{ $course->category->color ?? '#8b5cf6' }}" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                </svg>
            </div>
            @endif

            {{-- Badge categoría --}}
            <div class="absolute top-2 left-2">
                <span class="px-2 py-0.5 rounded-full text-xs font-semibold text-white"
                      style="background-color: {{ $course->category->color ?? '#8b5cf6' }}">
                    {{ $course->category->name }}
                </span>
            </div>

            {{-- Badge nivel --}}
            <div class="absolute top-2 right-2">
                @php $lc = $course->level_color @endphp
                <span class="px-2 py-0.5 rounded-full text-xs font-semibold
                    {{ $lc === 'green' ? 'bg-green-100 text-green-700' : ($lc === 'amber' ? 'bg-amber-100 text-amber-700' : 'bg-red-100 text-red-700') }}">
                    {{ $course->level_label }}
                </span>
            </div>
        </div>

        <div class="p-4">
            <h3 class="font-bold text-gray-900 mb-1 truncate">{{ $course->name }}</h3>
            @if($course->short_description)
            <p class="text-xs text-gray-500 mb-3 line-clamp-2">{{ $course->short_description }}</p>
            @endif

            {{-- Precio + modalidad --}}
            <div class="flex items-center justify-between text-sm mb-3">
                <span class="font-semibold" style="color: {{ $course->category->color ?? '#8b5cf6' }}">
                    {{ $course->price_display }}
                </span>
                <span class="text-xs px-2 py-0.5 rounded-lg bg-gray-100 text-gray-500 font-medium">
                    {{ $course->modality_label }}
                </span>
            </div>

            {{-- Footer stats --}}
            <div class="flex items-center justify-between pt-3 border-t border-gray-100 text-xs text-gray-500">

                {{-- Estudiantes en aperturas --}}
                <span class="flex items-center gap-1" title="Estudiantes inscritos en aperturas">
                    <svg class="w-3.5 h-3.5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                    <span>
                        {{ $totalOpeningStudents }}
                        @if($course->max_students)
                        <span class="text-gray-400">/ {{ $course->max_students }}</span>
                        @endif
                        alumnos
                    </span>
                </span>

                {{-- Instructores --}}
                <span class="flex items-center gap-1" title="Instructores del equipo">
                    <svg class="w-3.5 h-3.5 text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    {{ $course->instructors_count }} instructor(es)
                </span>

                {{-- Acciones --}}
                <div class="flex gap-2">
                    <a href="{{ route('courses.show', $course) }}" class="text-violet-600 hover:underline font-medium">Ver</a>
                    <a href="{{ route('courses.edit', $course) }}" class="text-gray-500 hover:text-gray-700">Editar</a>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>

@if($courses->hasPages())
<div class="mt-6">{{ $courses->withQueryString()->links() }}</div>
@endif
@endif

@endsection