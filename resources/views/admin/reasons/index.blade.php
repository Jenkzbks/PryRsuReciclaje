@extends('adminlte::page')

@section('title', 'Proyecto RSU')

@section('content_header')
    <button type="button" class="btn btn-success float-right" id="btnRegistrar">
        <i class="fas fa-plus"></i> Nuevo motivo
    </button>
    <h1>Lista de Motivos</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <table class="table table-striped table-bordered align-middle" id="reasons-table" style="width:100%">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <!-- <th>Descripción</th> -->
                        <th>Estado</th>
                        <th width="70px" class="text-center align-middle">Editar</th>
                        <th width="70px" class="text-center align-middle">Eliminar</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>

    <div class="modal fade" id="modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
               <div class="modal-header text-white" style="background:#072d3f;">
                    <h5 class="modal-title" id="exampleModalLabel">Formulario de motivos</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                </div>
            </div>
        </div>
    </div>
@stop

@section('js')
<script>
    $(document).ready(function() {
        $('#reasons-table').DataTable({
            'ajax': '{{ route('admin.reasons.index') }}',
            'columns': [
                { data: 'name' },
                // { data: 'description' },
                { data: 'active', render: function(data) {
                    return data ? '<span class="badge badge-success">Activo</span>' : '<span class="badge badge-secondary">Inactivo</span>';
                }},
                { data: 'edit', orderable: false, searchable: false, className: 'text-center align-middle' },
                { data: 'delete', orderable: false, searchable: false, className: 'text-center align-middle' },
            ]
        });
    });

    function refreshTable() {
        var table = $("#reasons-table").DataTable();
        table.ajax.reload(null, false);
    }

    $('#btnRegistrar').click(function() {
        $.ajax({
            url: "{{ route('admin.reasons.create') }}",
            type: 'GET',
            success: function(response) {
                $('#modal .modal-body').html(response);
                $('#modal .modal-title').html('Nuevo Motivo');
                $('#modal').modal({ backdrop: 'static', keyboard: false }).modal('show');

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
                            refreshTable();
                            Swal.fire({ title: "Proceso Exitoso!", text: response.message, icon: "success", draggable: true });
                        },
                        error: function(response) {
                            var error = response.responseJSON;
                            Swal.fire({ title: "Error!", text: error.message, icon: "error", draggable: true });
                        }
                    });
                });
            }
        });
    });

    $(document).on('click', '.btnEditar', function() {
        var id = $(this).attr('id');
        $.ajax({
            url: "{{ route('admin.reasons.edit', 'id') }}".replace('id', id),
            type: 'GET',
            success: function(response) {
                $('#modal .modal-body').html(response);
                $('#modal .modal-title').html('Editar Motivo');
                $('#modal').modal('show');

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
                            refreshTable();
                            Swal.fire({ title: "Proceso Exitoso!", text: response.message, icon: "success", draggable: true });
                        },
                        error: function(response) {
                            var error = response.responseJSON;
                            Swal.fire({ title: "Error!", text: error.message, icon: "error", draggable: true });
                        }
                    });
                });
            },
            error: function(xhr) {
                console.log('Error al cargar formulario:', xhr.responseText);
            }
        });
    });

    $(document).on('submit', '.frmDelete', function(e) {
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
                        refreshTable();
                        Swal.fire({ title: "Proceso Exitoso!", text: response.message, icon: "success", draggable: true });
                    },
                    error: function(response) {
                        var error = response.responseJSON;
                        Swal.fire({ title: "Error!", text: error.message, icon: "error", draggable: true });
                    }
                });
            }
        });
    });
</script>
@stop
