@extends('adminlte::page')

@section('title', 'Gestión de Asistencias')

@section('content_header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1>
                <i class="fas fa-clock"></i> Gestión de Asistencias
            </h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.personnel.dashboard') }}">Personal</a></li>
                <li class="breadcrumb-item active">Asistencias</li>
            </ol>
        </div>
    </div>
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Lista de Asistencias</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.personnel.attendances.dashboard') }}" class="btn btn-sm btn-info">
                            <i class="fas fa-chart-bar"></i> Dashboard
                        </a>
                        <a href="{{ route('admin.personnel.attendances.create') }}" class="btn btn-sm btn-primary">
                            <i class="fas fa-plus"></i> Registrar Asistencia
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Filtros -->
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="filter_employee">Empleado:</label>
                                <select id="filter_employee" class="form-control">
                                    <option value="">Todos los empleados</option>
                                    @if(isset($employees))
                                        @foreach($employees as $employee)
                                            <option value="{{ $employee->id }}">{{ $employee->names }} {{ $employee->lastnames }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="filter_date_from">Fecha desde:</label>
                                <input type="date" id="filter_date_from" class="form-control" value="{{ date('Y-m-01') }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="filter_date_to">Fecha hasta:</label>
                                <input type="date" id="filter_date_to" class="form-control" value="{{ date('Y-m-d') }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="filter_status">Estado:</label>
                                <select id="filter_status" class="form-control">
                                    <option value="">Todos los estados</option>
                                    <option value="present">Presente</option>
                                    <option value="late">Tarde</option>
                                    <option value="absent">Ausente</option>
                                    <option value="half_day">Medio Día</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-12">
                            <button type="button" class="btn btn-primary" onclick="applyFilters()">
                                <i class="fas fa-search"></i> Buscar
                            </button>
                            <button type="button" class="btn btn-secondary" onclick="clearFilters()">
                                <i class="fas fa-eraser"></i> Limpiar
                            </button>
                            <button type="button" class="btn btn-success" onclick="exportToExcel()">
                                <i class="fas fa-file-excel"></i> Exportar
                            </button>
                        </div>
                    </div>

                    <!-- Tabla de Asistencias -->
                    <div class="table-responsive">
                        <table id="attendances-table" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Foto</th>
                                    <th>Empleado</th>
                                    <th>Fecha</th>
                                    <th>Entrada</th>
                                    <th>Salida</th>
                                    <th>Horas</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(isset($attendances) && $attendances->count() > 0)
                                    @foreach($attendances as $attendance)
                                        <tr>
                                            <td>{{ $attendance->id }}</td>
                                            <td>
                                                @if($attendance->employee && $attendance->employee->photo)
                                                    <img src="{{ asset('storage/' . $attendance->employee->photo) }}" 
                                                         alt="Foto" class="img-circle" width="40" height="40">
                                                @else
                                                    <img src="{{ asset('vendor/adminlte/dist/img/avatar.png') }}" 
                                                         alt="Sin foto" class="img-circle" width="40" height="40">
                                                @endif
                                            </td>
                                            <td>
                                                @if($attendance->employee)
                                                    {{ $attendance->employee->names }} {{ $attendance->employee->lastnames }}<br>
                                                    <small class="text-muted">{{ $attendance->employee->employee_code }}</small>
                                                @else
                                                    <span class="text-muted">Empleado no encontrado</span>
                                                @endif
                                            </td>
                                            <td>{{ \Carbon\Carbon::parse($attendance->date ?? '')->format('d/m/Y') }}</td>
                                            <td>{{ $attendance->check_in ? \Carbon\Carbon::parse($attendance->check_in)->format('H:i') : '-' }}</td>
                                            <td>{{ $attendance->check_out ? \Carbon\Carbon::parse($attendance->check_out)->format('H:i') : '-' }}</td>
                                            <td>{{ $attendance->hours_worked ?? '0' }} hrs</td>
                                            <td>
                                                @switch($attendance->status ?? 'present')
                                                    @case('present')
                                                        <span class="badge badge-success">Presente</span>
                                                        @break
                                                    @case('late')
                                                        <span class="badge badge-warning">Tarde</span>
                                                        @break
                                                    @case('absent')
                                                        <span class="badge badge-danger">Ausente</span>
                                                        @break
                                                    @case('half_day')
                                                        <span class="badge badge-info">Medio Día</span>
                                                        @break
                                                    @default
                                                        <span class="badge badge-secondary">{{ ucfirst($attendance->status ?? 'N/A') }}</span>
                                                @endswitch
                                            </td>
                                            <td>
                                                <div class="btn-group">
                                                    <a href="{{ route('admin.personnel.attendances.show', $attendance->id) }}" 
                                                       class="btn btn-sm btn-info" title="Ver detalles">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('admin.personnel.attendances.edit', $attendance->id) }}" 
                                                       class="btn btn-sm btn-warning" title="Editar">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <button type="button" class="btn btn-sm btn-danger" 
                                                            onclick="deleteAttendance({{ $attendance->id }})" title="Eliminar">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="9" class="text-center">
                                            <div class="alert alert-info">
                                                <i class="fas fa-info-circle"></i>
                                                No hay registros de asistencia disponibles.
                                            </div>
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>

                    <!-- Paginación -->
                    @if(isset($attendances) && method_exists($attendances, 'links'))
                        <div class="d-flex justify-content-center">
                            {{ $attendances->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    <link rel="stylesheet" href="{{ asset('vendor/adminlte/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/adminlte/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
@stop

@section('js')
    <script src="{{ asset('vendor/adminlte/plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('vendor/adminlte/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('vendor/adminlte/plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('vendor/adminlte/plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>

    <script>
        $(document).ready(function() {
            $('#attendances-table').DataTable({
                responsive: true,
                lengthChange: false,
                autoWidth: false,
                searching: false,
                paging: false,
                info: false,
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.10.24/i18n/Spanish.json'
                }
            });
        });

        function applyFilters() {
            const employee = $('#filter_employee').val();
            const dateFrom = $('#filter_date_from').val();
            const dateTo = $('#filter_date_to').val();
            const status = $('#filter_status').val();

            let url = '{{ route("admin.personnel.attendances.index") }}?';
            const params = [];

            if (employee) params.push('employee_id=' + employee);
            if (dateFrom) params.push('date_from=' + dateFrom);
            if (dateTo) params.push('date_to=' + dateTo);
            if (status) params.push('status=' + status);

            url += params.join('&');
            window.location.href = url;
        }

        function clearFilters() {
            $('#filter_employee').val('');
            $('#filter_date_from').val('{{ date("Y-m-01") }}');
            $('#filter_date_to').val('{{ date("Y-m-d") }}');
            $('#filter_status').val('');
        }

        function exportToExcel() {
            const employee = $('#filter_employee').val();
            const dateFrom = $('#filter_date_from').val();
            const dateTo = $('#filter_date_to').val();
            const status = $('#filter_status').val();

            let url = '{{ route("admin.personnel.attendances.index") }}?export=excel';
            const params = [];

            if (employee) params.push('employee_id=' + employee);
            if (dateFrom) params.push('date_from=' + dateFrom);
            if (dateTo) params.push('date_to=' + dateTo);
            if (status) params.push('status=' + status);

            if (params.length > 0) {
                url += '&' + params.join('&');
            }

            window.open(url, '_blank');
        }

        function deleteAttendance(id) {
            Swal.fire({
                title: '¿Estás seguro?',
                text: "Esta acción no se puede deshacer",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = '{{ route("admin.personnel.attendances.destroy", ":id") }}'.replace(':id', id);
                    
                    const csrfToken = document.createElement('input');
                    csrfToken.type = 'hidden';
                    csrfToken.name = '_token';
                    csrfToken.value = '{{ csrf_token() }}';
                    
                    const methodField = document.createElement('input');
                    methodField.type = 'hidden';
                    methodField.name = '_method';
                    methodField.value = 'DELETE';
                    
                    form.appendChild(csrfToken);
                    form.appendChild(methodField);
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        }
    </script>
@stop