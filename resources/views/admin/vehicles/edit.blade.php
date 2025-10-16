{!! Form::model($vehicle, ['route' => ['admin.vehicles.update', $vehicle->id], 'method' => 'PUT', 'files' => true, 'id' => 'vehicleForm']) !!}
@include('admin.vehicles.template.form')
<button type="submit" class="btn btn-primary far fa-save"> Actualizar</button>
<button type="button" class="btn btn-danger" data-dismiss="modal"> <i class="fas fa-window-close"></i> Cancelar</button>
{!! Form::close() !!}
