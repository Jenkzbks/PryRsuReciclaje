<?php $__env->startSection('title', 'Gestión de Vacaciones'); ?>

<?php $__env->startSection('content_header'); ?>
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="h3 mb-1 font-weight-bold">
                <i class="fas fa-umbrella-beach text-info"></i> Gestión de Vacaciones
            </h1>
            <p class="text-muted mb-0">Administrar solicitudes y períodos vacacionales</p>
        </div>
        <div>
            <a href="<?php echo e(route('personnel.vacations.create')); ?>" class="btn btn-primary">
                <i class="fas fa-plus"></i> Nueva Solicitud
            </a>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <div class="row">
        <div class="col-12">
            <!-- Filtros -->
            <div class="card collapsed-card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-filter"></i> Filtros de Búsqueda
                    </h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body" style="display: none;">
                    <form method="GET" action="<?php echo e(route('personnel.vacations.index')); ?>" id="filterForm">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="employee_id">Empleado</label>
                                    <select class="form-control" id="employee_id" name="employee_id">
                                        <option value="">Todos los empleados</option>
                                        <?php $__currentLoopData = $employees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $employee): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($employee->id); ?>" 
                                                    <?php echo e(request('employee_id') == $employee->id ? 'selected' : ''); ?>>
                                                <?php echo e($employee->names); ?> <?php echo e($employee->lastnames); ?>

                                            </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="status">Estado</label>
                                    <select class="form-control" id="status" name="status">
                                        <option value="">Todos los estados</option>
                                        <option value="pending" <?php echo e(request('status') == 'pending' ? 'selected' : ''); ?>>Pendiente</option>
                                        <option value="approved" <?php echo e(request('status') == 'approved' ? 'selected' : ''); ?>>Aprobado</option>
                                        <option value="rejected" <?php echo e(request('status') == 'rejected' ? 'selected' : ''); ?>>Rechazado</option>
                                        <option value="taken" <?php echo e(request('status') == 'taken' ? 'selected' : ''); ?>>Tomado</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="year">Año</label>
                                    <select class="form-control" id="year" name="year">
                                        <option value="">Todos los años</option>
                                        <?php $__currentLoopData = $years; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $year): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($year); ?>" 
                                                    <?php echo e(request('year') == $year ? 'selected' : ''); ?>>
                                                <?php echo e($year); ?>

                                            </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="search">Búsqueda</label>
                                    <input type="text" 
                                           class="form-control" 
                                           id="search" 
                                           name="search" 
                                           value="<?php echo e(request('search')); ?>"
                                           placeholder="Empleado, observaciones...">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search"></i> Filtrar
                                </button>
                                <a href="<?php echo e(route('personnel.vacations.index')); ?>" class="btn btn-secondary">
                                    <i class="fas fa-undo"></i> Limpiar
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Lista de Vacaciones -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-list"></i> Lista de Vacaciones
                        <small class="text-muted">(<?php echo e($vacations->total()); ?> registros)</small>
                    </h3>
                </div>
                <div class="card-body table-responsive p-0">
                    <?php if($vacations->count() > 0): ?>
                        <table class="table table-hover table-striped">
                            <thead>
                                <tr>
                                    <th style="width: 80px;">Foto</th>
                                    <th>Empleado</th>
                                    <th>Fecha Solicitud</th>
                                    <th>Período Vacacional</th>
                                    <th>Días</th>
                                    <th>Estado</th>
                                    <th>Observaciones</th>
                                    <th style="width: 120px;">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__currentLoopData = $vacations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $vacation): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td class="text-center">
                                            <?php if($vacation->employee->photo): ?>
                                                <img src="<?php echo e(asset('storage/' . $vacation->employee->photo)); ?>" 
                                                     alt="Foto de <?php echo e($vacation->employee->names); ?>"
                                                     class="img-circle"
                                                     style="width: 40px; height: 40px; object-fit: cover;">
                                            <?php else: ?>
                                                <div class="bg-secondary rounded-circle d-flex align-items-center justify-content-center mx-auto"
                                                     style="width: 40px; height: 40px;">
                                                    <i class="fas fa-user text-white"></i>
                                                </div>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <strong><?php echo e($vacation->employee->names); ?> <?php echo e($vacation->employee->lastnames); ?></strong>
                                            <br>
                                            <small class="text-muted">DNI: <?php echo e($vacation->employee->dni); ?></small>
                                            <?php if($vacation->employee->employeeType): ?>
                                                <br>
                                                <span class="badge" style="background-color: <?php echo e($vacation->employee->employeeType->color); ?>; color: white;">
                                                    <i class="<?php echo e($vacation->employee->employeeType->icon); ?>"></i>
                                                    <?php echo e($vacation->employee->employeeType->name); ?>

                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php echo e(\Carbon\Carbon::parse($vacation->request_date)->format('d/m/Y')); ?>

                                        </td>
                                        <td>
                                            <strong>Inicio:</strong> <?php echo e(\Carbon\Carbon::parse($vacation->start_date)->format('d/m/Y')); ?>

                                            <br>
                                            <strong>Fin:</strong> <?php echo e(\Carbon\Carbon::parse($vacation->end_date)->format('d/m/Y')); ?>

                                        </td>
                                        <td>
                                            <span class="badge badge-info"><?php echo e($vacation->days); ?> días</span>
                                        </td>
                                        <td>
                                            <?php
                                                $statusClasses = [
                                                    'pending' => 'badge-warning',
                                                    'approved' => 'badge-success', 
                                                    'rejected' => 'badge-danger',
                                                    'taken' => 'badge-info'
                                                ];
                                                $statusLabels = [
                                                    'pending' => 'Pendiente',
                                                    'approved' => 'Aprobado',
                                                    'rejected' => 'Rechazado', 
                                                    'taken' => 'Tomado'
                                                ];
                                            ?>
                                            <span class="badge <?php echo e($statusClasses[$vacation->status] ?? 'badge-secondary'); ?>">
                                                <?php echo e($statusLabels[$vacation->status] ?? $vacation->status); ?>

                                            </span>
                                        </td>
                                        <td>
                                            <?php if($vacation->observations): ?>
                                                <span class="text-truncate d-inline-block" style="max-width: 150px;" 
                                                      title="<?php echo e($vacation->observations); ?>">
                                                    <?php echo e($vacation->observations); ?>

                                                </span>
                                            <?php else: ?>
                                                <span class="text-muted">Sin observaciones</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="<?php echo e(route('personnel.vacations.show', $vacation)); ?>" 
                                                   class="btn btn-info btn-sm"
                                                   title="Ver detalles">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <?php if($vacation->status == 'pending'): ?>
                                                    <a href="<?php echo e(route('personnel.vacations.edit', $vacation)); ?>" 
                                                       class="btn btn-warning btn-sm"
                                                       title="Editar">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                <?php endif; ?>
                                                <?php if(in_array($vacation->status, ['pending', 'approved'])): ?>
                                                    <button type="button" 
                                                            class="btn btn-danger btn-sm"
                                                            title="Eliminar"
                                                            onclick="confirmDelete('<?php echo e($vacation->id); ?>')">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <div class="text-center py-4">
                            <div class="mb-3">
                                <i class="fas fa-umbrella-beach text-muted" style="font-size: 3rem;"></i>
                            </div>
                            <h5 class="text-muted">No hay vacaciones registradas</h5>
                            <p class="text-muted mb-3">Aún no se han registrado solicitudes de vacaciones en el sistema.</p>
                            <a href="<?php echo e(route('personnel.vacations.create')); ?>" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Crear Primera Solicitud
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
                <?php if($vacations->hasPages()): ?>
                    <div class="card-footer">
                        <?php echo e($vacations->links()); ?>

                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Formularios para eliminar -->
    <?php $__currentLoopData = $vacations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $vacation): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <form id="delete-form-<?php echo e($vacation->id); ?>" 
              action="<?php echo e(route('personnel.vacations.destroy', $vacation)); ?>" 
              method="POST" 
              style="display: none;">
            <?php echo csrf_field(); ?>
            <?php echo method_field('DELETE'); ?>
        </form>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('css'); ?>
    <style>
        .table td {
            vertical-align: middle;
        }
        .badge {
            font-size: 0.75em;
        }
        .btn-group .btn {
            padding: 0.25rem 0.4rem;
        }
        .text-truncate {
            max-width: 150px;
        }
    </style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('js'); ?>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function confirmDelete(vacationId) {
            Swal.fire({
                title: '¿Estás seguro?',
                text: 'Esta acción no se puede deshacer. La solicitud de vacaciones será eliminada permanentemente.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('delete-form-' + vacationId).submit();
                }
            });
        }

        // Auto-submit form when filters change
        $('#employee_id, #status, #year').on('change', function() {
            $('#filterForm').submit();
        });
    </script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('adminlte::page', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\crist\OneDrive\Escritorio\TADS\PryFinal\PryRsuReciclaje\resources\views/vacations/index.blade.php ENDPATH**/ ?>