@extends('adminlte::page')

@section('title', 'Dashboard de Asistencias')

@section('content_header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1>
                <i class="fas fa-tachometer-alt"></i> Dashboard de Asistencias
            </h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('admin.personnel.attendances.index') }}">Asistencias</a></li>
                <li class="breadcrumb-item active">Dashboard</li>
            </ol>
        </div>
    </div>
@stop

@section('content')
    <!-- Estadísticas Principales -->
    <div class="row">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ $todayStats['present'] }}</h3>
                    <p>Presentes Hoy</p>
                </div>
                <div class="icon">
                    <i class="fas fa-user-check"></i>
                </div>
                <a href="{{ route('admin.personnel.attendances.index', ['status' => 'present', 'date_from' => date('Y-m-d')]) }}" class="small-box-footer">
                    Ver detalles <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ $todayStats['on_time'] }}</h3>
                    <p>A Tiempo</p>
                </div>
                <div class="icon">
                    <i class="fas fa-clock"></i>
                </div>
                <a href="{{ route('admin.personnel.attendances.index', ['status' => 'on_time', 'date_from' => date('Y-m-d')]) }}" class="small-box-footer">
                    Ver detalles <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ $todayStats['late'] }}</h3>
                    <p>Llegadas Tarde</p>
                </div>
                <div class="icon">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <a href="{{ route('admin.personnel.attendances.index', ['status' => 'late', 'date_from' => date('Y-m-d')]) }}" class="small-box-footer">
                    Ver detalles <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>{{ $todayStats['absent'] }}</h3>
                    <p>Ausentes</p>
                </div>
                <div class="icon">
                    <i class="fas fa-user-times"></i>
                </div>
                <a href="{{ route('admin.personnel.attendances.index', ['status' => 'absent', 'date_from' => date('Y-m-d')]) }}" class="small-box-footer">
                    Ver detalles <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Gráficos y Estadísticas -->
    <div class="row">
        <!-- Gráfico de Asistencias del Mes -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-chart-line"></i> Tendencia de Asistencias - {{ date('F Y') }}
                    </h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <canvas id="attendanceChart" style="height: 300px;"></canvas>
                </div>
            </div>
        </div>

        <!-- Estadísticas por Departamento -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-building"></i> Por Departamento
                    </h3>
                </div>
                <div class="card-body p-0">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Departamento</th>
                                <th>Presentes</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($departmentStats as $dept)
                                <tr>
                                    <td>{{ $dept['name'] }}</td>
                                    <td>
                                        <span class="badge badge-success">{{ $dept['present'] }}</span>
                                    </td>
                                    <td>{{ $dept['total'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Empleados con Problemas de Asistencia -->
    <div class="row">
        <div class="col-md-6">
            <div class="card card-warning">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-exclamation-circle"></i> Llegadas Tarde Frecuentes
                    </h3>
                </div>
                <div class="card-body p-0">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Empleado</th>
                                <th>Tardanzas (Mes)</th>
                                <th>Última Tardanza</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($frequentLateEmployees as $employee)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if($employee['photo'])
                                                <img src="{{ asset('storage/' . $employee['photo']) }}" 
                                                     alt="Foto" class="img-circle mr-2" 
                                                     style="width: 30px; height: 30px; object-fit: cover;">
                                            @endif
                                            <div>
                                                <strong>{{ $employee['names'] }} {{ $employee['lastnames'] }}</strong>
                                                <br><small class="text-muted">{{ $employee['employee_code'] }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge badge-warning">{{ $employee['late_count'] }}</span>
                                    </td>
                                    <td>{{ $employee['last_late'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card card-danger">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-user-times"></i> Ausencias Frecuentes
                    </h3>
                </div>
                <div class="card-body p-0">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Empleado</th>
                                <th>Ausencias (Mes)</th>
                                <th>Última Ausencia</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($frequentAbsentEmployees as $employee)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if($employee['photo'])
                                                <img src="{{ asset('storage/' . $employee['photo']) }}" 
                                                     alt="Foto" class="img-circle mr-2" 
                                                     style="width: 30px; height: 30px; object-fit: cover;">
                                            @endif
                                            <div>
                                                <strong>{{ $employee['names'] }} {{ $employee['lastnames'] }}</strong>
                                                <br><small class="text-muted">{{ $employee['employee_code'] }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge badge-danger">{{ $employee['absent_count'] }}</span>
                                    </td>
                                    <td>{{ $employee['last_absent'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Resumen de Horas -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-clock"></i> Resumen de Horas Trabajadas - Hoy
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="info-box bg-primary">
                                <span class="info-box-icon"><i class="fas fa-clock"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Total Horas</span>
                                    <span class="info-box-number">{{ number_format($hoursStats['total'], 2) }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box bg-success">
                                <span class="info-box-icon"><i class="fas fa-user-clock"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Promedio por Empleado</span>
                                    <span class="info-box-number">{{ number_format($hoursStats['average'], 2) }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box bg-warning">
                                <span class="info-box-icon"><i class="fas fa-users"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Jornadas Completas</span>
                                    <span class="info-box-number">{{ $hoursStats['full_day'] }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box bg-info">
                                <span class="info-box-icon"><i class="fas fa-percentage"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Eficiencia</span>
                                    <span class="info-box-number">{{ number_format($hoursStats['efficiency'], 1) }}%</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Acciones Rápidas -->
    <div class="row">
        <div class="col-12">
            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-bolt"></i> Acciones Rápidas
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <a href="{{ route('admin.personnel.attendances.index') }}" class="btn btn-info btn-block">
                                <i class="fas fa-list"></i><br>Ver Todas las Asistencias
                            </a>
                        </div>
                        <div class="col-md-3">
                            <button type="button" class="btn btn-success btn-block" onclick="window.print()">
                                <i class="fas fa-print"></i><br>Imprimir Dashboard
                            </button>
                        </div>
                        <div class="col-md-3">
                            <button type="button" class="btn btn-secondary btn-block" onclick="refreshDashboard()">
                                <i class="fas fa-refresh"></i><br>Actualizar Dashboard
                            </button>
                        </div>
                        <div class="col-md-3">
                            <button type="button" class="btn btn-primary btn-block" onclick="location.reload()">
                                <i class="fas fa-sync"></i><br>Recargar Página
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    <style>
        .small-box {
            border-radius: 10px;
        }
        .card {
            box-shadow: 0 0 1px rgba(0,0,0,.125), 0 1px 3px rgba(0,0,0,.2);
        }
        .info-box {
            border-radius: 10px;
        }
        .img-circle {
            border: 2px solid #fff;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
    </style>
@stop

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        $(document).ready(function() {
            // Configurar gráfico de asistencias
            const ctx = document.getElementById('attendanceChart').getContext('2d');
            const chartData = @json($chartData);
            
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: chartData.labels,
                    datasets: [{
                        label: 'Presentes',
                        data: chartData.present,
                        borderColor: 'rgb(75, 192, 192)',
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        tension: 0.1
                    }, {
                        label: 'Tarde',
                        data: chartData.late,
                        borderColor: 'rgb(255, 205, 86)',
                        backgroundColor: 'rgba(255, 205, 86, 0.2)',
                        tension: 0.1
                    }, {
                        label: 'Ausentes',
                        data: chartData.absent,
                        borderColor: 'rgb(255, 99, 132)',
                        backgroundColor: 'rgba(255, 99, 132, 0.2)',
                        tension: 0.1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });

            // Auto-refresh cada 5 minutos
            setInterval(function() {
                refreshDashboard();
            }, 300000);
        });

        function refreshDashboard() {
            location.reload();
        }
    </script>
@stop