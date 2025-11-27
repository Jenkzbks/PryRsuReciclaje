

<?php $__env->startSection('title','Nuevo horario de mantenimiento'); ?>

<?php $__env->startSection('content_header'); ?>
  <h1>Nuevo horario - <?php echo e($maintenance->name); ?></h1>
  <small class="text-muted">Rango: <?php echo e($maintenance->range_text); ?></small>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="card">
  <form method="POST" action="<?php echo e(route('admin.maintenances.schedules.store', $maintenance)); ?>">
    <?php echo csrf_field(); ?>
    <div class="card-body">
      <?php if($errors->any()): ?>
        <div class="alert alert-danger">
          <ul class="mb-0">
            <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><li><?php echo e($e); ?></li><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </ul>
        </div>
      <?php endif; ?>

      <div class="form-row">
        <div class="form-group col-md-4">
          <label>Vehículo *</label>
          <select name="vehicle_id" class="form-control" required>
            <option value="">-- Seleccione --</option>
            <?php $__currentLoopData = $vehicles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <option value="<?php echo e($v->id); ?>" <?php echo e(old('vehicle_id') == $v->id ? 'selected' : ''); ?>>
                <?php echo e($v->plate); ?> - <?php echo e($v->brand->name ?? ''); ?> <?php echo e($v->brandModel->name ?? ''); ?>

              </option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </select>
        </div>
        <div class="form-group col-md-4">
          <label>Conductor *</label>
          <select name="driver_id" class="form-control" required>
            <option value="">-- Seleccione --</option>
            <?php $__currentLoopData = $drivers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $d): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <option value="<?php echo e($d->id); ?>" <?php echo e(old('driver_id') == $d->id ? 'selected' : ''); ?>>
                <?php echo e($d->lastnames); ?> <?php echo e($d->names); ?>

              </option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </select>
        </div>
        <div class="form-group col-md-4">
    <label>Día de la semana *</label>
    <select name="day_of_week" class="form-control" required>
        <option value="">-- Seleccione --</option>

        <option value="lunes"     <?php echo e(old('day_of_week') == 'lunes' ? 'selected' : ''); ?>>Lunes</option>
        <option value="martes"    <?php echo e(old('day_of_week') == 'martes' ? 'selected' : ''); ?>>Martes</option>
        <option value="miercoles" <?php echo e(old('day_of_week') == 'miercoles' ? 'selected' : ''); ?>>Miércoles</option>
        <option value="jueves"    <?php echo e(old('day_of_week') == 'jueves' ? 'selected' : ''); ?>>Jueves</option>
        <option value="viernes"   <?php echo e(old('day_of_week') == 'viernes' ? 'selected' : ''); ?>>Viernes</option>
        <option value="sabado"    <?php echo e(old('day_of_week') == 'sabado' ? 'selected' : ''); ?>>Sábado</option>
        <option value="domingo"   <?php echo e(old('day_of_week') == 'domingo' ? 'selected' : ''); ?>>Domingo</option>

    </select>
</div>

      </div>

      <div class="form-row">
        <div class="form-group col-md-3">
          <label>Hora inicio *</label>
          <input type="time" name="start_time" class="form-control"
                 value="<?php echo e(old('start_time')); ?>" required>
        </div>
        <div class="form-group col-md-3">
          <label>Hora fin *</label>
          <input type="time" name="end_time" class="form-control"
                 value="<?php echo e(old('end_time')); ?>" required>
        </div>
        <div class="form-group col-md-6">
  <label>Tipo de mantenimiento *</label>
  <select name="maintenance_type" class="form-control" required>
    <option value="">-- Seleccione --</option>
    <option value="preventivo" <?php echo e(old('maintenance_type') == 'preventivo' ? 'selected' : ''); ?>>
      Preventivo
    </option>
    <option value="limpieza" <?php echo e(old('maintenance_type') == 'limpieza' ? 'selected' : ''); ?>>
      Limpieza
    </option>
    <option value="reparacion" <?php echo e(old('maintenance_type') == 'reparacion' ? 'selected' : ''); ?>>
      Reparación
    </option>
  </select>
</div>

      </div>

      <p class="text-muted mb-0">
        Al guardar, se crearán registros en todas las fechas dentro del rango
        <strong><?php echo e($maintenance->range_text); ?></strong> que coincidan con el día seleccionado.
      </p>
    </div>
    <div class="card-footer text-right">
      <a href="<?php echo e(route('admin.maintenances.schedules.index', $maintenance)); ?>" class="btn btn-outline-secondary">Volver</a>
      <button type="submit" class="btn btn-success">Guardar horario</button>
    </div>
  </form>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('adminlte::page', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH F:\USAT 2025-2\topicos\PryFINAL\PryRsuReciclaje\resources\views/maintenanceschedules/create.blade.php ENDPATH**/ ?>