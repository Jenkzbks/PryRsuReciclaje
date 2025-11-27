@extends('adminlte::page')

@section('title', 'Detalles de Asistencia')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="h3 mb-1 font-weight-bold">
                <i class="fas fa-eye text-primary"></i> Detalles de Asistencia
            </h1>
            <p class="text-muted mb-0">Información detallada del registro de asistencia</p>
        </div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.personnel.attendances.index') }}">Asistencias</a></li>
                <li class="breadcrumb-item active">Detalles</li>
            </ol>
        </nav>
    </div>
@stop

@section('content')
    <div class="row">
        <!-- Información del Empleado -->
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-user"></i> Información del Empleado
                    </h5>
                </div>
                <div class="card-body text-center">
                    <div class="mb-3">
                        <div class="avatar bg-light-primary rounded-circle d-flex align-items-center justify-content-center mx-auto" style="width: 80px; height: 80px;">
                            <i class="fas fa-user fa-2x text-primary"></i>
                        </div>
                    </div>
                    <h5 class="mb-1">{{ $attendance->employee->names }} {{ $attendance->employee->lastnames }}</h5>
                    <p class="text-muted mb-3">DNI: {{ $attendance->employee->dni ?? 'No especificado' }}</p>
                    
                    <div class="row text-left">
                        <div class="col-12">
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item d-flex justify-content-between px-0">
                                    <span><strong>Código:</strong></span>
                                    <span>{{ $attendance->employee->employee_code ?? 'N/A' }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between px-0">
                                    <span><strong>Email:</strong></span>
                                    <span>{{ $attendance->employee->email ?? 'N/A' }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between px-0">
                                    <span><strong>Teléfono:</strong></span>
                                    <span>{{ $attendance->employee->phone ?? 'N/A' }}</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Detalles de la Asistencia -->
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-clock"></i> Registro de Asistencia
                    </h3>
                    <div class="card-tools">
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
                        <span class="badge badge-{{ $color }} badge-lg">
                            <i class="fas fa-{{ $icon }}"></i> {{ $label }}
                        </span>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Información Básica -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="info-box bg-info">
                                <span class="info-box-icon"><i class="fas fa-calendar-day"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Fecha</span>
                                    <span class="info-box-number">{{ \Carbon\Carbon::parse($attendance->date)->format('d/m/Y') }}</span>
                                    <span class="progress-description">{{ \Carbon\Carbon::parse($attendance->date)->translatedFormat('l') }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-box bg-success">
                                <span class="info-box-icon"><i class="fas fa-clock"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Horas Trabajadas</span>
                                    <span class="info-box-number">
                                        {{ $attendance->hours_worked ? number_format($attendance->hours_worked, 1) . 'h' : 'Pendiente' }}
                                    </span>
                                    <span class="progress-description">
                                        @if($attendance->hours_worked >= 8)
                                            Jornada completa
                                        @elseif($attendance->hours_worked > 0)
                                            Jornada parcial
                                        @else
                                            Por calcular
                                        @endif
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Horarios -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card card-outline card-success">
                                <div class="card-header">
                                    <h5 class="card-title">
                                        <i class="fas fa-sign-in-alt text-success"></i> Entrada
                                    </h5>
                                </div>
                                <div class="card-body text-center">
                                    @if($attendance->check_in)
                                        <h3 class="text-success">
                                            {{ \Carbon\Carbon::parse($attendance->check_in)->format('H:i:s') }}
                                        </h3>
                                        <p class="text-muted">
                                            @php
                                                $checkIn = \Carbon\Carbon::parse($attendance->check_in);
                                                $workStart = \Carbon\Carbon::parse($attendance->date . ' 08:00:00');
                                                $diffMinutes = $workStart->diffInMinutes($checkIn, false);
                                            @endphp
                                            @if($diffMinutes > 0)
                                                <span class="badge badge-warning">Tarde por {{ $diffMinutes }} minutos</span>
                                            @else
                                                <span class="badge badge-success">A tiempo</span>
                                            @endif
                                        </p>
                                    @else
                                        <h3 class="text-muted">--:--:--</h3>
                                        <p class="text-muted">No registrado</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card card-outline card-danger">
                                <div class="card-header">
                                    <h5 class="card-title">
                                        <i class="fas fa-sign-out-alt text-danger"></i> Salida
                                    </h5>
                                </div>
                                <div class="card-body text-center">
                                    @if($attendance->check_out)
                                        <h3 class="text-danger">
                                            {{ \Carbon\Carbon::parse($attendance->check_out)->format('H:i:s') }}
                                        </h3>
                                        <p class="text-muted">
                                            <span class="badge badge-info">Registrado</span>
                                        </p>
                                    @else
                                        <h3 class="text-muted">--:--:--</h3>
                                        <p class="text-muted">Pendiente</p>
                                        @if($attendance->check_in)
                                            <button class="btn btn-sm btn-outline-danger" onclick="registerExit()">
                                                <i class="fas fa-sign-out-alt"></i> Registrar Salida
                                            </button>
                                        @endif
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($attendance->notes)
                    <!-- Observaciones -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card card-outline card-warning">
                                <div class="card-header">
                                    <h5 class="card-title">
                                        <i class="fas fa-sticky-note"></i> Observaciones
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <p class="mb-0">{{ $attendance->notes }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Metadatos -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card card-outline card-secondary">
                                <div class="card-header">
                                    <h5 class="card-title">
                                        <i class="fas fa-info-circle"></i> Información del Registro
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <small class="text-muted">
                                                <strong>Creado:</strong> {{ $attendance->created_at?->format('d/m/Y H:i:s') ?? 'N/A' }}
                                            </small>
                                        </div>
                                        <div class="col-md-6">
                                            <small class="text-muted">
                                                <strong>Actualizado:</strong> {{ $attendance->updated_at?->format('d/m/Y H:i:s') ?? 'N/A' }}
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="btn-group" role="group">
                        <a href="{{ route('admin.personnel.attendances.edit', $attendance) }}" class="btn btn-warning">
                            <i class="fas fa-edit"></i> Editar
                        </a>
                        <button class="btn btn-info" onclick="window.print()">
                            <i class="fas fa-print"></i> Imprimir
                        </button>
                    </div>
                    <div class="btn-group float-right" role="group">
                        <a href="{{ route('admin.personnel.attendances.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Volver
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
<style>
    .badge-lg {
        font-size: 1rem;
        padding: 0.5rem 1rem;
    }
    
    .info-box {
        border-radius: 10px;
        box-shadow: 0 0 1px rgba(0,0,0,.125), 0 1px 3px rgba(0,0,0,.2);
    }
    
    .card-outline.card-success {
        border-top: 3px solid #28a745;
    }
    
    .card-outline.card-danger {
        border-top: 3px solid #dc3545;
    }
    
    .card-outline.card-warning {
        border-top: 3px solid #ffc107;
    }
    
    .card-outline.card-secondary {
        border-top: 3px solid #6c757d;
    }
    
    .bg-light-primary {
        background-color: rgba(0, 43, 90, 0.1) !important;
    }
    
    @media print {
        .btn, .card-tools, .breadcrumb, .card-footer {
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
function registerExit() {
    Swal.fire({
        title: 'Registrar Salida',
        text: '¿Desea registrar la salida con la hora actual?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#002b5a',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, registrar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            // Aquí iría la lógica para registrar la salida
            Swal.fire(
                'Registrado',
                'La salida ha sido registrada exitosamente',
                'success'
            ).then(() => {
                location.reload();
            });
        }
    });
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