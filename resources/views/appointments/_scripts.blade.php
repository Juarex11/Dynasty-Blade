{{-- resources/views/appointments/_scripts.blade.php --}}
<script>
    // ─── State ───────────────────────────────────────────────────────────────
    let currentAppt = null;

    const STATUS = {
        pending:   { label: 'Pendiente',  cls: 'bg-amber-100 text-amber-700' },
        confirmed: { label: 'Confirmada', cls: 'bg-blue-100 text-blue-700'   },
        completed: { label: 'Completada', cls: 'bg-green-100 text-green-700' },
        cancelled: { label: 'Cancelada',  cls: 'bg-red-100 text-red-700'     },
    };

    // ─── Detail panel ────────────────────────────────────────────────────────
    function selectAppointment(id, name, service, stylist, date, startTime, endTime, status, notes, color, phone, email) {
        currentAppt = { id, name, service, stylist, date, startTime, endTime, status, notes, color, phone, email };

        // Populate header
        document.getElementById('detail-name').textContent    = name;
        document.getElementById('detail-service').textContent = service;
        document.getElementById('detail-header').style.background =
            `linear-gradient(135deg, ${color} 0%, ${color}aa 100%)`;

        // Time
        document.getElementById('detail-time').textContent = `${startTime} – ${endTime}`;

        // Date (formatted)
        const d = new Date(date + 'T00:00:00');
        document.getElementById('detail-date').textContent =
            d.toLocaleDateString('es-PE', { weekday:'long', year:'numeric', month:'long', day:'numeric' });

        // Stylist
        document.getElementById('detail-stylist').textContent = stylist || 'Sin asignar';

        // Status badge
        const sc = STATUS[status] || { label: status, cls: 'bg-gray-100 text-gray-600' };
        const badge = document.getElementById('detail-status-badge');
        badge.textContent = sc.label;
        badge.className   = `text-xs font-bold px-2.5 py-1 rounded-full inline-block ${sc.cls}`;

        // Notes
        const notesWrap = document.getElementById('detail-notes-wrap');
        if (notes && notes.trim()) {
            document.getElementById('detail-notes').textContent = notes;
            notesWrap.classList.remove('hidden');
        } else {
            notesWrap.classList.add('hidden');
        }

        // Delete form
        document.getElementById('quick-delete-form').action = `/appointments/${id}`;

        // Show panel
        document.getElementById('empty-panel').classList.add('hidden');
        document.getElementById('detail-panel').classList.remove('hidden');
    }

    function closeDetail() {
        document.getElementById('detail-panel').classList.add('hidden');
        document.getElementById('empty-panel').classList.remove('hidden');
        currentAppt = null;
    }

    // ─── Quick status change ──────────────────────────────────────────────────
    function quickStatus(newStatus) {
        if (!currentAppt) return;
        const f = document.getElementById('quick-status-form');
        f.action = `/appointments/${currentAppt.id}`;
        document.getElementById('qs-client_name').value  = currentAppt.name;
        document.getElementById('qs-client_phone').value = currentAppt.phone;
        document.getElementById('qs-client_email').value = currentAppt.email;
        document.getElementById('qs-service').value      = currentAppt.service;
        document.getElementById('qs-stylist').value      = currentAppt.stylist;
        document.getElementById('qs-date').value         = currentAppt.date;
        document.getElementById('qs-start_time').value   = currentAppt.startTime;
        document.getElementById('qs-end_time').value     = currentAppt.endTime;
        document.getElementById('qs-status').value       = newStatus;
        document.getElementById('qs-notes').value        = currentAppt.notes;
        document.getElementById('qs-color').value        = currentAppt.color;
        f.submit();
    }

    // ─── Open edit from detail panel ─────────────────────────────────────────
    function openEditFromDetail() {
        if (!currentAppt) return;
        openEditModal(
            currentAppt.id, currentAppt.name, currentAppt.service,
            currentAppt.stylist, currentAppt.date, currentAppt.startTime,
            currentAppt.endTime, currentAppt.status, currentAppt.notes,
            currentAppt.color, currentAppt.phone, currentAppt.email
        );
    }

    // ─── Create modal ─────────────────────────────────────────────────────────
    function openModal() {
        document.getElementById('create-modal').classList.replace('hidden', 'flex');
    }
    function openModalWithDate(date) {
        document.getElementById('create-date').value = date;
        openModal();
    }
    function closeModal() {
        document.getElementById('create-modal').classList.replace('flex', 'hidden');
    }

    // ─── Edit modal ───────────────────────────────────────────────────────────
    function openEditModal(id, name, service, stylist, date, startTime, endTime, status, notes, color, phone, email) {
        document.getElementById('edit-form').action         = `/appointments/${id}`;
        document.getElementById('edit-delete-form').action  = `/appointments/${id}`;
        document.getElementById('edit-client_name').value   = name;
        document.getElementById('edit-client_phone').value  = phone;
        document.getElementById('edit-client_email').value  = email;
        document.getElementById('edit-service').value       = service;
        document.getElementById('edit-stylist').value       = stylist;
        document.getElementById('edit-date').value          = date;
        document.getElementById('edit-start_time').value    = startTime;
        document.getElementById('edit-end_time').value      = endTime;
        document.getElementById('edit-status').value        = status;
        document.getElementById('edit-notes').value         = notes;

        // Set color radios
        document.querySelectorAll('.edit-color-radio').forEach(r => {
            r.checked = r.value === color;
            r.nextElementSibling.style.borderColor = r.checked ? r.value : 'transparent';
        });

        document.getElementById('edit-modal').classList.replace('hidden', 'flex');
    }
    function closeEditModal() {
        document.getElementById('edit-modal').classList.replace('flex', 'hidden');
    }

    // ─── Submit delete form (outside edit-form to avoid nesting) ─────────────
    function submitDeleteForm() {
        if (!confirm('¿Seguro que deseas eliminar esta cita?')) return;
        document.getElementById('edit-delete-form').submit();
    }

    // ─── Quick delete from detail panel ──────────────────────────────────────
    function submitQuickDelete() {
        if (!currentAppt) return;
        if (!confirm('¿Seguro que deseas eliminar esta cita?')) return;
        const f = document.getElementById('quick-delete-form');
        f.action = `/appointments/${currentAppt.id}`;
        f.submit();
    }

    // ─── Color picker feedback ────────────────────────────────────────────────
    function initColorPicker(selector) {
        document.querySelectorAll(selector).forEach(radio => {
            radio.addEventListener('change', function () {
                this.closest('.flex').querySelectorAll(selector).forEach(r => {
                    r.nextElementSibling.style.borderColor = 'transparent';
                });
                if (this.checked) this.nextElementSibling.style.borderColor = this.value;
            });
        });
    }
    initColorPicker('.create-color-radio');
    initColorPicker('.edit-color-radio');

    // ─── Close modals on backdrop click ──────────────────────────────────────
    document.getElementById('create-modal').addEventListener('click', e => {
        if (e.target === e.currentTarget) closeModal();
    });
    document.getElementById('edit-modal').addEventListener('click', e => {
        if (e.target === e.currentTarget) closeEditModal();
    });

    // ─── Close on Escape key ─────────────────────────────────────────────────
    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') {
            closeModal();
            closeEditModal();
        }
    });
</script>