<form action="<?php echo e(route('admin.personnel.employeegroups.update', $group)); ?>" method="POST">
    <?php echo csrf_field(); ?>
    <?php echo method_field('PUT'); ?>
    <?php echo $__env->make('personnel.employeegroup.template.form', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <div class="mt-3 d-flex justify-content-end">
        <button type="button" class="btn btn-danger mr-2" data-dismiss="modal">
            <i class="fas fa-ban mr-1"></i> Cancelar
        </button>
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save mr-1"></i> Guardar
        </button>
    </div>
</form>
<?php /**PATH F:\USAT 2025-2\topicos\PryFINAL\PryRsuReciclaje\resources\views/personnel/employeegroup/edit.blade.php ENDPATH**/ ?>