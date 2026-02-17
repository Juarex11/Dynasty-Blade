{{-- resources/views/appointments/_detail.blade.php --}}

{{-- Panel vacío (default) --}}
<div id="empty-panel" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex flex-col items-center justify-center text-center flex-shrink-0" style="min-height: 200px">
    <div class="w-14 h-14 bg-fuchsia-50 rounded-2xl flex items-center justify-center mb-3">
        <svg class="w-7 h-7 text-fuchsia-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
        </svg>
    </div>
    <p class="text-sm font-semibold text-gray-600">Selecciona una cita</p>
    <p class="text-xs text-gray-400 mt-1 leading-relaxed">Haz clic en una cita del calendario para ver sus detalles aquí</p>
</div>

{{-- Panel de detalle (oculto por defecto) --}}
<div id="detail-panel" class="hidden bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden flex-shrink-0">

    {{-- Header con color de la cita --}}
    <div id="detail-header" class="relative p-5" style="background: linear-gradient(135deg, #a855f7, #ec4899)">
        {{-- Patrón decorativo --}}
        <div class="absolute inset-0 opacity-10">
            <div class="absolute top-0 right-0 w-24 h-24 bg-white rounded-full -translate-y-8 translate-x-8"></div>
            <div class="absolute bottom-0 left-0 w-16 h-16 bg-white rounded-full translate-y-6 -translate-x-6"></div>
        </div>

        <div class="relative flex items-start justify-between">
            <div class="flex-1 min-w-0">
                <div class="flex items-center gap-2 mb-1">
                    <span class="text-white/70 text-xs font-semibold uppercase tracking-wider">Cita Seleccionada</span>
                </div>
                <h3 id="detail-name" class="text-white text-lg font-bold truncate"></h3>
                <p id="detail-service" class="text-white/85 text-sm mt-0.5 truncate"></p>
            </div>
            <button onclick="closeDetail()"
                class="flex-shrink-0 ml-2 w-8 h-8 flex items-center justify-center bg-white/20 hover:bg-white/30 rounded-lg transition-colors">
                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    </div>

    {{-- Info rows --}}
    <div class="p-5 space-y-3">

        {{-- Hora --}}
        <div class="flex items-center gap-3 p-3 bg-fuchsia-50 rounded-xl">
            <div class="w-8 h-8 bg-fuchsia-100 rounded-lg flex items-center justify-center flex-shrink-0">
                <svg class="w-4 h-4 text-fuchsia-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <p class="text-xs text-fuchsia-400 font-medium">Hora</p>
                <p id="detail-time" class="text-sm font-bold text-fuchsia-800"></p>
            </div>
        </div>

        {{-- Fecha --}}
        <div class="flex items-center gap-3 p-3 bg-pink-50 rounded-xl">
            <div class="w-8 h-8 bg-pink-100 rounded-lg flex items-center justify-center flex-shrink-0">
                <svg class="w-4 h-4 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </div>
            <div>
                <p class="text-xs text-pink-400 font-medium">Fecha</p>
                <p id="detail-date" class="text-sm font-bold text-pink-800"></p>
            </div>
        </div>

        {{-- Estilista --}}
        <div class="flex items-center gap-3 p-3 bg-amber-50 rounded-xl">
            <div class="w-8 h-8 bg-amber-100 rounded-lg flex items-center justify-center flex-shrink-0">
                <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
            </div>
            <div>
                <p class="text-xs text-amber-400 font-medium">Estilista</p>
                <p id="detail-stylist" class="text-sm font-bold text-amber-800"></p>
            </div>
        </div>

        {{-- Estado --}}
        <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-xl">
            <div class="w-8 h-8 bg-gray-100 rounded-lg flex items-center justify-center flex-shrink-0">
                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <p class="text-xs text-gray-400 font-medium">Estado actual</p>
                <span id="detail-status-badge" class="text-xs font-bold px-2.5 py-1 rounded-full mt-0.5 inline-block"></span>
            </div>
        </div>

        {{-- Notas --}}
        <div id="detail-notes-wrap" class="hidden">
            <div class="flex items-start gap-3 p-3 bg-blue-50 rounded-xl">
                <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0 mt-0.5">
                    <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-xs text-blue-400 font-medium">Notas</p>
                    <p id="detail-notes" class="text-sm text-blue-800 mt-0.5 leading-relaxed"></p>
                </div>
            </div>
        </div>

        {{-- Cambio rápido de estado --}}
        <div class="pt-1">
            <p class="text-xs text-gray-400 font-semibold uppercase tracking-wider mb-2.5">Cambiar estado rápido</p>
            <div class="grid grid-cols-2 gap-2">
                <button onclick="quickStatus('pending')"
                    class="py-2.5 px-3 text-xs font-bold rounded-xl bg-amber-50 text-amber-700 hover:bg-amber-100 border border-amber-200 transition-all flex items-center justify-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-amber-400"></span> Pendiente
                </button>
                <button onclick="quickStatus('confirmed')"
                    class="py-2.5 px-3 text-xs font-bold rounded-xl bg-blue-50 text-blue-700 hover:bg-blue-100 border border-blue-200 transition-all flex items-center justify-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-blue-500"></span> Confirmada
                </button>
                <button onclick="quickStatus('completed')"
                    class="py-2.5 px-3 text-xs font-bold rounded-xl bg-green-50 text-green-700 hover:bg-green-100 border border-green-200 transition-all flex items-center justify-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-green-500"></span> Completada
                </button>
                <button onclick="quickStatus('cancelled')"
                    class="py-2.5 px-3 text-xs font-bold rounded-xl bg-red-50 text-red-700 hover:bg-red-100 border border-red-200 transition-all flex items-center justify-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-red-400"></span> Cancelada
                </button>
            </div>
        </div>

        {{-- Acciones --}}
        <div class="flex gap-2 pt-1 border-t border-gray-100">
            <button onclick="openEditFromDetail()"
                class="flex-1 flex items-center justify-center gap-2 py-2.5 bg-gradient-to-r from-fuchsia-500 to-pink-600 text-white text-xs font-bold rounded-xl hover:from-fuchsia-600 hover:to-pink-700 transition-all shadow-md shadow-fuchsia-500/30">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Editar cita
            </button>

            {{-- Botón que dispara el form externo definido en index.blade.php --}}
            <button type="button" onclick="submitQuickDelete()"
                class="w-10 h-10 flex items-center justify-center bg-red-50 text-red-500 hover:bg-red-100 rounded-xl transition-all border border-red-200">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
            </button>
        </div>

    </div>
</div>