<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\DoctorController;
use App\Http\Controllers\API\PatientController;
use App\Http\Controllers\API\AppointmentController;
use App\Http\Controllers\API\MedicalRecordController;
use App\Http\Controllers\API\FileController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);


// Protected routes
Route::middleware('auth:sanctum')->group(function () {

    // Auth
    Route::post('/logout', [AuthController::class, 'logout']);

    // Dashboard Doctor
    Route::middleware('role:doctor')->group(function () {
        Route::get('/doctor-dashboard', [DoctorController::class, 'dashboard']);
    });

    // Dashboard Patient
    Route::middleware('role:patient')->group(function () {
        Route::get('/patient-dashboard', [PatientController::class, 'dashboard']);
    });

    // Doctors CRUD
    Route::apiResource('doctors', DoctorController::class);
    
    // Patients CRUD
    Route::apiResource('patients', PatientController::class);

    // Appointments CRUD
    Route::apiResource('appointments', AppointmentController::class);

    // Medical Records CRUD
    Route::apiResource('medical-records', MedicalRecordController::class);

    // File Upload Routes
    Route::post('/files/upload', [FileController::class, 'store']);
    Route::get('/files', [FileController::class, 'index']);
    Route::get('/files/{file}', [FileController::class, 'show']);
    Route::get('/files/{file}/download', [FileController::class, 'download']);
    Route::put('/files/{file}', [FileController::class, 'update']);
    Route::delete('/files/{file}', [FileController::class, 'destroy']);
});