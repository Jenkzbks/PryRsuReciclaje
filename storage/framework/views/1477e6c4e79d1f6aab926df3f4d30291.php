<?php $__env->startSection('title', 'Crear Ruta'); ?>
<?php $__env->startSection('content_header'); ?>
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="h3 mb-1 font-weight-bold">Crear Ruta</h1>
            <p class="text-muted mb-0">Llene el formulario para agregar la ruta.</p>
        </div>
       
    </div>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
    <div class="container-fluid">
        <div class="row align-items-stretch">
            <!-- Formulario -->
            <div class="col-md-6 d-flex">
                <div class="card shadow-sm w-100">
                    <div class="card-body">
                        <?php echo Form::open(['route' => 'admin.routes.store']); ?>

                            <?php echo $__env->make('admin.routes_zone.template.form', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                            <a href="<?php echo e(route('admin.routes.index')); ?>" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Regresar
                            </a>
                            <button type="submit" class="btn btn-success"><i class='fas fa-save'></i> Registrar</button>
                        <?php echo Form::close(); ?>

                    </div>
                </div>
            </div>
            <!-- Mapa -->
            <div class="col-md-6 d-flex">
                <div class="card shadow-sm w-100">
                    <div class="card-body">
                        <?php echo $__env->make('admin.routes_zone.template.map', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
<?php $__env->stopSection(); ?>

<?php echo $__env->make('adminlte::page', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH F:\USAT 2025-2\topicos\PryFINAL\PryRsuReciclaje\resources\views/admin/routes_zone/create.blade.php ENDPATH**/ ?>