<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Appointment;

class AppointmentController extends Controller
{
    // GET ALL APPOINTMENT NYA
    public function index()
    {
        $appointments = Appointment::with([
            'doctor',
            'patient',
            'medicalRecord'
        ])->get();

        return response()->json($appointments);
    }

    // BUAT APPOINTMENT
    public function store(Request $request)
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'doctor_id' => 'required|exists:doctors,id',
            'appointment_date' => 'required|date',
            'complaint' => 'nullable|string'
        ]);

        $appointment = Appointment::create([
            'patient_id' => $validated['patient_id'],
            'doctor_id' => $validated['doctor_id'],
            'appointment_date' => $validated['appointment_date'],
            'complaint' => $validated['complaint'] ?? null,
            'status' => 'pending'
        ]);

        return response()->json([
            'message' => 'Appointment created successfully',
            'data' => $appointment
        ], 201);
    }

    
    public function show($id)
    {
        $appointment = Appointment::with([
            'doctor',
            'patient',
            'medicalRecord'
        ])->findOrFail($id);

        return response()->json($appointment);
    }

    // UPDATE
    public function update(Request $request, $id)
    {
        $appointment = Appointment::findOrFail($id);

        $appointment->update($request->all());

        return response()->json([
            'message' => 'Appointment updated successfully',
            'data' => $appointment
        ]);
    }

    // DELETE APPOINTMENT
    public function destroy($id)
    {
        $appointment = Appointment::findOrFail($id);

        $appointment->delete();

        return response()->json([
            'message' => 'Appointment deleted successfully'
        ]);
    }
}