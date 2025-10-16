<div class="row">
    <div class="col-12">
        <div class="form-group">
            <label for="name">Nombre del Modelo</label>
            <input type="text" name="name" id="name" class="form-control" placeholder="Nombre del modelo" value="{{ isset($brandModel) ? $brandModel->name : old('name') }}" required>
        </div>
        <div class="form-group">
            <label for="brand_id">Marca</label>
            <select name="brand_id" id="brand_id" class="form-control" required>
                <option value="">Seleccione una marca</option>
                @foreach($brands as $brand)
                    <option value="{{ $brand->id }}" {{ (isset($brandModel) && $brandModel->brand_id == $brand->id) ? 'selected' : '' }}>
                        {{ $brand->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label for="description">Descripción</label>
            <textarea name="description" id="description" class="form-control" placeholder="Agregue una descripción" rows="3">{{ isset($brandModel) ? $brandModel->description : old('description') }}</textarea>
        </div>
    </div>
</div>