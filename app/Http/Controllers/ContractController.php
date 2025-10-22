<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use App\Models\Employee;
use App\Models\Department;
use App\Http\Requests\StoreContractRequest;
use App\Http\Requests\UpdateContractRequest;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ContractController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Contract::with(['employee']);

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

        // Filtro por tipo de contrato
        if ($request->filled('contract_type_id')) {
            $query->where('contract_type_id', $request->contract_type_id);
        }

        // Filtro por estado
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filtro por fecha de inicio
        if ($request->filled('start_date_from')) {
            $query->whereDate('start_date', '>=', $request->start_date_from);
        }
        if ($request->filled('start_date_to')) {
            $query->whereDate('start_date', '<=', $request->start_date_to);
        }

        // Filtro por contratos próximos a vencer
        if ($request->filled('expiring_soon')) {
            $query->where('status', 'active')
                  ->whereNotNull('end_date')
                  ->whereDate('end_date', '<=', Carbon::now()->addDays(30));
        }

        // Ordenamiento
        $sortField = $request->get('sort', 'start_date');
        $sortDirection = $request->get('direction', 'desc');
        
        if (in_array($sortField, ['start_date', 'end_date', 'salary', 'status', 'created_at'])) {
            $query->orderBy($sortField, $sortDirection);
        } else {
            $query->orderBy('start_date', 'desc');
        }

        $contracts = $query->paginate(15)->withQueryString();

        // Para la vista
        $employees = Employee::where('status', 1)
            ->orderBy('names')
            ->get();
        $contractTypes = Contract::getTypes();

        return view('contracts.index', compact('contracts', 'employees', 'contractTypes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $employees = Employee::where('status', 1)
            ->orderBy('names')
            ->get();
        $contractTypes = Contract::getTypes();
        $departments = Department::orderBy('name')->get();

        $selectedEmployee = null;
        if ($request->filled('employee_id')) {
            $selectedEmployee = Employee::find($request->employee_id);
        }

        return view('contracts.create', compact('employees', 'contractTypes', 'selectedEmployee', 'departments'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreContractRequest $request)
    {
        $data = $request->validated();

        // Desactivar contrato activo anterior si existe
        if (isset($data['status']) && $data['status'] === 'active') {
            Contract::where('employee_id', $data['employee_id'])
                ->where('status', 'active')
                ->update(['status' => 'inactive']);
        }

        $contract = Contract::create($data);

        return redirect()
            ->route('personnel.contracts.index')
            ->with('success', 'Contrato creado exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Contract $contract)
    {
        $contract->load(['employee']);

        return view('contracts.show', compact('contract'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Contract $contract)
    {
        $employees = Employee::where('status', 1)
            ->orderBy('names')
            ->get();
        $contractTypes = Contract::getTypes();
        $departments = Department::orderBy('name')->get();

        return view('contracts.edit', compact('contract', 'employees', 'contractTypes', 'departments'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateContractRequest $request, Contract $contract)
    {
        $data = $request->validated();

        // Si se está activando este contrato, desactivar otros del mismo empleado
        if (isset($data['status']) && $data['status'] === 'active' && $contract->status !== 'active') {
            Contract::where('employee_id', $contract->employee_id)
                ->where('id', '!=', $contract->id)
                ->where('status', 'active')
                ->update(['status' => 'inactive']);
        }

        $contract->update($data);

        return redirect()
            ->route('personnel.contracts.index')
            ->with('success', 'Contrato actualizado exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Contract $contract)
    {
        // No permitir eliminar contratos activos
        if ($contract->status === 'active') {
            return redirect()
                ->route('personnel.contracts.index')
                ->with('error', 'No se puede eliminar un contrato activo.');
        }

        $contract->delete();

        return redirect()
            ->route('personnel.contracts.index')
            ->with('success', 'Contrato eliminado exitosamente.');
    }

    /**
     * Activar contrato
     */
    public function activate(Contract $contract)
    {
        // Verificar que no esté ya activo
        if ($contract->status === 'active') {
            return response()->json([
                'success' => false,
                'message' => 'El contrato ya está activo.'
            ], 422);
        }

        // Desactivar otros contratos del mismo empleado
        Contract::where('employee_id', $contract->employee_id)
            ->where('id', '!=', $contract->id)
            ->where('status', 'active')
            ->update(['status' => 'inactive']);

        $contract->update(['status' => 'active']);

        return response()->json([
            'success' => true,
            'message' => 'Contrato activado exitosamente.'
        ]);
    }

    /**
     * Desactivar contrato
     */
    public function deactivate(Contract $contract)
    {
        if ($contract->status !== 'active') {
            return response()->json([
                'success' => false,
                'message' => 'El contrato no está activo.'
            ], 422);
        }

        $contract->update(['status' => 'inactive']);

        return response()->json([
            'success' => true,
            'message' => 'Contrato desactivado exitosamente.'
        ]);
    }

    /**
     * Finalizar contrato
     */
    public function finalize(Contract $contract, Request $request)
    {
        $request->validate([
            'end_date' => 'required|date|after_or_equal:' . $contract->start_date,
            'reason' => 'nullable|string|max:500'
        ]);

        $contract->update([
            'status' => 'finished',
            'end_date' => $request->end_date,
            'notes' => $request->reason
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Contrato finalizado exitosamente.'
        ]);
    }

    /**
     * Renovar contrato
     */
    public function renew(Contract $contract, Request $request)
    {
        $request->validate([
            'start_date' => 'required|date|after:' . $contract->end_date,
            'end_date' => 'nullable|date|after:start_date',
            'salary' => 'required|numeric|min:0',
            'contract_type_id' => 'required|exists:contract_types,id'
        ]);

        // Crear nuevo contrato basado en el anterior
        $newContract = Contract::create([
            'employee_id' => $contract->employee_id,
            'contract_type_id' => $request->contract_type_id,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'salary' => $request->salary,
            'status' => 'active',
            'notes' => 'Renovación del contrato #' . $contract->id
        ]);

        // Finalizar contrato anterior
        $contract->update([
            'status' => 'finished',
            'end_date' => Carbon::parse($request->start_date)->subDay()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Contrato renovado exitosamente.',
            'new_contract_id' => $newContract->id
        ]);
    }

    /**
     * Obtener contratos próximos a vencer
     */
    public function getExpiringContracts()
    {
        $contracts = Contract::with(['employee'])
            ->where('status', 'active')
            ->whereNotNull('end_date')
            ->whereDate('end_date', '<=', Carbon::now()->addDays(30))
            ->orderBy('end_date')
            ->get()
            ->map(function($contract) {
                return [
                    'id' => $contract->id,
                    'employee' => $contract->employee->names . ' ' . $contract->employee->lastnames,
                    'contract_type' => $contract->contract_type_name,
                    'end_date' => $contract->end_date->format('d/m/Y'),
                    'days_remaining' => $contract->end_date->diffInDays(Carbon::now()),
                    'salary' => $contract->salary
                ];
            });

        return response()->json($contracts);
    }

    /**
     * Obtener estadísticas de contratos
     */
    public function getStatistics()
    {
        $statistics = [
            'total' => Contract::count(),
            'active' => Contract::where('status', 'active')->count(),
            'inactive' => Contract::where('status', 'inactive')->count(),
            'finished' => Contract::where('status', 'finished')->count(),
            'expiring_soon' => Contract::where('status', 'active')
                ->whereNotNull('end_date')
                ->whereDate('end_date', '<=', Carbon::now()->addDays(30))
                ->count(),
            'by_type' => Contract::where('status', 'active')
                ->selectRaw('contrato_type, COUNT(*) as count')
                ->groupBy('contrato_type')
                ->get()
                ->pluck('count', 'contrato_type'),
            'total_payroll' => Contract::where('status', 'active')->sum('salary')
        ];

        return response()->json($statistics);
    }

    /**
     * Validar si se puede crear contrato eventual
     */
    public function validateEventualContract(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employee,id',
            'start_date' => 'required|date'
        ]);

        $employee = Employee::find($request->employee_id);
        $lastContract = $employee->contracts()
            ->where('status', 'finished')
            ->orderBy('end_date', 'desc')
            ->first();

        if (!$lastContract) {
            return response()->json([
                'valid' => true,
                'message' => 'Empleado sin contratos previos.'
            ]);
        }

        $daysSinceLastContract = Carbon::parse($request->start_date)
            ->diffInDays($lastContract->end_date);

        $canCreateEventual = $daysSinceLastContract <= 60;

        return response()->json([
            'valid' => $canCreateEventual,
            'days_since_last' => $daysSinceLastContract,
            'last_contract_end' => $lastContract->end_date->format('d/m/Y'),
            'message' => $canCreateEventual 
                ? 'Puede crear contrato eventual.'
                : 'No puede crear contrato eventual. Han pasado más de 60 días.'
        ]);
    }
}