<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Maintenance;
use Illuminate\Http\Request;

class MaintenanceController extends Controller
{
    public function index(Request $request)
    {
        $from = $request->input('from');
        $to   = $request->input('to');

        $query = Maintenance::orderBy('start_date', 'desc');

        if ($from) {
            $query->whereDate('start_date', '>=', $from);
        }
        if ($to) {
            $query->whereDate('end_date', '<=', $to);
        }

        $maintenances = $query->paginate(20);

        return view('maintenances.index', compact('maintenances', 'from', 'to'));
    }

    public function create()
    {
        return view('maintenances.create');
    }

    public function store(Request $request)
    {
        
        $data = $request->validate([
            'name'       => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date'   => 'required|date|after_or_equal:start_date',
        ]);

        
        $overlapExists = Maintenance::where(function ($q) use ($data) {
        
                $q->where('start_date', '<=', $data['end_date'])
                  ->where('end_date',   '>=', $data['start_date']);
            })
            ->exists();

        if ($overlapExists) {
            return back()
                ->withErrors([
                    'start_date' => 'Ya existe un mantenimiento cuyo rango de fechas se cruza con '
                        . $data['start_date'] . ' al ' . $data['end_date'] . '.',
                ])
                ->withInput();
        }

        
        Maintenance::create($data);

        return redirect()
            ->route('admin.maintenances.index')
            ->with('success', 'Mantenimiento creado correctamente.');
    }

    public function edit(Maintenance $maintenance)
    {
        return view('maintenances.edit', compact('maintenance'));
    }

    public function update(Request $request, Maintenance $maintenance)
    {
        
        $data = $request->validate([
            'name'       => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date'   => 'required|date|after_or_equal:start_date',
        ]);

        
        $overlapExists = Maintenance::where('id', '!=', $maintenance->id)
            ->where(function ($q) use ($data) {
                $q->where('start_date', '<=', $data['end_date'])
                  ->where('end_date',   '>=', $data['start_date']);
            })
            ->exists();

        if ($overlapExists) {
            return back()
                ->withErrors([
                    'start_date' => 'Ya existe otro mantenimiento cuyo rango de fechas se cruza con '
                        . $data['start_date'] . ' al ' . $data['end_date'] . '.',
                ])
                ->withInput();
        }

        
        $maintenance->update($data);

        return redirect()
            ->route('admin.maintenances.index')
            ->with('success', 'Mantenimiento actualizado correctamente.');
    }

    public function destroy(Maintenance $maintenance)
    {
        
        foreach ($maintenance->schedules as $schedule) {
            $schedule->records()->delete();
        }
        $maintenance->schedules()->delete();
        $maintenance->delete();

        return back()->with('success', 'Mantenimiento eliminado.');
    }
}
