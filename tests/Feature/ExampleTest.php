<?php

namespace Tests\Feature;

use App\Livewire\HospitalQueueManager;
use App\Models\Department;
use App\Models\DoctorStation;
use App\Models\Ticket;
use Database\Seeders\HospitalQueueSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test the Livewire queue manager application responds successfully.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $this->seed(HospitalQueueSeeder::class);

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('MediQueue');
        $response->assertSee('OPD-001');
    }

    /**
     * Test issuing ticket with NHIS and Vitals.
     */
    public function test_can_issue_ticket_with_nhis_and_vitals(): void
    {
        $this->seed(HospitalQueueSeeder::class);

        Livewire::test(HospitalQueueManager::class)
            ->set('receptionName', 'Kofi Annan')
            ->set('receptionPhone', '+233 24 999 8888')
            ->set('receptionDeptId', 1)
            ->set('receptionInsuranceType', 'NHIS')
            ->set('receptionNhisNumber', 'GH-NHIS-555111')
            ->set('receptionVitalsBp', '118/78')
            ->set('receptionVitalsTemp', '36.6')
            ->call('issueTicket')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('tickets', [
            'patient_name' => 'Kofi Annan',
            'insurance_type' => 'NHIS',
            'nhis_number' => 'GH-NHIS-555111',
            'vitals_bp' => '118/78',
        ]);
    }

    /**
     * Test dual-language voice announcement dispatch.
     */
    public function test_dispatches_dual_language_speech(): void
    {
        $this->seed(HospitalQueueSeeder::class);

        Livewire::test(HospitalQueueManager::class)
            ->set('voiceStyle', 'twi_dual')
            ->call('testVoice')
            ->assertDispatched('announce-call');
    }

    /**
     * Test doctor station break toggle.
     */
    public function test_can_toggle_doctor_station_break(): void
    {
        $this->seed(HospitalQueueSeeder::class);

        $doctor = DoctorStation::first();

        Livewire::test(HospitalQueueManager::class)
            ->call('toggleDoctorBreak', $doctor->id);

        $this->assertTrue($doctor->fresh()->is_on_break);
    }
}
