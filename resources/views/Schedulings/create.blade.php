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

      {{-- === CARDS DE PERSONAL === --}}
      <div class="row mt-3">
        <div class="col-md-4">
          <div class="card shadow-sm">
            <div class="card-header bg-light"><strong>Conductor</strong></div>
            <div class="card-body">
              <div class="mb-1"><small class="text-muted">Nombre</small><div id="drv_name">-</div></div>
              <div class="mb-1"><small class="text-muted">Contrato</small><div id="drv_contract">-</div></div>
              <div class="mb-1"><small class="text-muted">Vacaciones</small><div id="drv_vacation">-</div></div>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card shadow-sm">
            <div class="card-header bg-light"><strong>Ayudante 1</strong></div>
            <div class="card-body">
              <div class="mb-1"><small class="text-muted">Nombre</small><div id="a1_name">-</div></div>
              <div class="mb-1"><small class="text-muted">Contrato</small><div id="a1_contract">-</div></div>
              <div class="mb-1"><small class="text-muted">Vacaciones</small><div id="a1_vacation">-</div></div>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card shadow-sm">
            <div class="card-header bg-light"><strong>Ayudante 2</strong></div>
            <div class="card-body">
              <div class="mb-1"><small class="text-muted">Nombre</small><div id="a2_name">-</div></div>
              <div class="mb-1"><small class="text-muted">Contrato</small><div id="a2_contract">-</div></div>
              <div class="mb-1"><small class="text-muted">Vacaciones</small><div id="a2_vacation">-</div></div>
            </div>
          </div>
        </div>
      </div>
      {{-- === /CARDS DE PERSONAL === --}}

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

    const gid = opt.value;
    if (!gid) { clearCards(); return; }

    fetch("{{ route('admin.schedulings.group-info', ':id') }}".replace(':id', gid))
      .then(r => r.json())
      .then(fillCards)
      .catch(() => clearCards());
  }

  function fmtContract(c) {
    if (!c) return '-';
    const range = [c.start_date, c.end_date].filter(Boolean).join(' al ');
    return range ? `${range} ${c.is_active ? '(activo)' : ''}` : (c.is_active ? '(activo)' : '-');
  }

  function fmtVacation(v) {
    if (!v) return 'Sin vacaciones activas';
    const range = [v.start_date, v.end_date].filter(Boolean).join(' al ');
    return range ? `${range} (${v.status})` : v.status;
  }

  function fillCards(data) {
    // Conductor
    document.getElementById('drv_name').textContent     = data.driver?.full_name ?? '-';
    document.getElementById('drv_contract').textContent = fmtContract(data.driver?.contract ?? null);
    document.getElementById('drv_vacation').textContent = fmtVacation(data.driver?.vacation ?? null);
    // Ayudante 1
    document.getElementById('a1_name').textContent      = data.assistant1?.full_name ?? '-';
    document.getElementById('a1_contract').textContent  = fmtContract(data.assistant1?.contract ?? null);
    document.getElementById('a1_vacation').textContent  = fmtVacation(data.assistant1?.vacation ?? null);
    // Ayudante 2
    document.getElementById('a2_name').textContent      = data.assistant2?.full_name ?? '-';
    document.getElementById('a2_contract').textContent  = fmtContract(data.assistant2?.contract ?? null);
    document.getElementById('a2_vacation').textContent  = fmtVacation(data.assistant2?.vacation ?? null);
  }

  function clearCards() {
    ['drv','a1','a2'].forEach(p => {
      document.getElementById(p+'_name').textContent = '-';
      document.getElementById(p+'_contract').textContent = '-';
      document.getElementById(p+'_vacation').textContent = '-';
    });
  }

  // Previsualización (tu lógica original)
  document.getElementById('btnPreview').addEventListener('click', function() {
    const from = document.querySelector('input[name="from"]').value;
    const to   = document.querySelector('input[name="to"]').value;
    const opt  = document.querySelector('#group_id option:checked');
    const days = (opt?.dataset.days || '').split(',').map(s => s.trim().toLowerCase()).filter(Boolean);

    if (!from || !to || !days.length) {
      alert('Seleccione rango de fechas y grupo.');
      return;
    }

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

  document.getElementById('group_id').addEventListener('change', updateGroupInfo);
  updateGroupInfo(); // al cargar
</script>
@stop
