<?php $__env->startSection('title', 'Dashboard de Asistencias'); ?>

<?php $__env->startSection('content_header'); ?>
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="h3 mb-1 font-weight-bold">
                <i class="fas fa-clock text-success"></i> Dashboard de Asistencias
            </h1>
            <p class="text-muted mb-0">Control en tiempo real de las asistencias del día <?php echo e(date('d/m/Y')); ?></p>
        </div>
        <div class="btn-group">
            <button class="btn btn-success" data-toggle="modal" data-target="#clockInModal">
                <i class="fas fa-sign-in-alt"></i> Registrar Entrada
            </button>
            <button class="btn btn-warning" data-toggle="modal" data-target="#clockOutModal">
                <i class="fas fa-sign-out-alt"></i> Registrar Salida
            </button>
            <a href="<?php echo e(route('attendance-kiosk.index')); ?>" class="btn btn-info" target="_blank">
                <i class="fas fa-desktop"></i> Kiosco
            </a>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <!-- Estadísticas del Día -->
    <div class="row">
        <div class="col-lg-3 col-md-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3><?php echo e($statistics['entries_today']); ?></h3>
                    <p>Entradas Registradas</p>
                </div>
                <div class="icon">
                    <i class="fas fa-sign-in-alt"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3><?php echo e($statistics['exits_today']); ?></h3>
                    <p>Salidas Registradas</p>
                </div>
                <div class="icon">
                    <i class="fas fa-sign-out-alt"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3><?php echo e($statistics['without_entry']); ?></h3>
                    <p>Sin Entrada</p>
                </div>
                <div class="icon">
                    <i class="fas fa-user-times"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3><?php echo e($statistics['attendance_rate']); ?>%</h3>
                    <p>Tasa de Asistencia</p>
                </div>
                <div class="icon">
                    <i class="fas fa-chart-pie"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Entradas del Día -->
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-sign-in-alt text-success"></i> Entradas del Día
                    </h3>
                    <div class="card-tools">
                        <span class="badge badge-success"><?php echo e($entriesToday->count()); ?> empleados</span>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive" style="max-height: 400px;">
                        <table class="table table-sm table-striped">
                            <thead class="thead-light sticky-top">
                                <tr>
                                    <th>Empleado</th>
                                    <th>Hora</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $entriesToday; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $entry): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <tr>
                                        <td>
                                            <strong><?php echo e($entry->employee->names); ?> <?php echo e($entry->employee->lastnames); ?></strong>
                                            <br>
                                            <small class="text-muted"><?php echo e($entry->employee->dni); ?></small>
                                        </td>
                                        <td>
                                            <span class="badge badge-success">
                                                <?php echo e($entry->check_in ? \Carbon\Carbon::parse($entry->check_in)->format('H:i') : 'N/A'); ?>

                                            </span>
                                        </td>
                                        <td>
                                            <?php
                                                $hasExit = $exitsToday->where('employee_id', $entry->employee_id)->isNotEmpty();
                                            ?>
                                            <?php if($hasExit): ?>
                                                <span class="badge badge-primary">Completo</span>
                                            <?php else: ?>
                                                <span class="badge badge-warning">Sin salida</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr>
                                        <td colspan="3" class="text-center text-muted py-3">
                                            No hay entradas registradas
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Salidas del Día -->
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-sign-out-alt text-warning"></i> Salidas del Día
                    </h3>
                    <div class="card-tools">
                        <span class="badge badge-warning"><?php echo e($exitsToday->count()); ?> empleados</span>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive" style="max-height: 400px;">
                        <table class="table table-sm table-striped">
                            <thead class="thead-light sticky-top">
                                <tr>
                                    <th>Empleado</th>
                                    <th>Hora</th>
                                    <th>Duración</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $exitsToday; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $exit): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <?php
                                        $entry = $entriesToday->where('employee_id', $exit->employee_id)->first();
                                        $duration = 0;
                                        if ($entry && $entry->check_in && $exit->check_out) {
                                            $duration = \Carbon\Carbon::parse($entry->check_in)->diffInHours(\Carbon\Carbon::parse($exit->check_out));
                                        }
                                    ?>
                                    <tr>
                                        <td>
                                            <strong><?php echo e($exit->employee->names); ?> <?php echo e($exit->employee->lastnames); ?></strong>
                                            <br>
                                            <small class="text-muted"><?php echo e($exit->employee->dni); ?></small>
                                        </td>
                                        <td>
                                            <span class="badge badge-warning">
                                                <?php echo e($exit->check_out ? \Carbon\Carbon::parse($exit->check_out)->format('H:i') : 'N/A'); ?>

                                            </span>
                                        </td>
                                        <td>
                                            <?php if($entry): ?>
                                                <span class="badge badge-info"><?php echo e($duration); ?>h</span>
                                            <?php else: ?>
                                                <span class="badge badge-danger">Sin entrada</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr>
                                        <td colspan="3" class="text-center text-muted py-3">
                                            No hay salidas registradas
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Empleados Sin Registro -->
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-user-times text-danger"></i> Empleados Sin Entrada
                    </h3>
                    <div class="card-tools">
                        <span class="badge badge-danger"><?php echo e($employeesWithoutEntry->count()); ?> empleados</span>
                    </div>
                </div>
                <div class="card-body">
                    <?php if($employeesWithoutEntry->count() > 0): ?>
                        <div class="row">
                            <?php $__currentLoopData = $employeesWithoutEntry; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $employee): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="col-md-3 mb-2">
                                    <div class="card card-outline card-danger">
                                        <div class="card-body p-2">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <strong><?php echo e($employee->names); ?> <?php echo e($employee->lastnames); ?></strong>
                                                    <br>
                                                    <small class="text-muted"><?php echo e($employee->dni); ?></small>
                                                </div>
                                                <button class="btn btn-sm btn-success" 
                                                        onclick="quickClockIn(<?php echo e($employee->id); ?>)">
                                                    <i class="fas fa-sign-in-alt"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center text-success">
                            <i class="fas fa-check-circle fa-3x mb-3"></i>
                            <h5>¡Todos los empleados han marcado entrada!</h5>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Clock In -->
    <div class="modal fade" id="clockInModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Registrar Entrada</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <form id="clockInForm">
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Empleado</label>
                            <select class="form-control select2" name="employee_id" required>
                                <option value="">Seleccionar...</option>
                                <?php $__currentLoopData = $employeesWithoutEntry; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $employee): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($employee->id); ?>">
                                        <?php echo e($employee->names); ?> <?php echo e($employee->lastnames); ?> - <?php echo e($employee->dni); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Fecha y Hora</label>
                            <input type="datetime-local" class="form-control" name="datetime" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">Registrar Entrada</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Clock Out -->
    <div class="modal fade" id="clockOutModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Registrar Salida</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <form id="clockOutForm">
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Empleado</label>
                            <select class="form-control select2" name="employee_id" required>
                                <option value="">Seleccionar...</option>
                                <?php $__currentLoopData = $employeesWithoutExit; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $entry): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($entry->employee_id); ?>">
                                        <?php echo e($entry->employee->names); ?> <?php echo e($entry->employee->lastnames); ?> - <?php echo e($entry->employee->dni); ?>

                                        (Entrada: <?php echo e($entry->check_in ? \Carbon\Carbon::parse($entry->check_in)->format('H:i') : 'N/A'); ?>)
                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Fecha y Hora</label>
                            <input type="datetime-local" class="form-control" name="datetime" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-warning">Registrar Salida</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('css'); ?>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
    <style>
        .sticky-top {
            position: sticky;
            top: 0;
            z-index: 10;
        }
    </style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('js'); ?>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            $('.select2').select2();
            
            // Set current datetime
            const now = new Date();
            now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
            $('input[name="datetime"]').val(now.toISOString().slice(0, 16));

            // Auto refresh every 30 seconds
            setInterval(() => location.reload(), 30000);
        });

        function quickClockIn(employeeId) {
            $.post('<?php echo e(route("personnel.attendances.clock-in")); ?>', {
                employee_id: employeeId,
                datetime: new Date().toISOString().slice(0, 19),
                _token: '<?php echo e(csrf_token()); ?>'
            }).done(function(response) {
                if (response.success) {
                    Swal.fire('¡Éxito!', response.message, 'success');
                    setTimeout(() => location.reload(), 1000);
                }
            }).fail(function(xhr) {
                Swal.fire('Error', xhr.responseJSON.message, 'error');
            });
        }

        $('#clockInForm').on('submit', function(e) {
            e.preventDefault();
            $.post('<?php echo e(route("personnel.attendances.clock-in")); ?>', $(this).serialize() + '&_token=<?php echo e(csrf_token()); ?>')
                .done(function(response) {
                    $('#clockInModal').modal('hide');
                    Swal.fire('¡Éxito!', response.message, 'success');
                    setTimeout(() => location.reload(), 1000);
                });
        });

        $('#clockOutForm').on('submit', function(e) {
            e.preventDefault();
            $.post('<?php echo e(route("personnel.attendances.clock-out")); ?>', $(this).serialize() + '&_token=<?php echo e(csrf_token()); ?>')
                .done(function(response) {
                    $('#clockOutModal').modal('hide');
                    Swal.fire('¡Éxito!', response.message, 'success');
                    setTimeout(() => location.reload(), 1000);
                });
        });
    </script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('adminlte::page', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\crist\OneDrive\Escritorio\TADS\PryFinal\PryRsuReciclaje\resources\views/personnel/attendances/dashboard.blade.php ENDPATH**/ ?>