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
$lesson_name = "body";
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
<title>Body Parts Final Exam — E-KINDER</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Fredoka+One&family=Nunito:wght@400;600;700;800;900&display=swap" rel="stylesheet">
<style>
:root {
    --green-dark: #1a2e1a;
    --cream: #fdf8f0;
    --pill: 999px;
    --accent: #40c057;
    --danger: #ff4444;
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
        radial-gradient(circle at 10% 15%, rgba(255,107,107,0.08) 0%, transparent 40%),
        radial-gradient(circle at 88% 70%, rgba(77,150,255,0.08) 0%, transparent 40%),
        radial-gradient(circle at 50% 95%, rgba(107,203,119,0.07) 0%, transparent 40%);
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
.exam-badge { display: inline-flex; align-items: center; gap: 8px; background: #fff; border: 2px solid #dde8dd; border-radius: var(--pill); padding: 6px 18px; font-family: 'Fredoka One', cursive; font-size: .78rem; color: #2a4a2a; box-shadow: 0 2px 10px rgba(0,0,0,.06); margin-bottom: 14px; }
.exam-title { font-family: 'Fredoka One', cursive; font-size: clamp(1.8rem,4vw,2.4rem); color: var(--green-dark); margin-bottom: 6px; }
.exam-sub { font-size: .9rem; color: #a0a8a0; font-weight: 700; }

/* ── PROGRESS BAR ── */
.progress-card { background: #fff; border-radius: 20px; padding: 16px 24px; box-shadow: 0 4px 18px rgba(0,0,0,.06); border: 1.5px solid #eee; margin-bottom: 24px; display: flex; align-items: center; gap: 20px; flex-wrap: wrap; }
.prog-label { font-family: 'Fredoka One', cursive; font-size: .8rem; color: #bbb; text-transform: uppercase; letter-spacing: .8px; white-space: nowrap; }
.prog-bar-outer { flex: 1; height: 14px; background: #f0f0f0; border-radius: 99px; overflow: hidden; min-width: 120px; }
.prog-bar-inner { height: 100%; background: linear-gradient(90deg, #40c057, #74d680); border-radius: 99px; transition: width .5s cubic-bezier(.34,1.56,.64,1); width: 0%; }
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
.q-num-badge { width: 32px; height: 32px; border-radius: 50%; background: var(--green-dark); color: #fff; font-family: 'Fredoka One', cursive; font-size: .85rem; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.q-num-badge.mc-badge { background: #40c057; }
.q-num-badge.mt-badge { background: #4D96FF; }
.q-status-icon { margin-left: auto; font-size: 1.1rem; display: none; }
.q-card.answered-correct .q-status-icon { display: block; color: #40c057; }
.q-card.answered-wrong   .q-status-icon { display: block; color: #ff4444; }
.q-text { font-family: 'Fredoka One', cursive; font-size: 1.1rem; color: #1a1a2e; margin-bottom: 16px; line-height: 1.35; }

/* ── CHOICES GRID ── */
.choices-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }
@media(max-width: 480px) { .choices-grid { grid-template-columns: 1fr; } }

.choice-btn { width: 100%; padding: 12px 16px; border-radius: 14px; border: 2.5px solid #eee; background: #fafafa; font-family: 'Nunito', sans-serif; font-weight: 800; font-size: .92rem; color: #444; cursor: pointer; text-align: left; transition: all .2s cubic-bezier(.34,1.56,.64,1); display: flex; align-items: center; gap: 10px; }
.choice-btn:hover:not(:disabled):not(.selected) { border-color: var(--green-dark); background: #f0f5f0; transform: translateY(-2px) scale(1.01); box-shadow: 0 5px 14px rgba(0,0,0,.08); }
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

.img-card { background: #fff; border: 3px solid #f0f0f0; border-radius: 16px; padding: 10px; cursor: pointer; box-shadow: 0 4px 14px rgba(0,0,0,.06); transition: all .25s cubic-bezier(.34,1.56,.64,1); display: flex; flex-direction: column; align-items: center; gap: 6px; }
.match-col {
    display: flex;
    flex-direction: column;
    gap: 10px;
    max-height: 500px;
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
.img-card:hover:not(.matched):not(.matched-wrong):not(.selected-img) { transform: translateY(-3px) scale(1.02); box-shadow: 0 10px 26px rgba(0,0,0,.1); }
.img-card.selected-img      { border-color: var(--blue) !important; background: #e8f1ff; transform: scale(1.03); }
.img-card.matched            { border-color: #40c057; background: #d3f9d8; cursor: default; }
.img-card.matched-wrong      { border-color: #ff4444; background: #ffe3e3; cursor: default; }
.img-card.reveal-correct-match { border-color: #40c057 !important; background: #d3f9d8 !important; }
.img-card.reveal-wrong-match   { border-color: #ff4444 !important; background: #ffe3e3 !important; }
.img-card img { width: 100%; height: 300px; object-fit: cover; border-radius: 10px; pointer-events: none; }
.img-card .img-label { font-size: .7rem; font-weight: 800; color: #aaa; }

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
@keyframes resultPop { from { opacity:0; transform: scale(.7) translateY(40px); } to { opacity:1; transform:none; } }
.result-emoji { font-size: 4.5rem; margin-bottom: 10px; }
.result-title { font-family: 'Fredoka One', cursive; font-size: 2rem; color: var(--green-dark); margin-bottom: 4px; }
.result-sub   { font-size: .9rem; color: #aaa; font-weight: 700; margin-bottom: 18px; }

.score-breakdown { display: flex; gap: 12px; justify-content: center; margin-bottom: 18px; flex-wrap: wrap; }
.score-chip { font-family: 'Fredoka One', cursive; font-size: .9rem; padding: 8px 18px; border-radius: var(--pill); border: 2.5px solid; display: flex; align-items: center; gap: 6px; }
.score-chip.mc    { background: #f0faf2; border-color: #40c057; color: #1e6030; }
.score-chip.mt    { background: #e8f1ff; border-color: var(--blue); color: #1a4fa0; }
.score-chip.total { background: #fff9db; border-color: var(--warn); color: #a07000; font-size: 1.05rem; }

.result-verdict { font-family: 'Fredoka One', cursive; font-size: 1.05rem; padding: 6px 20px; border-radius: var(--pill); display: inline-block; margin-bottom: 22px; }
.result-verdict.pass { background: #40c057; color: #fff; }
.result-verdict.fail { background: #ff4444; color: #fff; }
.result-actions { display: flex; gap: 12px; justify-content: center; flex-wrap: wrap; }
.btn-review   { font-family: 'Fredoka One', cursive; font-size: .95rem; padding: 12px 28px; border-radius: var(--pill); border: 2.5px solid var(--green-dark); background: transparent; color: var(--green-dark); cursor: pointer; transition: all .2s cubic-bezier(.34,1.56,.64,1); }
.btn-review:hover { background: var(--green-dark); color: #fff; transform: scale(1.04); }
.btn-go-back  { font-family: 'Fredoka One', cursive; font-size: .95rem; padding: 12px 28px; border-radius: var(--pill); background: var(--green-dark); color: #fff; border: none; cursor: pointer; box-shadow: 0 5px 16px rgba(26,46,26,.25); transition: all .2s cubic-bezier(.34,1.56,.64,1); }
.btn-go-back:hover { transform: scale(1.04); }

/* ── REVIEW BANNER ── */
.review-banner { display: none; background: var(--green-dark); color: #fff; padding: 14px 24px; border-radius: 18px; margin-bottom: 20px; font-family: 'Fredoka One', cursive; font-size: .95rem; align-items: center; gap: 12px; box-shadow: 0 6px 20px rgba(26,46,26,.22); }
.review-banner.visible { display: flex; }
.review-score-chip { margin-left: auto; background: rgba(255,255,255,.15); border-radius: var(--pill); padding: 4px 14px; font-size: .85rem; white-space: nowrap; }

/* ── CONFETTI ── */
.cp { position: fixed; border-radius: 2px; pointer-events: none; z-index: 9999; animation: fall linear forwards; }
@keyframes fall { 0%{transform:translateY(0) rotate(0deg);opacity:1} 100%{transform:translateY(100vh) rotate(720deg);opacity:0} }
</style>
</head>

<body>

<!-- NAV -->
<nav class="lesson-nav">
    <a href="activities.php" class="lnav-back"><i class="fas fa-arrow-left"></i> Back</a>
    <div class="lnav-title">🧍 Body Parts Final Exam</div>
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
        <div class="exam-badge"><i class="fas fa-clipboard-list"></i> Final Exam</div>
        <h1 class="exam-title">🧍 Body Parts Final Exam</h1>
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
            <div class="sec-sub">Click a body part name, then click its matching picture.</div>
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
// BODY PARTS DATA
// ═══════════════════════════════════════════
const BODY_PARTS = [
    { name:"Head",  img:"pictures/humanbody/head.jpg"      },
    { name:"Eyes",  img:"pictures/humanbody/eyes.jpg"      },
    { name:"Ears",  img:"pictures/humanbody/ears.jpg"      },
    { name:"Nose",  img:"pictures/humanbody/nose.jpg"      },
    { name:"Mouth",  img:"pictures/humanbody/mouth.jpg"     },
    { name:"Neck",  img:"pictures/humanbody/neck.jpg"      },
    { name:"Shoulders",  img:"pictures/humanbody/shoulders.jpg" },
    { name:"Arms",  img:"pictures/humanbody/arms.jpg"      },
    { name:"Hands",  img:"pictures/humanbody/hands.jpg"     },
    { name:"Stomach",  img:"pictures/humanbody/stomachs.png"  },
    { name:"Legs",  img:"pictures/humanbody/legs.png"      },
    { name:"Knees",  img:"pictures/humanbody/knees.jpg"     },
    { name:"Feet",  img:"pictures/humanbody/feet.jpg"      },
    { name:"Fingers",  img:"pictures/humanbody/fingers.jpg"   },
    { name:"Elbows",  img:"pictures/humanbody/elbows.png"    },
    { name:"Tongue",  img:"pictures/humanbody/tongue.jpg"    },
    { name:"Eyebrows",  img:"pictures/humanbody/eyebrows.jpg"  },
];

// ── MULTIPLE CHOICE (15 questions) ──
const MC_QUESTIONS = [
    { q:"What do you use to see?",                              c:["Eyes","Hands","Feet","Ears"],            a:"Eyes"      },
    { q:"What do you use to hear?",                             c:["Nose","Ears","Mouth","Hands"],           a:"Ears"      },
    { q:"What do you use to smell?",                            c:["Eyes","Nose","Mouth","Feet"],            a:"Nose"      },
    { q:"What do you use to walk and run?",                     c:["Hands","Feet","Head","Eyes"],            a:"Feet"      },
    { q:"What do you use to eat and talk?",                     c:["Ears","Nose","Mouth","Eyes"],            a:"Mouth"     },
    { q:"What part is at the very top of your body?",           c:["Feet","Head","Stomach","Knees"],         a:"Head"      },
    { q:"Which body part connects your head to your body?",     c:["Shoulder","Elbow","Neck","Knee"],        a:"Neck"      },
    { q:"What do you use to hold and grab things?",             c:["Feet","Knees","Hands","Neck"],           a:"Hands"     },
    { q:"Which part bends in the middle of your arm?",          c:["Wrist","Elbows","Shoulder","Finger"],    a:"Elbows"    },
    { q:"Which part bends in the middle of your leg?",          c:["Ankle","Knees","Hip","Elbow"],           a:"Knees"     },
    { q:"What is the front middle part of your body called?",   c:["Back","Stomach","Neck","Head"],          a:"Stomach"   },
    { q:"What do you use to taste your food?",                  c:["Nose","Eyes","Tongue","Ears"],           a:"Tongue"    },
    { q:"Which body part protects your eyes from sweat?",       c:["Lashes","Eyebrows","Forehead","Nose"],   a:"Eyebrows"  },
    { q:"What are the small parts at the ends of your hands?",  c:["Toes","Palms","Fingers","Nails"],        a:"Fingers"   },
    { q:"What part sits on top of your arms, left and right?",  c:["Elbows","Neck","Shoulders","Head"],      a:"Shoulders" },
    { q:"Which body part do you use to kick a ball?",           c:["Hands","Feet","Elbows","Neck"],          a:"Feet"      },
    { q:"What part of your body helps you balance when standing?", c:["Arms","Legs","Head","Stomach"],       a:"Legs"      },
    { q:"Which body part do you use to wave hello?",            c:["Feet","Knees","Hands","Nose"],           a:"Hands"     },
    { q:"What do you call the parts at the ends of your feet?", c:["Fingers","Heels","Toes","Nails"],        a:"Toes"      },
    { q:"Which part is found in the middle of your face and you breathe through it?", c:["Mouth","Ears","Nose","Eyes"], a:"Nose" },
];

// ── MATCHING TYPE (10 body parts) ──
const MT_PARTS = [
    BODY_PARTS.find(p => p.name === "Head"),
    BODY_PARTS.find(p => p.name === "Eyes"),
    BODY_PARTS.find(p => p.name === "Ears"),
    BODY_PARTS.find(p => p.name === "Nose"),
    BODY_PARTS.find(p => p.name === "Mouth"),
    BODY_PARTS.find(p => p.name === "Hands"),
    BODY_PARTS.find(p => p.name === "Feet"),
    BODY_PARTS.find(p => p.name === "Knees"),
    BODY_PARTS.find(p => p.name === "Stomach"),
    BODY_PARTS.find(p => p.name === "Tongue"),
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

// ── HELPERS ──
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
                <span class="choice-letter">${LETTERS[ci]}</span>${choice}
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
function buildMT() {
    const area = document.getElementById('mtArea');
    area.innerHTML = '';
    mtImgOrder = shuffle(MT_PARTS.map((_, i) => i));

    const card = document.createElement('div');
    card.className = 'q-card';
    card.id = 'mtcard-main';
    card.style.animationDelay = '0.6s';

    const namesHTML = MT_PARTS.map((part, i) => `
        <div class="name-card" id="namecard-${i}" onclick="selectName(${i})">
            ${part.emoji} ${part.name}
        </div>
    `).join('');

    const imgsHTML = mtImgOrder.map((partIdx, pos) => {
        const part = MT_PARTS[partIdx];
        return `
            <div class="img-card" id="imgcard-${pos}" data-part-idx="${partIdx}" onclick="selectImg(${pos})">
                <img src="${part.img}" alt="${part.name}" onerror="this.parentElement.querySelector('.img-label').textContent='${part.emoji}';this.style.display='none'">
                <div class="img-label">?</div>
            </div>
        `;
    }).join('');

    card.innerHTML = `
        <div class="q-num-row">
            <div class="q-num-badge mt-badge">🔗</div>
            <div style="font-family:'Fredoka One',cursive;font-size:.78rem;color:#1a4fa0;text-transform:uppercase;letter-spacing:.8px;">Match the Name to the Picture</div>
        </div>
        <p class="match-hint">👈 Click a body part name on the left, then click its matching picture on the right!</p>
        <div class="match-container">
            <div>
                <div class="match-col-label">🏷️ Body Part Names</div>
                <div class="match-col" id="namesCol">${namesHTML}</div>
            </div>
            <div>
                <div class="match-col-label">🖼️ Body Part Pictures</div>
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

    // Unlink previous match for this name
    if (mtAnswers[selectedNameIdx] !== null) {
        const oldImgCard = document.getElementById('imgcard-' + mtAnswers[selectedNameIdx]);
        if (oldImgCard) { oldImgCard.classList.remove('matched', 'matched-wrong'); oldImgCard.querySelector('.img-label').textContent = '?'; }
    }

    mtAnswers[selectedNameIdx] = imgPos;
    nameCard.classList.remove('selected-name');
    nameCard.classList.add('matched');
    imgCard.classList.add('matched');
    imgCard.querySelector('.img-label').textContent = MT_PARTS[selectedNameIdx].name;
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

    // Score MC (1 pt each)
    finalMcScore = 0;
    MC_QUESTIONS.forEach((item, i) => { if (mcAnswers[i] === item.a) finalMcScore++; });

    // Score MT (2 pts each)
    finalMtScore = 0;
    MT_PARTS.forEach((part, nameIdx) => {
        const imgPos = mtAnswers[nameIdx];
        const matchedIdx = parseInt(document.getElementById('imgcard-' + imgPos).dataset.partIdx);
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
    const pct    = Math.round((total / MAX_SCORE) * 100);
    const passed = total >= PASS_SCORE;
    document.getElementById('resultEmoji').textContent = passed ? '🎉' : '😢';
    document.getElementById('resultTitle').textContent = passed ? 'Excellent Work!' : 'Keep Trying!';
    document.getElementById('resultSub').textContent   = passed ? 'You passed the Body Parts Exam!' : 'Study more and try again!';
    document.getElementById('mcScore').textContent     = finalMcScore;
    document.getElementById('mtScore').textContent     = Math.round(finalMtScore / 2); // show correct count
    document.getElementById('totalScore').textContent  = total + ' / ' + MAX_SCORE + ' (' + pct + '%)';
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
            note.className = 'q-review-note wrong';
            note.innerHTML = '<i class="fas fa-times-circle"></i> Wrong! You answered <strong>' + mcAnswers[i] + '</strong>. Correct answer: <strong>' + item.a + '</strong>';
        }
    });

    // Reveal MT
    let mtCorrect = 0;
    MT_PARTS.forEach((part, nameIdx) => {
        const imgPos     = mtAnswers[nameIdx];
        const matchedIdx = parseInt(document.getElementById('imgcard-' + imgPos).dataset.partIdx);
        const isCorrect  = matchedIdx === nameIdx;
        if (isCorrect) mtCorrect++;
        const nameCard = document.getElementById('namecard-' + nameIdx);
        const imgCard  = document.getElementById('imgcard-' + imgPos);
        nameCard.classList.remove('matched', 'matched-wrong', 'selected-name');
        imgCard.classList.remove('matched', 'matched-wrong');
        nameCard.classList.add(isCorrect ? 'reveal-correct-match' : 'reveal-wrong-match');
        imgCard.classList.add(isCorrect  ? 'reveal-correct-match' : 'reveal-wrong-match');
        imgCard.querySelector('.img-label').textContent = isCorrect
            ? '✅ ' + part.name
            : '❌ ' + MT_PARTS[matchedIdx].name;
    });

    const mtNote = document.getElementById('mtrnote');
    const mtPts  = mtCorrect * 2;
    mtNote.className = 'q-review-note ' + (mtCorrect === MT_TOTAL ? 'correct' : 'wrong');
    mtNote.innerHTML = (mtCorrect === MT_TOTAL
        ? '<i class="fas fa-check-circle"></i> Perfect matching!'
        : '<i class="fas fa-times-circle"></i>')
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
    const colors = ['#40c057','#fcc419','#ff6b6b','#4d96ff','#845ec2','#00c9a7'];
    for (let i = 0; i < 65; i++) {
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