<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CourseSession extends Model
{
    protected $fillable = [
        'course_opening_id', 'session_number', 'date',
        'time_start', 'time_end', 'topic', 'status', 'notes',
    ];

    protected $casts = ['date' => 'date'];

    public function opening(): BelongsTo  { return $this->belongsTo(CourseOpening::class, 'course_opening_id'); }
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