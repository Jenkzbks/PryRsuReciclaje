@php
	// $maintenance_id, $vehicles, $employees deben ser pasados desde el controlador
@endphp

{!! Form::open(['route' => ['admin.maintenance_shedules.store', $maintenance_id], 'method' => 'POST', 'autocomplete' => 'off']) !!}
	@include('admin.examen03.maintenance_shedules.template.form', [
		'vehicles' => $vehicles,
		'employees' => $employees
	])
    <button type="submit" class="btn btn-success"><i class='fas fa-save'></i> Registrar</button>
    <button type="button" class="btn btn-danger" data-dismiss="modal"> <i class="fas fa-window-close"></i> Cancelar</button>
{!! Form::close() !!}
