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
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.personnel.dashboard') }}">Personal</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.personnel.attendances.index') }}">Asistencias</a></li>
                <li class="breadcrumb-item active">Dashboard</li>
            </ol>
        </div>
    </div>
@stop

@section('content')
    <!-- Estadísticas Principales -->
    <div class="row mb-3">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ isset($attendances) ? $attendances->where('status', 'present')->count() : 0 }}</h3>
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
                    <h3>{{ isset($attendances) ? $attendances->where('status', 'on_time')->count() : 0 }}</h3>
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
                    <h3>{{ isset($attendances) ? $attendances->where('status', 'late')->count() : 0 }}</h3>
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
                    <h3>{{ isset($attendances) ? $attendances->where('status', 'absent')->count() : 0 }}</h3>
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

    <!-- Gráficos y Estadísticas Avanzadas -->
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-chart-line"></i> Tendencia de Asistencias (Últimos 7 días)
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
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-chart-pie"></i> Distribución por Estado
                    </h3>
                </div>
                <div class="card-body">
                    <canvas id="statusChart" style="height: 300px;"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Estadísticas Mensuales -->
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-calendar-alt"></i> Resumen del Mes
                    </h3>
                </div>
                <div class="card-body">
                    <div class="info-box">
                        <span class="info-box-icon bg-info"><i class="fas fa-percentage"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Porcentaje de Asistencia</span>
                            <span class="info-box-number">95.2%</span>
                            <div class="progress">
                                <div class="progress-bar bg-info" style="width: 95.2%"></div>
                            </div>
                            <span class="progress-description">
                                Este mes
                            </span>
                        </div>
                    </div>

                    <div class="info-box">
                        <span class="info-box-icon bg-success"><i class="fas fa-clock"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Puntualidad</span>
                            <span class="info-box-number">88.7%</span>
                            <div class="progress">
                                <div class="progress-bar bg-success" style="width: 88.7%"></div>
                            </div>
                            <span class="progress-description">
                                Llegadas a tiempo
                            </span>
                        </div>
                    </div>

                    <div class="info-box">
                        <span class="info-box-icon bg-warning"><i class="fas fa-user-clock"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Promedio Horas/Día</span>
                            <span class="info-box-number">8.2</span>
                            <div class="progress">
                                <div class="progress-bar bg-warning" style="width: 82%"></div>
                            </div>
                            <span class="progress-description">
                                Horas trabajadas
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-users"></i> Top Empleados del Mes
                    </h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Empleado</th>
                                    <th>Asistencia</th>
                                    <th>Puntualidad</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <img src="{{ asset('vendor/adminlte/dist/img/avatar.png') }}" 
                                             alt="Avatar" class="img-circle img-size-32 mr-2">
                                        Juan Pérez
                                    </td>
                                    <td><span class="badge badge-success">100%</span></td>
                                    <td><span class="badge badge-success">98%</span></td>
                                </tr>
                                <tr>
                                    <td>
                                        <img src="{{ asset('vendor/adminlte/dist/img/avatar.png') }}" 
                                             alt="Avatar" class="img-circle img-size-32 mr-2">
                                        María García
                                    </td>
                                    <td><span class="badge badge-success">98%</span></td>
                                    <td><span class="badge badge-success">95%</span></td>
                                </tr>
                                <tr>
                                    <td>
                                        <img src="{{ asset('vendor/adminlte/dist/img/avatar.png') }}" 
                                             alt="Avatar" class="img-circle img-size-32 mr-2">
                                        Carlos López
                                    </td>
                                    <td><span class="badge badge-success">96%</span></td>
                                    <td><span class="badge badge-warning">85%</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Acciones Rápidas -->
    <div class="row">
        <div class="col-12">
            <div class="card">
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

    <!-- Alertas y Notificaciones -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-bell"></i> Alertas y Notificaciones
                    </h3>
                </div>
                <div class="card-body">
                    <div class="alert alert-warning">
                        <h5><i class="icon fas fa-exclamation-triangle"></i> Atención!</h5>
                        Hay 3 empleados con más de 5 llegadas tarde este mes.
                    </div>
                    <div class="alert alert-info">
                        <h5><i class="icon fas fa-info"></i> Información</h5>
                        El promedio de horas trabajadas está por encima del objetivo mensual.
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    <style>
        .small-box .small-box-footer:hover {
            color: #fff;
            background: rgba(0,0,0,0.1);
        }
        .info-box .progress {
            height: 2px;
            margin-top: 5px;
        }
    </style>
@stop

@section('js')
    <script src="{{ asset('vendor/adminlte/plugins/chart.js/Chart.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            initializeCharts();
        });

        function initializeCharts() {
            // Gráfico de tendencia de asistencias
            const attendanceCtx = document.getElementById('attendanceChart').getContext('2d');
            new Chart(attendanceCtx, {
                type: 'line',
                data: {
                    labels: ['Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb', 'Dom'],
                    datasets: [{
                        label: 'Presentes',
                        data: [45, 42, 48, 44, 41, 20, 15],
                        borderColor: 'rgb(75, 192, 192)',
                        backgroundColor: 'rgba(75, 192, 192, 0.1)',
                        tension: 0.1
                    }, {
                        label: 'Tarde',
                        data: [5, 8, 2, 6, 9, 3, 2],
                        borderColor: 'rgb(255, 205, 86)',
                        backgroundColor: 'rgba(255, 205, 86, 0.1)',
                        tension: 0.1
                    }, {
                        label: 'Ausentes',
                        data: [2, 1, 3, 2, 1, 1, 0],
                        borderColor: 'rgb(255, 99, 132)',
                        backgroundColor: 'rgba(255, 99, 132, 0.1)',
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

            // Gráfico de distribución por estado
            const statusCtx = document.getElementById('statusChart').getContext('2d');
            new Chart(statusCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Presentes', 'Tarde', 'Ausentes', 'Medio Día'],
                    datasets: [{
                        data: [75, 15, 5, 5],
                        backgroundColor: [
                            'rgba(40, 167, 69, 0.8)',
                            'rgba(255, 193, 7, 0.8)',
                            'rgba(220, 53, 69, 0.8)',
                            'rgba(23, 162, 184, 0.8)'
                        ],
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });
        }

        function refreshDashboard() {
            location.reload();
        }
    </script>
@stop