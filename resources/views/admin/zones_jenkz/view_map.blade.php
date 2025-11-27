@extends('adminlte::page')

@section('title', 'Mapa de Zonas')

@section('content_header')
    <h1>Mapa de Zonas</h1>
@endsection

@section('content')
<div class="mb-3">
    <div class="card shadow-sm">
        <div class="card-body pb-0" style="border-bottom:1px solid #e5e7eb;">
            <div class="d-flex justify-content-between align-items-center">
                <span class="text-muted font-weight-bold mb-3" style="margin-bottom:18px!important;">Visualización de zonas activas</span>
                <a href="{{ route('admin.zonesjenkz.index') }}" class="btn btn-primary mb-3" style="margin-bottom:18px!important;">
                   <i class="fas fa-list"></i> Volver al listado
                </a>
            </div>
        </div>
        <div class="card-body pt-2">
            @include('admin.zones_jenkz.template.map', [
                'zonesPolygons' => $zonesPolygons,
                'visualizacion' => true,
                'modal' => false
            ])
        </div>
    </div>
</div>
@endsection
