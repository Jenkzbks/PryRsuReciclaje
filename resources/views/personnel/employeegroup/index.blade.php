@extends('adminlte::page')

@section('title', 'Grupos de Personal')

@section('content_header')
    <button type="button" class="btn btn-primary float-right" id="btnRegistrar">
        <i class="fas fa-plus"></i> Nuevo Grupo
    </button>
    <h1 class="h3 mb-1 font-weight-bold"><i class="fas fa-users"></i> Grupos de Personal</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <table class="table table-striped table-bordered align-middle" id="groups-table" style="width:100%">
                <thead>
                    <tr>
                        <th class="text-center align-middle">Nombre</th>
                        <th class="text-center align-middle">Zona</th>
                        <th class="text-center align-middle">Turno</th>
                        <th class="text-center align-middle">Vehículo</th>
                        <th class="text-center align-middle">Días</th>
                        <th width="180px" class="text-center align-middle">Fecha creación</th>
                        <th width="180px" class="text-center align-middle">Fecha actualización</th>
                        <th width="70px" class="text-center align-middle">Ver empleados</th>
                        <th width="70px" class="text-center align-middle">Editar</th>
                        <th width="70px" class="text-center align-middle">Eliminar</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>

    <div class="modal fade" id="modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white" >
                    <h5 class="modal-title" id="exampleModalLabel">Formulario Grupo de Personal</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body"></div>
            </div>
        </div>
    </div>

     <!-- Modal para ver empleados del grupo -->
                <div class="modal fade" id="modalViewEmployees" tabindex="-1" role="dialog" aria-labelledby="modalViewEmployeesLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header bg-info text-white">
                                <h5 class="modal-title" id="modalViewEmployeesLabel">Empleados del Grupo</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body" id="employees-modal-body">
                            </div>
                        </div>
                    </div>
                </div>
@stop

@section('js')
<script>
     // Script para combos dinámicos de conductor y ayudantes
    function renderCrewCombos(passengers, employees) {
        const container = document.getElementById('dynamic-crew');
        if (!container) return;
        container.innerHTML = '';
        if (!passengers || passengers < 2) return;
        const block = document.getElementById('dynamic-crew-block');
        if (!container || !block) return;
        container.innerHTML = '';
        if (!passengers || passengers < 2) {
            block.style.display = 'none';
            return;
        }
        block.style.display = '';

        // Obtener configuración previa (solo en edición)
        let crewConfig = {};
        const crewConfigInput = document.getElementById('crew-config');
        if (crewConfigInput) {
            try { crewConfig = JSON.parse(crewConfigInput.value); } catch(e) { crewConfig = {}; }
        }

        // Grid de cards para conductor y ayudantes
        container.innerHTML += '<div class="row" id="crew-cards-row"></div>';
        const cardsRow = document.getElementById('crew-cards-row');

        // Card del conductor: usar lista de conductores
        let conductoresData = document.getElementById('conductores-data').value;
        let conductores = JSON.parse(conductoresData);
        let conductorOptions = '<option value="">-- Seleccione --</option>';
        conductores.forEach(emp => {
            let selected = crewConfig[1] && crewConfig[1] == emp.id ? 'selected' : '';
            conductorOptions += `<option value="${emp.id}" ${selected}>${emp.lastnames} ${emp.names}</option>`;
        });
        cardsRow.innerHTML += `
            <div class="col-md-4 mb-3">
                <div class="card h-100 shadow-sm">
                    <div class="card-body">
                        <label for="conductor"><strong>Conductor</strong></label>
                        <select name="crew[1]" id="conductor" class="form-control">${conductorOptions}</select>
                    </div>
                </div>
            </div>
        `;

        // Cards de ayudantes: usar lista de ayudantes
        let ayudantesData = document.getElementById('ayudantes-data').value;
        let ayudantesArr = JSON.parse(ayudantesData);
        let ayudantes = passengers - 1;
        for (let i = 1; i <= ayudantes; i++) {
            let assistantOptions = '<option value="">-- Seleccione --</option>';
            ayudantesArr.forEach(emp => {
                let selected = crewConfig[i+1] && crewConfig[i+1] == emp.id ? 'selected' : '';
                assistantOptions += `<option value="${emp.id}" ${selected}>${emp.lastnames} ${emp.names}</option>`;
            });
            cardsRow.innerHTML += `
                <div class="col-md-4 mb-3">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body">
                            <label for="assistant${i}"><strong>Ayudante ${i}</strong></label>
                            <select name="crew[${i+1}]" id="assistant${i}" class="form-control">${assistantOptions}</select>
                        </div>
                    </div>
                </div>
            `;
        }
    }

    // Función para inicializar combos dinámicos cada vez que se carga el modal
    function initDynamicCrewCombos() {
        // Ya no se usa employees, ahora se usan conductores y ayudantes
        var vehicleSelect = $('#modal .modal-body #vehicle_id');
        if (!vehicleSelect.length) return;

        function updateCombos() {
            var passengers = parseInt(vehicleSelect.find('option:selected').data('passengers'));
            renderCrewCombos(passengers);
        }

        vehicleSelect.off('change.dynamicCrew').on('change.dynamicCrew', updateCombos);
        // Inicializar si ya hay un vehículo seleccionado
        updateCombos();
    }



    $(document).ready(function() {
        // Cargar moment.js si no está presente
        if (typeof moment === 'undefined') {
            var script = document.createElement('script');
            script.src = 'https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js';
            document.head.appendChild(script);
        }

        $('#groups-table').DataTable({
            'ajax': '{{ route('admin.personnel.employeegroups.index') }}',
            'columns': [
                { "data": "name", "className": "text-center align-middle" },
                { "data": "zone", "className": "text-center align-middle" },
                { "data": "shift", "className": "text-center align-middle" },
                { "data": "vehicle", "className": "text-center align-middle" },
                { "data": "days", "className": "text-center align-middle" },
                {
                    "data": "created_at",
                    "className": "text-center align-middle",
                    "render": function(data, type, row) {
                        if (!data) return '';
                        if (typeof moment !== 'undefined') {
                            return moment(data).format('DD/MM/YYYY HH:mm');
                        }
                        return data;
                    }
                },
                {
                    "data": "updated_at",
                    "className": "text-center align-middle",
                    "render": function(data, type, row) {
                        if (!data) return '';
                        if (typeof moment !== 'undefined') {
                            return moment(data).format('DD/MM/YYYY HH:mm');
                        }
                        return data;
                    }
                },
                { "data": "view_employees", "orderable": false, "searchable": false, "className": "text-center align-middle" },
                { "data": "edit", "orderable": false, "searchable": false, "className": "text-center align-middle" },
                { "data": "delete", "orderable": false, "searchable": false, "className": "text-center align-middle" }
            ],
            'language': { "url": "https://cdn.datatables.net/plug-ins/1.10.25/i18n/Spanish.json" }
        });

        $('#btnRegistrar').click(function() {
            $.ajax({
                url: "{{ route('admin.personnel.employeegroups.create') }}",
                type: 'GET',
                success: function(response) {
                    $('#modal .modal-body').html(response);
                    $('#modal .modal-title').html('Nuevo Grupo de Personal');
                    $('#modal').modal({ backdrop: 'static', keyboard: false }).modal('show');
                    initDynamicCrewCombos();
                    bindFormSubmit();
                }
            });
        });

        $(document).on('click', '.btnEditar', function() {
            var id = $(this).attr('id');
            $.ajax({
                url: "{{ route('admin.personnel.employeegroups.edit', 'id') }}".replace('id', id),
                type: 'GET',
                success: function(response) {
                    $('#modal .modal-body').html(response);
                    $('#modal .modal-title').html('Editar Grupo de Personal');
                    $('#modal').modal('show');
                    initDynamicCrewCombos();
                    bindFormSubmit();
                }
            });
        });

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
                            refreshTable();
                            Swal.fire({ title: "Proceso Exitoso!", text: response.message, icon: "success" });
                        },
                        error: function(response) { var error = response.responseJSON; Swal.fire({ title: "Error!", text: error.message, icon: "error" }); }
                    });
                }
            });
        });

        function bindFormSubmit() {
            $('#modal form').off('submit').on('submit', function(e) {
                e.preventDefault();
                var form = $(this);
                // Validación de empleados repetidos en crew
                var crewIds = [];
                form.find('select[name^="crew["]').each(function() {
                    var val = $(this).val();
                    if(val) crewIds.push(val);
                });
                var hasDuplicates = (new Set(crewIds)).size !== crewIds.length;
                if(hasDuplicates) {
                    Swal.fire({
                        title: "Error de selección",
                        text: "No puedes asignar el mismo empleado más de una vez en el grupo (conductor/ayudantes).",
                        icon: "error"
                    });
                    return false;
                }
                var formData = new FormData(this);
                $.ajax({
                    url: form.attr('action'),
                    type: form.attr('method'),
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        $('#modal').modal('hide');
                        refreshTable();
                        Swal.fire({ title: "Proceso Exitoso!", text: response.message, icon: "success" });
                    },
                    error: function(response) {
                        var error = response.responseJSON;
                        // Si el mensaje contiene una lista HTML, usar html: en lugar de text:
                        if (error && error.message && error.message.startsWith('<ul')) {
                            Swal.fire({ title: "Error!", html: error.message, icon: "error" });
                        } else {
                            Swal.fire({ title: "Error!", text: error.message, icon: "error" });
                        }
                    }
                });
            });
        }

        // Evento para ver empleados del grupo
                    $(document).on('click', '.btnViewEmployees', function() {
                        var groupId = $(this).data('id');
                        $.ajax({
                            url: '/admin/personnel/employeegroups/' + groupId + '/employees',
                            type: 'GET',
                            success: function(response) {
                                $('#employees-modal-body').html(response.html);
                                $('#modalViewEmployees').modal('show');
                            },
                            error: function() {
                                $('#employees-modal-body').html('<div class="alert alert-danger">No se pudo cargar la información de los empleados.</div>');
                                $('#modalViewEmployees').modal('show');
                            }
                        });
                    });

        function refreshTable() { var table = $('#groups-table').DataTable(); table.ajax.reload(null, false); }
    });
</script>
@endsection
