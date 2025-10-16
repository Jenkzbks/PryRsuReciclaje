<form action="{{ route('admin.brandmodels.update', $brandModel) }}" method="POST">
    @csrf
    @method('PUT')
    @include('admin.brandmodels.template.form')
    <button type="submit" class="btn btn-primary"><i class="far fa-save"></i> Actualizar</button>
    <button type="button" class="btn btn-danger" data-dismiss="modal"> <i class="fas fa-window-close"></i> Cancelar</button>
</form>