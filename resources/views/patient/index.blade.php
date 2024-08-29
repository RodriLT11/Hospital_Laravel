@extends('layouts.app')

@section('content')
<div class="container mx-auto mt-10">
    <!-- FullCalendar CSS -->
    <link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.10.2/main.min.css' rel='stylesheet' />

    <!-- FullCalendar JS -->
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.10.2/main.min.js'></script>

    <div class="bg-white p-6 rounded-lg shadow-lg">
        <h1 class="text-2xl font-bold mb-4">Bienvenido, Paciente</h1>
        <p class="mb-4">Aquí encontrarás registros sobre tus consultas.</p>

        <!-- Calendario de citas -->
        <div id="calendar" class="mb-6"></div>

        <!-- Tabla de servicios y medicamentos -->
        <table class="min-w-full bg-white">
            <thead class="bg-gray-800 text-white">
                <tr>
                    <th class="w-1/3 px-4 py-2">Servicio y Medicamentos</th>
                </tr>
            </thead>
            <tbody class="text-gray-700">
                @foreach($medicalInfos as $info)
                    <tr>
                        <td class="border px-4 py-2 max-w-xs truncate">
                            @php
                                $medicationsList = json_decode($info->medications, true);
                                $servicesList = json_decode($info->services, true);
                            @endphp
                            <strong>Servicios:</strong><br>
                            @if($servicesList && is_array($servicesList))
                                @foreach($servicesList as $service)
                                    {{ $service }}<br>
                                @endforeach
                            @else
                                No hay servicios
                            @endif
                            <br>
                                                           @if($medicationsList && is_array($medicationsList) && count($medicationsList) > 0)
                                    <strong>Medicamentos:</strong>
                                    <ul>
                                        @foreach($medicationsList as $medication)
                                            @php
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
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var calendarEl = document.getElementById('calendar');

        var calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            events: @json($appointments), // Pasar los datos del calendario
            eventColor: '#378006',
            editable: true, // Hacer eventos editables si es necesario
            selectable: true, // Permitir seleccionar fechas
            displayEventTime: true, // Mostrar la hora de los eventos
            eventTimeFormat: { // Formato de la hora del evento
                hour: '2-digit',
                minute: '2-digit',
                hour12: false
            }
        });

        calendar.render();
    });
</script>
@endsection
