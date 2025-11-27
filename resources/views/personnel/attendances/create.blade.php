@extends('adminlte::page')

@section('title', 'Registrar Asistencia')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="h3 mb-1 font-weight-bold">
                <i class="fas fa-plus text-primary"></i> Registrar Nueva Asistencia
            </h1>
            <p class="text-muted mb-0">Registrar la asistencia de un empleado</p>
        </div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.personnel.attendances.index') }}">Asistencias</a></li>
                <li class="breadcrumb-item active">Registrar</li>
            </ol>
        </nav>
    </div>
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-info-circle"></i> Información de Asistencia
                    </h3>
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
                                                {{ $employee->names }} {{ $employee->lastnames }} - DNI: {{ $employee->dni }}
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
                                    <small class="form-text text-muted">Opcional - se puede registrar después</small>
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
                                        <option value="present" {{ old('status') == 'present' ? 'selected' : '' }}>
                                            <i class="fas fa-check text-success"></i> Presente
                                        </option>
                                        <option value="late" {{ old('status') == 'late' ? 'selected' : '' }}>
                                            <i class="fas fa-clock text-warning"></i> Tarde
                                        </option>
                                        <option value="absent" {{ old('status') == 'absent' ? 'selected' : '' }}>
                                            <i class="fas fa-times text-danger"></i> Ausente
                                        </option>
                                        <option value="half_day" {{ old('status') == 'half_day' ? 'selected' : '' }}>
                                            <i class="fas fa-minus text-info"></i> Medio Día
                                        </option>
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
                                    <small class="form-text text-muted">Se calculará automáticamente basado en entrada y salida</small>
                                    @error('hours_worked')
                                        <span class="invalid-feedback" role="alert">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="notes">Observaciones</label>
                                    <textarea name="notes" id="notes" class="form-control @error('notes') is-invalid @enderror" 
                                              rows="3" placeholder="Notas adicionales sobre la asistencia (opcional)...">{{ old('notes') }}</textarea>
                                    @error('notes')
                                        <span class="invalid-feedback" role="alert">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Vista previa de información -->
                        <div class="row" id="preview-section" style="display: none;">
                            <div class="col-12">
                                <div class="alert alert-info">
                                    <h5><i class="fas fa-info-circle"></i> Vista Previa</h5>
                                    <div id="preview-content"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer bg-light">
                        <div class="row">
                            <div class="col-md-6">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Registrar Asistencia
                                </button>
                                <a href="{{ route('admin.personnel.attendances.index') }}" class="btn btn-secondary ml-2">
                                    <i class="fas fa-arrow-left"></i> Cancelar
                                </a>
                            </div>
                            <div class="col-md-6 text-right">
                                <small class="text-muted">
                                    <i class="fas fa-info-circle"></i> Los campos marcados con * son obligatorios
                                </small>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop

@section('css')
<style>
    .form-control:focus {
        border-color: #0086cd;
        box-shadow: 0 0 0 0.2rem rgba(0, 134, 205, 0.25);
    }
    
    .btn-primary {
        background-color: #002b5a;
        border-color: #002b5a;
    }
    
    .btn-primary:hover {
        background-color: #0086cd;
        border-color: #0086cd;
    }
    
    .card {
        border: none;
        border-radius: 10px;
    }
    
    .card-header {
        background-color: #f8f9fa;
        border-bottom: 1px solid #e9ecef;
        border-radius: 10px 10px 0 0 !important;
    }
    
    .breadcrumb {
        background-color: transparent;
        padding: 0;
    }
    
    .breadcrumb-item + .breadcrumb-item::before {
        color: #6c757d;
    }
</style>
@stop

@section('js')
<script>
$(document).ready(function() {
    // Auto-calcular horas trabajadas cuando se seleccionan las horas
    $('#check_in, #check_out').on('change', function() {
        calculateHours();
        updatePreview();
    });

    // Actualizar vista previa cuando cambien los campos
    $('#employee_id, #date, #status, #notes').on('change input', function() {
        updatePreview();
    });

    // Establecer hora actual si no hay valor al hacer foco
    $('#check_in').on('focus', function() {
        if (!$(this).val()) {
            const now = new Date();
            const time = now.getHours().toString().padStart(2, '0') + ':' + 
                        now.getMinutes().toString().padStart(2, '0');
            $(this).val(time);
            calculateHours();
            updatePreview();
        }
    });

    // Cambiar estado automáticamente según la hora de entrada
    $('#check_in').on('change', function() {
        const checkInTime = $(this).val();
        if (checkInTime && !$('#status').val()) {
            const workStartTime = '08:00'; // Hora de inicio de trabajo
            
            if (checkInTime > workStartTime) {
                $('#status').val('late');
            } else {
                $('#status').val('present');
            }
            updatePreview();
        }
    });

    // Validación en tiempo real
    $('form').on('submit', function(e) {
        const employee = $('#employee_id').val();
        const date = $('#date').val();
        const status = $('#status').val();

        if (!employee || !date || !status) {
            e.preventDefault();
            Swal.fire({
                title: 'Campos Requeridos',
                text: 'Por favor completa todos los campos obligatorios.',
                icon: 'warning',
                confirmButtonColor: '#002b5a'
            });
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
        } else {
            $('#hours_worked').val('');
        }
    } else {
        $('#hours_worked').val('');
    }
}

function updatePreview() {
    const employee = $('#employee_id option:selected').text();
    const date = $('#date').val();
    const checkIn = $('#check_in').val();
    const checkOut = $('#check_out').val();
    const status = $('#status option:selected').text();
    const hours = $('#hours_worked').val();
    const notes = $('#notes').val();

    if (employee && employee !== 'Seleccionar empleado...') {
        let preview = `
            <div class="row">
                <div class="col-md-4"><strong>Empleado:</strong> ${employee}</div>
                <div class="col-md-4"><strong>Fecha:</strong> ${date}</div>
                <div class="col-md-4"><strong>Estado:</strong> ${status}</div>
            </div>
        `;

        if (checkIn) {
            preview += `
                <div class="row mt-2">
                    <div class="col-md-3"><strong>Entrada:</strong> ${checkIn}</div>
                    <div class="col-md-3"><strong>Salida:</strong> ${checkOut || 'No registrada'}</div>
                    <div class="col-md-3"><strong>Horas:</strong> ${hours || 'Por calcular'}</div>
                </div>
            `;
        }

        if (notes) {
            preview += `
                <div class="row mt-2">
                    <div class="col-12"><strong>Observaciones:</strong> ${notes}</div>
                </div>
            `;
        }

        $('#preview-content').html(preview);
        $('#preview-section').show();
    } else {
        $('#preview-section').hide();
    }
}
</script>
@stop