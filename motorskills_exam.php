<?php
session_start();
require_once 'database.php';

// Guard
if (!isset($_SESSION['student_id']) || $_SESSION['role'] !== 'student') {
    header("Location: index.php");
    exit;
}

// 🔥 dynamic lesson_id
$stmt = $conn->prepare("SELECT lesson_id FROM lessons WHERE lesson_name = ?");
$lesson_name = "motorskills";
$stmt->bind_param("s", $lesson_name);
$stmt->execute();
$res = $stmt->get_result()->fetch_assoc();
$lesson_id = $res['lesson_id'] ?? 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Motor Skills Final Exam — E-KINDER</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Fredoka+One&family=Nunito:wght@400;600;700;800;900&display=swap" rel="stylesheet">
<style>
:root {
    --green-dark: #1a2e1a;
    --teal: #0097a7;
    --teal-dark: #006978;
    --cream: #f0fdff;
    --pill: 999px;
    --warn: #fcc419;
    --blue: #4D96FF;
}
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

body {
    font-family: 'Nunito', sans-serif;
    background: var(--cream);
    min-height: 100vh;
    overflow-x: hidden;
    background-image:
        radial-gradient(circle at 10% 15%, rgba(0,151,167,0.08) 0%, transparent 40%),
        radial-gradient(circle at 88% 70%, rgba(0,105,120,0.07) 0%, transparent 40%),
        radial-gradient(circle at 50% 95%, rgba(38,198,218,0.06) 0%, transparent 40%);
}
body::before {
    content: ''; position: fixed; inset: 0; pointer-events: none; z-index: 0;
    background-image: radial-gradient(circle, rgba(0,0,0,.03) 1.2px, transparent 1.2px);
    background-size: 26px 26px;
}

/* ── NAV ── */
.lesson-nav { height: 64px; padding: 0 28px; background: rgba(255,255,255,.95); backdrop-filter: blur(16px); border-bottom: 1px solid rgba(0,0,0,.07); box-shadow: 0 2px 18px rgba(0,0,0,.06); position: sticky; top: 0; z-index: 200; display: flex; align-items: center; gap: 14px; }
.lnav-back { display: inline-flex; align-items: center; gap: 8px; background: var(--teal-dark); color: #fff; font-family: 'Fredoka One', cursive; font-size: .88rem; padding: 8px 20px; border-radius: var(--pill); text-decoration: none; box-shadow: 0 4px 14px rgba(0,105,120,.3); transition: transform .22s cubic-bezier(.34,1.56,.64,1); flex-shrink: 0; }
.lnav-back:hover { transform: scale(1.06); color: #fff; }
.lnav-title { font-family: 'Fredoka One', cursive; font-size: 1.1rem; color: var(--teal-dark); flex: 1; text-align: center; }

/* ── LAYOUT ── */
.page-wrap { position: relative; z-index: 1; max-width: 820px; margin: 0 auto; padding: 32px 20px 80px; }

/* ── HEADER ── */
.exam-header { text-align: center; margin-bottom: 28px; }
.exam-badge { display: inline-flex; align-items: center; gap: 8px; background: #fff; border: 2px solid #b2ebf2; border-radius: var(--pill); padding: 6px 18px; font-family: 'Fredoka One', cursive; font-size: .78rem; color: var(--teal-dark); box-shadow: 0 2px 10px rgba(0,0,0,.06); margin-bottom: 14px; }
.exam-title { font-family: 'Fredoka One', cursive; font-size: clamp(1.8rem,4vw,2.4rem); color: var(--teal-dark); margin-bottom: 6px; }
.exam-sub { font-size: .9rem; color: #a0b0b0; font-weight: 700; }

/* ── PROGRESS BAR ── */
.progress-card { background: #fff; border-radius: 20px; padding: 16px 24px; box-shadow: 0 4px 18px rgba(0,0,0,.06); border: 1.5px solid #b2ebf2; margin-bottom: 24px; display: flex; align-items: center; gap: 20px; flex-wrap: wrap; }
.prog-label { font-family: 'Fredoka One', cursive; font-size: .8rem; color: #90cdd4; text-transform: uppercase; letter-spacing: .8px; white-space: nowrap; }
.prog-bar-outer { flex: 1; height: 14px; background: #e0f7fa; border-radius: 99px; overflow: hidden; min-width: 120px; }
.prog-bar-inner { height: 100%; background: linear-gradient(90deg, #006978, #00acc1, #26c6da); border-radius: 99px; transition: width .5s cubic-bezier(.34,1.56,.64,1); width: 0%; }
.prog-count { font-family: 'Fredoka One', cursive; font-size: 1rem; color: var(--teal-dark); white-space: nowrap; }

/* ── SECTION DIVIDER ── */
.section-divider { display: flex; align-items: center; gap: 16px; background: var(--teal-dark); color: #fff; border-radius: 18px; padding: 14px 22px; margin-bottom: 20px; margin-top: 12px; box-shadow: 0 6px 20px rgba(0,105,120,.25); }
.section-divider .sec-icon { font-size: 1.4rem; }
.section-divider .sec-info { flex: 1; }
.section-divider .sec-title { font-family: 'Fredoka One', cursive; font-size: 1rem; line-height: 1; }
.section-divider .sec-sub { font-size: .75rem; opacity: .7; margin-top: 2px; font-weight: 700; }
.section-divider .sec-chip { background: rgba(255,255,255,.18); border-radius: var(--pill); padding: 4px 14px; font-family: 'Fredoka One', cursive; font-size: .8rem; white-space: nowrap; }

/* ── QUESTION CARDS ── */
.exam-scroll-area { display: flex; flex-direction: column; gap: 20px; margin-bottom: 28px; }
.q-card { background: #fff; border-radius: 20px; padding: 22px 24px 18px; box-shadow: 0 4px 16px rgba(0,0,0,.07); border: 2.5px solid #e0f7fa; transition: border-color .3s, box-shadow .3s; animation: cardIn .4s ease both; }
@keyframes cardIn { from { opacity: 0; transform: translateY(18px); } to { opacity: 1; transform: none; } }
.q-card.answered-correct { border-color: #40c057; box-shadow: 0 4px 18px rgba(64,192,87,.15); }
.q-card.answered-wrong   { border-color: #ff4444; box-shadow: 0 4px 18px rgba(255,68,68,.12); }
.q-card.unanswered-highlight { border-color: var(--warn); box-shadow: 0 4px 18px rgba(252,196,25,.2); animation: shake .4s ease; }
@keyframes shake { 0%,100%{transform:none} 20%,60%{transform:translateX(-5px)} 40%,80%{transform:translateX(5px)} }

.q-num-row { display: flex; align-items: center; gap: 10px; margin-bottom: 14px; }
.q-num-badge { width: 32px; height: 32px; border-radius: 50%; color: #fff; font-family: 'Fredoka One', cursive; font-size: .85rem; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.q-num-badge.mc-badge { background: linear-gradient(135deg, #006978, #00acc1); }
.q-num-badge.mt-badge  { background: linear-gradient(135deg, #0097a7, #26c6da); }
.q-status-icon { margin-left: auto; font-size: 1.1rem; display: none; }
.q-card.answered-correct .q-status-icon { display: block; color: #40c057; }
.q-card.answered-wrong   .q-status-icon { display: block; color: #ff4444; }
.q-text { font-family: 'Fredoka One', cursive; font-size: 1.1rem; color: #1a1a2e; margin-bottom: 16px; line-height: 1.35; }

/* ── CHOICES GRID ── */
.choices-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }
@media(max-width: 480px) { .choices-grid { grid-template-columns: 1fr; } }

.choice-btn { width: 100%; padding: 12px 16px; border-radius: 14px; border: 2.5px solid #e0f7fa; background: #f8feff; font-family: 'Nunito', sans-serif; font-weight: 800; font-size: .92rem; color: #444; cursor: pointer; text-align: left; transition: all .2s cubic-bezier(.34,1.56,.64,1); display: flex; align-items: center; gap: 10px; }
.choice-btn:hover:not(:disabled):not(.selected) { border-color: var(--teal); background: #e0f7fa; transform: translateY(-2px) scale(1.01); box-shadow: 0 5px 14px rgba(0,0,0,.08); }
.choice-btn.selected  { border-color: var(--blue); background: #e8f1ff; color: #1a4fa0; box-shadow: 0 4px 14px rgba(77,150,255,.2); }
.choice-btn.reveal-correct { border-color: #40c057 !important; background: #d3f9d8 !important; color: #1e6030 !important; }
.choice-btn.reveal-wrong   { border-color: #ff4444 !important; background: #ffe3e3 !important; color: #a00 !important; }
.choice-letter { width: 24px; height: 24px; border-radius: 8px; background: rgba(0,151,167,.1); display: flex; align-items: center; justify-content: center; font-family: 'Fredoka One', cursive; font-size: .75rem; flex-shrink: 0; color: var(--teal); transition: background .2s, color .2s; }
.choice-btn.selected .choice-letter       { background: var(--blue); color: #fff; }
.choice-btn.reveal-correct .choice-letter { background: #40c057; color: #fff; }
.choice-btn.reveal-wrong .choice-letter   { background: #ff4444; color: #fff; }

/* ── MATCHING TYPE ── */
.match-container { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px; }
@media(max-width: 580px) { .match-container { grid-template-columns: 1fr; } }
.match-col-label { font-family: 'Fredoka One', cursive; font-size: .75rem; color: #80cdd4; text-transform: uppercase; letter-spacing: 1px; text-align: center; margin-bottom: 10px; }
.match-col { display: flex; flex-direction: column; gap: 10px; }

.name-card { background: #fff; border: 3px solid #e0f7fa; border-radius: 16px; padding: 12px 14px; text-align: center; cursor: pointer; box-shadow: 0 4px 14px rgba(0,0,0,.06); transition: all .25s cubic-bezier(.34,1.56,.64,1); font-family: 'Fredoka One', cursive; font-size: .9rem; color: #333; display: flex; align-items: center; justify-content: center; gap: 8px; }
.name-card:hover:not(.matched):not(.matched-wrong):not(.selected-name) { transform: translateY(-3px) scale(1.02); box-shadow: 0 10px 26px rgba(0,0,0,.1); border-color: var(--teal); }
.name-card.selected-name    { border-color: var(--blue) !important; background: #e8f1ff; color: #1a4fa0; transform: scale(1.03); }
.name-card.matched           { border-color: #40c057; background: #d3f9d8; color: #1e6030; cursor: default; }
.name-card.matched-wrong     { border-color: #ff4444; background: #ffe3e3; color: #a00; cursor: default; }
.name-card.reveal-correct-match { border-color: #40c057 !important; background: #d3f9d8 !important; color: #1e6030 !important; }
.name-card.reveal-wrong-match   { border-color: #ff4444 !important; background: #ffe3e3 !important; color: #a00 !important; }

.img-card { background: #fff; border: 3px solid #e0f7fa; border-radius: 16px; padding: 12px 14px; cursor: pointer; box-shadow: 0 4px 14px rgba(0,0,0,.06); transition: all .25s cubic-bezier(.34,1.56,.64,1); display: flex; flex-direction: column; align-items: center; gap: 8px; }
.img-card:hover:not(.matched):not(.matched-wrong):not(.selected-img) { transform: translateY(-3px) scale(1.02); box-shadow: 0 10px 26px rgba(0,0,0,.1); border-color: var(--teal); }
.img-card.selected-img { border-color: var(--blue) !important; background: #e8f1ff; transform: scale(1.03); }
.img-card.matched       { border-color: #40c057; background: #d3f9d8; cursor: default; }
.img-card.matched-wrong { border-color: #ff4444; background: #ffe3e3; cursor: default; }
.img-card.reveal-correct-match { border-color: #40c057 !important; background: #d3f9d8 !important; }
.img-card.reveal-wrong-match   { border-color: #ff4444 !important; background: #ffe3e3 !important; }
.img-card .img-emoji { font-size: 2rem; line-height: 1; }
.img-card .img-label { font-size: .7rem; font-weight: 800; color: #aaa; font-family: 'Fredoka One', cursive; text-align: center; }

.match-hint { font-size: .8rem; color: #90cdd4; text-align: center; font-weight: 700; margin-top: -6px; margin-bottom: 14px; }

/* ── REVIEW NOTE ── */
.q-review-note { margin-top: 12px; padding: 10px 14px; border-radius: 12px; font-size: .82rem; font-weight: 800; display: none; }
.q-review-note.correct { background: #d3f9d8; color: #1e6030; display: flex; align-items: center; gap: 8px; }
.q-review-note.wrong   { background: #ffe3e3; color: #a00000; display: flex; align-items: center; gap: 8px; }

/* ── SUBMIT AREA ── */
.submit-area { text-align: center; padding: 28px; background: #fff; border-radius: 24px; box-shadow: 0 6px 24px rgba(0,0,0,.08); border: 2px solid #b2ebf2; }
.unanswered-warn { display: none; margin-bottom: 14px; background: #fff9db; border: 1.5px solid #ffe066; border-radius: 12px; padding: 10px 16px; font-size: .85rem; font-weight: 800; color: #a07000; }
.btn-submit { font-family: 'Fredoka One', cursive; font-size: 1.15rem; padding: 14px 48px; border-radius: var(--pill); background: var(--teal-dark); color: #fff; border: none; cursor: pointer; box-shadow: 0 6px 20px rgba(0,105,120,.35); transition: transform .22s cubic-bezier(.34,1.56,.64,1), box-shadow .22s; display: inline-flex; align-items: center; gap: 10px; }
.btn-submit:hover { transform: scale(1.05); box-shadow: 0 10px 28px rgba(0,105,120,.45); }
.btn-submit:active { transform: scale(.97); }

/* ── RESULT OVERLAY ── */
.result-overlay { display: none; position: fixed; inset: 0; background: rgba(0,30,35,.65); backdrop-filter: blur(8px); z-index: 500; align-items: center; justify-content: center; padding: 20px; }
.result-overlay.active { display: flex; }
.result-card { background: #fff; border-radius: 28px; padding: 36px 40px 32px; text-align: center; max-width: 480px; width: 100%; box-shadow: 0 20px 60px rgba(0,0,0,.3); animation: resultPop .5s cubic-bezier(.34,1.56,.64,1); }
@keyframes resultPop { from{opacity:0;transform:scale(.7) translateY(40px)} to{opacity:1;transform:none} }
.result-emoji { font-size: 4.5rem; margin-bottom: 10px; }
.result-title { font-family: 'Fredoka One', cursive; font-size: 2rem; color: var(--teal-dark); margin-bottom: 4px; }
.result-sub   { font-size: .9rem; color: #aaa; font-weight: 700; margin-bottom: 18px; }

.score-breakdown { display: flex; gap: 12px; justify-content: center; margin-bottom: 18px; flex-wrap: wrap; }
.score-chip { font-family: 'Fredoka One', cursive; font-size: .9rem; padding: 8px 18px; border-radius: var(--pill); border: 2.5px solid; display: flex; align-items: center; gap: 6px; }
.score-chip.mc    { background: #e0f7fa; border-color: var(--teal); color: var(--teal-dark); }
.score-chip.mt    { background: #e8f1ff; border-color: var(--blue); color: #1a4fa0; }
.score-chip.total { background: #fff9db; border-color: var(--warn); color: #a07000; font-size: 1.05rem; }

.result-verdict { font-family: 'Fredoka One', cursive; font-size: 1.05rem; padding: 6px 20px; border-radius: var(--pill); display: inline-block; margin-bottom: 22px; }
.result-verdict.pass { background: #40c057; color: #fff; }
.result-verdict.fail { background: #ff4444; color: #fff; }
.result-actions { display: flex; gap: 12px; justify-content: center; flex-wrap: wrap; }
.btn-review  { font-family: 'Fredoka One', cursive; font-size: .95rem; padding: 12px 28px; border-radius: var(--pill); border: 2.5px solid var(--teal-dark); background: transparent; color: var(--teal-dark); cursor: pointer; transition: all .2s cubic-bezier(.34,1.56,.64,1); }
.btn-review:hover { background: var(--teal-dark); color: #fff; transform: scale(1.04); }
.btn-go-back { font-family: 'Fredoka One', cursive; font-size: .95rem; padding: 12px 28px; border-radius: var(--pill); background: var(--teal-dark); color: #fff; border: none; cursor: pointer; box-shadow: 0 5px 16px rgba(0,105,120,.3); transition: all .2s cubic-bezier(.34,1.56,.64,1); }
.btn-go-back:hover { transform: scale(1.04); }

/* ── REVIEW BANNER ── */
.review-banner { display: none; background: var(--teal-dark); color: #fff; padding: 14px 24px; border-radius: 18px; margin-bottom: 20px; font-family: 'Fredoka One', cursive; font-size: .95rem; align-items: center; gap: 12px; box-shadow: 0 6px 20px rgba(0,105,120,.25); }
.review-banner.visible { display: flex; }
.review-score-chip { margin-left: auto; background: rgba(255,255,255,.15); border-radius: var(--pill); padding: 4px 14px; font-size: .85rem; white-space: nowrap; }

/* ── CONFETTI ── */
.cp { position: fixed; border-radius: 2px; pointer-events: none; z-index: 9999; animation: fall linear forwards; }
@keyframes fall { 0%{transform:translateY(0) rotate(0deg);opacity:1} 100%{transform:translateY(100vh) rotate(720deg);opacity:0} }

.match-col {
    display: flex;
    flex-direction: column;
    gap: 10px;
    max-height: 320px;
    overflow-y: auto;
}

@media(max-width: 580px) {
    .match-container {
        grid-template-columns: 1fr;
    }

    /* gawin horizontal scroll yung images */
    #imgsCol {
        display: grid;
        grid-template-columns: repeat(5, 120px);
        overflow-x: auto;
        gap: 10px;
    }

    .img-card {
        min-width: 120px;
    }
}

</style>
</head>
<body>

<!-- NAV -->
<nav class="lesson-nav">
    <a href="activities.php" class="lnav-back"><i class="fas fa-arrow-left"></i> Back</a>
    <div class="lnav-title">🧼 Motor Skills Final Exam</div>
</nav>

<div class="page-wrap">

    <!-- REVIEW BANNER -->
    <div class="review-banner" id="reviewBanner">
        <i class="fas fa-magnifying-glass"></i>
        <span>Review Mode — Check your answers below!</span>
        <span class="review-score-chip" id="reviewScoreChip">Score: 0 / 40</span>
    </div>

    <!-- HEADER -->
    <div class="exam-header">
        <div class="exam-badge"><i class="fas fa-hand-sparkles"></i> Final Exam</div>
        <h1 class="exam-title">🧼 Motor Skills Final Exam</h1>
        <p class="exam-sub" id="examSubtitle">Answer all 30 items, then click Submit!</p>
    </div>

    <!-- PROGRESS BAR -->
    <div class="progress-card">
        <span class="prog-label">Progress</span>
        <div class="prog-bar-outer"><div class="prog-bar-inner" id="progressBar"></div></div>
        <span class="prog-count" id="progressCount">0 / 30 answered</span>
    </div>

    <!-- PART 1: MULTIPLE CHOICE -->
    <div class="section-divider">
        <div class="sec-icon">🔤</div>
        <div class="sec-info">
            <div class="sec-title">Part 1 — Multiple Choice</div>
            <div class="sec-sub">Choose the correct answer for each question.</div>
        </div>
        <div class="sec-chip">20 items</div>
    </div>
    <div class="exam-scroll-area" id="mcArea"></div>

    <!-- PART 2: MATCHING TYPE -->
    <div class="section-divider" style="background:#0097a7;">
        <div class="sec-icon">🔗</div>
        <div class="sec-info">
            <div class="sec-title">Part 2 — Matching Type</div>
            <div class="sec-sub">Match the hygiene activity to the correct tool or action!</div>
        </div>
        <div class="sec-chip">10 items</div>
    </div>
    <div class="exam-scroll-area" id="mtArea"></div>

    <!-- SUBMIT AREA -->
    <div class="submit-area" id="submitArea">
        <div class="unanswered-warn" id="unansweredWarn">
            <i class="fas fa-triangle-exclamation"></i>
            Please answer all items before submitting!
        </div>
        <button class="btn-submit" onclick="submitExam()">
            <i class="fas fa-paper-plane"></i> Submit Exam
        </button>
    </div>
</div>

<!-- RESULT OVERLAY -->
<div class="result-overlay" id="resultOverlay">
    <div class="result-card">
        <div class="result-emoji" id="resultEmoji">🎉</div>
        <div class="result-title" id="resultTitle">Exam Complete!</div>
        <div class="result-sub" id="resultSub">Here's how you did</div>
        <div class="score-breakdown">
            <div class="score-chip mc"><i class="fas fa-list"></i> MC: <span id="mcScore">0</span>/20</div>
            <div class="score-chip mt"><i class="fas fa-link"></i> Match: <span id="mtScore">0</span>/10</div>
        </div>
        <div class="score-chip total" style="margin:0 auto 18px;display:inline-flex;">
            <i class="fas fa-star"></i> Total: <span id="totalScore">0 / 40 (0%)</span>
        </div>
        <div class="result-verdict" id="resultVerdict">PASSED 🎉</div>
        <div class="result-actions">
            <button class="btn-review" onclick="openReview()"><i class="fas fa-search"></i> Review Answers</button>
            <button class="btn-go-back" onclick="window.location.href='activities.php'"><i class="fas fa-home"></i> Go Back</button>
        </div>
    </div>
</div>

<script>
// ═══════════════════════════════════════════
// MULTIPLE CHOICE (15 questions)
// Based on motorskills.php hygiene topics:
// Handwashing, Brushing Teeth, Bathing,
// Combing Hair, Cutting Nails, Clean Clothes
// ═══════════════════════════════════════════
const MC_QUESTIONS = [
    { q:"When should you wash your hands?",                        c:["Before eating","While sleeping","During play","After napping"],         a:"Before eating"   },
    { q:"How long should you wash your hands with soap?",          c:["5 seconds","10 seconds","20 seconds","1 minute"],                       a:"20 seconds"      },
    { q:"How many times a day should you brush your teeth?",       c:["Once a week","Once a day","Twice a day","Three times a week"],           a:"Twice a day"     },
    { q:"When is the best time to brush your teeth?",              c:["Only at noon","Morning and night","Only at night","Only in the morning"],a:"Morning and night"},
    { q:"Why do we take a bath?",                                  c:["To stay dirty","To sleep better","To stay clean and healthy","To eat more"],a:"To stay clean and healthy"},
    { q:"How often should you cut your nails?",                    c:["Every day","Once a week","Once a month","Never"],                       a:"Once a week"     },
    { q:"Why do we cut our nails?",                                c:["To look cool","To keep them clean and avoid germs","To grow them long","To paint them"], a:"To keep them clean and avoid germs"},
    { q:"What should you use to comb your hair?",                  c:["A fork","A brush or comb","Your fingers only","A ruler"],               a:"A brush or comb" },
    { q:"Why should you wear clean clothes every day?",            c:["To look pretty only","To avoid germs and stay healthy","Because it's fun","Because teacher says so"],a:"To avoid germs and stay healthy"},
    { q:"What do you use to brush your teeth?",                    c:["A towel","A toothbrush and toothpaste","Soap and water","A comb"],       a:"A toothbrush and toothpaste"},
    { q:"What should you do AFTER using the toilet?",              c:["Go to sleep","Wash your hands","Eat food","Watch TV"],                  a:"Wash your hands" },
    { q:"What do you use to wash your body during bath time?",     c:["Toothpaste","Shampoo only","Soap and water","Just water"],              a:"Soap and water"  },
    { q:"Why is it important to wash your hands before eating?",   c:["To remove color","To kill germs so you don't get sick","To make food taste better","To wake up"],a:"To kill germs so you don't get sick"},
    { q:"What do you use to wash your hair?",                      c:["Soap","Toothpaste","Shampoo","Lotion"],                                 a:"Shampoo"         },
    { q:"Which of these is a good hygiene habit?",                 c:["Not washing hands","Wearing dirty clothes","Brushing teeth daily","Skipping bath"],a:"Brushing teeth daily"},
    { q:"How should you dry your hands after washing?",            c:["Wipe on your clothes","Air dry only","Use a clean towel","Shake them hard"],      a:"Use a clean towel"   },
    { q:"What is the correct order when getting dressed?",         c:["Shoes first","Underwear first then outer clothes","Jacket first","Socks first"],   a:"Underwear first then outer clothes"},
    { q:"Where should you start combing tangled hair?",            c:["At the roots/top","At the middle","At the tips/ends first","Anywhere"],           a:"At the tips/ends first"},
    { q:"Why should you take a bath every day?",                   c:["To play in water","To remove germs and stay fresh","Because it is fun","To cool down only"],a:"To remove germs and stay fresh"},
    { q:"What should you do after cutting your nails?",            c:["Paint them","Clean under the nails","Leave them","Bite them"],                    a:"Clean under the nails"},
];

// ═══════════════════════════════════════════
// MATCHING TYPE (10 pairs)
// Activity name → Tool / Description emoji
// ═══════════════════════════════════════════
const MT_PAIRS = [
    { activity:"Brushing Teeth",  emoji:"🪥", desc:"Toothbrush & Toothpaste", color:"#00acc1" },
    { activity:"Washing Hands",   emoji:"🧼", desc:"Soap & Water",            color:"#0097a7" },
    { activity:"Taking a Bath",   emoji:"🚿", desc:"Shower & Soap",           color:"#00bcd4" },
    { activity:"Combing Hair",    emoji:"🪮", desc:"Brush or Comb",           color:"#26c6da" },
    { activity:"Cutting Nails",   emoji:"✂️",  desc:"Nail Cutter",             color:"#4dd0e1" },
    { activity:"Wearing Clean Clothes", emoji:"👕", desc:"Fresh Clothes",     color:"#0097a7" },
    { activity:"Washing Face",    emoji:"🫧", desc:"Soap & Face Towel",       color:"#00acc1" },
    { activity:"Drying Hands",    emoji:"🤲", desc:"Clean Towel",             color:"#006978" },
    { activity:"Using Shampoo",   emoji:"🧴", desc:"Shampoo for Hair",        color:"#00bcd4" },
    { activity:"Gargling",        emoji:"🌊", desc:"Water & Mouthwash",       color:"#26c6da" },
];

const MC_TOTAL   = 20;   // 20 × 1pt = 20pts
const MT_TOTAL   = 10;   // 10 × 2pts = 20pts
const TOTAL      = MC_TOTAL + MT_TOTAL; // 30 items
const MAX_SCORE  = 40;   // 40 points total
const PASS_SCORE = 24;   // 60% of 40
const LETTERS    = ['A','B','C','D'];

let mcAnswers       = new Array(MC_TOTAL).fill(null);
let mtAnswers       = new Array(MT_TOTAL).fill(null);
let examSubmitted   = false;
let finalMcScore    = 0;
let finalMtScore    = 0;
let mtImgOrder      = [];
let selectedNameIdx = null;

function shuffle(arr) {
    const a = [...arr];
    for (let i = a.length - 1; i > 0; i--) {
        const j = Math.floor(Math.random() * (i + 1));
        [a[i], a[j]] = [a[j], a[i]];
    }
    return a;
}

// ═══════════════════════════════════════════
// BUILD MULTIPLE CHOICE
// ═══════════════════════════════════════════
function buildMC() {
    const area = document.getElementById('mcArea');
    area.innerHTML = '';
    MC_QUESTIONS.forEach((item, i) => {
        const card = document.createElement('div');
        card.className = 'q-card';
        card.id = 'mccard-' + i;
        card.style.animationDelay = (i * 0.04) + 's';

        const qNumRow = document.createElement('div');
        qNumRow.className = 'q-num-row';
        qNumRow.innerHTML = `
            <div class="q-num-badge mc-badge">${i + 1}</div>
            <div style="font-family:'Fredoka One',cursive;font-size:.78rem;color:#90cdd4;text-transform:uppercase;letter-spacing:.8px;">Question ${i + 1} of ${MC_TOTAL}</div>
            <i class="fas fa-check-circle q-status-icon" id="mcicon-${i}"></i>
        `;

        const qText = document.createElement('div');
        qText.className = 'q-text';
        qText.textContent = item.q;

        const grid = document.createElement('div');
        grid.className = 'choices-grid';

        item.c.forEach((choice, ci) => {
            const btn = document.createElement('button');
            btn.className = 'choice-btn';
            btn.id = 'mcchoice-' + i + '-' + ci;
            btn.innerHTML = `<span class="choice-letter">${LETTERS[ci]}</span>${choice}`;
            btn.addEventListener('click', function() {
                if (examSubmitted) return;
                mcAnswers[i] = choice;
                item.c.forEach((_, idx) => {
                    document.getElementById('mcchoice-' + i + '-' + idx).classList.remove('selected');
                });
                btn.classList.add('selected');
                updateProgress();
            });
            grid.appendChild(btn);
        });

        const reviewNote = document.createElement('div');
        reviewNote.className = 'q-review-note';
        reviewNote.id = 'mcrnote-' + i;

        card.appendChild(qNumRow);
        card.appendChild(qText);
        card.appendChild(grid);
        card.appendChild(reviewNote);
        area.appendChild(card);
    });
}

// ═══════════════════════════════════════════
// BUILD MATCHING TYPE
// ═══════════════════════════════════════════
function buildMT() {
    const area = document.getElementById('mtArea');
    area.innerHTML = '';
    mtImgOrder = shuffle(MT_PAIRS.map((_, i) => i));

    const card = document.createElement('div');
    card.className = 'q-card';
    card.id = 'mtcard-main';
    card.style.animationDelay = '0.6s';

    const namesHTML = MT_PAIRS.map((pair, i) => `
        <div class="name-card" id="namecard-${i}" onclick="selectName(${i})">
            <span style="font-size:1.3rem;">${pair.emoji}</span>
            <span style="font-size:.85rem;">${pair.activity}</span>
        </div>
    `).join('');

    const imgsHTML = mtImgOrder.map((pairIdx, pos) => {
        const pair = MT_PAIRS[pairIdx];
        return `
            <div class="img-card" id="imgcard-${pos}" data-pair-idx="${pairIdx}" onclick="selectImg(${pos})">
                <div class="img-emoji">${pair.emoji}</div>
                <div class="img-label">?</div>
            </div>
        `;
    }).join('');

    card.innerHTML = `
        <div class="q-num-row">
            <div class="q-num-badge mt-badge">🔗</div>
            <div style="font-family:'Fredoka One',cursive;font-size:.78rem;color:#0097a7;text-transform:uppercase;letter-spacing:.8px;">Match the Activity to its Tool</div>
        </div>
        <p class="match-hint">👈 Click an activity on the left, then click the matching tool on the right!</p>
        <div class="match-container">
            <div>
                <div class="match-col-label">🧼 Hygiene Activities</div>
                <div class="match-col" id="namesCol">${namesHTML}</div>
            </div>
            <div>
                <div class="match-col-label">🛁 Tools / Descriptions</div>
                <div class="match-col" id="imgsCol">${imgsHTML}</div>
            </div>
        </div>
        <div class="q-review-note" id="mtrnote"></div>
    `;
    area.appendChild(card);
}

function selectName(nameIdx) {
    if (examSubmitted) return;
    const nameCard = document.getElementById('namecard-' + nameIdx);
    if (nameCard.classList.contains('matched') || nameCard.classList.contains('matched-wrong')) return;
    document.querySelectorAll('.name-card').forEach(c => c.classList.remove('selected-name'));
    selectedNameIdx = nameIdx;
    nameCard.classList.add('selected-name');
}

function selectImg(imgPos) {
    if (examSubmitted) return;
    if (selectedNameIdx === null) return;
    const imgCard  = document.getElementById('imgcard-' + imgPos);
    const nameCard = document.getElementById('namecard-' + selectedNameIdx);
    if (imgCard.classList.contains('matched') || imgCard.classList.contains('matched-wrong')) return;

    if (mtAnswers[selectedNameIdx] !== null) {
        const oldCard = document.getElementById('imgcard-' + mtAnswers[selectedNameIdx]);
        if (oldCard) { oldCard.classList.remove('matched', 'matched-wrong'); oldCard.querySelector('.img-label').textContent = '?'; }
    }

    mtAnswers[selectedNameIdx] = imgPos;
    nameCard.classList.remove('selected-name');
    nameCard.classList.add('matched');
    imgCard.classList.add('matched');
    imgCard.querySelector('.img-label').textContent = MT_PAIRS[selectedNameIdx].activity;
    selectedNameIdx = null;
    updateProgress();
}

// ═══════════════════════════════════════════
// PROGRESS
// ═══════════════════════════════════════════
function updateProgress() {
    const done = mcAnswers.filter(a => a !== null).length + mtAnswers.filter(a => a !== null).length;
    document.getElementById('progressBar').style.width = (done / TOTAL * 100) + '%';
    document.getElementById('progressCount').textContent = done + ' / ' + TOTAL + ' answered';
}

// ═══════════════════════════════════════════
// SUBMIT
// ═══════════════════════════════════════════
function submitExam() {
    const mcNull = mcAnswers.findIndex(a => a === null);
    const mtNull = mtAnswers.filter(a => a === null).length;
    if (mcNull >= 0 || mtNull > 0) {
        document.getElementById('unansweredWarn').style.cssText = 'display:flex;align-items:center;gap:8px;';
        const el = mcNull >= 0 ? document.getElementById('mccard-' + mcNull) : document.getElementById('mtcard-main');
        el.classList.add('unanswered-highlight');
        el.scrollIntoView({ behavior: 'smooth', block: 'center' });
        setTimeout(() => el.classList.remove('unanswered-highlight'), 600);
        return;
    }

    document.getElementById('unansweredWarn').style.display = 'none';
    examSubmitted = true;

    finalMcScore = 0;
    MC_QUESTIONS.forEach((item, i) => { if (mcAnswers[i] === item.a) finalMcScore++; });

    finalMtScore = 0;
    MT_PAIRS.forEach((pair, nameIdx) => {
        const imgPos = mtAnswers[nameIdx];
        const matchedIdx = parseInt(document.getElementById('imgcard-' + imgPos).dataset.pairIdx);
        if (matchedIdx === nameIdx) finalMtScore += 2; // 2pts each
    });

    document.querySelectorAll('.choice-btn').forEach(b => b.disabled = true);
    document.querySelectorAll('.name-card, .img-card').forEach(b => b.style.pointerEvents = 'none');

    showResult();

    const totalScore = finalMcScore + finalMtScore;
    const examPct    = Math.round((totalScore / MAX_SCORE) * 100);
    fetch("save_exam.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: "score=" + totalScore + "&percentage=" + examPct + "&lesson_id=<?php echo $lesson_id; ?>"
    });
}

// ═══════════════════════════════════════════
// RESULT
// ═══════════════════════════════════════════
function showResult() {
    const total  = finalMcScore + finalMtScore;
    const pct    = Math.round((total / MAX_SCORE) * 100);
    const passed = total >= PASS_SCORE;
    document.getElementById('resultEmoji').textContent = passed ? '🎉' : '😢';
    document.getElementById('resultTitle').textContent = passed ? 'Hygiene Champion!' : 'Keep Practicing!';
    document.getElementById('resultSub').textContent   = passed ? 'You passed the Motor Skills Exam!' : 'Review your hygiene lessons and try again!';
    document.getElementById('mcScore').textContent    = finalMcScore;
    document.getElementById('mtScore').textContent    = Math.round(finalMtScore / 2); // show correct count
    document.getElementById('totalScore').textContent = total + ' / ' + MAX_SCORE + ' (' + pct + '%)';
    const v = document.getElementById('resultVerdict');
    v.textContent = passed ? '✅ PASSED' : '❌ FAILED';
    v.className   = 'result-verdict ' + (passed ? 'pass' : 'fail');
    document.getElementById('resultOverlay').classList.add('active');
    if (passed) launchConfetti();
}

// ═══════════════════════════════════════════
// REVIEW
// ═══════════════════════════════════════════
function openReview() {
    document.getElementById('resultOverlay').classList.remove('active');
    document.getElementById('reviewBanner').classList.add('visible');
    const total = finalMcScore + finalMtScore;
    const reviewPct = Math.round((total / MAX_SCORE) * 100);
    document.getElementById('reviewScoreChip').textContent = 'Score: ' + total + ' / ' + MAX_SCORE + ' (' + reviewPct + '%)';
    document.getElementById('examSubtitle').textContent = 'Review your answers below!';
    document.getElementById('submitArea').style.display = 'none';

    MC_QUESTIONS.forEach((item, i) => {
        const card = document.getElementById('mccard-' + i);
        const isCorrect = mcAnswers[i] === item.a;
        card.classList.add(isCorrect ? 'answered-correct' : 'answered-wrong');
        item.c.forEach((choice, ci) => {
            const btn = document.getElementById('mcchoice-' + i + '-' + ci);
            if (choice === item.a)            btn.classList.add('reveal-correct');
            else if (choice === mcAnswers[i]) btn.classList.add('reveal-wrong');
        });
        const note = document.getElementById('mcrnote-' + i);
        note.className = isCorrect ? 'q-review-note correct' : 'q-review-note wrong';
        note.innerHTML = isCorrect
            ? `<i class="fas fa-check-circle"></i> Correct! The answer is <strong>${item.a}</strong>.`
            : `<i class="fas fa-times-circle"></i> Wrong! You answered <strong>${mcAnswers[i]}</strong>. Correct answer: <strong>${item.a}</strong>`;
    });

    let mtCorrect = 0;
    MT_PAIRS.forEach((pair, nameIdx) => {
        const imgPos     = mtAnswers[nameIdx];
        const matchedIdx = parseInt(document.getElementById('imgcard-' + imgPos).dataset.pairIdx);
        const isCorrect  = matchedIdx === nameIdx;
        if (isCorrect) mtCorrect++;
        const nameCard = document.getElementById('namecard-' + nameIdx);
        const imgCard  = document.getElementById('imgcard-' + imgPos);
        nameCard.classList.remove('matched','matched-wrong','selected-name');
        imgCard.classList.remove('matched','matched-wrong');
        nameCard.classList.add(isCorrect ? 'reveal-correct-match' : 'reveal-wrong-match');
        imgCard.classList.add(isCorrect  ? 'reveal-correct-match' : 'reveal-wrong-match');
        imgCard.querySelector('.img-label').textContent = isCorrect
            ? '✅ ' + pair.activity
            : '❌ ' + MT_PAIRS[matchedIdx].activity;
    });

    const mtNote = document.getElementById('mtrnote');
    const mtPts  = mtCorrect * 2;
    mtNote.className = 'q-review-note ' + (mtCorrect === MT_TOTAL ? 'correct' : 'wrong');
    mtNote.innerHTML = (mtCorrect === MT_TOTAL ? '<i class="fas fa-check-circle"></i> Perfect matching!' : '<i class="fas fa-times-circle"></i>')
        + ' Matching: <strong>' + mtCorrect + ' / ' + MT_TOTAL + ' correct = ' + mtPts + ' pts</strong>';

    const goBack = document.createElement('div');
    goBack.style.cssText = 'text-align:center;padding:20px 0';
    goBack.innerHTML = `<button class="btn-submit" onclick="window.location.href='activities.php'"><i class="fas fa-home"></i> Done — Go Back</button>`;
    document.querySelector('.page-wrap').appendChild(goBack);
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

// ═══════════════════════════════════════════
// CONFETTI
// ═══════════════════════════════════════════
function launchConfetti() {
    const colors = ['#0097a7','#006978','#00acc1','#26c6da','#fcc419','#40c057','#f06292'];
    for (let i = 0; i < 65; i++) {
        const p = document.createElement('div');
        p.className = 'cp';
        p.style.cssText = `left:${Math.random()*100}vw;top:-12px;background:${colors[Math.floor(Math.random()*colors.length)]};border-radius:${Math.random()>.5?'50%':'2px'};width:${6+Math.random()*8}px;height:${6+Math.random()*8}px;animation-duration:${1.3+Math.random()*1.8}s;animation-delay:${Math.random()*.6}s;`;
        document.body.appendChild(p);
        p.addEventListener('animationend', () => p.remove());
    }
}

buildMC();
buildMT();
updateProgress();
</script>
</body>
</html>