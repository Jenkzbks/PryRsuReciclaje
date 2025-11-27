<?php $__env->startSection('title', 'Tipos de Empleado'); ?>

<?php $__env->startSection('content_header'); ?>
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="h3 mb-1 font-weight-bold">
                <i class="fas fa-tags text-info"></i> Tipos de Empleado
            </h1>
            <p class="text-muted mb-0">Gestión de categorías y roles del personal</p>
        </div>
        <a href="<?php echo e(route('personnel.employee-types.create')); ?>" class="btn btn-primary">
            <i class="fas fa-plus"></i> Nuevo Tipo
        </a>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <div class="card shadow-sm">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-list"></i> Tipos de Empleado 
                <span class="badge badge-primary"><?php echo e($employeeTypes->total()); ?></span>
            </h3>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="thead-light">
                        <tr>
                            <th>Nombre</th>
                            <th>Descripción</th>
                            <th>Empleados</th>
                            <th>Estado</th>
                            <th>Creado</th>
                            <th width="200">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $employeeTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td>
                                    <strong><?php echo e($type->name); ?></strong>
                                    <?php if($type->protected): ?>
                                        <i class="fas fa-shield-alt text-warning ml-1" title="Tipo protegido"></i>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo e($type->description ?? 'Sin descripción'); ?></td>
                                <td>
                                    <span class="badge badge-info"><?php echo e($type->employees_count); ?> empleados</span>
                                </td>
                                <td>
                                    <?php if($type->protected): ?>
                                        <span class="badge badge-warning">Protegido</span>
                                    <?php else: ?>
                                        <span class="badge badge-success">Normal</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo e($type->created_at->format('d/m/Y')); ?></td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="<?php echo e(route('personnel.employee-types.show', $type)); ?>" 
                                           class="btn btn-outline-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="<?php echo e(route('personnel.employee-types.edit', $type)); ?>" 
                                           class="btn btn-outline-primary">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button class="btn btn-outline-secondary" 
                                                onclick="duplicateType(<?php echo e($type->id); ?>)">
                                            <i class="fas fa-copy"></i>
                                        </button>
                                        <?php if(!$type->protected): ?>
                                            <button class="btn btn-outline-danger" 
                                                    onclick="deleteType(<?php echo e($type->id); ?>, '<?php echo e($type->name); ?>')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="6" class="text-center py-4">
                                    <div class="text-muted">
                                        <i class="fas fa-tags fa-2x mb-3"></i>
                                        <h5>No hay tipos de empleado</h5>
                                        <a href="<?php echo e(route('personnel.employee-types.create')); ?>" class="btn btn-primary">
                                            <i class="fas fa-plus"></i> Crear Primer Tipo
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
            <?php echo e($employeeTypes->links()); ?>

        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('js'); ?>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function duplicateType(typeId) {
            $.post(`/personnel/employee-types/${typeId}/duplicate`, {
                _token: '<?php echo e(csrf_token()); ?>'
            }).done(function() {
                location.reload();
            });
        }

        function deleteType(typeId, typeName) {
            Swal.fire({
                title: '¿Eliminar tipo?',
                text: `¿Eliminar "${typeName}"?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                confirmButtonText: 'Sí, eliminar'
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = `/personnel/employee-types/${typeId}`;
                    form.innerHTML = '<?php echo csrf_field(); ?> <?php echo method_field("DELETE"); ?>';
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        }
    </script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('adminlte::page', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH F:\USAT 2025-2\topicos\PryFINAL\PryRsuReciclaje\resources\views/personnel/employee-types/index.blade.php ENDPATH**/ ?>