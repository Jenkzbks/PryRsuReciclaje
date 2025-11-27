<?php $__env->startSection('title', 'Gestión de Colores'); ?>

<?php $__env->startSection('content_header'); ?>
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="h3 mb-1 font-weight-bold">Gestión de Colores</h1>
            <p class="text-muted mb-0">Registro y gestión de colores.</p>
        </div>
        <button type="button" class="btn btn-dark ms-auto" id="btnNuevo">
            <i class="fas fa-plus"></i> Agregar Color
        </button>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="tblColores">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Código</th>
                        <th>Vista Previa</th>
                        <th>Descripción</th>
                        <th>Acciones</th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="colorModal" tabindex="-1" role="dialog" aria-labelledby="colorModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="colorModalLabel"></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="frmColor" method="POST">
                        <?php echo csrf_field(); ?>
                        <input type="hidden" id="color_id" name="color_id">
                        <div id="method_field"></div>

                        <?php echo $__env->make('admin.colors.template.form', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn btn-primary" form="frmColor" id="btnGuardar">Guardar</button>
                </div>
            </div>
        </div>
    </div>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('js'); ?>
    <script>
        $(document).ready(function() {
            // Inicialización de DataTables
            const table = $('#tblColores').DataTable({
                processing: true,
                serverSide: true,
                ajax: "<?php echo e(route('admin.colors.index')); ?>",
                language: {
                    url: "//cdn.datatables.net/plug-ins/1.11.5/i18n/es-ES.json"
                },
                columns: [
                    { data: 'id', name: 'id' },
                    { data: 'name', name: 'name' },
                    { data: 'code', name: 'code' },
                    { data: 'preview', name: 'preview', orderable: false, searchable: false },
                    { data: 'description', name: 'description' },
                    { data: 'acciones', name: 'acciones', orderable: false, searchable: false,
                        render: function ( data, type, row, meta ) {
                            return row.edit + ' ' + row.delete;
                        }
                    }
                ]
            });

            $('#btnNuevo').click(function() {
                $('#color_id').val('');
                $('#frmColor').trigger("reset");
                $('#method_field').html('');
                $('#colorModalLabel').html("Nuevo Color");
                $('#colorModal').modal('show');
            });

            $('body').on('click', '.btnEditar', function() {
                const color_id = $(this).attr('id');
                $.get("<?php echo e(url('admin/colors')); ?>" + '/' + color_id, function (data) {
                    $('#colorModalLabel').html("Editar Color");
                    $('#color_id').val(data.id);
                    $('#name').val(data.name);
                    $('#code').val(data.code);
                    $('#description').val(data.description);
                    $('#method_field').html('<?php echo method_field("PUT"); ?>');
                    $('#colorModal').modal('show');
                })
            });

            $('#frmColor').submit(function(e) {
                e.preventDefault();
                $('#btnGuardar').html('Guardando...').prop('disabled', true);

                let url = "<?php echo e(route('admin.colors.store')); ?>";
                let method = 'POST';
                const color_id = $('#color_id').val();

                if (color_id) {
                    url = "<?php echo e(url('admin/colors')); ?>" + '/' + color_id;
                }

                $.ajax({
                    data: $(this).serialize(),
                    url: url,
                    type: method,
                    dataType: 'json',
                    success: function (data) {
                        $('#frmColor').trigger("reset");
                        $('#colorModal').modal('hide');
                        table.draw();
                        Swal.fire('¡Éxito!', data.message, 'success');
                    },
                    error: function (data) {
                        console.log('Error:', data);
                        const errors = data.responseJSON.errors;
                        let errorMsg = '<ul>';
                        $.each(errors, function(key, value){
                            errorMsg += '<li>' + value[0] + '</li>';
                        });
                        errorMsg += '</ul>';
                        Swal.fire('Error', errorMsg, 'error');
                    },
                    complete: function() {
                        $('#btnGuardar').html('Guardar').prop('disabled', false);
                    }
                });
            });

            $(document).on('submit', '.frmDelete', function(e) {
                e.preventDefault();
                Swal.fire({
                    title: '¿Estás seguro?',
                    text: "¡No podrás revertir esto!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Sí, ¡bórralo!',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        this.submit();
                    }
                })
            });
        });
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('adminlte::page', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\crist\OneDrive\Escritorio\TADS\PryFinal\PryRsuReciclaje\resources\views/admin/colors/index.blade.php ENDPATH**/ ?>