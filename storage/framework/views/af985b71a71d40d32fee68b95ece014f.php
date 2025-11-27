<div class="row">
    <div class="col-12">
        <div class="form-row">
            <div class="form-group col-md-6">
                <label for="name">Nombre del grupo <span class="text-danger">*</span></label>
                <input type="text" name="name" id="name" class="form-control" placeholder="Ingrese el nombre del grupo" value="<?php echo e(isset($group) ? $group->name : old('name')); ?>" required>
            </div>
            <div class="form-group col-md-6">
                <label for="zone_id">Zona <span class="text-danger">*</span></label>
                <select name="zone_id" id="zone_id" class="form-control" required>
                    <option value="">-- Seleccione --</option>
                    <?php $__currentLoopData = $zones; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $zone): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($zone->id); ?>" <?php echo e((isset($group) && $group->zone_id == $zone->id) ? 'selected' : ''); ?>><?php echo e($zone->name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group col-md-6">
                <label for="shift_id">Turno <span class="text-danger">*</span></label>
                <select name="shift_id" id="shift_id" class="form-control" required>
                    <option value="">-- Seleccione --</option>
                    <?php $__currentLoopData = $shifts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $shift): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($shift->id); ?>" <?php echo e((isset($group) && $group->shift_id == $shift->id) ? 'selected' : ''); ?>><?php echo e($shift->name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div class="form-group col-md-6">
                <label for="vehicle_id">Vehículo <span class="text-danger">*</span></label>
                <select name="vehicle_id" id="vehicle_id" class="form-control" required>
                    <option value="">-- Seleccione --</option>
                    <?php $__currentLoopData = $vehicles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $vehicle): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($vehicle->id); ?>" data-passengers="<?php echo e($vehicle->passengers ?? 0); ?>" <?php echo e((isset($group) && $group->vehicle_id == $vehicle->id) ? 'selected' : ''); ?>><?php echo e($vehicle->plate); ?> (Capacidad: <?php echo e($vehicle->passengers ?? 'N/A'); ?>)</option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
        </div>

        <div class="form-group">
            <label>Días de trabajo <span class="text-danger">*</span></label>
            <?php
                $selectedDays = isset($group) ? explode(',', $group->days) : (old('days') ?? []);
            ?>
            <div>
                <?php $__currentLoopData = ['Lunes','Martes','Miércoles','Jueves','Viernes','Sábado']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $d): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="checkbox" name="days[]" id="day_<?php echo e($d); ?>" value="<?php echo e($d); ?>" <?php echo e(in_array($d, $selectedDays) ? 'checked' : ''); ?>>
                        <label class="form-check-label" for="day_<?php echo e($d); ?>"><?php echo e($d); ?></label>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>


        <hr>
        <div id="dynamic-crew-block" style="display:none;">
            <p class="text-muted">Estos datos son para pre configuración no son obligatorios</p>
            <div id="dynamic-crew"></div>
        </div>
  
        

        <input type="hidden" id="conductores-data" value='<?php echo json_encode($conductores, 15, 512) ?>'>
        <input type="hidden" id="ayudantes-data" value='<?php echo json_encode($ayudantes, 15, 512) ?>'>
        <?php if(isset($crewConfig)): ?>
            <input type="hidden" id="crew-config" value='<?php echo json_encode($crewConfig, 15, 512) ?>'>
        <?php endif; ?>

    </div>
</div>
<?php /**PATH C:\Users\crist\OneDrive\Escritorio\TADS\PryFinal\PryRsuReciclaje\resources\views/personnel/employeegroup/template/form.blade.php ENDPATH**/ ?>