@extends('adminlte::page')

@section('title', 'Proyecto RSU')

@section('content_header')
    <button type="button" class="btn btn-success float-right" id="btnRegistrar">
        <i class="fas fa-plus"></i> Nuevo turno
    </button>
    <h1>Lista de Turnos</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <table class="table table-striped" id="shifts-table">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Descripción</th>
                        <th>Hora inicio</th>
                        <th>Hora fin</th>
                        <th>Fecha creación</th>
                        <th>Fecha actualización</th>
                        <th width="10px"></th>
                        <th width="10px"></th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>

    <div class="modal fade" id="modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
               <div class="modal-header text-white" style="background:#0b4f6f;">
                    <h5 class="modal-title" id="exampleModalLabel">Formulario de turnos</h5>
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
        $('#shifts-table').DataTable({
            'ajax': '{{ route('admin.shifts.index') }}',
            'columns': [
                { "data": "name" },
                { "data": "description" },
                { "data": "hora_in" },
                { "data": "hora_out" },
                { "data": "created_at" },
                { "data": "updated_at" },
                { "data": "edit", "orderable": false, "searchable": false },
                { "data": "delete", "orderable": false, "searchable": false }
            ],
            'language': {
                "url": "https://cdn.datatables.net/plug-ins/1.10.25/i18n/Spanish.json"
            }
        });

        $('#btnRegistrar').click(function() {
            $.ajax({
                url: "{{ route('admin.shifts.create') }}",
                type: 'GET',
                success: function(response) {
                    $('#modal .modal-body').html(response);
                    $('#modal .modal-title').html('Nuevo Turno');
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
                url: "{{ route('admin.shifts.edit', 'id') }}".replace('id', id),
                type: 'GET',
                success: function(response) {
                    $('#modal .modal-body').html(response);
                    $('#modal .modal-title').html('Editar Turno');
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
    });

    function refreshTable() {
        var table = $("#shifts-table").DataTable();
        table.ajax.reload(null, false);
    }
</script>
@endsection
