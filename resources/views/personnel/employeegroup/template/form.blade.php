<div class="row">
    <div class="col-12">
        <div class="form-row">
            <div class="form-group col-md-6">
                <label for="name">Nombre del grupo <span class="text-danger">*</span></label>
                <input type="text" name="name" id="name" class="form-control" placeholder="Ingrese el nombre del grupo" value="{{ isset($group) ? $group->name : old('name') }}" required>
            </div>
            <div class="form-group col-md-6">
                <label for="zone_id">Zona <span class="text-danger">*</span></label>
                <select name="zone_id" id="zone_id" class="form-control" required>
                    <option value="">-- Seleccione --</option>
                    @foreach($zones as $zone)
                        <option value="{{ $zone->id }}" {{ (isset($group) && $group->zone_id == $zone->id) ? 'selected' : '' }}>{{ $zone->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group col-md-6">
                <label for="shift_id">Turno <span class="text-danger">*</span></label>
                <select name="shift_id" id="shift_id" class="form-control" required>
                    <option value="">-- Seleccione --</option>
                    @foreach($shifts as $shift)
                        <option value="{{ $shift->id }}" {{ (isset($group) && $group->shift_id == $shift->id) ? 'selected' : '' }}>{{ $shift->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group col-md-6">
                <label for="vehicle_id">Vehículo <span class="text-danger">*</span></label>
                <select name="vehicle_id" id="vehicle_id" class="form-control" required>
                    <option value="">-- Seleccione --</option>
                    @foreach($vehicles as $vehicle)
                        <option value="{{ $vehicle->id }}" data-passengers="{{ $vehicle->passengers ?? 0 }}" {{ (isset($group) && $group->vehicle_id == $vehicle->id) ? 'selected' : '' }}>{{ $vehicle->plate }} (Capacidad: {{ $vehicle->passengers ?? 'N/A' }})</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="form-group">
            <label>Días de trabajo <span class="text-danger">*</span></label>
            @php
                $selectedDays = isset($group) ? explode(',', $group->days) : (old('days') ?? []);
            @endphp
            <div>
                @foreach(['Lunes','Martes','Miércoles','Jueves','Viernes','Sábado'] as $d)
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="checkbox" name="days[]" id="day_{{ $d }}" value="{{ $d }}" {{ in_array($d, $selectedDays) ? 'checked' : '' }}>
                        <label class="form-check-label" for="day_{{ $d }}">{{ $d }}</label>
                    </div>
                @endforeach
            </div>
        </div>


        <hr>
        <div id="dynamic-crew-block" style="display:none;">
            <p class="text-muted">Estos datos son para pre configuración no son obligatorios</p>
            <div id="dynamic-crew"></div>
        </div>
  
        {{-- Input oculto para pasar empleados al JS principal --}}

        <input type="hidden" id="conductores-data" value='@json($conductores)'>
        <input type="hidden" id="ayudantes-data" value='@json($ayudantes)'>
        @if(isset($crewConfig))
            <input type="hidden" id="crew-config" value='@json($crewConfig)'>
        @endif

    </div>
</div>
