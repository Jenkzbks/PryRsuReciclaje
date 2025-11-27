
<div class="card mt-3">
  <div class="card-header">
    <strong>Fechas generadas (registros)</strong>
  </div>
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-striped mb-0">
        <thead class="thead-light">
          <tr>
            <th>Fecha</th>
            <th>Descripción</th>
            <th>Imagen</th>
            <th width="120">Acciones</th>
          </tr>
        </thead>
        <tbody>
          <?php $__empty_1 = true; $__currentLoopData = $records; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rec): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <tr>
              <td><?php echo e($rec->maintenance_date); ?></td>
              <td><?php echo e($rec->descripcion ?: '-'); ?></td>
              <td>
                <?php if($rec->image_url): ?>
                  <img src="<?php echo e(asset('storage/'.$rec->image_url)); ?>"
                       alt="Imagen mantenimiento"
                       style="max-width: 80px; max-height: 80px; object-fit: cover;">
                <?php else: ?>
                  <span class="text-muted">Sin imagen</span>
                <?php endif; ?>
              </td>
              <td class="d-flex">
                <a href="<?php echo e(route('admin.maintenances.records.edit', [$maintenance, $rec])); ?>"
                   class="btn btn-sm btn-outline-primary mr-2"
                   title="Editar">
                  <i class="fas fa-edit"></i>
                </a>
                <form method="POST"
                      action="<?php echo e(route('admin.maintenances.records.destroy', [$maintenance, $rec])); ?>"
                      onsubmit="return confirm('¿Eliminar este registro de mantenimiento?')">
                  <?php echo csrf_field(); ?>
                  <?php echo method_field('DELETE'); ?>
                  <button class="btn btn-sm btn-outline-danger" title="Eliminar">
                    <i class="fas fa-trash"></i>
                  </button>
                </form>
              </td>
            </tr>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr>
              <td colspan="4" class="text-center text-muted">
                No se han generado registros para este mantenimiento.
              </td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<?php /**PATH F:\USAT 2025-2\topicos\PryFINAL\PryRsuReciclaje\resources\views/maintenances/schedules/index.blade.php ENDPATH**/ ?>