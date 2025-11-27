<?php $__env->startSection('title', 'Mantenimientos'); ?>

<?php $__env->startSection('content_header'); ?>
    <button type="button" class="btn btn-success float-right" id="btnRegistrar">
        <i class="fas fa-plus"></i> Nuevo mantenimiento
    </button>
    <h1>Lista de Mantenimientos</h1>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <div class="card">
        <div class="card-body">
            <table class="table table-striped table-bordered align-middle" id="maintenances-table" style="width:100%">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Fecha Inicio</th>
                        <th>Fecha Fin</th>
                        <th width="70px" class="text-center align-middle">Horarios</th>
                        <th width="70px" class="text-center align-middle">Editar</th>
                        <th width="70px" class="text-center align-middle">Eliminar</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>

    <div class="modal fade" id="modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Formulario de mantenimiento</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                </div>
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
        $('#maintenances-table').DataTable({
            'ajax': '<?php echo e(route('admin.maintenances.index')); ?>',
            'columns': [
                { "data": "name" },
                {
                    "data": "start_date",
                    "render": function(data, type, row) {
                        if (!data) return '';
                        if (typeof moment !== 'undefined') {
                            return moment(data, 'YYYY-MM-DD').format('DD/MM/YYYY');
                        }
                        return data;
                    }
                },
                {
                    "data": "end_date",
                    "render": function(data, type, row) {
                        if (!data) return '';
                        if (typeof moment !== 'undefined') {
                            return moment(data, 'YYYY-MM-DD').format('DD/MM/YYYY');
                        }
                        return data;
                    }
                },
                { "data": "calendar", "orderable": false, "searchable": false, "className": "text-center align-middle" },
                { "data": "edit", "orderable": false, "searchable": false, "className": "text-center align-middle" },
                { "data": "delete", "orderable": false, "searchable": false, "className": "text-center align-middle" }
            ],
            'language': {
                "url": "https://cdn.datatables.net/plug-ins/1.10.25/i18n/Spanish.json"
            }
        });

        $('#btnRegistrar').click(function() {
            $.ajax({
                url: "<?php echo e(route('admin.maintenances.create')); ?>",
                type: 'GET',
                success: function(response) {
                    $('#modal .modal-body').html(response);
                    $('#modal .modal-title').html('Nuevo Mantenimiento');
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
                url: "<?php echo e(route('admin.maintenances.edit', 'id')); ?>".replace('id', id),
                type: 'GET',
                success: function(response) {
                    $('#modal .modal-body').html(response);
                    $('#modal .modal-title').html('Editar Mantenimiento');
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
        var table = $("#maintenances-table").DataTable();
        table.ajax.reload(null, false); 
    }
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('adminlte::page', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH F:\USAT 2025-2\topicos\PryFINAL\PryRsuReciclaje\resources\views/admin/examen03/maintenances/index.blade.php ENDPATH**/ ?>