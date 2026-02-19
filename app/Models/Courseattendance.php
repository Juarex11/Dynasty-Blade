<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CourseAttendance extends Model
{
    protected $fillable = [
        'course_session_id', 'course_opening_student_id', 'status', 'observation',
    ];

    public function session(): BelongsTo    { return $this->belongsTo(CourseSession::class, 'course_session_id'); }
    public function enrollment(): BelongsTo { return $this->belongsTo(CourseOpeningStudent::class, 'course_opening_student_id'); }
}