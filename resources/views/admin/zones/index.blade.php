@extends('adminlte::page')

@section('title', 'Gestión de Zonas')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="h3 mb-1 font-weight-bold">Gestión de Zonas</h1>
            <p class="text-muted mb-0">Registro y gestión de zonas de recolección, con asignación de perímetro geográfico.</p>
        </div>
        <a href="{{ route('admin.zones.create') }}" class="btn btn-dark">
            <i class="fas fa-plus"></i> Agregar Zona
        </a>
    </div>
@stop

@section('content')
    <div class="card shadow-sm">
        <div class="card-body">
            <!-- Filtros -->
            <form method="GET" action="{{ route('admin.zones.index') }}" class="mb-4">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <input type="text" 
                                   name="search" 
                                   class="form-control" 
                                   placeholder="Search"
                                   value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <select name="department_id" class="form-control" id="department_filter">
                                <option value="">Departamento</option>
                                @foreach($departments as $department)
                                    <option value="{{ $department->id }}" 
                                            {{ request('department_id') == $department->id ? 'selected' : '' }}>
                                        {{ $department->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <select name="province_id" class="form-control" id="province_filter">
                                <option value="">Provincia</option>
                                @foreach($provinces as $province)
                                    <option value="{{ $province->id }}" 
                                            {{ request('province_id') == $province->id ? 'selected' : '' }}>
                                        {{ $province->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <select name="district_id" class="form-control" id="district_filter">
                                <option value="">Distrito</option>
                                @foreach($districts as $district)
                                    <option value="{{ $district->id }}" 
                                            {{ request('district_id') == $district->id ? 'selected' : '' }}>
                                        {{ $district->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-outline-secondary">
                            <i class="fas fa-search"></i>
                        </button>
                        <a href="{{ route('admin.zones.index') }}" class="btn btn-outline-secondary ml-2">
                            <i class="fas fa-times"></i>
                        </a>
                    </div>
                </div>
            </form>

            <!-- Tabla -->
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="thead-light">
                        <tr>
                            <th>Código</th>
                            <th>Nombre de la Zona</th>
                            <th>Lugar (D/P/Ds)</th>
                            <th>Perímetro asignado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($zones as $zone)
                            <tr>
                                <td class="font-weight-bold">{{ $zone->code }}</td>
                                <td>{{ $zone->name }}</td>
                                <td>{{ $zone->full_location }}</td>
                                <td class="text-center">
                                    @if($zone->hasPolygon())
                                        <i class="fas fa-check-circle text-success" title="Perímetro asignado"></i>
                                    @else
                                        <i class="fas fa-times-circle text-danger" title="Sin perímetro"></i>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <!-- ABRE MODAL -->
                                        <a href="{{ route('admin.zones.show', $zone) }}" 
                                           class="btn btn-sm btn-outline-info btn-show-zone"
                                           data-title="Ver Zona - {{ $zone->name }}"
                                           data-url="{{ route('admin.zones.show', $zone) }}"
                                           title="Ver">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.zones.edit', $zone) }}" 
                                           class="btn btn-sm btn-outline-primary"
                                           title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form method="POST" 
                                              action="{{ route('admin.zones.destroy', $zone) }}" 
                                              class="d-inline"
                                              onsubmit="return confirm('¿Está seguro de eliminar esta zona?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="btn btn-sm btn-outline-danger"
                                                    title="Eliminar">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">
                                    No se encontraron zonas registradas.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Paginación -->
            @if($zones->hasPages())
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div class="text-muted small">
                        Mostrando {{ $zones->firstItem() }} a {{ $zones->lastItem() }} de {{ $zones->total() }} entradas
                    </div>
                    <div>
                        {{ $zones->links() }}
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Modal para ver zona -->
    <div class="modal fade" id="zoneModal" tabindex="-1" role="dialog" aria-hidden="true">
      <div class="modal-dialog modal-dialog-scrollable modal-xl" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Ver Zona</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body p-3">
            <div class="text-center text-muted py-5">Cargando…</div>
          </div>
        </div>
      </div>
    </div>
@stop

@section('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" />
    <!-- Leaflet CSS para el modal -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <style>
        body { background-color: #f9f7f7 !important; }
        .card { border: none; border-radius: 12px; }
        .table th { border-top: none; font-weight: 600; color: #495057; }
        .btn-group .btn { margin-right: 2px; }
    </style>
@stop

@section('js')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <!-- Leaflet JS para el modal -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <script>
        $(document).ready(function() {
            // Toastr
            toastr.options = {
                closeButton: true, progressBar: true, positionClass: "toast-top-right",
                showDuration: "300", hideDuration: "1000", timeOut: "5000"
            };

            // Filtros dependientes
            $('#department_filter').change(function() {
                const departmentId = $(this).val();
                $('#province_filter').html('<option value="">Provincia</option>');
                $('#district_filter').html('<option value="">Distrito</option>');
                if (departmentId) {
                    $.get(`/admin/api/provinces/${departmentId}`, function(provinces) {
                        provinces.forEach(p => $('#province_filter').append(`<option value="${p.id}">${p.name}</option>`));
                    });
                }
            });

            $('#province_filter').change(function() {
                const provinceId = $(this).val();
                $('#district_filter').html('<option value="">Distrito</option>');
                if (provinceId) {
                    $.get(`/admin/api/districts/${provinceId}`, function(districts) {
                        districts.forEach(d => $('#district_filter').append(`<option value="${d.id}">${d.name}</option>`));
                    });
                }
            });

            // Abrir modal y cargar show por AJAX
            $(document).on('click', '.btn-show-zone', function (e) {
                e.preventDefault();
                const url   = $(this).data('url');
                const title = $(this).data('title') || 'Ver Zona';
                $('#zoneModal .modal-title').text(title);
                $('#zoneModal .modal-body').html('<div class="text-center text-muted py-5">Cargando…</div>');
                $('#zoneModal').modal('show');

                $.get(url, function (html) {
                    // El controller nos devuelve SOLO la sección "content"
                    $('#zoneModal .modal-body').html(html);

                    // Si el script inline ya creó el mapa, asegura tamaño:
                    setTimeout(() => {
                        if (window.__zoneShowMap) { try { window.__zoneShowMap.invalidateSize(); } catch(e){} }
                    }, 300);
                });
            });

            // Limpieza al cerrar
            $('#zoneModal').on('hidden.bs.modal', function () {
                $('#zoneModal .modal-body').empty();
                if (window.__zoneShowMap) { try { window.__zoneShowMap.remove(); } catch(e){}; window.__zoneShowMap = null; }
            });
        });

        @if(session('success'))
            toastr.success('{{ session('success') }}');
        @endif
    </script>
@stop
