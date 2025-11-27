<?php $__env->startSection('title','Editar Programación'); ?>

<?php $__env->startSection('content_header'); ?>
  <h1>Editar Programación</h1>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="card">
  <form method="POST" action="<?php echo e(route('admin.schedulings.update',$scheduling)); ?>">
    <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
    <div class="card-body">
      <?php if($errors->any()): ?>
        <div class="alert alert-danger">
          <ul class="mb-0">
            <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><li><?php echo e($e); ?></li><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </ul>
        </div>
      <?php endif; ?>

      <!-- Información General -->
      <div class="row mb-4">
        <div class="col-md-6">
          <div class="form-group">
            <label>Fecha de Programación</label>
            <input type="date" name="date" class="form-control" value="<?php echo e($scheduling->date); ?>" required>
          </div>
        </div>
        <div class="col-md-6">
          <div class="form-group">
            <label>Notas</label>
            <input type="text" name="notes" class="form-control" value="<?php echo e($scheduling->notes ?? ''); ?>" placeholder="Notas opcionales">
          </div>
        </div>
      </div>

      <!-- Información del Grupo (solo lectura) -->
      <div class="row mb-4">
        <div class="col-md-12">
          <div class="alert alert-info py-2">
            <div class="row">
              <div class="col-md-3">
                <strong>Grupo:</strong> <?php echo e($scheduling->group->name ?? '-'); ?>

              </div>
              <div class="col-md-3">
                <strong>Zona:</strong> <?php echo e($scheduling->group->zone->name ?? '-'); ?>

              </div>
              <div class="col-md-3">
                <strong>Días del grupo:</strong> <?php echo e($scheduling->group->days ?? '-'); ?>

              </div>
              <div class="col-md-3">
                <strong>Configuración original</strong>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Carteles de Turno -->
      <div class="row mb-4">
        <div class="col-md-6">
          <div class="card border-primary">
            <div class="card-header bg-primary text-white">
              <strong>Turno Actual</strong>
            </div>
            <div class="card-body">
              <p class="mb-1"><?php echo e($scheduling->shift->name ?? $scheduling->group->shift->name ?? 'Sin turno asignado'); ?></p>
              <small class="text-muted">
                <?php if($scheduling->shift_id && $scheduling->shift_id != $scheduling->group->shift_id): ?>
                  <i class="fas fa-exclamation-triangle text-warning"></i> Modificado respecto al grupo original
                <?php else: ?>
                  Turno del grupo original
                <?php endif; ?>
              </small>
            </div>
          </div>
        </div>
        <div class="col-md-6">
          <div class="card border-warning">
            <div class="card-header bg-warning text-dark">
              <strong>Nuevo Turno</strong>
            </div>
            <div class="card-body">
              <select name="shift_id" class="form-control form-control-sm">
                <option value="">-- Mantener turno actual --</option>
                <?php $__currentLoopData = $shifts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $shift): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <option value="<?php echo e($shift->id); ?>" 
                    <?php echo e($scheduling->shift_id == $shift->id ? 'selected' : (old('shift_id') == $shift->id ? 'selected' : '')); ?>>
                    <?php echo e($shift->name); ?>

                  </option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </select>
              <small class="text-muted">Seleccione para cambiar el turno</small>
            </div>
          </div>
        </div>
      </div>

      <!-- Carteles de Vehículo -->
      <div class="row mb-4">
        <div class="col-md-6">
          <div class="card border-primary">
            <div class="card-header bg-primary text-white">
              <strong>Vehículo Actual</strong>
            </div>
            <div class="card-body">
              <p class="mb-1"><?php echo e($scheduling->vehicle->plate ?? $scheduling->group->vehicle->plate ?? 'Sin vehículo asignado'); ?></p>
              <small class="text-muted">
                <?php if($scheduling->vehicle_id && $scheduling->vehicle_id != $scheduling->group->vehicle_id): ?>
                  <i class="fas fa-exclamation-triangle text-warning"></i> Modificado respecto al grupo original
                <?php else: ?>
                  Vehículo del grupo original
                <?php endif; ?>
              </small>
            </div>
          </div>
        </div>
        <div class="col-md-6">
          <div class="card border-warning">
            <div class="card-header bg-warning text-dark">
              <strong>Nuevo Vehículo</strong>
            </div>
            <div class="card-body">
              <select name="vehicle_id" class="form-control form-control-sm">
                <option value="">-- Mantener vehículo actual --</option>
                <?php $__currentLoopData = $vehicles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $vehicle): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <option value="<?php echo e($vehicle->id); ?>" 
                    <?php echo e($scheduling->vehicle_id == $vehicle->id ? 'selected' : (old('vehicle_id') == $vehicle->id ? 'selected' : '')); ?>>
                    <?php echo e($vehicle->plate); ?>

                  </option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </select>
              <small class="text-muted">Seleccione para cambiar el vehículo</small>
            </div>
          </div>
        </div>
      </div>

      <!-- Carteles de Personal -->
      <div class="row mb-4">
        <div class="col-md-6">
          <div class="card border-primary">
            <div class="card-header bg-primary text-white">
              <strong>Personal Actual</strong>
            </div>
            <div class="card-body">
              <?php
                $driverDetail = $scheduling->details->firstWhere('employee.type_id', 1);
                $assistantDetails = $scheduling->details->filter(fn($d) => optional($d->employee)->type_id == 2)->values();
              ?>
              
              <div class="mb-2">
                <strong>Conductor:</strong><br>
                <?php echo e($driverDetail->employee->lastnames ?? '-'); ?> <?php echo e($driverDetail->employee->names ?? '-'); ?>

              </div>
              
              <div class="mb-2">
                <strong>Ayudante 1:</strong><br>
                <?php echo e($assistantDetails->get(0)->employee->lastnames ?? '-'); ?> <?php echo e($assistantDetails->get(0)->employee->names ?? '-'); ?>

              </div>
              
              <div class="mb-0">
                <strong>Ayudante 2:</strong><br>
                <?php echo e($assistantDetails->get(1)->employee->lastnames ?? '-'); ?> <?php echo e($assistantDetails->get(1)->employee->names ?? '-'); ?>

              </div>
            </div>
          </div>
        </div>
        <div class="col-md-6">
          <div class="card border-warning">
            <div class="card-header bg-warning text-dark">
              <strong>Nuevo Personal</strong>
            </div>
            <div class="card-body">
              <div class="form-group">
                <label class="small">Conductor</label>
                <select name="driver_id" class="form-control form-control-sm">
                  <option value="">-- Mantener conductor actual --</option>
                  <?php $__currentLoopData = $drivers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $driver): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                      <?php if(optional($driver)->type_id == 1): ?>
                      <option value="<?php echo e($driver->id); ?>" 
                        <?php echo e($selectedDriverId == $driver->id ? 'selected' : (old('driver_id') == $driver->id ? 'selected' : '')); ?>>
                        <?php echo e($driver->lastnames); ?> <?php echo e($driver->names); ?>

                      </option>
                      <?php endif; ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
              </div>
              
              <div class="form-group">
                <label class="small">Ayudante 1</label>
                <select name="assistant1_id" class="form-control form-control-sm">
                  <option value="">-- Mantener ayudante 1 actual --</option>
                  <?php $__currentLoopData = $assistants; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $assistant): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php if(optional($assistant)->type_id == 2): ?>
                    <option value="<?php echo e($assistant->id); ?>" 
                      <?php echo e($selectedA1Id == $assistant->id ? 'selected' : (old('assistant1_id') == $assistant->id ? 'selected' : '')); ?>>
                      <?php echo e($assistant->lastnames); ?> <?php echo e($assistant->names); ?>

                    </option>
                    <?php endif; ?>
                  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
              </div>
              
              <div class="form-group mb-0">
                <label class="small">Ayudante 2</label>
                <select name="assistant2_id" class="form-control form-control-sm">
                  <option value="">-- Mantener ayudante 2 actual --</option>
                  <?php $__currentLoopData = $assistants; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $assistant): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php if(optional($assistant)->type_id == 2): ?>
                    <option value="<?php echo e($assistant->id); ?>" 
                      <?php echo e($selectedA2Id == $assistant->id ? 'selected' : (old('assistant2_id') == $assistant->id ? 'selected' : '')); ?>>
                      <?php echo e($assistant->lastnames); ?> <?php echo e($assistant->names); ?>

                    </option>
                    <?php endif; ?>
                  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Cambios Registrados -->
      <div class="card border-info mb-4">
        <div class="card-header bg-info text-white">
          <strong>Resumen de Cambios</strong>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-sm table-bordered">
              <thead>
                <tr class="bg-light">
                  <th>Tipo de Cambio</th>
                  <th>Valor Original del Grupo</th>
                  <th>Valor Actual</th>
                  <th>Valor Nuevo</th>
                  <th>Estado</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>Turno</td>
                  <td><?php echo e($scheduling->group->shift->name ?? '-'); ?></td>
                  <td><?php echo e($scheduling->shift->name ?? $scheduling->group->shift->name ?? '-'); ?></td>
                  <td id="newShiftPreview">-</td>
                  <td>
                    <?php if($scheduling->shift_id && $scheduling->shift_id != $scheduling->group->shift_id): ?>
                      <span class="badge badge-warning">Modificado</span>
                    <?php else: ?>
                      <span class="badge badge-secondary">Original</span>
                    <?php endif; ?>
                  </td>
                </tr>
                <tr>
                  <td>Vehículo</td>
                  <td><?php echo e($scheduling->group->vehicle->plate ?? '-'); ?></td>
                  <td><?php echo e($scheduling->vehicle->plate ?? $scheduling->group->vehicle->plate ?? '-'); ?></td>
                  <td id="newVehiclePreview">-</td>
                  <td>
                    <?php if($scheduling->vehicle_id && $scheduling->vehicle_id != $scheduling->group->vehicle_id): ?>
                      <span class="badge badge-warning">Modificado</span>
                    <?php else: ?>
                      <span class="badge badge-secondary">Original</span>
                    <?php endif; ?>
                  </td>
                </tr>
                <tr>
                  <td>Personal</td>
                  <td><?php echo e($scheduling->group->employees->count() ?? 0); ?> trabajadores</td>
                  <td><?php echo e($scheduling->details->count()); ?> trabajadores</td>
                  <td id="newPersonnelPreview">-</td>
                  <td>
                    <?php if($scheduling->details->count() > 0): ?>
                      <span class="badge badge-info">Asignado</span>
                    <?php else: ?>
                      <span class="badge badge-secondary">Sin personal</span>
                    <?php endif; ?>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>

    </div>
    <div class="card-footer text-right">
      <a href="<?php echo e(route('admin.schedulings.index')); ?>" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left"></i> Volver
      </a>
      <button type="submit" class="btn btn-success">
        <i class="fas fa-save"></i> Guardar Cambios
      </button>
    </div>
  </form>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('js'); ?>
<script>
  // Actualizar vista previa de cambios
  document.addEventListener('DOMContentLoaded', function() {
    // Vista previa para turno
    const shiftSelect = document.querySelector('select[name="shift_id"]');
    const newShiftPreview = document.getElementById('newShiftPreview');
    
    shiftSelect.addEventListener('change', function() {
      if (this.value) {
        newShiftPreview.textContent = this.options[this.selectedIndex].text;
        newShiftPreview.className = 'text-warning font-weight-bold';
      } else {
        newShiftPreview.textContent = '-';
        newShiftPreview.className = '';
      }
    });

    // Vista previa para vehículo
    const vehicleSelect = document.querySelector('select[name="vehicle_id"]');
    const newVehiclePreview = document.getElementById('newVehiclePreview');
    
    vehicleSelect.addEventListener('change', function() {
      if (this.value) {
        newVehiclePreview.textContent = this.options[this.selectedIndex].text;
        newVehiclePreview.className = 'text-warning font-weight-bold';
      } else {
        newVehiclePreview.textContent = '-';
        newVehiclePreview.className = '';
      }
    });

    // Vista previa para personal
    const driverSelect = document.querySelector('select[name="driver_id"]');
    const assistant1Select = document.querySelector('select[name="assistant1_id"]');
    const assistant2Select = document.querySelector('select[name="assistant2_id"]');
    const newPersonnelPreview = document.getElementById('newPersonnelPreview');

    function updatePersonnelPreview() {
      const changes = [];
      
      if (driverSelect.value) {
        changes.push('Nuevo conductor');
      }
      if (assistant1Select.value) {
        changes.push('Nuevo ayudante 1');
      }
      if (assistant2Select.value) {
        changes.push('Nuevo ayudante 2');
      }
      
      if (changes.length > 0) {
        newPersonnelPreview.textContent = changes.join(', ');
        newPersonnelPreview.className = 'text-warning font-weight-bold';
      } else {
        newPersonnelPreview.textContent = '-';
        newPersonnelPreview.className = '';
      }
    }

    driverSelect.addEventListener('change', updatePersonnelPreview);
    assistant1Select.addEventListener('change', updatePersonnelPreview);
    assistant2Select.addEventListener('change', updatePersonnelPreview);

    // Inicializar vistas previas con valores actuales
    if (shiftSelect.value) {
      shiftSelect.dispatchEvent(new Event('change'));
    }
    if (vehicleSelect.value) {
      vehicleSelect.dispatchEvent(new Event('change'));
    }
    updatePersonnelPreview();
  });
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('adminlte::page', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\crist\OneDrive\Escritorio\TADS\PryFinal\PryRsuReciclaje\resources\views/schedulings/edit.blade.php ENDPATH**/ ?>