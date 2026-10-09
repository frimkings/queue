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
        // Global Audio Context Singleton
        let globalAudioCtx = null;
        function getAudioContext() {
            if (!globalAudioCtx || globalAudioCtx.state === 'closed') {
                const AudioCtx = window.AudioContext || window.webkitAudioContext;
                if (AudioCtx) {
                    globalAudioCtx = new AudioCtx();
                }
            }
            if (globalAudioCtx && globalAudioCtx.state === 'suspended') {
                globalAudioCtx.resume();
            }
            return globalAudioCtx;
        }

        // Global User Gesture Audio Unlock
        function unlockAudioEngine() {
            try {
                const ctx = getAudioContext();
                if (ctx && ctx.state === 'suspended') {
                    ctx.resume();
                }
                if (window.speechSynthesis && window.speechSynthesis.paused) {
                    window.speechSynthesis.resume();
                }
            } catch (e) {}
        }
        window.addEventListener('click', unlockAudioEngine, { passive: true });
        window.addEventListener('touchstart', unlockAudioEngine, { passive: true });
        window.addEventListener('keydown', unlockAudioEngine, { passive: true });

        // Play Hospital Chime (HTML5 Audio /audio/chime.wav + Web Audio Synth Fallback)
        function playHospitalChime(onDone) {
            let done = false;
            const finish = () => {
                if (!done) {
                    done = true;
                    if (onDone) onDone();
                }
            };

            // Attempt 1: Native HTML5 Audio
            try {
                const audio = new Audio('/audio/chime.wav');
                audio.volume = 1.0;
                audio.onended = finish;
                audio.onerror = () => {
                    playSyntheticChime(finish);
                };
                const p = audio.play();
                if (p !== undefined) {
                    p.then(() => {
                        // Playing successfully
                    }).catch(() => {
                        playSyntheticChime(finish);
                    });
                }
                setTimeout(finish, 1350);
                return;
            } catch (e) {
                playSyntheticChime(finish);
            }
        }

        function playSyntheticChime(onDone) {
            try {
                const ctx = getAudioContext();
                if (!ctx) {
                    if (onDone) onDone();
                    return;
                }

                const runChime = () => {
                    try {
                        const now = ctx.currentTime;

                        // Tone 1: D5 (587.33 Hz)
                        const osc1 = ctx.createOscillator();
                        const gain1 = ctx.createGain();
                        osc1.type = 'sine';
                        osc1.frequency.setValueAtTime(587.33, now);
                        gain1.gain.setValueAtTime(0.001, now);
                        gain1.gain.linearRampToValueAtTime(0.4, now + 0.04);
                        gain1.gain.linearRampToValueAtTime(0.001, now + 0.55);
                        osc1.connect(gain1);
                        gain1.connect(ctx.destination);
                        osc1.start(now);
                        osc1.stop(now + 0.6);

                        // Tone 2: A5 (880.00 Hz)
                        const osc2 = ctx.createOscillator();
                        const gain2 = ctx.createGain();
                        osc2.type = 'sine';
                        osc2.frequency.setValueAtTime(880.0, now + 0.28);
                        gain2.gain.setValueAtTime(0.001, now + 0.28);
                        gain2.gain.linearRampToValueAtTime(0.45, now + 0.32);
                        gain2.gain.linearRampToValueAtTime(0.001, now + 0.95);
                        osc2.connect(gain2);
                        gain2.connect(ctx.destination);
                        osc2.start(now + 0.28);
                        osc2.stop(now + 1.0);

                        setTimeout(() => {
                            if (onDone) onDone();
                        }, 850);
                    } catch (err) {
                        console.warn('Synth error:', err);
                        if (onDone) onDone();
                    }
                };

                if (ctx.state === 'suspended') {
                    ctx.resume().then(runChime).catch(() => {
                        if (onDone) onDone();
                    });
                } else {
                    runChime();
                }
            } catch (e) {
                console.warn('Audio chime error:', e);
                if (onDone) onDone();
            }
        }

        // Voice Caching & Selection
        let cachedVoices = [];
        function updateVoices() {
            if (window.speechSynthesis) {
                try {
                    cachedVoices = window.speechSynthesis.getVoices() || [];
                } catch(e) {}
            }
        }
        if (typeof window !== 'undefined' && window.speechSynthesis) {
            updateVoices();
            window.speechSynthesis.onvoiceschanged = updateVoices;
        }

        function selectGhanaianVoice() {
            if (!cachedVoices || cachedVoices.length === 0) {
                updateVoices();
            }
            if (!cachedVoices || cachedVoices.length === 0) return null;

            // 1. Ghanaian / African English
            let v = cachedVoices.find(voice => voice.lang === 'en-GH' || voice.lang.toLowerCase().includes('gh'));
            if (v) return v;

            v = cachedVoices.find(voice => voice.lang === 'en-NG' || voice.name.toLowerCase().includes('nigeria') || voice.lang.toLowerCase().includes('ng'));
            if (v) return v;

            v = cachedVoices.find(voice => voice.lang === 'en-ZA' || voice.name.toLowerCase().includes('south africa'));
            if (v) return v;

            // 2. British / Natural English
            v = cachedVoices.find(voice => voice.lang.startsWith('en-GB') && (voice.name.includes('Natural') || voice.name.includes('Google') || voice.name.includes('Female')));
            if (v) return v;

            v = cachedVoices.find(voice => voice.lang.startsWith('en-GB'));
            if (v) return v;

            // 3. Any English voice
            return cachedVoices.find(voice => voice.lang.startsWith('en')) || null;
        }

        // Global active utterances list to prevent Chromium garbage collection
        window._activeUtterances = [];

        function speakTextUtterance(text, onFinished) {
            if (!text || typeof text !== 'string' || !text.trim()) {
                if (onFinished) onFinished();
                return;
            }

            if (!('speechSynthesis' in window)) {
                console.warn('Speech synthesis not available in this browser.');
                if (onFinished) onFinished();
                return;
            }

            try {
                if (window.speechSynthesis.paused) {
                    window.speechSynthesis.resume();
                }

                const cleanText = text.replace(/[\n\r\t]+/g, ' ').trim();
                const utterance = new SpeechSynthesisUtterance(cleanText);

                utterance.volume = 1.0;
                utterance.rate = 0.90;
                utterance.pitch = 1.0;
                utterance.lang = 'en-US';

                if (cachedVoices && cachedVoices.length > 0) {
                    const voice = selectGhanaianVoice();
                    if (voice) {
                        try {
                            utterance.voice = voice;
                            if (voice.lang) utterance.lang = voice.lang;
                        } catch(e) {}
                    }
                }

                let isCompleted = false;
                const finish = () => {
                    if (isCompleted) return;
                    isCompleted = true;
                    const index = window._activeUtterances.indexOf(utterance);
                    if (index > -1) window._activeUtterances.splice(index, 1);
                    if (onFinished) onFinished();
                };

                utterance.onend = finish;
                utterance.onerror = (err) => {
                    console.warn('Utterance notice:', err);
                    finish();
                };

                // Safety timeout in case onend never fires (mobile browsers)
                const maxTime = Math.max(5000, cleanText.length * 150);
                setTimeout(finish, maxTime);

                window._activeUtterances.push(utterance);
                window.speechSynthesis.speak(utterance);

                if (window.speechSynthesis.paused) {
                    window.speechSynthesis.resume();
                }
            } catch (err) {
                console.warn('Speech error:', err);
                if (onFinished) onFinished();
            }
        }

        // Play Complete Hospital Announcement Sequence (Chime + Speech Queue)
        const pendingAnnouncements = [];
        let announcementPlaying = false;

        window.playHospitalAnnouncement = function(queue, chime = true) {
            unlockAudioEngine();
            if (!queue || !Array.isArray(queue) || queue.length === 0) return;
            pendingAnnouncements.push({ queue, chime });
            playNextAnnouncement();
        };

        function playNextAnnouncement() {
            if (announcementPlaying || pendingAnnouncements.length === 0) return;
            announcementPlaying = true;
            const { queue, chime } = pendingAnnouncements.shift();

            const playSequential = (idx) => {
                if (idx >= queue.length) {
                    announcementPlaying = false;
                    playNextAnnouncement();
                    return;
                }
                speakTextUtterance(queue[idx], () => {
                    setTimeout(() => playSequential(idx + 1), 250);
                });
            };

            if (chime !== false) {
                playHospitalChime(() => {
                    setTimeout(() => playSequential(0), 100);
                });
            } else {
                playSequential(0);
            }
        }

        // Instant Direct Test Announcement Helper
        window.testVoiceAnnouncement = function(style) {
            unlockAudioEngine();
            const phrases = {
                'twi_dual': [
                    'Attention please. Ticket O, P, D, zero, five, nine. Kindly proceed to Consultation Room 101. Thank you.',
                    'Mepaakyew, ticket nomba O, P, D, hwee, nnum, nkron. Yesre wo ko Consultation Room 101. Medaase.'
                ],
                'twi_only': [
                    'Mepaakyew, ticket nomba O, P, D, hwee, nnum, nkron. Yesre wo ko Consultation Room 101. Medaase.'
                ],
                'ga_dual': [
                    'Attention please. Ticket O, P, D, zero, five, nine. Kindly proceed to Consultation Room 101. Thank you.',
                    'Ofaine, ticket nomba O, P, D, zero, five, nine. Yaa Consultation Room 101. Oyiwaladong.'
                ],
                'hausa_dual': [
                    'Attention please. Ticket O, P, D, zero, five, nine. Kindly proceed to Consultation Room 101. Thank you.',
                    'Dan Allah, ticket lamba O, P, D, zero, five, nine. Ka je Consultation Room 101. Na gode.'
                ],
                'ghanaian_local': [
                    'Agoo! Attention please. Ticket number O, P, D, zero, five, nine. Kindly report to Consultation Room 101. Medaase.'
                ],
                'standard': [
                    'Attention please. Ticket OPD-059. Please proceed to Consultation Room 101. Thank you.'
                ]
            };
            const s = style || window._currentVoiceStyle || 'twi_dual';
            const queue = phrases[s] || phrases['twi_dual'];
            window.playHospitalAnnouncement(queue, true);
        };

        // Audio Announcement Livewire Dispatcher Listener
        window.addEventListener('announce-call', event => {
            let data = event.detail;
            if (Array.isArray(data) && data.length > 0) {
                data = data[0];
            } else if (data && data.data && typeof data.data === 'object' && !data.queue && !data.text) {
                data = data.data;
            }
            if (!data) return;

            const textQueue = data.queue && Array.isArray(data.queue) && data.queue.length > 0 
                ? data.queue 
                : [data.text || ''];

            if (textQueue.length === 0 || !textQueue[0]) return;

            window.playHospitalAnnouncement(textQueue, data.chime !== false);
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
