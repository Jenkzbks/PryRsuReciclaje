@extends('adminlte::page')

@section('title','Nueva Programación')

@section('content_header')
  <h1>Registrar programación</h1>
@stop

@section('content')
<div class="card">
  <form method="POST" action="{{ route('admin.schedulings.store') }}">
    @csrf
    <div class="card-body">
      @if($errors->any())
        <div class="alert alert-danger">
          <ul class="mb-0">
            @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
          </ul>
        </div>
      @endif

      <div class="form-row">
        <div class="form-group col-md-4">
          <label>Fecha de inicio *</label>
          <input type="date" name="from" class="form-control" required>
        </div>
        <div class="form-group col-md-4">
          <label>Fecha de fin *</label>
          <input type="date" name="to" class="form-control" required>
        </div>
        <div class="form-group col-md-4 d-flex align-items-end">
          <button type="button" id="btnPreview" class="btn btn-outline-info">Validar disponibilidad</button>
        </div>
      </div>

      <div class="form-row">
        <div class="form-group col-md-6">
          <label>Grupo de Personal *</label>
          <select name="group_id" id="group_id" class="form-control" required>
            <option value="">-- Seleccione --</option>
            @foreach($groups as $g)
              <option value="{{ $g->id }}"
                data-zone="{{ $g->zone->name ?? '' }}"
                data-shift="{{ $g->shift->name ?? '' }}"
                data-vehicle="{{ $g->vehicle->plate ?? '' }}"
                data-days="{{ $g->days }}">
                {{ $g->name }}
              </option>
            @endforeach
          </select>
        </div>
        <div class="form-group col-md-6">
          <label>Notas</label>
          <input type="text" name="notes" class="form-control" maxlength="120" placeholder="Opcional">
        </div>
      </div>

      <div class="row">
        <div class="col-md-4">
          <div class="alert alert-light border">
            <div><strong>Zona:</strong> <span id="infoZone">-</span></div>
            <div><strong>Turno:</strong> <span id="infoShift">-</span></div>
            <div><strong>Vehículo:</strong> <span id="infoVehicle">-</span></div>
            <div><strong>Días del grupo:</strong> <span id="infoDays">-</span></div>
          </div>
        </div>
        <div class="col-md-8">
          <div class="alert alert-info" id="previewBox" style="display:none;"></div>
        </div>
      </div>
    </div>

    <div class="card-footer text-right">
      <a href="{{ route('admin.schedulings.index') }}" class="btn btn-outline-secondary">Volver</a>
      <button type="submit" class="btn btn-success">Guardar Programación</button>
    </div>
  </form>
</div>
@stop

@section('js')
<script>
  function updateGroupInfo() {
    const opt = document.querySelector('#group_id option:checked');
    if (!opt) return;
    document.getElementById('infoZone').textContent    = opt.dataset.zone || '-';
    document.getElementById('infoShift').textContent   = opt.dataset.shift || '-';
    document.getElementById('infoVehicle').textContent = opt.dataset.vehicle || '-';
    document.getElementById('infoDays').textContent    = opt.dataset.days || '-';
  }
  document.getElementById('group_id').addEventListener('change', updateGroupInfo);
  updateGroupInfo();

  // preview simple: cuenta cuántos días coinciden
  document.getElementById('btnPreview').addEventListener('click', function() {
    const from = document.querySelector('input[name="from"]').value;
    const to   = document.querySelector('input[name="to"]').value;
    const opt  = document.querySelector('#group_id option:checked');
    const days = (opt.dataset.days || '').split(',').map(s => s.trim().toLowerCase());

    if (!from || !to || !days.length) {
      alert('Seleccione rango de fechas y grupo.');
      return;
    }

    // contemos aprox del lado del cliente (no crítico)
    const map = {domingo:0,lunes:1,martes:2,'miércoles':3,'miercoles':3,jueves:4,viernes:5,'sábado':6,'sabado':6};
    const allowed = days.map(d => map[d]).filter(n => n !== undefined);

    const start = new Date(from+'T00:00:00');
    const end   = new Date(to+'T00:00:00');

    let count = 0;
    for (let dt = new Date(start); dt <= end; dt.setDate(dt.getDate()+1)) {
      if (allowed.includes(dt.getDay())) count++;
    }

    const box = document.getElementById('previewBox');
    box.style.display = 'block';
    box.innerHTML = `<strong>Previsualización:</strong> se crearán aproximadamente <b>${count}</b> programaciones.`;
  });
</script>
@stop
