@extends('adminlte::page')

@section('title', 'Editar Cambio Masivo')

@section('content_header')
    <h1>Editar Cambio Masivo</h1>
@stop

@section('content')
<div class="card">
    <form method="POST" action="{{ route('admin.schedulings.store') }}">
        @csrf

        <div class="card-body">

            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach($errors->all() as $e)
                            <li>{{ $e }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="row">

                {{-- Fecha Inicio --}}
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Fecha de Inicio *</label>
                        <input type="date" name="from" class="form-control"
                               value="{{ $massiveChange->from }}" required>
                    </div>
                </div>

                {{-- Fecha Fin --}}
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Fecha de Fin *</label>
                        <input type="date" name="to" class="form-control"
                               value="{{ $massiveChange->to }}" required>
                    </div>
                </div>

                {{-- Zonas --}}
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Zonas (Opcional)</label>
                        <select name="zones[]" class="form-control" multiple>
                            @foreach($zones as $z)
                                <option value="{{ $z->id }}"
                                    @selected(in_array($z->id, $massiveChange->zones ?? []))>
                                    {{ $z->name }}
                                </option>
                            @endforeach
                        </select>
                        <small class="text-muted">Dejar vacío para aplicar a todas las zonas</small>
                    </div>
                </div>

                {{-- Tipo de Cambio --}}
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Tipo de Cambio *</label>
                        <select name="type" class="form-control" required>
                            <option value="Cambio de Conductor"
                                @selected($massiveChange->type === 'Cambio de Conductor')>
                                Cambio de Conductor
                            </option>
                            <option value="Reemplazo Temporal"
                                @selected($massiveChange->type === 'Reemplazo Temporal')>
                                Reemplazo Temporal
                            </option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="row">

                {{-- Conductor reemplazar --}}
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Conductor a Reemplazar *</label>
                        <select name="old_driver" class="form-control" required>
                            @foreach($drivers as $d)
                                <option value="{{ $d->id }}"
                                    @selected($massiveChange->old_driver == $d->id)>
                                    {{ $d->name }} - {{ $d->document }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Nuevo Conductor --}}
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Nuevo Conductor *</label>
                        <select name="new_driver" class="form-control" required>
                            @foreach($drivers as $d)
                                <option value="{{ $d->id }}"
                                    @selected($massiveChange->new_driver == $d->id)>
                                    {{ $d->name }} - {{ $d->document }}
                                    ({{ $d->contract_status }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            {{-- Motivo --}}
            <div class="form-group mt-3">
                <label>Motivo del Cambio Masivo *</label>
                <textarea name="reason" class="form-control" rows="3" required>{{ $massiveChange->reason }}</textarea>
            </div>

        </div>

        <div class="card-footer d-flex">
            <button type="submit" class="btn btn-success mr-2">
                <i class="fa fa-save"></i> Guardar
            </button>

            <a href="{{ route('admin.schedulings.index') }}" class="btn btn-danger">
                <i class="fa fa-times"></i> Cancelar
            </a>
        </div>

    </form>
</div>
@stop
