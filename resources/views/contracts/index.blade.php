@extends('adminlte::page')

@section('title', 'Contratos')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="h3 mb-1 font-weight-bold">
                <i class="fas fa-file-contract text-primary"></i> Gestión de Contratos
            </h1>
            <p class="text-muted mb-0">Administración de contratos laborales del personal</p>
        </div>
        <div class="btn-group">
            <a href="{{ route('personnel.contracts.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Nuevo Contrato
            </a>
            <button type="button" class="btn btn-info" onclick="exportContracts()">
                <i class="fas fa-file-excel"></i> Exportar
            </button>
        </div>
    </div>
@stop

@section('content')
    <!-- Filtros de Búsqueda -->
    <div class="card shadow-sm">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-filter"></i> Filtros de Búsqueda
            </h3>
            <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                    <i class="fas fa-minus"></i>
                </button>
            </div>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('personnel.contracts.index') }}" id="filterForm">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="search">Búsqueda General</label>
                            <input type="text" 
                                   name="search" 
                                   id="search"
                                   class="form-control" 
                                   placeholder="Empleado, DNI..."
                                   value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="employee_id">Empleado</label>
                            <select name="employee_id" id="employee_id" class="form-control">
                                <option value="">Todos los empleados</option>
                                @foreach($employees as $employee)
                                    <option value="{{ $employee->id }}" 
                                            {{ request('employee_id') == $employee->id ? 'selected' : '' }}>
                                        {{ $employee->names }} {{ $employee->lastnames }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="contract_type">Tipo de Contrato</label>
                            <select name="contract_type" id="contract_type" class="form-control">
                                <option value="">Todos los tipos</option>
                                @foreach($contractTypes as $key => $type)
                                    <option value="{{ $key }}" 
                                            {{ request('contract_type') == $key ? 'selected' : '' }}>
                                        {{ $type }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="status">Estado</label>
                            <select name="status" id="status" class="form-control">
                                <option value="">Todos los estados</option>
                                <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Activo</option>
                                <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>Inactivo</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="start_date_from">Fecha Inicio Desde</label>
                            <input type="date" 
                                   name="start_date_from" 
                                   id="start_date_from"
                                   class="form-control" 
                                   value="{{ request('start_date_from') }}">
                        </div>
                    </div>
                    <div class="col-md-1">
                        <div class="form-group">
                            <label>&nbsp;</label>
                            <div class="d-flex">
                                <button type="submit" class="btn btn-primary btn-sm mr-1">
                                    <i class="fas fa-search"></i>
                                </button>
                                <a href="{{ route('personnel.contracts.index') }}" class="btn btn-secondary btn-sm">
                                    <i class="fas fa-times"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabla de Contratos -->
    <div class="card shadow-sm">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-list"></i> Lista de Contratos
            </h3>
            <div class="card-tools">
                <span class="text-muted">
                    Mostrando {{ $contracts->firstItem() ?? 0 }} - {{ $contracts->lastItem() ?? 0 }} de {{ $contracts->total() }}
                </span>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="thead-light">
                        <tr>
                            <th>Empleado</th>
                            <th>Tipo de Contrato</th>
                            <th>Cargo</th>
                            <th>Fecha Inicio</th>
                            <th>Fecha Fin</th>
                            <th>Salario</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($contracts as $contract)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        @if($contract->employee->photo)
                                            <img src="{{ asset('storage/' . $contract->employee->photo) }}" 
                                                 class="rounded-circle mr-2" 
                                                 width="30" height="30"
                                                 style="object-fit: cover;">
                                        @else
                                            <div class="bg-secondary rounded-circle d-flex align-items-center justify-content-center mr-2" 
                                                 style="width: 30px; height: 30px;">
                                                <i class="fas fa-user text-white" style="font-size: 12px;"></i>
                                            </div>
                                        @endif
                                        <div>
                                            <strong>{{ $contract->employee->names }} {{ $contract->employee->lastnames }}</strong>
                                            <br>
                                            <small class="text-muted">DNI: {{ $contract->employee->dni }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge badge-info">{{ $contract->contract_type_name }}</span>
                                </td>
                                <td>{{ $contract->position->name ?? 'No especificado' }}</td>
                                <td>{{ $contract->start_date ? $contract->start_date->format('d/m/Y') : 'N/A' }}</td>
                                <td>
                                    @if($contract->end_date)
                                        {{ $contract->end_date ? $contract->end_date->format('d/m/Y') : 'Indefinido' }}
                                        @if($contract->end_date->isPast())
                                            <span class="badge badge-warning">Vencido</span>
                                        @elseif($contract->end_date->diffInDays(now()) <= 30)
                                            <span class="badge badge-warning">Por vencer</span>
                                        @endif
                                    @else
                                        <span class="text-muted">Indefinido</span>
                                    @endif
                                </td>
                                <td>
                                    <strong>S/. {{ number_format($contract->salary, 2) }}</strong>
                                </td>
                                <td>
                                    @if($contract->is_active)
                                        <span class="badge badge-success">Activo</span>
                                    @else
                                        <span class="badge badge-danger">Inactivo</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('personnel.contracts.show', $contract) }}" 
                                           class="btn btn-info btn-sm">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('personnel.contracts.edit', $contract) }}" 
                                           class="btn btn-warning btn-sm">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        @if($contract->is_active)
                                            <button class="btn btn-secondary btn-sm" 
                                                    onclick="deactivateContract({{ $contract->id }})">
                                                <i class="fas fa-pause"></i>
                                            </button>
                                        @else
                                            <button class="btn btn-success btn-sm" 
                                                    onclick="activateContract({{ $contract->id }})">
                                                <i class="fas fa-play"></i>
                                            </button>
                                        @endif
                                        <button class="btn btn-danger btn-sm" 
                                                onclick="deleteContract({{ $contract->id }}, '{{ $contract->employee->names }} {{ $contract->employee->lastnames }}')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-4">
                                    <div class="text-muted">
                                        <i class="fas fa-file-contract fa-3x mb-3"></i>
                                        <h4>No hay contratos registrados</h4>
                                        <p>Comienza creando tu primer contrato laboral</p>
                                        <a href="{{ route('personnel.contracts.create') }}" class="btn btn-primary">
                                            <i class="fas fa-plus"></i> Crear Primer Contrato
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($contracts->hasPages())
            <div class="card-footer">
                {{ $contracts->links() }}
            </div>
        @endif
    </div>
@stop

@section('css')
    <style>
        .table th {
            border-top: none;
            font-weight: 600;
            color: #495057;
        }
        .badge {
            font-size: 0.75em;
        }
        .btn-group .btn {
            padding: 0.25rem 0.5rem;
        }
    </style>
@stop

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function activateContract(contractId) {
            Swal.fire({
                title: '¿Activar contrato?',
                text: 'Esta acción desactivará otros contratos activos del empleado.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#dc3545',
                confirmButtonText: 'Sí, activar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`/personnel/contracts/${contractId}/activate`, {
                        method: 'PATCH',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Content-Type': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire('¡Activado!', data.message, 'success')
                                .then(() => window.location.reload());
                        } else {
                            Swal.fire('Error', data.message, 'error');
                        }
                    })
                    .catch(() => {
                        Swal.fire('Error', 'No se pudo activar el contrato', 'error');
                    });
                }
            });
        }

        function deactivateContract(contractId) {
            Swal.fire({
                title: '¿Desactivar contrato?',
                text: 'El contrato se marcará como inactivo.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ffc107',
                cancelButtonColor: '#dc3545',
                confirmButtonText: 'Sí, desactivar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`/personnel/contracts/${contractId}/deactivate`, {
                        method: 'PATCH',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Content-Type': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire('¡Desactivado!', data.message, 'success')
                                .then(() => window.location.reload());
                        } else {
                            Swal.fire('Error', data.message, 'error');
                        }
                    })
                    .catch(() => {
                        Swal.fire('Error', 'No se pudo desactivar el contrato', 'error');
                    });
                }
            });
        }

        function deleteContract(contractId, employeeName) {
            Swal.fire({
                title: '¿Eliminar contrato?',
                html: `¿Estás seguro de que deseas eliminar el contrato de <strong>${employeeName}</strong>?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`/personnel/contracts/${contractId}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Content-Type': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire('¡Eliminado!', data.message, 'success')
                                .then(() => window.location.reload());
                        } else {
                            Swal.fire('Error', data.message, 'error');
                        }
                    })
                    .catch(() => {
                        Swal.fire('Error', 'No se pudo eliminar el contrato', 'error');
                    });
                }
            });
        }

        function exportContracts() {
            window.location.href = '/personnel/contracts/export?' + new URLSearchParams(new FormData(document.getElementById('filterForm')));
        }
    </script>
@stop