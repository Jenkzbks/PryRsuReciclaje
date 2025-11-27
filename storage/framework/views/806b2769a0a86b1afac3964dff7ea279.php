<?php $__env->startSection('title', 'Nuevo Empleado'); ?>

<?php $__env->startSection('content_header'); ?>
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="h3 mb-1 font-weight-bold">
                <i class="fas fa-user-plus text-primary"></i> Registrar Nuevo Empleado
            </h1>
            <p class="text-muted mb-0">Complete los datos del empleado para crear su registro en el sistema</p>
        </div>
        <a href="<?php echo e(route('admin.personnel.employees.index')); ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Volver al Listado
        </a>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <form action="<?php echo e(route('admin.personnel.employees.store')); ?>" method="POST" enctype="multipart/form-data" id="employeeForm">
        <?php echo csrf_field(); ?>
        
        <div class="row">
            <!-- Información Personal -->
            <div class="col-lg-8">
                <div class="card shadow-sm">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-user"></i> Información Personal
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="names" class="required">Nombres</label>
                                    <input type="text" 
                                           class="form-control <?php $__errorArgs = ['names'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                           id="names" 
                                           name="names" 
                                           value="<?php echo e(old('names')); ?>" 
                                           required>
                                    <?php $__errorArgs = ['names'];
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
                                    <label for="lastnames" class="required">Apellidos</label>
                                    <input type="text" 
                                           class="form-control <?php $__errorArgs = ['lastnames'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                           id="lastnames" 
                                           name="lastnames" 
                                           value="<?php echo e(old('lastnames')); ?>" 
                                           required>
                                    <?php $__errorArgs = ['lastnames'];
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
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="dni" class="required">DNI</label>
                                    <input type="text" 
                                           class="form-control <?php $__errorArgs = ['dni'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                           id="dni" 
                                           name="dni" 
                                           value="<?php echo e(old('dni')); ?>" 
                                           pattern="[0-9]{8}"
                                           maxlength="8"
                                           placeholder="12345678"
                                           required>
                                    <?php $__errorArgs = ['dni'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <div class="invalid-feedback"><?php echo e($message); ?></div>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                    <small class="form-text text-muted">Documento Nacional de Identidad (8 dígitos)</small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="birthday" class="required">Fecha de Nacimiento</label>
                                    <input type="date" 
                                           class="form-control <?php $__errorArgs = ['birthday'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                           id="birthday" 
                                           name="birthday" 
                                           value="<?php echo e(old('birthday')); ?>" 
                                           max="<?php echo e(date('Y-m-d', strtotime('-18 years'))); ?>"
                                           required>
                                    <?php $__errorArgs = ['birthday'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <div class="invalid-feedback"><?php echo e($message); ?></div>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                    <small class="form-text text-muted">Debe ser mayor de 18 años</small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="age">Edad (calculada)</label>
                                    <input type="number" 
                                           class="form-control bg-light" 
                                           id="age" 
                                           readonly>
                                    <small class="form-text text-muted">Se calcula automáticamente</small>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="email">Email</label>
                                    <input type="email" 
                                           class="form-control <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                           id="email" 
                                           name="email" 
                                           value="<?php echo e(old('email')); ?>"
                                           placeholder="empleado@empresa.com">
                                    <?php $__errorArgs = ['email'];
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
                                    <label for="phone">Teléfono</label>
                                    <input type="tel" 
                                           class="form-control <?php $__errorArgs = ['phone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                           id="phone" 
                                           name="phone" 
                                           value="<?php echo e(old('phone')); ?>"
                                           placeholder="999123456">
                                    <?php $__errorArgs = ['phone'];
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
                        </div>

                        <div class="form-group">
                            <label for="address">Dirección</label>
                            <textarea class="form-control <?php $__errorArgs = ['address'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                      id="address" 
                                      name="address" 
                                      rows="2"
                                      placeholder="Dirección completa del empleado"><?php echo e(old('address')); ?></textarea>
                            <?php $__errorArgs = ['address'];
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
                </div>

                <!-- Información Laboral -->
                <div class="card shadow-sm">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-briefcase"></i> Información Laboral
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="type_id" class="required">Tipo de Empleado</label>
                                    <select class="form-control select2 <?php $__errorArgs = ['type_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                            id="type_id" 
                                            name="type_id" 
                                            required>
                                        <option value="">Seleccionar tipo...</option>
                                        <?php $__currentLoopData = $employeeTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($type->id); ?>" 
                                                    <?php echo e(old('type_id') == $type->id ? 'selected' : ''); ?>>
                                                <?php echo e($type->name); ?>

                                                <?php if($type->description): ?>
                                                    - <?php echo e($type->description); ?>

                                                <?php endif; ?>
                                            </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                    <?php $__errorArgs = ['type_id'];
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
                            
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="status">Estado Inicial</label>
                                    <select class="form-control" id="status" name="status">
                                        <option value="active" <?php echo e(old('status', 'active') == 'active' ? 'selected' : ''); ?>>
                                            Activo - El empleado puede trabajar inmediatamente
                                        </option>
                                        <option value="inactive" <?php echo e(old('status') == 'inactive' ? 'selected' : ''); ?>>
                                            Inactivo - El empleado no puede trabajar por el momento
                                        </option>
                                    </select>
                                    <small class="form-text text-muted">
                                        Generalmente se registra como "Activo" para empleados que empezarán a trabajar
                                    </small>
                                </div>
                            </div>
                        </div>

                        <!-- Credenciales de Acceso -->
                        <h5 class="mb-3 text-primary">
                            <i class="fas fa-key"></i> Credenciales de Acceso al Kiosco
                        </h5>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="password">Contraseña para Kiosco de Asistencias</label>
                                    <input type="password" 
                                           class="form-control <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                           id="password" 
                                           name="password" 
                                           value="<?php echo e(old('password')); ?>"
                                           placeholder="Contraseña para acceder al kiosco">
                                    <?php $__errorArgs = ['password'];
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
                                    <label for="password_confirmation">Confirmar Contraseña</label>
                                    <input type="password" 
                                           class="form-control <?php $__errorArgs = ['password_confirmation'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                           id="password_confirmation" 
                                           name="password_confirmation" 
                                           placeholder="Confirmar contraseña">
                                    <?php $__errorArgs = ['password_confirmation'];
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
                            <div class="col-md-12">
                                <small class="form-text text-muted">
                                    <i class="fas fa-info-circle"></i> Si no se especifica, se usará el DNI como contraseña por defecto
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Foto y Acciones -->
            <div class="col-lg-4">
                <div class="card shadow-sm">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-camera"></i> Foto del Empleado
                        </h3>
                    </div>
                    <div class="card-body text-center">
                        <div class="photo-container mb-3 position-relative">
                            <!-- Placeholder para "Sin foto" -->
                            <div id="photo-placeholder" class="photo-placeholder bg-light border border-dashed rounded d-flex align-items-center justify-content-center" 
                                 style="width: 200px; height: 200px; margin: 0 auto;">
                                <div class="text-muted">
                                    <i class="fas fa-user fa-3x mb-2"></i>
                                    <br>
                                    <small>Sin foto</small>
                                </div>
                            </div>
                            
                            <!-- Preview de la imagen seleccionada -->
                            <img id="photo-preview" src="#" alt="Preview" 
                                 class="img-thumbnail rounded photo-preview" 
                                 style="width: 200px; height: 200px; object-fit: cover; margin: 0 auto; display: none;">
                        </div>
                        
                        <div class="form-group">
                            <input type="file" 
                                   class="form-control-file <?php $__errorArgs = ['photo'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                   id="photo" 
                                   name="photo" 
                                   accept="image/*"
                                   style="display: none;">
                            
                            <button type="button" class="btn btn-outline-primary btn-block mb-2" onclick="document.getElementById('photo').click()">
                                <i class="fas fa-upload"></i> <span id="upload-text">Seleccionar Foto</span>
                            </button>
                            
                            <button type="button" class="btn btn-outline-danger btn-sm" id="remove-photo" style="display: none;">
                                <i class="fas fa-trash"></i> Quitar Foto
                            </button>
                            
                            <?php $__errorArgs = ['photo'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback d-block"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            <small class="form-text text-muted">
                                Formatos: JPG, PNG, GIF<br>
                                Tamaño máximo: 2MB
                            </small>
                        </div>
                    </div>
                </div>

                <!-- Resumen de Validación -->
                <div class="card shadow-sm">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-check-circle"></i> Validación de Datos
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="validation-summary">
                            <div class="validation-item" data-field="names">
                                <i class="fas fa-circle text-muted"></i> Nombres completos
                            </div>
                            <div class="validation-item" data-field="lastnames">
                                <i class="fas fa-circle text-muted"></i> Apellidos completos
                            </div>
                            <div class="validation-item" data-field="dni">
                                <i class="fas fa-circle text-muted"></i> DNI válido (8 dígitos)
                            </div>
                            <div class="validation-item" data-field="birthday">
                                <i class="fas fa-circle text-muted"></i> Mayor de edad
                            </div>
                            <div class="validation-item" data-field="type_id">
                                <i class="fas fa-circle text-muted"></i> Tipo de empleado
                            </div>
                            
                        </div>
                    </div>
                </div>

                <!-- Botones de Acción -->
                <div class="card shadow-sm">
                    <div class="card-body">
                        <button type="submit" class="btn btn-primary btn-block btn-lg" id="submitBtn">
                            <i class="fas fa-save"></i> Registrar Empleado
                        </button>
                        <a href="<?php echo e(route('admin.personnel.employees.index')); ?>" class="btn btn-secondary btn-block mt-2">
                            <i class="fas fa-times"></i> Cancelar
                        </a>
                        
                        <hr>
                        
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="create_contract">
                            <label class="form-check-label" for="create_contract">
                                <small>Crear contrato después del registro</small>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('css'); ?>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap4-theme@1.0.0/dist/select2-bootstrap4.min.css">
    <style>
        .required::after {
            content: ' *';
            color: #dc3545;
        }
        
        .validation-item {
            font-size: 0.875rem;
            margin-bottom: 0.5rem;
        }
        
        .validation-item.valid i {
            color: #28a745 !important;
        }
        
        .validation-item.invalid i {
            color: #dc3545 !important;
        }
        
        .border-dashed {
            border-style: dashed !important;
        }
        
        /* Estilos para manejo de fotos */
        .photo-container {
            min-height: 200px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .photo-placeholder {
            transition: all 0.3s ease;
            cursor: pointer;
        }
        
        .photo-placeholder:hover {
            border-color: #007bff;
            background-color: #f8f9fa;
        }
        
        .photo-preview {
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
        }
        
        .photo-preview:hover {
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
    </style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('js'); ?>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            // Inicializar Select2
            $('.select2').select2({
                theme: 'bootstrap4',
                allowClear: true
            });

            // Validación en tiempo real
            setupRealTimeValidation();

            // Manejo de foto
            setupPhotoHandling();

            // Cálculo automático de edad
            setupAgeCalculation();

            // Validación de DNI
            setupDNIValidation();
        });

        function setupRealTimeValidation() {
            const requiredFields = ['names', 'lastnames', 'dni', 'birthday', 'type_id']; // 'departament_id' comentado
            
            requiredFields.forEach(field => {
                $(`#${field}`).on('input change', function() {
                    validateField(field, $(this).val());
                });
            });
        }

        function validateField(field, value) {
            const item = $(`.validation-item[data-field="${field}"]`);
            let isValid = false;

            switch(field) {
                case 'names':
                case 'lastnames':
                    isValid = value.trim().length >= 2;
                    break;
                case 'dni':
                    isValid = /^[0-9]{8}$/.test(value);
                    break;
                case 'birthday':
                    if (value) {
                        const birthDate = new Date(value);
                        const today = new Date();
                        const age = today.getFullYear() - birthDate.getFullYear();
                        isValid = age >= 18;
                    }
                    break;
                case 'type_id':
                // case 'departament_id': // Comentado temporalmente
                    isValid = value !== '';
                    break;
            }

            item.removeClass('valid invalid');
            item.addClass(isValid ? 'valid' : 'invalid');
            
            updateSubmitButton();
        }

        function updateSubmitButton() {
            const validItems = $('.validation-item.valid').length;
            const totalItems = $('.validation-item').length;
            
            if (validItems === totalItems) {
                $('#submitBtn').prop('disabled', false).removeClass('btn-secondary').addClass('btn-primary');
            } else {
                $('#submitBtn').prop('disabled', true).removeClass('btn-primary').addClass('btn-secondary');
            }
        }

        function setupPhotoHandling() {
            // Event listener para el input file
            $('#photo').on('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    // Validar tipo de archivo
                    if (!file.type.startsWith('image/')) {
                        alert('Por favor seleccione un archivo de imagen válido');
                        $(this).val(''); // Limpiar input
                        return;
                    }

                    // Validar tamaño (2MB)
                    if (file.size > 2 * 1024 * 1024) {
                        alert('La imagen debe ser menor a 2MB');
                        $(this).val(''); // Limpiar input
                        return;
                    }

                    // Mostrar preview
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        // Ocultar placeholder
                        $('#photo-placeholder').hide();
                        
                        // Mostrar preview
                        $('#photo-preview').attr('src', e.target.result).show();
                        
                        // Mostrar botón de quitar y cambiar texto
                        $('#remove-photo').show();
                        $('#upload-text').text('Cambiar Foto');
                    };
                    reader.readAsDataURL(file);
                } else {
                    // Si no hay archivo, mostrar placeholder
                    resetPhotoPreview();
                }
            });

            // Event listener para el botón de quitar foto
            $('#remove-photo').on('click', function() {
                // Limpiar input file
                $('#photo').val('');
                
                // Resetear preview
                resetPhotoPreview();
            });

            // Event listener para hacer clic en el placeholder
            $('#photo-placeholder').on('click', function() {
                $('#photo').click();
            });
            
            // Función para resetear el preview
            function resetPhotoPreview() {
                $('#photo-preview').hide().attr('src', '#');
                $('#photo-placeholder').show();
                $('#remove-photo').hide();
                $('#upload-text').text('Seleccionar Foto');
            }
        }

        function setupAgeCalculation() {
            $('#birthday').on('change', function() {
                const birthDate = new Date($(this).val());
                const today = new Date();
                
                if (birthDate && birthDate <= today) {
                    let age = today.getFullYear() - birthDate.getFullYear();
                    const monthDiff = today.getMonth() - birthDate.getMonth();
                    
                    if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDate.getDate())) {
                        age--;
                    }
                    
                    $('#age').val(age);
                    
                    if (age < 18) {
                        $(this).addClass('is-invalid');
                        $(this).next('.invalid-feedback').remove();
                        $(this).after('<div class="invalid-feedback">El empleado debe ser mayor de edad</div>');
                    } else {
                        $(this).removeClass('is-invalid');
                        $(this).next('.invalid-feedback').remove();
                    }
                }
            });
        }

        function setupDNIValidation() {
            $('#dni').on('input', function() {
                const dni = $(this).val();
                
                // Solo permitir números
                $(this).val(dni.replace(/[^0-9]/g, ''));
                
                // Validar longitud
                if ($(this).val().length === 8) {
                    // Aquí podrías agregar validación RENIEC si tienes API
                    $(this).removeClass('is-invalid').addClass('is-valid');
                } else {
                    $(this).removeClass('is-valid').addClass('is-invalid');
                }
            });
        }

        // Form submit
        $('#employeeForm').on('submit', function(e) {
            $('#submitBtn').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Registrando...');
        });
    </script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('adminlte::page', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH F:\USAT 2025-2\topicos\PryFINAL\PryRsuReciclaje\resources\views/personnel/employees/create.blade.php ENDPATH**/ ?>