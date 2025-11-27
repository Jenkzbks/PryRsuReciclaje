<form action="<?php echo e(route('admin.shifts.store')); ?>" method="POST">
    <?php echo csrf_field(); ?>
    <?php echo $__env->make('admin.shifts.template.form', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <div class="mt-3 d-flex justify-content-end">
        <button type="button" class="btn btn-secondary mr-2" data-dismiss="modal"> <i class="fas fa-window-close"></i> Cancelar</button>
        <button type="submit" class="btn btn-primary"> <i class='fas fa-save'></i> Guardar</button>
    </div>
</form>
<?php /**PATH F:\USAT 2025-2\topicos\PryFINAL\PryRsuReciclaje\resources\views/admin/shifts/create.blade.php ENDPATH**/ ?>