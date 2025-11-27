

<?php $__env->startSection('title','Nueva Programación'); ?>

<?php $__env->startSection('content_header'); ?>
    <h1>Nueva Programación</h1>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('js'); ?>
<script>
document.addEventListener('DOMContentLoaded', function(){
    // Trash a group: mark hidden input value '1', add trashed class and show overlay
    document.querySelectorAll('.btn-trash-group').forEach(function(btn){
        btn.addEventListener('click', function(){
            var gid = this.dataset.groupId;
            var cardCol = this.closest('.col-md-4');
            if(!cardCol) return;
            var card = cardCol.querySelector('.card');
            if(!card) return;
            var hidden = card.querySelector('.group-removed-input');
            if(hidden) hidden.value = '1';
            card.classList.add('trashed');
            var overlay = card.querySelector('.trashed-overlay');
            if(overlay) overlay.style.display = 'flex';
        });
    });

    // Undo trash: set hidden input back to '0', remove class and hide overlay
    document.querySelectorAll('.btn-undo-trash').forEach(function(btn){
        btn.addEventListener('click', function(e){
            e.preventDefault();
            var gid = this.dataset.groupId;
            var cardCol = this.closest('.col-md-4');
            if(!cardCol) return;
            var card = cardCol.querySelector('.card');
            if(!card) return;
            var hidden = card.querySelector('.group-removed-input');
            if(hidden) hidden.value = '0';
            card.classList.remove('trashed');
            var overlay = card.querySelector('.trashed-overlay');
            if(overlay) overlay.style.display = 'none';
        });
    });
});
</script>

<style>
    .card.trashed { opacity: 0.45; }
    .trashed-overlay { display:none; position:absolute; inset:0; background:rgba(255,255,255,0.95); align-items:center; justify-content:center; z-index:10; flex-direction:column; }
    .card { position:relative; }
    .trashed-overlay .text { margin-bottom:10px; }
</style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="card">
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
                        <div class="card-header bg-light d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <input type="checkbox" name="group_ids[]" value="<?php echo e($group->id); ?>" class="mr-2" />
                                <strong class="ml-2"><?php echo e($group->name); ?></strong>
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-danger btn-trash-group" data-group-id="<?php echo e($group->id); ?>"><i class="fas fa-trash"></i></button>
                        </div>

                        <div class="card-body">
                            
                            <input type="hidden" name="group_<?php echo e($group->id); ?>[removed]" value="0" class="group-removed-input" data-group-id="<?php echo e($group->id); ?>">

                            
                            <div class="trashed-overlay" style="display:none;">
                                <div class="text"><strong>Grupo eliminado (temporal)</strong></div>
                                <button class="btn btn-sm btn-secondary btn-undo-trash" data-group-id="<?php echo e($group->id); ?>">Deshacer</button>
                            </div>

                            <?php
                                // Use selected ids computed in controller (from configgroups) if available
                                $driverId = $group->selected_driver_id ?? null;
                                $assistant1Id = $group->selected_assistant1_id ?? null;
                                $assistant2Id = $group->selected_assistant2_id ?? null;

                                // Fallback to pivot positions if controller didn't find them
                                if (!$driverId) {
                                    $driverId = optional($group->employees->firstWhere('pivot.posicion', 1))->id;
                                }
                                if (!$assistant1Id) {
                                    $assistant1Id = optional($group->employees->firstWhere('pivot.posicion', 2))->id;
                                }
                                if (!$assistant2Id) {
                                    $assistant2Id = optional($group->employees->firstWhere('pivot.posicion', 3))->id;
                                }

                                // Final fallback: by type
                                if (!$driverId) {
                                    $driverId = optional($group->employees->firstWhere('type_id', 1))->id;
                                }
                                if (!$assistant1Id || !$assistant2Id) {
                                    $assistantsCollection = $group->employees->where('type_id', 2)->values();
                                    $assistant1Id = $assistant1Id ?? $assistantsCollection->get(0)?->id;
                                    $assistant2Id = $assistant2Id ?? $assistantsCollection->get(1)?->id;
                                }
                            ?>

                            <p><strong>Zona:</strong> <?php echo e($group->zone->name ?? '-'); ?></p>
                            <p><strong>Turno:</strong> <?php echo e($group->shift->name ?? '-'); ?></p>
                            <p><strong>Días:</strong> <?php echo e($group->days ?? '-'); ?></p>
                            <p>
                                <strong>Vehículo:</strong> <?php echo e($group->vehicle->plate ?? '-'); ?>

                                <?php if(optional($group->vehicle)->capacity): ?>
                                    <span class="badge badge-warning">Capacidad: <?php echo e($group->vehicle->capacity); ?></span>
                                <?php endif; ?>
                            </p>

                            <div class="form-group">
                                <label>Conductor:</label>
                                <select name="group_<?php echo e($group->id); ?>[driver]" class="form-control">
                                    <option value="">Seleccione</option>
                                    <?php $__currentLoopData = $drivers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $emp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($emp->id); ?>" <?php echo e($emp->id == $driverId ? 'selected' : ''); ?>><?php echo e($emp->lastnames); ?> <?php echo e($emp->names); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <label>Ayudante 1:</label>
                                <select name="group_<?php echo e($group->id); ?>[assistant1]" class="form-control">
                                    <option value="">Seleccione</option>
                                    <?php $__currentLoopData = $assistants; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $emp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($emp->id); ?>" <?php echo e($emp->id == $assistant1Id ? 'selected' : ''); ?>><?php echo e($emp->lastnames); ?> <?php echo e($emp->names); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <label>Ayudante 2:</label>
                                <select name="group_<?php echo e($group->id); ?>[assistant2]" class="form-control">
                                    <option value="">Seleccione</option>
                                    <?php $__currentLoopData = $assistants; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $emp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($emp->id); ?>" <?php echo e($emp->id == $assistant2Id ? 'selected' : ''); ?>><?php echo e($emp->lastnames); ?> <?php echo e($emp->names); ?></option>
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
</div>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('adminlte::page', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\crist\OneDrive\Escritorio\TADS\PryFinal\PryRsuReciclaje\resources\views/schedulings/create-masive.blade.php ENDPATH**/ ?>