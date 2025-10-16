@extends('adminlte::page')

@section('title', 'Proyecto RSU')

@section('content_header')
    <button type="button" class="btn btn-success float-right" id="btnRegistrar">
        <i class="fas fa-plus"></i> Nueva marca
    </button>
    <h1>Lista de Marcas</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <table class="table table-striped" id="brands-table">
                <thead>
                    <tr>
                        <th>Logo</th>
                        <th>Nombre</th>
                        <th>Descripción</th>
                        <th>Fecha creación</th>
                        <th>Fecha actualización</th>
                        <th width="10px"></th>
                        <th width="10px"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($brands as $brand)
                        <tr>
                            <td><img src="{{ $brand->logo == '' ? asset('storage/brand_logo/noimage.jpg') : asset($brand->logo) }}"
                                    width="70px" height="50px"></td>
                            <td>{{ $brand->name }}</td>
                            <td>{{ $brand->description }}</td>
                            <td>{{ $brand->created_at }}</td>
                            <td>{{ $brand->updated_at }}</td>
                            <td>
                                <button class="btn btn-warning btn-sm btnEditar" id="{{ $brand->id }}"><i
                                        class="fas fa-pen"></i></button>
                            </td>
                            <td>
                                <form action="{{ route('admin.brands.destroy', $brand) }}" method="POST" class="frmDelete">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm"><i
                                            class="fas fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="modal fade" id="modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Formulario de marcas</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                </div>
            </div>
        </div>
    </div>
@stop


@section('js')

    <script>
        $(document).on('click', '.frmDelete', function(e) {
            e.preventDefault();
            var form = $(this);
            Swal.fire({
                title: "¿Estás seguro de eliminar?",
                text: "Esto no se puede deshacer!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Si, eliminar",
                cancelButtonText: "Cancelar"
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: form.attr('action'),
                        type: form.attr('method'),
                        data: form.serialize(),
                        success: function(response) {
                            refreshTable(); // Recargar la tabla para ver los cambios
                            Swal.fire({
                                title: "Proceso Exitoso!",
                                text: response.message,
                                icon: "success",
                                draggable: true
                            });
                        },
                        error: function(response) {
                            var error = response.responseJSON;
                            Swal.fire({
                                title: "Error!",
                                text: error.message,
                                icon: "error",
                                draggable: true
                            });
                        }
                    });
                }
            });
        });

       $('#btnRegistrar').click(function() {
    $.ajax({
        url: "{{ route('admin.brands.create') }}",
        type: 'GET',
        success: function(response) {
            $('#modal .modal-body').html(response);
            $('#modal .modal-title').html('Nueva Marca');
            
            // Fuerza el show del modal
            $('#modal').modal({
                backdrop: 'static',
                keyboard: false
            }).modal('show');
            
            // Debug: verifica si el modal tiene la clase 'show'
            setTimeout(function() {
                console.log('Modal tiene clase show:', $('#modal').hasClass('show'));
                console.log('Modal está visible:', $('#modal').is(':visible'));
            }, 500);
        }
    });
});
        $(document).on('click', '.btnEditar', function() {
            var id = $(this).attr('id');
            $.ajax({
                url: "{{ route('admin.brands.edit', 'id') }}".replace('id', id),
                type: 'GET',
                success: function(response) {
                    $('#modal .modal-body').html(response);
                    $('#modal .modal-title').html('Editar Marca');
                    $('#modal').modal('show');

                    // Manejar envío del formulario del modal
                    $('#modal form').on('submit', function(e) {
                        e.preventDefault();

                        var form = $(this);
                        var formData = new FormData(this);

                        $.ajax({
                            url: form.attr('action'),
                            type: form.attr('method'),
                            data: formData,
                            processData: false,
                            contentType: false,
                            success: function(response) {
                                $('#modal').modal('hide');
                                refreshTable(); // Recargar la página para ver los cambios
                                Swal.fire({
                                    title: "Proceso Exitoso!",
                                    text: response.message,
                                    icon: "success",
                                    draggable: true
                                });
                            },
                            error: function(response) {
                                var error = response.responseJSON;
                                Swal.fire({
                                    title: "Error!",
                                    text: error.message,
                                    icon: "error",
                                    draggable: true
                                });
                            }
                        });
                    });
                },
                error: function(xhr) {
                    console.log('Error al cargar formulario:', xhr.responseText);
                }
            });
        });

        $(document).ready(function() {
            $('#brands-table').DataTable({
                'ajax': '{{ route('admin.brands.index') }}',
                'columns': [{
                        "data": "logo",
                        "orderable": false,
                        "searchable": false
                    }, {
                        "data": "name",
                    }, {
                        "data": "description",
                    }, {
                        "data": "created_at",
                    }, {
                        "data": "updated_at",
                    }, {
                        "data": "edit",
                        "orderable": false,
                        "searchable": false
                    },
                    {
                        "data": "delete",
                        "orderable": false,
                        "searchable": false
                    },
                ],
                'language': {
                    "url": "https://cdn.datatables.net/plug-ins/1.10.25/i18n/Spanish.json"
                }
            });
        });


        function refreshTable() {
            var table = $("#brands-table").DataTable();
            table.ajax.reload(null, false);
        }
    </script>

@endsection

@section('css')
    {{-- Add here extra stylesheets --}}
    {{-- <link rel="stylesheet" href="/css/admin_custom.css"> --}}
@stop
