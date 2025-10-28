<form action="{{ route('admin.personnel.employeegroups.update', $group) }}" method="POST">
    @csrf
    @method('PUT')
    @include('personnel.employeegroup.template.form')
    <div class="mt-3 d-flex justify-content-end">
        <button type="button" class="btn btn-secondary mr-2" data-dismiss="modal">Cancelar</button>
        <button type="submit" class="btn btn-primary">Guardar</button>
    </div>
</form>
