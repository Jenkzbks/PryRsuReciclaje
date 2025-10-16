{{-- resources/views/admin/vehicles/edit.blade.php --}}

<form action="{{ route('admin.vehicles.update', $vehicle) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    {{-- Incluimos el formulario parcial. La variable $vehicle estará disponible aquí --}}
    @include('admin.vehicles.form')
</form>
