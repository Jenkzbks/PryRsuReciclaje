<?php $__env->startSection('title', 'Programación'); ?>

<?php $__env->startSection('content_header'); ?>
    <h1>Programación</h1>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>

<div class="card p-4">

    
    <form method="GET" action="<?php echo e(route('admin.index')); ?>" id="filterForm">
        <div class="row mb-4">
            <div class="col-md-4">
                <label for="date">Seleccione una fecha:</label>
                <input type="date" class="form-control" id="date" name="date" value="<?php echo e(request('date', date('Y-m-d'))); ?>">
            </div>

            <div class="col-md-4">
                <label for="shift_id">Seleccione un turno:</label>
                <select class="form-control" id="shift_id" name="shift_id">
                    <option value="">Seleccione un turno</option>
                    <?php $__currentLoopData = $shifts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $shift): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($shift->id); ?>" <?php echo e(request('shift_id') == $shift->id ? 'selected' : ''); ?>>
                            <?php echo e($shift->name); ?>

                        </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>

            <div class="col-md-4 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">
                    Buscar programación
                </button>
            </div>
        </div>
    </form>


    
    <div class="row text-center mb-4">

        <div class="col-md-3">
            <div class="card shadow-sm p-3">
                <h3><i class="fas fa-user"></i> <span id="attendances"><?php echo e($totalAttendances ?? 0); ?></span></h3>
                <span>Asistencias</span>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm p-3">
                <h3><i class="fas fa-truck"></i> <span id="completeGroups"><?php echo e($completeGroups ?? 0); ?></span></h3>
                <span>Grupos completos</span>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm p-3">
                <h3><i class="fas fa-walking"></i> 0</h3>
                <span>Apoyos disponibles</span>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm p-3">
                <h3><i class="fas fa-times text-danger"></i> <span id="incompleteZones"><?php echo e($incompleteZones ?? 0); ?></span></h3>
                <span>Faltan</span>
            </div>
        </div>

    </div>

    
    <div class="card p-3 mb-4">
        <h5>Leyenda de colores:</h5>
        <p><span class="badge bg-success">■</span> Grupo completo y listo para operar</p>
        <p><span class="badge bg-danger">■</span> Faltan integrantes por llegar o confirmar asistencia</p>
    </div>


    
    <div class="row" id="zonesContainer">
        <?php echo $__env->make('admin.zones', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    </div>

</div>


<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel">Editar Programación</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="editModalBody">
                <!-- Contenido cargado dinámicamente -->
            </div>
        </div>
    </div>
</div>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('css'); ?>
<style>
    .card {
        border-radius: 12px !important;
    }
</style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('js'); ?>
<script>
$(document).ready(function(){
    $('#filterForm').on('submit', function(e){
        e.preventDefault();
        var formData = $(this).serialize();
        $.ajax({
            url: $(this).attr('action'),
            type: 'GET',
            data: formData,
            success: function(data){
                $('#zonesContainer').html(data.zones);
                $('#attendances').text(data.attendances);
                $('#completeGroups').text(data.completeGroups);
                $('#incompleteZones').text(data.incompleteZones);
            }
        });
    });

    // Event delegation para botones de edición en zonas dinámicas
    $('#zonesContainer').on('click', '.edit-scheduling-btn', function(){
        var url = $(this).data('url');
        $.ajax({
            url: url,
            type: 'GET',
            data: { ajax: 1 }, // Indicar que es AJAX
            success: function(data){
                $('#editModalBody').html(data);
                $('#editModal').modal('show');
                // Inicializar eventos después de cargar
                initializeModalEvents();
            },
            error: function(xhr, status, error){
                console.log('Error al cargar la edición:', xhr.status, error, xhr.responseText);
                alert('Error al cargar la edición: ' + xhr.status + ' ' + error + '. Revisa la consola del navegador para más detalles.');
            }
        });
    });

    function initializeModalEvents() {
        // Filtrar nuevo personal por tipo y excluir empleados ocupados
        $('#personal_actual').on('change', function(){
            var selectedType = $(this).find('option:selected').data('type');
            $('#nuevo_personal option').hide();
            if (selectedType) {
                $('#nuevo_personal option[data-type="' + selectedType + '"]').each(function(){
                    // Mostrar solo si no está ocupado (data-busy != 1)
                    var busy = $(this).data('busy');
                    if (typeof busy === 'undefined' || busy != 1) {
                        $(this).show();
                    }
                });
            }
            $('#nuevo_personal').val('');
        });

        // Agregar cambio de turno
        $('#add_shift_change').on('click', function(){
            var newShiftText = $('#shift_select option:selected').text();
            var currentShift = $('#current_shift').val();
            var key = 'turno';
            if (newShiftText && newShiftText !== 'Seleccione un nuevo turno') {
                if ($('[name="motivos['+key+']"]').length) {
                    alert('Ya existe un cambio de turno registrado.');
                    return;
                }
                addChangeRow('Turno', currentShift, newShiftText, key);
            }
        });

        // Agregar cambio de vehículo
        $('#add_vehicle_change').on('click', function(){
            var newVehicleText = $('#vehicle_select option:selected').text();
            var currentVehicle = $('#current_vehicle').val();
            var key = 'vehiculo';
            if (newVehicleText && newVehicleText !== 'Seleccione un nuevo vehículo') {
                if ($('[name="motivos['+key+']"]').length) {
                    alert('Ya existe un cambio de vehículo registrado.');
                    return;
                }
                addChangeRow('Vehículo', currentVehicle, newVehicleText, key);
            }
        });

        // Agregar cambio de personal
        $('#add_personal_change').on('click', function(){
            var $selectedCurrent = $('#personal_actual option:selected');
            var $selectedNew = $('#nuevo_personal option:selected');
            var currentEmpText = $selectedCurrent.text();
            var newEmpText = $selectedNew.text();
            var roleKey = $selectedCurrent.data('role');
            var motivoKey = roleKey ? ('personal-' + roleKey) : null;
            var newEmpId = $selectedNew.val();
            if (currentEmpText && newEmpText && currentEmpText !== 'Seleccione un personal' && newEmpText !== 'Seleccione un nuevo personal') {
                if (motivoKey && $('[name="motivos['+motivoKey+']"]').length) {
                    alert('Ya existe un cambio para este rol registrado.');
                    return;
                }
                addChangeRow('Personal', currentEmpText, newEmpText, motivoKey, { role_field: roleKey, new_value_id: newEmpId });
            }
        });

        // Enviar form vía AJAX
        $('#editModal form').on('submit', function(e){
            e.preventDefault();
            var formData = new FormData(this);
            $.ajax({
                url: $(this).attr('action'),
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') || $('input[name="_token"]').val()
                },
                success: function(response){
                    if (response.success) {
                        $('#editModal').modal('hide');
                        // Refrescar las zonas
                        $('#filterForm').trigger('submit');
                        alert(response.message || 'Cambios guardados correctamente.');
                    }
                },
                error: function(xhr){
                    alert('Error al guardar cambios: ' + xhr.responseText);
                }
            });
        });
    }

    function addChangeRow(type, anterior, nuevo, key, meta) {
        // Build motivo select by cloning template inside modal
        var $motivoTemplate = $('#editModalBody').find('#motivo_template select').first();
        var $motivoSelect = $motivoTemplate.clone();
        if (key) {
            $motivoSelect.attr('name', 'motivos['+key+']');
            $motivoSelect.attr('required', 'required');
        }

        var $nota = $('<textarea>').addClass('form-control form-control-sm mt-1').attr('rows',2);
        if (key) {
            $nota.attr('name', 'notas['+key+']');
        }

        var $notasContainer = $('<div>').append($motivoSelect).append($nota);

        var $row = $('<tr>');
        if (meta && meta.role_field) {
            $row.attr('data-role-field', meta.role_field);
        }
        $row.append($('<td>').text(type));
        $row.append($('<td>').text(anterior));
        $row.append($('<td>').text(nuevo));
        $row.append($('<td>').append($notasContainer));
        $row.append($('<td>').html('<button type="button" class="btn btn-danger btn-sm remove-change">Eliminar</button>'));

        $('#tabla-cambios').append($row);

        // If meta includes a role_field and new_value_id, create hidden input to send the new personnel
        if (meta && meta.role_field && meta.new_value_id) {
            // Remove existing hidden input for this role if any
            $('#editModal form').find('input[name="' + meta.role_field + '"]').remove();
            var $hidden = $('<input>').attr('type','hidden').attr('name', meta.role_field).val(meta.new_value_id);
            $('#editModal form').append($hidden);
        }
    }

    // Event delegation para eliminar filas
    $(document).on('click', '.remove-change', function(){
        var $tr = $(this).closest('tr');
        var roleField = $tr.attr('data-role-field');
        if (roleField) {
            // Remove hidden input for this role
            $('#editModal form').find('input[name="' + roleField + '"]').remove();
        }
        $tr.remove();
    });
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('adminlte::page', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\crist\OneDrive\Escritorio\TADS\PryFinal\PryRsuReciclaje\resources\views/admin/index.blade.php ENDPATH**/ ?>