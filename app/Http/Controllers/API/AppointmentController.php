<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Mail\AppointmentConfirmationMail;
use App\Mail\AppointmentStatusChangedMail;
use App\Models\Appointment;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Mail;

class AppointmentController extends Controller
{
    public function index(): JsonResponse
    {
        $appointments = Appointment::with('doctor', 'patient', 'medicalRecord')->get();
        return response()->json([
            'message' => 'Appointments retrieved successfully.',
            'data' => $appointments
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'doctor_id' => 'required|exists:doctors,id',
            'appointment_date' => 'required|date_format:Y-m-d H:i:s|after:now',
            'status' => 'sometimes|in:pending,confirmed,completed,cancelled',
            'complaint' => 'required|string|max:500'
        ]);

        // Set default status kalau nggak diisi
        $validated['status'] = $validated['status'] ?? 'pending';
        
        // Langsung create, nggak usah di-substr aneh-aneh
        $appointment = Appointment::create($validated);

        // Load relationships for email
        $appointment->load('patient', 'doctor');

        // Send confirmation email
        try {
            Mail::to($appointment->patient->email)->send(
                new AppointmentConfirmationMail($appointment)
            );
        } catch (\Exception $e) {
            // Log error but don't fail the request
            \Log::error('Failed to send appointment confirmation email: ' . $e->getMessage());
        }

        return response()->json([
            'message' => 'Appointment created successfully.',
            'data' => $appointment
        ], 201);
    }

    public function show(Appointment $appointment): JsonResponse
    {
        $appointment->load('doctor', 'patient', 'medicalRecord');
        return response()->json([
            'message' => 'Appointment retrieved successfully.',
            'data' => $appointment
        ]);
    }

    public function update(Request $request, Appointment $appointment): JsonResponse
    {
        $oldStatus = $appointment->status;

        $validated = $request->validate([
            'appointment_date' => 'sometimes|date_format:Y-m-d H:i:s',
            'status' => 'sometimes|in:pending,confirmed,completed,cancelled',
            'complaint' => 'sometimes|string|max:500'
        ]);

        $appointment->update($validated);

        // Send status change email if status changed
        if (isset($validated['status']) && $validated['status'] !== $oldStatus) {
            $appointment->load('patient', 'doctor');
            try {
                Mail::to($appointment->patient->email)->send(
                    new AppointmentStatusChangedMail($appointment, $oldStatus)
                );
            } catch (\Exception $e) {
                // Log error but don't fail the request
                \Log::error('Failed to send appointment status changed email: ' . $e->getMessage());
            }
        }

        return response()->json([
            'message' => 'Appointment updated successfully.',
            'data' => $appointment
        ]);
    }

    public function destroy(Appointment $appointment): JsonResponse
    {
        $appointment->delete();
        return response()->json([
            'message' => 'Appointment deleted successfully.'
        ]);
    }
}