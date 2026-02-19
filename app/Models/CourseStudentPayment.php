<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CourseStudentPayment extends Model
{
    protected $fillable = [
        'course_opening_student_id', 'course_opening_id',
        'payment_type', 'installment_number', 'concept',
        'amount_due', 'amount_paid',
        'status', 'due_date', 'paid_at',
        'payment_method', 'reference', 'notes', 'recorded_by',
    ];

    protected $casts = [
        'due_date'   => 'date',
        'paid_at'    => 'date',
        'amount_due' => 'float',
        'amount_paid'=> 'float',
    ];

    // ── Labels ────────────────────────────────────────────────────────────────

    public static array $STATUS_LABELS = [
        'pendiente'  => 'Pendiente',
        'pagado'     => 'Pagado',
        'parcial'    => 'Parcial',
        'vencido'    => 'Vencido',
        'becado'     => 'Becado',
        'anulado'    => 'Anulado',
    ];

    public static array $METHOD_LABELS = [
        'efectivo'      => 'Efectivo',
        'transferencia' => 'Transferencia',
        'tarjeta'       => 'Tarjeta',
        'yape'          => 'Yape',
        'plin'          => 'Plin',
        'otro'          => 'Otro',
    ];

    public function getStatusLabelAttribute(): string
    {
        return self::$STATUS_LABELS[$this->status] ?? $this->status;
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'pagado'    => 'green',
            'pendiente' => 'amber',
            'parcial'   => 'orange',
            'vencido'   => 'red',
            'becado'    => 'violet',
            'anulado'   => 'gray',
            default     => 'gray',
        };
    }

    public function getBalanceAttribute(): float
    {
        return round($this->amount_due - $this->amount_paid, 2);
    }

    public function getConceptLabelAttribute(): string
    {
        if ($this->concept) return $this->concept;
        return match($this->payment_type) {
            'unico'        => 'Pago único',
            'cuota_N'      => "Cuota {$this->installment_number}",
            'mensual'      => "Mes {$this->installment_number}",
            'por_sesion'   => "Sesión {$this->installment_number}",
            'semanal'      => "Semana {$this->installment_number}",
            'ajuste'       => 'Ajuste',
            default        => $this->payment_type,
        };
    }

    // ── Relationships ─────────────────────────────────────────────────────────

    public function enrollment(): BelongsTo
    {
        return $this->belongsTo(CourseOpeningStudent::class, 'course_opening_student_id');
    }

    public function opening(): BelongsTo
    {
        return $this->belongsTo(CourseOpening::class, 'course_opening_id');
    }

    public function recorder(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'recorded_by');
    }
}