<?php echo Form::model($vehicle, ['route' => ['admin.vehicles.update', $vehicle->id], 'method' => 'PUT', 'files' => true, 'id' => 'vehicleForm']); ?>

<?php echo $__env->make('admin.vehicles.template.form', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<button type="submit" class="btn btn-primary far fa-save"> Actualizar</button>
<button type="button" class="btn btn-danger" data-dismiss="modal"> <i class="fas fa-window-close"></i> Cancelar</button>
<?php echo Form::close(); ?>

<?php /**PATH C:\Users\crist\OneDrive\Escritorio\TADS\PryFinal\PryRsuReciclaje\resources\views/admin/vehicles/edit.blade.php ENDPATH**/ ?>