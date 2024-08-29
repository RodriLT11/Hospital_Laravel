@extends('layouts.app')

@section('content')
<div class="container mx-auto mt-10 p-4 sm:p-6 lg:p-8">
    <h2 class="text-2xl font-semibold text-gray-800 dark:text-gray-100 mb-6">Lista de pacientes</h2>

    <!-- Gráficos en Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <!-- Consultations by Month -->
        <div class="bg-white dark:bg-gray-800 p-4 shadow-md rounded-lg">
            <h3 class="text-xl font-semibold text-gray-800 dark:text-gray-100 mb-4">Consultas del mes</h3>
            <canvas id="consultationsChart" width="200" height="150"></canvas>
        </div>
        <!-- Number of Patients -->
        <div class="bg-white dark:bg-gray-800 p-4 shadow-md rounded-lg flex items-center justify-center">
            <div class="text-center">
                <h3 class="text-xl font-semibold text-gray-800 dark:text-gray-100 mb-4">Total de pacientes</h3>
                <div class="text-3xl font-bold text-gray-800 dark:text-gray-100">{{ $patients->count() }}</div>
            </div>
        </div>
        <!-- Age Ranges of Patients -->
        <div class="bg-white dark:bg-gray-800 p-4 shadow-md rounded-lg">
            <h3 class="text-xl font-semibold text-gray-800 dark:text-gray-100 mb-4">Rango de edades de los pacientes</h3>
            <canvas id="ageRangesChart" width="200" height="150"></canvas>
        </div>
    </div>

    <!-- Información de pacientes -->
    @foreach($patients as $patient)
    <div class="mb-4">
        <h2 class="text-2xl font-semibold text-gray-800 dark:text-gray-100 mb-4">{{ $patient->name }}</h2>

        <!-- Botón para descargar PDF -->
        <a href="{{ route('patients.downloadPDF', $patient->id) }}"
           class="inline-block bg-blue-500 text-white px-4 py-2 rounded-lg shadow-md hover:bg-blue-600">
           Descargar PDF
        </a>
    </div>

    <div class="overflow-x-auto mb-10">
        <table class="min-w-full bg-white dark:bg-gray-800 shadow-md rounded-lg">
            <thead>
                <tr>
                    <th class="px-4 py-2 text-left">Email</th>
                    <th class="px-4 py-2 text-left">Fecha de consulta</th>
                    <th class="px-4 py-2 text-left">Razon</th>
                    <th class="px-4 py-2 text-left">Servicios</th>
                    <th class="px-4 py-2 text-left">Medicamentos</th>
                    <th class="px-4 py-2 text-left">Signos vitales</th>
                    <th class="px-4 py-2 text-left">Notas</th>
                    <th class="px-4 py-2 text-left">Atendió una enfermera</th>
                    <th class="px-4 py-2 text-left">Precio total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($patient->medicalInfos as $info)
                    <tr>
                        <td class="border px-4 py-2">{{ $patient->email }}</td>
                        <td class="border px-4 py-2">{{ $info->appointment_date }}</td>
                        <td class="border px-4 py-2">{{ $info->reason }}</td>
                        <td class="border px-4 py-2 max-w-xs truncate">
                            @php
                                $servicesList = json_decode($info->services, true);
                            @endphp
                            @if($servicesList && is_array($servicesList) && count($servicesList) > 0)
                                <strong>Servicios:</strong>
                                <ul>
                                    @foreach($servicesList as $service)
                                        <li>{{ $service }}</li>
                                    @endforeach
                                </ul>
                            @else
                                Sin servicios
                            @endif
                        </td>
                        <td class="border px-4 py-2 max-w-xs truncate">
    @php
        $medicationsList = json_decode($info->medications, true);
    @endphp
    @if($medicationsList && is_array($medicationsList) && count($medicationsList) > 0)
        <strong>Medicamentos:</strong>
        <ul>
            @foreach($medicationsList as $medication)
                @php
                    // Verificar claves esperadas
                    $name = $medication['name'] ?? 'Nombre no disponible';
                    $quantity = $medication['quantity'] ?? 'N/A';
                    $price = $medication['price'] ?? 'N/A';
                @endphp
                <li>
                    {{ $name }} - {{ $quantity }} unidades 
                    ({{ $price }})
                </li>
            @endforeach
        </ul>
    @else
        Sin medicamentos
    @endif
</td>

                        <td class="border px-4 py-2">
                            @php
                                $vitalSigns = json_decode($info->vital_signs, true);
                            @endphp
                            <ul>
                                <li>Temperatura: {{ $vitalSigns['temperature'] ?? 'N/A' }} °C</li>
                                <li>Frecuencia cardíaca: {{ $vitalSigns['heart_rate'] ?? 'N/A' }} bpm</li>
                                <li>Presión arterial: {{ $vitalSigns['blood_pressure'] ?? 'N/A' }}</li>
                            </ul>
                        </td>
                        <td class="border px-4 py-2">{{ $info->notes }}</td>
                        <td class="border px-4 py-2">{{ $info->nurse_attended ? 'Sí' : 'No' }}</td>
                        <td class="border px-4 py-2">${{ number_format($info->total_price, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endforeach
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Gráfico de Consultas por Mes
   // Gráfico de Consultas por Mes
var ctxConsultations = document.getElementById('consultationsChart').getContext('2d');
var consultationsChart = new Chart(ctxConsultations, {
    type: 'line',
    data: {
        labels: @json(array_keys($consultationsByMonthFinal)), // Nombres de los meses en el eje X
        datasets: [{
            label: 'Consultas',
            data: @json(array_values($consultationsByMonthFinal)), // Número de consultas en el eje Y
            backgroundColor: 'rgba(75, 192, 192, 0.2)',
            borderColor: 'rgba(75, 192, 192, 1)',
            borderWidth: 2,
            fill: false
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'top',
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        return context.dataset.label + ': ' + context.raw;
                    }
                }
            }
        },
        scales: {
            x: {
                title: {
                    display: true,
                    text: 'Mes'
                }
            },
            y: {
                title: {
                    display: true,
                    text: 'Número de Consultas'
                },
                beginAtZero: true
            }
        }
    }
});

    // Gráfico de Rangos de Edad de los Pacientes
    var ctxAgeRanges = document.getElementById('ageRangesChart').getContext('2d');
    var ageRangesChart = new Chart(ctxAgeRanges, {
        type: 'bar',
        data: {
            labels: @json(array_keys($ageRanges)), // Rango de Edad en el eje X
            datasets: [{
                label: 'Número de Pacientes',
                data: @json(array_values($ageRanges)), // Número de Pacientes en el eje Y
                backgroundColor: 'rgba(255, 206, 86, 0.2)',
                borderColor: 'rgba(255, 206, 86, 1)',
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top',
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.dataset.label + ': ' + context.raw;
                        }
                    }
                }
            },
            scales: {
                x: {
                    title: {
                        display: true,
                        text: 'Rango de Edad'
                    }
                },
                y: {
                    title: {
                        display: true,
                        text: 'Número de Pacientes'
                    },
                    beginAtZero: true
                }
            }
        }
    });
});
</script>
@endsection
