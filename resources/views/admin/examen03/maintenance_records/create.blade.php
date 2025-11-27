{!! Form::open(['route' => ['admin.maintenance_records.store', $maintenance_id, $schedule_id], 'method' => 'POST', 'autocomplete' => 'off', 'enctype' => 'multipart/form-data']) !!}
    @include('admin.examen03.maintenance_records.template.form')
    <button type="submit" class="btn btn-success"><i class='fas fa-save'></i> Registrar</button>
    <button type="button" class="btn btn-danger" data-dismiss="modal"> <i class="fas fa-window-close"></i> Cancelar</button>
{!! Form::close() !!}
