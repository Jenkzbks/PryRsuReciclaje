{{-- La cuadrícula se generará aquí. Uso de clases de Bootstrap para un diseño responsivo --}}
<div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 row-cols-xl-5 g-4">
    @foreach ($vehicles as $vehicle)
        <div class="col">
            <div class="card h-100 shadow-sm">
                <div class="position-relative" style="overflow: hidden;">
                    {{-- Mostrar carousel de imágenes si existen, si no usar imagen por defecto --}}
                    @if($vehicle->images->isNotEmpty())
                        <div id="carousel-{{ $vehicle->id }}" class="carousel slide" data-ride="carousel">
                            <div class="carousel-inner" style="height: 200px;">
                                @foreach($vehicle->images->sortBy('id') as $index => $image)
                                    <div class="carousel-item {{ $index == 0 ? 'active' : '' }}" style="height: 200px;">
                                        <img class="d-block w-100 h-100 object-fit-cover" src="{{ asset('storage/' . $image->image) }}" alt="Imagen {{ $index + 1 }}">
                                    </div>
                                @endforeach
                            </div>
                            @if($vehicle->images->count() > 1)
                                <a class="carousel-control-prev" href="#carousel-{{ $vehicle->id }}" role="button" data-slide="prev">
                                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                    <span class="sr-only">Previous</span>
                                </a>
                                <a class="carousel-control-next" href="#carousel-{{ $vehicle->id }}" role="button" data-slide="next">
                                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                    <span class="sr-only">Next</span>
                                </a>
                            @endif
                        </div>
                    @else
                        <img src="{{ asset('storage/vehicles/noimage.jpg') }}" class="card-img-top" alt="Imagen del vehículo" style="height: 200px; object-fit: cover;">
                    @endif
                    @if(!empty($vehicle->plate))
                        <span class="vehicle-plate">{{ $vehicle->plate }}</span>
                    @endif
                </div>
                <div class="card-body py-3 d-flex justify-content-between align-items-start">
                    <div class="vehicle-info">
                        {{-- Primera línea: nombre + tipo + estado --}}
                        <div class="d-flex align-items-center mb-2 gap-2">
                            <h6 class="mb-0 fw-bold text-truncate" style="max-width:180px;">{{ $vehicle->name ?? ($vehicle->model->brand->name ?? 'Marca') . ' ' . ($vehicle->model->name ?? 'Modelo') }}</h6>
                            <span class="badge bg-secondary">{{ $vehicle->type->name ?? 'Tipo' }}</span>
                            @if ($vehicle->status == 1)
                                <span class="badge bg-success">Activo</span>
                            @else
                                <span class="badge bg-danger">Inactivo</span>
                            @endif
                        </div>

                        <div class="mb-1 text-muted small">Modelo: {{ $vehicle->model->brand->name ?? 'Marca' }} {{ $vehicle->model->name ?? 'Modelo' }}</div>

                        <div class="text-muted small">Categoría: {{ $vehicle->color->name ?? 'N/A' }}</div>
                    </div>

                    {{-- Derecha: año --}}
                    <div class="text-end">
                        <div class="vehicle-year fw-bold">{{ $vehicle->year ?? '2025' }}</div>
                    </div>
                </div>                            <div class="card-footer bg-white border-0 d-flex justify-content-end gap-2">
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
