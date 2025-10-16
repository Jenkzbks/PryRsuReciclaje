@if($start && $end)
    <div id="map" style="height: 500px; border-radius: 8px;"></div>
    @section('css')
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    @endsection
    @section('js')
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
        <script>
            let map;
            let routeLine;
            let startMarker;
            let endMarker;
            $(document).ready(function() {
                map = L.map('map').setView([{{ $start->latitude }}, {{ $start->longitude }}], 13);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '© OpenStreetMap contributors'
                }).addTo(map);
                startMarker = L.marker([{{ $start->latitude }}, {{ $start->longitude }}], { draggable: false }).addTo(map).bindTooltip('Inicio', {permanent: true, direction: 'top'}).openTooltip();
                endMarker = L.marker([{{ $end->latitude }}, {{ $end->longitude }}], { draggable: false }).addTo(map).bindTooltip('Fin', {permanent: true, direction: 'top'}).openTooltip();
                routeLine = L.polyline([
                    [{{ $start->latitude }}, {{ $start->longitude }}],
                    [{{ $end->latitude }}, {{ $end->longitude }}]
                ], {color: 'blue', weight: 4}).addTo(map);
                let bounds = L.latLngBounds([startMarker.getLatLng(), endMarker.getLatLng()]);
                map.fitBounds(bounds, {padding: [40, 40]});
                routeLine.bindPopup(`
                    <div style='text-align:center;'>
                        <h6><strong>{{ $route->name }}</strong></h6>
                        <p class='mb-1'><strong>Código:</strong> {{ $route->code }}</p>
                        <p class='mb-1'><strong>Distancia:</strong> {{ number_format($route->distance, 3) }} km</p>
                        <p class='mb-0'><strong>Zona:</strong> {{ $route->zone->name ?? '-' }}</p>
                    </div>
                `);
                setTimeout(() => { routeLine.openPopup(); }, 500);
            });
        </script>
    @endsection
@else
    <div class="text-center py-5">
        <i class="fas fa-map-marked-alt text-muted" style="font-size: 4rem;"></i>
        <h5 class="text-muted mt-3">Sin puntos asignados</h5>
        <p class="text-muted">Esta ruta no tiene puntos de inicio y fin definidos.</p>
        <a href="{{ route('admin.routes.edit', $route) }}" class="btn btn-warning">
            <i class="fas fa-plus"></i> Asignar puntos
        </a>
    </div>
@endif
