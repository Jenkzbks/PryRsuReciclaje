@extends('adminlte::page')

@section('title', 'Ver Ruta - ' . $route->name)

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="h3 mb-1 font-weight-bold">Ver Ruta - {{ $route->name }}</h1>
            <p class="text-muted mb-0">Detalles completos de la ruta.</p>
        </div>
        <div class="btn-group">
            <a href="{{ route('admin.routes.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Regresar
            </a>
        </div>
    </div>
@stop

@section('content')
    <div class="row align-items-stretch">
        <!-- Información de la ruta -->
        <div class="col-md-6">
            <div class="w-100">
                @include('admin.routes_zone.template.info_show', ['route' => $route, 'start' => $start, 'end' => $end])
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
                    @include('admin.routes_zone.template.map_show', ['route' => $route, 'start' => $start, 'end' => $end])
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    @if($start && $end)
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    @endif
    <style>
        body { background-color: #f9f7f7 !important; }
        .card { border: none; border-radius: 12px; }
        .badge { font-size: 0.9rem; }
        .font-size-sm { font-size: 1rem !important; }
        #map { border: 2px solid #e9ecef; }
    </style>
@stop

@section('js')
    @if($start && $end)
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
                map = L.map('map').setView([{{ $start->latitude }}, {{ $start->longitude }}], 13);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '© OpenStreetMap contributors'
                }).addTo(map);
            }
            function loadRouteLine() {
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
            }
        </script>
    @endif
    <script>
        @if(session('success'))
            Swal.fire({ icon: 'success', title: '¡Éxito!', text: '{{ session('success') }}' });
        @endif
    </script>
@stop
