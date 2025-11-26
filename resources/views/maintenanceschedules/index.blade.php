@extends('adminlte::page')

@section('title', 'Horarios de mantenimiento')

@section('content_header')
<div class="d-flex justify-content-between align-items-center">
  <div>
    <h1>Horarios de: {{ $maintenance->name }}</h1>
    <small class="text-muted">Rango: {{ $maintenance->range_text }}</small>
  </div>
  <div>
    <a href="{{ route('admin.maintenances.index') }}" class="btn btn-outline-secondary mr-2">
      <i class="fas fa-arrow-left"></i> Volver a mantenimientos
    </a>
    <a href="{{ route('admin.maintenances.schedules.create', $maintenance) }}" class="btn btn-primary">
      <i class="fas fa-plus"></i> Nuevo horario
    </a>
  </div>
</div>
@stop

@section('content')
{{-- =================== HORARIOS =================== --}}
<div class="card mb-3">
  <div class="card-header">
    <strong>Horarios registrados</strong>
  </div>
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-striped mb-0">
        <thead class="thead-light">
          <tr>
            <th>Día semana</th>
            <th>Vehículo</th>
            <th>Responsable</th>
            <th>Hora INICIO</th>
            <th>Hora FIN</th>
            <th>Tipo mantenimiento</th>
           
            <th width="130">Acciones</th>
          </tr>
        </thead>
        <tbody>
          @forelse($schedules as $s)
            <tr>
              <td>{{ $s->day_of_week }}</td>
              <td>{{ $s->vehicle->plate ?? '-' }}</td>
              <td>{{ optional($s->driver)->lastnames }} {{ optional($s->driver)->names }}</td>
              <td>{{ $s->start_time }}</td>
              <td>{{ $s->end_time }}</td>
              <td>{{ $s->maintenance_type }}</td>
              
              <td class="d-flex">
                <a href="{{ route('admin.maintenances.schedules.edit', [$maintenance, $s]) }}"
                   class="btn btn-sm btn-outline-primary mr-2">
                  <i class="fas fa-edit"></i>
                </a>

                {{-- BOTÓN QUE ABRE MODAL DE ELIMINAR HORARIO --}}
                <button type="button"
                        class="btn btn-sm btn-outline-danger btn-delete-schedule"
                        data-action="{{ route('admin.maintenances.schedules.destroy', [$maintenance, $s]) }}"
                        data-info="Vehículo {{ $s->vehicle->plate ?? '-' }}, {{ $s->day_of_week }} {{ $s->start_time }} - {{ $s->end_time }}">
                  <i class="fas fa-trash"></i>
                </button>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="7" class="text-center text-muted">No hay horarios registrados</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>

{{-- =================== REGISTROS =================== --}}
<div class="card">
  <div class="card-header">
    <strong>Fechas generadas (registros)</strong>
  </div>
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-sm table-striped mb-0">
        <thead class="thead-light">
          <tr>
            <th>Fecha</th>
            <th>Descripción</th>
            <th>estado</th>
            <th>Imagen</th>
            <th width="140">Acciones</th>
          </tr>
        </thead>
        <tbody>
          @forelse($records as $r)
            <tr>
              <td>{{ $r->maintenance_date->format('Y-m-d') }}</td>
              <td>{{ $r->descripcion ?: '-' }}</td>
              <td>
        @php
          $estado = $r->estado ?? 'no realizado';
        @endphp

        @if($estado === 'realizado')
          <span class="badge badge-success">Realizado</span>
        @else
          <span class="badge badge-secondary">No realizado</span>
        @endif
      </td>
              <td>
                @if($r->image_url)
                  <img src="{{ asset('storage/'.$r->image_url) }}"
                       alt="Imagen mantenimiento"
                       style="max-width: 80px; max-height: 80px; object-fit: cover;">
                @else
                  <span class="text-muted">Sin imagen</span>
                @endif
              </td>
              <td class="d-flex">
                <a href="{{ route('admin.maintenances.records.edit', [$maintenance, $r]) }}"
                   class="btn btn-sm btn-outline-primary mr-2"
                   title="Editar">
                  <i class="fas fa-edit"></i>
                </a>

                {{-- BOTÓN QUE ABRE MODAL DE ELIMINAR REGISTRO --}}
                <button type="button"
                        class="btn btn-sm btn-outline-danger btn-delete-record"
                        title="Eliminar"
                        data-action="{{ route('admin.maintenances.records.destroy', [$maintenance, $r]) }}"
                        data-date="{{ $r->maintenance_date->format('Y-m-d') }}">
                  <i class="fas fa-trash"></i>
                </button>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="4" class="text-center text-muted">No hay registros generados</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>

{{-- ========== MODAL ELIMINAR HORARIO ==========
     (elimina horario y TODOS sus registros) --}}
<div class="modal fade" id="confirmDeleteScheduleModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header bg-danger text-white">
        <h5 class="modal-title">Eliminar horario</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <form method="POST" id="deleteScheduleForm">
        @csrf
        @method('DELETE')
        <div class="modal-body">
          <p>
            ¿Seguro que deseas eliminar este horario?
          </p>
          <p class="mb-1">
            <strong id="deleteScheduleInfo"></strong>
          </p>
          <p class="mb-0 text-muted">
            También se eliminarán todos los registros generados por este horario.
          </p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">
            Cancelar
          </button>
          <button type="submit" class="btn btn-danger">
            Eliminar
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

{{-- ========== MODAL ELIMINAR REGISTRO (FECHA) ========== --}}
<div class="modal fade" id="confirmDeleteRecordModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header bg-danger text-white">
        <h5 class="modal-title">Eliminar registro</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <form method="POST" id="deleteRecordForm">
        @csrf
        @method('DELETE')
        <div class="modal-body">
          <p>
            ¿Seguro que deseas eliminar el registro de mantenimiento del
            <strong id="deleteRecordDate"></strong>?
          </p>
          <p class="mb-0 text-muted">Esta acción no se puede deshacer.</p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">
            Cancelar
          </button>
          <button type="submit" class="btn btn-danger">
            Eliminar
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
@stop

@section('js')
<script>
  $(function () {
    // ----- eliminar HORARIO -----
    $('.btn-delete-schedule').on('click', function () {
      const action = $(this).data('action');
      const info   = $(this).data('info');

      $('#deleteScheduleForm').attr('action', action);
      $('#deleteScheduleInfo').text(info);

      $('#confirmDeleteScheduleModal').modal('show');
    });

    // ----- eliminar REGISTRO (fecha) -----
    $('.btn-delete-record').on('click', function () {
      const action = $(this).data('action');
      const date   = $(this).data('date');

      $('#deleteRecordForm').attr('action', action);
      $('#deleteRecordDate').text(date);

      $('#confirmDeleteRecordModal').modal('show');
    });
  });
</script>
@stop
