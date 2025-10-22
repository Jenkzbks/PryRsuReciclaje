@extends('adminlte::page')

@section('title', 'Gestión de Personal')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="h3 mb-1 font-weight-bold">
                <i class="fas fa-users text-primary"></i> Gestión de Personal
            </h1>
            <p class="text-muted mb-0">Panel de control integral para la administración del recurso humano</p>
        </div>
        <div class="btn-group">
            <a href="{{ route('admin.personnel.employees.create') }}" class="btn btn-primary">
                <i class="fas fa-user-plus"></i> Nuevo Empleado
            </a>
            <a href="{{ route('admin.personnel.attendances.dashboard') }}" class="btn btn-success">
                <i class="fas fa-clock"></i> Asistencias Hoy
            </a>
            <a href="{{ route('admin.personnel.vacations.index', ['status' => 'pending']) }}" class="btn btn-warning">
                <i class="fas fa-calendar-check"></i> Vacaciones Pendientes
            </a>
        </div>
    </div>
@stop

@section('content')
    <!-- Estadísticas Principales -->
    <div class="row" id="dashboard-stats">
        <div class="col-lg-3 col-md-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3 id="total-employees">-</h3>
                    <p>Total Empleados</p>
                </div>
                <div class="icon">
                    <i class="fas fa-users"></i>
                </div>
                <a href="{{ route('admin.personnel.employees.index') }}" class="small-box-footer">
                    Ver todos <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3 id="active-employees">-</h3>
                    <p>Empleados Activos</p>
                </div>
                <div class="icon">
                    <i class="fas fa-user-check"></i>
                </div>
                <a href="{{ route('admin.personnel.employees.index', ['status' => 'active']) }}" class="small-box-footer">
                    Ver activos <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3 id="pending-vacations">-</h3>
                    <p>Vacaciones Pendientes</p>
                </div>
                <div class="icon">
                    <i class="fas fa-calendar-alt"></i>
                </div>
                <a href="{{ route('admin.personnel.vacations.index', ['status' => 'pending']) }}" class="small-box-footer">
                    Revisar <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3 id="expiring-contracts">-</h3>
                    <p>Contratos por Vencer</p>
                </div>
                <div class="icon">
                    <i class="fas fa-file-contract"></i>
                </div>
                <a href="{{ route('admin.personnel.contracts.index', ['expiring_soon' => 1]) }}" class="small-box-footer">
                    Ver contratos <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Asistencias del Día -->
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-clock"></i> Asistencias del Día
                    </h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="refresh" onclick="loadAttendanceToday()">
                            <i class="fas fa-sync-alt"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row text-center" id="attendance-today">
                        <div class="col-6">
                            <div class="description-block border-right">
                                <span class="description-percentage text-success">
                                    <i class="fas fa-sign-in-alt"></i>
                                </span>
                                <h5 class="description-header" id="entries-today">-</h5>
                                <span class="description-text">ENTRADAS</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="description-block">
                                <span class="description-percentage text-danger">
                                    <i class="fas fa-sign-out-alt"></i>
                                </span>
                                <h5 class="description-header" id="exits-today">-</h5>
                                <span class="description-text">SALIDAS</span>
                            </div>
                        </div>
                    </div>
                    <div class="progress mt-3">
                        <div class="progress-bar bg-success" style="width: 0%" id="attendance-progress"></div>
                    </div>
                    <span class="progress-description" id="attendance-description">
                        Cargando estadísticas...
                    </span>
                </div>
                <div class="card-footer">
                    <a href="{{ route('admin.personnel.attendances.dashboard') }}" class="btn btn-primary btn-block">
                        <i class="fas fa-tachometer-alt"></i> Ver Dashboard Completo
                    </a>
                </div>
            </div>
        </div>

        <!-- Contratos y Nómina -->
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-file-contract"></i> Resumen de Contratos
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row" id="contracts-summary">
                        <div class="col-6">
                            <div class="description-block border-right">
                                <h5 class="description-header text-success" id="active-contracts">-</h5>
                                <span class="description-text">ACTIVOS</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="description-block">
                                <h5 class="description-header text-warning" id="expiring-contracts-detail">-</h5>
                                <span class="description-text">POR VENCER (30 días)</span>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="text-center">
                        <h4 class="text-bold" id="total-payroll">S/ 0.00</h4>
                        <p class="text-muted">Nómina Total Mensual</p>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="row">
                        <div class="col-6">
                            <a href="{{ route('admin.personnel.contracts.index') }}" class="btn btn-outline-primary btn-block">
                                <i class="fas fa-list"></i> Ver Contratos
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="{{ route('admin.personnel.employees.export') }}" class="btn btn-outline-success btn-block">
                                <i class="fas fa-file-excel"></i> Exportar Personal
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Próximos Eventos -->
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-calendar"></i> Próximos Eventos y Alertas
                    </h3>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped" id="upcoming-events">
                            <thead>
                                <tr>
                                    <th>Tipo</th>
                                    <th>Empleado</th>
                                    <th>Fecha</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td colspan="5" class="text-center">
                                        <i class="fas fa-spinner fa-spin"></i> Cargando eventos...
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Accesos Rápidos -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-bolt"></i> Accesos Rápidos
                    </h3>
                </div>
                <div class="card-body">
                    <div class="list-group">
                        <a href="{{ route('admin.personnel.employees.create') }}" class="list-group-item list-group-item-action">
                            <i class="fas fa-user-plus text-primary"></i> Registrar Empleado
                        </a>
                        <a href="{{ route('admin.personnel.contracts.create') }}" class="list-group-item list-group-item-action">
                            <i class="fas fa-file-signature text-success"></i> Nuevo Contrato
                        </a>
                        <a href="{{ route('admin.personnel.vacations.create') }}" class="list-group-item list-group-item-action">
                            <i class="fas fa-calendar-plus text-warning"></i> Solicitar Vacaciones
                        </a>
                        <a href="{{ route('attendance-kiosk.index') }}" class="list-group-item list-group-item-action" target="_blank">
                            <i class="fas fa-desktop text-info"></i> Kiosco de Asistencias
                        </a>
                        <a href="#" class="list-group-item list-group-item-action" onclick="return false;" data-toggle="modal" data-target="#clockInModal">
                            <i class="fas fa-clock text-dark"></i> Marcar Asistencia Manual
                        </a>
                    </div>
                </div>
            </div>

            <!-- Estadísticas Rápidas por Tipo -->
            <div class="card mt-3">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-chart-pie"></i> Empleados por Tipo
                    </h3>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush" id="employee-types-stats">
                        <div class="list-group-item text-center">
                            <i class="fas fa-spinner fa-spin"></i> Cargando...
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para Marcar Asistencia Manual -->
    <div class="modal fade" id="clockInModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">
                        <i class="fas fa-clock"></i> Marcar Asistencia Manual
                    </h4>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <form id="clockForm">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="employee_select">Empleado</label>
                            <select class="form-control select2" id="employee_select" name="employee_id" required>
                                <option value="">Seleccionar empleado...</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="clock_type">Tipo de Registro</label>
                            <select class="form-control" id="clock_type" name="type" required>
                                <option value="entry">Entrada</option>
                                <option value="exit">Salida</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="clock_datetime">Fecha y Hora</label>
                            <input type="datetime-local" class="form-control" id="clock_datetime" name="datetime" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Registrar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop

@section('css')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap4-theme@1.0.0/dist/select2-bootstrap4.min.css">
    <style>
        .small-box .icon {
            top: -10px;
            right: 10px;
        }
        .description-block {
            margin: 0;
            padding: 10px 0;
        }
        .progress {
            height: 10px;
        }
        .list-group-item-action {
            border: none;
            border-bottom: 1px solid #e9ecef;
        }
        .list-group-item-action:last-child {
            border-bottom: none;
        }
        .card-tools .btn-tool {
            color: #6c757d;
        }
        .description-percentage {
            font-size: 1.2em;
        }
    </style>
@stop

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            // Inicializar Select2
            $('.select2').select2({
                theme: 'bootstrap4',
                placeholder: 'Seleccionar empleado...',
                allowClear: true
            });

            // Establecer datetime actual
            const now = new Date();
            now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
            $('#clock_datetime').val(now.toISOString().slice(0, 16));

            // Cargar datos iniciales
            loadDashboardStats();
            loadAttendanceToday();
            loadUpcomingEvents();
            loadEmployeeTypesStats();
            loadEmployeesForSelect();

            // Auto-refresh cada 5 minutos
            setInterval(function() {
                loadDashboardStats();
                loadAttendanceToday();
            }, 300000);

            // Form submit para marcar asistencia
            $('#clockForm').on('submit', function(e) {
                e.preventDefault();
                clockEmployee();
            });
        });

        function loadDashboardStats() {
            // Cargar estadísticas directamente desde el servidor
            $('#total-employees').text('{{ \App\Models\Employee::count() }}');
            $('#active-employees').text('{{ \App\Models\Employee::where("status", 1)->count() }}');
            $('#pending-vacations').text('0');
            $('#expiring-contracts').text('0');
            $('#active-contracts').text('0');
            $('#expiring-contracts-detail').text('0');
            $('#total-payroll').text('S/ 0.00');
        }

        function loadAttendanceToday() {
            // Funcionalidad de asistencias simplificada por ahora
            const totalEmployees = {{ \App\Models\Employee::where('status', 1)->count() }};
            $('#entries-today').text('0');
            $('#exits-today').text('0');
            $('#attendance-progress').css('width', '0%');
            $('#attendance-description').text('Funcionalidad de asistencias en desarrollo');
        }
                .fail(function() {
                    $('#attendance-description').text('Error al cargar datos de asistencia');
                });
        }

        function loadUpcomingEvents() {
            // Funcionalidad simplificada por ahora
            const html = '<tr><td colspan="5" class="text-center text-muted">Funcionalidad en desarrollo</td></tr>';
            $('#upcoming-events tbody').html(html);
        }
        }

        function loadEmployeeTypesStats() {
            // Cargar datos directamente desde el servidor por ahora
            const html = `
                @foreach(\App\Models\EmployeeType::withCount(['employees' => function($query) { $query->where('status', 1); }])->get() as $type)
                <div class="list-group-item d-flex justify-content-between align-items-center">
                    <div>
                        <strong>{{ $type->name }}</strong>
                        @if($type->is_protected)
                            <i class="fas fa-shield-alt text-warning ml-1" title="Protegido"></i>
                        @endif
                        <br>
                        <small class="text-muted">{{ $type->employees_count }} empleados</small>
                    </div>
                    <div class="text-right">
                        <span class="badge badge-primary badge-pill">{{ $type->employees_count }}</span>
                    </div>
                </div>
                @endforeach
            `;
            $('#employee-types-stats').html(html);
        }
        }

        function loadEmployeesForSelect() {
            // Funcionalidad simplificada - cargar empleados directamente
            const select = $('#employee_select');
            select.empty().append('<option value="">Seleccionar empleado...</option>');
            
            @foreach(\App\Models\Employee::where('status', 1)->get() as $employee)
                select.append('<option value="{{ $employee->id }}">{{ $employee->names }} {{ $employee->lastnames }}</option>');
            @endforeach
        }

        function clockEmployee() {
            const formData = {
                employee_id: $('#employee_select').val(),
                datetime: $('#clock_datetime').val(),
                _token: '{{ csrf_token() }}'
            };

            const type = $('#clock_type').val();
            // Funcionalidad de reloj de asistencias pendiente de implementar
            const url = '#';

            // Por ahora solo mostramos un mensaje
            alert('Funcionalidad de reloj de asistencias pendiente de implementar');
            $('#clockInModal').modal('hide');
                        
                        Swal.fire({
                            icon: 'success',
                            title: '¡Éxito!',
                            text: response.message,
                            timer: 3000
                        });

                        // Recargar datos
                        loadAttendanceToday();
                    }
                })
                .fail(function(xhr) {
                    const error = xhr.responseJSON;
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: error.message || 'Error al registrar asistencia'
                    });
                });
        }
    </script>
@stop
