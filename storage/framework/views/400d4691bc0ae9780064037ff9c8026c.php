<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="card-title mb-0">Asignar Ruta</h5>
    <div class="btn-group" role="group">
        <button type="button" class="btn btn-success btn-sm" id="draw-route-btn"><i class="fas fa-pencil-alt"></i> Dibujar Ruta</button>
        <button type="button" class="btn btn-warning btn-sm" id="delete-route-btn"><i class="fas fa-trash"></i> Borrar</button>
        <button type="button" class="btn btn-secondary btn-sm" id="reset-route-btn"><i class="fas fa-undo"></i> Restablecer</button>
    </div>
</div>
<div id="map" style="height: 90%; min-height: 300px; border-radius: 8px; width: 100%;"></div>

<small class="text-muted">Haz clic en "Dibujar Ruta" y luego selecciona el punto de inicio y fin en el mapa.</small>
<?php $__env->startSection('css'); ?>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" />
    
<?php $__env->stopSection(); ?>
<?php $__env->startSection('js'); ?>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    let map = L.map('map').setView([-12.0464, -77.0428], 13);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '© OpenStreetMap'
    }).addTo(map);

    let startMarker = null;
    let endMarker = null;
    let line = null;
    let drawing = false;

    function updateCoordsAndDistance() {
        const startLat = startMarker ? startMarker.getLatLng().lat : '';
        const startLng = startMarker ? startMarker.getLatLng().lng : '';
        const endLat = endMarker ? endMarker.getLatLng().lat : '';
        const endLng = endMarker ? endMarker.getLatLng().lng : '';
        if (startMarker && endMarker) {
            document.getElementById('start-coords').value = `${startLat.toFixed(6)}, ${startLng.toFixed(6)}`;
            document.getElementById('end-coords').value = `${endLat.toFixed(6)}, ${endLng.toFixed(6)}`;
            document.getElementById('start_latitude').value = startLat;
            document.getElementById('start_longitude').value = startLng;
            document.getElementById('end_latitude').value = endLat;
            document.getElementById('end_longitude').value = endLng;
            let distance = (map.distance(startMarker.getLatLng(), endMarker.getLatLng()) / 1000).toFixed(3);
            document.getElementById('distance-label').value = distance;
            document.getElementById('distance').value = distance;
        } else {
            document.getElementById('start-coords').value = '-';
            document.getElementById('end-coords').value = '-';
            document.getElementById('start_latitude').value = '';
            document.getElementById('start_longitude').value = '';
            document.getElementById('end_latitude').value = '';
            document.getElementById('end_longitude').value = '';
            document.getElementById('distance-label').value = '-';
            document.getElementById('distance').value = '';
        }
    }

    <?php if(isset($start) && isset($end) && isset($route)): ?>
        let startLat = <?php echo e($start->latitude); ?>;
        let startLng = <?php echo e($start->longitude); ?>;
        let endLat = <?php echo e($end->latitude); ?>;
        let endLng = <?php echo e($end->longitude); ?>;
        startMarker = L.marker([startLat, startLng], { draggable: true }).addTo(map).bindTooltip('Inicio', {permanent: true, direction: 'top'}).openTooltip();
        endMarker = L.marker([endLat, endLng], { draggable: true }).addTo(map).bindTooltip('Fin', {permanent: true, direction: 'top'}).openTooltip();
        line = L.polyline([startMarker.getLatLng(), endMarker.getLatLng()], {color: 'blue'}).addTo(map);
        let bounds = L.latLngBounds([startMarker.getLatLng(), endMarker.getLatLng()]);
        map.fitBounds(bounds, {padding: [40, 40]});
        updateCoordsAndDistance();
        startMarker.on('dragend', function() {
            if (line) map.removeLayer(line);
            if (endMarker) line = L.polyline([startMarker.getLatLng(), endMarker.getLatLng()], {color: 'blue'}).addTo(map);
            updateCoordsAndDistance();
        });
        endMarker.on('dragend', function() {
            if (line) map.removeLayer(line);
            if (startMarker) line = L.polyline([startMarker.getLatLng(), endMarker.getLatLng()], {color: 'blue'}).addTo(map);
            updateCoordsAndDistance();
        });
    <?php endif; ?>
    

    function updateCoordsAndDistance() {
        const startLat = startMarker ? startMarker.getLatLng().lat : '';
        const startLng = startMarker ? startMarker.getLatLng().lng : '';
        const endLat = endMarker ? endMarker.getLatLng().lat : '';
        const endLng = endMarker ? endMarker.getLatLng().lng : '';
        document.getElementById('start-coords').value = startLat && startLng ? `${startLat.toFixed(6)}, ${startLng.toFixed(6)}` : '-';
        document.getElementById('end-coords').value = endLat && endLng ? `${endLat.toFixed(6)}, ${endLng.toFixed(6)}` : '-';
        document.getElementById('start_latitude').value = startLat;
        document.getElementById('start_longitude').value = startLng;
        document.getElementById('end_latitude').value = endLat;
        document.getElementById('end_longitude').value = endLng;
        let distance = '';
        if (startMarker && endMarker) {
            distance = (map.distance(startMarker.getLatLng(), endMarker.getLatLng()) / 1000).toFixed(3);
            document.getElementById('distance-label').value = distance;
            document.getElementById('distance').value = distance;
        } else {
            document.getElementById('distance-label').value = '-';
            document.getElementById('distance').value = '';
        }
    }

    document.getElementById('draw-route-btn').addEventListener('click', function() {
        drawing = true;
        map.getContainer().style.cursor = 'crosshair';
    });

    map.on('click', function(e) {
        if (!drawing) return;
        if (!startMarker) {
            startMarker = L.marker(e.latlng, { draggable: true }).addTo(map).bindTooltip('Inicio', {permanent: true, direction: 'top'}).openTooltip();
            startMarker.on('dragend', function() {
                if (endMarker) {
                    if (line) map.removeLayer(line);
                    line = L.polyline([startMarker.getLatLng(), endMarker.getLatLng()], {color: 'blue'}).addTo(map);
                }
                updateCoordsAndDistance();
            });
        } else if (!endMarker) {
            endMarker = L.marker(e.latlng, { draggable: true }).addTo(map).bindTooltip('Fin', {permanent: true, direction: 'top'}).openTooltip();
            endMarker.on('dragend', function() {
                if (line) map.removeLayer(line);
                line = L.polyline([startMarker.getLatLng(), endMarker.getLatLng()], {color: 'blue'}).addTo(map);
                updateCoordsAndDistance();
            });
            line = L.polyline([startMarker.getLatLng(), endMarker.getLatLng()], {color: 'blue'}).addTo(map);
            drawing = false;
            map.getContainer().style.cursor = '';
        } else {
            // Si ambos puntos existen, reiniciar
            map.removeLayer(startMarker);
            map.removeLayer(endMarker);
            if (line) map.removeLayer(line);
            startMarker = L.marker(e.latlng, { draggable: true }).addTo(map).bindTooltip('Inicio', {permanent: true, direction: 'top'}).openTooltip();
            endMarker = null;
            line = null;
            startMarker.on('dragend', function() {
                updateCoordsAndDistance();
            });
        }
        updateCoordsAndDistance();
    });

    document.getElementById('delete-route-btn').addEventListener('click', function() {
        if (startMarker) { map.removeLayer(startMarker); startMarker = null; }
        if (endMarker) { map.removeLayer(endMarker); endMarker = null; }
        if (line) { map.removeLayer(line); line = null; }
        updateCoordsAndDistance();
        drawing = true;
        map.getContainer().style.cursor = 'crosshair';
    });

    document.getElementById('reset-route-btn').addEventListener('click', function() {
        if (startMarker) { map.removeLayer(startMarker); startMarker = null; }
        if (endMarker) { map.removeLayer(endMarker); endMarker = null; }
        if (line) { map.removeLayer(line); line = null; }
        updateCoordsAndDistance();
        drawing = false;
        map.getContainer().style.cursor = '';
    });
</script>
<?php $__env->stopSection(); ?>
<?php /**PATH F:\USAT 2025-2\topicos\PryFINAL\PryRsuReciclaje\resources\views/admin/routes_zone/template/map.blade.php ENDPATH**/ ?>