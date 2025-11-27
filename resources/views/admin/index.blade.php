@extends('adminlte::page')

@section('title', 'Programación')

@section('content_header')
    <h1>Programación</h1>
@stop

@section('content')

<div class="card p-4">

    {{-- FILTROS SUPERIORES --}}
    <form method="GET" action="{{ route('admin.index') }}" id="filterForm">
        <div class="row mb-4">
            <div class="col-md-4">
                <label for="date">Seleccione una fecha:</label>
                <input type="date" class="form-control" id="date" name="date" value="{{ request('date', date('Y-m-d')) }}">
            </div>

            <div class="col-md-4">
                <label for="shift_id">Seleccione un turno:</label>
                <select class="form-control" id="shift_id" name="shift_id">
                    <option value="">Seleccione un turno</option>
                    @foreach($shifts as $shift)
                        <option value="{{ $shift->id }}" {{ request('shift_id') == $shift->id ? 'selected' : '' }}>
                            {{ $shift->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-4 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">
                    Buscar programación
                </button>
            </div>
        </div>
    </form>


    {{-- TARJETAS SUPERIORES --}}
    <div class="row text-center mb-4">

        <div class="col-md-3">
            <div class="card shadow-sm p-3">
                <h3><i class="fas fa-user"></i> <span id="attendances">{{ $totalAttendances ?? 0 }}</span></h3>
                <span>Asistencias</span>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm p-3">
                <h3><i class="fas fa-truck"></i> <span id="completeGroups">{{ $completeGroups ?? 0 }}</span></h3>
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
                <h3><i class="fas fa-times text-danger"></i> <span id="incompleteZones">{{ $incompleteZones ?? 0 }}</span></h3>
                <span>Faltan</span>
            </div>
        </div>

    </div>

    {{-- LEYENDA --}}
    <div class="card p-3 mb-4">
        <h5>Leyenda de colores:</h5>
        <p><span class="badge bg-success">■</span> Grupo completo y listo para operar</p>
        <p><span class="badge bg-danger">■</span> Faltan integrantes por llegar o confirmar asistencia</p>
    </div>


    {{-- ZONAS --}}
    <div class="row" id="zonesContainer">
        @include('admin.zones')
    </div>

</div>

{{-- MODAL PARA EDITAR PROGRAMACIÓN --}}
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

@stop

@section('css')
<style>
    .card {
        border-radius: 12px !important;
    }
</style>
@stop

@section('js')
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
        // Filtrar nuevo personal por tipo
        $('#personal_actual').on('change', function(){
            var selectedType = $(this).find('option:selected').data('type');
            $('#nuevo_personal option').hide();
            if (selectedType) {
                $('#nuevo_personal option[data-type="' + selectedType + '"]').show();
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
@stop
