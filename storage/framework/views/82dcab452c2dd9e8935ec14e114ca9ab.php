

<?php $__env->startSection('title', 'Horarios de mantenimiento'); ?>

<?php $__env->startSection('content_header'); ?>
<div class="d-flex justify-content-between align-items-center">
  <div>
    <h1>Horarios de mantenimiento</h1>
    <p class="mb-0 text-muted">
      <?php echo e($maintenance->name); ?> 
      (<?php echo e($maintenance->start_date->format('Y-m-d')); ?> al <?php echo e($maintenance->end_date->format('Y-m-d')); ?>)
    </p>
  </div>
  <a href="<?php echo e(route('maintenances.schedules.create', $maintenance)); ?>" class="btn btn-primary">
    <i class="fas fa-plus"></i> Nuevo horario
  </a>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="card">
  <div class="card-body">

    <?php if(session('success')): ?>
      <div class="alert alert-success"><?php echo e(session('success')); ?></div>
    <?php endif; ?>

    <div class="mb-3">
      <a href="<?php echo e(route('admin.maintenances.index')); ?>" class="btn btn-sm btn-outline-secondary">
        <i class="fas fa-arrow-left"></i> Volver a mantenimientos
      </a>
    </div>

    <div class="table-responsive">
      <table class="table table-striped">
        <thead class="thead-light">
          <tr>
            <th>Día</th>
            <th>Hora</th>
            <th>Vehículo</th>
            <th>Conductor</th>
            <th>Tipo mantenimiento</th>
            <th># Fechas generadas</th>
            <th width="10"></th>
          </tr>
        </thead>
        <tbody>
          <?php $__empty_1 = true; $__currentLoopData = $schedules; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sch): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <tr>
              <td><?php echo e(ucfirst($sch->day_of_week)); ?></td>
              <td><?php echo e($sch->start_time); ?> - <?php echo e($sch->end_time); ?></td>
              <td><?php echo e($sch->vehicle->plate ?? '-'); ?></td>
              <td><?php echo e($sch->driver->full_name ?? '-'); ?></td>
              <td><?php echo e($sch->maintenance_type); ?></td>
              <td><?php echo e($sch->records->count()); ?></td>
              <td class="d-flex">
                <a href="<?php echo e(route('maintenances.schedules.edit', [$maintenance, $sch])); ?>" 
                   class="btn btn-sm btn-outline-primary mr-2">
                  <i class="fas fa-edit"></i>
                </a>
                <form method="POST" 
                      action="<?php echo e(route('maintenances.schedules.destroy', [$maintenance, $sch])); ?>"
                      onsubmit="return confirm('¿Eliminar este horario y sus registros?')">
                  <?php echo csrf_field(); ?>
                  <?php echo method_field('DELETE'); ?>
                  <button class="btn btn-sm btn-outline-danger">
                    <i class="fas fa-trash"></i>
                  </button>
                </form>
              </td>
            </tr>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr>
              <td colspan="7" class="text-center text-muted">
                No hay horarios configurados para este mantenimiento.
              </td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>

  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('adminlte::page', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH F:\USAT 2025-2\topicos\PryFINAL\PryRsuReciclaje\resources\views/schedules/index.blade.php ENDPATH**/ ?>