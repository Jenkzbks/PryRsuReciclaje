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
            <div class="col-8"><span class="badge badge-primary font-size-sm">{{ $route->code }}</span></div>
        </div>
        <div class="row mb-3">
            <div class="col-4"><strong>Nombre:</strong></div>
            <div class="col-8">{{ $route->name }}</div>
        </div>
        <div class="row mb-3">
            <div class="col-4"><strong>Zona:</strong></div>
            <div class="col-8">{{ $route->zone->name ?? '-' }}</div>
        </div>
        <div class="row mb-3">
            <div class="col-4"><strong>Descripción:</strong></div>
            <div class="col-8">{{ $route->description ?: 'Sin descripción' }}</div>
        </div>
        <div class="row mb-3">
            <div class="col-4"><strong>Punto Inicio:</strong></div>
            <div class="col-8">{{ $start ? ($start->latitude . ', ' . $start->longitude) : '-' }}</div>
        </div>
        <div class="row mb-3">
            <div class="col-4"><strong>Punto Fin:</strong></div>
            <div class="col-8">{{ $end ? ($end->latitude . ', ' . $end->longitude) : '-' }}</div>
        </div>
        <div class="row mb-3">
            <div class="col-4"><strong>Distancia (km):</strong></div>
            <div class="col-8">{{ $route->distance ? number_format($route->distance, 3) : '-' }}</div>
        </div>
        <div class="row mb-3">
            <div class="col-4"><strong>Creado:</strong></div>
            <div class="col-8">{{ $route->created_at->format('d/m/Y H:i') }}</div>
        </div>
        <div class="row mb-3">
            <div class="col-4"><strong>Actualizado:</strong></div>
            <div class="col-8">{{ $route->updated_at->format('d/m/Y H:i') }}</div>
        </div>
    </div>
</div>
