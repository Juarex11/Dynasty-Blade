<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseOpening;
use App\Models\CourseOpeningStudent;
use App\Models\CourseSession;
use App\Models\CourseAttendance;
use App\Models\Branch;
use App\Models\Employee;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CourseOpeningController extends Controller
{
    public function index(Request $request)
    {
        $query = CourseOpening::with(['course', 'branch'])->withCount('enrollments');

        if ($request->filled('status'))  $query->where('status', $request->status);
        if ($request->filled('course'))  $query->where('course_id', $request->course);
        if ($request->filled('branch'))  $query->where('branch_id', $request->branch);
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn($q) => $q->where('name', 'like', "%$s%")->orWhere('code', 'like', "%$s%"));
        }

        $openings = $query->orderByDesc('start_date')->paginate(15);
        $courses  = Course::active()->orderBy('name')->get();
        $branches = Branch::active()->get();

        return view('course-openings.index', compact('openings', 'courses', 'branches'));
    }

    public function create(Request $request)
    {
        $courses   = Course::with(['branches', 'instructors'])->active()->orderBy('name')->get();
        $branches  = Branch::active()->orderBy('name')->get();
        $clients   = Client::active()->orderBy('first_name')->get();
        $employees = Employee::active()->orderBy('first_name')->get();
        $selected  = $request->filled('course_id')
            ? Course::with(['instructors', 'branches'])->find($request->course_id)
            : null;

        $codePrefix    = strtoupper(Str::random(3));
        $codeYear      = date('Y');
        $codeSeq       = str_pad(CourseOpening::whereYear('created_at', $codeYear)->count() + 1, 3, '0', STR_PAD_LEFT);
        $suggestedCode = "{$codePrefix}-{$codeYear}-{$codeSeq}";

        return view('course-openings.create', compact(
            'courses', 'branches', 'employees', 'clients', 'selected', 'suggestedCode'
        ));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'course_id'            => 'required|exists:courses,id',
            'branch_id'            => 'nullable|exists:branches,id',
            'code'                 => 'nullable|string|max:30',
            'name'                 => 'nullable|string|max:200',
            'start_date'           => 'required|date',
            'end_date'             => 'nullable|date|after_or_equal:start_date',
            'days_of_week'         => 'nullable|array',
            'days_of_week.*'       => 'integer|between:1,7',
            'session_time_start'   => 'nullable|array',
            'session_time_start.*' => 'nullable|date_format:H:i',
            'session_time_end'     => 'nullable|array',
            'session_time_end.*'   => 'nullable|date_format:H:i',
            'total_sessions'       => 'required|integer|min:1',
            'max_students'         => 'nullable|integer|min:1',
            'price'                => 'nullable|numeric|min:0',
            'price_promo'          => 'nullable|numeric|min:0',
            'promo_until'          => 'nullable|date',
            'promo_label'          => 'nullable|string|max:80',
            'status'               => 'required|in:borrador,publicado,en_curso,finalizado,cancelado',
            'notes'                => 'nullable|string',
            'instructor_ids'       => 'nullable|array',
            'instructor_ids.*'     => 'exists:employees,id',
            'client_ids'           => 'nullable|array',
            'client_ids.*'         => 'exists:clients,id',
            'student_price_paid'     => 'nullable|numeric|min:0',
            'student_payment_status' => 'nullable|in:pendiente,pagado,parcial,becado',
            'generate_sessions'    => 'nullable|boolean',
        ]);

        DB::beginTransaction();
        try {
            // Horarios por día
            $sessionTimes = [];
            foreach ($request->input('days_of_week', []) as $day) {
                $ts = $request->input("session_time_start.{$day}");
                $te = $request->input("session_time_end.{$day}");
                if ($ts || $te) {
                    $sessionTimes[(int) $day] = ['start' => $ts, 'end' => $te];
                }
            }
            $firstTime = collect($sessionTimes)->first();

            $opening = CourseOpening::create([
                'course_id'      => $data['course_id'],
                'branch_id'      => $data['branch_id']   ?? null,
                'code'           => $data['code']        ?? null,
                'name'           => $data['name']        ?? null,
                'start_date'     => $data['start_date'],
                'end_date'       => $data['end_date']    ?? null,
                'time_start'     => $firstTime['start']  ?? null,
                'time_end'       => $firstTime['end']    ?? null,
                'days_of_week'   => !empty($data['days_of_week']) ? $data['days_of_week'] : null,
                'session_times'  => !empty($sessionTimes) ? $sessionTimes : null,
                'total_sessions' => $data['total_sessions'],
                'max_students'   => $data['max_students'] ?? null,
                'price'          => $data['price']       ?? null,
                'price_promo'    => $data['price_promo'] ?? null,
                'promo_until'    => $data['promo_until'] ?? null,
                'promo_label'    => $data['promo_label'] ?? null,
                'status'         => $data['status'],
                'notes'          => $data['notes']       ?? null,
                'enrolled_count' => 0,
            ]);

            // Instructores
            if (!empty($data['instructor_ids'])) {
                $opening->instructors()->sync($data['instructor_ids']);
            }

            // Estudiantes — solo clientes externos
            $enrolledAt = now()->toDateString();
            $pricePaid  = $data['student_price_paid']     ?? $opening->price;
            $payStatus  = $data['student_payment_status'] ?? 'pendiente';

            foreach ($request->input('client_ids', []) as $clientId) {
                CourseOpeningStudent::create([
                    'course_opening_id' => $opening->id,
                    'client_id'         => (int) $clientId,
                    'employee_id'       => null,
                    'price_paid'        => $pricePaid,
                    'payment_status'    => $payStatus,
                    'enrolled_at'       => $enrolledAt,
                    'status'            => 'inscrito',
                ]);
            }

            $opening->syncEnrolledCount();

            // Generar sesiones automáticas
            if ($request->boolean('generate_sessions')) {
                $this->generateSessions($opening, $sessionTimes);
            }

            DB::commit();

        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Error al guardar: ' . $e->getMessage());
        }

        return redirect()
            ->route('course-openings.show', $opening->id)
            ->with('success', 'Apertura creada correctamente.');
    }

    public function show(CourseOpening $courseOpening)
    {
        $courseOpening->load([
            'course.category', 'branch',
            'instructors',
            'enrollments.employee',
            'enrollments.client',
            'sessions.attendances',
        ]);

        $totalStudents  = $courseOpening->enrollments->count();
        $attendanceRate = null;

        $realizedSessions = $courseOpening->sessions->where('status', 'realizada');
        if ($realizedSessions->count() > 0) {
            $totalPresent  = 0;
            $totalPossible = 0;
            foreach ($realizedSessions as $session) {
                $totalPresent  += $session->attendances->whereIn('status', ['presente', 'tardanza'])->count();
                $totalPossible += $totalStudents;
            }
            $attendanceRate = $totalPossible > 0 ? round($totalPresent / $totalPossible * 100) : 0;
        }

        return view('course-openings.show', compact('courseOpening', 'attendanceRate'));
    }

    public function edit(CourseOpening $courseOpening)
    {
        $courseOpening->load(['instructors', 'enrollments.employee', 'enrollments.client']);
        $courses   = Course::active()->orderBy('name')->get();
        $branches  = Branch::active()->orderBy('name')->get();
        $employees = Employee::active()->orderBy('first_name')->get();
        $clients   = Client::active()->orderBy('first_name')->get();

        return view('course-openings.edit', compact('courseOpening', 'courses', 'branches', 'employees', 'clients'));
    }

    public function update(Request $request, CourseOpening $courseOpening)
    {
        $data = $request->validate([
            'course_id'            => 'required|exists:courses,id',
            'branch_id'            => 'nullable|exists:branches,id',
            'code'                 => 'nullable|string|max:30',
            'name'                 => 'nullable|string|max:200',
            'start_date'           => 'required|date',
            'end_date'             => 'nullable|date|after_or_equal:start_date',
            'days_of_week'         => 'nullable|array',
            'days_of_week.*'       => 'integer|between:1,7',
            'session_time_start'   => 'nullable|array',
            'session_time_start.*' => 'nullable|date_format:H:i',
            'session_time_end'     => 'nullable|array',
            'session_time_end.*'   => 'nullable|date_format:H:i',
            'total_sessions'       => 'required|integer|min:1',
            'max_students'         => 'nullable|integer|min:1',
            'price'                => 'nullable|numeric|min:0',
            'price_promo'          => 'nullable|numeric|min:0',
            'promo_until'          => 'nullable|date',
            'promo_label'          => 'nullable|string|max:80',
            'status'               => 'required|in:borrador,publicado,en_curso,finalizado,cancelado',
            'notes'                => 'nullable|string',
            'instructor_ids'       => 'nullable|array',
            'instructor_ids.*'     => 'exists:employees,id',
        ]);

        $sessionTimes = [];
        foreach ($request->input('days_of_week', []) as $day) {
            $ts = $request->input("session_time_start.{$day}");
            $te = $request->input("session_time_end.{$day}");
            if ($ts || $te) {
                $sessionTimes[(int) $day] = ['start' => $ts, 'end' => $te];
            }
        }
        $firstTime = collect($sessionTimes)->first();

        $courseOpening->update(array_merge(
            collect($data)->except(['instructor_ids', 'session_time_start', 'session_time_end'])->toArray(),
            [
                'time_start'    => $firstTime['start'] ?? null,
                'time_end'      => $firstTime['end']   ?? null,
                'session_times' => !empty($sessionTimes) ? $sessionTimes : null,
            ]
        ));

        $courseOpening->instructors()->sync($request->input('instructor_ids', []));

        return redirect()
            ->route('course-openings.show', $courseOpening->id)
            ->with('success', 'Apertura actualizada.');
    }

    public function destroy(CourseOpening $courseOpening)
    {
        $courseOpening->delete();
        return redirect()->route('course-openings.index')->with('success', 'Apertura eliminada.');
    }

    public function saveAttendance(Request $request, CourseSession $session)
    {
        $data = $request->validate([
            'attendance'               => 'required|array',
            'attendance.*.id'          => 'required|exists:course_opening_student,id',
            'attendance.*.status'      => 'required|in:presente,tardanza,ausente,justificado',
            'attendance.*.observation' => 'nullable|string|max:200',
            'session_status'           => 'nullable|in:programada,realizada,cancelada,postergada',
            'topic'                    => 'nullable|string|max:200',
        ]);

        DB::beginTransaction();
        try {
            foreach ($data['attendance'] as $row) {
                CourseAttendance::updateOrCreate(
                    [
                        'course_session_id'         => $session->id,
                        'course_opening_student_id' => $row['id'],
                    ],
                    [
                        'status'      => $row['status'],
                        'observation' => $row['observation'] ?? null,
                    ]
                );
            }
            if ($request->filled('session_status')) {
                $session->update(['status' => $request->session_status, 'topic' => $request->topic]);
            }
            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'Error al guardar asistencia.');
        }

        return back()->with('success', 'Asistencia guardada correctamente.');
    }

    // ─── Generar sesiones ─────────────────────────────────────────────────────

    private function generateSessions(CourseOpening $opening, array $sessionTimes = []): void
    {
        if (!$opening->start_date || !$opening->end_date || empty($opening->days_of_week)) return;

        $current = $opening->start_date->copy();
        $end     = $opening->end_date->copy();
        $num     = 1;

        while ($current->lte($end) && $num <= $opening->total_sessions) {
            $isoDay = $current->isoWeekday();
            if (in_array($isoDay, $opening->days_of_week)) {
                $dayTimes = $sessionTimes[$isoDay] ?? null;
                CourseSession::create([
                    'course_opening_id' => $opening->id,
                    'session_number'    => $num,
                    'date'              => $current->toDateString(),
                    'time_start'        => $dayTimes['start'] ?? $opening->time_start,
                    'time_end'          => $dayTimes['end']   ?? $opening->time_end,
                    'status'            => 'programada',
                ]);
                $num++;
            }
            $current->addDay();
        }
    }
}