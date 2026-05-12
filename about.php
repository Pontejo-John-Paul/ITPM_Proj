<?php
session_start();
require_once 'database.php';

// Guard
if (!isset($_SESSION['student_id']) || $_SESSION['role'] !== 'student') {
    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>E-KINDER — About</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Fredoka+One&family=Nunito:wght@400;600;700;800;900&display=swap" rel="stylesheet">

<?php include 'navbar-styles.php'; ?>

<style>
:root { --green-dark: #0d5407; --green-mid: #1a7a10; --cream: #fffbf4; }
* { box-sizing: border-box; margin: 0; padding: 0; }
body { font-family: 'Nunito', sans-serif; background: var(--cream); overflow-x: hidden; }

/* PAGE HEADER */
.page-header { background: linear-gradient(160deg, #e8f5e9 0%, #fffbf4 50%, #e3f2fd 100%); padding: 72px 20px 90px; text-align: center; position: relative; overflow: hidden; }
.blob { position:absolute; border-radius:50%; filter:blur(70px); opacity:0.32; pointer-events:none; }
.blob-1 { width:320px; height:320px; background:#a8edba; top:-80px; left:-100px; }
.blob-2 { width:260px; height:260px; background:#ffd6e7; top:-40px; right:-60px; }
.blob-3 { width:180px; height:180px; background:#c5f6fa; bottom:-50px; left:35%; }
.page-badge { display:inline-block; background:#fff; border:2.5px solid #c8e6c9; border-radius:50px; padding:6px 20px; font-size:0.82rem; font-weight:800; color:var(--green-dark); letter-spacing:1.5px; text-transform:uppercase; margin-bottom:20px; box-shadow:0 4px 14px rgba(13,84,7,0.1); position:relative; z-index:1; }
.page-header h1 { font-family:'Fredoka One',cursive; font-size:clamp(2.4rem,6vw,4rem); color:var(--green-dark); line-height:1.1; margin-bottom:16px; position:relative; z-index:1; }
.page-header h1 span { color:#e53935; }
.page-header p { font-size:1.05rem; color:#666; font-weight:700; max-width:540px; margin:0 auto; position:relative; z-index:1; }
.float-emoji { position:absolute; animation:floatBounce 3s ease-in-out infinite; pointer-events:none; z-index:1; user-select:none; }
.fe1{top:12%;left:4%;font-size:2.8rem;animation-delay:0s;}
.fe2{top:18%;right:5%;font-size:2.2rem;animation-delay:.6s;}
.fe3{bottom:16%;left:7%;font-size:2rem;animation-delay:1.2s;}
.fe4{bottom:14%;right:6%;font-size:2.4rem;animation-delay:.9s;}
.fe5{top:50%;left:2%;font-size:1.6rem;animation-delay:1.7s;}
.fe6{top:45%;right:2%;font-size:1.8rem;animation-delay:2.1s;}
@keyframes floatBounce{0%,100%{transform:translateY(0)rotate(-5deg);}50%{transform:translateY(-16px)rotate(5deg);}}

/* WAVY DIVIDER */
.wavy { display:block; width:100%; line-height:0; }
.wavy svg { display:block; width:100%; }

/* SECTION LABEL */
.section-label{display:flex;align-items:center;gap:12px;margin-bottom:40px;}
.section-label-line{flex:1;height:2px;background:linear-gradient(90deg,#e0e0e0,transparent);}
.section-label-line.right{background:linear-gradient(90deg,transparent,#e0e0e0);}
.section-label-text{font-family:'Fredoka One',cursive;font-size:1.05rem;color:#bbb;letter-spacing:2px;white-space:nowrap;}

/* WHAT IS E-KINDER */
.what-section { padding: 80px 0 60px; background: var(--cream); }
.what-card { background: #fff; border-radius: 32px; padding: 48px 40px; border: 2.5px solid #e9ecef; box-shadow: 0 8px 32px rgba(0,0,0,0.06); position: relative; overflow: hidden; }
.what-card::before { content: 'E-KINDER'; position: absolute; font-family: 'Fredoka One', cursive; font-size: 8rem; color: rgba(13,84,7,0.04); bottom: -20px; right: 20px; line-height: 1; pointer-events: none; user-select: none; }
.what-title { font-family:'Fredoka One',cursive; font-size:clamp(1.6rem,3vw,2.4rem); color:var(--green-dark); margin-bottom:16px; }
.what-text { font-size:1rem; color:#555; font-weight:700; line-height:1.8; margin-bottom: 16px; }
.what-highlights { display: flex; flex-wrap: wrap; gap: 12px; margin-top: 24px; }
.what-pill { background: #f0fdf4; border: 2px solid #bbf7d0; border-radius: 50px; padding: 8px 20px; font-family:'Fredoka One',cursive; font-size:0.9rem; color:var(--green-dark); display: flex; align-items: center; gap: 8px; }

/* HOW IT WORKS */
.steps-section { padding: 80px 0; background: #f8fffe; }
.step-block { display: flex; align-items: flex-start; gap: 28px; background: #fff; border-radius: 28px; padding: 32px 28px; border: 2.5px solid #e9ecef; box-shadow: 0 6px 24px rgba(0,0,0,0.05); transition: transform 0.25s cubic-bezier(.34,1.56,.64,1), box-shadow 0.25s; animation: revealUp 0.5s ease both; }
.step-block:hover { transform: translateY(-6px); box-shadow: 0 18px 40px rgba(0,0,0,0.1); }
@keyframes revealUp { from{opacity:0;transform:translateY(30px);} to{opacity:1;transform:translateY(0);} }
.step-block:nth-child(1){animation-delay:.05s;} .step-block:nth-child(2){animation-delay:.12s;}
.step-block:nth-child(3){animation-delay:.19s;} .step-block:nth-child(4){animation-delay:.26s;}
.step-block:nth-child(5){animation-delay:.33s;} .step-block:nth-child(6){animation-delay:.40s;}
.step-num-big { width: 64px; height: 64px; border-radius: 50%; flex-shrink: 0; display: flex; align-items: center; justify-content: center; font-family: 'Fredoka One', cursive; font-size: 1.8rem; color: #fff; box-shadow: 0 6px 18px rgba(0,0,0,0.15); }
.s-pink   { background: linear-gradient(135deg,#f06595,#e64980); }
.s-blue   { background: linear-gradient(135deg,#339af0,#1c7ed6); }
.s-yellow { background: linear-gradient(135deg,#fcc419,#f59f00); }
.s-green  { background: linear-gradient(135deg,#51cf66,#2f9e44); }
.s-purple { background: linear-gradient(135deg,#9775fa,#7048e8); }
.s-orange { background: linear-gradient(135deg,#ff922b,#d9480f); }
.step-body { flex: 1; }
.step-label { display: inline-block; font-size: 0.7rem; font-weight: 900; letter-spacing: 1.5px; text-transform: uppercase; padding: 2px 10px; border-radius: 50px; margin-bottom: 8px; }
.step-title { font-family:'Fredoka One',cursive; font-size:1.25rem; color:#222; margin-bottom:6px; }
.step-desc  { font-size:0.88rem; color:#777; font-weight:700; line-height:1.6; }

/* BADGES SECTION */
.badges-section { background: var(--green-dark); padding: 80px 0; position: relative; overflow: hidden; }
.badges-section::before { content:'🏆'; position:absolute; font-size:280px; opacity:0.04; top:-40px; right:-30px; pointer-events:none; line-height:1; }
.badge-how-title { font-family:'Fredoka One',cursive; font-size:clamp(1.8rem,4vw,2.8rem); color:#fff; margin-bottom:12px; }
.badge-how-sub { font-size:1rem; color:rgba(255,255,255,0.7); font-weight:700; margin-bottom:48px; }
.badge-step-row { display: grid; grid-template-columns: repeat(4,1fr); gap: 20px; position: relative; }
.badge-step-row::before { content:''; position:absolute; top:36px; left:10%; right:10%; height:3px; background: rgba(255,255,255,0.15); border-radius:50px; z-index:0; }
@media(max-width:768px){ .badge-step-row{ grid-template-columns:repeat(2,1fr); } .badge-step-row::before{display:none;} }
@media(max-width:480px){ .badge-step-row{ grid-template-columns:1fr; } }
.badge-step { background: rgba(255,255,255,0.08); border: 2px solid rgba(255,255,255,0.15); border-radius: 24px; padding: 28px 18px 24px; text-align: center; position: relative; z-index: 1; transition: background 0.25s, transform 0.25s; animation: revealUp 0.5s ease both; }
.badge-step:hover { background: rgba(255,255,255,0.14); transform:translateY(-6px); }
.badge-step:nth-child(1){animation-delay:.06s;} .badge-step:nth-child(2){animation-delay:.14s;}
.badge-step:nth-child(3){animation-delay:.22s;} .badge-step:nth-child(4){animation-delay:.30s;}
.badge-step-icon { font-size: 2.6rem; display:block; margin-bottom:14px; animation: floatBounce 3s ease-in-out infinite; }
.badge-step:nth-child(2) .badge-step-icon{animation-delay:.5s;} .badge-step:nth-child(3) .badge-step-icon{animation-delay:1s;} .badge-step:nth-child(4) .badge-step-icon{animation-delay:1.5s;}
.badge-step-num { position:absolute; top:-14px; left:50%; transform:translateX(-50%); background:#fcc419; color:#333; font-family:'Fredoka One',cursive; font-size:0.85rem; padding:3px 14px; border-radius:50px; box-shadow:0 4px 12px rgba(0,0,0,0.2); }
.badge-step-title { font-family:'Fredoka One',cursive; font-size:1rem; color:#fff; margin-bottom:6px; }
.badge-step-text  { font-size:0.8rem; color:rgba(255,255,255,0.65); font-weight:700; line-height:1.5; }

/* FEATURES */
.features-section { padding: 80px 0; background: var(--cream); }
.feature-card { border-radius: 28px; padding: 36px 28px; text-align:center; position: relative; overflow: hidden; border: 2.5px solid transparent; box-shadow: 0 6px 24px rgba(0,0,0,0.06); transition: transform 0.25s cubic-bezier(.34,1.56,.64,1), box-shadow 0.25s; animation: revealUp 0.5s ease both; }
.feature-card::before { content:''; position:absolute; top:0; left:0; right:0; height:5px; background:var(--fc-color); border-radius:28px 28px 0 0; }
.feature-card::after { content:''; position:absolute; bottom:-30px; right:-30px; width:110px; height:110px; border-radius:50%; background:var(--fc-color); opacity:0.1; pointer-events:none; }
.feature-card:hover { transform:translateY(-8px) scale(1.02); box-shadow:0 20px 44px rgba(0,0,0,0.11); }
.feature-card:nth-child(1){animation-delay:.04s;} .feature-card:nth-child(2){animation-delay:.10s;}
.feature-card:nth-child(3){animation-delay:.16s;} .feature-card:nth-child(4){animation-delay:.22s;}
.feature-card:nth-child(5){animation-delay:.28s;} .feature-card:nth-child(6){animation-delay:.34s;}
.feature-emoji { font-size:2.8rem; display:block; margin-bottom:14px; transition: transform 0.3s cubic-bezier(.34,1.56,.64,1); }
.feature-card:hover .feature-emoji { transform:scale(1.2) rotate(-10deg); }
.feature-title { font-family:'Fredoka One',cursive; font-size:1.15rem; color:#222; margin-bottom:8px; }
.feature-text  { font-size:0.84rem; color:#777; font-weight:700; line-height:1.6; }
.feature-tag { display:inline-block; margin-top:14px; padding:4px 14px; border-radius:50px; font-size:0.72rem; font-weight:800; text-transform:uppercase; letter-spacing:0.5px; background:var(--fc-bg); color:var(--fc-tag-color); }
.fc1{background:#fff0f6;--fc-color:#f06595;--fc-bg:#ffe0eb;--fc-tag-color:#c2255c;}
.fc2{background:#e7f5ff;--fc-color:#339af0;--fc-bg:#d0ebff;--fc-tag-color:#1971c2;}
.fc3{background:#fff9db;--fc-color:#fcc419;--fc-bg:#fff3bf;--fc-tag-color:#e67700;}
.fc4{background:#ebfbee;--fc-color:#51cf66;--fc-bg:#d3f9d8;--fc-tag-color:#2f9e44;}
.fc5{background:#f3f0ff;--fc-color:#9775fa;--fc-bg:#e5dbff;--fc-tag-color:#6741d9;}
.fc6{background:#fff4e6;--fc-color:#ff922b;--fc-bg:#ffe8cc;--fc-tag-color:#d9480f;}

/* LESSONS OVERVIEW */
.lessons-overview { padding: 80px 0; background: #f8fffe; }
.lesson-pill-row { display: flex; flex-wrap: wrap; gap: 14px; justify-content: center; margin-bottom: 48px; }
.lesson-pill-card { background: #fff; border-radius: 20px; padding: 18px 22px; display: flex; align-items: center; gap: 14px; border: 2.5px solid #e9ecef; box-shadow: 0 4px 16px rgba(0,0,0,0.05); transition: transform 0.2s cubic-bezier(.34,1.56,.64,1), box-shadow 0.2s; animation: revealUp 0.5s ease both; min-width: 180px; }
.lesson-pill-card:hover { transform:translateY(-5px) scale(1.03); box-shadow:0 14px 32px rgba(0,0,0,0.1); }
.lesson-pill-card:nth-child(1){animation-delay:.04s;} .lesson-pill-card:nth-child(2){animation-delay:.08s;}
.lesson-pill-card:nth-child(3){animation-delay:.12s;} .lesson-pill-card:nth-child(4){animation-delay:.16s;}
.lesson-pill-card:nth-child(5){animation-delay:.20s;} .lesson-pill-card:nth-child(6){animation-delay:.24s;}
.lesson-pill-card:nth-child(7){animation-delay:.28s;} .lesson-pill-card:nth-child(8){animation-delay:.32s;}
.lpc-icon { font-size:1.8rem; }
.lpc-name { font-family:'Fredoka One',cursive; font-size:0.95rem; color:#222; }
.lpc-tag  { font-size:0.7rem; font-weight:800; color:#aaa; text-transform:uppercase; letter-spacing:0.5px; }

/* CTA */
.cta-section { padding: 80px 0 90px; background: linear-gradient(135deg, #ebfbee, #e7f5ff); }
.cta-inner { background: var(--green-dark); border-radius: 32px; padding: 60px 40px; text-align:center; position:relative; overflow:hidden; }
.cta-inner::before { content:'🌈'; position:absolute; font-size:220px; opacity:0.05; bottom:-40px; left:-20px; pointer-events:none; line-height:1; }
.cta-inner::after  { content:'⭐'; position:absolute; font-size:180px; opacity:0.05; top:-30px; right:-10px; pointer-events:none; line-height:1; }
.cta-title { font-family:'Fredoka One',cursive; font-size:clamp(1.8rem,4vw,3rem); color:#fff; margin-bottom:14px; }
.cta-sub   { font-size:1rem; font-weight:700; color:rgba(255,255,255,0.75); margin-bottom:36px; max-width:480px; margin-left:auto; margin-right:auto; }
.cta-btns  { display:flex; gap:16px; justify-content:center; flex-wrap:wrap; }
.cta-btn-white { background:#fff; color:var(--green-dark); font-family:'Fredoka One',cursive; font-size:1rem; padding:14px 32px; border-radius:50px; text-decoration:none; box-shadow:0 8px 24px rgba(0,0,0,0.15); transition:transform 0.2s cubic-bezier(.34,1.56,.64,1), box-shadow 0.2s; display:inline-block; }
.cta-btn-white:hover { transform:scale(1.07); box-shadow:0 14px 32px rgba(0,0,0,0.2); color:var(--green-dark); }
.cta-btn-outline { background:transparent; color:#fff; font-family:'Fredoka One',cursive; font-size:1rem; padding:14px 32px; border-radius:50px; text-decoration:none; border:2.5px solid rgba(255,255,255,0.45); transition:background 0.2s, transform 0.2s; display:inline-block; }
.cta-btn-outline:hover { background:rgba(255,255,255,0.12); color:#fff; transform:scale(1.05); }

/* FOOTER */
footer { background: var(--green-dark); color: rgba(255,255,255,0.7); text-align: center; padding: 32px 20px; font-size: 0.85rem; font-weight: 700; border-top: 3px solid rgba(255,255,255,0.08); }
footer .footer-brand { font-family:'Fredoka One',cursive; font-size:1.6rem; color:#fff; display:block; margin-bottom:8px; }
footer a { color:rgba(255,255,255,0.5); text-decoration:none; margin:0 8px; }
footer a:hover { color:#fff; }
</style>
</head>
<body>

<?php include 'navbar.php'; ?>

<!-- PAGE HEADER -->
<section class="page-header">
    <div class="blob blob-1"></div>
    <div class="blob blob-2"></div>
    <div class="blob blob-3"></div>
    <span class="float-emoji fe1">📖</span>
    <span class="float-emoji fe2">💡</span>
    <span class="float-emoji fe3">🌟</span>
    <span class="float-emoji fe4">🎓</span>
    <span class="float-emoji fe5">🇵🇭</span>
    <span class="float-emoji fe6">❤️</span>
    <div class="page-badge">ℹ️ About E-KINDER</div>
    <h1>What is <span>E-KINDER</span>?</h1>
    <p>Your fun, colorful, and interactive learning buddy — made just for Filipino kids! 🌈</p>
</section>

<!-- WHAT IS E-KINDER -->
<section class="what-section">
    <div class="container">
        <div class="section-label">
            <div class="section-label-line"></div>
            <span class="section-label-text">📚 OUR STORY</span>
            <div class="section-label-line right"></div>
        </div>
        <div class="what-card">
            <h2 class="what-title">Hello! We're E-KINDER! 👋</h2>
            <p class="what-text">E-KINDER is a <strong>free online learning platform</strong> made especially for Filipino kindergarteners aged 5 to 7 years old. We believe every child deserves a fun and colorful way to learn — and that's exactly what we built!</p>
            <p class="what-text">Our lessons cover everything a kindergartener needs — from <strong>Letters and Numbers</strong> to <strong>Animals, Colors, Shapes</strong>, and even <strong>Filipino National Heroes</strong>. Everything is presented in a playful, easy-to-understand way that kids love. 💛</p>
            <p class="what-text">Best of all? E-KINDER works in both <strong>English and Filipino (Tagalog)</strong>, so learners can study in the language they feel most comfortable in — or practice both!</p>
            <div class="what-highlights">
                <div class="what-pill">✅ 100% Free</div>
                <div class="what-pill">🇵🇭 Bilingual (EN & TL)</div>
                <div class="what-pill">🎮 Game-Based Learning</div>
                <div class="what-pill">📱 Works on Any Device</div>
                <div class="what-pill">🏅 Earn Badges</div>
                <div class="what-pill">👶 Ages 5–7</div>
            </div>
        </div>
    </div>
</section>

<div class="wavy" style="background:var(--cream);">
    <svg viewBox="0 0 1440 60" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none">
        <path d="M0,30 C240,60 480,0 720,30 C960,60 1200,0 1440,30 L1440,60 L0,60 Z" fill="#f8fffe"/>
    </svg>
</div>

<!-- HOW IT WORKS -->
<section class="steps-section">
    <div class="container">
        <div class="section-label">
            <div class="section-label-line"></div>
            <span class="section-label-text">🚀 HOW IT WORKS</span>
            <div class="section-label-line right"></div>
        </div>
        <div class="text-center mb-5">
            <h2 style="font-family:'Fredoka One',cursive;font-size:clamp(1.6rem,4vw,2.4rem);color:#333;">It's as easy as <span style="color:#51cf66;">1, 2, 3!</span> 🎉</h2>
        </div>
        <div class="row g-4">
            <div class="col-md-6 col-lg-4"><div class="step-block"><div class="step-num-big s-pink">1</div><div class="step-body"><span class="step-label" style="background:#fff0f6;color:#c2255c;">Start Here</span><div class="step-title">Open E-KINDER</div><p class="step-desc">Log in to your account and land on the Home page. You'll see a colorful welcome screen just for you! Say hello! 👋</p></div></div></div>
            <div class="col-md-6 col-lg-4"><div class="step-block"><div class="step-num-big s-blue">2</div><div class="step-body"><span class="step-label" style="background:#e7f5ff;color:#1971c2;">Choose</span><div class="step-title">Pick a Lesson</div><p class="step-desc">Go to <strong>Lessons</strong> in the menu and pick any topic you like — Letters, Animals, Numbers, and more! Tap the one that excites you. 🎯</p></div></div></div>
            <div class="col-md-6 col-lg-4"><div class="step-block"><div class="step-num-big s-yellow">3</div><div class="step-body"><span class="step-label" style="background:#fff9db;color:#e67700;">Watch</span><div class="step-title">Watch &amp; Listen</div><p class="step-desc">Each lesson shows you pictures, words, and plays sounds. Watch carefully and listen to how things are said in English or Filipino! 👀🔊</p></div></div></div>
            <div class="col-md-6 col-lg-4"><div class="step-block"><div class="step-num-big s-green">4</div><div class="step-body"><span class="step-label" style="background:#ebfbee;color:#2f9e44;">Practice</span><div class="step-title">Try the Activities</div><p class="step-desc">Now it's your turn! Go to <strong>Activities</strong> and try the exercises for each lesson. Tap, spell, match — and have fun doing it! 🕹️</p></div></div></div>
            <div class="col-md-6 col-lg-4"><div class="step-block"><div class="step-num-big s-purple">5</div><div class="step-body"><span class="step-label" style="background:#f3f0ff;color:#6741d9;">Progress</span><div class="step-title">Check Your Progress</div><p class="step-desc">See how much you've learned! Every activity you finish adds to your progress bar. Watch it fill up all the way to 100%! 📊✨</p></div></div></div>
            <div class="col-md-6 col-lg-4"><div class="step-block"><div class="step-num-big s-orange">6</div><div class="step-body"><span class="step-label" style="background:#fff4e6;color:#d9480f;">Reward</span><div class="step-title">Earn Your Badge! 🏅</div><p class="step-desc">Finish ALL activities in a lesson and you'll earn a special <strong>Badge</strong>! Collect all 8 badges to become an E-KINDER Superstar! ⭐</p></div></div></div>
        </div>
    </div>
</section>

<div class="wavy" style="background:#f8fffe;">
    <svg viewBox="0 0 1440 60" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none">
        <path d="M0,0 C240,50 480,10 720,40 C960,70 1200,10 1440,40 L1440,60 L0,60 Z" fill="#0d5407"/>
    </svg>
</div>

<!-- HOW TO EARN BADGES -->
<section class="badges-section">
    <div class="container">
        <div class="text-center mb-2">
            <h2 class="badge-how-title">How Do You Earn a Badge? 🏅</h2>
            <p class="badge-how-sub">Follow these 4 steps for every lesson and your badge will be waiting for you!</p>
        </div>
        <div class="badge-step-row">
            <div class="badge-step"><span class="badge-step-num">Step 1</span><span class="badge-step-icon">📖</span><div class="badge-step-title">Learn the Lesson</div><p class="badge-step-text">Open the lesson page and go through all the content. Look at the pictures and listen to the words!</p></div>
            <div class="badge-step"><span class="badge-step-num">Step 2</span><span class="badge-step-icon">🕹️</span><div class="badge-step-title">Do the Activities</div><p class="badge-step-text">Go to Activities and complete every exercise for that lesson — matching, quizzes, sorting, and more!</p></div>
            <div class="badge-step"><span class="badge-step-num">Step 3</span><span class="badge-step-icon">📊</span><div class="badge-step-title">Reach 100%</div><p class="badge-step-text">Once your progress bar hits 100% for a lesson, the system knows you're ready to be rewarded!</p></div>
            <div class="badge-step"><span class="badge-step-num">Step 4</span><span class="badge-step-icon">🏅</span><div class="badge-step-title">Badge Unlocked!</div><p class="badge-step-text">Your badge appears in the Badges page. Collect all 8 to become a certified E-KINDER Superstar! 🌟</p></div>
        </div>
        <div class="text-center mt-5">
            <a href="badges.php" style="display:inline-block;background:#fcc419;color:#333;font-family:'Fredoka One',cursive;font-size:1rem;padding:14px 36px;border-radius:50px;text-decoration:none;box-shadow:0 8px 24px rgba(0,0,0,0.2);transition:transform 0.2s;" onmouseover="this.style.transform='scale(1.07)'" onmouseout="this.style.transform='scale(1)'">🏅 View My Badges</a>
        </div>
    </div>
</section>

<div class="wavy" style="background:#0d5407;">
    <svg viewBox="0 0 1440 60" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none">
        <path d="M0,40 C360,0 720,60 1080,20 C1260,0 1380,50 1440,30 L1440,60 L0,60 Z" fill="var(--cream)"/>
    </svg>
</div>

<!-- FEATURES -->
<section class="features-section">
    <div class="container">
        <div class="section-label">
            <div class="section-label-line"></div>
            <span class="section-label-text">⚡ FEATURES</span>
            <div class="section-label-line right"></div>
        </div>
        <div class="text-center mb-5">
            <h2 style="font-family:'Fredoka One',cursive;font-size:clamp(1.6rem,4vw,2.4rem);color:#333;">What Makes E-KINDER Special? ✨</h2>
        </div>
        <div class="row g-4">
            <div class="col-md-6 col-lg-4"><div class="feature-card fc1"><span class="feature-emoji">🔊</span><div class="feature-title">Audio Pronunciation</div><p class="feature-text">Every word and letter is spoken out loud clearly! Kids can hear the correct pronunciation in both English and Filipino. Great for early readers!</p><span class="feature-tag">Speech & Audio</span></div></div>
            <div class="col-md-6 col-lg-4"><div class="feature-card fc2"><span class="feature-emoji">🌏</span><div class="feature-title">Bilingual Support</div><p class="feature-text">Switch between English 🇺🇸 and Filipino 🇵🇭 with one tap! All lessons, words, and activities are available in both languages.</p><span class="feature-tag">EN & Filipino</span></div></div>
            <div class="col-md-6 col-lg-4"><div class="feature-card fc3"><span class="feature-emoji">📊</span><div class="feature-title">Progress Tracking</div><p class="feature-text">Watch your progress bar fill up as you complete activities! Kids stay motivated when they can see how much they've already learned.</p><span class="feature-tag">Activities Page</span></div></div>
            <div class="col-md-6 col-lg-4"><div class="feature-card fc4"><span class="feature-emoji">🏅</span><div class="feature-title">Badge Rewards</div><p class="feature-text">Earn a special badge for every lesson completed at 100%! Badges are a fun way to celebrate hard work and motivate kids to keep going.</p><span class="feature-tag">Badges Page</span></div></div>
            <div class="col-md-6 col-lg-4"><div class="feature-card fc5"><span class="feature-emoji">📱</span><div class="feature-title">Works on Any Device</div><p class="feature-text">Whether you're on a phone, tablet, or computer — E-KINDER looks great and works perfectly on all screen sizes. Learn anywhere, anytime!</p><span class="feature-tag">Mobile Friendly</span></div></div>
            <div class="col-md-6 col-lg-4"><div class="feature-card fc6"><span class="feature-emoji">🎮</span><div class="feature-title">Interactive &amp; Playful</div><p class="feature-text">Tap, drag, spell, and match! Every lesson is designed like a mini-game so kids learn without even realizing they're studying. 100% fun guaranteed!</p><span class="feature-tag">Game-Based</span></div></div>
        </div>
    </div>
</section>

<div class="wavy" style="background:var(--cream);">
    <svg viewBox="0 0 1440 60" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none">
        <path d="M0,20 C300,60 600,0 900,40 C1100,65 1300,10 1440,30 L1440,60 L0,60 Z" fill="#f8fffe"/>
    </svg>
</div>

<!-- LESSONS OVERVIEW -->
<section class="lessons-overview">
    <div class="container">
        <div class="section-label">
            <div class="section-label-line"></div>
            <span class="section-label-text">📚 ALL 8 LESSONS</span>
            <div class="section-label-line right"></div>
        </div>
        <div class="text-center mb-5">
            <h2 style="font-family:'Fredoka One',cursive;font-size:clamp(1.6rem,4vw,2.4rem);color:#333;">What Will You Learn? 🤔</h2>
            <p style="font-size:1rem;color:#888;font-weight:700;margin-top:8px;">E-KINDER has 8 exciting lessons waiting for you!</p>
        </div>
        <div class="lesson-pill-row">
            <div class="lesson-pill-card" style="border-color:#faa2c1;"><span class="lpc-icon">🔤</span><div class="lpc-body"><div class="lpc-name">Letters</div><div class="lpc-tag">Alphabet</div></div></div>
            <div class="lesson-pill-card" style="border-color:#ffe066;"><span class="lpc-icon">🔷</span><div class="lpc-body"><div class="lpc-name">Shapes</div><div class="lpc-tag">Geometry</div></div></div>
            <div class="lesson-pill-card" style="border-color:#8ce99a;"><span class="lpc-icon">🐾</span><div class="lpc-body"><div class="lpc-name">Animals</div><div class="lpc-tag">Science</div></div></div>
            <div class="lesson-pill-card" style="border-color:#c5b5ff;"><span class="lpc-icon">🎨</span><div class="lpc-body"><div class="lpc-name">Colors</div><div class="lpc-tag">Art</div></div></div>
            <div class="lesson-pill-card" style="border-color:#ffc078;"><span class="lpc-icon">🔢</span><div class="lpc-body"><div class="lpc-name">Numbers</div><div class="lpc-tag">Math</div></div></div>
            <div class="lesson-pill-card" style="border-color:#66d9e8;"><span class="lpc-icon">🏅</span><div class="lpc-body"><div class="lpc-name">National Heroes</div><div class="lpc-tag">Filipino</div></div></div>
            <div class="lesson-pill-card" style="border-color:#ffa8a8;"><span class="lpc-icon">🧍</span><div class="lpc-body"><div class="lpc-name">Body Parts</div><div class="lpc-tag">Health</div></div></div>
            <div class="lesson-pill-card" style="border-color:#80deea;"><span class="lpc-icon">🙌</span><div class="lpc-body"><div class="lpc-name">Motor Skills</div><div class="lpc-tag">Hygiene</div></div></div>
        </div>
        <div class="text-center">
            <a href="lessons.php" style="display:inline-block;background:var(--green-dark);color:#fff;font-family:'Fredoka One',cursive;font-size:1rem;padding:14px 36px;border-radius:50px;text-decoration:none;box-shadow:0 8px 24px rgba(13,84,7,0.25);transition:transform 0.2s;" onmouseover="this.style.transform='scale(1.07)'" onmouseout="this.style.transform='scale(1)'">🎓 See All Lessons</a>
        </div>
    </div>
</section>

<!-- CTA -->
<section class="cta-section">
    <div class="container">
        <div class="cta-inner">
            <h2 class="cta-title">Ready to Start Your Adventure? 🚀</h2>
            <p class="cta-sub">Jump into a lesson, complete the activities, earn your badges, and become an E-KINDER Superstar!</p>
            <div class="cta-btns">
                <a href="lessons.php" class="cta-btn-white">🎓 Go to Lessons</a>
                <a href="activities.php" class="cta-btn-outline">📊 My Activities</a>
            </div>
        </div>
    </div>
</section>

<!-- FOOTER -->
<footer>
    <span class="footer-brand">E-KINDER</span>
    <p>An interactive learning platform for Filipino kindergarteners 🇵🇭</p>
    <div style="margin-top:14px;">
        <a href="student-dashboard.php">Home</a>
        <a href="lessons.php">Lessons</a>
        <a href="activities.php">Quizz</a>
        <a href="badges.php">Badges</a>
        <a href="about.php">About</a>
    </div>
    <p style="margin-top:16px;font-size:0.75rem;opacity:0.5;">© 2025 E-KINDER. Made with ❤️ for young Filipino learners.</p>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>