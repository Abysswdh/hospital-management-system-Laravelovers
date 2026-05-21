<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Patient;

class PatientController extends Controller
{
    public function index()
    {
        return response()->json(Patient::all());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required',
            'age' => 'required|integer',
            'gender' => 'required',
            'address' => 'required',
            'phone' => 'required'
        ]);

        $patient = Patient::create([
            'user_id' => auth()->id(),
            ...$validated
        ]);

        return response()->json([
            'message' => 'Patient created successfully',
            'data' => $patient
        ], 201);
    }

    public function show($id)
    {
        return response()->json(
            Patient::findOrFail($id)
        );
    }

    public function update(Request $request, $id)
    {
        $patient = Patient::findOrFail($id);

        $patient->update($request->all());

        return response()->json([
            'message' => 'Patient updated successfully',
            'data' => $patient
        ]);
    }

    public function destroy($id)
    {
        Patient::findOrFail($id)->delete();

        return response()->json([
            'message' => 'Patient deleted successfully'
        ]);
    }
}