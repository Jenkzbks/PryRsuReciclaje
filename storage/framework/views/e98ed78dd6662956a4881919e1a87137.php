<?php echo Form::open(['route' => 'admin.vehicles.store', 'files' => true, 'id' => 'vehicleForm']); ?>

<?php echo $__env->make('admin.vehicles.template.form', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<button type="submit" class="btn btn-success"><i class='fas fa-save'></i> Registrar</button>
<button type="button" class="btn btn-danger" data-dismiss="modal"> <i class="fas fa-window-close"></i> Cancelar</button>
<?php echo Form::close(); ?>

<?php /**PATH C:\Users\crist\OneDrive\Escritorio\TADS\PryFinal\PryRsuReciclaje\resources\views/admin/vehicles/create.blade.php ENDPATH**/ ?>