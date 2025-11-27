

<?php $__env->startSection('title','Editar mantenimiento'); ?>

<?php $__env->startSection('content_header'); ?>
  <h1>Editar mantenimiento</h1>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="card">
  <form method="POST" action="<?php echo e(route('admin.maintenances.update', $maintenance)); ?>">
    <?php echo csrf_field(); ?>
    <?php echo method_field('PUT'); ?>
    <div class="card-body">
      <?php if($errors->any()): ?>
        <div class="alert alert-danger">
          <ul class="mb-0">
            <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><li><?php echo e($e); ?></li><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </ul>
        </div>
      <?php endif; ?>

      <div class="form-row">
        <div class="form-group col-md-6">
          <label>Nombre *</label>
          <input type="text" name="name" class="form-control"
                 value="<?php echo e(old('name', $maintenance->name)); ?>" required>
        </div>
        <div class="form-group col-md-3">
          <label>Fecha inicio *</label>
          <input type="date" name="start_date" class="form-control"
                 value="<?php echo e(old('start_date', optional($maintenance->start_date)->format('Y-m-d'))); ?>" required>
        </div>
        <div class="form-group col-md-3">
          <label>Fecha fin *</label>
          <input type="date" name="end_date" class="form-control"
                 value="<?php echo e(old('end_date', optional($maintenance->end_date)->format('Y-m-d'))); ?>" required>
        </div>
      </div>

    </div>
    <div class="card-footer text-right">
      <a href="<?php echo e(route('admin.maintenances.index')); ?>" class="btn btn-outline-secondary">Volver</a>
      <button type="submit" class="btn btn-success">Actualizar</button>
    </div>
  </form>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('adminlte::page', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH F:\USAT 2025-2\topicos\PryFINAL\PryRsuReciclaje\resources\views/maintenances/edit.blade.php ENDPATH**/ ?>