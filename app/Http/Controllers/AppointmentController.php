<?php
// app/Http/Controllers/AppointmentController.php

namespace App\Http\Controllers;

use App\Models\Appointment;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AppointmentController extends Controller
{
    public function index(Request $request)
    {
        $month = $request->get('month', now()->month);
        $year  = $request->get('year', now()->year);

        $startOfMonth = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $endOfMonth   = Carbon::createFromDate($year, $month, 1)->endOfMonth();

        $appointments = Appointment::whereBetween('date', [$startOfMonth, $endOfMonth])
            ->orderBy('date')
            ->orderBy('start_time')
            ->get()
            ->groupBy(fn($a) => $a->date->format('Y-m-d'));

        $prevMonth = Carbon::createFromDate($year, $month, 1)->subMonth();
        $nextMonth = Carbon::createFromDate($year, $month, 1)->addMonth();

        return view('appointments.index', compact(
            'appointments', 'month', 'year',
            'startOfMonth', 'endOfMonth',
            'prevMonth', 'nextMonth'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'client_name' => 'required|string|max:100',
            'client_phone' => 'nullable|string|max:20',
            'client_email' => 'nullable|email|max:100',
            'service'     => 'required|string|max:100',
            'stylist'     => 'nullable|string|max:100',
            'date'        => 'required|date',
            'start_time'  => 'required',
            'end_time'    => 'required|after:start_time',
            'status'      => 'required|in:pending,confirmed,completed,cancelled',
            'notes'       => 'nullable|string',
            'color'       => 'required|string',
        ]);

        Appointment::create($request->all());

        return redirect()->route('appointments.index', [
            'month' => Carbon::parse($request->date)->month,
            'year'  => Carbon::parse($request->date)->year,
        ])->with('success', 'Cita creada correctamente.');
    }

    public function update(Request $request, Appointment $appointment)
    {
        $request->validate([
            'client_name' => 'required|string|max:100',
            'client_phone' => 'nullable|string|max:20',
            'client_email' => 'nullable|email|max:100',
            'service'     => 'required|string|max:100',
            'stylist'     => 'nullable|string|max:100',
            'date'        => 'required|date',
            'start_time'  => 'required',
            'end_time'    => 'required',
            'status'      => 'required|in:pending,confirmed,completed,cancelled',
            'notes'       => 'nullable|string',
            'color'       => 'required|string',
        ]);

        $appointment->update($request->all());

        return redirect()->route('appointments.index', [
            'month' => Carbon::parse($request->date)->month,
            'year'  => Carbon::parse($request->date)->year,
        ])->with('success', 'Cita actualizada correctamente.');
    }

    public function destroy(Appointment $appointment)
    {
        $month = $appointment->date->month;
        $year  = $appointment->date->year;
        $appointment->delete();

        return redirect()->route('appointments.index', compact('month', 'year'))
            ->with('success', 'Cita eliminada correctamente.');
    }
}