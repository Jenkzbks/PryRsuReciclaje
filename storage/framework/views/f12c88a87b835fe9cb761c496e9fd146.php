<?php $__env->startSection('title', 'Editar Zona'); ?>

<?php $__env->startSection('content_header'); ?>
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="h3 mb-1 font-weight-bold">Editar Zona</h1>
            <p class="text-muted mb-0">Modifique los datos de la zona.</p>
        </div>
        <a href="<?php echo e(route('admin.zonesjenkz.index')); ?>" class="btn btn-dark"><i class="fas fa-arrow-left"></i> Regresar</a>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="row align-items-stretch">
        <!-- Formulario -->
        <div class="col-md-6 d-flex">
            <div class="card shadow-sm w-100">
                <div class="card-body">
                    <?php echo Form::model($zone, ['route' => ['admin.zonesjenkz.update', $zone->id], 'method' => 'PUT', 'id' => 'zone-form']); ?>

                        <?php echo $__env->make('admin.zones_jenkz.template.form', ['departments' => $departments], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                        <div class="mt-3">
                            <a href="<?php echo e(route('admin.zonesjenkz.index')); ?>" class="btn btn-danger"> <i class="fas fa-ban mr-1"></i> Cancelar</a>
                            <button type="submit" class="btn btn-primary"> <i class="fas fa-save mr-1"></i> Actualizar</button>
                        </div>
                    <?php echo Form::close(); ?>

                </div>
            </div>
        </div>
        <!-- Mapa -->
        <div class="col-md-6 d-flex">
            <div class="card shadow-sm w-100">
                <div class="card-body">
                    <?php echo $__env->make('admin.zones_jenkz.template.map', ['zone' => $zone], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('js'); ?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function() {
    $('#zone-form').on('submit', function(e) {
        e.preventDefault();
        var form = $(this);
        var formData = new FormData(this);
        // Si coords se maneja como variable JS, agregarla manualmente:
        if (typeof window.zoneCoords !== 'undefined') {
            formData.delete('coords[]'); // Por si acaso
            window.zoneCoords.forEach(function(coord, i) {
                formData.append('coords['+i+'][lat]', coord.lat);
                formData.append('coords['+i+'][lng]', coord.lng);
            });
        }
        // Agregar token CSRF
        formData.append('_token', '<?php echo e(csrf_token()); ?>');
        // Forzar método POST y agregar _method=PUT
        formData.append('_method', 'PUT');
        $.ajax({
            url: form.attr('action'),
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
            },
            success: function(response) {
                Swal.fire({
                    icon: 'success',
                    title: '¡Zona actualizada!',
                    text: 'La zona se actualizó correctamente.',
                    timer: 1800,
                    showConfirmButton: false
                }).then(() => {
                    window.location.href = "<?php echo e(route('admin.zonesjenkz.index')); ?>";
                });
            },
            error: function(xhr) {
                if (xhr.status === 422) {
                    var errors = xhr.responseJSON.errors;
                    var msg = '';
                    Object.keys(errors).forEach(function(key) {
                        msg += errors[key].join('<br>') + '<br>';
                    });
                    Swal.fire({
                        icon: 'error',
                        title: 'Errores de validación',
                        html: msg
                    });
                } else {
                    let errorMsg = 'Ocurrió un error inesperado.';
                    if (xhr.responseText) {
                        errorMsg += '<br><small>' + xhr.responseText.substring(0, 500) + '</small>';
                    }
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        html: errorMsg
                    });
                }
            }
        });
    });
});
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('adminlte::page', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\crist\OneDrive\Escritorio\TADS\PryFinal\PryRsuReciclaje\resources\views/admin/zones_jenkz/edit.blade.php ENDPATH**/ ?>