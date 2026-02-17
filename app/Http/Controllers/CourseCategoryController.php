<?php

namespace App\Http\Controllers;

use App\Models\CourseCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CourseCategoryController extends Controller
{
    public function index(Request $request)
    {
        $query = CourseCategory::withCount('courses');
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }
        $categories = $query->orderBy('sort_order')->orderBy('name')->paginate(15);
        return view('course_categories.index', compact('categories'));
    }

    public function create()
    {
        return view('course_categories.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:100|unique:course_categories,name',
            'description' => 'nullable|string|max:500',
            'color'       => 'nullable|string|max:20',
            'sort_order'  => 'nullable|integer|min:0',
            'is_active'   => 'boolean',
        ]);
        $data['slug']      = Str::slug($data['name']);
        $data['is_active'] = $request->boolean('is_active', true);
        CourseCategory::create($data);
        return redirect()->route('course-categories.index')->with('success', 'Categoría creada.');
    }

    /** Crear vía AJAX desde el modal del formulario de cursos */
    public function storeAjax(Request $request)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:100|unique:course_categories,name',
            'description' => 'nullable|string|max:500',
            'color'       => 'nullable|string|max:20',
            'sort_order'  => 'nullable|integer|min:0',
        ]);
        $data['slug']      = Str::slug($data['name']);
        $data['is_active'] = true;
        $cat = CourseCategory::create($data);
        return response()->json(['id' => $cat->id, 'name' => $cat->name, 'color' => $cat->color], 201);
    }

    public function edit(CourseCategory $courseCategory)
    {
        return view('course_categories.edit', compact('courseCategory'));
    }

    public function update(Request $request, CourseCategory $courseCategory)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:100|unique:course_categories,name,' . $courseCategory->id,
            'description' => 'nullable|string|max:500',
            'color'       => 'nullable|string|max:20',
            'sort_order'  => 'nullable|integer|min:0',
            'is_active'   => 'boolean',
        ]);
        $data['slug']      = Str::slug($data['name']);
        $data['is_active'] = $request->boolean('is_active');
        $courseCategory->update($data);
        return redirect()->route('course-categories.index')->with('success', 'Categoría actualizada.');
    }

    public function destroy(CourseCategory $courseCategory)
    {
        if ($courseCategory->courses()->count() > 0) {
            return back()->with('error', 'No puedes eliminar una categoría con cursos asociados.');
        }
        $courseCategory->delete();
        return redirect()->route('course-categories.index')->with('success', 'Categoría eliminada.');
    }
}