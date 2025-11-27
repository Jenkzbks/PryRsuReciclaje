@extends('adminlte::page')

@section('title', 'Detalles de Asistencia')

@section('content_header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1>Detalles de Asistencia</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('admin.personnel.attendances.index') }}">Asistencias</a></li>
                <li class="breadcrumb-item active">Detalles</li>
            </ol>
        </div>
    </div>
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-clock"></i> 
                        Asistencia - {{ $attendance->employee->names }} {{ $attendance->employee->lastnames }}
                    </h3>
                    <div class="card-tools">
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
                        <span class="badge badge-{{ $statusColors[$attendance->status] ?? 'secondary' }} badge-lg">
                            {{ $statusLabels[$attendance->status] ?? ucfirst($attendance->status) }}
                        </span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Información del Empleado -->
                        <div class="col-md-4">
                            <div class="card card-outline card-primary">
                                <div class="card-header">
                                    <h5 class="card-title">
                                        <i class="fas fa-user"></i> Información del Empleado
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="text-center mb-3">
                                        @if($attendance->employee->photo)
                                            <img src="{{ asset('storage/' . $attendance->employee->photo) }}" 
                                                 alt="Foto de {{ $attendance->employee->names }}" 
                                                 class="img-circle elevation-2" 
                                                 style="width: 100px; height: 100px; object-fit: cover;">
                                        @else
                                            <div class="img-circle elevation-2 bg-gray d-flex align-items-center justify-content-center" 
                                                 style="width: 100px; height: 100px;">
                                                <i class="fas fa-user fa-2x text-white"></i>
                                            </div>
                                        @endif
                                    </div>
                                    <table class="table table-borderless table-sm">
                                        <tr>
                                            <td><strong>Nombre:</strong></td>
                                            <td>{{ $attendance->employee->names }} {{ $attendance->employee->lastnames }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Código:</strong></td>
                                            <td>{{ $attendance->employee->employee_code }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Departamento:</strong></td>
                                            <td>{{ $attendance->employee->department->name ?? 'No asignado' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Email:</strong></td>
                                            <td>{{ $attendance->employee->email }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Detalles de la Asistencia -->
                        <div class="col-md-8">
                            <div class="card card-outline card-info">
                                <div class="card-header">
                                    <h5 class="card-title">
                                        <i class="fas fa-info-circle"></i> Detalles de la Asistencia
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <table class="table table-borderless">
                                                <tr>
                                                    <td><strong>Fecha:</strong></td>
                                                    <td>{{ \Carbon\Carbon::parse($attendance->date)->format('d/m/Y') }}</td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Día de la Semana:</strong></td>
                                                    <td>{{ \Carbon\Carbon::parse($attendance->date)->locale('es')->dayName }}</td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Hora de Entrada:</strong></td>
                                                    <td>
                                                        @if($attendance->check_in)
                                                            <span class="badge badge-info badge-lg">
                                                                {{ \Carbon\Carbon::parse($attendance->check_in)->format('H:i:s') }}
                                                            </span>
                                                        @else
                                                            <span class="badge badge-secondary">No registrado</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Hora de Salida:</strong></td>
                                                    <td>
                                                        @if($attendance->check_out)
                                                            <span class="badge badge-info badge-lg">
                                                                {{ \Carbon\Carbon::parse($attendance->check_out)->format('H:i:s') }}
                                                            </span>
                                                        @else
                                                            <span class="badge badge-warning">Pendiente</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Horas Trabajadas:</strong></td>
                                                    <td>
                                                        @if($attendance->hours_worked)
                                                            <span class="badge badge-primary badge-lg">
                                                                {{ number_format($attendance->hours_worked, 2) }} horas
                                                            </span>
                                                        @else
                                                            <span class="badge badge-secondary">Pendiente</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                        <div class="col-md-6">
                                            <table class="table table-borderless">
                                                <tr>
                                                    <td><strong>Estado:</strong></td>
                                                    <td>
                                                        <span class="badge badge-{{ $statusColors[$attendance->status] ?? 'secondary' }} badge-lg">
                                                            <i class="fas fa-circle"></i>
                                                            {{ $statusLabels[$attendance->status] ?? ucfirst($attendance->status) }}
                                                        </span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Llegada:</strong></td>
                                                    <td>
                                                        @if($attendance->check_in)
                                                            @php
                                                                $checkIn = \Carbon\Carbon::parse($attendance->check_in);
                                                                $workStart = \Carbon\Carbon::parse($attendance->date . ' 08:00:00');
                                                                $isLate = $checkIn->gt($workStart);
                                                            @endphp
                                                            @if($isLate)
                                                                <span class="badge badge-warning">
                                                                    Tarde por {{ $workStart->diffInMinutes($checkIn) }} minutos
                                                                </span>
                                                            @else
                                                                <span class="badge badge-success">A tiempo</span>
                                                            @endif
                                                        @else
                                                            <span class="badge badge-secondary">--</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Registrado:</strong></td>
                                                    <td>{{ $attendance->created_at?->format('d/m/Y H:i:s') ?? 'N/A' }}</td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Actualizado:</strong></td>
                                                    <td>{{ $attendance->updated_at?->format('d/m/Y H:i:s') ?? 'N/A' }}</td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>

                                    @if($attendance->notes)
                                    <div class="row mt-3">
                                        <div class="col-12">
                                            <h6><strong>Observaciones:</strong></h6>
                                            <div class="border rounded p-3 bg-light">
                                                {{ $attendance->notes }}
                                            </div>
                                        </div>
                                    </div>
                                    @endif

                                    <!-- Estadísticas adicionales -->
                                    <div class="row mt-3">
                                        <div class="col-12">
                                            <h6><strong>Estadísticas del Día:</strong></h6>
                                            <div class="row">
                                                @if($attendance->hours_worked)
                                                <div class="col-md-3">
                                                    <div class="info-box bg-info">
                                                        <span class="info-box-icon"><i class="fas fa-clock"></i></span>
                                                        <div class="info-box-content">
                                                            <span class="info-box-text">Horas Trabajadas</span>
                                                            <span class="info-box-number">{{ number_format($attendance->hours_worked, 2) }}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                @endif

                                                @if($attendance->check_in && $attendance->check_out)
                                                <div class="col-md-3">
                                                    <div class="info-box bg-success">
                                                        <span class="info-box-icon"><i class="fas fa-check-circle"></i></span>
                                                        <div class="info-box-content">
                                                            <span class="info-box-text">Día Completo</span>
                                                            <span class="info-box-number">Sí</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                @endif

                                                @if($attendance->hours_worked >= 8)
                                                <div class="col-md-3">
                                                    <div class="info-box bg-warning">
                                                        <span class="info-box-icon"><i class="fas fa-medal"></i></span>
                                                        <div class="info-box-content">
                                                            <span class="info-box-text">Jornada Completa</span>
                                                            <span class="info-box-number">✓</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Acciones -->
                    <div class="row mt-3">
                        <div class="col-12">
                            <div class="card card-outline card-secondary">
                                <div class="card-header">
                                    <h5 class="card-title">
                                        <i class="fas fa-cogs"></i> Acciones
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('admin.personnel.attendances.edit', $attendance) }}" class="btn btn-warning">
                                            <i class="fas fa-edit"></i> Editar
                                        </a>
                                        
                                        @if(!$attendance->check_out)
                                            <form action="{{ route('admin.personnel.attendances.update', $attendance) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="check_out" value="{{ now()->format('H:i:s') }}">
                                                <button type="submit" class="btn btn-success" onclick="return confirm('¿Registrar salida con la hora actual?')">
                                                    <i class="fas fa-sign-out-alt"></i> Registrar Salida
                                                </button>
                                            </form>
                                        @endif

                                        <form action="{{ route('admin.personnel.attendances.destroy', $attendance) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger" onclick="return confirm('¿Está seguro de eliminar este registro?')">
                                                <i class="fas fa-trash"></i> Eliminar
                                            </button>
                                        </form>
                                    </div>

                                    <div class="btn-group float-right" role="group">
                                        <a href="{{ route('admin.personnel.attendances.index') }}" class="btn btn-secondary">
                                            <i class="fas fa-arrow-left"></i> Volver a la Lista
                                        </a>
                                        
                                        <button type="button" class="btn btn-info" onclick="window.print()">
                                            <i class="fas fa-print"></i> Imprimir
                                        </button>

                                        <button type="button" class="btn btn-primary" onclick="generateReport()">
                                            <i class="fas fa-file-pdf"></i> Generar Reporte
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    <style>
        .card-header {
            background: linear-gradient(90deg, #17a2b8, #117a8b);
            color: white;
        }
        .card-outline.card-primary {
            border-top: 3px solid #007bff;
        }
        .card-outline.card-info {
            border-top: 3px solid #17a2b8;
        }
        .card-outline.card-secondary {
            border-top: 3px solid #6c757d;
        }
        .table-borderless td {
            border: none;
            padding: 0.25rem 0.5rem;
        }
        .badge-lg {
            font-size: 1rem;
            padding: 0.5rem 1rem;
        }
        .info-box {
            border-radius: 10px;
            margin-bottom: 10px;
        }
        .img-circle {
            border: 3px solid #fff;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        @media print {
            .btn, .card-tools, .breadcrumb {
                display: none !important;
            }
            .card {
                border: none !important;
                box-shadow: none !important;
            }
        }
    </style>
@stop

@section('js')
    <script>
        function generateReport() {
            const attendanceId = {{ $attendance->id }};
            window.open(`{{ route('admin.personnel.attendances.index') }}/${attendanceId}/report`, '_blank');
        }

        $(document).ready(function() {
            // Auto-refresh si la asistencia no está completa
            @if(!$attendance->check_out)
                setInterval(function() {
                    location.reload();
                }, 60000); // Recargar cada minuto
            @endif
        });
    </script>
@stop