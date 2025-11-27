<?php $__env->startSection('title', 'Contratos'); ?>

<?php $__env->startSection('content_header'); ?>
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="h3 mb-1 font-weight-bold">
                <i class="fas fa-file-contract text-primary"></i> Gestión de Contratos
            </h1>
            <p class="text-muted mb-0">Administración de contratos laborales del personal</p>
        </div>
        <div class="btn-group">
            <a href="<?php echo e(route('personnel.contracts.create')); ?>" class="btn btn-primary">
                <i class="fas fa-plus"></i> Nuevo Contrato
            </a>
            <button type="button" class="btn btn-info" onclick="exportContracts()">
                <i class="fas fa-file-excel"></i> Exportar
            </button>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <!-- Filtros de Búsqueda -->
    <div class="card shadow-sm">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-filter"></i> Filtros de Búsqueda
            </h3>
            <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                    <i class="fas fa-minus"></i>
                </button>
            </div>
        </div>
        <div class="card-body">
            <form method="GET" action="<?php echo e(route('personnel.contracts.index')); ?>" id="filterForm">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="search">Búsqueda General</label>
                            <input type="text" 
                                   name="search" 
                                   id="search"
                                   class="form-control" 
                                   placeholder="Empleado, DNI..."
                                   value="<?php echo e(request('search')); ?>">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="employee_id">Empleado</label>
                            <select name="employee_id" id="employee_id" class="form-control">
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
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="contract_type">Tipo de Contrato</label>
                            <select name="contract_type" id="contract_type" class="form-control">
                                <option value="">Todos los tipos</option>
                                <?php $__currentLoopData = $contractTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($key); ?>" 
                                            <?php echo e(request('contract_type') == $key ? 'selected' : ''); ?>>
                                        <?php echo e($type); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="status">Estado</label>
                            <select name="status" id="status" class="form-control">
                                <option value="">Todos los estados</option>
                                <option value="1" <?php echo e(request('status') == '1' ? 'selected' : ''); ?>>Activo</option>
                                <option value="0" <?php echo e(request('status') == '0' ? 'selected' : ''); ?>>Inactivo</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="start_date_from">Fecha Inicio Desde</label>
                            <input type="date" 
                                   name="start_date_from" 
                                   id="start_date_from"
                                   class="form-control" 
                                   value="<?php echo e(request('start_date_from')); ?>">
                        </div>
                    </div>
                    <div class="col-md-1">
                        <div class="form-group">
                            <label>&nbsp;</label>
                            <div class="d-flex">
                                <button type="submit" class="btn btn-primary btn-sm mr-1">
                                    <i class="fas fa-search"></i>
                                </button>
                                <a href="<?php echo e(route('personnel.contracts.index')); ?>" class="btn btn-secondary btn-sm">
                                    <i class="fas fa-times"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabla de Contratos -->
    <div class="card shadow-sm">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-list"></i> Lista de Contratos
            </h3>
            <div class="card-tools">
                <span class="text-muted">
                    Mostrando <?php echo e($contracts->firstItem() ?? 0); ?> - <?php echo e($contracts->lastItem() ?? 0); ?> de <?php echo e($contracts->total()); ?>

                </span>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="thead-light">
                        <tr>
                            <th>Empleado</th>
                            <th>Tipo de Contrato</th>
                            <th>Cargo</th>
                            <th>Fecha Inicio</th>
                            <th>Fecha Fin</th>
                            <th>Salario</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $contracts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $contract): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <?php if($contract->employee->photo): ?>
                                            <img src="<?php echo e(asset('storage/' . $contract->employee->photo)); ?>" 
                                                 class="rounded-circle mr-2" 
                                                 width="30" height="30"
                                                 style="object-fit: cover;">
                                        <?php else: ?>
                                            <div class="bg-secondary rounded-circle d-flex align-items-center justify-content-center mr-2" 
                                                 style="width: 30px; height: 30px;">
                                                <i class="fas fa-user text-white" style="font-size: 12px;"></i>
                                            </div>
                                        <?php endif; ?>
                                        <div>
                                            <strong><?php echo e($contract->employee->names); ?> <?php echo e($contract->employee->lastnames); ?></strong>
                                            <br>
                                            <small class="text-muted">DNI: <?php echo e($contract->employee->dni); ?></small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge badge-info"><?php echo e($contract->contract_type_name); ?></span>
                                </td>
                                <td><?php echo e($contract->position->name ?? 'No especificado'); ?></td>
                                <td><?php echo e($contract->start_date ? $contract->start_date->format('d/m/Y') : 'N/A'); ?></td>
                                <td>
                                    <?php if($contract->end_date): ?>
                                        <?php echo e($contract->end_date ? $contract->end_date->format('d/m/Y') : 'Indefinido'); ?>

                                        <?php if($contract->end_date->isPast()): ?>
                                            <span class="badge badge-warning">Vencido</span>
                                        <?php elseif($contract->end_date->diffInDays(now()) <= 30): ?>
                                            <span class="badge badge-warning">Por vencer</span>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <span class="text-muted">Indefinido</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <strong>S/. <?php echo e(number_format($contract->salary, 2)); ?></strong>
                                </td>
                                <td>
                                    <?php if($contract->is_active): ?>
                                        <span class="badge badge-success">Activo</span>
                                    <?php else: ?>
                                        <span class="badge badge-danger">Inactivo</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="<?php echo e(route('personnel.contracts.show', $contract)); ?>" 
                                           class="btn btn-info btn-sm">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="<?php echo e(route('personnel.contracts.edit', $contract)); ?>" 
                                           class="btn btn-warning btn-sm">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <?php if($contract->is_active): ?>
                                            <button class="btn btn-secondary btn-sm" 
                                                    onclick="deactivateContract(<?php echo e($contract->id); ?>)">
                                                <i class="fas fa-pause"></i>
                                            </button>
                                        <?php else: ?>
                                            <button class="btn btn-success btn-sm" 
                                                    onclick="activateContract(<?php echo e($contract->id); ?>)">
                                                <i class="fas fa-play"></i>
                                            </button>
                                        <?php endif; ?>
                                        <button class="btn btn-danger btn-sm" 
                                                onclick="deleteContract(<?php echo e($contract->id); ?>, '<?php echo e($contract->employee->names); ?> <?php echo e($contract->employee->lastnames); ?>')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="8" class="text-center py-4">
                                    <div class="text-muted">
                                        <i class="fas fa-file-contract fa-3x mb-3"></i>
                                        <h4>No hay contratos registrados</h4>
                                        <p>Comienza creando tu primer contrato laboral</p>
                                        <a href="<?php echo e(route('personnel.contracts.create')); ?>" class="btn btn-primary">
                                            <i class="fas fa-plus"></i> Crear Primer Contrato
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php if($contracts->hasPages()): ?>
            <div class="card-footer">
                <?php echo e($contracts->links()); ?>

            </div>
        <?php endif; ?>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('css'); ?>
    <style>
        .table th {
            border-top: none;
            font-weight: 600;
            color: #495057;
        }
        .badge {
            font-size: 0.75em;
        }
        .btn-group .btn {
            padding: 0.25rem 0.5rem;
        }
    </style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('js'); ?>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function activateContract(contractId) {
            Swal.fire({
                title: '¿Activar contrato?',
                text: 'Esta acción desactivará otros contratos activos del empleado.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#dc3545',
                confirmButtonText: 'Sí, activar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`/personnel/contracts/${contractId}/activate`, {
                        method: 'PATCH',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Content-Type': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire('¡Activado!', data.message, 'success')
                                .then(() => window.location.reload());
                        } else {
                            Swal.fire('Error', data.message, 'error');
                        }
                    })
                    .catch(() => {
                        Swal.fire('Error', 'No se pudo activar el contrato', 'error');
                    });
                }
            });
        }

        function deactivateContract(contractId) {
            Swal.fire({
                title: '¿Desactivar contrato?',
                text: 'El contrato se marcará como inactivo.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ffc107',
                cancelButtonColor: '#dc3545',
                confirmButtonText: 'Sí, desactivar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`/personnel/contracts/${contractId}/deactivate`, {
                        method: 'PATCH',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Content-Type': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire('¡Desactivado!', data.message, 'success')
                                .then(() => window.location.reload());
                        } else {
                            Swal.fire('Error', data.message, 'error');
                        }
                    })
                    .catch(() => {
                        Swal.fire('Error', 'No se pudo desactivar el contrato', 'error');
                    });
                }
            });
        }

        function deleteContract(contractId, employeeName) {
            Swal.fire({
                title: '¿Eliminar contrato?',
                html: `¿Estás seguro de que deseas eliminar el contrato de <strong>${employeeName}</strong>?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`/personnel/contracts/${contractId}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Content-Type': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire('¡Eliminado!', data.message, 'success')
                                .then(() => window.location.reload());
                        } else {
                            Swal.fire('Error', data.message, 'error');
                        }
                    })
                    .catch(() => {
                        Swal.fire('Error', 'No se pudo eliminar el contrato', 'error');
                    });
                }
            });
        }

        function exportContracts() {
            window.location.href = '/personnel/contracts/export?' + new URLSearchParams(new FormData(document.getElementById('filterForm')));
        }
    </script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('adminlte::page', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\crist\OneDrive\Escritorio\TADS\PryFinal\PryRsuReciclaje\resources\views/contracts/index.blade.php ENDPATH**/ ?>