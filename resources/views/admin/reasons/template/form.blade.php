<div class="row">
    <div class="col-12">
        <div class="form-group">
            <label for="name">Nombre del Motivo <span class="text-danger">*</span></label>
            <input type="text" name="name" id="name" class="form-control" placeholder="Ingrese el nombre del motivo" value="{{ isset($reason) ? $reason->name : old('name') }}" required>
            <small class="form-text text-muted">Ejemplo: Falta de personal, Cambio de ruta, etc.</small>
        </div>
        <!-- Descripción eliminada, solo nombre y estado -->
        <div class="form-group mt-3">
            <label for="active">Estado:</label>
            <div class="custom-control custom-switch">
                <input type="checkbox" class="custom-control-input" id="activeSwitch"
                    {{ (isset($reason) ? $reason->active == '1' : true) ? 'checked' : '' }}>
                <label class="custom-control-label" for="activeSwitch" id="activeSwitchLabel">
                    {{ (isset($reason) ? $reason->active == '1' : true) ? 'Activo' : 'Inactivo' }}
                </label>
                <input type="hidden" name="active" id="activeHidden" value="{{ isset($reason) ? $reason->active : '1' }}">
            </div>
        </div>

@push('js')
<script>
    $(function () {
        $('#activeSwitch').change(function () {
            if ($(this).is(':checked')) {
                $('#activeHidden').val(1);
                $('#activeSwitchLabel').text('Activo');
            } else {
                $('#activeHidden').val(0);
                $('#activeSwitchLabel').text('Inactivo');
            }
        });
    });
</script>
@endpush
    </div>
</div>
