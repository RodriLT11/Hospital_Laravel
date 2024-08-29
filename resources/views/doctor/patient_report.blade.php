<!DOCTYPE html>
<html>
<head>
    <title>Reporte del paciente</title>
    <style>
    </style>
</head>
<body>
    <h1>{{ $patient->name }} - {{ $patient->email }}</h1>
    <table border="1" cellspacing="0" cellpadding="5">
        <thead>
            <tr>

                <th>Fecha de consulta</th>
                <th>Razon</th>
                <th>Servicios</th>
                <th>Medicamentos</th>
                <th>Signos vitales</th>
                <th>Notas</th>
                <th>Atendio una enfermera</th>
                <th>Precio total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($medicalInfos as $info)
                <tr>

                    <td>{{ $info->appointment_date }}</td>
                    <td>{{ $info->reason }}</td>
                    <td>
                        @php
                            $servicesList = json_decode($info->services, true);
                        @endphp
                        @if($servicesList && is_array($servicesList) && count($servicesList) > 0)
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
                    <td>
                        @php
                            $vitalSigns = json_decode($info->vital_signs, true);
                        @endphp
                        <ul>
                            <li>Temperatura: {{ $vitalSigns['temperature'] ?? 'N/A' }} °C</li>
                            <li>Frecuencia cardíaca: {{ $vitalSigns['heart_rate'] ?? 'N/A' }} bpm</li>
                            <li>Presión arterial: {{ $vitalSigns['blood_pressure'] ?? 'N/A' }}</li>
                        </ul>
                    </td>
                    <td>{{ $info->notes }}</td>
                    <td>
                        {{ $info->nurse_attended ? 'Sí' : 'No' }}
                    </td>
                    <td>${{ number_format($info->total_price, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
