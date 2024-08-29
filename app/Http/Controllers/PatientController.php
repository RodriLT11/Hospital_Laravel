<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Appointment;
use App\Models\MedicalInfo;
use Dompdf\Dompdf;
use Dompdf\Options;    
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class PatientController extends Controller
{
    public function index()
    {
        // Obtener la información médica del paciente autenticado
        $medicalInfos = MedicalInfo::where('patient_id', Auth::id())->get();
    
        // Obtener las citas relacionadas con la información médica
        $appointments = $medicalInfos->map(function ($info) {
            // Asumiendo que tienes una relación definida con el modelo Appointment
            $appointment = Appointment::where('patient_id', Auth::id())
                                      ->whereDate('date', $info->appointment_date)
                                      ->first();
            
            // Si existe una cita con hora, combinar fecha y hora para el campo start
            $start = $info->appointment_date;
            if ($appointment && $appointment->time) {
                $start = $info->appointment_date . 'T' . $appointment->time;
            }
    
            return [
                'title' => 'Cita: ' . $info->appointment_date,
                'start' => $start, // Usa la combinación de fecha y hora
                'allDay' => false // Cambiar a false para mostrar la hora
            ];
        });
    
        // Pasar la información médica y las citas a la vista
        return view('patient.index', compact('medicalInfos', 'appointments'));
    }
    

    public function medicalInfos()
    {
        $medicalInfos = MedicalInfo::where('patient_id', Auth::id())->get();
        return view('patient.medical_infos', compact('medicalInfos'));
    }



    public function downloadPatientPDF($patientId)
    {
        // Cargar paciente y sus datos médicos
        $patient = User::with('medicalInfos')->findOrFail($patientId);
        $medicalInfos = $patient->medicalInfos;
    
        // Configuración de Dompdf
        $dompdf = new Dompdf();
        $options = new Options();
        $options->set('defaultFont', 'Arial');
        $dompdf->setOptions($options);
    
        // Renderizar vista como HTML
        $html = view('doctor.patient_report', compact('patient', 'medicalInfos'))->render();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        
        // Crear nombre de archivo seguro
        $safeName = preg_replace('/[^A-Za-z0-9_\-]/', '_', $patient->name);
        $filename = "patient_report_{$safeName}.pdf";
        
        // Descargar el PDF
        return $dompdf->stream($filename, ['Attachment' => 1]);
    }
    
}
