<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Patient;

class PatientController extends Controller
{
    public function index()
    {
        return response()->json([
            'message' => 'Patients retrieved successfully.',
            'data' => Patient::all()
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'date_of_birth' => 'required|date',
            'address' => 'required|string',
            'gender' => 'required|in:male,female',
            'phone' => 'required|string|max:20',
        ]);

        $patient = Patient::create($validated);

        return response()->json([
            'message' => 'Patient created successfully',
            'data' => $patient
        ], 201);
    }

    public function show($id)
    {
        $patient = Patient::findOrFail($id);
        return response()->json([
            'message' => 'Patient retrieved successfully.',
            'data' => $patient
        ]);
    }

    public function update(Request $request, $id)
{
    $patient = Patient::findOrFail($id);

    $validated = $request->validate([
        'name' => 'sometimes|string|max:255',
        'email' => 'sometimes|email|max:255',
        'date_of_birth' => 'sometimes|date',
        'address' => 'sometimes|string',
        'gender' => 'sometimes|in:male,female',
        'phone' => 'sometimes|string|max:20',
    ]);

    $patient->update($validated);

    return response()->json([
        'message' => 'Patient updated successfully',
        'data' => $patient
    ]);
}

    public function destroy($id)
{
    $patient = Patient::findOrFail($id);

    // hapus relasi dulu kalau ada
    $patient->appointments()->delete();

    $patient->delete();

    return response()->json([
        'message' => 'Patient deleted successfully'
    ]);
}

public function dashboard(Request $request)
    {
        // 1. Ambil user yang sedang login
        $user = $request->user();
        
        // 2. Ambil data pasien berdasarkan user_id (karena ada relasi)
        $patient = Patient::where('user_id', $user->id)->first();

        if (!$patient) {
            return response()->json(['message' => 'Profil pasien tidak ditemukan'], 404);
        }

        // 3. Ambil janji temu milik pasien tersebut
        $appointments = \App\Models\Appointment::where('patient_id', $patient->id)
            ->with(['doctor', 'medicalRecord']) // Mengambil relasi sekalian
            ->get();

        return response()->json([
            'message' => 'Data dashboard berhasil dimuat',
            'data' => [
                'profile' => $patient,
                'appointments' => $appointments
            ]
        ]);
    }
}