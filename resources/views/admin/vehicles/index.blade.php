@extends('adminlte::page')

@section('title', 'Gestión de Vehículos')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="h3 mb-1 font-weight-bold">Gestión de Vehículos</h1>
            <p class="text-muted mb-0">Registro y gestión de vehiculos de recolección.</p>
        </div>
            <button type="button" class="btn btn-dark ms-auto" id="btnNuevoVehiculo">
                <i class="fas fa-plus"></i> Agregar Vehículo
            </button>
        </div>
@stop
@section('content')


        <div class="card-body">
            {{-- Sección de filtros --}}
            <div class="filters mb-4 p-3 border rounded">
                <div class="row">
                    <div class="col-12 col-md-3 mb-2">
                        <label for="searchPlaca" class="form-label">Placa:</label>
                        <input type="text" class="form-control" id="searchPlaca" placeholder="Buscar...">
                    </div>
                    <div class="col-12 col-md-2 mb-2">
                        <label for="selectMarca" class="form-label">Marca:</label>
                        <select class="form-control" id="selectMarca">
                            <option selected>Select option</option>
                            {{-- Las opciones se pueden cargar dinámicamente --}}
                        </select>
                    </div>
                    <div class="col-12 col-md-2 mb-2">
                        <label for="selectModelo" class="form-label">Modelo:</label>
                        <select class="form-control" id="selectModelo">
                            <option selected>Select option</option>
                        </select>
                    </div>
                    <div class="col-12 col-md-2 mb-2">
                        <label for="selectTipo" class="form-label">Tipo:</label>
                        <select class="form-control" id="selectTipo">
                            <option selected>Select option</option>
                        </select>
                    </div>
                    <div class="col-12 col-md-2 mb-2">
                        <label for="selectEstado" class="form-label">Estado:</label>
                        <select class="form-control" id="selectEstado">
                            <option selected>Select option</option>
                        </select>
                    </div>
                </div>
            </div>

            {{-- Contenedor de la cuadrícula de vehículos --}}
            <div id="vehicle-grid-container">
                {{-- La cuadrícula se generará aquí. Uso de clases de Bootstrap para un diseño responsivo --}}
                <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 row-cols-xl-5 g-4">
                    @foreach ($vehicles as $vehicle)
                        <div class="col">
                            <div class="card h-100 shadow-sm">
                                <div class="position-relative">
                                    <img src="{{ $vehicle->image ?? asset('path/to/default-vehicle-image.jpg') }}" class="card-img-top" alt="Imagen del vehículo">
                                    <span class="badge bg-dark position-absolute top-0 end-0 m-2">{{ $vehicle->plate }}</span>
                                </div>
                                <div class="card-body">
                                    <h6 class="card-title font-weight-bold">{{ $vehicle->model->brand->name ?? 'Marca' }} {{ $vehicle->model->name ?? 'Modelo' }}</h6>
                                    <div class="d-flex justify-content-between align-items-center my-2">
                                        <span class="badge bg-secondary">{{ $vehicle->type ?? 'SUV' }}</span>
                                        @if ($vehicle->status == 'Activo')
                                            <span class="badge bg-success">Activo</span>
                                        @else
                                            <span class="badge bg-danger">Inactivo</span>
                                        @endif
                                        <span>{{ $vehicle->year ?? '2024' }}</span>
                                    </div>
                                </div>
                                <div class="card-footer bg-white border-0 d-flex justify-content-end gap-2">
                                    <form action="{{ route('admin.vehicles.destroy', $vehicle) }}" method="POST" class="frmDelete d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">Eliminar</button>
                                    </form>
                                    <button class="btn btn-sm btn-dark btnEditar" id="{{ $vehicle->id }}">Editar</button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="card-footer">
            Mostrando {{ $vehicles->firstItem() }} a {{ $vehicles->lastItem() }} de {{ $vehicles->total() }} entradas
            <div class="float-right">
                {{ $vehicles->links() }}
            </div>
        </div>
    </div>

    <div class="modal fade" id="vehicleModal" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalLabel">Formulario de Vehículo</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                </div>
            </div>
        </div>
    </div>
@stop


@section('js')
    <script>
        $(document).ready(function() {

            function refreshVehicleGrid() {
                $(".card").load(location.href + " .card > *");
            }

            $('#btnNuevoVehiculo').click(function() {
                $.ajax({
                    url: "{{ route('admin.vehicles.create') }}",
                    type: 'GET',
                    success: function(response) {
                        $('#vehicleModal .modal-body').html(response);
                        $('#vehicleModal .modal-title').html('Nuevo Vehículo');
                        $('#vehicleModal').modal('show');
                    },
                    error: function() {
                        Swal.fire("Error", "No se pudo cargar el formulario.", "error");
                    }
                });
            });

            // --- Lógica para abrir el modal de EDITAR un vehículo ---
            $(document).on('click', '.btnEditar', function() {
                var vehicleId = $(this).attr('id');
                var url = "{{ route('admin.vehicles.edit', ':id') }}".replace(':id', vehicleId);

                $.ajax({
                    url: url,
                    type: 'GET',
                    success: function(response) {
                        $('#vehicleModal .modal-body').html(response);
                        $('#vehicleModal .modal-title').html('Editar Vehículo');
                        $('#vehicleModal').modal('show');
                    },
                    error: function() {
                        Swal.fire("Error", "No se pudo cargar el formulario de edición.", "error");
                    }
                });
            });

            // --- Lógica para ENVIAR el formulario (Crear o Actualizar) ---
            // Se usa delegación de eventos para que funcione con el formulario cargado por AJAX
            $(document).on('submit', '#vehicleModal form', function(e) {
                e.preventDefault();
                var form = $(this);
                var formData = new FormData(this);

                $.ajax({
                    url: form.attr('action'),
                    type: form.attr('method'),
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        $('#vehicleModal').modal('hide');
                        refreshVehicleGrid();
                        Swal.fire({
                            title: "¡Éxito!",
                            text: response.message,
                            icon: "success",
                            timer: 2000,
                            showConfirmButton: false
                        });
                    },
                    error: function(response) {
                        var error = response.responseJSON;

                        Swal.fire("Error", error.message || "Ocurrió un error.", "error");
                    }
                });
            });


            $(document).on('submit', '.frmDelete', function(e) {
                e.preventDefault();
                var form = $(this);

                Swal.fire({
                    title: "¿Estás seguro?",
                    text: "Esta acción no se puede deshacer.",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#d33",
                    cancelButtonColor: "#3085d6",
                    confirmButtonText: "Sí, ¡eliminar!",
                    cancelButtonText: "Cancelar"
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: form.attr('action'),
                            type: form.attr('method'),
                            data: form.serialize(),
                            success: function(response) {
                                refreshVehicleGrid(); // Recargar la cuadrícula
                                Swal.fire({
                                    title: "¡Eliminado!",
                                    text: response.message,
                                    icon: "success",
                                    timer: 2000,
                                    showConfirmButton: false
                                });
                            },
                            error: function(response) {
                                var error = response.responseJSON;
                                Swal.fire("Error", error.message || "No se pudo eliminar el registro.", "error");
                            }
                        });
                    }
                });
            });

        });
    </script>
@stop

@section('css')
    {{-- Estilos personalizados si son necesarios --}}
    <style>
        .card-img-top {
            aspect-ratio: 16 / 10;
            object-fit: cover;
        }
        .filters .form-label {
            font-weight: 500;
        }
        .gap-2 {
            gap: 0.5rem;
        }
    </style>
@stop
