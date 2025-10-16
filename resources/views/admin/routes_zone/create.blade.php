@extends('adminlte::page')
@section('title', 'Crear Ruta')
@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="h3 mb-1 font-weight-bold">Crear Ruta</h1>
            <p class="text-muted mb-0">Llene el formulario para agregar la ruta.</p>
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
                        {!! Form::open(['route' => 'admin.routes.store']) !!}
                            @include('admin.routes_zone.template.form')
                            <a href="{{ route('admin.routes.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Regresar
                            </a>
                            <button type="submit" class="btn btn-success"><i class='fas fa-save'></i> Registrar</button>
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
            <!-- Mapa -->
            <div class="col-md-6 d-flex">
                <div class="card shadow-sm w-100">
                    <div class="card-body">
                        @include('admin.routes_zone.template.map')
                    </div>
                </div>
            </div>
        </div>
    </div>
    
@stop
