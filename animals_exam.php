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
$lesson_name = "animals";
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
<title>Animal Final Exam — E-KINDER</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Fredoka+One&family=Nunito:wght@400;600;700;800;900&display=swap" rel="stylesheet">
<style>
:root {
    --green-dark: #2e7d32;
    --cream: #fdf8f0;
    --pill: 999px;
    --accent: #51cf66;
    --danger: #ff4444;
    --warn: #fcc419;
    --blue: #4D96FF;
    --land: #51cf66;
    --air: #74c0fc;
    --water: #4D96FF;
}
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

body {
    font-family: 'Nunito', sans-serif;
    background: #d5fcdb;
    min-height: 100vh;
    overflow-x: hidden;
    background-image:
        radial-gradient(circle at 10% 20%, rgba(160,82,45,0.07) 0%, transparent 40%),
        radial-gradient(circle at 85% 70%, rgba(30,136,229,0.07) 0%, transparent 40%),
        radial-gradient(circle at 50% 95%, rgba(0,172,193,0.07) 0%, transparent 40%);
}
body::before {
    content: '';
    position: fixed;
    inset: 0;
    pointer-events: none;
    z-index: 0;
    background-image: radial-gradient(circle, rgba(0,0,0,.03) 1.2px, transparent 1.2px);
    background-size: 26px 26px;
}

/* ── NAV ── */
.lesson-nav {
    height: 64px;
    padding: 0 28px;
    background: rgba(255,255,255,.95);
    backdrop-filter: blur(16px);
    border-bottom: 1px solid rgba(0,0,0,.07);
    box-shadow: 0 2px 18px rgba(0,0,0,.06);
    position: sticky;
    top: 0;
    z-index: 200;
    display: flex;
    align-items: center;
    gap: 14px;
}
.lnav-back {
    display: inline-flex; align-items: center; gap: 8px;
    background: var(--green-dark); color: #fff;
    font-family: 'Fredoka One', cursive; font-size: .88rem;
    padding: 8px 20px; border-radius: var(--pill);
    text-decoration: none;
    box-shadow: 0 4px 14px rgba(0,0,0,.22);
    transition: transform .22s cubic-bezier(.34,1.56,.64,1);
    flex-shrink: 0;
}
.lnav-back:hover { transform: scale(1.06); color: #fff; }
.lnav-title {
    font-family: 'Fredoka One', cursive; font-size: 1.1rem;
    color: var(--green-dark); flex: 1; text-align: center;
}

/* ── LAYOUT ── */
.page-wrap {
    position: relative; z-index: 1;
    max-width: 820px; margin: 0 auto;
    padding: 32px 20px 80px;
}

/* ── HEADER ── */
.exam-header { text-align: center; margin-bottom: 28px; }
.exam-badge {
    display: inline-flex; align-items: center; gap: 8px;
    background: #fff; border: 2px solid #dde8dd;
    border-radius: var(--pill); padding: 6px 18px;
    font-family: 'Fredoka One', cursive; font-size: .78rem;
    color: #2a4a2a; box-shadow: 0 2px 10px rgba(0,0,0,.06);
    margin-bottom: 14px;
}
.exam-title { font-family: 'Fredoka One', cursive; font-size: clamp(1.8rem,4vw,2.4rem); color: var(--green-dark); margin-bottom: 6px; }
.exam-sub { font-size: .9rem; color: #a0a8a0; font-weight: 700; }

/* ── PROGRESS BAR ── */
.progress-card {
    background: #fff; border-radius: 20px; padding: 16px 24px;
    box-shadow: 0 4px 18px rgba(0,0,0,.06); border: 1.5px solid #eee;
    margin-bottom: 24px; display: flex; align-items: center; gap: 20px; flex-wrap: wrap;
}
.prog-label { font-family: 'Fredoka One', cursive; font-size: .8rem; color: #bbb; text-transform: uppercase; letter-spacing: .8px; white-space: nowrap; }
.prog-bar-outer { flex: 1; height: 14px; background: #f0f0f0; border-radius: 99px; overflow: hidden; min-width: 120px; }
.prog-bar-inner { height: 100%; background: linear-gradient(90deg, #51cf66, #94d82d); border-radius: 99px; transition: width .5s cubic-bezier(.34,1.56,.64,1); width: 0%; }
.prog-count { font-family: 'Fredoka One', cursive; font-size: 1rem; color: var(--green-dark); white-space: nowrap; }

/* ── SECTION DIVIDER ── */
.section-divider {
    display: flex; align-items: center; gap: 16px;
    background: var(--green-dark); color: #fff;
    border-radius: 18px; padding: 14px 22px;
    margin-bottom: 20px; margin-top: 12px;
    box-shadow: 0 6px 20px rgba(26,46,26,.22);
}
.section-divider .sec-icon { font-size: 1.4rem; }
.section-divider .sec-info { flex: 1; }
.section-divider .sec-title { font-family: 'Fredoka One', cursive; font-size: 1rem; line-height: 1; }
.section-divider .sec-sub { font-size: .75rem; opacity: .7; margin-top: 2px; font-weight: 700; }
.section-divider .sec-chip { background: rgba(255,255,255,.18); border-radius: var(--pill); padding: 4px 14px; font-family: 'Fredoka One', cursive; font-size: .8rem; white-space: nowrap; }

/* ── QUESTION CARDS (MC) ── */
.exam-scroll-area { display: flex; flex-direction: column; gap: 20px; margin-bottom: 28px; }

.q-card {
    background: #fff; border-radius: 20px; padding: 22px 24px 18px;
    box-shadow: 0 4px 16px rgba(0,0,0,.07); border: 2.5px solid #eee;
    transition: border-color .3s, box-shadow .3s;
    animation: cardIn .4s ease both;
}
@keyframes cardIn { from { opacity: 0; transform: translateY(18px); } to { opacity: 1; transform: none; } }
.q-card.answered-correct { border-color: #51cf66; box-shadow: 0 4px 18px rgba(81,207,102,.15); }
.q-card.answered-wrong   { border-color: #ff4444; box-shadow: 0 4px 18px rgba(255,68,68,.12); }
.q-card.unanswered-highlight { border-color: var(--warn); box-shadow: 0 4px 18px rgba(252,196,25,.2); animation: shake .4s ease; }
@keyframes shake { 0%,100%{transform:none} 20%,60%{transform:translateX(-5px)} 40%,80%{transform:translateX(5px)} }

.q-num-row { display: flex; align-items: center; gap: 10px; margin-bottom: 14px; }
.q-num-badge {
    width: 32px; height: 32px; border-radius: 50%;
    background: var(--green-dark); color: #fff;
    font-family: 'Fredoka One', cursive; font-size: .85rem;
    display: flex; align-items: center; justify-content: center; flex-shrink: 0;
}
.q-num-badge.mc-badge { background: #51cf66; }
.q-num-badge.mt-badge { background: #008616; }
.q-status-icon { margin-left: auto; font-size: 1.1rem; display: none; }
.q-card.answered-correct .q-status-icon { display: block; color: #51cf66; }
.q-card.answered-wrong   .q-status-icon { display: block; color: #ff4444; }

.q-text { font-family: 'Fredoka One', cursive; font-size: 1.1rem; color: #008616; margin-bottom: 16px; line-height: 1.35; }

/* Animal image in question */
.q-animal-img {
    width: 80px; height: 80px; border-radius: 14px;
    object-fit: cover; border: 3px solid #eee;
    margin-bottom: 14px; display: block;
}

/* ── CHOICES GRID ── */
.choices-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }
@media(max-width: 480px) { .choices-grid { grid-template-columns: 1fr; } }

.choice-btn {
    width: 100%; padding: 12px 16px; border-radius: 14px;
    border: 2.5px solid #eee; background: #fafafa;
    font-family: 'Nunito', sans-serif; font-weight: 800; font-size: .92rem; color: #444;
    cursor: pointer; text-align: left;
    transition: all .2s cubic-bezier(.34,1.56,.64,1);
    display: flex; align-items: center; gap: 10px;
}
.choice-btn:hover:not(:disabled):not(.selected) {
    border-color: #51cf66; background: #f0faf2; transform: translateY(-2px) scale(1.01);
    box-shadow: 0 5px 14px rgba(0,0,0,.08);
}
.choice-btn.selected { border-color: var(--blue); background: #e8f1ff; color: #1a4fa0; box-shadow: 0 4px 14px rgba(77,150,255,.2); }
.choice-btn.reveal-correct { border-color: #51cf66 !important; background: #d3f9d8 !important; color: #1e6030 !important; }
.choice-btn.reveal-wrong   { border-color: #ff4444 !important; background: #ffe3e3 !important; color: #a00 !important; }
.choice-letter {
    width: 24px; height: 24px; border-radius: 8px; background: rgba(0,0,0,.06);
    display: flex; align-items: center; justify-content: center;
    font-family: 'Fredoka One', cursive; font-size: .75rem; flex-shrink: 0;
    transition: background .2s, color .2s;
}
.choice-btn.selected .choice-letter      { background: var(--blue); color: #fff; }
.choice-btn.reveal-correct .choice-letter { background: #51cf66; color: #fff; }
.choice-btn.reveal-wrong .choice-letter   { background: #ff4444; color: #fff; }

/* ── MATCHING TYPE ── */
.match-container {
    display: grid; grid-template-columns: 1fr 1fr;
    gap: 20px; margin-bottom: 20px;
}
@media(max-width: 580px) { .match-container { grid-template-columns: 1fr; } }

.match-col-label {
    font-family: 'Fredoka One', cursive; font-size: .75rem;
    color: #157e00; text-transform: uppercase; letter-spacing: 1px;
    text-align: center; margin-bottom: 10px;
}
.match-col { display: flex; flex-direction: column; gap: 10px; }

.name-card {
    background: #fff; border: 3px solid #f0f0f0; border-radius: 16px;
    padding: 12px 14px; text-align: center; cursor: pointer;
    box-shadow: 0 4px 14px rgba(0,0,0,.06);
    transition: all .25s cubic-bezier(.34,1.56,.64,1);
    font-family: 'Fredoka One', cursive; font-size: .95rem; color: #333;
    display: flex; align-items: center; justify-content: center; gap: 8px;
}
.name-card:hover:not(.matched):not(.disabled):not(.selected-name) {
    transform: translateY(-3px) scale(1.02);
    box-shadow: 0 10px 26px rgba(0,0,0,.1);
}
.name-card.selected-name { border-color: var(--blue) !important; background: #e8f1ff; color: #1a4fa0; transform: scale(1.03); }
.name-card.matched { border-color: #51cf66; background: #d3f9d8; color: #1e6030; cursor: default; }
.name-card.matched-wrong { border-color: #ff4444; background: #ffe3e3; color: #a00; cursor: default; }
.name-card.reveal-correct-match { border-color: #51cf66 !important; background: #d3f9d8 !important; color: #1e6030 !important; }
.name-card.reveal-wrong-match   { border-color: #ff4444 !important; background: #ffe3e3 !important; color: #a00 !important; }

.img-card {
    background: #fff; border: 3px solid #f0f0f0; border-radius: 16px;
    padding: 10px; cursor: pointer;
    box-shadow: 0 4px 14px rgba(0,0,0,.06);
    transition: all .25s cubic-bezier(.34,1.56,.64,1);
    display: flex; flex-direction: column; align-items: center; gap: 6px;
}
.img-card:hover:not(.matched):not(.disabled):not(.selected-img) {
    transform: translateY(-3px) scale(1.02); box-shadow: 0 10px 26px rgba(0,0,0,.1);
}
.img-card.selected-img { border-color: var(--blue) !important; background: #e8f1ff; transform: scale(1.03); }
.img-card.matched { border-color: #51cf66; background: #d3f9d8; cursor: default; }
.img-card.matched-wrong { border-color: #ff4444; background: #ffe3e3; cursor: default; }
.img-card.reveal-correct-match { border-color: #51cf66 !important; background: #d3f9d8 !important; }
.img-card.reveal-wrong-match   { border-color: #ff4444 !important; background: #ffe3e3 !important; }

.img-card img {
    width: 100%;
    height: clamp(80px, 20vw, 250px);
    object-fit: cover;
    border-radius: 10px;
}


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
.img-card .img-label { font-size: .7rem; font-weight: 800; color: #aaa; }

.match-hint { font-size: .8rem; color: #bbb; text-align: center; font-weight: 700; margin-top: -6px; margin-bottom: 14px; }

/* ── REVIEW NOTE ── */
.q-review-note { margin-top: 12px; padding: 10px 14px; border-radius: 12px; font-size: .82rem; font-weight: 800; display: none; }
.q-review-note.correct { background: #d3f9d8; color: #1e6030; display: flex; align-items: center; gap: 8px; }
.q-review-note.wrong   { background: #ffe3e3; color: #a00000; display: flex; align-items: center; gap: 8px; }

/* ── SUBMIT AREA ── */
.submit-area { text-align: center; padding: 28px; background: #fff; border-radius: 24px; box-shadow: 0 6px 24px rgba(0,0,0,.08); border: 2px solid #eee; }
.unanswered-warn { display: none; margin-bottom: 14px; background: #fff9db; border: 1.5px solid #ffe066; border-radius: 12px; padding: 10px 16px; font-size: .85rem; font-weight: 800; color: #a07000; }
.btn-submit {
    font-family: 'Fredoka One', cursive; font-size: 1.15rem;
    padding: 14px 48px; border-radius: var(--pill);
    background: var(--green-dark); color: #fff; border: none; cursor: pointer;
    box-shadow: 0 6px 20px rgba(26,46,26,.3);
    transition: transform .22s cubic-bezier(.34,1.56,.64,1), box-shadow .22s;
    display: inline-flex; align-items: center; gap: 10px;
}
.btn-submit:hover { transform: scale(1.05); box-shadow: 0 10px 28px rgba(26,46,26,.38); }
.btn-submit:active { transform: scale(.97); }

/* ── RESULT OVERLAY ── */
.result-overlay { display: none; position: fixed; inset: 0; background: rgba(15,20,15,.65); backdrop-filter: blur(8px); z-index: 500; align-items: center; justify-content: center; padding: 20px; }
.result-overlay.active { display: flex; }
.result-card {
    background: #fff; border-radius: 28px; padding: 36px 40px 32px; text-align: center;
    max-width: 480px; width: 100%; box-shadow: 0 20px 60px rgba(0,0,0,.3);
    animation: resultPop .5s cubic-bezier(.34,1.56,.64,1);
}
@keyframes resultPop { from { opacity:0; transform: scale(.7) translateY(40px); } to { opacity:1; transform:none; } }
.result-emoji { font-size: 4.5rem; margin-bottom: 10px; }
.result-title { font-family: 'Fredoka One', cursive; font-size: 2rem; color: var(--green-dark); margin-bottom: 4px; }
.result-sub   { font-size: .9rem; color: #aaa; font-weight: 700; margin-bottom: 18px; }

.score-breakdown {
    display: flex; gap: 12px; justify-content: center; margin-bottom: 18px; flex-wrap: wrap;
}
.score-chip {
    font-family: 'Fredoka One', cursive; font-size: .9rem;
    padding: 8px 18px; border-radius: var(--pill); border: 2.5px solid;
    display: flex; align-items: center; gap: 6px;
}
.score-chip.mc { background: #f0faf2; border-color: #51cf66; color: #1e6030; }
.score-chip.mt { background: #e8f1ff; border-color: var(--blue); color: #1a4fa0; }
.score-chip.total { background: #fff9db; border-color: var(--warn); color: #a07000; font-size: 1.05rem; }

.result-verdict { font-family: 'Fredoka One', cursive; font-size: 1.05rem; padding: 6px 20px; border-radius: var(--pill); display: inline-block; margin-bottom: 22px; }
.result-verdict.pass { background: #51cf66; color: #fff; }
.result-verdict.fail { background: #ff4444; color: #fff; }
.result-actions { display: flex; gap: 12px; justify-content: center; flex-wrap: wrap; }
.btn-review { font-family: 'Fredoka One', cursive; font-size: .95rem; padding: 12px 28px; border-radius: var(--pill); border: 2.5px solid var(--green-dark); background: transparent; color: var(--green-dark); cursor: pointer; transition: all .2s cubic-bezier(.34,1.56,.64,1); }
.btn-review:hover { background: var(--green-dark); color: #fff; transform: scale(1.04); }
.btn-go-back { font-family: 'Fredoka One', cursive; font-size: .95rem; padding: 12px 28px; border-radius: var(--pill); background: var(--green-dark); color: #fff; border: none; cursor: pointer; box-shadow: 0 5px 16px rgba(26,46,26,.25); transition: all .2s cubic-bezier(.34,1.56,.64,1); }
.btn-go-back:hover { transform: scale(1.04); }

/* ── REVIEW BANNER ── */
.review-banner { display: none; background: var(--green-dark); color: #fff; padding: 14px 24px; border-radius: 18px; margin-bottom: 20px; font-family: 'Fredoka One', cursive; font-size: .95rem; align-items: center; gap: 12px; box-shadow: 0 6px 20px rgba(26,46,26,.22); }
.review-banner.visible { display: flex; }
.review-score-chip { margin-left: auto; background: rgba(255,255,255,.15); border-radius: var(--pill); padding: 4px 14px; font-size: .85rem; white-space: nowrap; }

/* ── CONFETTI ── */
.cp { position: fixed; border-radius: 2px; pointer-events: none; z-index: 9999; animation: fall linear forwards; }
@keyframes fall { 0% { transform: translateY(0) rotate(0deg); opacity: 1; } 100% { transform: translateY(100vh) rotate(720deg); opacity: 0; } }
</style>
</head>

<body>

<!-- NAV -->
<nav class="lesson-nav">
    <a href="activities.php" class="lnav-back">
        <i class="fas fa-arrow-left"></i> Back
    </a>
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
        <div class="exam-badge"> Final Exam</div>
        <h1 class="exam-title">🐾 Animal Final Exam</h1>
        <p class="exam-sub" id="examSubtitle">Answer all 30 items, then click Submit!</p>
    </div>

    <!-- PROGRESS BAR -->
    <div class="progress-card">
        <span class="prog-label">Progress</span>
        <div class="prog-bar-outer">
            <div class="prog-bar-inner" id="progressBar"></div>
        </div>
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
    <div class="section-divider" style="background: #2e7d32;">
        <div class="sec-icon">🔗</div>
        <div class="sec-info">
            <div class="sec-title">Part 2 — Matching Type</div>
            <div class="sec-sub">Click a name, then click the matching animal picture.</div>
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
        <div class="score-chip total" style="margin: 0 auto 18px;display:inline-flex;">
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
// DATA
// ═══════════════════════════════════════════
const ANIMALS_DATA = [
    {name:"Dog",    emoji:"🐕", img:"pictures/animals/dogi.jpg",    cat:"Land",  desc:"Loyal and friendly — they love to play and cuddle!"},
    {name:"Cat",    emoji:"🐈", img:"pictures/animals/cat.jpg",     cat:"Land",  desc:"Independent and curious — they love to explore!"},
    {name:"Cow",    emoji:"🐄", img:"pictures/animals/cow.jpg",     cat:"Land",  desc:"Large and gentle — they give us milk every day!"},
    {name:"Horse",  emoji:"🐴", img:"pictures/animals/horse.jpg",   cat:"Land",  desc:"Strong and fast — people ride them on adventures!"},
    {name:"Pig",    emoji:"🐷", img:"pictures/animals/pig.jpg",     cat:"Land",  desc:"Smart and playful — they have a cute curly tail!"},
    {name:"Chicken",emoji:"🐓", img:"pictures/animals/chicken.jpg", cat:"Land",  desc:"Busy and clucky — they lay the eggs we eat!"},
    {name:"Rabbit", emoji:"🐰", img:"pictures/animals/rabbit.jpg",  cat:"Land",  desc:"Fluffy and quick — they hop around all day!"},
    {name:"Goat",   emoji:"🐐", img:"pictures/animals/goat.jpg",    cat:"Land",  desc:"Adventurous and sure-footed — they can climb anything!"},
    {name:"Eagle",  emoji:"🦅", img:"pictures/animals/eagle.jpg",   cat:"Air",   desc:"Mighty and sharp-eyed — they soar high in the sky!"},
    {name:"Parrot", emoji:"🦜", img:"pictures/animals/parrot.jpg",  cat:"Air",   desc:"Colorful and clever — they can mimic our voice!"},
    {name:"Owl",    emoji:"🦉", img:"pictures/animals/owl.jpg",     cat:"Air",   desc:"Wise and silent — they hunt at night with big eyes!"},
    {name:"Bat",    emoji:"🦇", img:"pictures/animals/bat.jpg",     cat:"Air",   desc:"Nocturnal flyer — they use sound to find their way!"},
    {name:"Fish",   emoji:"🐠", img:"pictures/animals/fishh.jpg",   cat:"Water", desc:"Graceful and colorful — they glide through the water!"},
    {name:"Shark",  emoji:"🦈", img:"pictures/animals/sharky.jpg",  cat:"Water", desc:"Powerful and fast — the great hunter of the ocean!"},
    {name:"Dolphin",emoji:"🐬", img:"pictures/animals/dolphin.jpg", cat:"Water", desc:"Playful and smart — one of the cleverest animals!"},
    {name:"Whale",  emoji:"🐋", img:"pictures/animals/whale.jpg",   cat:"Water", desc:"Enormous and gentle — the biggest animal on Earth!"},
    {name:"Turtle", emoji:"🐢", img:"pictures/animals/turtle.jpg",  cat:"Water", desc:"Slow and steady — they carry their home on their back!"},
    {name:"Octopus",emoji:"🐙", img:"pictures/animals/octopus.jpg", cat:"Water", desc:"Clever and sneaky — they have eight amazing arms!"},
    {name:"Crab",   emoji:"🦀", img:"pictures/animals/crab.jpg",    cat:"Water", desc:"Tough and sideways — they walk left and right!"},
    {name:"Flamingo",emoji:"🦩",img:"pictures/animals/flamingo.jpg",cat:"Air",   desc:"Graceful and pink — they stand on one leg to rest!"},
    {name:"Dove",   emoji:"🕊️", img:"pictures/animals/dove.jpg",   cat:"Air",   desc:"Gentle and peaceful — a symbol of love and peace!"},
    {name:"Swan",   emoji:"🦢", img:"pictures/animals/swan.jpg",    cat:"Air",   desc:"Elegant and white — they glide gracefully on water!"},
    {name:"Starfish",emoji:"⭐",img:"pictures/animals/starfish.jpg",cat:"Water", desc:"Beautiful and star-shaped — they live on the ocean floor!"},
];

// ── MULTIPLE CHOICE QUESTIONS (15) ──
const MC_QUESTIONS = [
    {q:"What animal barks?",                        img:null, c:["Dog","Cat","Fish","Eagle"],   a:"Dog"},
    {q:"What animal says meow?",                    img:null, c:["Dog","Cat","Horse","Cow"],    a:"Cat"},
    {q:"Where does a Fish live?",                   img:null, c:["Land","Air","Water","Tree"],  a:"Water"},
    {q:"What animal flies HIGH in the sky?",        img:null, c:["Cow","Eagle","Rabbit","Pig"], a:"Eagle"},
    {q:"Which animal gives us milk?",               img:null, c:["Dog","Rabbit","Cow","Goat"],  a:"Cow"},
    {q:"What animal can mimic human voices?",       img:null, c:["Owl","Parrot","Bat","Dove"],  a:"Parrot"},
    {q:"Which animal hunts at night and sleeps upside down?", img:null, c:["Owl","Eagle","Bat","Parrot"], a:"Bat"},
    {q:"What is the biggest animal in the ocean?",  img:null, c:["Shark","Dolphin","Whale","Turtle"], a:"Whale"},
    {q:"Which animal carries its home on its back?",img:null, c:["Crab","Turtle","Octopus","Fish"], a:"Turtle"},
    {q:"What animal lays eggs that we eat?",        img:null, c:["Duck","Dog","Chicken","Rabbit"], a:"Chicken"},
    {q:"Which animal has eight arms?",              img:null, c:["Crab","Starfish","Octopus","Shark"], a:"Octopus"},
    {q:"What animal stands on one leg to rest?",    img:null, c:["Dolphin","Flamingo","Eagle","Owl"], a:"Flamingo"},
    {q:"Which animal hops and is very fluffy?",     img:null, c:["Cat","Rabbit","Dog","Goat"],   a:"Rabbit"},
    {q:"Where do Eagles, Parrots, and Owls live?",  img:null, c:["Water","Land","Air","Cave"],  a:"Air"},
    {q:"What animal is known as the 'great hunter' of the ocean?", img:null, c:["Dolphin","Whale","Turtle","Shark"], a:"Shark"},
    {q:"Which animal is pink and graceful, often standing on one leg?", img:null, c:["Dove","Swan","Flamingo","Parrot"], a:"Flamingo"},
    {q:"What animal uses sound (echolocation) to navigate in the dark?", img:null, c:["Owl","Bat","Eagle","Dove"], a:"Bat"},
    {q:"Which animal is enormous and is the biggest animal on Earth?", img:null, c:["Shark","Elephant","Whale","Dolphin"], a:"Whale"},
    {q:"Where do land animals like Dogs, Cows, and Rabbits live?", img:null, c:["Water","Air","Land","Cave"], a:"Land"},
    {q:"Which animal is known for being very playful and smart in the ocean?", img:null, c:["Shark","Turtle","Dolphin","Fish"], a:"Dolphin"},
];

// ── MATCHING TYPE (10 pairs) ──
// Pick 10 animals for matching
const MT_ANIMALS = [
    ANIMALS_DATA.find(a=>a.name==="Dog"),
    ANIMALS_DATA.find(a=>a.name==="Eagle"),
    ANIMALS_DATA.find(a=>a.name==="Fish"),
    ANIMALS_DATA.find(a=>a.name==="Owl"),
    ANIMALS_DATA.find(a=>a.name==="Dolphin"),
    ANIMALS_DATA.find(a=>a.name==="Rabbit"),
    ANIMALS_DATA.find(a=>a.name==="Parrot"),
    ANIMALS_DATA.find(a=>a.name==="Shark"),
    ANIMALS_DATA.find(a=>a.name==="Cow"),
    ANIMALS_DATA.find(a=>a.name==="Turtle"),
];

const MC_TOTAL = 20;   // 20 × 1pt = 20pts
const MT_TOTAL = 10;   // 10 × 2pts = 20pts
const TOTAL    = MC_TOTAL + MT_TOTAL; // 30 items
const MAX_SCORE = 40;  // 40 points total
const PASS_SCORE = 24; // 60% of 40
const LETTERS  = ['A','B','C','D'];

let mcAnswers  = new Array(MC_TOTAL).fill(null);
let mtAnswers  = new Array(MT_TOTAL).fill(null); // index = name index, value = img index clicked
let examSubmitted = false;
let finalMcScore = 0;
let finalMtScore = 0;

// ── Shuffled image order for matching ──
let mtImgOrder = [];

// ═══════════════════════════════════════════
// HELPER
// ═══════════════════════════════════════════
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

        const choicesHTML = item.c.map((choice, ci) => `
            <button class="choice-btn" id="mcchoice-${i}-${ci}" onclick="selectMC(${i}, '${choice}', this)">
                <span class="choice-letter">${LETTERS[ci]}</span>
                ${choice}
            </button>
        `).join('');

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
let selectedNameIdx = null; // which name is currently selected

function buildMT() {
    const area = document.getElementById('mtArea');
    area.innerHTML = '';

    // Shuffle image column
    mtImgOrder = shuffle(MT_ANIMALS.map((_, i) => i));

    const card = document.createElement('div');
    card.className = 'q-card';
    card.id = 'mtcard-main';
    card.style.animationDelay = '0.6s';

    const namesHTML = MT_ANIMALS.map((animal, i) => `
        <div class="name-card" id="namecard-${i}" onclick="selectName(${i})">
            ${animal.emoji} ${animal.name}
        </div>
    `).join('');

    const imgsHTML = mtImgOrder.map((animalIdx, pos) => {
        const animal = MT_ANIMALS[animalIdx];
        return `
            <div class="img-card" id="imgcard-${pos}" data-animal-idx="${animalIdx}" onclick="selectImg(${pos})">
                <img src="${animal.img}" alt="${animal.name}" onerror="this.parentElement.querySelector('.img-label').textContent='${animal.emoji}';this.style.display='none'">
                <div class="img-label">?</div>
            </div>
        `;
    }).join('');

    card.innerHTML = `
        <div class="q-num-row">
            <div class="q-num-badge mt-badge">🔗</div>
            <div style="font-family:'Fredoka One',cursive;font-size:.78rem;color:#2e7d32;text-transform:uppercase;letter-spacing:.8px;">Match the Name to the Picture</div>
        </div>
        <p class="match-hint"> Click a name on the left, then click the matching picture on the right!</p>
        <div class="match-container">
            <div>
                <div class="match-col-label"> Animal Names</div>
                <div class="match-col" id="namesCol">${namesHTML}</div>
            </div>
            <div>
                <div class="match-col-label"> Animal Pictures</div>
                <div class="match-col" id="imgsCol">${imgsHTML}</div>
            </div>
        </div>
        <div class="q-review-note" id="mtrnote"></div>
    `;
    area.appendChild(card);
}

function selectName(nameIdx) {
    if (examSubmitted) return;
    // If already matched, don't allow reselect — allow change until submit
    const nameCard = document.getElementById('namecard-' + nameIdx);
    if (nameCard.classList.contains('matched') || nameCard.classList.contains('matched-wrong')) return;

    // Clear previous name selection
    document.querySelectorAll('.name-card').forEach(c => c.classList.remove('selected-name'));
    selectedNameIdx = nameIdx;
    nameCard.classList.add('selected-name');
}

function selectImg(imgPos) {
    if (examSubmitted) return;
    if (selectedNameIdx === null) return;

    const animalIdx = parseInt(document.getElementById('imgcard-' + imgPos).dataset.animalIdx);
    const nameCard  = document.getElementById('namecard-' + selectedNameIdx);
    const imgCard   = document.getElementById('imgcard-' + imgPos);

    // If img already matched, ignore
    if (imgCard.classList.contains('matched') || imgCard.classList.contains('matched-wrong')) return;

    // If this name was previously matched, unlink old pair
    if (mtAnswers[selectedNameIdx] !== null) {
        const oldImgPos = mtAnswers[selectedNameIdx];
        const oldImgCard = document.getElementById('imgcard-' + oldImgPos);
        if (oldImgCard) { oldImgCard.classList.remove('matched', 'matched-wrong'); oldImgCard.querySelector('.img-label').textContent = '?'; }
    }

    // Save answer
    mtAnswers[selectedNameIdx] = imgPos;

    // Show as matched (tentative — will reveal after submit)
    nameCard.classList.remove('selected-name');
    nameCard.classList.add('matched');
    imgCard.classList.add('matched');
    imgCard.querySelector('.img-label').textContent = MT_ANIMALS[selectedNameIdx].name;

    selectedNameIdx = null;
    updateProgress();
}

// ═══════════════════════════════════════════
// PROGRESS
// ═══════════════════════════════════════════
function updateProgress() {
    const mcDone = mcAnswers.filter(a => a !== null).length;
    const mtDone = mtAnswers.filter(a => a !== null).length;
    const total  = mcDone + mtDone;
    const pct    = (total / TOTAL) * 100;
    document.getElementById('progressBar').style.width = pct + '%';
    document.getElementById('progressCount').textContent = total + ' / ' + TOTAL + ' answered';
}

// ═══════════════════════════════════════════
// SUBMIT
// ═══════════════════════════════════════════
function submitExam() {
    const mcUnanswered = mcAnswers.map((a, i) => a === null ? i : -1).filter(i => i >= 0);
    const mtUnanswered = mtAnswers.filter(a => a === null).length;

    if (mcUnanswered.length > 0 || mtUnanswered > 0) {
        document.getElementById('unansweredWarn').style.cssText = 'display:flex;align-items:center;gap:8px;';
        if (mcUnanswered.length > 0) {
            const firstCard = document.getElementById('mccard-' + mcUnanswered[0]);
            firstCard.classList.add('unanswered-highlight');
            firstCard.scrollIntoView({ behavior: 'smooth', block: 'center' });
            setTimeout(() => firstCard.classList.remove('unanswered-highlight'), 600);
        } else {
            document.getElementById('mtcard-main').classList.add('unanswered-highlight');
            document.getElementById('mtcard-main').scrollIntoView({ behavior: 'smooth', block: 'center' });
            setTimeout(() => document.getElementById('mtcard-main').classList.remove('unanswered-highlight'), 600);
        }
        return;
    }

    document.getElementById('unansweredWarn').style.display = 'none';
    examSubmitted = true;

    // Score MC (1 pt each)
    finalMcScore = 0;
    MC_QUESTIONS.forEach((item, i) => {
        if (mcAnswers[i] === item.a) finalMcScore++;
    });

    // Score MT (2 pts each)
    finalMtScore = 0;
    MT_ANIMALS.forEach((animal, nameIdx) => {
        const imgPos = mtAnswers[nameIdx];
        const matchedAnimalIdx = parseInt(document.getElementById('imgcard-' + imgPos).dataset.animalIdx);
        if (matchedAnimalIdx === nameIdx) finalMtScore += 2;
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
    document.getElementById('resultTitle').textContent = passed ? 'Amazing Work!' : 'Keep Trying!';
    document.getElementById('resultSub').textContent   = passed ? 'You passed the Animal Final Exam!' : 'Study more and try again!';
    document.getElementById('mcScore').textContent    = finalMcScore;
    document.getElementById('mtScore').textContent    = Math.round(finalMtScore / 2); // show correct count (not pts)
    document.getElementById('totalScore').textContent = total + ' / ' + MAX_SCORE + ' (' + pct + '%)';
    const verdict = document.getElementById('resultVerdict');
    verdict.textContent = passed ? '✅ PASSED' : '❌ FAILED';
    verdict.className   = 'result-verdict ' + (passed ? 'pass' : 'fail');
    document.getElementById('resultOverlay').classList.add('active');
    if (passed) launchConfetti();
}

// ═══════════════════════════════════════════
// REVIEW
// ═══════════════════════════════════════════
function openReview() {
    document.getElementById('resultOverlay').classList.remove('active');

    const banner = document.getElementById('reviewBanner');
    banner.classList.add('visible');
    const total = finalMcScore + finalMtScore;
    const reviewPct = Math.round((total / MAX_SCORE) * 100);
    document.getElementById('reviewScoreChip').textContent = 'Score: ' + total + ' / ' + MAX_SCORE + ' (' + reviewPct + '%)';
    document.getElementById('examSubtitle').textContent = 'Review your answers below!';
    document.getElementById('submitArea').style.display = 'none';

    // Reveal MC
    MC_QUESTIONS.forEach((item, i) => {
        const card = document.getElementById('mccard-' + i);
        const isCorrect = mcAnswers[i] === item.a;
        card.classList.add(isCorrect ? 'answered-correct' : 'answered-wrong');
        item.c.forEach((choice, ci) => {
            const btn = document.getElementById('mcchoice-' + i + '-' + ci);
            if (choice === item.a)          btn.classList.add('reveal-correct');
            else if (choice === mcAnswers[i]) btn.classList.add('reveal-wrong');
        });
        const note = document.getElementById('mcrnote-' + i);
        if (isCorrect) {
            note.className = 'q-review-note correct';
            note.innerHTML = '<i class="fas fa-check-circle"></i> Correct! The answer is <strong>' + item.a + '</strong>.';
        } else {
            note.className = 'q-review-note wrong';
            note.innerHTML = '<i class="fas fa-times-circle"></i> Wrong! You answered <strong>' + mcAnswers[i] + '</strong>. Correct answer: <strong>' + item.a + '</strong>';
        }
    });

    // Reveal MT
    let mtCorrectCount = 0;
    MT_ANIMALS.forEach((animal, nameIdx) => {
        const imgPos = mtAnswers[nameIdx];
        const matchedAnimalIdx = parseInt(document.getElementById('imgcard-' + imgPos).dataset.animalIdx);
        const isCorrect = matchedAnimalIdx === nameIdx;
        if (isCorrect) mtCorrectCount++;

        const nameCard = document.getElementById('namecard-' + nameIdx);
        const imgCard  = document.getElementById('imgcard-' + imgPos);
        nameCard.classList.remove('matched', 'matched-wrong', 'selected-name');
        imgCard.classList.remove('matched', 'matched-wrong');

        if (isCorrect) {
            nameCard.classList.add('reveal-correct-match');
            imgCard.classList.add('reveal-correct-match');
        } else {
            nameCard.classList.add('reveal-wrong-match');
            imgCard.classList.add('reveal-wrong-match');
        }
        imgCard.querySelector('.img-label').textContent = isCorrect ? '✅ ' + animal.name : '❌ ' + MT_ANIMALS[matchedAnimalIdx].name;
    });

    const mtNote = document.getElementById('mtrnote');
    const mtPts  = mtCorrectCount * 2;
    mtNote.className = 'q-review-note ' + (mtCorrectCount === MT_TOTAL ? 'correct' : 'wrong');
    mtNote.innerHTML = (mtCorrectCount === MT_TOTAL
        ? '<i class="fas fa-check-circle"></i> Perfect matching!'
        : '<i class="fas fa-times-circle"></i> ')
        + ' Matching: <strong>' + mtCorrectCount + ' / ' + MT_TOTAL + ' correct = ' + mtPts + ' pts</strong>';

    // Done button
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
    const colors = ['#51cf66','#fcc419','#ff6b6b','#4d96ff','#845ec2','#00c9a7','#fd79a8'];
    for (let i = 0; i < 65; i++) {
        const p = document.createElement('div');
        p.className = 'cp';
        p.style.cssText = `
            left:${Math.random()*100}vw; top:-12px;
            background:${colors[Math.floor(Math.random()*colors.length)]};
            border-radius:${Math.random()>.5?'50%':'2px'};
            width:${6+Math.random()*8}px; height:${6+Math.random()*8}px;
            animation-duration:${1.3+Math.random()*1.8}s;
            animation-delay:${Math.random()*.6}s;
        `;
        document.body.appendChild(p);
        p.addEventListener('animationend', () => p.remove());
    }
}

// ═══════════════════════════════════════════
// INIT
// ═══════════════════════════════════════════
buildMC();
buildMT();
updateProgress();
</script>

</body>
</html>