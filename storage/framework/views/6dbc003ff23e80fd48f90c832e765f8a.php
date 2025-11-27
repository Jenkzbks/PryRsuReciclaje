<div class="row">
    <div class="col-12">
        <div class="form-group">
            <label for="name">Nombre del Tipo de Vehículo</label>
            <input type="text" name="name" id="name" class="form-control" placeholder="Nombre del tipo de vehículo" value="<?php echo e(isset($vehicleType) ? $vehicleType->name : old('name')); ?>" required>
        </div>
        <div class="form-group">
            <label for="description">Descripción</label>
            <textarea name="description" id="description" class="form-control" placeholder="Agregue una descripción" rows="3"><?php echo e(isset($vehicleType) ? $vehicleType->description : old('description')); ?></textarea>
        </div>
    </div>
</div><?php /**PATH F:\USAT 2025-2\topicos\PryFINAL\PryRsuReciclaje\resources\views/admin/vehicletypes/template/form.blade.php ENDPATH**/ ?>