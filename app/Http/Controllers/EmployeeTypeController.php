<?php

namespace App\Http\Controllers;

use App\Models\EmployeeType;
use App\Models\Employee;
use App\Http\Requests\StoreEmployeeTypeRequest;
use App\Http\Requests\UpdateEmployeeTypeRequest;
use Illuminate\Http\Request;

class EmployeeTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = EmployeeType::withCount('employees');

        // Búsqueda por nombre o descripción
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filtro por estado protegido
        if ($request->filled('protected')) {
            $query->where('protected', $request->protected);
        }

        // Ordenamiento
        $sortField = $request->get('sort', 'name');
        $sortDirection = $request->get('direction', 'asc');
        
        if (in_array($sortField, ['name', 'description', 'protected', 'created_at'])) {
            $query->orderBy($sortField, $sortDirection);
        } else {
            $query->orderBy('name', 'asc');
        }

        $employeeTypes = $query->paginate(10)->withQueryString();

        return view('personnel.employee-types.index', compact('employeeTypes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('personnel.employee-types.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreEmployeeTypeRequest $request)
    {
        $data = $request->validated();
        
        // Los nuevos tipos no son protegidos por defecto
        $data['protected'] = false;

        $employeeType = EmployeeType::create($data);

        return redirect()
            ->route('personnel.employee-types.index')
            ->with('success', 'Tipo de empleado creado exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(EmployeeType $employeeType)
    {
        $employeeType->load(['employees' => function($query) {
            $query->orderBy('names')->take(10);
        }]);

        return view('personnel.employee-types.show', compact('employeeType'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(EmployeeType $employeeType)
    {
        return view('personnel.employee-types.edit', compact('employeeType'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateEmployeeTypeRequest $request, EmployeeType $employeeType)
    {
        $data = $request->validated();

        // No permitir cambiar el estado protegido de tipos ya protegidos
        if ($employeeType->protected) {
            unset($data['protected']);
        }

        $employeeType->update($data);

        return redirect()
            ->route('personnel.employee-types.index')
            ->with('success', 'Tipo de empleado actualizado exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(EmployeeType $employeeType)
    {
        // Verificar si es un tipo protegido
        if ($employeeType->protected) {
            return redirect()
                ->route('personnel.employee-types.index')
                ->with('error', 'No se puede eliminar un tipo de empleado protegido.');
        }

        // Verificar si tiene empleados asociados
        if ($employeeType->employees()->count() > 0) {
            return redirect()
                ->route('personnel.employee-types.index')
                ->with('error', 'No se puede eliminar un tipo de empleado que tiene empleados asociados.');
        }

        $employeeType->delete();

        return redirect()
            ->route('personnel.employee-types.index')
            ->with('success', 'Tipo de empleado eliminado exitosamente.');
    }

    /**
     * Obtener tipos de empleado para select
     */
    public function getForSelect(Request $request)
    {
        $query = EmployeeType::select('id', 'name');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%");
        }

        $employeeTypes = $query->orderBy('name')
            ->get()
            ->map(function($type) {
                return [
                    'id' => $type->id,
                    'text' => $type->name
                ];
            });

        return response()->json($employeeTypes);
    }

    /**
     * Obtener estadísticas de empleados por tipo
     */
    public function getStatistics()
    {
        $statistics = EmployeeType::withCount([
            'employees',
            'employees as active_employees_count' => function($query) {
                $query->where('status', 'active');
            },
            'employees as inactive_employees_count' => function($query) {
                $query->where('status', 'inactive');
            }
        ])->get()->map(function($type) {
            return [
                'name' => $type->name,
                'total' => $type->employees_count,
                'active' => $type->active_employees_count,
                'inactive' => $type->inactive_employees_count,
                'protected' => $type->protected
            ];
        });

        return response()->json($statistics);
    }

    /**
     * Duplicar tipo de empleado (crear uno nuevo basado en existente)
     */
    public function duplicate(EmployeeType $employeeType)
    {
        $newEmployeeType = $employeeType->replicate();
        $newEmployeeType->name = $employeeType->name . ' (Copia)';
        $newEmployeeType->protected = false; // Las copias nunca son protegidas
        $newEmployeeType->save();

        return redirect()
            ->route('personnel.employee-types.index')
            ->with('success', 'Tipo de empleado duplicado exitosamente.');
    }

    /**
     * Cambiar orden de tipos de empleado
     */
    public function updateOrder(Request $request)
    {
        $request->validate([
            'orders' => 'required|array',
            'orders.*.id' => 'required|exists:employeetype,id',
            'orders.*.order' => 'required|integer|min:0'
        ]);

        foreach ($request->orders as $orderData) {
            EmployeeType::where('id', $orderData['id'])
                ->update(['order' => $orderData['order']]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Orden actualizado exitosamente.'
        ]);
    }
}