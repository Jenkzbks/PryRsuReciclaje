<div class="mb-3">
    <h6>Datos Generales</h6>
    <table class="table table-bordered mb-2">
        <tr>
            <th>Fecha</th>
            <th>Estado</th>
            <th>Zona</th>
            <th>Turno</th>
            <th>Vehículo</th>
        </tr>
        <tr>
            <td><?php echo e($scheduling->date); ?></td>
            <td>
                <?php if($scheduling->status == 0): ?>
                    <span class="badge badge-danger">Cancelado</span>
                <?php elseif($scheduling->status == 2): ?>
                    <span class="badge badge-warning">Reprogramado</span>
                <?php else: ?>
                    <span class="badge badge-success">Programado</span>
                <?php endif; ?>
            </td>
            <td><?php echo e($scheduling->group->zone->name ?? '-'); ?></td>
            <td><?php echo e($scheduling->shift->name ?? '-'); ?></td>
            <td><?php echo e($scheduling->vehicle->plate ?? '-'); ?></td>
        </tr>
    </table>
</div>
<div class="mb-3">
    <h6>Personal Asignado</h6>
    <table class="table table-bordered mb-2">
        <tr>
            <th>Rol</th>
            <th>Nombre</th>
        </tr>
        <?php $__currentLoopData = $scheduling->details; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $d): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr>
                <td><?php echo e($d->employee->type->name ?? '-'); ?></td>
                <td><?php echo e($d->employee->lastnames); ?> <?php echo e($d->employee->names); ?></td>
            </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </table>
</div>
<div>
    <h6>Historial de Cambios</h6>
    <table class="table table-bordered">
        <tr>
            <th>Fecha del Cambio</th>
            <th>Valor Anterior</th>
            <th>Valor Nuevo</th>
            <th>Motivo</th>
        </tr>
        <?php $__empty_1 = true; $__currentLoopData = $changes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <tr>
                <td><?php echo e($c->created_at->format('d/m/Y')); ?></td>
                <td><?php echo e($c->old_value); ?></td>
                <td><?php echo e($c->new_value); ?></td>
                <td><?php echo e($c->reason->name ?? '-'); ?></td>
            </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr><td colspan="4" class="text-center text-muted">Sin cambios registrados</td></tr>
        <?php endif; ?>
    </table>
</div>
<?php /**PATH C:\Users\crist\OneDrive\Escritorio\TADS\PryFinal\PryRsuReciclaje\resources\views/schedulings/partials/detalle.blade.php ENDPATH**/ ?>