@extends('adminlte::page')

@section('title', 'Grupos de Personal')

@section('content_header')
    <button type="button" class="btn btn-success float-right" id="btnRegistrar">
        <i class="fas fa-plus"></i> Nuevo Grupo
    </button>
    <h1>Lista de Grupos de Personal</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <table class="table table-striped" id="groups-table">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Zona</th>
                        <th>Turno</th>
                        <th>Vehículo</th>
                        <th>Días</th>
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
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="exampleModalLabel">Formulario Grupo de Personal</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body"></div>
            </div>
        </div>
    </div>
@stop

@section('js')
<script>
    $(document).ready(function() {
        $('#groups-table').DataTable({
            'ajax': '{{ route('admin.personnel.employeegroups.index') }}',
            'columns': [
                { "data": "name" },
                { "data": "zone" },
                { "data": "shift" },
                { "data": "vehicle" },
                { "data": "days" },
                { "data": "created_at" },
                { "data": "updated_at" },
                { "data": "edit", "orderable": false, "searchable": false },
                { "data": "delete", "orderable": false, "searchable": false }
            ],
            'language': { "url": "https://cdn.datatables.net/plug-ins/1.10.25/i18n/Spanish.json" }
        });

        $('#btnRegistrar').click(function() {
            $.ajax({
                url: "{{ route('admin.personnel.employeegroups.create') }}",
                type: 'GET',
                success: function(response) {
                    $('#modal .modal-body').html(response);
                    $('#modal .modal-title').html('Nuevo Grupo de Personal');
                    $('#modal').modal({ backdrop: 'static', keyboard: false }).modal('show');

                    bindFormSubmit();
                }
            });
        });

        $(document).on('click', '.btnEditar', function() {
            var id = $(this).attr('id');
            $.ajax({
                url: "{{ route('admin.personnel.employeegroups.edit', 'id') }}".replace('id', id),
                type: 'GET',
                success: function(response) {
                    $('#modal .modal-body').html(response);
                    $('#modal .modal-title').html('Editar Grupo de Personal');
                    $('#modal').modal('show');
                    bindFormSubmit();
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
                            Swal.fire({ title: "Proceso Exitoso!", text: response.message, icon: "success" });
                        },
                        error: function(response) { var error = response.responseJSON; Swal.fire({ title: "Error!", text: error.message, icon: "error" }); }
                    });
                }
            });
        });

        function bindFormSubmit() {
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
                        Swal.fire({ title: "Proceso Exitoso!", text: response.message, icon: "success" });
                    },
                    error: function(response) { var error = response.responseJSON; Swal.fire({ title: "Error!", text: error.message, icon: "error" }); }
                });
            });
        }

        function refreshTable() { var table = $('#groups-table').DataTable(); table.ajax.reload(null, false); }
    });
</script>
@endsection
