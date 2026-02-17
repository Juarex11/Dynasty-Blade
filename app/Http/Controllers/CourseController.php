<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Course;
use App\Models\CourseCategory;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CourseController extends Controller
{
    public function index(Request $request)
    {
        $query = Course::with(['category', 'branches'])->withCount(['employees', 'instructors', 'students']);

        if ($request->filled('category')) $query->where('course_category_id', $request->category);
        if ($request->filled('branch'))   $query->whereHas('branches', fn($q) => $q->where('branches.id', $request->branch));
        if ($request->filled('search'))   $query->where('name', 'like', '%' . $request->search . '%');
        if ($request->filled('level'))    $query->where('level', $request->level);
        if ($request->filled('modality')) $query->where('modality', $request->modality);

        $courses    = $query->orderBy('sort_order')->orderBy('name')->paginate(12);
        $categories = CourseCategory::active()->orderBy('name')->get();
        $branches   = Branch::active()->get();

        return view('courses.index', compact('courses', 'categories', 'branches'));
    }

    public function create()
    {
        $categories = CourseCategory::active()->orderBy('name')->get();
        $branches   = Branch::active()->orderBy('name')->get();
        $employees  = Employee::active()->orderBy('first_name')->get();

        return view('courses.create', compact('categories', 'branches', 'employees'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'course_category_id' => 'required|exists:course_categories,id',
            'name'               => 'required|string|max:150',
            'short_description'  => 'nullable|string|max:300',
            'description'        => 'nullable|string',
            'cover_image'        => 'nullable|image|max:3072',
            'price'              => 'required|numeric|min:0',
            'price_max'          => 'nullable|numeric|min:0',
            'duration_hours'     => 'required|numeric|min:0.5',
            'modality'           => 'required|in:presencial,online,mixto',
            'level'              => 'required|in:basico,intermedio,avanzado',
            'instructor'         => 'nullable|string|max:150',
            'max_students'       => 'nullable|integer|min:1',
            'branch_ids'         => 'nullable|array',
            'branch_ids.*'       => 'exists:branches,id',
            // Instructores: empleados con rol instructor
            'instructor_ids'     => 'nullable|array',
            'instructor_ids.*'   => 'exists:employees,id',
            // Estudiantes: empleados inscritos
            'student_ids'        => 'nullable|array',
            'student_ids.*'      => 'exists:employees,id',
        ]);

        $data['is_active']       = $request->boolean('is_active', true);
        $data['is_featured']     = $request->boolean('is_featured');
        $data['has_certificate'] = $request->boolean('has_certificate');
        $data['slug']            = Str::slug($data['name']);

        DB::beginTransaction();
        try {
            if ($request->hasFile('cover_image')) {
                $data['cover_image'] = $request->file('cover_image')->store('courses/covers', 'public');
            }

            $course = Course::create($data);

            // Sedes
            if (!empty($data['branch_ids'])) {
                $course->branches()->sync($data['branch_ids']);
            }

            // Instructores
            $employeeSync = [];
            foreach ($request->input('instructor_ids', []) as $empId) {
                $employeeSync[$empId] = ['role' => 'instructor'];
            }
            // Estudiantes (no pueden coincidir con instructores)
            $instructorIds = $request->input('instructor_ids', []);
            foreach ($request->input('student_ids', []) as $empId) {
                if (!in_array($empId, $instructorIds)) {
                    $employeeSync[$empId] = ['role' => 'estudiante', 'status' => 'inscrito', 'enrolled_at' => now()->toDateString()];
                }
            }
            if (!empty($employeeSync)) {
                $course->employees()->sync($employeeSync);
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Error al guardar: ' . $e->getMessage());
        }

        return redirect()->route('courses.show', $course)->with('success', 'Curso creado correctamente.');
    }

    public function show(Course $course)
    {
        $course->load(['category', 'branches', 'instructors', 'students']);
        return view('courses.show', compact('course'));
    }

    public function edit(Course $course)
    {
        $course->load(['branches', 'employees']);
        $categories = CourseCategory::active()->orderBy('name')->get();
        $branches   = Branch::active()->orderBy('name')->get();
        $employees  = Employee::active()->orderBy('first_name')->get();

        return view('courses.edit', compact('course', 'categories', 'branches', 'employees'));
    }

    public function update(Request $request, Course $course)
    {
        $data = $request->validate([
            'course_category_id' => 'required|exists:course_categories,id',
            'name'               => 'required|string|max:150',
            'short_description'  => 'nullable|string|max:300',
            'description'        => 'nullable|string',
            'cover_image'        => 'nullable|image|max:3072',
            'price'              => 'required|numeric|min:0',
            'price_max'          => 'nullable|numeric|min:0',
            'duration_hours'     => 'required|numeric|min:0.5',
            'modality'           => 'required|in:presencial,online,mixto',
            'level'              => 'required|in:basico,intermedio,avanzado',
            'instructor'         => 'nullable|string|max:150',
            'max_students'       => 'nullable|integer|min:1',
            'branch_ids'         => 'nullable|array',
            'branch_ids.*'       => 'exists:branches,id',
            'instructor_ids'     => 'nullable|array',
            'instructor_ids.*'   => 'exists:employees,id',
            'student_ids'        => 'nullable|array',
            'student_ids.*'      => 'exists:employees,id',
        ]);

        $data['is_active']       = $request->boolean('is_active');
        $data['is_featured']     = $request->boolean('is_featured');
        $data['has_certificate'] = $request->boolean('has_certificate');

        DB::beginTransaction();
        try {
            if ($request->hasFile('cover_image')) {
                if ($course->cover_image) Storage::disk('public')->delete($course->cover_image);
                $data['cover_image'] = $request->file('cover_image')->store('courses/covers', 'public');
            }

            $course->update(collect($data)->except(['branch_ids', 'instructor_ids', 'student_ids'])->toArray());

            $course->branches()->sync($request->input('branch_ids', []));

            // Reconstruir pivot empleados
            $employeeSync  = [];
            $instructorIds = $request->input('instructor_ids', []);
            $studentIds    = $request->input('student_ids', []);

            foreach ($instructorIds as $empId) {
                $employeeSync[$empId] = ['role' => 'instructor'];
            }
            foreach ($studentIds as $empId) {
                if (!in_array($empId, $instructorIds)) {
                    // Preservar datos previos si ya estaba inscrito
                    $existing = $course->employees()->where('employees.id', $empId)->first();
                    $employeeSync[$empId] = [
                        'role'        => 'estudiante',
                        'status'      => $existing?->pivot->status ?? 'inscrito',
                        'enrolled_at' => $existing?->pivot->enrolled_at ?? now()->toDateString(),
                    ];
                }
            }
            $course->employees()->sync($employeeSync);

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Error: ' . $e->getMessage());
        }

        return redirect()->route('courses.show', $course)->with('success', 'Curso actualizado correctamente.');
    }

    public function destroy(Course $course)
    {
        if ($course->cover_image) Storage::disk('public')->delete($course->cover_image);
        $course->delete();
        return redirect()->route('courses.index')->with('success', 'Curso eliminado.');
    }
}