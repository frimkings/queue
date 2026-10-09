<?php

namespace Tests\Feature;

use App\Models\Department;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QueueApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_staff_can_login_with_valid_credentials(): void
    {
        $response = $this->postJson('/api/auth/login', [
            'email' => 'admin@hospital.org',
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'user' => [
                        'email' => 'admin@hospital.org',
                        'role' => 'admin',
                    ],
                ],
            ]);
    }

    public function test_doctor_can_login_and_get_assigned_station(): void
    {
        $response = $this->postJson('/api/auth/login', [
            'email' => 'dr.mensah@hospital.org',
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'user' => [
                        'email' => 'dr.mensah@hospital.org',
                        'role' => 'doctor',
                    ],
                ],
            ]);
    }

    public function test_login_fails_with_invalid_password(): void
    {
        $response = $this->postJson('/api/auth/login', [
            'email' => 'admin@hospital.org',
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(401)
            ->assertJson([
                'success' => false,
            ]);
    }

    public function test_can_fetch_initial_queue_state(): void
    {
        $response = $this->getJson('/api/queue/state');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'departments',
                    'tickets',
                    'doctorStations',
                    'recentActivity',
                    'stats',
                ],
            ]);
    }

    public function test_can_issue_new_ticket(): void
    {
        $dept = Department::first();

        $response = $this->postJson('/api/queue/ticket/issue', [
            'name' => 'Kofi Mensah',
            'phone' => '+233 24 111 2222',
            'department_id' => $dept->id,
            'priority' => 'priority',
            'stages' => [$dept->name, 'Main Pharmacy'],
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'name' => 'Kofi Mensah',
                    'dept' => $dept->name,
                    'priority' => 'priority',
                ],
            ]);
    }
}
