<?php
    $days = [
        '0' => 'Domingo',
        '1' => 'Lunes',
        '2' => 'Martes',
        '3' => 'Miércoles',
        '4' => 'Jueves',
        '5' => 'Viernes',
        '6' => 'Sábado'
    ];
?>

<?php $__env->startSection('title', 'Gestionar Actividades - Horario #' . $schedule->id); ?>

<?php $__env->startSection('content_header'); ?>
    <div class="row">
        <div class="col-sm-6">
            <h1>
                <i class="fas fa-tasks mr-2"></i>
                Gestionar Actividades
            </h1>
        </div>
        <div class="col-sm-6 text-right">
            <a href="<?php echo e(url('/admin/maintenance/' . $schedule->maintenance_id . '/schedules')); ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left mr-1"></i>
                Volver a Horarios
            </a>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <!-- Información del Horario -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card card-info">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-info-circle mr-2"></i>
                        Información del Horario
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <strong>Mantenimiento:</strong><br>
                            <?php echo e($schedule->maintenance->name ?? 'N/A'); ?>

                        </div>
                        <div class="col-md-3">
                            <strong>Día:</strong><br>
                            <?php echo e($days[$schedule->day_of_week] ?? 'N/A'); ?>

                        </div>
                        <div class="col-md-3">
                            <strong>Horario:</strong><br>
                            <?php
                                $startTime = $schedule->start_time;
                                $endTime = $schedule->end_time;
                                // Si son objetos Carbon o datetime, formatear solo la hora
                                if ($startTime instanceof \Carbon\Carbon || $startTime instanceof \DateTime) {
                                    $startTime = $startTime->format('H:i');
                                } elseif (strlen($startTime) > 5) {
                                    // Si es string con formato datetime, extraer solo la hora
                                    $startTime = date('H:i', strtotime($startTime));
                                }
                                if ($endTime instanceof \Carbon\Carbon || $endTime instanceof \DateTime) {
                                    $endTime = $endTime->format('H:i');
                                } elseif (strlen($endTime) > 5) {
                                    $endTime = date('H:i', strtotime($endTime));
                                }
                            ?>
                            <?php echo e($startTime); ?> - <?php echo e($endTime); ?>

                        </div>
                        <div class="col-md-3">
                            <strong>Estado:</strong><br>
                            <?php
                                $statusText = [
                                    'scheduled' => 'Programado',
                                    'in_progress' => 'En Progreso', 
                                    'completed' => 'Completado'
                                ];
                                $statusClass = [
                                    'scheduled' => 'badge-warning',
                                    'in_progress' => 'badge-primary',
                                    'completed' => 'badge-success'
                                ];
                            ?>
                            <span class="badge <?php echo e($statusClass[$schedule->status] ?? 'badge-secondary'); ?>">
                                <?php echo e($statusText[$schedule->status] ?? $schedule->status); ?>

                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Estadísticas -->
    <div class="row mb-4">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3 id="stat-total-activities">0</h3>
                    <p>Total de Actividades</p>
                </div>
                <div class="icon">
                    <i class="fas fa-tasks"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3 id="stat-completed-activities">0</h3>
                    <p>Actividades Completadas</p>
                </div>
                <div class="icon">
                    <i class="fas fa-check-circle"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3 id="stat-pending-activities">0</h3>
                    <p>Actividades Pendientes</p>
                </div>
                <div class="icon">
                    <i class="fas fa-clock"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3 id="stat-overdue-activities">0</h3>
                    <p>Actividades Vencidas</p>
                </div>
                <div class="icon">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla de Actividades -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-list mr-2"></i>
                        Lista de Actividades
                    </h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#activityModal">
                            <i class="fas fa-plus mr-1"></i>
                            Nueva Actividad
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="activitiesTable" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Fecha</th>
                                    <th>Descripción</th>
                                    <th>Estado</th>
                                    <th>Imagen</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="activitiesTableBody">
                                <!-- Los datos se cargarán dinámicamente -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para Nueva/Editar Actividad -->
    <div class="modal fade" id="activityModal" tabindex="-1" role="dialog" aria-labelledby="activityModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="activityModalLabel">Nueva Actividad</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="activityForm" enctype="multipart/form-data">
                    <div class="modal-body">
                        <input type="hidden" id="schedule_id" name="schedule_id" value="<?php echo e($schedule->id); ?>">
                        <input type="hidden" id="activity_id" name="activity_id">
                        
                        <div class="form-group">
                            <label for="maintenance_date" class="required">Fecha de Mantenimiento</label>
                            <input type="date" class="form-control" id="maintenance_date" name="maintenance_date" required>
                            <small class="form-text text-muted">
                                La fecha debe estar entre <?php echo e($schedule->maintenance->start_date->format('d/m/Y')); ?> y <?php echo e($schedule->maintenance->end_date->format('d/m/Y')); ?>

                                y debe ser un <?php echo e($days[$schedule->day_of_week] ?? 'día válido'); ?>.
                            </small>
                        </div>
                        
                        <div class="form-group">
                            <label for="descripcion" class="required">Descripción</label>
                            <textarea class="form-control" id="descripcion" name="descripcion" rows="4" 
                                      placeholder="Describe la actividad de mantenimiento realizada..." required></textarea>
                        </div>
                        
                        <div class="form-group">
                            <label for="image">Imagen (opcional)</label>
                            <input type="file" class="form-control-file" id="image" name="image" accept="image/*">
                            <small class="form-text text-muted">Formatos permitidos: JPG, PNG, GIF (máximo 2MB)</small>
                        </div>
                        
                        <div id="current-image" style="display: none;">
                            <label>Imagen actual:</label><br>
                            <img id="current-image-preview" src="" alt="Imagen actual" style="max-width: 200px; max-height: 200px;">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save mr-1"></i>
                            Guardar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('css'); ?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
<style>
    .required::after {
        content: " *";
        color: red;
    }
    
    .activity-actions .btn {
        margin-right: 5px;
    }
    
    .activity-image {
        max-width: 60px;
        max-height: 60px;
        border-radius: 4px;
        cursor: pointer;
    }
</style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('js'); ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script>
    let scheduleId = <?php echo e($schedule->id); ?>;
    
    $(document).ready(function() {
        console.log('Document ready - Schedule ID:', scheduleId);
        
        // Configurar CSRF token para todas las peticiones AJAX
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        
        loadActivities();
        loadStatistics();

        // Configurar fechas mínima y máxima
        $('#maintenance_date').attr('min', '<?php echo e($schedule->maintenance->start_date->format('Y-m-d')); ?>');
        $('#maintenance_date').attr('max', '<?php echo e($schedule->maintenance->end_date->format('Y-m-d')); ?>');

        // Evento para enviar formulario
        $('#activityForm').on('submit', function(e) {
            e.preventDefault();
            saveActivity();
        });

        // Limpiar modal al cerrar
        $('#activityModal').on('hidden.bs.modal', function() {
            $('#activityForm')[0].reset();
            $('#activity_id').val('');
            $('#activityModalLabel').text('Nueva Actividad');
            $('#current-image').hide();
        });
    });

    function loadActivities() {
        console.log('Loading activities for schedule ID:', scheduleId);
        
        $.ajax({
            url: `<?php echo e(route('admin.maintenance-records.index')); ?>`,
            type: 'GET',
            data: {
                schedule_id: scheduleId
            },
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            success: function(response) {
                console.log('Activities response:', response);
                if (response.success) {
                    renderActivitiesTable(response.data);
                } else {
                    console.error('Response not successful:', response);
                    showError('Error al cargar las actividades');
                }
            },
            error: function(xhr) {
                console.error('Error loading activities:', xhr);
                showError('Error de conexión al cargar las actividades');
            }
        });
    }

    function loadStatistics() {
        console.log('Loading statistics for schedule ID:', scheduleId);
        
        $.ajax({
            url: `<?php echo e(route('admin.maintenance-records.index')); ?>`,
            type: 'GET', 
            data: {
                schedule_id: scheduleId,
                stats_only: true
            },
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            success: function(response) {
                if (response.success && response.stats) {
                    $('#stat-total-activities').text(response.stats.total || 0);
                    $('#stat-completed-activities').text(response.stats.completed || 0);
                    $('#stat-pending-activities').text(response.stats.pending || 0);
                    $('#stat-overdue-activities').text(response.stats.overdue || 0);
                }
            },
            error: function(xhr) {
                console.error('Error loading statistics:', xhr);
            }
        });
    }

    function renderActivitiesTable(activities) {
        const tbody = $('#activitiesTableBody');
        tbody.empty();

        console.log('Rendering activities:', activities); // Debug log

        if (activities.length === 0) {
            tbody.append(`
                <tr>
                    <td colspan="6" class="text-center text-muted py-4">
                        <i class="fas fa-tasks fa-3x mb-3"></i><br>
                        No hay actividades registradas para este horario
                    </td>
                </tr>
            `);
            return;
        }

        activities.forEach((activity, index) => {
            console.log(`Activity ${index + 1}:`, activity); // Debug log para cada actividad
            
            // Determinar si tiene datos (descripción o imagen)
            const descripcionText = activity.descripcion ? activity.descripcion.trim() : '';
            const hasData = (descripcionText !== '' && descripcionText !== 'null') || 
                          activity.image_url || activity.image_path || activity.image;
            
            const statusBadge = getActivityStatusBadge(activity.maintenance_date, hasData);
            const date = new Date(activity.maintenance_date).toLocaleDateString('es-ES');
            
            // Debug detallado
            console.log(`Activity ${activity.id}:`, {
                descripcion: descripcionText,
                image_url: activity.image_url,
                hasData: hasData
            });
            
            // Manejar imagen correctamente - probar múltiples campos
            const imagePath = activity.image_url || activity.image_path || activity.image;
            
            // Construir URL completa de la imagen
            const imageUrl = imagePath ? `<?php echo e(asset('storage')); ?>/${imagePath}` : null;
            
            const imageColumn = imageUrl ? 
                `<img src="${imageUrl}" alt="Imagen" class="activity-image" onclick="showImageModal('${imageUrl}')" onerror="console.error('Error loading image:', '${imageUrl}')">` :
                '<span class="text-muted">Sin imagen</span>';
            
            // Botón check - cambia de color según estado pero siempre clickeable
            const checkButton = hasData ?
                `<button type="button" class="btn btn-sm btn-success" onclick="markAsCompleted(${activity.id})" title="Completado - Click para editar">
                    <i class="fas fa-check"></i>
                </button>` :
                `<button type="button" class="btn btn-sm btn-outline-success" onclick="markAsCompleted(${activity.id})" title="Marcar como Completado">
                    <i class="fas fa-check"></i>
                </button>`;
            
            tbody.append(`
                <tr>
                    <td>${activity.id}</td>
                    <td>${date}</td>
                    <td>${activity.descripcion}</td>
                    <td>${statusBadge}</td>
                    <td class="text-center">${imageColumn}</td>
                    <td class="activity-actions">
                        ${checkButton}
                        <button type="button" class="btn btn-sm btn-primary" onclick="editActivity(${activity.id})" title="Editar">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-danger" onclick="deleteActivity(${activity.id})" title="Eliminar">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
            `);
        });
    }

    function saveActivity() {
        const formData = new FormData($('#activityForm')[0]);
        const isEditing = $('#activity_id').val() !== '';
        const url = isEditing ? 
            `<?php echo e(url('/admin/maintenance-records')); ?>/${$('#activity_id').val()}` :
            `<?php echo e(route('admin.maintenance-records.store')); ?>`;
        const method = isEditing ? 'PUT' : 'POST';

        if (isEditing) {
            formData.append('_method', 'PUT');
        }

        // Debug: mostrar qué se está enviando
        console.log('Form data being sent:');
        for (let pair of formData.entries()) {
            console.log(pair[0] + ': ' + pair[1]);
        }
        console.log('URL:', url);
        console.log('Method:', method);

        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'X-Requested-With': 'XMLHttpRequest'
            },
            success: function(response) {
                console.log('Success response:', response);
                if (response.success) {
                    toastr.success(isEditing ? 'Actividad actualizada correctamente' : 'Actividad creada correctamente');
                    $('#activityModal').modal('hide');
                    loadActivities();
                    loadStatistics();
                } else {
                    console.log('Response not successful:', response);
                    showValidationErrors(response.errors || response.message);
                }
            },
            error: function(xhr) {
                console.error('Error saving activity:', xhr);
                console.log('Response text:', xhr.responseText);
                console.log('Status:', xhr.status);
                
                if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                    console.log('Validation errors:', xhr.responseJSON.errors);
                    showValidationErrors(xhr.responseJSON.errors);
                } else {
                    showError('Error al guardar la actividad: ' + (xhr.responseJSON?.message || 'Error desconocido'));
                }
            }
        });
    }

    function editActivity(id) {
        console.log('Editing activity:', id);
        
        // Cargar datos de la actividad
        $.ajax({
            url: `<?php echo e(url('/admin/maintenance-records')); ?>/${id}`,
            type: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            success: function(response) {
                if (response.success && response.data) {
                    const activity = response.data;
                    
                    // Llenar el formulario
                    $('#activity_id').val(activity.id);
                    $('#maintenance_date').val(activity.maintenance_date);
                    $('#descripcion').val(activity.descripcion);
                    
                    // Mostrar imagen actual si existe
                    if (activity.image_url || activity.image_path) {
                        const imagePath = activity.image_url || activity.image_path;
                        $('#current-image').show();
                        $('#current-image-preview').attr('src', `/storage/${imagePath}`);
                    } else {
                        $('#current-image').hide();
                    }
                    
                    // Cambiar título del modal
                    $('#activityModalLabel').text('Editar Actividad');
                    
                    // Mostrar modal
                    $('#activityModal').modal('show');
                } else {
                    toastr.error('Error al cargar los datos de la actividad');
                }
            },
            error: function(xhr) {
                console.error('Error loading activity:', xhr);
                toastr.error('Error al cargar la actividad');
            }
        });
    }

    function markAsCompleted(id) {
        Swal.fire({
            title: '¿Marcar como completado?',
            text: "Se agregará una descripción de completado a la actividad",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, completar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                // Cambiar solo el badge visualmente
                const row = $(`button[onclick="markAsCompleted(${id})"]`).closest('tr');
                const statusCell = row.find('td').eq(3); // Columna Estado: ID(0), Fecha(1), Descripción(2), Estado(3)
                statusCell.html('<span class="badge badge-success">Completado</span>');
                
                Swal.fire({
                    icon: 'success',
                    title: '¡Completado!',
                    text: 'Estado actualizado',
                    timer: 1500,
                    showConfirmButton: false
                });
            }
        });
    }

    function deleteActivity(id) {
        if (confirm('¿Está seguro de que desea eliminar esta actividad? Esta acción no se puede deshacer.')) {
            console.log('Deleting activity:', id);
            
            $.ajax({
                url: `<?php echo e(url('/admin/maintenance-records')); ?>/${id}`,
                type: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    'X-Requested-With': 'XMLHttpRequest'
                },
                success: function(response) {
                    console.log('Delete response:', response);
                    if (response.success) {
                        toastr.success('Actividad eliminada correctamente');
                        loadActivities();
                        loadStatistics();
                    } else {
                        toastr.error('Error al eliminar la actividad: ' + (response.message || 'Error desconocido'));
                    }
                },
                error: function(xhr) {
                    console.error('Error deleting activity:', xhr);
                    toastr.error('Error al eliminar la actividad: ' + (xhr.responseJSON?.message || 'Error de conexión'));
                }
            });
        }
    }

    function getActivityStatusBadge(maintenanceDate, hasData) {
        const today = new Date();
        today.setHours(0, 0, 0, 0);
        
        const activityDate = new Date(maintenanceDate);
        activityDate.setHours(0, 0, 0, 0);
        
        if (activityDate > today) {
            return '<span class="badge badge-warning">Pendiente</span>';
        } else if (activityDate.getTime() === today.getTime()) {
            return hasData ? 
                '<span class="badge badge-success">Completado</span>' : 
                '<span class="badge badge-info">En Progreso</span>';
        } else {
            return hasData ? 
                '<span class="badge badge-success">Completado</span>' : 
                '<span class="badge badge-danger">Vencido</span>';
        }
    }

    function showValidationErrors(errors) {
        if (typeof errors === 'object') {
            Object.values(errors).forEach(function(error) {
                if (Array.isArray(error)) {
                    error.forEach(function(message) {
                        toastr.error(message);
                    });
                } else {
                    toastr.error(error);
                }
            });
        } else {
            toastr.error(errors);
        }
    }

    function showError(message) {
        toastr.error(message);
    }

    function showImageModal(imagePath) {
        // Crear modal dinámico para mostrar imagen
        const imageModal = `
            <div class="modal fade" id="imageViewModal" tabindex="-1" role="dialog">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Imagen de Actividad</h5>
                            <button type="button" class="close" data-dismiss="modal">
                                <span>&times;</span>
                            </button>
                        </div>
                        <div class="modal-body text-center">
                            <img src="/storage/${imagePath}" alt="Imagen de actividad" style="max-width: 100%; max-height: 70vh;">
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        // Remover modal anterior si existe
        $('#imageViewModal').remove();
        
        // Agregar nuevo modal
        $('body').append(imageModal);
        
        // Mostrar modal
        $('#imageViewModal').modal('show');
        
        // Remover modal cuando se cierre
        $('#imageViewModal').on('hidden.bs.modal', function() {
            $(this).remove();
        });
    }
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('adminlte::page', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\Operador\Documents\PryRsuReciclaje\resources\views/maintenance/activities/index.blade.php ENDPATH**/ ?>