<?php $__env->startSection('title', 'Asistencias'); ?>

<?php $__env->startSection('content_header'); ?>
    <h1 class="m-0">Asistencias</h1>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <div class="card">
        <div class="card-body">
            <form method="GET" class="mb-3">
                <div class="form-row">
                    <div class="col">
                        <input type="text" name="search" class="form-control" placeholder="Buscar empleado" value="<?php echo e(request('search')); ?>">
                    </div>
                    <div class="col">
                        <input type="date" name="date" class="form-control" value="<?php echo e(request('date')); ?>">
                    </div>
                    <div class="col-auto">
                        <button class="btn btn-primary">Filtrar</button>
                    </div>
                </div>
            </form>

            <table class="table table-sm table-striped">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Empleado</th>
                        <th>Fecha</th>
                        <th>Entrada</th>
                        <th>Salida</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $attendances; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $attendance): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td><?php echo e($attendance->id); ?></td>
                            <td><?php echo e($attendance->employee->names ?? 'N/A'); ?> <?php echo e($attendance->employee->lastnames ?? ''); ?></td>
                            <td><?php echo e($attendance->date ? $attendance->date->format('d/m/Y') : 'N/A'); ?></td>
                            <td><?php echo e($attendance->check_in ? $attendance->check_in->format('H:i:s') : 'N/A'); ?></td>
                            <td><?php echo e($attendance->check_out ? $attendance->check_out->format('H:i:s') : 'N/A'); ?></td>
                            <td>
                                <?php
                                    $statusColors = [
                                        'present' => 'success',
                                        'late' => 'warning', 
                                        'absent' => 'danger',
                                        'half_day' => 'info'
                                    ];
                                    $statusLabels = [
                                        'present' => 'Presente',
                                        'late' => 'Tarde',
                                        'absent' => 'Ausente', 
                                        'half_day' => 'Medio Día'
                                    ];
                                    $color = $statusColors[$attendance->status] ?? 'secondary';
                                    $label = $statusLabels[$attendance->status] ?? $attendance->status;
                                ?>
                                <span class="badge badge-<?php echo e($color); ?>"><?php echo e($label); ?></span>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="6" class="text-center">No hay registros</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>

            <?php echo e($attendances->links()); ?>

        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('adminlte::page', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\crist\OneDrive\Escritorio\TADS\PryFinal\PryRsuReciclaje\resources\views/attendances/index.blade.php ENDPATH**/ ?>