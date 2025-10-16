<form action="{{ route('admin.vehicletypes.update', $vehicleType) }}" method="POST">
    @csrf
    @method('PUT')
    @include('admin.vehicletypes.template.form')
    <button type="submit" class="btn btn-primary"><i class="far fa-save"></i> Actualizar</button>
    <button type="button" class="btn btn-danger" data-dismiss="modal"> <i class="fas fa-window-close"></i> Cancelar</button>
</form>