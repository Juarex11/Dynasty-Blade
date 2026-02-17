@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto py-6">

    <h1 class="text-2xl font-bold mb-4">
        {{ $course->name }}
    </h1>

    <p class="text-gray-600 mb-3">
        Categoría: {{ $course->category?->name ?? '—' }}
    </p>

    <p class="text-gray-600 mb-3">
        Modalidad: {{ ucfirst($course->modality) }}
    </p>

    <p class="text-gray-600 mb-6">
        Nivel: {{ ucfirst($course->level) }}
    </p>

    <h2 class="text-lg font-semibold mb-2">Sucursales</h2>
    <ul class="list-disc pl-6 mb-6">
        @foreach($course->branches as $branch)
            <li>{{ $branch->name }}</li>
        @endforeach
    </ul>

    <h2 class="text-lg font-semibold mb-2">Instructores</h2>
    <ul class="list-disc pl-6 mb-6">
        @foreach($course->instructors as $instructor)
            <li>{{ $instructor->full_name }}</li>
        @endforeach
    </ul>

    <h2 class="text-lg font-semibold mb-2">Estudiantes</h2>
    <ul class="list-disc pl-6">
        @foreach($course->students as $student)
            <li>{{ $student->full_name }}</li>
        @endforeach
    </ul>

</div>
@endsection
