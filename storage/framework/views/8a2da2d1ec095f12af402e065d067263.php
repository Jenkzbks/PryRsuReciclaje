<?php $__env->startSection('title', 'Detalles de Vacaciones'); ?>

<?php $__env->startSection('content_header'); ?>
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1>Detalles de Vacaciones</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="<?php echo e(route('admin.personnel.vacations.index')); ?>">Vacaciones</a></li>
                <li class="breadcrumb-item active">Detalles</li>
            </ol>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-calendar-alt"></i> 
                        Vacaciones - <?php echo e($vacation->employee->names); ?> <?php echo e($vacation->employee->lastnames); ?>

                    </h3>
                    <div class="card-tools">
                        <span class="badge badge-<?php echo e($vacation->status === 'approved' ? 'success' : ($vacation->status === 'rejected' ? 'danger' : 'warning')); ?> badge-lg">
                            <?php echo e(ucfirst($vacation->status)); ?>

                        </span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Información del Empleado -->
                        <div class="col-md-4">
                            <div class="card card-outline card-primary">
                                <div class="card-header">
                                    <h5 class="card-title">
                                        <i class="fas fa-user"></i> Información del Empleado
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="text-center mb-3">
                                        <?php if($vacation->employee->photo): ?>
                                            <img src="<?php echo e(asset('storage/' . $vacation->employee->photo)); ?>" 
                                                 alt="Foto de <?php echo e($vacation->employee->names); ?>" 
                                                 class="img-circle elevation-2" 
                                                 style="width: 80px; height: 80px; object-fit: cover;">
                                        <?php else: ?>
                                            <div class="img-circle elevation-2 bg-gray d-flex align-items-center justify-content-center" 
                                                 style="width: 80px; height: 80px;">
                                                <i class="fas fa-user fa-2x text-white"></i>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <table class="table table-borderless table-sm">
                                        <tr>
                                            <td><strong>Nombre:</strong></td>
                                            <td><?php echo e($vacation->employee->names); ?> <?php echo e($vacation->employee->lastnames); ?></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Código:</strong></td>
                                            <td><?php echo e($vacation->employee->employee_code); ?></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Departamento:</strong></td>
                                            <td><?php echo e($vacation->employee->department->name ?? 'No asignado'); ?></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Email:</strong></td>
                                            <td><?php echo e($vacation->employee->email); ?></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Detalles de las Vacaciones -->
                        <div class="col-md-8">
                            <div class="card card-outline card-info">
                                <div class="card-header">
                                    <h5 class="card-title">
                                        <i class="fas fa-info-circle"></i> Detalles de las Vacaciones
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <table class="table table-borderless">
                                                <tr>
                                                    <td><strong>Tipo de Vacaciones:</strong></td>
                                                    <td>
                                                        <?php
                                                            $types = [
                                                                'annual' => 'Vacaciones Anuales',
                                                                'personal' => 'Días Personales',
                                                                'sick' => 'Licencia por Enfermedad',
                                                                'maternity' => 'Licencia de Maternidad',
                                                                'paternity' => 'Licencia de Paternidad',
                                                                'emergency' => 'Emergencia Familiar'
                                                            ];
                                                        ?>
                                                        <span class="badge badge-info">
                                                            <?php echo e($types[$vacation->vacation_type] ?? $vacation->vacation_type); ?>

                                                        </span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Fecha de Inicio:</strong></td>
                                                    <td><?php echo e(\Carbon\Carbon::parse($vacation->start_date)->format('d/m/Y')); ?></td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Fecha de Fin:</strong></td>
                                                    <td><?php echo e(\Carbon\Carbon::parse($vacation->end_date)->format('d/m/Y')); ?></td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Días Solicitados:</strong></td>
                                                    <td><span class="badge badge-primary"><?php echo e($vacation->days_taken); ?> días</span></td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Estado:</strong></td>
                                                    <td>
                                                        <span class="badge badge-<?php echo e($vacation->status === 'approved' ? 'success' : ($vacation->status === 'rejected' ? 'danger' : 'warning')); ?>">
                                                            <?php if($vacation->status === 'pending'): ?>
                                                                <i class="fas fa-clock"></i> Pendiente
                                                            <?php elseif($vacation->status === 'approved'): ?>
                                                                <i class="fas fa-check"></i> Aprobado
                                                            <?php elseif($vacation->status === 'rejected'): ?>
                                                                <i class="fas fa-times"></i> Rechazado
                                                            <?php endif; ?>
                                                        </span>
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                        <div class="col-md-6">
                                            <table class="table table-borderless">
                                                <?php if($vacation->replacement_employee_id): ?>
                                                <tr>
                                                    <td><strong>Empleado de Reemplazo:</strong></td>
                                                    <td><?php echo e($vacation->replacementEmployee->names ?? ''); ?> <?php echo e($vacation->replacementEmployee->lastnames ?? ''); ?></td>
                                                </tr>
                                                <?php endif; ?>
                                                <?php if($vacation->approved_by): ?>
                                                <tr>
                                                    <td><strong>Aprobado por:</strong></td>
                                                    <td><?php echo e($vacation->approver->names ?? ''); ?> <?php echo e($vacation->approver->lastnames ?? ''); ?></td>
                                                </tr>
                                                <?php endif; ?>
                                                <?php if($vacation->approved_at): ?>
                                                <tr>
                                                    <td><strong>Fecha de Aprobación:</strong></td>
                                                    <td><?php echo e(\Carbon\Carbon::parse($vacation->approved_at)->format('d/m/Y H:i')); ?></td>
                                                </tr>
                                                <?php endif; ?>
                                                <tr>
                                                    <td><strong>Fecha de Solicitud:</strong></td>
                                                    <td><?php echo e($vacation->created_at?->format('d/m/Y H:i') ?? 'N/A'); ?></td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Última Actualización:</strong></td>
                                                    <td><?php echo e($vacation->updated_at?->format('d/m/Y H:i') ?? 'N/A'); ?></td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>

                                    <?php if($vacation->reason): ?>
                                    <div class="row mt-3">
                                        <div class="col-12">
                                            <h6><strong>Motivo/Observaciones:</strong></h6>
                                            <div class="border rounded p-3 bg-light">
                                                <?php echo e($vacation->reason); ?>

                                            </div>
                                        </div>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Acciones -->
                    <div class="row mt-3">
                        <div class="col-12">
                            <div class="card card-outline card-secondary">
                                <div class="card-header">
                                    <h5 class="card-title">
                                        <i class="fas fa-cogs"></i> Acciones
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="btn-group" role="group">
                                        <a href="<?php echo e(route('admin.personnel.vacations.edit', $vacation)); ?>" class="btn btn-warning">
                                            <i class="fas fa-edit"></i> Editar
                                        </a>
                                        
                                        <?php if($vacation->status === 'pending'): ?>
                                            <form action="<?php echo e(route('admin.personnel.vacations.approve', $vacation)); ?>" method="POST" class="d-inline">
                                                <?php echo csrf_field(); ?>
                                                <?php echo method_field('PATCH'); ?>
                                                <button type="submit" class="btn btn-success" onclick="return confirm('¿Está seguro de aprobar estas vacaciones?')">
                                                    <i class="fas fa-check"></i> Aprobar
                                                </button>
                                            </form>
                                            
                                            <form action="<?php echo e(route('admin.personnel.vacations.reject', $vacation)); ?>" method="POST" class="d-inline">
                                                <?php echo csrf_field(); ?>
                                                <?php echo method_field('PATCH'); ?>
                                                <button type="submit" class="btn btn-danger" onclick="return confirm('¿Está seguro de rechazar estas vacaciones?')">
                                                    <i class="fas fa-times"></i> Rechazar
                                                </button>
                                            </form>
                                        <?php endif; ?>

                                        <?php if($vacation->status === 'approved'): ?>
                                            <form action="<?php echo e(route('admin.personnel.vacations.cancel', $vacation)); ?>" method="POST" class="d-inline">
                                                <?php echo csrf_field(); ?>
                                                <?php echo method_field('PATCH'); ?>
                                                <button type="submit" class="btn btn-secondary" onclick="return confirm('¿Está seguro de cancelar estas vacaciones?')">
                                                    <i class="fas fa-ban"></i> Cancelar
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                    </div>

                                    <div class="btn-group float-right" role="group">
                                        <a href="<?php echo e(route('admin.personnel.vacations.index')); ?>" class="btn btn-secondary">
                                            <i class="fas fa-arrow-left"></i> Volver a la Lista
                                        </a>
                                        
                                        <button type="button" class="btn btn-info" onclick="window.print()">
                                            <i class="fas fa-print"></i> Imprimir
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('css'); ?>
    <style>
        .card-header {
            background: linear-gradient(90deg, #17a2b8, #117a8b);
            color: white;
        }
        .card-outline.card-primary {
            border-top: 3px solid #007bff;
        }
        .card-outline.card-info {
            border-top: 3px solid #17a2b8;
        }
        .card-outline.card-secondary {
            border-top: 3px solid #6c757d;
        }
        .table-borderless td {
            border: none;
            padding: 0.25rem 0.5rem;
        }
        .badge-lg {
            font-size: 1rem;
            padding: 0.5rem 1rem;
        }
        @media print {
            .btn, .card-tools, .breadcrumb {
                display: none !important;
            }
            .card {
                border: none !important;
                box-shadow: none !important;
            }
        }
    </style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('js'); ?>
    <script>
        $(document).ready(function() {
            // Auto-refresh de la página cada 30 segundos si el estado es pendiente
            <?php if($vacation->status === 'pending'): ?>
                setInterval(function() {
                    location.reload();
                }, 30000);
            <?php endif; ?>
        });
    </script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('adminlte::page', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH F:\USAT 2025-2\topicos\PryFINAL\PryRsuReciclaje\resources\views/vacations/show.blade.php ENDPATH**/ ?>