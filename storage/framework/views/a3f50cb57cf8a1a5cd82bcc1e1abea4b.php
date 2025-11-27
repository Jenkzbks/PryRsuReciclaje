<?php $__env->startSection('title', 'Ver Zona - ' . $zone->name); ?>

<?php $__env->startSection('content_header'); ?>
    <?php if(!request()->ajax()): ?>
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="h3 mb-1 font-weight-bold">Ver Zona - <?php echo e($zone->name); ?></h1>
            <p class="text-muted mb-0">Detalles completos de la zona de recolección.</p>
        </div>
        <div class="btn-group">
            <a href="<?php echo e(route('admin.zones.edit', $zone)); ?>" class="btn btn-primary">
                <i class="fas fa-edit"></i> Editar
            </a>
            <a href="<?php echo e(route('admin.zones.index')); ?>" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Regresar
            </a>
        </div>
    </div>
    <?php endif; ?>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="row">
    <!-- Bloque superior: Mapa -->
    <div class="col-12 mb-4">
        <div class="card shadow-sm">
            <div class="card-header bg-light">
                <h5 class="card-title mb-0">
                    <i class="fas fa-map text-success"></i>
                    Visualización del Perímetro
                </h5>
            </div>
            <div class="card-body">
                <?php if($zone->hasPolygon()): ?>
                    <div id="map" style="height: 500px; border-radius: 8px;"></div>

                    <!-- Script inline para que funcione en modal (AJAX) y en vista completa -->
                    <script>
                        (function() {
                            if (window.__zoneShowMap) { try { window.__zoneShowMap.remove(); } catch(e){}; window.__zoneShowMap = null; }

                            const coords = <?php echo json_encode($zone->polygon_coordinates, 15, 512) ?>;
                            if (!coords || !coords.length) return;

                            // Centro inicial (fallback)
                            const map = L.map('map').setView([-12.0464, -77.0428], 10);
                            window.__zoneShowMap = map;

                            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                                attribution: '© OpenStreetMap contributors'
                            }).addTo(map);

                            const latlngs = coords.map(c => [c[0], c[1]]);
                            const polygon = L.polygon(latlngs, {
                                color: '#28a745', fillColor: '#28a745', fillOpacity: 0.4, weight: 3
                            }).addTo(map);

                            polygon.bindPopup(
                                `<div style="text-align:center;">
                                    <h6><strong><?php echo e($zone->name); ?></strong></h6>
                                    <p class="mb-1"><strong>Código:</strong> <?php echo e($zone->code); ?></p>
                                    <p class="mb-1"><strong>Área:</strong> <?php echo e(number_format($zone->area, 2)); ?> km²</p>
                                    <p class="mb-0"><strong>Vértices:</strong> <?php echo e(count($zone->polygon_coordinates)); ?></p>
                                 </div>`
                            );

                            map.fitBounds(polygon.getBounds(), { padding:[20,20] });
                            setTimeout(() => { try { map.invalidateSize(); polygon.openPopup(); } catch(e){} }, 350);
                        })();
                    </script>
                <?php else: ?>
                    <div class="text-center py-5">
                        <i class="fas fa-map-marked-alt text-muted" style="font-size: 4rem;"></i>
                        <h5 class="text-muted mt-3">Sin perímetro asignado</h5>
                        <p class="text-muted">Esta zona no tiene un perímetro geográfico definido.</p>
                        <a href="<?php echo e(route('admin.zones.edit', $zone)); ?>" class="btn btn-warning">
                            <i class="fas fa-plus"></i> Asignar Perímetro
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Bloques medios: Información + Coordenadas -->
    <div class="col-md-6">
        <!-- Información de la zona -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-light">
                <h5 class="card-title mb-0">
                    <i class="fas fa-info-circle text-primary"></i>
                    Información de la Zona
                </h5>
            </div>
            <div class="card-body">
                <div class="row mb-3"><div class="col-4"><strong>Código:</strong></div><div class="col-8"><span class="badge badge-primary"><?php echo e($zone->code); ?></span></div></div>
                <div class="row mb-3"><div class="col-4"><strong>Nombre:</strong></div><div class="col-8"><?php echo e($zone->name); ?></div></div>
                <div class="row mb-3"><div class="col-4"><strong>Departamento:</strong></div><div class="col-8"><?php echo e($zone->department?->name ?? 'No asignado'); ?></div></div>
                <div class="row mb-3"><div class="col-4"><strong>Provincia:</strong></div><div class="col-8">
                    <?php if($zone->district): ?>
                        <?php echo e($zone->district->province->name); ?>

                    <?php elseif($zone->province): ?>
                        <?php echo e($zone->province->name); ?>

                    <?php else: ?>
                        No asignado
                    <?php endif; ?>
                </div></div>
                <div class="row mb-3"><div class="col-4"><strong>Distrito:</strong></div><div class="col-8"><?php echo e($zone->district?->name ?? 'Toda la provincia'); ?></div></div>
                <div class="row mb-3"><div class="col-4"><strong>Descripción:</strong></div><div class="col-8"><?php echo e($zone->description ?: 'Sin descripción'); ?></div></div>
                <div class="row mb-3"><div class="col-4"><strong>Perímetro:</strong></div><div class="col-8">
                    <?php if($zone->hasPolygon()): ?>
                        <span class="badge badge-success"><i class="fas fa-check-circle"></i> Asignado</span>
                    <?php else: ?>
                        <span class="badge badge-danger"><i class="fas fa-times-circle"></i> No asignado</span>
                    <?php endif; ?>
                </div></div>
                <?php if($zone->hasPolygon()): ?>
                    <div class="row mb-3"><div class="col-4"><strong># Vértices:</strong></div><div class="col-8"><?php echo e(count($zone->polygon_coordinates)); ?></div></div>
                    <div class="row mb-3"><div class="col-4"><strong>Área (km²):</strong></div><div class="col-8"><?php echo e(number_format($zone->area, 2)); ?> km²</div></div>
                <?php endif; ?>
                <div class="row mb-3"><div class="col-4"><strong>Creado:</strong></div><div class="col-8"><?php echo e($zone->created_at->format('d/m/Y H:i')); ?></div></div>
                <div class="row"><div class="col-4"><strong>Actualizado:</strong></div><div class="col-8"><?php echo e($zone->updated_at->format('d/m/Y H:i')); ?></div></div>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <?php if($zone->zonecoords->count() > 0): ?>
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-light">
                <h5 class="card-title mb-0">
                    <i class="fas fa-map-marker-alt text-success"></i>
                    Detalle de Coordenadas
                </h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table mb-0 table-striped">
                        <thead class="thead-light">
                            <tr><th>#</th><th>Latitud</th><th>Longitud</th></tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $zone->zonecoords; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $coord): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td><?php echo e($index + 1); ?></td>
                                    <td><?php echo e($coord->latitude); ?></td>
                                    <td><?php echo e($coord->longitude); ?></td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <!-- Bloque inferior: Acciones -->
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-header bg-light">
                <h5 class="card-title mb-0">
                    <i class="fas fa-cog text-secondary"></i>
                    Acciones
                </h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2 d-md-flex justify-content-md-center flex-wrap">
                    <a href="<?php echo e(route('admin.zones.edit', $zone)); ?>" class="btn btn-primary m-2">
                        <i class="fas fa-edit"></i> Editar Zona
                    </a>

                    <?php if(!$zone->hasPolygon()): ?>
                        <a href="<?php echo e(route('admin.zones.edit', $zone)); ?>" class="btn btn-warning m-2">
                            <i class="fas fa-map-marked-alt"></i> Asignar Perímetro
                        </a>
                    <?php endif; ?>

                    <form method="POST" 
                        action="<?php echo e(route('admin.zones.destroy', $zone)); ?>" 
                        onsubmit="return confirm('¿Está seguro de eliminar esta zona? Esta acción no se puede deshacer.')">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('DELETE'); ?>
                        <button type="submit" class="btn btn-danger m-2">
                            <i class="fas fa-trash"></i> Eliminar Zona
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>


<?php $__env->startSection('css'); ?>
    <?php if(!request()->ajax()): ?>
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
        <style>
            body { background-color: #f9f7f7 !important; }
            .card { border: none; border-radius: 12px; }
            .badge { font-size: 0.9rem; }
            #map { border: 2px solid #e9ecef; }
        </style>
    <?php endif; ?>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('js'); ?>
    <?php if(!request()->ajax()): ?>
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('adminlte::page', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH F:\USAT 2025-2\topicos\PryFINAL\PryRsuReciclaje\resources\views/admin/zones/show.blade.php ENDPATH**/ ?>