{{-- resources/views/appointments/_create-modal.blade.php --}}
<div id="create-modal" class="fixed inset-0 z-50 hidden items-center justify-center p-4 bg-black/40 backdrop-blur-sm">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg max-h-[92vh] flex flex-col">

        {{-- Header --}}
        <div class="flex items-center justify-between px-6 py-5 border-b border-gray-100 flex-shrink-0">
            <div>
                <h3 class="text-xl font-bold text-gray-900">Nueva Cita</h3>
                <p class="text-sm text-gray-400 mt-0.5">Registra una cita en el calendario</p>
            </div>
            <button onclick="closeModal()"
                class="w-9 h-9 flex items-center justify-center text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-xl transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- Form --}}
        <form action="{{ route('appointments.store') }}" method="POST" class="overflow-y-auto flex-1 px-6 py-5 space-y-4">
            @csrf

            {{-- Cliente --}}
            <div>
                <label class="block text-xs font-semibold text-gray-500 mb-1.5 uppercase tracking-wide">Nombre del cliente *</label>
                <input type="text" name="client_name" required placeholder="Ej: María García"
                    class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-fuchsia-200 focus:border-fuchsia-400 text-sm bg-gray-50 focus:bg-white transition-all">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-500 mb-1.5 uppercase tracking-wide">Teléfono</label>
                    <input type="text" name="client_phone" placeholder="+51 999 999 999"
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-fuchsia-200 focus:border-fuchsia-400 text-sm bg-gray-50 focus:bg-white transition-all">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-500 mb-1.5 uppercase tracking-wide">Email</label>
                    <input type="email" name="client_email" placeholder="cliente@email.com"
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-fuchsia-200 focus:border-fuchsia-400 text-sm bg-gray-50 focus:bg-white transition-all">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-500 mb-1.5 uppercase tracking-wide">Servicio *</label>
                    <select name="service" required
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-fuchsia-200 focus:border-fuchsia-400 text-sm bg-gray-50 focus:bg-white transition-all">
                        <option value="">Selecciona...</option>
                        @foreach(['Corte de cabello','Tinte / Coloración','Manicure','Pedicure','Manicure + Pedicure','Tratamiento capilar','Keratina','Maquillaje','Depilación','Masaje relajante','Limpieza facial'] as $svc)
                            <option>{{ $svc }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-500 mb-1.5 uppercase tracking-wide">Estilista</label>
                    <input type="text" name="stylist" placeholder="Nombre del estilista"
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-fuchsia-200 focus:border-fuchsia-400 text-sm bg-gray-50 focus:bg-white transition-all">
                </div>
            </div>

            <div class="grid grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-500 mb-1.5 uppercase tracking-wide">Fecha *</label>
                    <input type="date" name="date" id="create-date" required
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-fuchsia-200 focus:border-fuchsia-400 text-sm bg-gray-50 focus:bg-white transition-all">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-500 mb-1.5 uppercase tracking-wide">Inicio *</label>
                    <input type="time" name="start_time" required
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-fuchsia-200 focus:border-fuchsia-400 text-sm bg-gray-50 focus:bg-white transition-all">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-500 mb-1.5 uppercase tracking-wide">Fin *</label>
                    <input type="time" name="end_time" required
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-fuchsia-200 focus:border-fuchsia-400 text-sm bg-gray-50 focus:bg-white transition-all">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-500 mb-1.5 uppercase tracking-wide">Estado</label>
                <select name="status"
                    class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-fuchsia-200 focus:border-fuchsia-400 text-sm bg-gray-50 focus:bg-white transition-all">
                    <option value="pending">⏳ Pendiente</option>
                    <option value="confirmed">✓ Confirmada</option>
                    <option value="completed">✅ Completada</option>
                    <option value="cancelled">✕ Cancelada</option>
                </select>
            </div>

            {{-- Color picker --}}
            <div>
                <label class="block text-xs font-semibold text-gray-500 mb-2 uppercase tracking-wide">Color de la cita</label>
                <div class="flex items-center gap-2 flex-wrap">
                    @foreach(['#a855f7','#ec4899','#f59e0b','#3b82f6','#10b981','#ef4444','#6366f1','#14b8a6'] as $clr)
                        <label class="cursor-pointer">
                            <input type="radio" name="color" value="{{ $clr }}" class="sr-only create-color-radio"
                                {{ $clr === '#a855f7' ? 'checked' : '' }}>
                            <span class="block w-8 h-8 rounded-full transition-all hover:scale-110 border-4"
                                style="background-color: {{ $clr }}; border-color: {{ $clr === '#a855f7' ? $clr : 'transparent' }}"></span>
                        </label>
                    @endforeach
                </div>
            </div>

            {{-- Notas --}}
            <div>
                <label class="block text-xs font-semibold text-gray-500 mb-1.5 uppercase tracking-wide">Notas</label>
                <textarea name="notes" rows="2" placeholder="Observaciones adicionales..."
                    class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-fuchsia-200 focus:border-fuchsia-400 text-sm bg-gray-50 focus:bg-white transition-all resize-none"></textarea>
            </div>

            {{-- Actions --}}
            <div class="flex gap-3 pt-2 border-t border-gray-100">
                <button type="button" onclick="closeModal()"
                    class="flex-1 py-2.5 text-sm font-semibold text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-xl transition-all">
                    Cancelar
                </button>
                <button type="submit"
                    class="flex-1 py-2.5 text-sm font-semibold text-white bg-gradient-to-r from-fuchsia-500 to-pink-600 hover:from-fuchsia-600 hover:to-pink-700 rounded-xl shadow-lg shadow-fuchsia-500/30 transition-all">
                    Guardar Cita
                </button>
            </div>
        </form>

    </div>
</div>