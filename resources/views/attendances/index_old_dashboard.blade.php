@extends('adminlte::page')

@section('title', 'Gestión de Asistencias')

@section('content_header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1>
                <i class="fas fa-clock"></i> Gestión de Asistencias
            </h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Asistencias</li>
            </ol>
        </div>
    </div>
@stop

@section('content')
    <!-- Estadísticas -->
    <div class="row mb-3">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ $attendances->where('status', 'present')->count() }}</h3>
                    <p>Presentes Hoy</p>
                </div>
                <div class="icon">
                    <i class="fas fa-user-check"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ $attendances->where('status', 'on_time')->count() }}</h3>
                    <p>A Tiempo</p>
                </div>
                <div class="icon">
                    <i class="fas fa-clock"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ $attendances->where('status', 'late')->count() }}</h3>
                    <p>Llegadas Tarde</p>
                </div>
                <div class="icon">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>{{ $attendances->where('status', 'absent')->count() }}</h3>
                    <p>Ausentes</p>
                </div>
                <div class="icon">
                    <i class="fas fa-user-times"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="row">
        <div class="col-12">
            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-filter"></i> Filtros de Búsqueda
                    </h3>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.personnel.attendances.index') }}">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="search">Buscar Empleado</label>
                                    <input type="text" name="search" id="search" class="form-control" 
                                           placeholder="Nombre, apellido o DNI..." 
                                           value="{{ request('search') }}">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="employee_id">Empleado</label>
                                    <select name="employee_id" id="employee_id" class="form-control">
                                        <option value="">Todos</option>
                                        @foreach($employees as $employee)
                                            <option value="{{ $employee->id }}" 
                                                {{ request('employee_id') == $employee->id ? 'selected' : '' }}>
                                                {{ $employee->names }} {{ $employee->lastnames }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="status">Estado</label>
                                    <select name="status" id="status" class="form-control">
                                        <option value="">Todos</option>
                                        <option value="present" {{ request('status') == 'present' ? 'selected' : '' }}>Presente</option>
                                        <option value="absent" {{ request('status') == 'absent' ? 'selected' : '' }}>Ausente</option>
                                        <option value="late" {{ request('status') == 'late' ? 'selected' : '' }}>Tarde</option>
                                        <option value="on_time" {{ request('status') == 'on_time' ? 'selected' : '' }}>A Tiempo</option>
                                        <option value="half_day" {{ request('status') == 'half_day' ? 'selected' : '' }}>Medio Día</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="date_from">Fecha Desde</label>
                                    <input type="date" name="date_from" id="date_from" class="form-control" 
                                           value="{{ request('date_from') }}">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="date_to">Fecha Hasta</label>
                                    <input type="date" name="date_to" id="date_to" class="form-control" 
                                           value="{{ request('date_to') }}">
                                </div>
                            </div>
                            <div class="col-md-1">
                                <div class="form-group">
                                    <label>&nbsp;</label>
                                    <div>
                                        <button type="submit" class="btn btn-primary btn-block">
                                            <i class="fas fa-search"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla de Asistencias -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-list"></i> Lista de Asistencias
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.personnel.attendances.dashboard') }}" class="btn btn-success btn-sm">
                            <i class="fas fa-tachometer-alt"></i> Dashboard
                        </a>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th width="80">Foto</th>
                                    <th>Empleado</th>
                                    <th>Código</th>
                                    <th>Fecha</th>
                                    <th>Entrada</th>
                                    <th>Salida</th>
                                    <th>Horas Trabajadas</th>
                                    <th>Estado</th>
                                    <th>Observaciones</th>
                                    <th width="120">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($attendances as $attendance)
                                    <tr>
                                        <td class="text-center">
                                            @if($attendance->employee->photo)
                                                <img src="{{ asset('storage/' . $attendance->employee->photo) }}" 
                                                     alt="Foto de {{ $attendance->employee->names }}" 
                                                     class="img-circle elevation-2" 
                                                     style="width: 40px; height: 40px; object-fit: cover;">
                                            @else
                                                <div class="img-circle elevation-2 bg-gray d-flex align-items-center justify-content-center" 
                                                     style="width: 40px; height: 40px;">
                                                    <i class="fas fa-user text-white"></i>
                                                </div>
                                            @endif
                                        </td>
                                        <td>
                                            <strong>{{ $attendance->employee->names }} {{ $attendance->employee->lastnames }}</strong>
                                            <br>
                                            <small class="text-muted">{{ $attendance->employee->department->name ?? 'Sin departamento' }}</small>
                                        </td>
                                        <td>{{ $attendance->employee->employee_code }}</td>
                                        <td>{{ \Carbon\Carbon::parse($attendance->date)->format('d/m/Y') }}</td>
                                        <td>
                                            @if($attendance->check_in)
                                                <span class="badge badge-info">
                                                    {{ \Carbon\Carbon::parse($attendance->check_in)->format('H:i') }}
                                                </span>
                                            @else
                                                <span class="badge badge-secondary">No registrado</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($attendance->check_out)
                                                <span class="badge badge-info">
                                                    {{ \Carbon\Carbon::parse($attendance->check_out)->format('H:i') }}
                                                </span>
                                            @else
                                                <span class="badge badge-warning">Pendiente</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($attendance->hours_worked)
                                                <span class="badge badge-primary">
                                                    {{ number_format($attendance->hours_worked, 2) }} hrs
                                                </span>
                                            @else
                                                <span class="badge badge-secondary">--</span>
                                            @endif
                                        </td>
                                        <td>
                                            @php
                                                $statusColors = [
                                                    'present' => 'success',
                                                    'absent' => 'danger',
                                                    'late' => 'warning',
                                                    'on_time' => 'success',
                                                    'half_day' => 'info'
                                                ];
                                                $statusLabels = [
                                                    'present' => 'Presente',
                                                    'absent' => 'Ausente',
                                                    'late' => 'Tarde',
                                                    'on_time' => 'A Tiempo',
                                                    'half_day' => 'Medio Día'
                                                ];
                                            @endphp
                                            <span class="badge badge-{{ $statusColors[$attendance->status] ?? 'secondary' }}">
                                                {{ $statusLabels[$attendance->status] ?? ucfirst($attendance->status) }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($attendance->notes)
                                                <small title="{{ $attendance->notes }}">
                                                    {{ Str::limit($attendance->notes, 30) }}
                                                </small>
                                            @else
                                                <span class="text-muted">--</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm" role="group">
                                                <button class="btn btn-info btn-sm" title="Ver detalles" onclick="viewAttendanceDetails({{ $attendance->id }})">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <button class="btn btn-warning btn-sm" title="Corregir" onclick="correctAttendance({{ $attendance->id }})">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="10" class="text-center py-4">
                                            <div class="text-muted">
                                                <i class="fas fa-clock fa-3x mb-3"></i>
                                                <h5>No hay registros de asistencia</h5>
                                                <p>No se encontraron registros con los filtros aplicados.</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                
                @if($attendances->hasPages())
                <div class="card-footer">
                    <div class="row align-items-center">
                        <div class="col-sm-6">
                            <div class="dataTables_info">
                                Mostrando {{ $attendances->firstItem() }} a {{ $attendances->lastItem() }} 
                                de {{ $attendances->total() }} registros
                            </div>
                        </div>
                        <div class="col-sm-6">
                            {{ $attendances->appends(request()->query())->links() }}
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Acciones Rápidas -->
    <div class="row mt-3">
        <div class="col-12">
            <div class="card card-outline card-success">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-bolt"></i> Acciones Rápidas
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <a href="{{ route('admin.personnel.attendances.dashboard') }}" class="btn btn-info btn-block">
                                <i class="fas fa-tachometer-alt"></i> Dashboard de Asistencias
                            </a>
                        </div>
                        <div class="col-md-4">
                            <button type="button" class="btn btn-secondary btn-block" onclick="location.reload()">
                                <i class="fas fa-sync"></i> Actualizar Lista
                            </button>
                        </div>
                        <div class="col-md-4">
                            <button type="button" class="btn btn-success btn-block" onclick="window.print()">
                                <i class="fas fa-print"></i> Imprimir Lista
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    <style>
        .small-box {
            border-radius: 10px;
        }
        .card {
            box-shadow: 0 0 1px rgba(0,0,0,.125), 0 1px 3px rgba(0,0,0,.2);
        }
        .table th {
            border-top: none;
            font-weight: 600;
            background-color: #f8f9fa;
        }
        .btn-group-sm > .btn {
            border-radius: 3px;
            margin: 0 1px;
        }
        .img-circle {
            border: 2px solid #fff;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .badge {
            font-size: 0.875em;
        }
        .card-outline.card-primary {
            border-top: 3px solid #007bff;
        }
        .card-outline.card-success {
            border-top: 3px solid #28a745;
        }
    </style>
@stop

@section('js')
    <script>
        $(document).ready(function() {
            // Auto-refresh cada 5 minutos
            setInterval(function() {
                if (!$('input:focus, select:focus, textarea:focus').length) {
                    location.reload();
                }
            }, 300000);
        });

        function viewAttendanceDetails(attendanceId) {
            // Mostrar detalles en un modal simple
            alert('Ver detalles de asistencia ID: ' + attendanceId + '\n\nEsta funcionalidad se puede implementar con un modal o navegando a una página de detalles.');
        }

        function correctAttendance(attendanceId) {
            // Redirigir al sistema de corrección si existe
            if (confirm('¿Desea corregir esta asistencia?')) {
                // Aquí se podría implementar una función de corrección
                alert('Funcionalidad de corrección para asistencia ID: ' + attendanceId);
            }
        }

        // Filtro en tiempo real para la búsqueda
        $('#search').on('input', function() {
            const searchTerm = $(this).val().toLowerCase();
            $('tbody tr').each(function() {
                const rowText = $(this).text().toLowerCase();
                $(this).toggle(rowText.includes(searchTerm));
            });
        });
    </script>
@stop