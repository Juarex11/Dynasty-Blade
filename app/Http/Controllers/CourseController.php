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
        $query = Course::with(['category', 'branches'])
            ->withCount('instructors')
            // Suma de enrolled_count de todas las aperturas del curso
            ->withSum('openings', 'enrolled_count');

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
            'modality'           => 'required|in:presencial,online,mixto',
            'level'              => 'required|in:basico,intermedio,avanzado',
            'instructor'         => 'nullable|string|max:150',
            'max_students'       => 'nullable|integer|min:1',
            'branch_ids'         => 'nullable|array',
            'branch_ids.*'       => 'exists:branches,id',
            'instructor_ids'     => 'nullable|array',
            'instructor_ids.*'   => 'exists:employees,id',
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

            $course = Course::create(collect($data)->except(['branch_ids', 'instructor_ids'])->toArray());

            if (!empty($data['branch_ids'])) {
                $course->branches()->sync($data['branch_ids']);
            }

            $employeeSync = [];
            foreach ($request->input('instructor_ids', []) as $empId) {
                $employeeSync[$empId] = ['role' => 'instructor'];
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
        $course->load(['category', 'branches', 'instructors', 'openings']);
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
            'modality'           => 'required|in:presencial,online,mixto',
            'level'              => 'required|in:basico,intermedio,avanzado',
            'instructor'         => 'nullable|string|max:150',
            'max_students'       => 'nullable|integer|min:1',
            'branch_ids'         => 'nullable|array',
            'branch_ids.*'       => 'exists:branches,id',
            'instructor_ids'     => 'nullable|array',
            'instructor_ids.*'   => 'exists:employees,id',
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

            $course->update(collect($data)->except(['branch_ids', 'instructor_ids'])->toArray());

            $course->branches()->sync($request->input('branch_ids', []));

            $employeeSync = [];
            foreach ($request->input('instructor_ids', []) as $empId) {
                $employeeSync[$empId] = ['role' => 'instructor'];
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