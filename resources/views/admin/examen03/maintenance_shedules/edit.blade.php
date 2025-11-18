@php
	// $maintenance_id, $vehicles, $employees, $model deben ser pasados desde el controlador
@endphp

{!! Form::model($model, ['route' => ['admin.maintenance_shedules.update', $maintenance_id, $model->id], 'method' => 'PUT', 'autocomplete' => 'off']) !!}
	@include('admin.examen03.maintenance_shedules.template.form', [
		'vehicles' => $vehicles,
		'employees' => $employees,
		'model' => $model
	])
	<button type="submit" class="btn btn-primary"><i class='fas fa-save'></i> Guardar</button>
    <button type="button" class="btn btn-danger" data-dismiss="modal"> <i class="fas fa-window-close"></i> Cancelar</button>
{!! Form::close() !!}
