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

    

public function groupInfo(Employeegroup $group)
{
    // Miembros en el orden de inserción en la pivote
    $members = $group->employees()
        ->select('employee.*') // asegura columnas de employee
        ->orderBy('configgroups.id')
        ->get();

    // Clasifica por type_id
    $driver     = $members->firstWhere('type_id', 1);
    $assistants = $members->where('type_id', 2)->values();

    $assistant1 = $assistants->get(0);
    $assistant2 = $assistants->get(1);

    $today = Carbon::today();

    $buildPerson = function ($emp) use ($today) {
        if (!$emp) return null;

        // Contrato activo (según tu relación activeContract ya definida)
        $contract = $emp->activeContract()->first();

        // Vacaciones “activas” ahora mismo.
        // Tus modelos usan start_date (modelo) y también hay instalaciones con request_date (migración).
        // Para no tocar tus modelos, consultamos ambos campos de forma segura.
        $vacation = $emp->vacations()
            ->whereIn('status', ['approved', 'Approved', 'APPROVED']) // tolerante a variantes
            ->where(function($q) use ($today) {
                // Caso 1: start_date/end_date existen en tu modelo
                $q->where(function($qq) use ($today) {
                    $qq->whereNotNull('start_date')
                       ->whereDate('start_date', '<=', $today)
                       ->whereDate('end_date', '>=', $today);
                })
                // Caso 2: request_date/end_date (por si tu instalación usa estos nombres)
                ->orWhere(function($qq) use ($today) {
                    $qq->whereNotNull('request_date')
                       ->whereDate('request_date', '<=', $today)
                       ->whereDate('end_date', '>=', $today);
                });
            })
            ->orderByDesc('id')
            ->first();

        // Normalizamos fechas de contrato
        $contractStart = $contract?->start_date?->format('Y-m-d');
        $contractEnd   = $contract?->end_date?->format('Y-m-d');

        // Normalizamos fechas de vacaciones (según qué campo exista)
        $vacStart = $vacation?->start_date ?? $vacation?->request_date;
        $vacStart = $vacStart ? Carbon::parse($vacStart)->format('Y-m-d') : null;
        $vacEnd   = $vacation?->end_date ? Carbon::parse($vacation->end_date)->format('Y-m-d') : null;

        return [
            'full_name' => trim(($emp->lastnames ?? '').' '.($emp->names ?? '')),
            'contract'  => $contract ? [
                'start_date' => $contractStart,
                'end_date'   => $contractEnd,
                'is_active'  => (bool) $contract->is_active,
            ] : null,
            'vacation'  => $vacation ? [
                'start_date' => $vacStart,
                'end_date'   => $vacEnd,
                'status'     => $vacation->status,
            ] : null,
        ];
    };

    return response()->json([
        'driver'     => $buildPerson($driver),
        'assistant1' => $buildPerson($assistant1),
        'assistant2' => $buildPerson($assistant2),
    ]);
}

}
