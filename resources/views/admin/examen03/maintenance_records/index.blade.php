@extends('adminlte::page')

@section('title', 'Fechas de Ejecución de Mantenimiento')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center" style="margin-bottom: 0;">
        <h1 class="mb-0" style="font-weight:bold; font-size:1.5rem;">
            {{ strtoupper($maintenance->name ?? '') }} – {{ strtoupper($schedule->day_of_week ?? '') }} - {{ $vehicle->name ?? '' }}
        </h1>
        <div>
            <a href="{{ route('admin.maintenance_shedules.index', ['maintenance' => $maintenance->id]) }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Volver
            </a>
            <button type="button" class="btn btn-success ml-2" id="btnRegistrar">
                <i class="fas fa-plus"></i> Nueva fecha
            </button>
        </div>
    </div>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <table class="table table-striped table-bordered align-middle" id="records-table" style="width:100%">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Observación</th>
                        <th>Imagen</th>
                        <th width="60px" class="text-center align-middle">Editar</th>
                        <th width="60px" class="text-center align-middle">Estado</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>

    <div class="modal fade" id="modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Formulario de fecha de ejecución</h5>
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
    // Cargar moment.js si no está presente
    if (typeof moment === 'undefined') {
        var script = document.createElement('script');
        script.src = 'https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js';
        document.head.appendChild(script);
    }

    $(document).ready(function() {
        $('#records-table').DataTable({
            'ajax': window.location.href,
            'columns': [
                {
                    "data": "maintenance_date",
                    "render": function(data) {
                        if (!data) return '';
                        if (typeof moment !== 'undefined') {
                            return moment(data, 'YYYY-MM-DD').format('DD/MM/YYYY');
                        }
                        return data;
                    }
                },
                { "data": "descripcion" },
                {
                    "data": "image_url",
                    "orderable": false,
                    "searchable": false,
                    "render": function(data) {
                        if (!data) return '';
                        if (!data) return '';
                        return '<img src="' + data + '" alt="Imagen" class="img-table-preview" style="width:100px;height:100px;object-fit:cover;border-radius:10px;border:2px solid #ddd;display:block;margin:auto;cursor:pointer;">';
                    }
                },
                { "data": "edit", "orderable": false, "searchable": false, "className": "text-center align-middle" },
                {
                    "data": null,
                    "orderable": false,
                    "searchable": false,
                    "className": "text-center align-middle",
                    "render": function(data, type, row) {
                        if (row.estado == 1 || row.estado === true) {
                            return '<span class="toggle-estado" data-id="'+row.id+'" data-estado="1" title="Realizado" style="font-size:1.5em;color:green;cursor:pointer;">&#10004;</span>';
                        } else {
                            return '<span class="toggle-estado" data-id="'+row.id+'" data-estado="0" title="No realizado" style="font-size:1.5em;color:red;cursor:pointer;">&#10008;</span>';
                        }
                    }
                }
            ],
            'language': {
                "url": "https://cdn.datatables.net/plug-ins/1.10.25/i18n/Spanish.json"
            }
        });

        $('#btnRegistrar').click(function() {
            $.ajax({
                url: window.location.pathname + '/create',
                type: 'GET',
                success: function(response) {
                    $('#modal .modal-body').html(response);
                    $('#modal .modal-title').html('Nueva Fecha de Ejecución');
                    $('#modal').modal({
                        backdrop: 'static',
                        keyboard: false
                    }).modal('show');

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
                                    text: error && error.message ? error.message : 'Ocurrió un error inesperado.',
                                    icon: "error",
                                    draggable: true
                                });
                            }
                        });
                    });
                }
            });
        });

        $(document).on('click', '.btnEditar', function() {
            var id = $(this).attr('id');
            $.ajax({
                url: window.location.pathname + '/' + id + '/edit',
                type: 'GET',
                success: function(response) {
                    $('#modal .modal-body').html(response);
                    $('#modal .modal-title').html('Editar Fecha de Ejecución');
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
                                    text: error && error.message ? error.message : 'Ocurrió un error inesperado.',
                                    icon: "error",
                                    draggable: true
                                });
                            }
                        });
                    });
                },
                error: function(xhr) {
                    console.log('Error al cargar formulario:', xhr.responseText);
                }
            });
        });

        // Cambiar estado dinámicamente al hacer clic en el ícono
        $(document).on('click', '.toggle-estado', function() {
            var id = $(this).data('id');
            var estadoActual = $(this).data('estado');
            var nuevoEstado = estadoActual == 1 ? 0 : 1;
            $.ajax({
                url: window.location.pathname + '/' + id + '/toggle-estado',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    estado: nuevoEstado
                },
                success: function(response) {
                    refreshTable();
                },
                error: function(response) {
                    var error = response.responseJSON;
                    Swal.fire({
                        title: "Error!",
                        text: error && error.message ? error.message : 'Ocurrió un error inesperado.',
                        icon: "error",
                        draggable: true
                    });
                }
            });
        });
    });

    function refreshTable() {
        var table = $("#records-table").DataTable();
        table.ajax.reload(null, false);
    }
</script>
<!-- Modal para vista de imagen grande -->
<div class="modal fade" id="imageModal" tabindex="-1" role="dialog" aria-labelledby="imageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="imageModalLabel">Vista de imagen</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center">
                <img id="modalImagePreview" src="" alt="Imagen grande" style="max-width:100%;max-height:70vh;border-radius:12px;">
            </div>
        </div>
    </div>
</div>
<script>
        $(document).on('click', '.img-table-preview', function() {
                var src = $(this).attr('src');
                $('#modalImagePreview').attr('src', src);
                $('#imageModal').modal('show');
        });
</script>
@endsection
