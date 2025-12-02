<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>403 - Akses Ditolak (Comic Edition)</title>
  <link href="https://fonts.googleapis.com/css2?family=Bangers&family=Patrick+Hand&display=swap" rel="stylesheet">

  <style>
    :root {
      --black: #121212;
      --white: #ffffff;
      --border: 4px solid var(--black);
      --shadow: 8px 8px 0px var(--black);
      --shadow-sm: 4px 4px 0px var(--black);
    }

    /* --- LAYOUT UTAMA --- */
    html, body {
      height: 100%;
      margin: 0;
      /* Pola Halftone (Titik-titik komik) pada background */
      background-color: #3a3a3a;
      background-image: radial-gradient(#555 15%, transparent 16%), radial-gradient(#555 15%, transparent 16%);
      background-size: 20px 20px;
      background-position: 0 0, 10px 10px;
      
      font-family: 'Patrick Hand', cursive;
      overflow: hidden; /* Kunci scroll */
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
    }

    /* --- HEADER --- */
    .header {
      margin-bottom: 15px;
      text-align: center;
      z-index: 10;
      transform: rotate(-1deg);
    }
    
    .issue-tag {
      display: inline-block;
      background: #ffeb3b;
      border: var(--border);
      padding: 5px 20px;
      font-family: 'Bangers', cursive;
      font-size: 1.5rem;
      letter-spacing: 1px;
      box-shadow: var(--shadow-sm);
      margin-bottom: 5px;
      transform: rotate(-2deg);
    }

    .title {
      font-family: 'Bangers', cursive;
      font-size: 3.5rem;
      color: var(--white);
      -webkit-text-stroke: 2px var(--black); /* Outline teks */
      text-shadow: 4px 4px 0 var(--black);
      margin: 0;
      letter-spacing: 3px;
      line-height: 1;
    }

    /* --- KOMIK STRIP --- */
    .strip-container {
      background: var(--white);
      padding: 15px;
      border: var(--border);
      box-shadow: var(--shadow);
      width: 95vw;
      max-width: 1400px;
      height: 65vh; /* Tinggi area komik */
      display: flex;
      gap: 15px;
      box-sizing: border-box;
    }

    /* Panel Dasar */
    .panel {
      flex: 1;
      border: 3px solid var(--black);
      position: relative;
      display: flex;
      flex-direction: column;
      justify-content: flex-end;
      align-items: center;
      overflow: hidden;
      transition: transform 0.2s;
    }
    .panel:hover {
      transform: translateY(-5px);
      z-index: 5;
      box-shadow: 0 10px 20px rgba(0,0,0,0.2);
    }

    /* --- TEMA PANEL --- */
    
    /* 1. Software (Grid Hijau Matrix) */
    .p-soft { 
      background-color: #111;
      background-image: linear-gradient(0deg, transparent 24%, rgba(0, 255, 0, .1) 25%, rgba(0, 255, 0, .1) 26%, transparent 27%, transparent 74%, rgba(0, 255, 0, .1) 75%, rgba(0, 255, 0, .1) 76%, transparent 77%, transparent), linear-gradient(90deg, transparent 24%, rgba(0, 255, 0, .1) 25%, rgba(0, 255, 0, .1) 26%, transparent 27%, transparent 74%, rgba(0, 255, 0, .1) 75%, rgba(0, 255, 0, .1) 76%, transparent 77%, transparent);
      background-size: 30px 30px;
    }
    .code-rain {
      position: absolute; top: 0; left: 0; width: 100%; height: 100%;
      font-family: monospace; color: #0f0; opacity: 0.15; font-size: 10px; padding: 5px;
      overflow: hidden;
      word-break: break-all;
    }

    /* 2. Hardware (Garis Bahaya) */
    .p-hard { 
      background: repeating-linear-gradient(
        -45deg,
        #ffcdd2,
        #ffcdd2 15px,
        #ef5350 15px,
        #ef5350 30px
      );
    }
    /* Overlay putih agar karakter terbaca */
    .p-hard::before {
      content: ''; position: absolute; top:0; left:0; width:100%; height:100%;
      background: rgba(255,255,255,0.6); z-index: 0;
    }

    /* 3. Manager (Monitor CRT) */
    .p-mgr { background: #000; }
    /* Efek Scanline Monitor */
    .scanlines {
      position: absolute; top: 0; left: 0; width: 100%; height: 100%;
      background: linear-gradient(rgba(18, 16, 16, 0) 50%, rgba(0, 0, 0, 0.25) 50%), linear-gradient(90deg, rgba(255, 0, 0, 0.06), rgba(0, 255, 0, 0.02), rgba(0, 0, 255, 0.06));
      background-size: 100% 4px, 6px 100%;
      z-index: 1; pointer-events: none;
    }
    .server-line {
      position: absolute; top: 30%; left: 0; width: 100%; height: 100px;
      stroke: #00ff00; stroke-width: 3; fill: none;
      stroke-dasharray: 500; stroke-dashoffset: 500;
      animation: dash 4s linear infinite; filter: drop-shadow(0 0 5px #0f0);
    }
    @keyframes dash { to { stroke-dashoffset: 0; } }

    /* 4. User (Kertas Kusut) */
    .p-user { background: #fff9c4; }

    /* --- ELEMEN KOMIK --- */

    /* SFX Stickers */
    .sfx {
      position: absolute;
      font-family: 'Bangers';
      font-size: 2rem;
      color: #fff;
      -webkit-text-stroke: 1px black;
      padding: 5px 10px;
      border: 3px solid black;
      box-shadow: 5px 5px 0 rgba(0,0,0,0.3);
      z-index: 20;
      top: -10px; right: -10px; /* Keluar panel sedikit */
      transform: rotate(10deg);
      animation: pulse 2s infinite;
    }
    .sfx.left { right: auto; left: -10px; transform: rotate(-10deg); }
    
    @keyframes pulse { 0% { transform: scale(1) rotate(10deg); } 50% { transform: scale(1.1) rotate(10deg); } 100% { transform: scale(1) rotate(10deg); } }

    /* Balon Dialog */
    .bubble {
      background: var(--white);
      border: 3px solid var(--black);
      border-radius: 15px;
      padding: 15px;
      position: absolute;
      top: 60px; /* Aman dari SFX */
      left: 50%; transform: translateX(-50%);
      width: 85%;
      text-align: center;
      font-size: 1rem;
      line-height: 1.3;
      box-shadow: 4px 4px 0 rgba(0,0,0,0.15);
      z-index: 10;
    }
    .bubble::after {
      content: ""; position: absolute; bottom: -12px; left: 50%;
      border: 12px solid transparent; border-top-color: var(--black); transform: translateX(-50%);
    }
    .bubble::before {
      content: ""; position: absolute; bottom: -6px; left: 50%;
      border: 12px solid transparent; border-top-color: var(--white); transform: translateX(-50%); z-index: 1;
    }

    /* Nama Role */
    .role-tag {
      position: absolute; top: 0; left: 0;
      background: var(--black); color: var(--white);
      padding: 2px 8px; font-size: 0.8rem; font-weight: bold;
      z-index: 5; border-bottom-right-radius: 5px;
    }

    /* Karakter */
    .char {
      font-size: 6rem;
      margin-bottom: 10px;
      filter: drop-shadow(5px 5px 0 rgba(0,0,0,0.2));
      z-index: 2;
      transition: transform 0.2s;
      cursor: pointer;
    }
    .char:hover { transform: scale(1.15) rotate(-5deg); }

    /* --- FOOTER --- */
    .footer { margin-top: 25px; }
    
    .btn-comic {
      display: inline-block;
      background: #ff5722;
      color: var(--white);
      font-family: 'Bangers', cursive;
      font-size: 1.8rem;
      letter-spacing: 1px;
      text-decoration: none;
      padding: 10px 40px;
      border: var(--border);
      box-shadow: var(--shadow);
      border-radius: 50px; /* Kapsul */
      transition: all 0.1s;
    }
    .btn-comic:hover {
      transform: translate(-2px, -2px);
      box-shadow: 10px 10px 0 var(--black);
      background: #f44336;
    }
    .btn-comic:active {
      transform: translate(4px, 4px);
      box-shadow: 2px 2px 0 var(--black);
    }

    /* RESPONSIVE (HP) */
    @media (max-width: 900px) {
      html, body { overflow-y: auto; height: auto; padding-bottom: 50px; }
      .strip-container { flex-direction: column; height: auto; padding: 10px; width: 90vw; }
      .panel { height: 300px; margin-bottom: 15px; width: 100%; }
      .title { font-size: 2.5rem; }
      .char { font-size: 5rem; }
      .bubble { top: 50px; font-size: 1rem; }
    }
  </style>
  <link rel="icon" href="logo.ico" type="image/x-icon">
</head>
<body>

  <div class="header">
    <div class="issue-tag">ERROR 403</div>
    <h1 class="title">DINAMIKA TIM IT <</h1>
  </div>

  <div class="strip-container">

    <div class="panel p-soft">
      <div class="role-tag">The Programmer</div>
      <div class="code-rain">
        01001010100101 panic() error: stack_overflow 
        if(user_intruder) { scream("WHY"); }
        git push --force
        // do not touch legacy code
      </div>
      
      <div class="sfx" style="background: #d50000;">BOOM!</div>
      
      <div class="bubble">
        "Laptop gue udah bunyi kayak jet tempur! Jangan pencet apa pun! Satu syntax error lagi, gue banting setir jadi tukang cilok!"
      </div>
      
      <div class="char">🤯💻🔥</div>
    </div>

    <div class="panel p-hard">
      <div class="role-tag">The Security</div>
      <div class="sfx left" style="background: #ff6d00;">ALERT!</div>
      
      <div class="bubble">
        "Woy! Router joget dangdut, Firewall batuk-batuk! Ini serangan DDoS atau tamu nyasar?! Gue cabut kabel LAN satu gedung nih?!"
      </div>
      
      <div class="char">👷🚨🔌</div>
    </div>

    <div class="panel p-mgr">
      <div class="role-tag">The Manager</div>
      <div class="scanlines"></div>
      
      <svg class="server-line" viewBox="0 0 500 100" preserveAspectRatio="none">
         <path d="M0,50 L50,50 L60,10 L70,90 L80,50 L150,50 L160,20 L170,80 L180,50 L300,50 L310,0 L320,100 L330,50 L500,50"></path>
      </svg>
      
      <div class="bubble">
        "Tenang... Grafik server masih hijau. Halo kamu? Tolong mundur perlahan sebelum tim saya jantungan. Kita tetap profesional ya."
      </div>
      
      <div class="char">👩‍💼📊✨</div>
    </div>

    <div class="panel p-user">
      <div class="role-tag">The User (You)</div>
      
      <div class="sfx" style="background: #2962ff;">SWOOSH!</div>
      
      <div class="bubble">
        "AMPUN!! Saya nyerah! Dunia IT terlalu keras buat saya! Saya cuma mau liat gambar kucing, kenapa malah diancam kabel LAN?!"
      </div>
      
      <div class="char" style="transform: skewX(-10deg);">🏃‍♂️💨🚪</div>
    </div>

  </div>

  <div class="footer">
    <a class="btn-comic" href="/">Saya Mundur Teratur!</a>
  </div>

</body>
</html>