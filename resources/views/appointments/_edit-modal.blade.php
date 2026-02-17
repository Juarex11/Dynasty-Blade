{{-- resources/views/appointments/_edit-modal.blade.php --}}

{{-- ⚠️ Form de eliminar FUERA del edit-form (HTML no permite forms anidados) --}}
<form id="edit-delete-form" action="" method="POST"
    onsubmit="return confirm('¿Seguro que deseas eliminar esta cita?')" class="hidden">
    @csrf @method('DELETE')
</form>

<div id="edit-modal" class="fixed inset-0 z-50 hidden items-center justify-center p-4 bg-black/40 backdrop-blur-sm">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg max-h-[92vh] flex flex-col">

        {{-- Header --}}
        <div class="flex items-center justify-between px-6 py-5 border-b border-gray-100 flex-shrink-0">
            <div>
                <h3 class="text-xl font-bold text-gray-900">Editar Cita</h3>
                <p class="text-sm text-gray-400 mt-0.5">Modifica los datos de la cita</p>
            </div>
            <button onclick="closeEditModal()"
                class="w-9 h-9 flex items-center justify-center text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-xl transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- Form --}}
        <form id="edit-form" action="" method="POST" class="overflow-y-auto flex-1 px-6 py-5 space-y-4">
            @csrf @method('PUT')

            <div>
                <label class="block text-xs font-semibold text-gray-500 mb-1.5 uppercase tracking-wide">Nombre del cliente *</label>
                <input type="text" id="edit-client_name" name="client_name" required
                    class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-fuchsia-200 focus:border-fuchsia-400 text-sm bg-gray-50 focus:bg-white transition-all">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-500 mb-1.5 uppercase tracking-wide">Teléfono</label>
                    <input type="text" id="edit-client_phone" name="client_phone"
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-fuchsia-200 focus:border-fuchsia-400 text-sm bg-gray-50 focus:bg-white transition-all">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-500 mb-1.5 uppercase tracking-wide">Email</label>
                    <input type="email" id="edit-client_email" name="client_email"
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-fuchsia-200 focus:border-fuchsia-400 text-sm bg-gray-50 focus:bg-white transition-all">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-500 mb-1.5 uppercase tracking-wide">Servicio *</label>
                    <select id="edit-service" name="service" required
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-fuchsia-200 focus:border-fuchsia-400 text-sm bg-gray-50 focus:bg-white transition-all">
                        @foreach(['Corte de cabello','Tinte / Coloración','Manicure','Pedicure','Manicure + Pedicure','Tratamiento capilar','Keratina','Maquillaje','Depilación','Masaje relajante','Limpieza facial'] as $svc)
                            <option>{{ $svc }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-500 mb-1.5 uppercase tracking-wide">Estilista</label>
                    <input type="text" id="edit-stylist" name="stylist"
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-fuchsia-200 focus:border-fuchsia-400 text-sm bg-gray-50 focus:bg-white transition-all">
                </div>
            </div>

            <div class="grid grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-500 mb-1.5 uppercase tracking-wide">Fecha *</label>
                    <input type="date" id="edit-date" name="date" required
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-fuchsia-200 focus:border-fuchsia-400 text-sm bg-gray-50 focus:bg-white transition-all">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-500 mb-1.5 uppercase tracking-wide">Inicio *</label>
                    <input type="time" id="edit-start_time" name="start_time" required
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-fuchsia-200 focus:border-fuchsia-400 text-sm bg-gray-50 focus:bg-white transition-all">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-500 mb-1.5 uppercase tracking-wide">Fin *</label>
                    <input type="time" id="edit-end_time" name="end_time" required
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-fuchsia-200 focus:border-fuchsia-400 text-sm bg-gray-50 focus:bg-white transition-all">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-500 mb-1.5 uppercase tracking-wide">Estado</label>
                <select id="edit-status" name="status"
                    class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-fuchsia-200 focus:border-fuchsia-400 text-sm bg-gray-50 focus:bg-white transition-all">
                    <option value="pending">⏳ Pendiente</option>
                    <option value="confirmed">✓ Confirmada</option>
                    <option value="completed">✅ Completada</option>
                    <option value="cancelled">✕ Cancelada</option>
                </select>
            </div>

            {{-- Color picker --}}
            <div>
                <label class="block text-xs font-semibold text-gray-500 mb-2 uppercase tracking-wide">Color</label>
                <div id="edit-colors" class="flex items-center gap-2 flex-wrap">
                    @foreach(['#a855f7','#ec4899','#f59e0b','#3b82f6','#10b981','#ef4444','#6366f1','#14b8a6'] as $clr)
                        <label class="cursor-pointer">
                            <input type="radio" name="color" value="{{ $clr }}" class="sr-only edit-color-radio">
                            <span class="block w-8 h-8 rounded-full transition-all hover:scale-110 border-4"
                                style="background-color: {{ $clr }}; border-color: transparent"></span>
                        </label>
                    @endforeach
                </div>
            </div>

            {{-- Notas --}}
            <div>
                <label class="block text-xs font-semibold text-gray-500 mb-1.5 uppercase tracking-wide">Notas</label>
                <textarea id="edit-notes" name="notes" rows="2"
                    class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-fuchsia-200 focus:border-fuchsia-400 text-sm bg-gray-50 focus:bg-white transition-all resize-none"></textarea>
            </div>

            {{-- Actions --}}
            <div class="flex items-center justify-between pt-2 border-t border-gray-100">
                {{-- Botón que dispara el form externo (evita forms anidados) --}}
                <button type="button" onclick="submitDeleteForm()"
                    class="flex items-center gap-2 px-4 py-2.5 text-sm font-semibold text-red-600 bg-red-50 hover:bg-red-100 rounded-xl border border-red-200 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                    Eliminar
                </button>

                <div class="flex gap-3">
                    <button type="button" onclick="closeEditModal()"
                        class="px-5 py-2.5 text-sm font-semibold text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-xl transition-all">
                        Cancelar
                    </button>
                    <button type="submit"
                        class="px-5 py-2.5 text-sm font-semibold text-white bg-gradient-to-r from-fuchsia-500 to-pink-600 hover:from-fuchsia-600 hover:to-pink-700 rounded-xl shadow-lg shadow-fuchsia-500/30 transition-all">
                        Actualizar
                    </button>
                </div>
            </div>
        </form>

    </div>
</div>