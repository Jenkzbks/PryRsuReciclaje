<div class="card">
    <form id="massive-update-form" method="POST" action="{{ route('admin.schedulings.massive-update') }}">
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
                               value="{{ $massiveChange->from ?? now()->toDateString() }}" required>
                    </div>
                </div>

                {{-- Fecha Fin --}}
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Fecha de Fin *</label>
                        <input type="date" name="to" class="form-control"
                               value="{{ $massiveChange->to ?? now()->toDateString() }}" required>
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
                            <option value="Cambio de Conductor" selected>Cambio de Conductor</option>
                            <option value="Cambio de Ayudante">Cambio de Ayudante</option>
                            <option value="Cambio de Turno">Cambio de Turno</option>
                            <option value="Cambio de Vehiculo">Cambio de Vehiculo</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="row">
                {{-- Blocks for each change type: show current + replacement --}}
                <div id="block_conductor" class="w-100 change-block" style="display:flex">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Conductor Actual</label>
                            @if(isset($drivers))
                                <select id="current_driver" class="form-control current-select">
                                    <option value="">-- Seleccione --</option>
                                    @foreach($drivers as $d)
                                        <option value="{{ $d->id }}" @selected(($massiveChange->old_driver ?? null) == $d->id)>{{ $d->name }} - {{ $d->document }}</option>
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
                                        <option value="{{ $d->id }}" @selected(($massiveChange->new_driver ?? null) == $d->id)>{{ $d->name }} - {{ $d->document }} ({{ $d->contract_status ?? '' }})</option>
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
                                <select id="current_assistant" class="form-control current-select">
                                    <option value="">-- Seleccione --</option>
                                    @foreach($assistants as $a)
                                        <option value="{{ $a->id }}" @selected(($massiveChange->old_assistant ?? null) == $a->id)>{{ $a->lastnames }} {{ $a->names }}</option>
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
                                        <option value="{{ $a->id }}" @selected(($massiveChange->new_assistant ?? null) == $a->id)>{{ $a->lastnames }} {{ $a->names }}</option>
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
                                <select id="current_shift" class="form-control current-select">
                                    <option value="">-- Seleccione --</option>
                                    @foreach($shifts as $s)
                                        <option value="{{ $s->id }}" @selected(($massiveChange->old_shift ?? null) == $s->id)>{{ $s->name }}</option>
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
                                        <option value="{{ $s->id }}" @selected(($massiveChange->new_shift ?? null) == $s->id)>{{ $s->name }}</option>
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
                                <select id="current_vehicle" class="form-control current-select">
                                    <option value="">-- Seleccione --</option>
                                    @foreach($vehicles as $v)
                                        <option value="{{ $v->id }}" @selected(($massiveChange->old_vehicle ?? null) == $v->id)>{{ $v->plate ?? $v->name ?? $v->code }}</option>
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
                                        <option value="{{ $v->id }}" @selected(($massiveChange->new_vehicle ?? null) == $v->id)>{{ $v->plate ?? $v->name ?? $v->code }}</option>
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
                <textarea name="reason" class="form-control" rows="3" required>{{ $massiveChange->reason ?? '' }}</textarea>
            </div>

        </div>

        <div class="card-footer d-flex">
            <button type="submit" id="massive-update-submit" class="btn btn-success mr-2">
                <i class="fa fa-save"></i> Guardar
            </button>

            <button type="button" class="btn btn-danger" data-dismiss="modal">
                <i class="fa fa-times"></i> Cancelar
            </button>
        </div>

    </form>
</div>
    </form>
</div>

<script>
    (function(){
        const form = document.getElementById('massive-update-form');
        if (!form) return;

        form.addEventListener('submit', function(e){
            // Prevent immediate submit so we can show a confirmation dialog
            const confirmed = confirm('¿Confirmas aplicar el cambio masivo a las programaciones en el rango seleccionado? Esta acción actualizará las programaciones existentes.');
            if (!confirmed) {
                e.preventDefault();
                return false;
            }

            // Disable submit to avoid double submits
            const btn = document.getElementById('massive-update-submit');
            if (btn) {
                btn.disabled = true;
                btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Guardando...';
            }
        });
    })();
</script>
