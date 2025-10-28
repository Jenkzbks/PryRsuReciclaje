<form action="{{ route('admin.shifts.update', $shift) }}" method="POST">
    @csrf
    @method('PUT')
    @include('admin.shifts.template.form')
    <div class="mt-3 d-flex justify-content-end">
        <button type="button" class="btn btn-secondary mr-2" data-dismiss="modal"> <i class="fas fa-window-close"></i> Cancelar</button>
        <button type="submit" class="btn btn-primary"><i class="far fa-save"></i> Guardar</button>
    </div>
</form>
