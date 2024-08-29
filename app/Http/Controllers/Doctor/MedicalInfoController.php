<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use App\Models\MedicalInfo;
use App\Models\User;
use App\Models\Medicine;
use App\Models\Service;
use App\Models\Appointment;
use Carbon\Carbon;
use Illuminate\Http\Request;

class MedicalInfoController extends Controller
{
    public function create(Request $request)
    {
        // Obtener pacientes
        $patients = User::where('role', 'patient')->get();
        $medications = Medicine::all(); // Obtiene todos los medicamentos
        $services = Service::all(); // Obtiene todos los servicios
        // Inicializar citas vacías
        $appointments = [];

        // Si hay un patient_id en la solicitud, obtener citas correspondientes
        if ($request->has('patient_id') && $request->input('patient_id')) {
            $patientId = $request->input('patient_id');
            $appointments = Appointment::where('patient_id', $patientId)->pluck('date');
        }

        return view('doctor.create_medical_info', compact('patients', 'appointments', 'medications', 'services'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'patient_id' => 'required|exists:users,id',
            'appointment_date' => 'required|date',
            'services' => 'nullable|array',
            'nurse_attended' => 'required|boolean',
            'vital_signs.temperature' => 'required|numeric',
            'vital_signs.heart_rate' => 'required|numeric',
            'vital_signs.blood_pressure' => 'required|string',
            'reason' => 'required|string',
            'medications' => 'nullable|array',
            'medication_quantities' => 'nullable|array',
            'medication_prices' => 'nullable|array',
            'notes' => 'nullable|string',
            'total_price' => 'required|numeric',
        ]);
            // Verificar si la cita ya ha sido registrada
        $existingAppointment = MedicalInfo::where('patient_id', $request->input('patient_id'))
        ->where('appointment_date', $request->input('appointment_date'))
        ->first();
        // dd($existingAppointment);
        if ($existingAppointment) {
            return redirect()->back()->withErrors(['appointment_date' => 'La cita ya ha sido atendida.'])->withInput();
        }

        $vitalSigns = [
            'temperature' => $request->input('vital_signs.temperature'),
            'heart_rate' => $request->input('vital_signs.heart_rate'),
            'blood_pressure' => $request->input('vital_signs.blood_pressure'),
        ];

        // Crear la información médica en la base de datos
        $medicalInfo = MedicalInfo::create([
            'patient_id' => $request->input('patient_id'),
            'appointment_date' => $request->input('appointment_date'),
            'services' => json_encode($request->input('services', [])),
            'medications' => json_encode($this->prepareMedicationsData($request->input('medications', []), $request->input('medication_quantities', []), $request->input('medication_prices', []))),
            'nurse_attended' => $request->input('nurse_attended'),
            'vital_signs' => json_encode($vitalSigns),
            'reason' => $request->input('reason'),
            'notes' => $request->input('notes'),
            'total_price' => $request->input('total_price'),
        ]);


        // Actualizar la cantidad de medicamentos en la base de datos
        $this->updateMedicationQuantities($request->input('medications', []), $request->input('medication_quantities', []));

        return redirect()->route('doctor.dashboard')->with('success', 'Medical information added successfully.');
    }

// Método para preparar datos de medicamentos
private function prepareMedicationsData($medications, $quantities, $prices)
{
    $medicationData = [];

    foreach ($medications as $index => $medicationName) {
        // Busca el medicamento por nombre
        $medication = Medicine::where('name', $medicationName)->first();
        
        if (!$medication) {
            // Si el medicamento no se encuentra, retorna un error
            return redirect()->back()->withErrors(['medications' => "Medicamento con nombre $medicationName no encontrado."])->withInput();
        }

        // Agrega los datos del medicamento al array
        $medicationData[] = [
            'medication_id' => $medication->id,
            'name' => $medication->name,
            'quantity' => $quantities[$index] ?? 0,
            'price' => $prices[$index] ?? 0,
        ];
    }

    return $medicationData;
}


private function updateMedicationQuantities($medications, $quantities)
{
    foreach ($medications as $index => $medicationName) {
        // Busca el medicamento por nombre
        $medication = Medicine::where('name', $medicationName)->first();
        // dd($medication);
            
        if ($medication) {
            if ($medication->quantity < $quantities[$index]) {
                throw new \Exception("No hay suficiente cantidad de {$medication->name} disponible.");
            }
            $medication->quantity -= $quantities[$index];
            $medication->save();
        } else {
            throw new \Exception("Medicamento con nombre {$medicationName} no encontrado.");
        }
    }
}






    public function index()
{
    $patients = User::where('role', 'patient')->with('medicalInfos')->get();

    // Mapa de meses
    $monthsMap = [
        1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
        5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
        9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
    ];

    $months = [
        1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
        5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
        9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
    ];

    // Preparar datos para gráficos
    $consultationsByMonthRaw = MedicalInfo::selectRaw('MONTH(appointment_date) as month, COUNT(*) as count')
        ->groupBy('month')
        ->orderBy('month')
        ->pluck('count', 'month')
        ->toArray();

    // Reemplazar los números de los meses con los nombres de los meses
    $consultationsByMonth = [];
    foreach ($consultationsByMonthRaw as $month => $count) {
        $consultationsByMonth[$monthsMap[$month]] = $count;
    }

    // Asegurarse de que todos los meses estén presentes en el gráfico
    $consultationsByMonthFinal = [];
    foreach ($monthsMap as $key => $month) {
        $consultationsByMonthFinal[$month] = $consultationsByMonth[$month] ?? 0;
    }

    // Calcular las edades de los pacientes
    $ages = $patients->pluck('age');

    // Agrupar edades en rangos
    $ageRanges = [
        '0-10' => 0,
        '11-20' => 0,
        '21-30' => 0,
        '31-40' => 0,
        '41-50' => 0,
        '51-60' => 0,
        '61-70' => 0,
        '71-80' => 0,
        '81-90' => 0,
        '91-100' => 0,
    ];

    foreach ($ages as $age) {
        if ($age <= 10) $ageRanges['0-10']++;
        elseif ($age <= 20) $ageRanges['11-20']++;
        elseif ($age <= 30) $ageRanges['21-30']++;
        elseif ($age <= 40) $ageRanges['31-40']++;
        elseif ($age <= 50) $ageRanges['41-50']++;
        elseif ($age <= 60) $ageRanges['51-60']++;
        elseif ($age <= 70) $ageRanges['61-70']++;
        elseif ($age <= 80) $ageRanges['71-80']++;
        elseif ($age <= 90) $ageRanges['81-90']++;
        else $ageRanges['91-100']++;
    }

    return view('doctor.patients', compact('patients', 'consultationsByMonthFinal', 'months', 'ageRanges'));
}


    // Método para mostrar el formulario de registro de medicamentos
    public function createMedicine()
    {
        return view('doctor.register_medicine');
    }

    // Método para almacenar los datos del formulario
    public function storeMedicine(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric',
            'quantity' => 'required|numeric',
        ]);

        Medicine::create([
            'name' => $request->input('name'),
            'quantity' => $request->input('quantity'),
            'description' => $request->input('description'),
            'price' => $request->input('price'),
        ]);

        return redirect()->route('doctor.medicines.create')->with('success', 'Medicamento registrado exitosamente.');
    }

    // Método para obtener los medicamentos
    public function getMedicines()
    {
        $medicines = Medicine::all(['id', 'name', 'price']);
        return response()->json($medicines);
    }

    public function showMedicines()
    {
        $medicines = Medicine::all(); // Obtén todos los medicamentos
        return view('secretary.medicines', compact('medicines')); // Pasa los datos a la vista
    }

    public function showMedicinesdoctor()
    {
        $medicines = Medicine::all(); // Obtén todos los medicamentos
        return view('doctor.medicines', compact('medicines')); // Pasa los datos a la vista
    }

    // Para registrar servicios
    public function createService()
    {
        return view('doctor.register_services');
    }

    // Guardar el nuevo servicio en la base de datos
    public function storeService(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
        ]);

        Service::create([
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
        ]);

        return redirect()->route('services.create')->with('success', 'Servicio registrado con éxito.');
    }

    public function showServices()
    {
        $services = Service::all(); // Obtén todos los servicios
        return view('secretary.services', compact('services')); // Pasa los servicios a la vista
    }

    public function showServicesdoctor()
    {
        $services = Service::all(); // Obtén todos los servicios
        return view('doctor.services', compact('services')); // Pasa los servicios a la vista
    }

    
}
