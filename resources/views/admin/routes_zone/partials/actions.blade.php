<div class="btn-group" role="group">
    <a href="{{ route('admin.routes.show', $route) }}" class="btn btn-sm btn-outline-info" title="Ver">
        <i class="fas fa-eye"></i>
    </a>
    <a href="{{ route('admin.routes.edit', $route) }}" class="btn btn-sm btn-outline-primary" title="Editar">
        <i class="fas fa-edit"></i>
    </a>
    <form action="{{ route('admin.routes.destroy', $route) }}" method="POST" class="d-inline frmDelete">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-sm btn-outline-danger" title="Eliminar">
            <i class="fas fa-trash"></i>
        </button>
    </form>
</div>