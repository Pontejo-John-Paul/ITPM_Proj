<?php
session_start();
require_once 'database.php';
require_once 'activity_helper.php';

// Guard
if (!isset($_SESSION['student_id']) || $_SESSION['role'] !== 'student') {
    header("Location: index.php");
    exit;
}

$student_id = $_SESSION['student_id'];

// 🔥 dynamic lesson_id
$stmt = $conn->prepare("SELECT lesson_id FROM lessons WHERE lesson_name = ?");
$lesson_name = "letters";
$stmt->bind_param("s", $lesson_name);
$stmt->execute();
$result = $stmt->get_result()->fetch_assoc();
$lesson_id = $result['lesson_id'] ?? 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Letters Final Exam — E-KINDER</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Fredoka+One&family=Nunito:wght@400;600;700;800;900&display=swap" rel="stylesheet">
<style>
:root {
    --green-dark: #1a2e1a;
    --cream: #fdf8f0;
    --pill: 999px;
    --vowel: #e8336d;
    --cons: #1a6de8;
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
        radial-gradient(circle at 10% 15%, rgba(232,51,109,0.07) 0%, transparent 40%),
        radial-gradient(circle at 88% 70%, rgba(26,109,232,0.07) 0%, transparent 40%),
        radial-gradient(circle at 50% 95%, rgba(249,168,37,0.06) 0%, transparent 40%);
}
body::before {
    content: ''; position: fixed; inset: 0; pointer-events: none; z-index: 0;
    background-image: radial-gradient(circle, rgba(0,0,0,.03) 1.2px, transparent 1.2px);
    background-size: 26px 26px;
}

/* ── NAV ── */
.lesson-nav { height: 64px; padding: 0 28px; background: rgba(255,255,255,.95); backdrop-filter: blur(16px); border-bottom: 1px solid rgba(0,0,0,.07); box-shadow: 0 2px 18px rgba(0,0,0,.06); position: sticky; top: 0; z-index: 200; display: flex; align-items: center; gap: 14px; }
.lnav-back { display: inline-flex; align-items: center; gap: 8px; background: var(--green-dark); color: #fff; font-family: 'Fredoka One', cursive; font-size: .88rem; padding: 8px 20px; border-radius: var(--pill); text-decoration: none; box-shadow: 0 4px 14px rgba(0,0,0,.22); transition: transform .22s cubic-bezier(.34,1.56,.64,1); flex-shrink: 0; }
.lnav-back:hover { transform: scale(1.06); color: #fff; }
.lnav-title { font-family: 'Fredoka One', cursive; font-size: 1.1rem; color: var(--green-dark); flex: 1; text-align: center; }

/* ── LAYOUT ── */
.page-wrap { position: relative; z-index: 1; max-width: 820px; margin: 0 auto; padding: 32px 20px 80px; }

/* ── HEADER ── */
.exam-header { text-align: center; margin-bottom: 28px; }
.exam-badge { display: inline-flex; align-items: center; gap: 8px; background: #fff; border: 2px solid #fde8f0; border-radius: var(--pill); padding: 6px 18px; font-family: 'Fredoka One', cursive; font-size: .78rem; color: #a0184a; box-shadow: 0 2px 10px rgba(0,0,0,.06); margin-bottom: 14px; }
.exam-title { font-family: 'Fredoka One', cursive; font-size: clamp(1.8rem,4vw,2.4rem); color: var(--green-dark); margin-bottom: 6px; }
.exam-sub { font-size: .9rem; color: #a0a8a0; font-weight: 700; }

/* ── PROGRESS BAR ── */
.progress-card { background: #fff; border-radius: 20px; padding: 16px 24px; box-shadow: 0 4px 18px rgba(0,0,0,.06); border: 1.5px solid #eee; margin-bottom: 24px; display: flex; align-items: center; gap: 20px; flex-wrap: wrap; }
.prog-label { font-family: 'Fredoka One', cursive; font-size: .8rem; color: #bbb; text-transform: uppercase; letter-spacing: .8px; white-space: nowrap; }
.prog-bar-outer { flex: 1; height: 14px; background: #f0f0f0; border-radius: 99px; overflow: hidden; min-width: 120px; }
.prog-bar-inner { height: 100%; background: linear-gradient(90deg, var(--vowel), var(--cons)); border-radius: 99px; transition: width .5s cubic-bezier(.34,1.56,.64,1); width: 0%; }
.prog-count { font-family: 'Fredoka One', cursive; font-size: 1rem; color: var(--green-dark); white-space: nowrap; }

/* ── SECTION DIVIDER ── */
.section-divider { display: flex; align-items: center; gap: 16px; background: var(--green-dark); color: #fff; border-radius: 18px; padding: 14px 22px; margin-bottom: 20px; margin-top: 12px; box-shadow: 0 6px 20px rgba(26,46,26,.22); }
.section-divider .sec-icon { font-size: 1.4rem; }
.section-divider .sec-info { flex: 1; }
.section-divider .sec-title { font-family: 'Fredoka One', cursive; font-size: 1rem; line-height: 1; }
.section-divider .sec-sub { font-size: .75rem; opacity: .7; margin-top: 2px; font-weight: 700; }
.section-divider .sec-chip { background: rgba(255,255,255,.18); border-radius: var(--pill); padding: 4px 14px; font-family: 'Fredoka One', cursive; font-size: .8rem; white-space: nowrap; }

/* ── QUESTION CARDS ── */
.exam-scroll-area { display: flex; flex-direction: column; gap: 20px; margin-bottom: 28px; }
.q-card { background: #fff; border-radius: 20px; padding: 22px 24px 18px; box-shadow: 0 4px 16px rgba(0,0,0,.07); border: 2.5px solid #eee; transition: border-color .3s, box-shadow .3s; animation: cardIn .4s ease both; }
@keyframes cardIn { from { opacity: 0; transform: translateY(18px); } to { opacity: 1; transform: none; } }
.q-card.answered-correct { border-color: #40c057; box-shadow: 0 4px 18px rgba(64,192,87,.15); }
.q-card.answered-wrong   { border-color: #ff4444; box-shadow: 0 4px 18px rgba(255,68,68,.12); }
.q-card.unanswered-highlight { border-color: var(--warn); box-shadow: 0 4px 18px rgba(252,196,25,.2); animation: shake .4s ease; }
@keyframes shake { 0%,100%{transform:none} 20%,60%{transform:translateX(-5px)} 40%,80%{transform:translateX(5px)} }

.q-num-row { display: flex; align-items: center; gap: 10px; margin-bottom: 14px; }
.q-num-badge { width: 32px; height: 32px; border-radius: 50%; color: #fff; font-family: 'Fredoka One', cursive; font-size: .85rem; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.q-num-badge.mc-badge { background: var(--vowel); }
.q-num-badge.mt-badge  { background: var(--cons); }
.q-status-icon { margin-left: auto; font-size: 1.1rem; display: none; }
.q-card.answered-correct .q-status-icon { display: block; color: #40c057; }
.q-card.answered-wrong   .q-status-icon { display: block; color: #ff4444; }
.q-text { font-family: 'Fredoka One', cursive; font-size: 1.1rem; color: #1a1a2e; margin-bottom: 16px; line-height: 1.35; }

/* ── BIG LETTER DISPLAY ── */
.q-letter-display {
    display: inline-flex; align-items: baseline; gap: 6px;
    font-family: 'Fredoka One', cursive; font-size: 2.6rem;
    background: linear-gradient(135deg, #fff0f5 0%, #f5f0ff 100%);
    border-radius: 16px; padding: 8px 22px;
    border: 2.5px solid #f0d0e8; margin-bottom: 16px;
    box-shadow: 0 4px 14px rgba(232,51,109,.1);
}
.q-letter-upper { color: var(--vowel); }
.q-letter-lower { color: var(--cons); font-size: 1.8rem; opacity: .7; }

/* ── CHOICES GRID ── */
.choices-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }
@media(max-width: 480px) { .choices-grid { grid-template-columns: 1fr; } }

.choice-btn { width: 100%; padding: 12px 16px; border-radius: 14px; border: 2.5px solid #eee; background: #fafafa; font-family: 'Nunito', sans-serif; font-weight: 800; font-size: .92rem; color: #444; cursor: pointer; text-align: left; transition: all .2s cubic-bezier(.34,1.56,.64,1); display: flex; align-items: center; gap: 10px; }
.choice-btn:hover:not(:disabled):not(.selected) { border-color: var(--vowel); background: #fff0f5; transform: translateY(-2px) scale(1.01); box-shadow: 0 5px 14px rgba(0,0,0,.08); }
.choice-btn.selected  { border-color: var(--blue); background: #e8f1ff; color: #1a4fa0; box-shadow: 0 4px 14px rgba(77,150,255,.2); }
.choice-btn.reveal-correct { border-color: #40c057 !important; background: #d3f9d8 !important; color: #1e6030 !important; }
.choice-btn.reveal-wrong   { border-color: #ff4444 !important; background: #ffe3e3 !important; color: #a00 !important; }
.choice-letter { width: 24px; height: 24px; border-radius: 8px; background: rgba(0,0,0,.06); display: flex; align-items: center; justify-content: center; font-family: 'Fredoka One', cursive; font-size: .75rem; flex-shrink: 0; transition: background .2s, color .2s; }
.choice-btn.selected .choice-letter       { background: var(--blue); color: #fff; }
.choice-btn.reveal-correct .choice-letter { background: #40c057; color: #fff; }
.choice-btn.reveal-wrong .choice-letter   { background: #ff4444; color: #fff; }

/* ── MATCHING TYPE ── */
.match-container { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px; }
@media(max-width: 580px) { .match-container { grid-template-columns: 1fr; } }
.match-col-label { font-family: 'Fredoka One', cursive; font-size: .75rem; color: #888; text-transform: uppercase; letter-spacing: 1px; text-align: center; margin-bottom: 10px; }
.match-col { display: flex; flex-direction: column; gap: 10px; }

.name-card { background: #fff; border: 3px solid #f0f0f0; border-radius: 16px; padding: 12px 14px; text-align: center; cursor: pointer; box-shadow: 0 4px 14px rgba(0,0,0,.06); transition: all .25s cubic-bezier(.34,1.56,.64,1); font-family: 'Fredoka One', cursive; font-size: .95rem; color: #333; display: flex; align-items: center; justify-content: center; gap: 8px; }
.name-card:hover:not(.matched):not(.matched-wrong):not(.selected-name) { transform: translateY(-3px) scale(1.02); box-shadow: 0 10px 26px rgba(0,0,0,.1); }
.name-card.selected-name    { border-color: var(--blue) !important; background: #e8f1ff; color: #1a4fa0; transform: scale(1.03); }
.name-card.matched           { border-color: #40c057; background: #d3f9d8; color: #1e6030; cursor: default; }
.name-card.matched-wrong     { border-color: #ff4444; background: #ffe3e3; color: #a00; cursor: default; }
.name-card.reveal-correct-match { border-color: #40c057 !important; background: #d3f9d8 !important; color: #1e6030 !important; }
.name-card.reveal-wrong-match   { border-color: #ff4444 !important; background: #ffe3e3 !important; color: #a00 !important; }

/* ── LETTER + PICTURE CARD ── */
.img-card { background: #fff; border: 3px solid #f0f0f0; border-radius: 16px; padding: 10px; cursor: pointer; box-shadow: 0 4px 14px rgba(0,0,0,.06); transition: all .25s cubic-bezier(.34,1.56,.64,1); display: flex; flex-direction: column; align-items: center; gap: 6px; }
.img-card:hover:not(.matched):not(.matched-wrong):not(.selected-img) { transform: translateY(-3px) scale(1.02); box-shadow: 0 10px 26px rgba(0,0,0,.1); }
.img-card.selected-img { border-color: var(--blue) !important; background: #e8f1ff; transform: scale(1.03); }
.img-card.matched       { border-color: #40c057; background: #d3f9d8; cursor: default; }
.img-card.matched-wrong { border-color: #ff4444; background: #ffe3e3; cursor: default; }
.img-card.reveal-correct-match { border-color: #40c057 !important; background: #d3f9d8 !important; }
.img-card.reveal-wrong-match   { border-color: #ff4444 !important; background: #ffe3e3 !important; }
.img-card img { width: 100%; height: 65px; object-fit: cover; border-radius: 10px; pointer-events: none; }
.img-card .img-label { font-size: .68rem; font-weight: 800; color: #aaa; font-family: 'Fredoka One', cursive; }

/* ── REVIEW NOTE ── */
.q-review-note { margin-top: 12px; padding: 10px 14px; border-radius: 12px; font-size: .82rem; font-weight: 800; display: none; }
.q-review-note.correct { background: #d3f9d8; color: #1e6030; display: flex; align-items: center; gap: 8px; }
.q-review-note.wrong   { background: #ffe3e3; color: #a00000; display: flex; align-items: center; gap: 8px; }

.match-hint { font-size: .8rem; color: #bbb; text-align: center; font-weight: 700; margin-top: -6px; margin-bottom: 14px; }

/* ── SUBMIT AREA ── */
.submit-area { text-align: center; padding: 28px; background: #fff; border-radius: 24px; box-shadow: 0 6px 24px rgba(0,0,0,.08); border: 2px solid #eee; }
.unanswered-warn { display: none; margin-bottom: 14px; background: #fff9db; border: 1.5px solid #ffe066; border-radius: 12px; padding: 10px 16px; font-size: .85rem; font-weight: 800; color: #a07000; }
.btn-submit { font-family: 'Fredoka One', cursive; font-size: 1.15rem; padding: 14px 48px; border-radius: var(--pill); background: var(--green-dark); color: #fff; border: none; cursor: pointer; box-shadow: 0 6px 20px rgba(26,46,26,.3); transition: transform .22s cubic-bezier(.34,1.56,.64,1), box-shadow .22s; display: inline-flex; align-items: center; gap: 10px; }
.btn-submit:hover { transform: scale(1.05); box-shadow: 0 10px 28px rgba(26,46,26,.38); }
.btn-submit:active { transform: scale(.97); }

/* ── RESULT OVERLAY ── */
.result-overlay { display: none; position: fixed; inset: 0; background: rgba(15,20,15,.65); backdrop-filter: blur(8px); z-index: 500; align-items: center; justify-content: center; padding: 20px; }
.result-overlay.active { display: flex; }
.result-card { background: #fff; border-radius: 28px; padding: 36px 40px 32px; text-align: center; max-width: 480px; width: 100%; box-shadow: 0 20px 60px rgba(0,0,0,.3); animation: resultPop .5s cubic-bezier(.34,1.56,.64,1); }
@keyframes resultPop { from{opacity:0;transform:scale(.7) translateY(40px)} to{opacity:1;transform:none} }
.result-emoji { font-size: 4.5rem; margin-bottom: 10px; }
.result-title { font-family: 'Fredoka One', cursive; font-size: 2rem; color: var(--green-dark); margin-bottom: 4px; }
.result-sub   { font-size: .9rem; color: #aaa; font-weight: 700; margin-bottom: 18px; }

.score-breakdown { display: flex; gap: 12px; justify-content: center; margin-bottom: 18px; flex-wrap: wrap; }
.score-chip { font-family: 'Fredoka One', cursive; font-size: .9rem; padding: 8px 18px; border-radius: var(--pill); border: 2.5px solid; display: flex; align-items: center; gap: 6px; }
.score-chip.mc    { background: #fff0f5; border-color: var(--vowel); color: var(--vowel); }
.score-chip.mt    { background: #f0f5ff; border-color: var(--cons); color: var(--cons); }
.score-chip.total { background: #fff9db; border-color: var(--warn); color: #a07000; font-size: 1.05rem; }

.result-verdict { font-family: 'Fredoka One', cursive; font-size: 1.05rem; padding: 6px 20px; border-radius: var(--pill); display: inline-block; margin-bottom: 22px; }
.result-verdict.pass { background: #40c057; color: #fff; }
.result-verdict.fail { background: #ff4444; color: #fff; }
.result-actions { display: flex; gap: 12px; justify-content: center; flex-wrap: wrap; }
.btn-review  { font-family: 'Fredoka One', cursive; font-size: .95rem; padding: 12px 28px; border-radius: var(--pill); border: 2.5px solid var(--green-dark); background: transparent; color: var(--green-dark); cursor: pointer; transition: all .2s cubic-bezier(.34,1.56,.64,1); }
.btn-review:hover { background: var(--green-dark); color: #fff; transform: scale(1.04); }
.btn-go-back { font-family: 'Fredoka One', cursive; font-size: .95rem; padding: 12px 28px; border-radius: var(--pill); background: var(--green-dark); color: #fff; border: none; cursor: pointer; box-shadow: 0 5px 16px rgba(26,46,26,.25); transition: all .2s cubic-bezier(.34,1.56,.64,1); }
.btn-go-back:hover { transform: scale(1.04); }

/* ── REVIEW BANNER ── */
.review-banner { display: none; background: var(--green-dark); color: #fff; padding: 14px 24px; border-radius: 18px; margin-bottom: 20px; font-family: 'Fredoka One', cursive; font-size: .95rem; align-items: center; gap: 12px; box-shadow: 0 6px 20px rgba(26,46,26,.22); }
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
    <div class="lnav-title">🔤 Letters Final Exam</div>
</nav>

<div class="page-wrap">

    <!-- REVIEW BANNER -->
    <div class="review-banner" id="reviewBanner">
        <i class="fas fa-magnifying-glass"></i>
        <span>Review Mode — Check your answers below!</span>
        <span class="review-score-chip" id="reviewScoreChip">Score: 0 / 25</span>
    </div>

    <!-- HEADER -->
    <div class="exam-header">
        <div class="exam-badge"><i class="fas fa-font"></i> Final Exam</div>
        <h1 class="exam-title">🔤 Letters Final Exam</h1>
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
    <div class="section-divider" style="background:#1a2e4a;">
        <div class="sec-icon">🔗</div>
        <div class="sec-info">
            <div class="sec-title">Part 2 — Matching Type</div>
            <div class="sec-sub">Match the letter to the picture that starts with it!</div>
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
// LETTERS DATA  (from letters.php)
// ═══════════════════════════════════════════
const LETTERS_DATA = [
    { letter:"A", word:"Apple",     img:"pictures/apple.jpg",        color:"#FF6B6B" },
    { letter:"B", word:"Banana",    img:"pictures/bananas.jpg",       color:"#FFD93D" },
    { letter:"C", word:"Cat",       img:"pictures/catt.jpg",          color:"#FF8E53" },
    { letter:"D", word:"Dog",       img:"pictures/dogi.jpg",          color:"#A0522D" },
    { letter:"E", word:"Elephant",  img:"pictures/elephantt.jpg",     color:"#6B7B8D" },
    { letter:"F", word:"Frog",      img:"pictures/frogg.jpg",         color:"#6BCB77" },
    { letter:"G", word:"Giraffe",   img:"pictures/giraffee.jpg",      color:"#F9A825" },
    { letter:"H", word:"Horse",     img:"pictures/horsee.jpg",        color:"#E57373" },
    { letter:"I", word:"Ice Cream", img:"pictures/ice creamm.jpg",    color:"#FD79A8" },
    { letter:"J", word:"Jellyfish", img:"pictures/jellyfishh.jpg",    color:"#845EC2" },
    { letter:"K", word:"Kangaroo",  img:"pictures/kangarooo.jpg",     color:"#C0854A" },
    { letter:"L", word:"Lion",      img:"pictures/lionn.jpg",         color:"#E6AC00" },
    { letter:"M", word:"Monkey",    img:"pictures/monkeyy.jpg",       color:"#7B5EA7" },
    { letter:"N", word:"Nest",      img:"pictures/nestt.jpg",         color:"#5C9E6B" },
    { letter:"O", word:"Octopus",   img:"pictures/octopuss.jpg",      color:"#E53935" },
    { letter:"P", word:"Parrot",    img:"pictures/parrott.jpg",       color:"#26A69A" },
    { letter:"R", word:"Rabbit",    img:"pictures/rabbitt.jpg",       color:"#F48FB1" },
    { letter:"S", word:"Sunflower", img:"pictures/sunflowerr.jpg",    color:"#FDD835" },
    { letter:"T", word:"Tiger",     img:"pictures/tigerr.jpg",        color:"#FF6F00" },
    { letter:"U", word:"Umbrella",  img:"pictures/umbrellaa.jpg",     color:"#1E88E5" },
    { letter:"W", word:"Whale",     img:"pictures/whalee.jpg",        color:"#00ACC1" },
    { letter:"Z", word:"Zebra",     img:"pictures/zebraa.jpg",        color:"#424242" },
];

const VOWELS = ['A','E','I','O','U'];

// ── MULTIPLE CHOICE (20 questions, 1 pt each = 20 pts) ──
const MC_QUESTIONS = [
    { q:"What letter comes after A?",                  c:["B","C","D","E"],       a:"B"  },
    { q:"What letter comes before D?",                 c:["A","B","C","E"],       a:"C"  },
    { q:"Which of these is a VOWEL?",                  c:["B","C","A","D"],       a:"A"  },
    { q:"What letter comes after M?",                  c:["N","O","P","L"],       a:"N"  },
    { q:"Which of these is NOT a vowel?",              c:["A","E","B","I"],       a:"B"  },
    { q:"What is the FIRST letter of the alphabet?",   c:["B","Z","A","E"],       a:"A"  },
    { q:"What is the LAST letter of the alphabet?",    c:["X","Y","W","Z"],       a:"Z"  },
    { q:"How many vowels are in the alphabet?",        c:["3","4","5","6"],       a:"5"  },
    { q:"Which letter does 'ELEPHANT' start with?",    c:["A","E","I","O"],       a:"E"  },
    { q:"Which letter does 'UMBRELLA' start with?",    c:["A","E","U","O"],       a:"U"  },
    { q:"What letter comes after G?",                  c:["F","H","I","J"],       a:"H"  },
    { q:"What letter comes before K?",                 c:["I","J","L","M"],       a:"J"  },
    { q:"Which letter does 'ORANGE' start with?",      c:["A","E","I","O"],       a:"O"  },
    { q:"Which letter does 'IGLOO' start with?",       c:["A","E","I","O"],       a:"I"  },
    { q:"What letter comes between P and R?",          c:["N","O","Q","S"],       a:"Q"  },
    { q:"Which letter does 'APPLE' start with?",       c:["A","B","C","D"],       a:"A"  },
    { q:"How many letters are in the English alphabet?", c:["24","25","26","27"], a:"26" },
    { q:"Which letter does 'BANANA' start with?",      c:["A","B","C","D"],       a:"B"  },
    { q:"Which of these is a vowel?",                  c:["F","G","O","R"],       a:"O"  },
    { q:"What letter comes after Y?",                  c:["W","X","Y","Z"],       a:"Z"  },
];

// ── MATCHING TYPE — Letter → Picture (10 pairs) ──
const MT_PAIRS = [
    LETTERS_DATA.find(l => l.letter === "A"),
    LETTERS_DATA.find(l => l.letter === "B"),
    LETTERS_DATA.find(l => l.letter === "C"),
    LETTERS_DATA.find(l => l.letter === "D"),
    LETTERS_DATA.find(l => l.letter === "E"),
    LETTERS_DATA.find(l => l.letter === "F"),
    LETTERS_DATA.find(l => l.letter === "G"),
    LETTERS_DATA.find(l => l.letter === "H"),
    LETTERS_DATA.find(l => l.letter === "I"),
    LETTERS_DATA.find(l => l.letter === "L"),
];

const MC_TOTAL   = 20;   // 20 × 1pt = 20pts
const MT_TOTAL   = 10;   // 10 × 2pts = 20pts
const TOTAL      = MC_TOTAL + MT_TOTAL; // 30 items
const MAX_SCORE  = 40;   // 40 points total
const PASS_SCORE = 24;   // 60% of 40
const LETTERS_ARR = ['A','B','C','D'];

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

        const choicesHTML = item.c.map((choice, ci) => {
            // Highlight vowels in pink, consonants in blue
            const isVowel = VOWELS.includes(choice);
            const dotStyle = choice.length === 1 && (isVowel ? `color:var(--vowel);font-weight:900;` : `color:var(--cons);font-weight:900;`) ;
            return `
                <button class="choice-btn" id="mcchoice-${i}-${ci}" onclick="selectMC(${i}, '${choice}', this)">
                    <span class="choice-letter">${LETTERS_ARR[ci]}</span>
                    <span style="${dotStyle || ''}">${choice}</span>
                </button>
            `;
        }).join('');

        card.innerHTML = `
            <div class="q-num-row">
                <div class="q-num-badge mc-badge">${i + 1}</div>
                <div style="font-family:'Fredoka One',cursive;font-size:.78rem;color:#bbb;text-transform:uppercase;letter-spacing:.8px;">Question ${i + 1} of ${MC_TOTAL}</div>
                <i class="fas fa-check-circle q-status-icon" id="mcicon-${i}"></i>
            </div>
            <div class="q-text">${item.q}</div>
            <div class="choices-grid">${choicesHTML}</div>
            <div class="q-review-note" id="mcrnote-${i}"></div>
        `;
        area.appendChild(card);
    });
}

function selectMC(qIdx, choice, clickedBtn) {
    if (examSubmitted) return;
    mcAnswers[qIdx] = choice;
    MC_QUESTIONS[qIdx].c.forEach((_, ci) => {
        document.getElementById('mcchoice-' + qIdx + '-' + ci).classList.remove('selected');
    });
    clickedBtn.classList.add('selected');
    updateProgress();
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

    const namesHTML = MT_PAIRS.map((pair, i) => {
        const isVowel = VOWELS.includes(pair.letter);
        const clr = isVowel ? 'var(--vowel)' : 'var(--cons)';
        return `
            <div class="name-card" id="namecard-${i}" onclick="selectName(${i})">
                <span style="font-family:'Fredoka One',cursive;font-size:1.4rem;color:${clr};">${pair.letter}</span>
                <span style="font-size:.7rem;color:#bbb;font-weight:700;">${pair.word}</span>
            </div>
        `;
    }).join('');

    const imgsHTML = mtImgOrder.map((pairIdx, pos) => {
        const pair = MT_PAIRS[pairIdx];
        return `
            <div class="img-card" id="imgcard-${pos}" data-pair-idx="${pairIdx}" onclick="selectImg(${pos})">
                <img src="${pair.img}" alt="${pair.word}" onerror="this.parentElement.querySelector('.img-label').textContent='${pair.letter}';this.style.display='none'">
                <div class="img-label">?</div>
            </div>
        `;
    }).join('');

    card.innerHTML = `
        <div class="q-num-row">
            <div class="q-num-badge mt-badge">🔗</div>
            <div style="font-family:'Fredoka One',cursive;font-size:.78rem;color:#1a4fa0;text-transform:uppercase;letter-spacing:.8px;">Match the Letter to its Picture</div>
        </div>
        <p class="match-hint">👈 Click a letter on the left, then click the picture it starts with on the right!</p>
        <div class="match-container">
            <div>
                <div class="match-col-label">🔤 Letters</div>
                <div class="match-col" id="namesCol">${namesHTML}</div>
            </div>
            <div>
                <div class="match-col-label">🖼️ Pictures</div>
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
    imgCard.querySelector('.img-label').textContent = MT_PAIRS[selectedNameIdx].letter + ' — ' + MT_PAIRS[selectedNameIdx].word;
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

    // Score MC: 1 pt each
    finalMcScore = 0;
    MC_QUESTIONS.forEach((item, i) => { if (mcAnswers[i] === item.a) finalMcScore++; });

    // Score MT: 2 pts each correct pair
    finalMtScore = 0;
    MT_PAIRS.forEach((pair, nameIdx) => {
        const imgPos = mtAnswers[nameIdx];
        const matchedIdx = parseInt(document.getElementById('imgcard-' + imgPos).dataset.pairIdx);
        if (matchedIdx === nameIdx) finalMtScore += 2;
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
    const passed = total >= PASS_SCORE;
    const pct    = Math.round((total / MAX_SCORE) * 100);
    document.getElementById('resultEmoji').textContent = passed ? '🎉' : '😢';
    document.getElementById('resultTitle').textContent = passed ? 'Super Reader!' : 'Keep Practicing!';
    document.getElementById('resultSub').textContent   = passed ? 'You passed the Letters Final Exam!' : 'Study your ABCs and try again!';
    document.getElementById('mcScore').textContent    = finalMcScore;
    document.getElementById('mtScore').textContent    = Math.round(finalMtScore / 2); // show correct count (not pts)
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
            ? '✅ ' + pair.letter + ' — ' + pair.word
            : '❌ ' + MT_PAIRS[matchedIdx].letter + ' — ' + MT_PAIRS[matchedIdx].word;
    });

    const mtNote = document.getElementById('mtrnote');
    const mtPts  = mtCorrect * 2;
    mtNote.className = 'q-review-note ' + (mtCorrect === MT_TOTAL ? 'correct' : 'wrong');
    mtNote.innerHTML = (mtCorrect === MT_TOTAL
        ? '<i class="fas fa-check-circle"></i> Perfect matching!'
        : '<i class="fas fa-times-circle"></i> ')
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
    const colors = ['#e8336d','#1a6de8','#fcc419','#40c057','#845EC2','#ff8e53','#00acc1'];
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