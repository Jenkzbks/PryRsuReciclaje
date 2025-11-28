
<div class="form-group">
    {!! Form::label('name', 'Nombre') !!}
    {!! Form::text('name', null, ['class' => 'form-control', 'required', 'maxlength' => 255]) !!}
    <span class="text-danger" id="error-name"></span>
</div>
<div class="form-group">
    {!! Form::label('start_date', 'Fecha de Inicio') !!}
    {!! Form::date('start_date', isset($maintenance) ? $maintenance->start_date : null, ['class' => 'form-control', 'required']) !!}
    <span class="text-danger" id="error-start_date"></span>
</div>
<div class="form-group">
    {!! Form::label('end_date', 'Fecha de Fin') !!}
    {!! Form::date('end_date', isset($maintenance) ? $maintenance->end_date : null, ['class' => 'form-control', 'required']) !!}
    <span class="text-danger" id="error-end_date"></span>
</div>
