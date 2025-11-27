<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\EmployeeType;
use App\Models\Department;
use App\Http\Requests\StoreEmployeeRequest;
use App\Http\Requests\UpdateEmployeeRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Employee::with(['employeeType']);

        // Búsqueda por nombre, DNI o email
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('names', 'like', "%{$search}%")
                  ->orWhere('lastnames', 'like', "%{$search}%")
                  ->orWhere('dni', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filtro por tipo de empleado
        if ($request->filled('employee_type_id')) {
            $query->where('type_id', $request->employee_type_id);
        }

        // Filtro por estado
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filtro por edad (rango)
        if ($request->filled('age_from')) {
            $query->whereRaw('TIMESTAMPDIFF(YEAR, birthday, CURDATE()) >= ?', [$request->age_from]);
        }
        if ($request->filled('age_to')) {
            $query->whereRaw('TIMESTAMPDIFF(YEAR, birthday, CURDATE()) <= ?', [$request->age_to]);
        }

        // Ordenamiento
        $sortField = $request->get('sort', 'names');
        $sortDirection = $request->get('direction', 'asc');
        
        if (in_array($sortField, ['names', 'lastnames', 'dni', 'created_at', 'status'])) {
            $query->orderBy($sortField, $sortDirection);
        } else {
            $query->orderBy('names', 'asc');
        }

        // Si es una solicitud AJAX
        if ($request->ajax()) {
            $employees = $query->where('status', 1)
                ->get()
                ->map(function($employee) {
                    return [
                        'id' => $employee->id,
                        'names' => $employee->names,
                        'lastnames' => $employee->lastnames,
                        'dni' => $employee->dni,
                        'name' => "{$employee->names} {$employee->lastnames}"
                    ];
                });
            
            return response()->json([
                'success' => true,
                'data' => $employees
            ]);
        }

        $employees = $query->paginate(15)->withQueryString();

        // Para la vista
        $employeeTypes = EmployeeType::orderBy('name')->get();
        // $departments = \App\Models\Department::orderBy('name')->get(); // Comentado temporalmente

        return view('personnel.employees.index', compact('employees', 'employeeTypes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $employeeTypes = EmployeeType::orderBy('name')->get();
        // $departments = Department::orderBy('name')->get();
        
        return view('personnel.employees.create', compact('employeeTypes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreEmployeeRequest $request)
    {
        $data = $request->validated();

        // Manejar contraseña
        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            // Si no se proporciona contraseña, usar el DNI
            $data['password'] = Hash::make($data['dni']);
        }

        // Manejar upload de foto
        if ($request->hasFile('photo')) {
            $data['photo'] = $this->uploadPhoto($request->file('photo'));
        }

        $employee = Employee::create($data);

        return redirect()
            ->route('admin.personnel.employees.index')
            ->with('success', 'Empleado creado exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Employee $employee)
    {
        $employee->load([
            'employeeType'
            // 'department', // Comentado - relación no existe
            // 'contracts.contractType', // Comentado - para implementar después
            // 'vacations' => function($query) {
            //     $query->orderBy('start_date', 'desc');
            // },
            // 'attendances' => function($query) {
            //     $query->orderBy('datetime', 'desc')->limit(30);
            // }
        ]);

        return view('personnel.employees.show', compact('employee'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Employee $employee)
    {
        $employeeTypes = EmployeeType::orderBy('name')->get();
        // $departments = Department::orderBy('name')->get();
        
        return view('personnel.employees.edit', compact('employee', 'employeeTypes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateEmployeeRequest $request, Employee $employee)
    {
        $data = $request->validated();

        // Manejar contraseña (solo si se proporciona una nueva)
        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            // Si no se proporciona nueva contraseña, no cambiar la actual
            unset($data['password']);
        }

        // Manejar upload de nueva foto
        if ($request->hasFile('photo')) {
            // Eliminar foto anterior si existe
            if ($employee->photo && Storage::disk('public')->exists($employee->photo)) {
                Storage::disk('public')->delete($employee->photo);
            }
            
            $data['photo'] = $this->uploadPhoto($request->file('photo'));
        }

        $employee->update($data);

        return redirect()
            ->route('admin.personnel.employees.index')
            ->with('success', 'Empleado actualizado exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Employee $employee)
    {
        // Verificar si tiene contratos activos
        if ($employee->activeContract) {
            return redirect()
                ->route('admin.personnel.employees.index')
                ->with('error', 'No se puede eliminar un empleado con contrato activo.');
        }

        // Verificar si tiene vacaciones pendientes o aprobadas
        $pendingVacations = $employee->vacations()
            ->whereIn('status', ['pending', 'approved'])
            ->count();

        if ($pendingVacations > 0) {
            return redirect()
                ->route('admin.personnel.employees.index')
                ->with('error', 'No se puede eliminar un empleado con vacaciones pendientes o aprobadas.');
        }

        // Eliminar foto si existe
        if ($employee->photo && Storage::disk('public')->exists($employee->photo)) {
            Storage::disk('public')->delete($employee->photo);
        }

        $employee->delete();

        return redirect()
            ->route('admin.personnel.employees.index')
            ->with('success', 'Empleado eliminado exitosamente.');
    }

    /**
     * Cambiar estado del empleado
     */
    public function toggleStatus(Employee $employee)
    {
        $newStatus = $employee->status === 'active' ? 'inactive' : 'active';
        
        // Si se está desactivando, verificar restricciones
        if ($newStatus === 'inactive') {
            if ($employee->activeContract) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se puede desactivar un empleado con contrato activo.'
                ], 422);
            }

            $pendingVacations = $employee->vacations()
                ->whereIn('status', ['pending', 'approved'])
                ->count();

            if ($pendingVacations > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se puede desactivar un empleado con vacaciones pendientes o aprobadas.'
                ], 422);
            }
        }

        $employee->update(['status' => $newStatus]);

        $statusText = $newStatus === 'active' ? 'activado' : 'desactivado';
        
        return response()->json([
            'success' => true,
            'message' => "Empleado {$statusText} exitosamente.",
            'status' => $newStatus
        ]);
    }

    /**
     * Eliminar foto del empleado
     */
    public function removePhoto(Employee $employee)
    {
        if ($employee->photo && Storage::disk('public')->exists($employee->photo)) {
            Storage::disk('public')->delete($employee->photo);
            $employee->update(['photo' => null]);

            return response()->json([
                'success' => true,
                'message' => 'Foto eliminada exitosamente.'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'No hay foto para eliminar.'
        ], 404);
    }

    /**
     * Obtener empleados activos para select/autocomplete
     */
    public function getActiveEmployees(Request $request)
    {
        $query = Employee::where('status', 1)
            ->select('id', 'names', 'lastnames', 'dni');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('names', 'like', "%{$search}%")
                  ->orWhere('lastnames', 'like', "%{$search}%")
                  ->orWhere('dni', 'like', "%{$search}%");
            });
        }

        $employees = $query->orderBy('names')
            ->limit(20)
            ->get()
            ->map(function($employee) {
                return [
                    'id' => $employee->id,
                    'text' => "{$employee->names} {$employee->lastnames} - {$employee->dni}",
                    'names' => $employee->names,
                    'lastnames' => $employee->lastnames,
                    'dni' => $employee->dni
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $employees
        ]);
    }

    /**
     * Subir foto del empleado
     */
    private function uploadPhoto($file)
    {
        // Generar nombre único
        $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
        
        // Guardar en storage/app/public/employees
        $path = $file->storeAs('employees', $filename, 'public');
        
        return $path;
    }

    /**
     * Exportar empleados a Excel/CSV
     */
    public function export(Request $request)
    {
        // Esta funcionalidad se puede implementar con Laravel Excel
        // Por ahora retornamos los datos básicos
        
        $employees = Employee::with(['employeeType', 'department'])
            ->get()
            ->map(function($employee) {
                return [
                    'DNI' => $employee->dni,
                    'Nombre' => $employee->name,
                    'Apellido' => $employee->lastname,
                    'Email' => $employee->email,
                    'Teléfono' => $employee->phone,
                    'Fecha Nacimiento' => $employee->birthday,
                    'Edad' => $employee->age,
                    'Tipo' => $employee->employeeType?->name,
                    'Departamento' => $employee->department?->name,
                    'Estado' => $employee->status,
                    'Fecha Registro' => $employee->created_at?->format('d/m/Y') ?? 'N/A'
                ];
            });

        return response()->json($employees);
    }
}
