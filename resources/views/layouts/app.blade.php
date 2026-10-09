<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>{{ $title ?? 'Church of Christ Mission Hospital — Queue System' }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <style>
        [x-cloak] { display: none !important; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .font-mono-num { font-family: 'JetBrains Mono', monospace; }

        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: #f1f5f9; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }

        @keyframes ticketPulse {
            0%, 100% { box-shadow: 0 0 0 0 rgba(37,99,235,0.4); }
            50%       { box-shadow: 0 0 0 14px rgba(37,99,235,0); }
        }
        .ticket-pulse { animation: ticketPulse 2s infinite; }

        @keyframes liveGlow {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.4; transform: scale(0.95); }
        }
        .live-glow { animation: liveGlow 1.5s infinite ease-in-out; }

        .nav-active {
            background: linear-gradient(90deg, rgba(255,255,255,0.18) 0%, rgba(255,255,255,0.06) 100%);
            border-left: 3.5px solid #60a5fa;
            color: #ffffff !important;
        }

        @media print {
            body * { visibility: hidden; }
            #thermal-print-slip, #thermal-print-slip * { visibility: visible; }
            #thermal-print-slip {
                position: absolute;
                left: 0;
                top: 0;
                width: 80mm;
                margin: 0;
                padding: 4mm;
                display: block !important;
            }
            aside, header, main, nav, .no-print { display: none !important; }
        }
    </style>
    @livewireStyles
</head>
<body class="bg-slate-100 text-slate-800 antialiased selection:bg-blue-500 selection:text-white">

    {{ $slot }}

    @livewireScripts
    <script>
        // Web Audio API Hospital PA Chime (Ding-Dong 2-Tone Bell)
        function playHospitalChime(callback) {
            try {
                const AudioContext = window.AudioContext || window.webkitAudioContext;
                if (!AudioContext) {
                    if (callback) callback();
                    return;
                }
                const ctx = new AudioContext();
                const now = ctx.currentTime;

                // Tone 1: 587.33 Hz (D5)
                const osc1 = ctx.createOscillator();
                const gain1 = ctx.createGain();
                osc1.type = 'sine';
                osc1.frequency.setValueAtTime(587.33, now);
                gain1.gain.setValueAtTime(0, now);
                gain1.gain.linearRampToValueAtTime(0.35, now + 0.05);
                gain1.gain.exponentialRampToValueAtTime(0.001, now + 0.6);
                osc1.connect(gain1);
                gain1.connect(ctx.destination);
                osc1.start(now);
                osc1.stop(now + 0.65);

                // Tone 2: 880 Hz (A5)
                const osc2 = ctx.createOscillator();
                const gain2 = ctx.createGain();
                osc2.type = 'sine';
                osc2.frequency.setValueAtTime(880.0, now + 0.3);
                gain2.gain.setValueAtTime(0, now + 0.3);
                gain2.gain.linearRampToValueAtTime(0.4, now + 0.35);
                gain2.gain.exponentialRampToValueAtTime(0.001, now + 1.1);
                osc2.connect(gain2);
                gain2.connect(ctx.destination);
                osc2.start(now + 0.3);
                osc2.stop(now + 1.15);

                setTimeout(() => {
                    if (callback) callback();
                }, 900);
            } catch (e) {
                console.warn('Audio chime error:', e);
                if (callback) callback();
            }
        }

        // Find Best Ghanaian / West African / British English Voice
        function selectGhanaianVoice() {
            const voices = window.speechSynthesis.getVoices();
            if (!voices || voices.length === 0) return null;

            // 1. Explicit Ghanaian English
            let v = voices.find(voice => voice.lang === 'en-GH' || voice.lang.toLowerCase().includes('gh'));
            if (v) return v;

            // 2. West African English (en-NG)
            v = voices.find(voice => voice.lang === 'en-NG' || voice.name.toLowerCase().includes('nigeria') || voice.lang.toLowerCase().includes('ng'));
            if (v) return v;

            // 3. African English (en-ZA)
            v = voices.find(voice => voice.lang === 'en-ZA' || voice.name.toLowerCase().includes('south africa'));
            if (v) return v;

            // 4. British / Commonwealth English (Natural / Female / Google UK)
            v = voices.find(voice => voice.lang === 'en-GB' && (voice.name.includes('Natural') || voice.name.includes('Google') || voice.name.includes('Female') || voice.name.includes('Hazel') || voice.name.includes('Susan')));
            if (v) return v;

            v = voices.find(voice => voice.lang.startsWith('en-GB'));
            if (v) return v;

            return voices.find(voice => voice.lang.startsWith('en')) || voices[0];
        }

        // Audio Text-to-Speech Announcement Listener for Livewire (Handles Multi-Language Dual Queues)
        window.addEventListener('announce-call', event => {
            const data = event.detail[0] || event.detail;
            if (!data) return;

            const textQueue = data.queue && data.queue.length > 0 ? data.queue : [data.text];
            if (textQueue.length === 0 || !textQueue[0]) return;

            window.speechSynthesis.cancel();

            const playQueue = (queueIndex) => {
                if (queueIndex >= textQueue.length) return;

                const textToSpeak = textQueue[queueIndex];
                const utterance = new SpeechSynthesisUtterance(textToSpeak);
                const voice = selectGhanaianVoice();
                if (voice) {
                    utterance.voice = voice;
                    utterance.lang = voice.lang;
                }
                utterance.rate = 0.88;
                utterance.pitch = 1.02;
                utterance.volume = 1;

                utterance.onend = () => {
                    if (queueIndex + 1 < textQueue.length) {
                        setTimeout(() => playQueue(queueIndex + 1), 450);
                    }
                };

                window.speechSynthesis.speak(utterance);
            };

            if (data.chime !== false) {
                playHospitalChime(() => playQueue(0));
            } else {
                playQueue(0);
            }
        });

        // Thermal Print Slip Listener + Dynamic QR Code Generation
        window.addEventListener('print-slip', event => {
            const qrContainer = document.getElementById('slip-qr-code');
            if (qrContainer) {
                qrContainer.innerHTML = '';
                const trackingUrl = window.location.origin + '/?track=' + (qrContainer.dataset.ticket || 'OPD-001');
                new QRCode(qrContainer, {
                    text: trackingUrl,
                    width: 76,
                    height: 76,
                    colorDark: '#000000',
                    colorLight: '#ffffff',
                    correctLevel: QRCode.CorrectLevel.M
                });
            }

            setTimeout(() => {
                window.print();
            }, 300);
        });
    </script>
</body>
</html>
