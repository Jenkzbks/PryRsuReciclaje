@extends('adminlte::page')
@section('title', 'Editar Ruta')
@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="h3 mb-1 font-weight-bold">Editar Ruta</h1>
            <p class="text-muted mb-0">Modifica los datos y la ubicación de la ruta.</p>
        </div>
    </div>
@stop
@section('content')
    <div class="container-fluid">
        <div class="row align-items-stretch">
            <!-- Formulario -->
            <div class="col-md-6 d-flex">
                <div class="card shadow-sm w-100">
                    <div class="card-body">
                        {!! Form::model($route, ['route' => ['admin.routes.update', $route->id], 'method' => 'PUT']) !!}
                            @include('admin.routes_zone.template.form', [
                                'start' => $start,
                                'end' => $end,
                                'edit' => true
                            ])
                            <a href="{{ route('admin.routes.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Regresar
                            </a>
                            <button type="submit" class="btn btn-success"><i class='fas fa-save'></i> Actualizar</button>
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
            <!-- Mapa -->
            <div class="col-md-6 d-flex">
                <div class="card shadow-sm w-100">
                    <div class="card-body">
                        @include('admin.routes_zone.template.map', [
                            'start' => $start,
                            'end' => $end,
                            'route' => $route
                        ])
                    </div>
                </div>
            </div>
        </div>
    </div>
    
@stop
