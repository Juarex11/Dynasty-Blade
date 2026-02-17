<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'employee_id',
        'avatar',
        'is_active',
        'phone',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login_at'     => 'datetime',
        'is_active'         => 'boolean',
        'password'          => 'hashed',
    ];

    // ─── Roles ─────────────────────────────────────────────────────────────────

    public const ROLES = [
        'admin'        => 'Administrador',
        'manager'      => 'Manager',
        'employee'     => 'Empleado',
        'receptionist' => 'Recepcionista',
        'client'       => 'Cliente',
    ];

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isManager(): bool
    {
        return in_array($this->role, ['admin', 'manager']);
    }

    public function isEmployee(): bool
    {
        return in_array($this->role, ['admin', 'manager', 'employee', 'receptionist']);
    }

    public function getRoleNameAttribute(): string
    {
        return self::ROLES[$this->role] ?? $this->role;
    }

    // ─── Relationships ─────────────────────────────────────────────────────────

    /** Perfil de empleado vinculado */
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    // ─── Accessors ─────────────────────────────────────────────────────────────

    public function getAvatarUrlAttribute(): string
    {
        return $this->avatar
            ? asset('storage/' . $this->avatar)
            : 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&background=e879f9&color=fff';
    }

    // ─── Scopes ────────────────────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}