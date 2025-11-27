<?php $__env->startSection('title', 'Programaciones'); ?>

<?php $__env->startSection('content_header'); ?>
<div class="d-flex align-items-center">
  <h1 class="mb-0">Programaciones</h1>

  <div class="ml-auto d-flex align-items-center">
    <button type="button" id="btnOpenMassive" class="btn btn-secondary btn-sm">
      <i class="fas fa-plus"></i> Cambio Masivo
    </button>
    <a href="<?php echo e(route('admin.schedulings.create-masive')); ?>" class="btn btn-primary btn-sm ml-2">
      <i class="fas fa-plus"></i> Nueva Programación Masiva
    </a>
    <a href="<?php echo e(route('admin.schedulings.create')); ?>" class="btn btn-primary btn-sm ml-2">
      <i class="fas fa-plus"></i> Nueva Programación
    </a>
  </div>

</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="card">
  <div class="card-body">
    <form method="GET" class="mb-3">
      <div class="form-row">
        <div class="col-md-3">
          <label>Fecha inicio</label>
          <input type="date" name="from" class="form-control" value="<?php echo e($from); ?>">
        </div>
        <div class="col-md-3">
          <label>Fecha fin</label>
          <input type="date" name="to" class="form-control" value="<?php echo e($to); ?>">
        </div>
        <div class="col-md-3 d-flex align-items-end">
          <button class="btn btn-outline-secondary"><i class="fas fa-filter"></i> Filtrar</button>
        </div>
      </div>
    </form>

    <?php if(session('success')): ?>
      <div class="alert alert-success"><?php echo e(session('success')); ?></div>
    <?php endif; ?>

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
          <?php $__empty_1 = true; $__currentLoopData = $schedulings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <tr>
              <td><?php echo e($s->date); ?></td>
              <td><?php echo e($s->zone->name ?? '-'); ?></td>
              <td><?php echo e($s->shift->name ?? '-'); ?></td>
              <td><?php echo e($s->vehicle->plate ?? '-'); ?></td>
              <td><?php echo e($s->group->name ?? '-'); ?></td>
              <td><?php echo e($s->notes); ?></td>
              <td class="d-flex">
                <a href="<?php echo e(route('admin.schedulings.edit',$s)); ?>" class="btn btn-sm btn-outline-primary mr-2">
                  <i class="fas fa-edit"></i>
                </a>
                <button type="button" class="btn btn-sm btn-info mr-2 btnDetalle" data-id="<?php echo e($s->id); ?>" title="Ver detalle">
                  <i class="fas fa-users"></i>
                </button>
                <form method="POST" action="<?php echo e(route('admin.schedulings.destroy',$s)); ?>" onsubmit="return confirm('¿Eliminar programación?')">
                  <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                  <button class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                </form>
              </td>
            </tr>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr><td colspan="8" class="text-center text-muted">Sin resultados</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>

    <div class="mt-3"><?php echo e($schedulings->appends(request()->query())->links()); ?></div>
  </div>
</div>
    <!-- Shared modal wrapper (used for Editar / Cambio Masivo) -->
    
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
    <div class="modal fade" id="modalDetalle" tabindex="-1" role="dialog" aria-labelledby="modalDetalleLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl" role="document">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title" id="modalDetalleLabel">Visualización de día programado e historial de cambios</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div id="detalleContenido">
          <!-- Aquí se cargará el contenido por AJAX -->
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fas fa-times"></i> Cerrar</button>
      </div>
    </div>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('js'); ?>
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
      const res = await fetch("<?php echo e(route('admin.schedulings.edit-massive')); ?>", { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
      const text = await res.text();
      // Inject the returned fragment into the shared modal body and show it
      modalBody.innerHTML = text;
      modalBody.dataset.loaded = '1';

      // Initialize the form after injection
      initMassiveChangeForm();

      try { $('#editModal').modal('show'); } catch(e) { console.warn(e); }
    } catch (err) {
      console.error(err);
      alert('Error cargando contenido. Revisa la consola.');
    }
  });
});

// Function to initialize the massive change form
function initMassiveChangeForm(){
    // no static resolution here: resolve elements dynamically in functions
    function getTypeSelect(){
        return document.getElementById('change_type') || document.querySelector('select[name="type"]');
    }
    function getBlockByType(t){
        if(!t) return null;
        if(t === 'Cambio de Conductor') return document.getElementById('block_conductor');
        if(t === 'Cambio de Ayudante') return document.getElementById('block_ayudante');
        if(t === 'Cambio de Turno') return document.getElementById('block_turno');
        if(t === 'Cambio de Vehiculo') return document.getElementById('block_vehiculo');
        return null;
    }

    function hideAll(){
        // hide all known blocks (resolve dynamically)
        ['block_conductor','block_ayudante','block_turno','block_vehiculo'].forEach(function(id){
            const el = document.getElementById(id);
            if(el) el.style.display = 'none';
        });
        document.querySelectorAll('.new-select').forEach(el => { el.required = false; el.disabled = true; });
    }

    function applyType(t){
        console.debug('[massive-change] applyType ->', t);
        hideAll();
        const block = getBlockByType(t);
        if(!block){ console.debug('[massive-change] no block found for', t); return; }
        console.debug('[massive-change] showing block for', t);
        // show block using flex so internal cols align as before
        block.style.display = 'flex';
        const newSel = block.querySelector('.new-select');
        if(newSel){ newSel.disabled = false; newSel.required = true; }
    }

    // init: try to find the select now; if not present (partial inserted later), poll briefly
    function initWhenReady(){
        let sel = document.getElementById('change_type') || document.querySelector('select[name="type"]');
        if(sel){
            console.debug('[massive-change] Found select, value:', sel.value);
            // ensure id for consistent reference
            if(!sel.id) sel.id = 'change_type';
            // apply current value or default
            const val = sel.value || 'Cambio de Conductor';
            sel.value = val;
            console.debug('[massive-change] Applying type:', val);
            applyType(val);
            sel.addEventListener('change', function(){ applyType(this.value); });
            return true;
        } else {
            console.debug('[massive-change] Select not found yet');
        }
        return false;
    }

    // init: if select exists with value, apply it (for static loads)
    initWhenReady();
    // poll for dynamic loads (e.g., modals) - faster
    let attempts = 0;
    const intId = setInterval(function(){
        attempts++;
        if(initWhenReady() || attempts > 50){
            clearInterval(intId);
        }
    }, 100);

    document.querySelectorAll('.new-select').forEach(function(sel){
        sel.addEventListener('change', function(){
            const name = sel.id;
            const oldName = name.replace('new_', 'old_');
            const oldVal = document.querySelector('input[name="' + oldName + '"]')?.value || '';
            if(oldVal && sel.value === oldVal){ alert('No puede seleccionar el mismo elemento que el actual. Elija otro.'); sel.value = ''; }
        });
    });

    // Listeners for current selects to update hidden and disable in new
    document.querySelectorAll('.current-select').forEach(function(sel){
        sel.addEventListener('change', function(){
            const type = sel.id.replace('current_', ''); // e.g., driver
            const hidden = document.querySelector('input[name="old_' + type + '"]');
            if(hidden) hidden.value = sel.value;
            const newSel = document.getElementById('new_' + type);
            if(newSel){
                newSel.querySelectorAll('option').forEach(o => o.disabled = false);
                if(sel.value){
                    const opt = newSel.querySelector('option[value="' + sel.value + '"]');
                    if(opt) opt.disabled = true;
                    if(newSel.value === sel.value) newSel.value = '';
                }
            }
        });
        // Trigger on init to set hidden and disable
        sel.dispatchEvent(new Event('change'));
    });

    // Delegated listener for type select changes, works even if select is added later
    document.addEventListener('change', function(e){
        const t = e.target;
        if(t && (t.id === 'change_type' || t.name === 'type')){
            console.log('[massive-change] Delegated change detected:', t.value);
            applyType(t.value);
        }
    });
}
</script>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('js'); ?>
<script>
$(function() {
  $('.btnDetalle').click(function() {
    var id = $(this).data('id');
    $('#detalleContenido').html('<div class="text-center p-5"><i class="fas fa-spinner fa-spin fa-2x"></i> Cargando...</div>');
    $('#modalDetalle').modal('show');
    $.get("<?php echo e(url('admin/schedulings')); ?>/" + id + "/detalle", function(data) {
      $('#detalleContenido').html(data);
    });
  });
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('adminlte::page', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH F:\USAT 2025-2\topicos\PryFINAL\PryRsuReciclaje\resources\views/schedulings/index.blade.php ENDPATH**/ ?>