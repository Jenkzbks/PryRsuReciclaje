<div class="form-group">
    <label for="name">Nombre</label>
    <input type="text" class="form-control" id="name" name="name" placeholder="Ej: Rojo Pasión" required>
</div>

<div class="form-group">
    <label for="code">Código de Color</label>
    <div class="input-group">
        <div class="input-group-prepend">
            <input type="color" id="color_picker" value="{{ old('code', $color->code ?? '#ff0000') }}" style="height: 38px;">        </div>
        <input type="text" class="form-control" id="code" name="code" placeholder="Ej: #FF0000" required>
    </div>
</div>

<div class="form-group">
    <label for="description">Descripción (Opcional)</label>
    <textarea class="form-control" id="description" name="description" rows="3"></textarea>
</div>

{{-- Script para sincronizar el color picker con el input de texto --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const colorPicker = document.getElementById('color_picker');
        const codeInput = document.getElementById('code');

        if(colorPicker && codeInput) {
            // Sincronizar input de texto cuando cambia el color picker
            colorPicker.addEventListener('input', function() {
                codeInput.value = this.value.toUpperCase();
            });

            // Sincronizar color picker cuando cambia el input de texto
            codeInput.addEventListener('input', function() {
                colorPicker.value = this.value;
            });
        }
    });
</script>
