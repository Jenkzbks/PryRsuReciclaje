

<?php $__env->startSection('title', 'Mantenimientos'); ?>

<?php $__env->startSection('content_header'); ?>
<div class="d-flex justify-content-between align-items-center">
  <h1>Mantenimientos</h1>
  <a href="<?php echo e(route('admin.maintenances.create')); ?>" class="btn btn-primary">
    <i class="fas fa-plus"></i> Nuevo mantenimiento
  </a>
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
          <button class="btn btn-outline-secondary">
            <i class="fas fa-filter"></i> Filtrar
          </button>
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
            <th>Nombre</th>
            <th>Fecha inicio</th>
            <th>Fecha fin</th>
            <th width="180">Acciones</th>
          </tr>
        </thead>
        <tbody>
          <?php $__empty_1 = true; $__currentLoopData = $maintenances; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <tr>
              <td><?php echo e($m->name); ?></td>
              <td><?php echo e(optional($m->start_date)->format('Y-m-d')); ?></td>
              <td><?php echo e(optional($m->end_date)->format('Y-m-d')); ?></td>
              <td class="d-flex">

                <a href="<?php echo e(route('admin.maintenances.schedules.index', $m)); ?>"
                   class="btn btn-sm btn-outline-info mr-2">
                  <i class="fas fa-clock"></i> Horarios
                </a>

                <a href="<?php echo e(route('admin.maintenances.edit', $m)); ?>"
                   class="btn btn-sm btn-outline-primary mr-2">
                  <i class="fas fa-edit"></i>
                </a>

                <form method="POST"
                      action="<?php echo e(route('admin.maintenances.destroy', $m)); ?>"
                      class="form-delete-maintenance m-0 p-0 d-inline">
                  <?php echo csrf_field(); ?>
                  <?php echo method_field('DELETE'); ?>
                  <button type="button"
                          class="btn btn-sm btn-outline-danger btn-open-delete-modal"
                          data-name="<?php echo e($m->name); ?>">
                    <i class="fas fa-trash"></i>
                  </button>
                </form>
              </td>
            </tr>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr>
              <td colspan="4" class="text-center text-muted">Sin resultados</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>

    <div class="mt-3">
      <?php echo e($maintenances->appends(request()->query())->links()); ?>

    </div>
  </div>
</div>

<div class="modal fade" id="confirmDeleteMaintenanceModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header bg-danger text-white">
        <h5 class="modal-title">Confirmar eliminación</h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <p>
          ¿Seguro que deseas eliminar el mantenimiento
          <strong id="deleteMaintenanceName"></strong>?
        </p>
        <p class="mb-0 text-muted">
          Se eliminarán también todos sus horarios y registros asociados.
        </p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">
          Cancelar
        </button>
        <button type="button" class="btn btn-danger" id="btnConfirmDeleteMaintenance">
          Eliminar
        </button>
      </div>
    </div>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('js'); ?>
<script>
  let maintenanceFormToSubmit = null;

  document.addEventListener('DOMContentLoaded', function () {

    document.querySelectorAll('.btn-open-delete-modal').forEach(btn => {
      btn.addEventListener('click', function () {

        maintenanceFormToSubmit = this.closest('form');

        const name = this.dataset.name || '';
        document.getElementById('deleteMaintenanceName').textContent = name;

        $('#confirmDeleteMaintenanceModal').modal('show');
      });
    });

    document.getElementById('btnConfirmDeleteMaintenance').addEventListener('click', function () {
      if (maintenanceFormToSubmit) {
        maintenanceFormToSubmit.submit();
      }
    });
  });
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('adminlte::page', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH F:\USAT 2025-2\topicos\PryFINAL\PryRsuReciclaje\resources\views/maintenances/index.blade.php ENDPATH**/ ?>