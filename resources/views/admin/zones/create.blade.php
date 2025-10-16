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
            // Configurar Toastr
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
        });

        function initializeMap() {
            // Inicializar mapa centrado en Lima, Perú
            map = L.map('map').setView([-12.0464, -77.0428], 10);

            // Agregar tile layer
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors'
            }).addTo(map);

            // Crear layer para elementos dibujados
            drawnItems = new L.FeatureGroup();
            map.addLayer(drawnItems);

            // Configurar controles de dibujo
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

            // Eventos del mapa
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
        }

        function centerMapOnDepartment(departmentId, showNotification = true) {
            $.get(`/admin/api/department-coordinates/${departmentId}`, function(data) {
                // Remover marcador anterior si existe
                if (departmentMarker) {
                    map.removeLayer(departmentMarker);
                }
                
                // Centrar mapa
                map.setView([data.latitude, data.longitude], data.zoom_level);
                
                // Agregar marcador temporal solo si se solicita notificación
                if (showNotification) {
                    departmentMarker = L.marker([data.latitude, data.longitude], {
                        icon: L.divIcon({
                            className: 'department-marker',
                            html: `<div style="background: #007bff; color: white; padding: 5px 10px; border-radius: 15px; font-size: 12px; font-weight: bold; box-shadow: 0 2px 5px rgba(0,0,0,0.3);">${data.name}</div>`,
                            iconSize: [100, 30],
                            iconAnchor: [50, 15]
                        })
                    }).addTo(map);
                    
                    // Remover marcador después de 5 segundos
                    setTimeout(function() {
                        if (departmentMarker) {
                            map.removeLayer(departmentMarker);
                            departmentMarker = null;
                        }
                    }, 5000);
                    
                    toastr.success(`Mapa centrado en ${data.name}`);
                }
            }).fail(function() {
                console.log('No se pudieron obtener las coordenadas del departamento');
            });
        }

        function setupDependentSelects() {
            $('#department_id').change(function() {
                const departmentId = $(this).val();
                
                $('#province_id').html('<option value="">Seleccione una provincia</option>').prop('disabled', !departmentId);
                $('#district_id').html('<option value="">Seleccione un distrito</option>').prop('disabled', true);
                
                if (departmentId) {
                    // Centrar mapa en el departamento seleccionado
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
                            // La provincia tiene distritos
                            districts.forEach(function(district) {
                                $('#district_id').append(
                                    `<option value="${district.id}">${district.name}</option>`
                                );
                            });
                            $('#district_id').prop('disabled', false);
                            $('#district_id').prop('required', true); // Hacer distrito requerido
                            $('#district_id').removeClass('is-invalid'); // Limpiar errores previos
                            
                            // Remover mensaje informativo si existe
                            $('.no-districts-info').remove();
                        } else {
                            // La provincia no tiene distritos
                            $('#district_id').html('<option value="">Esta provincia no tiene distritos registrados</option>');
                            $('#district_id').prop('disabled', true);
                            $('#district_id').prop('required', false); // Hacer distrito NO requerido
                            $('#district_id').removeClass('is-invalid'); // Limpiar errores de validación
                            
                            // Mostrar mensaje informativo
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
                        $('#district_id').prop('required', false); // No requerido en caso de error
                    });
                }
                
                // Limpiar mensaje informativo cuando se cambia de provincia
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
                    
                    // Crear nueva herramienta de dibujo de polígonos
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
                    
                    // Activar el dibujo
                    polygonDrawer.enable();
                    
                    // Mostrar instrucciones
                    toastr.info('Haga clic en el mapa para comenzar a dibujar el polígono. Doble clic para terminar.');
                    
                } else {
                    // Detener modo de dibujo
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
                map.setView([-12.0464, -77.0428], 10);
                updatePolygonData();
                stopDrawing();
                toastr.info('Mapa restablecido');
            });
        }

        function updatePolygonData() {
            if (currentPolygon) {
                const latlngs = currentPolygon.getLatLngs()[0];
                const coordinates = latlngs.map(latlng => [latlng.lat, latlng.lng]);
                
                // Calcular área (aproximada usando la fórmula shoelace)
                const area = calculatePolygonArea(coordinates);
                
                // Actualizar campos
                $('#vertices_count').val(coordinates.length);
                $('#area_display').val(area.toFixed(2));
                $('#polygon_coordinates').val(JSON.stringify(coordinates));
                $('#area').val(area);
            } else {
                $('#vertices_count').val(0);
                $('#area_display').val('0.00');
                $('#polygon_coordinates').val('');
                $('#area').val('');
            }
        }

        function calculatePolygonArea(coordinates) {
            if (coordinates.length < 3) return 0;
            
            let area = 0;
            const earthRadius = 6371; // Radio de la Tierra en km
            
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
            // Estado inicial: distrito no requerido hasta que se seleccione una provincia con distritos
            $('#district_id').prop('required', false);
        }

        // Validación del formulario
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
            
            // Solo validar distrito si es requerido (cuando hay distritos disponibles)
            if ($('#district_id').prop('required') && !$('#district_id').val()) {
                isValid = false;
                $('#district_id').addClass('is-invalid');
            }
            
            if (!isValid) {
                e.preventDefault();
                toastr.error('Por favor complete todos los campos requeridos.');
            }
        });
    </script>
@stop