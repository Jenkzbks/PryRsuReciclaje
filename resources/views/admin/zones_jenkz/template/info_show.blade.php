<div class="modal-header bg-primary text-white">
    <h5 class="modal-title">Detalles de la Zona</h5>
    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Cerrar">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
<div class="modal-body">
    <div class="row mb-3">
        <div class="col-md-6 mb-2">
            <div class="d-flex align-items-center bg-light rounded p-2">
                <span class="bg-info text-white rounded-circle d-flex align-items-center justify-content-center mr-2" style="width:32px;height:32px;"><i class="fas fa-map-marker-alt"></i></span>
                <div>
                    <small class="text-muted">Nombre</small><br>
                    <span class="font-weight-bold">{{ $zone->name }}</span>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-2">
            <div class="d-flex align-items-center bg-light rounded p-2">
                <span class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center mr-2" style="width:32px;height:32px;"><i class="fas fa-map"></i></span>
                <div>
                    <small class="text-muted">Puntos</small><br>
                    <span class="font-weight-bold">{{ count($zone->coords) }}</span>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-2">
            <div class="d-flex align-items-center bg-light rounded p-2">
                <span class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center mr-2" style="width:32px;height:32px;"><i class="fas fa-building"></i></span>
                <div>
                    <small class="text-muted">Departamento</small><br>
                    <span class="font-weight-bold">{{ $zone->district->province->department->name ?? '-' }}</span>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-2">
            <div class="d-flex align-items-center bg-light rounded p-2">
                <span class="bg-warning text-white rounded-circle d-flex align-items-center justify-content-center mr-2" style="width:32px;height:32px;"><i class="fas fa-trash-alt"></i></span>
                <div>
                    <small class="text-muted">Residuos promedio</small><br>
                    <span class="font-weight-bold">{{ $zone->average_waste ?? 'No especificado' }}</span>
                </div>
            </div>
        </div>
    </div>
    <div class="mb-3">
        <label class="font-weight-bold">Descripción</label>
        <div class="bg-light rounded p-2">{{ $zone->description ?? 'Sin descripción.' }}</div>
    </div>
    <div>
        <label class="font-weight-bold">Coordenadas</label>
        <div class="table-responsive">
            <table class="table table-bordered table-sm mb-0">
                <thead class="thead-light">
                    <tr>
                        <th class="text-center">#</th>
                        <th>Latitud</th>
                        <th>Longitud</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($zone->coords as $i => $coord)
                    <tr>
                        <td class="text-center">{{ $i+1 }}</td>
                        <td>{{ $coord->latitude }}</td>
                        <td>{{ $coord->longitude }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
