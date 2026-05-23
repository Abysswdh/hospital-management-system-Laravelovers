<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Doctor;

class DoctorController extends Controller
{
    // GET ALL
    public function index()
{
    return response()->json([
        'message' => 'doctor route works'
    ]);
}

    // CREATE
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'specialization' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
        ]);

        $doctor = Doctor::create($validated);

        return response()->json([
            'message' => 'Doctor created successfully',
            'data' => $doctor
        ], 201);
    }

    // GET SINGLE
    public function show($id)
    {
        return response()->json(
            Doctor::findOrFail($id)
        );
    }

    // UPDATE
    public function update(Request $request, $id)
    {
        $doctor = Doctor::findOrFail($id);

        $validated = $request->validate([
            'specialization' => 'sometimes|string|max:255',
            'phone' => 'sometimes|string|max:20',
        ]);

        $doctor->update($validated);

        return response()->json([
            'message' => 'Doctor updated successfully',
            'data' => $doctor
        ]);
    }

    // DELETE
    public function destroy($id)
    {
        $doctor = Doctor::findOrFail($id);

        $doctor->delete();

        return response()->json([
            'message' => 'Doctor deleted successfully'
        ]);
    }
}