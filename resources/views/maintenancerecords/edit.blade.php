@extends('adminlte::page')

@section('title', 'Editar registro de mantenimiento')

@section('content_header')
<div class="d-flex justify-content-between align-items-center">
  <h1>Editar registro de mantenimiento</h1>
  <a href="{{ route('admin.maintenances.schedules.index', $maintenance) }}"
     class="btn btn-outline-secondary">
    Volver a horarios
  </a>
</div>
@stop

@section('content')
<div class="card">
  <form method="POST"
        action="{{ route('admin.maintenances.records.update', [$maintenance, $record]) }}"
        enctype="multipart/form-data">
    @csrf
    @method('PUT')

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

      <div class="form-group">
        <label>Fecha</label>
        <input type="date"
               class="form-control"
               value="{{ $record->maintenance_date->format('Y-m-d') }}"
               disabled>
      </div>

      <div class="form-group">
        <label>Descripción</label>
        <textarea name="descripcion"
                  class="form-control"
                  rows="3"
                  placeholder="Opcional">{{ old('descripcion', $record->descripcion) }}</textarea>
      </div>

      {{-- ESTADO --}}
      <div class="form-group">
        <label>Estado *</label>
        <select name="estado" class="form-control" required>
          @php
            $currentEstado = old('estado', $record->estado ?? 'no realizado');
          @endphp
          <option value="no realizado" {{ $currentEstado === 'no realizado' ? 'selected' : '' }}>
            No realizado
          </option>
          <option value="realizado" {{ $currentEstado === 'realizado' ? 'selected' : '' }}>
            Realizado
          </option>
        </select>
      </div>

      <div class="form-group">
        <label>Imagen (opcional)</label>
        @if($record->image_url)
          <div class="mb-2">
            <img src="{{ asset('storage/'.$record->image_url) }}"
                 alt="Imagen actual"
                 style="max-width: 150px; max-height: 150px; object-fit: cover;">
          </div>
        @endif
        <input type="file" name="image" class="form-control-file">
        <small class="form-text text-muted">
          Formatos aceptados: JPG, PNG, GIF. Máx: 4 MB.
        </small>
      </div>

    </div>

    <div class="card-footer text-right">
      <a href="{{ route('admin.maintenances.schedules.index', $maintenance) }}"
         class="btn btn-outline-secondary">Cancelar</a>
      <button type="submit" class="btn btn-primary">Guardar cambios</button>
    </div>
  </form>
</div>
@stop
