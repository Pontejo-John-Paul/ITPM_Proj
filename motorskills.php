<?php
session_start();
require_once __DIR__ . '/database.php';

if (!isset($_SESSION['student_id']) || $_SESSION['role'] !== 'student') {
    header("Location: index.php");
    exit;
}

$first_name = $_SESSION['student_fname'] ?? 'Student';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Motor Skills - E-KINDER</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Fredoka+One&family=Nunito:wght@400;600;700;800;900&display=swap" rel="stylesheet">
<?php include 'navbar-styles.php'; ?>
<style>
:root {
    --teal:        #0097a7;
    --teal-dark:   #006978;
    --teal-light:  #e0f7fa;
    --teal-mid:    #00acc1;
    --cream:       #f0fdff;
    --accent-pink: #f06292;
    --accent-yel:  #ffc107;
    --accent-grn:  #66bb6a;
    --accent-pur:  #ab47bc;
    --green-dark:  #0d5407;
}
* { box-sizing: border-box; margin: 0; padding: 0; }
body { font-family: 'Nunito', sans-serif; background: var(--cream); overflow-x: hidden; }

.lesson-nav {
            padding: 0 28px; height: 64px; background: #fff;
            box-shadow: 0 4px 20px rgba(0,0,0,0.07);
            position: sticky; top: 0; z-index: 100;
            display: flex; align-items: center; justify-content: space-between;
        }
        .lnav-back {
            display: inline-flex; align-items: center; gap: 7px;
            background: var(--green-dark); color: #fff;
            font-family: 'Fredoka One', cursive; font-size: 0.9rem;
            padding: 8px 18px; border-radius: 50px; text-decoration: none;
            box-shadow: 0 4px 12px rgba(13,84,7,0.25);
            transition: transform 0.2s cubic-bezier(.34,1.56,.64,1);
        }
        .lnav-back:hover { transform: scale(1.06); color: #fff; }
        .lang-toggle {
            display: inline-flex; align-items: center;
            background: #f0f0f0; border-radius: 50px; padding: 4px;
            border: 2px solid #e0e0e0;
        }
        .lang-btn {
            font-family: 'Fredoka One', cursive; font-size: 0.82rem;
            padding: 6px 16px; border-radius: 50px; border: none; cursor: pointer;
            background: transparent; color: #aaa;
            transition: background 0.2s, color 0.2s, transform 0.15s;
            display: flex; align-items: center; gap: 5px;
        }
        .lang-btn.active {
            background: var(--green-dark); color: #fff;
            box-shadow: 0 3px 10px rgba(13,84,7,0.25); transform: scale(1.04);
        }

/* ── HERO ── */
.hero {
    background: linear-gradient(145deg, #006978 0%, #00acc1 55%, #26c6da 100%);
    padding: 72px 20px 100px; text-align: center; position: relative; overflow: hidden;
}
.hero-dots { position: absolute; inset: 0; pointer-events: none;
    background-image: radial-gradient(circle, rgba(255,255,255,0.13) 1px, transparent 1px);
    background-size: 30px 30px; animation: dotsDrift 18s linear infinite; }
@keyframes dotsDrift { from{background-position:0 0} to{background-position:30px 30px} }
.hero-badge { display: inline-flex; align-items: center; gap: 8px;
    background: rgba(255,255,255,0.15); border: 1.5px solid rgba(255,255,255,0.3);
    border-radius: 50px; padding: 7px 22px; font-size: 0.78rem; font-weight: 800;
    color: #fff; letter-spacing: 2px; text-transform: uppercase; margin-bottom: 22px;
    position: relative; z-index: 1; backdrop-filter: blur(4px); }
.hero h1 { font-family: 'Fredoka One', cursive; font-size: clamp(2.4rem, 6vw, 4rem);
    color: #fff; margin-bottom: 14px; position: relative; z-index: 1;
    text-shadow: 0 4px 20px rgba(0,0,0,0.18); }
.hero h1 em { color: #ffc107; font-style: normal; }
.hero p { font-size: 1.05rem; color: rgba(255,255,255,0.82); font-weight: 700;
    max-width: 480px; margin: 0 auto 28px; position: relative; z-index: 1; line-height: 1.6; }
.hero-icons { display: flex; justify-content: center; gap: 20px; flex-wrap: wrap;
    position: relative; z-index: 1; margin-bottom: 10px; }
.hero-icon-item { background: rgba(255,255,255,0.15); border-radius: 20px;
    padding: 14px 22px; font-size: 2rem; border: 1.5px solid rgba(255,255,255,0.25);
    animation: floatBounce 3s ease-in-out infinite;
    display: flex; align-items: center; justify-content: center; color: #fff; }
.hero-icon-item:nth-child(2){animation-delay:.5s}
.hero-icon-item:nth-child(3){animation-delay:1s}
.hero-icon-item:nth-child(4){animation-delay:1.5s}
.hero-icon-item:nth-child(5){animation-delay:2s}
@keyframes floatBounce { 0%,100%{transform:translateY(0)} 50%{transform:translateY(-10px)} }
.hero-wave { position: absolute; bottom: -1px; left: 0; right: 0; line-height: 0; }
.hero-wave svg { display: block; width: 100%; }

/* ── PAGE HEADER (lang badge, under hero) ── */
.page-header { text-align: center; padding: 28px 0 4px; }
.lang-badge {
    display: inline-flex; align-items: center; gap: 6px;
    margin-top: 0; padding: 5px 16px; border-radius: 50px;
    font-family: 'Fredoka One', cursive; font-size: 0.82rem;
    background: #fff; border: 2px solid #b2ebf2; color: var(--teal-dark);
    box-shadow: 0 2px 8px rgba(0,105,120,0.08); transition: all 0.3s;
}
.lang-badge.tl-mode { background: #fff3e0; border-color: #ffcc80; color: #e65100; }

/* ── SECTION LABEL ── */
.section-label { display:flex; align-items:center; gap:12px; margin-bottom:36px; }
.section-label-line { flex:1; height:2px; background:linear-gradient(90deg,#b2ebf2,transparent); }
.section-label-line.right { background:linear-gradient(90deg,transparent,#b2ebf2); }
.section-label-text { font-family:'Fredoka One',cursive; font-size:1rem; color:#80deea; letter-spacing:2px; white-space:nowrap; }

/* ── INFO INTRO ── */
.info-section { padding: 72px 0 20px; }

/* ── IMPORTANCE BANNER ── */
.importance-banner { background: linear-gradient(135deg, #006978 0%, #00acc1 100%);
    border-radius: 28px; padding: 44px 40px; color: #fff; margin-bottom: 40px;
    position: relative; overflow: hidden; }
.importance-banner::before { content:''; position:absolute; font-size:200px; opacity:0.05;
    bottom:-30px; right:-10px; line-height:1; pointer-events:none; }
.importance-title { font-family:'Fredoka One',cursive; font-size:clamp(1.5rem,3vw,2rem); margin-bottom:20px; color:#fff; }
.importance-grid { display: grid; grid-template-columns: repeat(2,1fr); gap: 16px; }
@media(max-width:600px){ .importance-grid { grid-template-columns: 1fr; } }
.importance-item { display:flex; align-items:flex-start; gap:14px;
    background:rgba(255,255,255,0.1); border-radius:18px; padding:18px;
    border:1.5px solid rgba(255,255,255,0.18); }
.importance-num { width:38px; height:38px; border-radius:50%; background:rgba(255,255,255,0.2);
    display:flex; align-items:center; justify-content:center;
    font-family:'Fredoka One',cursive; font-size:1.1rem; color:#fff; flex-shrink:0; }
.importance-text-title { font-family:'Fredoka One',cursive; font-size:1rem; color:#fff; margin-bottom:3px; }
.importance-text-desc { font-size:0.8rem; color:rgba(255,255,255,0.75); font-weight:700; line-height:1.5; }

/* ── PURPOSE BANNER ── */
.purpose-banner { background: linear-gradient(135deg, #7b1fa2 0%, #ab47bc 100%);
    border-radius: 28px; padding: 44px 40px; color: #fff; margin-bottom: 56px;
    position: relative; overflow: hidden; }
.purpose-title { font-family:'Fredoka One',cursive; font-size:clamp(1.5rem,3vw,2rem); margin-bottom:20px; color:#fff; }
.purpose-grid { display: grid; grid-template-columns: repeat(2,1fr); gap: 16px; }
@media(max-width:600px){ .purpose-grid { grid-template-columns: 1fr; } }
.purpose-item { display:flex; align-items:flex-start; gap:14px;
    background:rgba(255,255,255,0.1); border-radius:18px; padding:18px;
    border:1.5px solid rgba(255,255,255,0.18); }
.purpose-icon-box { font-size:1.8rem; flex-shrink:0; color:#fff; }
.purpose-text-title { font-family:'Fredoka One',cursive; font-size:1rem; color:#fff; margin-bottom:3px; }
.purpose-text-desc { font-size:0.8rem; color:rgba(255,255,255,0.82); font-weight:700; line-height:1.5; }

/* ── ACTIVITY SECTIONS ── */
.activities-wrapper { padding: 0 0 80px; }
.activity-block { padding: 64px 0 0; }
.activity-header {
    display: flex; align-items: center; gap: 18px;
    background: linear-gradient(135deg, #006978 0%, #00acc1 100%);
    border-radius: 24px; padding: 28px 32px; margin-bottom: 28px;
    position: relative; overflow: hidden;
}
.activity-header::after { content:''; position:absolute; top:-30px; right:-30px; width:140px; height:140px;
    border-radius:50%; background:rgba(255,255,255,0.07); }
.activity-header-icon { font-size: 3rem; flex-shrink:0; color:#fff; animation: floatBounce 3s ease-in-out infinite; }
.activity-header-label { font-size:0.73rem; font-weight:800; color:rgba(255,255,255,0.6);
    letter-spacing:2px; text-transform:uppercase; margin-bottom:4px; }
.activity-header-title { font-family:'Fredoka One',cursive; font-size:clamp(1.4rem,3vw,1.9rem); color:#fff; margin-bottom:6px; }
.activity-header-desc { font-size:0.88rem; color:rgba(255,255,255,0.82); font-weight:700; line-height:1.5; }

/* Benefits bar */
.benefits-bar { background: #fff; border-radius: 20px; padding: 22px 26px;
    border: 2.5px solid #e0f7fa; box-shadow: 0 4px 18px rgba(0,151,167,0.07); margin-bottom: 28px; }
.benefits-bar-title { font-family:'Fredoka One',cursive; font-size:1.05rem; color:#006978;
    margin-bottom:12px; display:flex; align-items:center; gap:8px; }
.benefits-list { display: flex; flex-wrap:wrap; gap:10px; }
.benefit-chip { display:inline-flex; align-items:center; gap:7px;
    background: var(--teal-light); border:1.5px solid #b2ebf2;
    border-radius:50px; padding:7px 15px; font-size:0.8rem; font-weight:800; color:#006978; }
.benefit-chip i { font-size:0.85rem; }

/* Step rows */
.steps-heading { font-family:'Fredoka One',cursive; font-size:1.15rem; color:#006978;
    margin-bottom:16px; display:flex; align-items:center; gap:10px; }
.step-row {
    display: grid; grid-template-columns: 1fr 1fr; gap: 22px;
    background: #fff; border-radius: 22px; padding: 26px 26px;
    border: 2.5px solid #e0f7fa; box-shadow: 0 4px 18px rgba(0,151,167,0.06);
    margin-bottom: 16px; align-items: center;
}
.step-row.flip .step-image-box { order: -1; }
@media(max-width:680px){ .step-row { grid-template-columns: 1fr; }
    .step-row.flip .step-image-box { order: 0; } }
.step-number-badge {
    display:inline-flex; align-items:center; justify-content:center;
    width:34px; height:34px; border-radius:50%;
    background: linear-gradient(135deg, #00acc1, #006978);
    font-family:'Fredoka One',cursive; font-size:0.95rem; color:#fff; margin-bottom:10px;
}
.step-title { font-family:'Fredoka One',cursive; font-size:1.1rem; color:#006978; margin-bottom:6px; }
.step-desc { font-size:0.86rem; color:#666; font-weight:700; line-height:1.65; }
.step-image-box {
    background: linear-gradient(135deg, #e0f7fa, #f0fdff);
    border-radius: 16px;
    border: 3px solid #b2ebf2;
    box-shadow: 0 4px 16px rgba(0,151,167,0.15), inset 0 1px 0 rgba(255,255,255,0.8);
    width: 100%;
    aspect-ratio: 1 / 1;      /* ← SQUARE box */
    display: flex;
    align-items: center;
    justify-content: center;
    flex-direction: column;
    gap: 8px;
    padding: 12px;
    text-align: center;
    overflow: hidden;
    position: relative;
}
.step-image-box img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: 10px;
    display: block;
    box-shadow: 0 2px 10px rgba(0,0,0,0.12);
}
.step-image-placeholder {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 8px;
    width: 100%;
    height: 100%;
}
.placeholder-icon { font-size: 3rem; color: #80deea; display:block; margin-bottom:6px; }
.placeholder-label { font-size:0.73rem; color:#80deea; font-weight:800; letter-spacing:1px; text-transform:uppercase; }

/* ── MINI QUIZ ── */
.mini-quiz {
    background: linear-gradient(135deg, #fffde7, #fff);
    border-radius: 28px; padding: 34px 30px;
    border: 2.5px solid #ffe082; box-shadow: 0 8px 30px rgba(255,193,7,0.12);
    margin-top: 28px; margin-bottom: 0;
    position: relative; overflow: hidden;
}
.mini-quiz-header { display:flex; align-items:center; gap:12px; margin-bottom:20px; flex-wrap:wrap; }
.mini-quiz-badge { background: var(--accent-yel); color: #5d4037;
    border-radius:50px; padding:5px 16px; font-family:'Fredoka One',cursive;
    font-size:0.82rem; letter-spacing:1px; flex-shrink:0;
    display: flex; align-items: center; gap: 6px; }
.mini-quiz-title { font-family:'Fredoka One',cursive; font-size:1.2rem; color:#5d4037; }
.quiz-progress { display:flex; gap:8px; align-items:center; margin-bottom:18px; }
.quiz-dot { width:10px; height:10px; border-radius:50%; background:#e0f7fa;
    border:2px solid #b2ebf2; transition:all .3s; }
.quiz-dot.qactive { background: var(--accent-yel); border-color:#f9a825; transform:scale(1.3); }
.quiz-dot.qdone-ok { background: var(--accent-grn); border-color:#43a047; }
.quiz-dot.qdone-no { background:#ef9a9a; border-color:#e53935; }
.quiz-question-block { background:#fff; border-radius:18px; padding:20px 18px;
    border:1.5px solid #ffe082; margin-bottom:14px; }
.quiz-q-num { font-size:0.73rem; font-weight:800; color:#bbb; letter-spacing:1px; margin-bottom:5px; }
.quiz-q-text { font-family:'Fredoka One',cursive; font-size:1.02rem; color:#006978; margin-bottom:13px; line-height:1.4; }
.quiz-choices { display:flex; flex-direction:column; gap:8px; }
.quiz-choice-btn { padding:10px 16px; border-radius:13px;
    border:2.5px solid #e0f7fa; background:#f0fdff;
    font-family:'Fredoka One',cursive; font-size:0.92rem; color:#006978;
    cursor:pointer; transition:all 0.2s cubic-bezier(.34,1.56,.64,1);
    text-align:left; display:flex; align-items:center; gap:9px; }
.quiz-choice-btn:hover:not(:disabled) { border-color:var(--teal); background:var(--teal-light); transform:translateX(4px); }
.quiz-choice-btn.correct { background:#e8f5e9; border-color:#66bb6a; color:#2e7d32; }
.quiz-choice-btn.wrong { background:#ffebee; border-color:#ef9a9a; color:#c62828; animation:shakeIt .35s ease; }
.quiz-choice-btn:disabled { cursor:default; }
@keyframes shakeIt { 0%,100%{transform:translateX(0)} 25%{transform:translateX(-6px)} 75%{transform:translateX(6px)} }
.quiz-feedback { border-radius:13px; padding:12px 15px; font-family:'Fredoka One',cursive;
    font-size:0.92rem; margin-top:10px; display:none; }
.quiz-feedback.show { display:block; animation:fadeSlide .3s ease; }
.quiz-feedback.good { background:#e8f5e9; color:#2e7d32; border:2px solid #a5d6a7; }
.quiz-feedback.bad  { background:#ffebee; color:#c62828; border:2px solid #ef9a9a; }
@keyframes fadeSlide { from{opacity:0;transform:translateY(10px)} to{opacity:1;transform:translateY(0)} }
.quiz-next-btn { background:var(--teal); color:#fff; border:none;
    border-radius:50px; padding:10px 26px;
    font-family:'Fredoka One',cursive; font-size:0.92rem;
    cursor:pointer; box-shadow:0 5px 16px rgba(0,151,167,0.25);
    transition:transform 0.2s cubic-bezier(.34,1.56,.64,1);
    margin-top:6px; display:none; }
.quiz-next-btn.show { display:inline-block; }
.quiz-next-btn:hover { transform:scale(1.05); }
.quiz-result { text-align:center; padding:16px; background:#fff;
    border-radius:16px; border:2px solid #ffe082; margin-top:14px;
    font-family:'Fredoka One',cursive; font-size:1.1rem; color:#006978; display:none; }
.quiz-result.show { display:block; animation:popIn .4s cubic-bezier(.34,1.56,.64,1); }
.quiz-result-score { font-size:2.2rem; color:var(--teal); display:block; margin:4px 0; }
@keyframes popIn { from{opacity:0;transform:scale(0.85)} to{opacity:1;transform:scale(1)} }

/* ── DIVIDER ── */
.rainbow-divider { height:4px; margin: 48px 0 0;
    background: linear-gradient(90deg, #26c6da, #66bb6a, #ffc107, #f06292, #26c6da);
    background-size:300% 100%; animation:rainbowSlide 4s linear infinite; border-radius:4px; }
@keyframes rainbowSlide { 0%{background-position:0%} 100%{background-position:300%} }

/* ── FINAL BIG QUIZ ── */
.game-section { padding: 64px 0 80px; background: #fff; position: relative; }
.game-section::before { content:''; position:absolute; top:0; left:0; right:0; height:4px;
    background:linear-gradient(90deg,#26c6da,#66bb6a,#ffc107,#f06292,#26c6da);
    background-size:300% 100%; animation:rainbowSlide 4s linear infinite; }
.game-title { font-family:'Fredoka One',cursive; font-size:clamp(1.6rem,4vw,2.4rem); color:#006978; margin-bottom:8px; }
.game-sub { font-size:1rem; color:#888; font-weight:700; margin-bottom:40px; }
.step-tracker { display:flex; align-items:center; justify-content:center; gap:0; margin-bottom:40px; flex-wrap:wrap; }
.step-dot { width:44px; height:44px; border-radius:50%; background:#e0f7fa; border:3px solid #b2ebf2;
    display:flex; align-items:center; justify-content:center;
    font-family:'Fredoka One',cursive; font-size:1rem; color:#80deea;
    transition:all 0.3s; z-index:1; }
.step-dot.active { background:var(--teal); border-color:var(--teal-dark); color:#fff; transform:scale(1.15); box-shadow:0 6px 18px rgba(0,151,167,0.35); }
.step-dot.done { background:#66bb6a; border-color:#43a047; color:#fff; }
.step-connector { width:40px; height:3px; background:#e0f7fa; transition:background .3s; }
.step-connector.done { background:#66bb6a; }
.activity-card { background:#fff; border-radius:28px; padding:40px 32px;
    border:2.5px solid #e0f7fa; box-shadow:0 8px 32px rgba(0,151,167,0.1);
    max-width:640px; margin:0 auto 32px; animation:fadeSlide 0.4s ease; }
.activity-q-icon { font-size:4rem; display:block; margin-bottom:16px; color:var(--teal); animation:floatBounce 2.5s ease-in-out infinite; }
.activity-question { font-family:'Fredoka One',cursive; font-size:1.4rem; color:#006978; margin-bottom:8px; line-height:1.3; }
.activity-subtitle { font-size:0.9rem; color:#aaa; font-weight:700; margin-bottom:28px; }
.choices { display:flex; flex-wrap:wrap; gap:12px; justify-content:center; margin-bottom:20px; }
.choice-btn { padding:12px 22px; border-radius:50px; border:3px solid #e0f7fa; background:#f0fdff;
    font-family:'Fredoka One',cursive; font-size:1rem; color:#006978; cursor:pointer;
    transition:all 0.2s cubic-bezier(.34,1.56,.64,1); display:flex; align-items:center; gap:8px; }
.choice-btn:hover { border-color:var(--teal); background:var(--teal-light); transform:scale(1.04); }
.choice-btn.correct { background:#e8f5e9; border-color:#66bb6a; color:#2e7d32; transform:scale(1.06); }
.choice-btn.wrong { background:#ffebee; border-color:#ef9a9a; color:#c62828; animation:shakeIt .35s ease; }
.choice-btn:disabled { cursor:default; }
.feedback-msg { border-radius:18px; padding:16px 20px; font-family:'Fredoka One',cursive;
    font-size:1.1rem; margin-bottom:20px; display:none; }
.feedback-msg.show { display:block; animation:fadeSlide .3s ease; }
.feedback-msg.good { background:#e8f5e9; color:#2e7d32; border:2px solid #a5d6a7; }
.feedback-msg.bad { background:#ffebee; color:#c62828; border:2px solid #ef9a9a; }
.next-btn { background:var(--teal); color:#fff; border:none; border-radius:50px; padding:14px 36px;
    font-family:'Fredoka One',cursive; font-size:1.05rem; cursor:pointer;
    box-shadow:0 6px 20px rgba(0,151,167,0.3);
    transition:transform 0.2s cubic-bezier(.34,1.56,.64,1),box-shadow 0.2s; display:none; }
.next-btn:hover { transform:scale(1.05); box-shadow:0 10px 28px rgba(0,151,167,0.4); }
.next-btn.show { display:inline-block; }
.score-badge { background:var(--teal-light); border:2.5px solid var(--teal);
    border-radius:50px; padding:8px 22px; font-family:'Fredoka One',cursive;
    font-size:0.95rem; color:var(--teal-dark); display:inline-flex; align-items:center; gap:8px; margin-bottom:24px; }
.completion-card { background:linear-gradient(135deg,#e0f7fa,#fff);
    border-radius:28px; padding:48px 32px; text-align:center;
    border:2.5px solid #b2ebf2; box-shadow:0 12px 40px rgba(0,151,167,0.12);
    max-width:520px; margin:0 auto; animation:popIn .5s cubic-bezier(.34,1.56,.64,1); }
.completion-trophy-icon { font-size:4rem; color:var(--teal); display:block; margin-bottom:16px; animation:floatBounce 2s ease-in-out infinite; }
.completion-title { font-family:'Fredoka One',cursive; font-size:2rem; color:#006978; margin-bottom:8px; }
.completion-sub { font-size:1rem; color:#888; font-weight:700; margin-bottom:24px; }
.completion-score { font-family:'Fredoka One',cursive; font-size:2.8rem; color:var(--teal); margin-bottom:24px; }
.retry-btn { background:#fff; color:var(--teal-dark); border:3px solid var(--teal);
    border-radius:50px; padding:12px 32px; font-family:'Fredoka One',cursive;
    font-size:1rem; cursor:pointer; margin:0 8px; transition:all .2s cubic-bezier(.34,1.56,.64,1); }
.retry-btn:hover { background:var(--teal-light); transform:scale(1.05); }
.lessons-btn { background:var(--teal); color:#fff; border:none; border-radius:50px;
    padding:12px 32px; font-family:'Fredoka One',cursive; font-size:1rem; cursor:pointer;
    margin:0 8px; box-shadow:0 6px 20px rgba(0,151,167,.3);
    transition:all .2s cubic-bezier(.34,1.56,.64,1); text-decoration:none; display:inline-block; }
.lessons-btn:hover { transform:scale(1.05); color:#fff; }

/* ── TIPS ── */
.tips-section { padding: 64px 0 80px; }
.tips-grid { display:grid; grid-template-columns:repeat(2,1fr); gap:20px; }
@media(max-width:640px){ .tips-grid { grid-template-columns:1fr; } }
.tip-card { background:#fff; border-radius:22px; padding:28px 24px;
    border-left:5px solid var(--teal); box-shadow:0 5px 20px rgba(0,151,167,0.08);
    display:flex; gap:16px; align-items:flex-start;
    transition:transform .25s cubic-bezier(.34,1.56,.64,1); }
.tip-card:hover { transform:translateX(6px); }
.tip-icon-box { font-size:1.8rem; color:var(--teal); flex-shrink:0; }
.tip-title { font-family:'Fredoka One',cursive; font-size:1rem; color:#006978; margin-bottom:5px; }
.tip-text { font-size:0.83rem; color:#777; font-weight:700; line-height:1.6; }

footer { background:#006978; color:rgba(255,255,255,0.7); text-align:center; padding:32px 20px; font-size:0.85rem; font-weight:700; }
footer .footer-brand { font-family:'Fredoka One',cursive; font-size:1.6rem; color:#fff; display:block; margin-bottom:8px; }
footer a { color:rgba(255,255,255,0.55); text-decoration:none; margin:0 8px; }
footer a:hover { color:#fff; }
</style>
</head>
<body>

<!-- ═══ NAV (colors.php style) ═══ -->
<nav class="lesson-nav">
    <a href="lessons.php" class="lnav-back"><i class="fas fa-arrow-left"></i> <span id="navBack">Back</span></a>
    <div style="flex:1"></div>
    <div class="lang-toggle">
        <button class="lang-btn active" id="btnEN" onclick="setLang('en')"><i class="fas fa-flag-usa"></i> EN</button>
        <button class="lang-btn"        id="btnTL" onclick="setLang('tl')"><i class="fas fa-flag"></i> TL</button>
    </div>
</nav>

<!-- ── HERO ── -->
<section class="hero">
    <div class="hero-dots"></div>
    <div style="position:relative;z-index:1">
        <div class="hero-badge"><i class="fas fa-hands"></i> <span id="heroBadge">Motor Skills Lesson</span></div>
        <h1><i class="fas fa-hand-sparkles" style="color:#ffc107;font-size:0.85em;vertical-align:middle;margin-right:8px;"></i> Motor <em id="heroTitleEm">Skills</em>!</h1>
        <p id="heroDesc">Matuto kung paano mag-aalaga ng ating katawan — paghuhugas ng kamay, pagsisipilyo, paliligo, at marami pa!</p>
        <div class="hero-icons">
            <div class="hero-icon-item"><i class="fas fa-shower"></i></div>
            <div class="hero-icon-item"><i class="fas fa-tooth"></i></div>
            <div class="hero-icon-item"><i class="fas fa-soap"></i></div>
            <div class="hero-icon-item"><i class="fas fa-hands-wash"></i></div>
            <div class="hero-icon-item"><i class="fas fa-cut"></i></div>
        </div>
    </div>
    <div class="hero-wave">
        <svg viewBox="0 0 1440 70" fill="none" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none">
            <path d="M0,35 C240,70 480,0 720,35 C960,70 1200,0 1440,35 L1440,70 L0,70 Z" fill="#f0fdff"/>
        </svg>
    </div>
</section>

<!-- Page lang badge -->
<div class="page-header">
    <div class="lang-badge" id="langBadge"><i class="fas fa-flag-usa"></i> English</div>
</div>

<!-- ── INTRO INFO ── -->
<section class="info-section">
    <div class="container">
        <div class="section-label">
            <div class="section-label-line"></div>
            <span class="section-label-text" id="sectionWhatLabel"><i class="fas fa-question-circle"></i> WHAT ARE MOTOR SKILLS?</span>
            <div class="section-label-line right"></div>
        </div>
        <div class="text-center mb-5">
            <h2 id="introTitle" style="font-family:'Fredoka One',cursive;font-size:clamp(1.6rem,4vw,2.4rem);color:#006978;">What are Motor Skills? <i class="fas fa-brain" style="color:#00acc1;"></i></h2>
            <p id="introDesc" style="font-size:1rem;color:#888;font-weight:700;margin-top:10px;max-width:580px;margin-left:auto;margin-right:auto;line-height:1.7;">
                <strong style="color:#006978;">Motor skills</strong> are the ability of our body to move and do things using our hands, feet, and other body parts. In this lesson, we will learn how to take care of our own body — an important part of growing up as a healthy child!
            </p>
        </div>

        <!-- Importance Banner -->
        <div class="importance-banner">
            <h3 class="importance-title" id="importanceTitle"><i class="fas fa-star" style="color:#ffc107;"></i> Why are Motor Skills Important?</h3>
            <div class="importance-grid">
                <div class="importance-item">
                    <div class="importance-num">1</div>
                    <div>
                        <div class="importance-text-title" id="imp1title">Health</div>
                        <div class="importance-text-desc" id="imp1desc">A clean body protects us from germs, bacteria, and infections. When we are clean, we are healthier!</div>
                    </div>
                </div>
                <div class="importance-item">
                    <div class="importance-num">2</div>
                    <div>
                        <div class="importance-text-title" id="imp2title">Confidence</div>
                        <div class="importance-text-desc" id="imp2desc">Being clean and neat gives us self-confidence in school, with friends, and everywhere we go.</div>
                    </div>
                </div>
                <div class="importance-item">
                    <div class="importance-num">3</div>
                    <div>
                        <div class="importance-text-title" id="imp3title">Independence</div>
                        <div class="importance-text-desc" id="imp3desc">You learn to take care of yourself without help from others — a proud moment for you and your family!</div>
                    </div>
                </div>
                <div class="importance-item">
                    <div class="importance-num">4</div>
                    <div>
                        <div class="importance-text-title" id="imp4title">Good Habits</div>
                        <div class="importance-text-desc" id="imp4desc">The habits you learn now will stay with you as you grow up. This is the foundation of a healthy life!</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Purpose Banner -->
        <div class="purpose-banner">
            <h3 class="purpose-title" id="purposeTitle"><i class="fas fa-bullseye" style="color:#fff;"></i> Goals of This Lesson</h3>
            <div class="purpose-grid">
                <div class="purpose-item">
                    <div class="purpose-icon-box"><i class="fas fa-eye"></i></div>
                    <div>
                        <div class="purpose-text-title" id="pur1title">Learn the Right Way</div>
                        <div class="purpose-text-desc" id="pur1desc">We will learn the proper method for each self-care habit — step by step so it's easy to follow.</div>
                    </div>
                </div>
                <div class="purpose-item">
                    <div class="purpose-icon-box"><i class="fas fa-rotate"></i></div>
                    <div>
                        <div class="purpose-text-title" id="pur2title">Make It a Habit</div>
                        <div class="purpose-text-desc" id="pur2desc">The goal is to make proper hygiene a regular routine — not just once, but every day!</div>
                    </div>
                </div>
                <div class="purpose-item">
                    <div class="purpose-icon-box"><i class="fas fa-shield-halved"></i></div>
                    <div>
                        <div class="purpose-text-title" id="pur3title">Prevent Illness</div>
                        <div class="purpose-text-desc" id="pur3desc">Through proper hygiene, we can avoid common illnesses like colds, flu, and tooth infections.</div>
                    </div>
                </div>
                <div class="purpose-item">
                    <div class="purpose-icon-box"><i class="fas fa-seedling"></i></div>
                    <div>
                        <div class="purpose-text-title" id="pur4title">Grow and Develop</div>
                        <div class="purpose-text-desc" id="pur4desc">Every step you learn in this lesson helps you grow as a healthy and happy child!</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ══════════════════════════════════════════════ -->
<!--             ACTIVITY LESSONS                   -->
<!-- ══════════════════════════════════════════════ -->
<div class="activities-wrapper">
    <div class="container">

        <!-- ① HANDWASHING / PAGHUHUGAS NG KAMAY -->
        <div class="activity-block" id="act-wash">
            <div class="section-label">
                <div class="section-label-line"></div>
                <span class="section-label-text"><i class="fas fa-soap"></i> <span class="act-label-1">ACTIVITY 1</span></span>
                <div class="section-label-line right"></div>
            </div>
            <div class="activity-header">
                <div class="activity-header-icon"><i class="fas fa-hands-wash"></i></div>
                <div class="activity-header-info">
                    <div class="activity-header-label" id="act1of6">Activity 1 of 6</div>
                    <div class="activity-header-title" id="act1title">Handwashing</div>
                    <div class="activity-header-desc" id="act1desc">Handwashing is the simplest way to prevent the spread of germs and illness. Learn the right way!</div>
                </div>
            </div>

            <div class="benefits-bar">
                <div class="benefits-bar-title" id="wash-ben-title"><i class="fas fa-circle-check" style="color:#006978;"></i> Benefits of Handwashing</div>
                <div class="benefits-list">
                    <span class="benefit-chip"><i class="fas fa-virus-slash"></i> <span id="wb1">Kills germs</span></span>
                    <span class="benefit-chip"><i class="fas fa-head-side-cough-slash"></i> <span id="wb2">Prevents illness</span></span>
                    <span class="benefit-chip"><i class="fas fa-utensils"></i> <span id="wb3">Safe to eat</span></span>
                    <span class="benefit-chip"><i class="fas fa-people-roof"></i> <span id="wb4">Protects family</span></span>
                    <span class="benefit-chip"><i class="fas fa-school"></i> <span id="wb5">Healthy at school</span></span>
                </div>
            </div>

            <div class="steps-heading" id="wash-steps-heading"><i class="fas fa-list-ol" style="color:#00acc1;"></i> Steps for Handwashing</div>

            <div class="step-row">
                <div class="step-content">
                    <div class="step-number-badge">1</div>
                    <div class="step-title" id="wash-s1-title">Turn on the Tap</div>
                    <div class="step-desc" id="wash-s1-desc">Turn on the faucet and let the water run. Place your hands under the water and wet them thoroughly — including between your fingers!</div>
                </div>
                <div class="step-image-box">
                    <img src="pictures/motorskills/handwash_step1.jpg" alt="Turn on tap" onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
                    <div class="step-image-placeholder" style="display:none;flex-direction:column;align-items:center;gap:8px;">
                        <i class="fas fa-faucet placeholder-icon"></i>
                        <p class="placeholder-label" id="wash-p1">Turn on the Tap</p>
                    </div>
                </div>
            </div>

            <div class="step-row flip">
                <div class="step-content">
                    <div class="step-number-badge">2</div>
                    <div class="step-title" id="wash-s2-title">Apply Soap</div>
                    <div class="step-desc" id="wash-s2-desc">Get soap and apply it to your hands. Make sure there's enough soap on both hands — including your palms and the back of your hands.</div>
                </div>
                <div class="step-image-box">
                    <img src="pictures/motorskills/handwash_step2.jpg" alt="Apply soap" onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
                    <div class="step-image-placeholder" style="display:none;flex-direction:column;align-items:center;gap:8px;">
                        <i class="fas fa-pump-soap placeholder-icon"></i>
                        <p class="placeholder-label" id="wash-p2">Apply Soap</p>
                    </div>
                </div>
            </div>

            <div class="step-row">
                <div class="step-content">
                    <div class="step-number-badge">3</div>
                    <div class="step-title" id="wash-s3-title">Scrub for 20 Seconds</div>
                    <div class="step-desc" id="wash-s3-desc">Scrub your hands vigorously for 20 seconds — as long as singing "Happy Birthday" twice! Clean the palms, backs, between fingers, and under nails.</div>
                </div>
                <div class="step-image-box">
                    <img src="pictures/motorskills/handwash_step3.jpg" alt="Scrub hands" onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
                    <div class="step-image-placeholder" style="display:none;flex-direction:column;align-items:center;gap:8px;">
                        <i class="fas fa-hands placeholder-icon"></i>
                        <p class="placeholder-label" id="wash-p3">Scrub 20 Seconds</p>
                    </div>
                </div>
            </div>

            <div class="step-row flip">
                <div class="step-content">
                    <div class="step-number-badge">4</div>
                    <div class="step-title" id="wash-s4-title">Rinse Thoroughly</div>
                    <div class="step-desc" id="wash-s4-desc">Rinse all the soap off with clean running water. Make sure no soap remains between your fingers or under your nails.</div>
                </div>
                <div class="step-image-box">
                    <img src="pictures/motorskills/handwash_step4.jpg" alt="Rinse hands" onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
                    <div class="step-image-placeholder" style="display:none;flex-direction:column;align-items:center;gap:8px;">
                        <i class="fas fa-droplet placeholder-icon"></i>
                        <p class="placeholder-label" id="wash-p4">Rinse Well</p>
                    </div>
                </div>
            </div>

            <div class="step-row">
                <div class="step-content">
                    <div class="step-number-badge">5</div>
                    <div class="step-title" id="wash-s5-title">Dry Your Hands</div>
                    <div class="step-desc" id="wash-s5-desc">Use a clean cloth or paper towel to dry your hands. Don't leave them wet — wet hands pick up germs more easily!</div>
                </div>
                <div class="step-image-box">
                    <img src="pictures/motorskills/handwash_step5.jpg" alt="Dry hands" onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
                    <div class="step-image-placeholder" style="display:none;flex-direction:column;align-items:center;gap:8px;">
                        <i class="fas fa-wind placeholder-icon"></i>
                        <p class="placeholder-label" id="wash-p5">Dry Your Hands</p>
                    </div>
                </div>
            </div>

        </div>

        <div class="rainbow-divider"></div>

        <!-- ② TOOTHBRUSHING / PAGSISIPILYO NG NGIPIN -->
        <div class="activity-block" id="act-brush">
            <div class="section-label" style="margin-top:48px;">
                <div class="section-label-line"></div>
                <span class="section-label-text"><i class="fas fa-tooth"></i> <span class="act-label-2">ACTIVITY 2</span></span>
                <div class="section-label-line right"></div>
            </div>
            <div class="activity-header" style="background: linear-gradient(135deg, #388e3c 0%, #66bb6a 100%);">
                <div class="activity-header-icon"><i class="fas fa-tooth"></i></div>
                <div class="activity-header-info">
                    <div class="activity-header-label" id="act2of6">Activity 2 of 6</div>
                    <div class="activity-header-title" id="act2title">Tooth Brushing</div>
                    <div class="activity-header-desc" id="act2desc">Healthy teeth are the start of a healthy body! Learn the right way to brush so your smile stays clean.</div>
                </div>
            </div>

            <div class="benefits-bar" style="border-color:#c8e6c9;">
                <div class="benefits-bar-title" id="brush-ben-title" style="color:#2e7d32;"><i class="fas fa-circle-check" style="color:#2e7d32;"></i> Benefits of Tooth Brushing</div>
                <div class="benefits-list">
                    <span class="benefit-chip" style="background:#e8f5e9;border-color:#a5d6a7;color:#2e7d32;"><i class="fas fa-tooth"></i> <span id="bb1">Healthy teeth</span></span>
                    <span class="benefit-chip" style="background:#e8f5e9;border-color:#a5d6a7;color:#2e7d32;"><i class="fas fa-face-smile"></i> <span id="bb2">Beautiful smile</span></span>
                    <span class="benefit-chip" style="background:#e8f5e9;border-color:#a5d6a7;color:#2e7d32;"><i class="fas fa-wind"></i> <span id="bb3">Fresh breath</span></span>
                    <span class="benefit-chip" style="background:#e8f5e9;border-color:#a5d6a7;color:#2e7d32;"><i class="fas fa-ban"></i> <span id="bb4">No cavities</span></span>
                    <span class="benefit-chip" style="background:#e8f5e9;border-color:#a5d6a7;color:#2e7d32;"><i class="fas fa-piggy-bank"></i> <span id="bb5">Save on dentist</span></span>
                </div>
            </div>

            <div class="steps-heading" id="brush-steps-heading" style="color:#2e7d32;"><i class="fas fa-list-ol" style="color:#66bb6a;"></i> Steps for Tooth Brushing</div>

            <div class="step-row" style="border-color:#c8e6c9;">
                <div class="step-content">
                    <div class="step-number-badge" style="background:linear-gradient(135deg,#66bb6a,#2e7d32);">1</div>
                    <div class="step-title" id="brush-s1-title" style="color:#2e7d32;">Get Your Toothbrush & Toothpaste</div>
                    <div class="step-desc" id="brush-s1-desc">Use your own toothbrush — never borrow! Apply a pea-sized amount of toothpaste to the brush. Don't use too much!</div>
                </div>
                <div class="step-image-box" style="background:linear-gradient(135deg,#e8f5e9,#f1f8f2);border-color:#a5d6a7;">
                    <img src="pictures/motorskills/brush_step1.jpg" alt="Toothbrush and toothpaste" onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
                    <div class="step-image-placeholder" style="display:none;flex-direction:column;align-items:center;gap:8px;">
                        <i class="fas fa-toothbrush placeholder-icon" style="color:#81c784;"></i>
                        <p class="placeholder-label" id="brush-p1" style="color:#81c784;">Toothbrush &amp; Toothpaste</p>
                    </div>
                </div>
            </div>

            <div class="step-row flip" style="border-color:#c8e6c9;">
                <div class="step-content">
                    <div class="step-number-badge" style="background:linear-gradient(135deg,#66bb6a,#2e7d32);">2</div>
                    <div class="step-title" id="brush-s2-title" style="color:#2e7d32;">Brush the Front of Your Teeth</div>
                    <div class="step-desc" id="brush-s2-desc">Start brushing the front of your teeth. Use small circular motions — don't scrub too hard so you don't hurt your gums!</div>
                </div>
                <div class="step-image-box" style="background:linear-gradient(135deg,#e8f5e9,#f1f8f2);border-color:#a5d6a7;">
                    <img src="pictures/motorskills/brush_step2.png" alt="Brush front" onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
                    <div class="step-image-placeholder" style="display:none;flex-direction:column;align-items:center;gap:8px;">
                        <i class="fas fa-smile placeholder-icon" style="color:#81c784;"></i>
                        <p class="placeholder-label" id="brush-p2" style="color:#81c784;">Front Teeth</p>
                    </div>
                </div>
            </div>

            <div class="step-row" style="border-color:#c8e6c9;">
                <div class="step-content">
                    <div class="step-number-badge" style="background:linear-gradient(135deg,#66bb6a,#2e7d32);">3</div>
                    <div class="step-title" id="brush-s3-title" style="color:#2e7d32;">Brush the Inside & Back</div>
                    <div class="step-desc" id="brush-s3-desc">Don't forget to brush the inside and back of your teeth! This is where food and bacteria often hide. Also brush the top and bottom of each tooth.</div>
                </div>
                <div class="step-image-box" style="background:linear-gradient(135deg,#e8f5e9,#f1f8f2);border-color:#a5d6a7;">
                    <img src="pictures/motorskills/brush_step3.jpg" alt="Brush inside" onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
                    <div class="step-image-placeholder" style="display:none;flex-direction:column;align-items:center;gap:8px;">
                        <i class="fas fa-teeth placeholder-icon" style="color:#81c784;"></i>
                        <p class="placeholder-label" id="brush-p3" style="color:#81c784;">Inside &amp; Back</p>
                    </div>
                </div>
            </div>

            <div class="step-row flip" style="border-color:#c8e6c9;">
                <div class="step-content">
                    <div class="step-number-badge" style="background:linear-gradient(135deg,#66bb6a,#2e7d32);">4</div>
                    <div class="step-title" id="brush-s4-title" style="color:#2e7d32;">Brush Your Tongue</div>
                    <div class="step-desc" id="brush-s4-desc">Gently brush your tongue from back to front. This is where many bacteria hide that cause bad breath!</div>
                </div>
                <div class="step-image-box" style="background:linear-gradient(135deg,#e8f5e9,#f1f8f2);border-color:#a5d6a7;">
                    <img src="pictures/motorskills/brush_step4.jpg" alt="Brush tongue" onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
                    <div class="step-image-placeholder" style="display:none;flex-direction:column;align-items:center;gap:8px;">
                        <i class="fas fa-mouth placeholder-icon" style="color:#81c784;"></i>
                        <p class="placeholder-label" id="brush-p4" style="color:#81c784;">Brush Your Tongue</p>
                    </div>
                </div>
            </div>

            <div class="step-row" style="border-color:#c8e6c9;">
                <div class="step-content">
                    <div class="step-number-badge" style="background:linear-gradient(135deg,#66bb6a,#2e7d32);">5</div>
                    <div class="step-title" id="brush-s5-title" style="color:#2e7d32;">Rinse & Spit</div>
                    <div class="step-desc" id="brush-s5-desc">Take water and rinse 2-3 times. Spit it out — don't swallow! Do this every morning when you wake up and at night before sleeping for the cleanest teeth.</div>
                </div>
                <div class="step-image-box" style="background:linear-gradient(135deg,#e8f5e9,#f1f8f2);border-color:#a5d6a7;">
                    <img src="pictures/motorskills/brush_step5.jpg" alt="Rinse" onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
                    <div class="step-image-placeholder" style="display:none;flex-direction:column;align-items:center;gap:8px;">
                        <i class="fas fa-faucet placeholder-icon" style="color:#81c784;"></i>
                        <p class="placeholder-label" id="brush-p5" style="color:#81c784;">Rinse &amp; Spit</p>
                    </div>
                </div>
            </div>

        </div>

        <div class="rainbow-divider"></div>

        <!-- ③ BATHING / PALILIGO -->
        <div class="activity-block" id="act-bath">
            <div class="section-label" style="margin-top:48px;">
                <div class="section-label-line"></div>
                <span class="section-label-text"><i class="fas fa-shower"></i> <span class="act-label-3">ACTIVITY 3</span></span>
                <div class="section-label-line right"></div>
            </div>
            <div class="activity-header" style="background: linear-gradient(135deg, #1565c0 0%, #42a5f5 100%);">
                <div class="activity-header-icon"><i class="fas fa-shower"></i></div>
                <div class="activity-header-info">
                    <div class="activity-header-label" id="act3of6">Activity 3 of 6</div>
                    <div class="activity-header-title" id="act3title">Proper Bathing</div>
                    <div class="activity-header-desc" id="act3desc">Daily bathing removes dirt, sweat, and odor from our bodies. Let's learn the right way to bathe!</div>
                </div>
            </div>

            <div class="benefits-bar" style="border-color:#bbdefb;">
                <div class="benefits-bar-title" id="bath-ben-title" style="color:#1565c0;"><i class="fas fa-circle-check" style="color:#1565c0;"></i> Benefits of Bathing</div>
                <div class="benefits-list">
                    <span class="benefit-chip" style="background:#e3f2fd;border-color:#90caf9;color:#1565c0;"><i class="fas fa-spa"></i> <span id="bathb1">Clean body</span></span>
                    <span class="benefit-chip" style="background:#e3f2fd;border-color:#90caf9;color:#1565c0;"><i class="fas fa-face-smile-relaxed"></i> <span id="bathb2">Relaxing</span></span>
                    <span class="benefit-chip" style="background:#e3f2fd;border-color:#90caf9;color:#1565c0;"><i class="fas fa-wind"></i> <span id="bathb3">No body odor</span></span>
                    <span class="benefit-chip" style="background:#e3f2fd;border-color:#90caf9;color:#1565c0;"><i class="fas fa-virus-slash"></i> <span id="bathb4">Removes germs</span></span>
                    <span class="benefit-chip" style="background:#e3f2fd;border-color:#90caf9;color:#1565c0;"><i class="fas fa-star"></i> <span id="bathb5">Look great</span></span>
                </div>
            </div>

            <div class="steps-heading" id="bath-steps-heading" style="color:#1565c0;"><i class="fas fa-list-ol" style="color:#42a5f5;"></i> Steps for Proper Bathing</div>

            <div class="step-row" style="border-color:#bbdefb;">
                <div class="step-content">
                    <div class="step-number-badge" style="background:linear-gradient(135deg,#42a5f5,#1565c0);">1</div>
                    <div class="step-title" id="bath-s1-title" style="color:#1565c0;">Prepare Everything You Need</div>
                    <div class="step-desc" id="bath-s1-desc">Before entering the bathroom, make sure you have everything ready: soap, shampoo, and a clean towel. So you don't have to get up and leave while bathing!</div>
                </div>
                <div class="step-image-box" style="background:linear-gradient(135deg,#e3f2fd,#f0f8ff);border-color:#90caf9;">
                    <img src="pictures/motorskills/bath_step1.jpg" alt="Prepare" onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
                    <div class="step-image-placeholder" style="display:none;flex-direction:column;align-items:center;gap:8px;">
                        <i class="fas fa-basket-shopping placeholder-icon" style="color:#90caf9;"></i>
                        <p class="placeholder-label" id="bath-p1" style="color:#90caf9;">Prepare Supplies</p>
                    </div>
                </div>
            </div>

            <div class="step-row flip" style="border-color:#bbdefb;">
                <div class="step-content">
                    <div class="step-number-badge" style="background:linear-gradient(135deg,#42a5f5,#1565c0);">2</div>
                    <div class="step-title" id="bath-s2-title" style="color:#1565c0;">Wet Your Whole Body</div>
                    <div class="step-desc" id="bath-s2-desc">Start by wetting your whole body with clean water — from head to toe. Make sure every part of your body is wet.</div>
                </div>
                <div class="step-image-box" style="background:linear-gradient(135deg,#e3f2fd,#f0f8ff);border-color:#90caf9;">
                    <img src="pictures/motorskills/bath_step2.jpg" alt="Wet body" onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
                    <div class="step-image-placeholder" style="display:none;flex-direction:column;align-items:center;gap:8px;">
                        <i class="fas fa-shower placeholder-icon" style="color:#90caf9;"></i>
                        <p class="placeholder-label" id="bath-p2" style="color:#90caf9;">Wet Your Body</p>
                    </div>
                </div>
            </div>

            <div class="step-row" style="border-color:#bbdefb;">
                <div class="step-content">
                    <div class="step-number-badge" style="background:linear-gradient(135deg,#42a5f5,#1565c0);">3</div>
                    <div class="step-title" id="bath-s3-title" style="color:#1565c0;">Soap Your Whole Body</div>
                    <div class="step-desc" id="bath-s3-desc">Apply soap to your whole body — including armpits, between toes, back, and other areas. Make cleaning fun!</div>
                </div>
                <div class="step-image-box" style="background:linear-gradient(135deg,#e3f2fd,#f0f8ff);border-color:#90caf9;">
                    <img src="pictures/motorskills/bath_step3.png" alt="Apply soap" onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
                    <div class="step-image-placeholder" style="display:none;flex-direction:column;align-items:center;gap:8px;">
                        <i class="fas fa-soap placeholder-icon" style="color:#90caf9;"></i>
                        <p class="placeholder-label" id="bath-p3" style="color:#90caf9;">Apply Soap</p>
                    </div>
                </div>
            </div>

            <div class="step-row flip" style="border-color:#bbdefb;">
                <div class="step-content">
                    <div class="step-number-badge" style="background:linear-gradient(135deg,#42a5f5,#1565c0);">4</div>
                    <div class="step-title" id="bath-s4-title" style="color:#1565c0;">Wash Your Hair</div>
                    <div class="step-desc" id="bath-s4-desc">Use the right amount of shampoo to wash your hair and scalp. Gently massage the scalp and make sure to rinse completely before finishing.</div>
                </div>
                <div class="step-image-box" style="background:linear-gradient(135deg,#e3f2fd,#f0f8ff);border-color:#90caf9;">
                    <img src="pictures/motorskills/bath_step4.jpg" alt="Wash hair" onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
                    <div class="step-image-placeholder" style="display:none;flex-direction:column;align-items:center;gap:8px;">
                        <i class="fas fa-person-half-dress placeholder-icon" style="color:#90caf9;"></i>
                        <p class="placeholder-label" id="bath-p4" style="color:#90caf9;">Wash Your Hair</p>
                    </div>
                </div>
            </div>

            <div class="step-row" style="border-color:#bbdefb;">
                <div class="step-content">
                    <div class="step-number-badge" style="background:linear-gradient(135deg,#42a5f5,#1565c0);">5</div>
                    <div class="step-title" id="bath-s5-title" style="color:#1565c0;">Rinse & Dry Off</div>
                    <div class="step-desc" id="bath-s5-desc">Rinse all the soap and shampoo off thoroughly — none should remain! Then dry your body well with a clean towel.</div>
                </div>
                <div class="step-image-box" style="background:linear-gradient(135deg,#e3f2fd,#f0f8ff);border-color:#90caf9;">
                    <img src="pictures/motorskills/bath_step5.jpg" alt="Dry off" onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
                    <div class="step-image-placeholder" style="display:none;flex-direction:column;align-items:center;gap:8px;">
                        <i class="fas fa-scroll placeholder-icon" style="color:#90caf9;"></i>
                        <p class="placeholder-label" id="bath-p5" style="color:#90caf9;">Rinse &amp; Dry Off</p>
                    </div>
                </div>
            </div>

        </div>

        <div class="rainbow-divider"></div>

        <!-- ④ HAIR COMBING / PAGSUSUKLAY -->
        <div class="activity-block" id="act-comb">
            <div class="section-label" style="margin-top:48px;">
                <div class="section-label-line"></div>
                <span class="section-label-text"><i class="fas fa-user-hair-long"></i> <span class="act-label-4">ACTIVITY 4</span></span>
                <div class="section-label-line right"></div>
            </div>
            <div class="activity-header" style="background: linear-gradient(135deg, #e91e63 0%, #f48fb1 100%);">
                <div class="activity-header-icon"><i class="fas fa-comb"></i></div>
                <div class="activity-header-info">
                    <div class="activity-header-label" id="act4of6">Activity 4 of 6</div>
                    <div class="activity-header-title" id="act4title">Hair Combing</div>
                    <div class="activity-header-desc" id="act4desc">Neat hair shows proper self-care. Learn how to comb your hair without it hurting!</div>
                </div>
            </div>

            <div class="benefits-bar" style="border-color:#f8bbd0;">
                <div class="benefits-bar-title" id="comb-ben-title" style="color:#c2185b;"><i class="fas fa-circle-check" style="color:#c2185b;"></i> Benefits of Hair Combing</div>
                <div class="benefits-list">
                    <span class="benefit-chip" style="background:#fce4ec;border-color:#f48fb1;color:#c2185b;"><i class="fas fa-wand-magic-sparkles"></i> <span id="cb1">Neat hair</span></span>
                    <span class="benefit-chip" style="background:#fce4ec;border-color:#f48fb1;color:#c2185b;"><i class="fas fa-shield"></i> <span id="cb2">No lice</span></span>
                    <span class="benefit-chip" style="background:#fce4ec;border-color:#f48fb1;color:#c2185b;"><i class="fas fa-heart-pulse"></i> <span id="cb3">Healthy scalp</span></span>
                    <span class="benefit-chip" style="background:#fce4ec;border-color:#f48fb1;color:#c2185b;"><i class="fas fa-star"></i> <span id="cb4">Good looks</span></span>
                    <span class="benefit-chip" style="background:#fce4ec;border-color:#f48fb1;color:#c2185b;"><i class="fas fa-face-grin-stars"></i> <span id="cb5">Confidence</span></span>
                </div>
            </div>

            <div class="steps-heading" id="comb-steps-heading" style="color:#c2185b;"><i class="fas fa-list-ol" style="color:#f48fb1;"></i> Steps for Hair Combing</div>

            <div class="step-row" style="border-color:#f8bbd0;">
                <div class="step-content">
                    <div class="step-number-badge" style="background:linear-gradient(135deg,#f48fb1,#c2185b);">1</div>
                    <div class="step-title" id="comb-s1-title" style="color:#c2185b;">Use Your Own Comb</div>
                    <div class="step-desc" id="comb-s1-desc">Always use your own comb — never borrow! Sharing combs can spread lice and scalp infections to others.</div>
                </div>
                <div class="step-image-box" style="background:linear-gradient(135deg,#fce4ec,#fff0f5);border-color:#f48fb1;">
                    <img src="pictures/motorskills/comb_step1.jpg" alt="Own comb" onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
                    <div class="step-image-placeholder" style="display:none;flex-direction:column;align-items:center;gap:8px;">
                        <i class="fas fa-comb placeholder-icon" style="color:#f48fb1;"></i>
                        <p class="placeholder-label" id="comb-p1" style="color:#f48fb1;">Your Own Comb</p>
                    </div>
                </div>
            </div>

            <div class="step-row flip" style="border-color:#f8bbd0;">
                <div class="step-content">
                    <div class="step-number-badge" style="background:linear-gradient(135deg,#f48fb1,#c2185b);">2</div>
                    <div class="step-title" id="comb-s2-title" style="color:#c2185b;">Start at the Tips of Your Hair</div>
                    <div class="step-desc" id="comb-s2-desc">This is the secret to painless combing! Start at the ends of your hair and slowly work upward. Never start at the top — it just creates more tangles!</div>
                </div>
                <div class="step-image-box" style="background:linear-gradient(135deg,#fce4ec,#fff0f5);border-color:#f48fb1;">
                    <img src="pictures/motorskills/comb_step2.jpg" alt="Start at tips" onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
                    <div class="step-image-placeholder" style="display:none;flex-direction:column;align-items:center;gap:8px;">
                        <i class="fas fa-arrow-down placeholder-icon" style="color:#f48fb1;"></i>
                        <p class="placeholder-label" id="comb-p2" style="color:#f48fb1;">Tips First</p>
                    </div>
                </div>
            </div>

            <div class="step-row" style="border-color:#f8bbd0;">
                <div class="step-content">
                    <div class="step-number-badge" style="background:linear-gradient(135deg,#f48fb1,#c2185b);">3</div>
                    <div class="step-title" id="comb-s3-title" style="color:#c2185b;">Comb Carefully</div>
                    <div class="step-desc" id="comb-s3-desc">Comb your whole hair from scalp to tips slowly and carefully. If there's a knot, hold the hair near the root so it doesn't hurt while untangling.</div>
                </div>
                <div class="step-image-box" style="background:linear-gradient(135deg,#fce4ec,#fff0f5);border-color:#f48fb1;">
                    <img src="pictures/motorskills/comb_step3.jpg" alt="Comb carefully" onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
                    <div class="step-image-placeholder" style="display:none;flex-direction:column;align-items:center;gap:8px;">
                        <i class="fas fa-user placeholder-icon" style="color:#f48fb1;"></i>
                        <p class="placeholder-label" id="comb-p3" style="color:#f48fb1;">Comb Carefully</p>
                    </div>
                </div>
            </div>

            <div class="step-row flip" style="border-color:#f8bbd0;">
                <div class="step-content">
                    <div class="step-number-badge" style="background:linear-gradient(135deg,#f48fb1,#c2185b);">4</div>
                    <div class="step-title" id="comb-s4-title" style="color:#c2185b;">Comb Every Day</div>
                    <div class="step-desc" id="comb-s4-desc">Make it a habit to comb your hair every morning before school and at night before sleeping. Neat hair = happy day!</div>
                </div>
                <div class="step-image-box" style="background:linear-gradient(135deg,#fce4ec,#fff0f5);border-color:#f48fb1;">
                    <img src="pictures/motorskills/comb_step4.jpg" alt="Every day" onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
                    <div class="step-image-placeholder" style="display:none;flex-direction:column;align-items:center;gap:8px;">
                        <i class="fas fa-calendar-day placeholder-icon" style="color:#f48fb1;"></i>
                        <p class="placeholder-label" id="comb-p4" style="color:#f48fb1;">Make It a Habit</p>
                    </div>
                </div>
            </div>

        </div>

        <div class="rainbow-divider"></div>

        <!-- ⑤ NAIL TRIMMING / PAGGUPIT NG KUKO -->
        <div class="activity-block" id="act-nails">
            <div class="section-label" style="margin-top:48px;">
                <div class="section-label-line"></div>
                <span class="section-label-text"><i class="fas fa-scissors"></i> <span class="act-label-5">ACTIVITY 5</span></span>
                <div class="section-label-line right"></div>
            </div>
            <div class="activity-header" style="background: linear-gradient(135deg, #e65100 0%, #ffa726 100%);">
                <div class="activity-header-icon"><i class="fas fa-scissors"></i></div>
                <div class="activity-header-info">
                    <div class="activity-header-label" id="act5of6">Activity 5 of 6</div>
                    <div class="activity-header-title" id="act5title">Nail Trimming</div>
                    <div class="activity-header-desc" id="act5desc">Short, clean nails prevent dirt and germs from collecting. Learn the right way to trim your nails!</div>
                </div>
            </div>

            <div class="benefits-bar" style="border-color:#ffe0b2;">
                <div class="benefits-bar-title" id="nails-ben-title" style="color:#e65100;"><i class="fas fa-circle-check" style="color:#e65100;"></i> Benefits of Nail Trimming</div>
                <div class="benefits-list">
                    <span class="benefit-chip" style="background:#fff3e0;border-color:#ffcc80;color:#e65100;"><i class="fas fa-virus-slash"></i> <span id="nb1">No germs under nails</span></span>
                    <span class="benefit-chip" style="background:#fff3e0;border-color:#ffcc80;color:#e65100;"><i class="fas fa-utensils"></i> <span id="nb2">Safe to eat</span></span>
                    <span class="benefit-chip" style="background:#fff3e0;border-color:#ffcc80;color:#e65100;"><i class="fas fa-hand-sparkles"></i> <span id="nb3">Beautiful hands</span></span>
                    <span class="benefit-chip" style="background:#fff3e0;border-color:#ffcc80;color:#e65100;"><i class="fas fa-ban"></i> <span id="nb4">No in-grown nails</span></span>
                    <span class="benefit-chip" style="background:#fff3e0;border-color:#ffcc80;color:#e65100;"><i class="fas fa-wand-magic-sparkles"></i> <span id="nb5">Clean look</span></span>
                </div>
            </div>

            <div class="steps-heading" id="nails-steps-heading" style="color:#e65100;"><i class="fas fa-list-ol" style="color:#ffa726;"></i> Steps for Nail Trimming</div>

            <div class="step-row" style="border-color:#ffe0b2;">
                <div class="step-content">
                    <div class="step-number-badge" style="background:linear-gradient(135deg,#ffa726,#e65100);">1</div>
                    <div class="step-title" id="nails-s1-title" style="color:#e65100;">Ask a Parent for Help</div>
                    <div class="step-desc" id="nails-s1-desc">For children, always ask a parent or teacher for help before trimming nails. Nail cutters are sharp — careful handling prevents accidents!</div>
                </div>
                <div class="step-image-box" style="background:linear-gradient(135deg,#fff3e0,#fffde7);border-color:#ffcc80;">
                    <img src="pictures/motorskills/nails_step1.jpg" alt="Parent help" onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
                    <div class="step-image-placeholder" style="display:none;flex-direction:column;align-items:center;gap:8px;">
                        <i class="fas fa-people-pulling placeholder-icon" style="color:#ffa726;"></i>
                        <p class="placeholder-label" id="nails-p1" style="color:#ffa726;">Parent's Help</p>
                    </div>
                </div>
            </div>

            <div class="step-row flip" style="border-color:#ffe0b2;">
                <div class="step-content">
                    <div class="step-number-badge" style="background:linear-gradient(135deg,#ffa726,#e65100);">2</div>
                    <div class="step-title" id="nails-s2-title" style="color:#e65100;">Use the Right Tool</div>
                    <div class="step-desc" id="nails-s2-desc">Use the right nail clipper for children — there are special clippers for small children's nails. Safer and easier to use than scissors.</div>
                </div>
                <div class="step-image-box" style="background:linear-gradient(135deg,#fff3e0,#fffde7);border-color:#ffcc80;">
                    <img src="pictures/motorskills/nails_step2.jpg" alt="Nail clipper" onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
                    <div class="step-image-placeholder" style="display:none;flex-direction:column;align-items:center;gap:8px;">
                        <i class="fas fa-scissors placeholder-icon" style="color:#ffa726;"></i>
                        <p class="placeholder-label" id="nails-p2" style="color:#ffa726;">Nail Clipper</p>
                    </div>
                </div>
            </div>

            <div class="step-row" style="border-color:#ffe0b2;">
                <div class="step-content">
                    <div class="step-number-badge" style="background:linear-gradient(135deg,#ffa726,#e65100);">3</div>
                    <div class="step-title" id="nails-s3-title" style="color:#e65100;">Cut Straight Across</div>
                    <div class="step-desc" id="nails-s3-desc">Cut the nail straight — not too short and not too long. The right length is level with the fingertip. Avoid cutting the sides to prevent in-grown nails.</div>
                </div>
                <div class="step-image-box" style="background:linear-gradient(135deg,#fff3e0,#fffde7);border-color:#ffcc80;">
                    <img src="pictures/motorskills/nails_step3.jpg" alt="Cut straight" onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
                    <div class="step-image-placeholder" style="display:none;flex-direction:column;align-items:center;gap:8px;">
                        <i class="fas fa-hand placeholder-icon" style="color:#ffa726;"></i>
                        <p class="placeholder-label" id="nails-p3" style="color:#ffa726;">Cut Straight</p>
                    </div>
                </div>
            </div>

            <div class="step-row flip" style="border-color:#ffe0b2;">
                <div class="step-content">
                    <div class="step-number-badge" style="background:linear-gradient(135deg,#ffa726,#e65100);">4</div>
                    <div class="step-title" id="nails-s4-title" style="color:#e65100;">Clean Under the Nails</div>
                    <div class="step-desc" id="nails-s4-desc">After trimming, use a small brush to clean under the nails. This is where dirt and germs you can't see usually hide!</div>
                </div>
                <div class="step-image-box" style="background:linear-gradient(135deg,#fff3e0,#fffde7);border-color:#ffcc80;">
                    <img src="pictures/motorskills/nails_step4.jpg" alt="Clean under nails" onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
                    <div class="step-image-placeholder" style="display:none;flex-direction:column;align-items:center;gap:8px;">
                        <i class="fas fa-broom placeholder-icon" style="color:#ffa726;"></i>
                        <p class="placeholder-label" id="nails-p4" style="color:#ffa726;">Clean Under Nails</p>
                    </div>
                </div>
            </div>

            <div class="step-row" style="border-color:#ffe0b2;">
                <div class="step-content">
                    <div class="step-number-badge" style="background:linear-gradient(135deg,#ffa726,#e65100);">5</div>
                    <div class="step-title" id="nails-s5-title" style="color:#e65100;">Weekly Habit</div>
                    <div class="step-desc" id="nails-s5-desc">Trim nails once a week to keep them clean and the right length. You can make a schedule — like every Saturday after bathing!</div>
                </div>
                <div class="step-image-box" style="background:linear-gradient(135deg,#fff3e0,#fffde7);border-color:#ffcc80;">
                    <img src="pictures/motorskills/nails_step5.jpg" alt="Weekly habit" onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
                    <div class="step-image-placeholder" style="display:none;flex-direction:column;align-items:center;gap:8px;">
                        <i class="fas fa-calendar-week placeholder-icon" style="color:#ffa726;"></i>
                        <p class="placeholder-label" id="nails-p5" style="color:#ffa726;">Weekly Habit</p>
                    </div>
                </div>
            </div>

        </div>

        <div class="rainbow-divider"></div>

        <!-- ⑥ GETTING DRESSED / PAGBIBIHIS NG DAMIT -->
        <div class="activity-block" id="act-clothes">
            <div class="section-label" style="margin-top:48px;">
                <div class="section-label-line"></div>
                <span class="section-label-text"><i class="fas fa-shirt"></i> <span class="act-label-6">ACTIVITY 6</span></span>
                <div class="section-label-line right"></div>
            </div>
            <div class="activity-header" style="background: linear-gradient(135deg, #4527a0 0%, #7e57c2 100%);">
                <div class="activity-header-icon"><i class="fas fa-shirt"></i></div>
                <div class="activity-header-info">
                    <div class="activity-header-label" id="act6of6">Activity 6 of 6</div>
                    <div class="activity-header-title" id="act6title">Getting Dressed</div>
                    <div class="activity-header-desc" id="act6desc">Getting dressed on your own is a big step toward independence! Learn the right order for putting on clothes.</div>
                </div>
            </div>

            <div class="benefits-bar" style="border-color:#d1c4e9;">
                <div class="benefits-bar-title" id="clothes-ben-title" style="color:#4527a0;"><i class="fas fa-circle-check" style="color:#4527a0;"></i> Benefits of Getting Dressed Properly</div>
                <div class="benefits-list">
                    <span class="benefit-chip" style="background:#ede7f6;border-color:#b39ddb;color:#4527a0;"><i class="fas fa-temperature-half"></i> <span id="clb1">Protection from weather</span></span>
                    <span class="benefit-chip" style="background:#ede7f6;border-color:#b39ddb;color:#4527a0;"><i class="fas fa-person-walking"></i> <span id="clb2">Independence</span></span>
                    <span class="benefit-chip" style="background:#ede7f6;border-color:#b39ddb;color:#4527a0;"><i class="fas fa-face-grin-stars"></i> <span id="clb3">Confidence</span></span>
                    <span class="benefit-chip" style="background:#ede7f6;border-color:#b39ddb;color:#4527a0;"><i class="fas fa-school"></i> <span id="clb4">Ready for school</span></span>
                    <span class="benefit-chip" style="background:#ede7f6;border-color:#b39ddb;color:#4527a0;"><i class="fas fa-wand-magic-sparkles"></i> <span id="clb5">Great looks</span></span>
                </div>
            </div>

            <div class="steps-heading" id="clothes-steps-heading" style="color:#4527a0;"><i class="fas fa-list-ol" style="color:#7e57c2;"></i> Steps for Getting Dressed</div>

            <div class="step-row" style="border-color:#d1c4e9;">
                <div class="step-content">
                    <div class="step-number-badge" style="background:linear-gradient(135deg,#7e57c2,#4527a0);">1</div>
                    <div class="step-title" id="clothes-s1-title" style="color:#4527a0;">Choose Clean Clothes</div>
                    <div class="step-desc" id="clothes-s1-desc">Before dressing, make sure the clothes are clean and appropriate for the weather — hot or cold. Don't wear yesterday's dirty clothes to stay healthy and fresh!</div>
                </div>
                <div class="step-image-box" style="background:linear-gradient(135deg,#ede7f6,#f5f0ff);border-color:#b39ddb;">
                    <img src="pictures/motorskills/clothes_step1.jpg" alt="Clean clothes" onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
                    <div class="step-image-placeholder" style="display:none;flex-direction:column;align-items:center;gap:8px;">
                        <i class="fas fa-shirt placeholder-icon" style="color:#b39ddb;"></i>
                        <p class="placeholder-label" id="clothes-p1" style="color:#b39ddb;">Clean Clothes</p>
                    </div>
                </div>
            </div>

            <div class="step-row flip" style="border-color:#d1c4e9;">
                <div class="step-content">
                    <div class="step-number-badge" style="background:linear-gradient(135deg,#7e57c2,#4527a0);">2</div>
                    <div class="step-title" id="clothes-s2-title" style="color:#4527a0;">Put It on the Right Way</div>
                    <div class="step-desc" id="clothes-s2-desc">Check the label or tag inside the clothing to know which is the front and which is the back. Most clothes have a logo or design on the front to help you remember!</div>
                </div>
                <div class="step-image-box" style="background:linear-gradient(135deg,#ede7f6,#f5f0ff);border-color:#b39ddb;">
                    <img src="pictures/motorskills/clothes_step2.jpg" alt="Right direction" onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
                    <div class="step-image-placeholder" style="display:none;flex-direction:column;align-items:center;gap:8px;">
                        <i class="fas fa-rotate placeholder-icon" style="color:#b39ddb;"></i>
                        <p class="placeholder-label" id="clothes-p2" style="color:#b39ddb;">Right Direction</p>
                    </div>
                </div>
            </div>

            <div class="step-row" style="border-color:#d1c4e9;">
                <div class="step-content">
                    <div class="step-number-badge" style="background:linear-gradient(135deg,#7e57c2,#4527a0);">3</div>
                    <div class="step-title" id="clothes-s3-title" style="color:#4527a0;">Underwear First</div>
                    <div class="step-desc" id="clothes-s3-desc">Always put on underwear first — like briefs, panties, and undershirts — before other clothing. Underwear provides comfort and protection for your body.</div>
                </div>
                <div class="step-image-box" style="background:linear-gradient(135deg,#ede7f6,#f5f0ff);border-color:#b39ddb;">
                    <img src="pictures/motorskills/clothes_step3.jpg" alt="Underwear first" onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
                    <div class="step-image-placeholder" style="display:none;flex-direction:column;align-items:center;gap:8px;">
                        <i class="fas fa-vest placeholder-icon" style="color:#b39ddb;"></i>
                        <p class="placeholder-label" id="clothes-p3" style="color:#b39ddb;">Underwear First</p>
                    </div>
                </div>
            </div>

            <div class="step-row flip" style="border-color:#d1c4e9;">
                <div class="step-content">
                    <div class="step-number-badge" style="background:linear-gradient(135deg,#7e57c2,#4527a0);">4</div>
                    <div class="step-title" id="clothes-s4-title" style="color:#4527a0;">Use Buttons & Zippers Properly</div>
                    <div class="step-desc" id="clothes-s4-desc">Learn to use buttons — start from the bottom button and work upward. For zippers, hold the bottom and slowly zip up. Don't rush!</div>
                </div>
                <div class="step-image-box" style="background:linear-gradient(135deg,#ede7f6,#f5f0ff);border-color:#b39ddb;">
                    <img src="pictures/motorskills/clothes_step4.jpg" alt="Buttons and zipper" onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
                    <div class="step-image-placeholder" style="display:none;flex-direction:column;align-items:center;gap:8px;">
                        <i class="fas fa-circle-dot placeholder-icon" style="color:#b39ddb;"></i>
                        <p class="placeholder-label" id="clothes-p4" style="color:#b39ddb;">Buttons &amp; Zipper</p>
                    </div>
                </div>
            </div>

            <div class="step-row" style="border-color:#d1c4e9;">
                <div class="step-content">
                    <div class="step-number-badge" style="background:linear-gradient(135deg,#7e57c2,#4527a0);">5</div>
                    <div class="step-title" id="clothes-s5-title" style="color:#4527a0;">Tidy Up & Check in the Mirror</div>
                    <div class="step-desc" id="clothes-s5-desc">After dressing, adjust your clothes — nothing sticking out, nothing folded wrong, collar is straight. Check in the mirror to make sure you're ready and looking great!</div>
                </div>
                <div class="step-image-box" style="background:linear-gradient(135deg,#ede7f6,#f5f0ff);border-color:#b39ddb;">
                    <img src="pictures/motorskills/clothes_step5.jpg" alt="Check mirror" onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
                    <div class="step-image-placeholder" style="display:none;flex-direction:column;align-items:center;gap:8px;">
                        <i class="fas fa-mirror placeholder-icon" style="color:#b39ddb;"></i>
                        <p class="placeholder-label" id="clothes-p5" style="color:#b39ddb;">Check in Mirror</p>
                    </div>
                </div>
            </div>

        </div>

    </div><!-- /container -->
</div><!-- /activities-wrapper -->

<!-- ── TIPS ── -->
<section class="tips-section">
    <div class="container">
        <div class="section-label">
            <div class="section-label-line"></div>
            <span class="section-label-text"><i class="fas fa-lightbulb"></i> <span id="tipsLabel">TIPS</span></span>
            <div class="section-label-line right"></div>
        </div>
        <div class="text-center mb-4">
            <h2 id="tipsTitle" style="font-family:'Fredoka One',cursive;font-size:clamp(1.5rem,3.5vw,2.2rem);color:#006978;">Reminders to Stay Clean <i class="fas fa-star" style="color:#ffc107;"></i></h2>
        </div>
        <div class="tips-grid">
            <div class="tip-card">
                <div class="tip-icon-box"><i class="fas fa-clock"></i></div>
                <div>
                    <div class="tip-title" id="tip1title">Brush for 2 Minutes</div>
                    <div class="tip-text" id="tip1text">Brush your teeth for 2 minutes — 30 seconds for each section of your mouth. You can sing a song while brushing!</div>
                </div>
            </div>
            <div class="tip-card">
                <div class="tip-icon-box"><i class="fas fa-droplet"></i></div>
                <div>
                    <div class="tip-title" id="tip2title">20-Second Handwashing</div>
                    <div class="tip-text" id="tip2text">Wash your hands for at least 20 seconds with soap and water — think of "Happy Birthday" twice!</div>
                </div>
            </div>
            <div class="tip-card">
                <div class="tip-icon-box"><i class="fas fa-sun"></i></div>
                <div>
                    <div class="tip-title" id="tip3title">Brush Morning and Night</div>
                    <div class="tip-text" id="tip3text">Brush your teeth when you wake up in the morning and before sleeping at night to prevent cavities!</div>
                </div>
            </div>
            <div class="tip-card">
                <div class="tip-icon-box"><i class="fas fa-hand-sparkles"></i></div>
                <div>
                    <div class="tip-title" id="tip4title">Never Forget to Wash</div>
                    <div class="tip-text" id="tip4text">Always wash your hands before eating, after using the bathroom, and after playing!</div>
                </div>
            </div>
            <div class="tip-card">
                <div class="tip-icon-box"><i class="fas fa-scissors"></i></div>
                <div>
                    <div class="tip-title" id="tip5title">Weekly Nail Trimming</div>
                    <div class="tip-text" id="tip5text">Trim your nails once a week so they don't collect dirt and germs under them.</div>
                </div>
            </div>
            <div class="tip-card">
                <div class="tip-icon-box"><i class="fas fa-shirt"></i></div>
                <div>
                    <div class="tip-title" id="tip6title">Always Wear Clean Clothes</div>
                    <div class="tip-text" id="tip6text">Wear clean clothes every day! Avoid wearing dirty clothes that can affect your health.</div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'take-quiz.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
/* ══════════════════════════════════════════════════════════
   LANGUAGE DATA
══════════════════════════════════════════════════════════ */
const UI = {
    en: {
        navBack: 'Back',
        heroBadge: 'Motor Skills Lesson',
        heroDesc: 'Learn how to take care of our body — handwashing, tooth brushing, bathing, and more!',
        langBadge: '<i class="fas fa-flag-usa"></i> English',
        sectionWhatLabel: 'WHAT ARE MOTOR SKILLS?',
        introTitle: 'What are Motor Skills?',
        introDesc: '<strong style="color:#006978;">Motor skills</strong> are the ability of our body to move and do things using our hands, feet, and other body parts. In this lesson, we will learn how to take care of our own body — an important part of growing up as a healthy child!',
        importanceTitle: 'Why are Motor Skills Important?',
        imp1title:'Health', imp1desc:'A clean body protects us from germs, bacteria, and infections. When we are clean, we are healthier!',
        imp2title:'Confidence', imp2desc:'Being clean and neat gives us self-confidence in school, with friends, and everywhere we go.',
        imp3title:'Independence', imp3desc:'You learn to take care of yourself without help from others — a proud moment for you and your family!',
        imp4title:'Good Habits', imp4desc:'The habits you learn now will stay with you as you grow up. This is the foundation of a healthy life!',
        purposeTitle: 'Goals of This Lesson',
        pur1title:'Learn the Right Way', pur1desc:'We will learn the proper method for each self-care habit — step by step so it\'s easy to follow.',
        pur2title:'Make It a Habit', pur2desc:'The goal is to make proper hygiene a regular routine — not just once, but every day!',
        pur3title:'Prevent Illness', pur3desc:'Through proper hygiene, we can avoid common illnesses like colds, flu, and tooth infections.',
        pur4title:'Grow and Develop', pur4desc:'Every step you learn in this lesson helps you grow as a healthy and happy child!',
        // Activities
        actLabels: ['ACTIVITY 1','ACTIVITY 2','ACTIVITY 3','ACTIVITY 4','ACTIVITY 5','ACTIVITY 6'],
        act1of6:'Activity 1 of 6', act1title:'Handwashing', act1desc:'Handwashing is the simplest way to prevent the spread of germs and illness. Learn the right way!',
        act2of6:'Activity 2 of 6', act2title:'Tooth Brushing', act2desc:'Healthy teeth are the start of a healthy body! Learn the right way to brush so your smile stays clean.',
        act3of6:'Activity 3 of 6', act3title:'Proper Bathing', act3desc:'Daily bathing removes dirt, sweat, and odor from our bodies. Let\'s learn the right way to bathe!',
        act4of6:'Activity 4 of 6', act4title:'Hair Combing', act4desc:'Neat hair shows proper self-care. Learn how to comb your hair without it hurting!',
        act5of6:'Activity 5 of 6', act5title:'Nail Trimming', act5desc:'Short, clean nails prevent dirt and germs from collecting. Learn the right way to trim your nails!',
        act6of6:'Activity 6 of 6', act6title:'Getting Dressed', act6desc:'Getting dressed on your own is a big step toward independence! Learn the right order for putting on clothes.',
        // Benefits
        'wash-ben-title':'Benefits of Handwashing', wb1:'Kills germs', wb2:'Prevents illness', wb3:'Safe to eat', wb4:'Protects family', wb5:'Healthy at school',
        'wash-steps-heading':'Steps for Handwashing',
        'wash-s1-title':'Turn on the Tap', 'wash-s1-desc':'Turn on the faucet and let the water run. Place your hands under the water and wet them thoroughly — including between your fingers!',
        'wash-s2-title':'Apply Soap', 'wash-s2-desc':'Get soap and apply it to your hands. Make sure there\'s enough soap on both hands — including your palms and the back of your hands.',
        'wash-s3-title':'Scrub for 20 Seconds', 'wash-s3-desc':'Scrub your hands vigorously for 20 seconds — as long as singing "Happy Birthday" twice! Clean the palms, backs, between fingers, and under nails.',
        'wash-s4-title':'Rinse Thoroughly', 'wash-s4-desc':'Rinse all the soap off with clean running water. Make sure no soap remains between your fingers or under your nails.',
        'wash-s5-title':'Dry Your Hands', 'wash-s5-desc':'Use a clean cloth or paper towel to dry your hands. Don\'t leave them wet — wet hands pick up germs more easily!',
        'wash-p1':'Turn on Tap','wash-p2':'Apply Soap','wash-p3':'Scrub 20 Seconds','wash-p4':'Rinse Well','wash-p5':'Dry Your Hands',
        mqBadge1:'MINI QUIZ', mqTitle1:'Handwashing — Test Yourself!',
        'qresult-wash-top':'Done! Your score for Handwashing:', 'qresult-wash-bot':'Ready for the next activity!',
        'brush-ben-title':'Benefits of Tooth Brushing', bb1:'Healthy teeth', bb2:'Beautiful smile', bb3:'Fresh breath', bb4:'No cavities', bb5:'Save on dentist',
        'brush-steps-heading':'Steps for Tooth Brushing',
        'brush-s1-title':'Get Your Toothbrush & Toothpaste','brush-s1-desc':'Use your own toothbrush — never borrow! Apply a pea-sized amount of toothpaste to the brush. Don\'t use too much!',
        'brush-s2-title':'Brush the Front of Your Teeth','brush-s2-desc':'Start brushing the front of your teeth. Use small circular motions — don\'t scrub too hard so you don\'t hurt your gums!',
        'brush-s3-title':'Brush the Inside & Back','brush-s3-desc':'Don\'t forget to brush the inside and back of your teeth! This is where food and bacteria often hide. Also brush the top and bottom of each tooth.',
        'brush-s4-title':'Brush Your Tongue','brush-s4-desc':'Gently brush your tongue from back to front. This is where many bacteria hide that cause bad breath!',
        'brush-s5-title':'Rinse & Spit','brush-s5-desc':'Take water and rinse 2-3 times. Spit it out — don\'t swallow! Do this every morning and at night before sleeping for the cleanest teeth.',
        'brush-p1':'Toothbrush & Toothpaste','brush-p2':'Front Teeth','brush-p3':'Inside & Back','brush-p4':'Brush Your Tongue','brush-p5':'Rinse & Spit',
        mqBadge2:'MINI QUIZ', mqTitle2:'Tooth Brushing — Test Yourself!',
        'qresult-brush-top':'Done! Your score for Tooth Brushing:', 'qresult-brush-bot':'Great job! On to the next activity!',
        'bath-ben-title':'Benefits of Bathing', bathb1:'Clean body', bathb2:'Relaxing', bathb3:'No body odor', bathb4:'Removes germs', bathb5:'Look great',
        'bath-steps-heading':'Steps for Proper Bathing',
        'bath-s1-title':'Prepare Everything You Need','bath-s1-desc':'Before entering the bathroom, make sure you have everything ready: soap, shampoo, and a clean towel. So you don\'t have to get up and leave while bathing!',
        'bath-s2-title':'Wet Your Whole Body','bath-s2-desc':'Start by wetting your whole body with clean water — from head to toe. Make sure every part of your body is wet.',
        'bath-s3-title':'Soap Your Whole Body','bath-s3-desc':'Apply soap to your whole body — including armpits, between toes, back, and other areas. Make cleaning fun!',
        'bath-s4-title':'Wash Your Hair','bath-s4-desc':'Use the right amount of shampoo to wash your hair and scalp. Gently massage the scalp and make sure to rinse completely before finishing.',
        'bath-s5-title':'Rinse & Dry Off','bath-s5-desc':'Rinse all the soap and shampoo off thoroughly — none should remain! Then dry your body well with a clean towel.',
        'bath-p1':'Prepare Supplies','bath-p2':'Wet Your Body','bath-p3':'Apply Soap','bath-p4':'Wash Your Hair','bath-p5':'Rinse & Dry Off',
        mqBadge3:'MINI QUIZ', mqTitle3:'Bathing — Test Yourself!',
        'qresult-bath-top':'Done! Your score for Bathing:', 'qresult-bath-bot':'Excellent! Next one!',
        'comb-ben-title':'Benefits of Hair Combing', cb1:'Neat hair', cb2:'No lice', cb3:'Healthy scalp', cb4:'Good looks', cb5:'Confidence',
        'comb-steps-heading':'Steps for Hair Combing',
        'comb-s1-title':'Use Your Own Comb','comb-s1-desc':'Always use your own comb — never borrow! Sharing combs can spread lice and scalp infections to others.',
        'comb-s2-title':'Start at the Tips of Your Hair','comb-s2-desc':'This is the secret to painless combing! Start at the ends of your hair and slowly work upward. Never start at the top — it just creates more tangles!',
        'comb-s3-title':'Comb Carefully','comb-s3-desc':'Comb your whole hair from scalp to tips slowly and carefully. If there\'s a knot, hold the hair near the root so it doesn\'t hurt while untangling.',
        'comb-s4-title':'Comb Every Day','comb-s4-desc':'Make it a habit to comb your hair every morning before school and at night before sleeping. Neat hair = happy day!',
        'comb-p1':'Your Own Comb','comb-p2':'Tips First','comb-p3':'Comb Carefully','comb-p4':'Make It a Habit',
        mqBadge4:'MINI QUIZ', mqTitle4:'Hair Combing — Test Yourself!',
        'qresult-comb-top':'Done! Your score for Hair Combing:', 'qresult-comb-bot':'Amazing! One more!',
        'nails-ben-title':'Benefits of Nail Trimming', nb1:'No germs under nails', nb2:'Safe to eat', nb3:'Beautiful hands', nb4:'No in-grown nails', nb5:'Clean look',
        'nails-steps-heading':'Steps for Nail Trimming',
        'nails-s1-title':'Ask a Parent for Help','nails-s1-desc':'For children, always ask a parent or teacher for help before trimming nails. Nail cutters are sharp — careful handling prevents accidents!',
        'nails-s2-title':'Use the Right Tool','nails-s2-desc':'Use the right nail clipper for children — there are special clippers for small children\'s nails. Safer and easier to use than scissors.',
        'nails-s3-title':'Cut Straight Across','nails-s3-desc':'Cut the nail straight — not too short and not too long. The right length is level with the fingertip. Avoid cutting the sides to prevent in-grown nails.',
        'nails-s4-title':'Clean Under the Nails','nails-s4-desc':'After trimming, use a small brush to clean under the nails. This is where dirt and germs you can\'t see usually hide!',
        'nails-s5-title':'Weekly Habit','nails-s5-desc':'Trim nails once a week to keep them clean and the right length. You can make a schedule — like every Saturday after bathing!',
        'nails-p1':'Parent\'s Help','nails-p2':'Nail Clipper','nails-p3':'Cut Straight','nails-p4':'Clean Under Nails','nails-p5':'Weekly Habit',
        mqBadge5:'MINI QUIZ', mqTitle5:'Nail Trimming — Test Yourself!',
        'qresult-nails-top':'Done! Your score for Nail Trimming:', 'qresult-nails-bot':'Last activity coming up! You can do it!',
        'clothes-ben-title':'Benefits of Getting Dressed Properly', clb1:'Protection from weather', clb2:'Independence', clb3:'Confidence', clb4:'Ready for school', clb5:'Great looks',
        'clothes-steps-heading':'Steps for Getting Dressed',
        'clothes-s1-title':'Choose Clean Clothes','clothes-s1-desc':'Before dressing, make sure the clothes are clean and appropriate for the weather — hot or cold. Don\'t wear yesterday\'s dirty clothes to stay healthy and fresh!',
        'clothes-s2-title':'Put It on the Right Way','clothes-s2-desc':'Check the label or tag inside the clothing to know which is the front and which is the back. Most clothes have a logo or design on the front to help you remember!',
        'clothes-s3-title':'Underwear First','clothes-s3-desc':'Always put on underwear first — like briefs, panties, and undershirts — before other clothing. Underwear provides comfort and protection for your body.',
        'clothes-s4-title':'Use Buttons & Zippers Properly','clothes-s4-desc':'Learn to use buttons — start from the bottom button and work upward. For zippers, hold the bottom and slowly zip up. Don\'t rush!',
        'clothes-s5-title':'Tidy Up & Check in the Mirror','clothes-s5-desc':'After dressing, adjust your clothes — nothing sticking out, nothing folded wrong, collar is straight. Check in the mirror to make sure you\'re ready and looking great!',
        'clothes-p1':'Clean Clothes','clothes-p2':'Right Direction','clothes-p3':'Underwear First','clothes-p4':'Buttons & Zipper','clothes-p5':'Check in Mirror',
        mqBadge6:'MINI QUIZ', mqTitle6:'Getting Dressed — Test Yourself!',
        'qresult-clothes-top':'All activities done! Your score for Getting Dressed:', 'qresult-clothes-bot':'Try the Grand Quiz below!',
        grandQuizLabel:'GRAND QUIZ', grandQuizTitle:'Grand Quiz! Test Yourself!', grandQuizSub:'Are you ready? Answer all the questions and show how great you are!',
        tipsLabel:'TIPS', tipsTitle:'Reminders to Stay Clean',
        tip1title:'Brush for 2 Minutes', tip1text:'Brush your teeth for 2 minutes — 30 seconds for each section of your mouth. You can sing a song while brushing!',
        tip2title:'20-Second Handwashing', tip2text:'Wash your hands for at least 20 seconds with soap and water — think of "Happy Birthday" twice!',
        tip3title:'Brush Morning and Night', tip3text:'Brush your teeth when you wake up in the morning and before sleeping at night to prevent cavities!',
        tip4title:'Never Forget to Wash', tip4text:'Always wash your hands before eating, after using the bathroom, and after playing!',
        tip5title:'Weekly Nail Trimming', tip5text:'Trim your nails once a week so they don\'t collect dirt and germs under them.',
        tip6title:'Always Wear Clean Clothes', tip6text:'Wear clean clothes every day! Avoid wearing dirty clothes that can affect your health.',
        retryBtn:'Retry', lessonsBtn:'Back to Lessons', grandNextBtn:'Next',
        completionSub:'You finished the Motor Skills Grand Quiz!',
    },
    tl: {
        navBack: 'Bumalik',
        heroBadge: 'Araling Motor Skills',
        heroDesc: 'Matuto kung paano mag-aalaga ng ating katawan — paghuhugas ng kamay, pagsisipilyo, paliligo, at marami pa!',
        langBadge: '<i class="fas fa-flag"></i> Filipino',
        sectionWhatLabel: 'ANO ANG MOTOR SKILLS?',
        introTitle: 'Ano ang Motor Skills?',
        introDesc: 'Ang <strong style="color:#006978;">motor skills</strong> ay ang kakayahan ng ating katawan na gumalaw at gumawa ng mga bagay sa pamamagitan ng ating mga kamay, paa, at iba pang bahagi ng katawan. Sa araling ito, matututo tayo kung paano mag-aalaga ng sarili nating katawan — isang mahalagang bahagi ng paglaki at pag-unlad bilang isang bata!',
        importanceTitle: 'Bakit Mahalaga ang Motor Skills?',
        imp1title:'Kalusugan', imp1desc:'Ang malinis na katawan ay nagtatanggol sa atin laban sa mga sakit, mikrobyo, at impeksyon. Kapag malinis tayo, mas malusog tayo!',
        imp2title:'Kumpiyansa', imp2desc:'Ang pagiging malinis at maayos ay nagbibigay ng tiwala sa sarili sa school, sa mga kaibigan, at sa lahat ng lugar na pupuntahan natin.',
        imp3title:'Kasarinlan', imp3desc:'Matuto kang mag-alaga ng iyong sarili nang walang tulong ng iba — proud moment ito para sa iyo at sa iyong pamilya!',
        imp4title:'Magandang Gawi', imp4desc:'Ang mga gawi na natututo mo ngayon ay dadalhin mo hanggang sa paglaki mo. Ito ang pundasyon ng malusog na pamumuhay!',
        purposeTitle: 'Layunin ng Araling Ito',
        pur1title:'Matuto ng Tamang Paraan', pur1desc:'Matututo tayo ng wastong pamamaraan sa bawat gawi sa pag-aalaga ng katawan — hakbang sa hakbang para madaling sundin.',
        pur2title:'Ugaliing Gawin Ito', pur2desc:'Ang layunin ay gawing regular na gawi ang wastong kalinisan — hindi lang isang beses, kundi araw-araw na routine!',
        pur3title:'Maiwasan ang Sakit', pur3desc:'Sa pamamagitan ng tamang kalinisan, maiiwasan natin ang mga karaniwang sakit tulad ng sipon, trangkaso, at impeksyon sa ngipin.',
        pur4title:'Lumago at Umunlad', pur4desc:'Ang bawat hakbang na matutunan mo sa araling ito ay tumutulong sa iyong paglago bilang isang malusog at masayang bata!',
        actLabels: ['GAWAIN 1','GAWAIN 2','GAWAIN 3','GAWAIN 4','GAWAIN 5','GAWAIN 6'],
        act1of6:'Gawain 1 ng 6', act1title:'Paghuhugas ng Kamay', act1desc:'Ang paghuhugas ng kamay ang pinaka-simpleng paraan para maiwasan ang pagkalat ng mga mikrobyo at sakit. Alamin ang tamang paraan!',
        act2of6:'Gawain 2 ng 6', act2title:'Pagsisipilyo ng Ngipin', act2desc:'Ang malusog na ngipin ay simula ng malusog na katawan! Alamin ang tamang paraan ng pagsisipilyo para laging malinis ang iyong ngiti.',
        act3of6:'Gawain 3 ng 6', act3title:'Tamang Paliligo', act3desc:'Ang araw-araw na paliligo ay nagpapaalis ng dumi, pawis, at amoy mula sa ating katawan. Matutunan natin ang tamang paraan!',
        act4of6:'Gawain 4 ng 6', act4title:'Pagsusuklay ng Buhok', act4desc:'Ang maayos na buhok ay nagpapakita ng tamang pag-aalaga sa sarili. Alamin kung paano suklayin ang buhok nang hindi masakit!',
        act5of6:'Gawain 5 ng 6', act5title:'Paggupit ng Kuko', act5desc:'Ang maikling at malinis na kuko ay nagpipigil sa pagtitipon ng dumi at mikrobyo. Alamin ang tamang paraan ng paggupit ng kuko!',
        act6of6:'Gawain 6 ng 6', act6title:'Pagbibihis ng Damit', act6desc:'Ang pagbibihis nang mag-isa ay isang malaking hakbang tungo sa kasarinlan! Matuto ng tamang pagkakasunod-sunod ng pagsusuot ng damit.',
        'wash-ben-title':'Mga Benepisyo ng Paghuhugas ng Kamay', wb1:'Napapatay ang mga mikrobyo', wb2:'Naiiwasan ang sakit', wb3:'Ligtas kumain', wb4:'Proteksyon sa pamilya', wb5:'Malusog sa school',
        'wash-steps-heading':'Mga Hakbang sa Paghuhugas ng Kamay',
        'wash-s1-title':'Buksan ang Gripo', 'wash-s1-desc':'I-on ang gripo at hayaan ang tubig na dumilim. Ilagay ang iyong mga kamay sa ilalim ng tubig at basain nang mabuti — pati na ang pagitan ng mga daliri!',
        'wash-s2-title':'Mag-sabon', 'wash-s2-desc':'Kumuha ng sabon at lagyan ang iyong mga kamay. Siguraduhing may sapat na sabon sa magkabilang kamay — pati na ang iyong mga palad at likod ng kamay.',
        'wash-s3-title':'Kuskusin nang 20 Segundo', 'wash-s3-desc':'Kuskusin ang iyong mga kamay nang masigla sa loob ng 20 segundo — kasing haba ng pagkanta ng "Happy Birthday" nang dalawang beses! Linisin ang mga palad, likod, pagitan ng daliri, at ilalim ng kuko.',
        'wash-s4-title':'Banlawan nang Mabuti', 'wash-s4-desc':'Banlawan ang lahat ng sabon gamit ang malinis na tubig na tumatakbo. Siguraduhing walang natitira na sabon sa pagitan ng mga daliri at ilalim ng kuko.',
        'wash-s5-title':'Patuyuin ang Kamay', 'wash-s5-desc':'Gamitin ang malinis na tela o paper towel para patuyuin ang iyong mga kamay. Huwag hayaang basa-basa — ang basang kamay ay mas madaling makapag-kuha ng mikrobyo!',
        'wash-p1':'Buksan ang Gripo','wash-p2':'Mag-sabon','wash-p3':'Kuskusin 20 Segundo','wash-p4':'Banlawan','wash-p5':'Patuyuin ang Kamay',
        mqBadge1:'MINI QUIZ', mqTitle1:'Paghuhugas ng Kamay — Subukin Mo!',
        'qresult-wash-top':'Tapos na! Ang iyong score sa Paghuhugas ng Kamay:', 'qresult-wash-bot':'Handa ka na para sa susunod na gawain!',
        'brush-ben-title':'Mga Benepisyo ng Pagsisipilyo ng Ngipin', bb1:'Malusog na ngipin', bb2:'Magandang ngiti', bb3:'Malabong hininga', bb4:'Walang cavity', bb5:'Tipid sa dentista',
        'brush-steps-heading':'Mga Hakbang sa Pagsisipilyo ng Ngipin',
        'brush-s1-title':'Kumuha ng Sipilyo at Toothpaste','brush-s1-desc':'Gamitin ang sarili mong sipilyo — huwag mamahiram! Lagyan ng toothpaste ang sipilyo — kasing laki lang ng isang gisantes (pea-size). Huwag labisan!',
        'brush-s2-title':'Sipilyo ang Harap ng Ngipin','brush-s2-desc':'Isimula ang pagsisipilyo sa harapan ng mga ngipin. Gawin ang maliliit na paikot na galaw — huwag i-scrub nang malakas para hindi masaktan ang gilagid!',
        'brush-s3-title':'Sipilyo ang Loob at Likod','brush-s3-desc':'Huwag kalimutang sipilyo ang loob at likod ng mga ngipin! Dito madalas naitatago ang pagkain at bakterya. Sipilyo rin ang itaas at ibaba ng bawat ngipin.',
        'brush-s4-title':'Sipilyo ang Dila','brush-s4-desc':'Sipilyo ang iyong dila nang malumanay — mula sa likod hanggang sa harap. Dito nagtatago ang maraming bakterya na nagdudulot ng masamang amoy ng hininga!',
        'brush-s5-title':'Banlawan at Mumog','brush-s5-desc':'Kumuha ng tubig at magmumog nang 2-3 beses. Iluwa ang tubig — huwag lalunukin! Gawin ito tuwing umaga pagkagising at gabi bago matulog para sa pinaka-malinis na ngipin.',
        'brush-p1':'Sipilyo at Toothpaste','brush-p2':'Harap ng Ngipin','brush-p3':'Loob at Likod','brush-p4':'Sipilyo ang Dila','brush-p5':'Banlawan at Mumog',
        mqBadge2:'MINI QUIZ', mqTitle2:'Pagsisipilyo — Subukin Mo!',
        'qresult-brush-top':'Tapos na! Ang iyong score sa Pagsisipilyo:', 'qresult-brush-bot':'Magaling! Tara, susunod na gawain!',
        'bath-ben-title':'Mga Benepisyo ng Paliligo', bathb1:'Linisin ang katawan', bathb2:'Nakakarelax', bathb3:'Malayo sa amoy', bathb4:'Naalis ang germs', bathb5:'Magandang hitsura',
        'bath-steps-heading':'Mga Hakbang sa Tamang Paliligo',
        'bath-s1-title':'Ihanda ang Lahat ng Kailangan','bath-s1-desc':'Bago pumasok sa banyo, siguraduhing handa na ang lahat: sabon, shampoo, at malinis na tuwalya. Para hindi ka na magtayo at lumabas habang naliligo!',
        'bath-s2-title':'Basain ang Buong Katawan','bath-s2-desc':'Simulan sa pagbasain ng buong katawan gamit ang malinis na tubig — mula ulo hanggang paa. Siguraduhing nabasa ang lahat ng bahagi ng katawan.',
        'bath-s3-title':'Mag-sabon sa Buong Katawan','bath-s3-desc':'Lagyan ng sabon ang buong katawan — pati na ang kilikili, pagitan ng mga daliri ng paa, likod, at iba pang parte. Gawing masaya ang paglilinis!',
        'bath-s4-title':'Hugasan ang Buhok','bath-s4-desc':'Gamitin ang tamang halaga ng shampoo para hugasan ang buhok at antalya. Kuskusin nang malumanay ang scalp at siguraduhing lubusan ang banlawan bago tapusin.',
        'bath-s5-title':'Banlawan at Patuyuin','bath-s5-desc':'Banlawan nang mabuti ang lahat ng sabon at shampoo — walang matitira! Pagkatapos, patuyuin ang katawan gamit ang malinis na tuwalya nang maigi.',
        'bath-p1':'Ihanda ang Gamit','bath-p2':'Basain ang Katawan','bath-p3':'Mag-sabon','bath-p4':'Hugasan ang Buhok','bath-p5':'Banlawan at Patuyuin',
        mqBadge3:'MINI QUIZ', mqTitle3:'Paliligo — Subukin Mo!',
        'qresult-bath-top':'Tapos na! Ang iyong score sa Paliligo:', 'qresult-bath-bot':'Napakahusay! Sunod na!',
        'comb-ben-title':'Mga Benepisyo ng Pagsusuklay ng Buhok', cb1:'Maayos na buhok', cb2:'Malayo sa kuto', cb3:'Malusog na antalya', cb4:'Magandang hitsura', cb5:'Kumpiyansa',
        'comb-steps-heading':'Mga Hakbang sa Pagsusuklay ng Buhok',
        'comb-s1-title':'Gamitin ang Sariling Suklay','comb-s1-desc':'Palaging gumamit ng sariling suklay — huwag mamahiram ng suklay! Ang pagpapahiram ng suklay ay maaaring magpalaganap ng kuto at iba pang impeksyon sa antalya.',
        'comb-s2-title':'Magsimula sa Dulo ng Buhok','comb-s2-desc':'Ito ang sikreto para hindi masakit ang suklayin! Magsimula sa dulo ng buhok at dahan-dahang gumawa pataas. Huwag magsimula sa tuktok — masisikot lang ang buhok!',
        'comb-s3-title':'Suklayin nang Maingat','comb-s3-desc':'Suklayin ang buong buhok mula ulo hanggang dulo nang dahan-dahan at maingat. Kung may buhol, hawakan ang buhok malapit sa ugat para hindi masakit habang tinatanggal ang buhol.',
        'comb-s4-title':'Suklayin Araw-Araw','comb-s4-desc':'Gawing gawi ang pagsusuklay ng buhok tuwing umaga bago pumunta sa school at gabi bago matulog. Maayos na buhok = masayang araw!',
        'comb-p1':'Sariling Suklay','comb-p2':'Dulo ng Buhok Muna','comb-p3':'Suklayin nang Maingat','comb-p4':'Gawing Ugali',
        mqBadge4:'MINI QUIZ', mqTitle4:'Pagsusuklay — Subukin Mo!',
        'qresult-comb-top':'Tapos na! Ang iyong score sa Pagsusuklay:', 'qresult-comb-bot':'Kahanga-hanga! Isa pa!',
        'nails-ben-title':'Mga Benepisyo ng Paggupit ng Kuko', nb1:'Walang mikrobyo sa kuko', nb2:'Ligtas ang pagkain', nb3:'Magandang kamay', nb4:'Walang in-grown', nb5:'Malinis na hitsura',
        'nails-steps-heading':'Mga Hakbang sa Paggupit ng Kuko',
        'nails-s1-title':'Humingi ng Tulong sa Magulang','nails-s1-desc':'Para sa mga bata, laging humingi ng tulong sa magulang o guro bago gupitin ang kuko. Ang nail cutter ay matalim — mahalaga ang pangangalaga para maiwasan ang aksidente!',
        'nails-s2-title':'Gamitin ang Tamang Kagamitan','nails-s2-desc':'Gumamit ng tamang nail cutter para sa bata — may espesyal na nail cutter para sa maliit na kuko ng mga bata. Mas ligtas at mas madali gamitin kaysa sa gunting.',
        'nails-s3-title':'Gupitin nang Tuwid','nails-s3-desc':'Gupitin ang kuko nang tuwid — hindi masyadong maikli at hindi masyadong mahaba. Ang tamang haba ay kasing layo ng dulo ng daliri. Iwasang gupitin ang gilid para maiwasan ang in-grown na kuko.',
        'nails-s4-title':'Linisin ang Ilalim ng Kuko','nails-s4-desc':'Pagkatapos gupitin, gamitin ang maliit na brush para linisin ang ilalim ng kuko. Dito kadalasang naitatago ang dumi at mikrobyo na hindi mo nakikita!',
        'nails-s5-title':'Lingguhang Gawi','nails-s5-desc':'Gupitin ang kuko isang beses sa isang linggo para palaging malinis at angkop ang haba. Maaari kang gumawa ng schedule — tulad ng tuwing Sabado pagkatapos ng paliligo!',
        'nails-p1':'Tulong ng Magulang','nails-p2':'Nail Cutter','nails-p3':'Gupitin nang Tuwid','nails-p4':'Linisin ang Ilalim','nails-p5':'Lingguhang Gawi',
        mqBadge5:'MINI QUIZ', mqTitle5:'Paggupit ng Kuko — Subukin Mo!',
        'qresult-nails-top':'Tapos na! Ang iyong score sa Paggupit ng Kuko:', 'qresult-nails-bot':'Huling gawain na! Kaya mo ito!',
        'clothes-ben-title':'Mga Benepisyo ng Wastong Pagbibihis', clb1:'Proteksyon sa klima', clb2:'Kasarinlan', clb3:'Kumpiyansa', clb4:'Handa sa school', clb5:'Magandang hitsura',
        'clothes-steps-heading':'Mga Hakbang sa Pagbibihis ng Damit',
        'clothes-s1-title':'Piliin ang Malinis na Damit','clothes-s1-desc':'Bago magbihis, siguraduhing malinis ang damit at angkop sa panahon — mainit man o malamig. Huwag muling isuot ang maruming damit mula kahapon para malusog at mabango ka!',
        'clothes-s2-title':'Ilagay sa Tamang Direksyon','clothes-s2-desc':'Tingnan ang label o tanda sa loob ng damit para malaman kung alin ang harap at alin ang likod. Karamihan ng damit ay may logo o disenyo sa harap para matandaan!',
        'clothes-s3-title':'Isuot ang Panloob na Damit Muna','clothes-s3-desc':'Laging isuot muna ang panloob na damit tulad ng brief, panty, at sando bago ang ibang damit. Ang panloob na damit ay nagbibigay ng komportable at proteksyon sa iyong katawan.',
        'clothes-s4-title':'Tamang Paggamit ng Butones at Zipper','clothes-s4-desc':'Matuto ng tamang paggamit ng butones — magsimula sa pinakamababang butones pataas. Para sa zipper, hawakan ang ibaba at dahan-dahang i-zip pataas. Huwag magmadali!',
        'clothes-s5-title':'I-ayos at Tingnan sa Salamin','clothes-s5-desc':'Pagkatapos magbihis, ayusin ang damit — walang nakalabas, walang naka-tiklop, tuwid ang collar. Tingnan sa salamin para masigurado na handa ka na at maganda ang hitsura!',
        'clothes-p1':'Malinis na Damit','clothes-p2':'Tamang Direksyon','clothes-p3':'Panloob Muna','clothes-p4':'Butones at Zipper','clothes-p5':'Tingnan sa Salamin',
        mqBadge6:'MINI QUIZ', mqTitle6:'Pagbibihis — Subukin Mo!',
        'qresult-clothes-top':'Tapos na ang lahat ng gawain! Ang iyong score sa Pagbibihis:', 'qresult-clothes-bot':'Subukan ang Grand Quiz sa ibaba!',
        grandQuizLabel:'GRAND QUIZ', grandQuizTitle:'Grand Quiz! Subukin Mo!', grandQuizSub:'Handa ka na ba? Sagutan ang lahat ng tanong at ipakita kung gaano ka kagaling!',
        tipsLabel:'MGA TIPS', tipsTitle:'Mga Paalala para Laging Malinis',
        tip1title:'Mag-sipilyo ng 2 Minuto', tip1text:'Magsipilyo ng 2 minuto — 30 segundo para sa bawat bahagi ng bibig. Maaari kang mag-awit habang nagsisipilyo!',
        tip2title:'20 Segundo ang Paghuhugas', tip2text:'Hugasan ang kamay nang hindi bababa sa 20 segundo gamit ang sabon at tubig — isipin ang "Happy Birthday" dalawang beses!',
        tip3title:'Magsipilyo Umaga at Gabi', tip3text:'Magsipilyo ng ngipin pagkagising sa umaga at bago matulog sa gabi para maiwasan ang cavities!',
        tip4title:'Huwag Kalimutang Maghugas', tip4text:'Palaging maghugas ng kamay bago kumain, pagkatapos gumamit ng CR, at pagkatapos maglaro!',
        tip5title:'Lingguhang Paggupit ng Kuko', tip5text:'Gupitin ang mga kuko isang beses sa isang linggo para hindi ito tumatagal ng dumi at mikrobyo.',
        tip6title:'Palaging Malinis ang Damit', tip6text:'Magbihis ng malinis na damit araw-araw! Iwasan ang pagsusuot ng maruming damit na nakakaapekto sa kalusugan.',
        retryBtn:'Ulitin', lessonsBtn:'Bumalik sa Lessons', grandNextBtn:'Susunod',
        completionSub:'Natapos mo ang Motor Skills Grand Quiz!',
    }
};

/* ══════════════════════════════════════════════════════════
   LANGUAGE SWITCH
══════════════════════════════════════════════════════════ */
let currentLang = 'en';

function setLang(lang) {
    if (lang === currentLang) return;
    currentLang = lang;
    document.getElementById('btnEN').classList.toggle('active', lang === 'en');
    document.getElementById('btnTL').classList.toggle('active', lang === 'tl');
    applyLang(lang);
    // Re-init all mini quizzes in new lang
    ['wash','brush','bath','comb','nails','clothes'].forEach(k => initMiniQuiz(k));
    // Re-init grand quiz
    currentQ = 0; score = 0; answered = false;
    document.getElementById('completionCard').style.display = 'none';
    document.getElementById('questionCard').style.display = 'block';
    document.getElementById('stepTracker').style.display = 'flex';
    document.getElementById('scoreBadge').style.display = 'inline-flex';
    buildTracker();
    renderQuestion();
}

function applyLang(lang) {
    const u = UI[lang];
    const isTL = lang === 'tl';
    // Helper
    const set = (id, val) => { const el = document.getElementById(id); if (el) el.innerHTML = val; };
    const setText = (id, val) => { const el = document.getElementById(id); if (el) el.textContent = val; };

    setText('navBack', u.navBack);
    setText('heroBadge', u.heroBadge);
    setText('heroDesc', u.heroDesc);
    const badge = document.getElementById('langBadge');
    if (badge) { badge.innerHTML = u.langBadge; badge.classList.toggle('tl-mode', isTL); }
    setText('sectionWhatLabel', (isTL ? 'ANO ANG MOTOR SKILLS?' : 'WHAT ARE MOTOR SKILLS?'));
    setText('introTitle', u.introTitle);
    set('introDesc', u.introDesc);
    setText('importanceTitle', u.importanceTitle);
    ['1','2','3','4'].forEach(n => { setText(`imp${n}title`, u[`imp${n}title`]); setText(`imp${n}desc`, u[`imp${n}desc`]); });
    setText('purposeTitle', u.purposeTitle);
    ['1','2','3','4'].forEach(n => { setText(`pur${n}title`, u[`pur${n}title`]); setText(`pur${n}desc`, u[`pur${n}desc`]); });

    // Activity labels & headers
    ['1','2','3','4','5','6'].forEach(n => {
        setText(`act${n}of6`, u[`act${n}of6`]);
        setText(`act${n}title`, u[`act${n}title`]);
        setText(`act${n}desc`, u[`act${n}desc`]);
    });
    const actLabelEls = document.querySelectorAll('[class^="act-label-"]');
    actLabelEls.forEach((el, i) => { el.textContent = u.actLabels[i]; });

    // All text IDs - benefits, steps, placeholders, quiz text
    const textIds = [
        'wash-ben-title','wb1','wb2','wb3','wb4','wb5','wash-steps-heading',
        'wash-s1-title','wash-s1-desc','wash-s2-title','wash-s2-desc','wash-s3-title','wash-s3-desc',
        'wash-s4-title','wash-s4-desc','wash-s5-title','wash-s5-desc',
        'wash-p1','wash-p2','wash-p3','wash-p4','wash-p5',
        'mqBadge1','mqTitle1','qresult-wash-top','qresult-wash-bot',
        'brush-ben-title','bb1','bb2','bb3','bb4','bb5','brush-steps-heading',
        'brush-s1-title','brush-s1-desc','brush-s2-title','brush-s2-desc','brush-s3-title','brush-s3-desc',
        'brush-s4-title','brush-s4-desc','brush-s5-title','brush-s5-desc',
        'brush-p1','brush-p2','brush-p3','brush-p4','brush-p5',
        'mqBadge2','mqTitle2','qresult-brush-top','qresult-brush-bot',
        'bath-ben-title','bathb1','bathb2','bathb3','bathb4','bathb5','bath-steps-heading',
        'bath-s1-title','bath-s1-desc','bath-s2-title','bath-s2-desc','bath-s3-title','bath-s3-desc',
        'bath-s4-title','bath-s4-desc','bath-s5-title','bath-s5-desc',
        'bath-p1','bath-p2','bath-p3','bath-p4','bath-p5',
        'mqBadge3','mqTitle3','qresult-bath-top','qresult-bath-bot',
        'comb-ben-title','cb1','cb2','cb3','cb4','cb5','comb-steps-heading',
        'comb-s1-title','comb-s1-desc','comb-s2-title','comb-s2-desc','comb-s3-title','comb-s3-desc',
        'comb-s4-title','comb-s4-desc',
        'comb-p1','comb-p2','comb-p3','comb-p4',
        'mqBadge4','mqTitle4','qresult-comb-top','qresult-comb-bot',
        'nails-ben-title','nb1','nb2','nb3','nb4','nb5','nails-steps-heading',
        'nails-s1-title','nails-s1-desc','nails-s2-title','nails-s2-desc','nails-s3-title','nails-s3-desc',
        'nails-s4-title','nails-s4-desc','nails-s5-title','nails-s5-desc',
        'nails-p1','nails-p2','nails-p3','nails-p4','nails-p5',
        'mqBadge5','mqTitle5','qresult-nails-top','qresult-nails-bot',
        'clothes-ben-title','clb1','clb2','clb3','clb4','clb5','clothes-steps-heading',
        'clothes-s1-title','clothes-s1-desc','clothes-s2-title','clothes-s2-desc','clothes-s3-title','clothes-s3-desc',
        'clothes-s4-title','clothes-s4-desc','clothes-s5-title','clothes-s5-desc',
        'clothes-p1','clothes-p2','clothes-p3','clothes-p4','clothes-p5',
        'mqBadge6','mqTitle6','qresult-clothes-top','qresult-clothes-bot',
        'grandQuizLabel','grandQuizTitle','grandQuizSub',
        'tipsLabel','tipsTitle',
        'tip1title','tip1text','tip2title','tip2text','tip3title','tip3text',
        'tip4title','tip4text','tip5title','tip5text','tip6title','tip6text',
    ];
    textIds.forEach(id => { if (u[id] !== undefined) setText(id, u[id]); });
    // Retry/lessons/next buttons
    const retryBtn = document.getElementById('retryBtn'); if (retryBtn) retryBtn.innerHTML = `<i class="fas fa-rotate-left"></i> ${u.retryBtn}`;
    const lessonsBtn = document.getElementById('lessonsBtn'); if (lessonsBtn) lessonsBtn.innerHTML = `<i class="fas fa-book-open"></i> ${u.lessonsBtn}`;
    const nextBtn = document.getElementById('nextBtn'); if (nextBtn) nextBtn.innerHTML = u.grandNextBtn + ' <i class="fas fa-arrow-right"></i>';
    setText('completionSub', u.completionSub);
}

</script>

<!-- ── TAKE QUIZ BUTTON ── -->
<style>
.quiz-btn-wrap {
    display: flex;
    justify-content: center;
    padding: 32px 16px 56px;
}
.take-quiz-btn {
    display: inline-flex;
    align-items: center;
    gap: 12px;
    background: linear-gradient(135deg, #0d5407 0%, #1a8c0a 100%);
    color: #fff;
    font-family: 'Fredoka One', cursive;
    font-size: 1.35rem;
    padding: 18px 48px;
    border-radius: 60px;
    text-decoration: none;
    box-shadow: 0 8px 28px rgba(13,84,7,0.35);
    transition: transform 0.25s cubic-bezier(.34,1.56,.64,1), box-shadow 0.25s;
    letter-spacing: 0.5px;
    border: 3px solid rgba(255,255,255,0.2);
}
.take-quiz-btn:hover {
    transform: translateY(-5px) scale(1.05);
    box-shadow: 0 16px 40px rgba(13,84,7,0.4);
    color: #fff;
}
.take-quiz-btn:active { transform: scale(0.97); }
.take-quiz-btn .quiz-icon { font-size: 1.5rem; }
</style>

<div class="quiz-btn-wrap">
    <a href="activities.php" class="take-quiz-btn">
        Take Quiz
    </a>
</div>

</body>
</html>