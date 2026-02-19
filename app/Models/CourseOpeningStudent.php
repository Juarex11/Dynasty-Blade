<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CourseOpeningStudent extends Model
{
    protected $table = 'course_opening_student';

    protected $fillable = [
        'course_opening_id', 'employee_id', 'client_id',
        'price_paid', 'payment_status', 'enrolled_at', 'status',
        'certificate_issued', 'notes',
    ];

    protected $casts = [
        'enrolled_at'        => 'date',
        'certificate_issued' => 'boolean',
    ];

    public function opening(): BelongsTo   { return $this->belongsTo(CourseOpening::class, 'course_opening_id'); }
    public function employee(): BelongsTo  { return $this->belongsTo(Employee::class); }
    public function client(): BelongsTo    { return $this->belongsTo(Client::class); }
    public function attendances(): HasMany { return $this->hasMany(CourseAttendance::class, 'course_opening_student_id'); }
    public function payments(): HasMany    { return $this->hasMany(CourseStudentPayment::class, 'course_opening_student_id'); }

    public function getPersonNameAttribute(): string
    {
        return $this->employee?->full_name ?? $this->client?->full_name ?? '—';
    }

    public function getPersonTypeAttribute(): string
    {
        return $this->employee_id ? 'employee' : 'client';
    }

    // ── Helpers de pagos ──────────────────────────────────────────────────────

    public function getTotalDueAttribute(): float
    {
        return (float) $this->payments->whereNotIn('status', ['anulado'])->sum('amount_due');
    }

    public function getTotalPaidAttribute(): float
    {
        return (float) $this->payments->whereNotIn('status', ['anulado'])->sum('amount_paid');
    }

    public function getBalanceAttribute(): float
    {
        return round($this->total_due - $this->total_paid, 2);
    }

    public function getPaymentProgressAttribute(): int
    {
        if ($this->total_due <= 0) return 100;
        return (int) min(100, round($this->total_paid / $this->total_due * 100));
    }
}