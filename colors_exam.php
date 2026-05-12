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
$lesson_name = "colors";
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
<title>Colors Final Exam — E-KINDER</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Fredoka+One&family=Nunito:wght@400;600;700;800;900&display=swap" rel="stylesheet">
<style>
:root {
    --green-dark: #1a2e1a;
    --cream: #fdf8f0;
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
        radial-gradient(circle at 15% 20%, rgba(78,205,196,.10) 0%, transparent 40%),
        radial-gradient(circle at 85% 70%, rgba(255,107,107,.10) 0%, transparent 40%),
        radial-gradient(circle at 50% 90%, rgba(162,155,254,.08) 0%, transparent 40%);
}
body::before {
    content: '';
    position: fixed; inset: 0; pointer-events: none; z-index: 0;
    background-image: radial-gradient(circle, rgba(0,0,0,.03) 1.2px, transparent 1.2px);
    background-size: 26px 26px;
}

/* ── NAV ── */
.lesson-nav {
    height: 64px; padding: 0 28px;
    background: rgba(255,255,255,.95); backdrop-filter: blur(16px);
    border-bottom: 1px solid rgba(0,0,0,.07);
    box-shadow: 0 2px 18px rgba(0,0,0,.06);
    position: sticky; top: 0; z-index: 200;
    display: flex; align-items: center; gap: 14px;
}
.lnav-back {
    display: inline-flex; align-items: center; gap: 8px;
    background: var(--green-dark); color: #fff;
    font-family: 'Fredoka One', cursive; font-size: .88rem;
    padding: 8px 20px; border-radius: var(--pill);
    text-decoration: none; box-shadow: 0 4px 14px rgba(0,0,0,.22);
    transition: transform .22s cubic-bezier(.34,1.56,.64,1); flex-shrink: 0;
}
.lnav-back:hover { transform: scale(1.06); color: #fff; }
.lnav-title { font-family: 'Fredoka One', cursive; font-size: 1.1rem; color: var(--green-dark); flex: 1; text-align: center; }

/* ── LAYOUT ── */
.page-wrap { position: relative; z-index: 1; max-width: 820px; margin: 0 auto; padding: 32px 20px 80px; }

/* ── HEADER ── */
.exam-header { text-align: center; margin-bottom: 28px; }
.exam-badge { display: inline-flex; align-items: center; gap: 8px; background: #fff; border: 2px solid #e0d4f7; border-radius: var(--pill); padding: 6px 18px; font-family: 'Fredoka One', cursive; font-size: .78rem; color: #5a2e8a; box-shadow: 0 2px 10px rgba(0,0,0,.06); margin-bottom: 14px; }
.exam-title { font-family: 'Fredoka One', cursive; font-size: clamp(1.8rem,4vw,2.4rem); color: var(--green-dark); margin-bottom: 6px; }
.exam-sub { font-size: .9rem; color: #a0a8a0; font-weight: 700; }

/* ── PROGRESS BAR ── */
.progress-card { background: #fff; border-radius: 20px; padding: 16px 24px; box-shadow: 0 4px 18px rgba(0,0,0,.06); border: 1.5px solid #eee; margin-bottom: 24px; display: flex; align-items: center; gap: 20px; flex-wrap: wrap; }
.prog-label { font-family: 'Fredoka One', cursive; font-size: .8rem; color: #bbb; text-transform: uppercase; letter-spacing: .8px; white-space: nowrap; }
.prog-bar-outer { flex: 1; height: 14px; background: #f0f0f0; border-radius: 99px; overflow: hidden; min-width: 120px; }
.prog-bar-inner { height: 100%; background: linear-gradient(90deg, #E53935, #FB8C00, #FDD835, #43A047, #1E88E5, #8E24AA); background-size: 200% 100%; border-radius: 99px; transition: width .5s cubic-bezier(.34,1.56,.64,1); width: 0%; }
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
.q-num-badge.mc-badge { background: linear-gradient(135deg, #E53935, #FB8C00); }
.q-num-badge.mt-badge { background: #4D96FF; }
.q-status-icon { margin-left: auto; font-size: 1.1rem; display: none; }
.q-card.answered-correct .q-status-icon { display: block; color: #40c057; }
.q-card.answered-wrong   .q-status-icon { display: block; color: #ff4444; }
.q-text { font-family: 'Fredoka One', cursive; font-size: 1.1rem; color: #1a1a2e; margin-bottom: 16px; line-height: 1.35; }

/* ── COLOR SWATCH INLINE (for questions that show a color) ── */
.q-color-swatch {
    display: inline-block;
    width: 28px; height: 28px;
    border-radius: 8px;
    vertical-align: middle;
    margin-left: 8px;
    border: 2px solid rgba(0,0,0,.1);
    box-shadow: 0 2px 6px rgba(0,0,0,.15);
}

/* ── CHOICES GRID ── */
.choices-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }
@media(max-width: 480px) { .choices-grid { grid-template-columns: 1fr; } }

.choice-btn { width: 100%; padding: 12px 16px; border-radius: 14px; border: 2.5px solid #eee; background: #fafafa; font-family: 'Nunito', sans-serif; font-weight: 800; font-size: .92rem; color: #444; cursor: pointer; text-align: left; transition: all .2s cubic-bezier(.34,1.56,.64,1); display: flex; align-items: center; gap: 10px; }
.choice-btn:hover:not(:disabled):not(.selected) { border-color: var(--green-dark); background: #f0f5f0; transform: translateY(-2px) scale(1.01); box-shadow: 0 5px 14px rgba(0,0,0,.08); }
.choice-btn.selected { border-color: var(--blue); background: #e8f1ff; color: #1a4fa0; box-shadow: 0 4px 14px rgba(77,150,255,.2); }
.choice-btn.reveal-correct { border-color: #40c057 !important; background: #d3f9d8 !important; color: #1e6030 !important; }
.choice-btn.reveal-wrong   { border-color: #ff4444 !important; background: #ffe3e3 !important; color: #a00 !important; }

.choice-letter { width: 24px; height: 24px; border-radius: 8px; background: rgba(0,0,0,.06); display: flex; align-items: center; justify-content: center; font-family: 'Fredoka One', cursive; font-size: .75rem; flex-shrink: 0; transition: background .2s, color .2s; }
.choice-btn.selected .choice-letter       { background: var(--blue); color: #fff; }
.choice-btn.reveal-correct .choice-letter { background: #40c057; color: #fff; }
.choice-btn.reveal-wrong .choice-letter   { background: #ff4444; color: #fff; }

/* ── COLOR DOT inside choice ── */
.choice-dot { width: 22px; height: 22px; border-radius: 50%; flex-shrink: 0; border: 2px solid rgba(0,0,0,.1); box-shadow: 0 1px 4px rgba(0,0,0,.15); }

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

/* ── COLOR SWATCH CARD (right column) ── */
.swatch-card { background: #fff; border: 3px solid #f0f0f0; border-radius: 16px; padding: 10px; cursor: pointer; box-shadow: 0 4px 14px rgba(0,0,0,.06); transition: all .25s cubic-bezier(.34,1.56,.64,1); display: flex; flex-direction: column; align-items: center; gap: 8px; }
.swatch-card:hover:not(.matched):not(.matched-wrong):not(.selected-swatch) { transform: translateY(-3px) scale(1.02); box-shadow: 0 10px 26px rgba(0,0,0,.1); }
.swatch-card.selected-swatch { border-color: var(--blue) !important; background: #e8f1ff; transform: scale(1.03); }
.swatch-card.matched          { border-color: #40c057; background: #d3f9d8; cursor: default; }
.swatch-card.matched-wrong    { border-color: #ff4444; background: #ffe3e3; cursor: default; }
.swatch-card.reveal-correct-match { border-color: #40c057 !important; background: #d3f9d8 !important; }
.swatch-card.reveal-wrong-match   { border-color: #ff4444 !important; background: #ffe3e3 !important; }

.swatch-block { width: 100%; height: 60px; border-radius: 10px; border: 2px solid rgba(0,0,0,.08); }
.swatch-label { font-size: .7rem; font-weight: 800; color: #aaa; font-family: 'Fredoka One', cursive; }

.match-hint { font-size: .8rem; color: #bbb; text-align: center; font-weight: 700; margin-top: -6px; margin-bottom: 14px; }

/* ── REVIEW NOTE ── */
.q-review-note { margin-top: 12px; padding: 10px 14px; border-radius: 12px; font-size: .82rem; font-weight: 800; display: none; }
.q-review-note.correct { background: #d3f9d8; color: #1e6030; display: flex; align-items: center; gap: 8px; }
.q-review-note.wrong   { background: #ffe3e3; color: #a00000; display: flex; align-items: center; gap: 8px; }

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
.score-chip.mc    { background: #fff0f0; border-color: #E53935; color: #a00; }
.score-chip.mt    { background: #e8f1ff; border-color: var(--blue); color: #1a4fa0; }
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
</style>
</head>

<body>

<!-- NAV -->
<nav class="lesson-nav">
    <a href="activities.php" class="lnav-back"><i class="fas fa-arrow-left"></i> Back</a>
    <div class="lnav-title">🎨 Colors Final Exam</div>
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
        <div class="exam-badge"><i class="fas fa-palette"></i> Final Exam</div>
        <h1 class="exam-title">🎨 Colors Final Exam</h1>
        <p class="exam-sub" id="examSubtitle">Answer all 30 items, then click Submit! (Total: 40 points)</p>
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
            <div class="sec-sub">Choose the correct answer. 1 point each.</div>
        </div>
        <div class="sec-chip">20 items · 20 pts</div>
    </div>
    <div class="exam-scroll-area" id="mcArea"></div>

    <!-- PART 2: MATCHING TYPE -->
    <div class="section-divider" style="background:#1a2e4a;">
        <div class="sec-icon">🎨</div>
        <div class="sec-info">
            <div class="sec-title">Part 2 — Matching Type</div>
            <div class="sec-sub">Click a color name, then click its matching color swatch. 2 points each.</div>
        </div>
        <div class="sec-chip">10 pairs · 20 pts</div>
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
            <div class="score-chip mt"><i class="fas fa-link"></i> Match: <span id="mtScore">0</span>/20</div>
        </div>
        <div class="score-chip total" style="margin:0 auto 8px;display:inline-flex;">
            <i class="fas fa-star"></i> Total: <span id="totalScore">0</span> / 40 pts
        </div>
        <div style="font-family:'Fredoka One',cursive;font-size:1.6rem;color:var(--green-dark);margin-bottom:14px;" id="examPct">0%</div>
        <div class="result-verdict" id="resultVerdict">PASSED 🎉</div>
        <div class="result-actions">
            <button class="btn-review" onclick="openReview()"><i class="fas fa-search"></i> Review Answers</button>
            <button class="btn-go-back" onclick="window.location.href='activities.php'"><i class="fas fa-home"></i> Go Back</button>
        </div>
    </div>
</div>

<script>
// ═══════════════════════════════════════════
// COLORS DATA
// ═══════════════════════════════════════════
const COLORS_DATA = [
    { name:"Red",    hex:"#E53935", tl:"Pula",         example:"apple, fire truck"   },
    { name:"Blue",   hex:"#1E88E5", tl:"Asul",         example:"sky, ocean"          },
    { name:"Yellow", hex:"#FDD835", tl:"Dilaw",        example:"sun, banana"         },
    { name:"Green",  hex:"#43A047", tl:"Berde",        example:"grass, leaves"       },
    { name:"Orange", hex:"#FB8C00", tl:"Kahel",        example:"orange fruit, carrot"},
    { name:"Purple", hex:"#8E24AA", tl:"Lila",         example:"grapes, lavender"    },
    { name:"Pink",   hex:"#E91E8C", tl:"Rosas",        example:"flamingo, pig"       },
    { name:"Brown",  hex:"#6D4C41", tl:"Kayumanggi",   example:"chocolate, wood"     },
    { name:"Black",  hex:"#212121", tl:"Itim",         example:"night sky, crow"     },
    { name:"White",  hex:"#ECEFF1", tl:"Puti",         example:"clouds, snow"        },
];

// ── MULTIPLE CHOICE (20 questions, 1 point each) ──
const MC_QUESTIONS = [
    { q:"What color is the sky?",                          c:["Blue","Red","Green","Yellow"],       a:"Blue",   showSwatch:null  },
    { q:"What color is a banana?",                         c:["Yellow","Blue","Pink","White"],      a:"Yellow", showSwatch:null  },
    { q:"What color is grass?",                            c:["Green","Blue","Orange","Purple"],    a:"Green",  showSwatch:null  },
    { q:"What color is an apple?",                         c:["Red","Blue","Black","Green"],        a:"Red",    showSwatch:null  },
    { q:"What color is the sun?",                          c:["Yellow","Orange","White","Blue"],    a:"Yellow", showSwatch:null  },
    { q:"What do you get when you mix Red + Blue?",        c:["Purple","Green","Orange","Black"],   a:"Purple", showSwatch:null  },
    { q:"What do you get when you mix Red + Yellow?",      c:["Orange","Purple","Green","Pink"],    a:"Orange", showSwatch:null  },
    { q:"What do you get when you mix Blue + Yellow?",     c:["Green","Orange","Purple","Brown"],   a:"Green",  showSwatch:null  },
    { q:"What color is chocolate?",                        c:["Brown","Black","Red","Orange"],      a:"Brown",  showSwatch:null  },
    { q:"What color are clouds?",                          c:["White","Blue","Gray","Yellow"],      a:"White",  showSwatch:null  },
    { q:"What color is the night sky?",                    c:["Black","Blue","Purple","Navy"],      a:"Black",  showSwatch:null  },
    { q:"What color are grapes?",                          c:["Purple","Red","Blue","Black"],       a:"Purple", showSwatch:null  },
    { q:"What color is a carrot?",                         c:["Orange","Yellow","Red","Brown"],     a:"Orange", showSwatch:null  },
    { q:"What color do you get when you mix Red + White?", c:["Pink","Purple","Orange","Yellow"],   a:"Pink",   showSwatch:null  },
    { q:"Which color is a PRIMARY color?",                 c:["Red","Green","Orange","Purple"],     a:"Red",    showSwatch:null  },
    { q:"What color is a flamingo?",                       c:["Pink","Red","White","Purple"],       a:"Pink",   showSwatch:null  },
    { q:"What color is a lemon?",                          c:["Yellow","Green","Orange","White"],   a:"Yellow", showSwatch:null  },
    { q:"What color is the ocean?",                        c:["Blue","Green","Black","Purple"],     a:"Blue",   showSwatch:null  },
    { q:"What color is a ripe tomato?",                    c:["Red","Orange","Yellow","Brown"],     a:"Red",    showSwatch:null  },
    { q:"What color do you get when you mix Black + White?", c:["Gray","Blue","Brown","Pink"],      a:"Gray",   showSwatch:null  },
];

// ── MATCHING TYPE — all 10 colors (2 points each) ──
const MT_COLORS = [...COLORS_DATA]; // all 10

const MC_TOTAL   = 20;
const MT_TOTAL   = 10;
const MT_PTS_EACH = 2;                          // 2 points per correct match
const TOTAL      = MC_TOTAL + MT_TOTAL;         // 30 items to answer
const MAX_SCORE  = MC_TOTAL + (MT_TOTAL * MT_PTS_EACH); // 20 + 20 = 40 points
const PASS_SCORE = Math.round(MAX_SCORE * 0.6); // 60% = 24 points
const LETTERS    = ['A','B','C','D'];

let mcAnswers       = new Array(MC_TOTAL).fill(null);
let mtAnswers       = new Array(MT_TOTAL).fill(null);
let examSubmitted   = false;
let finalMcScore    = 0;
let finalMtScore    = 0;   // will hold count of correct matches (×2 for points)
let mtSwatchOrder   = [];
let selectedNameIdx = null;

// ── HELPER ──
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

        // Build choices — add color dot if choice is a color name
        const choicesHTML = item.c.map((choice, ci) => {
            const colorData = COLORS_DATA.find(c => c.name === choice);
            const dotHTML = colorData
                ? `<span class="choice-dot" style="background:${colorData.hex}"></span>`
                : '';
            return `
                <button class="choice-btn" id="mcchoice-${i}-${ci}" onclick="selectMC(${i}, '${choice}', this)">
                    <span class="choice-letter">${LETTERS[ci]}</span>
                    ${dotHTML}
                    ${choice}
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
    mtSwatchOrder = shuffle(MT_COLORS.map((_, i) => i));

    const card = document.createElement('div');
    card.className = 'q-card';
    card.id = 'mtcard-main';
    card.style.animationDelay = '0.6s';

    const namesHTML = MT_COLORS.map((color, i) => `
        <div class="name-card" id="namecard-${i}" onclick="selectName(${i})">
            <span style="display:inline-block;width:16px;height:16px;border-radius:50%;background:${color.hex};border:2px solid rgba(0,0,0,.12);flex-shrink:0;"></span>
            ${color.name}
            <span style="font-size:.7rem;color:#bbb;font-weight:700;">(${color.tl})</span>
        </div>
    `).join('');

    const swatchesHTML = mtSwatchOrder.map((colorIdx, pos) => {
        const color = MT_COLORS[colorIdx];
        // White needs a border to be visible
        const blockStyle = color.name === 'White'
            ? `background:${color.hex};border:2px solid #ddd;`
            : `background:${color.hex};`;
        return `
            <div class="swatch-card" id="swatchcard-${pos}" data-color-idx="${colorIdx}" onclick="selectSwatch(${pos})">
                <div class="swatch-block" style="${blockStyle}"></div>
                <div class="swatch-label">?</div>
            </div>
        `;
    }).join('');

    card.innerHTML = `
        <div class="q-num-row">
            <div class="q-num-badge mt-badge">🎨</div>
            <div style="font-family:'Fredoka One',cursive;font-size:.78rem;color:#1a4fa0;text-transform:uppercase;letter-spacing:.8px;">Match the Color Name to its Swatch</div>
        </div>
        <p class="match-hint">👈 Click a color name on the left, then click its matching color on the right!</p>
        <div class="match-container">
            <div>
                <div class="match-col-label">🏷️ Color Names</div>
                <div class="match-col" id="namesCol">${namesHTML}</div>
            </div>
            <div>
                <div class="match-col-label">🎨 Color Swatches</div>
                <div class="match-col" id="swatchesCol">${swatchesHTML}</div>
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

function selectSwatch(swatchPos) {
    if (examSubmitted) return;
    if (selectedNameIdx === null) return;
    const swatchCard = document.getElementById('swatchcard-' + swatchPos);
    const nameCard   = document.getElementById('namecard-' + selectedNameIdx);
    if (swatchCard.classList.contains('matched') || swatchCard.classList.contains('matched-wrong')) return;

    // Unlink old pair if already matched
    if (mtAnswers[selectedNameIdx] !== null) {
        const oldCard = document.getElementById('swatchcard-' + mtAnswers[selectedNameIdx]);
        if (oldCard) { oldCard.classList.remove('matched', 'matched-wrong'); oldCard.querySelector('.swatch-label').textContent = '?'; }
    }

    mtAnswers[selectedNameIdx] = swatchPos;
    nameCard.classList.remove('selected-name');
    nameCard.classList.add('matched');
    swatchCard.classList.add('matched');
    swatchCard.querySelector('.swatch-label').textContent = MT_COLORS[selectedNameIdx].name;
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
        if (mcNull >= 0) {
            const fc = document.getElementById('mccard-' + mcNull);
            fc.classList.add('unanswered-highlight');
            fc.scrollIntoView({ behavior: 'smooth', block: 'center' });
            setTimeout(() => fc.classList.remove('unanswered-highlight'), 600);
        } else {
            const mt = document.getElementById('mtcard-main');
            mt.classList.add('unanswered-highlight');
            mt.scrollIntoView({ behavior: 'smooth', block: 'center' });
            setTimeout(() => mt.classList.remove('unanswered-highlight'), 600);
        }
        return;
    }

    document.getElementById('unansweredWarn').style.display = 'none';
    examSubmitted = true;

    // Score MC — 1 point each
    finalMcScore = 0;
    MC_QUESTIONS.forEach((item, i) => { if (mcAnswers[i] === item.a) finalMcScore++; });

    // Score MT — 2 points each correct match
    let finalMtCorrect = 0;
    MT_COLORS.forEach((color, nameIdx) => {
        const swatchPos = mtAnswers[nameIdx];
        const matchedColorIdx = parseInt(document.getElementById('swatchcard-' + swatchPos).dataset.colorIdx);
        if (matchedColorIdx === nameIdx) finalMtCorrect++;
    });
    finalMtScore = finalMtCorrect * MT_PTS_EACH; // 2 pts each

    document.querySelectorAll('.choice-btn').forEach(b => b.disabled = true);
    document.querySelectorAll('.name-card, .swatch-card').forEach(b => b.style.pointerEvents = 'none');

    showResult();

    fetch("save_exam.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: "score=" + Math.round(((finalMcScore + finalMtScore) / MAX_SCORE) * 100) + "&lesson_id=<?php echo $lesson_id; ?>"
    });
}

// ═══════════════════════════════════════════
// RESULT
// ═══════════════════════════════════════════
function showResult() {
    const totalPoints = finalMcScore + finalMtScore;
    const pct = Math.round((totalPoints / MAX_SCORE) * 100);
    const passed = totalPoints >= PASS_SCORE;
    document.getElementById('resultEmoji').textContent = passed ? '🎉' : '😢';
    document.getElementById('resultTitle').textContent = passed ? 'Colorful Work!' : 'Keep Trying!';
    document.getElementById('resultSub').textContent   = passed ? 'You passed the Colors Final Exam!' : 'Study your colors and try again!';
    document.getElementById('mcScore').textContent    = finalMcScore;
    document.getElementById('mtScore').textContent    = finalMtScore;
    document.getElementById('totalScore').textContent = totalPoints;
    document.getElementById('examPct').textContent    = pct + '%';
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
    const totalPoints = finalMcScore + finalMtScore;
    document.getElementById('reviewScoreChip').textContent = 'Score: ' + totalPoints + ' / 40 pts (' + Math.round((totalPoints/MAX_SCORE)*100) + '%)';
    document.getElementById('examSubtitle').textContent = 'Review your answers below!';
    document.getElementById('submitArea').style.display = 'none';

    // Reveal MC
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
        if (isCorrect) {
            note.className = 'q-review-note correct';
            note.innerHTML = '<i class="fas fa-check-circle"></i> Correct! The answer is <strong>' + item.a + '</strong>.';
        } else {
            const correctColor = COLORS_DATA.find(c => c.name === item.a);
            const swatchHTML = correctColor
                ? `<span style="display:inline-block;width:14px;height:14px;border-radius:50%;background:${correctColor.hex};border:1px solid rgba(0,0,0,.2);vertical-align:middle;margin:0 3px;"></span>`
                : '';
            note.className = 'q-review-note wrong';
            note.innerHTML = `<i class="fas fa-times-circle"></i> Wrong! You answered <strong>${mcAnswers[i]}</strong>. Correct answer: ${swatchHTML}<strong>${item.a}</strong>`;
        }
    });

    // Reveal MT
    let mtCorrect = 0;
    MT_COLORS.forEach((color, nameIdx) => {
        const swatchPos     = mtAnswers[nameIdx];
        const matchedIdx    = parseInt(document.getElementById('swatchcard-' + swatchPos).dataset.colorIdx);
        const isCorrect     = matchedIdx === nameIdx;
        if (isCorrect) mtCorrect++;

        const nameCard   = document.getElementById('namecard-' + nameIdx);
        const swatchCard = document.getElementById('swatchcard-' + swatchPos);
        nameCard.classList.remove('matched', 'matched-wrong', 'selected-name');
        swatchCard.classList.remove('matched', 'matched-wrong');
        nameCard.classList.add(isCorrect   ? 'reveal-correct-match' : 'reveal-wrong-match');
        swatchCard.classList.add(isCorrect ? 'reveal-correct-match' : 'reveal-wrong-match');
        swatchCard.querySelector('.swatch-label').textContent = isCorrect
            ? '✅ ' + color.name
            : '❌ ' + MT_COLORS[matchedIdx].name;
    });

    const mtNote = document.getElementById('mtrnote');
    mtNote.className = 'q-review-note ' + (mtCorrect === MT_TOTAL ? 'correct' : 'wrong');
    mtNote.innerHTML = (mtCorrect === MT_TOTAL
        ? '<i class="fas fa-check-circle"></i> Perfect matching!'
        : '<i class="fas fa-times-circle"></i>')
        + ' Matching Score: <strong>' + mtCorrect + ' / ' + MT_TOTAL + ' correct (' + (mtCorrect * MT_PTS_EACH) + ' pts)</strong>';

    const goBack = document.createElement('div');
    goBack.style.cssText = 'text-align:center;padding:20px 0';
    goBack.innerHTML = `<button class="btn-submit" onclick="window.location.href='activities.php'"><i class="fas fa-home"></i> Done — Go Back</button>`;
    document.querySelector('.page-wrap').appendChild(goBack);

    window.scrollTo({ top: 0, behavior: 'smooth' });
}

// ═══════════════════════════════════════════
// CONFETTI — rainbow colors!
// ═══════════════════════════════════════════
function launchConfetti() {
    const colors = ['#E53935','#FB8C00','#FDD835','#43A047','#1E88E5','#8E24AA','#E91E8C','#6D4C41'];
    for (let i = 0; i < 70; i++) {
        const p = document.createElement('div');
        p.className = 'cp';
        p.style.cssText = `left:${Math.random()*100}vw;top:-12px;background:${colors[Math.floor(Math.random()*colors.length)]};border-radius:${Math.random()>.5?'50%':'2px'};width:${6+Math.random()*8}px;height:${6+Math.random()*8}px;animation-duration:${1.3+Math.random()*1.8}s;animation-delay:${Math.random()*.6}s;`;
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