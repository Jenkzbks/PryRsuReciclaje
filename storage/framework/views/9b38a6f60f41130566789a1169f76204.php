<form method="POST" action="<?php echo e(route('admin.schedulings.update',$scheduling)); ?>">
<?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>

<input type="hidden" id="current_shift" value="<?php echo e($scheduling->turno_actual); ?>">
<input type="hidden" id="current_vehicle" value="<?php echo e($scheduling->vehiculo_actual); ?>">
<input type="hidden" name="date" value="<?php echo e($scheduling->date); ?>">

<div class="card shadow">
    <div class="card-body">

        
        <h5 class="mb-3">Cambio de Turno y Vehículo</h5>

        <div class="row mb-3">
            
            <div class="col-md-4">
                <div class="form-group">
                    <label class="font-weight-bold">Turno Actual</label>
                    <input type="text" class="form-control" value="<?php echo e($scheduling->turno_actual); ?>" readonly>
                </div>
            </div>

            
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="font-weight-bold">Nuevo Turno</label>
                            <select name="shift_id" class="form-control" id="shift_select">
                                <option value="">Seleccione un nuevo turno</option>
                                <?php $__currentLoopData = $shifts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $shift): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($shift->id); ?>" 
                                    <?php echo e($scheduling->shift_id == $shift->id ? 'selected' : ''); ?>>
                                    <?php echo e($shift->name); ?>

                                </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                    </div>

            
            <div class="col-md-1 d-flex align-items-end">
                <button type="button" class="btn btn-success w-100" id="add_shift_change">+</button>
            </div>
        </div>

        <div class="row mb-3">
            
            <div class="col-md-4">
                <div class="form-group">
                    <label class="font-weight-bold">Vehículo Actual</label>
                    <input type="text" class="form-control" value="<?php echo e($scheduling->vehiculo_actual); ?>" readonly>
                </div>
            </div>

            
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="font-weight-bold">Nuevo Vehículo</label>
                            <select name="vehicle_id" class="form-control" id="vehicle_select">
                                <option value="">Seleccione un nuevo vehículo</option>
                                <?php $__currentLoopData = $vehicles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $vehicle): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($vehicle->id); ?>"
                                    <?php echo e($scheduling->vehicle_id == $vehicle->id ? 'selected' : ''); ?>>
                                    <?php echo e($vehicle->plate); ?>

                                </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                    </div>

            
            <div class="col-md-1 d-flex align-items-end">
                <button type="button" class="btn btn-success w-100" id="add_vehicle_change">+</button>
            </div>
        </div>


        
        <h5 class="mt-4 mb-3">Cambio de Personal</h5>

        <div class="row">

            
            <div class="col-md-4">
                <div class="form-group">
                    <label class="font-weight-bold">Personal Actual</label>
                    <select class="form-control" id="personal_actual">
                        <option value="">Seleccione un personal</option>
                        <?php $__currentLoopData = $scheduling->details; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $detail): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php if($detail->employee): ?>
                                <?php
                                    $role = 'Asistente';
                                    $role_key = 'assistant2_id';
                                    if ($detail->employee->id == $selectedDriverId) { $role = 'Conductor'; $role_key = 'driver_id'; }
                                    elseif ($detail->employee->id == $selectedA1Id) { $role = 'Asistente 1'; $role_key = 'assistant1_id'; }
                                    elseif ($detail->employee->id == $selectedA2Id) { $role = 'Asistente 2'; $role_key = 'assistant2_id'; }
                                ?>
                                <option value="<?php echo e($detail->employee->id); ?>" data-type="<?php echo e($detail->employee->type_id); ?>" data-role="<?php echo e($role_key); ?>">
                                    <?php echo e($role); ?>: <?php echo e($detail->employee->names); ?> <?php echo e($detail->employee->lastnames); ?>

                                </option>
                            <?php endif; ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
            </div>

            
                    <div class="col-md-5">
                        <div class="form-group">
                            <label class="font-weight-bold">Nuevo Personal</label>
                            <select name="new_employee_id" class="form-control" id="nuevo_personal">
                                <option value="">Seleccione un nuevo personal</option>
                                <?php $__currentLoopData = $employees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $emp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($emp->id); ?>" data-type="<?php echo e($emp->type_id); ?>" data-busy="<?php echo e(in_array($emp->id, $busyEmployeeIds ?? []) ? '1' : '0'); ?>" style="display: none;">
                                        <?php echo e($emp->names); ?> <?php echo e($emp->lastnames); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                    </div>

            
            <div class="col-md-1 d-flex align-items-end">
                <button type="button" class="btn btn-success w-100" id="add_personal_change">+</button>
            </div>

        </div>


        
        <h5 class="mt-4">Cambios Registrados</h5>

        <div class="table-responsive mt-3">
            <table class="table table-bordered text-center">
                <thead class="bg-light">
                    <tr>
                        <th>Tipo de Cambio</th>
                        <th>Valor Anterior</th>
                        <th>Valor Nuevo</th>
                        <th>Notas</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody id="tabla-cambios">
                    
                </tbody>
            </table>
        </div>

        
        <div id="motivo_template" class="d-none">
            <select class="form-control form-control-sm motivo-select">
                <option value="">-- Seleccione un motivo --</option>
                <?php $__currentLoopData = $reasons; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $reason): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($reason->id); ?>"><?php echo e($reason->name); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>

    </div>

    <div class="card-footer text-right">
        <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">
            <i class="fas fa-arrow-left"></i> Cerrar
        </button>

        <button type="submit" class="btn btn-primary">
            Guardar Cambios
        </button>
    </div>
</div>

</form><?php /**PATH C:\Users\crist\OneDrive\Escritorio\TADS\PryFinal\PryRsuReciclaje\resources\views/admin/edit_modal.blade.php ENDPATH**/ ?>