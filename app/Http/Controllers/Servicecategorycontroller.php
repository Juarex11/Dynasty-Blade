<?php

namespace App\Http\Controllers;

use App\Models\ServiceCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ServiceCategoryController extends Controller
{
    public function index(Request $request)
    {
        $query = ServiceCategory::withCount('services');

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $categories = $query->orderBy('sort_order')->orderBy('name')->paginate(15);

        return view('service_categories.index', compact('categories'));
    }

    public function create()
    {
        return view('service_categories.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:100|unique:service_categories,name',
            'description' => 'nullable|string|max:500',
            'color'       => 'nullable|string|max:20',
            'icon'        => 'nullable|string|max:100',
            'sort_order'  => 'nullable|integer|min:0',
            'is_active'   => 'boolean',
        ]);

        $data['slug']      = Str::slug($data['name']);
        $data['is_active'] = $data['is_active'] ?? true;

        $category = ServiceCategory::create($data);

        return redirect()
            ->route('service-categories.index')
            ->with('success', 'Categoría creada correctamente.');
    }

    /**
     * Crear categoría vía AJAX (desde el modal del formulario de servicios).
     */
    public function storeAjax(Request $request)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:100|unique:service_categories,name',
            'description' => 'nullable|string|max:500',
            'color'       => 'nullable|string|max:20',
            'sort_order'  => 'nullable|integer|min:0',
        ]);

        $data['slug']      = Str::slug($data['name']);
        $data['is_active'] = true;

        $category = ServiceCategory::create($data);

        return response()->json([
            'id'    => $category->id,
            'name'  => $category->name,
            'color' => $category->color,
            'slug'  => $category->slug,
        ], 201);
    }

    public function edit(ServiceCategory $serviceCategory)
    {
        return view('service_categories.edit', compact('serviceCategory'));
    }

    public function update(Request $request, ServiceCategory $serviceCategory)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:100|unique:service_categories,name,' . $serviceCategory->id,
            'description' => 'nullable|string|max:500',
            'color'       => 'nullable|string|max:20',
            'icon'        => 'nullable|string|max:100',
            'sort_order'  => 'nullable|integer|min:0',
            'is_active'   => 'boolean',
        ]);

        $data['slug'] = Str::slug($data['name']);

        $serviceCategory->update($data);

        return redirect()
            ->route('service-categories.index')
            ->with('success', 'Categoría actualizada correctamente.');
    }

   public function destroy(ServiceCategory $serviceCategory)
{
    if ($serviceCategory->services()->count() > 0) {
        $message = 'No puedes eliminar una categoría que tiene servicios asociados.';
        
        if (request()->expectsJson()) {
            return response()->json(['message' => $message], 422);
        }
        return back()->with('error', $message);
    }

    $serviceCategory->delete();

    if (request()->expectsJson()) {
        return response()->json(['message' => 'Categoría eliminada correctamente.']);
    }

    return redirect()
        ->route('service-categories.index')
        ->with('success', 'Categoría eliminada.');
}
}