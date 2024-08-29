@extends('layouts.app')

@section('content')
<div class="container mx-auto mt-10">
    <div class="bg-white p-8 rounded-lg shadow-lg max-w-4xl mx-auto">
        <h2 class="text-2xl font-bold mb-6 text-gray-800">Agendar Cita</h2>
        <form action="{{ route('doctor.appointments.store') }}" method="POST" class="space-y-6">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="form-group">
                    <label for="doctor_id" class="block text-gray-700 font-medium">Doctor</label>
                    <select name="doctor_id" id="doctor_id" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                        <option value="">Seleccione un doctor</option>
                        @foreach($doctors as $doctor)
                            <option value="{{ $doctor->id }}">{{ $doctor->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="patient_id" class="block text-gray-700 font-medium">Paciente</label>
                    <select name="patient_id" id="patient_id" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                        <option value="">Seleccione un paciente</option>
                        @foreach($patients as $patient)
                            <option value="{{ $patient->id }}">{{ $patient->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group col-span-2">
                    <label for="date" class="block text-gray-100 font-medium">Fecha y Hora</label>
                    <input type="hidden" name="date" id="date">
                    <input type="hidden" name="time" id="time">
                    <div id="calendar" class="mt-4 w-full overflow-hidden"></div>
                </div>

                <div class="form-group col-span-2">
                    <label for="topic" class="block text-gray-700 font-medium">Tema a tratar</label>
                    <input type="text" name="topic" id="topic" required class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                </div>

                <div class="form-group col-span-2">
                    <label for="phone" class="block text-gray-700 font-medium">Teléfono</label>
                    <input type="text" name="phone" id="phone" required class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                </div>


            <div class="flex justify-end">
                <button type="submit" class="bg-blue-500 text-white px-6 py-2 rounded-lg shadow-md hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50">Agendar Cita</button>
            </div>
        </form>
    </div>
</div>

<!-- FullCalendar CSS and JS -->
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/main.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/locales-all.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');
    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'timeGridWeek',
        locale: 'es',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        selectable: true,
        slotDuration: '00:30:00',
        slotMinTime: '07:00:00',
        slotMaxTime: '20:59:00',
        timeZone: 'local',
        select: function(info) {
            var start = info.start;
            var localTime = new Date(start.getTime() - (start.getTimezoneOffset() * 60000));
            var startStr = localTime.toISOString().split('T')[1].substring(0, 5);

            document.getElementById('date').value = localTime.toISOString().split('T')[0];
            document.getElementById('time').value = startStr;

            console.log('Start Time: ' + startStr);
        },
        events: function(fetchInfo, successCallback, failureCallback) {
            var doctorId = document.getElementById('doctor_id').value;
            if (doctorId) {
                fetch(`/doctor/appointments/doctor/${doctorId}/events`)
                    .then(response => response.json())
                    .then(events => {
                        const adjustedEvents = events.map(event => ({
                            ...event,
                            backgroundColor: event.color || '#378006',
                            borderColor: event.color || '#378006'
                        }));

                        successCallback(adjustedEvents);
                    })
                    .catch(error => {
                        console.error('Error fetching events:', error);
                        failureCallback(error);
                    });
            } else {
                successCallback([]);
            }
        },
        editable: false,
        height: 'auto'
    });

    calendar.render();

    document.getElementById('doctor_id').addEventListener('change', function() {
        calendar.refetchEvents();
    });
});

</script>
@endsection
