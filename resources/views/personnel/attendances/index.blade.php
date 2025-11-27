@extends('adminlte::page')

@section('title', 'Asistencias')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="h3 mb-1 font-weight-bold">
                <i class="fas fa-clock text-primary"></i> Gestión de Asistencias
            </h1>
            <p class="text-muted mb-0">Control y seguimiento de la asistencia del personal</p>
        </div>
        <div class="btn-group">
            <a href="{{ route('admin.personnel.attendances.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Nueva Asistencia
            </a>
            <a href="#" class="btn btn-success">
                <i class="fas fa-file-excel"></i> Exportar
            </a>
        </div>
    </div>
@stop

@section('content')
    <!-- Filtros de Búsqueda -->
    <div class="card shadow-sm">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-filter"></i> Filtros de Búsqueda
            </h3>
            <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                    <i class="fas fa-minus"></i>
                </button>
            </div>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.personnel.attendances.index') }}">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="search">Buscar Empleado</label>
                            <input type="text" 
                                   name="search" 
                                   id="search"
                                   class="form-control" 
                                   placeholder="Nombre, DNI..."
                                   value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="date">Fecha</label>
                            <input type="date" 
                                   name="date" 
                                   id="date"
                                   class="form-control" 
                                   value="{{ request('date') }}">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="status">Estado</label>
                            <select name="status" id="status" class="form-control">
                                <option value="">Todos los estados</option>
                                <option value="present" {{ request('status') == 'present' ? 'selected' : '' }}>Presente</option>
                                <option value="late" {{ request('status') == 'late' ? 'selected' : '' }}>Tarde</option>
                                <option value="absent" {{ request('status') == 'absent' ? 'selected' : '' }}>Ausente</option>
                                <option value="half_day" {{ request('status') == 'half_day' ? 'selected' : '' }}>Medio Día</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>&nbsp;</label>
                            <div class="d-flex">
                                <button type="submit" class="btn btn-primary mr-2">
                                    <i class="fas fa-search"></i> Buscar
                                </button>
                                <a href="{{ route('admin.personnel.attendances.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Limpiar
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Listado de Asistencias -->
    <div class="card shadow-sm">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-list"></i> Asistencias 
                <span class="badge badge-primary">{{ $attendances->total() ?? count($attendances) }} total</span>
            </h3>
            <div class="card-tools">
                @if(method_exists($attendances, 'total'))
                    <span class="text-muted">
                        Mostrando {{ $attendances->firstItem() ?? 0 }} - {{ $attendances->lastItem() ?? 0 }} de {{ $attendances->total() }}
                    </span>
                @endif
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="thead-light">
                        <tr>
                            <th width="80">#</th>
                            <th>Empleado</th>
                            <th>Fecha</th>
                            <th>Entrada</th>
                            <th>Salida</th>
                            <th>Estado</th>
                            <th width="150">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($attendances as $index => $attendance)
                            <tr>
                                <td>{{ method_exists($attendances, 'currentPage') ? (($attendances->currentPage() - 1) * $attendances->perPage() + $loop->iteration) : ($index + 1) }}</td>
                                <td>
                                    <div class="media align-items-center">
                                        <div class="media-object">
                                            <div class="avatar bg-light-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                <i class="fas fa-user text-primary"></i>
                                            </div>
                                        </div>
                                        <div class="media-body ml-3">
                                            <h6 class="mb-0">{{ $attendance->employee->names ?? 'N/A' }} {{ $attendance->employee->lastnames ?? '' }}</h6>
                                            <small class="text-muted">{{ $attendance->employee->dni ?? 'Sin DNI' }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="text-dark">{{ $attendance->date ? \Carbon\Carbon::parse($attendance->date)->format('d/m/Y') : 'N/A' }}</span>
                                    <small class="d-block text-muted">{{ $attendance->date ? \Carbon\Carbon::parse($attendance->date)->translatedFormat('l') : '' }}</small>
                                </td>
                                <td>
                                    @if($attendance->check_in)
                                        <span class="text-success font-weight-medium">{{ \Carbon\Carbon::parse($attendance->check_in)->format('H:i:s') }}</span>
                                    @else
                                        <span class="text-muted">--:--</span>
                                    @endif
                                </td>
                                <td>
                                    @if($attendance->check_out)
                                        <span class="text-danger font-weight-medium">{{ \Carbon\Carbon::parse($attendance->check_out)->format('H:i:s') }}</span>
                                    @else
                                        <span class="text-muted">--:--</span>
                                    @endif
                                </td>
                                <td>
                                    @php
                                        $statusColors = [
                                            'present' => 'success',
                                            'late' => 'warning', 
                                            'absent' => 'danger',
                                            'half_day' => 'info'
                                        ];
                                        $statusLabels = [
                                            'present' => 'Presente',
                                            'late' => 'Tarde',
                                            'absent' => 'Ausente', 
                                            'half_day' => 'Medio Día'
                                        ];
                                        $statusIcons = [
                                            'present' => 'check',
                                            'late' => 'clock',
                                            'absent' => 'times',
                                            'half_day' => 'minus'
                                        ];
                                        $color = $statusColors[$attendance->status ?? 'present'] ?? 'secondary';
                                        $label = $statusLabels[$attendance->status ?? 'present'] ?? ($attendance->status ?? 'Desconocido');
                                        $icon = $statusIcons[$attendance->status ?? 'present'] ?? 'question';
                                    @endphp
                                    <span class="badge badge-{{ $color }}">
                                        <i class="fas fa-{{ $icon }}"></i> {{ $label }}
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('admin.personnel.attendances.show', $attendance->id) }}" class="btn btn-sm btn-info" title="Ver detalles">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.personnel.attendances.edit', $attendance->id) }}" class="btn btn-sm btn-warning" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button class="btn btn-sm btn-danger" title="Eliminar" onclick="confirmDelete({{ $attendance->id ?? 0 }})">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4">
                                    <div class="empty-state">
                                        <i class="fas fa-clock fa-3x text-muted mb-3"></i>
                                        <h5 class="text-muted">No hay registros de asistencia</h5>
                                        <p class="text-muted">No se encontraron asistencias con los criterios seleccionados.</p>
                                        <a href="{{ route('admin.personnel.attendances.create') }}" class="btn btn-primary">
                                            <i class="fas fa-plus"></i> Registrar Primera Asistencia
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        
        @if(method_exists($attendances, 'links'))
            <div class="card-footer">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <p class="mb-0 text-muted">
                            Mostrando {{ $attendances->firstItem() }} a {{ $attendances->lastItem() }} de {{ $attendances->total() }} resultados
                        </p>
                    </div>
                    <div class="col-md-6">
                        <div class="float-right">
                            {{ $attendances->appends(request()->input())->links() }}
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="card-footer">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <p class="mb-0 text-muted">
                            Total de registros: {{ count($attendances) }}
                        </p>
                    </div>
                </div>
            </div>
        @endif
    </div>
@stop

@section('css')
<style>
    .empty-state {
        padding: 40px 20px;
    }
    
    .avatar {
        font-size: 14px;
    }
    
    .media-object {
        flex: none;
    }
    
    .btn-group .btn {
        border-radius: 0;
    }
    
    .btn-group .btn:first-child {
        border-top-left-radius: .25rem;
        border-bottom-left-radius: .25rem;
    }
    
    .btn-group .btn:last-child {
        border-top-right-radius: .25rem;
        border-bottom-right-radius: .25rem;
    }
    
    .card-tools {
        margin-left: auto;
    }
    
    .badge {
        font-size: 0.75rem;
    }
    
    /* Bootstrap badge colors matching our theme */
    .badge-success {
        background-color: #28a745;
    }
    
    .badge-warning {
        background-color: #ffc107;
        color: #212529;
    }
    
    .badge-danger {
        background-color: #dc3545;
    }
    
    .badge-info {
        background-color: #0086cd;
    }
    
    .badge-secondary {
        background-color: #6c757d;
    }
    
    .bg-light-primary {
        background-color: rgba(0, 43, 90, 0.1) !important;
    }
</style>
@stop

@section('js')
<script>
function confirmDelete(id) {
    Swal.fire({
        title: '¿Estás seguro?',
        text: "Esta acción no se puede deshacer",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            // Aquí iría la lógica de eliminación
            Swal.fire(
                'Eliminado',
                'El registro ha sido eliminado.',
                'success'
            );
        }
    });
}
</script>
@stop