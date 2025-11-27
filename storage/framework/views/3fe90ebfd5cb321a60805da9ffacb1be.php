<?php $__env->startSection('title', 'Programaciones'); ?>

<?php $__env->startSection('content_header'); ?>
<div class="d-flex align-items-center">
  <h1 class="mb-0">Programaciones</h1>

  <div class="ml-auto d-flex align-items-center">
    <a href="<?php echo e(route('admin.schedulings.create-masive')); ?>" class="btn btn-primary btn-sm">
      <i class="fas fa-plus"></i> Nueva Programación Masiva
    </a>
    <a href="<?php echo e(route('admin.schedulings.create')); ?>" class="btn btn-primary btn-sm ml-2">
      <i class="fas fa-plus"></i> Nueva Programación
    </a>
  </div>

</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="card">
  <div class="card-body">
    <form method="GET" class="mb-3">
      <div class="form-row">
        <div class="col-md-3">
          <label>Fecha inicio</label>
          <input type="date" name="from" class="form-control" value="<?php echo e($from); ?>">
        </div>
        <div class="col-md-3">
          <label>Fecha fin</label>
          <input type="date" name="to" class="form-control" value="<?php echo e($to); ?>">
        </div>
        <div class="col-md-3 d-flex align-items-end">
          <button class="btn btn-outline-secondary"><i class="fas fa-filter"></i> Filtrar</button>
        </div>
      </div>
    </form>

    <?php if(session('success')): ?>
      <div class="alert alert-success"><?php echo e(session('success')); ?></div>
    <?php endif; ?>

    <div class="table-responsive">
      <table class="table table-striped">
        <thead class="thead-light">
          <tr>
            <th>Fecha</th>
            <th>Zona</th>
            <th>Turno</th>
            <th>Vehículo</th>
            <th>Grupo</th>
            <th>Notas</th>
            <th width="10"></th>
          </tr>
        </thead>
        <tbody>
          <?php $__empty_1 = true; $__currentLoopData = $schedulings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <tr>
              <td><?php echo e($s->date); ?></td>
              <td><?php echo e($s->zone->name ?? '-'); ?></td>
              <td><?php echo e($s->shift->name ?? '-'); ?></td>
              <td><?php echo e($s->vehicle->plate ?? '-'); ?></td>
              <td><?php echo e($s->group->name ?? '-'); ?></td>
              <td><?php echo e($s->notes); ?></td>
              <td class="d-flex">
                <a href="<?php echo e(route('admin.schedulings.edit',$s)); ?>" class="btn btn-sm btn-outline-primary mr-2">
                  <i class="fas fa-edit"></i>
                </a>
                <form method="POST" action="<?php echo e(route('admin.schedulings.destroy',$s)); ?>" onsubmit="return confirm('¿Eliminar programación?')">
                  <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                  <button class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                </form>
              </td>
            </tr>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr><td colspan="8" class="text-center text-muted">Sin resultados</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>

    <div class="mt-3"><?php echo e($schedulings->appends(request()->query())->links()); ?></div>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('adminlte::page', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\crist\OneDrive\Escritorio\TADS\PryFinal\PryRsuReciclaje\resources\views/schedulings/index.blade.php ENDPATH**/ ?>