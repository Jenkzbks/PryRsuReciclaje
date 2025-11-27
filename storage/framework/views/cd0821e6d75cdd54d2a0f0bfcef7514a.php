<?php $__env->startSection('title', 'Gestión de Vehículos'); ?>

<?php $__env->startSection('content_header'); ?>
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="h3 mb-1 font-weight-bold">Gestión de Vehículos</h1>
            <p class="text-muted mb-0">Registro y gestión de vehiculos de recolección.</p>
        </div>
        <button type="button" class="btn btn-dark ms-auto" id="btnNuevoVehiculo">
            <i class="fas fa-plus"></i> Agregar Vehículo
        </button>
    </div>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>


    <div class="card-body">
        
        <div class="filters mb-4 p-3 border rounded">
            <div class="row">
                <div class="col-12 col-md-3 mb-2">
                    <label for="searchPlaca" class="form-label">Placa:</label>
                    <input type="text" class="form-control" id="searchPlaca" placeholder="Buscar...">
                </div>
                <div class="col-12 col-md-2 mb-2">
                    <label for="selectMarca" class="form-label">Marca:</label>
                    <select class="form-control" id="selectMarca">
                        <option value="">Seleccione</option>
                        <?php $__currentLoopData = $brands; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $brand): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($brand->id); ?>"><?php echo e($brand->name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="col-12 col-md-2 mb-2">
                    <label for="selectModelo" class="form-label">Modelo:</label>
                    <select class="form-control" id="selectModelo">
                        <option value="">Seleccione</option>
                        <?php $__currentLoopData = $models; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $model): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($model->id); ?>"><?php echo e($model->name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="col-12 col-md-2 mb-2">
                    <label for="selectTipo" class="form-label">Tipo:</label>
                    <select class="form-control" id="selectTipo">
                        <option value="">Seleccione</option>
                        <?php $__currentLoopData = $types; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($type->id); ?>"><?php echo e($type->name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="col-12 col-md-2 mb-2">
                    <label for="selectEstado" class="form-label">Estado:</label>
                    <select class="form-control" id="selectEstado">
                        <option value="">Seleccione</option>
                        <option value="1">Activo</option>
                        <option value="0">Inactivo</option>
                    </select>
                </div>
            </div>
        </div>

        
        <div id="vehicle-grid-container">
            <?php echo $__env->make('admin.vehicles.partials.grid', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        </div>
    </div>

    <div class="card-footer">
        Mostrando <?php echo e($vehicles->firstItem()); ?> a <?php echo e($vehicles->lastItem()); ?> de <?php echo e($vehicles->total()); ?> entradas
        <div class="float-right">
            <?php echo e($vehicles->links()); ?>

        </div>
    </div>

    <div class="modal fade" id="vehicleModal" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalLabel">Formulario de Vehículo</h5>
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

            // --- Helpers para evitar múltiples handlers al recargar HTML dinámicamente ---
            $(document).off('click', '#btnNuevoVehiculo').on('click', '#btnNuevoVehiculo', function() {
                $.ajax({
                    url: "<?php echo e(route('admin.vehicles.create')); ?>",
                    type: 'GET',
                    success: function(response) {
                        $('#vehicleModal .modal-body').html(response);
                        $('#vehicleModal .modal-title').html('Nuevo Vehículo');
                        $('#vehicleModal').modal('show');
                    },
                    error: function() {
                        Swal.fire("Error", "No se pudo cargar el formulario.", "error");
                    }
                });
            });

            // --- Lógica para abrir el modal de EDITAR un vehículo ---
            $(document).off('click', '.btnEditar').on('click', '.btnEditar', function() {
                var vehicleId = $(this).attr('id');
                var url = "<?php echo e(route('admin.vehicles.edit', ':id')); ?>".replace(':id', vehicleId);

                $.ajax({
                    url: url,
                    type: 'GET',
                    success: function(response) {
                        $('#vehicleModal .modal-body').html(response);
                        $('#vehicleModal .modal-title').html('Editar Vehículo');
                        $('#vehicleModal').modal('show');
                    },
                    error: function() {
                        Swal.fire("Error", "No se pudo cargar el formulario de edición.", "error");
                    }
                });
            });

            // Manejo de envío del formulario (crear/editar) dentro del modal
            $(document).off('submit', '#vehicleModal form').on('submit', '#vehicleModal form', function(e) {
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
                        $('#vehicleModal').modal('hide');
                        refreshVehicleGrid();
                        Swal.fire({
                            title: "¡Éxito!",
                            text: response.message || 'Operación realizada correctamente.',
                            icon: "success",
                            timer: 2000,
                            showConfirmButton: false
                        });
                    },
                    error: function(xhr) {
                        var json = xhr.responseJSON || {};
                        var msg = json.message || 'Ocurrió un error.';
                        // si viene un array de errores de validación, concatenarlos
                        if (json.errors) {
                            var errList = [];
                            $.each(json.errors, function(k, v) { errList.push(v.join ? v.join(', ') : v); });
                            msg = errList.join("\n");
                        }
                        Swal.fire("Error", msg, "error");
                    }
                });
            });

            // Manejo de eliminación con confirmación
            $(document).off('submit', '.frmDelete').on('submit', '.frmDelete', function(e) {
                e.preventDefault();
                var form = $(this);
                Swal.fire({
                    title: "¿Estás seguro?",
                    text: "Esta acción no se puede deshacer.",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#d33",
                    cancelButtonColor: "#3085d6",
                    confirmButtonText: "Sí, ¡eliminar!",
                    cancelButtonText: "Cancelar"
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: form.attr('action'),
                            type: form.attr('method'),
                            data: form.serialize(),
                            success: function(response) {
                                refreshVehicleGrid();
                                Swal.fire({
                                    title: "¡Eliminado!",
                                    text: response.message || 'Registro eliminado.',
                                    icon: "success",
                                    timer: 2000,
                                    showConfirmButton: false
                                });
                            },
                            error: function(xhr) {
                                var json = xhr.responseJSON || {};
                                var msg = json.message || 'No se pudo eliminar el registro.';
                                Swal.fire("Error", msg, "error");
                            }
                        });
                    }
                });
            });

            // --- Filtros funcionales ---
            $(document).off('change keyup', '#searchPlaca, #selectMarca, #selectModelo, #selectTipo, #selectEstado')
                .on('change keyup', '#searchPlaca, #selectMarca, #selectModelo, #selectTipo, #selectEstado', function() {
                    filterVehicles();
                });

            // --- Función para filtrar vehículos ---
            function filterVehicles() {
                var filters = {
                    plate: $('#searchPlaca').val(),
                    brand_id: $('#selectMarca').val(),
                    model_id: $('#selectModelo').val(),
                    type_id: $('#selectTipo').val(),
                    status: $('#selectEstado').val(),
                };

                // muestra un indicador pequeño
                var loadingHtml = '<div class="text-center w-100 py-5">Cargando...</div>';
                $('#vehicle-grid-container').html(loadingHtml);

                $.ajax({
                    url: "<?php echo e(route('admin.vehicles.filter')); ?>",
                    type: 'POST',
                    data: { ...filters, _token: '<?php echo e(csrf_token()); ?>' },
                    success: function(response) {
                        // El response es el HTML de la vista parcial
                        $('#vehicle-grid-container').html(response);
                        // Actualizar el footer si es necesario, pero como es paginación, quizás no
                    },
                    error: function() {
                        $('#vehicle-grid-container').html('<div class="text-danger p-3">No se pudo filtrar los resultados.</div>');
                    }
                });
            }

            // --- Función que recarga solo el fragmento de grid y el footer (paginación) ---
            function refreshVehicleGrid() {
                var url = window.location.href.split('?')[0]; // mantener la misma ruta base
                // muestra un indicador pequeño
                var loadingHtml = '<div class="text-center w-100 py-5">Cargando...</div>';
                $('#vehicle-grid-container').html(loadingHtml);

                $.ajax({
                    url: url,
                    type: 'GET',
                    dataType: 'html',
                    data: { full: 1 },
                    success: function(response) {
                        // crear un contenedor temporal para parsear la respuesta completa
                        var temp = $('<div>').html(response);
                        var newGrid = temp.find('#vehicle-grid-container').html();
                        var newFooter = temp.find('.card-footer').first().html();

                        if (newGrid) {
                            $('#vehicle-grid-container').html(newGrid);
                        }
                        if (newFooter) {
                            $('.card-footer').first().html(newFooter);
                        }

                        // Después de reemplazar el HTML, no olvides que los eventos delegados siguen funcionando
                        // Si necesitas re-inicializar plugins (tooltips, etc.) hazlo aquí.
                    },
                    error: function() {
                        $('#vehicle-grid-container').html('<div class="text-danger p-3">No se pudo actualizar la cuadrícula.</div>');
                    }
                });
            }

                // Interceptar clicks en paginación dentro del grid para cargar por AJAX
                $(document).off('click', '#vehicle-grid-container .pagination a').on('click', '#vehicle-grid-container .pagination a', function(e){
                    e.preventDefault();
                    var url = $(this).attr('href');
                    if (!url) return;
                    // indicador de carga
                    $('#vehicle-grid-container').html('<div class="text-center w-100 py-5">Cargando...</div>');
                    $.ajax({
                        url: (url.indexOf('?') === -1 ? url + '?full=1' : url + '&full=1'),
                        type: 'GET',
                        dataType: 'html',
                        success: function(response) {
                            var temp = $('<div>').html(response);
                            var newGrid = temp.find('#vehicle-grid-container').html();
                            var newFooter = temp.find('.card-footer').first().html();
                            if (newGrid) $('#vehicle-grid-container').html(newGrid);
                            if (newFooter) $('.card-footer').first().html(newFooter);
                            // opcional: actualizar la URL sin recargar
                            try { history.replaceState(null, '', url); } catch(e) {}
                        },
                        error: function() {
                            $('#vehicle-grid-container').html('<div class="text-danger p-3">No se pudo cargar la página solicitada.</div>');
                        }
                    });
                });

        });
    </script>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('css'); ?>
<style>
    /* Badge personalizado para la placa (asegura que siempre quede encima de la imagen) */
    .vehicle-plate {
        position: absolute;
        top: 8px;
        right: 8px;
        /* z-index pequeño para no sobrepasar modales, y no capturar clicks */
        z-index: 5;
        pointer-events: none;
        padding: .35rem .5rem;
        font-weight: 600;
        color: #fff;
        background: rgba(0,0,0,0.75);
        border-radius: .375rem;
        box-shadow: 0 1px 4px rgba(0,0,0,0.2);
        font-size: 0.85rem;
        line-height: 1;
        display: inline-block;
    }

    .card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        border: none;
        border-radius: 10px;
        overflow: hidden;
    }
    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    }
    .card-img-top {
        height: 200px;
        object-fit: cover;
        transition: transform 0.3s ease;
    }
    .card:hover .card-img-top {
        transform: scale(1.05);
    }
    .card-title {
        font-size: 1.1rem;
        color: #333;
    }
    .card-body {
        padding: 1rem;
    }
    .card-footer {
        padding: 0.75rem 1rem;
        background-color: #f8f9fa;
    }
    .badge {
        font-size: 0.75rem;
    }
    .filters .form-label {
        font-weight: 500;
    }
    .gap-2 {
        gap: 0.5rem;
    }
</style>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('adminlte::page', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\crist\OneDrive\Escritorio\TADS\PryFinal\PryRsuReciclaje\resources\views/admin/vehicles/index.blade.php ENDPATH**/ ?>