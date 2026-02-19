<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Course extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'course_category_id', 'name', 'slug', 'short_description', 'description',
        'cover_image', 'price', 'price_max', 'modality', 'level',
        'instructor', 'max_students', 'has_certificate', 'is_active', 'is_featured', 'sort_order',
    ];

    protected $casts = [
        'price'          => 'float',
        'price_max'      => 'float',
        'has_certificate'=> 'boolean',
        'is_active'      => 'boolean',
        'is_featured'    => 'boolean',
    ];

    protected static function boot(): void
    {
        parent::boot();
        static::creating(fn($c) => $c->slug = $c->slug ?: Str::slug($c->name));
    }

    // ─── Relationships ─────────────────────────────────────────────────────────

    public function category()
    {
        return $this->belongsTo(CourseCategory::class, 'course_category_id');
    }

    public function branches()
    {
        return $this->belongsToMany(Branch::class, 'course_branch')
            ->withPivot('is_active')
            ->withTimestamps();
    }

    /** Todos los empleados vinculados al curso (instructores) */
    public function employees()
    {
        return $this->belongsToMany(Employee::class, 'employee_course')
            ->withPivot('role', 'status', 'enrolled_at', 'completed_at', 'certificate_number', 'score', 'notes')
            ->withTimestamps();
    }

    /** Solo los instructores */
    public function instructors()
    {
        return $this->belongsToMany(Employee::class, 'employee_course')
            ->withPivot('role', 'notes')
            ->wherePivot('role', 'instructor')
            ->withTimestamps();
    }

    /** Aperturas (ediciones/grupos) de este curso */
    public function openings()
    {
        return $this->hasMany(CourseOpening::class);
    }

    // ─── Scopes ────────────────────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('courses.is_active', true);
    }

    // ─── Accessors ─────────────────────────────────────────────────────────────

    public function getPriceDisplayAttribute(): string
    {
        if ($this->price == 0) return 'Gratis';
        if ($this->price_max && $this->price_max > $this->price) {
            return 'S/. ' . number_format($this->price, 0) . ' - ' . number_format($this->price_max, 0);
        }
        return 'S/. ' . number_format($this->price, 0);
    }

    /**
     * Total de estudiantes inscritos en todas las aperturas de este curso.
     * Usar con ->withSum('openings', 'enrolled_count') en el query.
     */
    public function getOpeningsEnrolledCountAttribute(): int
    {
        // Populated by withSum() as 'openings_sum_enrolled_count'
        return (int) ($this->openings_sum_enrolled_count ?? 0);
    }

    public function getModalityLabelAttribute(): string
    {
        return match($this->modality) {
            'presencial' => 'Presencial', 'online' => 'Online', 'mixto' => 'Mixto', default => $this->modality,
        };
    }

    public function getLevelLabelAttribute(): string
    {
        return match($this->level) {
            'basico' => 'Básico', 'intermedio' => 'Intermedio', 'avanzado' => 'Avanzado', default => $this->level,
        };
    }

    public function getLevelColorAttribute(): string
    {
        return match($this->level) {
            'basico' => 'green', 'intermedio' => 'amber', 'avanzado' => 'red', default => 'gray',
        };
    }

    public function getCoverImageUrlAttribute(): string
    {
        return $this->cover_image ? asset('storage/' . $this->cover_image) : asset('images/course-placeholder.png');
    }
}