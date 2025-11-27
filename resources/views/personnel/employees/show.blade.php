@extends('adminlte::page')

@section('title', 'Perfil del Empleado')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="h3 mb-1 font-weight-bold">
                <i class="fas fa-user text-primary"></i> Perfil del Empleado
            </h1>
            <p class="text-muted mb-0">Información detallada de {{ $employee->names }} {{ $employee->lastnames }}</p>
        </div>
        <div>
            <a href="{{ route('admin.personnel.employees.edit', $employee) }}" class="btn btn-warning">
                <i class="fas fa-edit"></i> Editar
            </a>
            <a href="{{ route('admin.personnel.employees.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Volver al Listado
            </a>
        </div>
    </div>
@stop

@section('content')
    <div class="row">
        <!-- Información Principal -->
        <div class="col-lg-8">
            <!-- Información Personal -->
            <div class="card shadow-sm">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-user"></i> Información Personal
                    </h3>
                    <div class="card-tools">
                        <span class="badge badge-{{ $employee->status == 'active' ? 'success' : ($employee->status == 'inactive' ? 'secondary' : ($employee->status == 'suspended' ? 'warning' : 'danger')) }}">
                            {{ ucfirst($employee->status) }}
                        </span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="info-group mb-3">
                                <label class="info-label">Nombres Completos:</label>
                                <p class="info-value">{{ $employee->names }} {{ $employee->lastnames }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-group mb-3">
                                <label class="info-label">DNI:</label>
                                <p class="info-value">{{ $employee->dni }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="info-group mb-3">
                                <label class="info-label">Fecha de Nacimiento:</label>
                                <p class="info-value">
                                    @if($employee->birthday)
                                        {{ $employee->birthday ? $employee->birthday->format('d/m/Y') : 'No especificado' }}
                                        <small class="text-muted">({{ $employee->birthday->age }} años)</small>
                                    @else
                                        <span class="text-muted">No especificado</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-group mb-3">
                                <label class="info-label">Género:</label>
                                <p class="info-value">
                                    @if($employee->gender)
                                        @switch($employee->gender)
                                            @case('M')
                                                <i class="fas fa-mars text-primary"></i> Masculino
                                                @break
                                            @case('F')
                                                <i class="fas fa-venus text-pink"></i> Femenino
                                                @break
                                            @case('O')
                                                <i class="fas fa-genderless text-info"></i> Otro
                                                @break
                                        @endswitch
                                    @else
                                        <span class="text-muted">No especificado</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="info-group mb-3">
                                <label class="info-label">Teléfono:</label>
                                <p class="info-value">
                                    @if($employee->phone)
                                        <i class="fas fa-phone text-success"></i> {{ $employee->phone }}
                                        <a href="tel:{{ $employee->phone }}" class="btn btn-sm btn-outline-success ml-2">
                                            <i class="fas fa-phone-alt"></i> Llamar
                                        </a>
                                    @else
                                        <span class="text-muted">No especificado</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-group mb-3">
                                <label class="info-label">Email:</label>
                                <p class="info-value">
                                    @if($employee->email)
                                        <i class="fas fa-envelope text-info"></i> {{ $employee->email }}
                                        <a href="mailto:{{ $employee->email }}" class="btn btn-sm btn-outline-info ml-2">
                                            <i class="fas fa-envelope"></i> Enviar
                                        </a>
                                    @else
                                        <span class="text-muted">No especificado</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <div class="info-group mb-3">
                                <label class="info-label">Dirección:</label>
                                <p class="info-value">
                                    @if($employee->address)
                                        <i class="fas fa-map-marker-alt text-danger"></i> {{ $employee->address }}
                                    @else
                                        <span class="text-muted">No especificado</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Información Laboral -->
            <div class="card shadow-sm">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-briefcase"></i> Información Laboral
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="info-group mb-3">
                                <label class="info-label">Tipo de Empleado:</label>
                                <p class="info-value">
                                    @if($employee->employeeType)
                                        <span class="badge badge-primary">{{ $employee->employeeType->name }}</span>
                                    @else
                                        <span class="text-muted">No asignado</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-group mb-3">
                                <label class="info-label">Fecha de Contratación:</label>
                                <p class="info-value">
                                    @if($employee->hire_date)
                                        {{ $employee->hire_date->format('d/m/Y') }}
                                        <small class="text-muted">({{ $employee->hire_date->diffForHumans() }})</small>
                                    @else
                                        <span class="text-muted">No especificado</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="info-group mb-3">
                                <label class="info-label">Salario:</label>
                                <p class="info-value">
                                    @if($employee->salary)
                                        <span class="font-weight-bold text-success">S/ {{ number_format($employee->salary, 2) }}</span>
                                    @else
                                        <span class="text-muted">No especificado</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-group mb-3">
                                <label class="info-label">Estado:</label>
                                <p class="info-value">
                                    <span class="badge badge-{{ $employee->status == 'active' ? 'success' : ($employee->status == 'inactive' ? 'secondary' : ($employee->status == 'suspended' ? 'warning' : 'danger')) }}">
                                        @switch($employee->status)
                                            @case('active')
                                                <i class="fas fa-check"></i> Activo
                                                @break
                                            @case('inactive')
                                                <i class="fas fa-pause"></i> Inactivo
                                                @break
                                            @case('suspended')
                                                <i class="fas fa-exclamation-triangle"></i> Suspendido
                                                @break
                                            @case('terminated')
                                                <i class="fas fa-times"></i> Terminado
                                                @break
                                            @default
                                                {{ ucfirst($employee->status) }}
                                        @endswitch
                                    </span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Estadísticas Rápidas -->
            <div class="card shadow-sm">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-chart-bar"></i> Resumen de Actividad
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="info-box bg-info">
                                <span class="info-box-icon"><i class="fas fa-calendar-check"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Asistencias</span>
                                    <span class="info-box-number">--</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box bg-success">
                                <span class="info-box-icon"><i class="fas fa-umbrella-beach"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Vacaciones</span>
                                    <span class="info-box-number">--</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box bg-warning">
                                <span class="info-box-icon"><i class="fas fa-file-contract"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Contratos</span>
                                    <span class="info-box-number">--</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box bg-secondary">
                                <span class="info-box-icon"><i class="fas fa-clock"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Antigüedad</span>
                                    <span class="info-box-number">
                                        @if($employee->hire_date)
                                            {{ $employee->hire_date->diffInDays(now()) }} días
                                        @else
                                            --
                                        @endif
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Panel Lateral -->
        <div class="col-lg-4">
            <!-- Foto del Empleado -->
            <div class="card shadow-sm">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-camera"></i> Foto del Empleado
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.personnel.employees.edit', $employee) }}" class="btn btn-sm btn-warning">
                            <i class="fas fa-edit"></i> Cambiar
                        </a>
                    </div>
                </div>
                <div class="card-body text-center">
                    @if($employee->photo)
                        <img src="{{ asset('storage/' . $employee->photo) }}" 
                             alt="Foto de {{ $employee->names }}" 
                             class="img-thumbnail employee-photo" 
                             style="max-width: 100%; max-height: 300px;">
                    @else
                        <div class="placeholder-photo bg-light d-flex align-items-center justify-content-center" 
                             style="width: 100%; height: 200px;">
                            <div class="text-center">
                                <i class="fas fa-user fa-5x text-muted mb-3"></i>
                                <p class="text-muted">Sin foto</p>
                            </div>
                        </div>
                    @endif
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
                        <strong>ID del Empleado:</strong><br>
                        <span class="text-muted">#{{ str_pad($employee->id, 6, '0', STR_PAD_LEFT) }}</span>
                    </div>
                    <div class="info-item">
                        <strong>Fecha de Registro:</strong><br>
                        <span class="text-muted">{{ $employee->created_at?->format('d/m/Y H:i') ?? 'N/A' }}</span>
                    </div>
                    <div class="info-item">
                        <strong>Última Actualización:</strong><br>
                        <span class="text-muted">{{ $employee->updated_at?->format('d/m/Y H:i') ?? 'N/A' }}</span>
                    </div>
                    <div class="info-item">
                        <strong>Actualizado por:</strong><br>
                        <span class="text-muted">Sistema</span>
                    </div>
                </div>
            </div>

            <!-- Acciones Rápidas -->
            <div class="card shadow-sm">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-bolt"></i> Acciones Rápidas
                    </h3>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.personnel.employees.edit', $employee) }}" class="btn btn-warning btn-block mb-2">
                            <i class="fas fa-edit"></i> Editar Información
                        </a>
                        
                        @if($employee->status == 'active')
                            <button class="btn btn-danger btn-block mb-2" onclick="changeStatus('inactive')">
                                <i class="fas fa-pause"></i> Desactivar Empleado
                            </button>
                        @else
                            <button class="btn btn-success btn-block mb-2" onclick="changeStatus('active')">
                                <i class="fas fa-play"></i> Activar Empleado
                            </button>
                        @endif
                        
                        <button class="btn btn-info btn-block mb-2" onclick="generateReport()">
                            <i class="fas fa-file-pdf"></i> Generar Reporte
                        </button>
                        
                        <form action="{{ route('admin.personnel.employees.destroy', $employee) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Está seguro de eliminar este empleado? Esta acción no se puede deshacer.')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-block">
                                <i class="fas fa-trash"></i> Eliminar Empleado
                            </button>
                        </form>
                    </div>
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
    
    .employee-photo {
        border-radius: 8px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    }
    
    .placeholder-photo {
        border: 2px dashed #ddd;
        border-radius: 8px;
    }
    
    .info-item {
        padding: 12px 0;
        border-bottom: 1px solid #eee;
    }
    
    .info-item:last-child {
        border-bottom: none;
        padding-bottom: 0;
    }
    
    .card {
        border: none;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    }
    
    .card-header {
        background: #f8f9fa;
        border-bottom: 1px solid #dee2e6;
    }
    
    .info-box {
        border-radius: 0.375rem;
        margin-bottom: 1rem;
    }
    
    .badge {
        font-size: 0.875rem;
        padding: 0.375rem 0.75rem;
    }
</style>
@endpush

@push('js')
<script>
    function changeStatus(newStatus) {
        const employeeId = {{ $employee->id }};
        const statusText = newStatus === 'active' ? 'activar' : 'desactivar';
        
        if (confirm(`¿Está seguro de ${statusText} este empleado?`)) {
            // Aquí se puede implementar una llamada AJAX para cambiar el estado
            // Por ahora, redirigir a la página de edición
            window.location.href = `/admin/personnel/employees/${employeeId}/edit`;
        }
    }
    
    function generateReport() {
        const employeeId = {{ $employee->id }};
        // Implementar generación de reporte PDF
        toastr.info('Función de reporte en desarrollo');
    }
    
    $(document).ready(function() {
        // Inicializar tooltips
        $('[data-toggle="tooltip"]').tooltip();
        
        // Efectos hover para las acciones
        $('.btn').hover(
            function() { $(this).addClass('shadow-sm'); },
            function() { $(this).removeClass('shadow-sm'); }
        );
    });
</script>
@endpush