<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BranchController extends Controller
{
    public function index()
    {
        $branches = Branch::withCount(['employees', 'services'])
            ->orderBy('sort_order')
            ->get();

        return view('branches.index', compact('branches'));
    }

    public function create()
    {
        return view('branches.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'          => 'required|string|max:150',
            'address'       => 'required|string|max:255',
            'district'      => 'nullable|string|max:100',
            'city'          => 'required|string|max:100',
            'phone'         => 'nullable|string|max:20',
            'email'         => 'nullable|email|max:150',
            'whatsapp'      => 'nullable|string|max:20',
            'description'   => 'nullable|string',
            'image'         => 'nullable|image|max:2048',
            'latitude'      => 'nullable|numeric',
            'longitude'     => 'nullable|numeric',
            'opening_hours' => 'nullable|array',
            'is_active'     => 'boolean',
            'sort_order'    => 'integer',
        ]);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('branches', 'public');
        }

        $data['slug'] = Str::slug($data['name']);

        $branch = Branch::create($data);

        return redirect()
            ->route('branches.show', $branch)
            ->with('success', 'Local creado correctamente.');
    }

    public function show(Branch $branch)
    {
        $branch->load(['employees.user', 'services.category']);

        return view('branches.show', compact('branch'));
    }

    public function edit(Branch $branch)
    {
        return view('branches.edit', compact('branch'));
    }

    public function update(Request $request, Branch $branch)
    {
        $data = $request->validate([
            'name'          => 'required|string|max:150',
            'address'       => 'required|string|max:255',
            'district'      => 'nullable|string|max:100',
            'city'          => 'required|string|max:100',
            'phone'         => 'nullable|string|max:20',
            'email'         => 'nullable|email|max:150',
            'whatsapp'      => 'nullable|string|max:20',
            'description'   => 'nullable|string',
            'image'         => 'nullable|image|max:2048',
            'latitude'      => 'nullable|numeric',
            'longitude'     => 'nullable|numeric',
            'opening_hours' => 'nullable|array',
            'is_active'     => 'boolean',
            'sort_order'    => 'integer',
        ]);

        if ($request->hasFile('image')) {
            // Eliminar imagen anterior
            if ($branch->image) {
                Storage::disk('public')->delete($branch->image);
            }
            $data['image'] = $request->file('image')->store('branches', 'public');
        }

        $branch->update($data);

        return redirect()
            ->route('branches.show', $branch)
            ->with('success', 'Local actualizado correctamente.');
    }

    public function destroy(Branch $branch)
    {
        $branch->delete();

        return redirect()
            ->route('branches.index')
            ->with('success', 'Local eliminado.');
    }

    /** Activar / desactivar */
    public function toggleActive(Branch $branch)
    {
        $branch->update(['is_active' => !$branch->is_active]);

        return back()->with('success', 'Estado actualizado.');
    }
}