<form action="{{ route('admin.personnel.employeegroups.update', $group) }}" method="POST">
    @csrf
    @method('PUT')
    @include('personnel.employeegroup.template.form')
    <div class="mt-3 d-flex justify-content-end">
        <button type="button" class="btn btn-danger mr-2" data-dismiss="modal">
            <i class="fas fa-ban mr-1"></i> Cancelar
        </button>
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save mr-1"></i> Guardar
        </button>
    </div>
</form>
