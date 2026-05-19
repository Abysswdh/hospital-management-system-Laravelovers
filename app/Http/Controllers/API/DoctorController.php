<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Doctor;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class DoctorController extends Controller
{
    public function index(): JsonResponse
    {
        $doctors = Doctor::with('user', 'appointments')->get();
        return response()->json([
            'message' => 'Doctors retrieved successfully.',
            'data' => $doctors
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'specialization' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')->store('doctors', 'public');
        }

        $doctor = Doctor::create($validated);
        return response()->json([
            'message' => 'Doctor created successfully.',
            'data' => $doctor
        ], 201);
    }

    public function show(Doctor $doctor): JsonResponse
    {
        $doctor->load('user', 'appointments');
        return response()->json([
            'message' => 'Doctor retrieved successfully.',
            'data' => $doctor
        ]);
    }

    public function update(Request $request, Doctor $doctor): JsonResponse
    {
        $validated = $request->validate([
            'specialization' => 'sometimes|string|max:255',
            'phone' => 'sometimes|string|max:20',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')->store('doctors', 'public');
        }

        $doctor->update($validated);
        return response()->json([
            'message' => 'Doctor updated successfully.',
            'data' => $doctor
        ]);
    }

    public function destroy(Doctor $doctor): JsonResponse
    {
        $doctor->delete();
        return response()->json([
            'message' => 'Doctor deleted successfully.'
        ]);
    }

    public function dashboard()
    {
        if (auth()->user()->role !== 'doctor') {
        return response()->json([
            'message' => 'Forbidden'
        ], 403);
        }

        return response()->json([
            'message' => 'Welcome Doctor'
        ]);
    }
}
