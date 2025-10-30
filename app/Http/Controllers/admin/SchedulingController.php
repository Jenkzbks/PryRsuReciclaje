<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Scheduling;
use App\Models\Employeegroup;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class SchedulingController extends Controller
{
    // Listado con filtros de fecha
    public function index(Request $request)
    {
        $from = $request->input('from');
        $to   = $request->input('to');

        $query = Scheduling::with(['group','shift','vehicle','zone'])
            ->orderBy('date','asc')
            ->orderBy('group_id','asc');

        if ($from) $query->whereDate('date','>=',$from);
        if ($to)   $query->whereDate('date','<=',$to);

        $schedulings = $query->paginate(25);

        return view('schedulings.index', compact('schedulings','from','to'));
    }

    // Formulario crear
    public function create()
    {
        // cargamos grupos con sus relaciones para mostrar datos del grupo al usuario
        $groups = Employeegroup::with(['shift','vehicle','zone'])->orderBy('name')->get();
        return view('schedulings.create', compact('groups'));
    }

    // Generación masiva por rango de fechas en base a los días del grupo
    public function store(Request $request)
    {
        $request->validate([
            'group_id'  => 'required|exists:employeegroups,id',
            'from'      => 'required|date',
            'to'        => 'required|date|after_or_equal:from',
            'notes'     => 'nullable|string|max:120',
        ]);

        $group = Employeegroup::with(['shift','vehicle','zone'])->findOrFail($request->group_id);

        // Los días del grupo vienen como "Lunes,Martes,Viernes"
        $allowedDays = $this->parseSpanishDays($group->days); // -> [1,2,5] por ejemplo

        $period = CarbonPeriod::create(Carbon::parse($request->from), Carbon::parse($request->to));

        $created = 0;
        foreach ($period as $date) {
            // Carbon: 0=Domingo, 1=Lunes, ..., 6=Sábado
            if (!in_array($date->dayOfWeek, $allowedDays)) {
                continue;
            }

            // Evitar duplicados: mismo grupo + misma fecha
            $exists = Scheduling::where('group_id', $group->id)
                ->whereDate('date', $date->toDateString())
                ->exists();

            if ($exists) continue;

            Scheduling::create([
                'group_id'  => $group->id,
                'shift_id'  => $group->shift_id,
                'vehicle_id'=> $group->vehicle_id,
                'zone_id'   => $group->zone_id,
                'date'      => $date->toDateString(),
                'notes'     => $request->notes ?? '',
                'status'    => 1, // 1=programado
            ]);

            $created++;
        }

        return redirect()
            ->route('admin.schedulings.index', ['from'=>$request->from,'to'=>$request->to])
            ->with('success', "Se generaron {$created} programaciones.");
    }

    // Utilidad: convierte "Lunes,Miércoles,..." a números de Carbon [1..6]
    private function parseSpanishDays(string $daysCsv): array
    {
        $map = [
            'domingo'   => 0,
            'lunes'     => 1,
            'martes'    => 2,
            'miércoles' => 3, 'miercoles' => 3,
            'jueves'    => 4,
            'viernes'   => 5,
            'sábado'    => 6, 'sabado' => 6,
        ];

        $out = [];
        foreach (explode(',', $daysCsv) as $d) {
            $key = trim(mb_strtolower($d,'UTF-8'));
            if (isset($map[$key])) $out[] = $map[$key];
        }
        // aseguramos únicos y ordenados
        return array_values(array_unique($out));
    }

    // (Opcional) eliminar una programación
    public function destroy(Scheduling $scheduling)
    {
        $scheduling->delete();
        return back()->with('success', 'Programación eliminada.');
    }
}
