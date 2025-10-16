@extends('adminlte::page')

@section('title', 'Ver Zona - ' . $zone->name)

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="h3 mb-1 font-weight-bold">Ver Zona - {{ $zone->name }}</h1>
            <p class="text-muted mb-0">Detalles completos de la zona de recolección.</p>
        </div>
        <div class="btn-group">
            <a href="{{ route('admin.zones.edit', $zone) }}" class="btn btn-primary">
                <i class="fas fa-edit"></i> Editar
            </a>
            <a href="{{ route('admin.zones.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Regresar
            </a>
        </div>
    </div>
@stop

@section('content')
    <div class="row">
        <!-- Información de la zona -->
        <div class="col-md-5">
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-info-circle text-primary"></i>
                        Información de la Zona
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-4">
                            <strong>Código:</strong>
                        </div>
                        <div class="col-8">
                            <span class="badge badge-primary font-size-sm">{{ $zone->code }}</span>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-4">
                            <strong>Nombre:</strong>
                        </div>
                        <div class="col-8">
                            {{ $zone->name }}
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-4">
                            <strong>Departamento:</strong>
                        </div>
                        <div class="col-8">
                            {{ $zone->district->department->name }}
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-4">
                            <strong>Provincia:</strong>
                        </div>
                        <div class="col-8">
                            {{ $zone->district->province->name }}
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-4">
                            <strong>Distrito:</strong>
                        </div>
                        <div class="col-8">
                            {{ $zone->district->name }}
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-4">
                            <strong>Descripción:</strong>
                        </div>
                        <div class="col-8">
                            {{ $zone->description ?: 'Sin descripción' }}
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-4">
                            <strong>Perímetro:</strong>
                        </div>
                        <div class="col-8">
                            @if($zone->hasPolygon())
                                <span class="badge badge-success">
                                    <i class="fas fa-check-circle"></i> Asignado
                                </span>
                            @else
                                <span class="badge badge-danger">
                                    <i class="fas fa-times-circle"></i> No asignado
                                </span>
                            @endif
                        </div>
                    </div>

                    @if($zone->hasPolygon())
                        <div class="row mb-3">
                            <div class="col-4">
                                <strong># Vértices:</strong>
                            </div>
                            <div class="col-8">
                                {{ count($zone->polygon_coordinates) }}
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-4">
                                <strong>Área (km²):</strong>
                            </div>
                            <div class="col-8">
                                {{ number_format($zone->area, 2) }} km²
                            </div>
                        </div>
                    @endif

                    <div class="row mb-3">
                        <div class="col-4">
                            <strong>Creado:</strong>
                        </div>
                        <div class="col-8">
                            {{ $zone->created_at->format('d/m/Y H:i') }}
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-4">
                            <strong>Actualizado:</strong>
                        </div>
                        <div class="col-8">
                            {{ $zone->updated_at->format('d/m/Y H:i') }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Acciones -->
            <div class="card shadow-sm mt-3">
                <div class="card-header bg-light">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-cog text-secondary"></i>
                        Acciones
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.zones.edit', $zone) }}" class="btn btn-primary btn-block">
                            <i class="fas fa-edit"></i> Editar Zona
                        </a>
                        
                        @if(!$zone->hasPolygon())
                            <a href="{{ route('admin.zones.edit', $zone) }}" class="btn btn-warning btn-block">
                                <i class="fas fa-map-marked-alt"></i> Asignar Perímetro
                            </a>
                        @endif

                        <form method="POST" 
                              action="{{ route('admin.zones.destroy', $zone) }}" 
                              class="d-inline"
                              onsubmit="return confirm('¿Está seguro de eliminar esta zona? Esta acción no se puede deshacer.')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-block">
                                <i class="fas fa-trash"></i> Eliminar Zona
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Mapa -->
        <div class="col-md-7">
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-map text-success"></i>
                        Visualización del Perímetro
                    </h5>
                </div>
                <div class="card-body">
                    @if($zone->hasPolygon())
                        <div id="map" style="height: 500px; border-radius: 8px;"></div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-map-marked-alt text-muted" style="font-size: 4rem;"></i>
                            <h5 class="text-muted mt-3">Sin perímetro asignado</h5>
                            <p class="text-muted">Esta zona no tiene un perímetro geográfico definido.</p>
                            <a href="{{ route('admin.zones.edit', $zone) }}" class="btn btn-warning">
                                <i class="fas fa-plus"></i> Asignar Perímetro
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    @if($zone->hasPolygon())
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    @endif
    <style>
        body {
            background-color: #f9f7f7 !important;
        }
        .card {
            border: none;
            border-radius: 12px;
        }
        .badge {
            font-size: 0.9rem;
        }
        .font-size-sm {
            font-size: 1rem !important;
        }
        #map {
            border: 2px solid #e9ecef;
        }
    </style>
@stop

@section('js')
    @if($zone->hasPolygon())
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
        <script>
            let map;
            let zonePolygon;

            $(document).ready(function() {
                initializeMap();
                loadZonePolygon();
            });

            function initializeMap() {
                // Inicializar mapa
                map = L.map('map').setView([-12.0464, -77.0428], 10);

                // Agregar tile layer
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '© OpenStreetMap contributors'
                }).addTo(map);
            }

            function loadZonePolygon() {
                const coordinates = @json($zone->polygon_coordinates);
                
                if (coordinates && coordinates.length > 0) {
                    const latlngs = coordinates.map(coord => [coord[0], coord[1]]);
                    
                    zonePolygon = L.polygon(latlngs, {
                        color: '#28a745',
                        fillColor: '#28a745',
                        fillOpacity: 0.4,
                        weight: 3
                    }).addTo(map);

                    // Agregar popup con información
                    zonePolygon.bindPopup(`
                        <div style="text-align: center;">
                            <h6><strong>{{ $zone->name }}</strong></h6>
                            <p class="mb-1"><strong>Código:</strong> {{ $zone->code }}</p>
                            <p class="mb-1"><strong>Área:</strong> {{ number_format($zone->area, 2) }} km²</p>
                            <p class="mb-0"><strong>Vértices:</strong> {{ count($zone->polygon_coordinates) }}</p>
                        </div>
                    `);

                    // Centrar el mapa en el polígono
                    map.fitBounds(zonePolygon.getBounds(), { padding: [20, 20] });

                    // Abrir popup automáticamente
                    setTimeout(() => {
                        zonePolygon.openPopup();
                    }, 500);
                }
            }
        </script>
    @endif

    <script>
        @if(session('success'))
            toastr.success('{{ session('success') }}');
        @endif
    </script>
@stop