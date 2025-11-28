@extends('adminlte::page')

@section('title', 'Editar Cambio Masivo')

@section('content_header')
    <h1>Editar Cambio Masivo</h1>
@stop

@section('content')
<div class="card">
    <form method="POST" action="{{ route('admin.schedulings.store') }}">
        @csrf

        <div class="card-body">

            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach($errors->all() as $e)
                            <li>{{ $e }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="row">

                {{-- Fecha Inicio --}}
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Fecha de Inicio *</label>
                        <input type="date" name="from" class="form-control"
                               value="{{ $massiveChange->from }}" required>
                    </div>
                </div>

                {{-- Fecha Fin --}}
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Fecha de Fin *</label>
                        <input type="date" name="to" class="form-control"
                               value="{{ $massiveChange->to }}" required>
                    </div>
                </div>

                {{-- Zonas --}}
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Zonas (Opcional)</label>
                        <select name="zones[]" class="form-control" multiple>
                            @foreach($zones as $z)
                                <option value="{{ $z->id }}"
                                    @selected(in_array($z->id, $massiveChange->zones ?? []))>
                                    {{ $z->name }}
                                </option>
                            @endforeach
                        </select>
                        <small class="text-muted">Dejar vacío para aplicar a todas las zonas</small>
                    </div>
                </div>

                {{-- Tipo de Cambio --}}
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Tipo de Cambio *</label>
                        <select id="change_type" name="type" class="form-control" required>
                            <option value="Cambio de Conductor" @selected($massiveChange->type === 'Cambio de Conductor')>Cambio de Conductor</option>
                            <option value="Cambio de Ayudante" @selected($massiveChange->type === 'Cambio de Ayudante')>Cambio de Ayudante</option>
                            <option value="Cambio de Turno" @selected($massiveChange->type === 'Cambio de Turno')>Cambio de Turno</option>
                            <option value="Cambio de Vehiculo" @selected($massiveChange->type === 'Cambio de Vehiculo')>Cambio de Vehiculo</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="row">
                {{-- Blocks for each change type: show current + replacement --}}
                <div id="block_conductor" class="w-100 change-block" style="display:none">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Conductor Actual</label>
                            @if(isset($drivers))
                                <select class="form-control current-select" disabled>
                                    @foreach($drivers as $d)
                                        <option value="{{ $d->id }}" @selected($massiveChange->old_driver == $d->id)>{{ $d->name }} - {{ $d->document }}</option>
                                    @endforeach
                                </select>
                                <input type="hidden" name="old_driver" value="{{ $massiveChange->old_driver ?? '' }}">
                            @else
                                <p class="form-control-plaintext text-muted">No hay lista de conductores</p>
                            @endif
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Conductor Reemplazo *</label>
                            @if(isset($drivers))
                                <select id="new_driver" name="new_driver" class="form-control new-select">
                                    <option value="">-- Seleccione --</option>
                                    @foreach($drivers as $d)
                                        <option value="{{ $d->id }}" @selected($massiveChange->new_driver == $d->id)>
                                            {{ $d->name }} - {{ $d->document }} ({{ $d->contract_status ?? '' }})
                                        </option>
                                    @endforeach
                                </select>
                            @else
                                <p class="form-control-plaintext text-muted">No hay lista de conductores</p>
                            @endif
                        </div>
                    </div>
                </div>

                <div id="block_ayudante" class="w-100 change-block" style="display:none">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Ayudante Actual</label>
                            @if(isset($assistants))
                                <select class="form-control current-select" disabled>
                                    @foreach($assistants as $a)
                                        <option value="{{ $a->id }}" @selected($massiveChange->old_assistant == $a->id)>{{ $a->lastnames }} {{ $a->names }}</option>
                                    @endforeach
                                </select>
                                <input type="hidden" name="old_assistant" value="{{ $massiveChange->old_assistant ?? '' }}">
                            @else
                                <p class="form-control-plaintext text-muted">No hay lista de ayudantes</p>
                            @endif
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Ayudante Reemplazo *</label>
                            @if(isset($assistants))
                                <select id="new_assistant" name="new_assistant" class="form-control new-select">
                                    <option value="">-- Seleccione --</option>
                                    @foreach($assistants as $a)
                                        <option value="{{ $a->id }}" @selected($massiveChange->new_assistant == $a->id)>{{ $a->lastnames }} {{ $a->names }}</option>
                                    @endforeach
                                </select>
                            @else
                                <p class="form-control-plaintext text-muted">No hay lista de ayudantes</p>
                            @endif
                        </div>
                    </div>
                </div>

                <div id="block_turno" class="w-100 change-block" style="display:none">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Turno Actual</label>
                            @if(isset($shifts))
                                <select class="form-control current-select" disabled>
                                    @foreach($shifts as $s)
                                        <option value="{{ $s->id }}" @selected($massiveChange->old_shift == $s->id)>{{ $s->name }}</option>
                                    @endforeach
                                </select>
                                <input type="hidden" name="old_shift" value="{{ $massiveChange->old_shift ?? '' }}">
                            @else
                                <p class="form-control-plaintext text-muted">No hay lista de turnos</p>
                            @endif
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Turno Reemplazo *</label>
                            @if(isset($shifts))
                                <select id="new_shift" name="new_shift" class="form-control new-select">
                                    <option value="">-- Seleccione --</option>
                                    @foreach($shifts as $s)
                                        <option value="{{ $s->id }}" @selected($massiveChange->new_shift == $s->id)>{{ $s->name }}</option>
                                    @endforeach
                                </select>
                            @else
                                <p class="form-control-plaintext text-muted">No hay lista de turnos</p>
                            @endif
                        </div>
                    </div>
                </div>

                <div id="block_vehiculo" class="w-100 change-block" style="display:none">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Vehículo Actual</label>
                            @if(isset($vehicles))
                                <select class="form-control current-select" disabled>
                                    @foreach($vehicles as $v)
                                        <option value="{{ $v->id }}" @selected($massiveChange->old_vehicle == $v->id)>{{ $v->plate ?? $v->name ?? $v->code }}</option>
                                    @endforeach
                                </select>
                                <input type="hidden" name="old_vehicle" value="{{ $massiveChange->old_vehicle ?? '' }}">
                            @else
                                <p class="form-control-plaintext text-muted">No hay lista de vehículos</p>
                            @endif
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Vehículo Reemplazo *</label>
                            @if(isset($vehicles))
                                <select id="new_vehicle" name="new_vehicle" class="form-control new-select">
                                    <option value="">-- Seleccione --</option>
                                    @foreach($vehicles as $v)
                                        <option value="{{ $v->id }}" @selected($massiveChange->new_vehicle == $v->id)>{{ $v->plate ?? $v->name ?? $v->code }}</option>
                                    @endforeach
                                </select>
                            @else
                                <p class="form-control-plaintext text-muted">No hay lista de vehículos</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Motivo --}}
            <div class="form-group mt-3">
                <label>Motivo del Cambio Masivo *</label>
                <textarea name="reason" class="form-control" rows="3" required>{{ $massiveChange->reason }}</textarea>
            </div>

        </div>

        <div class="card-footer d-flex">
            <button type="submit" class="btn btn-success mr-2">
                <i class="fa fa-save"></i> Guardar
            </button>

            <a href="{{ route('admin.schedulings.index') }}" class="btn btn-danger">
                <i class="fa fa-times"></i> Cancelar
            </a>
        </div>

    </form>
</div>
@section('js')
<script>
document.addEventListener('DOMContentLoaded', function(){
    const typeSelect = document.getElementById('change_type');
    const blocks = {
        'Cambio de Conductor': document.getElementById('block_conductor'),
        'Cambio de Ayudante': document.getElementById('block_ayudante'),
        'Cambio de Turno': document.getElementById('block_turno'),
        'Cambio de Vehiculo': document.getElementById('block_vehiculo'),
    };

    function hideAll(){
        Object.values(blocks).forEach(b => { if(b) b.style.display = 'none'; });
        // remove required from all new-selects
        document.querySelectorAll('.new-select').forEach(el => { el.required = false; el.disabled = true; });
    }

    function disableMatchingOptions(newSelectId, oldValue){
        const sel = document.getElementById(newSelectId);
        if(!sel) return;
        // enable all first
        sel.querySelectorAll('option').forEach(o => o.disabled = false);
        if(oldValue){
            const opt = sel.querySelector('option[value="' + oldValue + '"]');
            if(opt){
                opt.disabled = true;
                // if currently selected, reset
                if(sel.value === oldValue) sel.value = '';
            }
        }
    }

    function applyType(t){
        hideAll();
        const block = blocks[t];
        if(!block) return;
        block.style.display = 'flex';
        // enable the replacement select inside shown block and make it required
        const newSel = block.querySelector('.new-select');
        if(newSel){ newSel.disabled = false; newSel.required = true; }

        // disable same-value option for the shown replacement select using hidden old_* inputs
        // driver
        const oldDriver = document.querySelector('input[name="old_driver"]')?.value || '';
        disableMatchingOptions('new_driver', oldDriver);
        const oldAssistant = document.querySelector('input[name="old_assistant"]')?.value || '';
        disableMatchingOptions('new_assistant', oldAssistant);
        const oldShift = document.querySelector('input[name="old_shift"]')?.value || '';
        disableMatchingOptions('new_shift', oldShift);
        const oldVehicle = document.querySelector('input[name="old_vehicle"]')?.value || '';
        disableMatchingOptions('new_vehicle', oldVehicle);
    }

    // init
    applyType(typeSelect.value);

    typeSelect.addEventListener('change', function(){
        applyType(this.value);
    });

    // Also ensure user cannot pick same after interacting (in case old hidden changed server-side)
    document.querySelectorAll('.new-select').forEach(function(sel){
        sel.addEventListener('change', function(){
            const name = sel.id; // new_driver etc
            const oldName = name.replace('new_', 'old_');
            const oldVal = document.querySelector('input[name="' + oldName + '"]')?.value || '';
            if(oldVal && sel.value === oldVal){
                alert('No puede seleccionar el mismo elemento que el actual. Elija otro.');
                sel.value = '';
            }
        });
    });
});
</script>
@stop
