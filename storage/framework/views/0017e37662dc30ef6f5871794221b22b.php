<?php $__env->startSection('title','Nueva Programación'); ?>

<?php $__env->startSection('content_header'); ?>
  <h1>Registrar programación</h1>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="card">
  <form method="POST" action="<?php echo e(route('admin.schedulings.store')); ?>">
    <?php echo csrf_field(); ?>
    <div class="card-body">
      <?php if($errors->any()): ?>
        <div class="alert alert-danger">
          <ul class="mb-0">
            <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><li><?php echo e($e); ?></li><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </ul>
        </div>
      <?php endif; ?>

      
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
            <?php $__currentLoopData = $groups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $g): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <option value="<?php echo e($g->id); ?>"
                data-zone="<?php echo e($g->zone->name ?? ''); ?>"
                data-shift="<?php echo e($g->shift->name ?? ''); ?>"
                data-vehicle="<?php echo e($g->vehicle->plate ?? ''); ?>"
                data-days="<?php echo e($g->days); ?>">
                <?php echo e($g->name); ?>

              </option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </select>
        </div>

        <div class="form-group col-md-6">
          <label>Notas</label>
          <input type="text" name="notes" class="form-control" maxlength="120" placeholder="Opcional">
        </div>
      </div>

      
      <div class="row">
        <div class="col-md-12">
          <div class="alert alert-info py-2 mb-3" id="groupInfoCompact" style="display: none;">
            <div class="row">
              <div class="col-md-3">
                <strong>Zona:</strong> <span id="infoZone">-</span>
              </div>
              <div class="col-md-3">
                <strong>Turno:</strong> <span id="infoShift">-</span>
              </div>
              <div class="col-md-3">
                <strong>Vehículo:</strong> <span id="infoVehicle">-</span>
              </div>
              <div class="col-md-3">
                <strong>Días base:</strong> <span id="infoDays">-</span>
              </div>
            </div>
          </div>
        </div>
      </div>

      
      <div class="row mt-3">
        <div class="col-md-12">
          <div class="card">
            <div class="card-header bg-primary text-white">
              <strong>Días de Trabajo</strong>
              <small class="float-right">Los días del grupo ya están marcados. Puede agregar días adicionales si es necesario.</small>
            </div>
            <div class="card-body">
              <div class="form-group mb-0">
                <div class="row" id="daysChecklist">
                  <?php
                    $daysMap = [
                      'lunes' => 'Lunes',
                      'martes' => 'Martes', 
                      'miércoles' => 'Miércoles',
                      'jueves' => 'Jueves',
                      'viernes' => 'Viernes',
                      'sábado' => 'Sábado',
                      'domingo' => 'Domingo'
                    ];
                  ?>
                  <?php $__currentLoopData = $daysMap; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="col-md-3 col-sm-4 col-6 mb-2">
                      <div class="form-check">
                        <input class="form-check-input day-checkbox" type="checkbox" 
                               name="additional_days[]" value="<?php echo e($key); ?>" id="day_<?php echo e($key); ?>">
                        <label class="form-check-label" for="day_<?php echo e($key); ?>">
                          <?php echo e($label); ?>

                        </label>
                      </div>
                    </div>
                  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
                <small class="text-muted mt-2 d-block">
                  <i class="fas fa-info-circle"></i> Los días marcados automáticamente son los configurados en el grupo. 
                  Puede agregar días adicionales para esta programación específica.
                </small>
              </div>
            </div>
          </div>
        </div>
      </div>

      
      <div class="row mt-3">
        <div class="col-md-12">
          <div class="alert" id="previewBox" style="display:none;"></div>
        </div>
      </div>

      
      <div class="row mt-3" id="membersContainer">
        
      </div>

      
      <div id="replacementsContainer">
        
      </div>

      
      <input type="hidden" name="additional_days_processed" id="additional_days_processed">

    </div>

    <div class="card-footer text-right">
      <a href="<?php echo e(route('admin.schedulings.index')); ?>" class="btn btn-outline-secondary">Volver</a>
      <button type="submit" class="btn btn-success">Guardar Programación</button>
    </div>
  </form>
</div>




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

<?php $__env->stopSection(); ?>


<?php $__env->startSection('js'); ?>

<script>
/* ============================================================
   ========== TODA LA LÓGICA JS COMPLETA CON MIEMBROS DINÁMICOS ==========
   ============================================================ */

function badge(text, level) {
  const cls = level==='danger' ? 'badge badge-danger'
            : level==='warning' ? 'badge badge-warning'
            : 'badge badge-success';
  return `<span class="${cls}">${text}</span>`;
}

function setCardStatus(memberIndex, status, messages) {
  const card = document.getElementById(`card_member_${memberIndex}`);
  const warn = document.getElementById(`warn_member_${memberIndex}`);

  if (!card || !warn) return;

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
  // Se limpiarán dinámicamente según los miembros
}

/* ============================================================
   ========== CARGA DE INFO DEL GRUPO + TARJETAS DINÁMICAS ==========
   ============================================================ */

function updateGroupInfo() {
  const opt = document.querySelector('#group_id option:checked');
  if (!opt) {
    document.getElementById('groupInfoCompact').style.display = 'none';
    clearCards(); 
    return; 
  }

  // Mostrar información compacta del grupo
  const groupInfo = document.getElementById('groupInfoCompact');
  document.getElementById('infoZone').textContent = opt.dataset.zone || '-';
  document.getElementById('infoShift').textContent = opt.dataset.shift || '-';
  document.getElementById('infoVehicle').textContent = opt.dataset.vehicle || '-';
  document.getElementById('infoDays').textContent = opt.dataset.days || '-';
  groupInfo.style.display = 'block';

  // Marcar automáticamente los días del grupo en el checklist
  markGroupDays(opt.dataset.days);

  const gid = opt.value;
  if (!gid) { 
    clearCards(); 
    return; 
  }

  fetch("<?php echo e(route('admin.schedulings.group-info', ':id')); ?>".replace(':id', gid))
    .then(r => r.json())
    .then(data => {
      fillCards(data.members);
      clearCardsStatus();
    })
    .catch(() => { 
      clearCards(); 
      clearCardsStatus(); 
    });
}

function markGroupDays(daysCsv) {
  // Limpiar todos los checkboxes primero
  const allCheckboxes = document.querySelectorAll('.day-checkbox');
  allCheckboxes.forEach(checkbox => {
    checkbox.checked = false;
    checkbox.disabled = false;
  });

  if (!daysCsv) return;

  // Marcar los días del grupo y hacerlos readonly
  const groupDays = daysCsv.split(',').map(day => day.trim().toLowerCase());
  groupDays.forEach(day => {
    const checkbox = document.getElementById(`day_${day}`);
    if (checkbox) {
      checkbox.checked = true;
      checkbox.disabled = true;
      // Agregar estilo visual para días del grupo
      checkbox.parentElement.classList.add('text-primary', 'font-weight-bold');
    }
  });
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

function fillCards(members) {
  const container = document.getElementById('membersContainer');
  const replacementsContainer = document.getElementById('replacementsContainer');
  
  // Limpiar contenedores
  container.innerHTML = '';
  replacementsContainer.innerHTML = '';

  if (!members || members.length === 0) {
    container.innerHTML = '<div class="col-12"><div class="alert alert-warning">No hay miembros en este grupo</div></div>';
    return;
  }

  // Generar cards dinámicamente
  members.forEach((member, index) => {
    const colSize = members.length <= 3 ? '4' : 
                   members.length === 4 ? '3' : '2';
    
    const cardHtml = `
      <div class="col-md-${colSize} mb-3">
        <div class="card shadow-sm" id="card_member_${index}">
          <div class="card-header bg-light d-flex justify-content-between">
            <strong>${member.role}</strong>
            <button type="button" class="btn btn-sm btn-outline-secondary d-none" id="btnChange_member_${index}">
              <i class="fas fa-exchange-alt"></i>
            </button>
          </div>
          <div class="card-body">
            <div class="mb-1"><small class="text-muted">Nombre</small>
              <div id="name_member_${index}" data-id="${member.id}">${member.full_name}</div>
            </div>
            <div class="mb-1"><small class="text-muted">Tipo</small>
              <div>${member.type_name}</div>
            </div>
            <div class="mb-1"><small class="text-muted">Contrato</small>
              <div id="contract_member_${index}">${fmtContract(member.contract)}</div>
            </div>
            <div class="mb-1"><small class="text-muted">Vacaciones</small>
              <div id="vacation_member_${index}">${fmtVacation(member.vacation)}</div>
            </div>
            <div class="mt-2 small" id="warn_member_${index}"></div>
          </div>
        </div>
      </div>
    `;
    
    container.innerHTML += cardHtml;

    // Generar hidden inputs para reemplazos
    const hiddenInputs = `
      <input type="hidden" name="replacements[member_${index}][employee_id]" id="rep_member_${index}_employee_id">
      <input type="hidden" name="replacements[member_${index}][dates]" id="rep_member_${index}_dates">
    `;
    
    replacementsContainer.innerHTML += hiddenInputs;

    // Agregar event listener al botón de cambio
    setTimeout(() => {
      const changeBtn = document.getElementById(`btnChange_member_${index}`);
      if (changeBtn) {
        changeBtn.addEventListener('click', () => openReplaceModal(index, member.type_id));
      }
    }, 100);
  });
}

function clearCards() {
  document.getElementById('membersContainer').innerHTML = '';
  document.getElementById('replacementsContainer').innerHTML = '';
}

let lastAvailability = null;

/* ============================================================
   ========== VALIDACIÓN DISPONIBILIDAD ========
   ============================================================ */

function toggleChangeButtons() {
  // Se activarán dinámicamente según los miembros con conflictos
  if (!lastAvailability || !lastAvailability.byRole) return;

  Object.keys(lastAvailability.byRole).forEach(memberKey => {
    const memberIndex = memberKey.replace('member_', '');
    const btn = document.getElementById(`btnChange_member_${memberIndex}`);
    if (!btn) return;

    const hasIssues = !!(lastAvailability.byRole[memberKey] && 
                        lastAvailability.byRole[memberKey].length);

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

  // Limpiar estados de todas las cards
  const membersContainer = document.getElementById('membersContainer');
  const cards = membersContainer.querySelectorAll('[id^="card_member_"]');
  cards.forEach(card => {
    card.classList.remove('border','border-danger','border-success','border-warning');
    const warn = card.querySelector('[id^="warn_member_"]');
    if (warn) warn.innerHTML = '';
  });

  const res = await fetch("<?php echo e(route('admin.schedulings.check-availability')); ?>", {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
    },
    body: JSON.stringify({ 
      group_id: gid, 
      from, 
      to,
      additional_days: getAdditionalDays() // 🔥 Incluir días adicionales
    })
  });

  if (!res.ok) return { ok:false, data:null };

  const data = await res.json();
  const byRole = data.byRole || {};

  // Renderizar estado para cada miembro
  Object.keys(byRole).forEach(memberKey => {
    const memberIndex = memberKey.replace('member_', '');
    const items = byRole[memberKey] || [];
    
    if (!items.length) {
      setCardStatus(memberIndex, 'ok', []);
    } else {
      const hasContract = items.some(x =>
        x.reason.toLowerCase().includes('contrato') ||
        x.reason.toLowerCase().includes('sin contrato')
      );
      const msgs = items.map(x => `${x.reason}: ${x.dates.join(', ')}`);
      setCardStatus(memberIndex, hasContract ? 'error' : 'warn', msgs);
    }
  });

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
   ========== LÓGICA PARA DÍAS ADICIONALES ==========
   ============================================================ */

function getAdditionalDays() {
    const checkboxes = document.querySelectorAll('.day-checkbox:checked');
    return Array.from(checkboxes).map(cb => cb.value);
}

function parseSpanishDays(daysCsv) {
    const map = {
        'domingo': 0, 'lunes': 1, 'martes': 2,
        'miércoles': 3, 'miercoles': 3,
        'jueves': 4, 'viernes': 5,
        'sábado': 6, 'sabado': 6,
    };

    const out = [];
    const daysArray = daysCsv.split(',');
    
    daysArray.forEach(day => {
        const key = day.trim().toLowerCase();
        if (map[key] !== undefined) {
            out.push(map[key]);
        }
    });
    
    return out;
}

function getAllWorkingDays(groupDays, additionalDays) {
    // Convertir días del grupo a números
    const baseDays = parseSpanishDays(groupDays);
    
    // Convertir días adicionales a números
    const additionalDaysNumbers = additionalDays.map(day => {
        const map = {
            'domingo': 0, 'lunes': 1, 'martes': 2,
            'miércoles': 3, 'miercoles': 3,
            'jueves': 4, 'viernes': 5,
            'sábado': 6, 'sabado': 6,
        };
        return map[day.toLowerCase()];
    }).filter(day => day !== undefined);
    
    // Combinar y eliminar duplicados
    const allDays = [...new Set([...baseDays, ...additionalDaysNumbers])];
    return allDays.sort();
}

function countWorkingDaysInRange(from, to, groupDays, additionalDays) {
    const allWorkingDays = getAllWorkingDays(groupDays, additionalDays);
    
    let count = 0;
    const start = new Date(from + 'T00:00:00');
    const end = new Date(to + 'T00:00:00');
    
    for (let dt = new Date(start); dt <= end; dt.setDate(dt.getDate() + 1)) {
        if (allWorkingDays.includes(dt.getDay())) {
            count++;
        }
    }
    
    return count;
}

function updatePreviewWithAdditionalDays() {
    const from = document.querySelector('input[name="from"]').value;
    const to = document.querySelector('input[name="to"]').value;
    const opt = document.querySelector('#group_id option:checked');
    
    if (!from || !to || !opt) return;
    
    const groupDays = opt.dataset.days || '';
    const additionalDays = getAdditionalDays();
    
    const baseCount = countWorkingDaysInRange(from, to, groupDays, []);
    const totalCount = countWorkingDaysInRange(from, to, groupDays, additionalDays);
    const additionalCount = totalCount - baseCount;
    
    const box = document.getElementById('previewBox');
    box.style.display = 'block';
    box.classList.remove('alert-danger');
    box.classList.add('alert-info');
    
    let message = `<strong>Previsualización:</strong> se crearán <b>${totalCount}</b> programaciones.`;
    
    if (additionalCount > 0) {
        message += `<br><small class="text-warning">Incluye <b>${additionalCount}</b> días adicionales seleccionados.</small>`;
    } else {
        message += `<br><small class="text-muted">Basado en los días configurados del grupo.</small>`;
    }
    
    box.innerHTML = message;
    
    // Actualizar el hidden input con los días adicionales procesados
    document.getElementById('additional_days_processed').value = additionalDays.join(',');
}

/* ============================================================
   ========== MODAL DE REEMPLAZO DINÁMICO ==========
   ============================================================ */

function openReplaceModal(memberIndex, typeId) {
  if (!lastAvailability || !lastAvailability.byRole) return;

  const memberKey = `member_${memberIndex}`;
  const items = lastAvailability.byRole[memberKey] || [];
  const dates = [...new Set(items.flatMap(it => it.dates))].sort();
  
  const groupId = document.getElementById('group_id').value;
  const memberNameElement = document.getElementById(`name_member_${memberIndex}`);
  const memberName = memberNameElement ? memberNameElement.textContent : 'Miembro';

  document.getElementById('replaceRoleLabel').textContent = memberName;

  const info = document.getElementById('replaceDatesInfo');
  info.style.display = 'block';
  info.innerHTML = `<strong>Fechas a cubrir:</strong> ${dates.join(', ')}`;

  const params = new URLSearchParams({
    type_id: typeId,
    dates: dates.join(','),
    group_id: groupId
  });

  fetch("<?php echo e(route('admin.schedulings.available-candidates')); ?>?" + params.toString())
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

      sel.dataset.memberIndex = memberIndex;
      sel.dataset.dates = dates.join(',');

      $('#modalReplace').modal('show');
    })
    .catch(() => alert("Error cargando candidatos"));
}

/* ============================================================
   ========== APLICAR REEMPLAZO DINÁMICO ==========
   ============================================================ */

document.getElementById('btnApplyReplace').addEventListener('click', function () {
  const sel = document.getElementById('replaceSelect');
  const memberIndex = sel.dataset.memberIndex;
  const dates = sel.dataset.dates || '';
  const empId = sel.value;

  if (!memberIndex || !dates || !empId) {
    alert('Seleccione un empleado.');
    return;
  }

  document.getElementById(`rep_member_${memberIndex}_employee_id`).value = empId;
  document.getElementById(`rep_member_${memberIndex}_dates`).value = dates;

  const warnEl = document.getElementById(`warn_member_${memberIndex}`);
  if (warnEl) {
    warnEl.innerHTML = `<span class="badge badge-primary">Reemplazo seleccionado</span>`;
  }

  $('#modalReplace').modal('hide');
});

/* ============================================================
   ========== PREVIEW CON DÍAS ADICIONALES ========
   ============================================================ */

document.getElementById('btnPreview').addEventListener('click', async function() {
  const from = document.querySelector('input[name="from"]').value;
  const to   = document.querySelector('input[name="to"]').value;
  const opt  = document.querySelector('#group_id option:checked');

  if (!from || !to || !opt) {
    alert('Seleccione rango de fechas y grupo.');
    return;
  }

  updatePreviewWithAdditionalDays();
  await runAvailabilityAndDecorate();
});

/* ============================================================
   ========== VALIDAR SUBMIT ==========
   ============================================================ */

document.querySelector('form[action="<?php echo e(route('admin.schedulings.store')); ?>"]').addEventListener('submit', async function(e) {
  const res = await checkAvailabilityClient();
  lastAvailability = res.data || null;
  toggleChangeButtons();

  if (!res.ok) {
    const stillHasErrors = memberKey => {
      const items = (lastAvailability?.byRole?.[memberKey] || []);
      if (!items.length) return false;

      const memberIndex = memberKey.replace('member_', '');
      const repDates = (document.getElementById(`rep_${memberKey}_dates`).value || '').split(',').filter(Boolean);
      const conflictDates = [...new Set(items.flatMap(it => it.dates))];

      return !conflictDates.every(d => repDates.includes(d));
    };

    const pending = Object.keys(lastAvailability?.byRole || {}).filter(stillHasErrors);

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

// Agregar event listeners a los checkboxes de días adicionales
document.addEventListener('DOMContentLoaded', function() {
    const additionalDayCheckboxes = document.querySelectorAll('.day-checkbox');
    additionalDayCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const from = document.querySelector('input[name="from"]').value;
            const to = document.querySelector('input[name="to"]').value;
            const opt = document.querySelector('#group_id option:checked');
            
            if (from && to && opt) {
                updatePreviewWithAdditionalDays();
            }
        });
    });
    
    updateGroupInfo();
});

</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('adminlte::page', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH F:\USAT 2025-2\topicos\PryFINAL\PryRsuReciclaje\resources\views/schedulings/create.blade.php ENDPATH**/ ?>