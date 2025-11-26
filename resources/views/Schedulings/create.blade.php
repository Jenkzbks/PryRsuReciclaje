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

      {{-- ======================== CAMPOS FECHAS ======================== --}}
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

      {{-- ======================== GRUPO ======================== --}}
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

      {{-- ======================== INFO GRUPO ======================== --}}
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
          <div class="alert" id="previewBox" style="display:none;"></div>
        </div>
      </div>

      {{-- ======================== CARDS PERSONAL ======================== --}}
      <div class="row mt-3">

        {{-- === CONDUCTOR === --}}
        <div class="col-md-4">
          <div class="card shadow-sm" id="card_driver">
            <div class="card-header bg-light d-flex justify-content-between">
              <strong>Conductor</strong>
              <button type="button" class="btn btn-sm btn-outline-secondary d-none" id="btnChange_driver">
                <i class="fas fa-exchange-alt"></i>
              </button>
            </div>
            <div class="card-body">
              <div class="mb-1"><small class="text-muted">Nombre</small>
                <div id="drv_name" data-id=""></div>
              </div>
              <div class="mb-1"><small class="text-muted">Contrato</small>
                <div id="drv_contract">-</div>
              </div>
              <div class="mb-1"><small class="text-muted">Vacaciones</small>
                <div id="drv_vacation">-</div>
              </div>
              <div class="mt-2 small" id="drv_warn"></div>
            </div>
          </div>
        </div>

        {{-- === AYUDANTE 1 === --}}
        <div class="col-md-4">
          <div class="card shadow-sm" id="card_a1">
            <div class="card-header bg-light d-flex justify-content-between">
              <strong>Ayudante 1</strong>
              <button type="button" class="btn btn-sm btn-outline-secondary d-none" id="btnChange_assistant1">
                <i class="fas fa-exchange-alt"></i>
              </button>
            </div>
            <div class="card-body">
              <div class="mb-1"><small class="text-muted">Nombre</small>
                <div id="a1_name" data-id=""></div>
              </div>
              <div class="mb-1"><small class="text-muted">Contrato</small>
                <div id="a1_contract">-</div>
              </div>
              <div class="mb-1"><small class="text-muted">Vacaciones</small>
                <div id="a1_vacation">-</div>
              </div>
              <div class="mt-2 small" id="a1_warn"></div>
            </div>
          </div>
        </div>

        {{-- === AYUDANTE 2 === --}}
        <div class="col-md-4">
          <div class="card shadow-sm" id="card_a2">
            <div class="card-header bg-light d-flex justify-content-between">
              <strong>Ayudante 2</strong>
              <button type="button" class="btn btn-sm btn-outline-secondary d-none" id="btnChange_assistant2">
                <i class="fas fa-exchange-alt"></i>
              </button>
            </div>
            <div class="card-body">
              <div class="mb-1"><small class="text-muted">Nombre</small>
                <div id="a2_name" data-id=""></div>
              </div>
              <div class="mb-1"><small class="text-muted">Contrato</small>
                <div id="a2_contract">-</div>
              </div>
              <div class="mb-1"><small class="text-muted">Vacaciones</small>
                <div id="a2_vacation">-</div>
              </div>
              <div class="mt-2 small" id="a2_warn"></div>
            </div>
          </div>
        </div>

      </div>

      {{-- Hidden REPLACEMENTS --}}
      <input type="hidden" name="replacements[driver][employee_id]" id="rep_driver_employee_id">
      <input type="hidden" name="replacements[driver][dates]" id="rep_driver_dates">

      <input type="hidden" name="replacements[assistant1][employee_id]" id="rep_assistant1_employee_id">
      <input type="hidden" name="replacements[assistant1][dates]" id="rep_assistant1_dates">

      <input type="hidden" name="replacements[assistant2][employee_id]" id="rep_assistant2_employee_id">
      <input type="hidden" name="replacements[assistant2][dates]" id="rep_assistant2_dates">

    </div>

    <div class="card-footer text-right">
      <a href="{{ route('admin.schedulings.index') }}" class="btn btn-outline-secondary">Volver</a>
      <button type="submit" class="btn btn-success">Guardar Programación</button>
    </div>
  </form>
</div>

{{-- ========================================================= --}}
{{-- ========================= MODAL ========================== --}}
{{-- ========================================================= --}}
<div class="modal fade" id="modalReplace" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-md" role="document">
    <div class="modal-content">
      <div class="modal-header bg-light">
        <h5 class="modal-title">Seleccionar reemplazo</h5>
        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
      </div>
      <div class="modal-body">

        <div class="mb-2"><strong id="replaceRoleLabel">Rol</strong></div>

        <div class="form-group">
          <label>Empleado disponible</label>
          <select id="replaceSelect" class="form-control">
            <option value="">-- Seleccione --</option>
          </select>
        </div>

        <div class="alert alert-info" id="replaceDatesInfo" style="display:none;"></div>

      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-primary" id="btnApplyReplace">Aplicar</button>
      </div>
    </div>
  </div>
</div>

@stop


@section('js')

<script>
/* ============================================================
   ========== TODA LA LÓGICA JS COMPLETA CON EXCLUSIÓN ==========
   ============================================================ */

function badge(text, level) {
  const cls = level==='danger' ? 'badge badge-danger'
            : level==='warning' ? 'badge badge-warning'
            : 'badge badge-success';
  return `<span class="${cls}">${text}</span>`;
}

function setCardStatus(role, status, messages) {
  const map = { driver: 'card_driver', assistant1: 'card_a1', assistant2: 'card_a2' };
  const warnMap = { driver: 'drv_warn', assistant1: 'a1_warn', assistant2: 'a2_warn' };
  const card = document.getElementById(map[role]);
  const warn = document.getElementById(warnMap[role]);

  card.classList.remove('border','border-danger','border-success','border-warning');
  warn.innerHTML = '';

  if (status === 'ok') {
    card.classList.add('border','border-success');
    warn.innerHTML = badge('Disponible', 'success');
  } else if (status === 'warn') {
    card.classList.add('border','border-warning');
    warn.innerHTML = badge(messages.join(' | '), 'warning');
  } else if (status === 'error') {
    card.classList.add('border','border-danger');
    warn.innerHTML = badge(messages.join(' | '), 'danger');
  }
}

function clearCardsStatus() {
  setCardStatus('driver','ok',[]);
  setCardStatus('assistant1','ok',[]);
  setCardStatus('assistant2','ok',[]);
}


/* ============================================================
   ========== CARGA DE INFO DEL GRUPO + TARJETAS ==========
   ============================================================ */

function updateGroupInfo() {
  const opt = document.querySelector('#group_id option:checked');
  if (!opt) return;

  document.getElementById('infoZone').textContent = opt.dataset.zone || '-';
  document.getElementById('infoShift').textContent = opt.dataset.shift || '-';
  document.getElementById('infoVehicle').textContent = opt.dataset.vehicle || '-';
  document.getElementById('infoDays').textContent = opt.dataset.days || '-';

  const gid = opt.value;
  if (!gid) { clearCards(); return; }

  fetch("{{ route('admin.schedulings.group-info', ':id') }}".replace(':id', gid))
    .then(r => r.json())
    .then(fillCards)
    .then(clearCardsStatus)
    .catch(() => { clearCards(); clearCardsStatus(); });
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
  document.getElementById('drv_name').textContent = data.driver?.full_name ?? '-';
  document.getElementById('drv_name').dataset.id = data.driver?.id ?? '';

  document.getElementById('drv_contract').textContent = fmtContract(data.driver?.contract ?? null);
  document.getElementById('drv_vacation').textContent = fmtVacation(data.driver?.vacation ?? null);

  document.getElementById('a1_name').textContent = data.assistant1?.full_name ?? '-';
  document.getElementById('a1_name').dataset.id = data.assistant1?.id ?? '';

  document.getElementById('a1_contract').textContent = fmtContract(data.assistant1?.contract ?? null);
  document.getElementById('a1_vacation').textContent = fmtVacation(data.assistant1?.vacation ?? null);

  document.getElementById('a2_name').textContent = data.assistant2?.full_name ?? '-';
  document.getElementById('a2_name').dataset.id = data.assistant2?.id ?? '';

  document.getElementById('a2_contract').textContent = fmtContract(data.assistant2?.contract ?? null);
  document.getElementById('a2_vacation').textContent = fmtVacation(data.assistant2?.vacation ?? null);
}

function clearCards() {
  ['drv','a1','a2'].forEach(p => {
    document.getElementById(p+'_name').textContent = '-';
    document.getElementById(p+'_name').dataset.id = '';
    document.getElementById(p+'_contract').textContent = '-';
    document.getElementById(p+'_vacation').textContent = '-';
  });
}

let lastAvailability = null;


/* ============================================================
   ========== VALIDACIÓN DISPONIBILIDAD ========
   ============================================================ */

function toggleChangeButtons() {
  const roles = ['driver','assistant1','assistant2'];
  roles.forEach(role => {
    const btn = document.getElementById('btnChange_'+role);
    if (!btn) return;

    const hasIssues = !!(lastAvailability &&
      lastAvailability.byRole &&
      (lastAvailability.byRole[role] || []).length);

    btn.classList.toggle('d-none', !hasIssues);
  });
}


async function checkAvailabilityClient() {
  const gid = document.getElementById('group_id').value;
  const from = document.querySelector('input[name="from"]').value;
  const to   = document.querySelector('input[name="to"]').value;

  if (!gid || !from || !to) {
    alert('Seleccione grupo y rango de fechas.');
    return { ok:false, data:null };
  }

  clearCardsStatus();

  const res = await fetch("{{ route('admin.schedulings.check-availability') }}", {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-CSRF-TOKEN': '{{ csrf_token() }}'
    },
    body: JSON.stringify({ group_id: gid, from, to })
  });

  if (!res.ok) return { ok:false, data:null };

  const data = await res.json();
  const byRole = data.byRole || {};

  const renderRole = role => {
    const items = byRole[role] || [];
    if (!items.length) {
      setCardStatus(role, 'ok', []);
    } else {
      const hasContract = items.some(x =>
        x.reason.toLowerCase().includes('contrato') ||
        x.reason.toLowerCase().includes('sin contrato')
      );
      const msgs = items.map(x => `${x.reason}: ${x.dates.join(', ')}`);
      setCardStatus(role, hasContract ? 'error' : 'warn', msgs);
    }
  };

  renderRole('driver');
  renderRole('assistant1');
  renderRole('assistant2');

  const box = document.getElementById('previewBox');
  if (!data.ok) {
    const lines = data.conflicts.flatMap(c =>
      c.items.map(it =>
        `${c.name} no está disponible por ${it.reason} en: ${it.dates.join(', ')}`
      )
    );
    box.style.display = 'block';
    box.classList.remove('alert-info');
    box.classList.add('alert-danger');
    box.innerHTML = `<strong>Conflictos detectados:</strong><br>${lines.map(l=>`• ${l}`).join('<br>')}`;
  } else {
    box.style.display = 'block';
    box.classList.remove('alert-danger');
    box.classList.add('alert-info');
    box.innerHTML = `<strong>Validación OK:</strong> No se encontraron conflictos.`;
  }

  return { ok:data.ok, data };
}

async function runAvailabilityAndDecorate() {
  const res = await checkAvailabilityClient();
  if (res && res.data) lastAvailability = res.data;
  toggleChangeButtons();
}


/* ============================================================
   ========== MODAL DE REEMPLAZO (CON EXCLUSIÓN) ==========
   ============================================================ */

function openReplaceModal(role) {
  if (!lastAvailability || !lastAvailability.byRole) return;

  const items = lastAvailability.byRole[role] || [];
  const dates = [...new Set(items.flatMap(it => it.dates))].sort();
  
  const groupId = document.getElementById('group_id').value; // 🔥 OBTENER EL GROUP_ID

  document.getElementById('replaceRoleLabel').textContent =
    role === 'driver' ? 'Conductor'
    : role === 'assistant1' ? 'Ayudante 1'
    : 'Ayudante 2';

  const info = document.getElementById('replaceDatesInfo');
  info.style.display = 'block';
  info.innerHTML = `<strong>Fechas a cubrir:</strong> ${dates.join(', ')}`;

  const typeId = (role === 'driver') ? 1 : 2;

  // 🔥 ENVIAR GROUP_ID EN LA PETICIÓN
  const params = new URLSearchParams({
    type_id: typeId,
    dates: dates.join(','),
    group_id: groupId // 🔥 NUEVO PARÁMETRO
  });

  fetch("{{ route('admin.schedulings.available-candidates') }}?" + params.toString())
    .then(r => r.json())
    .then(list => {
      const sel = document.getElementById('replaceSelect');
      sel.innerHTML = '<option value="">-- Seleccione --</option>';

      if (!list.length) {
        const opt = document.createElement('option');
        opt.textContent = 'No hay empleados disponibles';
        opt.disabled = true;
        sel.appendChild(opt);
      } else {
        list.forEach(it => {
          const opt = document.createElement('option');
          opt.value = it.id;
          opt.textContent = it.name;
          sel.appendChild(opt);
        });
      }

      sel.dataset.role = role;
      sel.dataset.dates = dates.join(',');

      $('#modalReplace').modal('show');
    })
    .catch(() => alert("Error cargando candidatos"));
}


/* ============================================================
   ========== APLICAR REEMPLAZO ==========
   ============================================================ */

document.getElementById('btnApplyReplace').addEventListener('click', function () {
  const sel = document.getElementById('replaceSelect');
  const role  = sel.dataset.role;
  const dates = sel.dataset.dates || '';
  const empId = sel.value;

  if (!role || !dates || !empId) {
    alert('Seleccione un empleado.');
    return;
  }

  document.getElementById(`rep_${role}_employee_id`).value = empId;
  document.getElementById(`rep_${role}_dates`).value       = dates;

  const warnId =
    role === 'driver' ? 'drv_warn' :
    role === 'assistant1' ? 'a1_warn' :
    'a2_warn';

  const warnEl = document.getElementById(warnId);
  warnEl.innerHTML = `<span class="badge badge-primary">Reemplazo seleccionado</span>`;

  $('#modalReplace').modal('hide');
});


/* ============================================================
   ========== PREVIEW ========
   ============================================================ */

document.getElementById('btnPreview').addEventListener('click', async function() {
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

  let count = 0;
  const start = new Date(from+'T00:00:00');
  const end   = new Date(to+'T00:00:00');

  for (let dt = new Date(start); dt <= end; dt.setDate(dt.getDate()+1)) {
    if (allowed.includes(dt.getDay())) count++;
  }

  const box = document.getElementById('previewBox');
  box.style.display = 'block';
  box.classList.remove('alert-danger');
  box.classList.add('alert-info');
  box.innerHTML = `<strong>Previsualización:</strong> se crearán aproximadamente <b>${count}</b> programaciones.`;

  await runAvailabilityAndDecorate();
});


/* ============================================================
   ========== VALIDAR SUBMIT ==========
   ============================================================ */

document.querySelector('form[action="{{ route('admin.schedulings.store') }}"]').addEventListener('submit', async function(e) {
  const res = await checkAvailabilityClient();
  lastAvailability = res.data || null;
  toggleChangeButtons();

  if (!res.ok) {
    const stillHasErrors = role => {
      const items = (lastAvailability?.byRole?.[role] || []);
      if (!items.length) return false;

      const repDates = (document.getElementById(`rep_${role}_dates`).value || '').split(',').filter(Boolean);
      const conflictDates = [...new Set(items.flatMap(it => it.dates))];

      return !conflictDates.every(d => repDates.includes(d));
    };

    const pending = ['driver','assistant1','assistant2'].filter(stillHasErrors);

    if (pending.length) {
      e.preventDefault();
      alert('Hay conflictos sin cubrir. Use "Cambiar" para asignar reemplazos.');
    }
  }
});


/* ============================================================
   ========== INIT ==========
   ============================================================ */

document.getElementById('group_id').addEventListener('change', updateGroupInfo);
updateGroupInfo();

document.getElementById('btnChange_driver').addEventListener('click', () => openReplaceModal('driver'));
document.getElementById('btnChange_assistant1').addEventListener('click', () => openReplaceModal('assistant1'));
document.getElementById('btnChange_assistant2').addEventListener('click', () => openReplaceModal('assistant2'));

</script>

@stop
