<div class="row">
    <div class="col-8">
        <div class="form-group">
            {!! Form::label('maintenance_date', 'Fecha') !!}
            {!! Form::date('maintenance_date', isset($model) ? $model->maintenance_date : null, ['class' => 'form-control', 'required']) !!}
        </div>
        <div class="form-group">
            {!! Form::label('descripcion', 'Descripción') !!}
            {!! Form::textarea('descripcion', isset($model) ? $model->descripcion : null, [
                'class' => 'form-control',
                'placeholder' => 'Describa la actividad realizada',
                'rows' => 3,
                'required'
            ]) !!}
        </div>
    </div>
    <div class="col-4">
        <div class="form-group">
            <div id="imageButton" style="width: 100%; text-align:center; padding:10px;">
                <img id="imagePreview"
                    src="{{ empty($model->image_url) ? asset('storage/maintenance_records/noimage.jpg') : asset($model->image_url) }}"
                    alt="Vista previa de la imagen" style="width: 100%;height:180px;cursor: pointer;">
                <p style="font-size:12px">Imagen Referencial - Haga click para seleccionar una imagen</p>
            </div>
        </div>
        <div class="form-group">
            {!! Form::file('image_url', [
                'class' => 'form-control-file d-none',
                'accept' => 'image/*',
                'id' => 'imageInput',
            ]) !!}
        </div>
    </div>

    {{-- Campo oculto para estado, para que siempre se envíe el valor actual --}}
    {!! Form::hidden('estado', isset($model) ? $model->estado : 0) !!}
</div>

<script>
    $('#imageInput').change(function(event) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                $('#imagePreview').attr('src', e.target.result).show();
            };
            reader.readAsDataURL(file);
        }
    });

    $('#imageButton').click(function() {
        $('#imageInput').click();
    });
</script>
