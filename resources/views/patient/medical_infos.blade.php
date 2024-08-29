@extends('layouts.app')

@section('content')
<div class="container mx-auto mt-10">
    <div class="bg-white p-6 rounded-lg shadow-lg">
        <h1 class="text-2xl font-bold mb-4">Tus Citas y Signos Vitales</h1>
        <p class="mb-4">Aquí puedes ver tus citas y los signos vitales que han sido registrados por tu doctor.</p>
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white">
                <thead>
                    <tr>
                        <th class="py-2 px-4 border-b">Fecha de Consulta</th>
                        <th class="py-2 px-4 border-b">Signos Vitales</th>
                        <th class="py-2 px-4 border-b">Motivo</th>
                        <th class="px-4 py-2 text-left">Servicios</th>
                        <th class="px-4 py-2 text-left">Medicamentos</th>                        
                        <th class="py-2 px-4 border-b">Notas</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($medicalInfos as $info)
                        @php
                            $vitalSigns = json_decode($info->vital_signs, true);
                            $medicationsList = json_decode($info->medications, true);
                            $servicesList = json_decode($info->services, true);
                        @endphp
                        <tr>
                            <td class="border px-4 py-2">{{ $info->appointment_date }}</td>
                            <td class="border px-4 py-2">
                                <ul>
                                    <li>Temperatura: {{ $vitalSigns['temperature'] ?? 'N/A' }} °C</li>
                                    <li>Frecuencia Cardíaca: {{ $vitalSigns['heart_rate'] ?? 'N/A' }} bpm</li>
                                    <li>Presión Arterial: {{ $vitalSigns['blood_pressure'] ?? 'N/A' }}</li>
                                </ul>
                            </td>
                            <td class="border px-4 py-2">{{ $info->reason }}</td>
                            <td class="border px-4 py-2 max-w-xs truncate">
                                @if($servicesList && is_array($servicesList) && count($servicesList) > 0)
                                    <strong>Servicios:</strong>
                                    <ul>
                                        @foreach($servicesList as $service)
                                            @php
                                                $serviceName = $service ?? 'Nombre no disponible';
                                            @endphp
                                            <li>{{ $serviceName }}</li>
                                        @endforeach
                                    </ul>
                                @else
                                    Sin servicios
                                @endif
                            </td>
                            <td class="border px-4 py-2">
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
                            <td class="border px-4 py-2">{{ $info->notes }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
    