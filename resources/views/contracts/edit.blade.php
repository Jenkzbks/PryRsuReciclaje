@extends('adminlte::page')

@section('title', 'Editar Contrato')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="h3 mb-1 font-weight-bold">
                <i class="fas fa-edit text-warning"></i> Editar Contrato
            </h1>
            <p class="text-muted mb-0">Modificar información del contrato laboral</p>
        </div>
        <div>
            <a href="{{ route('personnel.contracts.show', $contract) }}" class="btn btn-info mr-2">
                <i class="fas fa-eye"></i> Ver Detalles
            </a>
            <a href="{{ route('personnel.contracts.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Volver al Listado
            </a>
        </div>
    </div>
@stop

@section('content')
    <form action="{{ route('personnel.contracts.update', $contract) }}" method="POST" id="contractForm">
        @csrf
        @method('PUT')
        
        <div class="row">
            <div class="col-lg-8">
                <div class="card shadow-sm">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-user"></i> Información del Empleado
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="employee_id" class="required">Empleado</label>
                                    <select class="form-control @error('employee_id') is-invalid @enderror" 
                                            id="employee_id" 
                                            name="employee_id" 
                                            required>
                                        <option value="">Seleccionar empleado...</option>
                                        @foreach($employees as $employee)
                                            <option value="{{ $employee->id }}" 
                                                    {{ old('employee_id', $contract->employee_id) == $employee->id ? 'selected' : '' }}
                                                    data-dni="{{ $employee->dni }}"
                                                    data-type="{{ $employee->employeeType->name ?? '' }}">
                                                {{ $employee->names }} {{ $employee->lastnames }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('employee_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>DNI del Empleado</label>
                                    <input type="text" 
                                           class="form-control" 
                                           id="employee_dni" 
                                           readonly
                                           value="{{ $contract->employee->dni }}"
                                           placeholder="Se completa automáticamente">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-file-contract"></i> Datos del Contrato
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="contrato_type" class="required">Tipo de Contrato</label>
                                    <select class="form-control @error('contrato_type') is-invalid @enderror" 
                                            id="contrato_type" 
                                            name="contrato_type" 
                                            required>
                                        <option value="">Seleccionar tipo...</option>
                                        @foreach($contractTypes as $key => $type)
                                            <option value="{{ $key }}" 
                                                    {{ old('contrato_type', $contract->contrato_type) == $key ? 'selected' : '' }}>
                                                {{ $type }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('contrato_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="position_id" class="required">Cargo/Posición</label>
                                    <select class="form-control @error('position_id') is-invalid @enderror" 
                                            id="position_id" 
                                            name="position_id" 
                                            required>
                                        <option value="">Seleccionar cargo...</option>
                                        @foreach(\App\Models\EmployeeType::orderBy('name')->get() as $type)
                                            <option value="{{ $type->id }}" 
                                                    {{ old('position_id', $contract->position_id) == $type->id ? 'selected' : '' }}>
                                                {{ $type->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('position_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="start_date" class="required">Fecha de Inicio</label>
                                    <input type="date" 
                                           class="form-control @error('start_date') is-invalid @enderror" 
                                           id="start_date" 
                                           name="start_date" 
                                           value="{{ old('start_date', $contract->start_date) }}" 
                                           required>
                                    @error('start_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="end_date">Fecha de Fin</label>
                                    <input type="date" 
                                           class="form-control @error('end_date') is-invalid @enderror" 
                                           id="end_date" 
                                           name="end_date" 
                                           value="{{ old('end_date', $contract->end_date) }}">
                                    @error('end_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Dejar vacío para contratos indefinidos</small>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="salary" class="required">Salario Mensual</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">S/.</span>
                                        </div>
                                        <input type="number" 
                                               class="form-control @error('salary') is-invalid @enderror" 
                                               id="salary" 
                                               name="salary" 
                                               value="{{ old('salary', $contract->salary) }}" 
                                               step="0.01"
                                               min="0"
                                               placeholder="0.00"
                                               required>
                                        @error('salary')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="vacations_days_per_year">Días de Vacaciones por Año</label>
                                    <input type="number" 
                                           class="form-control @error('vacations_days_per_year') is-invalid @enderror" 
                                           id="vacations_days_per_year" 
                                           name="vacations_days_per_year" 
                                           value="{{ old('vacations_days_per_year', $contract->vacations_days_per_year ?? 30) }}" 
                                           min="0"
                                           max="365">
                                    @error('vacations_days_per_year')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="probation_period_months">Período de Prueba (meses)</label>
                                    <input type="number" 
                                           class="form-control @error('probation_period_months') is-invalid @enderror" 
                                           id="probation_period_months" 
                                           name="probation_period_months" 
                                           value="{{ old('probation_period_months', $contract->probation_period_months) }}" 
                                           min="0"
                                           max="12">
                                    @error('probation_period_months')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="departament_id">Departamento</label>
                                    <select class="form-control @error('departament_id') is-invalid @enderror" 
                                            id="departament_id" 
                                            name="departament_id">
                                        <option value="">Seleccionar departamento...</option>
                                        @foreach($departments as $department)
                                            <option value="{{ $department->id }}" 
                                                    {{ old('departament_id', $contract->departament_id) == $department->id ? 'selected' : '' }}>
                                                {{ $department->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('departament_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
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
                            <i class="fas fa-info-circle"></i> Estado del Contrato
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" 
                                       class="custom-control-input" 
                                       id="is_active" 
                                       name="is_active" 
                                       value="1" 
                                       {{ old('is_active', $contract->is_active) ? 'checked' : '' }}>
                                <label class="custom-control-label" for="is_active">Contrato Activo</label>
                            </div>
                            <small class="form-text text-muted">Los contratos activos aparecen en los reportes</small>
                        </div>

                        @if($contract->is_active)
                            <div class="alert alert-warning">
                                <h6><i class="fas fa-exclamation-triangle"></i> Atención:</h6>
                                <p class="mb-0 small">
                                    Este contrato está actualmente activo. Cambios en las fechas o salario 
                                    pueden afectar otros módulos del sistema.
                                </p>
                            </div>
                        @endif

                        <div class="alert alert-info">
                            <h6><i class="fas fa-lightbulb"></i> Consejos:</h6>
                            <ul class="mb-0 small">
                                <li>Solo un contrato puede estar activo por empleado</li>
                                <li>Los contratos eventuales requieren fecha de fin</li>
                                <li>El período de prueba es opcional</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-history"></i> Información del Registro
                        </h3>
                    </div>
                    <div class="card-body">
                        <table class="table table-sm table-borderless">
                            <tr>
                                <td class="text-muted">Creado:</td>
                                <td>{{ $contract->created_at?->format('d/m/Y H:i') ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Última modificación:</td>
                                <td>{{ $contract->updated_at?->format('d/m/Y H:i') ?? 'N/A' }}</td>
                            </tr>
                        </table>
                    </div>
                </div>

                <div class="card shadow-sm">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-save"></i> Acciones
                        </h3>
                    </div>
                    <div class="card-body">
                        <button type="submit" class="btn btn-warning btn-block">
                            <i class="fas fa-save"></i> Actualizar Contrato
                        </button>
                        <a href="{{ route('personnel.contracts.show', $contract) }}" class="btn btn-info btn-block">
                            <i class="fas fa-eye"></i> Ver Detalles
                        </a>
                        <a href="{{ route('personnel.contracts.index') }}" class="btn btn-secondary btn-block">
                            <i class="fas fa-times"></i> Cancelar
                        </a>
                        
                        @can('delete', $contract)
                            <button type="button" class="btn btn-danger btn-block mt-3" onclick="confirmDelete()">
                                <i class="fas fa-trash"></i> Eliminar Contrato
                            </button>
                        @endcan
                    </div>
                </div>
            </div>
        </div>
    </form>

    <!-- Modal de confirmación para eliminar -->
    @can('delete', $contract)
        <form id="deleteForm" action="{{ route('personnel.contracts.destroy', $contract) }}" method="POST" style="display: none;">
            @csrf
            @method('DELETE')
        </form>
    @endcan
@stop

@section('css')
    <style>
        .required:after {
            content: " *";
            color: red;
        }
    </style>
@stop

@section('js')
    <script>
        $(document).ready(function() {
            // Actualizar DNI cuando se selecciona empleado
            $('#employee_id').on('change', function() {
                const selectedOption = $(this).find('option:selected');
                const dni = selectedOption.data('dni');
                $('#employee_dni').val(dni || '');
            });

            // Validar fecha de fin según tipo de contrato
            $('#contrato_type').on('change', function() {
                const type = $(this).val();
                const endDateField = $('#end_date');
                
                if (type === 'eventual') {
                    endDateField.prop('required', true);
                    endDateField.closest('.form-group').find('label').addClass('required');
                } else {
                    endDateField.prop('required', false);
                    endDateField.closest('.form-group').find('label').removeClass('required');
                }
            });

            // Validar fechas
            $('#start_date, #end_date').on('change', function() {
                const startDate = new Date($('#start_date').val());
                const endDate = new Date($('#end_date').val());
                
                if (startDate && endDate && endDate <= startDate) {
                    alert('La fecha de fin debe ser posterior a la fecha de inicio');
                    $('#end_date').val('');
                }
            });

            // Trigger inicial
            $('#employee_id').trigger('change');
            $('#contrato_type').trigger('change');
        });

        function confirmDelete() {
            Swal.fire({
                title: '¿Estás seguro?',
                text: 'Esta acción no se puede deshacer. El contrato será eliminado permanentemente.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('deleteForm').submit();
                }
            });
        }
    </script>
@stop