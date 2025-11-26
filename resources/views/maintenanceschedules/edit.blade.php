@extends('adminlte::page')

@section('title','Editar horario de mantenimiento')

@section('content_header')
  <h1>Editar horario - {{ $maintenance->name }}</h1>
  <small class="text-muted">Rango: {{ $maintenance->range_text }}</small>
@stop

@section('content')
<div class="card">
  <form method="POST" action="{{ route('admin.maintenances.schedules.update', [$maintenance, $schedule]) }}">
    @csrf
    @method('PUT')
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
          <label>Vehículo *</label>
          <select name="vehicle_id" class="form-control" required>
            <option value="">-- Seleccione --</option>
            @foreach($vehicles as $v)
              <option value="{{ $v->id }}" {{ old('vehicle_id', $schedule->vehicle_id) == $v->id ? 'selected' : '' }}>
                {{ $v->plate }} - {{ $v->brand->name ?? '' }} {{ $v->brandModel->name ?? '' }}
              </option>
            @endforeach
          </select>
        </div>
        <div class="form-group col-md-4">
          <label>Conductor *</label>
          <select name="driver_id" class="form-control" required>
            <option value="">-- Seleccione --</option>
            @foreach($drivers as $d)
              <option value="{{ $d->id }}" {{ old('driver_id', $schedule->driver_id) == $d->id ? 'selected' : '' }}>
                {{ $d->lastnames }} {{ $d->names }}
              </option>
            @endforeach
          </select>
        </div>
        <div class="form-group col-md-4">
          <label>Día de la semana *</label>
          <select name="day_of_week" class="form-control" required>
            <option value="">-- Seleccione --</option>
            @foreach($daysOfWeek as $key => $label)
              <option value="{{ $key }}" {{ old('day_of_week', $schedule->day_of_week) == $key ? 'selected' : '' }}>
                {{ $label }}
              </option>
            @endforeach
          </select>
        </div>
      </div>

      <div class="form-row">
        <div class="form-group col-md-3">
          <label>Hora inicio *</label>
          <input type="time" name="start_time" class="form-control"
                 value="{{ old('start_time', $schedule->start_time) }}" required>
        </div>
        <div class="form-group col-md-3">
          <label>Hora fin *</label>
          <input type="time" name="end_time" class="form-control"
                 value="{{ old('end_time', $schedule->end_time) }}" required>
        </div>
        <div class="form-group col-md-6">
          <label>Tipo de mantenimiento *</label>
          <input type="text" name="maintenance_type" class="form-control"
                 value="{{ old('maintenance_type', $schedule->maintenance_type) }}" required>
        </div>
      </div>

      <p class="text-muted mb-0">
        Al actualizar, se regenerarán los registros de fechas según el nuevo día de semana y rango.
      </p>
    </div>
    <div class="card-footer text-right">
      <a href="{{ route('admin.maintenances.schedules.index', $maintenance) }}" class="btn btn-outline-secondary">Volver</a>
      <button type="submit" class="btn btn-success">Actualizar horario</button>
    </div>
  </form>
</div>
@stop
