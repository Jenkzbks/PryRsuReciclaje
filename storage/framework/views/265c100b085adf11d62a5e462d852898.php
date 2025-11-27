<?php $__env->startSection('title','Editar Programación'); ?>

<?php $__env->startSection('content_header'); ?>
  <h1>Editar Programación</h1>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="card">
  <form method="POST" action="<?php echo e(route('admin.schedulings.update',$scheduling)); ?>">
    <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
    <div class="card-body">
      <?php if($errors->any()): ?>
        <div class="alert alert-danger"><ul><?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><li><?php echo e($e); ?></li><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></ul></div>
      <?php endif; ?>

      <div class="alert alert-light border">
        <div><strong>Fecha:</strong> <?php echo e($scheduling->date); ?></div>
        <div><strong>Grupo:</strong> <?php echo e($scheduling->group->name ?? '-'); ?></div>
        <div><strong>Zona:</strong> <?php echo e($scheduling->group->zone->name ?? '-'); ?></div>
        <div><strong>Turno:</strong> <?php echo e($scheduling->group->shift->name ?? '-'); ?></div>
        <div><strong>Vehículo:</strong> <?php echo e($scheduling->group->vehicle->plate ?? '-'); ?></div>
        <div><strong>Notas:</strong> <?php echo e($scheduling->notes ?? '-'); ?></div>
      </div>

      <div class="row">
        <div class="col-md-4">
          <label>Conductor</label>
          <select name="driver_id" class="form-control">
            <option value="">-- Seleccione --</option>
            <?php $__currentLoopData = $drivers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $d): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <option value="<?php echo e($d->id); ?>" <?php echo e($selectedDriverId==$d->id?'selected':''); ?>>
                <?php echo e($d->lastnames); ?> <?php echo e($d->names); ?>

              </option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </select>
        </div>
        <div class="col-md-4">
          <label>Ayudante 1</label>
          <select name="assistant1_id" class="form-control">
            <option value="">-- Seleccione --</option>
            <?php $__currentLoopData = $assistants; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $a): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <option value="<?php echo e($a->id); ?>" <?php echo e($selectedA1Id==$a->id?'selected':''); ?>>
                <?php echo e($a->lastnames); ?> <?php echo e($a->names); ?>

              </option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </select>
        </div>
        <div class="col-md-4">
          <label>Ayudante 2</label>
          <select name="assistant2_id" class="form-control">
            <option value="">-- Seleccione --</option>
            <?php $__currentLoopData = $assistants; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $a): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <option value="<?php echo e($a->id); ?>" <?php echo e($selectedA2Id==$a->id?'selected':''); ?>>
                <?php echo e($a->lastnames); ?> <?php echo e($a->names); ?>

              </option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </select>
        </div>
      </div>
    </div>
    <div class="card-footer text-right">
      <a href="<?php echo e(route('admin.schedulings.index')); ?>" class="btn btn-outline-secondary">Volver</a>
      <button class="btn btn-success"><i class="fas fa-save"></i> Guardar cambios</button>
    </div>
  </form>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('adminlte::page', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH F:\USAT 2025-2\topicos\PryFINAL\PryRsuReciclaje\resources\views/schedulings/edit.blade.php ENDPATH**/ ?>