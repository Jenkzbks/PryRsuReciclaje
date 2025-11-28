@extends('adminlte::page')

@section('title', 'Nuevo Tipo de Empleado')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="h3 mb-1 font-weight-bold">
                <i class="fas fa-user-tag text-primary"></i> Crear Tipo de Empleado
            </h1>
            <p class="text-muted mb-0">Definir un nuevo tipo o categoría de empleado</p>
        </div>
        <a href="{{ route('personnel.employee-types.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Volver al Listado
        </a>
    </div>
@stop

@section('content')
    <form action="{{ route('personnel.employee-types.store') }}" method="POST" id="employeeTypeForm">
        @csrf
        
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
                                           class="form-control @error('name') is-invalid @enderror" 
                                           id="name" 
                                           name="name" 
                                           value="{{ old('name') }}" 
                                           placeholder="Ej: Administrador, Operador..."
                                           required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="code">Código</label>
                                    <input type="text" 
                                           class="form-control @error('code') is-invalid @enderror" 
                                           id="code" 
                                           name="code" 
                                           value="{{ old('code') }}" 
                                           placeholder="Ej: ADM, OPE..."
                                           maxlength="10">
                                    @error('code')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Código corto para identificar el tipo</small>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="description">Descripción</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" 
                                      name="description" 
                                      rows="3" 
                                      placeholder="Describe las responsabilidades y características de este tipo de empleado...">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="level">Nivel Jerárquico</label>
                                    <select class="form-control @error('level') is-invalid @enderror" 
                                            id="level" 
                                            name="level">
                                        <option value="">Seleccionar nivel...</option>
                                        <option value="1" {{ old('level') == '1' ? 'selected' : '' }}>Directivo</option>
                                        <option value="2" {{ old('level') == '2' ? 'selected' : '' }}>Gerencial</option>
                                        <option value="3" {{ old('level') == '3' ? 'selected' : '' }}>Supervisión</option>
                                        <option value="4" {{ old('level') == '4' ? 'selected' : '' }}>Operativo</option>
                                        <option value="5" {{ old('level') == '5' ? 'selected' : '' }}>Apoyo</option>
                                    </select>
                                    @error('level')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="sort_order">Orden de Visualización</label>
                                    <input type="number" 
                                           class="form-control @error('sort_order') is-invalid @enderror" 
                                           id="sort_order" 
                                           name="sort_order" 
                                           value="{{ old('sort_order', 0) }}" 
                                           min="0">
                                    @error('sort_order')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
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
                                               {{ old('active', true) ? 'checked' : '' }}>
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
                                               {{ old('protected') ? 'checked' : '' }}>
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
                                       class="form-control @error('color') is-invalid @enderror" 
                                       id="color" 
                                       name="color" 
                                       value="{{ old('color', '#007bff') }}" 
                                       style="height: 40px;">
                                <div class="input-group-append">
                                    <button type="button" class="btn btn-outline-secondary" onclick="resetColor()">
                                        <i class="fas fa-undo"></i>
                                    </button>
                                </div>
                            </div>
                            @error('color')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Color representativo del tipo de empleado</small>
                        </div>

                        <div class="form-group">
                            <label for="icon">Icono</label>
                            <input type="text" 
                                   class="form-control @error('icon') is-invalid @enderror" 
                                   id="icon" 
                                   name="icon" 
                                   value="{{ old('icon', 'fas fa-user') }}" 
                                   placeholder="fas fa-user">
                            @error('icon')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                Icono FontAwesome. 
                                <a href="https://fontawesome.com/icons" target="_blank">Ver iconos</a>
                            </small>
                        </div>

                        <div class="form-group">
                            <label>Vista Previa</label>
                            <div class="preview-badge p-3 border rounded text-center">
                                <span class="badge badge-pill p-2" id="badge-preview" style="background-color: #007bff; color: white;">
                                    <i class="fas fa-user" id="icon-preview"></i>
                                    <span id="name-preview">Tipo de Empleado</span>
                                </span>
                            </div>
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
                            <i class="fas fa-save"></i> Crear Tipo de Empleado
                        </button>
                        <a href="{{ route('personnel.employee-types.index') }}" class="btn btn-secondary btn-block">
                            <i class="fas fa-times"></i> Cancelar
                        </a>
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
    
    .preview-badge {
        background: #f8f9fa;
    }
    
    .badge {
        font-size: 0.9rem;
    }
</style>
@endpush

@push('js')
<script>
    $(document).ready(function() {
        // Preview en tiempo real
        updatePreview();
        
        $('#name, #color, #icon').on('input change', function() {
            updatePreview();
        });
        
        // Auto-generar código desde el nombre
        $('#name').on('input', function() {
            const name = $(this).val();
            const code = name.toUpperCase()
                .replace(/[^A-Z ]/g, '')
                .split(' ')
                .map(word => word.substring(0, 3))
                .join('')
                .substring(0, 10);
            $('#code').val(code);
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
@endpush