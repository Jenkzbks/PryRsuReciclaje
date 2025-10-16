@extends('adminlte::page')

@section('title', 'Gestión de Rutas')
@section('content_header')
	<div class="d-flex justify-content-between align-items-center">
		<div>
			<h1 class="h3 mb-1 font-weight-bold">Gestión de Rutas</h1>
			<p class="text-muted mb-0">Registro de rutas por zona, definiendo punto de inicio y fin.</p>
		</div>
		<a href="{{ route('admin.routes.create') }}" class="btn btn-dark">
			<i class="fas fa-plus"></i> Nueva Ruta
		</a>
	</div>
@stop

@section('content')
	<div class="card shadow-sm">
		<div class="card-body">
			<!-- Filtros -->
			<form method="GET" action="{{ route('admin.routes.index') }}" class="mb-4">
				<div class="row">
					<div class="col-md-2">
						<input type="text" name="search" class="form-control" placeholder="Search" value="{{ request('search') }}">
					</div>
					<div class="col-md-2">
						<select name="zone_id" class="form-control">
							<option value="">Zona</option>
							@foreach($zones as $zone)
								<option value="{{ $zone->id }}" {{ request('zone_id') == $zone->id ? 'selected' : '' }}>{{ $zone->name }}</option>
							@endforeach
						</select>
					</div>
					
					<div class="col-md-2">
						<button type="submit" class="btn btn-outline-secondary">
							<i class="fas fa-search"></i>
						</button>
						<a href="{{ route('admin.routes.index') }}" class="btn btn-outline-secondary ml-2">
							<i class="fas fa-times"></i>
						</a>
					</div>
				</div>
			</form>

			<!-- Tabla -->
			<div class="table-responsive">
				<table class="table table-striped" id="routes-table">
					<thead class="thead-light">
						<tr>
							<th>Código</th>
							<th>Nombre de la Ruta</th>
							<th>Zona Asignada</th>
							<th>Punto Inicio</th>
							<th>Punto Fin</th>
							<th>Distancia</th>
							<th width="10px">Acciones</th>
						</tr>
					</thead>
				</table>
			</div>
		</div>
	</div>
@stop

@section('css')
@stop

@section('js')
	<script>
		$(document).ready(function() {
			var table = $('#routes-table').DataTable({
				ajax: '{{ route('admin.routes.index') }}',
				columns: [
					{ data: 'code', orderable: false, searchable: false },
					{ data: 'name' },
					{ data: 'zone' },
					{ data: 'start_point' },
					{ data: 'end_point' },
					{ data: 'distance' },
					{ data: 'actions', orderable: false, searchable: false }
				],
				language: {
					url: 'https://cdn.datatables.net/plug-ins/1.10.25/i18n/Spanish.json'
				}
			});

			$(document).on('submit', '.frmDelete', function(e) {
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
								// Recargar la tabla sin refrescar la página
								var table = $('#routes-table').DataTable();
								table.row(form.closest('tr')).remove().draw();
								Swal.fire({
									title: "Proceso Exitoso!",
									text: response.message || 'Ruta eliminada correctamente.',
									icon: "success"
								});
							},
							error: function(response) {
								var error = response.responseJSON;
								Swal.fire({
									title: "Error!",
									text: error && error.message ? error.message : 'No se pudo eliminar la ruta.',
									icon: "error"
								});
							}
						});
					}
				});
			});
		});
	</script>
@stop