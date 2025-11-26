@extends('adminlte::page')

@section('title','Nuevo mantenimiento')

@section('content_header')
  <h1>Registrar mantenimiento</h1>
@stop

@section('content')
<div class="card">
  <form method="POST" action="{{ route('admin.maintenances.store') }}">
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
        <div class="form-group col-md-6">
          <label>Nombre *</label>
          <input type="text" name="name" class="form-control"
                 value="{{ old('name') }}" required>
        </div>
        <div class="form-group col-md-3">
          <label>Fecha inicio *</label>
          <input type="date" name="start_date" class="form-control"
                 value="{{ old('start_date') }}" required>
        </div>
        <div class="form-group col-md-3">
          <label>Fecha fin *</label>
          <input type="date" name="end_date" class="form-control"
                 value="{{ old('end_date') }}" required>
        </div>
      </div>

    </div>
    <div class="card-footer text-right">
      <a href="{{ route('admin.maintenances.index') }}" class="btn btn-outline-secondary">Volver</a>
      <button type="submit" class="btn btn-success">Guardar</button>
    </div>
  </form>
</div>
@stop
