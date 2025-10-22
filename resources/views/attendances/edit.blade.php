@extends('adminlte::page')

@section('title', 'Editar Asistencia')

@section('content_header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1>Editar Asistencia</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('admin.personnel.attendances.index') }}">Asistencias</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.personnel.attendances.show', $attendance) }}">Detalles</a></li>
                <li class="breadcrumb-item active">Editar</li>
            </ol>
        </div>
    </div>
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-edit"></i> 
                        Editar Asistencia - {{ $attendance->employee->names }} {{ $attendance->employee->lastnames }}
                    </h3>
                    <div class="card-tools">
                        @php
                            $statusColors = [
                                'present' => 'success',
                                'absent' => 'danger',
                                'late' => 'warning',
                                'on_time' => 'success',
                                'half_day' => 'info'
                            ];
                            $statusLabels = [
                                'present' => 'Presente',
                                'absent' => 'Ausente',
                                'late' => 'Tarde',
                                'on_time' => 'A Tiempo',
                                'half_day' => 'Medio Día'
                            ];
                        @endphp
                        <span class="badge badge-{{ $statusColors[$attendance->status] ?? 'secondary' }} badge-lg">
                            Estado: {{ $statusLabels[$attendance->status] ?? ucfirst($attendance->status) }}
                        </span>
                    </div>
                </div>
                <form action="{{ route('admin.personnel.attendances.update', $attendance) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="employee_id">Empleado <span class="text-danger">*</span></label>
                                    <select name="employee_id" id="employee_id" class="form-control @error('employee_id') is-invalid @enderror" required>
                                        <option value="">Seleccionar empleado...</option>
                                        @foreach($employees as $employee)
                                            <option value="{{ $employee->id }}" 
                                                {{ (old('employee_id', $attendance->employee_id) == $employee->id) ? 'selected' : '' }}>
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
                                           value="{{ old('date', $attendance->date) }}" required>
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
                                           value="{{ old('check_in', $attendance->check_in ? \Carbon\Carbon::parse($attendance->check_in)->format('H:i') : '') }}">
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
                                           value="{{ old('check_out', $attendance->check_out ? \Carbon\Carbon::parse($attendance->check_out)->format('H:i') : '') }}">
                                    @error('check_out')
                                        <span class="invalid-feedback" role="alert">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="hours_worked">Horas Trabajadas</label>
                                    <input type="number" name="hours_worked" id="hours_worked" 
                                           class="form-control @error('hours_worked') is-invalid @enderror" 
                                           value="{{ old('hours_worked', $attendance->hours_worked) }}" 
                                           step="0.1" min="0" max="24" readonly>
                                    @error('hours_worked')
                                        <span class="invalid-feedback" role="alert">{{ $message }}</span>
                                    @enderror
                                    <small class="form-text text-muted">Se calcula automáticamente basado en las horas de entrada y salida</small>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="status">Estado <span class="text-danger">*</span></label>
                                    <select name="status" id="status" class="form-control @error('status') is-invalid @enderror" required>
                                        <option value="">Seleccionar estado...</option>
                                        <option value="present" {{ old('status', $attendance->status) == 'present' ? 'selected' : '' }}>Presente</option>
                                        <option value="absent" {{ old('status', $attendance->status) == 'absent' ? 'selected' : '' }}>Ausente</option>
                                        <option value="late" {{ old('status', $attendance->status) == 'late' ? 'selected' : '' }}>Tarde</option>
                                        <option value="on_time" {{ old('status', $attendance->status) == 'on_time' ? 'selected' : '' }}>A Tiempo</option>
                                        <option value="half_day" {{ old('status', $attendance->status) == 'half_day' ? 'selected' : '' }}>Medio Día</option>
                                    </select>
                                    @error('status')
                                        <span class="invalid-feedback" role="alert">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="notes">Observaciones</label>
                                    <textarea name="notes" id="notes" rows="3" 
                                              class="form-control @error('notes') is-invalid @enderror" 
                                              placeholder="Observaciones adicionales sobre la asistencia...">{{ old('notes', $attendance->notes) }}</textarea>
                                    @error('notes')
                                        <span class="invalid-feedback" role="alert">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Preview del empleado seleccionado -->
                        <div class="row" id="employee-preview">
                            <div class="col-12">
                                <div class="alert alert-info">
                                    <h6><i class="fas fa-info-circle"></i> Información del Empleado</h6>
                                    <div class="row">
                                        <div class="col-md-2 text-center">
                                            @if($attendance->employee->photo)
                                                <img src="{{ asset('storage/' . $attendance->employee->photo) }}" 
                                                     alt="Foto del empleado" 
                                                     class="img-circle elevation-2" 
                                                     style="width: 60px; height: 60px; object-fit: cover;">
                                            @else
                                                <div class="img-circle elevation-2 bg-gray d-flex align-items-center justify-content-center" 
                                                     style="width: 60px; height: 60px;">
                                                    <i class="fas fa-user text-white"></i>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="col-md-10">
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <small><strong>Nombre:</strong> {{ $attendance->employee->names }} {{ $attendance->employee->lastnames }}</small>
                                                </div>
                                                <div class="col-md-4">
                                                    <small><strong>Código:</strong> {{ $attendance->employee->employee_code }}</small>
                                                </div>
                                                <div class="col-md-4">
                                                    <small><strong>Departamento:</strong> {{ $attendance->employee->department->name ?? 'Sin departamento' }}</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Información de auditoría -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="alert alert-secondary">
                                    <h6><i class="fas fa-info-circle"></i> Información de Auditoría</h6>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <small><strong>Creado:</strong> {{ $attendance->created_at->format('d/m/Y H:i:s') }}</small>
                                        </div>
                                        <div class="col-md-6">
                                            <small><strong>Última modificación:</strong> {{ $attendance->updated_at->format('d/m/Y H:i:s') }}</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Actualizar Asistencia
                        </button>
                        <a href="{{ route('admin.personnel.attendances.show', $attendance) }}" class="btn btn-info">
                            <i class="fas fa-eye"></i> Ver Detalles
                        </a>
                        <a href="{{ route('admin.personnel.attendances.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Cancelar
                        </a>
                        
                        <div class="float-right">
                            <button type="button" class="btn btn-info" onclick="setCurrentTime('in')">
                                <i class="fas fa-clock"></i> Hora Actual (Entrada)
                            </button>
                            <button type="button" class="btn btn-warning" onclick="setCurrentTime('out')">
                                <i class="fas fa-clock"></i> Hora Actual (Salida)
                            </button>
                            <button type="button" class="btn btn-danger" onclick="confirmDelete()">
                                <i class="fas fa-trash"></i> Eliminar
                            </button>
                        </div>
                    </div>
                </form>

                <!-- Formulario de eliminación (oculto) -->
                <form id="delete-form" action="{{ route('admin.personnel.attendances.destroy', $attendance) }}" method="POST" style="display: none;">
                    @csrf
                    @method('DELETE')
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
            background: linear-gradient(90deg, #ffc107, #e0a800);
            color: black;
        }
        .badge-lg {
            font-size: 0.9rem;
            padding: 0.4rem 0.8rem;
        }
        .img-circle {
            border: 2px solid #fff;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .alert-info {
            background-color: #d1ecf1;
            border-color: #bee5eb;
        }
        .alert-secondary {
            background-color: #f8f9fa;
            border-color: #dee2e6;
        }
    </style>
@stop

@section('js')
    <script>
        $(document).ready(function() {
            // Calcular horas trabajadas automáticamente
            $('#check_in, #check_out').on('change', function() {
                calculateHours();
            });

            function calculateHours() {
                const checkIn = $('#check_in').val();
                const checkOut = $('#check_out').val();
                
                if (checkIn && checkOut) {
                    const start = new Date('2000-01-01 ' + checkIn);
                    const end = new Date('2000-01-01 ' + checkOut);
                    
                    if (end > start) {
                        const diffMs = end - start;
                        const diffHours = diffMs / (1000 * 60 * 60);
                        $('#hours_worked').val(diffHours.toFixed(2));
                    } else {
                        $('#hours_worked').val('');
                        if (checkOut !== '') {
                            alert('La hora de salida debe ser posterior a la hora de entrada');
                        }
                    }
                }
            }

            // Determinar estado automáticamente basado en la hora de entrada
            $('#check_in').on('change', function() {
                const checkIn = $(this).val();
                if (checkIn) {
                    const [hours, minutes] = checkIn.split(':').map(Number);
                    const checkInTime = hours * 60 + minutes;
                    const workStartTime = 8 * 60; // 8:00 AM en minutos
                    const lateThreshold = 8 * 60 + 15; // 8:15 AM en minutos
                    
                    if (checkInTime <= workStartTime) {
                        $('#status').val('on_time');
                    } else if (checkInTime <= lateThreshold) {
                        $('#status').val('late');
                    } else {
                        $('#status').val('present');
                    }
                }
            });

            // Actualizar estado basado en ausencia
            $('#status').on('change', function() {
                const status = $(this).val();
                if (status === 'absent') {
                    $('#check_in').val('');
                    $('#check_out').val('');
                    $('#hours_worked').val('0');
                }
            });

            // Calcular horas al cargar la página
            calculateHours();
        });

        function setCurrentTime(type) {
            const now = new Date();
            const timeString = now.getHours().toString().padStart(2, '0') + ':' + 
                             now.getMinutes().toString().padStart(2, '0');
            
            if (type === 'in') {
                $('#check_in').val(timeString).trigger('change');
            } else {
                $('#check_out').val(timeString).trigger('change');
            }
        }

        function confirmDelete() {
            if (confirm('¿Está seguro de que desea eliminar este registro de asistencia? Esta acción no se puede deshacer.')) {
                document.getElementById('delete-form').submit();
            }
        }
    </script>
@stop