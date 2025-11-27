
@php
	$dias = [
		'Lunes' => 'Lunes',
		'Martes' => 'Martes',
		'Miércoles' => 'Miércoles',
		'Jueves' => 'Jueves',
		'Viernes' => 'Viernes',
		'Sábado' => 'Sábado',
		'Domingo' => 'Domingo',
	];
	$tipos = [
		'Preventivo' => 'Preventivo',
		'Limpieza' => 'Limpieza',
		'Reparación' => 'Reparación',
	];
@endphp

<div class="form-group">
	<label for="day_of_week">Día de la semana</label>
	<select name="day_of_week" id="day_of_week" class="form-control" required>
		<option value="">Seleccione...</option>
		@foreach($dias as $dia)
			<option value="{{ $dia }}" {{ (isset($model) && $model->day_of_week == $dia) ? 'selected' : '' }}>{{ $dia }}</option>
		@endforeach
	</select>
</div>

<div class="form-group">
	<label for="vehicle_id">Vehículo</label>
	<select name="vehicle_id" id="vehicle_id" class="form-control" required>
		<option value="">Seleccione...</option>
		@foreach($vehicles as $vehiculo)
			<option value="{{ $vehiculo->id }}" {{ (isset($model) && $model->vehicle_id == $vehiculo->id) ? 'selected' : '' }}>
				{{ $vehiculo->name }}{{ $vehiculo->plate ? ' - '.$vehiculo->plate : '' }}
			</option>
		@endforeach
	</select>
</div>

<div class="form-group">
	<label for="driver_id">Responsable</label>
	<select name="driver_id" id="driver_id" class="form-control" required>
		<option value="">Seleccione...</option>
		@foreach($employees as $empleado)
			<option value="{{ $empleado->id }}" {{ (isset($model) && $model->driver_id == $empleado->id) ? 'selected' : '' }}>
				{{ $empleado->names }} {{ $empleado->lastnames }}
			</option>
		@endforeach
	</select>
</div>

<div class="form-group">
	<label for="maintenance_type">Tipo de mantenimiento</label>
	<select name="maintenance_type" id="maintenance_type" class="form-control" required>
		<option value="">Seleccione...</option>
		@foreach($tipos as $tipo)
			<option value="{{ $tipo }}" {{ (isset($model) && $model->maintenance_type == $tipo) ? 'selected' : '' }}>{{ $tipo }}</option>
		@endforeach
	</select>
</div>

<div class="form-group">
	<label for="start_time">Hora de inicio</label>
	<input type="time" name="start_time" id="start_time" class="form-control" value="{{ isset($model) ? $model->start_time : '' }}" required>
</div>

<div class="form-group">
	<label for="end_time">Hora de fin</label>
	<input type="time" name="end_time" id="end_time" class="form-control" value="{{ isset($model) ? $model->end_time : '' }}" required>
</div>
