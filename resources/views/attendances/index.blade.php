@extends('adminlte::page')

@section('title', 'Asistencias')

@section('content_header')
    <h1 class="m-0">Asistencias</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <form method="GET" class="mb-3">
                <div class="form-row">
                    <div class="col">
                        <input type="text" name="search" class="form-control" placeholder="Buscar empleado" value="{{ request('search') }}">
                    </div>
                    <div class="col">
                        <input type="date" name="date" class="form-control" value="{{ request('date') }}">
                    </div>
                    <div class="col-auto">
                        <button class="btn btn-primary">Filtrar</button>
                    </div>
                </div>
            </form>

            <table class="table table-sm table-striped">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Empleado</th>
                        <th>Tipo</th>
                        <th>Fecha/Hora</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($attendances as $attendance)
                        <tr>
                            <td>{{ $attendance->id }}</td>
                            <td>{{ $attendance->employee->name ?? 'N/A' }} {{ $attendance->employee->lastname ?? '' }}</td>
                            <td>{{ $attendance->type }}</td>
                            <td>{{ $attendance->check_in ? $attendance->check_in->format('d/m/Y H:i:s') : 'N/A' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center">No hay registros</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            {{ $attendances->links() }}
        </div>
    </div>
@stop
