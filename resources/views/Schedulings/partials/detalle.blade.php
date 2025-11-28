<div class="mb-3">
    <h6>Datos Generales</h6>
    <table class="table table-bordered mb-2">
        <tr>
            <th>Fecha</th>
            <th>Estado</th>
            <th>Zona</th>
            <th>Turno</th>
            <th>Vehículo</th>
        </tr>
        <tr>
            <td>{{ $scheduling->date }}</td>
            <td>
                @if($scheduling->status == 0)
                    <span class="badge badge-danger">Cancelado</span>
                @elseif($scheduling->status == 2)
                    <span class="badge badge-warning">Reprogramado</span>
                @else
                    <span class="badge badge-success">Programado</span>
                @endif
            </td>
            <td>{{ $scheduling->group->zone->name ?? '-' }}</td>
            <td>{{ $scheduling->shift->name ?? '-' }}</td>
            <td>{{ $scheduling->vehicle->plate ?? '-' }}</td>
        </tr>
    </table>
</div>
<div class="mb-3">
    <h6>Personal Asignado</h6>
    <table class="table table-bordered mb-2">
        <tr>
            <th>Rol</th>
            <th>Nombre</th>
        </tr>
        @foreach($scheduling->details as $d)
            <tr>
                <td>{{ $d->employee->type->name ?? '-' }}</td>
                <td>{{ $d->employee->lastnames }} {{ $d->employee->names }}</td>
            </tr>
        @endforeach
    </table>
</div>
<div>
    <h6>Historial de Cambios</h6>
    <table class="table table-bordered">
        <tr>
            <th>Fecha del Cambio</th>
            <th>Valor Anterior</th>
            <th>Valor Nuevo</th>
            <th>Motivo</th>
        </tr>
        @forelse($changes as $c)
            <tr>
                <td>{{ $c->created_at->format('d/m/Y') }}</td>
                <td>{{ $c->old_value }}</td>
                <td>{{ $c->new_value }}</td>
                <td>{{ $c->reason->name ?? '-' }}</td>
            </tr>
        @empty
            <tr><td colspan="4" class="text-center text-muted">Sin cambios registrados</td></tr>
        @endforelse
    </table>
</div>
