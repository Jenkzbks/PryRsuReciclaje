@extends('adminlte::page')

@section('title', 'Historial de Cambios')

@section('content_header')
    <h1 class="h3 mb-1 font-weight-bold"><i class="fas fa-history"></i> Historial de Cambios</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <table class="table table-striped table-bordered align-middle" id="changes-table" style="width:100%">
                <thead>
                    <tr>
                        <th>Programación</th>
                        <th>Tipo de Cambio</th>
                        <th>Razón</th>
                        <th>Valor Anterior</th>
                        <th>Valor Nuevo</th>
                        <th>Notas</th>
                        <th>Usuario</th>
                        <th>Fecha</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
@endsection

@section('js')
<script>
    $(document).ready(function() {
        $('#changes-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{{ route('admin.scheduling-changes.data') }}',
            columns: [
                { data: 'scheduling_id', name: 'scheduling_id' },
                { data: 'change_type', name: 'change_type' },
                { data: 'reason', name: 'reason' },
                { data: 'old_value', name: 'old_value' },
                { data: 'new_value', name: 'new_value' },
                { data: 'notes', name: 'notes' },
                { data: 'user', name: 'user' },
                { data: 'created_at', name: 'created_at' }
            ],
            order: [[0, 'desc']],
            language: {
                url: 'https://cdn.datatables.net/plug-ins/1.10.25/i18n/Spanish.json'
            }
        });
    });
</script>
@endsection
