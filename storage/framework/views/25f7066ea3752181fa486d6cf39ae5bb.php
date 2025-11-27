
<?php echo Form::model($brand, ['route' => ['admin.brands.update', $brand], 'method' => 'PUT', 'files' => true]); ?>

 <?php echo $__env->make('admin.brands.template.form', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<button type="submit" class="btn btn-primary far fa-save"> Actualizar</button>
<button type="button" class="btn btn-danger" data-dismiss="modal"> <i class="fas fa-window-close"></i> Cancelar</button>
<?php echo Form::close(); ?>

     <?php /**PATH F:\USAT 2025-2\topicos\PryFINAL\PryRsuReciclaje\resources\views/admin/brands/edit.blade.php ENDPATH**/ ?>