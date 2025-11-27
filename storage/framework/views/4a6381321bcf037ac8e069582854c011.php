<?php $__env->startSection('title', 'Ver Ruta - ' . $route->name); ?>

<?php $__env->startSection('content_header'); ?>
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="h3 mb-1 font-weight-bold">Ver Ruta - <?php echo e($route->name); ?></h1>
            <p class="text-muted mb-0">Detalles completos de la ruta.</p>
        </div>
        <div class="btn-group">
            <a href="<?php echo e(route('admin.routes.index')); ?>" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Regresar
            </a>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <div class="row align-items-stretch">
        <!-- Información de la ruta -->
        <div class="col-md-6">
            <div class="w-100">
                <?php echo $__env->make('admin.routes_zone.template.info_show', ['route' => $route, 'start' => $start, 'end' => $end], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            </div>
        </div>
        <!-- Mapa -->
        <div class="col-md-6">
            <div class="card shadow-sm w-100">
                <div class="card-header bg-light">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-map text-success"></i>
                        Visualización de la Ruta
                    </h5>
                </div>
                <div class="card-body">
                    <?php echo $__env->make('admin.routes_zone.template.map_show', ['route' => $route, 'start' => $start, 'end' => $end], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('css'); ?>
    <?php if($start && $end): ?>
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <?php endif; ?>
    <style>
        body { background-color: #f9f7f7 !important; }
        .card { border: none; border-radius: 12px; }
        .badge { font-size: 0.9rem; }
        .font-size-sm { font-size: 1rem !important; }
        #map { border: 2px solid #e9ecef; }
    </style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('js'); ?>
    <?php if($start && $end): ?>
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
        <script>
            let map;
            let routeLine;
            let startMarker;
            let endMarker;
            $(document).ready(function() {
                initializeMap();
                loadRouteLine();
            });
            function initializeMap() {
                map = L.map('map').setView([<?php echo e($start->latitude); ?>, <?php echo e($start->longitude); ?>], 13);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '© OpenStreetMap contributors'
                }).addTo(map);
            }
            function loadRouteLine() {
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
            }
        </script>
    <?php endif; ?>
    <script>
        <?php if(session('success')): ?>
            Swal.fire({ icon: 'success', title: '¡Éxito!', text: '<?php echo e(session('success')); ?>' });
        <?php endif; ?>
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('adminlte::page', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH F:\USAT 2025-2\topicos\PryFINAL\PryRsuReciclaje\resources\views/admin/routes_zone/show.blade.php ENDPATH**/ ?>