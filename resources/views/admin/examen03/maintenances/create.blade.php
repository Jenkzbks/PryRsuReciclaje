{!! Form::open(['route' => 'admin.maintenances.store', 'method' => 'POST']) !!}
    
    @include('admin.examen03.maintenances.template.form')
    <button type="submit" class="btn btn-success"><i class='fas fa-save'></i> Registrar</button>
    <button type="button" class="btn btn-danger" data-dismiss="modal"> <i class="fas fa-window-close"></i> Cancelar</button>
{!! Form::close() !!}
