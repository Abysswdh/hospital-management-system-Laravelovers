<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_doctor_can_access_dashboard()
    {
        $doctor = User::factory()->create([
            'role' => 'doctor'
        ]);

        Sanctum::actingAs($doctor);

        $response = $this->getJson('/api/doctor-dashboard');

        $response->assertStatus(200)
                 ->assertJson([
                     'message' => 'Welcome Doctor'
                 ]);
    }

    public function test_patient_cannot_access_doctor_dashboard()
    {
        $patient = User::factory()->create([
            'role' => 'patient'
        ]);

        Sanctum::actingAs($patient);

        $response = $this->getJson('/api/doctor-dashboard');

        $response->assertStatus(403);
    }
}