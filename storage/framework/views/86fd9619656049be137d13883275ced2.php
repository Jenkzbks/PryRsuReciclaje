<?php $__env->startSection('title', 'Editar Tipo de Empleado'); ?>

<?php $__env->startSection('content_header'); ?>
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="h3 mb-1 font-weight-bold">
                <i class="fas fa-user-edit text-primary"></i> Editar Tipo de Empleado
            </h1>
            <p class="text-muted mb-0">Modificar: <?php echo e($employeeType->name); ?></p>
        </div>
        <div>
            <a href="<?php echo e(route('personnel.employee-types.show', $employeeType)); ?>" class="btn btn-info">
                <i class="fas fa-eye"></i> Ver Detalles
            </a>
            <a href="<?php echo e(route('personnel.employee-types.index')); ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Volver al Listado
            </a>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <form action="<?php echo e(route('personnel.employee-types.update', $employeeType)); ?>" method="POST" id="employeeTypeForm">
        <?php echo csrf_field(); ?>
        <?php echo method_field('PUT'); ?>
        
        <div class="row">
            <div class="col-lg-8">
                <div class="card shadow-sm">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-info-circle"></i> Información Básica
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name" class="required">Nombre del Tipo</label>
                                    <input type="text" 
                                           class="form-control <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                           id="name" 
                                           name="name" 
                                           value="<?php echo e(old('name', $employeeType->name)); ?>" 
                                           placeholder="Ej: Administrador, Operador..."
                                           required>
                                    <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <div class="invalid-feedback"><?php echo e($message); ?></div>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="code">Código</label>
                                    <input type="text" 
                                           class="form-control <?php $__errorArgs = ['code'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                           id="code" 
                                           name="code" 
                                           value="<?php echo e(old('code', $employeeType->code)); ?>" 
                                           placeholder="Ej: ADM, OPE..."
                                           maxlength="10">
                                    <?php $__errorArgs = ['code'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <div class="invalid-feedback"><?php echo e($message); ?></div>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                    <small class="form-text text-muted">Código corto para identificar el tipo</small>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="description">Descripción</label>
                            <textarea class="form-control <?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                      id="description" 
                                      name="description" 
                                      rows="3" 
                                      placeholder="Describe las responsabilidades y características de este tipo de empleado..."><?php echo e(old('description', $employeeType->description)); ?></textarea>
                            <?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="level">Nivel Jerárquico</label>
                                    <select class="form-control <?php $__errorArgs = ['level'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                            id="level" 
                                            name="level">
                                        <option value="">Seleccionar nivel...</option>
                                        <option value="1" <?php echo e(old('level', $employeeType->level) == '1' ? 'selected' : ''); ?>>Directivo</option>
                                        <option value="2" <?php echo e(old('level', $employeeType->level) == '2' ? 'selected' : ''); ?>>Gerencial</option>
                                        <option value="3" <?php echo e(old('level', $employeeType->level) == '3' ? 'selected' : ''); ?>>Supervisión</option>
                                        <option value="4" <?php echo e(old('level', $employeeType->level) == '4' ? 'selected' : ''); ?>>Operativo</option>
                                        <option value="5" <?php echo e(old('level', $employeeType->level) == '5' ? 'selected' : ''); ?>>Apoyo</option>
                                    </select>
                                    <?php $__errorArgs = ['level'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <div class="invalid-feedback"><?php echo e($message); ?></div>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="sort_order">Orden de Visualización</label>
                                    <input type="number" 
                                           class="form-control <?php $__errorArgs = ['sort_order'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                           id="sort_order" 
                                           name="sort_order" 
                                           value="<?php echo e(old('sort_order', $employeeType->sort_order ?? 0)); ?>" 
                                           min="0">
                                    <?php $__errorArgs = ['sort_order'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <div class="invalid-feedback"><?php echo e($message); ?></div>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                    <small class="form-text text-muted">Orden en que aparece en las listas</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-cogs"></i> Configuración
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" 
                                               class="custom-control-input" 
                                               id="active" 
                                               name="active" 
                                               value="1" 
                                               <?php echo e(old('active', $employeeType->active) ? 'checked' : ''); ?>>
                                        <label class="custom-control-label" for="active">Activo</label>
                                    </div>
                                    <small class="form-text text-muted">Determina si este tipo está disponible para nuevos empleados</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" 
                                               class="custom-control-input" 
                                               id="protected" 
                                               name="protected" 
                                               value="1" 
                                               <?php echo e(old('protected', $employeeType->protected) ? 'checked' : ''); ?>>
                                        <label class="custom-control-label" for="protected">Protegido</label>
                                    </div>
                                    <small class="form-text text-muted">Los tipos protegidos no pueden ser eliminados</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card shadow-sm">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-palette"></i> Apariencia
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="color">Color</label>
                            <div class="input-group">
                                <input type="color" 
                                       class="form-control <?php $__errorArgs = ['color'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                       id="color" 
                                       name="color" 
                                       value="<?php echo e(old('color', $employeeType->color ?? '#007bff')); ?>" 
                                       style="height: 40px;">
                                <div class="input-group-append">
                                    <button type="button" class="btn btn-outline-secondary" onclick="resetColor()">
                                        <i class="fas fa-undo"></i>
                                    </button>
                                </div>
                            </div>
                            <?php $__errorArgs = ['color'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            <small class="form-text text-muted">Color representativo del tipo de empleado</small>
                        </div>

                        <div class="form-group">
                            <label for="icon">Icono</label>
                            <input type="text" 
                                   class="form-control <?php $__errorArgs = ['icon'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                   id="icon" 
                                   name="icon" 
                                   value="<?php echo e(old('icon', $employeeType->icon ?? 'fas fa-user')); ?>" 
                                   placeholder="fas fa-user">
                            <?php $__errorArgs = ['icon'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            <small class="form-text text-muted">
                                Icono FontAwesome. 
                                <a href="https://fontawesome.com/icons" target="_blank">Ver iconos</a>
                            </small>
                        </div>

                        <div class="form-group">
                            <label>Vista Previa</label>
                            <div class="preview-badge p-3 border rounded text-center">
                                <span class="badge badge-pill p-2" id="badge-preview" style="background-color: <?php echo e($employeeType->color ?? '#007bff'); ?>; color: white;">
                                    <i class="<?php echo e($employeeType->icon ?? 'fas fa-user'); ?>" id="icon-preview"></i>
                                    <span id="name-preview"><?php echo e($employeeType->name); ?></span>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-users"></i> Empleados Asociados
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="text-center">
                            <h4 class="text-primary"><?php echo e($employeeType->employees->count()); ?></h4>
                            <p class="text-muted">empleados con este tipo</p>
                            <?php if($employeeType->employees->count() > 0): ?>
                                <a href="<?php echo e(route('personnel.employee-types.show', $employeeType)); ?>" class="btn btn-outline-primary btn-sm">
                                    Ver empleados
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-save"></i> Acciones
                        </h3>
                    </div>
                    <div class="card-body">
                        <button type="submit" class="btn btn-success btn-block">
                            <i class="fas fa-save"></i> Guardar Cambios
                        </button>
                        <a href="<?php echo e(route('personnel.employee-types.show', $employeeType)); ?>" class="btn btn-info btn-block">
                            <i class="fas fa-eye"></i> Ver Detalles
                        </a>
                        <a href="<?php echo e(route('personnel.employee-types.index')); ?>" class="btn btn-secondary btn-block">
                            <i class="fas fa-times"></i> Cancelar
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </form>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('css'); ?>
<style>
    .required:after {
        content: " *";
        color: red;
    }
    
    .preview-badge {
        background: #f8f9fa;
    }
    
    .badge {
        font-size: 0.9rem;
    }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('js'); ?>
<script>
    $(document).ready(function() {
        // Preview en tiempo real
        updatePreview();
        
        $('#name, #color, #icon').on('input change', function() {
            updatePreview();
        });
    });
    
    function updatePreview() {
        const name = $('#name').val() || 'Tipo de Empleado';
        const color = $('#color').val();
        const icon = $('#icon').val() || 'fas fa-user';
        
        $('#name-preview').text(name);
        $('#badge-preview').css('background-color', color);
        $('#icon-preview').attr('class', icon);
    }
    
    function resetColor() {
        $('#color').val('#007bff');
        updatePreview();
    }
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('adminlte::page', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH F:\USAT 2025-2\topicos\PryFINAL\PryRsuReciclaje\resources\views/personnel/employee-types/edit.blade.php ENDPATH**/ ?>