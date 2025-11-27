@extends('adminlte::page')

@section('title', 'Gestión de Zonas')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="h3 mb-1 font-weight-bold">Gestión de Zonas</h1>
            <p class="text-muted mb-0">Registro y administración de zonas geográficas.</p>
        </div>
        <div class="d-flex align-items-center gap-2">
                <a href="{{ route('admin.zonesjenkz.map') }}" class="btn btn-info mr-2">
                <i class="fas fa-map-marked-alt"></i> Mapa de Zonas
                </a>
            <a href="{{ route('admin.zonesjenkz.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Nueva Zona
            </a>
        </div>
        
    </div>

    <!-- Modal Detalle Zona -->
        <div class="modal fade" id="modalDetalleZona" tabindex="-1" role="dialog" aria-labelledby="modalDetalleZonaLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content" id="modalDetalleZonaContent">
                </div>
            </div>
        </div>
@stop

@section('content')
    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-bordered align-middle" id="zones-table" style="width:100%">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Descripción</th>
                            <th>Ubicación</th>
                            <th>Perímetro Asignado</th>
                            <th width="70px" class="text-center align-middle">Ver Detalle</th>
                            <th width="70px" class="text-center align-middle">Editar</th>
                            <th width="70px" class="text-center align-middle">Eliminar</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
@stop

@section('js')
<script>
    $(document).ready(function() {
        var table = $('#zones-table').DataTable({
            ajax: '{{ route('admin.zonesjenkz.index') }}',
            columns: [
                { data: 'name' },
                { data: 'description', defaultContent: '', render: function(data) { return data ? data : '<span class="text-muted">Sin descripción</span>'; } },
                { data: 'full_location', defaultContent: '', render: function(data) { return data ? data : '<span class="text-muted">-</span>'; } },
                { data: 'has_polygon', render: function(data) { return data ? '<span class="text-success"><i class="fas fa-check-circle"></i> Sí</span>' : '<span class="text-danger"><i class="fas fa-times-circle"></i> No</span>'; }, className: 'text-center align-middle' },
                { data: 'show', orderable: false, searchable: false, className: 'text-center align-middle' },
                { data: 'edit', orderable: false, searchable: false, className: 'text-center align-middle' },
                { data: 'delete', orderable: false, searchable: false, className: 'text-center align-middle' }
            ],
            language: {
                url: 'https://cdn.datatables.net/plug-ins/1.10.25/i18n/Spanish.json'
            }
        });

        $(document).on('submit', '.frmDelete', function(e) {
            e.preventDefault();
            var form = $(this);
            Swal.fire({
                title: "¿Estás seguro de eliminar?",
                text: "Esto no se puede deshacer!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Si, eliminar",
                cancelButtonText: "Cancelar"
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: form.attr('action'),
                        type: form.attr('method'),
                        data: form.serialize(),
                        success: function(response) {
                            table.ajax.reload(null, false);
                            Swal.fire({
                                title: "Proceso Exitoso!",
                                text: response.message || 'Zona eliminada correctamente.',
                                icon: "success"
                            });
                        },
                        error: function(response) {
                            var error = response.responseJSON;
                            Swal.fire({
                                title: "Error!",
                                text: error && error.message ? error.message : 'No se pudo eliminar la zona.',
                                icon: "error"
                            });
                        }
                    });
                }
            });
        });

        // Botón para mostrar el modal del mapa de zonas
        $('#btnMapaZonas').click(function() {
            $('#modalMapaZonas').modal('show');
            setTimeout(function() {
                if (!window._mapaZonasLoaded) {
                    // Cargar Leaflet y el mapa solo una vez
                    var leafletLoaded = typeof L !== 'undefined';
                    function renderMapaZonas() {
                        var mapDiv = document.getElementById('mapaZonasContainer');
                        if (!mapDiv) return;
                        // Limpiar contenido previo
                        mapDiv.innerHTML = '';
                        var map = L.map('mapaZonasContainer').setView([-6.7604806497116, -79.83481407165529], 15);
                        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                            maxZoom: 19,
                            attribution: '© OpenStreetMap'
                        }).addTo(map);
                        // AJAX para obtener zonas activas
                        $.getJSON("{{ route('admin.zonesjenkz.index') }}", function(resp) {
                            if (resp.data) {
                                var colors = ['#e57373','#ba68c8','#64b5f6','#4db6ac','#ffd54f','#ffb74d','#a1887f','#90a4ae','#81c784','#f06292'];
                                resp.data.forEach(function(z, i) {
                                    if (z.coords && z.coords.length > 2) {
                                        var color = colors[i % colors.length];
                                        var poly = L.polygon(z.coords, {
                                            color: color,
                                            fillColor: color,
                                            fillOpacity: 0.18,
                                            weight: 2
                                        }).addTo(map);
                                        var popup = '<b>' + (z.name || '') + '</b><br>';
                                        if (z.full_location) popup += 'Departamento: ' + z.full_location + '<br>';
                                        if (z.description) popup += z.description + '<br>';
                                        popup += (z.coords.length) + ' puntos<br>';
                                        popup += '<a href="#" class="btn btn-sm btn-info mt-2" style="pointer-events:none;opacity:0.7;">Ver detalles</a>';
                                        poly.bindPopup(popup);
                                    }
                                });
                            }
                        });
                        window._mapaZonasLoaded = true;
                    }
                    if (!leafletLoaded) {
                        $.when(
                            $.getScript('https://unpkg.com/leaflet@1.9.4/dist/leaflet.js'),
                            $('<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />').appendTo('head')
                        ).done(renderMapaZonas);
                    } else {
                        renderMapaZonas();
                    }
                }
            }, 300);
        });
            // Modal Detalle Zona (para tabla y mapa)
            $(document).on('click', '.btn-ver-detalle-zona', function(e) {
                e.preventDefault();
                var id = $(this).data('id');
                if (!id) return;
                $('#modalDetalleZonaContent').html('<div class="modal-body text-center py-5"><i class="fas fa-spinner fa-spin fa-2x"></i></div>');
                $('#modalDetalleZona').modal('show');
                $.get('/admin/zonesjenkz/' + id + '/modal', function(html) {
                    $('#modalDetalleZonaContent').html(html);
                }).fail(function() {
                    $('#modalDetalleZonaContent').html('<div class="modal-body text-danger">Error al cargar el detalle.</div>');
                });
            });
    });
</script>
@stop