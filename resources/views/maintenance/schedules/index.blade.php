@extends('adminlte::page')

@section('title', 'Horarios de Mantenimiento')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1><i class="fas fa-calendar-alt"></i> Horarios de Mantenimiento</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.maintenance.index') }}">
                            <i class="fas fa-tools"></i> Mantenimientos
                        </a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">
                        Horarios: <span id="maintenance-name">{{ $maintenance->name ?? 'Cargando...' }}</span>
                    </li>
                </ol>
            </nav>
        </div>
        <div>
            <button type="button" class="btn btn-success" onclick="window.showScheduleModal()">
                <i class="fas fa-plus"></i> Nuevo Horario
            </button>
        </div>
    </div>
@stop

@section('content')
    <!-- Información del Mantenimiento -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <strong>Mantenimiento:</strong><br>
                    <span id="maintenance-title">{{ $maintenance->name ?? 'Cargando...' }}</span>
                </div>
                <div class="col-md-3">
                    <strong>Fecha de Inicio:</strong><br>
                    <span id="maintenance-start">{{ $maintenance->start_date ?? 'N/A' }}</span>
                </div>
                <div class="col-md-3">
                    <strong>Fecha de Fin:</strong><br>
                    <span id="maintenance-end">{{ $maintenance->end_date ?? 'N/A' }}</span>
                </div>
                <div class="col-md-3">
                    <strong>Estado:</strong><br>
                    <span id="maintenance-status" class="badge badge-info">{{ $maintenance->status_text ?? 'N/A' }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Estadísticas -->
    <div class="row mb-4">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3 id="stat-total-schedules">0</h3>
                    <p>Total Horarios</p>
                </div>
                <div class="icon">
                    <i class="fas fa-calendar"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3 id="stat-scheduled">0</h3>
                    <p>Programados</p>
                </div>
                <div class="icon">
                    <i class="fas fa-clock"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-primary">
                <div class="inner">
                    <h3 id="stat-in-progress">0</h3>
                    <p>En Progreso</p>
                </div>
                <div class="icon">
                    <i class="fas fa-spinner"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3 id="stat-completed">0</h3>
                    <p>Completados</p>
                </div>
                <div class="icon">
                    <i class="fas fa-check"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <label for="dayFilter">Día de la Semana:</label>
                    <select id="dayFilter" class="form-control">
                        <option value="">Todos los días</option>
                        <option value="0">Domingo</option>
                        <option value="1">Lunes</option>
                        <option value="2">Martes</option>
                        <option value="3">Miércoles</option>
                        <option value="4">Jueves</option>
                        <option value="5">Viernes</option>
                        <option value="6">Sábado</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="typeFilter">Tipo de Mantenimiento:</label>
                    <select id="typeFilter" class="form-control">
                        <option value="">Todos los tipos</option>
                        <option value="preventive">Preventivo</option>
                        <option value="corrective">Correctivo</option>
                        <option value="predictive">Predictivo</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="statusFilter">Estado:</label>
                    <select id="statusFilter" class="form-control">
                        <option value="">Todos los estados</option>
                        <option value="scheduled">Programado</option>
                        <option value="in_progress">En Progreso</option>
                        <option value="completed">Completado</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="button" class="btn btn-secondary mr-2" onclick="window.clearFilters()">
                        <i class="fas fa-times"></i> Limpiar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla de Horarios -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-calendar-alt"></i> Lista de Horarios</h3>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Día</th>
                            <th>Horario</th>
                            <th>Tipo</th>
                            <th>Vehículo</th>
                            <th>Conductor</th>
                            <th>Recurrencia</th>
                            <th>Estado</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="schedulesTableBody">
                        <!-- Se carga dinámicamente con JavaScript -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal para Nuevo/Editar Horario -->
    <div class="modal fade" id="scheduleModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">
                        <i class="fas fa-calendar-plus"></i> Nuevo Horario de Mantenimiento
                    </h4>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <form id="scheduleForm">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Día de la Semana <span class="text-danger">*</span></label>
                                    <select id="day_of_week" name="day_of_week" class="form-control" required>
                                        <option value="">Seleccionar día</option>
                                        <option value="0">Domingo</option>
                                        <option value="1">Lunes</option>
                                        <option value="2">Martes</option>
                                        <option value="3">Miércoles</option>
                                        <option value="4">Jueves</option>
                                        <option value="5">Viernes</option>
                                        <option value="6">Sábado</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Tipo de Mantenimiento <span class="text-danger">*</span></label>
                                    <select id="maintenance_type" name="maintenance_type" class="form-control" required>
                                        <option value="">Seleccionar tipo</option>
                                        <option value="preventive">Preventivo</option>
                                        <option value="corrective">Correctivo</option>
                                        <option value="predictive">Predictivo</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Hora de Inicio <span class="text-danger">*</span></label>
                                    <input type="time" id="start_time" name="start_time" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Hora de Fin <span class="text-danger">*</span></label>
                                    <input type="time" id="end_time" name="end_time" class="form-control" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Vehículo <span class="text-danger">*</span></label>
                                    <select id="vehicle_id" name="vehicle_id" class="form-control" required>
                                        <option value="">Seleccionar vehículo</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Conductor <span class="text-danger">*</span></label>
                                    <select id="driver_id" name="driver_id" class="form-control" required>
                                        <option value="">Seleccionar conductor</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Recurrencia (semanas) <span class="text-danger">*</span></label>
                                    <select id="recurrence_weeks" name="recurrence_weeks" class="form-control" required>
                                        <option value="1">Cada semana</option>
                                        <option value="2">Cada 2 semanas</option>
                                        <option value="3">Cada 3 semanas</option>
                                        <option value="4">Cada 4 semanas (mensual)</option>
                                        <option value="8">Cada 8 semanas (bimestral)</option>
                                        <option value="12">Cada 12 semanas (trimestral)</option>
                                        <option value="26">Cada 26 semanas (semestral)</option>
                                        <option value="52">Cada 52 semanas (anual)</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Estado</label>
                                    <select id="status" name="status" class="form-control">
                                        <option value="scheduled">Programado</option>
                                        <option value="in_progress">En Progreso</option>
                                        <option value="completed">Completado</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Descripción</label>
                            <textarea id="description" name="description" class="form-control" rows="3" placeholder="Descripción opcional del horario de mantenimiento..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                            <i class="fas fa-times"></i> Cancelar
                        </button>
                        <button type="submit" class="btn btn-success" id="saveScheduleBtn">
                            <i class="fas fa-save"></i> Guardar Horario
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop

@section('js')
<script>
    // Variable global para el ID del mantenimiento
    const maintenanceId = {{ $maintenance->id ?? 'null' }};

    // Funciones globales para acceso desde onclick
    window.showScheduleModal = function() {
        console.log('Abriendo modal de horario');
        
        // Resetear el formulario
        $('#scheduleForm')[0].reset();
        
        // Cargar vehículos y empleados
        window.loadVehiclesForSelect('#vehicle_id');
        window.loadEmployeesForSelect('#driver_id');
        
        // Mostrar el modal
        $('#scheduleModal').modal('show');
    };

    window.editSchedule = function(id) {
        console.log('Editando horario:', id);
        // Implementar edición
    };

    window.deleteSchedule = function(id) {
        console.log('Eliminando horario:', id);
        // Implementar eliminación
    };

    window.viewActivities = function(id) {
        console.log('Viendo actividades del horario:', id);
        window.location.href = `{{ url('/admin/maintenance-schedules') }}/${id}/activities`;
    };

    window.filterSchedules = function() {
        loadSchedules();
    };

    window.clearFilters = function() {
        $('#dayFilter').val('');
        $('#typeFilter').val('');
        $('#statusFilter').val('');
        loadSchedules();
    };

    window.loadVehiclesForSelect = function(selector) {
        $.ajax({
            url: '{{ route("admin.vehicles.index") }}',
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            success: function(response) {
                console.log('Vehicles response:', response);
                let options = '<option value="">Seleccionar Vehículo</option>';
                if(response.data) {
                    response.data.forEach(function(vehicle) {
                        const vehicleText = vehicle.license_plate ? 
                            `${vehicle.license_plate}${vehicle.brand ? ' - ' + vehicle.brand : ''}${vehicle.model ? ' ' + vehicle.model : ''}` :
                            `ID: ${vehicle.id}`;
                        options += `<option value="${vehicle.id}">${vehicleText}</option>`;
                    });
                } else {
                    console.warn('No vehicle data found in response');
                }
                $(selector).html(options);
            },
            error: function(xhr) {
                console.error('Error al cargar vehículos para select:', xhr);
                $(selector).html('<option value="">Error al cargar vehículos</option>');
            }
        });
    };

    window.loadEmployeesForSelect = function(selector) {
        $.ajax({
            url: '{{ route("admin.personnel.employees.api.active") }}',
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            success: function(response) {
                console.log('Employees response:', response);
                let options = '<option value="">Seleccionar Empleado</option>';
                if(response.data) {
                    response.data.forEach(function(employee) {
                        const employeeName = employee.names ? 
                            `${employee.names} ${employee.lastnames || ''}` : 
                            `${employee.name || 'ID: ' + employee.id}`;
                        options += `<option value="${employee.id}">${employeeName}</option>`;
                    });
                } else if(response.length) {
                    // Si viene como array directo
                    response.forEach(function(employee) {
                        const employeeName = employee.names ? 
                            `${employee.names} ${employee.lastnames || ''}` : 
                            `${employee.name || 'ID: ' + employee.id}`;
                        options += `<option value="${employee.id}">${employeeName}</option>`;
                    });
                }
                $(selector).html(options);
            },
            error: function(xhr) {
                console.error('Error al cargar empleados para select:', xhr);
                // Fallback: intentar con otra ruta
                $.ajax({
                    url: '{{ route("admin.personnel.employees.index") }}',
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    success: function(response) {
                        console.log('Employees fallback response:', response);
                        let options = '<option value="">Seleccionar Empleado</option>';
                        if(response.data) {
                            response.data.forEach(function(employee) {
                                const employeeName = employee.names ? 
                                    `${employee.names} ${employee.lastnames || ''}` : 
                                    `${employee.name || 'ID: ' + employee.id}`;
                                options += `<option value="${employee.id}">${employeeName}</option>`;
                            });
                        }
                        $(selector).html(options);
                    },
                    error: function(xhr2) {
                        console.error('Error en fallback de empleados:', xhr2);
                        $(selector).html('<option value="">Error al cargar empleados</option>');
                    }
                });
            }
        });
    };

    // Document ready
    $(document).ready(function() {
        console.log('Document ready - Maintenance ID:', maintenanceId);
        
        if (!maintenanceId) {
            console.error('No maintenance ID provided');
            return;
        }
        
        // Cargar datos iniciales
        loadSchedules();
        loadStatistics();
        
        // Configurar filtros
        $('#dayFilter, #typeFilter, #statusFilter').change(function() {
            loadSchedules();
        });

        // Configurar formulario de horario
        $('#scheduleForm').on('submit', function(e) {
            e.preventDefault();
            saveSchedule();
        });
    });

    // Funciones principales
    function loadSchedules(page = 1) {
        console.log('Loading schedules for maintenance ID:', maintenanceId);
        
        if (!maintenanceId) {
            console.error('No maintenance ID provided');
            $('#schedulesTableBody').html(`
                <tr>
                    <td colspan="9" class="text-center text-muted py-4">
                        <i class="fas fa-exclamation-triangle fa-3x mb-3"></i><br>
                        Error: ID de mantenimiento no válido
                    </td>
                </tr>
            `);
            return;
        }

        const dayFilter = $('#dayFilter').val();
        const typeFilter = $('#typeFilter').val();
        const statusFilter = $('#statusFilter').val();

        // Solo incluir filtros que tengan valores
        const data = { page: page };
        
        if (dayFilter && dayFilter !== '' && dayFilter !== null) {
            data.day_of_week = dayFilter;
        }
        if (typeFilter && typeFilter !== '' && typeFilter !== null) {
            data.maintenance_type = typeFilter;
        }
        if (statusFilter && statusFilter !== '' && statusFilter !== null) {
            data.status = statusFilter;
        }

        console.log('Sending data:', data); // Debug log

        $.ajax({
            url: `{{ url("/admin/maintenance") }}/${maintenanceId}/schedules`,
            type: 'GET',
            data: data,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            success: function(response) {
                console.log('Schedules response:', response);
                console.log('Response data length:', response.data ? response.data.length : 'No data');
                console.log('Debug info:', response.debug);
                console.log('Schedules raw data:', response.debug.schedules_raw);
                console.log('Final query SQL:', response.debug.final_query_sql);
                console.log('Final query bindings:', response.debug.final_query_bindings);
                console.log('Applied filters:', response.debug.filters);
                
                // Verificar cada schedule raw individualmente
                if (response.debug.schedules_raw && response.debug.schedules_raw.length > 0) {
                    response.debug.schedules_raw.forEach((schedule, index) => {
                        console.log(`Schedule ${index + 1}:`, schedule);
                    });
                }
                
                if (response.success) {
                    renderSchedulesTable(response.data);
                    if (response.pagination) {
                        renderPagination(response.pagination, 'loadSchedules');
                    }
                } else {
                    console.error('Response not successful:', response);
                    $('#schedulesTableBody').html(`
                        <tr>
                            <td colspan="9" class="text-center text-muted py-4">
                                <i class="fas fa-exclamation-triangle fa-3x mb-3"></i><br>
                                Error al cargar los horarios
                            </td>
                        </tr>
                    `);
                }
            },
            error: function(xhr) {
                console.error('Error loading schedules:', xhr);
                $('#schedulesTableBody').html(`
                    <tr>
                        <td colspan="9" class="text-center text-muted py-4">
                            <i class="fas fa-exclamation-triangle fa-3x mb-3"></i><br>
                            Error de conexión al cargar los horarios
                        </td>
                    </tr>
                `);
            }
        });
    }

    function loadStatistics() {
        console.log('Loading statistics for maintenance ID:', maintenanceId);
        
        if (!maintenanceId) {
            console.error('No maintenance ID for statistics');
            return;
        }

        $.ajax({
            url: `{{ url("/admin/maintenance") }}/${maintenanceId}/schedules`,
            type: 'GET',
            data: {
                stats_only: true
            },
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            success: function(response) {
                console.log('Statistics response:', response);
                if (response.success && response.stats) {
                    $('#stat-total-schedules').text(response.stats.total || 0);
                    $('#stat-scheduled').text(response.stats.scheduled || 0);
                    $('#stat-in-progress').text(response.stats.in_progress || 0);
                    $('#stat-completed').text(response.stats.completed || 0);
                }
            },
            error: function(xhr) {
                console.error('Error loading statistics:', xhr);
            }
        });
    }

    function renderSchedulesTable(schedules) {
        console.log('Rendering schedules table with:', schedules);
        const tbody = $('#schedulesTableBody');
        tbody.empty();

        if (!schedules || schedules.length === 0) {
            tbody.append(`
                <tr>
                    <td colspan="9" class="text-center text-muted py-4">
                        <i class="fas fa-calendar fa-3x mb-3"></i><br>
                        No hay horarios registrados
                    </td>
                </tr>
            `);
            return;
        }

        schedules.forEach(schedule => {
            console.log('Processing schedule:', schedule);
            const statusBadge = getStatusBadge(schedule.status);
            const dayText = getDayText(schedule.day_of_week);
            const typeText = getTypeText(schedule.maintenance_type);
            const timeRange = `${schedule.start_time} - ${schedule.end_time}`;
            const vehicleText = schedule.vehicle ? 
                (schedule.vehicle.plate || schedule.vehicle.license_plate || 'Sin placa') : 'N/A';
            const driverText = schedule.driver ? 
                `${schedule.driver.names || ''} ${schedule.driver.lastnames || ''}`.trim() || 'Sin nombre' : 'N/A';
            
            tbody.append(`
                <tr>
                    <td>${schedule.id}</td>
                    <td>${dayText}</td>
                    <td>${timeRange}</td>
                    <td><span class="badge badge-info">${typeText}</span></td>
                    <td>${vehicleText}</td>
                    <td>${driverText}</td>
                    <td>Cada ${schedule.recurrence_weeks || 1} semana(s)</td>
                    <td>${statusBadge}</td>
                    <td class="text-center">
                        <button type="button" class="btn btn-sm btn-primary" onclick="window.editSchedule(${schedule.id})" title="Editar">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-danger" onclick="window.deleteSchedule(${schedule.id})" title="Eliminar">
                            <i class="fas fa-trash"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-success" onclick="window.viewActivities(${schedule.id})" title="Ver Actividades">
                            <i class="fas fa-tasks"></i>
                        </button>
                    </td>
                </tr>
            `);
        });
    }

    function saveSchedule() {
        const formData = {
            maintenance_id: maintenanceId,
            day_of_week: $('#day_of_week').val(),
            maintenance_type: $('#maintenance_type').val(),
            start_time: $('#start_time').val(),
            end_time: $('#end_time').val(),
            vehicle_id: $('#vehicle_id').val(),
            driver_id: $('#driver_id').val(),
            recurrence_weeks: $('#recurrence_weeks').val(),
            status: $('#status').val(),
            description: $('#description').val()
        };

        console.log('Saving schedule:', formData);

        $.ajax({
            url: '{{ route("admin.maintenance-schedules.store") }}',
            method: 'POST',
            data: formData,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'X-Requested-With': 'XMLHttpRequest'
            },
            success: function(response) {
                console.log('Schedule saved:', response);
                if (response.success) {
                    $('#scheduleModal').modal('hide');
                    
                    // Forzar recarga después de un pequeño delay
                    setTimeout(function() {
                        console.log('Reloading schedules after save...');
                        loadSchedules();
                        loadStatistics();
                    }, 500);
                    
                    // Mostrar mensaje de éxito
                    if (typeof toastr !== 'undefined') {
                        toastr.success('Horario guardado exitosamente');
                    } else {
                        alert('Horario guardado exitosamente');
                    }
                } else {
                    if (typeof toastr !== 'undefined') {
                        toastr.error(response.message || 'Error al guardar el horario');
                    } else {
                        alert('Error al guardar el horario');
                    }
                }
            },
            error: function(xhr) {
                console.error('Error saving schedule:', xhr);
                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    const errors = xhr.responseJSON.errors;
                    let errorMessage = 'Errores de validación:\n';
                    Object.keys(errors).forEach(key => {
                        errorMessage += `- ${errors[key][0]}\n`;
                    });
                    toastr.error(errorMessage);
                } else {
                    toastr.error('Error al guardar el horario');
                }
            }
        });
    }

    // Funciones auxiliares
    function getStatusBadge(status) {
        const badges = {
            'scheduled': 'badge-warning',
            'in_progress': 'badge-primary',
            'completed': 'badge-success'
        };
        const texts = {
            'scheduled': 'Programado',
            'in_progress': 'En Progreso',
            'completed': 'Completado'
        };
        const badgeClass = badges[status] || 'badge-secondary';
        const badgeText = texts[status] || status;
        return `<span class="badge ${badgeClass}">${badgeText}</span>`;
    }

    function getDayText(dayNumber) {
        const days = {
            '0': 'Domingo',
            '1': 'Lunes', 
            '2': 'Martes',
            '3': 'Miércoles',
            '4': 'Jueves',
            '5': 'Viernes',
            '6': 'Sábado'
        };
        return days[dayNumber] || 'N/A';
    }

    function getTypeText(type) {
        const types = {
            'preventive': 'Preventivo',
            'corrective': 'Correctivo',
            'predictive': 'Predictivo'
        };
        return types[type] || type;
    }

    function renderPagination(pagination, callback) {
        console.log('Pagination:', pagination);
        // Implementar paginación si es necesario
    }
</script>
@stop

@section('content')
    <!-- Información del Mantenimiento -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <strong>Mantenimiento:</strong><br>
                    <span id="maintenance-title">{{ $maintenance->name ?? 'Cargando...' }}</span>
                </div>
                <div class="col-md-3">
                    <strong>Fecha de Inicio:</strong><br>
                    <span id="maintenance-start">{{ $maintenance->start_date ?? 'N/A' }}</span>
                </div>
                <div class="col-md-3">
                    <strong>Fecha de Fin:</strong><br>
                    <span id="maintenance-end">{{ $maintenance->end_date ?? 'N/A' }}</span>
                </div>
                <div class="col-md-3">
                    <strong>Estado:</strong><br>
                    <span id="maintenance-status" class="badge badge-info">{{ $maintenance->status_text ?? 'N/A' }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Estadísticas de Horarios -->
    <div class="row mb-4">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3 id="stat-total-schedules">0</h3>
                    <p>Total Horarios</p>
                </div>
                <div class="icon">
                    <i class="fas fa-calendar"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
            <div class="inner">
                    <h3 id="stat-scheduled">0</h3>
                    <p>Programados</p>
                </div>
                <div class="icon">
                    <i class="fas fa-clock"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-primary">
                <div class="inner">
                    <h3 id="stat-in-progress">0</h3>
                    <p>En Progreso</p>
                </div>
                <div class="icon">
                    <i class="fas fa-play"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3 id="stat-completed">0</h3>
                    <p>Completados</p>
                </div>
                <div class="icon">
                    <i class="fas fa-check"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Día de la semana:</label>
                        <select class="form-control" id="dayFilter">
                            <option value="">Todos los días</option>
                            <option value="1">Lunes</option>
                            <option value="2">Martes</option>
                            <option value="3">Miércoles</option>
                            <option value="4">Jueves</option>
                            <option value="5">Viernes</option>
                            <option value="6">Sábado</option>
                            <option value="0">Domingo</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Tipo de Mantenimiento:</label>
                        <select class="form-control" id="typeFilter">
                            <option value="">Todos los tipos</option>
                            <option value="preventive">Preventivo</option>
                            <option value="corrective">Correctivo</option>
                            <option value="predictive">Predictivo</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Estado:</label>
                        <select class="form-control" id="statusFilter">
                            <option value="">Todos los estados</option>
                            <option value="scheduled">Programado</option>
                            <option value="in_progress">En Progreso</option>
                            <option value="completed">Completado</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>&nbsp;</label>
                        <div>
                            <button type="button" class="btn btn-primary" onclick="filterSchedules()">
                                <i class="fas fa-search"></i> Filtrar
                            </button>
                            <button type="button" class="btn btn-secondary" onclick="clearFilters()">
                                <i class="fas fa-times"></i> Limpiar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla de Horarios -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Lista de Horarios</h3>
            <div class="card-tools">
                <button type="button" class="btn btn-sm btn-primary" onclick="loadSchedules()">
                    <i class="fas fa-sync-alt"></i> Actualizar
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover" id="schedulesTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Día de la Semana</th>
                            <th>Horario</th>
                            <th>Tipo</th>
                            <th>Vehículo</th>
                            <th>Responsable</th>
                            <th>Recurrencia</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="schedulesTableBody">
                        <tr>
                            <td colspan="9" class="text-center text-muted py-4">
                                <i class="fas fa-spinner fa-spin fa-3x mb-3"></i><br>
                                Cargando horarios...
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer">
            <div id="schedulesPagination"></div>
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
                        <input type="hidden" id="maintenanceId" name="maintenance_id" value="{{ $maintenance->id ?? '' }}">
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="schedule_vehicle_id">Vehículo <span class="text-danger">*</span></label>
                                    <select class="form-control" id="schedule_vehicle_id" name="vehicle_id" required>
                                        <option value="">Seleccionar vehículo</option>
                                    </select>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="schedule_driver_id">Responsable <span class="text-danger">*</span></label>
                                    <select class="form-control" id="schedule_driver_id" name="driver_id" required>
                                        <option value="">Seleccionar responsable</option>
                                    </select>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
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
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="schedule_start_time">Hora de Inicio <span class="text-danger">*</span></label>
                                    <input type="time" class="form-control" id="schedule_start_time" name="start_time" required>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="schedule_end_time">Hora de Fin <span class="text-danger">*</span></label>
                                    <input type="time" class="form-control" id="schedule_end_time" name="end_time" required>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="schedule_recurrence_weeks">Recurrencia (semanas)</label>
                                    <input type="number" class="form-control" id="schedule_recurrence_weeks" name="recurrence_weeks" min="1" max="52" value="1">
                                    <small class="form-text text-muted">Cada cuántas semanas se repite (1 = semanal)</small>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="schedule_status">Estado</label>
                                    <select class="form-control" id="schedule_status" name="status">
                                        <option value="scheduled">Programado</option>
                                        <option value="in_progress">En Progreso</option>
                                        <option value="completed">Completado</option>
                                    </select>
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

@stop

@section('css')
    <style>
        .table th, .table td {
            vertical-align: middle;
        }
        .badge {
            font-size: 0.9em;
        }
        .schedule-actions .btn {
            margin: 0 2px;
        }
        .breadcrumb {
            background: none;
            margin-bottom: 0;
            padding: 0;
        }
    </style>
@stop

@section('js')
<script>
    // Variables globales
    const maintenanceId = {{ $maintenance->id ?? 0 }};
    
    console.log('Script loaded, maintenance ID:', maintenanceId);
    
    // Función de prueba
    window.testFunction = function() {
        alert('Functions are working! Maintenance ID: ' + maintenanceId);
        console.log('Test function called, all functions should work');
    };
    
    // FUNCIONES GLOBALES - Disponibles inmediatamente para onclick
    window.showScheduleModal = function(schedule = null) {
        console.log('showScheduleModal called with:', schedule);
        
        if (typeof $ === 'undefined') {
            alert('jQuery no está cargado. Recarga la página.');
            return;
        }
        
        const modal = $('#scheduleModal');
        const form = $('#scheduleForm')[0];
        
        if (!form) {
            alert('Formulario no encontrado. Verifica que el modal esté presente.');
            return;
        }
        
        form.reset();
        $('.form-control').removeClass('is-invalid');
        $('.invalid-feedback').text('');
        $('#scheduleOverlapWarning').hide();

        // Cargar datos para los selects
        loadVehiclesForSelect('#schedule_vehicle_id');
        loadEmployeesForSelect('#schedule_driver_id');

        if (schedule) {
            $('#scheduleModalLabel').text('Editar Horario');
            $('#scheduleId').val(schedule.id);
            
            setTimeout(function() {
                $('#schedule_vehicle_id').val(schedule.vehicle_id);
                $('#schedule_driver_id').val(schedule.driver_id);
                $('#schedule_maintenance_type').val(schedule.maintenance_type);
                $('#schedule_day_of_week').val(schedule.day_of_week);
                $('#schedule_start_time').val(schedule.start_time);
                $('#schedule_end_time').val(schedule.end_time);
                $('#schedule_recurrence_weeks').val(schedule.recurrence_weeks || 1);
                $('#schedule_status').val(schedule.status);
                $('#schedule_description').val(schedule.description);
            }, 800);
        } else {
            $('#scheduleModalLabel').text('Nuevo Horario');
            $('#scheduleId').val('');
            $('#maintenanceId').val(maintenanceId);
        }

        setTimeout(function() {
            modal.modal('show');
        }, 300);
    };

    window.saveSchedule = function() {
        const scheduleId = $('#scheduleId').val();
        const url = scheduleId 
            ? `{{ url('/admin/maintenance-schedules') }}/${scheduleId}`
            : '{{ url("/admin/maintenance-schedules") }}';
        const method = scheduleId ? 'PUT' : 'POST';

        const formData = new FormData($('#scheduleForm')[0]);

        $.ajax({
            url: url,
            type: method,
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'X-Requested-With': 'XMLHttpRequest'
            },
            success: function(response) {
                if (response.success) {
                    $('#scheduleModal').modal('hide');
                    toastr.success(scheduleId ? 'Horario actualizado correctamente' : 'Horario creado correctamente');
                    loadSchedules();
                    loadStatistics();
                } else {
                    toastr.error(response.message || 'Error al guardar el horario');
                }
            },
            error: function(xhr) {
                if (xhr.status === 422) {
                    const errors = xhr.responseJSON.errors;
                    Object.keys(errors).forEach(function(key) {
                        const input = $(`[name="${key}"]`);
                        input.addClass('is-invalid');
                        input.siblings('.invalid-feedback').text(errors[key][0]);
                    });
                } else {
                    toastr.error('Error al guardar el horario');
                }
            }
        });
    };

    window.editSchedule = function(id) {
        $.ajax({
            url: `{{ url('/admin/maintenance-schedules') }}/${id}`,
            type: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            success: function(response) {
                if (response.success) {
                    showScheduleModal(response.data);
                }
            },
            error: function(xhr) {
                toastr.error('Error al cargar el horario');
            }
        });
    };

    window.deleteSchedule = function(id) {
        if (confirm('¿Está seguro de que desea eliminar este horario?')) {
            $.ajax({
                url: `{{ url('/admin/maintenance-schedules') }}/${id}`,
                type: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    'X-Requested-With': 'XMLHttpRequest'
                },
                success: function(response) {
                    if (response.success) {
                        toastr.success('Horario eliminado correctamente');
                        loadSchedules();
                        loadStatistics();
                    }
                },
                error: function(xhr) {
                    toastr.error('Error al eliminar el horario');
                }
            });
        }
    };

    window.viewActivities = function(scheduleId) {
        window.location.href = `{{ url('/admin/maintenance-records') }}?schedule_id=${scheduleId}`;
    };

    window.filterSchedules = function() {
        loadSchedules();
    };

    window.clearFilters = function() {
        $('#dayFilter').val('');
        $('#typeFilter').val('');
        $('#statusFilter').val('');
        loadSchedules();
    };

    console.log('All global functions defined');

    $(document).ready(function() {
        console.log('Document ready, maintenance ID:', maintenanceId);
        
        if (maintenanceId === 0) {
            console.error('Invalid maintenance ID');
            $('#schedulesTableBody').html(`
                <tr>
                    <td colspan="9" class="text-center text-muted py-4">
                        <i class="fas fa-exclamation-triangle fa-3x mb-3"></i><br>
                        Error: Mantenimiento no válido
                    </td>
                </tr>
            `);
            return;
        }
        
        loadSchedules();
        loadStatistics();
        
        $('#dayFilter, #typeFilter, #statusFilter').change(function() {
            loadSchedules();
        });
    });
        
        // Limpiar errores previos
        $('.form-control').removeClass('is-invalid');
        $('.invalid-feedback').text('');
        $('#scheduleOverlapWarning').hide();

        // Cargar datos para los selects ANTES de abrir el modal
        loadVehiclesForSelect('#schedule_vehicle_id');
        loadEmployeesForSelect('#schedule_driver_id');

        if (schedule) {
            $('#scheduleModalLabel').text('Editar Horario');
            $('#scheduleId').val(schedule.id);
            
            // Cargar datos en el formulario después de que se carguen los selects
            setTimeout(function() {
                $('#schedule_vehicle_id').val(schedule.vehicle_id);
                $('#schedule_driver_id').val(schedule.driver_id);
                $('#schedule_maintenance_type').val(schedule.maintenance_type);
                $('#schedule_day_of_week').val(schedule.day_of_week);
                $('#schedule_start_time').val(schedule.start_time);
                $('#schedule_end_time').val(schedule.end_time);
                $('#schedule_recurrence_weeks').val(schedule.recurrence_weeks || 1);
                $('#schedule_status').val(schedule.status);
                $('#schedule_description').val(schedule.description);
            }, 800); // Aumentar tiempo para que carguen los selects
        } else {
            $('#scheduleModalLabel').text('Nuevo Horario');
            $('#scheduleId').val('');
            $('#maintenanceId').val(maintenanceId);
        }

        // Abrir modal después de un pequeño delay para que se carguen los selects
        setTimeout(function() {
            modal.modal('show');
        }, 300);
    };

    window.saveSchedule = function() {
        const scheduleId = $('#scheduleId').val();
        const url = scheduleId 
            ? `{{ url('/admin/maintenance-schedules') }}/${scheduleId}`
            : '{{ url("/admin/maintenance-schedules") }}';
        const method = scheduleId ? 'PUT' : 'POST';

        const formData = new FormData($('#scheduleForm')[0]);

        $.ajax({
            url: url,
            type: method,
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'X-Requested-With': 'XMLHttpRequest'
            },
            success: function(response) {
                if (response.success) {
                    $('#scheduleModal').modal('hide');
                    toastr.success(scheduleId ? 'Horario actualizado correctamente' : 'Horario creado correctamente');
                    loadSchedules();
                    loadStatistics();
                } else {
                    toastr.error(response.message || 'Error al guardar el horario');
                }
            },
            error: function(xhr) {
                if (xhr.status === 422) {
                    const errors = xhr.responseJSON.errors;
                    Object.keys(errors).forEach(function(key) {
                        const input = $(`[name="${key}"]`);
                        input.addClass('is-invalid');
                        input.siblings('.invalid-feedback').text(errors[key][0]);
                    });
                } else {
                    toastr.error('Error al guardar el horario');
                }
            }
        });
    };

    window.editSchedule = function(id) {
        $.ajax({
            url: `{{ url('/admin/maintenance-schedules') }}/${id}`,
            type: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            success: function(response) {
                if (response.success) {
                    showScheduleModal(response.data);
                }
            },
            error: function(xhr) {
                toastr.error('Error al cargar el horario');
            }
        });
    };

    window.deleteSchedule = function(id) {
        if (confirm('¿Está seguro de que desea eliminar este horario?')) {
            $.ajax({
                url: `{{ url('/admin/maintenance-schedules') }}/${id}`,
                type: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    'X-Requested-With': 'XMLHttpRequest'
                },
                success: function(response) {
                    if (response.success) {
                        toastr.success('Horario eliminado correctamente');
                        loadSchedules();
                        loadStatistics();
                    }
                },
                error: function(xhr) {
                    toastr.error('Error al eliminar el horario');
                }
            });
        }
    };

    window.viewActivities = function(scheduleId) {
        window.location.href = `{{ url('/admin/maintenance-records') }}?schedule_id=${scheduleId}`;
    };

    $(document).ready(function() {
        console.log('Page loaded, maintenance ID:', maintenanceId);
        
        if (maintenanceId === 0) {
            console.error('Invalid maintenance ID');
            $('#schedulesTableBody').html(`
                <tr>
                    <td colspan="9" class="text-center text-muted py-4">
                        <i class="fas fa-exclamation-triangle fa-3x mb-3"></i><br>
                        Error: Mantenimiento no válido
                    </td>
                </tr>
            `);
            return;
        }
        
        loadSchedules();
        loadStatistics();
        
        // Event listeners
        $('#dayFilter, #typeFilter, #statusFilter').change(function() {
            loadSchedules();
        });
    });

    // Funciones principales
    function loadSchedules(page = 1) {
        console.log('Loading schedules for maintenance ID:', maintenanceId); // Debug log
        
        if (!maintenanceId) {
            console.error('No maintenance ID provided');
            $('#schedulesTableBody').html(`
                <tr>
                    <td colspan="9" class="text-center text-muted py-4">
                        <i class="fas fa-exclamation-triangle fa-3x mb-3"></i><br>
                        Error: ID de mantenimiento no válido
                    </td>
                </tr>
            `);
            return;
        }

        const dayFilter = $('#dayFilter').val();
        const typeFilter = $('#typeFilter').val();
        const statusFilter = $('#statusFilter').val();

        // Solo incluir filtros que tengan valores
        const data = { page: page };
        
        if (dayFilter && dayFilter !== '' && dayFilter !== null) {
            data.day_of_week = dayFilter;
        }
        if (typeFilter && typeFilter !== '' && typeFilter !== null) {
            data.maintenance_type = typeFilter;
        }
        if (statusFilter && statusFilter !== '' && statusFilter !== null) {
            data.status = statusFilter;
        }

        console.log('Sending data:', data); // Debug log

        $.ajax({
            url: `{{ url("/admin/maintenance") }}/${maintenanceId}/schedules`,
            type: 'GET',
            data: data,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            success: function(response) {
                console.log('Schedules response:', response); // Debug log
                if (response.success) {
                    renderSchedulesTable(response.data);
                    if (response.pagination) {
                        renderPagination(response.pagination, 'loadSchedules');
                    }
                } else {
                    console.error('Response not successful:', response);
                    $('#schedulesTableBody').html(`
                        <tr>
                            <td colspan="9" class="text-center text-muted py-4">
                                <i class="fas fa-exclamation-triangle fa-3x mb-3"></i><br>
                                Error al cargar los horarios
                            </td>
                        </tr>
                    `);
                }
            },
            error: function(xhr) {
                console.error('Error loading schedules:', xhr);
                $('#schedulesTableBody').html(`
                    <tr>
                        <td colspan="9" class="text-center text-muted py-4">
                            <i class="fas fa-exclamation-triangle fa-3x mb-3"></i><br>
                            Error de conexión al cargar los horarios
                        </td>
                    </tr>
                `);
            }
        });
    }

    function loadStatistics() {
        console.log('Loading statistics for maintenance ID:', maintenanceId); // Debug log
        
        if (!maintenanceId) {
            console.error('No maintenance ID for statistics');
            return;
        }

        $.ajax({
            url: `{{ url("/admin/maintenance") }}/${maintenanceId}/schedules`,
            type: 'GET',
            data: {
                stats_only: true
            },
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            success: function(response) {
                console.log('Statistics response:', response); // Debug log
                if (response.success && response.stats) {
                    $('#stat-total-schedules').text(response.stats.total || 0);
                    $('#stat-scheduled').text(response.stats.scheduled || 0);
                    $('#stat-in-progress').text(response.stats.in_progress || 0);
                    $('#stat-completed').text(response.stats.completed || 0);
                }
            },
            error: function(xhr) {
                console.error('Error loading statistics:', xhr);
            }
        });
    }

    function renderSchedulesTable(schedules) {
        const tbody = $('#schedulesTableBody');
        tbody.empty();

        if (schedules.length === 0) {
            tbody.append(`
                <tr>
                    <td colspan="9" class="text-center text-muted py-4">
                        <i class="fas fa-calendar fa-3x mb-3"></i><br>
                        No hay horarios registrados
                    </td>
                </tr>
            `);
            return;
        }

        schedules.forEach(schedule => {
            const statusBadge = getStatusBadge(schedule.status);
            const dayText = getDayText(schedule.day_of_week);
            const typeText = getTypeText(schedule.maintenance_type);
            const timeRange = `${schedule.start_time} - ${schedule.end_time}`;
            const vehicleText = schedule.vehicle ? `${schedule.vehicle.license_plate}` : 'N/A';
            const driverText = schedule.driver ? `${schedule.driver.names} ${schedule.driver.lastnames}` : 'N/A';
            
            tbody.append(`
                <tr>
                    <td>${schedule.id}</td>
                    <td>${dayText}</td>
                    <td>${timeRange}</td>
                    <td><span class="badge badge-info">${typeText}</span></td>
                    <td>${vehicleText}</td>
                    <td>${driverText}</td>
                    <td>Cada ${schedule.recurrence_weeks} semana(s)</td>
                    <td>${statusBadge}</td>
                    <td class="schedule-actions">
                        <button type="button" class="btn btn-sm btn-primary" onclick="editSchedule(${schedule.id})" title="Editar">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-danger" onclick="deleteSchedule(${schedule.id})" title="Eliminar">
                            <i class="fas fa-trash"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-success" onclick="viewActivities(${schedule.id})" title="Ver Actividades">
                            <i class="fas fa-tasks"></i>
                        </button>
                    </td>
                </tr>
            `);
        });
    }

    // Funciones auxiliares (mantenidas aquí para no duplicar)
    function loadVehiclesForSelect(selector) {
        $.ajax({
            url: '{{ route("admin.vehicles.index") }}',
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            success: function(response) {
                console.log('Vehicles response:', response); // Debug log
                let options = '<option value="">Seleccionar Vehículo</option>';
                if(response.data) {
                    response.data.forEach(function(vehicle) {
                        const vehicleText = vehicle.license_plate ? 
                            `${vehicle.license_plate}${vehicle.brand ? ' - ' + vehicle.brand : ''}${vehicle.model ? ' ' + vehicle.model : ''}` :
                            `ID: ${vehicle.id}`;
                        options += `<option value="${vehicle.id}">${vehicleText}</option>`;
                    });
                } else {
                    console.warn('No vehicle data found in response');
                }
                $(selector).html(options);
            },
            error: function(xhr) {
                console.error('Error al cargar vehículos para select:', xhr);
                $(selector).html('<option value="">Error al cargar vehículos</option>');
            }
        });
    }

    function loadEmployeesForSelect(selector) {
        $.ajax({
            url: '{{ route("admin.personnel.employees.api.active") }}',
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            success: function(response) {
                console.log('Employees response:', response); // Debug log
                let options = '<option value="">Seleccionar Empleado</option>';
                if(response.data) {
                    response.data.forEach(function(employee) {
                        options += `<option value="${employee.id}">${employee.name}</option>`;
                    });
                }
                $(selector).html(options);
            },
            error: function(xhr) {
                console.error('Error al cargar empleados para select:', xhr);
                // Fallback: intentar con otra ruta
                $.ajax({
                    url: '{{ route("admin.personnel.employees.index") }}',
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    success: function(response) {
                        console.log('Employees fallback response:', response);
                        let options = '<option value="">Seleccionar Empleado</option>';
                        if(response.data) {
                            response.data.forEach(function(employee) {
                                const employeeName = employee.names ? 
                                    `${employee.names} ${employee.lastnames || ''}` : 
                                    `${employee.name || 'ID: ' + employee.id}`;
                                options += `<option value="${employee.id}">${employeeName}</option>`;
                            });
                        }
                        $(selector).html(options);
                    },
                    error: function(xhr2) {
                        console.error('Error en fallback de empleados:', xhr2);
                        $(selector).html('<option value="">Error al cargar empleados</option>');
                    }
                });
            }
        });
    }
            },
            error: function(xhr) {
                console.error('Error al cargar empleados para select');
            }
        });
    }

    function getStatusBadge(status) {
        const badges = {
            'scheduled': 'badge-warning',
            'in_progress': 'badge-primary',
            'completed': 'badge-success'
        };
        const texts = {
            'scheduled': 'Programado',
            'in_progress': 'En Progreso',
            'completed': 'Completado'
        };
        const badgeClass = badges[status] || 'badge-secondary';
        const badgeText = texts[status] || status;
        return `<span class="badge ${badgeClass}">${badgeText}</span>`;
    }

    function getDayText(dayNumber) {
        const days = {
            '0': 'Domingo',
            '1': 'Lunes', 
            '2': 'Martes',
            '3': 'Miércoles',
            '4': 'Jueves',
            '5': 'Viernes',
            '6': 'Sábado'
        };
        return days[dayNumber] || 'N/A';
    }

    function getTypeText(type) {
        const types = {
            'preventive': 'Preventivo',
            'corrective': 'Correctivo',
            'predictive': 'Predictivo'
        };
        return types[type] || type;
    }

    // Funciones globales para filtros
    window.filterSchedules = function() {
        loadSchedules();
    };

    window.clearFilters = function() {
        $('#dayFilter').val('');
        $('#typeFilter').val('');
        $('#statusFilter').val('');
        loadSchedules();
    };

    function renderPagination(pagination, callback) {
        // Implementar paginación si es necesario
        console.log('Pagination:', pagination);
    }
</script>
@stop