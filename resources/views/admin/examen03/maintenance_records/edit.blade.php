{!! Form::model($model, ['route' => ['admin.maintenance_records.update', $maintenance_id, $schedule_id, $model->id], 'method' => 'PUT', 'autocomplete' => 'off', 'enctype' => 'multipart/form-data']) !!}
    @include('admin.examen03.maintenance_records.template.form', ['model' => $model])
    <button type="submit" class="btn btn-primary"><i class='fas fa-save'></i> Guardar</button>
    <button type="button" class="btn btn-danger" data-dismiss="modal"> <i class="fas fa-window-close"></i> Cancelar</button>
{!! Form::close() !!}
