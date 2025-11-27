<div class="row">
    <div class="col-12">
        <div class="form-group">
            <label for="name">Nombre del Turno <span class="text-danger">*</span></label>
            <input type="text" name="name" id="name" class="form-control" placeholder="Ingrese el nombre del turno" value="<?php echo e(isset($shift) ? $shift->name : old('name')); ?>" required>
            <small class="form-text text-muted">Ejemplo: Turno Mañana, Turno Tarde, Turno Noche</small>
        </div>

        <div class="form-row">
            <div class="form-group col-md-6">
                <label for="hora_in">Hora de Entrada <span class="text-danger">*</span></label>
                <div class="input-group">
                    <input type="time" name="hora_in" id="hora_in" class="form-control" value="<?php echo e(isset($shift) ? $shift->hora_in : old('hora_in')); ?>" required>
                </div>
                <small class="form-text text-muted">Formato de 24 horas</small>
            </div>
            <div class="form-group col-md-6">
                <label for="hora_out">Hora de Salida <span class="text-danger">*</span></label>
                <div class="input-group">
                    <input type="time" name="hora_out" id="hora_out" class="form-control" value="<?php echo e(isset($shift) ? $shift->hora_out : old('hora_out')); ?>" required>
                </div>
                <small class="form-text text-muted">Formato de 24 horas</small>
            </div>
        </div>

        <div class="form-group mt-3">
            <label for="description">Descripción</label>
            <textarea name="description" id="description" class="form-control" placeholder="Ingrese una descripción del turno (opcional)" rows="3"><?php echo e(isset($shift) ? $shift->description : old('description')); ?></textarea>
            <small class="form-text text-muted">Descripción de las características del turno</small>
        </div>

        <div class="callout callout-info mt-3 p-3" style="background:#17a2b8;color:#fff;border-radius:4px;">
            <h6><i class="fas fa-info-circle"></i> Nota:</h6>
            <p class="mb-0">Configure los horarios de entrada y salida para este turno.</p>
        </div>
    </div>
</div>
<?php /**PATH C:\Users\crist\OneDrive\Escritorio\TADS\PryFinal\PryRsuReciclaje\resources\views/admin/shifts/template/form.blade.php ENDPATH**/ ?>