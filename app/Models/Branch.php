<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Branch extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'address',
        'district',
        'city',
        'phone',
        'email',
        'whatsapp',
        'description',
        'image',
        'latitude',
        'longitude',
        'opening_hours',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'opening_hours' => 'array',
        'is_active'     => 'boolean',
        'latitude'      => 'float',
        'longitude'     => 'float',
    ];

    // ─── Auto-generar slug ─────────────────────────────────────────────────────
    protected static function boot(): void
    {
        parent::boot();
        static::creating(function ($branch) {
            if (empty($branch->slug)) {
                $branch->slug = Str::slug($branch->name);
            }
        });
    }

    // ─── Relationships ─────────────────────────────────────────────────────────

    /** Servicios que se ofrecen en este local */
    public function services()
    {
        return $this->belongsToMany(Service::class, 'service_branch')
            ->withPivot('price_override', 'is_active')
            ->withTimestamps();
    }

    /** Empleados que trabajan en este local */
    public function employees()
    {
        return $this->belongsToMany(Employee::class, 'employee_branch')
            ->withPivot('is_primary')
            ->withTimestamps();
    }

    /** Horarios de empleados en este local */
    public function schedules()
    {
        return $this->hasMany(EmployeeSchedule::class);
    }

    // ─── Scopes ────────────────────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // ─── Accessors ─────────────────────────────────────────────────────────────

    public function getImageUrlAttribute(): string
    {
        return $this->image
            ? asset('storage/' . $this->image)
            : asset('images/branch-placeholder.png');
    }

    public function getFullAddressAttribute(): string
    {
        return implode(', ', array_filter([
            $this->address,
            $this->district,
            $this->city,
        ]));
    }
}