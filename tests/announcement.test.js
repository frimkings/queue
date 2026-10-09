import { readFileSync } from 'node:fs';
import vm from 'node:vm';
import assert from 'node:assert/strict';
import test from 'node:test';

const layout = readFileSync(new URL('../resources/views/layouts/app.blade.php', import.meta.url), 'utf8');
const script = layout.match(/<script>\s*([\s\S]*?)<\/script>/)[1];

function browser(voices = []) {
    const timers = new Map();
    const spoken = [];
    const chimes = [];
    const alert = { hidden: true, textContent: '' };
    let timerId = 0;
    let cancellations = 0;
    const synth = {
        getVoices: () => voices,
        speak: utterance => spoken.push(utterance),
        cancel: () => { cancellations++; },
    };
    const window = { speechSynthesis: synth, addEventListener() {} };
    vm.runInNewContext(script, {
        window,
        document: { getElementById: () => alert },
        SpeechSynthesisUtterance: class { constructor(text) { this.text = text; } },
        Audio: class {
            constructor() { chimes.push(this); }
            play() {}
        },
        console: { warn() {} },
        setTimeout: (callback, delay) => { timers.set(++timerId, { callback, delay }); return timerId; },
        clearTimeout: id => timers.delete(id),
    });
    return {
        window, spoken, chimes, alert, synth,
        cancellations: () => cancellations,
        tick(delay) {
            const timer = [...timers].find(([, timer]) => timer.delay === delay);
            assert.ok(timer, `Expected a ${delay}ms timer`);
            timers.delete(timer[0]);
            timer[1].callback();
        },
    };
}

test('plays speech once after the chime even when its fallback timer fires', () => {
    const b = browser();
    b.window.playHospitalAnnouncement(['Ticket one']);
    assert.equal(b.chimes.length, 1);
    assert.equal(b.spoken.length, 0);
    b.chimes[0].onended();
    b.tick(100);
    b.tick(1350);
    assert.equal(b.spoken.length, 1);
    assert.equal(b.spoken[0].text, 'Ticket one');
});

test('prefers installed English speech over remote voices', () => {
    const local = { name: 'Installed', lang: 'en-US', localService: true };
    const b = browser([{ name: 'Natural', lang: 'en-GB', localService: false }, local]);
    b.window.playHospitalAnnouncement(['Ticket one'], false);
    assert.equal(b.spoken[0].voice, local);
});

test('retries failed speech with the browser default voice', () => {
    const b = browser([{ name: 'Remote', lang: 'en-GB' }]);
    b.window.playHospitalAnnouncement(['Ticket one'], false);
    b.spoken[0].onerror({ error: 'network' });
    b.tick(100);
    assert.equal(b.spoken.length, 2);
    assert.equal(b.spoken[1].text, 'Ticket one');
    assert.equal(b.spoken[1].voice, undefined);
    b.spoken[1].onerror({ error: 'synthesis-unavailable' });
    assert.equal(b.alert.hidden, false);
    assert.match(b.alert.textContent, /English speech voice/);
});

test('retries speech that never starts instead of silently skipping it', () => {
    const b = browser();
    b.window.playHospitalAnnouncement(['Ticket one'], false);
    b.tick(5000);
    b.tick(100);
    assert.equal(b.spoken.length, 2);
    b.spoken[1].onstart();
    b.spoken[1].onend();
    assert.equal(b.window._activeUtterances.length, 0);
});

test('finishes both languages before playing the next announcement', () => {
    const b = browser();
    b.window.playHospitalAnnouncement(['English', 'Twi'], false);
    b.window.playHospitalAnnouncement(['Next ticket'], false);
    assert.equal(b.spoken.length, 1);
    for (let i = 0; i < 2; i++) {
        b.spoken[i].onstart();
        b.spoken[i].onend();
        b.tick(250);
    }
    assert.deepEqual(b.spoken.map(utterance => utterance.text), ['English', 'Twi', 'Next ticket']);
    assert.equal(b.cancellations(), 0);
});

test('shows actionable feedback for blocked and unsupported speech', () => {
    const b = browser();
    b.window.playHospitalAnnouncement(['Ticket one'], false);
    b.spoken[0].onerror({ error: 'not-allowed' });
    assert.match(b.alert.textContent, /Click Play Test Announcement/);
    assert.equal(b.spoken.length, 1);
    const unsupported = browser();
    delete unsupported.window.speechSynthesis;
    unsupported.window.playHospitalAnnouncement(['Ticket one'], false);
    assert.equal(unsupported.alert.hidden, false);
    assert.match(unsupported.alert.textContent, /unavailable in this browser/);
});
