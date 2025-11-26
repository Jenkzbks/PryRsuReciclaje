@extends('adminlte::page')

@section('title','Editar mantenimiento')

@section('content_header')
  <h1>Editar mantenimiento</h1>
@stop

@section('content')
<div class="card">
  <form method="POST" action="{{ route('admin.maintenances.update', $maintenance) }}">
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
        <div class="form-group col-md-6">
          <label>Nombre *</label>
          <input type="text" name="name" class="form-control"
                 value="{{ old('name', $maintenance->name) }}" required>
        </div>
        <div class="form-group col-md-3">
          <label>Fecha inicio *</label>
          <input type="date" name="start_date" class="form-control"
                 value="{{ old('start_date', optional($maintenance->start_date)->format('Y-m-d')) }}" required>
        </div>
        <div class="form-group col-md-3">
          <label>Fecha fin *</label>
          <input type="date" name="end_date" class="form-control"
                 value="{{ old('end_date', optional($maintenance->end_date)->format('Y-m-d')) }}" required>
        </div>
      </div>

    </div>
    <div class="card-footer text-right">
      <a href="{{ route('admin.maintenances.index') }}" class="btn btn-outline-secondary">Volver</a>
      <button type="submit" class="btn btn-success">Actualizar</button>
    </div>
  </form>
</div>
@stop
