@extends('adminlte::page')

@section('title', 'Crear Zona')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="h3 mb-1 font-weight-bold">Nueva Zona</h1>
            <p class="text-muted mb-0">Llene el formulario para agregar/modificar la zona.</p>
        </div>
        <a href="{{ route('admin.zonesjenkz.index') }}" class="btn btn-dark"><i class="fas fa-arrow-left"></i> Regresar</a>
    </div>
@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function() {
    $('#zone-form').on('submit', function(e) {
        e.preventDefault();
        var form = $(this);
        var formData = new FormData(this);
        // Si coords se maneja como variable JS, agregarla manualmente:
        if (typeof window.zoneCoords !== 'undefined') {
            formData.delete('coords[]'); // Por si acaso
            window.zoneCoords.forEach(function(coord, i) {
                formData.append('coords['+i+'][lat]', coord.lat);
                formData.append('coords['+i+'][lng]', coord.lng);
            });
        }
        $.ajax({
            url: form.attr('action'),
            method: form.attr('method'),
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                Swal.fire({
                    icon: 'success',
                    title: '¡Zona registrada!',
                    text: 'La zona se guardó correctamente.',
                    timer: 1800,
                    showConfirmButton: false
                }).then(() => {
                    window.location.href = "{{ route('admin.zonesjenkz.index') }}";
                });
            },
            error: function(xhr) {
                if (xhr.status === 422) {
                    var errors = xhr.responseJSON.errors;
                    var msg = '';
                    Object.keys(errors).forEach(function(key) {
                        msg += errors[key].join('<br>') + '<br>';
                    });
                    Swal.fire({
                        icon: 'error',
                        title: 'Errores de validación',
                        html: msg
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Ocurrió un error inesperado.'
                    });
                }
            }
        });
    });
});
</script>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row align-items-stretch">
        <!-- Formulario -->
        <div class="col-md-6 d-flex">
            <div class="card shadow-sm w-100">
                <div class="card-body">
                    {!! Form::open(['route' => 'admin.zonesjenkz.store', 'method' => 'POST', 'id' => 'zone-form']) !!}
                        @include('admin.zones_jenkz.template.form')
                        <div class="mt-3">
                            <a href="{{ route('admin.zonesjenkz.index') }}" class="btn btn-danger"> <i class="fas fa-ban mr-1"></i> Cancelar</a>
                            <button type="submit" class="btn btn-primary"><i class="fas fa-save mr-1"></i> Guardar</button>
                        </div>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
        <!-- Mapa -->
        <div class="col-md-6 d-flex">
            <div class="card shadow-sm w-100">
                <div class="card-body">
                    @include('admin.zones_jenkz.template.map')
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
