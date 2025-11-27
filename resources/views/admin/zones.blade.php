@if($schedulings->isNotEmpty())
    @foreach($schedulings as $zoneName => $zoneSchedulings)
        <div class="col-md-4 mb-4">
            @php
                $isComplete = false;
                $totalEmployees = 0;
                $totalAttendances = 0;
                $selectedDate = $request->date ?? date('Y-m-d');
                $firstScheduling = $zoneSchedulings->first(); // Tomar la primera scheduling para el enlace de edición
                foreach($zoneSchedulings as $scheduling) {
                    $group = $scheduling->group;
                    if ($group) {
                        $employees = $group->employees;
                        $totalEmployees += $employees->count();
                        foreach($employees as $employee) {
                            $hasAttendance = $employee->attendances()->where('date', $selectedDate)->exists();
                            if ($hasAttendance) {
                                $totalAttendances++;
                            }
                        }
                    }
                }
                if ($totalEmployees > 0 && $totalAttendances >= $totalEmployees) {
                    $isComplete = true;
                }
            @endphp
            <div class="card {{ $isComplete ? 'border-success' : 'border-danger' }}" style="border-width:2px;">
                <div class="card-body text-center">
                    <h5>Zona: {{ $zoneName }}</h5>
                    <p>Empleados: {{ $totalEmployees }}, Asistencias: {{ $totalAttendances }}</p>
                    <p class="{{ $isComplete ? 'text-success' : 'text-danger' }}">
                        {{ $isComplete ? 'Grupo completo y listo para operar' : 'Faltan integrantes por registrar asistencia' }}
                    </p>
                    @if(!$isComplete && $firstScheduling)
                        <button class="btn btn-warning w-100 edit-scheduling-btn" data-url="{{ route('admin.schedulings.edit', $firstScheduling->id) }}">
                            <i class="fas fa-sync-alt"></i>
                        </button>
                    @endif
                </div>
            </div>
        </div>
    @endforeach
@else
    <div class="col-12">
        <div class="alert alert-info">
            No hay programaciones para la fecha y turno seleccionados.
        </div>
    </div>
@endif