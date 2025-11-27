<?php echo Form::open(['route' => 'admin.brands.store', 'files' => true]); ?>

<?php echo $__env->make('admin.brands.template.form', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<button type="submit" class="btn btn-success"><i class='fas fa-save'></i> Registrar</button>
<button type="button" class="btn btn-danger" data-dismiss="modal"> <i class="fas fa-window-close"></i> Cancelar</button>
<?php echo Form::close(); ?>



<?php /**PATH F:\USAT 2025-2\topicos\PryFINAL\PryRsuReciclaje\resources\views/admin/brands/create.blade.php ENDPATH**/ ?>