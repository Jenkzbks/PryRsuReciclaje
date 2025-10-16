<form action="{{ route('admin.vehicletypes.store') }}" method="POST">
    @csrf
    @include('admin.vehicletypes.template.form')
    <button type="submit" class="btn btn-success"><i class='fas fa-save'></i> Registrar</button>
    <button type="button" class="btn btn-danger" data-dismiss="modal"> <i class="fas fa-window-close"></i> Cancelar</button>
</form>