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
            if (newShiftText && newShiftText !== 'Seleccione un nuevo turno') {
                addChangeRow('Turno', currentShift, newShiftText, '');
            }
        });

        // Agregar cambio de vehículo
        $('#add_vehicle_change').on('click', function(){
            var newVehicleText = $('#vehicle_select option:selected').text();
            var currentVehicle = $('#current_vehicle').val();
            if (newVehicleText && newVehicleText !== 'Seleccione un nuevo vehículo') {
                addChangeRow('Vehículo', currentVehicle, newVehicleText, '');
            }
        });

        // Agregar cambio de personal
        $('#add_personal_change').on('click', function(){
            var currentEmpText = $('#personal_actual option:selected').text();
            var newEmpText = $('#nuevo_personal option:selected').text();
            if (currentEmpText && newEmpText && currentEmpText !== 'Seleccione un personal' && newEmpText !== 'Seleccione un nuevo personal') {
                addChangeRow('Personal', currentEmpText, newEmpText, '');
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

    function addChangeRow(type, anterior, nuevo, notas) {
        var row = '<tr>' +
            '<td>' + type + '</td>' +
            '<td>' + anterior + '</td>' +
            '<td>' + nuevo + '</td>' +
            '<td>' + notas + '</td>' +
            '<td><button type="button" class="btn btn-danger btn-sm remove-change">Eliminar</button></td>' +
            '</tr>';
        $('#tabla-cambios').append(row);
    }

    // Event delegation para eliminar filas
    $(document).on('click', '.remove-change', function(){
        $(this).closest('tr').remove();
    });
});
</script>
<?php $__env->stopSection(); ?>
<?php /**PATH C:\Users\crist\OneDrive\Escritorio\TADS\PryFinal\PryRsuReciclaje\resources\views/admin/partials/scheduling_js.blade.php ENDPATH**/ ?>