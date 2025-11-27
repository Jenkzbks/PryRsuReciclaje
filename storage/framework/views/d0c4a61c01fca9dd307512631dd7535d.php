<div class="row">
    <div class="col-12">
        <div class="form-group">
            <label for="name">Nombre del Modelo</label>
            <input type="text" name="name" id="name" class="form-control" placeholder="Nombre del modelo" value="<?php echo e(isset($brandModel) ? $brandModel->name : old('name')); ?>" required>
        </div>
        <div class="form-group">
            <label for="brand_id">Marca</label>
            <select name="brand_id" id="brand_id" class="form-control" required>
                <option value="">Seleccione una marca</option>
                <?php $__currentLoopData = $brands; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $brand): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($brand->id); ?>" <?php echo e((isset($brandModel) && $brandModel->brand_id == $brand->id) ? 'selected' : ''); ?>>
                        <?php echo e($brand->name); ?>

                    </option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
        <div class="form-group">
            <label for="description">Descripción</label>
            <textarea name="description" id="description" class="form-control" placeholder="Agregue una descripción" rows="3"><?php echo e(isset($brandModel) ? $brandModel->description : old('description')); ?></textarea>
        </div>
    </div>
</div><?php /**PATH F:\USAT 2025-2\topicos\PryFINAL\PryRsuReciclaje\resources\views/admin/brandmodels/template/form.blade.php ENDPATH**/ ?>