

<?php $__env->startSection('title', 'Editar registro de mantenimiento'); ?>

<?php $__env->startSection('content_header'); ?>
<div class="d-flex justify-content-between align-items-center">
  <h1>Editar registro de mantenimiento</h1>
  <a href="<?php echo e(route('admin.maintenances.schedules.index', $maintenance)); ?>"
     class="btn btn-outline-secondary">
    Volver a horarios
  </a>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="card">
  <form method="POST"
        action="<?php echo e(route('admin.maintenances.records.update', [$maintenance, $record])); ?>"
        enctype="multipart/form-data">
    <?php echo csrf_field(); ?>
    <?php echo method_field('PUT'); ?>

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

      <div class="form-group">
        <label>Fecha</label>
        <input type="date"
               class="form-control"
               value="<?php echo e($record->maintenance_date->format('Y-m-d')); ?>"
               disabled>
      </div>

      <div class="form-group">
        <label>Descripción</label>
        <textarea name="descripcion"
                  class="form-control"
                  rows="3"
                  placeholder="Opcional"><?php echo e(old('descripcion', $record->descripcion)); ?></textarea>
      </div>

      
      <div class="form-group">
        <label>Estado *</label>
        <select name="estado" class="form-control" required>
          <?php
            $currentEstado = old('estado', $record->estado ?? 'no realizado');
          ?>
          <option value="no realizado" <?php echo e($currentEstado === 'no realizado' ? 'selected' : ''); ?>>
            No realizado
          </option>
          <option value="realizado" <?php echo e($currentEstado === 'realizado' ? 'selected' : ''); ?>>
            Realizado
          </option>
        </select>
      </div>

      <div class="form-group">
        <label>Imagen (opcional)</label>
        <?php if($record->image_url): ?>
          <div class="mb-2">
            <img src="<?php echo e(asset('storage/'.$record->image_url)); ?>"
                 alt="Imagen actual"
                 style="max-width: 150px; max-height: 150px; object-fit: cover;">
          </div>
        <?php endif; ?>
        <input type="file" name="image" class="form-control-file">
        <small class="form-text text-muted">
          Formatos aceptados: JPG, PNG, GIF. Máx: 4 MB.
        </small>
      </div>

    </div>

    <div class="card-footer text-right">
      <a href="<?php echo e(route('admin.maintenances.schedules.index', $maintenance)); ?>"
         class="btn btn-outline-secondary">Cancelar</a>
      <button type="submit" class="btn btn-primary">Guardar cambios</button>
    </div>
  </form>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('adminlte::page', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH F:\USAT 2025-2\topicos\PryFINAL\PryRsuReciclaje\resources\views/maintenancerecords/edit.blade.php ENDPATH**/ ?>