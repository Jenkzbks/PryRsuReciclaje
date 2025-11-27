@extends('adminlte::page')

@section('title','Editar Programación')

@section('content_header')
    <h1>Editar Programación</h1>
@stop

@section('content')

<form method="POST" action="{{ route('admin.schedulings.update',$scheduling) }}">
@csrf @method('PUT')

<div class="card shadow">
    <div class="card-body">

        {{-- ===========================
            CAMBIO DE TURNO Y VEHÍCULO
        ============================ --}}
        <h5 class="mb-3">Cambio de Turno</h5>

        <div class="row">

            {{-- TURNO ACTUAL --}}
            <div class="col-md-3">
                <div class="form-group">
                    <label class="font-weight-bold">Turno Actual</label>
                    <input type="text" class="form-control" value="{{ $scheduling->turno_actual }}" readonly>
                </div>
            </div>

            {{-- NUEVO TURNO --}}
            <div class="col-md-3">
                <div class="form-group">
                    <label class="font-weight-bold">Nuevo Turno</label>
                    <select name="shift_id" class="form-control">
                        <option value="">Seleccione un nuevo turno</option>
                        @foreach($shifts as $shift)
                        <option value="{{ $shift->id }}" 
                            {{ $scheduling->shift_id == $shift->id ? 'selected' : '' }}>
                            {{ $shift->name }}
                        </option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- BOTÓN + TURNO --}}
            <div class="col-md-1 d-flex align-items-end mt-2 mt-md-0">
                <button type="button" class="btn btn-success w-100">+</button>
            </div>

            {{-- VEHÍCULO ACTUAL --}}
            <div class="col-md-2">
                <div class="form-group">
                    <label class="font-weight-bold">Vehículo Actual</label>
                    <input type="text" class="form-control" value="{{ $scheduling->vehiculo_actual }}" readonly>
                </div>
            </div>

            {{-- NUEVO VEHÍCULO --}}
            <div class="col-md-2">
                <div class="form-group">
                    <label class="font-weight-bold">Nuevo Vehículo</label>
                    <select name="vehicle_id" class="form-control">
                        <option value="">Seleccione un nuevo vehículo</option>
                        @foreach($vehicles as $vehicle)
                        <option value="{{ $vehicle->id }}"
                            {{ $scheduling->vehicle_id == $vehicle->id ? 'selected' : '' }}>
                            {{ $vehicle->plate }}
                        </option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- BOTÓN + VEHÍCULO --}}
            <div class="col-md-1 d-flex align-items-end">
                <button type="button" class="btn btn-success w-100">+</button>
            </div>

        </div>


        {{-- ===========================
            CAMBIO DE PERSONAL
        ============================ --}}
        <h5 class="mt-4 mb-3">Cambio de Personal</h5>

        <div class="row">

            {{-- PERSONAL ACTUAL --}}
            <div class="col-md-4">
                <div class="form-group">
                    <label class="font-weight-bold">Personal Actual</label>
                    <select class="form-control">
                        <option value="">Seleccione un personal</option>
                        @foreach($employees as $emp)
                        <option value="{{ $emp->id }}">{{ $emp->fullname }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- NUEVO PERSONAL --}}
            <div class="col-md-6">
                <div class="form-group">
                    <label class="font-weight-bold">Nuevo Personal</label>
                    <select name="new_employee_id" class="form-control">
                        <option value="">Seleccione un nuevo personal</option>
                        @foreach($employees as $emp)
                        <option value="{{ $emp->id }}">{{ $emp->fullname }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- BOTÓN + PERSONAL --}}
            <div class="col-md-2 d-flex align-items-end">
                <button type="button" class="btn btn-success w-100">+</button>
            </div>

        </div>


        {{-- ===========================
            TABLA DE CAMBIOS
        ============================ --}}
        <h5 class="mt-4">Cambios Registrados</h5>

        <div class="table-responsive mt-3">
            <table class="table table-bordered text-center">
                <thead class="bg-light">
                    <tr>
                        <th>Tipo de Cambio</th>
                        <th>Valor Anterior</th>
                        <th>Valor Nuevo</th>
                        <th>Notas</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody id="tabla-cambios">
                    {{-- Filas agregadas dinámicamente con JS --}}
                </tbody>
            </table>
        </div>

    </div>

    <div class="card-footer text-right">
        <a href="{{ route('admin.schedulings.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left"></i> Volver
        </a>

        <button type="submit" class="btn btn-primary">
            Guardar Cambios
        </button>
    </div>
</div>

</form>

@stop
