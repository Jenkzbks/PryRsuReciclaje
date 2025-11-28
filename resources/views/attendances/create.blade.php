@extends('adminlte::page')

@section('title', 'Registrar Asistencia')

@section('content_header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1>Registrar Asistencia</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.personnel.dashboard') }}">Personal</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.personnel.attendances.index') }}">Asistencias</a></li>
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
                    <h3 class="card-title">Información de Asistencia</h3>
                </div>
                <form action="{{ route('admin.personnel.attendances.store') }}" method="POST">
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
                                    <label for="date">Fecha <span class="text-danger">*</span></label>
                                    <input type="date" name="date" id="date" 
                                           class="form-control @error('date') is-invalid @enderror" 
                                           value="{{ old('date', date('Y-m-d')) }}" required>
                                    @error('date')
                                        <span class="invalid-feedback" role="alert">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="check_in">Hora de Entrada</label>
                                    <input type="time" name="check_in" id="check_in" 
                                           class="form-control @error('check_in') is-invalid @enderror" 
                                           value="{{ old('check_in') }}">
                                    <small class="form-text text-muted">Dejar vacío para registrar hora actual</small>
                                    @error('check_in')
                                        <span class="invalid-feedback" role="alert">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="check_out">Hora de Salida</label>
                                    <input type="time" name="check_out" id="check_out" 
                                           class="form-control @error('check_out') is-invalid @enderror" 
                                           value="{{ old('check_out') }}">
                                    <small class="form-text text-muted">Opcional</small>
                                    @error('check_out')
                                        <span class="invalid-feedback" role="alert">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="status">Estado <span class="text-danger">*</span></label>
                                    <select name="status" id="status" class="form-control @error('status') is-invalid @enderror" required>
                                        <option value="">Seleccionar estado...</option>
                                        <option value="present" {{ old('status') == 'present' ? 'selected' : '' }}>Presente</option>
                                        <option value="late" {{ old('status') == 'late' ? 'selected' : '' }}>Tarde</option>
                                        <option value="absent" {{ old('status') == 'absent' ? 'selected' : '' }}>Ausente</option>
                                        <option value="half_day" {{ old('status') == 'half_day' ? 'selected' : '' }}>Medio Día</option>
                                    </select>
                                    @error('status')
                                        <span class="invalid-feedback" role="alert">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="hours_worked">Horas Trabajadas</label>
                                    <input type="number" name="hours_worked" id="hours_worked" 
                                           class="form-control @error('hours_worked') is-invalid @enderror" 
                                           value="{{ old('hours_worked') }}" step="0.5" min="0" max="24">
                                    <small class="form-text text-muted">Se calculará automáticamente si no se especifica</small>
                                    @error('hours_worked')
                                        <span class="invalid-feedback" role="alert">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="notes">Notas</label>
                                    <textarea name="notes" id="notes" class="form-control @error('notes') is-invalid @enderror" 
                                              rows="3" placeholder="Observaciones adicionales...">{{ old('notes') }}</textarea>
                                    @error('notes')
                                        <span class="invalid-feedback" role="alert">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Registrar Asistencia
                        </button>
                        <a href="{{ route('admin.personnel.attendances.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Cancelar
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop

@section('js')
    <script>
        $(document).ready(function() {
            // Auto-calcular horas trabajadas cuando se seleccionan las horas
            $('#check_in, #check_out').on('change', function() {
                calculateHours();
            });

            // Establecer hora actual si no hay valor
            $('#check_in').on('focus', function() {
                if (!$(this).val()) {
                    const now = new Date();
                    const time = now.getHours().toString().padStart(2, '0') + ':' + 
                                now.getMinutes().toString().padStart(2, '0');
                    $(this).val(time);
                }
            });

            // Cambiar estado automáticamente según la hora de entrada
            $('#check_in').on('change', function() {
                const checkInTime = $(this).val();
                if (checkInTime) {
                    const workStartTime = '08:00'; // Hora de inicio de trabajo
                    
                    if (checkInTime > workStartTime) {
                        $('#status').val('late');
                    } else {
                        $('#status').val('present');
                    }
                }
            });
        });

        function calculateHours() {
            const checkIn = $('#check_in').val();
            const checkOut = $('#check_out').val();
            
            if (checkIn && checkOut) {
                const start = new Date('1970-01-01T' + checkIn + ':00');
                const end = new Date('1970-01-01T' + checkOut + ':00');
                
                if (end > start) {
                    const diff = (end - start) / (1000 * 60 * 60); // Diferencia en horas
                    $('#hours_worked').val(diff.toFixed(1));
                }
            }
        }
    </script>
@stop