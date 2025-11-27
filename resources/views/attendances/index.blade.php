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
                        <th>Fecha</th>
                        <th>Entrada</th>
                        <th>Salida</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($attendances as $attendance)
                        <tr>
                            <td>{{ $attendance->id }}</td>
                            <td>{{ $attendance->employee->names ?? 'N/A' }} {{ $attendance->employee->lastnames ?? '' }}</td>
                            <td>{{ $attendance->date ? $attendance->date->format('d/m/Y') : 'N/A' }}</td>
                            <td>{{ $attendance->check_in ? $attendance->check_in->format('H:i:s') : 'N/A' }}</td>
                            <td>{{ $attendance->check_out ? $attendance->check_out->format('H:i:s') : 'N/A' }}</td>
                            <td>
                                @php
                                    $statusColors = [
                                        'present' => 'success',
                                        'late' => 'warning', 
                                        'absent' => 'danger',
                                        'half_day' => 'info'
                                    ];
                                    $statusLabels = [
                                        'present' => 'Presente',
                                        'late' => 'Tarde',
                                        'absent' => 'Ausente', 
                                        'half_day' => 'Medio Día'
                                    ];
                                    $color = $statusColors[$attendance->status] ?? 'secondary';
                                    $label = $statusLabels[$attendance->status] ?? $attendance->status;
                                @endphp
                                <span class="badge badge-{{ $color }}">{{ $label }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">No hay registros</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            {{ $attendances->links() }}
        </div>
    </div>
@stop
