@extends('adminlte::page')

@section('title','Nuevo horario de mantenimiento')

@section('content_header')
  <h1>Nuevo horario - {{ $maintenance->name }}</h1>
  <small class="text-muted">Rango: {{ $maintenance->range_text }}</small>
@stop

@section('content')
<div class="card">
  <form method="POST" action="{{ route('admin.maintenances.schedules.store', $maintenance) }}">
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
          <label>Vehículo *</label>
          <select name="vehicle_id" class="form-control" required>
            <option value="">-- Seleccione --</option>
            @foreach($vehicles as $v)
              <option value="{{ $v->id }}" {{ old('vehicle_id') == $v->id ? 'selected' : '' }}>
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
              <option value="{{ $d->id }}" {{ old('driver_id') == $d->id ? 'selected' : '' }}>
                {{ $d->lastnames }} {{ $d->names }}
              </option>
            @endforeach
          </select>
        </div>
        <div class="form-group col-md-4">
    <label>Día de la semana *</label>
    <select name="day_of_week" class="form-control" required>
        <option value="">-- Seleccione --</option>

        <option value="lunes"     {{ old('day_of_week') == 'lunes' ? 'selected' : '' }}>Lunes</option>
        <option value="martes"    {{ old('day_of_week') == 'martes' ? 'selected' : '' }}>Martes</option>
        <option value="miercoles" {{ old('day_of_week') == 'miercoles' ? 'selected' : '' }}>Miércoles</option>
        <option value="jueves"    {{ old('day_of_week') == 'jueves' ? 'selected' : '' }}>Jueves</option>
        <option value="viernes"   {{ old('day_of_week') == 'viernes' ? 'selected' : '' }}>Viernes</option>
        <option value="sabado"    {{ old('day_of_week') == 'sabado' ? 'selected' : '' }}>Sábado</option>
        <option value="domingo"   {{ old('day_of_week') == 'domingo' ? 'selected' : '' }}>Domingo</option>

    </select>
</div>

      </div>

      <div class="form-row">
        <div class="form-group col-md-3">
          <label>Hora inicio *</label>
          <input type="time" name="start_time" class="form-control"
                 value="{{ old('start_time') }}" required>
        </div>
        <div class="form-group col-md-3">
          <label>Hora fin *</label>
          <input type="time" name="end_time" class="form-control"
                 value="{{ old('end_time') }}" required>
        </div>
        <div class="form-group col-md-6">
  <label>Tipo de mantenimiento *</label>
  <select name="maintenance_type" class="form-control" required>
    <option value="">-- Seleccione --</option>
    <option value="preventivo" {{ old('maintenance_type') == 'preventivo' ? 'selected' : '' }}>
      Preventivo
    </option>
    <option value="limpieza" {{ old('maintenance_type') == 'limpieza' ? 'selected' : '' }}>
      Limpieza
    </option>
    <option value="reparacion" {{ old('maintenance_type') == 'reparacion' ? 'selected' : '' }}>
      Reparación
    </option>
  </select>
</div>

      </div>

      <p class="text-muted mb-0">
        Al guardar, se crearán registros en todas las fechas dentro del rango
        <strong>{{ $maintenance->range_text }}</strong> que coincidan con el día seleccionado.
      </p>
    </div>
    <div class="card-footer text-right">
      <a href="{{ route('admin.maintenances.schedules.index', $maintenance) }}" class="btn btn-outline-secondary">Volver</a>
      <button type="submit" class="btn btn-success">Guardar horario</button>
    </div>
  </form>
</div>
@stop
