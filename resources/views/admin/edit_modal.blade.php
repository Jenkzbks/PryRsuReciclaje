<form method="POST" action="{{ route('admin.schedulings.update',$scheduling) }}">
@csrf @method('PUT')

<input type="hidden" id="current_shift" value="{{ $scheduling->turno_actual }}">
<input type="hidden" id="current_vehicle" value="{{ $scheduling->vehiculo_actual }}">
<input type="hidden" name="date" value="{{ $scheduling->date }}">

<div class="card shadow">
    <div class="card-body">

        {{-- ===========================
            CAMBIO DE TURNO Y VEHÍCULO
        ============================ --}}
        <h5 class="mb-3">Cambio de Turno y Vehículo</h5>

        <div class="row mb-3">
            {{-- TURNO ACTUAL --}}
            <div class="col-md-4">
                <div class="form-group">
                    <label class="font-weight-bold">Turno Actual</label>
                    <input type="text" class="form-control" value="{{ $scheduling->turno_actual }}" readonly>
                </div>
            </div>

            {{-- NUEVO TURNO --}}
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="font-weight-bold">Nuevo Turno</label>
                            <select name="shift_id" class="form-control" id="shift_select">
                                <option value="">Seleccione un nuevo turno</option>
                                @foreach($shifts as $shift)
                                <option value="{{ $shift->id }}" 
                                    {{ $scheduling->shift_id == $shift->id ? 'selected' : '' }}>
                                    {{ $shift->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

            {{-- BOTÓN + TURNO --}}
            <div class="col-md-1 d-flex align-items-end">
                <button type="button" class="btn btn-success w-100" id="add_shift_change">+</button>
            </div>
        </div>

        <div class="row mb-3">
            {{-- VEHÍCULO ACTUAL --}}
            <div class="col-md-4">
                <div class="form-group">
                    <label class="font-weight-bold">Vehículo Actual</label>
                    <input type="text" class="form-control" value="{{ $scheduling->vehiculo_actual }}" readonly>
                </div>
            </div>

            {{-- NUEVO VEHÍCULO --}}
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="font-weight-bold">Nuevo Vehículo</label>
                            <select name="vehicle_id" class="form-control" id="vehicle_select">
                                <option value="">Seleccione un nuevo vehículo</option>
                                @foreach($vehicles as $vehicle)
                                <option value="{{ $vehicle->id }}"
                                    {{ $scheduling->vehicle_id == $vehicle->id ? 'selected' : '' }}>
                                    {{ $vehicle->plate }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

            {{-- BOTÓN + VEHÍCULO --}}
            <div class="col-md-1 d-flex align-items-end">
                <button type="button" class="btn btn-success w-100" id="add_vehicle_change">+</button>
            </div>
        </div>


        {{-- ===========================
            CAMBIO DE PERSONAL
        ============================ --}}
        <h5 class="mt-4 mb-3">Cambio de Personal</h5>

        <div class="row">

            {{-- PERSONAL ACTUAL --}}
            <div class="col-md-4">
                <div class="form-group">
                    <label class="font-weight-bold">Personal Actual</label>
                    <select class="form-control" id="personal_actual">
                        <option value="">Seleccione un personal</option>
                        @foreach($scheduling->details as $detail)
                            @if($detail->employee)
                                @php
                                    $role = 'Asistente';
                                    $role_key = 'assistant2_id';
                                    if ($detail->employee->id == $selectedDriverId) { $role = 'Conductor'; $role_key = 'driver_id'; }
                                    elseif ($detail->employee->id == $selectedA1Id) { $role = 'Asistente 1'; $role_key = 'assistant1_id'; }
                                    elseif ($detail->employee->id == $selectedA2Id) { $role = 'Asistente 2'; $role_key = 'assistant2_id'; }
                                @endphp
                                <option value="{{ $detail->employee->id }}" data-type="{{ $detail->employee->type_id }}" data-role="{{ $role_key }}">
                                    {{ $role }}: {{ $detail->employee->names }} {{ $detail->employee->lastnames }}
                                </option>
                            @endif
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- NUEVO PERSONAL --}}
                    <div class="col-md-5">
                        <div class="form-group">
                            <label class="font-weight-bold">Nuevo Personal</label>
                            <select name="new_employee_id" class="form-control" id="nuevo_personal">
                                <option value="">Seleccione un nuevo personal</option>
                                @foreach($employees as $emp)
                                    <option value="{{ $emp->id }}" data-type="{{ $emp->type_id }}" data-busy="{{ in_array($emp->id, $busyEmployeeIds ?? []) ? '1' : '0' }}" style="display: none;">
                                        {{ $emp->names }} {{ $emp->lastnames }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

            {{-- BOTÓN + PERSONAL --}}
            <div class="col-md-1 d-flex align-items-end">
                <button type="button" class="btn btn-success w-100" id="add_personal_change">+</button>
            </div>

        </div>


        {{-- ===========================
            TABLA DE CAMBIOS
        ============================ --}}
        <h5 class="mt-4">Cambios Registrados</h5>

        <div class="table-responsive mt-3">
            <table class="table table-bordered text-center">
                <thead class="bg-light">
                    <tr>
                        <th>Tipo de Cambio</th>
                        <th>Valor Anterior</th>
                        <th>Valor Nuevo</th>
                        <th>Notas</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody id="tabla-cambios">
                    {{-- Filas agregadas dinámicamente con JS --}}
                </tbody>
            </table>
        </div>

        {{-- Plantilla para select de motivos (oculta) --}}
        <div id="motivo_template" class="d-none">
            <select class="form-control form-control-sm motivo-select">
                <option value="">-- Seleccione un motivo --</option>
                @foreach($reasons as $reason)
                    <option value="{{ $reason->id }}">{{ $reason->name }}</option>
                @endforeach
            </select>
        </div>

    </div>

    <div class="card-footer text-right">
        <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">
            <i class="fas fa-arrow-left"></i> Cerrar
        </button>

        <button type="submit" class="btn btn-primary">
            Guardar Cambios
        </button>
    </div>
</div>

</form>