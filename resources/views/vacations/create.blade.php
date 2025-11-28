@extends('adminlte::page')

@section('title', 'Registrar Vacaciones')

@section('content_header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1>Registrar Vacaciones</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('admin.personnel.vacations.index') }}">Vacaciones</a></li>
                <li class="breadcrumb-item active">Registrar</li>
            </ol>
        </div>
    </div>
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Información de Vacaciones</h3>
                </div>
                <form action="{{ route('admin.personnel.vacations.store') }}" method="POST">
                    @csrf
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="employee_id">Empleado <span class="text-danger">*</span></label>
                                    <select name="employee_id" id="employee_id" class="form-control @error('employee_id') is-invalid @enderror" required>
                                        <option value="">Seleccionar empleado...</option>
                                        @foreach($employees as $employee)
                                            <option value="{{ $employee->id }}" {{ old('employee_id') == $employee->id ? 'selected' : '' }}>
                                                {{ $employee->names }} {{ $employee->lastnames }} - {{ $employee->employee_code }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('employee_id')
                                        <span class="invalid-feedback" role="alert">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="vacation_type">Tipo de Vacaciones <span class="text-danger">*</span></label>
                                    <select name="vacation_type" id="vacation_type" class="form-control @error('vacation_type') is-invalid @enderror" required>
                                        <option value="">Seleccionar tipo...</option>
                                        <option value="annual" {{ old('vacation_type') == 'annual' ? 'selected' : '' }}>Vacaciones Anuales</option>
                                        <option value="personal" {{ old('vacation_type') == 'personal' ? 'selected' : '' }}>Días Personales</option>
                                        <option value="sick" {{ old('vacation_type') == 'sick' ? 'selected' : '' }}>Licencia por Enfermedad</option>
                                        <option value="maternity" {{ old('vacation_type') == 'maternity' ? 'selected' : '' }}>Licencia de Maternidad</option>
                                        <option value="paternity" {{ old('vacation_type') == 'paternity' ? 'selected' : '' }}>Licencia de Paternidad</option>
                                        <option value="emergency" {{ old('vacation_type') == 'emergency' ? 'selected' : '' }}>Emergencia Familiar</option>
                                    </select>
                                    @error('vacation_type')
                                        <span class="invalid-feedback" role="alert">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="start_date">Fecha de Inicio <span class="text-danger">*</span></label>
                                    <input type="date" name="start_date" id="start_date" 
                                           class="form-control @error('start_date') is-invalid @enderror" 
                                           value="{{ old('start_date') }}" required>
                                    @error('start_date')
                                        <span class="invalid-feedback" role="alert">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="end_date">Fecha de Fin <span class="text-danger">*</span></label>
                                    <input type="date" name="end_date" id="end_date" 
                                           class="form-control @error('end_date') is-invalid @enderror" 
                                           value="{{ old('end_date') }}" required>
                                    @error('end_date')
                                        <span class="invalid-feedback" role="alert">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="days_taken">Días Solicitados</label>
                                    <input type="number" name="days_taken" id="days_taken" 
                                           class="form-control @error('days_taken') is-invalid @enderror" 
                                           value="{{ old('days_taken') }}" readonly>
                                    @error('days_taken')
                                        <span class="invalid-feedback" role="alert">{{ $message }}</span>
                                    @enderror
                                    <small class="form-text text-muted">Se calcula automáticamente basado en las fechas</small>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="replacement_employee_id">Empleado de Reemplazo</label>
                                    <select name="replacement_employee_id" id="replacement_employee_id" class="form-control @error('replacement_employee_id') is-invalid @enderror">
                                        <option value="">Sin reemplazo específico...</option>
                                        @foreach($employees as $employee)
                                            <option value="{{ $employee->id }}" {{ old('replacement_employee_id') == $employee->id ? 'selected' : '' }}>
                                                {{ $employee->names }} {{ $employee->lastnames }} - {{ $employee->employee_code }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('replacement_employee_id')
                                        <span class="invalid-feedback" role="alert">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="reason">Motivo/Observaciones</label>
                                    <textarea name="reason" id="reason" rows="3" 
                                              class="form-control @error('reason') is-invalid @enderror" 
                                              placeholder="Descripción del motivo de las vacaciones...">{{ old('reason') }}</textarea>
                                    @error('reason')
                                        <span class="invalid-feedback" role="alert">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="status">Estado</label>
                                    <select name="status" id="status" class="form-control @error('status') is-invalid @enderror">
                                        <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>Pendiente</option>
                                        <option value="approved" {{ old('status') == 'approved' ? 'selected' : '' }}>Aprobado</option>
                                        <option value="rejected" {{ old('status') == 'rejected' ? 'selected' : '' }}>Rechazado</option>
                                    </select>
                                    @error('status')
                                        <span class="invalid-feedback" role="alert">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="approved_by">Aprobado por</label>
                                    <select name="approved_by" id="approved_by" class="form-control @error('approved_by') is-invalid @enderror">
                                        <option value="">Seleccionar supervisor...</option>
                                        @foreach($employees as $employee)
                                            <option value="{{ $employee->id }}" {{ old('approved_by') == $employee->id ? 'selected' : '' }}>
                                                {{ $employee->names }} {{ $employee->lastnames }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('approved_by')
                                        <span class="invalid-feedback" role="alert">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Guardar Vacaciones
                        </button>
                        <a href="{{ route('admin.personnel.vacations.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Cancelar
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop

@section('css')
    <style>
        .form-group label {
            font-weight: 600;
        }
        .text-danger {
            color: #dc3545 !important;
        }
        .card-header {
            background: linear-gradient(90deg, #007bff, #0056b3);
            color: white;
        }
    </style>
@stop

@section('js')
    <script>
        $(document).ready(function() {
            // Calcular días automáticamente cuando cambian las fechas
            $('#start_date, #end_date').on('change', function() {
                calculateDays();
            });

            function calculateDays() {
                var startDate = $('#start_date').val();
                var endDate = $('#end_date').val();
                
                if (startDate && endDate) {
                    var start = new Date(startDate);
                    var end = new Date(endDate);
                    
                    if (end >= start) {
                        var timeDiff = end.getTime() - start.getTime();
                        var daysDiff = Math.ceil(timeDiff / (1000 * 3600 * 24)) + 1; // +1 para incluir ambos días
                        $('#days_taken').val(daysDiff);
                    } else {
                        $('#days_taken').val('');
                        alert('La fecha de fin debe ser posterior a la fecha de inicio');
                    }
                }
            }

            // Validar que el empleado de reemplazo no sea el mismo
            $('#replacement_employee_id').on('change', function() {
                var employeeId = $('#employee_id').val();
                var replacementId = $(this).val();
                
                if (employeeId && replacementId && employeeId == replacementId) {
                    alert('El empleado de reemplazo no puede ser el mismo empleado');
                    $(this).val('');
                }
            });

            // Mostrar/ocultar campo de aprobado por según el estado
            $('#status').on('change', function() {
                var status = $(this).val();
                var approvedByField = $('#approved_by').closest('.form-group');
                
                if (status === 'approved') {
                    approvedByField.show();
                    $('#approved_by').attr('required', true);
                } else {
                    approvedByField.hide();
                    $('#approved_by').attr('required', false);
                    $('#approved_by').val('');
                }
            });

            // Inicializar la visibilidad del campo aprobado por
            $('#status').trigger('change');
        });
    </script>
@stop