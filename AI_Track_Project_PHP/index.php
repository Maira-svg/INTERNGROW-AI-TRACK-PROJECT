<?php
require_once __DIR__ . '/ai_functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    $action = $_POST['action'] ?? '';

    if ($action === 'translate') {
        echo json_encode(ai_translate(
            $_POST['text'] ?? '',
            $_POST['source'] ?? 'en',
            $_POST['target'] ?? 'es'
        ));
        exit;
    }

    if ($action === 'chat') {
        $question = trim($_POST['question'] ?? '');
        if ($question === '') {
            echo json_encode(['error' => 'No question provided']);
            exit;
        }
        static $chatbot = null;
        if ($chatbot === null) $chatbot = new RAGChatbotPHP();
        echo json_encode($chatbot->getResponse($question));
        exit;
    }

    if ($action === 'generate_music') {
        echo json_encode(ai_generate_music(
            $_POST['genre'] ?? 'classical',
            intval($_POST['tempo'] ?? 120),
            $_POST['mood'] ?? 'happy'
        ));
        exit;
    }

    echo json_encode(['error' => 'Unknown action']);
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>AI Track — Console</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Sora:wght@500;600;700&family=Inter:wght@400;500;600&family=IBM+Plex+Mono:wght@400;500&display=swap" rel="stylesheet">
<style>
:root{
  --ink:#12141f;
  --panel:#1b1f2e;
  --panel-raised:#232838;
  --line:#2f3550;
  --amber:#e8a33d;
  --teal:#4fd1c5;
  --red:#e2635a;
  --text:#edeef2;
  --text-dim:#9aa0b4;
  --font-display:'Sora',sans-serif;
  --font-body:'Inter',sans-serif;
  --font-mono:'IBM Plex Mono',monospace;
}
*{box-sizing:border-box;}
body{
  margin:0;
  background:var(--ink);
  background-image:
    radial-gradient(circle at 15% 10%, rgba(232,163,61,0.07), transparent 40%),
    radial-gradient(circle at 85% 90%, rgba(79,209,197,0.06), transparent 40%);
  color:var(--text);
  font-family:var(--font-body);
  min-height:100vh;
  padding:28px 18px 60px;
}
.rack{max-width:980px;margin:0 auto;}
.rack-head{
  display:flex;justify-content:space-between;align-items:flex-end;gap:16px;flex-wrap:wrap;
  margin-bottom:18px;padding-bottom:18px;border-bottom:1px solid var(--line);
}
.rack-head h1{font-family:var(--font-display);font-size:1.9rem;margin:0 0 4px;letter-spacing:-0.02em;}
.rack-head p{margin:0;color:var(--text-dim);font-size:0.9rem;}
.status-led{
  display:flex;align-items:center;gap:8px;font-family:var(--font-mono);font-size:0.78rem;
  color:var(--text-dim);background:var(--panel);border:1px solid var(--line);
  padding:8px 14px;border-radius:999px;
}
.dot{width:8px;height:8px;border-radius:50%;background:var(--teal);box-shadow:0 0 8px var(--teal);}

.channels{display:flex;gap:8px;margin-bottom:20px;border-bottom:1px solid var(--line);}
.channel-btn{
  font-family:var(--font-mono);font-size:0.8rem;background:transparent;border:none;
  color:var(--text-dim);padding:12px 6px;cursor:pointer;border-bottom:2px solid transparent;
  display:flex;align-items:center;gap:8px;
}
.channel-btn .tag{
  font-family:var(--font-mono);font-size:0.68rem;color:var(--ink);background:var(--text-dim);
  padding:1px 6px;border-radius:4px;
}
.channel-btn.active{color:var(--text);border-bottom-color:var(--amber);}
.channel-btn.active .tag{background:var(--amber);}

.panel{background:var(--panel);border:1px solid var(--line);border-radius:14px;padding:26px;display:none;}
.panel.active{display:block;}
.panel h2{font-family:var(--font-display);font-size:1.15rem;margin:0 0 4px;}
.panel .sub{color:var(--text-dim);font-size:0.85rem;margin:0 0 20px;}

.grid{display:grid;grid-template-columns:1fr 1fr;gap:14px;}
@media (max-width:640px){.grid{grid-template-columns:1fr;}}

label{
  display:block;font-family:var(--font-mono);font-size:0.72rem;text-transform:uppercase;
  letter-spacing:0.06em;color:var(--text-dim);margin-bottom:6px;
}
select, textarea, input[type=text], input[type=number]{
  width:100%;background:var(--panel-raised);border:1px solid var(--line);color:var(--text);
  border-radius:8px;padding:11px 12px;font-family:var(--font-body);font-size:0.92rem;
}
select:focus, textarea:focus, input:focus{outline:none;border-color:var(--amber);}
textarea{resize:vertical;}
.field{margin-bottom:14px;}

.run-btn{
  font-family:var(--font-mono);font-size:0.85rem;letter-spacing:0.03em;text-transform:uppercase;
  background:var(--amber);color:#241a05;border:none;padding:13px 22px;border-radius:8px;
  cursor:pointer;font-weight:600;transition:filter .15s;
}
.run-btn:hover{filter:brightness(1.08);}
.run-btn:disabled{opacity:0.5;cursor:not-allowed;}

.readout{
  margin-top:18px;background:var(--panel-raised);border:1px solid var(--line);
  border-left:3px solid var(--line);border-radius:8px;padding:16px;font-size:0.9rem;
  min-height:50px;line-height:1.5;
}
.readout.ok{border-left-color:var(--teal);}
.readout.err{border-left-color:var(--red);color:#f2b6b1;}
.readout .k{color:var(--text-dim);font-family:var(--font-mono);font-size:0.75rem;}

.mic-btn{
  background:var(--panel-raised);border:1px solid var(--line);color:var(--text-dim);
  border-radius:8px;padding:0 14px;cursor:pointer;margin-top:6px;
}
.mic-btn.live{border-color:var(--red);color:var(--red);animation:pulse 1s infinite;}
@keyframes pulse{0%,100%{opacity:1;}50%{opacity:.5;}}

.chat-log{
  max-height:280px;overflow-y:auto;background:var(--panel-raised);border:1px solid var(--line);
  border-radius:8px;padding:14px;display:flex;flex-direction:column;gap:8px;margin-bottom:12px;
}
.bubble{max-width:80%;padding:9px 13px;border-radius:10px;font-size:0.9rem;line-height:1.4;}
.bubble.bot{background:var(--panel);align-self:flex-start;border:1px solid var(--line);}
.bubble.user{background:var(--amber);color:#241a05;align-self:flex-end;font-weight:500;}
.chat-row{display:flex;gap:8px;}
.chat-row input{flex:1;}
.score-line{font-family:var(--font-mono);font-size:0.75rem;color:var(--text-dim);margin-top:8px;}
.meter{width:100%;height:5px;background:var(--panel);border-radius:3px;margin-top:4px;overflow:hidden;}
.meter-fill{height:100%;background:var(--teal);width:0%;transition:width .4s;}

.notes{font-family:var(--font-mono);font-size:0.85rem;color:var(--amber);letter-spacing:0.02em;}
.stack{display:flex;flex-direction:column;gap:4px;margin-top:10px;}
.stack .row{display:flex;justify-content:space-between;font-size:0.85rem;border-bottom:1px dashed var(--line);padding:6px 0;}
.stack .row span:first-child{color:var(--text-dim);font-family:var(--font-mono);font-size:0.75rem;text-transform:uppercase;}

footer{text-align:center;color:var(--text-dim);font-size:0.75rem;font-family:var(--font-mono);margin-top:28px;}
</style>
</head>
<body>

<div class="rack">
  <div class="rack-head">
    <div>
      <h1>AI Track — Console</h1>
      <p>Pure PHP build — no Python backend needed</p>
    </div>
    <div class="status-led">
      <span class="dot"></span>
      <span>PHP ENGINE ONLINE</span>
    </div>
  </div>

  <div class="channels">
    <button class="channel-btn active" data-panel="p1"><span class="tag">01</span> Translate</button>
    <button class="channel-btn" data-panel="p2"><span class="tag">02</span> FAQ Chat</button>
    <button class="channel-btn" data-panel="p3"><span class="tag">03</span> Music Gen</button>
  </div>

  <!-- PANEL 1: Translation -->
  <div class="panel active" id="p1">
    <h2>Voice-enabled translation</h2>
    <p class="sub">Free Google Translate endpoint, called directly from PHP — no API key required.</p>

    <form id="translateForm">
      <div class="grid">
        <div class="field">
          <label>Source language</label>
          <select id="sourceLang">
            <option value="en">English</option><option value="es">Spanish</option>
            <option value="fr">French</option><option value="de">German</option>
            <option value="it">Italian</option><option value="pt">Portuguese</option>
            <option value="ru">Russian</option><option value="zh">Chinese</option>
            <option value="ja">Japanese</option><option value="ar">Arabic</option>
            <option value="hi">Hindi</option><option value="ur">Urdu</option>
          </select>
        </div>
        <div class="field">
          <label>Target language</label>
          <select id="targetLang">
            <option value="es">Spanish</option><option value="en">English</option>
            <option value="fr">French</option><option value="de">German</option>
            <option value="it">Italian</option><option value="pt">Portuguese</option>
            <option value="ru">Russian</option><option value="zh">Chinese</option>
            <option value="ja">Japanese</option><option value="ar">Arabic</option>
            <option value="hi">Hindi</option><option value="ur">Urdu</option>
          </select>
        </div>
      </div>
      <div class="field">
        <label>Text</label>
        <textarea id="translateText" rows="4" placeholder="Type, or press the mic to speak…"></textarea>
        <button type="button" class="mic-btn" id="voiceBtn">🎙 Speak</button>
      </div>
      <button type="submit" class="run-btn" id="translateBtn">Translate</button>
    </form>
    <div class="readout" id="translationOutput"><span class="k">OUTPUT</span><br>Waiting for input…</div>
  </div>

  <!-- PANEL 2: Chatbot -->
  <div class="panel" id="p2">
    <h2>Context-aware FAQ chatbot</h2>
    <p class="sub">Real retrieval: TF-IDF vectors + cosine similarity, computed in PHP against 10 FAQs.</p>

    <div class="chat-log" id="chatLog">
      <div class="bubble bot">Hi — ask me about orders, shipping, returns, payments, or your account.</div>
    </div>
    <div class="chat-row">
      <input type="text" id="chatInput" placeholder="Type your question…">
      <button class="run-btn" id="chatSendBtn">Send</button>
    </div>
    <div class="score-line" id="scoreLine">Match confidence will show here</div>
    <div class="meter"><div class="meter-fill" id="meterFill"></div></div>
  </div>

  <!-- PANEL 3: Music generator -->
  <div class="panel" id="p3">
    <h2>AI music pattern generator</h2>
    <p class="sub">Rule-based composition assistant — suggests style, chord progression, and an 8-note melody.</p>

    <form id="musicForm">
      <div class="grid">
        <div class="field">
          <label>Genre</label>
          <select id="musicGenre">
            <option value="classical">Classical</option><option value="jazz">Jazz</option>
            <option value="electronic">Electronic</option><option value="rock">Rock</option>
            <option value="ambient">Ambient</option>
          </select>
        </div>
        <div class="field">
          <label>Tempo (BPM)</label>
          <input type="number" id="musicTempo" value="120" min="40" max="220">
        </div>
      </div>
      <div class="field">
        <label>Mood</label>
        <select id="musicMood">
          <option value="happy">Happy</option><option value="sad">Sad</option>
          <option value="energetic">Energetic</option><option value="calm">Calm</option>
          <option value="mysterious">Mysterious</option>
        </select>
      </div>
      <button type="submit" class="run-btn" id="musicBtn">Generate</button>
    </form>
    <div class="readout" id="musicOutput"><span class="k">OUTPUT</span><br>Waiting for input…</div>
  </div>

  <footer>All AI logic runs inside this PHP file — no separate server needed.</footer>
</div>

<script>
document.querySelectorAll('.channel-btn').forEach(btn => {
  btn.addEventListener('click', () => {
    document.querySelectorAll('.channel-btn').forEach(b => b.classList.remove('active'));
    document.querySelectorAll('.panel').forEach(p => p.classList.remove('active'));
    btn.classList.add('active');
    document.getElementById(btn.dataset.panel).classList.add('active');
  });
});

// ---- Task 1: Translate ----
document.getElementById('translateForm').addEventListener('submit', async function(e){
  e.preventDefault();
  const text = document.getElementById('translateText').value.trim();
  const source = document.getElementById('sourceLang').value;
  const target = document.getElementById('targetLang').value;
  const output = document.getElementById('translationOutput');
  const btn = document.getElementById('translateBtn');

  if (!text) {
    output.className = 'readout err';
    output.innerHTML = '<span class="k">ERROR</span><br>Please enter some text.';
    return;
  }

  btn.disabled = true; btn.textContent = 'Translating…';
  output.className = 'readout';
  output.innerHTML = '<span class="k">OUTPUT</span><br>Loading…';

  try {
    const fd = new FormData();
    fd.append('action','translate'); fd.append('text',text);
    fd.append('source',source); fd.append('target',target);
    const res = await fetch(window.location.href, {method:'POST', body:fd});
    const result = await res.json();

    if (result.error) {
      output.className = 'readout err';
      output.innerHTML = '<span class="k">ERROR</span><br>' + result.error;
    } else {
      output.className = 'readout ok';
      output.innerHTML = '<span class="k">TRANSLATION</span><br>' + result.translation;
    }
  } catch (err) {
    output.className = 'readout err';
    output.innerHTML = '<span class="k">ERROR</span><br>Network error contacting server.';
  }

  btn.disabled = false; btn.textContent = 'Translate';
});

// ---- Voice input ----
if ('webkitSpeechRecognition' in window || 'SpeechRecognition' in window) {
  const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
  const recognition = new SpeechRecognition();
  recognition.lang = 'en-US';
  const voiceBtn = document.getElementById('voiceBtn');

  voiceBtn.addEventListener('click', () => {
    voiceBtn.classList.toggle('live');
    if (voiceBtn.classList.contains('live')) {
      voiceBtn.textContent = '⏹ Stop';
      recognition.start();
    } else {
      recognition.stop();
      voiceBtn.textContent = '🎙 Speak';
    }
  });

  recognition.onresult = (event) => {
    document.getElementById('translateText').value = event.results[0][0].transcript;
    voiceBtn.classList.remove('live');
    voiceBtn.textContent = '🎙 Speak';
    document.getElementById('translateForm').dispatchEvent(new Event('submit'));
  };
  recognition.onerror = () => {
    voiceBtn.classList.remove('live');
    voiceBtn.textContent = '🎙 Speak';
  };
} else {
  document.getElementById('voiceBtn').style.display = 'none';
}

// ---- Task 2: Chatbot ----
const chatLog = document.getElementById('chatLog');
const chatInput = document.getElementById('chatInput');
const chatSendBtn = document.getElementById('chatSendBtn');
const scoreLine = document.getElementById('scoreLine');
const meterFill = document.getElementById('meterFill');

function addBubble(text, who){
  const b = document.createElement('div');
  b.className = 'bubble ' + who;
  b.textContent = text;
  chatLog.appendChild(b);
  chatLog.scrollTop = chatLog.scrollHeight;
}

async function sendChat(){
  const q = chatInput.value.trim();
  if (!q) return;
  addBubble(q, 'user');
  chatInput.value = '';
  chatSendBtn.disabled = true; chatSendBtn.textContent = '…';

  try {
    const fd = new FormData();
    fd.append('action','chat'); fd.append('question', q);
    const res = await fetch(window.location.href, {method:'POST', body:fd});
    const result = await res.json();

    if (result.error) {
      addBubble('Error: ' + result.error, 'bot');
    } else {
      addBubble(result.answer, 'bot');
      scoreLine.textContent = 'Match confidence: ' + result.score + '%' +
        (result.matched_question ? ' — matched "' + result.matched_question + '"' : '');
      meterFill.style.width = Math.min(result.score, 100) + '%';
    }
  } catch (err) {
    addBubble('Network error reaching backend.', 'bot');
  }

  chatSendBtn.disabled = false; chatSendBtn.textContent = 'Send';
}

chatSendBtn.addEventListener('click', sendChat);
chatInput.addEventListener('keydown', (e) => { if (e.key === 'Enter') sendChat(); });

// ---- Task 3: Music generator ----
document.getElementById('musicForm').addEventListener('submit', async function(e){
  e.preventDefault();
  const genre = document.getElementById('musicGenre').value;
  const tempo = document.getElementById('musicTempo').value;
  const mood = document.getElementById('musicMood').value;
  const output = document.getElementById('musicOutput');
  const btn = document.getElementById('musicBtn');

  btn.disabled = true; btn.textContent = 'Generating…';
  output.className = 'readout';
  output.innerHTML = '<span class="k">OUTPUT</span><br>Loading…';

  try {
    const fd = new FormData();
    fd.append('action','generate_music'); fd.append('genre',genre);
    fd.append('tempo',tempo); fd.append('mood',mood);
    const res = await fetch(window.location.href, {method:'POST', body:fd});
    const result = await res.json();

    if (result.error) {
      output.className = 'readout err';
      output.innerHTML = '<span class="k">ERROR</span><br>' + result.error;
    } else {
      output.className = 'readout ok';
      output.innerHTML = `
        <span class="k">PATTERN</span>
        <div class="stack">
          <div class="row"><span>Genre</span><span>${result.genre}</span></div>
          <div class="row"><span>Tempo</span><span>${result.tempo} BPM</span></div>
          <div class="row"><span>Mood</span><span>${result.mood}</span></div>
          <div class="row"><span>Style</span><span>${result.suggested_style}</span></div>
          <div class="row"><span>Chords</span><span>${result.chord_progression}</span></div>
        </div>
        <div class="notes">${result.melody_notes}</div>`;
    }
  } catch (err) {
    output.className = 'readout err';
    output.innerHTML = '<span class="k">ERROR</span><br>Network error contacting server.';
  }

  btn.disabled = false; btn.textContent = 'Generate';
});
</script>
</body>
</html>
