<form action="<?php echo e(route('admin.vehicletypes.store')); ?>" method="POST">
    <?php echo csrf_field(); ?>
    <?php echo $__env->make('admin.vehicletypes.template.form', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <button type="submit" class="btn btn-success"><i class='fas fa-save'></i> Registrar</button>
    <button type="button" class="btn btn-danger" data-dismiss="modal"> <i class="fas fa-window-close"></i> Cancelar</button>
</form><?php /**PATH F:\USAT 2025-2\topicos\PryFINAL\PryRsuReciclaje\resources\views/admin/vehicletypes/create.blade.php ENDPATH**/ ?>