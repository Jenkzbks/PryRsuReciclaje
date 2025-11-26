<div class="form-group">
    {!! Form::label('name', 'Nombre de la Zona') !!}
    {!! Form::text('name', null, ['class' => 'form-control', 'placeholder' => 'Nombre de la zona', 'required']) !!}
</div>
<div class="form-row mb-2">
    <div class="form-group col-md-4">
        <label for="department_id">Departamento</label>
        <select id="department_id" name="department_id" class="form-control" required>
            <option value="">Seleccione</option>
            @foreach($departments as $id => $name)
                <option value="{{ $id }}">{{ $name }}</option>
            @endforeach
        </select>
    </div>
    <div class="form-group col-md-4">
        <label for="province_id">Provincia</label>
        <select id="province_id" name="province_id" class="form-control" required disabled>
            <option value="">Seleccione</option>
        </select>
    </div>
    <div class="form-group col-md-4">
        <label for="district_id">Distrito</label>
        <select id="district_id" name="district_id" class="form-control" required disabled>
            <option value="">Seleccione</option>
        </select>
    </div>
</div>
<div class="form-group">
    {!! Form::label('average_waste', 'Residuos promedio (Tn)') !!}
    <div class="input-group">
        {!! Form::number('average_waste', null, ['class' => 'form-control', 'placeholder' => 'Ingrese residuos promedio', 'step' => '0.01', 'min' => '0', 'required']) !!}
        <div class="input-group-append">
            <span class="input-group-text">Tn</span>
        </div>
    </div>
</div>
<div class="form-group">
    {!! Form::label('description', 'Descripción') !!}
    {!! Form::textarea('description', null, [
        'class' => 'form-control',
        'placeholder' => 'Detalle de la zona',
        'rows' => 2,
    ]) !!}
</div>
<div class="form-group">
    {!! Form::label('status', 'Estado:') !!}
    <div class="custom-control custom-switch">
        <input type="checkbox" class="custom-control-input" id="statusSwitch" {{ (isset($zone) ? $zone->status == '1' : true) ? 'checked' : '' }}>
        <label class="custom-control-label" for="statusSwitch">Activo</label>
        <input type="hidden" name="status" id="statusHidden" value="{{ isset($zone) ? $zone->status : '1' }}">
    </div>
</div>
<hr>
<div class="row mt-3">
    <div class="col-md-6">
        <label for="vertices_count"><strong># Vértices</strong></label>
        <input type="text" id="vertices_count" class="form-control" value="0" readonly>
    </div>
    <div class="col-md-6">
        <label for="area_km2"><strong>Área aproximada (km²)</strong></label>
        <input type="text" id="area_km2" class="form-control" value="0.00" readonly>
    </div>
</div>

@push('css')
<style>
    .custom-control-label::before, .custom-control-label::after {
        top: 0.25rem;
        left: -2.25rem;
    }
    .custom-switch .custom-control-label::before {
        left: -2.5rem;
    }
    .custom-switch .custom-control-label::after {
        left: -2.5rem;
    }
</style>
@endpush

@push('js')
<script>
$(function() {
    // Sincronizar el valor del switch con el input hidden (1=activo, 0=inactivo)
    $('#statusSwitch').on('change', function() {
        $('#statusHidden').val(this.checked ? '1' : '0');
    });
    // Al cargar, asegurar que el valor hidden coincida con el estado del switch
    $('#statusHidden').val($('#statusSwitch').is(':checked') ? '1' : '0');
    // Provincias por departamento
    $('#department_id').on('change', function() {
        let depId = $(this).val();
        $('#province_id').prop('disabled', true).html('<option value="">Cargando...</option>');
        $('#district_id').prop('disabled', true).html('<option value="">Seleccione</option>');
        if(depId) {
            $.get('/admin/api/provinces?department_id=' + depId, function(data) {
                let options = '<option value="">Seleccione</option>';
                $.each(data, function(id, name) {
                    options += `<option value="${id}">${name}</option>`;
                });
                $('#province_id').html(options).prop('disabled', false);
            });
        } else {
            $('#province_id').html('<option value="">Seleccione</option>').prop('disabled', true);
        }
    });
    // Distritos por provincia
    $('#province_id').on('change', function() {
        let provId = $(this).val();
        $('#district_id').prop('disabled', true).html('<option value="">Cargando...</option>');
        if(provId) {
            $.get('/admin/api/districts?province_id=' + provId, function(data) {
                let options = '<option value="">Seleccione</option>';
                $.each(data, function(id, name) {
                    options += `<option value="${id}">${name}</option>`;
                });
                $('#district_id').html(options).prop('disabled', false);
            });
        } else {
            $('#district_id').html('<option value="">Seleccione</option>').prop('disabled', true);
        }
    });

    // Centrar mapa al cambiar distrito si tiene lat/lng/zoom
    $('#district_id').on('change', function() {
        var distId = $(this).val();
        if (distId && typeof map !== 'undefined') {
            $.get('/admin/api/district-data?district_id=' + distId, function(data) {
                if (data && data.lat && data.lng) {
                    map.setView([data.lat, data.lng], data.zoom || 15);
                }
            });
        }
    });
    // Precarga para edición: selecciona los valores actuales de la zona
    @if (isset($zone))
        let depId = null, provId = null, distId = null;
        depId = @json(optional(optional($zone->district)->province)->department_id ?? '');
        provId = @json(optional($zone->district)->province_id ?? '');
        distId = @json($zone->district_id ?? '');
        if(depId) {
            $('#department_id').val(depId).trigger('change');
            $.get('/admin/api/provinces?department_id=' + depId, function(provinces) {
                let options = '<option value="">Seleccione</option>';
                $.each(provinces, function(id, name) {
                    options += `<option value="${id}" ${id == provId ? 'selected' : ''}>${name}</option>`;
                });
                $('#province_id').html(options).prop('disabled', false).trigger('change');
                if(provId) {
                    $.get('/admin/api/districts?province_id=' + provId, function(districts) {
                        let options = '<option value="">Seleccione</option>';
                        $.each(districts, function(id, name) {
                            options += `<option value="${id}" ${id == distId ? 'selected' : ''}>${name}</option>`;
                        });
                        $('#district_id').html(options).prop('disabled', false);
                    });
                }
            });
        }
    @else
        // Precarga robusta para create: busca IDs por nombre (Lambayeque, Chiclayo, José Leonardo Ortiz)
        let depId = null, provId = null, distId = null;
        let depName = 'Lambayeque', provName = 'Chiclayo', distName = 'José Leonardo Ortiz';
        // Buscar departamento
        $('#department_id option').each(function() {
            if ($(this).text().trim().toLowerCase() === depName.toLowerCase()) {
                depId = $(this).val();
                $('#department_id').val(depId).trigger('change');
            }
        });
        // Provincias (AJAX)
        if (depId) {
            $.get('/admin/api/provinces?department_id=' + depId, function(provinces) {
                let options = '<option value="">Seleccione</option>';
                $.each(provinces, function(id, name) {
                    options += `<option value="${id}">${name}</option>`;
                    if (name.trim().toLowerCase() === provName.toLowerCase()) provId = id;
                });
                $('#province_id').html(options).prop('disabled', false);
                if (provId) $('#province_id').val(provId).trigger('change');
                // Distritos (AJAX)
                if (provId) {
                    $.get('/admin/api/districts?province_id=' + provId, function(districts) {
                        let options = '<option value="">Seleccione</option>';
                        $.each(districts, function(id, name) {
                            options += `<option value="${id}">${name}</option>`;
                            if (name.trim().toLowerCase() === distName.toLowerCase()) distId = id;
                        });
                        $('#district_id').html(options).prop('disabled', false);
                        if (distId) $('#district_id').val(distId);
                    });
                }
            });
        }
        // Centrar mapa en José Leonardo Ortiz
        if (typeof map !== 'undefined') {
            setTimeout(function() {
                map.setView([-6.7714, -79.8406], 15); // Coordenadas aproximadas JLO
            }, 1000);
        }
    @endif
});

// Escuchar eventos personalizados del mapa para actualizar los campos
window.updateVerticesAndArea = function(vertices, area) {
    $('#vertices_count').val(vertices);
    $('#area_km2').val(area.toFixed(2));
}
</script>
@endpush
