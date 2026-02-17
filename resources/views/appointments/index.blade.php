@extends('layouts.app')

@section('title', 'Calendario | Dynasty')

@section('content')

{{-- Toast --}}
@if(session('success'))
    <div id="toast" class="fixed top-6 right-6 z-50 flex items-center gap-3 bg-white border border-green-200 text-green-700 px-5 py-4 rounded-2xl shadow-2xl text-sm font-medium">
        <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
            <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
        </div>
        {{ session('success') }}
    </div>
    <script>setTimeout(() => { document.getElementById('toast')?.remove(); }, 3000);</script>
@endif

<div class="flex flex-col lg:flex-row gap-6 h-[calc(100vh-140px)]">

    {{-- Calendario principal --}}
    <div class="flex-1 min-w-0">
        @include('appointments._calendar')
    </div>

    {{-- Panel lateral derecho --}}
    <div class="w-full lg:w-80 flex flex-col gap-4 overflow-y-auto">
        @include('appointments._detail')
        @include('appointments._today')

        {{-- Leyenda --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 px-5 py-4 flex-shrink-0">
            <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-3">Estados</p>
            <div class="grid grid-cols-2 gap-2">
                <div class="flex items-center gap-2">
                    <div class="w-3 h-3 rounded-full bg-amber-400"></div>
                    <span class="text-xs text-gray-600">Pendiente</span>
                </div>
                <div class="flex items-center gap-2">
                    <div class="w-3 h-3 rounded-full bg-blue-500"></div>
                    <span class="text-xs text-gray-600">Confirmada</span>
                </div>
                <div class="flex items-center gap-2">
                    <div class="w-3 h-3 rounded-full bg-green-500"></div>
                    <span class="text-xs text-gray-600">Completada</span>
                </div>
                <div class="flex items-center gap-2">
                    <div class="w-3 h-3 rounded-full bg-red-400"></div>
                    <span class="text-xs text-gray-600">Cancelada</span>
                </div>
            </div>
        </div>
    </div>

</div>

{{-- Modales --}}
@include('appointments._create-modal')
@include('appointments._edit-modal')

{{-- Hidden forms para acciones rápidas --}}
<form id="quick-status-form" action="" method="POST" class="hidden">
    @csrf @method('PUT')
    <input type="hidden" name="client_name"  id="qs-client_name">
    <input type="hidden" name="client_phone" id="qs-client_phone">
    <input type="hidden" name="client_email" id="qs-client_email">
    <input type="hidden" name="service"      id="qs-service">
    <input type="hidden" name="stylist"      id="qs-stylist">
    <input type="hidden" name="date"         id="qs-date">
    <input type="hidden" name="start_time"   id="qs-start_time">
    <input type="hidden" name="end_time"     id="qs-end_time">
    <input type="hidden" name="status"       id="qs-status">
    <input type="hidden" name="notes"        id="qs-notes">
    <input type="hidden" name="color"        id="qs-color">
</form>

<form id="quick-delete-form" action="" method="POST" class="hidden">
    @csrf @method('DELETE')
</form>

@include('appointments._scripts')

@endsection