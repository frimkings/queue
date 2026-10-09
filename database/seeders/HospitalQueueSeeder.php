<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\Counter;
use App\Models\DoctorStation;
use App\Models\Ticket;
use App\Models\TicketStage;
use App\Models\QueueActivity;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class HospitalQueueSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create Core Departments
        $depts = [
            [
                'name' => 'OPD / General Consult',
                'prefix' => 'OPD',
                'avg_service_time' => 18,
                'capacity' => 30,
                'color' => 'bg-blue-600',
                'last_ticket_number' => 59,
                'counters' => ['Room 101', 'Room 102', 'Counter 1'],
            ],
            [
                'name' => 'Emergency & Triage',
                'prefix' => 'EMG',
                'avg_service_time' => 4,
                'capacity' => 10,
                'color' => 'bg-rose-600',
                'last_ticket_number' => 5,
                'counters' => ['Triage Bay 1', 'Resus Room'],
            ],
            [
                'name' => 'Main Pharmacy',
                'prefix' => 'PHM',
                'avg_service_time' => 10,
                'capacity' => 25,
                'color' => 'bg-emerald-600',
                'last_ticket_number' => 31,
                'counters' => ['Window 1', 'Window 2 (Express)', 'Window 3'],
            ],
            [
                'name' => 'Diagnostic Laboratory',
                'prefix' => 'LAB',
                'avg_service_time' => 8,
                'capacity' => 15,
                'color' => 'bg-purple-600',
                'last_ticket_number' => 19,
                'counters' => ['Phlebotomy Booth A', 'Booth B'],
            ],
            [
                'name' => 'Radiology & X-Ray',
                'prefix' => 'RAD',
                'avg_service_time' => 22,
                'capacity' => 10,
                'color' => 'bg-teal-600',
                'last_ticket_number' => 14,
                'counters' => ['X-Ray Room 1', 'Ultrasound Bay 2'],
            ],
            [
                'name' => 'Cashier & Billing',
                'prefix' => 'BLG',
                'avg_service_time' => 7,
                'capacity' => 20,
                'color' => 'bg-amber-600',
                'last_ticket_number' => 22,
                'counters' => ['Cashier 1', 'Cashier 2 (NHIS/Insurance)'],
            ],
            [
                'name' => 'Specialist Consultation',
                'prefix' => 'CON',
                'avg_service_time' => 25,
                'capacity' => 10,
                'color' => 'bg-indigo-600',
                'last_ticket_number' => 11,
                'counters' => ['Specialist Suite 201', 'Room 202'],
            ],
        ];

        $deptModels = [];
        $counterModels = [];
        foreach ($depts as $d) {
            $countersList = $d['counters'];
            unset($d['counters']);

            $dept = Department::create($d);
            $deptModels[$dept->prefix] = $dept;

            foreach ($countersList as $cName) {
                $cnt = Counter::create([
                    'department_id' => $dept->id,
                    'name' => $cName,
                    'status' => 'active',
                ]);
                $counterModels[$cName] = $cnt;
            }
        }

        // 2. Create Doctor Stations
        $doc1 = DoctorStation::create([
            'department_id' => $deptModels['OPD']->id,
            'name' => 'Dr. Kwesi Mensah',
            'room' => 'Consultation Room 101',
            'specialty' => 'General & Internal Medicine',
            'is_active' => true,
        ]);

        $doc2 = DoctorStation::create([
            'department_id' => $deptModels['OPD']->id,
            'name' => 'Dr. Sarah Jenkins',
            'room' => 'Consultation Room 102',
            'specialty' => 'Pediatrics & Child Health',
            'is_active' => true,
        ]);

        $doc3 = DoctorStation::create([
            'department_id' => $deptModels['CON']->id,
            'name' => 'Dr. Evans Owusu',
            'room' => 'Specialist Suite 201',
            'specialty' => 'Cardiology & Specialist',
            'is_active' => true,
        ]);

        // 3. Create Staff User Accounts with Passwords (password123)
        $password = Hash::make('password123');

        // Administrator
        User::create([
            'name' => 'Hospital Admin',
            'email' => 'admin@hospital.org',
            'password' => $password,
            'role' => 'admin',
            'status' => 'active',
        ]);

        // Doctor 1 (Dr. Mensah)
        User::create([
            'name' => 'Dr. Kwesi Mensah',
            'email' => 'dr.mensah@hospital.org',
            'password' => $password,
            'role' => 'doctor',
            'department_id' => $deptModels['OPD']->id,
            'doctor_station_id' => $doc1->id,
            'status' => 'active',
        ]);

        // Doctor 2 (Dr. Jenkins)
        User::create([
            'name' => 'Dr. Sarah Jenkins',
            'email' => 'dr.jenkins@hospital.org',
            'password' => $password,
            'role' => 'doctor',
            'department_id' => $deptModels['OPD']->id,
            'doctor_station_id' => $doc2->id,
            'status' => 'active',
        ]);

        // Doctor 3 (Dr. Owusu)
        User::create([
            'name' => 'Dr. Evans Owusu',
            'email' => 'dr.owusu@hospital.org',
            'password' => $password,
            'role' => 'doctor',
            'department_id' => $deptModels['CON']->id,
            'doctor_station_id' => $doc3->id,
            'status' => 'active',
        ]);

        // Receptionist
        User::create([
            'name' => 'Rita Ansong (Receptionist)',
            'email' => 'reception@hospital.org',
            'password' => $password,
            'role' => 'receptionist',
            'department_id' => $deptModels['OPD']->id,
            'status' => 'active',
        ]);

        // Pharmacist
        User::create([
            'name' => 'Pharm. Evelyn Osei',
            'email' => 'pharmacy@hospital.org',
            'password' => $password,
            'role' => 'pharmacist',
            'department_id' => $deptModels['PHM']->id,
            'counter_id' => $counterModels['Window 1']->id ?? null,
            'status' => 'active',
        ]);

        // Lab Tech
        User::create([
            'name' => 'Tech. Richard Ansah',
            'email' => 'lab@hospital.org',
            'password' => $password,
            'role' => 'lab_tech',
            'department_id' => $deptModels['LAB']->id,
            'counter_id' => $counterModels['Phlebotomy Booth A']->id ?? null,
            'status' => 'active',
        ]);

        // Cashier / Billing
        User::create([
            'name' => 'Akua Darko (Cashier)',
            'email' => 'billing@hospital.org',
            'password' => $password,
            'role' => 'billing',
            'department_id' => $deptModels['BLG']->id,
            'counter_id' => $counterModels['Cashier 1']->id ?? null,
            'status' => 'active',
        ]);

        // 4. Create Sample Active Tickets & Multi-Stage Journeys
        $t1 = Ticket::create([
            'ticket_number' => 'OPD-059',
            'patient_name' => 'Samuel Adjei',
            'patient_phone' => '024-555-0101',
            'department_id' => $deptModels['OPD']->id,
            'priority' => 'priority',
            'status' => 'waiting',
        ]);
        TicketStage::create(['ticket_id' => $t1->id, 'department_id' => $deptModels['OPD']->id, 'order' => 1, 'status' => 'active']);
        TicketStage::create(['ticket_id' => $t1->id, 'department_id' => $deptModels['LAB']->id, 'order' => 2, 'status' => 'pending']);
        TicketStage::create(['ticket_id' => $t1->id, 'department_id' => $deptModels['PHM']->id, 'order' => 3, 'status' => 'pending']);

        $t2 = Ticket::create([
            'ticket_number' => 'EMG-005',
            'patient_name' => 'Ama Kyei',
            'patient_phone' => '055-333-0202',
            'department_id' => $deptModels['EMG']->id,
            'priority' => 'emergency',
            'status' => 'serving',
            'called_at' => now()->subMinutes(3),
        ]);
        TicketStage::create(['ticket_id' => $t2->id, 'department_id' => $deptModels['EMG']->id, 'order' => 1, 'status' => 'active']);

        $t3 = Ticket::create([
            'ticket_number' => 'PHM-031',
            'patient_name' => 'Kwame Asante',
            'patient_phone' => '020-111-0303',
            'department_id' => $deptModels['PHM']->id,
            'priority' => 'normal',
            'status' => 'called',
            'called_at' => now()->subMinutes(1),
        ]);
        TicketStage::create(['ticket_id' => $t3->id, 'department_id' => $deptModels['OPD']->id, 'order' => 1, 'status' => 'completed']);
        TicketStage::create(['ticket_id' => $t3->id, 'department_id' => $deptModels['PHM']->id, 'order' => 2, 'status' => 'active']);

        $t4 = Ticket::create([
            'ticket_number' => 'LAB-019',
            'patient_name' => 'Efua Boateng',
            'patient_phone' => '050-222-0404',
            'department_id' => $deptModels['LAB']->id,
            'priority' => 'normal',
            'status' => 'waiting',
        ]);
        TicketStage::create(['ticket_id' => $t4->id, 'department_id' => $deptModels['LAB']->id, 'order' => 1, 'status' => 'active']);
        TicketStage::create(['ticket_id' => $t4->id, 'department_id' => $deptModels['OPD']->id, 'order' => 2, 'status' => 'pending']);

        $t5 = Ticket::create([
            'ticket_number' => 'RAD-014',
            'patient_name' => 'Nana Yaw',
            'patient_phone' => '059-777-0707',
            'department_id' => $deptModels['RAD']->id,
            'priority' => 'normal',
            'status' => 'waiting',
        ]);
        TicketStage::create(['ticket_id' => $t5->id, 'department_id' => $deptModels['RAD']->id, 'order' => 1, 'status' => 'active']);
        TicketStage::create(['ticket_id' => $t5->id, 'department_id' => $deptModels['PHM']->id, 'order' => 2, 'status' => 'pending']);

        $t6 = Ticket::create([
            'ticket_number' => 'BLG-022',
            'patient_name' => 'Abena Darko',
            'patient_phone' => '026-666-0606',
            'department_id' => $deptModels['BLG']->id,
            'priority' => 'normal',
            'status' => 'waiting',
        ]);
        TicketStage::create(['ticket_id' => $t6->id, 'department_id' => $deptModels['BLG']->id, 'order' => 1, 'status' => 'active']);

        // 5. Create Activity Logs
        QueueActivity::create(['type' => 'issued', 'text' => 'OPD-059 registered for Samuel Adjei', 'department_id' => $deptModels['OPD']->id]);
        QueueActivity::create(['type' => 'transfer', 'text' => 'PHM-031 referred from OPD to Pharmacy', 'department_id' => $deptModels['PHM']->id]);
        QueueActivity::create(['type' => 'called', 'text' => 'EMG-005 acute case called into Triage Bay 1', 'department_id' => $deptModels['EMG']->id]);
    }
}
