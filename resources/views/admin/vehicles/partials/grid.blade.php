<div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 row-cols-xl-5 g-4">
    @foreach ($vehicles as $vehicle)
        <div class="col">
            <div class="card h-100 shadow-sm">
                <div class="position-relative">
                    <img src="{{ $vehicle->images->isNotEmpty() ? asset('storage/' . $vehicle->images->first()->image) : 'https://via.placeholder.com/400x250/cccccc/000000?text=No+Image' }}" class="card-img-top" alt="Imagen del vehículo">
                    <span class="badge bg-dark position-absolute top-0 end-0 m-2">{{ $vehicle->plate }}</span>
                </div>
                <div class="card-body">
                    <h6 class="card-title font-weight-bold">{{ $vehicle->model->brand->name ?? 'Marca' }} {{ $vehicle->model->name ?? 'Modelo' }}</h6>
                    <div class="d-flex justify-content-between align-items-center my-2">
                        <span class="badge bg-secondary">{{ $vehicle->type->name ?? 'SUV' }}</span>
                        @if ($vehicle->status == 1)
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
<div class="card-footer">
    Mostrando {{ $vehicles->firstItem() }} a {{ $vehicles->lastItem() }} de {{ $vehicles->total() }} entradas
    <div class="float-right">
        {{ $vehicles->links() }}
    </div>
</div>
