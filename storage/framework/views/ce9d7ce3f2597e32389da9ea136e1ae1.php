<?php if(empty($visualizacion)): ?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="card-title mb-0">Dibujar Zona en el Mapa</h5>
    <div class="btn-group" role="group">
        <button type="button" class="btn btn-success btn-sm" id="drawPolygon"><i class="fas fa-draw-polygon"></i> Dibujar Zona</button>
        <button type="button" class="btn btn-warning btn-sm" id="clearPolygon"><i class="fas fa-eraser"></i> Borrar</button>
    </div>
</div>
<?php endif; ?>
<?php if(isset($modal) && $modal): ?>
<div id="mapaZonasModal" style="height: 600px; border-radius: 8px; width: 100%;"></div>
<?php else: ?>
<?php if(!empty($visualizacion)): ?>
<div id="map" style="height: 600px; border-radius: 8px; width: 100%;"></div>
<?php else: ?>
<div id="map" style="height: 350px; border-radius: 8px; width: 100%; height: 90%;"></div>
<?php endif; ?>
<?php endif; ?>
<?php if(empty($visualizacion)): ?>
<small class="text-muted d-block mt-2">Haz clic en "Dibujar Zona" y selecciona los puntos del polígono. Haz clic en el primer punto para cerrar la zona.</small>
<?php endif; ?>

<?php $__env->startPush('css'); ?>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<link rel="stylesheet" href="https://unpkg.com/leaflet-draw@1.0.4/dist/leaflet.draw.css" />
<style>
    .leaflet-draw-toolbar {
        display: none !important;
    }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('js'); ?>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet-draw@1.0.4/dist/leaflet.draw.js"></script>
<script>
let map, drawnItems, drawControl, currentPolygon = null;
$(function() {
    var isModal = $('#mapaZonasModal').length > 0;
    var mapDivId = isModal ? 'mapaZonasModal' : 'map';
    var jloLat = -6.7604806497116, jloLng = -79.83481407165529;
    // Si hay polígono (edit), centrar en el polígono
    <?php if(isset($zone) && isset($zone->coords) && count($zone->coords)): ?>
        var coords = <?php echo json_encode($zone->coords, 15, 512) ?>;
        var bounds = L.latLngBounds(coords.map(function(c){ return [c.latitude, c.longitude]; }));
        map = L.map(mapDivId).fitBounds(bounds, {padding: [40, 40]});
    <?php else: ?>
        map = L.map(mapDivId).setView([jloLat, jloLng], isModal ? 15 : 16);
    <?php endif; ?>
        // Si hay coords existentes, dibujar el polígono al cargar (para edit)
        <?php if(isset($zone) && isset($zone->coords) && count($zone->coords)): ?>
            setTimeout(function() {
                var polygonLatLngs = coords.map(function(c) {
                    return [c.latitude, c.longitude];
                });
                currentPolygon = L.polygon(polygonLatLngs, {
                    color: '#007bff',
                    fillOpacity: 0.3
                });
                drawnItems.addLayer(currentPolygon);
                updatePolygonData();
            }, 500);
        <?php endif; ?>
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '© OpenStreetMap'
    }).addTo(map);
    drawnItems = new L.FeatureGroup();
    map.addLayer(drawnItems);

    // --- DIBUJAR ZONAS EXISTENTES (en create y edit) ---
    <?php if(isset($zonesPolygons)): ?>
        var existingZones = <?php echo json_encode($zonesPolygons, 15, 512) ?>;
        // Paleta de colores pastel
        var zoneColors = [
            '#e57373', // rojo
            '#ba68c8', // morado
            '#64b5f6', // azul
            '#4db6ac', // turquesa
            '#ffd54f', // amarillo
            '#ffb74d', // naranja
            '#a1887f', // marrón
            '#90a4ae', // gris
            '#81c784', // verde
            '#f06292'  // rosa
        ];
        existingZones.forEach(function(zone, i) {
            if(zone.coords.length > 2) {
                var color = zoneColors[i % zoneColors.length];
                var poly = L.polygon(zone.coords, {
                    color: color,
                    fillColor: color,
                    fillOpacity: 0.18,
                    weight: 2,
                    dashArray: '5, 5'
                }).addTo(map);
                // Popup personalizado solo en modo visualización
                if (<?php echo json_encode(!empty($visualizacion), 15, 512) ?>) {
                    var popup = '<b>' + (zone.name || '') + '</b><br>';
                    if (zone.full_location) popup += '<span><b>Lugar:</b> ' + zone.full_location + '</span><br>';
                    if (zone.description) popup += '<span><b>Descripción:</b> ' + zone.description + '</span><br>';
                    popup += '<span><b>Vértices:</b> ' + zone.coords.length + '</span><br>';
                    popup += '<a href="#" data-id="'+zone.id+'" class="btn btn-info btn-sm mt-2 text-white btn-ver-detalle-zona" style="pointer-events:auto;"><i class=\'fas fa-eye\'></i> Ver detalles</a>';
                    poly.bindPopup(popup, {maxWidth: 320});
                    // Modal para detalle de zona
                    if ($('#modalDetalleZona').length === 0) {
                        $('body').append(`
                        <div class="modal fade" id="modalDetalleZona" tabindex="-1" role="dialog" aria-labelledby="modalDetalleZonaLabel" aria-hidden="true">
                          <div class="modal-dialog modal-lg" role="document">
                            <div class="modal-content" id="modalDetalleZonaContent">
                            </div>
                          </div>
                        </div>
                        `);
                    }

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
                }
            }
        });
    <?php endif; ?>
    // Si estamos en edit, resaltar la zona actual
    <?php if(isset($zone) && isset($zone->coords) && count($zone->coords) > 2): ?>
        var mainZoneCoords = <?php echo json_encode($zone->coords->map(function($c){return ['lat'=>$c->latitude, 'lng'=>$c->longitude];})->toArray(), 512) ?>;
        var mainPoly = L.polygon(mainZoneCoords, {
            color: '#007bff',
            fillColor: '#007bff',
            fillOpacity: 0.25,
            weight: 3
        }).addTo(map);
        mainPoly.bindTooltip('Zona en edición', {permanent: false, direction: 'center'});
    <?php endif; ?>
    if (!<?php echo json_encode(!empty($visualizacion), 15, 512) ?>) {
        drawControl = new L.Control.Draw({
            draw: {
                polygon: {
                    allowIntersection: false,
                    showArea: true,
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
        map.addControl(drawControl);
    }
    function validatePolygonAjax(coords, onSuccess, onError) {
        $.ajax({
            url: "<?php echo e(route('admin.zonesjenkz.validatePolygon')); ?>",
            method: 'POST',
            data: {
                coords: coords,
                _token: '<?php echo e(csrf_token()); ?>'
            },
            success: function(resp) {
                if (resp.valid) {
                    if (onSuccess) onSuccess();
                } else {
                    if (onError) onError(resp.message);
                }
            },
            error: function() {
                if (onError) onError('Error de validación.');
            }
        });
    }

    map.on(L.Draw.Event.CREATED, function (e) {
        if (currentPolygon) {
            drawnItems.removeLayer(currentPolygon);
        }
        currentPolygon = e.layer;
        drawnItems.addLayer(currentPolygon);
        let coords = currentPolygon.getLatLngs()[0].map(function(latlng) {
            return { lat: latlng.lat, lng: latlng.lng };
        });
        validatePolygonAjax(coords, function() {
            updatePolygonData();
        }, function(msg) {
            drawnItems.removeLayer(currentPolygon);
            currentPolygon = null;
            updatePolygonData();
            Swal.fire({ icon: 'error', title: 'Zona inválida', text: msg });
        });
    });
    map.on(L.Draw.Event.EDITED, function (e) {
        if (!currentPolygon) return;
        let coords = currentPolygon.getLatLngs()[0].map(function(latlng) {
            return { lat: latlng.lat, lng: latlng.lng };
        });
        validatePolygonAjax(coords, function() {
            updatePolygonData();
        }, function(msg) {
            drawnItems.removeLayer(currentPolygon);
            currentPolygon = null;
            updatePolygonData();
            Swal.fire({ icon: 'error', title: 'Zona inválida', text: msg });
        });
    });
    map.on(L.Draw.Event.DELETED, function (e) {
        currentPolygon = null;
        updatePolygonData();
    });
    $('#drawPolygon').click(function() {
        new L.Draw.Polygon(map, drawControl.options.draw.polygon).enable();
    });
    $('#clearPolygon').click(function() {
        if (currentPolygon) {
            drawnItems.removeLayer(currentPolygon);
            currentPolygon = null;
            updatePolygonData();
        }
        if (typeof mainPoly !== 'undefined' && map.hasLayer(mainPoly)) {
            map.removeLayer(mainPoly);
        }
    });
    $('#resetMap').click(function() {
        if (currentPolygon) {
            drawnItems.removeLayer(currentPolygon);
            currentPolygon = null;
        }
        map.setView([-12.0464, -77.0428], 16);
        updatePolygonData();
    });
    // --- CAPTURA GLOBAL DE COORDENADAS DEL POLÍGONO ---
    window.zoneCoords = [];
    function updatePolygonData() {
        let vertices = 0;
        let area = 0;
        let coords = [];
        if (currentPolygon) {
            coords = currentPolygon.getLatLngs()[0];
            vertices = coords.length;
            area = calculatePolygonArea(coords);
        }
        // Actualizar variable global para AJAX
        window.zoneCoords = coords.map(function(latlng) {
            return { lat: latlng.lat, lng: latlng.lng };
        });
        // Actualizar campos del formulario si existen
        if ($('#vertices_count').length) {
            $('#vertices_count').val(vertices);
        }
        if ($('#area_km2').length) {
            $('#area_km2').val(area.toFixed(2));
        }
        if ($('#coords').length) {
            $('#coords').val(JSON.stringify(coords));
        }
    }
    // Cálculo de área en km² usando Shoelace formula
    function calculatePolygonArea(latlngs) {
        if (latlngs.length < 3) return 0;
        let area = 0;
        const earthRadius = 6371;
        for (let i = 0; i < latlngs.length; i++) {
            const j = (i + 1) % latlngs.length;
            const lat1 = latlngs[i].lat * Math.PI / 180;
            const lat2 = latlngs[j].lat * Math.PI / 180;
            const deltaLng = (latlngs[j].lng - latlngs[i].lng) * Math.PI / 180;
            area += deltaLng * (2 + Math.sin(lat1) + Math.sin(lat2));
        }
        area = Math.abs(area * earthRadius * earthRadius / 2);
        return area;
    }
});
</script>
<?php $__env->stopPush(); ?>
<?php /**PATH C:\Users\crist\OneDrive\Escritorio\TADS\PryFinal\PryRsuReciclaje\resources\views/admin/zones_jenkz/template/map.blade.php ENDPATH**/ ?>