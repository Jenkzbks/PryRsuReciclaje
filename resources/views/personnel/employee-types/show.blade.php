@extends('adminlte::page')

@section('title', 'Tipo de Empleado - ' . $employeeType->name)

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="h3 mb-1 font-weight-bold">
                <i class="fas fa-user-tag text-primary"></i> {{ $employeeType->name }}
            </h1>
            <p class="text-muted mb-0">Detalles del tipo de empleado</p>
        </div>
        <div>
            <a href="{{ route('personnel.employee-types.edit', $employeeType) }}" class="btn btn-warning">
                <i class="fas fa-edit"></i> Editar
            </a>
            <a href="{{ route('personnel.employee-types.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Volver al Listado
            </a>
        </div>
    </div>
@stop

@section('content')
    <div class="row">
        <div class="col-lg-8">
            <!-- Información Principal -->
            <div class="card shadow-sm">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-info-circle"></i> Información General
                    </h3>
                    <div class="card-tools">
                        <span class="badge badge-{{ $employeeType->active ? 'success' : 'secondary' }}">
                            {{ $employeeType->active ? 'Activo' : 'Inactivo' }}
                        </span>
                        @if($employeeType->protected)
                            <span class="badge badge-warning">
                                <i class="fas fa-shield-alt"></i> Protegido
                            </span>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="info-group mb-3">
                                <label class="info-label">Nombre:</label>
                                <p class="info-value">{{ $employeeType->name }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-group mb-3">
                                <label class="info-label">Código:</label>
                                <p class="info-value">
                                    @if($employeeType->code)
                                        <code>{{ $employeeType->code }}</code>
                                    @else
                                        <span class="text-muted">No definido</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="info-group mb-3">
                        <label class="info-label">Descripción:</label>
                        <p class="info-value">
                            @if($employeeType->description)
                                {{ $employeeType->description }}
                            @else
                                <span class="text-muted">Sin descripción</span>
                            @endif
                        </p>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="info-group mb-3">
                                <label class="info-label">Nivel Jerárquico:</label>
                                <p class="info-value">
                                    @if($employeeType->level)
                                        @switch($employeeType->level)
                                            @case(1)
                                                <span class="badge badge-danger">Directivo</span>
                                                @break
                                            @case(2)
                                                <span class="badge badge-warning">Gerencial</span>
                                                @break
                                            @case(3)
                                                <span class="badge badge-info">Supervisión</span>
                                                @break
                                            @case(4)
                                                <span class="badge badge-primary">Operativo</span>
                                                @break
                                            @case(5)
                                                <span class="badge badge-secondary">Apoyo</span>
                                                @break
                                        @endswitch
                                    @else
                                        <span class="text-muted">No definido</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-group mb-3">
                                <label class="info-label">Orden:</label>
                                <p class="info-value">{{ $employeeType->sort_order ?? 0 }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Empleados Asociados -->
            <div class="card shadow-sm">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-users"></i> Empleados con este Tipo
                    </h3>
                    <div class="card-tools">
                        <span class="badge badge-primary">{{ $employeeType->employees->count() }} empleado(s)</span>
                    </div>
                </div>
                <div class="card-body">
                    @if($employeeType->employees->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Empleado</th>
                                        <th>DNI</th>
                                        <th>Estado</th>
                                        <th>Fecha Ingreso</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($employeeType->employees->take(10) as $employee)
                                        <tr>
                                            <td>
                                                <a href="{{ route('admin.personnel.employees.show', $employee) }}">
                                                    {{ $employee->names }} {{ $employee->lastnames }}
                                                </a>
                                            </td>
                                            <td>{{ $employee->dni }}</td>
                                            <td>
                                                <span class="badge badge-{{ $employee->status == 1 ? 'success' : 'secondary' }}">
                                                    {{ $employee->status == 1 ? 'Activo' : 'Inactivo' }}
                                                </span>
                                            </td>
                                            <td>{{ $employee->created_at->format('d/m/Y') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        @if($employeeType->employees->count() > 10)
                            <div class="text-center mt-3">
                                <a href="{{ route('admin.personnel.employees.index', ['employee_type_id' => $employeeType->id]) }}" 
                                   class="btn btn-outline-primary">
                                    Ver todos los empleados ({{ $employeeType->employees->count() }})
                                </a>
                            </div>
                        @endif
                    @else
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-users fa-3x mb-3"></i>
                            <p>No hay empleados asignados a este tipo</p>
                            <a href="{{ route('admin.personnel.employees.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Crear Primer Empleado
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Apariencia -->
            <div class="card shadow-sm">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-palette"></i> Apariencia
                    </h3>
                </div>
                <div class="card-body text-center">
                    <div class="mb-3">
                        <span class="badge badge-pill p-3" 
                              style="background-color: {{ $employeeType->color ?? '#007bff' }}; color: white; font-size: 1.1rem;">
                            <i class="{{ $employeeType->icon ?? 'fas fa-user' }} mr-2"></i>
                            {{ $employeeType->name }}
                        </span>
                    </div>
                    
                    <div class="row">
                        <div class="col-6">
                            <div class="info-group">
                                <label class="info-label">Color:</label>
                                <div>
                                    <div class="color-preview d-inline-block rounded" 
                                         style="width: 30px; height: 30px; background-color: {{ $employeeType->color ?? '#007bff' }}; border: 1px solid #ddd;"></div>
                                    <code class="ml-2">{{ $employeeType->color ?? '#007bff' }}</code>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="info-group">
                                <label class="info-label">Icono:</label>
                                <div>
                                    <i class="{{ $employeeType->icon ?? 'fas fa-user' }} fa-2x"></i>
                                    <br>
                                    <code>{{ $employeeType->icon ?? 'fas fa-user' }}</code>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Información del Sistema -->
            <div class="card shadow-sm">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-info-circle"></i> Información del Sistema
                    </h3>
                </div>
                <div class="card-body">
                    <div class="info-item">
                        <strong>ID:</strong><br>
                        <span class="text-muted">#{{ $employeeType->id }}</span>
                    </div>
                    <div class="info-item">
                        <strong>Creado:</strong><br>
                        <span class="text-muted">{{ $employeeType->created_at->format('d/m/Y H:i') }}</span>
                    </div>
                    <div class="info-item">
                        <strong>Última Actualización:</strong><br>
                        <span class="text-muted">{{ $employeeType->updated_at->format('d/m/Y H:i') }}</span>
                    </div>
                    <div class="info-item">
                        <strong>Estado:</strong><br>
                        <span class="badge badge-{{ $employeeType->active ? 'success' : 'secondary' }}">
                            {{ $employeeType->active ? 'Activo' : 'Inactivo' }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Acciones -->
            <div class="card shadow-sm">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-bolt"></i> Acciones Rápidas
                    </h3>
                </div>
                <div class="card-body">
                    <a href="{{ route('personnel.employee-types.edit', $employeeType) }}" 
                       class="btn btn-warning btn-block mb-2">
                        <i class="fas fa-edit"></i> Editar Tipo
                    </a>
                    
                    <button type="button" class="btn btn-info btn-block mb-2" onclick="duplicateType()">
                        <i class="fas fa-copy"></i> Duplicar Tipo
                    </button>
                    
                    @if(!$employeeType->protected)
                        <form action="{{ route('personnel.employee-types.destroy', $employeeType) }}" 
                              method="POST" 
                              class="d-inline w-100"
                              onsubmit="return confirm('¿Está seguro de eliminar este tipo de empleado?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-block">
                                <i class="fas fa-trash"></i> Eliminar Tipo
                            </button>
                        </form>
                    @else
                        <button type="button" class="btn btn-danger btn-block" disabled title="Tipo protegido">
                            <i class="fas fa-shield-alt"></i> Tipo Protegido
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>
@stop

@push('css')
<style>
    .info-label {
        font-weight: 600;
        color: #495057;
        font-size: 0.9rem;
        margin-bottom: 0.25rem;
    }
    
    .info-value {
        margin-bottom: 0;
        font-size: 1rem;
        color: #212529;
    }
    
    .info-group {
        padding-bottom: 0.75rem;
        border-bottom: 1px solid #eee;
    }
    
    .info-group:last-child {
        border-bottom: none;
        padding-bottom: 0;
    }
    
    .info-item {
        padding: 12px 0;
        border-bottom: 1px solid #eee;
    }
    
    .info-item:last-child {
        border-bottom: none;
        padding-bottom: 0;
    }
    
    .color-preview {
        vertical-align: middle;
    }
</style>
@endpush

@push('js')
<script>
    function duplicateType() {
        if (confirm('¿Crear una copia de este tipo de empleado?')) {
            $.post('{{ route("personnel.employee-types.duplicate", $employeeType) }}', {
                _token: '{{ csrf_token() }}'
            }).done(function(response) {
                location.reload();
            }).fail(function() {
                alert('Error al duplicar el tipo de empleado');
            });
        }
    }
</script>
@endpush