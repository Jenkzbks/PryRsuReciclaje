<?php $__env->startSection('title', 'Horarios de Mantenimiento'); ?>

<?php $__env->startSection('content_header'); ?>
    <div class="d-flex justify-content-between align-items-center" style="margin-bottom: 0;">
        <h1 class="mb-0" style="font-weight:bold; font-size:1.5rem;"><?php echo e(strtoupper($maintenance->name ?? '')); ?></h1>
        <div>
            <a href="<?php echo e(route('admin.maintenances.index')); ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Volver
            </a>
            <button type="button" class="btn btn-success ml-2" id="btnRegistrar">
                <i class="fas fa-plus"></i> Nuevo horario
            </button>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <div class="card">
        <div class="card-body">
            <table class="table table-striped table-bordered align-middle" id="shedules-table" style="width:100%">
                <thead>
                    <tr>
                        <th>Día</th>
                        <th>Vehículo</th>
                        <th>Responsable</th>
                        <th>Tipo</th>
                        <th>Inicio</th>
                        <th>Fin</th>
                        <th width="60px" class="text-center align-middle">Ver</th>
                        <th width="60px" class="text-center align-middle">Editar</th>
                        <th width="60px" class="text-center align-middle">Eliminar</th>
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
                    <h5 class="modal-title" id="exampleModalLabel">Formulario de horario</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body"></div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('js'); ?>
<script>
    // Cargar moment.js si no está presente
    if (typeof moment === 'undefined') {
        var script = document.createElement('script');
        script.src = 'https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js';
        document.head.appendChild(script);
    }

    $(document).ready(function() {
        $('#shedules-table').DataTable({
            'ajax': window.location.href,
            'columns': [
                { "data": "day_of_week" },
                { "data": "vehicle" },
                { "data": "responsable" },
                { "data": "maintenance_type" },
                {
                    "data": "start_time",
                    "render": function(data) {
                        if (!data) return '';
                        if (typeof moment !== 'undefined') {
                            return moment(data, 'HH:mm:ss').format('hh:mm a');
                        }
                        return data;
                    }
                },
                {
                    "data": "end_time",
                    "render": function(data) {
                        if (!data) return '';
                        if (typeof moment !== 'undefined') {
                            return moment(data, 'HH:mm:ss').format('hh:mm a');
                        }
                        return data;
                    }
                },
                { "data": "act", "orderable": false, "searchable": false, "className": "text-center align-middle" },
                { "data": "edit", "orderable": false, "searchable": false, "className": "text-center align-middle" },
                { "data": "delete", "orderable": false, "searchable": false, "className": "text-center align-middle" }
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
                    $('#modal .modal-title').html('Nuevo Horario');
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
                    $('#modal .modal-title').html('Editar Horario');
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

        $(document).on('click', '.frmDelete', function(e) {
            e.preventDefault();
            var form = $(this);
            Swal.fire({
                title: "¿Estás seguro de eliminar?",
                text: "Se eliminará el horario y todas las fechas generadas asociadas. ¡Esto no se puede deshacer!",
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
                }
            });
        });
    });

    function refreshTable() {
        var table = $("#shedules-table").DataTable();
        table.ajax.reload(null, false);
    }
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('adminlte::page', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\crist\OneDrive\Escritorio\TADS\PryFinal\PryRsuReciclaje\resources\views/admin/examen03/maintenance_shedules/index.blade.php ENDPATH**/ ?>