<?php

namespace App\Http\Controllers\admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Shift;
use App\Models\Scheduling;

class AdminController extends Controller
{
    public function index(Request $request)
    {
        $shifts = Shift::all();
        $schedulings = collect();

        $selectedDate = $request->date ?: date('Y-m-d');
        $selectedShift = $request->shift_id;

        if ($request->filled('date') && $request->filled('shift_id')) {
            $schedulings = Scheduling::with(['zone', 'shift', 'group', 'details'])
                ->where('date', $request->date)
                ->where('shift_id', $request->shift_id)
                ->get()
                ->groupBy('zone.name');
        }

        // Calcular estadísticas
        $totalAttendances = 0;
        $completeGroups = 0;
        $incompleteZones = 0;

        foreach($schedulings as $zoneName => $zoneSchedulings) {
            $zoneComplete = false;
            $zoneEmployees = 0;
            $zoneAttendances = 0;
            foreach($zoneSchedulings as $scheduling) {
                $group = $scheduling->group;
                if ($group) {
                    $employees = $group->employees;
                    $zoneEmployees += $employees->count();
                    foreach($employees as $employee) {
                        if ($employee->attendances()->where('date', $selectedDate)->exists()) {
                            $zoneAttendances++;
                        }
                    }
                }
            }
            $totalAttendances += $zoneAttendances;
            if ($zoneEmployees > 0 && $zoneAttendances >= $zoneEmployees) {
                $completeGroups++;
                $zoneComplete = true;
            }
            if (!$zoneComplete) {
                $incompleteZones++;
            }
        }

        if ($request->ajax()) {
            return response()->json([
                'zones' => view('admin.zones', compact('schedulings', 'request'))->render(),
                'attendances' => $totalAttendances,
                'completeGroups' => $completeGroups,
                'incompleteZones' => $incompleteZones
            ]);
        }

        return view('admin.index', compact('shifts', 'schedulings', 'totalAttendances', 'completeGroups', 'incompleteZones', 'request'));
    }
}
