@extends('adminlte::page')

@section('title', 'Detalle del Contrato')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="h3 mb-1 font-weight-bold">
                <i class="fas fa-file-contract text-primary"></i> Detalle del Contrato
            </h1>
            <p class="text-muted mb-0">Información completa del contrato laboral</p>
        </div>
        <div>
            <a href="{{ route('personnel.contracts.index') }}" class="btn btn-secondary mr-2">
                <i class="fas fa-arrow-left"></i> Volver al Listado
            </a>
            @can('update', $contract)
                <a href="{{ route('personnel.contracts.edit', $contract) }}" class="btn btn-warning mr-2">
                    <i class="fas fa-edit"></i> Editar
                </a>
            @endcan
            @can('delete', $contract)
                <button type="button" class="btn btn-danger" onclick="confirmDelete()">
                    <i class="fas fa-trash"></i> Eliminar
                </button>
            @endcan
        </div>
    </div>
@stop

@section('content')
    <div class="row">
        <div class="col-lg-8">
            <!-- Información del Empleado -->
            <div class="card shadow-sm">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-user"></i> Información del Empleado
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 text-center">
                            @if($contract->employee->photo)
                                <img src="{{ asset('storage/' . $contract->employee->photo) }}" 
                                     alt="Foto de {{ $contract->employee->names }}"
                                     class="img-fluid rounded-circle mb-3"
                                     style="width: 120px; height: 120px; object-fit: cover;">
                            @else
                                <div class="bg-secondary rounded-circle d-flex align-items-center justify-content-center mb-3 mx-auto"
                                     style="width: 120px; height: 120px;">
                                    <i class="fas fa-user text-white" style="font-size: 3rem;"></i>
                                </div>
                            @endif
                        </div>
                        <div class="col-md-9">
                            <table class="table table-borderless">
                                <tr>
                                    <td class="font-weight-bold text-muted" style="width: 30%;">Nombre Completo:</td>
                                    <td>{{ $contract->employee->names }} {{ $contract->employee->lastnames }}</td>
                                </tr>
                                <tr>
                                    <td class="font-weight-bold text-muted">DNI:</td>
                                    <td>{{ $contract->employee->dni }}</td>
                                </tr>
                                <tr>
                                    <td class="font-weight-bold text-muted">Email:</td>
                                    <td>
                                        @if($contract->employee->email)
                                            <a href="mailto:{{ $contract->employee->email }}">{{ $contract->employee->email }}</a>
                                        @else
                                            <span class="text-muted">No registrado</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td class="font-weight-bold text-muted">Teléfono:</td>
                                    <td>
                                        @if($contract->employee->phone)
                                            <a href="tel:{{ $contract->employee->phone }}">{{ $contract->employee->phone }}</a>
                                        @else
                                            <span class="text-muted">No registrado</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td class="font-weight-bold text-muted">Tipo de Empleado:</td>
                                    <td>
                                        @if($contract->employee->employeeType)
                                            <span class="badge" style="background-color: {{ $contract->employee->employeeType->color }}; color: white;">
                                                <i class="{{ $contract->employee->employeeType->icon }}"></i>
                                                {{ $contract->employee->employeeType->name }}
                                            </span>
                                        @else
                                            <span class="text-muted">No asignado</span>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Detalles del Contrato -->
            <div class="card shadow-sm">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-file-contract"></i> Detalles del Contrato
                    </h3>
                    <div class="card-tools">
                        @if($contract->is_active)
                            <span class="badge badge-success">
                                <i class="fas fa-check-circle"></i> Activo
                            </span>
                        @else
                            <span class="badge badge-secondary">
                                <i class="fas fa-pause-circle"></i> Inactivo
                            </span>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td class="font-weight-bold text-muted" style="width: 40%;">Tipo de Contrato:</td>
                                    <td>
                                        @php
                                            $types = \App\Models\Contract::getTypes();
                                            $typeName = $types[$contract->contrato_type] ?? $contract->contrato_type;
                                            $badgeClass = match($contract->contrato_type) {
                                                'permanente' => 'badge-success',
                                                'temporal' => 'badge-warning',
                                                'eventual' => 'badge-info',
                                                'practicas' => 'badge-secondary',
                                                default => 'badge-primary'
                                            };
                                        @endphp
                                        <span class="badge {{ $badgeClass }}">{{ $typeName }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="font-weight-bold text-muted">Cargo/Posición:</td>
                                    <td>
                                        @if($contract->position)
                                            <span class="badge" style="background-color: {{ $contract->position->color }}; color: white;">
                                                <i class="{{ $contract->position->icon }}"></i>
                                                {{ $contract->position->name }}
                                            </span>
                                        @else
                                            <span class="text-muted">No asignado</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td class="font-weight-bold text-muted">Departamento:</td>
                                    <td>
                                        @if($contract->department)
                                            {{ $contract->department->name }}
                                        @else
                                            <span class="text-muted">No asignado</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td class="font-weight-bold text-muted">Salario Mensual:</td>
                                    <td class="font-weight-bold text-success">
                                        S/. {{ number_format($contract->salary, 2) }}
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td class="font-weight-bold text-muted" style="width: 40%;">Fecha de Inicio:</td>
                                    <td>{{ \Carbon\Carbon::parse($contract->start_date)->format('d/m/Y') }}</td>
                                </tr>
                                <tr>
                                    <td class="font-weight-bold text-muted">Fecha de Fin:</td>
                                    <td>
                                        @if($contract->end_date)
                                            {{ \Carbon\Carbon::parse($contract->end_date)->format('d/m/Y') }}
                                            @php
                                                $daysRemaining = \Carbon\Carbon::parse($contract->end_date)->diffInDays(now(), false);
                                            @endphp
                                            @if($daysRemaining > 0)
                                                <br><small class="text-danger">
                                                    <i class="fas fa-exclamation-triangle"></i>
                                                    Expirado hace {{ abs($daysRemaining) }} días
                                                </small>
                                            @elseif($daysRemaining > -30)
                                                <br><small class="text-warning">
                                                    <i class="fas fa-clock"></i>
                                                    Expira en {{ abs($daysRemaining) }} días
                                                </small>
                                            @endif
                                        @else
                                            <span class="text-success">
                                                <i class="fas fa-infinity"></i> Indefinido
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td class="font-weight-bold text-muted">Período de Prueba:</td>
                                    <td>
                                        @if($contract->probation_period_months)
                                            {{ $contract->probation_period_months }} mes(es)
                                            @php
                                                $probationEnd = \Carbon\Carbon::parse($contract->start_date)->addMonths($contract->probation_period_months);
                                                $isProbationActive = now()->lt($probationEnd);
                                            @endphp
                                            @if($isProbationActive)
                                                <br><small class="text-warning">
                                                    <i class="fas fa-hourglass-half"></i>
                                                    En período de prueba hasta {{ $probationEnd ? $probationEnd->format('d/m/Y') : 'N/A' }}
                                                </small>
                                            @else
                                                <br><small class="text-success">
                                                    <i class="fas fa-check"></i>
                                                    Período completado
                                                </small>
                                            @endif
                                        @else
                                            <span class="text-muted">Sin período de prueba</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td class="font-weight-bold text-muted">Vacaciones por Año:</td>
                                    <td>
                                        {{ $contract->vacations_days_per_year ?? 30 }} días
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Estadísticas -->
            <div class="card shadow-sm">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-chart-line"></i> Estadísticas
                    </h3>
                </div>
                <div class="card-body">
                    @php
                        $contractDuration = $contract->end_date 
                            ? \Carbon\Carbon::parse($contract->start_date)->diffInDays(\Carbon\Carbon::parse($contract->end_date))
                            : \Carbon\Carbon::parse($contract->start_date)->diffInDays(now());
                        $workingDays = floor($contractDuration * 5/7); // Aproximado
                    @endphp
                    
                    <div class="info-box bg-light">
                        <span class="info-box-icon bg-primary">
                            <i class="fas fa-calendar-day"></i>
                        </span>
                        <div class="info-box-content">
                            <span class="info-box-text">Duración</span>
                            <span class="info-box-number">{{ $contractDuration }} días</span>
                        </div>
                    </div>

                    <div class="info-box bg-light">
                        <span class="info-box-icon bg-success">
                            <i class="fas fa-briefcase"></i>
                        </span>
                        <div class="info-box-content">
                            <span class="info-box-text">Días Laborales</span>
                            <span class="info-box-number">~{{ $workingDays }}</span>
                        </div>
                    </div>

                    <div class="info-box bg-light">
                        <span class="info-box-icon bg-warning">
                            <i class="fas fa-money-bill-wave"></i>
                        </span>
                        <div class="info-box-content">
                            <span class="info-box-text">Salario Total</span>
                            <span class="info-box-number">
                                S/. {{ number_format($contract->salary * ceil($contractDuration / 30), 2) }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Historial -->
            <div class="card shadow-sm">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-history"></i> Historial
                    </h3>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        <div class="time-label">
                            <span class="bg-primary">{{ \Carbon\Carbon::parse($contract->created_at)->format('M Y') }}</span>
                        </div>
                        <div>
                            <i class="fas fa-plus bg-success"></i>
                            <div class="timeline-item">
                                <span class="time">
                                    <i class="fas fa-clock"></i> {{ \Carbon\Carbon::parse($contract->created_at)->format('d/m/Y H:i') }}
                                </span>
                                <h3 class="timeline-header">Contrato Creado</h3>
                                <div class="timeline-body">
                                    El contrato fue registrado en el sistema.
                                </div>
                            </div>
                        </div>

                        @if($contract->updated_at != $contract->created_at)
                            <div>
                                <i class="fas fa-edit bg-warning"></i>
                                <div class="timeline-item">
                                    <span class="time">
                                        <i class="fas fa-clock"></i> {{ \Carbon\Carbon::parse($contract->updated_at)->format('d/m/Y H:i') }}
                                    </span>
                                    <h3 class="timeline-header">Última Modificación</h3>
                                    <div class="timeline-body">
                                        El contrato fue actualizado.
                                    </div>
                                </div>
                            </div>
                        @endif
                        
                        <div>
                            <i class="fas fa-clock bg-gray"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de confirmación para eliminar -->
    @can('delete', $contract)
        <form id="deleteForm" action="{{ route('personnel.contracts.destroy', $contract) }}" method="POST" style="display: none;">
            @csrf
            @method('DELETE')
        </form>
    @endcan
@stop

@section('css')
    <style>
        .timeline {
            position: relative;
            margin: 0 0 30px 0;
            padding: 0;
            list-style: none;
        }
        
        .timeline:before {
            content: '';
            position: absolute;
            top: 0;
            bottom: 0;
            width: 4px;
            background: #ddd;
            left: 31px;
            margin: 0;
            border-radius: 2px;
        }
        
        .timeline > div {
            position: relative;
        }
        
        .timeline > div > .timeline-item {
            box-shadow: 0 1px 1px rgba(0,0,0,0.1);
            border-radius: 3px;
            margin-top: 0;
            background: #fff;
            color: #444;
            margin-left: 60px;
            margin-right: 15px;
            margin-bottom: 15px;
            padding: 0;
        }
        
        .timeline > div > .fa,
        .timeline > div > .fas,
        .timeline > div > .far,
        .timeline > div > .fab,
        .timeline > div > .fal,
        .timeline > div > .fad,
        .timeline > div > .svg-inline--fa {
            width: 30px;
            height: 30px;
            font-size: 15px;
            line-height: 30px;
            position: absolute;
            color: #666;
            background: #d2d6de;
            border-radius: 50%;
            text-align: center;
            left: 18px;
            top: 0;
        }
        
        .timeline > .time-label > span {
            font-weight: 600;
            color: #fff;
            border-radius: 4px;
            display: inline-block;
            padding: 5px;
        }
        
        .timeline-header {
            margin: 0;
            color: #555;
            border-bottom: 1px solid #f4f4f4;
            padding: 10px;
            font-weight: 600;
            font-size: 16px;
        }
        
        .timeline-body,
        .timeline-footer {
            padding: 10px;
        }
        
        .time {
            color: #999;
            float: right;
            padding: 10px;
            font-size: 12px;
        }
    </style>
@stop

@section('js')
    <script>
        function confirmDelete() {
            Swal.fire({
                title: '¿Estás seguro?',
                text: 'Esta acción no se puede deshacer. El contrato será eliminado permanentemente.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('deleteForm').submit();
                }
            });
        }
    </script>
@stop