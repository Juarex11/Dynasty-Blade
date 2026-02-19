<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CourseOpening extends Model
{
    protected $fillable = [
        'course_id', 'branch_id', 'code', 'name',
        'start_date', 'end_date', 'time_start', 'time_end',
        'days_of_week', 'total_sessions', 'max_students', 'enrolled_count',
        'price', 'price_promo', 'promo_until', 'promo_label',
        'status', 'notes',
    ];

    protected $casts = [
        'start_date'   => 'date',
        'end_date'     => 'date',
        'promo_until'  => 'date',
        'days_of_week' => 'array',
    ];

    // Días de semana en español
    public static array $DAY_NAMES = [1=>'Lun',2=>'Mar',3=>'Mié',4=>'Jue',5=>'Vie',6=>'Sáb',7=>'Dom'];

    public function getDaysLabelAttribute(): string
    {
        if (!$this->days_of_week) return '—';
        return collect($this->days_of_week)
            ->map(fn($d) => self::$DAY_NAMES[$d] ?? $d)
            ->join(', ');
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'borrador'   => 'Borrador',
            'publicado'  => 'Publicado',
            'en_curso'   => 'En curso',
            'finalizado' => 'Finalizado',
            'cancelado'  => 'Cancelado',
            default      => $this->status,
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'borrador'   => 'gray',
            'publicado'  => 'blue',
            'en_curso'   => 'amber',
            'finalizado' => 'green',
            'cancelado'  => 'red',
            default      => 'gray',
        };
    }

    public function getEffectivePriceAttribute(): ?float
    {
        if ($this->price_promo && $this->promo_until && $this->promo_until->isFuture()) {
            return (float) $this->price_promo;
        }
        return $this->price ? (float) $this->price : null;
    }

    public function getAvailableSlotsAttribute(): ?int
    {
        $max = $this->max_students ?? $this->course->max_students ?? null;
        return $max !== null ? max(0, $max - $this->enrolled_count) : null;
    }

    public function getDisplayNameAttribute(): string
    {
        return $this->name ?: $this->course->name . ' — ' . $this->start_date->format('M Y');
    }

    // ── Relaciones ────────────────────────────────────────────────────────────

    public function course(): BelongsTo    { return $this->belongsTo(Course::class); }
    public function branch(): BelongsTo    { return $this->belongsTo(Branch::class); }
    public function sessions(): HasMany    { return $this->hasMany(CourseSession::class)->orderBy('date')->orderBy('session_number'); }
    public function enrollments(): HasMany { return $this->hasMany(CourseOpeningStudent::class); }

    public function instructors(): BelongsToMany
    {
        return $this->belongsToMany(Employee::class, 'course_opening_instructor', 'course_opening_id', 'employee_id');
    }

    public function employeeStudents(): BelongsToMany
    {
        return $this->belongsToMany(Employee::class, 'course_opening_student', 'course_opening_id', 'employee_id')
                    ->withPivot(['price_paid','payment_status','enrolled_at','status','certificate_issued','notes'])
                    ->withTimestamps();
    }

    public function clientStudents(): BelongsToMany
    {
        return $this->belongsToMany(Client::class, 'course_opening_student', 'course_opening_id', 'client_id')
                    ->withPivot(['price_paid','payment_status','enrolled_at','status','certificate_issued','notes'])
                    ->withTimestamps();
    }

    public function syncEnrolledCount(): void
    {
        $this->enrolled_count = $this->enrollments()->count();
        $this->saveQuietly();
    }

    // ── Scopes ────────────────────────────────────────────────────────────────
    public function scopeActive($q)     { return $q->whereIn('status', ['publicado','en_curso']); }
    public function scopeUpcoming($q)   { return $q->where('start_date', '>=', now()->toDateString()); }
}

// ─────────────────────────────────────────────────────────────────────────────

class CourseSession extends Model
{
    protected $fillable = [
        'course_opening_id','session_number','date',
        'time_start','time_end','topic','status','notes',
    ];

    protected $casts = ['date' => 'date'];

    public function opening(): BelongsTo { return $this->belongsTo(CourseOpening::class, 'course_opening_id'); }

    public function attendances(): HasMany { return $this->hasMany(CourseAttendance::class); }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'programada'  => 'Programada',
            'realizada'   => 'Realizada',
            'cancelada'   => 'Cancelada',
            'postergada'  => 'Postergada',
            default       => $this->status,
        };
    }
}

// ─────────────────────────────────────────────────────────────────────────────

class CourseOpeningStudent extends Model
{
    protected $table = 'course_opening_student';

    protected $fillable = [
        'course_opening_id','employee_id','client_id',
        'price_paid','payment_status','enrolled_at','status','certificate_issued','notes',
    ];

    protected $casts = [
        'enrolled_at'        => 'date',
        'certificate_issued' => 'boolean',
    ];

    public function opening(): BelongsTo  { return $this->belongsTo(CourseOpening::class, 'course_opening_id'); }
    public function employee(): BelongsTo { return $this->belongsTo(Employee::class); }
    public function client(): BelongsTo   { return $this->belongsTo(Client::class); }
    public function attendances(): HasMany { return $this->hasMany(CourseAttendance::class, 'course_opening_student_id'); }

    public function getPersonNameAttribute(): string
    {
        return $this->employee?->full_name ?? $this->client?->full_name ?? '—';
    }

    public function getPersonTypeAttribute(): string
    {
        return $this->employee_id ? 'employee' : 'client';
    }
}

// ─────────────────────────────────────────────────────────────────────────────

class CourseAttendance extends Model
{
    protected $fillable = ['course_session_id','course_opening_student_id','status','observation'];

    public function session(): BelongsTo    { return $this->belongsTo(CourseSession::class, 'course_session_id'); }
    public function enrollment(): BelongsTo { return $this->belongsTo(CourseOpeningStudent::class, 'course_opening_student_id'); }
}