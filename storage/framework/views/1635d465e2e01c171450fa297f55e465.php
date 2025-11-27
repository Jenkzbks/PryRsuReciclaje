<?php if($start && $end): ?>
    <div id="map" style="height: 500px; border-radius: 8px;"></div>
    <?php $__env->startSection('css'); ?>
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <?php $__env->stopSection(); ?>
    <?php $__env->startSection('js'); ?>
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
        <script>
            let map;
            let routeLine;
            let startMarker;
            let endMarker;
            $(document).ready(function() {
                map = L.map('map').setView([<?php echo e($start->latitude); ?>, <?php echo e($start->longitude); ?>], 13);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '© OpenStreetMap contributors'
                }).addTo(map);
                startMarker = L.marker([<?php echo e($start->latitude); ?>, <?php echo e($start->longitude); ?>], { draggable: false }).addTo(map).bindTooltip('Inicio', {permanent: true, direction: 'top'}).openTooltip();
                endMarker = L.marker([<?php echo e($end->latitude); ?>, <?php echo e($end->longitude); ?>], { draggable: false }).addTo(map).bindTooltip('Fin', {permanent: true, direction: 'top'}).openTooltip();
                routeLine = L.polyline([
                    [<?php echo e($start->latitude); ?>, <?php echo e($start->longitude); ?>],
                    [<?php echo e($end->latitude); ?>, <?php echo e($end->longitude); ?>]
                ], {color: 'blue', weight: 4}).addTo(map);
                let bounds = L.latLngBounds([startMarker.getLatLng(), endMarker.getLatLng()]);
                map.fitBounds(bounds, {padding: [40, 40]});
                routeLine.bindPopup(`
                    <div style='text-align:center;'>
                        <h6><strong><?php echo e($route->name); ?></strong></h6>
                        <p class='mb-1'><strong>Código:</strong> <?php echo e($route->code); ?></p>
                        <p class='mb-1'><strong>Distancia:</strong> <?php echo e(number_format($route->distance, 3)); ?> km</p>
                        <p class='mb-0'><strong>Zona:</strong> <?php echo e($route->zone->name ?? '-'); ?></p>
                    </div>
                `);
                setTimeout(() => { routeLine.openPopup(); }, 500);
            });
        </script>
    <?php $__env->stopSection(); ?>
<?php else: ?>
    <div class="text-center py-5">
        <i class="fas fa-map-marked-alt text-muted" style="font-size: 4rem;"></i>
        <h5 class="text-muted mt-3">Sin puntos asignados</h5>
        <p class="text-muted">Esta ruta no tiene puntos de inicio y fin definidos.</p>
        <a href="<?php echo e(route('admin.routes.edit', $route)); ?>" class="btn btn-warning">
            <i class="fas fa-plus"></i> Asignar puntos
        </a>
    </div>
<?php endif; ?>
<?php /**PATH F:\USAT 2025-2\topicos\PryFINAL\PryRsuReciclaje\resources\views/admin/routes_zone/template/map_show.blade.php ENDPATH**/ ?>