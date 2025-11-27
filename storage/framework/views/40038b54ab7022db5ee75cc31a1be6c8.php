<?php $__env->startSection('title', 'Empleados'); ?>

<?php $__env->startSection('content_header'); ?>
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="h3 mb-1 font-weight-bold">
                <i class="fas fa-users text-primary"></i> Gestión de Empleados
            </h1>
            <p class="text-muted mb-0">Administración integral del personal de la empresa</p>
        </div>
        <div class="btn-group">
            <a href="<?php echo e(route('admin.personnel.employees.create')); ?>" class="btn btn-primary">
                <i class="fas fa-user-plus"></i> Nuevo Empleado
            </a>
            <a href="<?php echo e(route('admin.personnel.employees.export')); ?>" class="btn btn-success">
                <i class="fas fa-file-excel"></i> Exportar
            </a>
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
            <form method="GET" action="<?php echo e(route('admin.personnel.employees.index')); ?>" id="filterForm">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="search">Búsqueda General</label>
                            <input type="text" 
                                   name="search" 
                                   id="search"
                                   class="form-control" 
                                   placeholder="Nombre, DNI, email..."
                                   value="<?php echo e(request('search')); ?>">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="employee_type_id">Tipo de Empleado</label>
                            <select name="employee_type_id" id="employee_type_id" class="form-control">
                                <option value="">Todos los tipos</option>
                                <?php $__currentLoopData = $employeeTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($type->id); ?>" 
                                            <?php echo e(request('employee_type_id') == $type->id ? 'selected' : ''); ?>>
                                        <?php echo e($type->name); ?>

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
                                <option value="active" <?php echo e(request('status') == 'active' ? 'selected' : ''); ?>>Activo</option>
                                <option value="inactive" <?php echo e(request('status') == 'inactive' ? 'selected' : ''); ?>>Inactivo</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-1">
                        <div class="form-group">
                            <label for="age_from">Edad Desde</label>
                            <input type="number" 
                                   name="age_from" 
                                   id="age_from"
                                   class="form-control" 
                                   placeholder="18"
                                   min="18" max="65"
                                   value="<?php echo e(request('age_from')); ?>">
                        </div>
                    </div>
                    <div class="col-md-1">
                        <div class="form-group">
                            <label for="age_to">Edad Hasta</label>
                            <input type="number" 
                                   name="age_to" 
                                   id="age_to"
                                   class="form-control" 
                                   placeholder="65"
                                   min="18" max="65"
                                   value="<?php echo e(request('age_to')); ?>">
                        </div>
                    </div>
                    <div class="col-md-1">
                        <div class="form-group">
                            <label>&nbsp;</label>
                            <div>
                                <button type="submit" class="btn btn-primary btn-block">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <small class="text-muted">
                            <strong>Ordenar por:</strong>
                            <a href="<?php echo e(request()->fullUrlWithQuery(['sort' => 'name', 'direction' => 'asc'])); ?>" 
                               class="text-decoration-none <?php echo e(request('sort') == 'name' && request('direction') == 'asc' ? 'text-primary font-weight-bold' : ''); ?>">
                                Nombre ↑
                            </a> |
                            <a href="<?php echo e(request()->fullUrlWithQuery(['sort' => 'name', 'direction' => 'desc'])); ?>" 
                               class="text-decoration-none <?php echo e(request('sort') == 'name' && request('direction') == 'desc' ? 'text-primary font-weight-bold' : ''); ?>">
                                Nombre ↓
                            </a> |
                            <a href="<?php echo e(request()->fullUrlWithQuery(['sort' => 'created_at', 'direction' => 'desc'])); ?>" 
                               class="text-decoration-none <?php echo e(request('sort') == 'created_at' && request('direction') == 'desc' ? 'text-primary font-weight-bold' : ''); ?>">
                                Más Recientes
                            </a> |
                            <a href="<?php echo e(request()->fullUrlWithQuery(['sort' => 'status', 'direction' => 'asc'])); ?>" 
                               class="text-decoration-none <?php echo e(request('sort') == 'status' && request('direction') == 'asc' ? 'text-primary font-weight-bold' : ''); ?>">
                                Estado
                            </a>
                        </small>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Listado de Empleados -->
    <div class="card shadow-sm">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-list"></i> Empleados 
                <span class="badge badge-primary"><?php echo e($employees->total()); ?> total</span>
            </h3>
            <div class="card-tools">
                <span class="text-muted">
                    Mostrando <?php echo e($employees->firstItem() ?? 0); ?> - <?php echo e($employees->lastItem() ?? 0); ?> de <?php echo e($employees->total()); ?>

                </span>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="thead-light">
                        <tr>
                            <th width="80">Foto</th>
                            <th>Empleado</th>
                            <th>DNI</th>
                            <th>Contacto</th>
                            <th>Tipo</th>
                            <th>Departamento</th>
                            <th>Edad</th>
                            <th>Estado</th>
                            <th>Contrato</th>
                            <th width="150">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $employees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $employee): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td class="text-center">
                                    <?php if($employee->photo): ?>
                                        <img src="<?php echo e(asset('storage/' . $employee->photo)); ?>" 
                                             class="img-thumbnail rounded-circle employee-photo" 
                                             width="50" height="50"
                                             alt="<?php echo e($employee->names); ?> <?php echo e($employee->lastnames); ?>"
                                             data-toggle="modal" 
                                             data-target="#photoModal"
                                             data-photo="<?php echo e(asset('storage/' . $employee->photo)); ?>"
                                             data-name="<?php echo e($employee->names); ?> <?php echo e($employee->lastnames); ?>"
                                             style="cursor: pointer; object-fit: cover;">
                                    <?php else: ?>
                                        <div class="bg-secondary rounded-circle d-flex align-items-center justify-content-center employee-avatar" 
                                             style="cursor: default;">
                                            <i class="fas fa-user text-white"></i>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <strong><?php echo e($employee->names); ?> <?php echo e($employee->lastnames); ?></strong>
                                    <br>
                                    <small class="text-muted">
                                        Registrado: <?php echo e($employee->created_at->format('d/m/Y')); ?>

                                    </small>
                                </td>
                                <td>
                                    <code class="bg-light"><?php echo e($employee->dni); ?></code>
                                </td>
                                <td>
                                    <?php if($employee->email): ?>
                                        <i class="fas fa-envelope text-primary"></i> 
                                        <a href="mailto:<?php echo e($employee->email); ?>"><?php echo e($employee->email); ?></a>
                                        <br>
                                    <?php endif; ?>
                                    <?php if($employee->phone): ?>
                                        <i class="fas fa-phone text-success"></i> 
                                        <a href="tel:<?php echo e($employee->phone); ?>"><?php echo e($employee->phone); ?></a>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge badge-info">
                                        <?php echo e($employee->employeeType->name ?? 'Sin tipo'); ?>

                                    </span>
                                </td>
                                <td>
                                    <?php echo e($employee->department->name ?? 'Sin departamento'); ?>

                                </td>
                                <td>
                                    <span class="badge badge-light"><?php echo e($employee->age); ?> años</span>
                                </td>
                                <td>
                                    <span class="badge badge-<?php echo e($employee->status === 'active' ? 'success' : 'secondary'); ?>">
                                        <?php echo e($employee->status === 'active' ? 'Activo' : 'Inactivo'); ?>

                                    </span>
                                </td>
                                <td>
                                    <?php if($employee->activeContract): ?>
                                        <span class="badge badge-success" title="Contrato Activo">
                                            <i class="fas fa-check-circle"></i> Activo
                                        </span>
                                        <br>
                                        <small class="text-muted">
                                            S/ <?php echo e(number_format($employee->activeContract->salary, 2)); ?>

                                        </small>
                                    <?php else: ?>
                                        <span class="badge badge-warning" title="Sin Contrato Activo">
                                            <i class="fas fa-exclamation-triangle"></i> Sin contrato
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a href="<?php echo e(route('admin.personnel.employees.show', $employee)); ?>" 
                                           class="btn btn-outline-info btn-sm" title="Ver detalles">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="<?php echo e(route('admin.personnel.employees.edit', $employee)); ?>" 
                                           class="btn btn-outline-primary btn-sm" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" 
                                                class="btn btn-outline-<?php echo e($employee->status === 'active' ? 'warning' : 'success'); ?> btn-sm"
                                                onclick="toggleEmployeeStatus(<?php echo e($employee->id); ?>)"
                                                title="<?php echo e($employee->status === 'active' ? 'Desactivar' : 'Activar'); ?>">
                                            <i class="fas fa-<?php echo e($employee->status === 'active' ? 'user-times' : 'user-check'); ?>"></i>
                                        </button>
                                        <div class="btn-group" role="group">
                                            <button class="btn btn-outline-secondary btn-sm dropdown-toggle" 
                                                    type="button" data-toggle="dropdown">
                                                <i class="fas fa-ellipsis-v"></i>
                                            </button>
                                            <div class="dropdown-menu">
                                                <a class="dropdown-item" href="<?php echo e(route('admin.personnel.contracts.create', ['employee_id' => $employee->id])); ?>">
                                                    <i class="fas fa-file-contract"></i> Nuevo Contrato
                                                </a>
                                                <a class="dropdown-item" href="<?php echo e(route('admin.personnel.vacations.create', ['employee_id' => $employee->id])); ?>">
                                                    <i class="fas fa-calendar-plus"></i> Solicitar Vacaciones
                                                </a>
                                                <div class="dropdown-divider"></div>
                                                <?php if($employee->photo): ?>
                                                    <button class="dropdown-item text-warning" 
                                                            onclick="removeEmployeePhoto(<?php echo e($employee->id); ?>)">
                                                        <i class="fas fa-image"></i> Eliminar Foto
                                                    </button>
                                                <?php endif; ?>
                                                <button class="dropdown-item text-danger" 
                                                        onclick="deleteEmployee(<?php echo e($employee->id); ?>, '<?php echo e($employee->names); ?> <?php echo e($employee->lastnames); ?>')">
                                                    <i class="fas fa-trash"></i> Eliminar
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="10" class="text-center py-5">
                                    <div class="text-muted">
                                        <i class="fas fa-users fa-3x mb-3"></i>
                                        <h5>No se encontraron empleados</h5>
                                        <p>No hay empleados que coincidan con los filtros seleccionados.</p>
                                        <a href="<?php echo e(route('admin.personnel.employees.create')); ?>" class="btn btn-primary">
                                            <i class="fas fa-user-plus"></i> Registrar Primer Empleado
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <small class="text-muted">
                        Mostrando <?php echo e($employees->firstItem() ?? 0); ?> - <?php echo e($employees->lastItem() ?? 0); ?> de <?php echo e($employees->total()); ?> resultados
                    </small>
                </div>
                <div>
                    <?php echo e($employees->appends(request()->query())->links()); ?>

                </div>
            </div>
        </div>
    </div>

    <!-- Modal para Ver Foto -->
    <div class="modal fade" id="photoModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="photoModalTitle">Foto del Empleado</h4>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <img id="photoModalImage" src="" class="img-fluid rounded" alt="Foto del empleado">
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('css'); ?>
    <style>
        .employee-photo {
            transition: transform 0.2s;
            border: 2px solid #dee2e6;
            width: 50px !important;
            height: 50px !important;
            object-fit: cover;
        }
        .employee-photo:hover {
            transform: scale(1.1);
            border-color: #007bff;
        }
        .employee-avatar {
            width: 50px;
            height: 50px;
            border: 2px solid #dee2e6;
            transition: border-color 0.2s;
        }
        .employee-avatar:hover {
            border-color: #6c757d;
        }
        .table th {
            border-top: none;
            font-weight: 600;
            color: #495057;
        }
        .badge {
            font-size: 0.75em;
        }
        .btn-group-sm > .btn {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
        }
        .dropdown-menu {
            font-size: 0.875rem;
        }
        .card-tools .text-muted {
            font-size: 0.875rem;
        }
    </style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('js'); ?>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            // Modal de foto
            $('.employee-photo').on('click', function() {
                const photo = $(this).data('photo');
                const name = $(this).data('name');
                
                $('#photoModalImage').attr('src', photo);
                $('#photoModalTitle').text('Foto de ' + name);
            });

            // Auto-submit del formulario de filtros con debounce
            let timeout;
            $('#search, #age_from, #age_to').on('input', function() {
                clearTimeout(timeout);
                timeout = setTimeout(function() {
                    $('#filterForm').submit();
                }, 1000);
            });

            $('#employee_type_id, #department_id, #status').on('change', function() {
                $('#filterForm').submit();
            });
        });

        function toggleEmployeeStatus(employeeId) {
            Swal.fire({
                title: '¿Cambiar estado del empleado?',
                text: 'Esta acción cambiará el estado del empleado',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sí, cambiar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `/personnel/employees/${employeeId}/toggle-status`,
                        type: 'PATCH',
                        data: {
                            _token: '<?php echo e(csrf_token()); ?>'
                        },
                        success: function(response) {
                            if (response.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: '¡Éxito!',
                                    text: response.message,
                                    timer: 3000
                                }).then(() => {
                                    location.reload();
                                });
                            }
                        },
                        error: function(xhr) {
                            const error = xhr.responseJSON;
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: error.message || 'Error al cambiar el estado del empleado'
                            });
                        }
                    });
                }
            });
        }

        function removeEmployeePhoto(employeeId) {
            Swal.fire({
                title: '¿Eliminar foto del empleado?',
                text: 'Esta acción no se puede deshacer',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `/personnel/employees/${employeeId}/remove-photo`,
                        type: 'DELETE',
                        data: {
                            _token: '<?php echo e(csrf_token()); ?>'
                        },
                        success: function(response) {
                            if (response.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: '¡Éxito!',
                                    text: response.message,
                                    timer: 3000
                                }).then(() => {
                                    location.reload();
                                });
                            }
                        },
                        error: function(xhr) {
                            const error = xhr.responseJSON;
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: error.message || 'Error al eliminar la foto'
                            });
                        }
                    });
                }
            });
        }

        function deleteEmployee(employeeId, employeeName) {
            Swal.fire({
                title: '¿Eliminar empleado?',
                html: `¿Estás seguro de que deseas eliminar a <strong>${employeeName}</strong>?<br><small class="text-muted">Esta acción no se puede deshacer</small>`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = `/personnel/employees/${employeeId}`;
                    form.innerHTML = `
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('DELETE'); ?>
                    `;
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        }
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('adminlte::page', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\crist\OneDrive\Escritorio\TADS\PryFinal\PryRsuReciclaje\resources\views/personnel/employees/index.blade.php ENDPATH**/ ?>