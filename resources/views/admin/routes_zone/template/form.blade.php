<div class="form-group">
    {!! Form::label('name', 'Nombre de la ruta') !!}
    {!! Form::text('name', null, ['class' => 'form-control', 'placeholder' => 'Nombre de la ruta', 'required']) !!}
</div>
<div class="form-group">
    {!! Form::label('zone_id', 'Zona') !!}
    {!! Form::select('zone_id', $zones, null, ['class' => 'form-control', 'placeholder' => 'Seleccione la zona', 'required']) !!}
</div>
<div class="form-group">
    {!! Form::label('status', 'Estado') !!}
    {!! Form::select('status', ['activa' => 'Activa', 'inactiva' => 'Inactiva'], null, ['class' => 'form-control', 'placeholder' => 'Seleccione el estado']) !!}
</div>
<div class="form-group">
    {!! Form::label('description', 'Descripción') !!}
    {!! Form::textarea('description', null, [
        'class' => 'form-control',
        'placeholder' => 'Detalle de la ruta',
        'rows' => 3,
    ]) !!}
</div>


<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label class="font-weight-semibold">Coordenadas de inicio</label>
            <input type="text" id="start-coords" class="form-control" value="{{ isset($start) ? ($start->latitude . ', ' . $start->longitude) : '-' }}" readonly>
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label class="font-weight-semibold">Coordenadas de fin</label>
            <input type="text" id="end-coords" class="form-control" value="{{ isset($end) ? ($end->latitude . ', ' . $end->longitude) : '-' }}" readonly>
        </div>
    </div>
</div>
<div class="form-group">
    <label class="font-weight-semibold">Distancia (km)</label>
    <input type="text" id="distance-label" class="form-control" value="{{ isset($route) ? $route->distance : '-' }}" readonly>
</div>
{!! Form::hidden('start_latitude', isset($start) ? $start->latitude : null, ['id' => 'start_latitude']) !!}
{!! Form::hidden('start_longitude', isset($start) ? $start->longitude : null, ['id' => 'start_longitude']) !!}
{!! Form::hidden('end_latitude', isset($end) ? $end->latitude : null, ['id' => 'end_latitude']) !!}
{!! Form::hidden('end_longitude', isset($end) ? $end->longitude : null, ['id' => 'end_longitude']) !!}
{!! Form::hidden('distance', isset($route) ? $route->distance : null, ['id' => 'distance']) !!}
