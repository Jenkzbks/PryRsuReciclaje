@extends('adminlte::page')

@section('title', 'Editar Zona')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="h3 mb-1 font-weight-bold">Editar Zona</h1>
            <p class="text-muted mb-0">Modifique los datos de la zona.</p>
        </div>
        <a href="{{ route('admin.zonesjenkz.index') }}" class="btn btn-dark"><i class="fas fa-arrow-left"></i> Regresar</a>
    </div>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row align-items-stretch">
        <!-- Formulario -->
        <div class="col-md-6 d-flex">
            <div class="card shadow-sm w-100">
                <div class="card-body">
                    {!! Form::model($zone, ['route' => ['admin.zonesjenkz.update', $zone->id], 'method' => 'PUT', 'id' => 'zone-form']) !!}
                        @include('admin.zones_jenkz.template.form', ['departments' => $departments])
                        <div class="mt-3">
                            <button type="submit" class="btn btn-primary">Actualizar</button>
                            <a href="{{ route('admin.zonesjenkz.index') }}" class="btn btn-secondary">Cancelar</a>
                        </div>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
        <!-- Mapa -->
        <div class="col-md-6 d-flex">
            <div class="card shadow-sm w-100">
                <div class="card-body">
                    @include('admin.zones_jenkz.template.map', ['zone' => $zone])
                </div>
            </div>
        </div>
    </div>
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
        // Agregar token CSRF
        formData.append('_token', '{{ csrf_token() }}');
        // Forzar método POST y agregar _method=PUT
        formData.append('_method', 'PUT');
        $.ajax({
            url: form.attr('action'),
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            success: function(response) {
                Swal.fire({
                    icon: 'success',
                    title: '¡Zona actualizada!',
                    text: 'La zona se actualizó correctamente.',
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
                    let errorMsg = 'Ocurrió un error inesperado.';
                    if (xhr.responseText) {
                        errorMsg += '<br><small>' + xhr.responseText.substring(0, 500) + '</small>';
                    }
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        html: errorMsg
                    });
                }
            }
        });
    });
});
</script>
@endsection