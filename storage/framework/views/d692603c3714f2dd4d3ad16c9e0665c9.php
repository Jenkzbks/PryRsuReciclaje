<div class="card shadow-sm">
    <div class="card-header bg-light">
        <h5 class="card-title mb-0">
            <i class="fas fa-info-circle text-primary"></i>
            Información de la Ruta
        </h5>
    </div>
    <div class="card-body">
        <div class="row mb-3">
            <div class="col-4"><strong>Código:</strong></div>
            <div class="col-8"><span class="badge badge-primary font-size-sm"><?php echo e($route->code); ?></span></div>
        </div>
        <div class="row mb-3">
            <div class="col-4"><strong>Nombre:</strong></div>
            <div class="col-8"><?php echo e($route->name); ?></div>
        </div>
        <div class="row mb-3">
            <div class="col-4"><strong>Zona:</strong></div>
            <div class="col-8"><?php echo e($route->zone->name ?? '-'); ?></div>
        </div>
        <div class="row mb-3">
            <div class="col-4"><strong>Descripción:</strong></div>
            <div class="col-8"><?php echo e($route->description ?: 'Sin descripción'); ?></div>
        </div>
        <div class="row mb-3">
            <div class="col-4"><strong>Punto Inicio:</strong></div>
            <div class="col-8"><?php echo e($start ? ($start->latitude . ', ' . $start->longitude) : '-'); ?></div>
        </div>
        <div class="row mb-3">
            <div class="col-4"><strong>Punto Fin:</strong></div>
            <div class="col-8"><?php echo e($end ? ($end->latitude . ', ' . $end->longitude) : '-'); ?></div>
        </div>
        <div class="row mb-3">
            <div class="col-4"><strong>Distancia (km):</strong></div>
            <div class="col-8"><?php echo e($route->distance ? number_format($route->distance, 3) : '-'); ?></div>
        </div>
        <div class="row mb-3">
            <div class="col-4"><strong>Creado:</strong></div>
            <div class="col-8"><?php echo e($route->created_at->format('d/m/Y H:i')); ?></div>
        </div>
        <div class="row mb-3">
            <div class="col-4"><strong>Actualizado:</strong></div>
            <div class="col-8"><?php echo e($route->updated_at->format('d/m/Y H:i')); ?></div>
        </div>
    </div>
</div>
<?php /**PATH F:\USAT 2025-2\topicos\PryFINAL\PryRsuReciclaje\resources\views/admin/routes_zone/template/info_show.blade.php ENDPATH**/ ?>