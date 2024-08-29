@extends('layouts.app')

@section('content')

<div class="container mx-auto mt-10 p-4 sm:p-6 lg:p-8">
    <h2 class="text-2xl font-semibold text-gray-800 dark:text-gray-100 mb-6">Add Medical Information</h2>

    @if ($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-6" role="alert">
            <strong class="font-bold">Whoops!</strong>
            <span class="block sm:inline"> Hay problemas.</span>
            <ul class="mt-3 list-disc list-inside text-sm text-red-600">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('doctor.medical_infos.store') }}" method="POST" class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-6 space-y-6">
        @csrf

        <select class="form-control mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" id="patient_id" name="patient_id" required>
            <option value="">Selecciona el paciente</option>
            @foreach($patients as $patient)
                <option value="{{ $patient->id }}" data-id="{{ $patient->id }}">{{ $patient->name }}</option>
            @endforeach
        </select>

        <select class="form-control mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" id="appointment_date" name="appointment_date" required>
            <option value="">Selecciona la fecha de la cita</option>
            @if(isset($appointments))
                @foreach($appointments as $appointment)
                    <option value="{{ $appointment }}">{{ $appointment }}</option>
                @endforeach
            @endif
        </select>

        <div class="form-group">
            <label for="temperature" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Temperatura (°C)</label>
            <input type="number" step="0.1" min="1" class="form-control mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" id="temperature" name="vital_signs[temperature]" required>
        </div>

        <div class="form-group">
            <label for="heart_rate" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Ritmo cardiaco (bpm)</label>
            <input type="number" min="1" class="form-control mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" id="heart_rate" name="vital_signs[heart_rate]" required>
        </div>

        <div class="form-group">
            <label for="blood_pressure" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Presión arterial (mmHg)</label>
            <input type="text" class="form-control mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" id="blood_pressure" name="vital_signs[blood_pressure]" required>
        </div>


        <div class="form-group">
            <label for="reason" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Razon de la consulta</label>
            <textarea class="form-control mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" id="reason" name="reason"></textarea>
        </div>


        <div class="mt-6">
            <label class="block text-gray-700 font-medium mb-2">Buscar medicamentos:</label>
            <input type="text" id="search_medications" class="form-input block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" placeholder="Buscar medicamentos...">
        </div>

        <div class="mt-6" id="medications-checkboxes">
    <label class="block text-gray-700 font-medium mb-2">Selecciona los medicamentos:</label>
    <div id="medication-list">
        @if($medications->isNotEmpty())
            @foreach($medications as $medication)
                <div class="flex items-center mb-2 medication-item">
                    <input type="checkbox" name="medications[]" value="{{ $medication->name }}" id="medication_{{ $medication->id }}" data-price="{{ $medication->price }}" data-quantity="{{ $medication->quantity }}" class="form-checkbox h-5 w-5 text-indigo-600 focus:ring-indigo-500">
                    <label for="medication_{{ $medication->id }}" class="ml-2 text-gray-700">{{ $medication->name }}</label>
                    <input type="text" name="medication_prices[]" value="${{ number_format($medication->price, 2) }}" readonly class="form-input ml-2 w-24 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                    <input type="number" name="medication_quantities[]" min="1" max="{{ $medication->quantity }}" placeholder="Cantidad" class="form-input ml-2 w-20 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                </div>
            @endforeach
        @else
            <p class="text-gray-700">Sin medicamentos.</p>
        @endif
    </div>
</div>




        <div class="mt-6">
            <label class="block text-gray-700 font-medium mb-2">Buscar servicios:</label>
            <input type="text" id="search_services" class="form-input block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" placeholder="Buscar servicios...">
        </div>

        <div class="form-group mt-6" id="services-checkboxes">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Selecciona los servicios:</label>
            <div id="service-list">
                @if($services->isNotEmpty())
                    @foreach($services as $service)
                        <div class="flex items-center mb-2 service-item">
                            <input type="checkbox" name="services[]" value="{{ $service->name }}" id="service_{{ $service->name }}" data-price="{{ $service->price }}" class="form-checkbox h-5 w-5 text-indigo-600 focus:ring-indigo-500">
                            <label for="service_{{ $service->name }}" class="ml-2 text-gray-700">{{ $service->name }} - ${{ number_format($service->price, 2) }}</label>
                        </div>
                    @endforeach
                @else
                    <p class="text-gray-700">Sin servicios disponibles.</p>
                @endif
            </div>
        </div>



        <div class="form-group">
            <label for="nurse_attended" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Te atendio una enfermera antes?</label>
            <select id="nurse_attended" name="nurse_attended" class="form-control mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                <option value="0">No</option>
                <option value="1">Si</option>
            </select>
        </div>

        <div class="form-group">
            <label for="base_price" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Precio base de la consulta</label>
            <div class="flex items-center">
            <span class="text-gray-500">$</span>
            <input type="number" step="0.01" min="0" value="80" class="form-control mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" id="base_price" name="base_price" readonly>
            </div>
        </div>

        <div class="form-group">
            <label for="total_price" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Precio total</label>
            <div class="flex items-center">
            <span class="text-gray-500">$</span>
            <input type="text" id="total_price" name="total_price" readonly class="form-input mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" placeholder="0.00">
            </div>
        </div>


        <div class="form-group">
            <label for="notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Notas</label>
            <textarea class="form-control mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" id="notes" name="notes"></textarea>
        </div>

        <button type="submit" class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50">Add Medical Information</button>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const checkboxes = document.querySelectorAll('input[type="checkbox"][name="medications[]"]');
    const totalPriceField = document.getElementById('total_price');
    const medicationQuantities = document.querySelectorAll('input[name="medication_quantities[]"]');
    const serviceCheckboxes = document.querySelectorAll('input[type="checkbox"][name="services[]"]');
    const basePriceField = document.getElementById('base_price');

    function updateTotalPrice() {
        let totalPrice = 0;
        const basePrice = parseFloat(basePriceField.value) || 0;

        totalPrice += basePrice;

        // Add medications price to total price
        checkboxes.forEach((checkbox, index) => {
            if (checkbox.checked) {
                const price = parseFloat(checkbox.getAttribute('data-price')) || 0;
                const quantity = parseFloat(medicationQuantities[index].value) || 1;
                totalPrice += price * quantity;
            }
        });

        // Add selected service price to total price
        serviceCheckboxes.forEach((checkbox) => {
            if (checkbox.checked) {
                const price = parseFloat(checkbox.getAttribute('data-price')) || 0;
                totalPrice += price;
            }
        });


        totalPriceField.value = totalPrice.toFixed(2);
    }

    // Update total price when checkboxes are changed
    checkboxes.forEach((checkbox, index) => {
        checkbox.addEventListener('change', function() {
            if (checkbox.checked) {
                medicationQuantities[index].removeAttribute('disabled');
                medicationQuantities[index].setAttribute('required', 'required');
            } else {
                medicationQuantities[index].setAttribute('disabled', 'disabled');
                medicationQuantities[index].removeAttribute('required');
                medicationQuantities[index].value = '';
            }
            updateTotalPrice();
        });
    });

    // Update total price when quantities are changed
    medicationQuantities.forEach((quantityField, index) => {
        quantityField.addEventListener('input', updateTotalPrice);
    });

        // Update total price when service checkboxes are changed
    serviceCheckboxes.forEach((checkbox) => {
        checkbox.addEventListener('change', updateTotalPrice);
    });


    // Initial calculation on page load if there are pre-selected checkboxes
    updateTotalPrice();

    // Handle dynamic patient selection and related medications and appointments
    document.getElementById('patient_id').addEventListener('change', function() {
        const patientId = this.value;
        const appointmentSelect = document.getElementById('appointment_date');
        appointmentSelect.innerHTML = '<option value="">Select Appointment Date</option>'; // Clear current options

        if (patientId) {
            fetch(`/get-appointments/${patientId}`)
                .then(response => response.json())
                .then(data => {
                    data.forEach(appointment => {
                        const option = document.createElement('option');
                        option.value = appointment.dateTime; // Set the value to the formatted dateTime
                        option.textContent = appointment.dateTime; // Display the formatted dateTime
                        appointmentSelect.appendChild(option);
                    });
                })
                .catch(error => console.error('Error fetching appointments:', error));
        }

        // Fetch medications based on selected patient
        fetch(`/get-medications/${patientId}`)
            .then(response => response.json())
            .then(data => {
                const medicationsContainer = document.getElementById('medications-checkboxes');
                medicationsContainer.innerHTML = ''; // Clear existing checkboxes
                if (Array.isArray(data)) {
                    data.forEach(medication => {
                        const checkboxContainer = document.createElement('div');
                        checkboxContainer.className = 'flex items-center mb-2';
                        checkboxContainer.innerHTML = `
                            <input type="checkbox" id="medication_${medication.id}" name="medications[]" value="${medication.id}" data-price="${medication.price}" class="form-checkbox h-5 w-5 text-indigo-600 focus:ring-indigo-500">
                            <label for="medication_${medication.id}" class="ml-2 text-gray-700">${medication.name}</label>
                            <input type="number" step="0.01" name="medication_prices[]" value="${medication.price}" readonly class="form-input ml-2 w-20 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            <input type="number" name="medication_quantities[]" min="1" max="${medication.quantity}" placeholder="Quantity" disabled class="form-input ml-2 w-20 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                        `;
                        medicationsContainer.appendChild(checkboxContainer);
                    });

                    // Reattach event listeners to new checkboxes and quantity fields
                    const newCheckboxes = medicationsContainer.querySelectorAll('input[type="checkbox"][name="medications[]"]');
                    const newQuantities = medicationsContainer.querySelectorAll('input[name="medication_quantities[]"]');

                    newCheckboxes.forEach((checkbox, index) => {
                        checkbox.addEventListener('change', function() {
                            if (checkbox.checked) {
                                newQuantities[index].removeAttribute('disabled');
                                newQuantities[index].setAttribute('required', 'required');
                            } else {
                                newQuantities[index].setAttribute('disabled', 'disabled');
                                newQuantities[index].removeAttribute('required');
                                newQuantities[index].value = '';
                            }
                            updateTotalPrice();
                        });
                    });

                    newQuantities.forEach((quantityField) => {
                        quantityField.addEventListener('input', updateTotalPrice);
                    });

                    // Initial calculation for newly added checkboxes and quantities
                    updateTotalPrice();
                } else {
                    console.error('Invalid data format', data);
                }
            })
            .catch(error => console.error('Error fetching medications:', error));
    });

});
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('search_medications');
    const medicationItems = document.querySelectorAll('.medication-item');

    // Ocultar todos los medicamentos excepto los seleccionados al cargar la página
    medicationItems.forEach(function(item) {
        const checkbox = item.querySelector('input[type="checkbox"]');
        if (!checkbox.checked) {
            item.style.display = 'none';
        }
    });

    searchInput.addEventListener('input', function() {
        const searchTerm = searchInput.value.toLowerCase();

        medicationItems.forEach(function(item) {
            const medicationName = item.querySelector('label').textContent.toLowerCase();
            const checkbox = item.querySelector('input[type="checkbox"]');

            if (searchTerm) {
                // Si hay un término de búsqueda, mostrar los que coinciden con el filtro
                if (medicationName.includes(searchTerm)) {
                    item.style.display = 'flex';
                } else {
                    item.style.display = 'none';
                }
            } else {
                // Si el campo de búsqueda está vacío, mostrar solo los seleccionados
                if (checkbox.checked) {
                    item.style.display = 'flex';
                } else {
                    item.style.display = 'none';
                }
            }
        });
    });
});
document.addEventListener('DOMContentLoaded', function() {
    const searchServicesInput = document.getElementById('search_services');
    const serviceItems = document.querySelectorAll('.service-item');

    // Ocultar todos los servicios excepto los seleccionados al cargar la página
    serviceItems.forEach(function(item) {
        const checkbox = item.querySelector('input[type="checkbox"]');
        if (!checkbox.checked) {
            item.style.display = 'none';
        }
    });

    searchServicesInput.addEventListener('input', function() {
        const searchTerm = searchServicesInput.value.toLowerCase();

        serviceItems.forEach(function(item) {
            const serviceName = item.querySelector('label').textContent.toLowerCase();
            const checkbox = item.querySelector('input[type="checkbox"]');

            if (searchTerm) {
                // Si hay un término de búsqueda, mostrar los que coinciden con el filtro
                if (serviceName.includes(searchTerm)) {
                    item.style.display = 'flex';
                } else {
                    item.style.display = 'none';
                }
            } else {
                // Si el campo de búsqueda está vacío, mostrar solo los seleccionados
                if (checkbox.checked) {
                    item.style.display = 'flex';
                } else {
                    item.style.display = 'none';
                }
            }
        });
    });
});


</script>


@endsection
