@extends('adminlte::page')

@section('title', 'Programaciones')

@section('content_header')
<div class="d-flex align-items-center">
  <h1 class="mb-0">Programaciones</h1>

  <div class="ml-auto d-flex align-items-center">
    <button type="button" id="btnOpenMassive" class="btn btn-secondary btn-sm">
      <i class="fas fa-plus"></i> Cambio Masivo
    </button>
    <a href="{{ route('admin.schedulings.create-masive') }}" class="btn btn-primary btn-sm ml-2">
      <i class="fas fa-plus"></i> Nueva Programación Masiva
    </a>
    <a href="{{ route('admin.schedulings.create') }}" class="btn btn-primary btn-sm ml-2">
      <i class="fas fa-plus"></i> Nueva Programación
    </a>
  </div>

</div>
@stop

@section('content')
<div class="card">
  <div class="card-body">
    <form method="GET" class="mb-3">
      <div class="form-row">
        <div class="col-md-3">
          <label>Fecha inicio</label>
          <input type="date" name="from" class="form-control" value="{{ $from }}">
        </div>
        <div class="col-md-3">
          <label>Fecha fin</label>
          <input type="date" name="to" class="form-control" value="{{ $to }}">
        </div>
        <div class="col-md-3 d-flex align-items-end">
          <button class="btn btn-outline-secondary"><i class="fas fa-filter"></i> Filtrar</button>
        </div>
      </div>
    </form>

    @if(session('success'))
      <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="table-responsive">
      <table class="table table-striped">
        <thead class="thead-light">
          <tr>
            <th>Fecha</th>
            <th>Zona</th>
            <th>Turno</th>
            <th>Vehículo</th>
            <th>Grupo</th>
            <th>Notas</th>
            <th width="10"></th>
          </tr>
        </thead>
        <tbody>
          @forelse($schedulings as $s)
            <tr>
              <td>{{ $s->date }}</td>
              <td>{{ $s->zone->name ?? '-' }}</td>
              <td>{{ $s->shift->name ?? '-' }}</td>
              <td>{{ $s->vehicle->plate ?? '-' }}</td>
              <td>{{ $s->group->name ?? '-' }}</td>
              <td>{{ $s->notes }}</td>
              <td class="d-flex">
                <a href="{{ route('admin.schedulings.edit',$s) }}" class="btn btn-sm btn-outline-primary mr-2">
                  <i class="fas fa-edit"></i>
                </a>
                <button type="button" class="btn btn-sm btn-info mr-2 btnDetalle" data-id="{{ $s->id }}" title="Ver detalle">
                  <i class="fas fa-users"></i>
                </button>
                <form method="POST" action="{{ route('admin.schedulings.destroy',$s) }}" onsubmit="return confirm('¿Eliminar programación?')">
                  @csrf @method('DELETE')
                  <button class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                </form>
              </td>
            </tr>
          @empty
            <tr><td colspan="8" class="text-center text-muted">Sin resultados</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <div class="mt-3">{{ $schedulings->appends(request()->query())->links() }}</div>
  </div>
</div>
    <!-- Shared modal wrapper (used for Editar / Cambio Masivo) -->
    {{-- MODAL PARA EDITAR PROGRAMACIÓN --}}
    <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="editModalLabel">Cambio Masivo</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body" id="editModalBody">
            <!-- Contenido cargado dinámicamente -->
          </div>
        </div>
      </div>
    </div>
@stop

@section('js')
<script>
document.addEventListener('DOMContentLoaded', function(){
  const btn = document.getElementById('btnOpenMassive');
  const modalBody = document.getElementById('editModalBody');
  if (!btn || !modalBody) return;

  btn.addEventListener('click', async function () {
    // Prevent duplicate load
    if (modalBody.dataset.loaded === '1') {
      try { $('#editModal').modal('show'); } catch(e) { console.warn(e); }
      return;
    }

    try {
      const res = await fetch("{{ route('admin.schedulings.edit-massive') }}", { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
      const text = await res.text();
      // Inject the returned fragment into the shared modal body and show it
      modalBody.innerHTML = text;
      modalBody.dataset.loaded = '1';

      try { $('#editModal').modal('show'); } catch(e) { console.warn(e); }
    } catch (err) {
      console.error(err);
      alert('Error cargando contenido. Revisa la consola.');
    }
  });
});
</script>
@stop

@push('js')
<script>
$(function() {
  $('.btnDetalle').click(function() {
    var id = $(this).data('id');
    $('#detalleContenido').html('<div class="text-center p-5"><i class="fas fa-spinner fa-spin fa-2x"></i> Cargando...</div>');
    $('#modalDetalle').modal('show');
    $.get("{{ url('admin/schedulings') }}/" + id + "/detalle", function(data) {
      $('#detalleContenido').html(data);
    });
  });
});
</script>
@endpush
