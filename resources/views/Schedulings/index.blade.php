@extends('adminlte::page')

@section('title', 'Programaciones')

@section('content_header')
<div class="d-flex justify-content-between align-items-center">
  <h1>Programaciones</h1>
  <a href="{{ route('admin.schedulings.create') }}" class="btn btn-primary">
    <i class="fas fa-plus"></i> Nueva Programación
  </a>
</div>
@stop

@section('content')
<div class="card">
  <div class="card-body">
    <form method="GET" class="mb-3">
      <div class="form-row">
        <div class="col-md-3">
          <label>Fecha inicio</label>
          <input type="date" name="from" class="form-control" value="{{ $from }}">
        </div>
        <div class="col-md-3">
          <label>Fecha fin</label>
          <input type="date" name="to" class="form-control" value="{{ $to }}">
        </div>
        <div class="col-md-3 d-flex align-items-end">
          <button class="btn btn-outline-secondary"><i class="fas fa-filter"></i> Filtrar</button>
        </div>
      </div>
    </form>

    @if(session('success'))
      <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="table-responsive">
      <table class="table table-striped">
        <thead class="thead-light">
          <tr>
            <th>Fecha</th>
            <th>Zona</th>
            <th>Turno</th>
            <th>Vehículo</th>
            <th>Grupo</th>
            <th>Notas</th>
            <th width="10"></th>
          </tr>
        </thead>
        <tbody>
          @forelse($schedulings as $s)
            <tr>
              <td>{{ $s->date }}</td>
              <td>{{ $s->zone->name ?? '-' }}</td>
              <td>{{ $s->shift->name ?? '-' }}</td>
              <td>{{ $s->vehicle->plate ?? '-' }}</td>
              <td>{{ $s->group->name ?? '-' }}</td>
              <td>{{ $s->notes }}</td>
              <td class="d-flex">
                <a href="{{ route('admin.schedulings.edit',$s) }}" class="btn btn-sm btn-outline-primary mr-2">
                  <i class="fas fa-edit"></i>
                </a>
                <form method="POST" action="{{ route('admin.schedulings.destroy',$s) }}" onsubmit="return confirm('¿Eliminar programación?')">
                  @csrf @method('DELETE')
                  <button class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                </form>
              </td>
            </tr>
          @empty
            <tr><td colspan="8" class="text-center text-muted">Sin resultados</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <div class="mt-3">{{ $schedulings->appends(request()->query())->links() }}</div>
  </div>
</div>
@stop
