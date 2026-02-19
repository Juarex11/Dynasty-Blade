<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\CourseOpening;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ClientController extends Controller
{
  public function index(Request $request)
{
    $query = Client::query();

    if ($request->filled('search')) {
        $s = $request->search;
        $query->where(fn ($q) =>
            $q->where('first_name', 'like', "%$s%")
              ->orWhere('last_name',  'like', "%$s%")
              ->orWhere('phone',      'like', "%$s%")
              ->orWhere('email',      'like', "%$s%")
              ->orWhere('dni',        'like', "%$s%")
        );
    }

    if ($request->filled('type')) {
        $query->where('client_type', $request->type);
    }

    if ($request->filled('mode')) {
        $query->where('client_mode', $request->mode);
    }

    if ($request->filled('source')) {
        $query->where('acquisition_source', $request->source);
    }

    if ($request->filled('district')) {
        $query->where('district', $request->district);
    }

    if ($request->filled('department')) {
        $query->where('department', $request->department);
    }

    if ($request->filled('active')) {
        $query->where('is_active', $request->active === '1');
    }

    // ✅ Filtros por fecha de registro
    if ($request->filled('from')) {
        $query->whereDate('created_at', '>=', $request->from);
    }

    if ($request->filled('to')) {
        $query->whereDate('created_at', '<=', $request->to);
    }

    // ✅ UNA sola paginación
    $clients = $query
        ->orderByDesc('created_at')
        ->paginate(20)
        ->withQueryString();

    // 📊 Estadísticas
    $stats = [
        'total'      => Client::count(),
        'vip'        => Client::where('client_type', 'vip')->count(),
        'recurrente' => Client::where('client_type', 'recurrente')->count(),
        'unico'      => Client::where('client_type', 'unico')->count(),
        'inactivo'   => Client::where('client_type', 'inactivo')->count(),
        'nuevo'      => Client::where('client_type', 'nuevo')->count(),
        'frecuente'  => Client::where('client_mode', 'frecuente')->count(),
        'ocasional'  => Client::where('client_mode', 'ocasional')->count(),
    ];

    $topDistricts = Client::selectRaw('district, count(*) as total')
        ->whereNotNull('district')
        ->groupBy('district')
        ->orderByDesc('total')
        ->limit(5)
        ->pluck('total', 'district');

    $topSources = Client::selectRaw('acquisition_source, count(*) as total')
        ->whereNotNull('acquisition_source')
        ->groupBy('acquisition_source')
        ->orderByDesc('total')
        ->pluck('total', 'acquisition_source');

    return view('clients.index', compact(
        'clients',
        'stats',
        'topDistricts',
        'topSources'
    ));
}


    public function create()
    {
        $openings = CourseOpening::with('course')
            ->whereIn('status', ['publicado', 'en_curso'])
            ->orderBy('start_date')
            ->get();

        return view('clients.create', compact('openings'));
    }

  public function store(Request $request)
{
    $data = $request->validate([
        'first_name'            => 'required|string|max:80',
        'last_name'             => 'required|string|max:80',
        'client_mode'           => 'required|in:frecuente,ocasional',
        'dni'                   => 'nullable|string|max:15|unique:clients,dni',
        'phone'                 => 'nullable|string|max:20',
        'email'                 => 'nullable|email|max:150|unique:clients,email',
        'birthdate'             => 'nullable|date|before:today',
        'gender'                => 'nullable|in:masculino,femenino,otro,no_especifica',
        'photo'                 => 'nullable|image|max:2048',
        'department'            => 'nullable|string|max:80',
        'province'              => 'nullable|string|max:80',
        'district'              => 'nullable|string|max:80',
        'address'               => 'nullable|string|max:200',
        'acquisition_source'    => 'nullable|in:instagram,facebook,tiktok,google,referido,walk_in,whatsapp,otro',
        'referred_by'           => 'nullable|string|max:100',
        'hair_type'             => 'nullable|in:liso,ondulado,rizado,muy_rizado,otro',
        'services_interest'     => 'nullable|array',
        'services_interest.*'   => 'string|max:80',
        'notes'                 => 'nullable|string',
        'tags'                  => 'nullable|string|max:300',
        'username'              => 'nullable|string|max:60|unique:clients,username',
        'password'              => 'nullable|string|min:6',
        'enroll_opening_id'     => 'nullable|exists:course_openings,id',
        'enroll_price_paid'     => 'nullable|numeric|min:0',
        'enroll_payment_status' => 'nullable|in:pendiente,pagado,parcial,becado',
    ]);

    DB::transaction(function () use ($request, &$client, $data) {

        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('clients/photos', 'public');
        }

        if (empty($data['password'])) {
            unset($data['password']);
        } else {
            $data['password'] = bcrypt($data['password']);
        }

        $data['is_active']   = true;
        $data['client_type'] = 'nuevo';

        // Crear cliente
        $client = Client::create($data);

        // ── Inscripción a curso ───────────────────────────────
        if ($request->filled('enroll_opening_id')) {

            $opening = CourseOpening::lockForUpdate()->find($request->enroll_opening_id);

            if (! $opening) {
                throw ValidationException::withMessages([
                    'enroll_opening_id' => 'La apertura seleccionada no existe.'
                ]);
            }

            // 🔴 VALIDACIÓN DE CUPOS
            if ($opening->enrolled_count >= $opening->max_students) {
                throw ValidationException::withMessages([
                    'enroll_opening_id' => 'Este curso ya no tiene cupos disponibles.'
                ]);
            }

            // Crear inscripción
            $opening->enrollments()->create([
                'client_id'      => $client->id,
                'price_paid'     => $request->enroll_price_paid ?? $opening->effective_price,
                'payment_status' => $request->enroll_payment_status ?? 'pendiente',
                'enrolled_at'    => now()->toDateString(),
                'status'         => 'inscrito',
            ]);

            // Actualizar contador
            $opening->syncEnrolledCount();
        }
    });

    return redirect()
        ->route('clients.show', $client)
        ->with('success', 'Cliente registrado correctamente.');
}

    public function show(Client $client)
    {
        $client->load(['courseOpenings.course']);
        return view('clients.show', compact('client'));
    }

    public function edit(Client $client)
    {
        $openings = CourseOpening::with('course')
            ->whereIn('status', ['publicado', 'en_curso'])
            ->orderBy('start_date')
            ->get();

        return view('clients.edit', compact('client', 'openings'));
    }

    public function update(Request $request, Client $client)
    {
        $data = $request->validate([
            'first_name'            => 'required|string|max:80',
            'last_name'             => 'required|string|max:80',
            'client_mode'           => 'required|in:frecuente,ocasional',
            'dni'                   => 'nullable|string|max:15|unique:clients,dni,' . $client->id,
            'phone'                 => 'nullable|string|max:20',
            'email'                 => 'nullable|email|max:150|unique:clients,email,' . $client->id,
            'birthdate'             => 'nullable|date|before:today',
            'gender'                => 'nullable|in:masculino,femenino,otro,no_especifica',
            'photo'                 => 'nullable|image|max:2048',
            'department'            => 'nullable|string|max:80',
            'province'              => 'nullable|string|max:80',
            'district'              => 'nullable|string|max:80',
            'address'               => 'nullable|string|max:200',
            'acquisition_source'    => 'nullable|in:instagram,facebook,tiktok,google,referido,walk_in,whatsapp,otro',
            'referred_by'           => 'nullable|string|max:100',
            'hair_type'             => 'nullable|in:liso,ondulado,rizado,muy_rizado,otro',
            'services_interest'     => 'nullable|array',
            'services_interest.*'   => 'string|max:80',
            'notes'                 => 'nullable|string',
            'tags'                  => 'nullable|string|max:300',
            'is_active'             => 'boolean',
            'username'              => 'nullable|string|max:60|unique:clients,username,' . $client->id,
            'password'              => 'nullable|string|min:6',
        ]);

        $data['is_active'] = $request->boolean('is_active');

        if (empty($data['password'])) {
            unset($data['password']);
        }

        if ($request->hasFile('photo')) {
            if ($client->photo) Storage::disk('public')->delete($client->photo);
            $data['photo'] = $request->file('photo')->store('clients/photos', 'public');
        }

        $client->update($data);
        $client->recalculateType();
        $client->save();

        return redirect()->route('clients.show', $client)->with('success', 'Cliente actualizado.');
    }

    public function destroy(Client $client)
    {
        if ($client->photo) Storage::disk('public')->delete($client->photo);
        $client->delete();
        return redirect()->route('clients.index')->with('success', 'Cliente eliminado.');
    }
}