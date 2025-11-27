<?php $__env->startSection('title', 'Proyecto RSU'); ?>

<?php $__env->startSection('content_header'); ?>
    <button type="button" class="btn btn-success float-right" id="btnRegistrar">
        <i class="fas fa-plus"></i> Nuevo modelo
    </button>
    <h1>Lista de Modelos</h1>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <div class="card">
        <div class="card-body">
            <table class="table table-striped" id="brandmodels-table">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Descripción</th>
                        <th>Marca</th>
                        <th>Fecha creación</th>
                        <th>Fecha actualización</th>
                        <th width="10px"></th>
                        <th width="10px"></th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $brandModels; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $model): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td><?php echo e($model->name); ?></td>
                            <td><?php echo e($model->description); ?></td>
                            <td><?php echo e($model->brand->name); ?></td>
                            <td><?php echo e($model->created_at); ?></td>
                            <td><?php echo e($model->updated_at); ?></td>
                            <td>
                                <button class="btn btn-warning btn-sm btnEditar" id="<?php echo e($model->id); ?>"><i class="fas fa-pen"></i></button>
                            </td>
                            <td>
                                <form action="<?php echo e(route('admin.brandmodels.destroy', $model)); ?>" method="POST" class="frmDelete">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('DELETE'); ?>
                                    <button type="submit" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="modal fade" id="modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Formulario de modelos</h5>
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
    $(document).ready(function() {
        $('#brandmodels-table').DataTable({
            'ajax': '<?php echo e(route('admin.brandmodels.index')); ?>',
            'columns': [
                { 
                    "data": "name" 
                },
                { 
                    "data": "description" 
                },
                { 
                    "data": "brand_name" 
                },
                { 
                    "data": "created_at" 
                },
                { 
                    "data": "updated_at" 
                },
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

        $('#btnRegistrar').click(function() {
            $.ajax({
                url: "<?php echo e(route('admin.brandmodels.create')); ?>",
                type: 'GET',
                success: function(response) {
                    $('#modal .modal-body').html(response);
                    $('#modal .modal-title').html('Nuevo Modelo');
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

        $(document).on('click', '.btnEditar', function() {
            var id = $(this).attr('id');
            $.ajax({
                url: "<?php echo e(route('admin.brandmodels.edit', 'id')); ?>".replace('id', id),
                type: 'GET',
                success: function(response) {
                    $('#modal .modal-body').html(response);
                    $('#modal .modal-title').html('Editar Modelo');
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
                                    text: error.message,
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

    function refreshTable() {
        var table = $("#brandmodels-table").DataTable();
        table.ajax.reload(null, false); 
    }
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('adminlte::page', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\Pc\Documents\PryRsuReciclaje\resources\views/admin/brandmodels/index.blade.php ENDPATH**/ ?>