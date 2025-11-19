{!! Form::model($maintenance, ['route' => ['admin.maintenances.update', $maintenance->id], 'method' => 'PUT']) !!}
    @csrf
    @include('admin.examen03.maintenances.template.form')
    <button type="submit" class="btn btn-primary"><i class='fas fa-save'></i> Guardar</button>
    <button type="button" class="btn btn-danger" data-dismiss="modal"> <i class="fas fa-window-close"></i> Cancelar</button>
{!! Form::close() !!}
