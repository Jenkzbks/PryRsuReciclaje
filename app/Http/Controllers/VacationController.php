<?php

namespace App\Http\Controllers;

use App\Models\Vacation;
use App\Models\Employee;
use App\Http\Requests\StoreVacationRequest;
use App\Http\Requests\UpdateVacationRequest;
use Illuminate\Http\Request;
use Carbon\Carbon;

class VacationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Vacation::with(['employee']);

        // Búsqueda por empleado
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('employee', function($q) use ($search) {
                $q->where('names', 'like', "%{$search}%")
                  ->orWhere('lastnames', 'like', "%{$search}%")
                  ->orWhere('dni', 'like', "%{$search}%");
            });
        }

        // Filtro por empleado específico
        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        // Filtro por estado
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filtro por año
        if ($request->filled('year')) {
            $query->whereYear('start_date', $request->year);
        }

        // Filtro por mes
        if ($request->filled('month')) {
            $query->whereMonth('start_date', $request->month);
        }

        // Filtro por rango de fechas
        if ($request->filled('date_from')) {
            $query->whereDate('start_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('end_date', '<=', $request->date_to);
        }

        // Filtro por vacaciones pendientes de aprobación
        if ($request->filled('pending_approval')) {
            $query->where('status', Vacation::STATUS_PENDING);
        }

        // Ordenamiento
        $sortField = $request->get('sort', 'start_date');
        $sortDirection = $request->get('direction', 'desc');
        
        if (in_array($sortField, ['start_date', 'end_date', 'days', 'status', 'created_at'])) {
            $query->orderBy($sortField, $sortDirection);
        } else {
            $query->orderBy('start_date', 'desc');
        }

        $vacations = $query->paginate(15)->withQueryString();

        // Para la vista
        $employees = Employee::where('status', 1)
            ->orderBy('names')
            ->get();

        $years = Vacation::selectRaw('YEAR(start_date) as year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year');

        return view('vacations.index', compact('vacations', 'employees', 'years'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $employees = Employee::where('status', 1)
            ->orderBy('names')
            ->get();

        $selectedEmployee = null;
        if ($request->filled('employee_id')) {
            $selectedEmployee = Employee::find($request->employee_id);
        }

        return view('vacations.create', compact('employees', 'selectedEmployee'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreVacationRequest $request)
    {
        $data = $request->validated();
        
        // Calcular días automáticamente
        $startDate = Carbon::parse($data['start_date']);
        $endDate = Carbon::parse($data['end_date']);
        $data['days_taken'] = $startDate->diffInDays($endDate) + 1;
        $data['requested_days'] = $data['days_taken']; // Para compatibilidad

        // Estado inicial: pendiente si no se especifica
        if (!isset($data['status'])) {
            $data['status'] = Vacation::STATUS_PENDING;
        }

        // Establecer fecha de solicitud
        if (!isset($data['request_date'])) {
            $data['request_date'] = now();
        }

        // Si el estado es aprobado, establecer fecha de aprobación
        if ($data['status'] === Vacation::STATUS_APPROVED && isset($data['approved_by'])) {
            $data['approved_at'] = now();
        }

        $vacation = Vacation::create($data);

        return redirect()
            ->route('admin.personnel.vacations.index')
            ->with('success', 'Solicitud de vacaciones creada exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Vacation $vacation)
    {
        $vacation->load(['employee', 'replacementEmployee', 'approver']);

        return view('vacations.show', compact('vacation'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Vacation $vacation)
    {
        $employees = Employee::where('status', 1)
            ->orderBy('names')
            ->get();

        return view('vacations.edit', compact('vacation', 'employees'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateVacationRequest $request, Vacation $vacation)
    {
        $data = $request->validated();
        
        // Recalcular días
        $startDate = Carbon::parse($data['start_date']);
        $endDate = Carbon::parse($data['end_date']);
        $data['days_taken'] = $startDate->diffInDays($endDate) + 1;
        $data['requested_days'] = $data['days_taken']; // Para compatibilidad

        // Si el estado cambia a aprobado y no tenía fecha de aprobación, establecerla
        if ($data['status'] === Vacation::STATUS_APPROVED && !$vacation->approved_at) {
            $data['approved_at'] = now();
        }

        // Si el estado cambia de aprobado a otro, limpiar datos de aprobación
        if ($data['status'] !== Vacation::STATUS_APPROVED) {
            $data['approved_at'] = null;
        }

        $vacation->update($data);

        return redirect()
            ->route('admin.personnel.vacations.show', $vacation)
            ->with('success', 'Solicitud de vacaciones actualizada exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Vacation $vacation)
    {
        $vacation->delete();

        return redirect()
            ->route('admin.personnel.vacations.index')
            ->with('success', 'Solicitud de vacaciones eliminada exitosamente.');
    }

    /**
     * Aprobar vacación
     */
    public function approve(Vacation $vacation, Request $request)
    {
        if ($vacation->status !== Vacation::STATUS_PENDING) {
            return response()->json([
                'success' => false,
                'message' => 'Solo se pueden aprobar vacaciones pendientes.'
            ], 422);
        }

        $vacation->update([
            'status' => Vacation::STATUS_APPROVED,
            'approved_at' => now(),
            'approved_by' => auth()->id(),
            'approval_notes' => $request->notes
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Vacación aprobada exitosamente.'
        ]);
    }

    /**
     * Rechazar vacación
     */
    public function reject(Vacation $vacation, Request $request)
    {
        if ($vacation->status !== Vacation::STATUS_PENDING) {
            return response()->json([
                'success' => false,
                'message' => 'Solo se pueden rechazar vacaciones pendientes.'
            ], 422);
        }

        $request->validate([
            'rejection_reason' => 'required|string|max:500'
        ]);

        $vacation->update([
            'status' => Vacation::STATUS_REJECTED,
            'approved_at' => now(),
            'approved_by' => auth()->id(),
            'approval_notes' => $request->rejection_reason
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Vacación rechazada exitosamente.'
        ]);
    }

    /**
     * Cancelar vacación
     */
    public function cancel(Vacation $vacation, Request $request)
    {
        if (!in_array($vacation->status, [Vacation::STATUS_PENDING, Vacation::STATUS_APPROVED])) {
            return response()->json([
                'success' => false,
                'message' => 'Solo se pueden cancelar vacaciones pendientes o aprobadas.'
            ], 422);
        }

        $vacation->update([
            'status' => Vacation::STATUS_CANCELLED,
            'cancellation_reason' => $request->reason,
            'cancelled_at' => now()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Vacación cancelada exitosamente.'
        ]);
    }

    /**
     * Completar vacación (marcar como tomada)
     */
    public function complete(Vacation $vacation)
    {
        if ($vacation->status !== Vacation::STATUS_APPROVED) {
            return response()->json([
                'success' => false,
                'message' => 'Solo se pueden completar vacaciones aprobadas.'
            ], 422);
        }

        if ($vacation->end_date >= Carbon::now()) {
            return response()->json([
                'success' => false,
                'message' => 'No se puede completar una vacación que aún no ha terminado.'
            ], 422);
        }

        $vacation->update([
            'status' => Vacation::STATUS_COMPLETED
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Vacación completada exitosamente.'
        ]);
    }

    /**
     * Obtener días de vacaciones disponibles para un empleado
     */
    public function getAvailableDays(Employee $employee)
    {
        $availableDays = $employee->getAvailableVacationDays();
        $usedDays = $employee->getUsedVacationDays();
        $pendingDays = $employee->getPendingVacationDays();

        return response()->json([
            'available' => $availableDays,
            'used' => $usedDays,
            'pending' => $pendingDays,
            'remaining' => $availableDays - $usedDays - $pendingDays
        ]);
    }

    /**
     * Verificar disponibilidad de fechas para vacaciones
     */
    public function checkAvailability(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employee,id',
            'start_date' => 'required|date|after:today',
            'end_date' => 'required|date|after:start_date'
        ]);

        $employee = Employee::find($request->employee_id);
        $startDate = Carbon::parse($request->start_date);
        $endDate = Carbon::parse($request->end_date);

        // Verificar conflictos con otras vacaciones
        $conflicts = Vacation::where('employee_id', $request->employee_id)
            ->whereIn('status', [Vacation::STATUS_PENDING, Vacation::STATUS_APPROVED])
            ->where(function($query) use ($startDate, $endDate) {
                $query->whereBetween('start_date', [$startDate, $endDate])
                      ->orWhereBetween('end_date', [$startDate, $endDate])
                      ->orWhere(function($q) use ($startDate, $endDate) {
                          $q->where('start_date', '<=', $startDate)
                            ->where('end_date', '>=', $endDate);
                      });
            })
            ->exists();

        // Calcular días solicitados
        $requestedDays = $startDate->diffInDays($endDate) + 1;
        
        // Verificar días disponibles
        $availableDays = $employee->getAvailableVacationDays();
        $usedDays = $employee->getUsedVacationDays();
        $pendingDays = $employee->getPendingVacationDays();
        $remainingDays = $availableDays - $usedDays - $pendingDays;

        return response()->json([
            'available' => !$conflicts && $requestedDays <= $remainingDays,
            'has_conflicts' => $conflicts,
            'requested_days' => $requestedDays,
            'remaining_days' => $remainingDays,
            'message' => $conflicts 
                ? 'Las fechas seleccionadas tienen conflictos con otras vacaciones.'
                : ($requestedDays > $remainingDays 
                    ? 'No tiene suficientes días de vacaciones disponibles.'
                    : 'Fechas disponibles para vacaciones.')
        ]);
    }

    /**
     * Obtener calendario de vacaciones
     */
    public function getCalendar(Request $request)
    {
        $year = $request->get('year', Carbon::now()->year);
        $month = $request->get('month');

        $query = Vacation::with(['employee'])
            ->whereIn('status', [Vacation::STATUS_APPROVED, Vacation::STATUS_COMPLETED])
            ->whereYear('start_date', $year);

        if ($month) {
            $query->whereMonth('start_date', $month);
        }

        $vacations = $query->get()->map(function($vacation) {
            return [
                'id' => $vacation->id,
                'title' => $vacation->employee->names . ' ' . $vacation->employee->lastnames,
                'start' => $vacation->start_date->format('Y-m-d'),
                'end' => $vacation->end_date->addDay()->format('Y-m-d'), // FullCalendar end es exclusivo
                'backgroundColor' => $vacation->status === Vacation::STATUS_APPROVED ? '#28a745' : '#6c757d',
                'borderColor' => $vacation->status === Vacation::STATUS_APPROVED ? '#28a745' : '#6c757d',
                'days' => $vacation->days,
                'status' => $vacation->status
            ];
        });

        return response()->json($vacations);
    }

    /**
     * Generar reporte de vacaciones
     */
    public function generateReport(Request $request)
    {
        $request->validate([
            'year' => 'required|integer|min:2020|max:2030',
            'employee_id' => 'nullable|exists:employee,id',
            'department_id' => 'nullable|exists:departments,id'
        ]);

        $query = Vacation::with(['employee.department']);

        // Filtros
        $query->whereYear('start_date', $request->year);
        
        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        if ($request->filled('department_id')) {
            $query->whereHas('employee', function($q) use ($request) {
                $q->where('departament_id', $request->department_id);
            });
        }

        $vacations = $query->orderBy('start_date')->get();

        $report = [
            'year' => $request->year,
            'total_vacations' => $vacations->count(),
            'total_days' => $vacations->sum('days'),
            'by_status' => $vacations->groupBy('status')->map->count(),
            'by_month' => $vacations->groupBy(function($vacation) {
                return $vacation->start_date->format('n');
            })->map->count(),
            'by_employee' => $vacations->groupBy('employee_id')->map(function($group) {
                return [
                    'employee' => $group->first()->employee->names . ' ' . $group->first()->employee->lastnames,
                    'total_days' => $group->sum('days'),
                    'vacations_count' => $group->count()
                ];
            })->values()
        ];

        return response()->json($report);
    }
}