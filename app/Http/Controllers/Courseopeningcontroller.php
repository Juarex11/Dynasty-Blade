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
            $query->where(fn($q) => $q->where('name','like',"%$s%")->orWhere('code','like',"%$s%"));
        }

        $openings = $query->orderByDesc('start_date')->paginate(15);
        $courses  = Course::active()->orderBy('name')->get();
        $branches = Branch::active()->get();

        return view('course-openings.index', compact('openings', 'courses', 'branches'));
    }

    public function create(Request $request)
    {
        $courses   = Course::with(['branches','instructors'])->active()->orderBy('name')->get();
        $branches  = Branch::active()->orderBy('name')->get();
        $employees = Employee::active()->orderBy('first_name')->get();
        $clients   = Client::active()->orderBy('first_name')->get();
        $selected  = $request->filled('course_id') ? Course::with(['instructors','employees'])->find($request->course_id) : null;

        return view('course-openings.create', compact('courses', 'branches', 'employees', 'clients', 'selected'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'course_id'      => 'required|exists:courses,id',
            'branch_id'      => 'nullable|exists:branches,id',
            'code'           => 'nullable|string|max:30',
            'name'           => 'nullable|string|max:200',
            'start_date'     => 'required|date',
            'end_date'       => 'nullable|date|after_or_equal:start_date',
            'time_start'     => 'nullable|date_format:H:i',
            'time_end'       => 'nullable|date_format:H:i',
            'days_of_week'   => 'nullable|array',
            'days_of_week.*' => 'integer|between:1,7',
            'total_sessions' => 'required|integer|min:1',
            'max_students'   => 'nullable|integer|min:1',
            'price'          => 'nullable|numeric|min:0',
            'price_promo'    => 'nullable|numeric|min:0',
            'promo_until'    => 'nullable|date',
            'promo_label'    => 'nullable|string|max:80',
            'status'         => 'required|in:borrador,publicado,en_curso,finalizado,cancelado',
            'notes'          => 'nullable|string',
            // Instructores y estudiantes
            'instructor_ids'   => 'nullable|array',
            'instructor_ids.*' => 'exists:employees,id',
            'employee_student_ids'   => 'nullable|array',
            'employee_student_ids.*' => 'exists:employees,id',
            'client_ids'       => 'nullable|array',
            'client_ids.*'     => 'exists:clients,id',
            // Precios estudiantes
            'student_price_paid'     => 'nullable|numeric|min:0',
            'student_payment_status' => 'nullable|in:pendiente,pagado,parcial,becado',
            // Generar sesiones automáticamente
            'generate_sessions' => 'nullable|boolean',
        ]);

        DB::beginTransaction();
        try {
            $opening = CourseOpening::create(collect($data)->except([
                'instructor_ids','employee_student_ids','client_ids',
                'student_price_paid','student_payment_status','generate_sessions',
            ])->toArray());

            // Instructores
            if (!empty($data['instructor_ids'])) {
                $opening->instructors()->sync($data['instructor_ids']);
            }

            // Estudiantes empleados
            $enrolledAt = now()->toDateString();
            $pricePaid  = $request->student_price_paid ?? $opening->price;
            $payStatus  = $request->student_payment_status ?? 'pendiente';

            foreach ($request->input('employee_student_ids', []) as $empId) {
                $opening->enrollments()->create([
                    'employee_id'    => $empId,
                    'price_paid'     => $pricePaid,
                    'payment_status' => $payStatus,
                    'enrolled_at'    => $enrolledAt,
                    'status'         => 'inscrito',
                ]);
            }

            // Estudiantes clientes
            foreach ($request->input('client_ids', []) as $clientId) {
                $opening->enrollments()->create([
                    'client_id'      => $clientId,
                    'price_paid'     => $pricePaid,
                    'payment_status' => $payStatus,
                    'enrolled_at'    => $enrolledAt,
                    'status'         => 'inscrito',
                ]);
            }

            $opening->syncEnrolledCount();

            // Generar sesiones automáticas
            if ($request->boolean('generate_sessions') && $data['start_date']) {
                $this->generateSessions($opening);
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Error: ' . $e->getMessage());
        }

        return redirect()->route('course-openings.show', $opening)->with('success', 'Apertura creada correctamente.');
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

        $totalStudents = $courseOpening->enrollments->count();
        $attendanceRate = null;

        if ($courseOpening->sessions->where('status','realizada')->count() > 0) {
            $realized = $courseOpening->sessions->where('status','realizada');
            $totalPresent = 0;
            $totalPossible = 0;
            foreach ($realized as $session) {
                $totalPresent   += $session->attendances->whereIn('status',['presente','tardanza'])->count();
                $totalPossible  += $totalStudents;
            }
            $attendanceRate = $totalPossible > 0 ? round($totalPresent / $totalPossible * 100) : 0;
        }

        return view('course-openings.show', compact('courseOpening', 'attendanceRate'));
    }

    public function edit(CourseOpening $courseOpening)
    {
        $courseOpening->load(['instructors','enrollments.employee','enrollments.client']);
        $courses   = Course::active()->orderBy('name')->get();
        $branches  = Branch::active()->orderBy('name')->get();
        $employees = Employee::active()->orderBy('first_name')->get();
        $clients   = Client::active()->orderBy('first_name')->get();

        return view('course-openings.edit', compact('courseOpening', 'courses', 'branches', 'employees', 'clients'));
    }

    public function update(Request $request, CourseOpening $courseOpening)
    {
        $data = $request->validate([
            'course_id'      => 'required|exists:courses,id',
            'branch_id'      => 'nullable|exists:branches,id',
            'code'           => 'nullable|string|max:30',
            'name'           => 'nullable|string|max:200',
            'start_date'     => 'required|date',
            'end_date'       => 'nullable|date|after_or_equal:start_date',
            'time_start'     => 'nullable|date_format:H:i',
            'time_end'       => 'nullable|date_format:H:i',
            'days_of_week'   => 'nullable|array',
            'days_of_week.*' => 'integer|between:1,7',
            'total_sessions' => 'required|integer|min:1',
            'max_students'   => 'nullable|integer|min:1',
            'price'          => 'nullable|numeric|min:0',
            'price_promo'    => 'nullable|numeric|min:0',
            'promo_until'    => 'nullable|date',
            'promo_label'    => 'nullable|string|max:80',
            'status'         => 'required|in:borrador,publicado,en_curso,finalizado,cancelado',
            'notes'          => 'nullable|string',
            'instructor_ids'   => 'nullable|array',
            'instructor_ids.*' => 'exists:employees,id',
        ]);

        $courseOpening->update(collect($data)->except(['instructor_ids'])->toArray());
        $courseOpening->instructors()->sync($request->input('instructor_ids', []));

        return redirect()->route('course-openings.show', $courseOpening)->with('success', 'Apertura actualizada.');
    }

    public function destroy(CourseOpening $courseOpening)
    {
        $courseOpening->delete();
        return redirect()->route('course-openings.index')->with('success', 'Apertura eliminada.');
    }

    /**
     * Guardar asistencia de una sesión (AJAX o form)
     */
    public function saveAttendance(Request $request, CourseSession $session)
    {
        $data = $request->validate([
            'attendance'           => 'required|array',
            'attendance.*.id'      => 'required|exists:course_opening_student,id',
            'attendance.*.status'  => 'required|in:presente,tardanza,ausente,justificado',
            'attendance.*.observation' => 'nullable|string|max:200',
            'session_status'       => 'nullable|in:programada,realizada,cancelada,postergada',
            'topic'                => 'nullable|string|max:200',
        ]);

        DB::beginTransaction();
        try {
            foreach ($data['attendance'] as $row) {
                CourseAttendance::updateOrCreate(
                    ['course_session_id' => $session->id, 'course_opening_student_id' => $row['id']],
                    ['status' => $row['status'], 'observation' => $row['observation'] ?? null]
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

    /**
     * Generar sesiones automáticas según días de semana y rango de fechas
     */
    private function generateSessions(CourseOpening $opening): void
    {
        if (!$opening->start_date || !$opening->end_date || empty($opening->days_of_week)) return;

        $current = $opening->start_date->copy();
        $end     = $opening->end_date->copy();
        $num     = 1;

        while ($current->lte($end) && $num <= $opening->total_sessions) {
            // 1=Lun ... 7=Dom (isoWeekday)
            if (in_array($current->isoWeekday(), $opening->days_of_week)) {
                CourseSession::create([
                    'course_opening_id' => $opening->id,
                    'session_number'    => $num,
                    'date'              => $current->toDateString(),
                    'time_start'        => $opening->time_start,
                    'time_end'          => $opening->time_end,
                    'status'            => 'programada',
                ]);
                $num++;
            }
            $current->addDay();
        }
    }
}