@extends('layouts.app')

@section('title', 'Pagos — ' . $courseOpening->display_name)

@section('content')

<div class="flex items-center gap-3 mb-6">
    <a href="{{ route('course-openings.show', $courseOpening) }}"
       class="w-9 h-9 flex items-center justify-center rounded-xl border border-gray-200 hover:border-violet-300 hover:bg-violet-50 text-gray-500 hover:text-violet-600 transition-all shrink-0">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
    </a>
    <div class="flex-1 min-w-0">
        <p class="text-xs text-gray-400 mb-0.5">{{ $courseOpening->course->name }}</p>
        <h1 class="text-xl font-bold text-gray-900">Gestión de Pagos</h1>
    </div>
    <button onclick="openModal('modal-generate')"
        class="inline-flex items-center gap-2 bg-gradient-to-r from-violet-500 to-purple-600 hover:from-violet-600 hover:to-purple-700 text-white font-semibold px-4 py-2 rounded-xl shadow-lg shadow-violet-500/30 transition-all text-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Generar cuotas
    </button>
</div>

@if(session('success'))
<div class="mb-5 bg-green-50 border border-green-200 rounded-xl p-4 flex items-center gap-3 text-sm text-green-700">
    <svg class="w-5 h-5 text-green-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
    </svg>
    {{ session('success') }}
</div>
@endif

@if(session('error'))
<div class="mb-5 bg-red-50 border border-red-200 rounded-xl p-4 flex items-center gap-3 text-sm text-red-700">
    <svg class="w-5 h-5 text-red-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
    </svg>
    {{ session('error') }}
</div>
@endif

{{-- ─── Info del precio del curso ──────────────────────────────────────────── --}}
@php
    $effectivePrice = $courseOpening->effective_price ?? $courseOpening->course->price ?? null;
@endphp
@if($effectivePrice)
<div class="mb-5 bg-violet-50 border border-violet-200 rounded-xl p-4 flex items-center gap-3">
    <svg class="w-5 h-5 text-violet-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
    </svg>
    <p class="text-sm text-violet-700">
        Precio del curso: <strong>S/. {{ number_format($effectivePrice, 2) }}</strong>
        @if($courseOpening->price_promo && $courseOpening->promo_until?->isFuture())
        <span class="ml-2 text-pink-600">(Promo: S/. {{ number_format($courseOpening->price_promo, 2) }} hasta {{ $courseOpening->promo_until->format('d/m/Y') }})</span>
        @endif
        — El cronograma se genera en base a este precio.
    </p>
</div>
@endif

{{-- ─── KPIs de resumen ────────────────────────────────────────────────────── --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    @php
    $kpis = [
        ['Cobrado',    'S/. '.number_format($summary['total_paid'],2),    'green',  'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
        ['Por cobrar', 'S/. '.number_format($summary['total_pending'],2), 'amber',  'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'],
        ['Vencido',    'S/. '.number_format($summary['total_overdue'],2), 'red',    'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z'],
        ['Total esp.', 'S/. '.number_format($summary['total_due'],2),    'violet', 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z'],
    ];
    $colorMap = [
        'green'  => ['bg'=>'bg-green-50',  'text'=>'text-green-600',  'icon'=>'text-green-400'],
        'amber'  => ['bg'=>'bg-amber-50',  'text'=>'text-amber-600',  'icon'=>'text-amber-400'],
        'red'    => ['bg'=>'bg-red-50',    'text'=>'text-red-600',    'icon'=>'text-red-400'],
        'violet' => ['bg'=>'bg-violet-50', 'text'=>'text-violet-600', 'icon'=>'text-violet-400'],
    ];
    @endphp
    @foreach($kpis as [$label, $value, $color, $svgPath])
    @php $c = $colorMap[$color]; @endphp
    <div class="bg-white rounded-2xl border border-gray-200 p-4 flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl {{ $c['bg'] }} flex items-center justify-center shrink-0">
            <svg class="w-5 h-5 {{ $c['icon'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="{{ $svgPath }}"/>
            </svg>
        </div>
        <div>
            <p class="text-xs text-gray-500">{{ $label }}</p>
            <p class="text-lg font-bold {{ $c['text'] }} tabular-nums leading-tight">{{ $value }}</p>
        </div>
    </div>
    @endforeach
</div>

{{-- Barra de progreso global --}}
@if($summary['total_due'] > 0)
@php $globalPct = min(100, round($summary['total_paid'] / $summary['total_due'] * 100)); @endphp
<div class="bg-white rounded-2xl border border-gray-200 p-4 mb-6">
    <div class="flex items-center justify-between text-xs text-gray-500 mb-2">
        <span>Progreso de cobros</span>
        <span class="font-semibold text-gray-700">{{ $globalPct }}%</span>
    </div>
    <div class="h-2.5 bg-gray-100 rounded-full overflow-hidden">
        <div class="h-full rounded-full bg-gradient-to-r from-violet-400 to-green-400 transition-all"
             style="width:{{ $globalPct }}%"></div>
    </div>
    <div class="flex justify-between text-xs text-gray-400 mt-1">
        <span>S/. {{ number_format($summary['total_paid'],2) }} pagado</span>
        <span>S/. {{ number_format($summary['total_due'],2) }} total</span>
    </div>
</div>
@endif

{{-- ─── Sin cuotas generadas: CTA prominente ──────────────────────────────── --}}
@if($summary['total_due'] == 0 && $courseOpening->enrollments->isNotEmpty())
<div class="bg-amber-50 border border-amber-200 rounded-2xl p-6 mb-6 flex items-center gap-4">
    <div class="w-12 h-12 bg-amber-100 rounded-xl flex items-center justify-center shrink-0">
        <svg class="w-6 h-6 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
    </div>
    <div class="flex-1">
        <p class="font-semibold text-amber-800 text-sm">Sin cronograma de pagos aún</p>
        <p class="text-xs text-amber-600 mt-0.5">
            Hay {{ $courseOpening->enrollments->count() }} estudiante(s) inscritos pero aún no se ha generado el cronograma de cuotas.
            El precio del curso es <strong>S/. {{ number_format($effectivePrice ?? 0, 2) }}</strong>.
        </p>
    </div>
    <button onclick="openModal('modal-generate')"
        class="shrink-0 px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white font-semibold text-sm rounded-xl transition-colors">
        Generar ahora
    </button>
</div>
@endif

{{-- ─── Lista de estudiantes con sus pagos ────────────────────────────────── --}}
<div class="space-y-4">
    @forelse($courseOpening->enrollments as $enrollment)
    @php
        $totalDue   = $enrollment->payments->whereNotIn('status',['anulado'])->sum('amount_due');
        $totalPaid  = $enrollment->payments->whereNotIn('status',['anulado'])->sum('amount_paid');
        $balance    = round($totalDue - $totalPaid, 2);
        $pct        = $totalDue > 0 ? min(100, round($totalPaid / $totalDue * 100)) : 0;
        $person     = $enrollment->employee ?? $enrollment->client;
        $hasOverdue = $enrollment->payments->where('status','vencido')->isNotEmpty();
    @endphp

    <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden" id="enroll-{{ $enrollment->id }}">

        {{-- Cabecera del estudiante --}}
        <div class="flex items-center gap-3 p-4 cursor-pointer hover:bg-gray-50/80 transition-colors"
             onclick="toggleEnroll('{{ $enrollment->id }}')">

            {{-- Avatar --}}
            <div class="w-10 h-10 rounded-xl overflow-hidden shrink-0">
                @if($person?->photo)
                    <img src="{{ asset('storage/'.$person->photo) }}" class="w-full h-full object-cover">
                @else
                    <div class="w-full h-full {{ $enrollment->person_type === 'employee' ? 'bg-violet-100 text-violet-600' : 'bg-blue-100 text-blue-600' }} flex items-center justify-center font-bold text-sm">
                        {{ strtoupper(substr($enrollment->person_name, 0, 1)) }}
                    </div>
                @endif
            </div>

            {{-- Nombre + progreso --}}
            <div class="flex-1 min-w-0">
                <div class="flex items-center gap-2">
                    <p class="text-sm font-semibold text-gray-800">{{ $enrollment->person_name }}</p>
                    @if($hasOverdue)
                    <span class="px-1.5 py-0.5 bg-red-100 text-red-600 text-xs rounded-full font-medium">⚠ Vencido</span>
                    @endif
                    @if($totalDue == 0)
                    <span class="px-1.5 py-0.5 bg-amber-100 text-amber-600 text-xs rounded-full font-medium">Sin cuotas</span>
                    @endif
                </div>
                <div class="flex items-center gap-2 mt-1">
                    <div class="flex-1 h-1.5 bg-gray-100 rounded-full overflow-hidden max-w-40">
                        <div class="h-full rounded-full {{ $pct >= 100 ? 'bg-green-400' : ($pct > 0 ? 'bg-violet-400' : 'bg-gray-200') }}"
                             style="width:{{ $pct }}%"></div>
                    </div>
                    <span class="text-xs text-gray-400 tabular-nums">{{ $pct }}%</span>
                </div>
            </div>

            {{-- Montos --}}
            <div class="text-right shrink-0 hidden sm:block">
                <p class="text-sm font-bold {{ $balance <= 0 && $totalDue > 0 ? 'text-green-600' : 'text-gray-800' }} tabular-nums">
                    S/. {{ number_format($totalPaid, 2) }}
                </p>
                <p class="text-xs text-gray-400">
                    @if($totalDue > 0)
                    de S/. {{ number_format($totalDue, 2) }}
                    @else
                    sin cronograma
                    @endif
                </p>
            </div>

            {{-- Estado --}}
            @if($totalDue > 0)
                @if($balance > 0)
                <div class="hidden md:block px-3 py-1.5 bg-amber-50 rounded-xl text-right shrink-0">
                    <p class="text-xs text-amber-600 font-semibold tabular-nums">S/. {{ number_format($balance,2) }}</p>
                    <p class="text-xs text-amber-400">pendiente</p>
                </div>
                @else
                <div class="hidden md:block px-3 py-1.5 bg-green-50 rounded-xl shrink-0">
                    <p class="text-xs text-green-600 font-semibold text-center">✅ Al día</p>
                </div>
                @endif
            @endif

            <svg class="w-4 h-4 text-gray-400 shrink-0 transition-transform enroll-icon-{{ $enrollment->id }}"
                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
        </div>

        {{-- Panel de cuotas --}}
        <div id="enroll-panel-{{ $enrollment->id }}" class="hidden border-t border-gray-100">
            <div class="p-4 space-y-3">

                @if($enrollment->payments->isEmpty())
                <div class="flex items-center gap-3 p-3 bg-amber-50 border border-amber-200 rounded-xl">
                    <svg class="w-4 h-4 text-amber-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <p class="text-sm text-amber-700">
                        Sin cuotas generadas. Usa
                        <button type="button" onclick="openModal('modal-generate')" class="font-semibold underline hover:text-amber-800">
                            Generar cuotas
                        </button>
                        para crear el cronograma basado en el precio del curso (S/. {{ number_format($effectivePrice ?? 0, 2) }}).
                    </p>
                </div>
                @else

                {{-- Resumen estado pago de este estudiante --}}
                @php
                    $epaid = $enrollment->payments->whereNotIn('status',['anulado'])->sum('amount_paid');
                    $edue  = $enrollment->payments->whereNotIn('status',['anulado'])->sum('amount_due');
                    $payColors = ['pagado'=>'text-green-600','pendiente'=>'text-amber-600','parcial'=>'text-orange-500','becado'=>'text-violet-600'];
                    $epc = $payColors[$enrollment->payment_status] ?? 'text-gray-500';
                @endphp
                @if($edue > 0)
                <p class="text-xs {{ $epc }} font-medium">
                    S/. {{ number_format($epaid, 2) }} / {{ number_format($edue, 2) }} · {{ ucfirst($enrollment->payment_status) }}
                </p>
                @endif

                {{-- Tabla de cuotas --}}
                <div class="rounded-xl border border-gray-200 overflow-hidden">
                    <table class="w-full text-xs">
                        <thead>
                            <tr class="bg-gray-50 border-b border-gray-100">
                                <th class="text-left px-3 py-2.5 font-semibold text-gray-500">Concepto</th>
                                <th class="text-right px-3 py-2.5 font-semibold text-gray-500">Monto</th>
                                <th class="text-right px-3 py-2.5 font-semibold text-gray-500">Pagado</th>
                                <th class="text-center px-3 py-2.5 font-semibold text-gray-500">Estado</th>
                                <th class="text-center px-3 py-2.5 font-semibold text-gray-500">Vence</th>
                                <th class="text-center px-3 py-2.5 font-semibold text-gray-500">Método</th>
                                <th class="text-center px-3 py-2.5 font-semibold text-gray-500 w-20">Acción</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @foreach($enrollment->payments->sortBy('installment_number') as $payment)
                            @php
                            $statusColors = [
                                'pendiente' => 'bg-amber-100 text-amber-700',
                                'pagado'    => 'bg-green-100 text-green-700',
                                'parcial'   => 'bg-orange-100 text-orange-600',
                                'vencido'   => 'bg-red-100 text-red-600',
                                'becado'    => 'bg-violet-100 text-violet-700',
                                'anulado'   => 'bg-gray-100 text-gray-400',
                            ];
                            $sc = $statusColors[$payment->status] ?? 'bg-gray-100 text-gray-500';
                            $isActionable = !in_array($payment->status, ['pagado','becado','anulado']);
                            $pendingBalance = round($payment->amount_due - $payment->amount_paid, 2);
                            @endphp
                            <tr class="hover:bg-gray-50/50">
                                <td class="px-3 py-2.5 font-medium text-gray-700">
                                    {{ $payment->concept_label }}
                                    @if($payment->reference)
                                    <span class="text-gray-400 font-mono ml-1">·{{ $payment->reference }}</span>
                                    @endif
                                </td>
                                <td class="px-3 py-2.5 text-right text-gray-600 tabular-nums font-medium">
                                    S/. {{ number_format($payment->amount_due, 2) }}
                                </td>
                                <td class="px-3 py-2.5 text-right tabular-nums font-semibold
                                    {{ $payment->amount_paid >= $payment->amount_due ? 'text-green-600' : 'text-gray-600' }}">
                                    S/. {{ number_format($payment->amount_paid, 2) }}
                                </td>
                                <td class="px-3 py-2.5 text-center">
                                    <span class="px-2 py-0.5 rounded-full font-semibold {{ $sc }}">
                                        {{ $payment->status_label }}
                                    </span>
                                </td>
                                <td class="px-3 py-2.5 text-center text-gray-500 tabular-nums">
                                    {{ $payment->due_date?->format('d/m/Y') ?? '—' }}
                                </td>
                                <td class="px-3 py-2.5 text-center text-gray-500">
                                    @if($payment->payment_method)
                                    <span class="px-1.5 py-0.5 bg-gray-100 rounded text-gray-500">
                                        {{ \App\Models\CourseStudentPayment::$METHOD_LABELS[$payment->payment_method] ?? $payment->payment_method }}
                                    </span>
                                    @else —
                                    @endif
                                </td>
                                <td class="px-3 py-2.5 text-center">
                                    @if($isActionable)
                                    <button type="button"
                                        onclick="openPayModal({{ $payment->id }}, '{{ addslashes($payment->concept_label) }}', {{ $pendingBalance }})"
                                        class="px-2.5 py-1.5 bg-violet-50 text-violet-600 hover:bg-violet-100 rounded-lg font-semibold transition-colors text-xs">
                                        Pagar
                                    </button>
                                    @else
                                    <span class="text-gray-300">—</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @endif

                {{-- Botón pago manual --}}
                <button type="button"
                    onclick="openManualModal({{ $enrollment->id }}, '{{ addslashes($enrollment->person_name) }}')"
                    class="text-xs text-violet-600 hover:underline font-medium flex items-center gap-1 mt-1">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Agregar pago / ajuste manual
                </button>
            </div>
        </div>
    </div>
    @empty
    <div class="bg-white rounded-2xl border border-gray-200 p-10 text-center text-gray-400 italic text-sm">
        Sin estudiantes inscritos en esta apertura.
    </div>
    @endforelse
</div>


{{-- ═══════════════════════════════════════════════════════════════════════════
    MODAL: Generar cuotas
    El precio es readonly — se toma del precio configurado en la apertura/curso
════════════════════════════════════════════════════════════════════════════ --}}
<div id="modal-generate" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="closeModal('modal-generate')"></div>
    <div class="absolute inset-0 flex items-center justify-center p-4">
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                <div>
                    <h3 class="font-semibold text-gray-900">Generar cronograma de pagos</h3>
                    <p class="text-xs text-gray-400 mt-0.5">
                        Precio del curso: <strong class="text-violet-700">S/. {{ number_format($effectivePrice ?? 0, 2) }}</strong>
                    </p>
                </div>
                <button onclick="closeModal('modal-generate')"
                    class="w-8 h-8 flex items-center justify-center rounded-xl hover:bg-gray-100 text-gray-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <form action="{{ route('course-openings.payments.generate', $courseOpening) }}" method="POST" class="p-6 space-y-5">
                @csrf

                {{-- Tipo de cobro --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tipo de cobro</label>
                    <div class="grid grid-cols-2 gap-2" id="payment-type-group">
                        @foreach([
                            'unico'     => ['💳', 'Pago único',   'Un solo cobro por el total'],
                            'mensual'   => ['📅', 'Mensual',      'Cuotas mensuales'],
                            'semanal'   => ['🗓', 'Semanal',      'Cuotas semanales'],
                            'por_sesion'=> ['📚', 'Por sesión',   'Cobro por cada clase'],
                        ] as $val => [$icon, $label, $desc])
                        <label class="payment-type-opt flex items-start gap-3 p-3 border-2 rounded-xl cursor-pointer transition-all
                            {{ ($courseOpening->payment_type ?? 'unico') === $val ? 'border-violet-400 bg-violet-50' : 'border-gray-200 hover:border-gray-300' }}">
                            <input type="radio" name="payment_type" value="{{ $val }}" class="hidden"
                                {{ ($courseOpening->payment_type ?? 'unico') === $val ? 'checked' : '' }}
                                onchange="onPayTypeChange(this)">
                            <span class="text-xl mt-0.5">{{ $icon }}</span>
                            <div>
                                <p class="text-sm font-semibold text-gray-800">{{ $label }}</p>
                                <p class="text-xs text-gray-500">{{ $desc }}</p>
                            </div>
                        </label>
                        @endforeach
                    </div>
                </div>

                {{-- Número de cuotas --}}
                <div id="field-installments" class="{{ ($courseOpening->payment_type ?? 'unico') === 'unico' ? 'hidden' : '' }}">
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">
                        Número de cuotas / periodos
                    </label>
                    <input type="number" name="installments" min="1" max="60"
                        value="{{ $courseOpening->payment_installments ?? 3 }}"
                        class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-violet-300">
                    <p class="text-xs text-gray-400 mt-1" id="installments-hint">
                        Define cuántas cuotas tendrá el cronograma.
                    </p>
                </div>

                {{-- Precio por unidad — READONLY, tomado del curso --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">
                        Precio por <span id="unit-label">pago</span> (S/.)
                    </label>
                    <div class="relative">
                        <input type="number" name="unit_price" step="0.01" min="0"
                            id="unit-price-input"
                            value="{{ $effectivePrice ?? $courseOpening->course->price ?? '' }}"
                            readonly
                            class="w-full px-3 py-2 pr-10 border border-gray-200 bg-gray-50 rounded-xl text-sm text-gray-700 font-semibold cursor-not-allowed focus:outline-none">
                        <div class="absolute right-3 top-1/2 -translate-y-1/2">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                        </div>
                    </div>
                    <p class="text-xs text-gray-400 mt-1">
                        Precio configurado en la apertura/curso. Para cambiarlo, edita la apertura.
                    </p>
                </div>

                {{-- Total a generar (calculado) --}}
                <div id="total-preview" class="p-3 bg-violet-50 border border-violet-200 rounded-xl hidden">
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-violet-700">Total por estudiante:</span>
                        <span class="font-bold text-violet-800" id="total-preview-value">—</span>
                    </div>
                    <p class="text-xs text-violet-500 mt-1" id="total-preview-detail">—</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Primer vencimiento</label>
                    <input type="date" name="first_due_date"
                        value="{{ $courseOpening->start_date?->format('Y-m-d') }}"
                        class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-violet-300">
                </div>

                <div class="flex items-center gap-2">
                    <input type="checkbox" name="overwrite" value="1" id="chk-overwrite"
                        class="w-4 h-4 rounded accent-violet-600">
                    <label for="chk-overwrite" class="text-sm text-gray-600">
                        Eliminar cuotas pendientes existentes y regenerar
                    </label>
                </div>

                <div class="pt-2">
                    <button type="submit"
                        class="w-full py-3 bg-gradient-to-r from-violet-500 to-purple-600 hover:from-violet-600 hover:to-purple-700 text-white font-semibold rounded-xl shadow-md shadow-violet-500/25 transition-all text-sm">
                        Generar cuotas para todos los estudiantes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>


{{-- ═══════════════════════════════════════════════════════════════════════════
    MODAL: Registrar pago de una cuota
════════════════════════════════════════════════════════════════════════════ --}}
<div id="modal-pay" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="closeModal('modal-pay')"></div>
    <div class="absolute inset-0 flex items-center justify-center p-4">
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                <div>
                    <h3 class="font-semibold text-gray-900">Registrar pago</h3>
                    <p class="text-xs text-gray-400 mt-0.5" id="pay-modal-subtitle">—</p>
                </div>
                <button onclick="closeModal('modal-pay')"
                    class="w-8 h-8 flex items-center justify-center rounded-xl hover:bg-gray-100 text-gray-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <form id="form-pay" method="POST" class="p-6 space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Monto a pagar (S/.)</label>
                    <input type="number" name="amount_paid" id="pay-amount" step="0.01" min="0.01"
                        class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-violet-300 text-lg font-semibold tabular-nums">
                    <p class="text-xs text-gray-400 mt-1">
                        Saldo pendiente: <strong id="pay-balance" class="text-amber-600">—</strong>
                    </p>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Método de pago</label>
                        <select name="payment_method"
                            class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm bg-white text-gray-900 focus:outline-none focus:ring-2 focus:ring-violet-300">
                            @foreach(\App\Models\CourseStudentPayment::$METHOD_LABELS as $v => $l)
                            <option value="{{ $v }}">{{ $l }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Fecha de pago</label>
                        <input type="date" name="paid_at" value="{{ now()->format('Y-m-d') }}"
                            class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-violet-300">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Nº de operación / referencia</label>
                    <input type="text" name="reference" placeholder="Opcional"
                        class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-violet-300">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Notas</label>
                    <textarea name="notes" rows="2" placeholder="Opcional"
                        class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-violet-300 resize-none"></textarea>
                </div>
                <button type="submit"
                    class="w-full py-3 bg-green-500 hover:bg-green-600 text-white font-semibold rounded-xl shadow-lg shadow-green-500/40 transition-all text-sm">
                    Confirmar pago
                </button>
            </form>
        </div>
    </div>
</div>


{{-- ═══════════════════════════════════════════════════════════════════════════
    MODAL: Pago manual
════════════════════════════════════════════════════════════════════════════ --}}
<div id="modal-manual" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="closeModal('modal-manual')"></div>
    <div class="absolute inset-0 flex items-center justify-center p-4">
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                <div>
                    <h3 class="font-semibold text-gray-900">Pago / ajuste manual</h3>
                    <p class="text-xs text-gray-400 mt-0.5" id="manual-modal-subtitle">—</p>
                </div>
                <button onclick="closeModal('modal-manual')"
                    class="w-8 h-8 flex items-center justify-center rounded-xl hover:bg-gray-100 text-gray-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <form action="{{ route('course-openings.payments.store', $courseOpening) }}" method="POST" class="p-6 space-y-4">
                @csrf
                <input type="hidden" name="course_opening_student_id" id="manual-enrollment-id">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Concepto</label>
                    <input type="text" name="concept" required
                        placeholder="Ej: Cuota extraordinaria, Descuento, etc."
                        class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-violet-300">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Monto esperado (S/.)</label>
                        <input type="number" name="amount_due" step="0.01" min="0" placeholder="0.00"
                            class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-violet-300">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Monto pagado (S/.)</label>
                        <input type="number" name="amount_paid" step="0.01" min="0" placeholder="0.00"
                            class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-violet-300">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Fecha vencimiento</label>
                        <input type="date" name="due_date"
                            class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-violet-300">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Fecha de pago</label>
                        <input type="date" name="paid_at"
                            class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-violet-300">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Método</label>
                        <select name="payment_method"
                            class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm bg-white focus:outline-none focus:ring-2 focus:ring-violet-300">
                            <option value="">— Ninguno —</option>
                            @foreach(\App\Models\CourseStudentPayment::$METHOD_LABELS as $v => $l)
                            <option value="{{ $v }}">{{ $l }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Estado</label>
                        <select name="status"
                            class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm bg-white focus:outline-none focus:ring-2 focus:ring-violet-300">
                            <option value="pendiente">Pendiente</option>
                            <option value="pagado">Pagado</option>
                            <option value="parcial">Parcial</option>
                            <option value="becado">Becado</option>
                            <option value="anulado">Anulado</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Referencia</label>
                    <input type="text" name="reference" placeholder="Nº operación (opcional)"
                        class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-violet-300">
                </div>
                <button type="submit"
                    class="w-full py-3 bg-gradient-to-r from-violet-500 to-purple-600 hover:from-violet-600 hover:to-purple-700 text-white font-semibold rounded-xl shadow-md shadow-violet-500/25 transition-all text-sm">
                    Guardar pago
                </button>
            </form>
        </div>
    </div>
</div>


<script>
// ── Modales ───────────────────────────────────────────────────────────────────
function openModal(id)  { document.getElementById(id).classList.remove('hidden'); document.body.style.overflow='hidden'; }
function closeModal(id) { document.getElementById(id).classList.add('hidden'); document.body.style.overflow=''; }

// ── Acordeón estudiantes ──────────────────────────────────────────────────────
function toggleEnroll(id) {
    const panel = document.getElementById('enroll-panel-' + id);
    const icon  = document.querySelector('.enroll-icon-' + id);
    panel.classList.toggle('hidden');
    icon?.classList.toggle('rotate-180');
}

// ── Modal registrar pago ──────────────────────────────────────────────────────
function openPayModal(paymentId, concept, balance) {
    document.getElementById('pay-modal-subtitle').textContent = concept;
    document.getElementById('pay-balance').textContent = 'S/. ' + balance.toFixed(2);
    document.getElementById('pay-amount').value = balance.toFixed(2);
    document.getElementById('form-pay').action = `/course-openings/payments/${paymentId}/pay`;
    openModal('modal-pay');
}

// ── Modal pago manual ─────────────────────────────────────────────────────────
function openManualModal(enrollmentId, name) {
    document.getElementById('manual-modal-subtitle').textContent = name;
    document.getElementById('manual-enrollment-id').value = enrollmentId;
    openModal('modal-manual');
}

// ── Tipo de pago ──────────────────────────────────────────────────────────────
const COURSE_PRICE    = {{ $effectivePrice ?? 0 }};
const TOTAL_SESSIONS  = {{ $courseOpening->total_sessions }};

function onPayTypeChange(radio) {
    // Estilo selección
    document.querySelectorAll('.payment-type-opt').forEach(el => {
        el.classList.remove('border-violet-400', 'bg-violet-50');
        el.classList.add('border-gray-200');
    });
    radio.closest('.payment-type-opt').classList.add('border-violet-400', 'bg-violet-50');
    radio.closest('.payment-type-opt').classList.remove('border-gray-200');

    const val        = radio.value;
    const fieldInst  = document.getElementById('field-installments');
    const unitLabel  = document.getElementById('unit-label');
    const instHint   = document.getElementById('installments-hint');
    const preview    = document.getElementById('total-preview');
    const previewVal = document.getElementById('total-preview-value');
    const previewDet = document.getElementById('total-preview-detail');

    const unitLabels = { unico: 'pago', mensual: 'mes', semanal: 'semana', por_sesion: 'sesión' };
    unitLabel.textContent = unitLabels[val] || 'unidad';

    if (val === 'unico') {
        fieldInst.classList.add('hidden');
        // precio = precio total del curso
        document.getElementById('unit-price-input').value = COURSE_PRICE.toFixed(2);
        preview.classList.remove('hidden');
        previewVal.textContent = 'S/. ' + COURSE_PRICE.toFixed(2);
        previewDet.textContent = '1 pago de S/. ' + COURSE_PRICE.toFixed(2);
    } else {
        fieldInst.classList.remove('hidden');
        if (val === 'por_sesion') {
            instHint.textContent = `Se generará 1 cuota por sesión (${TOTAL_SESSIONS} sesiones en total).`;
            const sesPrice = TOTAL_SESSIONS > 0 ? (COURSE_PRICE / TOTAL_SESSIONS) : 0;
            document.getElementById('unit-price-input').value = sesPrice.toFixed(2);
            preview.classList.remove('hidden');
            previewVal.textContent = 'S/. ' + COURSE_PRICE.toFixed(2);
            previewDet.textContent = `${TOTAL_SESSIONS} cuotas de S/. ${sesPrice.toFixed(2)}`;
        } else {
            instHint.textContent = 'Define cuántas cuotas tendrá el cronograma.';
            updateInstallmentPreview(val);
        }
    }
}

function updateInstallmentPreview(type) {
    const installments = parseInt(document.querySelector('input[name="installments"]')?.value) || 1;
    const cuotaPrice = installments > 0 ? (COURSE_PRICE / installments) : COURSE_PRICE;
    document.getElementById('unit-price-input').value = cuotaPrice.toFixed(2);
    document.getElementById('total-preview').classList.remove('hidden');
    document.getElementById('total-preview-value').textContent = 'S/. ' + COURSE_PRICE.toFixed(2);
    document.getElementById('total-preview-detail').textContent = `${installments} cuota(s) de S/. ${cuotaPrice.toFixed(2)}`;
}

// Recalcular cuando cambian las cuotas
document.querySelector('input[name="installments"]')?.addEventListener('input', function() {
    const selected = document.querySelector('input[name="payment_type"]:checked');
    if (selected && selected.value !== 'unico' && selected.value !== 'por_sesion') {
        updateInstallmentPreview(selected.value);
    }
});

// ── Keyboard ESC ──────────────────────────────────────────────────────────────
document.addEventListener('keydown', e => {
    if (e.key === 'Escape') ['modal-generate','modal-pay','modal-manual'].forEach(closeModal);
});

// ── Init: mostrar preview inicial ─────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', () => {
    const selected = document.querySelector('input[name="payment_type"]:checked');
    if (selected) onPayTypeChange(selected);
});
</script>

@endsection