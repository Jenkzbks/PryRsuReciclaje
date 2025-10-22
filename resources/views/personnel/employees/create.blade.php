@extends('adminlte::page')

@section('title', 'Nuevo Empleado')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="h3 mb-1 font-weight-bold">
                <i class="fas fa-user-plus text-primary"></i> Registrar Nuevo Empleado
            </h1>
            <p class="text-muted mb-0">Complete los datos del empleado para crear su registro en el sistema</p>
        </div>
        <a href="{{ route('admin.personnel.employees.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Volver al Listado
        </a>
    </div>
@stop

@section('content')
    <form action="{{ route('admin.personnel.employees.store') }}" method="POST" enctype="multipart/form-data" id="employeeForm">
        @csrf
        
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
                                           class="form-control @error('names') is-invalid @enderror" 
                                           id="names" 
                                           name="names" 
                                           value="{{ old('names') }}" 
                                           required>
                                    @error('names')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="lastnames" class="required">Apellidos</label>
                                    <input type="text" 
                                           class="form-control @error('lastnames') is-invalid @enderror" 
                                           id="lastnames" 
                                           name="lastnames" 
                                           value="{{ old('lastnames') }}" 
                                           required>
                                    @error('lastnames')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="dni" class="required">DNI</label>
                                    <input type="text" 
                                           class="form-control @error('dni') is-invalid @enderror" 
                                           id="dni" 
                                           name="dni" 
                                           value="{{ old('dni') }}" 
                                           pattern="[0-9]{8}"
                                           maxlength="8"
                                           placeholder="12345678"
                                           required>
                                    @error('dni')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Documento Nacional de Identidad (8 dígitos)</small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="birthday" class="required">Fecha de Nacimiento</label>
                                    <input type="date" 
                                           class="form-control @error('birthday') is-invalid @enderror" 
                                           id="birthday" 
                                           name="birthday" 
                                           value="{{ old('birthday') }}" 
                                           max="{{ date('Y-m-d', strtotime('-18 years')) }}"
                                           required>
                                    @error('birthday')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
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
                                           class="form-control @error('email') is-invalid @enderror" 
                                           id="email" 
                                           name="email" 
                                           value="{{ old('email') }}"
                                           placeholder="empleado@empresa.com">
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="phone">Teléfono</label>
                                    <input type="tel" 
                                           class="form-control @error('phone') is-invalid @enderror" 
                                           id="phone" 
                                           name="phone" 
                                           value="{{ old('phone') }}"
                                           placeholder="999123456">
                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="address">Dirección</label>
                            <textarea class="form-control @error('address') is-invalid @enderror" 
                                      id="address" 
                                      name="address" 
                                      rows="2"
                                      placeholder="Dirección completa del empleado">{{ old('address') }}</textarea>
                            @error('address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
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
                                    <select class="form-control select2 @error('type_id') is-invalid @enderror" 
                                            id="type_id" 
                                            name="type_id" 
                                            required>
                                        <option value="">Seleccionar tipo...</option>
                                        @foreach($employeeTypes as $type)
                                            <option value="{{ $type->id }}" 
                                                    {{ old('type_id') == $type->id ? 'selected' : '' }}>
                                                {{ $type->name }}
                                                @if($type->description)
                                                    - {{ $type->description }}
                                                @endif
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('type_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            {{-- Departamento comentado temporalmente
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="departament_id" class="required">Departamento</label>
                                    <select class="form-control select2 @error('departament_id') is-invalid @enderror" 
                                            id="departament_id" 
                                            name="departament_id" 
                                            required>
                                        <option value="">Seleccionar departamento...</option>
                                        @foreach($departments as $department)
                                            <option value="{{ $department->id }}" 
                                                    {{ old('departament_id') == $department->id ? 'selected' : '' }}>
                                                {{ $department->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('departament_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            --}}
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="status">Estado Inicial</label>
                                    <select class="form-control" id="status" name="status">
                                        <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>
                                            Activo - El empleado puede trabajar inmediatamente
                                        </option>
                                        <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>
                                            Inactivo - El empleado no puede trabajar por el momento
                                        </option>
                                    </select>
                                    <small class="form-text text-muted">
                                        Generalmente se registra como "Activo" para empleados que empezarán a trabajar
                                    </small>
                                </div>
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
                                   class="form-control-file @error('photo') is-invalid @enderror" 
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
                            
                            @error('photo')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
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
                            {{-- Departamento comentado temporalmente
                            <div class="validation-item" data-field="departament_id">
                                <i class="fas fa-circle text-muted"></i> Departamento asignado
                            </div>
                            --}}
                        </div>
                    </div>
                </div>

                <!-- Botones de Acción -->
                <div class="card shadow-sm">
                    <div class="card-body">
                        <button type="submit" class="btn btn-primary btn-block btn-lg" id="submitBtn">
                            <i class="fas fa-save"></i> Registrar Empleado
                        </button>
                        <a href="{{ route('admin.personnel.employees.index') }}" class="btn btn-secondary btn-block mt-2">
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
@stop

@section('css')
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
@stop

@section('js')
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
@stop