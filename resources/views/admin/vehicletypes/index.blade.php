@extends('adminlte::page')

@section('title', 'Proyecto RSU')

@section('content_header')
    <button type="button" class="btn btn-success float-right" id="btnRegistrar">
        <i class="fas fa-plus"></i> Nuevo tipo de vehículo
    </button>
    <h1>Lista de Tipos de Vehículos</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <table class="table table-striped" id="vehicletypes-table">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Descripción</th>
                        <th>Fecha creación</th>
                        <th>Fecha actualización</th>
                        <th width="10px"></th>
                        <th width="10px"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($vehicleTypes as $vehicleType)
                        <tr>
                            <td>{{ $vehicleType->name }}</td>
                            <td>{{ $vehicleType->description }}</td>
                            <td>{{ $vehicleType->created_at }}</td>
                            <td>{{ $vehicleType->updated_at }}</td>
                            <td>
                                <button class="btn btn-warning btn-sm btnEditar" id="{{ $vehicleType->id }}"><i class="fas fa-pen"></i></button>
                            </td>
                            <td>
                                <form action="{{ route('admin.vehicletypes.destroy', $vehicleType) }}" method="POST" class="frmDelete">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="modal fade" id="modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Formulario de tipos de vehículos</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <!-- El contenido se cargará aquí via AJAX -->
                </div>
            </div>
        </div>
    </div>
@stop

@section('js')
<script>
    $(document).ready(function() {
        // Inicializar DataTables
        $('#vehicletypes-table').DataTable({
            'ajax': '{{ route('admin.vehicletypes.index') }}',
            'columns': [
                { "data": "name" },
                { "data": "description" },
                { "data": "created_at" },
                { "data": "updated_at" },
                { 
                    "data": "edit", 
                    "orderable": false, 
                    "searchable": false 
                },
                { 
                    "data": "delete", 
                    "orderable": false, 
                    "searchable": false 
                }
            ],
            'language': {
                "url": "https://cdn.datatables.net/plug-ins/1.10.25/i18n/Spanish.json"
            }
        });

        // Botón Registrar - CON REFRESH TABLE
        $('#btnRegistrar').click(function() {
            $.ajax({
                url: "{{ route('admin.vehicletypes.create') }}",
                type: 'GET',
                success: function(response) {
                    $('#modal .modal-body').html(response);
                    $('#modal .modal-title').html('Nuevo Tipo de Vehículo');
                    $('#modal').modal({
                        backdrop: 'static',
                        keyboard: false
                    }).modal('show');

                    // Configurar el envío del formulario de CREATE
                    $('#modal form').off('submit').on('submit', function(e) {
                        e.preventDefault();
                        var form = $(this);
                        var formData = new FormData(this);

                        $.ajax({
                            url: form.attr('action'),
                            type: form.attr('method'),
                            data: formData,
                            processData: false,
                            contentType: false,
                            success: function(response) {
                                $('#modal').modal('hide');
                                refreshTable(); // ← ESTA ES LA LÍNEA IMPORTANTE
                                Swal.fire({
                                    title: "Proceso Exitoso!",
                                    text: response.message,
                                    icon: "success",
                                    draggable: true
                                });
                            },
                            error: function(response) {
                                var error = response.responseJSON;
                                Swal.fire({
                                    title: "Error!",
                                    text: error.message,
                                    icon: "error",
                                    draggable: true
                                });
                            }
                        });
                    });
                }
            });
        });

        // Botón Editar - CON REFRESH TABLE
        $(document).on('click', '.btnEditar', function() {
            var id = $(this).attr('id');
            $.ajax({
                url: "{{ route('admin.vehicletypes.edit', 'id') }}".replace('id', id),
                type: 'GET',
                success: function(response) {
                    $('#modal .modal-body').html(response);
                    $('#modal .modal-title').html('Editar Tipo de Vehículo');
                    $('#modal').modal('show');

                    // Configurar el envío del formulario de UPDATE
                    $('#modal form').off('submit').on('submit', function(e) {
                        e.preventDefault();
                        var form = $(this);
                        var formData = new FormData(this);

                        $.ajax({
                            url: form.attr('action'),
                            type: form.attr('method'),
                            data: formData,
                            processData: false,
                            contentType: false,
                            success: function(response) {
                                $('#modal').modal('hide');
                                refreshTable(); // ← ESTA ES LA LÍNEA IMPORTANTE
                                Swal.fire({
                                    title: "Proceso Exitoso!",
                                    text: response.message,
                                    icon: "success",
                                    draggable: true
                                });
                            },
                            error: function(response) {
                                var error = response.responseJSON;
                                Swal.fire({
                                    title: "Error!",
                                    text: error.message,
                                    icon: "error",
                                    draggable: true
                                });
                            }
                        });
                    });
                }
            });
        });

        // Eliminar - YA TIENE REFRESH TABLE (está correcto)
        $(document).on('click', '.frmDelete', function(e) {
            e.preventDefault();
            var form = $(this);
            Swal.fire({
                title: "¿Estás seguro de eliminar?",
                text: "Esto no se puede deshacer!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Si, eliminar",
                cancelButtonText: "Cancelar"
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: form.attr('action'),
                        type: form.attr('method'),
                        data: form.serialize(),
                        success: function(response) {
                            refreshTable(); // ← Este sí lo tienes
                            Swal.fire({
                                title: "Proceso Exitoso!",
                                text: "Tipo de vehículo eliminado correctamente",
                                icon: "success",
                                draggable: true
                            });
                        },
                        error: function(response) {
                            var error = response.responseJSON;
                            Swal.fire({
                                title: "Error!",
                                text: error.message,
                                icon: "error",
                                draggable: true
                            });
                        }
                    });
                }
            });
        });
    });

    // Función para refrescar la tabla
    function refreshTable() {
        var table = $("#vehicletypes-table").DataTable();
        table.ajax.reload(null, false); // false = mantiene la paginación actual
    }
</script>
@endsection