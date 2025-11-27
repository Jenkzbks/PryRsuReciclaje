

<?php $__env->startSection('title', 'Editar Cambio Masivo'); ?>

<?php $__env->startSection('content_header'); ?>
    <h1>Editar Cambio Masivo</h1>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="card">
    <form method="POST" action="<?php echo e(route('admin.schedulings.store')); ?>">
        <?php echo csrf_field(); ?>

        <div class="card-body">

            <?php if($errors->any()): ?>
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li><?php echo e($e); ?></li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                </div>
            <?php endif; ?>

            <div class="row">

                
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Fecha de Inicio *</label>
                        <input type="date" name="from" class="form-control"
                               value="<?php echo e($massiveChange->from); ?>" required>
                    </div>
                </div>

                
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Fecha de Fin *</label>
                        <input type="date" name="to" class="form-control"
                               value="<?php echo e($massiveChange->to); ?>" required>
                    </div>
                </div>

                
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Zonas (Opcional)</label>
                        <select name="zones[]" class="form-control" multiple>
                            <?php $__currentLoopData = $zones; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $z): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($z->id); ?>"
                                    <?php if(in_array($z->id, $massiveChange->zones ?? [])): echo 'selected'; endif; ?>>
                                    <?php echo e($z->name); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                        <small class="text-muted">Dejar vacío para aplicar a todas las zonas</small>
                    </div>
                </div>

                
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Tipo de Cambio *</label>
                        <select name="type" class="form-control" required>
                            <option value="Cambio de Conductor"
                                <?php if($massiveChange->type === 'Cambio de Conductor'): echo 'selected'; endif; ?>>
                                Cambio de Conductor
                            </option>
                            <option value="Reemplazo Temporal"
                                <?php if($massiveChange->type === 'Reemplazo Temporal'): echo 'selected'; endif; ?>>
                                Reemplazo Temporal
                            </option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="row">

                
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Conductor a Reemplazar *</label>
                        <select name="old_driver" class="form-control" required>
                            <?php $__currentLoopData = $drivers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $d): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($d->id); ?>"
                                    <?php if($massiveChange->old_driver == $d->id): echo 'selected'; endif; ?>>
                                    <?php echo e($d->name); ?> - <?php echo e($d->document); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                </div>

                
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Nuevo Conductor *</label>
                        <select name="new_driver" class="form-control" required>
                            <?php $__currentLoopData = $drivers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $d): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($d->id); ?>"
                                    <?php if($massiveChange->new_driver == $d->id): echo 'selected'; endif; ?>>
                                    <?php echo e($d->name); ?> - <?php echo e($d->document); ?>

                                    (<?php echo e($d->contract_status); ?>)
                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                </div>
            </div>

            
            <div class="form-group mt-3">
                <label>Motivo del Cambio Masivo *</label>
                <textarea name="reason" class="form-control" rows="3" required><?php echo e($massiveChange->reason); ?></textarea>
            </div>

        </div>

        <div class="card-footer d-flex">
            <button type="submit" class="btn btn-success mr-2">
                <i class="fa fa-save"></i> Guardar
            </button>

            <a href="<?php echo e(route('admin.schedulings.index')); ?>" class="btn btn-danger">
                <i class="fa fa-times"></i> Cancelar
            </a>
        </div>

    </form>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('adminlte::page', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\crist\OneDrive\Escritorio\TADS\PryFinal\PryRsuReciclaje\resources\views/schedulings/edit-massive.blade.php ENDPATH**/ ?>