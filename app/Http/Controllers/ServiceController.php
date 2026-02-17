<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\ServiceImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ServiceController extends Controller
{
    public function index(Request $request)
    {
        $query = Service::with(['category', 'branches'])
            ->withCount('employees');

        if ($request->filled('category')) {
            $query->where('service_category_id', $request->category);
        }

        if ($request->filled('branch')) {
            $query->whereHas('branches', fn($q) => $q->where('branches.id', $request->branch));
        }

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $services   = $query->orderBy('sort_order')->paginate(12);
        $categories = ServiceCategory::active()->get();
        $branches   = Branch::active()->get();

        return view('services.index', compact('services', 'categories', 'branches'));
    }

    public function create()
    {
        $categories = ServiceCategory::active()->orderBy('name')->get();
        $branches   = Branch::active()->orderBy('name')->get();

        return view('services.create', compact('categories', 'branches'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'service_category_id' => 'required|exists:service_categories,id',
            'name'                => 'required|string|max:150',
            'short_description'   => 'nullable|string|max:300',
            'description'         => 'nullable|string',
            'cover_image'         => 'nullable|image|max:3072',
            'price'               => 'required|numeric|min:0',
            'price_max'           => 'nullable|numeric|min:0',
            'duration_minutes'    => 'required|integer|min:5',
            'buffer_minutes'      => 'nullable|integer|min:0',
            'requires_deposit'    => 'boolean',
            'deposit_amount'      => 'nullable|numeric|min:0',
            'online_booking'      => 'boolean',
            'is_active'           => 'boolean',
            'is_featured'         => 'boolean',
            'sort_order'          => 'integer',
            'branch_ids'          => 'nullable|array',
            'branch_ids.*'        => 'exists:branches,id',
            // Galería de imágenes
            'images'              => 'nullable|array',
            'images.*'            => 'image|max:2048',
        ]);

        DB::beginTransaction();
        try {
            if ($request->hasFile('cover_image')) {
                $data['cover_image'] = $request->file('cover_image')
                    ->store('services/covers', 'public');
            }

            $data['slug'] = Str::slug($data['name']);
            $service = Service::create($data);

            // Asociar sedes
            if (!empty($data['branch_ids'])) {
                $service->branches()->sync($data['branch_ids']);
            }

            // Guardar galería de imágenes
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $index => $imageFile) {
                    $path = $imageFile->store('services/gallery', 'public');
                    ServiceImage::create([
                        'service_id' => $service->id,
                        'path'       => $path,
                        'type'       => 'gallery',
                        'is_primary' => $index === 0 && !$service->cover_image,
                        'sort_order' => $index,
                    ]);
                }
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Error al guardar el servicio: ' . $e->getMessage());
        }

        return redirect()
            ->route('services.show', $service)
            ->with('success', 'Servicio creado correctamente.');
    }

    public function show(Service $service)
    {
        $service->load([
            'category',
            'images',
            'branches',
            'employees' => fn($q) => $q->active()->with('branches'),
        ]);

        return view('services.show', compact('service'));
    }

    public function edit(Service $service)
    {
        $categories = ServiceCategory::active()->orderBy('name')->get();
        $branches   = Branch::active()->orderBy('name')->get();
        $service->load(['branches', 'images']);

        return view('services.edit', compact('service', 'categories', 'branches'));
    }

    public function update(Request $request, Service $service)
    {
        $data = $request->validate([
            'service_category_id' => 'required|exists:service_categories,id',
            'name'                => 'required|string|max:150',
            'short_description'   => 'nullable|string|max:300',
            'description'         => 'nullable|string',
            'cover_image'         => 'nullable|image|max:3072',
            'price'               => 'required|numeric|min:0',
            'price_max'           => 'nullable|numeric|min:0',
            'duration_minutes'    => 'required|integer|min:5',
            'buffer_minutes'      => 'nullable|integer|min:0',
            'requires_deposit'    => 'boolean',
            'deposit_amount'      => 'nullable|numeric|min:0',
            'online_booking'      => 'boolean',
            'is_active'           => 'boolean',
            'is_featured'         => 'boolean',
            'sort_order'          => 'integer',
            'branch_ids'          => 'nullable|array',
            'branch_ids.*'        => 'exists:branches,id',
            'images'              => 'nullable|array',
            'images.*'            => 'image|max:2048',
        ]);

        DB::beginTransaction();
        try {
            if ($request->hasFile('cover_image')) {
                if ($service->cover_image) {
                    Storage::disk('public')->delete($service->cover_image);
                }
                $data['cover_image'] = $request->file('cover_image')
                    ->store('services/covers', 'public');
            }

            $service->update($data);
            $service->branches()->sync($data['branch_ids'] ?? []);

            // Nuevas imágenes de galería
            if ($request->hasFile('images')) {
                $lastOrder = $service->images()->max('sort_order') ?? -1;
                foreach ($request->file('images') as $index => $imageFile) {
                    $path = $imageFile->store('services/gallery', 'public');
                    ServiceImage::create([
                        'service_id' => $service->id,
                        'path'       => $path,
                        'type'       => 'gallery',
                        'sort_order' => $lastOrder + $index + 1,
                    ]);
                }
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Error al actualizar: ' . $e->getMessage());
        }

        return redirect()
            ->route('services.show', $service)
            ->with('success', 'Servicio actualizado correctamente.');
    }

    public function destroy(Service $service)
    {
        // Eliminar imágenes del storage
        foreach ($service->images as $image) {
            Storage::disk('public')->delete($image->path);
        }
        if ($service->cover_image) {
            Storage::disk('public')->delete($service->cover_image);
        }

        $service->delete();

        return redirect()->route('services.index')->with('success', 'Servicio eliminado.');
    }

    /** Eliminar una imagen individual de la galería */
    public function destroyImage(ServiceImage $image)
    {
        Storage::disk('public')->delete($image->path);
        $image->delete();

        return back()->with('success', 'Imagen eliminada.');
    }

    /** Marcar imagen como principal */
    public function setPrimaryImage(ServiceImage $image)
    {
        ServiceImage::where('service_id', $image->service_id)
            ->update(['is_primary' => false]);

        $image->update(['is_primary' => true]);

        return back()->with('success', 'Imagen principal actualizada.');
    }
}