<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Service extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'service_category_id',
        'name',
        'slug',
        'short_description',
        'description',
        'cover_image',
        'price',
        'price_max',
        'duration_minutes',
        'buffer_minutes',
        'requires_deposit',
        'deposit_amount',
        'online_booking',
        'is_active',
        'is_featured',
        'sort_order',
    ];

    protected $casts = [
        'price'            => 'float',
        'price_max'        => 'float',
        'deposit_amount'   => 'float',
        'requires_deposit' => 'boolean',
        'online_booking'   => 'boolean',
        'is_active'        => 'boolean',
        'is_featured'      => 'boolean',
    ];

    protected static function boot(): void
    {
        parent::boot();
        static::creating(function ($service) {
            if (empty($service->slug)) {
                $service->slug = Str::slug($service->name);
            }
        });
    }

    // ─── Relationships ─────────────────────────────────────────────────────────

    public function category()
    {
        return $this->belongsTo(ServiceCategory::class, 'service_category_id');
    }

    /** Galería de imágenes */
    public function images()
    {
        return $this->hasMany(ServiceImage::class)->orderBy('sort_order');
    }

    /** Imagen principal de la galería */
    public function primaryImage()
    {
        return $this->hasOne(ServiceImage::class)->where('is_primary', true);
    }

    /** Sedes donde se ofrece el servicio */
    public function branches()
    {
        return $this->belongsToMany(Branch::class, 'service_branch')
            ->withPivot('price_override', 'is_active')
            ->withTimestamps();
    }

    /** Empleados que realizan este servicio */
    public function employees()
    {
        return $this->belongsToMany(Employee::class, 'employee_service')
            ->withPivot('price_override', 'skill_level', 'is_active')
            ->withTimestamps();
    }

    // ─── Scopes ────────────────────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    // ─── Accessors ─────────────────────────────────────────────────────────────

    public function getCoverImageUrlAttribute(): string
    {
        return $this->cover_image
            ? asset('storage/' . $this->cover_image)
            : asset('images/service-placeholder.png');
    }

    /** Precio formateado: "S/. 50" o "S/. 50 - 80" si tiene rango */
    public function getPriceDisplayAttribute(): string
    {
        if ($this->price_max && $this->price_max > $this->price) {
            return 'S/. ' . number_format($this->price, 0) . ' - ' . number_format($this->price_max, 0);
        }
        return 'S/. ' . number_format($this->price, 0);
    }

    /** Duración formateada: "1h 30min" */
    public function getDurationDisplayAttribute(): string
    {
        $hours   = intdiv($this->duration_minutes, 60);
        $minutes = $this->duration_minutes % 60;

        if ($hours > 0 && $minutes > 0) {
            return "{$hours}h {$minutes}min";
        } elseif ($hours > 0) {
            return "{$hours}h";
        }
        return "{$minutes}min";
    }
}