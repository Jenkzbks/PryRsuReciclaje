@extends('adminlte::page')

@section('title','Editar Programación')

@section('content_header')
  <h1>Editar Programación</h1>
@stop

@section('content')
<div class="card">
  <form method="POST" action="{{ route('admin.schedulings.update',$scheduling) }}">
    @csrf @method('PUT')
    <div class="card-body">
      @if($errors->any())
        <div class="alert alert-danger"><ul>@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
      @endif

      <div class="alert alert-light border">
        <div><strong>Fecha:</strong> {{ $scheduling->date }}</div>
        <div><strong>Grupo:</strong> {{ $scheduling->group->name ?? '-' }}</div>
        <div><strong>Zona:</strong> {{ $scheduling->group->zone->name ?? '-' }}</div>
        <div><strong>Turno:</strong> {{ $scheduling->group->shift->name ?? '-' }}</div>
        <div><strong>Vehículo:</strong> {{ $scheduling->group->vehicle->plate ?? '-' }}</div>
        <div><strong>Notas:</strong> {{ $scheduling->notes ?? '-' }}</div>
      </div>

      <div class="row">
        <div class="col-md-4">
          <label>Conductor</label>
          <select name="driver_id" class="form-control">
            <option value="">-- Seleccione --</option>
            @foreach($drivers as $d)
              <option value="{{ $d->id }}" {{ $selectedDriverId==$d->id?'selected':'' }}>
                {{ $d->lastnames }} {{ $d->names }}
              </option>
            @endforeach
          </select>
        </div>
        <div class="col-md-4">
          <label>Ayudante 1</label>
          <select name="assistant1_id" class="form-control">
            <option value="">-- Seleccione --</option>
            @foreach($assistants as $a)
              <option value="{{ $a->id }}" {{ $selectedA1Id==$a->id?'selected':'' }}>
                {{ $a->lastnames }} {{ $a->names }}
              </option>
            @endforeach
          </select>
        </div>
        <div class="col-md-4">
          <label>Ayudante 2</label>
          <select name="assistant2_id" class="form-control">
            <option value="">-- Seleccione --</option>
            @foreach($assistants as $a)
              <option value="{{ $a->id }}" {{ $selectedA2Id==$a->id?'selected':'' }}>
                {{ $a->lastnames }} {{ $a->names }}
              </option>
            @endforeach
          </select>
        </div>
      </div>
    </div>
    <div class="card-footer text-right">
      <a href="{{ route('admin.schedulings.index') }}" class="btn btn-outline-secondary">Volver</a>
      <button class="btn btn-success"><i class="fas fa-save"></i> Guardar cambios</button>
    </div>
  </form>
</div>
@stop
