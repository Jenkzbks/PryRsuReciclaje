<form method="POST" action="<?php echo e(route('admin.schedulings.store')); ?>">
    <?php echo csrf_field(); ?>
    
    <?php if(session('success')): ?>
        <div class="alert alert-success"><?php echo e(session('success')); ?></div>
    <?php endif; ?>
    <?php if(session('warning')): ?>
        <div class="alert alert-warning"><?php echo e(session('warning')); ?></div>
    <?php endif; ?>

    <?php if($errors->any()): ?>
        <div class="alert alert-danger">
            <ul class="mb-0">
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li><?php echo e($error); ?></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
    <?php endif; ?>
    
    <div class="card-body">

        
        <div class="row mb-3">
            <div class="col-md-4">
                <label>Fecha de inicio *</label>
                <input type="date" name="from" class="form-control" required>
            </div>

            <div class="col-md-4">
                <label>Fecha de fin *</label>
                <input type="date" name="to" class="form-control" required>
            </div>

            <div class="col-md-4 d-flex align-items-end">
                <button type="button" class="btn btn-info w-100">
                    <i class="fas fa-search"></i> Validar Disponibilidad
                </button>
            </div>
        </div>

        
        <div class="row">
            <?php $__empty_1 = true; $__currentLoopData = $groups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $group): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <div class="col-md-4 mb-3">
                <div class="card shadow">
                    <div class="card-header bg-light d-flex justify-content-between">
                        <strong><?php echo e($group->name); ?></strong>
                        <button type="button" class="btn btn-sm btn-outline-danger btn-trash-group" data-group-id="<?php echo e($group->id); ?>"><i class="fas fa-trash"></i></button>
                    </div>

                    <div class="card-body">
                        <input type="hidden" name="group_<?php echo e($group->id); ?>[removed]" value="0" class="group-removed-input" data-group-id="<?php echo e($group->id); ?>">

                        <p><strong>Zona:</strong> <?php echo e($group->zone->name ?? '-'); ?></p>
                        <p><strong>Turno:</strong> <?php echo e($group->shift->name ?? '-'); ?></p>
                        <p><strong>Días:</strong> <?php echo e($group->days ?? '-'); ?></p>
                        <p>
                            <strong>Vehículo:</strong> <?php echo e($group->vehicle->plate ?? '-'); ?>

                        </p>

                        <div class="form-group">
                            <label>Conductor:</label>
                            <select name="group_<?php echo e($group->id); ?>[driver]" class="form-control">
                                <option value="">Seleccione</option>
                                <?php $__currentLoopData = $drivers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $emp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($emp->id); ?>"><?php echo e($emp->lastnames); ?> <?php echo e($emp->names); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Ayudante 1:</label>
                            <select name="group_<?php echo e($group->id); ?>[assistant1]" class="form-control">
                                <option value="">Seleccione</option>
                                <?php $__currentLoopData = $assistants; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $emp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($emp->id); ?>"><?php echo e($emp->lastnames); ?> <?php echo e($emp->names); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Ayudante 2:</label>
                            <select name="group_<?php echo e($group->id); ?>[assistant2]" class="form-control">
                                <option value="">Seleccione</option>
                                <?php $__currentLoopData = $assistants; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $emp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($emp->id); ?>"><?php echo e($emp->lastnames); ?> <?php echo e($emp->names); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>

                    </div>
                </div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div class="col-12"><p class="text-muted">No hay grupos disponibles.</p></div>
            <?php endif; ?>

        </div> 

    </div>

    <div class="card-footer text-right">
        <a href="<?php echo e(route('admin.schedulings.index')); ?>" class="btn btn-outline-secondary">Volver</a>
        <button type="submit" class="btn btn-success">Registrar Programación</button>
    </div>
</form>
<?php /**PATH C:\Users\crist\OneDrive\Escritorio\TADS\PryFinal\PryRsuReciclaje\resources\views/schedulings/_massive_form.blade.php ENDPATH**/ ?>