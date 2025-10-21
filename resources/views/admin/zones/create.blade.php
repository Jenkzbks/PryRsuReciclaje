@extends('adminlte::page')

@section('title', 'Agregar Zona')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="h3 mb-1 font-weight-bold">Agregar Zona</h1>
            <p class="text-muted mb-0">Llene el formulario para agregar la zona.</p>
        </div>
        <a href="{{ route('admin.zones.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left"></i> Regresar
        </a>
    </div>
@stop

@section('content')
    <form method="POST" action="{{ route('admin.zones.store') }}" id="zoneForm">
        @csrf
        <div class="row">
            <!-- Formulario -->
            <div class="col-md-5">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="form-group">
                            <label for="name" class="font-weight-semibold">Nombre de la zona *</label>
                            <input type="text" 
                                   name="name" 
                                   id="name"
                                   class="form-control @error('name') is-invalid @enderror" 
                                   value="{{ old('name') }}"
                                   required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="department_id" class="font-weight-semibold">Departamento *</label>
                            <select name="department_id" 
                                    id="department_id" 
                                    class="form-control @error('department_id') is-invalid @enderror" 
                                    required>
                                <option value="">Seleccione un departamento</option>
                                @foreach($departments as $department)
                                    <option value="{{ $department->id }}" 
                                            {{ old('department_id') == $department->id ? 'selected' : '' }}>
                                        {{ $department->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('department_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="province_id" class="font-weight-semibold">Provincia *</label>
                            <select name="province_id" 
                                    id="province_id" 
                                    class="form-control @error('province_id') is-invalid @enderror" 
                                    required disabled>
                                <option value="">Seleccione una provincia</option>
                            </select>
                            @error('province_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="district_id" class="font-weight-semibold">Distrito</label>
                            <select name="district_id" 
                                    id="district_id" 
                                    class="form-control @error('district_id') is-invalid @enderror" 
                                    disabled>
                                <option value="">Sin distritos disponibles</option>
                            </select>
                            <small class="text-muted">Opcional: Solo si la provincia tiene distritos registrados</small>
                            @error('district_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="description" class="font-weight-semibold">Descripción</label>
                            <textarea name="description" 
                                      id="description" 
                                      class="form-control @error('description') is-invalid @enderror" 
                                      rows="3">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Información del polígono -->
                        <div class="row">
                            <div class="col-6">
                                <div class="form-group">
                                    <label class="font-weight-semibold"># Vértices</label>
                                    <input type="text" 
                                           id="vertices_count" 
                                           class="form-control" 
                                           value="0" 
                                           readonly>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label class="font-weight-semibold">Área aproximada (km²)</label>
                                    <input type="text" 
                                           id="area_display" 
                                           class="form-control" 
                                           value="0.00" 
                                           readonly>
                                </div>
                            </div>
                        </div>

                        <!-- Campos ocultos para datos del mapa -->
                        <input type="hidden" name="polygon_coordinates" id="polygon_coordinates">
                        <input type="hidden" name="area" id="area">
                    </div>
                </div>
            </div>

            <!-- Mapa -->
            <div class="col-md-7">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="card-title mb-0">Asignar Perímetro</h5>
                            <div class="btn-group" role="group">
                                <button type="button" class="btn btn-sm btn-info" id="expandMap" title="Expandir mapa">
                                    <i class="fas fa-expand"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-success" id="drawPolygon">
                                    <i class="fas fa-draw-polygon"></i> Dibujar Zona
                                </button>
                                <button type="button" class="btn btn-sm btn-warning" id="clearPolygon">
                                    <i class="fas fa-eraser"></i> Borrar
                                </button>
                                <button type="button" class="btn btn-sm btn-secondary" id="resetMap">
                                    <i class="fas fa-undo"></i> Restablecer
                                </button>
                            </div>
                        </div>
                        
                        <div id="map" style="height: 500px; border-radius: 8px;"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Botones de acción -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="d-flex justify-content-end">
                    <a href="{{ route('admin.zones.index') }}" class="btn btn-secondary mr-2">
                        Cancelar
                    </a>
                    <button type="submit" class="btn btn-dark">
                        <i class="fas fa-save"></i> Guardar
                    </button>
                </div>
            </div>
        </div>
    </form>

    <!-- Modal para mapa expandido -->
    <div class="modal fade" id="mapModal" tabindex="-1" aria-labelledby="mapModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-fullscreen">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="mapModalLabel">Mapa Expandido - Asignar Perímetro</h5>
                    <div class="btn-group mr-3" role="group">
                        <button type="button" class="btn btn-sm btn-success" id="drawPolygonModal">
                            <i class="fas fa-draw-polygon"></i> Dibujar Zona
                        </button>
                        <button type="button" class="btn btn-sm btn-warning" id="clearPolygonModal">
                            <i class="fas fa-eraser"></i> Borrar
                        </button>
                        <button type="button" class="btn btn-sm btn-secondary" id="resetMapModal">
                            <i class="fas fa-undo"></i> Restablecer
                        </button>
                    </div>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-0">
                    <div id="mapExpanded" style="height: calc(100vh - 120px);"></div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet-draw@1.0.4/dist/leaflet.draw.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" />
    <style>
        body {
            background-color: #f9f7f7 !important;
        }
        .card {
            border: none;
            border-radius: 12px;
        }
        .font-weight-semibold {
            font-weight: 600;
        }
        #map {
            border: 2px solid #e9ecef;
            cursor: crosshair;
        }
        .leaflet-draw-toolbar {
            display: none !important;
        }
        .drawing-mode #map {
            border-color: #007bff;
            box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.25);
        }
        .btn-danger.drawing {
            animation: pulse 2s infinite;
        }
        @keyframes pulse {
            0% { box-shadow: 0 0 0 0 rgba(220, 53, 69, 0.7); }
            70% { box-shadow: 0 0 0 10px rgba(220, 53, 69, 0); }
            100% { box-shadow: 0 0 0 0 rgba(220, 53, 69, 0); }
        }
        
        /* Estilos para modal expandido */
        .modal-fullscreen {
            width: 100vw !important;
            max-width: none !important;
            height: 100vh !important;
            margin: 0 !important;
        }
        .modal-fullscreen .modal-dialog {
            width: 100vw !important;
            max-width: none !important;
            height: 100vh !important;
            margin: 0 !important;
        }
        .modal-fullscreen .modal-content {
            width: 100vw !important;
            height: 100vh !important;
            border: none;
            border-radius: 0;
        }
        .modal-fullscreen .modal-header {
            border-bottom: 1px solid #dee2e6;
            padding: 15px 20px;
            flex-shrink: 0;
        }
        .modal-fullscreen .modal-body {
            flex: 1;
            padding: 0;
            overflow: hidden;
            height: calc(100vh - 80px) !important;
        }
        #mapExpanded {
            width: 100% !important;
            height: 100% !important;
        }
        
        /* Estilos para alerta de superposición */
        .overlap-warning {
            border-left: 4px solid #dc3545 !important;
            animation: fadeInShake 0.5s ease-out;
        }
        
        .overlap-warning h6 {
            color: #721c24;
            font-weight: 600;
        }
        
        .overlap-warning ul {
            list-style-type: none;
            padding-left: 0;
        }
        
        .overlap-warning li {
            background: #f8d7da;
            padding: 4px 8px;
            margin: 2px 0;
            border-radius: 4px;
            font-weight: 500;
        }
        
        @keyframes fadeInShake {
            0% {
                opacity: 0;
                transform: translateX(-10px);
            }
            50% {
                transform: translateX(5px);
            }
            100% {
                opacity: 1;
                transform: translateX(0);
            }
        }
    </style>
@stop

@section('js')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://unpkg.com/leaflet-draw@1.0.4/dist/leaflet.draw.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script>
        let map;
        let drawnItems;
        let drawControl;
        let currentPolygon = null;
        let departmentMarker = null;

        $(document).ready(function() {
            toastr.options = {
                "closeButton": true,
                "debug": false,
                "newestOnTop": true,
                "progressBar": true,
                "positionClass": "toast-top-right",
                "preventDuplicates": false,
                "onclick": null,
                "showDuration": "300",
                "hideDuration": "1000",
                "timeOut": "5000",
                "extendedTimeOut": "1000",
                "showEasing": "swing",
                "hideEasing": "linear",
                "showMethod": "fadeIn",
                "hideMethod": "fadeOut"
            };

            initializeMap();
            setupDependentSelects();
            setupMapControls();
            initializeFormValidation();
            
            setTimeout(function() {
                preloadJLOLocation();
            }, 500);
        });

        function initializeMap() {
            map = L.map('map').setView([-6.7706, -79.8406], 14);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors'
            }).addTo(map);

            drawnItems = new L.FeatureGroup();
            map.addLayer(drawnItems);

            drawControl = new L.Control.Draw({
                draw: {
                    polygon: {
                        allowIntersection: false,
                        drawError: {
                            color: '#e1e100',
                            message: '<strong>¡Error!</strong> El polígono no puede intersectarse.'
                        },
                        shapeOptions: {
                            color: '#007bff',
                            fillOpacity: 0.3
                        }
                    },
                    polyline: false,
                    rectangle: false,
                    circle: false,
                    marker: false,
                    circlemarker: false
                },
                edit: {
                    featureGroup: drawnItems,
                    remove: true
                }
            });

            map.on('draw:created', function(e) {
                if (currentPolygon) {
                    drawnItems.removeLayer(currentPolygon);
                }
                
                currentPolygon = e.layer;
                drawnItems.addLayer(currentPolygon);
                
                updatePolygonData();
            });

            map.on('draw:edited', function(e) {
                updatePolygonData();
            });

            map.on('draw:deleted', function(e) {
                currentPolygon = null;
                updatePolygonData();
            });

            loadExistingZones();
        }

        function loadExistingZones() {
            $.get('/admin/api/zones-polygons', function(zones) {
                zones.forEach(function(zone) {
                    if (zone.polygon_coordinates && zone.polygon_coordinates.length > 0) {
                        let coordinates;
                        
                        try {
                            if (Array.isArray(zone.polygon_coordinates[0]) && zone.polygon_coordinates[0].length === 2) {
                                coordinates = zone.polygon_coordinates.map(coord => [coord[0], coord[1]]);
                            } 
                            // Si las coordenadas están en formato {lat: x, lng: y}
                            else if (zone.polygon_coordinates[0] && zone.polygon_coordinates[0].lat !== undefined) {
                                coordinates = zone.polygon_coordinates.map(coord => [coord.lat, coord.lng]);
                            }
                            // Si las coordenadas están en otro formato, intentar extraer números
                            else {
                                console.warn('Formato de coordenadas no reconocido para zona:', zone.name);
                                return; // Saltar esta zona
                            }
                            
                            // Validar que todas las coordenadas son números válidos
                            const validCoordinates = coordinates.every(coord => 
                                Array.isArray(coord) && 
                                coord.length === 2 && 
                                !isNaN(coord[0]) && 
                                !isNaN(coord[1]) &&
                                coord[0] >= -90 && coord[0] <= 90 && // Latitud válida
                                coord[1] >= -180 && coord[1] <= 180 // Longitud válida
                            );
                            
                            if (!validCoordinates) {
                                return;
                            }
                            
                            const existingPolygon = L.polygon(coordinates, {
                                color: '#28a745',
                                weight: 2,
                                fillOpacity: 0.1,
                                dashArray: '5, 5'
                            });
                            
                            existingPolygon.bindPopup(`
                                <div style="min-width: 200px;">
                                    <h6 class="mb-2"><strong>${zone.name}</strong></h6>
                                    <p class="mb-1"><small><i class="fas fa-map-marker-alt"></i> ${zone.location}</small></p>
                                    ${zone.area ? `<p class="mb-1"><small><i class="fas fa-ruler-combined"></i> ${parseFloat(zone.area).toFixed(2)} km²</small></p>` : ''}
                                    ${zone.description ? `<p class="mb-0"><small>${zone.description}</small></p>` : ''}
                                </div>
                            `);
                            
                            existingZones.push({
                                id: zone.id,
                                name: zone.name,
                                coordinates: coordinates
                            });
                            
                            existingPolygon.addTo(map);
                            
                        } catch (error) {
                            console.error('Error procesando zona:', zone.name, error);
                        }
                    }
                });
            }).fail(function(xhr, status, error) {
                console.error('Error al cargar zonas:', error);
            });
        }

        function centerMapOnDepartment(departmentId, showNotification = true) {
            $.get(`/admin/api/department-coordinates/${departmentId}`, function(data) {
                // Remover marcador anterior si existe
                if (departmentMarker) {
                    map.removeLayer(departmentMarker);
                }
                
                map.setView([data.latitude, data.longitude], data.zoom_level);
                
                if (showNotification) {
                    departmentMarker = L.marker([data.latitude, data.longitude], {
                        icon: L.divIcon({
                            className: 'department-marker',
                            html: `<div style="background: #007bff; color: white; padding: 5px 10px; border-radius: 15px; font-size: 12px; font-weight: bold; box-shadow: 0 2px 5px rgba(0,0,0,0.3);">${data.name}</div>`,
                            iconSize: [100, 30],
                            iconAnchor: [50, 15]
                        })
                    }).addTo(map);
                    
                    setTimeout(function() {
                        if (departmentMarker) {
                            map.removeLayer(departmentMarker);
                            departmentMarker = null;
                        }
                    }, 5000);
                    
                    toastr.success(`Mapa centrado en ${data.name}`);
                }
            }).fail(function() {
                console.log('Error al obtener coordenadas del departamento');
            });
        }

        function setupDependentSelects() {
            $('#department_id').change(function() {
                const departmentId = $(this).val();
                
                $('#province_id').html('<option value="">Seleccione una provincia</option>').prop('disabled', !departmentId);
                $('#district_id').html('<option value="">Seleccione un distrito</option>').prop('disabled', true);
                
                if (departmentId) {
                    centerMapOnDepartment(departmentId, true);

                    // Cargar provincias
                    $.get(`/admin/api/provinces/${departmentId}`, function(provinces) {
                        provinces.forEach(function(province) {
                            $('#province_id').append(
                                `<option value="${province.id}">${province.name}</option>`
                            );
                        });
                        $('#province_id').prop('disabled', false);
                    });
                }
            });

            $('#province_id').change(function() {
                const provinceId = $(this).val();
                
                $('#district_id').html('<option value="">Seleccione un distrito</option>').prop('disabled', !provinceId);
                
                if (provinceId) {
                    $.get(`/admin/api/districts/${provinceId}`, function(districts) {
                        if (districts.length > 0) {
                            districts.forEach(function(district) {
                                $('#district_id').append(
                                    `<option value="${district.id}">${district.name}</option>`
                                );
                            });
                            $('#district_id').prop('disabled', false);
                            $('#district_id').prop('required', true);
                            $('#district_id').removeClass('is-invalid');
                            
                            $('.no-districts-info').remove();
                        } else {
                            $('#district_id').html('<option value="">Esta provincia no tiene distritos registrados</option>');
                            $('#district_id').prop('disabled', true);
                            $('#district_id').prop('required', false);
                            $('#district_id').removeClass('is-invalid');
                            
                            if (!$('.no-districts-info').length) {
                                $('#district_id').parent().append(
                                    '<div class="alert alert-info no-districts-info mt-2" style="font-size: 0.9em;">' +
                                    '<i class="fas fa-info-circle"></i> Esta provincia no tiene distritos. Puede guardar la zona sin seleccionar distrito.' +
                                    '</div>'
                                );
                            }
                        }
                    }).fail(function() {
                        $('#district_id').html('<option value="">Error al cargar distritos</option>');
                        $('#district_id').prop('disabled', true);
                        $('#district_id').prop('required', false);
                    });
                }
                
                $('.no-districts-info').remove();
            });
        }

        function setupMapControls() {
            let isDrawing = false;
            let polygonDrawer = null;

            $('#drawPolygon').click(function() {
                if (!isDrawing) {
                    // Activar modo de dibujo
                    isDrawing = true;
                    $(this).removeClass('btn-success').addClass('btn-danger drawing');
                    $(this).html('<i class="fas fa-stop"></i> Detener Dibujo');
                    $('body').addClass('drawing-mode');
                    
                    polygonDrawer = new L.Draw.Polygon(map, {
                        allowIntersection: false,
                        drawError: {
                            color: '#e1e100',
                            message: '<strong>¡Error!</strong> El polígono no puede intersectarse.'
                        },
                        shapeOptions: {
                            color: '#007bff',
                            fillColor: '#007bff',
                            fillOpacity: 0.3,
                            weight: 2
                        }
                    });
                    
                    polygonDrawer.enable();
                    
                    toastr.info('Haga clic en el mapa para comenzar a dibujar el polígono. Doble clic para terminar.');
                    
                } else {
                    stopDrawing();
                }
            });

            function stopDrawing() {
                isDrawing = false;
                if (polygonDrawer) {
                    polygonDrawer.disable();
                    polygonDrawer = null;
                }
                $('#drawPolygon').removeClass('btn-danger drawing').addClass('btn-success');
                $('#drawPolygon').html('<i class="fas fa-draw-polygon"></i> Dibujar Zona');
                $('body').removeClass('drawing-mode');
            }

            $('#clearPolygon').click(function() {
                if (currentPolygon) {
                    drawnItems.removeLayer(currentPolygon);
                    currentPolygon = null;
                    updatePolygonData();
                    toastr.success('Polígono eliminado');
                }
                stopDrawing();
            });

            $('#resetMap').click(function() {
                if (currentPolygon) {
                    drawnItems.removeLayer(currentPolygon);
                    currentPolygon = null;
                }
                map.setView([-6.7706, -79.8406], 14);
                updatePolygonData();
                stopDrawing();
                toastr.info('Mapa restablecido');
            });

            let expandedMap = null;
            let expandedDrawnItems = null;
            let expandedCurrentPolygon = null;
            
            $('#expandMap').click(function() {
                $('#mapModal').modal('show');
                
                // Crear mapa expandido después de mostrar el modal
                setTimeout(function() {
                    initializeExpandedMap();
                }, 300);
            });

            function initializeExpandedMap() {
                expandedMap = L.map('mapExpanded').setView([-6.7706, -79.8406], 14);

                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '© OpenStreetMap contributors'
                }).addTo(expandedMap);

                expandedDrawnItems = new L.FeatureGroup();
                expandedMap.addLayer(expandedDrawnItems);

                if (currentPolygon) {
                    const latlngs = currentPolygon.getLatLngs()[0];
                    expandedCurrentPolygon = L.polygon(latlngs, {
                        color: '#007bff',
                        fillOpacity: 0.3
                    });
                    expandedDrawnItems.addLayer(expandedCurrentPolygon);
                    expandedMap.fitBounds(expandedCurrentPolygon.getBounds(), { padding: [20, 20] });
                }

                loadExistingZonesInExpanded();
                setupExpandedMapControls();
            }

            function loadExistingZonesInExpanded() {
                if (!expandedMap) return;
                
                $.get('/admin/api/zones-polygons', function(zones) {
                    zones.forEach(function(zone) {
                        if (zone.polygon_coordinates && zone.polygon_coordinates.length > 0) {
                            let coordinates;
                            
                            try {
                                if (Array.isArray(zone.polygon_coordinates[0]) && zone.polygon_coordinates[0].length === 2) {
                                    coordinates = zone.polygon_coordinates.map(coord => [coord[0], coord[1]]);
                                } 
                                else if (zone.polygon_coordinates[0] && zone.polygon_coordinates[0].lat !== undefined) {
                                    coordinates = zone.polygon_coordinates.map(coord => [coord.lat, coord.lng]);
                                }
                                else {
                                    return;
                                }
                                
                                const validCoordinates = coordinates.every(coord => 
                                    Array.isArray(coord) && 
                                    coord.length === 2 && 
                                    !isNaN(coord[0]) && 
                                    !isNaN(coord[1]) &&
                                    coord[0] >= -90 && coord[0] <= 90 &&
                                    coord[1] >= -180 && coord[1] <= 180
                                );
                                
                                if (!validCoordinates) {
                                    return;
                                }
                                
                                const existingPolygon = L.polygon(coordinates, {
                                    color: '#28a745',
                                    weight: 2,
                                    fillOpacity: 0.1,
                                    dashArray: '5, 5'
                                });
                                
                                existingPolygon.bindPopup(`
                                    <div style="min-width: 200px;">
                                        <h6 class="mb-2"><strong>${zone.name}</strong></h6>
                                        <p class="mb-1"><small><i class="fas fa-map-marker-alt"></i> ${zone.location}</small></p>
                                        ${zone.area ? `<p class="mb-1"><small><i class="fas fa-ruler-combined"></i> ${parseFloat(zone.area).toFixed(2)} km²</small></p>` : ''}
                                        ${zone.description ? `<p class="mb-0"><small>${zone.description}</small></p>` : ''}
                                    </div>
                                `);
                                
                                existingPolygon.addTo(expandedMap);
                                
                            } catch (error) {
                                console.error('Error procesando zona:', zone.name, error);
                            }
                        }
                    });
                });
            }

            function setupExpandedMapControls() {
                let isDrawingExpanded = false;
                let polygonDrawerExpanded = null;

                // Eventos del mapa expandido
                expandedMap.on('draw:created', function(e) {
                    if (expandedCurrentPolygon) {
                        expandedDrawnItems.removeLayer(expandedCurrentPolygon);
                    }
                    
                    expandedCurrentPolygon = e.layer;
                    expandedDrawnItems.addLayer(expandedCurrentPolygon);
                    
                    // Sincronizar con el mapa principal
                    syncPolygonToMainMap();
                });

                function syncPolygonToMainMap() {
                    if (expandedCurrentPolygon) {
                        const latlngs = expandedCurrentPolygon.getLatLngs()[0];
                        
                        if (currentPolygon) {
                            drawnItems.removeLayer(currentPolygon);
                        }
                        
                        currentPolygon = L.polygon(latlngs, {
                            color: '#007bff',
                            fillOpacity: 0.3
                        });
                        drawnItems.addLayer(currentPolygon);
                        
                        updatePolygonData();
                    }
                }

                $('#drawPolygonModal').off('click').on('click', function() {
                    if (!isDrawingExpanded) {
                        isDrawingExpanded = true;
                        $(this).removeClass('btn-success').addClass('btn-danger');
                        $(this).html('<i class="fas fa-stop"></i> Detener Dibujo');
                        
                        polygonDrawerExpanded = new L.Draw.Polygon(expandedMap, {
                            allowIntersection: false,
                            shapeOptions: {
                                color: '#007bff',
                                fillColor: '#007bff',
                                fillOpacity: 0.3,
                                weight: 2
                            }
                        });
                        
                        polygonDrawerExpanded.enable();
                        toastr.info('Haga clic en el mapa para comenzar a dibujar el polígono. Doble clic para terminar.');
                        
                    } else {
                        stopDrawingExpanded();
                    }
                });

                function stopDrawingExpanded() {
                    isDrawingExpanded = false;
                    if (polygonDrawerExpanded) {
                        polygonDrawerExpanded.disable();
                        polygonDrawerExpanded = null;
                    }
                    $('#drawPolygonModal').removeClass('btn-danger').addClass('btn-success');
                    $('#drawPolygonModal').html('<i class="fas fa-draw-polygon"></i> Dibujar Zona');
                }

                $('#clearPolygonModal').off('click').on('click', function() {
                    if (expandedCurrentPolygon) {
                        expandedDrawnItems.removeLayer(expandedCurrentPolygon);
                        expandedCurrentPolygon = null;
                        
                        if (currentPolygon) {
                            drawnItems.removeLayer(currentPolygon);
                            currentPolygon = null;
                            updatePolygonData();
                        }
                        
                        toastr.success('Polígono eliminado');
                    }
                    stopDrawingExpanded();
                });

                $('#resetMapModal').off('click').on('click', function() {
                    if (expandedCurrentPolygon) {
                        expandedDrawnItems.removeLayer(expandedCurrentPolygon);
                        expandedCurrentPolygon = null;
                    }
                    expandedMap.setView([-6.7706, -79.8406], 14);
                    
                    if (currentPolygon) {
                        drawnItems.removeLayer(currentPolygon);
                        currentPolygon = null;
                        updatePolygonData();
                    }
                    
                    stopDrawingExpanded();
                    toastr.info('Mapa restablecido');
                });
            }

            $('#mapModal').on('hidden.bs.modal', function() {
                if (expandedMap) {
                    expandedMap.remove();
                    expandedMap = null;
                    expandedDrawnItems = null;
                    expandedCurrentPolygon = null;
                    $('#mapExpanded').empty();
                }
            });


        }

        function updatePolygonData() {
            if (currentPolygon) {
                const latlngs = currentPolygon.getLatLngs()[0];
                const coordinates = latlngs.map(latlng => [latlng.lat, latlng.lng]);
                
                // Verificar superposición con zonas existentes
                checkPolygonOverlap(coordinates);
                
                // Calcular área (aproximada usando la fórmula shoelace)
                const area = calculatePolygonArea(coordinates);
                
                $('#vertices_count').val(coordinates.length);
                $('#area_display').val(area.toFixed(2));
                $('#polygon_coordinates').val(JSON.stringify(coordinates));
                $('#area').val(area);
            } else {
                clearOverlapWarnings();
                
                $('#vertices_count').val(0);
                $('#area_display').val('0.00');
                $('#polygon_coordinates').val('');
                $('#area').val('');
            }
        }

        let existingZones = [];

        function clearOverlapWarnings() {
            $('.overlap-warning').fadeOut(300, function() {
                $(this).remove();
            });
        }

        function checkPolygonOverlap(newCoordinates) {
            let hasOverlap = false;
            let overlappingZones = [];
            
            existingZones.forEach(function(existingZone) {
                if (existingZone.coordinates && existingZone.coordinates.length > 0) {
                    if (polygonsIntersect(newCoordinates, existingZone.coordinates)) {
                        hasOverlap = true;
                        overlappingZones.push(existingZone.name);
                    }
                }
            });
            
            $('.overlap-warning').remove();
            
            if (hasOverlap) {
                if (currentPolygon) {
                    currentPolygon.setStyle({
                        color: '#dc3545',
                        fillColor: '#dc3545',
                        fillOpacity: 0.3
                    });
                }
                
                const warningHtml = `
                    <div class="alert alert-danger overlap-warning mt-3" role="alert">
                        <h6><i class="fas fa-exclamation-triangle"></i> ¡Superposición detectada!</h6>
                        <p class="mb-2">El polígono se superpone con las siguientes zonas:</p>
                        <ul class="mb-2">${overlappingZones.map(zone => `<li>${zone}</li>`).join('')}</ul>
                        <small><strong>Recomendación:</strong> Ajuste el perímetro para evitar superposiciones.</small>
                    </div>
                `;
                $('.card-body').has('#polygon_coordinates').append(warningHtml);
                
                toastr.warning(`Superposición detectada con: ${overlappingZones.join(', ')}`, 'Atención', {
                    timeOut: 8000,
                    progressBar: true
                });
                
            } else {
                if (currentPolygon) {
                    currentPolygon.setStyle({
                        color: '#007bff',
                        fillColor: '#007bff',
                        fillOpacity: 0.3
                    });
                }
            }
        }

        function polygonsIntersect(poly1, poly2) {
            for (let i = 0; i < poly1.length; i++) {
                if (pointInPolygon(poly1[i], poly2)) {
                    return true;
                }
            }
            
            // Verificar si algún punto de poly2 está dentro de poly1
            for (let i = 0; i < poly2.length; i++) {
                if (pointInPolygon(poly2[i], poly1)) {
                    return true;
                }
            }
            
            // Verificar si los bordes se intersectan
            for (let i = 0; i < poly1.length; i++) {
                const a1 = poly1[i];
                const a2 = poly1[(i + 1) % poly1.length];
                
                for (let j = 0; j < poly2.length; j++) {
                    const b1 = poly2[j];
                    const b2 = poly2[(j + 1) % poly2.length];
                    
                    if (linesIntersect(a1, a2, b1, b2)) {
                        return true;
                    }
                }
            }
            
            return false;
        }

        function pointInPolygon(point, polygon) {
            const [x, y] = point;
            let inside = false;
            
            for (let i = 0, j = polygon.length - 1; i < polygon.length; j = i++) {
                const [xi, yi] = polygon[i];
                const [xj, yj] = polygon[j];
                
                if (((yi > y) !== (yj > y)) && (x < (xj - xi) * (y - yi) / (yj - yi) + xi)) {
                    inside = !inside;
                }
            }
            
            return inside;
        }

        function linesIntersect(p1, p2, p3, p4) {
            const [x1, y1] = p1;
            const [x2, y2] = p2;
            const [x3, y3] = p3;
            const [x4, y4] = p4;
            
            const denominator = (x1 - x2) * (y3 - y4) - (y1 - y2) * (x3 - x4);
            
            if (denominator === 0) {
                return false;
            }
            
            const t = ((x1 - x3) * (y3 - y4) - (y1 - y3) * (x3 - x4)) / denominator;
            const u = -((x1 - x2) * (y1 - y3) - (y1 - y2) * (x1 - x3)) / denominator;
            
            return t >= 0 && t <= 1 && u >= 0 && u <= 1;
        }

        function calculatePolygonArea(coordinates) {
            if (coordinates.length < 3) return 0;
            
            let area = 0;
            const earthRadius = 6371;
            
            for (let i = 0; i < coordinates.length; i++) {
                const j = (i + 1) % coordinates.length;
                const lat1 = coordinates[i][0] * Math.PI / 180;
                const lat2 = coordinates[j][0] * Math.PI / 180;
                const deltaLng = (coordinates[j][1] - coordinates[i][1]) * Math.PI / 180;
                
                area += deltaLng * (2 + Math.sin(lat1) + Math.sin(lat2));
            }
            
            area = Math.abs(area * earthRadius * earthRadius / 2);
            return area;
        }

        function initializeFormValidation() {
            $('#district_id').prop('required', false);
        }
        $('#zoneForm').submit(function(e) {
            let isValid = true;
            
            if (!$('#name').val().trim()) {
                isValid = false;
                $('#name').addClass('is-invalid');
            }
            
            if (!$('#province_id').val()) {
                isValid = false;
                $('#province_id').addClass('is-invalid');
            }
            
            if ($('#district_id').prop('required') && !$('#district_id').val()) {
                isValid = false;
                $('#district_id').addClass('is-invalid');
            }
            
            if ($('.overlap-warning').length > 0) {
                e.preventDefault();
                toastr.error('No se puede guardar la zona porque se superpone con zonas existentes. Por favor ajuste el perímetro.', 'Error de Superposición', {
                    timeOut: 10000,
                    progressBar: true
                });
                
                // Hacer scroll hacia la alerta
                $('html, body').animate({
                    scrollTop: $('.overlap-warning').offset().top - 100
                }, 500);
                
                return false;
            }
            
            if (!$('#polygon_coordinates').val()) {
                e.preventDefault();
                toastr.error('Por favor dibuje el perímetro de la zona en el mapa.', 'Perímetro requerido');
                return false;
            }
            
            if (!isValid) {
                e.preventDefault();
                toastr.error('Por favor complete todos los campos requeridos.');
            }
        });

        function preloadJLOLocation() {
            const departmentId = 14; // Lambayeque
            const provinceId = 93;   // Chiclayo
            const districtId = 97;   // José Leonardo Ortiz
            
            $('#department_id').val(departmentId);
            
            $.get(`/admin/api/provinces/${departmentId}`, function(provinces) {
                $('#province_id').html('<option value="">Seleccione una provincia</option>');
                provinces.forEach(function(province) {
                    const selected = province.id == provinceId ? 'selected' : '';
                    $('#province_id').append(
                        `<option value="${province.id}" ${selected}>${province.name}</option>`
                    );
                });
                $('#province_id').prop('disabled', false);
                
                $.get(`/admin/api/districts/${provinceId}`, function(districts) {
                    $('#district_id').html('<option value="">Seleccione un distrito</option>');
                    districts.forEach(function(district) {
                        const selected = district.id == districtId ? 'selected' : '';
                        $('#district_id').append(
                            `<option value="${district.id}" ${selected}>${district.name}</option>`
                        );
                    });
                    $('#district_id').prop('disabled', false);
                    $('#district_id').prop('required', true);
                }).fail(function(xhr, status, error) {
                    toastr.error('Error al cargar distritos de Chiclayo');
                });
            }).fail(function(xhr, status, error) {
                toastr.error('Error al cargar provincias de Lambayeque');
            });
        }
    </script>
@stop