@extends('adminlte::page')

@section('title', 'Tipos de Empleado')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="h3 mb-1 font-weight-bold">
                <i class="fas fa-tags text-info"></i> Tipos de Empleado
            </h1>
            <p class="text-muted mb-0">Gestión de categorías y roles del personal</p>
        </div>
        <a href="{{ route('personnel.employee-types.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Nuevo Tipo
        </a>
    </div>
@stop

@section('content')
    <div class="card shadow-sm">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-list"></i> Tipos de Empleado 
                <span class="badge badge-primary">{{ $employeeTypes->total() }}</span>
            </h3>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="thead-light">
                        <tr>
                            <th>Nombre</th>
                            <th>Descripción</th>
                            <th>Empleados</th>
                            <th>Estado</th>
                            <th>Creado</th>
                            <th width="200">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($employeeTypes as $type)
                            <tr>
                                <td>
                                    <strong>{{ $type->name }}</strong>
                                    @if($type->protected)
                                        <i class="fas fa-shield-alt text-warning ml-1" title="Tipo protegido"></i>
                                    @endif
                                </td>
                                <td>{{ $type->description ?? 'Sin descripción' }}</td>
                                <td>
                                    <span class="badge badge-info">{{ $type->employees_count }} empleados</span>
                                </td>
                                <td>
                                    @if($type->protected)
                                        <span class="badge badge-warning">Protegido</span>
                                    @else
                                        <span class="badge badge-success">Normal</span>
                                    @endif
                                </td>
                                <td>{{ $type->created_at->format('d/m/Y') }}</td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('personnel.employee-types.show', $type) }}" 
                                           class="btn btn-outline-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('personnel.employee-types.edit', $type) }}" 
                                           class="btn btn-outline-primary">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button class="btn btn-outline-secondary" 
                                                onclick="duplicateType({{ $type->id }})">
                                            <i class="fas fa-copy"></i>
                                        </button>
                                        @if(!$type->protected)
                                            <button class="btn btn-outline-danger" 
                                                    onclick="deleteType({{ $type->id }}, '{{ $type->name }}')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4">
                                    <div class="text-muted">
                                        <i class="fas fa-tags fa-2x mb-3"></i>
                                        <h5>No hay tipos de empleado</h5>
                                        <a href="{{ route('personnel.employee-types.create') }}" class="btn btn-primary">
                                            <i class="fas fa-plus"></i> Crear Primer Tipo
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer">
            {{ $employeeTypes->links() }}
        </div>
    </div>
@stop

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function duplicateType(typeId) {
            $.post(`/personnel/employee-types/${typeId}/duplicate`, {
                _token: '{{ csrf_token() }}'
            }).done(function() {
                location.reload();
            });
        }

        function deleteType(typeId, typeName) {
            Swal.fire({
                title: '¿Eliminar tipo?',
                text: `¿Eliminar "${typeName}"?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                confirmButtonText: 'Sí, eliminar'
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = `/personnel/employee-types/${typeId}`;
                    form.innerHTML = '@csrf @method("DELETE")';
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        }
    </script>
@stop