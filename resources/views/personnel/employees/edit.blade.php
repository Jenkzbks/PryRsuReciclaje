@extends('adminlte::page')

@section('title', 'Editar Empleado')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="h3 mb-1 font-weight-bold">
                <i class="fas fa-user-edit text-primary"></i> Editar Empleado
            </h1>
            <p class="text-muted mb-0">Actualizar información del empleado: {{ $employee->names }} {{ $employee->lastnames }}</p>
        </div>
        <div>
            <a href="{{ route('admin.personnel.employees.show', $employee) }}" class="btn btn-info">
                <i class="fas fa-eye"></i> Ver Perfil
            </a>
            <a href="{{ route('admin.personnel.employees.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Volver al Listado
            </a>
        </div>
    </div>
@stop

@section('content')
    <form action="{{ route('admin.personnel.employees.update', $employee) }}" method="POST" enctype="multipart/form-data" id="employeeForm">
        @csrf
        @method('PUT')
        
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
                                           value="{{ old('names', $employee->names) }}" 
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
                                           value="{{ old('lastnames', $employee->lastnames) }}" 
                                           required>
                                    @error('lastnames')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="dni" class="required">DNI</label>
                                    <input type="text" 
                                           class="form-control @error('dni') is-invalid @enderror" 
                                           id="dni" 
                                           name="dni" 
                                           value="{{ old('dni', $employee->dni) }}" 
                                           maxlength="8" 
                                           pattern="[0-9]{8}" 
                                           required>
                                    @error('dni')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="birthday" class="required">Fecha de Nacimiento</label>
                                    <input type="date" 
                                           class="form-control @error('birthday') is-invalid @enderror" 
                                           id="birthday" 
                                           name="birthday" 
                                           value="{{ old('birthday', $employee->birthday ? $employee->birthday->format('Y-m-d') : '') }}" 
                                           required>
                                    @error('birthday')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="gender">Género</label>
                                    <select class="form-control @error('gender') is-invalid @enderror" 
                                            id="gender" 
                                            name="gender">
                                        <option value="">Seleccionar género...</option>
                                        <option value="M" {{ old('gender', $employee->gender) == 'M' ? 'selected' : '' }}>Masculino</option>
                                        <option value="F" {{ old('gender', $employee->gender) == 'F' ? 'selected' : '' }}>Femenino</option>
                                        <option value="O" {{ old('gender', $employee->gender) == 'O' ? 'selected' : '' }}>Otro</option>
                                    </select>
                                    @error('gender')
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
                                           value="{{ old('phone', $employee->phone) }}" 
                                           maxlength="20">
                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
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
                                           value="{{ old('email', $employee->email) }}">
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="address">Dirección</label>
                                    <input type="text" 
                                           class="form-control @error('address') is-invalid @enderror" 
                                           id="address" 
                                           name="address" 
                                           value="{{ old('address', $employee->address) }}">
                                    @error('address')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
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
                                                    {{ old('type_id', $employee->type_id) == $type->id ? 'selected' : '' }}>
                                                {{ $type->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('type_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="hire_date">Fecha de Contratación</label>
                                    <input type="date" 
                                           class="form-control @error('hire_date') is-invalid @enderror" 
                                           id="hire_date" 
                                           name="hire_date" 
                                           value="{{ old('hire_date', $employee->hire_date ? $employee->hire_date->format('Y-m-d') : '') }}">
                                    @error('hire_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="salary">Salario</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">S/</span>
                                        </div>
                                        <input type="number" 
                                               class="form-control @error('salary') is-invalid @enderror" 
                                               id="salary" 
                                               name="salary" 
                                               value="{{ old('salary', $employee->salary) }}" 
                                               step="0.01" 
                                               min="0">
                                        @error('salary')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="status">Estado</label>
                                    <select class="form-control @error('status') is-invalid @enderror" 
                                            id="status" 
                                            name="status">
                                        <option value="active" {{ old('status', $employee->status) == 'active' ? 'selected' : '' }}>Activo</option>
                                        <option value="inactive" {{ old('status', $employee->status) == 'inactive' ? 'selected' : '' }}>Inactivo</option>
                                        <option value="suspended" {{ old('status', $employee->status) == 'suspended' ? 'selected' : '' }}>Suspendido</option>
                                        <option value="terminated" {{ old('status', $employee->status) == 'terminated' ? 'selected' : '' }}>Terminado</option>
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
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
                                    <label for="password">Nueva Contraseña para Kiosco</label>
                                    <input type="password" 
                                           class="form-control @error('password') is-invalid @enderror" 
                                           id="password" 
                                           name="password" 
                                           placeholder="Dejar vacío para mantener la actual">
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="password_confirmation">Confirmar Contraseña</label>
                                    <input type="password" 
                                           class="form-control @error('password_confirmation') is-invalid @enderror" 
                                           id="password_confirmation" 
                                           name="password_confirmation" 
                                           placeholder="Confirmar nueva contraseña">
                                    @error('password_confirmation')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-12">
                                <small class="form-text text-muted">
                                    <i class="fas fa-info-circle"></i> Solo completa si deseas cambiar la contraseña. Si está vacío, mantendrá la actual.
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Panel Lateral -->
            <div class="col-lg-4">
                <!-- Foto del Empleado -->
                <div class="card shadow-sm">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-camera"></i> Foto del Empleado
                        </h3>
                    </div>
                    <div class="card-body text-center">
                        <div class="employee-photo mb-3">
                            @if($employee->photo)
                                <img src="{{ asset('storage/' . $employee->photo) }}" 
                                     alt="Foto de {{ $employee->names }}" 
                                     class="img-thumbnail" 
                                     style="max-width: 200px; max-height: 200px;" 
                                     id="photoPreview">
                            @else
                                <div class="placeholder-photo bg-light d-flex align-items-center justify-content-center" 
                                     style="width: 200px; height: 200px; margin: 0 auto;" 
                                     id="photoPreview">
                                    <i class="fas fa-user fa-5x text-muted"></i>
                                </div>
                            @endif
                        </div>
                        
                        <div class="form-group">
                            <label for="photo">Cambiar Foto</label>
                            <input type="file" 
                                   class="form-control-file @error('photo') is-invalid @enderror" 
                                   id="photo" 
                                   name="photo" 
                                   accept="image/*" 
                                   onchange="previewPhoto(this)">
                            @error('photo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                Formatos: JPG, PNG, GIF. Máximo 2MB.
                            </small>
                        </div>
                    </div>
                </div>

                <!-- Información Adicional -->
                <div class="card shadow-sm">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-info-circle"></i> Información del Registro
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="info-item mb-2">
                            <strong>Creado:</strong><br>
                            <span class="text-muted">{{ $employee->created_at->format('d/m/Y H:i') }}</span>
                        </div>
                        <div class="info-item mb-2">
                            <strong>Última actualización:</strong><br>
                            <span class="text-muted">{{ $employee->updated_at->format('d/m/Y H:i') }}</span>
                        </div>
                        @if($employee->hire_date)
                        <div class="info-item mb-2">
                            <strong>Antigüedad:</strong><br>
                            <span class="text-muted">{{ $employee->hire_date->diffForHumans() }}</span>
                        </div>
                        @endif
                        @if($employee->birthday)
                        <div class="info-item">
                            <strong>Edad:</strong><br>
                            <span class="text-muted">{{ $employee->birthday->age }} años</span>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Botones de Acción -->
        <div class="row mt-3">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-save"></i> Guardar Cambios
                                </button>
                                <button type="reset" class="btn btn-warning">
                                    <i class="fas fa-undo"></i> Restablecer
                                </button>
                            </div>
                            <div>
                                <a href="{{ route('admin.personnel.employees.show', $employee) }}" class="btn btn-info">
                                    <i class="fas fa-eye"></i> Ver Perfil
                                </a>
                                <a href="{{ route('admin.personnel.employees.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Cancelar
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
@stop

@push('css')
<style>
    .required:after {
        content: " *";
        color: red;
    }
    
    .employee-photo img {
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    
    .placeholder-photo {
        border: 2px dashed #ddd;
        border-radius: 8px;
    }
    
    .info-item {
        padding: 8px 0;
        border-bottom: 1px solid #eee;
    }
    
    .info-item:last-child {
        border-bottom: none;
    }
    
    .select2-container--default .select2-selection--single {
        height: 38px;
        border: 1px solid #ced4da;
    }
    
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 36px;
        padding-left: 12px;
    }
</style>
@endpush

@push('js')
<script>
    // Previsualización de foto
    function previewPhoto(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                $('#photoPreview').html('<img src="' + e.target.result + '" class="img-thumbnail" style="max-width: 200px; max-height: 200px;">');
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    // Inicializar Select2
    $(document).ready(function() {
        $('.select2').select2({
            theme: 'bootstrap4',
            width: '100%'
        });

        // Validación del formulario
        $('#employeeForm').on('submit', function(e) {
            let isValid = true;
            
            // Validar campos requeridos
            $('input[required], select[required]').each(function() {
                if (!$(this).val()) {
                    $(this).addClass('is-invalid');
                    isValid = false;
                } else {
                    $(this).removeClass('is-invalid');
                }
            });

            // Validar DNI
            const dni = $('#dni').val();
            if (dni && (dni.length !== 8 || !/^\d+$/.test(dni))) {
                $('#dni').addClass('is-invalid');
                isValid = false;
            }

            // Validar email
            const email = $('#email').val();
            if (email && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
                $('#email').addClass('is-invalid');
                isValid = false;
            }

            if (!isValid) {
                e.preventDefault();
                toastr.error('Por favor corrige los errores en el formulario');
            }
        });

        // Quitar clase de error al escribir
        $('input, select').on('input change', function() {
            $(this).removeClass('is-invalid');
        });
    });
</script>
@endpush