<?php if($schedulings->isNotEmpty()): ?>
    <?php $__currentLoopData = $schedulings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $zoneName => $zoneSchedulings): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="col-md-4 mb-4">
            <?php
                $isComplete = false;
                $totalEmployees = 0;
                $totalAttendances = 0;
                $selectedDate = $request->date ?? date('Y-m-d');
                $firstScheduling = $zoneSchedulings->first(); // Tomar la primera scheduling para el enlace de edición
                foreach($zoneSchedulings as $scheduling) {
                    $group = $scheduling->group;
                    if ($group) {
                        $employees = $group->employees;
                        $totalEmployees += $employees->count();
                        foreach($employees as $employee) {
                            $hasAttendance = $employee->attendances()->where('date', $selectedDate)->exists();
                            if ($hasAttendance) {
                                $totalAttendances++;
                            }
                        }
                    }
                }
                if ($totalEmployees > 0 && $totalAttendances >= $totalEmployees) {
                    $isComplete = true;
                }
            ?>
            <div class="card <?php echo e($isComplete ? 'border-success' : 'border-danger'); ?>" style="border-width:2px;">
                <div class="card-body text-center">
                    <h5>Zona: <?php echo e($zoneName); ?></h5>
                    <p>Empleados: <?php echo e($totalEmployees); ?>, Asistencias: <?php echo e($totalAttendances); ?></p>
                    <p class="<?php echo e($isComplete ? 'text-success' : 'text-danger'); ?>">
                        <?php echo e($isComplete ? 'Grupo completo y listo para operar' : 'Faltan integrantes por registrar asistencia'); ?>

                    </p>
                    <?php if(!$isComplete && $firstScheduling): ?>
                        <button class="btn btn-warning w-100 edit-scheduling-btn" data-url="<?php echo e(route('admin.schedulings.edit-modal', $firstScheduling->id)); ?>">
                            <i class="fas fa-sync-alt"></i>
                        </button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<?php else: ?>
    <div class="col-12">
        <div class="alert alert-info">
            No hay programaciones para la fecha y turno seleccionados.
        </div>
    </div>
<?php endif; ?><?php /**PATH C:\Users\crist\OneDrive\Escritorio\TADS\PryFinal\PryRsuReciclaje\resources\views/admin/zones.blade.php ENDPATH**/ ?>