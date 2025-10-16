{{-- resources/views/admin/vehicles/_form.blade.php --}}

<div class="row">
    {{-- Columna Izquierda: Imágenes del Vehículo --}}
    <div class="col-md-5">
        <h6>Imágenes</h6>
        <div class="mb-3">
            <label for="main_image" class="form-label">Imagen Principal</label>
            {{-- Vista previa de la imagen principal si ya existe (en modo edición) --}}
            @if (isset($vehicle) && $vehicle->image)
                <img src="{{ asset($vehicle->image) }}" alt="Imagen principal" class="img-fluid rounded mb-2" id="image_preview">
            @else
                <img src="https://via.placeholder.com/400x250.png?text=Imagen+Principal" alt="Imagen principal" class="img-fluid rounded mb-2" id="image_preview">
            @endif
            <input class="form-control" type="file" id="main_image" name="image" accept="image/*">
            <small class="text-muted">Sube la foto principal del vehículo.</small>
        </div>
        
        {{-- Aquí iría la lógica para imágenes secundarias/galería si la implementas --}}
        <hr>
        <h6>Galería (Opcional)</h6>
        <div class="row">
            <div class="col-4">
                 <img src="https://via.placeholder.com/150x100.png?text=Img+2" class="img-fluid rounded">
            </div>
            <div class="col-4">
                 <img src="https://via.placeholder.com/150x100.png?text=Img+3" class="img-fluid rounded">
            </div>
            <div class="col-4">
                 <img src="https://via.placeholder.com/150x100.png?text=Img+4" class="img-fluid rounded">
            </div>
        </div>
    </div>

    {{-- Columna Derecha: Pestañas con la Información --}}
    <div class="col-md-7">
        <ul class="nav nav-tabs" id="vehicleTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="general-info-tab" data-toggle="tab" data-target="#general-info" type="button" role="tab" aria-controls="general-info" aria-selected="true">Información General</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="specs-tab" data-toggle="tab" data-target="#specs" type="button" role="tab" aria-controls="specs" aria-selected="false">Especificaciones</button>
            </li>
        </ul>

        <div class="tab-content p-3 border border-top-0 rounded-bottom" id="vehicleTabContent">
            
            {{-- Pestaña 1: Información General --}}
            <div class="tab-pane fade show active" id="general-info" role="tabpanel" aria-labelledby="general-info-tab">
                <div class="row">
                    <div class="col-md-12 form-group">
                        <label for="name">Nombre</label>
                        <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $vehicle->name ?? '') }}" placeholder="Ej: Camioneta Hilux 4x4">
                        <small class="text-muted">Ingresa un nombre para el vehículo.</small>
                    </div>
                    <div class="col-md-6 form-group">
                        <label for="code">Código</label>
                        <input type="text" name="code" id="code" class="form-control" value="{{ old('code', $vehicle->code ?? '') }}" placeholder="Ej: VH-001">
                        <small class="text-muted">Ingresa el código del vehículo.</small>
                    </div>
                    <div class="col-md-6 form-group">
                        <label for="year">Año</label>
                        <input type="number" name="year" id="year" class="form-control" value="{{ old('year', $vehicle->year ?? '') }}" placeholder="Ej: 2024">
                        <small class="text-muted">Ingresa el año del vehículo.</small>
                    </div>
                    <div class="col-md-6 form-group">
                        <label for="plate">Placa</label>
                        <input type="text" name="plate" id="plate" class="form-control" value="{{ old('plate', $vehicle->plate ?? '') }}" placeholder="Ej: ABC-123">
                         <small class="text-muted">Ingresa la placa del vehículo.</small>
                    </div>
                    <div class="col-md-6 form-group">
                        <label for="status">Estado</label>
                        <select name="status" id="status" class="form-control">
                            <option value="1" {{ old('status', $vehicle->status ?? '') == 1 ? 'selected' : '' }}>Activo</option>
                            <option value="0" {{ old('status', $vehicle->status ?? '') == 0 ? 'selected' : '' }}>Inactivo</option>
                        </select>
                        <small class="text-muted">Seleccione un estado.</small>
                    </div>
                    <div class="col-md-12 form-group">
                        <label for="description">Descripción</label>
                        <textarea name="description" id="description" class="form-control" rows="3">{{ old('description', $vehicle->description ?? '') }}</textarea>
                        <small class="text-muted">Ingresa una descripción para el vehículo.</small>
                    </div>
                </div>
            </div>

            {{-- Pestaña 2: Especificaciones --}}
            <div class="tab-pane fade" id="specs" role="tabpanel" aria-labelledby="specs-tab">
                 <div class="row">
                    <div class="col-md-6 form-group">
                        <label for="brand_id">Marca</label>
                        <select name="brand_id" id="brand_id" class="form-control">
                            <option value="">Seleccione una marca</option>
                            @foreach ($brands as $brand)
                                <option value="{{ $brand->id }}" {{ old('brand_id', $vehicle->brand_id ?? '') == $brand->id ? 'selected' : '' }}>{{ $brand->name }}</option>
                            @endforeach
                        </select>
                    </div>
                     <div class="col-md-6 form-group">
                        <label for="model_id">Modelo</label>
                        <select name="model_id" id="model_id" class="form-control">
                             <option value="">Seleccione un modelo</option>
                            @foreach ($models as $model)
                                <option value="{{ $model->id }}" {{ old('model_id', $vehicle->model_id ?? '') == $model->id ? 'selected' : '' }}>{{ $model->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 form-group">
                        <label for="type_id">Tipo</label>
                        <select name="type_id" id="type_id" class="form-control">
                             <option value="">Seleccione un tipo</option>
                             @foreach ($types as $type)
                                <option value="{{ $type->id }}" {{ old('type_id', $vehicle->type_id ?? '') == $type->id ? 'selected' : '' }}>{{ $type->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 form-group">
                        <label for="color_id">Color</label>
                        <select name="color_id" id="color_id" class="form-control">
                             <option value="">Seleccione un color</option>
                            @foreach ($colors as $color)
                                <option value="{{ $color->id }}" {{ old('color_id', $vehicle->color_id ?? '') == $color->id ? 'selected' : '' }}>{{ $color->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 form-group">
                        <label for="load_capacity">Capacidad de Carga (Kg)</label>
                        <input type="number" step="0.01" name="load_capacity" id="load_capacity" class="form-control" value="{{ old('load_capacity', $vehicle->load_capacity ?? '') }}" placeholder="Ej: 1500.50">
                    </div>
                 </div>
            </div>
        </div>
    </div>
</div>

<hr>

{{-- Botones de acción --}}
<div class="d-flex justify-content-end">
    <button type="button" class="btn btn-secondary mr-2" data-dismiss="modal">Cancelar</button>
    <button type="submit" class="btn btn-primary">
        <i class="fas fa-save"></i> {{ isset($vehicle) ? 'Actualizar' : 'Guardar' }}
    </button>
</div>

<script>
// Pequeño script para previsualizar la imagen principal antes de subirla
document.getElementById('main_image').addEventListener('change', function(event) {
    if (event.target.files && event.target.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('image_preview').setAttribute('src', e.target.result);
        };
        reader.readAsDataURL(event.target.files[0]);
    }
});
</script>