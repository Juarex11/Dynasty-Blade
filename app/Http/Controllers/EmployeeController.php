<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Employee;
use App\Models\EmployeeSchedule;
use App\Models\Service;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        $query = Employee::with(['branches', 'user'])
            ->withCount('services');

        if ($request->filled('branch')) {
            $query->whereHas('branches', fn($q) => $q->where('branches.id', $request->branch));
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('first_name', 'like', '%' . $request->search . '%')
                    ->orWhere('last_name', 'like', '%' . $request->search . '%')
                    ->orWhere('position', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $employees = $query->orderBy('sort_order')->orderBy('first_name')->paginate(15);
        $branches  = Branch::active()->get();

        return view('employees.index', compact('employees', 'branches'));
    }

    public function create()
    {
        $branches = Branch::active()->orderBy('name')->get();
        $services = Service::active()->with('category')->orderBy('name')->get();

        return view('employees.create', compact('branches', 'services'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            // Datos personales
            'first_name'      => 'required|string|max:80',
            'last_name'       => 'required|string|max:80',
            'dni'             => 'nullable|string|max:20|unique:employees',
            'birth_date'      => 'nullable|date|before:today',
            'gender'          => 'nullable|in:male,female,other',
            'phone'           => 'nullable|string|max:20',
            'email'           => 'nullable|email|max:150|unique:employees',
            'address'         => 'nullable|string|max:255',
            'photo'           => 'nullable|image|max:2048',
            // Datos laborales
            'position'        => 'required|string|max:100',
            'bio'             => 'nullable|string',
            'hire_date'       => 'required|date',
            'commission_rate' => 'nullable|numeric|min:0|max:100',
            'employment_type' => 'required|in:full_time,part_time,freelance',
            'instagram'       => 'nullable|string|max:100',
            'tiktok'          => 'nullable|string|max:100',
            'is_active'       => 'boolean',
            // Sedes
            'branch_ids'      => 'required|array|min:1',
            'branch_ids.*'    => 'exists:branches,id',
            'primary_branch'  => 'required|exists:branches,id',
            // Servicios
            'service_ids'     => 'nullable|array',
            'service_ids.*'   => 'exists:services,id',
            // Cursos
            'instructor_course_ids'   => 'nullable|array',
            'instructor_course_ids.*' => 'exists:courses,id',
            'student_course_ids'      => 'nullable|array',
            'student_course_ids.*'    => 'exists:courses,id',
            // Acceso al sistema
            'has_system_access' => 'boolean',
            'user_name'         => 'nullable|required_if:has_system_access,1|string|max:80',
            'user_email'        => 'nullable|required_if:has_system_access,1|email|unique:users,email',
            'user_password'     => 'nullable|required_if:has_system_access,1|string|min:8',
            'user_role'         => 'nullable|in:employee,manager,receptionist',
        ]);

        DB::beginTransaction();
        try {
            // Subir foto
            if ($request->hasFile('photo')) {
                $data['photo'] = $request->file('photo')->store('employees', 'public');
            }

            // Crear usuario del sistema si aplica
            if ($data['has_system_access'] ?? false) {
                $user = User::create([
                    'name'     => $data['user_name'],
                    'email'    => $data['user_email'],
                    'password' => Hash::make($data['user_password']),
                    'role'     => $data['user_role'] ?? 'employee',
                ]);
                $data['user_id'] = $user->id;
            }

            $employee = Employee::create($data);

            // Vincular usuario al empleado (relación inversa)
            if (isset($user)) {
                $user->update(['employee_id' => $employee->id]);
            }

            // ── Sedes ──────────────────────────────────────────────────────────
            $branchSync = [];
            foreach ($data['branch_ids'] as $branchId) {
                $branchSync[$branchId] = ['is_primary' => $branchId == $data['primary_branch']];
            }
            $employee->branches()->sync($branchSync);

            // ── Servicios ──────────────────────────────────────────────────────
            $employee->services()->sync($request->input('service_ids', []));

            // ── Cursos ─────────────────────────────────────────────────────────
            $courseSync          = [];
            $instructorCourseIds = $request->input('instructor_course_ids', []);
            $studentCourseIds    = $request->input('student_course_ids', []);

            foreach ($instructorCourseIds as $courseId) {
                $courseSync[$courseId] = ['role' => 'instructor'];
            }
            foreach ($studentCourseIds as $courseId) {
                // Si ya está como instructor en ese curso, se ignora
                if (!in_array($courseId, $instructorCourseIds)) {
                    $courseSync[$courseId] = [
                        'role'        => 'estudiante',
                        'status'      => 'inscrito',
                        'enrolled_at' => now()->toDateString(),
                    ];
                }
            }
            if (!empty($courseSync)) {
                $employee->courses()->sync($courseSync);
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Error al guardar: ' . $e->getMessage());
        }

        return redirect()
            ->route('employees.show', $employee)
            ->with('success', 'Empleado creado correctamente.');
    }

    public function show(Employee $employee)
    {
        $employee->load([
            'user',
            'branches',
            'services.category',
            'schedules.branch',
        ]);

        return view('employees.show', compact('employee'));
    }

    public function edit(Employee $employee)
    {
        $employee->load(['branches', 'services', 'user']);
        $branches = Branch::active()->orderBy('name')->get();
        $services = Service::active()->with('category')->orderBy('name')->get();

        return view('employees.edit', compact('employee', 'branches', 'services'));
    }

public function update(Request $request, Employee $employee)
{
    $data = $request->validate([
        'first_name'        => 'required|string|max:80',
        'last_name'         => 'required|string|max:80',
        'dni'               => 'nullable|string|max:20|unique:employees,dni,' . $employee->id,
        'birth_date'        => 'nullable|date|before:today',
        'gender'            => 'nullable|in:male,female,other',
        'phone'             => 'nullable|string|max:20',
        'email'             => 'nullable|email|max:150|unique:employees,email,' . $employee->id,
        'address'           => 'nullable|string|max:255',
        'photo'             => 'nullable|image|max:2048',
        'position'          => 'required|string|max:100',
        'bio'               => 'nullable|string',
        'hire_date'         => 'required|date',
        'end_date'          => 'nullable|date|after:hire_date',
        'commission_rate'   => 'nullable|numeric|min:0|max:100',
        'employment_type'   => 'required|in:full_time,part_time,freelance',
        'instagram'         => 'nullable|string|max:100',
        'tiktok'            => 'nullable|string|max:100',
        // Sedes
        'branch_ids'        => 'required|array|min:1',
        'branch_ids.*'      => 'exists:branches,id',
        'primary_branch'    => 'required|exists:branches,id',
        // Servicios — nullable array, puede llegar vacío
        'service_ids'       => 'nullable|array',
        'service_ids.*'     => 'exists:services,id',
        // Acceso al sistema
        'has_system_access' => 'nullable|boolean',
    ]);

    // is_active lo leemos directamente del request (el hidden+checkbox envía 0 o 1)
    $data['is_active'] = $request->boolean('is_active');

    DB::beginTransaction();
    try {
        // Foto
        if ($request->hasFile('photo')) {
            if ($employee->photo) {
                Storage::disk('public')->delete($employee->photo);
            }
            $data['photo'] = $request->file('photo')->store('employees', 'public');
        }

        // Actualizar campos del empleado (excluir relaciones del array)
        $employee->update(collect($data)->except([
            'branch_ids', 'primary_branch', 'service_ids', 'has_system_access',
        ])->toArray());

        // ── Sedes ──────────────────────────────────────────────────────────────
        $branchSync = [];
        foreach ($data['branch_ids'] as $branchId) {
            $branchSync[$branchId] = ['is_primary' => $branchId == $data['primary_branch']];
        }
        $employee->branches()->sync($branchSync);

        // ── Servicios ──────────────────────────────────────────────────────────
        // Usamos $request->input() en lugar de $data para capturar array vacío correctamente
        $serviceIds = $request->input('service_ids', []);
        $employee->services()->sync($serviceIds);

        // ── Acceso al sistema ─────────────────────────────────────────────────
        $hasAccess = $request->boolean('has_system_access');

        if ($hasAccess && !$employee->user_id) {
            $userData = $request->validate([
                'user_name'     => 'required|string|max:80',
                'user_email'    => 'required|email|unique:users,email',
                'user_password' => 'required|string|min:8',
                'user_role'     => 'nullable|in:employee,manager,receptionist',
            ]);

            $user = User::create([
                'name'        => $userData['user_name'],
                'email'       => $userData['user_email'],
                'password'    => Hash::make($userData['user_password']),
                'role'        => $userData['user_role'] ?? 'employee',
                'employee_id' => $employee->id,
            ]);
            $employee->update(['user_id' => $user->id]);

        } elseif (!$hasAccess && $employee->user_id) {
            $employee->user?->update(['is_active' => false]);
        }

        DB::commit();
    } catch (\Throwable $e) {
        DB::rollBack();
        return back()->withInput()->with('error', 'Error: ' . $e->getMessage());
    }

    return redirect()
        ->route('employees.show', $employee)
        ->with('success', 'Empleado actualizado correctamente.');
}

    public function destroy(Employee $employee)
    {
        if ($employee->photo) {
            Storage::disk('public')->delete($employee->photo);
        }
        $employee->delete();

        return redirect()->route('employees.index')->with('success', 'Empleado eliminado.');
    }

    // ─── Horarios ─────────────────────────────────────────────────────────────

    public function schedules(Employee $employee)
    {
        $employee->load(['schedules.branch', 'branches']);
        $branches = $employee->branches;

        return view('employees.schedules', compact('employee', 'branches'));
    }

    public function updateSchedules(Request $request, Employee $employee)
    {
        $request->validate([
            'schedules'                 => 'required|array',
            'schedules.*.branch_id'     => 'required|exists:branches,id',
            'schedules.*.day_of_week'   => 'required|integer|between:0,6',
            'schedules.*.is_working'    => 'boolean',
            'schedules.*.start_time'    => 'required_if:schedules.*.is_working,true|date_format:H:i',
            'schedules.*.end_time'      => 'required_if:schedules.*.is_working,true|date_format:H:i|after:schedules.*.start_time',
            'schedules.*.break_start'   => 'nullable|date_format:H:i',
            'schedules.*.break_end'     => 'nullable|date_format:H:i',
        ]);

        DB::beginTransaction();
        try {
            foreach ($request->schedules as $scheduleData) {
                EmployeeSchedule::updateOrCreate(
                    [
                        'employee_id' => $employee->id,
                        'branch_id'   => $scheduleData['branch_id'],
                        'day_of_week' => $scheduleData['day_of_week'],
                    ],
                    $scheduleData + ['employee_id' => $employee->id]
                );
            }
            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'Error al guardar horarios: ' . $e->getMessage());
        }

        return back()->with('success', 'Horarios guardados correctamente.');
    }

    /** Activar o desactivar empleado */
    public function toggleActive(Employee $employee)
    {
        $employee->update(['is_active' => !$employee->is_active]);

        return back()->with('success', 'Estado del empleado actualizado.');
    }
}