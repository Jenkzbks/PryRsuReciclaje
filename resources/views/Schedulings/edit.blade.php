@extends('adminlte::page')

@section('title','Editar Programación')

@section('content_header')
  <h1>Editar Programación</h1>
@stop

@section('content')
<div class="card">
  <form method="POST" action="{{ route('admin.schedulings.update',$scheduling) }}">
    @csrf @method('PUT')
    <div class="card-body">
      @if($errors->any())
        <div class="alert alert-danger">
          <ul class="mb-0">
            @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
          </ul>
        </div>
      @endif

      <!-- Información General -->
      <div class="row mb-4">
        <div class="col-md-6">
          <div class="form-group">
            <label>Fecha de Programación</label>
            <input type="date" name="date" class="form-control" value="{{ $scheduling->date }}" required>
          </div>
        </div>
        <div class="col-md-6">
          <div class="form-group">
            <label>Notas</label>
            <input type="text" name="notes" class="form-control" value="{{ $scheduling->notes ?? '' }}" placeholder="Notas opcionales">
          </div>
        </div>
      </div>

      <!-- Información del Grupo (solo lectura) -->
      <div class="row mb-4">
        <div class="col-md-12">
          <div class="alert alert-info py-2">
            <div class="row">
              <div class="col-md-3">
                <strong>Grupo:</strong> {{ $scheduling->group->name ?? '-' }}
              </div>
              <div class="col-md-3">
                <strong>Zona:</strong> {{ $scheduling->group->zone->name ?? '-' }}
              </div>
              <div class="col-md-3">
                <strong>Días del grupo:</strong> {{ $scheduling->group->days ?? '-' }}
              </div>
              <div class="col-md-3">
                <strong>Configuración original</strong>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Carteles de Turno -->
      <div class="row mb-4">
        <div class="col-md-6">
          <div class="card border-primary">
            <div class="card-header bg-primary text-white">
              <strong>Turno Actual</strong>
            </div>
            <div class="card-body">
              <p class="mb-1">{{ $scheduling->shift->name ?? $scheduling->group->shift->name ?? 'Sin turno asignado' }}</p>
              <small class="text-muted">
                @if($scheduling->shift_id && $scheduling->shift_id != $scheduling->group->shift_id)
                  <i class="fas fa-exclamation-triangle text-warning"></i> Modificado respecto al grupo original
                @else
                  Turno del grupo original
                @endif
              </small>
            </div>
          </div>
        </div>
        <div class="col-md-6">
          <div class="card border-warning">
            <div class="card-header bg-warning text-dark">
              <strong>Nuevo Turno</strong>
            </div>
            <div class="card-body">
              <select name="shift_id" class="form-control form-control-sm">
                <option value="">-- Mantener turno actual --</option>
                @foreach($shifts as $shift)
                  <option value="{{ $shift->id }}" 
                    {{ $scheduling->shift_id == $shift->id ? 'selected' : (old('shift_id') == $shift->id ? 'selected' : '') }}>
                    {{ $shift->name }}
                  </option>
                @endforeach
              </select>
              <small class="text-muted">Seleccione para cambiar el turno</small>
            </div>
          </div>
        </div>
      </div>

      <!-- Carteles de Vehículo -->
      <div class="row mb-4">
        <div class="col-md-6">
          <div class="card border-primary">
            <div class="card-header bg-primary text-white">
              <strong>Vehículo Actual</strong>
            </div>
            <div class="card-body">
              <p class="mb-1">{{ $scheduling->vehicle->plate ?? $scheduling->group->vehicle->plate ?? 'Sin vehículo asignado' }}</p>
              <small class="text-muted">
                @if($scheduling->vehicle_id && $scheduling->vehicle_id != $scheduling->group->vehicle_id)
                  <i class="fas fa-exclamation-triangle text-warning"></i> Modificado respecto al grupo original
                @else
                  Vehículo del grupo original
                @endif
              </small>
            </div>
          </div>
        </div>
        <div class="col-md-6">
          <div class="card border-warning">
            <div class="card-header bg-warning text-dark">
              <strong>Nuevo Vehículo</strong>
            </div>
            <div class="card-body">
              <select name="vehicle_id" class="form-control form-control-sm">
                <option value="">-- Mantener vehículo actual --</option>
                @foreach($vehicles as $vehicle)
                  <option value="{{ $vehicle->id }}" 
                    {{ $scheduling->vehicle_id == $vehicle->id ? 'selected' : (old('vehicle_id') == $vehicle->id ? 'selected' : '') }}>
                    {{ $vehicle->plate }}
                  </option>
                @endforeach
              </select>
              <small class="text-muted">Seleccione para cambiar el vehículo</small>
            </div>
          </div>
        </div>
      </div>

      <!-- Carteles de Personal -->
      <div class="row mb-4">
        <div class="col-md-6">
          <div class="card border-primary">
            <div class="card-header bg-primary text-white">
              <strong>Personal Actual</strong>
            </div>
            <div class="card-body">
              @php
                $driverDetail = $scheduling->details->firstWhere('employee.type_id', 1);
                $assistantDetails = $scheduling->details->filter(fn($d) => optional($d->employee)->type_id == 2)->values();
              @endphp
              
              <div class="mb-2">
                <strong>Conductor:</strong><br>
                {{ $driverDetail->employee->lastnames ?? '-' }} {{ $driverDetail->employee->names ?? '-' }}
              </div>
              
              <div class="mb-2">
                <strong>Ayudante 1:</strong><br>
                {{ $assistantDetails->get(0)->employee->lastnames ?? '-' }} {{ $assistantDetails->get(0)->employee->names ?? '-' }}
              </div>
              
              <div class="mb-0">
                <strong>Ayudante 2:</strong><br>
                {{ $assistantDetails->get(1)->employee->lastnames ?? '-' }} {{ $assistantDetails->get(1)->employee->names ?? '-' }}
              </div>
            </div>
          </div>
        </div>
        <div class="col-md-6">
          <div class="card border-warning">
            <div class="card-header bg-warning text-dark">
              <strong>Nuevo Personal</strong>
            </div>
            <div class="card-body">
              <div class="form-group">
                <label class="small">Conductor</label>
                <select name="driver_id" class="form-control form-control-sm">
                  <option value="">-- Mantener conductor actual --</option>
                  @foreach($drivers as $driver)
                      @if(optional($driver)->type_id == 1)
                      <option value="{{ $driver->id }}" 
                        {{ $selectedDriverId == $driver->id ? 'selected' : (old('driver_id') == $driver->id ? 'selected' : '') }}>
                        {{ $driver->lastnames }} {{ $driver->names }}
                      </option>
                      @endif
                    @endforeach
                </select>
              </div>
              
              <div class="form-group">
                <label class="small">Ayudante 1</label>
                <select name="assistant1_id" class="form-control form-control-sm">
                  <option value="">-- Mantener ayudante 1 actual --</option>
                  @foreach($assistants as $assistant)
                    @if(optional($assistant)->type_id == 2)
                    <option value="{{ $assistant->id }}" 
                      {{ $selectedA1Id == $assistant->id ? 'selected' : (old('assistant1_id') == $assistant->id ? 'selected' : '') }}>
                      {{ $assistant->lastnames }} {{ $assistant->names }}
                    </option>
                    @endif
                  @endforeach
                </select>
              </div>
              
              <div class="form-group mb-0">
                <label class="small">Ayudante 2</label>
                <select name="assistant2_id" class="form-control form-control-sm">
                  <option value="">-- Mantener ayudante 2 actual --</option>
                  @foreach($assistants as $assistant)
                    @if(optional($assistant)->type_id == 2)
                    <option value="{{ $assistant->id }}" 
                      {{ $selectedA2Id == $assistant->id ? 'selected' : (old('assistant2_id') == $assistant->id ? 'selected' : '') }}>
                      {{ $assistant->lastnames }} {{ $assistant->names }}
                    </option>
                    @endif
                  @endforeach
                </select>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Cambios Registrados -->
      <div class="card border-info mb-4">
        <div class="card-header bg-info text-white">
          <strong>Resumen de Cambios</strong>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-sm table-bordered">
              <thead>
                <tr class="bg-light">
                  <th>Tipo de Cambio</th>
                  <th>Valor Original del Grupo</th>
                  <th>Valor Actual</th>
                  <th>Valor Nuevo</th>
                  <th>Estado</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>Turno</td>
                  <td>{{ $scheduling->group->shift->name ?? '-' }}</td>
                  <td>{{ $scheduling->shift->name ?? $scheduling->group->shift->name ?? '-' }}</td>
                  <td id="newShiftPreview">-</td>
                  <td>
                    @if($scheduling->shift_id && $scheduling->shift_id != $scheduling->group->shift_id)
                      <span class="badge badge-warning">Modificado</span>
                    @else
                      <span class="badge badge-secondary">Original</span>
                    @endif
                  </td>
                </tr>
                <tr>
                  <td>Vehículo</td>
                  <td>{{ $scheduling->group->vehicle->plate ?? '-' }}</td>
                  <td>{{ $scheduling->vehicle->plate ?? $scheduling->group->vehicle->plate ?? '-' }}</td>
                  <td id="newVehiclePreview">-</td>
                  <td>
                    @if($scheduling->vehicle_id && $scheduling->vehicle_id != $scheduling->group->vehicle_id)
                      <span class="badge badge-warning">Modificado</span>
                    @else
                      <span class="badge badge-secondary">Original</span>
                    @endif
                  </td>
                </tr>
                <tr>
                  <td>Personal</td>
                  <td>{{ $scheduling->group->employees->count() ?? 0 }} trabajadores</td>
                  <td>{{ $scheduling->details->count() }} trabajadores</td>
                  <td id="newPersonnelPreview">-</td>
                  <td>
                    @if($scheduling->details->count() > 0)
                      <span class="badge badge-info">Asignado</span>
                    @else
                      <span class="badge badge-secondary">Sin personal</span>
                    @endif
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>

    </div>
    <div class="card-footer text-right">
      <a href="{{ route('admin.schedulings.index') }}" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left"></i> Volver
      </a>
      <button type="submit" class="btn btn-success">
        <i class="fas fa-save"></i> Guardar Cambios
      </button>
    </div>
  </form>
</div>
@stop

@section('js')
<script>
  // Actualizar vista previa de cambios
  document.addEventListener('DOMContentLoaded', function() {
    // Vista previa para turno
    const shiftSelect = document.querySelector('select[name="shift_id"]');
    const newShiftPreview = document.getElementById('newShiftPreview');
    
    shiftSelect.addEventListener('change', function() {
      if (this.value) {
        newShiftPreview.textContent = this.options[this.selectedIndex].text;
        newShiftPreview.className = 'text-warning font-weight-bold';
      } else {
        newShiftPreview.textContent = '-';
        newShiftPreview.className = '';
      }
    });

    // Vista previa para vehículo
    const vehicleSelect = document.querySelector('select[name="vehicle_id"]');
    const newVehiclePreview = document.getElementById('newVehiclePreview');
    
    vehicleSelect.addEventListener('change', function() {
      if (this.value) {
        newVehiclePreview.textContent = this.options[this.selectedIndex].text;
        newVehiclePreview.className = 'text-warning font-weight-bold';
      } else {
        newVehiclePreview.textContent = '-';
        newVehiclePreview.className = '';
      }
    });

    // Vista previa para personal
    const driverSelect = document.querySelector('select[name="driver_id"]');
    const assistant1Select = document.querySelector('select[name="assistant1_id"]');
    const assistant2Select = document.querySelector('select[name="assistant2_id"]');
    const newPersonnelPreview = document.getElementById('newPersonnelPreview');

    function updatePersonnelPreview() {
      const changes = [];
      
      if (driverSelect.value) {
        changes.push('Nuevo conductor');
      }
      if (assistant1Select.value) {
        changes.push('Nuevo ayudante 1');
      }
      if (assistant2Select.value) {
        changes.push('Nuevo ayudante 2');
      }
      
      if (changes.length > 0) {
        newPersonnelPreview.textContent = changes.join(', ');
        newPersonnelPreview.className = 'text-warning font-weight-bold';
      } else {
        newPersonnelPreview.textContent = '-';
        newPersonnelPreview.className = '';
      }
    }

    driverSelect.addEventListener('change', updatePersonnelPreview);
    assistant1Select.addEventListener('change', updatePersonnelPreview);
    assistant2Select.addEventListener('change', updatePersonnelPreview);

    // Inicializar vistas previas con valores actuales
    if (shiftSelect.value) {
      shiftSelect.dispatchEvent(new Event('change'));
    }
    if (vehicleSelect.value) {
      vehicleSelect.dispatchEvent(new Event('change'));
    }
    updatePersonnelPreview();
  });
</script>
@stop