<form action="<?php echo e(route('admin.shifts.update', $shift)); ?>" method="POST">
    <?php echo csrf_field(); ?>
    <?php echo method_field('PUT'); ?>
    <?php echo $__env->make('admin.shifts.template.form', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <div class="mt-3 d-flex justify-content-end">
        <button type="button" class="btn btn-secondary mr-2" data-dismiss="modal"> <i class="fas fa-window-close"></i> Cancelar</button>
        <button type="submit" class="btn btn-primary"><i class="far fa-save"></i> Guardar</button>
    </div>
</form>
<?php /**PATH C:\Users\crist\OneDrive\Escritorio\TADS\PryFinal\PryRsuReciclaje\resources\views/admin/shifts/edit.blade.php ENDPATH**/ ?>