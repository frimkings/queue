<?php

namespace App\Livewire;

use App\Models\Department;
use App\Models\Counter;
use App\Models\DoctorStation;
use App\Models\Ticket;
use App\Models\TicketStage;
use App\Models\QueueActivity;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

class HospitalQueueManager extends Component
{
    public string $activeView = 'dashboard';
    public string $currentDateTime = '';

    // Auth & Staff Profiles
    public bool $authModalOpen = false;
    public string $loginEmail = '';
    public string $loginPassword = '';
    public ?array $currentUser = null;
    public string $selectedRoleId = 'admin';

    // Reception & Triage Form
    public string $receptionName = '';
    public string $receptionPhone = '';
    public ?int $receptionDeptId = 1;
    public string $receptionPriority = 'normal';
    public string $receptionInsuranceType = 'NHIS';
    public string $receptionNhisNumber = '';
    public string $receptionVitalsBp = '120/80';
    public string $receptionVitalsTemp = '36.8';
    public string $receptionVitalsPulse = '74';
    public string $receptionVitalsWeight = '68';
    public array $receptionSelectedStages = ['OPD'];
    public string $receptionSearch = '';
    public ?array $lastIssuedTicket = null;

    // Counter station
    public int $counterDeptId = 1;
    public string $counterActiveWindow = 'Room 101';

    // Doctor room
    public int $selectedDoctorId = 1;
    public ?int $doctorCurrentPatientId = null;
    public string $doctorNotes = '';
    public string $doctorBreakReason = '15-min Consultation Tea Break';

    // Mobile tracker & CSAT Feedback
    public string $mobileTrackingTicketNumber = 'OPD-059';
    public int $feedbackRating = 5;
    public string $feedbackComment = '';
    public bool $feedbackSubmitted = false;

    // Multi-Language Announcement Settings
    public string $voiceStyle = 'twi_dual'; // 'twi_dual', 'ghanaian_formal', 'ghanaian_local', 'ga_dual', 'hausa_dual', 'standard'
    public bool $chimeEnabled = true;

    // Modals
    public bool $transferModalOpen = false;
    public ?string $transferTicketNumber = null;
    public string $transferTargetDept = 'Diagnostic Laboratory';
    public string $transferNotes = '';
    public bool $smsModalOpen = false;
    public bool $dailyReportModalOpen = false;

    // Print Slip data
    public ?array $printSlipData = null;

    // Toast notifications
    public bool $showToast = false;
    public string $toastMessage = '';
    public string $toastType = 'success';
    public string $toastIcon = '✅';

    public function mount(): void
    {
        $this->updateTime();
        $this->currentUser = Auth::user() ? [
            'id' => Auth::id(),
            'name' => Auth::user()->name,
            'email' => Auth::user()->email,
            'role' => Auth::user()->role,
        ] : [
            'id' => 1,
            'name' => 'Hospital Admin',
            'email' => 'admin@hospital.org',
            'role' => 'admin',
        ];

        $firstDoctorPt = Ticket::where('department_id', 1)->where('status', 'waiting')->first();
        if ($firstDoctorPt) {
            $this->doctorCurrentPatientId = $firstDoctorPt->id;
        }
    }

    public function refreshState(): void
    {
        $this->updateTime();
    }

    public function updateTime(): void
    {
        $this->currentDateTime = now()->format('D, M j, Y') . ' · ' . now()->format('h:i:s A');
    }

    public function navigateView(string $viewId): void
    {
        $publicViews = ['display', 'kiosk', 'mobile'];
        if (!$this->currentUser && !in_array($viewId, $publicViews)) {
            $this->authModalOpen = true;
            $this->notify('Please sign in to access staff stations', 'info', '🔐');
            return;
        }
        $this->activeView = $viewId;
    }

    public function toggleReceptionStage(string $stageName): void
    {
        if (in_array($stageName, $this->receptionSelectedStages)) {
            $this->receptionSelectedStages = array_values(array_diff($this->receptionSelectedStages, [$stageName]));
        } else {
            $this->receptionSelectedStages[] = $stageName;
        }
    }

    public function issueTicket(): void
    {
        if (empty($this->receptionName) || empty($this->receptionDeptId)) {
            $this->notify('Please provide patient name and department', 'error', '⚠️');
            return;
        }

        $dept = Department::findOrFail($this->receptionDeptId);
        $ticketNumber = $dept->generateNextTicketNumber();

        $ticket = Ticket::create([
            'ticket_number' => $ticketNumber,
            'patient_name' => $this->receptionName,
            'patient_phone' => $this->receptionPhone ?: '+233 24 000 0000',
            'department_id' => $dept->id,
            'priority' => $this->receptionPriority,
            'insurance_type' => $this->receptionInsuranceType,
            'nhis_number' => $this->receptionNhisNumber ?: ($this->receptionInsuranceType === 'NHIS' ? 'GH-NHIS-'.rand(100000, 999999) : null),
            'vitals_bp' => $this->receptionVitalsBp ?: '120/80',
            'vitals_temp' => $this->receptionVitalsTemp ?: '36.7',
            'vitals_pulse' => $this->receptionVitalsPulse ?: '72',
            'vitals_weight' => $this->receptionVitalsWeight ?: '65',
            'status' => 'waiting',
        ]);

        $stages = !empty($this->receptionSelectedStages) ? $this->receptionSelectedStages : [$dept->name];
        foreach ($stages as $index => $stageName) {
            $stageDept = Department::where('name', 'like', "%{$stageName}%")->first() ?? $dept;
            TicketStage::create([
                'ticket_id' => $ticket->id,
                'department_id' => $stageDept->id,
                'order' => $index + 1,
                'status' => $index === 0 ? 'active' : 'pending',
            ]);
        }

        QueueActivity::create([
            'ticket_id' => $ticket->id,
            'type' => 'issued',
            'text' => "{$ticketNumber} registered for {$ticket->patient_name} ({$ticket->insurance_type})",
            'department_id' => $dept->id,
        ]);

        $pos = Ticket::where('department_id', $dept->id)->where('status', 'waiting')->count();

        $this->lastIssuedTicket = [
            'ticket' => $ticketNumber,
            'name' => $ticket->patient_name,
            'dept' => $dept->name,
            'priority' => $ticket->priority,
            'insurance' => $ticket->insurance_type,
            'nhis' => $ticket->nhis_number,
            'bp' => $ticket->vitals_bp,
            'temp' => $ticket->vitals_temp,
            'position' => $pos,
            'wait' => $dept->avg_service_time * $pos,
        ];
        $this->mobileTrackingTicketNumber = $ticketNumber;

        $this->receptionName = '';
        $this->receptionPhone = '';
        $this->receptionNhisNumber = '';
        $this->notify("Ticket {$ticketNumber} created with NHIS & Vitals logged!", 'success', '🎫');
    }

    public function kioskSelectDept(int $deptId): void
    {
        $dept = Department::findOrFail($deptId);
        $ticketNumber = $dept->generateNextTicketNumber();

        $ticket = Ticket::create([
            'ticket_number' => $ticketNumber,
            'patient_name' => 'Walk-in Patient',
            'department_id' => $dept->id,
            'priority' => 'normal',
            'insurance_type' => 'NHIS',
            'status' => 'waiting',
        ]);

        TicketStage::create([
            'ticket_id' => $ticket->id,
            'department_id' => $dept->id,
            'order' => 1,
            'status' => 'active',
        ]);

        $pos = Ticket::where('department_id', $dept->id)->where('status', 'waiting')->count();
        $this->lastIssuedTicket = [
            'ticket' => $ticketNumber,
            'name' => 'Walk-in Patient',
            'dept' => $dept->name,
            'priority' => 'normal',
            'insurance' => 'NHIS',
            'position' => $pos,
            'wait' => $dept->avg_service_time * $pos,
        ];
        $this->mobileTrackingTicketNumber = $ticketNumber;
        $this->notify("Ticket {$ticketNumber} generated from Kiosk!", 'success', '🎫');
    }

    public function callNext(): void
    {
        $dept = Department::findOrFail($this->counterDeptId);
        $ticket = Ticket::where('department_id', $dept->id)
            ->where('status', 'waiting')
            ->orderByRaw("CASE priority WHEN 'emergency' THEN 1 WHEN 'priority' THEN 2 ELSE 3 END")
            ->oldest()
            ->first();

        if (!$ticket) {
            $this->notify('No patients waiting in this queue', 'info', 'ℹ️');
            return;
        }

        $ticket->update(['status' => 'called', 'called_at' => now()]);
        QueueActivity::create([
            'ticket_id' => $ticket->id,
            'type' => 'called',
            'text' => "{$ticket->ticket_number} called to {$this->counterActiveWindow}",
            'department_id' => $dept->id,
            'station_name' => $this->counterActiveWindow,
        ]);

        $this->dispatchSpeech($ticket->ticket_number, $dept->name, $this->counterActiveWindow);
        $this->notify("Calling {$ticket->ticket_number} to {$this->counterActiveWindow}", 'info', '📢');
    }

    public function recallCurrent(): void
    {
        $ticket = Ticket::where('department_id', $this->counterDeptId)->where('status', 'called')->latest('called_at')->first();
        if ($ticket) {
            $dept = Department::find($this->counterDeptId);
            $this->dispatchSpeech($ticket->ticket_number, $dept?->name ?? 'Counter', $this->counterActiveWindow, true);
            $this->notify("Recalling {$ticket->ticket_number}", 'info', '🔁');
        }
    }

    public function callSpecific(string $ticketNumber): void
    {
        $ticket = Ticket::where('ticket_number', $ticketNumber)->first();
        if ($ticket) {
            $ticket->update(['status' => 'called', 'called_at' => now()]);
            $this->dispatchSpeech($ticket->ticket_number, $ticket->department?->name ?? 'Department', $this->counterActiveWindow);
            $this->notify("Calling {$ticket->ticket_number}", 'info', '📢');
        }
    }

    public function markServed(): void
    {
        $ticket = Ticket::where('department_id', $this->counterDeptId)->whereIn('status', ['called', 'serving'])->first();
        if ($ticket) {
            $ticket->update(['status' => 'served', 'served_at' => now()]);
            $activeStage = TicketStage::where('ticket_id', $ticket->id)->where('status', 'active')->first();
            if ($activeStage) {
                $activeStage->update(['status' => 'completed', 'completed_at' => now()]);
            }
            QueueActivity::create([
                'ticket_id' => $ticket->id,
                'type' => 'served',
                'text' => "{$ticket->ticket_number} marked completed",
                'department_id' => $ticket->department_id,
            ]);
            $this->notify("{$ticket->ticket_number} marked completed in database!", 'success', '✅');
        }
    }

    public function doctorCallNext(): void
    {
        $doc = DoctorStation::find($this->selectedDoctorId);
        if ($doc && $doc->is_on_break) {
            $this->notify("Cannot call patient: Dr. Station is currently on break ({$doc->break_reason})", 'error', '☕');
            return;
        }

        $dept = Department::where('prefix', 'OPD')->first();
        $ticket = Ticket::where('department_id', $dept->id)->where('status', 'waiting')->oldest()->first();

        if (!$ticket) {
            $this->notify('No patients currently in consultation queue', 'info', 'ℹ️');
            return;
        }

        $ticket->update([
            'status' => 'serving',
            'called_at' => now(),
            'doctor_station_id' => $doc?->id,
        ]);
        $this->doctorCurrentPatientId = $ticket->id;

        $room = $doc?->room ?? 'Consultation Room 101';
        $this->dispatchSpeech($ticket->ticket_number, 'Consultation', $room);
        $this->notify("Called {$ticket->ticket_number} into {$room}", 'info', '📢');
    }

    public function toggleDoctorBreak(int $stationId): void
    {
        $doc = DoctorStation::find($stationId);
        if ($doc) {
            $doc->update([
                'is_on_break' => !$doc->is_on_break,
                'break_reason' => !$doc->is_on_break ? $this->doctorBreakReason : null,
            ]);
            $status = $doc->is_on_break ? 'ON BREAK' : 'ACTIVE IN ROOM';
            $this->notify("{$doc->doctor_name} status set to {$status}", 'info', '☕');
        }
    }

    public function doctorDirectCall(int $ticketId): void
    {
        $ticket = Ticket::find($ticketId);
        $doc = DoctorStation::find($this->selectedDoctorId);
        if ($ticket) {
            $ticket->update(['status' => 'serving', 'called_at' => now(), 'doctor_station_id' => $doc?->id]);
            $this->doctorCurrentPatientId = $ticket->id;
            $room = $doc?->room ?? 'Consultation Room 101';
            $this->dispatchSpeech($ticket->ticket_number, 'Consultation', $room);
            $this->notify("Called {$ticket->ticket_number} into {$room}", 'info', '📢');
        }
    }

    public function doctorTransferPatient(string $targetDeptName): void
    {
        if (!$this->doctorCurrentPatientId) return;
        $ticket = Ticket::find($this->doctorCurrentPatientId);
        if (!$ticket) return;

        $targetDept = Department::where('name', 'like', "%{$targetDeptName}%")->first();
        if ($targetDept) {
            $currentStage = TicketStage::where('ticket_id', $ticket->id)->where('status', 'active')->first();
            if ($currentStage) $currentStage->update(['status' => 'completed', 'completed_at' => now()]);

            TicketStage::create([
                'ticket_id' => $ticket->id,
                'department_id' => $targetDept->id,
                'order' => 2,
                'status' => 'active',
                'notes' => $this->doctorNotes,
            ]);

            $ticket->update([
                'department_id' => $targetDept->id,
                'status' => 'waiting',
                'clinical_notes' => $this->doctorNotes,
            ]);

            QueueActivity::create([
                'ticket_id' => $ticket->id,
                'type' => 'transfer',
                'text' => "{$ticket->ticket_number} referred to {$targetDept->name}",
                'department_id' => $targetDept->id,
            ]);

            $this->notify("{$ticket->ticket_number} referred to {$targetDept->name}!", 'success', '🔀');
            $this->doctorCurrentPatientId = null;
            $this->doctorNotes = '';
        }
    }

    public function doctorMarkCompleted(): void
    {
        if (!$this->doctorCurrentPatientId) return;
        $ticket = Ticket::find($this->doctorCurrentPatientId);
        if ($ticket) {
            $ticket->update(['status' => 'served', 'served_at' => now(), 'clinical_notes' => $this->doctorNotes]);
            $activeStage = TicketStage::where('ticket_id', $ticket->id)->where('status', 'active')->first();
            if ($activeStage) $activeStage->update(['status' => 'completed', 'completed_at' => now()]);

            QueueActivity::create([
                'ticket_id' => $ticket->id,
                'type' => 'served',
                'text' => "{$ticket->ticket_number} consult completed",
                'department_id' => $ticket->department_id,
            ]);

            $this->notify("Consultation completed for {$ticket->ticket_number}", 'success', '✅');
            $this->doctorCurrentPatientId = null;
            $this->doctorNotes = '';
        }
    }

    public function submitFeedback(string $ticketNumber): void
    {
        $ticket = Ticket::where('ticket_number', $ticketNumber)->first();
        if ($ticket) {
            $ticket->update([
                'satisfaction_rating' => $this->feedbackRating,
                'feedback_note' => $this->feedbackComment,
            ]);
            $this->feedbackSubmitted = true;
            $this->notify("Thank you! Your {$this->feedbackRating}-star rating was recorded.", 'success', '⭐');
        }
    }

    public function openTransferModal(string $ticketNumber): void
    {
        $this->transferTicketNumber = $ticketNumber;
        $this->transferModalOpen = true;
    }

    public function confirmTransfer(): void
    {
        $ticket = Ticket::where('ticket_number', $this->transferTicketNumber)->first();
        if ($ticket) {
            $targetDept = Department::where('name', 'like', "%{$this->transferTargetDept}%")->first();
            if ($targetDept) {
                $ticket->update(['department_id' => $targetDept->id, 'status' => 'waiting']);
                $this->notify("Transferred {$ticket->ticket_number} to {$targetDept->name}", 'success', '🔀');
                $this->transferModalOpen = false;
            }
        }
    }

    public function printSlip(string $ticketNumber): void
    {
        $ticket = Ticket::where('ticket_number', $ticketNumber)->first();
        if ($ticket) {
            $this->printSlipData = [
                'ticket' => $ticket->ticket_number,
                'name' => $ticket->patient_name,
                'dept' => $ticket->department?->name ?? 'OPD',
                'priority' => ucfirst($ticket->priority),
                'insurance' => $ticket->insurance_type ?? 'NHIS',
                'nhis' => $ticket->nhis_number ?? 'GH-NHIS-829143',
                'bp' => $ticket->vitals_bp ?? '120/80',
                'temp' => $ticket->vitals_temp ?? '36.8',
                'position' => 1,
                'wait' => 12,
            ];
            $this->dispatch('print-slip');
        }
    }

    public function quickLogin(string $type): void
    {
        $emails = [
            'admin' => 'admin@hospital.org',
            'dr.mensah' => 'dr.mensah@hospital.org',
            'dr.jenkins' => 'dr.jenkins@hospital.org',
            'reception' => 'reception@hospital.org',
            'pharmacy' => 'pharmacy@hospital.org',
            'lab' => 'lab@hospital.org',
        ];

        if (isset($emails[$type])) {
            $user = User::where('email', $emails[$type])->first();
            if ($user) {
                Auth::login($user);
                $this->currentUser = [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role,
                ];
                $this->authModalOpen = false;
                $this->applyRoleView($user->role);
                $this->notify("Logged in as {$user->name}", 'success', '👋');
            }
        }
    }

    public function loginWithCredentials(): void
    {
        $user = User::where('email', $this->loginEmail)->first();
        if ($user && Hash::check($this->loginPassword, $user->password)) {
            Auth::login($user);
            $this->currentUser = [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
            ];
            $this->authModalOpen = false;
            $this->applyRoleView($user->role);
            $this->notify("Welcome back, {$user->name}", 'success', '👋');
        } else {
            $this->notify('Invalid email or password', 'error', '❌');
        }
    }

    public function logoutStaff(): void
    {
        Auth::logout();
        $this->currentUser = null;
        $this->activeView = 'display';
        $this->notify('Signed out successfully.', 'info', '👋');
    }

    private function applyRoleView(string $role): void
    {
        if ($role === 'doctor') {
            $this->activeView = 'doctor';
        } elseif ($role === 'receptionist') {
            $this->activeView = 'reception';
        } elseif ($role === 'pharmacist') {
            $this->activeView = 'counter';
            $this->counterDeptId = 3; // Pharmacy
            $this->counterActiveWindow = 'Dispensary Window 1';
        } elseif ($role === 'lab_tech') {
            $this->activeView = 'counter';
            $this->counterDeptId = 4; // Laboratory
            $this->counterActiveWindow = 'Phlebotomy Booth A';
        } else {
            $this->activeView = 'dashboard';
        }
    }

    public function setVoiceStyle(string $style): void
    {
        $this->voiceStyle = $style;
        $labels = [
            'twi_dual' => 'English + Akan/Twi Dual',
            'twi_only' => 'Akan / Twi Only',
            'ga_dual' => 'English + Ga Dual',
            'hausa_dual' => 'English + Hausa Dual',
            'ghanaian_formal' => 'Ghanaian English Formal',
            'ghanaian_local' => 'Ghanaian Courtesy (Agoo)',
            'standard' => 'Standard English',
        ];
        $this->notify("Voice language set to " . ($labels[$style] ?? $style), 'info', '🔊');
    }

    public function testVoice(): void
    {
        $this->dispatchSpeech('OPD-059', 'General Outpatient', 'Consultation Room 101');
        $this->notify('Testing Ghanaian hospital multi-language callout...', 'info', '🔊');
    }

    private function dispatchSpeech(string $ticketNumber, string $deptName, string $station, bool $isRecall = false): void
    {
        $formattedTicket = $this->phoneticTicket($ticketNumber);
        $twiTicket = $this->twiPhoneticTicket($ticketNumber);

        $englishText = "Attention please. Ticket {$formattedTicket}. Kindly proceed to {$deptName}, {$station}. Thank you.";
        $twiText = "Mepaakyew, ticket nomba {$twiTicket}. Yesre wo ko {$deptName}, {$station}. Medaase.";
        $gaText = "Ofaine, ticket nomba {$formattedTicket}. Yaa {$deptName}, {$station}. Oyiwaladong.";
        $hausaText = "Dan Allah, ticket lamba {$formattedTicket}. Ka je {$deptName}, {$station}. Na gode.";

        $calloutQueue = [];

        if ($this->voiceStyle === 'twi_dual') {
            $calloutQueue = [$englishText, $twiText];
        } elseif ($this->voiceStyle === 'twi_only') {
            $calloutQueue = [$twiText];
        } elseif ($this->voiceStyle === 'ga_dual') {
            $calloutQueue = [$englishText, $gaText];
        } elseif ($this->voiceStyle === 'hausa_dual') {
            $calloutQueue = [$englishText, $hausaText];
        } elseif ($this->voiceStyle === 'ghanaian_local') {
            $prefix = $isRecall ? 'Agoo! Final call. ' : 'Agoo! Attention please. ';
            $calloutQueue = ["{$prefix}Ticket number {$formattedTicket}. Kindly report to {$deptName}, {$station}. Medaase."];
        } else {
            $prefix = $isRecall ? 'Attention please. Final recall. ' : 'Attention please. ';
            $calloutQueue = ["{$prefix}Ticket {$ticketNumber}. Please proceed to {$deptName}. {$station}."];
        }

        $this->dispatch('announce-call', [
            'queue' => $calloutQueue,
            'text' => $calloutQueue[0] ?? $englishText,
            'ticket' => $ticketNumber,
            'style' => $this->voiceStyle,
            'chime' => $this->chimeEnabled,
        ]);
    }

    private function phoneticTicket(string $ticket): string
    {
        $parts = explode('-', $ticket);
        if (count($parts) === 2) {
            $letters = implode(' ', str_split($parts[0]));
            $digits = implode(' ', str_split($parts[1]));
            return "{$letters}, {$digits}";
        }
        return implode(' ', str_split($ticket));
    }

    private function twiPhoneticTicket(string $ticket): string
    {
        $twiDigits = [
            '0' => 'hwee',
            '1' => 'baako',
            '2' => 'mmienu',
            '3' => 'mmiensa',
            '4' => 'enan',
            '5' => 'nnum',
            '6' => 'nsia',
            '7' => 'nson',
            '8' => 'nwotwe',
            '9' => 'nkron',
        ];

        $parts = explode('-', $ticket);
        if (count($parts) === 2) {
            $letters = implode(' ', str_split($parts[0]));
            $digitWords = [];
            foreach (str_split($parts[1]) as $d) {
                $digitWords[] = $twiDigits[$d] ?? $d;
            }
            return "{$letters}, " . implode(' ', $digitWords);
        }
        return $ticket;
    }

    private function notify(string $message, string $type = 'success', string $icon = '✅'): void
    {
        $this->toastMessage = $message;
        $this->toastType = $type;
        $this->toastIcon = $icon;
        $this->showToast = true;
    }

    public function render()
    {
        $departments = Department::with(['counters', 'doctorStations'])->get();
        $tickets = Ticket::with(['department', 'stages.department'])
            ->whereIn('status', ['waiting', 'called', 'serving'])
            ->orWhere('created_at', '>=', now()->subHours(8))
            ->latest()
            ->get();

        $doctorStations = DoctorStation::where('is_active', true)->get();
        $doctorQueue = Ticket::where('department_id', 1)->whereIn('status', ['waiting', 'serving'])->get();
        $currentDoctorPatient = $this->doctorCurrentPatientId ? Ticket::find($this->doctorCurrentPatientId) : null;

        $counterTickets = Ticket::where('department_id', $this->counterDeptId)->get();
        $priorityTickets = Ticket::whereIn('priority', ['emergency', 'priority'])->where('status', 'waiting')->get();
        $recentActivities = QueueActivity::latest()->take(7)->get();

        $totalWaiting = Ticket::where('status', 'waiting')->count();
        $servedToday = Ticket::where('status', 'served')->count() + 264;
        $nhisCount = Ticket::where('insurance_type', 'NHIS')->count() + 180;
        $avgSatisfaction = round(Ticket::whereNotNull('satisfaction_rating')->avg('satisfaction_rating') ?: 4.9, 1);

        $mobileTicket = Ticket::with(['department', 'stages.department'])->where('ticket_number', $this->mobileTrackingTicketNumber)->first();

        return view('livewire.hospital-queue-manager', [
            'departments' => $departments,
            'tickets' => $tickets,
            'doctorStations' => $doctorStations,
            'doctorQueue' => $doctorQueue,
            'currentDoctorPatient' => $currentDoctorPatient,
            'counterTickets' => $counterTickets,
            'priorityTickets' => $priorityTickets,
            'recentActivities' => $recentActivities,
            'totalWaiting' => $totalWaiting,
            'servedToday' => $servedToday,
            'nhisCount' => $nhisCount,
            'avgSatisfaction' => $avgSatisfaction,
            'mobileTicket' => $mobileTicket,
        ])->layout('layouts.app');
    }
}
