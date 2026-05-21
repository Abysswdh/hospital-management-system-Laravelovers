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
Route::post('/login',    [AuthController::class, 'login']);

// Protected routes (require Sanctum token)
Route::middleware('auth:sanctum')->group(function () {

    // Auth
    Route::post('/logout', [AuthController::class, 'logout']);

    // Dashboards
    Route::get('/doctor-dashboard',  [DoctorController::class,  'dashboard']);
    Route::get('/patient-dashboard', [PatientController::class, 'dashboard']);
    
    //PATIENTS
    Route::apiResource('patients',PatientController::class);

    // Appointments
    Route::apiResource('appointments', AppointmentController::class);

    // Medical Records
    Route::apiResource('medical-records', MedicalRecordController::class);

    // File uploads
    Route::post('/files/upload', [FileController::class, 'upload']);
    Route::get('/files/{id}',    [FileController::class, 'show']);
    Route::delete('/files/{id}', [FileController::class, 'destroy']);
});