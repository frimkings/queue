<div wire:poll.3s="refreshState" class="min-h-screen flex flex-col bg-slate-900 text-slate-100 font-sans selection:bg-blue-600 selection:text-white">
    <!-- Top Global App Bar -->
    <header class="bg-slate-950/90 backdrop-blur-md border-b border-slate-800/80 sticky top-0 z-40 px-3 lg:px-6 py-2.5 flex items-center justify-between">
        <div class="flex items-center space-x-3">
            <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-blue-600 to-cyan-500 flex items-center justify-center shadow-lg shadow-blue-500/20 text-white font-black text-xl">
                +
            </div>
            <div>
                <div class="flex items-center space-x-2">
                    <span class="font-extrabold text-base tracking-tight text-white">MediQueue<span class="text-blue-400">Pro</span></span>
                    <span class="px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider bg-blue-500/20 text-blue-300 border border-blue-500/30 rounded-full">Ghana Edition</span>
                    <span class="hidden sm:inline-flex items-center space-x-1 px-2 py-0.5 text-[10px] font-medium bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 rounded-full">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-ping"></span>
                        <span>DB Live Sync</span>
                    </span>
                </div>
                <div class="text-[11px] text-slate-400 flex items-center space-x-2">
                    <span>Church of Christ Mission Hospital</span>
                    <span class="text-slate-600">|</span>
                    <span class="text-slate-400 font-mono-num">{{ $currentDateTime }}</span>
                </div>
            </div>
        </div>

        <!-- Right Quick Actions & Staff Pill -->
        <div class="flex items-center space-x-2 sm:space-x-3">
            <!-- Ghanaian Multi-Language Voice Selector (Visible on all screens) -->
            <div class="flex items-center space-x-1.5 bg-slate-900/90 px-2.5 py-1 rounded-xl border border-slate-700/80 text-xs">
                <span class="text-sm">🇬🇭</span>
                <select wire:model.live="voiceStyle" class="bg-transparent text-slate-200 text-xs font-semibold focus:outline-none cursor-pointer max-w-[140px] sm:max-w-none">
                    <option value="twi_dual" class="bg-slate-900 text-white">English + Akan/Twi Dual</option>
                    <option value="twi_only" class="bg-slate-900 text-white">Akan / Twi Only ("Mepaakyɛw...")</option>
                    <option value="ga_dual" class="bg-slate-900 text-white">English + Ga Dual ("Ofainɛ...")</option>
                    <option value="hausa_dual" class="bg-slate-900 text-white">English + Hausa Dual</option>
                    <option value="ghanaian_formal" class="bg-slate-900 text-white">Ghanaian Formal ("Kindly proceed...")</option>
                    <option value="ghanaian_local" class="bg-slate-900 text-white">Ghanaian Courtesy (Agoo / Medaase)</option>
                    <option value="standard" class="bg-slate-900 text-white">Standard English</option>
                </select>
                <button wire:click="testVoice" title="Play Voice Announcement & Chime" class="px-2.5 py-1 bg-blue-600 hover:bg-blue-500 text-white rounded-lg font-bold text-xs transition flex items-center space-x-1 shadow-md shadow-blue-600/30">
                    <span>🔊</span>
                    <span>Test Voice</span>
                </button>
            </div>

            <!-- Daily Shift Summary Report Button -->
            <button wire:click="$set('dailyReportModalOpen', true)" class="hidden sm:flex items-center space-x-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-200 border border-slate-700 transition">
                <span>📑</span>
                <span>Daily Shift Report</span>
            </button>

            <!-- Fullscreen TV Mode Direct Switch -->
            <button wire:click="navigateView('display')" 
                class="hidden md:flex items-center space-x-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-200 border border-slate-700 transition">
                <span>📺</span>
                <span>TV Board</span>
            </button>

            <!-- Kiosk Mode Direct Switch -->
            <button wire:click="navigateView('kiosk')" 
                class="hidden md:flex items-center space-x-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-200 border border-slate-700 transition">
                <span>📱</span>
                <span>Kiosk</span>
            </button>

            <!-- Current Staff Profile Button -->
            <div class="relative">
                @if($currentUser)
                    <button wire:click="$set('authModalOpen', true)" class="flex items-center space-x-2.5 bg-slate-800/80 hover:bg-slate-800 px-3 py-1.5 rounded-xl border border-slate-700/80 transition">
                        <div class="w-7 h-7 rounded-lg bg-blue-600/30 border border-blue-500/40 flex items-center justify-center text-xs font-bold text-blue-300">
                            {{ substr($currentUser['name'], 0, 2) }}
                        </div>
                        <div class="text-left hidden sm:block">
                            <div class="text-xs font-semibold text-slate-200 leading-none">{{ $currentUser['name'] }}</div>
                            <div class="text-[10px] uppercase font-bold text-blue-400 tracking-wider mt-0.5">{{ $currentUser['role'] }}</div>
                        </div>
                        <span class="text-slate-400 text-xs">▼</span>
                    </button>
                @else
                    <button wire:click="$set('authModalOpen', true)" class="flex items-center space-x-2 bg-blue-600 hover:bg-blue-500 text-white px-3.5 py-1.5 rounded-xl font-semibold text-xs shadow-md transition">
                        <span>🔐</span>
                        <span>Staff Login</span>
                    </button>
                @endif
            </div>
        </div>
    </header>

    <!-- Main App Body -->
    <div class="flex-1 flex flex-col md:flex-row overflow-hidden">
        <!-- Sidebar Navigation (Hidden in Display and Kiosk modes for full screen) -->
        @if($activeView !== 'display' && $activeView !== 'kiosk')
            <aside class="w-full md:w-64 bg-slate-950 border-r border-slate-800/80 p-3 flex flex-col justify-between shrink-0">
                <div class="space-y-1">
                    <div class="px-3 py-2 text-[10px] font-bold uppercase tracking-wider text-slate-500">Navigation & Stations</div>

                    <button wire:click="navigateView('dashboard')" class="w-full flex items-center space-x-3 px-3 py-2 rounded-xl text-xs font-semibold transition {{ $activeView === 'dashboard' ? 'bg-blue-600 text-white shadow-md shadow-blue-600/20' : 'text-slate-400 hover:bg-slate-900 hover:text-slate-200' }}">
                        <span class="text-base">📊</span>
                        <span>Admin Dashboard</span>
                    </button>

                    <button wire:click="navigateView('reception')" class="w-full flex items-center space-x-3 px-3 py-2 rounded-xl text-xs font-semibold transition {{ $activeView === 'reception' ? 'bg-blue-600 text-white shadow-md shadow-blue-600/20' : 'text-slate-400 hover:bg-slate-900 hover:text-slate-200' }}">
                        <span class="text-base">🎫</span>
                        <span>Reception, NHIS & Triage</span>
                    </button>

                    <button wire:click="navigateView('doctor')" class="w-full flex items-center space-x-3 px-3 py-2 rounded-xl text-xs font-semibold transition {{ $activeView === 'doctor' ? 'bg-blue-600 text-white shadow-md shadow-blue-600/20' : 'text-slate-400 hover:bg-slate-900 hover:text-slate-200' }}">
                        <span class="text-base">🩺</span>
                        <span>Doctor Consultation</span>
                    </button>

                    <button wire:click="navigateView('counter')" class="w-full flex items-center space-x-3 px-3 py-2 rounded-xl text-xs font-semibold transition {{ $activeView === 'counter' ? 'bg-blue-600 text-white shadow-md shadow-blue-600/20' : 'text-slate-400 hover:bg-slate-900 hover:text-slate-200' }}">
                        <span class="text-base">🏢</span>
                        <span>Counter Terminal</span>
                    </button>

                    <button wire:click="navigateView('mobile')" class="w-full flex items-center space-x-3 px-3 py-2 rounded-xl text-xs font-semibold transition {{ $activeView === 'mobile' ? 'bg-blue-600 text-white shadow-md shadow-blue-600/20' : 'text-slate-400 hover:bg-slate-900 hover:text-slate-200' }}">
                        <span class="text-base">📲</span>
                        <span>Mobile Tracker & CSAT</span>
                    </button>

                    <button wire:click="navigateView('analytics')" class="w-full flex items-center space-x-3 px-3 py-2 rounded-xl text-xs font-semibold transition {{ $activeView === 'analytics' ? 'bg-blue-600 text-white shadow-md shadow-blue-600/20' : 'text-slate-400 hover:bg-slate-900 hover:text-slate-200' }}">
                        <span class="text-base">📈</span>
                        <span>Analytics & Audit</span>
                    </button>

                    <div class="pt-4 px-3 py-2 text-[10px] font-bold uppercase tracking-wider text-slate-500">Public Screens</div>

                    <button wire:click="navigateView('display')" class="w-full flex items-center space-x-3 px-3 py-2 rounded-xl text-xs font-semibold text-slate-400 hover:bg-slate-900 hover:text-slate-200 transition">
                        <span class="text-base">📺</span>
                        <span>TV Waiting Board</span>
                    </button>

                    <button wire:click="navigateView('kiosk')" class="w-full flex items-center space-x-3 px-3 py-2 rounded-xl text-xs font-semibold text-slate-400 hover:bg-slate-900 hover:text-slate-200 transition">
                        <span class="text-base">📟</span>
                        <span>Patient Self-Kiosk</span>
                    </button>
                </div>

                <!-- Footer System Status & Quick Voice Test -->
                <div class="p-3 bg-slate-900/60 rounded-xl border border-slate-800/80 text-[11px] text-slate-400 space-y-2 mt-4">
                    <div class="flex items-center justify-between">
                        <span class="font-medium text-slate-300">NHIS Gateway</span>
                        <span class="text-emerald-400 font-bold">ONLINE</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span>Audio PA System</span>
                        <span class="text-blue-400 font-bold">Twi / Eng Ready</span>
                    </div>
                    <button wire:click="testVoice" onclick="window.testVoiceAnnouncement('{{ $voiceStyle }}')" class="w-full mt-2 py-1.5 bg-blue-600/20 hover:bg-blue-600 text-blue-300 hover:text-white rounded-lg font-bold text-[11px] transition flex items-center justify-center space-x-1.5 border border-blue-500/30">
                        <span>🔊</span>
                        <span>Test Twi Audio Announcement</span>
                    </button>
                </div>
            </aside>
        @endif

        <!-- Main Content Area -->
        <main class="flex-1 bg-slate-900/50 p-4 md:p-6 overflow-y-auto min-h-screen">
            
            {{-- VIEW 1: DASHBOARD --}}
            @if($activeView === 'dashboard')
                <div class="space-y-6 max-w-7xl mx-auto">
                    <!-- Top Stat Cards -->
                    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                        <div class="bg-slate-950 p-4 rounded-2xl border border-slate-800 shadow-sm relative overflow-hidden">
                            <div class="absolute -right-2 -bottom-2 text-5xl opacity-5">⏳</div>
                            <div class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Total Waiting</div>
                            <div class="text-3xl font-extrabold text-blue-400 font-mono-num mt-1">{{ $totalWaiting }}</div>
                            <div class="text-[11px] text-slate-500 mt-1">Across all clinics & counters</div>
                        </div>

                        <div class="bg-slate-950 p-4 rounded-2xl border border-slate-800 shadow-sm relative overflow-hidden">
                            <div class="absolute -right-2 -bottom-2 text-5xl opacity-5">✅</div>
                            <div class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Served Today</div>
                            <div class="text-3xl font-extrabold text-emerald-400 font-mono-num mt-1">{{ $servedToday }}</div>
                            <div class="text-[11px] text-emerald-400 mt-1">99.4% resolution rate</div>
                        </div>

                        <div class="bg-slate-950 p-4 rounded-2xl border border-slate-800 shadow-sm relative overflow-hidden">
                            <div class="absolute -right-2 -bottom-2 text-5xl opacity-5">🇬🇭</div>
                            <div class="text-xs font-semibold text-slate-400 uppercase tracking-wider">NHIS / Insured</div>
                            <div class="text-3xl font-extrabold text-amber-400 font-mono-num mt-1">{{ $nhisCount }}</div>
                            <div class="text-[11px] text-slate-400 mt-1">74.2% Insurance ratio</div>
                        </div>

                        <div class="bg-slate-950 p-4 rounded-2xl border border-slate-800 shadow-sm relative overflow-hidden">
                            <div class="absolute -right-2 -bottom-2 text-5xl opacity-5">⭐</div>
                            <div class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Patient CSAT Rating</div>
                            <div class="text-3xl font-extrabold text-purple-400 font-mono-num mt-1">{{ $avgSatisfaction }} <span class="text-sm font-normal text-slate-400">/ 5.0</span></div>
                            <div class="text-[11px] text-emerald-400 mt-1">⭐⭐⭐⭐⭐ Excellent</div>
                        </div>
                    </div>

                    <!-- Department Queues Grid -->
                    <div>
                        <div class="flex items-center justify-between mb-3">
                            <h2 class="text-sm font-bold uppercase tracking-wider text-slate-400">Department Queue Status</h2>
                            <div class="flex items-center space-x-2">
                                <button wire:click="$set('dailyReportModalOpen', true)" class="text-xs font-semibold text-slate-300 hover:text-white bg-slate-800 px-2.5 py-1 rounded-lg border border-slate-700">
                                    📑 Shift Report
                                </button>
                                <button wire:click="navigateView('reception')" class="text-xs font-semibold text-blue-400 hover:text-blue-300 flex items-center space-x-1">
                                    <span>+ New Patient Intake</span>
                                </button>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach($departments as $dept)
                                @php
                                    $deptWaiting = $dept->tickets->where('status', 'waiting')->count();
                                    $deptServing = $dept->tickets->whereIn('status', ['called', 'serving'])->first();
                                @endphp
                                <div class="bg-slate-950 p-4 rounded-2xl border border-slate-800/80 hover:border-slate-700 transition">
                                    <div class="flex items-start justify-between">
                                        <div>
                                            <span class="px-2 py-0.5 text-[10px] font-bold rounded-md uppercase" style="background: {{ $dept->color }}20; color: {{ $dept->color }}; border: 1px solid {{ $dept->color }}40;">
                                                {{ $dept->prefix }}
                                            </span>
                                            <h3 class="text-sm font-bold text-slate-200 mt-1.5">{{ $dept->name }}</h3>
                                            <p class="text-[11px] text-slate-400">{{ $dept->location }} · Avg {{ $dept->avg_service_time }}m</p>
                                        </div>
                                        <div class="text-right">
                                            <div class="text-xl font-extrabold font-mono-num text-slate-200">{{ $deptWaiting }}</div>
                                            <div class="text-[10px] text-slate-500 uppercase font-semibold">Waiting</div>
                                        </div>
                                    </div>

                                    <div class="mt-4 pt-3 border-t border-slate-800/60 flex items-center justify-between">
                                        <div>
                                            <div class="text-[10px] text-slate-500 uppercase font-semibold">Now Serving</div>
                                            <div class="text-xs font-bold text-emerald-400 font-mono-num">
                                                {{ $deptServing ? $deptServing->ticket_number : '— Idle —' }}
                                            </div>
                                        </div>
                                        <button wire:click="callSpecific('{{ $deptServing ? $deptServing->ticket_number : '' }}')" 
                                            class="px-3 py-1 text-xs font-semibold rounded-lg bg-blue-600/20 hover:bg-blue-600/30 text-blue-300 border border-blue-500/30 transition">
                                            Manage
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Dedicated Voice & Language Announcements Panel -->
                    <div class="bg-gradient-to-r from-slate-950 via-slate-900 to-slate-950 p-5 rounded-3xl border border-blue-500/30 shadow-xl shadow-blue-500/5 space-y-3">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 rounded-2xl bg-gradient-to-br from-blue-600 to-cyan-500 flex items-center justify-center text-xl shadow-lg shadow-blue-500/20">
                                    🇬🇭
                                </div>
                                <div>
                                    <h3 class="text-sm font-extrabold text-white flex items-center space-x-2">
                                        <span>Hospital Voice & Multi-Language PA System</span>
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">ACTIVE</span>
                                    </h3>
                                    <p class="text-xs text-slate-400">Automatic sequential 2-tone hospital chime + English & Ghanaian local language callouts.</p>
                                </div>
                            </div>

                            <button wire:click="testVoice" onclick="window.testVoiceAnnouncement('{{ $voiceStyle }}')" class="px-4 py-2 bg-gradient-to-r from-blue-600 to-cyan-600 hover:from-blue-500 hover:to-cyan-500 text-white rounded-xl text-xs font-bold shadow-lg shadow-blue-600/30 transition flex items-center justify-center space-x-2 shrink-0">
                                <span class="text-base">🔊</span>
                                <span>Play Test Announcement</span>
                            </button>
                        </div>

                        <!-- Language Option Pills -->
                        <div class="flex flex-wrap items-center gap-2 pt-1 border-t border-slate-800/80">
                            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mr-1">Select Language Mode:</span>
                            <button wire:click="setVoiceStyle('twi_dual')" class="px-3 py-1.5 rounded-xl text-xs font-bold transition flex items-center space-x-1.5 {{ $voiceStyle === 'twi_dual' ? 'bg-blue-600 text-white shadow-md' : 'bg-slate-900 text-slate-300 hover:bg-slate-800 border border-slate-700/80' }}">
                                <span>🇬🇭</span>
                                <span>English + Akan/Twi Dual (Recommended)</span>
                            </button>
                            <button wire:click="setVoiceStyle('twi_only')" class="px-3 py-1.5 rounded-xl text-xs font-bold transition flex items-center space-x-1.5 {{ $voiceStyle === 'twi_only' ? 'bg-blue-600 text-white shadow-md' : 'bg-slate-900 text-slate-300 hover:bg-slate-800 border border-slate-700/80' }}">
                                <span>🇬🇭</span>
                                <span>Akan / Twi Only ("Mepaakyɛw...")</span>
                            </button>
                            <button wire:click="setVoiceStyle('ga_dual')" class="px-3 py-1.5 rounded-xl text-xs font-bold transition flex items-center space-x-1.5 {{ $voiceStyle === 'ga_dual' ? 'bg-blue-600 text-white shadow-md' : 'bg-slate-900 text-slate-300 hover:bg-slate-800 border border-slate-700/80' }}">
                                <span>🇬🇭</span>
                                <span>English + Ga Dual ("Ofainɛ...")</span>
                            </button>
                            <button wire:click="setVoiceStyle('hausa_dual')" class="px-3 py-1.5 rounded-xl text-xs font-bold transition flex items-center space-x-1.5 {{ $voiceStyle === 'hausa_dual' ? 'bg-blue-600 text-white shadow-md' : 'bg-slate-900 text-slate-300 hover:bg-slate-800 border border-slate-700/80' }}">
                                <span>🇬🇭</span>
                                <span>English + Hausa Dual</span>
                            </button>
                            <button wire:click="setVoiceStyle('ghanaian_local')" class="px-3 py-1.5 rounded-xl text-xs font-bold transition flex items-center space-x-1.5 {{ $voiceStyle === 'ghanaian_local' ? 'bg-blue-600 text-white shadow-md' : 'bg-slate-900 text-slate-300 hover:bg-slate-800 border border-slate-700/80' }}">
                                <span>🇬🇭</span>
                                <span>Courtesy (Agoo / Medaase)</span>
                            </button>
                        </div>

                        <!-- Current Speech Phrasing Live Script Preview -->
                        <div class="p-3 rounded-xl bg-slate-950/80 border border-slate-800/80 text-xs font-mono space-y-1">
                            <div class="text-[10px] text-slate-500 uppercase font-bold">Currently Configured Audio Speech Phrasing:</div>
                            @if($voiceStyle === 'twi_dual')
                                <div class="text-blue-300">1. "Attention please. Ticket O, P, D, zero, five, nine. Kindly proceed to Consultation Room 101. Thank you."</div>
                                <div class="text-emerald-400">2. "Mepaakyɛw, ticket nɔmba O, P, D, hwee, nnum, nkron. Yɛsrɛ wo kɔ Consultation Room 101. Medaase."</div>
                            @elseif($voiceStyle === 'twi_only')
                                <div class="text-emerald-400">"Mepaakyɛw, ticket nɔmba O, P, D, hwee, nnum, nkron. Yɛsrɛ wo kɔ Consultation Room 101. Medaase."</div>
                            @elseif($voiceStyle === 'ga_dual')
                                <div class="text-blue-300">1. "Attention please. Ticket O, P, D, zero, five, nine. Kindly proceed to Consultation Room 101. Thank you."</div>
                                <div class="text-emerald-400">2. "Ofainɛ, ticket nɔmba O, P, D, 0, 5, 9. Yaa Consultation Room 101. Oyiwaladɔŋŋ."</div>
                            @elseif($voiceStyle === 'hausa_dual')
                                <div class="text-blue-300">1. "Attention please. Ticket O, P, D, zero, five, nine. Kindly proceed to Consultation Room 101. Thank you."</div>
                                <div class="text-emerald-400">2. "Dan Allah, ticket lamba O, P, D, 0, 5, 9. Ka je Consultation Room 101. Na gode."</div>
                            @elseif($voiceStyle === 'ghanaian_local')
                                <div class="text-emerald-400">"Agoo! Attention please. Ticket number O, P, D, zero, five, nine. Kindly report to Consultation Room 101. Medaase."</div>
                            @else
                                <div class="text-blue-300">"Attention please. Ticket OPD-059. Please proceed to Consultation Room 101."</div>
                            @endif
                        </div>
                    </div>

                    <!-- Lower Section: Emergency Cases & Live Activity Stream -->
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                        <!-- Priority Patients Alert -->
                        <div class="lg:col-span-1 bg-slate-950 p-4 rounded-2xl border border-red-900/40 bg-gradient-to-b from-red-950/20 to-transparent">
                            <div class="flex items-center space-x-2 text-red-400 mb-3">
                                <span class="text-lg animate-bounce">🚨</span>
                                <h3 class="text-xs font-bold uppercase tracking-wider">Priority / Emergency Intake</h3>
                            </div>
                            <div class="space-y-2">
                                @forelse($priorityTickets as $pt)
                                    <div class="p-2.5 rounded-xl bg-slate-900 border border-red-500/30 flex items-center justify-between">
                                        <div>
                                            <div class="flex items-center space-x-2">
                                                <span class="font-mono-num font-bold text-red-300 text-xs">{{ $pt->ticket_number }}</span>
                                                <span class="text-[10px] px-1.5 py-0.5 rounded bg-red-500/20 text-red-300 font-bold uppercase">{{ $pt->priority }}</span>
                                                <span class="text-[10px] px-1.5 py-0.5 rounded bg-blue-500/20 text-blue-300 font-bold">{{ $pt->insurance_type }}</span>
                                            </div>
                                            <div class="text-xs font-medium text-slate-300 mt-0.5">{{ $pt->patient_name }}</div>
                                        </div>
                                        <button wire:click="callSpecific('{{ $pt->ticket_number }}')" class="px-2.5 py-1 text-xs font-bold rounded-lg bg-red-500 hover:bg-red-400 text-white transition">
                                            Fast-Call
                                        </button>
                                    </div>
                                @empty
                                    <div class="text-xs text-slate-500 text-center py-6">No emergency cases waiting.</div>
                                @endforelse
                            </div>
                        </div>

                        <!-- Live Activity Stream -->
                        <div class="lg:col-span-2 bg-slate-950 p-4 rounded-2xl border border-slate-800">
                            <div class="flex items-center justify-between mb-3">
                                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400">Live Hospital Queue Events</h3>
                                <span class="text-[10px] text-emerald-400 font-mono-num flex items-center space-x-1">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-ping"></span>
                                    <span>Realtime Stream</span>
                                </span>
                            </div>
                            <div class="space-y-2">
                                @foreach($recentActivities as $act)
                                    <div class="p-2.5 rounded-xl bg-slate-900/60 border border-slate-800/60 flex items-center justify-between text-xs">
                                        <div class="flex items-center space-x-3">
                                            <span class="w-7 h-7 rounded-lg bg-slate-800 flex items-center justify-center text-sm">
                                                @if($act->type === 'issued') 🎫
                                                @elseif($act->type === 'called') 📢
                                                @elseif($act->type === 'served') ✅
                                                @elseif($act->type === 'transfer') 🔀
                                                @else ℹ️ @endif
                                            </span>
                                            <div>
                                                <div class="text-slate-200 font-medium">{{ $act->text }}</div>
                                                <div class="text-[10px] text-slate-500 font-mono-num">{{ $act->created_at->diffForHumans() }}</div>
                                            </div>
                                        </div>
                                        <span class="text-[10px] font-bold uppercase px-2 py-0.5 rounded bg-slate-800 text-slate-400">
                                            {{ $act->type }}
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            {{-- VIEW 2: RECEPTION, NHIS & TRIAGE INTAKE --}}
            @if($activeView === 'reception')
                <div class="space-y-6 max-w-6xl mx-auto">
                    <div>
                        <h2 class="text-lg font-bold text-white">Reception, NHIS & Triage Intake</h2>
                        <p class="text-xs text-slate-400">Capture patient demographics, NHIS/Insurance classification, clinical vitals (BP/Temp/Weight), and print 80mm slip with QR code.</p>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                        <!-- Patient Registration & Triage Form -->
                        <div class="lg:col-span-2 bg-slate-950 p-6 rounded-2xl border border-slate-800 space-y-4">
                            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400">Patient Intake & Vitals</h3>

                            <!-- Personal Details -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs font-semibold text-slate-300 mb-1">Patient Full Name *</label>
                                    <input type="text" wire:model="receptionName" placeholder="e.g. Kwesi Manu" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-blue-500" />
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-300 mb-1">Mobile Phone (for SMS updates)</label>
                                    <input type="text" wire:model="receptionPhone" placeholder="+233 24 123 4567" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-blue-500" />
                                </div>
                            </div>

                            <!-- Insurance & Priority -->
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                                <div>
                                    <label class="block text-xs font-semibold text-slate-300 mb-1">Primary Clinic *</label>
                                    <select wire:model="receptionDeptId" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-blue-500">
                                        @foreach($departments as $dept)
                                            <option value="{{ $dept->id }}">{{ $dept->name }} ({{ $dept->prefix }})</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-300 mb-1">Insurance / Payment Type</label>
                                    <select wire:model="receptionInsuranceType" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-blue-500">
                                        <option value="NHIS">🇬🇭 NHIS Ghana</option>
                                        <option value="Cash/Self-Pay">💵 Cash / Self-Pay</option>
                                        <option value="Acacia">🛡️ Acacia Health</option>
                                        <option value="Enterprise">🛡️ Enterprise Insurance</option>
                                        <option value="Glico">🛡️ Glico Healthcare</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-300 mb-1">Triage Urgency</label>
                                    <select wire:model="receptionPriority" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-blue-500">
                                        <option value="normal">Normal Walk-In</option>
                                        <option value="priority">Priority (Elderly/Maternity)</option>
                                        <option value="emergency">🚨 Emergency / Urgent</option>
                                    </select>
                                </div>
                            </div>

                            <!-- NHIS Card Number (Conditional) -->
                            @if($receptionInsuranceType === 'NHIS')
                                <div>
                                    <label class="block text-xs font-semibold text-slate-300 mb-1">NHIS Membership Number / Ghana Card PIN</label>
                                    <input type="text" wire:model="receptionNhisNumber" placeholder="e.g. 24891043 or GHA-7182931-2" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-blue-500" />
                                </div>
                            @endif

                            <!-- Clinical Vitals Triage Panel -->
                            <div class="p-3.5 bg-slate-900/80 rounded-xl border border-slate-800 space-y-2">
                                <div class="text-[11px] font-bold uppercase tracking-wider text-cyan-400 flex items-center space-x-1.5">
                                    <span>🩺</span>
                                    <span>Triage Nurse Vitals Capture</span>
                                </div>
                                <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                                    <div>
                                        <label class="block text-[10px] text-slate-400 font-semibold mb-0.5">Blood Pressure (mmHg)</label>
                                        <input type="text" wire:model="receptionVitalsBp" placeholder="120/80" class="w-full bg-slate-950 border border-slate-700 rounded-lg px-2.5 py-1.5 text-xs text-white" />
                                    </div>
                                    <div>
                                        <label class="block text-[10px] text-slate-400 font-semibold mb-0.5">Temperature (°C)</label>
                                        <input type="text" wire:model="receptionVitalsTemp" placeholder="36.8" class="w-full bg-slate-950 border border-slate-700 rounded-lg px-2.5 py-1.5 text-xs text-white" />
                                    </div>
                                    <div>
                                        <label class="block text-[10px] text-slate-400 font-semibold mb-0.5">Pulse (bpm)</label>
                                        <input type="text" wire:model="receptionVitalsPulse" placeholder="74" class="w-full bg-slate-950 border border-slate-700 rounded-lg px-2.5 py-1.5 text-xs text-white" />
                                    </div>
                                    <div>
                                        <label class="block text-[10px] text-slate-400 font-semibold mb-0.5">Weight (kg)</label>
                                        <input type="text" wire:model="receptionVitalsWeight" placeholder="68" class="w-full bg-slate-950 border border-slate-700 rounded-lg px-2.5 py-1.5 text-xs text-white" />
                                    </div>
                                </div>
                            </div>

                            <!-- Multi-Stage Pathway -->
                            <div>
                                <label class="block text-xs font-semibold text-slate-300 mb-1.5">Multi-Stage Clinical Routing</label>
                                <div class="flex flex-wrap gap-2">
                                    @foreach(['OPD Consultation', 'Diagnostic Lab', 'Pharmacy Dispensary', 'Radiology / X-Ray'] as $stage)
                                        <button type="button" wire:click="toggleReceptionStage('{{ $stage }}')" 
                                            class="px-3 py-1 text-xs font-semibold rounded-lg border transition {{ in_array($stage, $receptionSelectedStages) ? 'bg-blue-600 border-blue-500 text-white' : 'bg-slate-900 border-slate-700 text-slate-400 hover:text-slate-200' }}">
                                            {{ in_array($stage, $receptionSelectedStages) ? '✓ ' : '+ ' }}{{ $stage }}
                                        </button>
                                    @endforeach
                                </div>
                            </div>

                            <div class="pt-2 flex items-center justify-between border-t border-slate-800">
                                <span class="text-xs text-slate-400">Database auto-increments & syncs live.</span>
                                <button wire:click="issueTicket" class="px-5 py-2.5 bg-gradient-to-r from-blue-600 to-cyan-600 hover:from-blue-500 hover:to-cyan-500 text-white text-xs font-bold rounded-xl shadow-lg shadow-blue-600/20 transition flex items-center space-x-2">
                                    <span>🎫</span>
                                    <span>Generate Ticket with QR</span>
                                </button>
                            </div>
                        </div>

                        <!-- 80mm POS Slip Preview with QR Code -->
                        <div class="bg-slate-950 p-6 rounded-2xl border border-slate-800 flex flex-col justify-between">
                            <div>
                                <div class="flex items-center justify-between mb-3">
                                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400">80mm POS Slip Preview</h3>
                                    <span class="px-2 py-0.5 text-[10px] font-bold rounded bg-emerald-500/20 text-emerald-300">QR Enabled</span>
                                </div>

                                @if($lastIssuedTicket)
                                    <div class="bg-white text-slate-900 p-4 rounded-xl font-mono text-center shadow-md space-y-1.5 text-xs">
                                        <div class="text-xs font-black uppercase">🏥 CHURCH OF CHRIST MISSION HOSPITAL</div>
                                        <div class="text-[10px] text-slate-600">Accra Main Pavilion</div>
                                        <div class="border-b border-dashed border-slate-300 my-1"></div>
                                        <div class="text-3xl font-black text-blue-700">{{ $lastIssuedTicket['ticket'] }}</div>
                                        <div class="text-xs font-bold uppercase">{{ $lastIssuedTicket['dept'] }}</div>
                                        <div class="text-[11px] text-slate-700">Patient: {{ $lastIssuedTicket['name'] }}</div>
                                        <div class="text-[10px] font-bold text-blue-800">[{{ $lastIssuedTicket['insurance'] }}] {{ $lastIssuedTicket['nhis'] ?? '' }}</div>
                                        <div class="text-[10px] text-slate-600">BP: {{ $lastIssuedTicket['bp'] ?? '120/80' }} · Temp: {{ $lastIssuedTicket['temp'] ?? '36.8' }}°C</div>
                                        <div class="border-b border-dashed border-slate-300 my-1"></div>
                                        
                                        <!-- QR Code preview container -->
                                        <div class="py-1 flex flex-col items-center justify-center">
                                            <div id="slip-qr-code" data-ticket="{{ $lastIssuedTicket['ticket'] }}" class="inline-block p-1 bg-white border border-slate-200 rounded">
                                                <div class="w-16 h-16 bg-slate-100 flex items-center justify-center text-[9px] text-slate-500 font-bold border border-slate-300">
                                                    [SCAN QR]
                                                </div>
                                            </div>
                                            <span class="text-[9px] text-slate-500 mt-1">Scan phone camera to track live</span>
                                        </div>

                                        <div class="text-[10px] text-slate-600">Ahead: <strong>{{ $lastIssuedTicket['position'] }} patients</strong> (~{{ $lastIssuedTicket['wait'] }} min)</div>
                                    </div>
                                    <div class="mt-3 flex space-x-2">
                                        <button wire:click="printSlip('{{ $lastIssuedTicket['ticket'] }}')" class="flex-1 py-2 bg-blue-600 hover:bg-blue-500 text-white rounded-xl text-xs font-bold transition flex items-center justify-center space-x-1 shadow-md">
                                            <span>🖨️</span>
                                            <span>Print 80mm Slip</span>
                                        </button>
                                        <button wire:click="navigateView('mobile')" class="px-3 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 rounded-xl text-xs font-bold transition">
                                            📲 Track
                                        </button>
                                    </div>
                                @else
                                    <div class="bg-slate-900 p-8 rounded-xl text-center text-xs text-slate-500">
                                        Issue a ticket above to preview the 80mm slip with dynamic QR code.
                                    </div>
                                @endif
                            </div>

                            <div class="mt-4 pt-3 border-t border-slate-800 text-[11px] text-slate-500 flex items-center justify-between">
                                <span>Thermal Driver: Native</span>
                                <span class="text-emerald-400">80mm Paper</span>
                            </div>
                        </div>
                    </div>

                    <!-- Active Waiting Queue Table -->
                    <div class="bg-slate-950 p-6 rounded-2xl border border-slate-800">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400">Current Waiting Tickets (Database Records)</h3>
                            <span class="text-xs text-slate-500">{{ $tickets->count() }} active records</span>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-xs text-slate-300">
                                <thead class="text-[10px] uppercase font-bold text-slate-500 bg-slate-900/60 border-b border-slate-800">
                                    <tr>
                                        <th class="p-3">Ticket</th>
                                        <th class="p-3">Patient Name</th>
                                        <th class="p-3">Insurance</th>
                                        <th class="p-3">Vitals</th>
                                        <th class="p-3">Department</th>
                                        <th class="p-3">Priority</th>
                                        <th class="p-3">Status</th>
                                        <th class="p-3 text-right">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-800/60 font-medium">
                                    @foreach($tickets as $t)
                                        <tr class="hover:bg-slate-900/40 transition">
                                            <td class="p-3 font-mono-num font-bold text-blue-400">{{ $t->ticket_number }}</td>
                                            <td class="p-3 text-white">{{ $t->patient_name }}</td>
                                            <td class="p-3">
                                                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase {{ $t->insurance_type === 'NHIS' ? 'bg-emerald-500/20 text-emerald-300' : 'bg-slate-800 text-slate-300' }}">
                                                    {{ $t->insurance_type ?? 'NHIS' }}
                                                </span>
                                            </td>
                                            <td class="p-3 font-mono-num text-[11px] text-slate-400">
                                                {{ $t->vitals_bp ?: '120/80' }} · {{ $t->vitals_temp ?: '36.8' }}°C
                                            </td>
                                            <td class="p-3">{{ $t->department?->name }}</td>
                                            <td class="p-3">
                                                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase {{ $t->priority === 'emergency' ? 'bg-red-500/20 text-red-300' : ($t->priority === 'priority' ? 'bg-amber-500/20 text-amber-300' : 'bg-slate-800 text-slate-400') }}">
                                                    {{ $t->priority }}
                                                </span>
                                            </td>
                                            <td class="p-3">
                                                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase {{ $t->status === 'serving' ? 'bg-emerald-500/20 text-emerald-300' : ($t->status === 'called' ? 'bg-blue-500/20 text-blue-300 animate-pulse' : 'bg-slate-800 text-slate-400') }}">
                                                    {{ $t->status }}
                                                </span>
                                            </td>
                                            <td class="p-3 text-right space-x-2">
                                                <button wire:click="printSlip('{{ $t->ticket_number }}')" class="px-2 py-1 bg-slate-800 hover:bg-slate-700 rounded text-slate-300 text-[11px] font-semibold" title="Print Slip">
                                                    🖨️
                                                </button>
                                                <button wire:click="openTransferModal('{{ $t->ticket_number }}')" class="px-2 py-1 bg-slate-800 hover:bg-slate-700 rounded text-blue-300 text-[11px] font-semibold">
                                                    🔀 Refer
                                                </button>
                                                <button wire:click="callSpecific('{{ $t->ticket_number }}')" class="px-2 py-1 bg-blue-600 hover:bg-blue-500 rounded text-white text-[11px] font-semibold">
                                                    📢 Call
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif

            {{-- VIEW 3: DOCTOR CONSULTATION STATION --}}
            @if($activeView === 'doctor')
                @php
                    $activeDoctor = $doctorStations->find($selectedDoctorId) ?? $doctorStations->first();
                @endphp
                <div class="space-y-6 max-w-6xl mx-auto">
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                        <div>
                            <h2 class="text-lg font-bold text-white">Doctor Consultation Station</h2>
                            <p class="text-xs text-slate-400">Direct patient callout, vitals review, clinical notes logging, multi-department referrals.</p>
                        </div>
                        <div class="flex items-center space-x-3">
                            <select wire:model.live="selectedDoctorId" class="bg-slate-950 border border-slate-700 rounded-xl px-3 py-1.5 text-xs text-white focus:outline-none">
                                @foreach($doctorStations as $doc)
                                    <option value="{{ $doc->id }}">{{ $doc->doctor_name }} ({{ $doc->room }}) {{ $doc->is_on_break ? '☕ ON BREAK' : '' }}</option>
                                @endforeach
                            </select>

                            <!-- Doctor Break Toggle -->
                            @if($activeDoctor)
                                <button wire:click="toggleDoctorBreak({{ $activeDoctor->id }})" class="px-3 py-1.5 rounded-xl text-xs font-bold transition flex items-center space-x-1.5 {{ $activeDoctor->is_on_break ? 'bg-amber-500 text-slate-950' : 'bg-slate-800 hover:bg-slate-700 text-slate-300' }}">
                                    <span>☕</span>
                                    <span>{{ $activeDoctor->is_on_break ? 'On Break (Resume)' : 'Take Break' }}</span>
                                </button>
                            @endif
                        </div>
                    </div>

                    @if($activeDoctor && $activeDoctor->is_on_break)
                        <div class="p-4 rounded-2xl bg-amber-950/40 border border-amber-500/40 flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <span class="text-2xl">☕</span>
                                <div>
                                    <div class="text-xs font-bold text-amber-300">{{ $activeDoctor->doctor_name }} is currently on break</div>
                                    <div class="text-[11px] text-amber-200/80">{{ $activeDoctor->break_reason ?? '15m Consultation Break' }} · Waiting queue is paused for this room.</div>
                                </div>
                            </div>
                            <button wire:click="toggleDoctorBreak({{ $activeDoctor->id }})" class="px-3.5 py-1.5 bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold text-xs rounded-xl transition">
                                Resume Station
                            </button>
                        </div>
                    @endif

                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                        <!-- Currently Inside Doctor Room -->
                        <div class="lg:col-span-2 bg-slate-950 p-6 rounded-2xl border border-slate-800 space-y-4">
                            <div class="flex items-center justify-between">
                                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400">Active Consultation</h3>
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
                                    In Room
                                </span>
                            </div>

                            @if($currentDoctorPatient)
                                <div class="p-4 rounded-xl bg-slate-900 border border-slate-800 space-y-3">
                                    <div class="flex items-start justify-between">
                                        <div>
                                            <div class="flex items-center space-x-2">
                                                <span class="text-2xl font-black font-mono-num text-blue-400">{{ $currentDoctorPatient->ticket_number }}</span>
                                                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-blue-500/20 text-blue-300">{{ $currentDoctorPatient->priority }}</span>
                                                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-emerald-500/20 text-emerald-300">{{ $currentDoctorPatient->insurance_type ?? 'NHIS' }}</span>
                                            </div>
                                            <div class="text-sm font-bold text-white mt-1">{{ $currentDoctorPatient->patient_name }}</div>
                                            <div class="text-xs text-slate-400">Phone: {{ $currentDoctorPatient->patient_phone }}</div>
                                        </div>
                                        <div class="text-right text-xs text-slate-400">
                                            <div>Called at: <span class="text-slate-200 font-mono-num">{{ $currentDoctorPatient->called_at ? $currentDoctorPatient->called_at->format('h:i A') : 'Just now' }}</span></div>
                                            <div class="mt-1 text-emerald-400 font-semibold">{{ $activeDoctor?->room ?? 'Room 101' }}</div>
                                        </div>
                                    </div>

                                    <!-- Patient Vitals Summary -->
                                    <div class="grid grid-cols-4 gap-2 pt-2 border-t border-slate-800/80 text-center">
                                        <div class="p-2 bg-slate-950 rounded-lg">
                                            <div class="text-[9px] text-slate-500 uppercase font-bold">BP (mmHg)</div>
                                            <div class="text-xs font-mono-num font-bold text-slate-200">{{ $currentDoctorPatient->vitals_bp ?: '120/80' }}</div>
                                        </div>
                                        <div class="p-2 bg-slate-950 rounded-lg">
                                            <div class="text-[9px] text-slate-500 uppercase font-bold">Temp</div>
                                            <div class="text-xs font-mono-num font-bold text-slate-200">{{ $currentDoctorPatient->vitals_temp ?: '36.8' }}°C</div>
                                        </div>
                                        <div class="p-2 bg-slate-950 rounded-lg">
                                            <div class="text-[9px] text-slate-500 uppercase font-bold">Pulse</div>
                                            <div class="text-xs font-mono-num font-bold text-slate-200">{{ $currentDoctorPatient->vitals_pulse ?: '74' }} bpm</div>
                                        </div>
                                        <div class="p-2 bg-slate-950 rounded-lg">
                                            <div class="text-[9px] text-slate-500 uppercase font-bold">Weight</div>
                                            <div class="text-xs font-mono-num font-bold text-slate-200">{{ $currentDoctorPatient->vitals_weight ?: '68' }} kg</div>
                                        </div>
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-xs font-semibold text-slate-300 mb-1">Clinical Notes & Prescriptions</label>
                                    <textarea wire:model="doctorNotes" rows="3" placeholder="Enter clinical diagnosis, lab tests requested, or medication dosage..." class="w-full bg-slate-900 border border-slate-700 rounded-xl p-3 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-blue-500"></textarea>
                                </div>

                                <div class="pt-2 border-t border-slate-800/80 flex flex-wrap items-center justify-between gap-3">
                                    <div class="flex items-center space-x-2">
                                        <button wire:click="doctorTransferPatient('Diagnostic Laboratory')" class="px-3 py-2 bg-purple-600/20 hover:bg-purple-600/30 text-purple-300 border border-purple-500/30 rounded-xl text-xs font-bold transition flex items-center space-x-1">
                                            <span>🧪</span>
                                            <span>Refer to Lab</span>
                                        </button>
                                        <button wire:click="doctorTransferPatient('Pharmacy Dispensary')" class="px-3 py-2 bg-cyan-600/20 hover:bg-cyan-600/30 text-cyan-300 border border-cyan-500/30 rounded-xl text-xs font-bold transition flex items-center space-x-1">
                                            <span>💊</span>
                                            <span>Send to Pharmacy</span>
                                        </button>
                                    </div>

                                    <button wire:click="doctorMarkCompleted" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl text-xs font-bold shadow-md transition flex items-center space-x-1">
                                        <span>✅</span>
                                        <span>Complete Consult</span>
                                    </button>
                                </div>
                            @else
                                <div class="bg-slate-900/60 p-12 rounded-xl text-center space-y-3">
                                    <div class="text-4xl">👨‍⚕️</div>
                                    <div class="text-xs font-bold text-slate-300">No Patient Currently in Room</div>
                                    <p class="text-xs text-slate-500">Call the next waiting patient from the queue on the right.</p>
                                    <button wire:click="doctorCallNext" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-500 text-white font-bold text-xs rounded-xl shadow-lg transition">
                                        📢 Call Next Patient
                                    </button>
                                </div>
                            @endif
                        </div>

                        <!-- Waiting List in Doctor Queue -->
                        <div class="bg-slate-950 p-6 rounded-2xl border border-slate-800 space-y-3">
                            <div class="flex items-center justify-between">
                                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400">Waiting in Consultation Queue</h3>
                                <button wire:click="doctorCallNext" class="text-xs font-bold text-blue-400 hover:text-blue-300">
                                    + Call Next
                                </button>
                            </div>

                            <div class="space-y-2 max-h-96 overflow-y-auto pr-1">
                                @forelse($doctorQueue as $dq)
                                    <div class="p-3 rounded-xl bg-slate-900 border border-slate-800/80 flex items-center justify-between">
                                        <div>
                                            <div class="flex items-center space-x-2">
                                                <span class="font-mono-num font-bold text-xs text-blue-400">{{ $dq->ticket_number }}</span>
                                                <span class="text-[10px] px-1.5 py-0.5 rounded bg-slate-800 text-slate-400">{{ $dq->priority }}</span>
                                            </div>
                                            <div class="text-xs font-medium text-slate-200 mt-0.5">{{ $dq->patient_name }}</div>
                                            <div class="text-[10px] text-slate-400">{{ $dq->insurance_type ?? 'NHIS' }} · {{ $dq->vitals_bp ?? '120/80' }}</div>
                                        </div>
                                        <button wire:click="doctorDirectCall({{ $dq->id }})" class="px-2.5 py-1 text-xs font-bold rounded-lg bg-blue-600/20 hover:bg-blue-600 text-blue-300 hover:text-white transition">
                                            Call In
                                        </button>
                                    </div>
                                @empty
                                    <div class="text-center py-8 text-xs text-slate-500">Queue is currently clear.</div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            {{-- VIEW 4: TV WAITING DISPLAY BOARD (FULLSCREEN READY) --}}
            @if($activeView === 'display')
                <div class="min-h-full space-y-6">
                    <!-- Top TV Banner -->
                    <div class="bg-slate-950 p-4 md:p-6 rounded-3xl border border-slate-800 shadow-2xl flex flex-col md:flex-row items-center justify-between gap-4">
                        <div class="flex items-center space-x-4">
                            <div class="w-12 h-12 rounded-2xl bg-blue-600 flex items-center justify-center text-white font-black text-2xl shadow-lg">
                                +
                            </div>
                            <div>
                                <h1 class="text-xl md:text-2xl font-black text-white tracking-tight">CHURCH OF CHRIST MISSION HOSPITAL</h1>
                                <div class="text-xs text-slate-400 flex items-center space-x-3 mt-0.5">
                                    <span>CENTRAL WAITING PAVILION</span>
                                    <span>•</span>
                                    <span class="text-emerald-400 font-bold flex items-center space-x-1">
                                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-ping"></span>
                                        <span>TWI & ENGLISH AUDIO CALLOUT ACTIVE</span>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center space-x-3">
                            <!-- TV Voice selector (Visible on all screens) -->
                            <div class="flex items-center space-x-2 bg-slate-900 px-3 py-1.5 rounded-xl border border-slate-700/80">
                                <span class="text-xs">🇬🇭</span>
                                <select wire:model.live="voiceStyle" class="bg-transparent text-slate-200 text-xs font-semibold focus:outline-none cursor-pointer">
                                    <option value="twi_dual" class="bg-slate-900 text-white">English + Akan/Twi</option>
                                    <option value="twi_only" class="bg-slate-900 text-white">Akan / Twi Only</option>
                                    <option value="ga_dual" class="bg-slate-900 text-white">English + Ga</option>
                                    <option value="hausa_dual" class="bg-slate-900 text-white">English + Hausa</option>
                                    <option value="ghanaian_formal" class="bg-slate-900 text-white">Ghanaian Formal</option>
                                    <option value="ghanaian_local" class="bg-slate-900 text-white">Ghanaian Courtesy</option>
                                    <option value="standard" class="bg-slate-900 text-white">Standard</option>
                                </select>
                                <button wire:click="testVoice" onclick="window.testVoiceAnnouncement('{{ $voiceStyle }}')" class="px-2.5 py-1 bg-blue-600 text-white rounded-lg text-xs font-bold hover:bg-blue-500 transition shadow-md shadow-blue-600/30">
                                    🔊 Test
                                </button>
                            </div>

                            <div class="text-right">
                                <div class="text-2xl md:text-3xl font-black text-blue-400 font-mono-num">{{ now()->format('h:i A') }}</div>
                                <div class="text-xs text-slate-400 font-medium">{{ now()->format('l, d F Y') }}</div>
                            </div>
                            <button wire:click="navigateView('dashboard')" class="px-3 py-1.5 bg-slate-800 hover:bg-slate-700 text-slate-300 rounded-xl text-xs font-semibold">
                                Exit TV Mode
                            </button>
                        </div>
                    </div>

                    <!-- Now Calling Hero Cards -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($departments as $dept)
                            @php
                                $calledTicket = $dept->tickets->whereIn('status', ['called', 'serving'])->first();
                                $waitingList = $dept->tickets->where('status', 'waiting')->take(3);
                            @endphp
                            <div class="bg-slate-950 rounded-3xl border-2 {{ $calledTicket ? 'border-blue-500 shadow-2xl shadow-blue-500/20' : 'border-slate-800' }} p-6 flex flex-col justify-between">
                                <div>
                                    <div class="flex items-center justify-between pb-4 border-b border-slate-800">
                                        <div>
                                            <span class="px-2.5 py-1 text-xs font-black rounded-lg uppercase" style="background: {{ $dept->color }}20; color: {{ $dept->color }}; border: 1px solid {{ $dept->color }}40;">
                                                {{ $dept->prefix }}
                                            </span>
                                            <h3 class="text-base font-black text-white mt-2">{{ $dept->name }}</h3>
                                        </div>
                                        <div class="text-right text-xs text-slate-400 font-semibold">
                                            {{ $dept->location }}
                                        </div>
                                    </div>

                                    <!-- Giant Active Ticket Callout -->
                                    <div class="my-6 text-center py-6 rounded-2xl bg-slate-900/80 border border-slate-800/80">
                                        <div class="text-xs font-bold uppercase tracking-widest text-slate-400">NOW CALLING</div>
                                        @if($calledTicket)
                                            <div class="text-5xl md:text-6xl font-black font-mono-num text-amber-400 tracking-tight mt-2 animate-pulse">
                                                {{ $calledTicket->ticket_number }}
                                            </div>
                                            <div class="text-sm font-bold text-slate-200 mt-2">
                                                Proceed to: <span class="text-blue-400 font-extrabold">{{ $dept->location }}</span>
                                            </div>
                                        @else
                                            <div class="text-3xl font-bold font-mono-num text-slate-600 mt-3">— WAITING —</div>
                                            <div class="text-xs text-slate-500 mt-1">Ready for next patient</div>
                                        @endif
                                    </div>
                                </div>

                                <!-- Up Next List -->
                                <div>
                                    <div class="text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-2">Next in Line:</div>
                                    <div class="flex items-center space-x-2">
                                        @forelse($waitingList as $w)
                                            <span class="px-3 py-1.5 rounded-xl bg-slate-900 border border-slate-800 text-xs font-mono-num font-bold text-slate-300">
                                                {{ $w->ticket_number }}
                                            </span>
                                        @empty
                                            <span class="text-xs text-slate-600 italic">No patients in queue</span>
                                        @endforelse
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Bottom Scrolling Ticker Tape -->
                    <div class="bg-slate-950 px-6 py-3 rounded-2xl border border-slate-800 text-xs text-slate-300 flex items-center space-x-4 overflow-hidden">
                        <span class="px-2 py-0.5 bg-blue-600 text-white rounded font-black text-[10px] uppercase">AKWAABA</span>
                        <div class="whitespace-nowrap animate-marquee">
                            📢 Mepaakyɛw, have your NHIS or hospital card ready. Priority is given to emergency cases, expectant mothers, and the elderly. You can scan the QR code on your slip to follow your turn on your smartphone.
                        </div>
                    </div>
                </div>
            @endif

            {{-- VIEW 5: MOBILE PATIENT TRACKER & CSAT RATING --}}
            @if($activeView === 'mobile')
                <div class="max-w-md mx-auto space-y-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-lg font-bold text-white">Patient Mobile Tracker</h2>
                            <p class="text-xs text-slate-400">Live view accessed by scanning 80mm slip QR code.</p>
                        </div>
                        <button wire:click="navigateView('dashboard')" class="text-xs text-blue-400 font-semibold">← Back</button>
                    </div>

                    <!-- Realistic Mobile Phone Mockup -->
                    <div class="bg-slate-950 rounded-[2.5rem] border-4 border-slate-800 p-5 shadow-2xl space-y-5">
                        <!-- Top Mobile Header -->
                        <div class="flex items-center justify-between pb-3 border-b border-slate-800/80">
                            <div class="flex items-center space-x-2">
                                <div class="w-6 h-6 rounded-lg bg-blue-600 flex items-center justify-center text-white font-black text-xs">+</div>
                                <span class="text-xs font-extrabold text-white">Church of Christ Mission Hospital</span>
                            </div>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/20 text-emerald-300 flex items-center space-x-1">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-ping"></span>
                                <span>LIVE SYNC</span>
                            </span>
                        </div>

                        <!-- Ticket Big Banner -->
                        <div class="text-center p-6 rounded-2xl bg-gradient-to-br from-blue-900/40 via-slate-900 to-slate-950 border border-blue-500/30">
                            <div class="text-xs font-semibold text-slate-400">YOUR QUEUE TICKET NUMBER</div>
                            <div class="text-4xl font-black font-mono-num text-white mt-2">{{ $mobileTrackingTicketNumber }}</div>
                            <div class="text-xs font-semibold text-blue-400 mt-1">General Outpatient (OPD)</div>
                        </div>

                        <!-- Queue Status Indicators -->
                        <div class="grid grid-cols-2 gap-3 text-center">
                            <div class="p-3 bg-slate-900 rounded-xl border border-slate-800">
                                <div class="text-[10px] font-bold uppercase text-slate-500">Patients Ahead</div>
                                <div class="text-2xl font-black font-mono-num text-amber-400 mt-1">2</div>
                            </div>
                            <div class="p-3 bg-slate-900 rounded-xl border border-slate-800">
                                <div class="text-[10px] font-bold uppercase text-slate-500">Estimated Wait</div>
                                <div class="text-2xl font-black font-mono-num text-emerald-400 mt-1">~14 min</div>
                            </div>
                        </div>

                        <!-- Multi-Stage Pathway Progress -->
                        <div class="bg-slate-900 p-4 rounded-2xl border border-slate-800 space-y-3">
                            <div class="text-xs font-bold uppercase tracking-wider text-slate-400">Visit Journey Stages</div>
                            
                            <div class="space-y-3">
                                <div class="flex items-center space-x-3">
                                    <div class="w-6 h-6 rounded-full bg-emerald-500 text-slate-950 flex items-center justify-center font-bold text-xs">✓</div>
                                    <div class="text-xs">
                                        <div class="font-bold text-white">Triage & NHIS Check</div>
                                        <div class="text-[10px] text-emerald-400">Completed at 09:15 AM</div>
                                    </div>
                                </div>

                                <div class="flex items-center space-x-3">
                                    <div class="w-6 h-6 rounded-full bg-blue-600 text-white flex items-center justify-center font-bold text-xs animate-pulse">2</div>
                                    <div class="text-xs">
                                        <div class="font-bold text-blue-300">Doctor Consultation</div>
                                        <div class="text-[10px] text-slate-400">Consultation Room 101 · Up Next</div>
                                    </div>
                                </div>

                                <div class="flex items-center space-x-3 opacity-50">
                                    <div class="w-6 h-6 rounded-full bg-slate-800 text-slate-400 flex items-center justify-center font-bold text-xs">3</div>
                                    <div class="text-xs">
                                        <div class="font-bold text-slate-300">Diagnostic Laboratory</div>
                                        <div class="text-[10px] text-slate-500">Pending doctor order</div>
                                    </div>
                                </div>

                                <div class="flex items-center space-x-3 opacity-50">
                                    <div class="w-6 h-6 rounded-full bg-slate-800 text-slate-400 flex items-center justify-center font-bold text-xs">4</div>
                                    <div class="text-xs">
                                        <div class="font-bold text-slate-300">Pharmacy Dispensary</div>
                                        <div class="text-[10px] text-slate-500">Prescription pickup</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- 1-Tap Patient Feedback (CSAT) -->
                        <div class="bg-slate-900 p-4 rounded-2xl border border-slate-800 space-y-3">
                            <div class="text-xs font-bold uppercase tracking-wider text-slate-300">Rate Your Hospital Visit</div>
                            <div class="flex justify-between items-center px-2">
                                @foreach([1 => '😡', 2 => '🙁', 3 => '😐', 4 => '😊', 5 => '🤩'] as $r => $emoji)
                                    <button wire:click="$set('feedbackRating', {{ $r }})" class="text-2xl p-1 rounded-xl hover:scale-125 transition {{ $feedbackRating === $r ? 'bg-blue-600/30 scale-125' : 'opacity-60' }}">
                                        {{ $emoji }}
                                    </button>
                                @endforeach
                            </div>
                            <button wire:click="submitFeedback('{{ $mobileTrackingTicketNumber }}')" class="w-full py-2 bg-blue-600 hover:bg-blue-500 text-white rounded-xl text-xs font-bold transition">
                                Submit Rating
                            </button>
                        </div>

                        <!-- SMS Notification Sim -->
                        <button wire:click="$set('smsModalOpen', true)" class="w-full py-2.5 bg-slate-800 hover:bg-slate-700 text-slate-200 rounded-xl text-xs font-bold transition flex items-center justify-center space-x-2">
                            <span>💬</span>
                            <span>Simulate SMS Alert Notification</span>
                        </button>
                    </div>
                </div>
            @endif

            {{-- VIEW 6: COUNTER TERMINAL (PHARMACY / LAB / CASHIER) --}}
            @if($activeView === 'counter')
                <div class="space-y-6 max-w-5xl mx-auto">
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                        <div>
                            <h2 class="text-lg font-bold text-white">Service Counter Terminal</h2>
                            <p class="text-xs text-slate-400">Multi-counter dispatch for Pharmacy, Phlebotomy Laboratory, and Specialty Windows.</p>
                        </div>
                        <div class="flex items-center space-x-3">
                            <select wire:model="counterDeptId" class="bg-slate-950 border border-slate-700 rounded-xl px-3 py-1.5 text-xs text-white focus:outline-none">
                                @foreach($departments as $dept)
                                    <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                                @endforeach
                            </select>
                            <input type="text" wire:model="counterActiveWindow" placeholder="Window / Booth" class="bg-slate-950 border border-slate-700 rounded-xl px-3 py-1.5 text-xs text-white w-36 focus:outline-none" />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Caller Controls -->
                        <div class="bg-slate-950 p-6 rounded-2xl border border-slate-800 space-y-4">
                            <div class="flex items-center justify-between">
                                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400">Calling Console</h3>
                                <span class="text-xs font-semibold text-blue-400">{{ $counterActiveWindow }}</span>
                            </div>

                            <div class="p-6 rounded-xl bg-slate-900 border border-slate-800 text-center space-y-2">
                                <div class="text-xs font-semibold text-slate-400">Current Called Patient</div>
                                @php
                                    $activeCounterTicket = $counterTickets->whereIn('status', ['called', 'serving'])->first();
                                @endphp
                                @if($activeCounterTicket)
                                    <div class="text-4xl font-black font-mono-num text-amber-400">{{ $activeCounterTicket->ticket_number }}</div>
                                    <div class="text-sm font-bold text-white">{{ $activeCounterTicket->patient_name }}</div>
                                    <div class="text-xs text-slate-400">Insurance: {{ $activeCounterTicket->insurance_type ?? 'NHIS' }}</div>
                                @else
                                    <div class="text-3xl font-bold font-mono-num text-slate-600">— NO TICKET —</div>
                                    <div class="text-xs text-slate-500">Ready to call next waiting patient</div>
                                @endif
                            </div>

                            <!-- Buttons -->
                            <div class="grid grid-cols-2 gap-3">
                                <button wire:click="callNext" class="py-3 bg-gradient-to-r from-blue-600 to-cyan-600 hover:from-blue-500 hover:to-cyan-500 text-white rounded-xl text-xs font-bold shadow-lg transition flex items-center justify-center space-x-1.5">
                                    <span>📢</span>
                                    <span>Call Next</span>
                                </button>
                                <button wire:click="recallCurrent" class="py-3 bg-slate-800 hover:bg-slate-700 text-slate-200 rounded-xl text-xs font-bold transition flex items-center justify-center space-x-1.5">
                                    <span>🔁</span>
                                    <span>Recall (Chime)</span>
                                </button>
                            </div>

                            <button wire:click="markServed" class="w-full py-3 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl text-xs font-bold shadow-md transition flex items-center justify-center space-x-1.5">
                                <span>✅</span>
                                <span>Mark Completed / Served</span>
                            </button>
                        </div>

                        <!-- Department Queue List -->
                        <div class="bg-slate-950 p-6 rounded-2xl border border-slate-800 space-y-3">
                            <div class="flex items-center justify-between">
                                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400">Waiting Patients</h3>
                                <span class="text-xs text-slate-500">{{ $counterTickets->where('status', 'waiting')->count() }} waiting</span>
                            </div>

                            <div class="space-y-2 max-h-80 overflow-y-auto">
                                @forelse($counterTickets->where('status', 'waiting') as $ct)
                                    <div class="p-3 rounded-xl bg-slate-900 border border-slate-800 flex items-center justify-between">
                                        <div>
                                            <div class="font-mono-num font-bold text-xs text-blue-400">{{ $ct->ticket_number }}</div>
                                            <div class="text-xs text-slate-200 font-medium">{{ $ct->patient_name }}</div>
                                            <div class="text-[10px] text-slate-400">{{ $ct->insurance_type ?? 'NHIS' }}</div>
                                        </div>
                                        <button wire:click="callSpecific('{{ $ct->ticket_number }}')" class="px-2.5 py-1 text-xs font-bold bg-blue-600/20 hover:bg-blue-600 text-blue-300 hover:text-white rounded-lg transition">
                                            Call
                                        </button>
                                    </div>
                                @empty
                                    <div class="text-center py-8 text-xs text-slate-500">No waiting patients for this counter.</div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            {{-- VIEW 7: ANALYTICS & AUDIT --}}
            @if($activeView === 'analytics')
                <div class="space-y-6 max-w-6xl mx-auto">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-lg font-bold text-white">Queue Analytics & SLA Compliance</h2>
                            <p class="text-xs text-slate-400">Operational performance, patient volume, NHIS ratio, and wait-time bottlenecks.</p>
                        </div>
                        <button wire:click="$set('dailyReportModalOpen', true)" class="px-4 py-2 bg-blue-600 hover:bg-blue-500 text-white rounded-xl text-xs font-bold shadow-md transition flex items-center space-x-1.5">
                            <span>📑</span>
                            <span>Generate Daily Summary Report</span>
                        </button>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div class="bg-slate-950 p-5 rounded-2xl border border-slate-800">
                            <div class="text-xs text-slate-400 font-semibold uppercase">Daily Throughput</div>
                            <div class="text-3xl font-black font-mono-num text-blue-400 mt-2">264</div>
                            <div class="text-xs text-slate-500 mt-1">Patients processed today</div>
                        </div>
                        <div class="bg-slate-950 p-5 rounded-2xl border border-slate-800">
                            <div class="text-xs text-slate-400 font-semibold uppercase">NHIS Ratio</div>
                            <div class="text-3xl font-black font-mono-num text-amber-400 mt-2">74.2%</div>
                            <div class="text-xs text-slate-500 mt-1">196 NHIS claims registered</div>
                        </div>
                        <div class="bg-slate-950 p-5 rounded-2xl border border-slate-800">
                            <div class="text-xs text-slate-400 font-semibold uppercase">SLA Compliance</div>
                            <div class="text-3xl font-black font-mono-num text-emerald-400 mt-2">94.8%</div>
                            <div class="text-xs text-slate-500 mt-1">Under 20m target wait</div>
                        </div>
                        <div class="bg-slate-950 p-5 rounded-2xl border border-slate-800">
                            <div class="text-xs text-slate-400 font-semibold uppercase">Patient Satisfaction</div>
                            <div class="text-3xl font-black font-mono-num text-purple-400 mt-2">{{ $avgSatisfaction }} / 5</div>
                            <div class="text-xs text-slate-500 mt-1">98.2% positive ratings</div>
                        </div>
                    </div>

                    <!-- Department Load Breakdown -->
                    <div class="bg-slate-950 p-6 rounded-2xl border border-slate-800 space-y-4">
                        <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400">Department Workload Breakdown</h3>
                        <div class="space-y-3">
                            @foreach($departments as $d)
                                <div>
                                    <div class="flex items-center justify-between text-xs font-semibold mb-1">
                                        <span class="text-slate-300">{{ $d->name }}</span>
                                        <span class="text-slate-400 font-mono-num">{{ $d->tickets->count() * 12 }}% load</span>
                                    </div>
                                    <div class="w-full bg-slate-900 rounded-full h-2">
                                        <div class="h-2 rounded-full" style="width: {{ min(100, max(15, $d->tickets->count() * 20)) }}%; background-color: {{ $d->color }}"></div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            {{-- VIEW 8: PATIENT SELF-KIOSK --}}
            @if($activeView === 'kiosk')
                <div class="max-w-4xl mx-auto space-y-8 py-8 text-center">
                    <div class="space-y-2">
                        <div class="w-16 h-16 rounded-3xl bg-blue-600 mx-auto flex items-center justify-center text-white text-3xl font-black shadow-xl shadow-blue-600/30">
                            +
                        </div>
                        <h1 class="text-3xl md:text-4xl font-black text-white">TOUCH TO GET YOUR QUEUE TICKET</h1>
                        <p class="text-sm text-slate-400 max-w-md mx-auto">Please select the clinic or service department you are visiting today.</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-left">
                        @foreach($departments as $d)
                            <button wire:click="kioskSelectDept({{ $d->id }})" class="p-6 rounded-3xl bg-slate-950 border-2 border-slate-800 hover:border-blue-500 hover:bg-slate-900 transition flex items-center justify-between group shadow-xl">
                                <div>
                                    <span class="px-3 py-1 rounded-lg text-xs font-black uppercase" style="background: {{ $d->color }}20; color: {{ $d->color }};">
                                        {{ $d->prefix }}
                                    </span>
                                    <h3 class="text-xl font-black text-white mt-3 group-hover:text-blue-400 transition">{{ $d->name }}</h3>
                                    <p class="text-xs text-slate-400 mt-1">{{ $d->location }}</p>
                                </div>
                                <div class="w-12 h-12 rounded-2xl bg-slate-900 group-hover:bg-blue-600 flex items-center justify-center text-slate-400 group-hover:text-white text-xl font-bold transition">
                                    →
                                </div>
                            </button>
                        @endforeach
                    </div>

                    <div class="pt-6">
                        <button wire:click="navigateView('dashboard')" class="text-xs text-slate-500 hover:text-slate-300 font-semibold underline">
                            ← Return to Staff Administration Dashboard
                        </button>
                    </div>
                </div>
            @endif

        </main>
    </div>

    <!-- MODAL 1: Role Authentication & Fast Staff Switcher -->
    @if($authModalOpen)
        <div class="fixed inset-0 z-50 bg-slate-950/80 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="bg-slate-900 border border-slate-800 rounded-3xl p-6 max-w-md w-full shadow-2xl space-y-5">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-base font-bold text-white">Staff Station Access</h3>
                        <p class="text-xs text-slate-400">Select demo staff role or login with credentials</p>
                    </div>
                    <button wire:click="$set('authModalOpen', false)" class="text-slate-400 hover:text-white text-sm font-bold">✕</button>
                </div>

                <!-- 1-Click Role Switcher -->
                <div class="space-y-2">
                    <div class="text-[10px] font-bold uppercase tracking-wider text-slate-500">Fast 1-Click Staff Profile:</div>
                    <div class="grid grid-cols-2 gap-2">
                        <button wire:click="quickLogin('admin')" class="p-2.5 rounded-xl bg-slate-950 hover:bg-blue-600/20 border border-slate-800 hover:border-blue-500/40 text-left transition">
                            <div class="text-xs font-bold text-slate-200">👑 Admin</div>
                            <div class="text-[10px] text-slate-400">Full control</div>
                        </button>

                        <button wire:click="quickLogin('dr.mensah')" class="p-2.5 rounded-xl bg-slate-950 hover:bg-blue-600/20 border border-slate-800 hover:border-blue-500/40 text-left transition">
                            <div class="text-xs font-bold text-slate-200">🩺 Dr. Mensah</div>
                            <div class="text-[10px] text-slate-400">Consultation Rm 101</div>
                        </button>

                        <button wire:click="quickLogin('reception')" class="p-2.5 rounded-xl bg-slate-950 hover:bg-blue-600/20 border border-slate-800 hover:border-blue-500/40 text-left transition">
                            <div class="text-xs font-bold text-slate-200">🎫 Reception / NHIS</div>
                            <div class="text-[10px] text-slate-400">Triage intake</div>
                        </button>

                        <button wire:click="quickLogin('pharmacy')" class="p-2.5 rounded-xl bg-slate-950 hover:bg-blue-600/20 border border-slate-800 hover:border-blue-500/40 text-left transition">
                            <div class="text-xs font-bold text-slate-200">💊 Pharmacy Desk</div>
                            <div class="text-[10px] text-slate-400">Dispensary window</div>
                        </button>
                    </div>
                </div>

                <div class="relative flex py-1 items-center">
                    <div class="flex-grow border-t border-slate-800"></div>
                    <span class="flex-shrink mx-2 text-[10px] uppercase font-bold text-slate-500">Or enter password</span>
                    <div class="flex-grow border-t border-slate-800"></div>
                </div>

                <!-- Credential form -->
                <div class="space-y-3">
                    <input type="email" wire:model="loginEmail" placeholder="admin@hospital.org" class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-blue-500" />
                    <input type="password" wire:model="loginPassword" placeholder="password123" class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-blue-500" />
                    <button wire:click="loginWithCredentials" class="w-full py-2.5 bg-blue-600 hover:bg-blue-500 text-white rounded-xl text-xs font-bold transition">
                        Sign In
                    </button>
                </div>

                @if($currentUser)
                    <div class="pt-2 border-t border-slate-800 flex justify-end">
                        <button wire:click="logoutStaff" class="text-xs text-red-400 hover:text-red-300 font-semibold">
                            Sign Out of Current Session
                        </button>
                    </div>
                @endif
            </div>
        </div>
    @endif

    <!-- MODAL 2: Transfer Patient Modal -->
    @if($transferModalOpen)
        <div class="fixed inset-0 z-50 bg-slate-950/80 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="bg-slate-900 border border-slate-800 rounded-3xl p-6 max-w-md w-full shadow-2xl space-y-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-base font-bold text-white">Transfer Patient {{ $transferTicketNumber }}</h3>
                        <p class="text-xs text-slate-400">Reassign ticket to another hospital department</p>
                    </div>
                    <button wire:click="$set('transferModalOpen', false)" class="text-slate-400 hover:text-white text-sm font-bold">✕</button>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Target Department</label>
                    <select wire:model="transferTargetDept" class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3.5 py-2.5 text-xs text-white focus:outline-none">
                        @foreach($departments as $d)
                            <option value="{{ $d->name }}">{{ $d->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="pt-3 flex space-x-3">
                    <button wire:click="$set('transferModalOpen', false)" class="flex-1 py-2.5 bg-slate-800 hover:bg-slate-700 text-slate-300 rounded-xl text-xs font-bold transition">
                        Cancel
                    </button>
                    <button wire:click="confirmTransfer" class="flex-1 py-2.5 bg-blue-600 hover:bg-blue-500 text-white rounded-xl text-xs font-bold shadow-md transition">
                        Confirm Transfer
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- MODAL 3: SMS Preview Modal -->
    @if($smsModalOpen)
        <div class="fixed inset-0 z-50 bg-slate-950/80 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="bg-slate-900 border border-slate-800 rounded-3xl p-6 max-w-sm w-full shadow-2xl space-y-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-2">
                        <span class="text-lg">💬</span>
                        <h3 class="text-sm font-bold text-white">Hubtel SMS Alert Simulator</h3>
                    </div>
                    <button wire:click="$set('smsModalOpen', false)" class="text-slate-400 hover:text-white text-sm font-bold">✕</button>
                </div>

                <div class="p-4 bg-slate-950 rounded-2xl border border-slate-800 text-xs text-slate-300 space-y-2 font-mono">
                    <div class="text-[10px] text-slate-500">From: CHURCH-OF-CHRIST-HOSPITAL</div>
                    <div class="text-white">"Hello! Ticket <strong>{{ $mobileTrackingTicketNumber }}</strong> is now #2 in queue for Doctor Consultation. Please proceed near Consultation Room 101."</div>
                </div>

                <button wire:click="$set('smsModalOpen', false)" class="w-full py-2 bg-blue-600 text-white rounded-xl text-xs font-bold">
                    Close Preview
                </button>
            </div>
        </div>
    @endif

    <!-- MODAL 4: Daily Shift & End-of-Day Summary Report -->
    @if($dailyReportModalOpen)
        <div class="fixed inset-0 z-50 bg-slate-950/80 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="bg-slate-900 border border-slate-800 rounded-3xl p-6 max-w-2xl w-full shadow-2xl space-y-5">
                <div class="flex items-center justify-between pb-3 border-b border-slate-800">
                    <div>
                        <h3 class="text-base font-bold text-white">End-of-Day Shift Handover Report</h3>
                        <p class="text-xs text-slate-400">Date: {{ now()->format('l, d F Y') }} · Church of Christ Mission Hospital</p>
                    </div>
                    <button wire:click="$set('dailyReportModalOpen', false)" class="text-slate-400 hover:text-white text-sm font-bold">✕</button>
                </div>

                <div class="grid grid-cols-3 gap-3 text-center">
                    <div class="p-3 bg-slate-950 rounded-xl border border-slate-800">
                        <div class="text-[10px] text-slate-400 uppercase font-bold">Total Patient Visits</div>
                        <div class="text-2xl font-black font-mono-num text-blue-400 mt-1">{{ $servedToday }}</div>
                    </div>
                    <div class="p-3 bg-slate-950 rounded-xl border border-slate-800">
                        <div class="text-[10px] text-slate-400 uppercase font-bold">NHIS Insurance Ratio</div>
                        <div class="text-2xl font-black font-mono-num text-amber-400 mt-1">74.2%</div>
                    </div>
                    <div class="p-3 bg-slate-950 rounded-xl border border-slate-800">
                        <div class="text-[10px] text-slate-400 uppercase font-bold">Avg Consultation Time</div>
                        <div class="text-2xl font-black font-mono-num text-emerald-400 mt-1">11.4 min</div>
                    </div>
                </div>

                <div class="space-y-2">
                    <div class="text-xs font-bold uppercase tracking-wider text-slate-400">Doctor Station Performance:</div>
                    <div class="space-y-1.5 text-xs text-slate-300">
                        <div class="p-2.5 bg-slate-950 rounded-xl flex items-center justify-between">
                            <div>
                                <span class="font-bold text-white">Dr. Kwame Mensah</span>
                                <span class="text-slate-500"> (Consultation Room 101)</span>
                            </div>
                            <span class="font-mono-num font-bold text-blue-400">38 patients seen</span>
                        </div>
                        <div class="p-2.5 bg-slate-950 rounded-xl flex items-center justify-between">
                            <div>
                                <span class="font-bold text-white">Dr. Sarah Jenkins</span>
                                <span class="text-slate-500"> (Pediatrics Room 102)</span>
                            </div>
                            <span class="font-mono-num font-bold text-blue-400">32 patients seen</span>
                        </div>
                    </div>
                </div>

                <div class="pt-3 border-t border-slate-800 flex justify-end space-x-3">
                    <button wire:click="$set('dailyReportModalOpen', false)" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 rounded-xl text-xs font-bold transition">
                        Close
                    </button>
                    <button onclick="window.print()" class="px-5 py-2 bg-blue-600 hover:bg-blue-500 text-white rounded-xl text-xs font-bold transition shadow-md flex items-center space-x-1.5">
                        <span>🖨️</span>
                        <span>Print / Export PDF</span>
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- Toast Notification Banner -->
    @if($showToast)
        <div x-data="{ init() { setTimeout(() => { $wire.set('showToast', false) }, 4000) } }" 
            class="fixed bottom-5 right-5 z-50 flex items-center space-x-3 bg-slate-950 border border-slate-700 px-4 py-3 rounded-2xl shadow-2xl text-xs text-white transition animate-bounce">
            <span class="text-base">{{ $toastIcon }}</span>
            <span class="font-semibold">{{ $toastMessage }}</span>
            <button wire:click="$set('showToast', false)" class="text-slate-400 hover:text-white font-bold ml-2">✕</button>
        </div>
    @endif

    <!-- Hidden 80mm Thermal Receipt Print Layout -->
    <div id="thermal-print-slip" style="display:none;">
        <div style="font-family: monospace; text-align: center; font-size: 12px; line-height: 1.4; color: #000;">
            <div style="font-size: 14px; font-weight: bold;">🏥 CHURCH OF CHRIST MISSION HOSPITAL</div>
            <div style="font-size: 10px;">Accra Central Main Pavilion</div>
            <div style="border-top: 1px dashed #000; margin: 6px 0;"></div>
            <div style="font-size: 26px; font-weight: 900; margin: 4px 0;">{{ $printSlipData['ticket'] ?? $lastIssuedTicket['ticket'] ?? 'OPD-001' }}</div>
            <div style="font-weight: bold; font-size: 13px;">{{ $printSlipData['dept'] ?? $lastIssuedTicket['dept'] ?? 'Outpatient' }}</div>
            <div>Patient: {{ $printSlipData['name'] ?? $lastIssuedTicket['name'] ?? 'Walk-In Patient' }}</div>
            <div>[{{ $printSlipData['insurance'] ?? $lastIssuedTicket['insurance'] ?? 'NHIS' }}] {{ $printSlipData['nhis'] ?? $lastIssuedTicket['nhis'] ?? '' }}</div>
            <div>BP: {{ $printSlipData['bp'] ?? $lastIssuedTicket['bp'] ?? '120/80' }} · Temp: {{ $printSlipData['temp'] ?? $lastIssuedTicket['temp'] ?? '36.8' }}°C</div>
            <div style="border-top: 1px dashed #000; margin: 6px 0;"></div>
            
            <div style="margin: 6px 0; display: flex; justify-content: center;">
                <div id="slip-qr-code" data-ticket="{{ $printSlipData['ticket'] ?? $lastIssuedTicket['ticket'] ?? 'OPD-001' }}"></div>
            </div>
            
            <div style="font-size: 9px; margin-top: 4px;">Scan QR with phone to follow your turn live.</div>
            <div style="font-size: 9px; margin-top: 4px;">Issued: {{ now()->format('Y-m-d h:i A') }}</div>
        </div>
    </div>
</div>
