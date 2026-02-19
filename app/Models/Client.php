<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Hash;

class Client extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'first_name', 'last_name', 'dni', 'phone', 'email', 'birthdate', 'gender', 'photo',
        'department', 'province', 'district', 'address',
        'acquisition_source', 'referred_by', 'hair_type',
        'services_interest', 'notes', 'tags',
        'visit_count', 'first_visit_at', 'last_visit_at', 'client_type', 'is_active',
        // Nuevos
        'username', 'password',
        'client_mode',
    ];

    protected $casts = [
        'birthdate'         => 'date',
        'first_visit_at'    => 'date',
        'last_visit_at'     => 'date',
        'services_interest' => 'array',
        'is_active'         => 'boolean',
    ];

    protected $hidden = ['password'];

    // ── Accessors ──────────────────────────────────────────────────────────────

    public function getFullNameAttribute(): string
    {
        return trim("{$this->first_name} {$this->last_name}");
    }

    public function getAgeAttribute(): ?int
    {
        return $this->birthdate ? $this->birthdate->age : null;
    }

    public function getClientTypeLabelAttribute(): string
    {
        return match($this->client_type) {
            'nuevo'      => 'Nuevo',
            'recurrente' => 'Recurrente',
            'vip'        => 'VIP',
            'inactivo'   => 'Inactivo',
            'unico'      => 'Único',
            default      => 'Nuevo',
        };
    }

    public function getClientTypeColorAttribute(): string
    {
        return match($this->client_type) {
            'nuevo'      => 'blue',
            'recurrente' => 'violet',
            'vip'        => 'amber',
            'inactivo'   => 'gray',
            'unico'      => 'red',
            default      => 'blue',
        };
    }

    public function getClientModeLabelAttribute(): string
    {
        return match($this->client_mode ?? 'frecuente') {
            'frecuente' => 'Frecuente',
            'ocasional' => 'Ocasional',
            default     => 'Frecuente',
        };
    }

    public function getAcquisitionLabelAttribute(): string
    {
        return match($this->acquisition_source) {
            'instagram' => 'Instagram',
            'facebook'  => 'Facebook',
            'tiktok'    => 'TikTok',
            'google'    => 'Google',
            'referido'  => 'Referido',
            'walk_in'   => 'Walk-in',
            'whatsapp'  => 'WhatsApp',
            'otro'      => 'Otro',
            default     => '—',
        };
    }

    // ── Mutator password ───────────────────────────────────────────────────────

    public function setPasswordAttribute(?string $value): void
    {
        $this->attributes['password'] = $value ? Hash::make($value) : null;
    }

    // ── Métodos de negocio ─────────────────────────────────────────────────────

    public function recalculateType(): void
    {
        $now          = now()->toDateString();
        $daysSinceLast = $this->last_visit_at
            ? $this->last_visit_at->diffInDays($now)
            : null;

        if ($this->visit_count === 0) {
            $this->client_type = 'nuevo';
        } elseif ($this->visit_count >= 5) {
            $this->client_type = $daysSinceLast !== null && $daysSinceLast > 60 ? 'inactivo' : 'vip';
        } elseif ($this->visit_count >= 2) {
            $this->client_type = $daysSinceLast !== null && $daysSinceLast > 60 ? 'inactivo' : 'recurrente';
        } else {
            $this->client_type = $daysSinceLast !== null && $daysSinceLast > 90 ? 'unico' : 'nuevo';
        }
    }

    public function registerVisit(): void
    {
        $today = now()->toDateString();
        $this->visit_count++;
        $this->last_visit_at  = $today;
        if (!$this->first_visit_at) {
            $this->first_visit_at = $today;
        }
        $this->recalculateType();
        $this->save();
    }

    // ── Scopes ─────────────────────────────────────────────────────────────────

    public function scopeActive($q)    { return $q->where('is_active', true); }
    public function scopeVip($q)       { return $q->where('client_type', 'vip'); }
    public function scopeInactive($q)  { return $q->where('client_type', 'inactivo'); }
    public function scopeUnique($q)    { return $q->where('client_type', 'unico'); }
    public function scopeFrequent($q)  { return $q->where('client_mode', 'frecuente'); }
    public function scopeOcasional($q) { return $q->where('client_mode', 'ocasional'); }

    // ── Relaciones ─────────────────────────────────────────────────────────────

    public function courseOpenings(): BelongsToMany
    {
        return $this->belongsToMany(CourseOpening::class, 'course_opening_student', 'client_id', 'course_opening_id')
                    ->withPivot(['price_paid', 'payment_status', 'enrolled_at', 'status', 'certificate_issued', 'notes'])
                    ->withTimestamps();
    }
}