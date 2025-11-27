<div class="btn-group" role="group">
    <a href="<?php echo e(route('admin.routes.show', $route)); ?>" class="btn btn-sm btn-outline-info" title="Ver">
        <i class="fas fa-eye"></i>
    </a>
    <a href="<?php echo e(route('admin.routes.edit', $route)); ?>" class="btn btn-sm btn-outline-primary" title="Editar">
        <i class="fas fa-edit"></i>
    </a>
    <form action="<?php echo e(route('admin.routes.destroy', $route)); ?>" method="POST" class="d-inline frmDelete">
        <?php echo csrf_field(); ?>
        <?php echo method_field('DELETE'); ?>
        <button type="submit" class="btn btn-sm btn-outline-danger" title="Eliminar">
            <i class="fas fa-trash"></i>
        </button>
    </form>
</div><?php /**PATH F:\USAT 2025-2\topicos\PryFINAL\PryRsuReciclaje\resources\views/admin/routes_zone/partials/actions.blade.php ENDPATH**/ ?>