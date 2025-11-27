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
        <div class="col-md-3 mb-2">
            <div class="d-flex align-items-center bg-light rounded border p-2 h-100">
              <span class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center mr-2" style="width:32px;height:32px;"><i class="fas fa-users"></i></span>
              <div>
                <small class="text-muted">Nombre</small><br>
                <span class="fw-bold">{{ $scheduling->group->name ?? '-' }}</span>
              </div>
            </div>
        </div>
        <div class="col-md-3 mb-2">
            <div class="d-flex align-items-center bg-light rounded border p-2 h-100">
              <span class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center mr-2" style="width:32px;height:32px;"><i class="fas fa-map-marked-alt"></i></span>
              <div>
                <small class="text-muted">Zona</small><br>
                <span class="fw-bold">{{ $scheduling->group->zone->name ?? '-' }}</span>
              </div>
            </div>
        </div>
        <div class="col-md-3 mb-2">
            <div class="d-flex align-items-center bg-light rounded border p-2 h-100">
              <span class="bg-warning text-white rounded-circle d-flex align-items-center justify-content-center mr-2" style="width:32px;height:32px;"><i class="fas fa-calendar-alt"></i></span>
              <div>
                <small class="text-muted">Días del grupo</small><br>
                <span class="fw-bold">{{ $scheduling->group->days ?? '-' }}</span>
              </div>
            </div>
        </div>
        <div class="col-md-3 mb-2">
            <div class="d-flex align-items-center bg-light rounded border p-2 h-100">
              <span class="bg-info text-white rounded-circle d-flex align-items-center justify-content-center mr-2" style="width:32px;height:32px;"><i class="fas fa-cogs"></i></span>
              <div>
                <small class="text-muted">Configuración original</small><br>
                <span class="fw-bold">&nbsp;</span>
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

      <!-- Cambios Registrados (solo nuevos cambios de esta edición) -->
      <div id="changeSummaryCard" class="card border-info mb-4" style="display:none;">
        <div class="card-header bg-info text-white">
          <strong>Resumen de Cambios (solo los que realices ahora)</strong>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-sm table-bordered" id="changeSummaryTable">
              <thead>
                <tr class="bg-light">
                  <th>Tipo de Cambio</th>
                  <th>Valor Original del Grupo</th>
                  <th>Valor Actual</th>
                  <th>Valor Nuevo</th>
                  <th>Motivo del Cambio <span class="text-danger">*</span></th>
                  <th>Notas</th>
                  <th>Acción</th>
                </tr>
              </thead>
              <tbody>
                <!-- Filas generadas por JS (solo cambios nuevos) -->
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
  
  // Motivos desde backend (pasar como $reasons)
  const reasons = @json($reasons ?? []);

  document.addEventListener('DOMContentLoaded', function() {
    // Selectores
    const shiftSelect = document.querySelector('select[name="shift_id"]');
    const vehicleSelect = document.querySelector('select[name="vehicle_id"]');
    const driverSelect = document.querySelector('select[name="driver_id"]');
    const assistant1Select = document.querySelector('select[name="assistant1_id"]');
    const assistant2Select = document.querySelector('select[name="assistant2_id"]');
    const changeSummaryCard = document.getElementById('changeSummaryCard');
    const changeSummaryTable = document.getElementById('changeSummaryTable').querySelector('tbody');

    // Valores originales
    const original = {
      shift_id: '{{ $scheduling->group->shift_id ?? '' }}',
      vehicle_id: '{{ $scheduling->group->vehicle_id ?? '' }}',
      driver_id: '{{ $selectedDriverId ?? '' }}',
      assistant1_id: '{{ $selectedA1Id ?? '' }}',
      assistant2_id: '{{ $selectedA2Id ?? '' }}',
    };

    // Nombres originales
    const originalNames = {
      shift: `{{ $scheduling->group->shift->name ?? '-' }}`,
      vehicle: `{{ $scheduling->group->vehicle->plate ?? '-' }}`,
      driver: `{{ $drivers->firstWhere('id', $selectedDriverId)->lastnames ?? '-' }} {{ $drivers->firstWhere('id', $selectedDriverId)->names ?? '' }}`,
      assistant1: `{{ $assistants->firstWhere('id', $selectedA1Id)->lastnames ?? '-' }} {{ $assistants->firstWhere('id', $selectedA1Id)->names ?? '' }}`,
      assistant2: `{{ $assistants->firstWhere('id', $selectedA2Id)->lastnames ?? '-' }} {{ $assistants->firstWhere('id', $selectedA2Id)->names ?? '' }}`
    };

    
    // Helper para crear select de motivo
    function motivoSelectHtml(tipo) {
      let html = `<select name="motivos[${tipo}]" class="form-control form-control-sm motivo-select" required><option value="">Seleccione motivo</option>`;
      for (const r of reasons) {
        html += `<option value="${r.id}">${r.name}</option>`;
      }
      html += '</select>';
      return html;
    }

    // Helper para crear textarea de notas
    function notasTextareaHtml(tipo) {
      return `<textarea name="notas[${tipo}]" class="form-control form-control-sm" rows="1" placeholder="Notas del cambio..."></textarea>`;
    }

    // Helper para crear botón eliminar
    function eliminarBtnHtml(tipo) {
      return `<button type="button" class="btn btn-danger btn-sm eliminar-cambio" data-tipo="${tipo}"><i class="fas fa-times"></i></button>`;
    }


    // Detectar cambios y renderizar tabla (mostrar cualquier acción, incluso si vuelve al original)
    function renderChangeSummary() {
      let rows = '';
      let hasChange = false;

      // Turno
      if (shiftSelect.value && shiftSelect.value !== '{{ $scheduling->shift_id ?? '' }}') {
        hasChange = true;
        rows += `<tr data-tipo="turno">
          <td>Turno</td>
          <td>{{ $scheduling->shift->name ?? $scheduling->group->shift->name ?? '-' }}</td>
          <td>{{ $scheduling->shift->name ?? $scheduling->group->shift->name ?? '-' }}</td>
          <td>${shiftSelect.options[shiftSelect.selectedIndex].text}</td>
          <td>${motivoSelectHtml('turno')}</td>
          <td>${notasTextareaHtml('turno')}</td>
          <td>${eliminarBtnHtml('turno')}</td>
        </tr>`;
      }
      // Vehículo
      if (vehicleSelect.value && vehicleSelect.value !== '{{ $scheduling->vehicle_id ?? '' }}') {
        hasChange = true;
        rows += `<tr data-tipo="vehiculo">
          <td>Vehículo</td>
          <td>{{ $scheduling->vehicle->plate ?? $scheduling->group->vehicle->plate ?? '-' }}</td>
          <td>{{ $scheduling->vehicle->plate ?? $scheduling->group->vehicle->plate ?? '-' }}</td>
          <td>${vehicleSelect.options[vehicleSelect.selectedIndex].text}</td>
          <td>${motivoSelectHtml('vehiculo')}</td>
          <td>${notasTextareaHtml('vehiculo')}</td>
          <td>${eliminarBtnHtml('vehiculo')}</td>
        </tr>`;
      }
      // Personal (cualquier cambio en conductor o ayudantes)
      let personalChanged = false;
      if (
        (driverSelect.value && driverSelect.value !== '{{ $selectedDriverId ?? '' }}') ||
        (assistant1Select.value && assistant1Select.value !== '{{ $selectedA1Id ?? '' }}') ||
        (assistant2Select.value && assistant2Select.value !== '{{ $selectedA2Id ?? '' }}')
      ) {
        personalChanged = true;
        hasChange = true;
      }
      if (personalChanged) {
        let nuevoPersonal = [];
        if (driverSelect.value) {
          const d = driverSelect.options[driverSelect.selectedIndex].text;
          nuevoPersonal.push('Conductor: ' + d);
        }
        if (assistant1Select.value) {
          const a1 = assistant1Select.options[assistant1Select.selectedIndex].text;
          nuevoPersonal.push('Ayudante 1: ' + a1);
        }
        if (assistant2Select.value) {
          const a2 = assistant2Select.options[assistant2Select.selectedIndex].text;
          nuevoPersonal.push('Ayudante 2: ' + a2);
        }
        rows += `<tr data-tipo="personal">
          <td>Personal</td>
          <td>-</td>
          <td>-</td>
          <td>${nuevoPersonal.length ? nuevoPersonal.join('<br>') : '-'}</td>
          <td>${motivoSelectHtml('personal')}</td>
          <td>${notasTextareaHtml('personal')}</td>
          <td>${eliminarBtnHtml('personal')}</td>
        </tr>`;
      }

      changeSummaryTable.innerHTML = rows;
      changeSummaryCard.style.display = hasChange ? '' : 'none';

      // Asignar eventos a los botones eliminar
      document.querySelectorAll('.eliminar-cambio').forEach(btn => {
        btn.addEventListener('click', function() {
          const tipo = this.getAttribute('data-tipo');
          if (tipo === 'turno') {
            shiftSelect.value = '{{ $scheduling->shift_id ?? '' }}';
            shiftSelect.dispatchEvent(new Event('change'));
          } else if (tipo === 'vehiculo') {
            vehicleSelect.value = '{{ $scheduling->vehicle_id ?? '' }}';
            vehicleSelect.dispatchEvent(new Event('change'));
          } else if (tipo === 'personal') {
            driverSelect.value = '{{ $selectedDriverId ?? '' }}';
            assistant1Select.value = '{{ $selectedA1Id ?? '' }}';
            assistant2Select.value = '{{ $selectedA2Id ?? '' }}';
            driverSelect.dispatchEvent(new Event('change'));
            assistant1Select.dispatchEvent(new Event('change'));
            assistant2Select.dispatchEvent(new Event('change'));
          }
        });
      });
    }

    // Listeners
    shiftSelect.addEventListener('change', renderChangeSummary);
    vehicleSelect.addEventListener('change', renderChangeSummary);
    driverSelect.addEventListener('change', renderChangeSummary);
    assistant1Select.addEventListener('change', renderChangeSummary);
    assistant2Select.addEventListener('change', renderChangeSummary);

    // Inicializar
    renderChangeSummary();
  });
</script>
@stop