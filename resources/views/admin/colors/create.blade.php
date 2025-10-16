{{-- resources/views/admin/vehicles/create.blade.php --}}

<form action="{{ route('admin.vehicles.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    {{-- Incluimos el formulario parcial --}}
    @include('admin.vehicles.template.form')
</form>
