<div class="card">
    <form id="massive-update-form" method="POST" action="<?php echo e(route('admin.schedulings.massive-update')); ?>">
        <?php echo csrf_field(); ?>

        <div class="card-body">

            <?php if($errors->any()): ?>
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li><?php echo e($e); ?></li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                </div>
            <?php endif; ?>

            <div class="row">

                
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Fecha de Inicio *</label>
                        <input type="date" name="from" class="form-control"
                               value="<?php echo e($massiveChange->from ?? now()->toDateString()); ?>" required>
                    </div>
                </div>

                
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Fecha de Fin *</label>
                        <input type="date" name="to" class="form-control"
                               value="<?php echo e($massiveChange->to ?? now()->toDateString()); ?>" required>
                    </div>
                </div>

                
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Zonas (Opcional)</label>
                        <select name="zones[]" class="form-control" multiple>
                            <?php $__currentLoopData = $zones; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $z): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($z->id); ?>"
                                    <?php if(in_array($z->id, $massiveChange->zones ?? [])): echo 'selected'; endif; ?>>
                                    <?php echo e($z->name); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                        <small class="text-muted">Dejar vacío para aplicar a todas las zonas</small>
                    </div>
                </div>

                
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Tipo de Cambio *</label>
                        <select id="change_type" name="type" class="form-control" required>
                            <option value="Cambio de Conductor" selected>Cambio de Conductor</option>
                            <option value="Cambio de Ayudante">Cambio de Ayudante</option>
                            <option value="Cambio de Turno">Cambio de Turno</option>
                            <option value="Cambio de Vehiculo">Cambio de Vehiculo</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="row">
                
                <div id="block_conductor" class="w-100 change-block" style="display:flex">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Conductor Actual</label>
                            <?php if(isset($drivers)): ?>
                                <select id="current_driver" class="form-control current-select">
                                    <option value="">-- Seleccione --</option>
                                    <?php $__currentLoopData = $drivers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $d): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($d->id); ?>" <?php if(($massiveChange->old_driver ?? null) == $d->id): echo 'selected'; endif; ?>><?php echo e($d->name); ?> - <?php echo e($d->document); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                                <input type="hidden" name="old_driver" value="<?php echo e($massiveChange->old_driver ?? ''); ?>">
                            <?php else: ?>
                                <p class="form-control-plaintext text-muted">No hay lista de conductores</p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Conductor Reemplazo *</label>
                            <?php if(isset($drivers)): ?>
                                <select id="new_driver" name="new_driver" class="form-control new-select">
                                    <option value="">-- Seleccione --</option>
                                    <?php $__currentLoopData = $drivers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $d): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($d->id); ?>" <?php if(($massiveChange->new_driver ?? null) == $d->id): echo 'selected'; endif; ?>><?php echo e($d->name); ?> - <?php echo e($d->document); ?> (<?php echo e($d->contract_status ?? ''); ?>)</option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            <?php else: ?>
                                <p class="form-control-plaintext text-muted">No hay lista de conductores</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div id="block_ayudante" class="w-100 change-block" style="display:none">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Ayudante Actual</label>
                            <?php if(isset($assistants)): ?>
                                <select id="current_assistant" class="form-control current-select">
                                    <option value="">-- Seleccione --</option>
                                    <?php $__currentLoopData = $assistants; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $a): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($a->id); ?>" <?php if(($massiveChange->old_assistant ?? null) == $a->id): echo 'selected'; endif; ?>><?php echo e($a->lastnames); ?> <?php echo e($a->names); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                                <input type="hidden" name="old_assistant" value="<?php echo e($massiveChange->old_assistant ?? ''); ?>">
                            <?php else: ?>
                                <p class="form-control-plaintext text-muted">No hay lista de ayudantes</p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Ayudante Reemplazo *</label>
                            <?php if(isset($assistants)): ?>
                                <select id="new_assistant" name="new_assistant" class="form-control new-select">
                                    <option value="">-- Seleccione --</option>
                                    <?php $__currentLoopData = $assistants; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $a): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($a->id); ?>" <?php if(($massiveChange->new_assistant ?? null) == $a->id): echo 'selected'; endif; ?>><?php echo e($a->lastnames); ?> <?php echo e($a->names); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            <?php else: ?>
                                <p class="form-control-plaintext text-muted">No hay lista de ayudantes</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div id="block_turno" class="w-100 change-block" style="display:none">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Turno Actual</label>
                            <?php if(isset($shifts)): ?>
                                <select id="current_shift" class="form-control current-select">
                                    <option value="">-- Seleccione --</option>
                                    <?php $__currentLoopData = $shifts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($s->id); ?>" <?php if(($massiveChange->old_shift ?? null) == $s->id): echo 'selected'; endif; ?>><?php echo e($s->name); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                                <input type="hidden" name="old_shift" value="<?php echo e($massiveChange->old_shift ?? ''); ?>">
                            <?php else: ?>
                                <p class="form-control-plaintext text-muted">No hay lista de turnos</p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Turno Reemplazo *</label>
                            <?php if(isset($shifts)): ?>
                                <select id="new_shift" name="new_shift" class="form-control new-select">
                                    <option value="">-- Seleccione --</option>
                                    <?php $__currentLoopData = $shifts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($s->id); ?>" <?php if(($massiveChange->new_shift ?? null) == $s->id): echo 'selected'; endif; ?>><?php echo e($s->name); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            <?php else: ?>
                                <p class="form-control-plaintext text-muted">No hay lista de turnos</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div id="block_vehiculo" class="w-100 change-block" style="display:none">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Vehículo Actual</label>
                            <?php if(isset($vehicles)): ?>
                                <select id="current_vehicle" class="form-control current-select">
                                    <option value="">-- Seleccione --</option>
                                    <?php $__currentLoopData = $vehicles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($v->id); ?>" <?php if(($massiveChange->old_vehicle ?? null) == $v->id): echo 'selected'; endif; ?>><?php echo e($v->plate ?? $v->name ?? $v->code); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                                <input type="hidden" name="old_vehicle" value="<?php echo e($massiveChange->old_vehicle ?? ''); ?>">
                            <?php else: ?>
                                <p class="form-control-plaintext text-muted">No hay lista de vehículos</p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Vehículo Reemplazo *</label>
                            <?php if(isset($vehicles)): ?>
                                <select id="new_vehicle" name="new_vehicle" class="form-control new-select">
                                    <option value="">-- Seleccione --</option>
                                    <?php $__currentLoopData = $vehicles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($v->id); ?>" <?php if(($massiveChange->new_vehicle ?? null) == $v->id): echo 'selected'; endif; ?>><?php echo e($v->plate ?? $v->name ?? $v->code); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            <?php else: ?>
                                <p class="form-control-plaintext text-muted">No hay lista de vehículos</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            
            <div class="form-group mt-3">
                <label>Motivo del Cambio Masivo *</label>
                <textarea name="reason" class="form-control" rows="3" required><?php echo e($massiveChange->reason ?? ''); ?></textarea>
            </div>

        </div>

        <div class="card-footer d-flex">
            <button type="submit" id="massive-update-submit" class="btn btn-success mr-2">
                <i class="fa fa-save"></i> Guardar
            </button>

            <button type="button" class="btn btn-danger" data-dismiss="modal">
                <i class="fa fa-times"></i> Cancelar
            </button>
        </div>

    </form>
</div>
    </form>
</div>

<script>
    (function(){
        const form = document.getElementById('massive-update-form');
        if (!form) return;

        form.addEventListener('submit', function(e){
            // Prevent immediate submit so we can show a confirmation dialog
            const confirmed = confirm('¿Confirmas aplicar el cambio masivo a las programaciones en el rango seleccionado? Esta acción actualizará las programaciones existentes.');
            if (!confirmed) {
                e.preventDefault();
                return false;
            }

            // Disable submit to avoid double submits
            const btn = document.getElementById('massive-update-submit');
            if (btn) {
                btn.disabled = true;
                btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Guardando...';
            }
        });
    })();
</script>
<?php /**PATH C:\Users\crist\OneDrive\Escritorio\TADS\PryFinal\PryRsuReciclaje\resources\views/schedulings/_edit_massive_form.blade.php ENDPATH**/ ?>