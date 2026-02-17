<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employee extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'first_name', 'last_name', 'dni', 'birth_date', 'gender', 'phone', 'email',
        'address', 'photo', 'position', 'bio', 'hire_date', 'end_date', 'commission_rate',
        'employment_type', 'instagram', 'tiktok', 'has_system_access', 'user_id',
        'is_active', 'sort_order',
    ];

    protected $casts = [
        'birth_date'        => 'date',
        'hire_date'         => 'date',
        'end_date'          => 'date',
        'commission_rate'   => 'float',
        'has_system_access' => 'boolean',
        'is_active'         => 'boolean',
    ];

    // ─── Relationships ─────────────────────────────────────────────────────────

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function branches()
    {
        return $this->belongsToMany(Branch::class, 'employee_branch')
            ->withPivot('is_primary')
            ->withTimestamps();
    }

    public function primaryBranch()
    {
        return $this->branches()->wherePivot('is_primary', true)->first();
    }

    public function services()
    {
        return $this->belongsToMany(Service::class, 'employee_service')
            ->withPivot('price_override', 'skill_level', 'is_active')
            ->withTimestamps();
    }

    /** Todos los cursos vinculados (como estudiante o instructor) */
    public function courses()
    {
        return $this->belongsToMany(Course::class, 'employee_course')
            ->withPivot('role', 'status', 'enrolled_at', 'completed_at', 'certificate_number', 'score', 'notes')
            ->withTimestamps();
    }

    /** Cursos donde es instructor */
    public function teachingCourses()
    {
        return $this->belongsToMany(Course::class, 'employee_course')
            ->withPivot('role', 'notes')
            ->wherePivot('role', 'instructor')
            ->withTimestamps();
    }

    /** Cursos donde es estudiante */
    public function enrolledCourses()
    {
        return $this->belongsToMany(Course::class, 'employee_course')
            ->withPivot('role', 'status', 'enrolled_at', 'completed_at', 'score', 'notes')
            ->wherePivot('role', 'estudiante')
            ->withTimestamps();
    }

    public function schedules()
    {
        return $this->hasMany(EmployeeSchedule::class);
    }

    public function scheduleExceptions()
    {
        return $this->hasMany(EmployeeScheduleException::class);
    }

    // ─── Scopes ────────────────────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('employees.is_active', true);
    }

    public function scopeWithAccess($query)
    {
        return $query->where('has_system_access', true);
    }

    // ─── Accessors ─────────────────────────────────────────────────────────────

    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function getPhotoUrlAttribute(): string
    {
        return $this->photo ? asset('storage/' . $this->photo) : asset('images/avatar-placeholder.png');
    }

    public function getTenureAttribute(): string
    {
        $diff = $this->hire_date->diff(now());
        $parts = [];
        if ($diff->y > 0) $parts[] = $diff->y . ' ' . ($diff->y === 1 ? 'año' : 'años');
        if ($diff->m > 0) $parts[] = $diff->m . ' ' . ($diff->m === 1 ? 'mes' : 'meses');
        return implode(' y ', $parts) ?: 'Reciente';
    }

    public function getScheduleForDay(int $dayOfWeek, int $branchId): ?EmployeeSchedule
    {
        return $this->schedules()
            ->where('branch_id', $branchId)
            ->where('day_of_week', $dayOfWeek)
            ->where('is_working', true)
            ->first();
    }
}