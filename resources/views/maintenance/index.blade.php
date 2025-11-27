@extends('adminlte::page')

@section('title', 'Gestión de Mantenimientos')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1><i class="fas fa-tools"></i> Gestión de Mantenimientos</h1>
        <button type="button" class="btn btn-success" id="btnNewMaintenance">
            <i class="fas fa-plus"></i> Nuevo Mantenimiento
        </button>
    </div>
@stop

@section('content')
    <!-- Estadísticas -->
    <div class="row mb-4">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3 id="stat-total">0</h3>
                    <p>Total Mantenimientos</p>
                </div>
                <div class="icon">
                    <i class="fas fa-tools"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3 id="stat-active">0</h3>
                    <p>Activos</p>
                </div>
                <div class="icon">
                    <i class="fas fa-play"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3 id="stat-upcoming">0</h3>
                    <p>Próximos</p>
                </div>
                <div class="icon">
                    <i class="fas fa-clock"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-secondary">
                <div class="inner">
                    <h3 id="stat-schedules">0</h3>
                    <p>Horarios Programados</p>
                </div>
                <div class="icon">
                    <i class="fas fa-calendar"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros y búsqueda -->
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Buscar por nombre:</label>
                        <input type="text" class="form-control" id="searchInput" placeholder="Escriba el nombre del mantenimiento">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Estado:</label>
                        <select class="form-control" id="statusFilter">
                            <option value="">Todos</option>
                            <option value="active">Activos</option>
                            <option value="upcoming">Próximos</option>
                            <option value="finished">Finalizados</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>&nbsp;</label>
                        <div>
                            <button type="button" class="btn btn-primary" id="btnFilter">
                                <i class="fas fa-search"></i> Filtrar
                            </button>
                            <button type="button" class="btn btn-secondary" id="btnClearFilter">
                                <i class="fas fa-times"></i> Limpiar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla de mantenimientos -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Lista de Mantenimientos</h3>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover" id="maintenanceTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Fecha Inicio</th>
                            <th>Fecha Fin</th>
                            <th>Duración</th>
                            <th>Estado</th>
                            <th>Horarios</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="maintenanceTableBody">
                        <!-- Los datos se cargan dinámicamente -->
                    </tbody>
                </table>
            </div>

            <!-- Paginación -->
            <div id="paginationContainer" class="d-flex justify-content-center mt-3">
                <!-- La paginación se carga dinámicamente -->
            </div>
        </div>
    </div>

    <!-- Modal para crear/editar mantenimiento -->
    <div class="modal fade" id="maintenanceModal" tabindex="-1" role="dialog" aria-labelledby="maintenanceModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="maintenanceModalLabel">Nuevo Mantenimiento</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="maintenanceForm">
                    @csrf
                    <div class="modal-body">
                        <input type="hidden" id="maintenanceId" name="id">
                        
                        <div class="form-group">
                            <label for="name">Nombre del Mantenimiento <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="name" name="name" required>
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="start_date">Fecha de Inicio <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control" id="start_date" name="start_date" required>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="end_date">Fecha de Fin <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control" id="end_date" name="end_date" required>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-warning" id="overlapWarning" style="display: none;">
                            <i class="fas fa-exclamation-triangle"></i>
                            Las fechas seleccionadas se solapan con otro mantenimiento existente.
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary" id="btnSaveMaintenance">
                            <i class="fas fa-save"></i> Guardar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal para ver detalles del mantenimiento -->
    <div class="modal fade" id="maintenanceDetailModal" tabindex="-1" role="dialog" aria-labelledby="maintenanceDetailModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="maintenanceDetailModalLabel">Detalles del Mantenimiento</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="maintenanceDetails">
                        <!-- Los detalles se cargan dinámicamente -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para Horarios -->
    <div class="modal fade" id="scheduleModal" tabindex="-1" role="dialog" aria-labelledby="scheduleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="scheduleModalLabel">Nuevo Horario</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="scheduleForm" method="POST">
                        @csrf
                        <input type="hidden" id="scheduleId" name="scheduleId" value="">
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="schedule_maintenance_id">Mantenimiento <span class="text-danger">*</span></label>
                                    <select class="form-control" id="schedule_maintenance_id" name="maintenance_id" required>
                                        <option value="">Seleccionar mantenimiento</option>
                                    </select>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="schedule_vehicle_id">Vehículo <span class="text-danger">*</span></label>
                                    <select class="form-control" id="schedule_vehicle_id" name="vehicle_id" required>
                                        <option value="">Seleccionar vehículo</option>
                                    </select>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="schedule_driver_id">Responsable <span class="text-danger">*</span></label>
                                    <select class="form-control" id="schedule_driver_id" name="driver_id" required>
                                        <option value="">Seleccionar responsable</option>
                                    </select>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="schedule_maintenance_type">Tipo de Mantenimiento <span class="text-danger">*</span></label>
                                    <select class="form-control" id="schedule_maintenance_type" name="maintenance_type" required>
                                        <option value="">Seleccionar tipo</option>
                                        <option value="preventive">Preventivo</option>
                                        <option value="corrective">Correctivo</option>
                                        <option value="predictive">Predictivo</option>
                                    </select>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="schedule_day_of_week">Día de la Semana <span class="text-danger">*</span></label>
                                    <select class="form-control" id="schedule_day_of_week" name="day_of_week" required>
                                        <option value="">Seleccionar día</option>
                                        <option value="1">Lunes</option>
                                        <option value="2">Martes</option>
                                        <option value="3">Miércoles</option>
                                        <option value="4">Jueves</option>
                                        <option value="5">Viernes</option>
                                        <option value="6">Sábado</option>
                                        <option value="0">Domingo</option>
                                    </select>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="schedule_start_time">Hora de Inicio <span class="text-danger">*</span></label>
                                    <input type="time" class="form-control" id="schedule_start_time" name="start_time" required>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="schedule_end_time">Hora de Fin <span class="text-danger">*</span></label>
                                    <input type="time" class="form-control" id="schedule_end_time" name="end_time" required>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="schedule_recurrence_weeks">Recurrencia (semanas)</label>
                                    <input type="number" class="form-control" id="schedule_recurrence_weeks" name="recurrence_weeks" min="1" max="52" value="1">
                                    <small class="form-text text-muted">Cada cuántas semanas se repite (1 = semanal)</small>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="schedule_description">Descripción</label>
                            <textarea class="form-control" id="schedule_description" name="description" rows="3"></textarea>
                            <div class="invalid-feedback"></div>
                        </div>
                        
                        <div id="scheduleOverlapWarning" class="alert alert-warning" style="display: none;">
                            <i class="fas fa-exclamation-triangle"></i>
                            <strong>¡Advertencia!</strong> Existe una superposición de horarios.
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" onclick="saveSchedule()">Guardar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para Registros de Actividades -->
    <div class="modal fade" id="recordModal" tabindex="-1" role="dialog" aria-labelledby="recordModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="recordModalLabel">Nueva Actividad</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="recordForm" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" id="recordId" name="recordId" value="">
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="record_maintenance_id">Mantenimiento <span class="text-danger">*</span></label>
                                    <select class="form-control" id="record_maintenance_id" name="maintenance_id" required>
                                        <option value="">Seleccionar mantenimiento</option>
                                    </select>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="record_employee_id">Empleado Responsable <span class="text-danger">*</span></label>
                                    <select class="form-control" id="record_employee_id" name="employee_id" required>
                                        <option value="">Seleccionar empleado</option>
                                    </select>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="record_activity_date">Fecha de Actividad <span class="text-danger">*</span></label>
                                    <input type="datetime-local" class="form-control" id="record_activity_date" name="activity_date" required>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="record_activity_image">Imagen (opcional)</label>
                                    <input type="file" class="form-control-file" id="record_activity_image" name="activity_image" accept="image/*">
                                    <small class="form-text text-muted">Formatos permitidos: JPG, PNG, GIF. Máximo 2MB.</small>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="record_activity_description">Descripción de la Actividad <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="record_activity_description" name="activity_description" rows="4" required></textarea>
                            <div class="invalid-feedback"></div>
                        </div>
                        
                        <div id="current_image_preview" class="form-group" style="display: none;">
                            <label>Imagen Actual:</label>
                            <div>
                                <img id="current_image" src="" class="img-thumbnail" style="max-width: 200px;">
                                <button type="button" class="btn btn-sm btn-danger ml-2" onclick="removeCurrentImage()">
                                    <i class="fas fa-trash"></i> Eliminar imagen
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" onclick="saveRecord()">Guardar</button>
                </div>
            </div>
        </div>
    </div>

@stop

@section('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <style>
        .table th, .table td {
            vertical-align: middle;
        }
        .badge {
            font-size: 0.9em;
        }
        .maintenance-actions .btn {
            margin: 0 2px;
        }
        .modal-xl {
            max-width: 90%;
        }
        .schedule-card {
            border-left: 4px solid #007bff;
            margin-bottom: 15px;
        }
        .activity-item {
            border-bottom: 1px solid #eee;
            padding: 10px 0;
        }
        .activity-item:last-child {
            border-bottom: none;
        }
    </style>
@stop

@section('js')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script>
        // Configuración de Toastr
        toastr.options = {
            "closeButton": true,
            "progressBar": true,
            "positionClass": "toast-top-right",
            "timeOut": "3000"
        };

        $(document).ready(function() {
            // Inicialización
            loadMaintenances();
            loadStatistics();

            // Event listeners
            $('#btnNewMaintenance').click(function() {
                showMaintenanceModal();
            });

            $('#maintenanceForm').submit(function(e) {
                e.preventDefault();
                saveMaintenance();
            });

            $('#btnFilter').click(function() {
                loadMaintenances();
            });

            $('#btnClearFilter').click(function() {
                $('#searchInput').val('');
                $('#statusFilter').val('');
                loadMaintenances();
            });

            $('#searchInput').keyup(function(e) {
                if (e.keyCode === 13) {
                    loadMaintenances();
                }
            });

            // Validación de fechas en tiempo real
            $('#start_date, #end_date').change(function() {
                validateDateOverlap();
            });
        });

        // Funciones principales
        function loadMaintenances(page = 1) {
            const search = $('#searchInput').val();
            const status = $('#statusFilter').val();

            $.ajax({
                url: '{{ route("admin.maintenance.index") }}',
                type: 'GET',
                data: {
                    page: page,
                    search: search,
                    status: status
                },
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                success: function(response) {
                    console.log('Response:', response); // Debug log
                    if (response.success) {
                        renderMaintenanceTable(response.data);
                        renderPagination(response.pagination, 'loadMaintenances');
                    }
                },
                error: function(xhr) {
                    console.error('Error loading maintenances:', xhr);
                    toastr.error('Error al cargar los mantenimientos');
                }
            });
        }

        function loadStatistics() {
            $.ajax({
                url: '{{ route("admin.maintenance.statistics") }}',
                type: 'GET',
                success: function(response) {
                    if (response.success) {
                        $('#stat-total').text(response.data.total);
                        $('#stat-active').text(response.data.active);
                        $('#stat-upcoming').text(response.data.upcoming);
                        $('#stat-schedules').text(response.data.total_schedules);
                    }
                }
            });
        }

        function renderMaintenanceTable(maintenances) {
            console.log('Rendering maintenances:', maintenances); // Debug log
            const tbody = $('#maintenanceTableBody');
            tbody.empty();

            if (maintenances.length === 0) {
                tbody.append(`
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">
                            <i class="fas fa-tools fa-3x mb-3"></i><br>
                            No hay mantenimientos registrados
                        </td>
                    </tr>
                `);
                return;
            }

            maintenances.forEach((maintenance, index) => {
                console.log(`Maintenance ${index + 1}:`, maintenance); // Debug para cada mantenimiento
                console.log(`Duration: ${maintenance.duration}, Status: ${maintenance.status}, Status text: ${maintenance.status_text}`);
                
                const statusBadge = getStatusBadge(maintenance.status, maintenance.status_text);
                
                // Validar que duration no sea undefined
                const durationText = maintenance.duration !== undefined ? `${maintenance.duration} días` : 'N/A';
                
                tbody.append(`
                    <tr>
                        <td>${maintenance.id}</td>
                        <td>
                            <strong>${maintenance.name}</strong>
                        </td>
                        <td>${formatDate(maintenance.start_date)}</td>
                        <td>${formatDate(maintenance.end_date)}</td>
                        <td>
                            <span class="badge badge-info">${durationText}</span>
                        </td>
                        <td>${statusBadge}</td>
                        <td>
                            <button type="button" class="btn btn-sm btn-warning" onclick="assignScheduleToMaintenance(${maintenance.id})" title="Gestionar Horarios">
                                <i class="fas fa-calendar-plus"></i>
                            </button>
                        </td>
                        <td class="maintenance-actions">
                            <button type="button" class="btn btn-sm btn-info" onclick="showMaintenanceDetail(${maintenance.id})" title="Ver detalles">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-primary" onclick="editMaintenance(${maintenance.id})" title="Editar">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-danger" onclick="deleteMaintenance(${maintenance.id})" title="Eliminar">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                `);
            });
        }

        function getStatusBadge(status, statusText) {
            // Debug
            console.log('Status badge - status:', status, 'statusText:', statusText);
            
            const badges = {
                'active': 'badge-success',
                'upcoming': 'badge-warning',
                'finished': 'badge-secondary'
            };
            const badgeClass = badges[status] || 'badge-secondary';
            const finalText = statusText || status || 'Desconocido';
            
            return `<span class="badge ${badgeClass}">${finalText}</span>`;
        }

        function showMaintenanceModal(maintenance = null) {
            const modal = $('#maintenanceModal');
            const form = $('#maintenanceForm')[0];
            form.reset();
            
            // Limpiar errores previos
            $('.form-control').removeClass('is-invalid');
            $('.invalid-feedback').text('');
            $('#overlapWarning').hide();

            if (maintenance) {
                $('#maintenanceModalLabel').text('Editar Mantenimiento');
                $('#maintenanceId').val(maintenance.id);
                $('#name').val(maintenance.name);
                
                // Formatear las fechas correctamente para input type="date" (YYYY-MM-DD)
                // Extraer solo la fecha sin hora
                let startDate = maintenance.start_date;
                let endDate = maintenance.end_date;
                
                // Si la fecha viene con hora, extraer solo la parte de la fecha
                if (startDate && startDate.includes(' ')) {
                    startDate = startDate.split(' ')[0];
                } else if (startDate && startDate.includes('T')) {
                    startDate = startDate.split('T')[0];
                }
                
                if (endDate && endDate.includes(' ')) {
                    endDate = endDate.split(' ')[0];
                } else if (endDate && endDate.includes('T')) {
                    endDate = endDate.split('T')[0];
                }
                
                console.log('Setting dates - Start:', startDate, 'End:', endDate); // Debug
                
                $('#start_date').val(startDate || '');
                $('#end_date').val(endDate || '');
            } else {
                $('#maintenanceModalLabel').text('Nuevo Mantenimiento');
                $('#maintenanceId').val('');
                // Establecer fecha mínima como hoy
                $('#start_date').attr('min', new Date().toISOString().split('T')[0]);
            }

            modal.modal('show');
        }

        function saveMaintenance() {
            const maintenanceId = $('#maintenanceId').val();
            const url = maintenanceId 
                ? `{{ route("admin.maintenance.index") }}/${maintenanceId}`
                : '{{ route("admin.maintenance.store") }}';
            const method = maintenanceId ? 'PUT' : 'POST';

            const formData = {
                name: $('#name').val(),
                start_date: $('#start_date').val(),
                end_date: $('#end_date').val(),
                _token: $('meta[name="csrf-token"]').attr('content')
            };

            if (method === 'PUT') {
                formData._method = 'PUT';
            }

            $.ajax({
                url: url,
                type: 'POST',
                data: formData,
                success: function(response) {
                    if (response.success) {
                        toastr.success(response.message);
                        $('#maintenanceModal').modal('hide');
                        
                        // Forzar recarga después de que el modal se cierre completamente
                        $('#maintenanceModal').on('hidden.bs.modal', function () {
                            loadMaintenances();
                            loadStatistics();
                            // Remover el evento para evitar múltiples llamadas
                            $(this).off('hidden.bs.modal');
                        });
                    }
                },
                error: function(xhr) {
                    handleValidationErrors(xhr);
                }
            });
        }

        function editMaintenance(id) {
            $.ajax({
                url: `{{ route("admin.maintenance.index") }}/${id}`,
                type: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                success: function(response) {
                    if (response.success) {
                        showMaintenanceModal(response.data);
                    }
                },
                error: function(xhr) {
                    toastr.error('Error al cargar los datos del mantenimiento');
                }
            });
        }

        function deleteMaintenance(id) {
            Swal.fire({
                title: '¿Está seguro?',
                text: 'Esta acción eliminará el mantenimiento permanentemente',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `{{ route("admin.maintenance.index") }}/${id}`,
                        type: 'POST',
                        data: {
                            _method: 'DELETE',
                            _token: $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            if (response.success) {
                                toastr.success(response.message);
                                loadMaintenances();
                                loadStatistics();
                            }
                        },
                        error: function(xhr) {
                            const response = xhr.responseJSON;
                            toastr.error(response.message || 'Error al eliminar el mantenimiento');
                        }
                    });
                }
            });
        }

        function showMaintenanceDetail(id) {
            $.ajax({
                url: `{{ route("admin.maintenance.index") }}/${id}`,
                type: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                success: function(response) {
                    if (response.success) {
                        renderMaintenanceDetail(response.data);
                        $('#maintenanceDetailModal').modal('show');
                    }
                },
                error: function(xhr) {
                    toastr.error('Error al cargar los detalles del mantenimiento');
                }
            });
        }

        function renderMaintenanceDetail(maintenance) {
            const schedules = maintenance.schedules || [];
            
            let schedulesHtml = '';
            if (schedules.length > 0) {
                schedules.forEach(schedule => {
                    const activities = schedule.activities || [];
                    let activitiesHtml = '';
                    
                    if (activities.length > 0) {
                        activities.forEach(activity => {
                            activitiesHtml += `
                                <div class="activity-item">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <strong>${formatDate(activity.maintenance_date)}</strong>
                                            <p class="mb-1">${activity.descripcion}</p>
                                        </div>
                                        <div>
                                            ${activity.image_full_url ? `<img src="${activity.image_full_url}" class="img-thumbnail" style="max-width: 80px;">` : '<span class="text-muted">Sin imagen</span>'}
                                        </div>
                                    </div>
                                </div>
                            `;
                        });
                    } else {
                        activitiesHtml = '<p class="text-muted">No hay actividades registradas</p>';
                    }

                    schedulesHtml += `
                        <div class="card schedule-card">
                            <div class="card-body">
                                <h6 class="card-title">
                                    ${schedule.day_of_week_text} ${schedule.time_range} - 
                                    <span class="badge badge-info">${schedule.maintenance_type_text}</span>
                                </h6>
                                <p class="text-muted mb-2">
                                    <strong>Vehículo:</strong> ${schedule.vehicle ? schedule.vehicle.license_plate : 'N/A'} |
                                    <strong>Responsable:</strong> ${schedule.driver ? schedule.driver.names + ' ' + schedule.driver.lastnames : 'N/A'}
                                </p>
                                <div class="mt-3">
                                    <h6>Actividades:</h6>
                                    ${activitiesHtml}
                                </div>
                            </div>
                        </div>
                    `;
                });
            } else {
                schedulesHtml = '<div class="alert alert-info">No hay horarios programados para este mantenimiento</div>';
            }

            const detailHtml = `
                <div class="row">
                    <div class="col-md-6">
                        <h5>${maintenance.name}</h5>
                        <p><strong>Fecha de inicio:</strong> ${formatDate(maintenance.start_date)}</p>
                        <p><strong>Fecha de fin:</strong> ${formatDate(maintenance.end_date)}</p>
                        <p><strong>Duración:</strong> ${maintenance.duration} días</p>
                        <p><strong>Estado:</strong> ${getStatusBadge(maintenance.status, maintenance.status_text)}</p>
                    </div>
                </div>
                <hr>
                <h5>Horarios Programados</h5>
                ${schedulesHtml}
            `;

            $('#maintenanceDetails').html(detailHtml);
        }

        function validateDateOverlap() {
            const startDate = $('#start_date').val();
            const endDate = $('#end_date').val();
            const excludeId = $('#maintenanceId').val();

            if (!startDate || !endDate) {
                $('#overlapWarning').hide();
                return;
            }

            $.ajax({
                url: '{{ route("admin.maintenance.validate-overlap") }}',
                type: 'POST',
                data: {
                    start_date: startDate,
                    end_date: endDate,
                    exclude_id: excludeId,
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        if (response.has_overlap) {
                            $('#overlapWarning').show();
                        } else {
                            $('#overlapWarning').hide();
                        }
                    }
                }
            });
        }

        // Funciones de utilidad
        function formatDate(dateString) {
            const date = new Date(dateString);
            return date.toLocaleDateString('es-ES');
        }

        function handleValidationErrors(xhr) {
            if (xhr.status === 422) {
                const errors = xhr.responseJSON.errors;
                $('.form-control').removeClass('is-invalid');
                $('.invalid-feedback').text('');

                for (const field in errors) {
                    const input = $(`#${field}`);
                    const feedback = input.siblings('.invalid-feedback');
                    input.addClass('is-invalid');
                    feedback.text(errors[field][0]);
                }
            } else {
                toastr.error(xhr.responseJSON.message || 'Error en el servidor');
            }
        }

        function renderPagination(pagination, callback) {
            const container = $('#paginationContainer');
            container.empty();

            if (pagination.last_page <= 1) return;

            let paginationHtml = '<nav aria-label="Page navigation"><ul class="pagination">';

            // Botón anterior
            paginationHtml += `
                <li class="page-item ${pagination.current_page === 1 ? 'disabled' : ''}">
                    <a class="page-link" href="#" onclick="${callback}(${pagination.current_page - 1}); return false;">Anterior</a>
                </li>
            `;

            // Páginas
            for (let i = 1; i <= pagination.last_page; i++) {
                paginationHtml += `
                    <li class="page-item ${i === pagination.current_page ? 'active' : ''}">
                        <a class="page-link" href="#" onclick="${callback}(${i}); return false;">${i}</a>
                    </li>
                `;
            }

            // Botón siguiente
            paginationHtml += `
                <li class="page-item ${pagination.current_page === pagination.last_page ? 'disabled' : ''}">
                    <a class="page-link" href="#" onclick="${callback}(${pagination.current_page + 1}); return false;">Siguiente</a>
                </li>
            `;

            paginationHtml += '</ul></nav>';
            container.html(paginationHtml);
        }

        // ========== FUNCIONES PARA HORARIOS ==========
        function loadSchedules() {
            $.ajax({
                url: '{{ route("admin.maintenance-schedules.index") }}',
                method: 'GET',
                success: function(response) {
                    let tbody = '';
                    response.data.forEach(function(schedule) {
                        tbody += `
                            <tr>
                                <td>${schedule.id}</td>
                                <td>${schedule.maintenance ? schedule.maintenance.name : 'N/A'}</td>
                                <td>${schedule.start_date ? new Date(schedule.start_date).toLocaleString() : 'N/A'}</td>
                                <td>${schedule.end_date ? new Date(schedule.end_date).toLocaleString() : 'N/A'}</td>
                                <td>${schedule.description || 'Sin descripción'}</td>
                                <td><span class="badge badge-${getStatusColor(schedule.status)}">${schedule.status}</span></td>
                                <td>
                                    <button class="btn btn-sm btn-info" onclick="editSchedule(${schedule.id})">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-sm btn-danger" onclick="deleteSchedule(${schedule.id})">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        `;
                    });
                    $('#schedulesTableBody').html(tbody);
                },
                error: function(xhr) {
                    toastr.error('Error al cargar horarios');
                }
            });
        }

        function showScheduleModal(schedule = null) {
            // Cargar los datos necesarios para los selects
            loadMaintenancesForSelect('#schedule_maintenance_id');
            loadVehiclesForSelect('#schedule_vehicle_id');
            loadEmployeesForSelect('#schedule_driver_id');
            
            if (schedule) {
                $('#scheduleModalLabel').text('Editar Horario');
                $('#scheduleId').val(schedule.id);
                
                // Esperar a que se carguen los selects antes de establecer valores
                setTimeout(function() {
                    $('#schedule_maintenance_id').val(schedule.maintenance_id);
                    $('#schedule_vehicle_id').val(schedule.vehicle_id);
                    $('#schedule_driver_id').val(schedule.driver_id);
                    $('#schedule_maintenance_type').val(schedule.maintenance_type);
                    $('#schedule_day_of_week').val(schedule.day_of_week);
                    $('#schedule_start_time').val(schedule.start_time);
                    $('#schedule_end_time').val(schedule.end_time);
                    $('#schedule_recurrence_weeks').val(schedule.recurrence_weeks || 1);
                    $('#schedule_description').val(schedule.description);
                }, 300);
            } else {
                $('#scheduleModalLabel').text('Nuevo Horario');
                $('#scheduleForm')[0].reset();
                $('#scheduleId').val('');
            }
            $('#scheduleModal').modal('show');
        }

        function saveSchedule() {
            const formData = new FormData($('#scheduleForm')[0]);
            const scheduleId = $('#scheduleId').val();
            const url = scheduleId 
                ? `{{ route("admin.maintenance-schedules.update", ":id") }}`.replace(':id', scheduleId)
                : '{{ route("admin.maintenance-schedules.store") }}';
            const method = scheduleId ? 'PUT' : 'POST';

            if (method === 'PUT') {
                formData.append('_method', 'PUT');
            }

            $.ajax({
                url: url,
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    toastr.success(response.message);
                    $('#scheduleModal').modal('hide');
                    loadSchedules();
                },
                error: function(xhr) {
                    handleValidationErrors(xhr);
                }
            });
        }

        function editSchedule(id) {
            $.ajax({
                url: `{{ route("admin.maintenance-schedules.show", ":id") }}`.replace(':id', id),
                method: 'GET',
                success: function(response) {
                    showScheduleModal(response.data);
                },
                error: function(xhr) {
                    toastr.error('Error al cargar datos del horario');
                }
            });
        }

        function deleteSchedule(id) {
            if (confirm('¿Está seguro de eliminar este horario?')) {
                $.ajax({
                    url: `{{ route("admin.maintenance-schedules.destroy", ":id") }}`.replace(':id', id),
                    method: 'DELETE',
                    data: { _token: $('meta[name="csrf-token"]').attr('content') },
                    success: function(response) {
                        toastr.success(response.message);
                        loadSchedules();
                    },
                    error: function(xhr) {
                        toastr.error('Error al eliminar horario');
                    }
                });
            }
        }

        // Función para redirigir a gestionar horarios de un mantenimiento específico
        function assignScheduleToMaintenance(maintenanceId) {
            // Redirigir a la página de gestión de horarios para este mantenimiento
            window.location.href = `{{ url('/admin/maintenance') }}/${maintenanceId}/schedules`;
        }

        // ========== FUNCIONES PARA REGISTROS ==========
        function loadRecords() {
            $.ajax({
                url: '{{ route("admin.maintenance-records.index") }}',
                method: 'GET',
                success: function(response) {
                    let tbody = '';
                    response.data.forEach(function(record) {
                        const imageHtml = record.image_path 
                            ? `<img src="${record.image_path}" class="img-thumbnail" style="width: 50px; height: 50px;">`
                            : '<span class="text-muted">Sin imagen</span>';
                        
                        tbody += `
                            <tr>
                                <td>${record.id}</td>
                                <td>${record.maintenance ? record.maintenance.name : 'N/A'}</td>
                                <td>${record.employee ? record.employee.name : 'N/A'}</td>
                                <td>${record.activity_date ? new Date(record.activity_date).toLocaleString() : 'N/A'}</td>
                                <td>${record.activity_description || 'Sin descripción'}</td>
                                <td>${imageHtml}</td>
                                <td>
                                    <button class="btn btn-sm btn-info" onclick="editRecord(${record.id})">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-sm btn-danger" onclick="deleteRecord(${record.id})">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        `;
                    });
                    $('#recordsTableBody').html(tbody);
                },
                error: function(xhr) {
                    toastr.error('Error al cargar registros');
                }
            });
        }

        function showRecordModal(record = null) {
            if (record) {
                $('#recordModalLabel').text('Editar Registro');
                $('#recordId').val(record.id);
                $('#record_maintenance_id').val(record.maintenance_id);
                $('#record_employee_id').val(record.employee_id);
                $('#record_activity_date').val(record.activity_date);
                $('#record_activity_description').val(record.activity_description);
                $('#record_notes').val(record.notes);
            } else {
                $('#recordModalLabel').text('Nuevo Registro');
                $('#recordForm')[0].reset();
                $('#recordId').val('');
            }
            $('#recordModal').modal('show');
        }

        function saveRecord() {
            const formData = new FormData($('#recordForm')[0]);
            const recordId = $('#recordId').val();
            const url = recordId 
                ? `{{ route("admin.maintenance-records.update", ":id") }}`.replace(':id', recordId)
                : '{{ route("admin.maintenance-records.store") }}';
            
            if (recordId) {
                formData.append('_method', 'PUT');
            }

            $.ajax({
                url: url,
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    toastr.success(response.message);
                    $('#recordModal').modal('hide');
                    loadRecords();
                },
                error: function(xhr) {
                    handleValidationErrors(xhr);
                }
            });
        }

        function editRecord(id) {
            $.ajax({
                url: `{{ route("admin.maintenance-records.show", ":id") }}`.replace(':id', id),
                method: 'GET',
                success: function(response) {
                    showRecordModal(response.data);
                },
                error: function(xhr) {
                    toastr.error('Error al cargar datos del registro');
                }
            });
        }

        function deleteRecord(id) {
            if (confirm('¿Está seguro de eliminar este registro?')) {
                $.ajax({
                    url: `{{ route("admin.maintenance-records.destroy", ":id") }}`.replace(':id', id),
                    method: 'DELETE',
                    data: { _token: $('meta[name="csrf-token"]').attr('content') },
                    success: function(response) {
                        toastr.success(response.message);
                        loadRecords();
                    },
                    error: function(xhr) {
                        toastr.error('Error al eliminar registro');
                    }
                });
            }
        }

        // ========== FUNCIONES AUXILIARES ==========
        function loadMaintenancesForSelect(selector) {
            $.ajax({
                url: '{{ route("admin.maintenance.index") }}',
                method: 'GET',
                success: function(response) {
                    let options = '<option value="">Seleccionar Mantenimiento</option>';
                    response.data.forEach(function(maintenance) {
                        options += `<option value="${maintenance.id}">${maintenance.name}</option>`;
                    });
                    $(selector).html(options);
                },
                error: function(xhr) {
                    console.error('Error al cargar mantenimientos para select');
                }
            });
        }

        function loadEmployeesForSelect(selector) {
            $.ajax({
                url: '{{ route("admin.personnel.employees.api.active") }}',
                method: 'GET',
                success: function(response) {
                    let options = '<option value="">Seleccionar Empleado</option>';
                    response.data.forEach(function(employee) {
                        options += `<option value="${employee.id}">${employee.name}</option>`;
                    });
                    $(selector).html(options);
                },
                error: function(xhr) {
                    console.error('Error al cargar empleados para select');
                }
            });
        }

        function loadVehiclesForSelect(selector) {
            $.ajax({
                url: '{{ route("admin.vehicles.index") }}',
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                success: function(response) {
                    let options = '<option value="">Seleccionar Vehículo</option>';
                    if(response.data) {
                        response.data.forEach(function(vehicle) {
                            const vehicleText = vehicle.license_plate ? 
                                `${vehicle.license_plate}${vehicle.brand ? ' - ' + vehicle.brand : ''}${vehicle.model ? ' ' + vehicle.model : ''}` :
                                `ID: ${vehicle.id}`;
                            options += `<option value="${vehicle.id}">${vehicleText}</option>`;
                        });
                    }
                    $(selector).html(options);
                },
                error: function(xhr) {
                    console.error('Error al cargar vehículos para select:', xhr);
                    // Fallback simple en caso de error
                    $(selector).html('<option value="">Error al cargar vehículos</option>');
                }
            });
        }

        function getStatusColor(status) {
            switch(status) {
                case 'programado': return 'primary';
                case 'en_progreso': return 'warning';
                case 'completado': return 'success';
                case 'cancelado': return 'secondary';
                default: return 'secondary';
            }
        }
    </script>
@stop
