<?php
session_start();
require_once 'database.php';
require_once 'activity_helper.php';

if (!isset($_SESSION['student_id']) || $_SESSION['role'] !== 'student') {
    header("Location: index.php");
    exit;
}

$student_id = $_SESSION['student_id'];
$lesson_id  = isset($_GET['lesson_id']) ? (int)$_GET['lesson_id'] : 1; // Letters = 1

// ── AJAX ──
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $quizzes_id = (int)($_POST['quizzes_id'] ?? 0);
    if ($_POST['action'] === 'start') {
        quizzes_start($conn, $student_id, $quizzes_id);
        echo json_encode(['success' => true]);
        exit;
    }
    if ($_POST['action'] === 'progress') {
        $score      = (int)($_POST['score']      ?? 0);
        $checkpoint = (int)($_POST['checkpoint'] ?? 0);
        activity_save_progress($conn, $student_id, $quizzes_id, $score, $checkpoint);
        echo json_encode(['success' => true]);
        exit;
    }
    if ($_POST['action'] === 'complete') {
        $score = (int)($_POST['score'] ?? 0);
        quizzes_complete($conn, $student_id, $quizzes_id, $score);
        echo json_encode(['success' => true, 'score' => $score]);
        exit;
    }
}

// ── FETCH ACTIVITIES ──
$stmt = $conn->prepare("SELECT * FROM quizzes WHERE lesson_id = ? ORDER BY quizzes_id");
$stmt->bind_param("i", $lesson_id);
$stmt->execute();
$quizzes = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$act_map = [];
foreach ($quizzes as $act) {
    $prog = quizzes_get($conn, $student_id, $act['quizzes_id']);
    $act_map[$act['quizzes_name']] = [
        'quizzes_id' => $act['quizzes_id'],
        'status'      => $prog['status'],
        'score'       => $prog['score'],
    ];
}

$a1 = $act_map['letter_matching']    ?? ['quizzes_id'=>0,'status'=>'not_started','score'=>0];
$a2 = $act_map['spot_the_letter']    ?? ['quizzes_id'=>0,'status'=>'not_started','score'=>0];
$a3 = $act_map['fill_in_the_letter'] ?? ['quizzes_id'=>0,'status'=>'not_started','score'=>0];
$a4 = $act_map['letter_tracing']     ?? ['quizzes_id'=>0,'status'=>'not_started','score'=>0];

$active_tab = isset($_GET['tab']) ? max(1, min(4, (int)$_GET['tab'])) : 1;
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Letters Activities — E-KINDER</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Fredoka+One&family=Nunito:wght@400;600;700;800;900&display=swap" rel="stylesheet">
<style>
:root{--green-dark:#1a2e1a;--cream:#fdf8f0;--pill:999px;}
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0;}
body{font-family:'Nunito',sans-serif;background:var(--cream);min-height:100vh;overflow-x:hidden;}
body::before{content:'';position:fixed;inset:0;pointer-events:none;z-index:0;background-image:radial-gradient(circle,rgba(0,0,0,.035) 1.2px,transparent 1.2px);background-size:26px 26px;}
.page-wrap{position:relative;z-index:1;padding-bottom:80px;}

/* NAV */
.lesson-nav{height:64px;padding:0 28px;background:rgba(255,255,255,.94);backdrop-filter:blur(16px);border-bottom:1px solid rgba(0,0,0,.07);box-shadow:0 2px 18px rgba(0,0,0,.06);position:sticky;top:0;z-index:200;display:flex;align-items:center;gap:14px;}
.lnav-back{display:inline-flex;align-items:center;gap:8px;background:var(--green-dark);color:#fff;font-family:'Fredoka One',cursive;font-size:.88rem;padding:8px 20px;border-radius:var(--pill);text-decoration:none;box-shadow:0 4px 14px rgba(0,0,0,.22);transition:transform .22s cubic-bezier(.34,1.56,.64,1);flex-shrink:0;}
.lnav-back:hover{transform:scale(1.06);color:#fff;}
.lnav-title{font-family:'Fredoka One',cursive;font-size:1.1rem;color:var(--green-dark);flex:1;text-align:center;}
.lang-toggle{display:inline-flex;align-items:center;background:#f0f0f0;border-radius:var(--pill);padding:4px;border:1.5px solid #e0e0e0;}
.lang-btn{font-family:'Fredoka One',cursive;font-size:.8rem;padding:6px 18px;border-radius:var(--pill);border:none;cursor:pointer;background:transparent;color:#aaa;transition:background .2s,color .2s;}
.lang-btn.active{background:var(--green-dark);color:#fff;box-shadow:0 3px 10px rgba(0,0,0,.22);}

/* TABS */
.quizzes-tabs{display:flex;gap:8px;padding:20px 0 0;overflow-x:auto;scrollbar-width:none;}
.quizzes-tabs::-webkit-scrollbar{display:none;}
.act-tab{flex-shrink:0;display:flex;align-items:center;gap:8px;padding:10px 18px;border-radius:var(--pill);border:2.5px solid #e8e8e8;background:#fff;font-family:'Fredoka One',cursive;font-size:.82rem;color:#aaa;cursor:pointer;transition:all .22s cubic-bezier(.34,1.56,.64,1);box-shadow:0 2px 8px rgba(0,0,0,.05);}
.act-tab:hover:not(.active){border-color:#c8e6c9;color:#555;transform:translateY(-2px);}
.act-tab.active{background:var(--green-dark);border-color:var(--green-dark);color:#fff;box-shadow:0 6px 18px rgba(26,46,26,.25);}
.tab-status{width:18px;height:18px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:.6rem;}
.act-tab.active .tab-status{background:rgba(255,255,255,.2);}
.tab-status.done{background:#40c057;color:#fff;}
.tab-status.progress{background:#fcc419;color:#fff;}
.tab-status.locked{background:#e8e8e8;color:#bbb;}
.tab-score{font-size:.7rem;opacity:.7;}

/* PANELS */
.quizzes-panel{display:none;}
.quizzes-panel.active{display:block;animation:panelIn .35s ease both;}
@keyframes panelIn{from{opacity:0;transform:translateY(14px)}to{opacity:1;transform:none}}

/* PAGE HEADER */
.page-header{text-align:center;padding:28px 0 20px;}
.quizzes-badge{display:inline-flex;align-items:center;gap:8px;background:#fff;border:1.5px solid #dde8dd;border-radius:var(--pill);padding:6px 18px;font-family:'Fredoka One',cursive;font-size:.78rem;color:#2a4a2a;box-shadow:0 2px 10px rgba(0,0,0,.06);margin-bottom:14px;}
.page-title{font-family:'Fredoka One',cursive;font-size:clamp(1.8rem,4vw,2.6rem);color:var(--green-dark);margin-bottom:6px;}
.page-sub{font-size:.92rem;color:#a0a8a0;font-weight:700;}
.best-score-badge{display:inline-flex;align-items:center;gap:6px;background:#fff9db;border:1.5px solid #ffe066;border-radius:var(--pill);padding:5px 14px;font-family:'Fredoka One',cursive;font-size:.78rem;color:#e67700;margin-top:6px;}

/* SCORE BAR */
.score-bar{display:flex;align-items:center;justify-content:center;gap:24px;background:#fff;border-radius:20px;padding:16px 28px;margin-bottom:28px;box-shadow:0 4px 18px rgba(0,0,0,.06);border:1.5px solid #eee;flex-wrap:wrap;}
.score-item{text-align:center;}
.score-num{font-family:'Fredoka One',cursive;font-size:1.8rem;color:var(--green-dark);line-height:1;}
.score-label{font-size:.7rem;font-weight:800;color:#bbb;text-transform:uppercase;letter-spacing:.8px;}
.score-sep{width:1.5px;height:40px;background:#f0f0f0;}
.lives-wrap{display:flex;gap:6px;}
.life-icon{font-size:1.2rem;transition:opacity .3s;}
.life-icon.lost{opacity:.2;filter:grayscale(1);}
.progress-wrap{flex:1;min-width:160px;}
.progress-bar-outer{height:10px;background:#f0f0f0;border-radius:99px;overflow:hidden;}
.progress-bar-inner{height:100%;border-radius:99px;transition:width .4s cubic-bezier(.34,1.56,.64,1);}
.progress-label{font-family:'Fredoka One',cursive;font-size:.75rem;color:#aaa;margin-top:4px;}

/* ── quizzes 1: LETTER MATCHING ── */
.game-area{max-width:800px;margin:0 auto;}
.round-label{font-family:'Fredoka One',cursive;font-size:.82rem;color:#bbb;text-align:center;margin-bottom:20px;letter-spacing:1px;text-transform:uppercase;}
.match-container{display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:32px;}
.match-col-label{font-family:'Fredoka One',cursive;font-size:.82rem;color:#888;text-transform:uppercase;letter-spacing:1px;text-align:center;margin-bottom:12px;}
.match-col{display:flex;flex-direction:column;gap:12px;}
.match-card{background:#fff;border:3px solid #f0f0f0;border-radius:18px;padding:18px 12px;text-align:center;cursor:pointer;position:relative;box-shadow:0 4px 14px rgba(0,0,0,.06);transition:transform .25s cubic-bezier(.34,1.56,.64,1),border-color .2s,box-shadow .2s;}
.match-card:hover:not(.matched):not(.disabled){transform:translateY(-4px) scale(1.03);box-shadow:0 10px 28px rgba(0,0,0,.12);}
.match-card.selected{border-color:var(--card-color);background:color-mix(in srgb,var(--card-color) 8%,white);transform:translateY(-4px) scale(1.04);box-shadow:0 10px 28px color-mix(in srgb,var(--card-color) 30%,transparent);}
.match-card.matched{border-color:#40c057;background:#f0fdf4;cursor:default;animation:matchPop .4s cubic-bezier(.34,1.56,.64,1);}
.match-card.wrong{border-color:#ff4444;background:#fff5f5;animation:shake .4s ease;}
@keyframes matchPop{0%{transform:scale(.9)}60%{transform:scale(1.08)}100%{transform:scale(1)}}
@keyframes shake{0%,100%{transform:translateX(0)}25%{transform:translateX(-6px)}75%{transform:translateX(6px)}}
.match-letter{font-family:'Fredoka One',cursive;font-size:2.8rem;color:var(--card-color,#555);line-height:1;}
.match-check{position:absolute;top:8px;right:10px;color:#40c057;font-size:.9rem;opacity:0;transition:opacity .2s;}
.match-card.matched .match-check{opacity:1;}

/* ── quizzes 2: SPOT THE LETTER ── */
.question-card{background:#fff;border-radius:28px;padding:36px 28px;text-align:center;box-shadow:0 8px 36px rgba(0,0,0,.08);border:2px solid #f0ece4;margin-bottom:28px;max-width:560px;margin-left:auto;margin-right:auto;}
.question-prompt{font-family:'Fredoka One',cursive;font-size:1rem;color:#888;margin-bottom:12px;text-transform:uppercase;letter-spacing:1px;}
.question-target{font-family:'Fredoka One',cursive;font-size:5rem;line-height:1;color:var(--q-color,#339af0);margin-bottom:8px;animation:bounce 2s ease-in-out infinite;}
@keyframes bounce{0%,100%{transform:translateY(0) rotate(-3deg)}50%{transform:translateY(-12px) rotate(3deg)}}
.question-word{font-size:.88rem;font-weight:800;color:#bbb;letter-spacing:.6px;}
.choices-grid-4{display:grid;grid-template-columns:repeat(4,1fr);gap:14px;max-width:600px;margin:0 auto 28px;}
@media(max-width:500px){.choices-grid-4{grid-template-columns:repeat(2,1fr);}}
.choice-card{background:#fff;border:3px solid #f0f0f0;border-radius:18px;padding:20px 8px;text-align:center;cursor:pointer;box-shadow:0 4px 14px rgba(0,0,0,.06);transition:transform .25s cubic-bezier(.34,1.56,.64,1),border-color .2s;animation:cardIn .4s ease both;}
.choice-card:hover:not(.answered){transform:translateY(-6px) scale(1.06);box-shadow:0 12px 28px rgba(0,0,0,.12);}
.choice-card.selected{border-color:var(--c-color)!important;background:color-mix(in srgb,var(--c-color) 10%,white)!important;transform:translateY(-6px) scale(1.06);box-shadow:0 12px 28px rgba(0,0,0,.12);}
.choice-card.correct{border-color:#40c057!important;background:#f0fdf4!important;animation:popGreen .4s cubic-bezier(.34,1.56,.64,1);}
.choice-card.wrong{border-color:#ff4444!important;background:#fff5f5!important;animation:shake .4s ease;}
.choice-card.answered{cursor:default;}
@keyframes popGreen{0%{transform:scale(.9)}60%{transform:scale(1.1)}100%{transform:scale(1)}}
@keyframes cardIn{from{opacity:0;transform:translateY(20px) scale(.9)}to{opacity:1;transform:none}}
.choice-card:nth-child(1){animation-delay:.04s}.choice-card:nth-child(2){animation-delay:.08s}
.choice-card:nth-child(3){animation-delay:.12s}.choice-card:nth-child(4){animation-delay:.16s}
.choice-letter{font-family:'Fredoka One',cursive;font-size:2.4rem;color:var(--c-color,#555);line-height:1;}

/* ── quizzes 3: FILL IN THE LETTER ── */
.game-card{background:#fff;border-radius:28px;padding:32px 28px;box-shadow:0 8px 36px rgba(0,0,0,.08);border:2px solid #f0ece4;max-width:580px;margin:0 auto 28px;}
.word-image-wrap{width:140px;height:140px;border-radius:20px;overflow:hidden;margin:0 auto 20px;border:3px solid var(--q-color,#339af0);box-shadow:0 8px 24px rgba(0,0,0,.1);}
.word-image-wrap img{width:100%;height:100%;object-fit:cover;}
.word-image-placeholder{width:100%;height:100%;display:flex;align-items:center;justify-content:center;background:#f8f8f8;font-size:3rem;}
.word-display{display:flex;align-items:center;justify-content:center;gap:8px;margin-bottom:28px;flex-wrap:wrap;}
.letter-tile{width:54px;height:64px;border-radius:14px;display:flex;align-items:center;justify-content:center;font-family:'Fredoka One',cursive;font-size:2rem;border:3px solid #f0f0f0;background:#f8f8f8;color:#2d2d2d;transition:all .3s cubic-bezier(.34,1.56,.64,1);}
.letter-tile.blank{background:var(--q-color,#339af0);border-color:var(--q-color,#339af0);color:transparent;position:relative;animation:blankPulse 1.5s ease-in-out infinite;}
.letter-tile.blank::after{content:'?';position:absolute;inset:0;display:flex;align-items:center;justify-content:center;color:rgba(255,255,255,.7);font-family:'Fredoka One',cursive;font-size:1.8rem;}
.letter-tile.filled{background:var(--q-color,#339af0);border-color:var(--q-color,#339af0);color:#fff;animation:tilePop .35s cubic-bezier(.34,1.56,.64,1);}
.letter-tile.correct{background:#40c057;border-color:#40c057;color:#fff;animation:tilePop .35s cubic-bezier(.34,1.56,.64,1);}
.letter-tile.wrong{background:#ff4444;border-color:#ff4444;color:#fff;animation:shake .4s ease;}
@keyframes blankPulse{0%,100%{transform:scale(1)}50%{transform:scale(1.06)}}
@keyframes tilePop{0%{transform:scale(.8)}60%{transform:scale(1.12)}100%{transform:scale(1)}}
.choices-label{font-family:'Fredoka One',cursive;font-size:.78rem;color:#bbb;text-transform:uppercase;letter-spacing:1px;text-align:center;margin-bottom:14px;}
.choices-row{display:flex;flex-wrap:wrap;justify-content:center;gap:12px;margin-bottom:24px;}
.choice-btn{width:56px;height:64px;border-radius:14px;background:#fff;border:3px solid #e8e8e8;display:flex;align-items:center;justify-content:center;font-family:'Fredoka One',cursive;font-size:1.8rem;color:var(--c-color,#555);cursor:pointer;transition:all .2s cubic-bezier(.34,1.56,.64,1);box-shadow:0 4px 12px rgba(0,0,0,.07);}
.choice-btn:hover:not(.used):not(.disabled){border-color:var(--c-color);transform:translateY(-4px) scale(1.1);box-shadow:0 8px 20px rgba(0,0,0,.12);}
.choice-btn.selected{border-color:var(--c-color)!important;background:color-mix(in srgb,var(--c-color) 15%,white)!important;transform:translateY(-4px) scale(1.1);box-shadow:0 8px 20px rgba(0,0,0,.12);}
.choice-btn.used{opacity:.25;transform:scale(.9);pointer-events:none;}
.choice-btn.disabled{pointer-events:none;}

/* ── quizzes 4: LETTER TRACING ── */
.picker-section{background:#fff;border-radius:24px;padding:20px;box-shadow:0 4px 18px rgba(0,0,0,.06);border:1.5px solid #eee;margin-bottom:28px;}
.picker-label{font-family:'Fredoka One',cursive;font-size:.78rem;color:#bbb;text-transform:uppercase;letter-spacing:1px;margin-bottom:14px;display:flex;align-items:center;gap:8px;}
.picker-grid{display:flex;flex-wrap:wrap;gap:10px;justify-content:center;}
.pick-btn{width:50px;height:50px;border-radius:50%;border:2.5px solid #f0f0f0;background:#fafafa;cursor:pointer;display:flex;flex-direction:column;align-items:center;justify-content:center;font-family:'Fredoka One',cursive;transition:all .2s cubic-bezier(.34,1.56,.64,1);}
.pick-btn:hover{transform:scale(1.18) translateY(-3px);border-color:var(--pc);}
.pick-btn.active{background:var(--pc);border-color:var(--pc);transform:scale(1.22) translateY(-4px);box-shadow:0 8px 20px rgba(0,0,0,.18);}
.pick-btn.active .pb-u,.pick-btn.active .pb-l{color:#fff;}
.pb-u{font-size:1.3rem;color:var(--pc);line-height:1;}
.pb-l{font-size:.75rem;color:var(--pc);opacity:.5;line-height:1;}
.trace-section{max-width:680px;margin:0 auto;}
.trace-header{display:flex;align-items:center;justify-content:space-between;background:#fff;border-radius:20px;padding:18px 24px;margin-bottom:20px;box-shadow:0 4px 18px rgba(0,0,0,.06);border:1.5px solid #eee;flex-wrap:wrap;gap:12px;}
.trace-letter-big{font-family:'Fredoka One',cursive;font-size:4rem;color:var(--tl-color,#339af0);line-height:1;}
.trace-word{font-family:'Fredoka One',cursive;font-size:1.3rem;color:#2a2a2a;}
.trace-word-sub{font-size:.78rem;color:#bbb;font-weight:700;}
.trace-mode-btns{display:flex;gap:8px;}
.mode-btn{font-family:'Fredoka One',cursive;font-size:.82rem;padding:8px 16px;border-radius:var(--pill);border:2px solid #e0e0e0;background:#fff;color:#aaa;cursor:pointer;transition:all .2s;}
.mode-btn.active{background:var(--tl-color,#339af0);border-color:var(--tl-color,#339af0);color:#fff;}
.canvas-wrap{position:relative;background:#fff;border-radius:20px;box-shadow:0 6px 28px rgba(0,0,0,.08);border:2px solid #f0ece4;margin-bottom:20px;overflow:hidden;cursor:crosshair;}
canvas{display:block;width:100%;touch-action:none;}
.canvas-guide-letter{position:absolute;inset:0;display:flex;align-items:center;justify-content:center;font-family:'Fredoka One',cursive;pointer-events:none;color:var(--tl-color,#339af0);opacity:.12;user-select:none;}
.line-overlay{position:absolute;inset:0;pointer-events:none;}
.line-overlay svg{width:100%;height:100%;}
.trace-controls{display:flex;justify-content:center;align-items:center;gap:12px;flex-wrap:wrap;}
.brush-row{display:flex;align-items:center;justify-content:center;gap:10px;margin-top:14px;}
.brush-label{font-family:'Fredoka One',cursive;font-size:.75rem;color:#bbb;text-transform:uppercase;letter-spacing:.8px;}
.brush-btn{border:none;cursor:pointer;border-radius:50%;background:#e8e8e8;display:flex;align-items:center;justify-content:center;transition:all .2s;}
.brush-btn.active{background:var(--tl-color,#339af0);}
.brush-dot{border-radius:50%;background:#888;}
.brush-btn.active .brush-dot{background:#fff;}
.stars-row{display:flex;justify-content:center;gap:6px;margin-top:16px;}
.star{font-size:1.6rem;opacity:.25;transition:opacity .3s,transform .3s;}
.star.lit{opacity:1;transform:scale(1.2);animation:starPop .4s cubic-bezier(.34,1.56,.64,1);}
@keyframes starPop{0%{transform:scale(.5)}60%{transform:scale(1.4)}100%{transform:scale(1.2)}}
.hint-msg{text-align:center;font-family:'Fredoka One',cursive;font-size:.9rem;color:#bbb;margin-top:8px;min-height:22px;}
.tracing-score-info{text-align:center;margin-top:12px;font-family:'Fredoka One',cursive;font-size:.82rem;color:#bbb;}
.btn-save-trace{margin-top:12px;}

/* SHARED */
.feedback-msg{font-family:'Fredoka One',cursive;font-size:1.1rem;text-align:center;margin-bottom:16px;min-height:28px;}
.feedback-msg.correct{color:#2f9e44;}
.feedback-msg.wrong{color:#e03131;}
.controls{display:flex;justify-content:center;gap:14px;flex-wrap:wrap;}
.btn-action{font-family:'Fredoka One',cursive;font-size:.95rem;padding:12px 28px;border-radius:var(--pill);border:none;cursor:pointer;display:inline-flex;align-items:center;gap:8px;box-shadow:0 6px 18px rgba(0,0,0,.14);transition:transform .2s;}
.btn-action:hover{transform:scale(1.06);}
.btn-primary{background:var(--green-dark);color:#fff;}
.btn-secondary{background:#f0f2f0;color:#5a6a58;box-shadow:0 2px 8px rgba(0,0,0,.07);}

/* RESULT */
.result-overlay{display:none;position:fixed;inset:0;background:rgba(10,18,10,.55);backdrop-filter:blur(10px);z-index:400;align-items:center;justify-content:center;padding:20px;}
.result-overlay.active{display:flex;}
.result-box{background:#fff;border-radius:28px;padding:40px 36px;text-align:center;max-width:380px;width:100%;box-shadow:0 40px 100px rgba(0,0,0,.25);animation:popIn .4s cubic-bezier(.34,1.56,.64,1);}
@keyframes popIn{from{opacity:0;transform:scale(.6) translateY(30px)}to{opacity:1;transform:none}}
.result-icon{font-size:3.5rem;margin-bottom:12px;}
.result-title{font-family:'Fredoka One',cursive;font-size:2rem;color:var(--green-dark);margin-bottom:8px;}
.result-sub{font-size:.92rem;color:#aaa;font-weight:700;margin-bottom:12px;}
.result-score{font-family:'Fredoka One',cursive;font-size:3rem;color:var(--green-dark);margin-bottom:4px;}
.result-best{font-size:.8rem;color:#bbb;font-weight:800;margin-bottom:20px;}
.saving-msg{font-size:.82rem;color:#aaa;font-weight:700;margin-bottom:16px;min-height:20px;}
.next-quizzes-btn{width:100%;margin-top:8px;background:linear-gradient(135deg,#40c057,#2f9e44);color:#fff;border:none;border-radius:var(--pill);padding:12px 24px;font-family:'Fredoka One',cursive;font-size:.95rem;cursor:pointer;box-shadow:0 6px 18px rgba(0,0,0,.15);transition:transform .2s;display:flex;align-items:center;justify-content:center;gap:8px;}
.next-quizzes-btn:hover{transform:scale(1.04);}
.cfbit{position:fixed;pointer-events:none;z-index:1000;animation:cfFall linear forwards;}
@keyframes cfFall{0%{transform:translateY(-16px) rotate(0deg);opacity:1}100%{transform:translateY(105vh) rotate(700deg);opacity:0}}
</style>
</head>
<body>
<div class="page-wrap">

<nav class="lesson-nav">
  <a href="activities.php" class="lnav-back"><i class="fas fa-arrow-left"></i> Back</a>
  <div class="lnav-title">🔤 Letters Activities</div>
  <div class="lang-toggle">
    <button class="lang-btn active" id="btnEN" onclick="setLang('en')">🇺🇸 EN</button>
    <button class="lang-btn" id="btnTL" onclick="setLang('tl')">🇵🇭 TL</button>
  </div>
</nav>

<div class="container">

  <!-- TABS -->
  <div class="quizzes-tabs" id="quizzesTabs">
    <?php
    $tabs = [
        1 => ['icon'=>'fas fa-puzzle-piece', 'en'=>'Matching',  'tl'=>'Pagtutugma', 'act'=>$a1],
        2 => ['icon'=>'fas fa-search',        'en'=>'Spot It',  'tl'=>'Hanapin',    'act'=>$a2],
        3 => ['icon'=>'fas fa-pen',            'en'=>'Fill In',  'tl'=>'Punan',      'act'=>$a3],
        4 => ['icon'=>'fas fa-paint-brush',    'en'=>'Tracing',  'tl'=>'Pagsulat',   'act'=>$a4],
    ];
    foreach ($tabs as $num => $tab):
        $status = $tab['act']['status'];
        $score  = $tab['act']['score'];
        $dot_class = match($status){ 'completed'=>'done','in_progress'=>'progress',default=>'locked'};
        $dot_icon  = match($status){ 'completed'=>'<i class="fas fa-check"></i>','in_progress'=>'<i class="fas fa-play"></i>',default=>$num};
    ?>
    <div class="act-tab <?php echo $num===$active_tab?'active':''; ?>" onclick="switchTab(<?php echo $num;?>)" id="tab<?php echo $num;?>">
      <i class="<?php echo $tab['icon'];?>"></i>
      <span class="tab-label" data-en="<?php echo $tab['en'];?>" data-tl="<?php echo $tab['tl'];?>"><?php echo $tab['en'];?></span>
      <span class="tab-status <?php echo $dot_class;?>"><?php echo $dot_icon;?></span>
      <?php if($status==='completed'):?><span class="tab-score"><?php echo $score;?>%</span><?php endif;?>
    </div>
    <?php endforeach;?>
  </div>

  <!-- ══════════════════ PANEL 1: LETTER MATCHING ══════════════════ -->
  <div class="quizzes-panel <?php echo $active_tab===1?'active':'';?>" id="panel1">
    <div class="page-header">
      <div class="quizzes-badge"><i class="fas fa-puzzle-piece"></i> Quizzes 1 of 4</div>
      <div class="page-title" id="p1Title">Letter Match!</div>
      <div class="page-sub" id="p1Sub">Match each uppercase letter to its lowercase partner!</div>
      <?php if($a1['score']>0):?><div class="best-score-badge">⭐ Best Score: <?php echo $a1['score'];?>%</div><?php endif;?>
    </div>
    <div class="score-bar">
      <div class="score-item"><div class="score-num" id="p1Score">0</div><div class="score-label">Score</div></div>
      <div class="score-sep"></div>
      <div class="score-item"><div class="score-num" id="p1Round">1</div><div class="score-label" id="p1RoundLbl">Round</div></div>
      <div class="score-sep"></div>
      <div class="progress-wrap">
        <div class="progress-bar-outer"><div class="progress-bar-inner" id="p1Bar" style="width:0%;background:linear-gradient(90deg,#40c057,#2f9e44)"></div></div>
        <div class="progress-label" id="p1BarLbl">0 / 4 matched</div>
      </div>
    </div>
    <div class="game-area">
      <div class="round-label" id="p1RoundInfo">Round 1 of 5 — Match 4 pairs!</div>
      <div class="match-container">
        <div><div class="match-col-label" id="p1ColUpper">Uppercase</div><div class="match-col" id="p1UpperCol"></div></div>
        <div><div class="match-col-label" id="p1ColLower">Lowercase</div><div class="match-col" id="p1LowerCol"></div></div>
      </div>
      <div class="feedback-msg" id="p1Feedback"></div>
      <div class="controls">
        <button class="btn-action btn-primary" id="p1SubmitBtn" onclick="lm_submitRound()"><i class="fas fa-check"></i> <span id="p1SubmitLbl">Submit Round</span></button>
        <button class="btn-action btn-secondary" id="p1NextBtn" onclick="lm_proceedNext()" style="display:none"><i class="fas fa-arrow-right"></i> <span id="p1NextLbl">Next Round</span></button>
      </div>
    </div>
  </div>

  <!-- ══════════════════ PANEL 2: SPOT THE LETTER ══════════════════ -->
  <div class="quizzes-panel <?php echo $active_tab===2?'active':'';?>" id="panel2">
    <div class="page-header">
      <div class="quizzes-badge"><i class="fas fa-search"></i> Quizzes 2 of 4</div>
      <div class="page-title" id="p2Title">Spot the Letter!</div>
      <div class="page-sub" id="p2Sub">Find the matching letter from the choices below</div>
      <?php if($a2['score']>0):?><div class="best-score-badge">⭐ Best Score: <?php echo $a2['score'];?>%</div><?php endif;?>
    </div>
    <div class="score-bar">
      <div class="score-item"><div class="score-num" id="p2Score">0</div><div class="score-label">Score</div></div>
      <div class="score-sep"></div>
      <div class="score-item"><div class="score-num" id="p2QNum">1</div><div class="score-label">Question</div></div>
      <div class="score-sep"></div>
      <div class="score-item">
        <div class="lives-wrap" id="p2Lives"><span class="life-icon">❤️</span><span class="life-icon">❤️</span><span class="life-icon">❤️</span></div>
        <div class="score-label">Lives</div>
      </div>
    </div>
    <div class="question-card">
      <div class="question-prompt" id="p2Prompt">Find this letter:</div>
      <div class="question-target" id="p2Target">A</div>
      <div class="question-word" id="p2Word"></div>
    </div>
    <div class="feedback-msg" id="p2Feedback"></div>
    <div class="choices-grid-4" id="p2Choices"></div>
    <div class="controls">
      <button class="btn-action btn-primary" id="p2SubmitBtn" onclick="sl_submitAnswer()" style="display:none">
        <i class="fas fa-check"></i> <span id="p2SubmitLbl">Submit</span>
      </button>
      <button class="btn-action btn-secondary" id="p2NextBtn" onclick="sl_nextQuestion()" style="display:none">
        <i class="fas fa-arrow-right"></i> <span id="p2NextLbl">Next</span>
      </button>
    </div>
  </div>

  <!-- ══════════════════ PANEL 3: FILL IN THE LETTER ══════════════════ -->
  <div class="quizzes-panel <?php echo $active_tab===3?'active':'';?>" id="panel3">
    <div class="page-header">
      <div class="quizzes-badge"><i class="fas fa-pen"></i> Quizzes 3 of 4</div>
      <div class="page-title" id="p3Title">Fill in the Letter!</div>
      <div class="page-sub" id="p3Sub">Choose the missing letter to complete the word</div>
      <?php if($a3['score']>0):?><div class="best-score-badge">⭐ Best Score: <?php echo $a3['score'];?>%</div><?php endif;?>
    </div>
    <div class="score-bar">
      <div class="score-item"><div class="score-num" id="p3Score">0</div><div class="score-label">Score</div></div>
      <div class="score-sep"></div>
      <div class="score-item"><div class="score-num" id="p3QNum">1</div><div class="score-label">Question</div></div>
      <div class="score-sep"></div>
      <div class="progress-wrap">
        <div class="progress-bar-outer"><div class="progress-bar-inner" id="p3Bar" style="width:0%;background:linear-gradient(90deg,#339af0,#1971c2)"></div></div>
        <div class="progress-label" id="p3BarLbl">0 / 10</div>
      </div>
    </div>
    <div class="game-card">
      <div class="word-image-wrap" id="p3ImgWrap">
        <img id="p3Img" src="" alt="" onerror="this.closest('.word-image-wrap').innerHTML='<div class=\'word-image-placeholder\'>🖼️</div>'">
      </div>
      <div class="word-display" id="p3WordDisplay"></div>
      <div class="choices-label" id="p3ChoicesLabel">Pick the missing letter:</div>
      <div class="choices-row" id="p3ChoicesRow"></div>
      <div class="feedback-msg" id="p3Feedback"></div>
      <div class="controls">
        <button class="btn-action btn-primary" id="p3SubmitBtn" onclick="fi_submitAnswer()" style="display:none">
          <i class="fas fa-check"></i> <span id="p3SubmitLbl">Submit</span>
        </button>
        <button class="btn-action btn-secondary" id="p3NextBtn" onclick="fi_nextQuestion()" style="display:none">
          <i class="fas fa-arrow-right"></i> <span id="p3NextLbl">Next</span>
        </button>
      </div>
    </div>
  </div>

  <!-- ══════════════════ PANEL 4: LETTER TRACING ══════════════════ -->
  <div class="quizzes-panel <?php echo $active_tab===4?'active':'';?>" id="panel4">
    <div class="page-header">
      <div class="quizzes-badge"><i class="fas fa-paint-brush"></i> Quizzes 4 of 4</div>
      <div class="page-title" id="p4Title">Letter Tracing!</div>
      <div class="page-sub" id="p4Sub">Trace the letter with your finger or mouse</div>
      <?php if($a4['score']>0):?><div class="best-score-badge">⭐ Best Score: <?php echo $a4['score'];?>%</div><?php endif;?>
    </div>
    <div class="picker-section">
      <div class="picker-label"><i class="fas fa-hand-pointer"></i> <span id="p4PickerLabel">Choose a letter to trace</span></div>
      <div class="picker-grid" id="p4PickerGrid"></div>
    </div>
    <div class="trace-section">
      <div class="trace-header">
        <div><div class="trace-letter-big" id="p4TraceLetter">A</div></div>
        <div>
          <div class="trace-word" id="p4TraceWord">Apple</div>
          <div class="trace-word-sub" id="p4TraceWordSub">Trace the letter above!</div>
        </div>
        <div class="trace-mode-btns">
          <button class="mode-btn active" id="p4ModeUpper" onclick="tr_setMode('upper')"><span id="p4ModeUpperLbl">Capital</span></button>
          <button class="mode-btn" id="p4ModeLower" onclick="tr_setMode('lower')"><span id="p4ModeLowerLbl">Small</span></button>
        </div>
      </div>
      <div class="canvas-wrap" id="p4CanvasWrap">
        <div class="canvas-guide-letter" id="p4GuideLetter" style="font-size:min(50vw,280px)">A</div>
        <div class="line-overlay"><svg id="p4LinesSvg" viewBox="0 0 600 320" preserveAspectRatio="none"></svg></div>
        <canvas id="p4Canvas" height="320"></canvas>
      </div>
      <div class="trace-controls">
        <button class="btn-action btn-primary" onclick="tr_clearCanvas()" style="background:#339af0">
          <i class="fas fa-eraser"></i> <span id="p4ClearLbl">Clear</span>
        </button>
        <button class="btn-action btn-secondary" onclick="tr_speakLetter()">
          <i class="fas fa-volume-up"></i> <span id="p4SpeakLbl">Hear it</span>
        </button>
        <button class="btn-action btn-primary btn-save-trace" onclick="tr_saveScore()">
          <i class="fas fa-save"></i> <span id="p4SaveLbl">Save Progress</span>
        </button>
      </div>
      <div class="brush-row">
        <span class="brush-label" id="p4BrushLabel">Brush size:</span>
        <button class="brush-btn active" id="p4brush-sm" onclick="tr_setBrush(8,'sm')" style="width:32px;height:32px;"><div class="brush-dot" style="width:8px;height:8px;"></div></button>
        <button class="brush-btn" id="p4brush-md" onclick="tr_setBrush(16,'md')" style="width:38px;height:38px;"><div class="brush-dot" style="width:14px;height:14px;"></div></button>
        <button class="brush-btn" id="p4brush-lg" onclick="tr_setBrush(26,'lg')" style="width:46px;height:46px;"><div class="brush-dot" style="width:22px;height:22px;"></div></button>
      </div>
      <div class="stars-row" id="p4StarsRow">
        <span class="star">⭐</span><span class="star">⭐</span><span class="star">⭐</span>
      </div>
      <div class="hint-msg" id="p4HintMsg"></div>
      <div class="tracing-score-info" id="p4ScoreInfo"></div>
    </div>
  </div>

</div><!-- /container -->

<!-- RESULT OVERLAY -->
<div class="result-overlay" id="resultOverlay">
  <div class="result-box">
    <div class="result-icon" id="resultIcon">🎉</div>
    <div class="result-title" id="resultTitle">Amazing!</div>
    <div class="result-sub" id="resultSub">Great job!</div>
    <div class="result-score" id="resultScore">0%</div>
    <div class="result-best" id="resultBest"></div>
    <div class="saving-msg" id="savingMsg"></div>
    <div class="controls" style="justify-content:center;flex-direction:column;gap:8px;">
      <div style="display:flex;gap:8px;justify-content:center;">
        <button class="btn-action btn-primary" onclick="playAgain()"><i class="fas fa-redo"></i> Play Again</button>
        <button class="btn-action btn-secondary" onclick="closeResult()"><i class="fas fa-times"></i> Close</button>
      </div>
      <button class="next-quizzes-btn" id="nextActBtn" style="display:none" onclick="goNextQuizzes()">
        <i class="fas fa-arrow-right"></i> Next Quizzes
      </button>
    </div>
  </div>
</div>

<script>
// ── PHP DATA ──
const ACT_IDS   = {1:<?php echo(int)$a1['quizzes_id'];?>,2:<?php echo(int)$a2['quizzes_id'];?>,3:<?php echo(int)$a3['quizzes_id'];?>,4:<?php echo(int)$a4['quizzes_id'];?>};
const PREV_SCORES={1:<?php echo(int)$a1['score'];?>,2:<?php echo(int)$a2['score'];?>,3:<?php echo(int)$a3['score'];?>,4:<?php echo(int)$a4['score'];?>};
const CURRENT_URL = window.location.href.split('?')[0];
let lang='en', activeTab=<?php echo(int)$active_tab;?>;

const COLORS=['#f06595','#339af0','#e8a000','#40c057','#7950f2','#ff6b2b','#0db9c4','#e03131','#845EC2','#FF8E53'];
const ALPHABET_EN='ABCDEFGHIJKLMNOPQRSTUVWXYZ'.split('');
const ALPHABET_TL=['A','B','D','E','G','H','I','K','L','M','N','Ng','O','P','R','S','T','U','W','Y'];
const WORDS_EN={A:'Apple',B:'Banana',C:'Cat',D:'Dog',E:'Elephant',F:'Frog',G:'Giraffe',H:'Horse',I:'Ice Cream',J:'Jellyfish',K:'Kangaroo',L:'Lion',M:'Monkey',N:'Nest',O:'Octopus',P:'Parrot',Q:'Quail',R:'Rabbit',S:'Sunflower',T:'Tiger',U:'Umbrella',V:'Violin',W:'Whale',X:'Xylophone',Y:'Yak',Z:'Zebra'};
const WORDS_TL={A:'Aso',B:'Bahay',D:'Daga',E:'Elepante',G:'Gatas',H:'Hangin',I:'Ibon',K:'Kambing',L:'Langit',M:'Mangga',N:'Niyog',Ng:'Ngiti',O:'Oso',P:'Pato',R:'Relo',S:'Saging',T:'Tasa',U:'Uod',W:'Watawat',Y:'Yelo'};

function getAlphabet(){ return lang==='en'?[...ALPHABET_EN]:[...ALPHABET_TL]; }
function getWords(){ return lang==='en'?{...WORDS_EN}:{...WORDS_TL}; }

// ── DB SAVE ──
async function saveStart(actId){
  try{await fetch(CURRENT_URL,{method:'POST',headers:{'Content-Type':'application/x-www-form-urlencoded'},body:`action=start&quizzes_id=${actId}`});}catch(e){}
}
function saveProgress(actId, score, checkpoint=0) {
  if(score<=0)return;
  const id=actId;
  if(navigator.sendBeacon){
    const fd=new FormData();
    fd.append('action','progress');fd.append('quizzes_id',id);
    fd.append('score',score);fd.append('checkpoint',checkpoint);
    navigator.sendBeacon(CURRENT_URL,fd);
  } else {
    fetch(CURRENT_URL,{method:'POST',headers:{'Content-Type':'application/x-www-form-urlencoded'},body:`action=progress&quizzes_id=${id}&score=${score}&checkpoint=${checkpoint}`,keepalive:true}).catch(()=>{});
  }
}
async function saveComplete(actId,score){
  document.getElementById('savingMsg').textContent='💾 Saving your score...';
  try{
    const res=await fetch(CURRENT_URL,{method:'POST',headers:{'Content-Type':'application/x-www-form-urlencoded'},body:`action=complete&quizzes_id=${actId}&score=${score}`});
    const d=await res.json();
    if(d.success)document.getElementById('savingMsg').textContent='✅ Score saved!';
  }catch(e){document.getElementById('savingMsg').textContent='';}
}

// ── LANG ──
function setLang(l){
  if(l===lang)return; lang=l;
  document.getElementById('btnEN').classList.toggle('active',l==='en');
  document.getElementById('btnTL').classList.toggle('active',l==='tl');
  document.querySelectorAll('.tab-label').forEach(el=>el.textContent=el.dataset[l]);
  // Refresh Activity 1 button labels on lang switch
  if(activeTab===1){
    const c1=LM_COPY[lang];
    document.getElementById('p1SubmitLbl').textContent=c1.submitLbl;
    const isLastRound=lm_round>=LM_TOTAL_ROUNDS;
    document.getElementById('p1NextLbl').textContent=isLastRound?c1.finishLbl:c1.nextLbl;
  }
  if(activeTab===1)lm_startGame();
  if(activeTab===2)sl_startGame();
  if(activeTab===3)fi_startGame();
  if(activeTab===4)tr_buildPicker();
}

// ── TABS ──
function switchTab(num){
  activeTab=num;
  document.querySelectorAll('.act-tab').forEach((t,i)=>t.classList.toggle('active',i+1===num));
  document.querySelectorAll('.quizzes-panel').forEach((p,i)=>p.classList.toggle('active',i+1===num));
  if(num===1)lm_startGame();
  if(num===2)sl_startGame();
  if(num===3)fi_startGame();
  if(num===4){tr_buildPicker();tr_selectLetter('A',COLORS[0]);}
  window.scrollTo({top:0,behavior:'smooth'});
}

// ── RESULT ──
let _curActNum=1;
function showResult(actNum,rawScore,maxScore){
  _curActNum=actNum;
  const finalScore=Math.round((rawScore/maxScore)*100);
  const prev=PREV_SCORES[actNum];
  document.getElementById('resultScore').textContent=finalScore+'%';
  document.getElementById('resultIcon').textContent=finalScore>=80?'🏆':finalScore>=50?'🎉':'⭐';
  document.getElementById('resultBest').textContent=finalScore>prev?'🌟 New best score!':prev>0?`Previous best: ${prev}%`:'';
  document.getElementById('savingMsg').textContent='';
  document.getElementById('nextActBtn').style.display=actNum<4?'flex':'none';
  document.getElementById('resultOverlay').classList.add('active');
  confetti('#40c057');confetti('#ffd43b');
  saveProgress(ACT_IDS[actNum],finalScore);
  saveComplete(ACT_IDS[actNum],finalScore);
  PREV_SCORES[actNum]=Math.max(PREV_SCORES[actNum],finalScore);
}
function closeResult(){document.getElementById('resultOverlay').classList.remove('active');}
function playAgain(){
  closeResult();
  if(_curActNum===1)lm_startGame();
  if(_curActNum===2)sl_startGame();
  if(_curActNum===3)fi_startGame();
  if(_curActNum===4){tr_buildPicker();tr_selectLetter('A',COLORS[0]);}
}
function goNextQuizzes(){closeResult();switchTab(_curActNum+1);}

// ── UTILS ──
function shuffle(a){const r=[...a];for(let i=r.length-1;i>0;i--){const j=0|Math.random()*(i+1);[r[i],r[j]]=[r[j],r[i]];}return r;}
function speak(w,l){if(!window.speechSynthesis)return;window.speechSynthesis.cancel();const u=new SpeechSynthesisUtterance(w);u.lang=l==='tl'?'fil-PH':'en-US';u.rate=0.8;u.pitch=1.2;window.speechSynthesis.speak(u);}
function confetti(color){const cols=[color,'#FFE66D','#FF6B6B','#A29BFE','#4ECDC4','#FD79A8'];for(let i=0;i<30;i++){const p=document.createElement('div');p.className='cfbit';p.style.cssText=`left:${Math.random()*100}vw;top:-12px;background:${cols[0|Math.random()*cols.length]};border-radius:${Math.random()>.5?'50%':'3px'};width:${6+Math.random()*8}px;height:${6+Math.random()*8}px;animation-duration:${1.2+Math.random()*1.5}s;animation-delay:${Math.random()*.4}s;`;document.body.appendChild(p);p.addEventListener('animationend',()=>p.remove());}}

// ════════════════════════════════════════
// quizzes 1: LETTER MATCHING
// ════════════════════════════════════════
const LM_COPY={en:{title:'Letter Match!',sub:'Match each uppercase letter to its lowercase partner!',colUpper:'Uppercase',colLower:'Lowercase',roundInfo:r=>`Round ${r} of 5 — Match 4 pairs!`,progressLbl:(m,t)=>`${m} / ${t} matched`,submitLbl:'Submit Round',nextLbl:'Next Round',finishLbl:'See Results',feedbackCorrect:(n,pts)=>`✅ Perfect! +${pts} pts earned this round!`,feedbackPartial:(c,gained)=>`⭐ ${c}/4 correct! +${gained} pts earned.`,resultTitle:'Amazing!',resultSub:'You matched all the letters!'},tl:{title:'Letter Match!',sub:'Itugma ang bawat malaking titik sa maliit na katapat nito!',colUpper:'Malaking Titik',colLower:'Maliit na Titik',roundInfo:r=>`Round ${r} ng 5 — Itugma ang 4 pares!`,progressLbl:(m,t)=>`${m} / ${t} natugma`,submitLbl:'I-submit ang Round',nextLbl:'Susunod na Round',finishLbl:'Tingnan ang Resulta',feedbackCorrect:(n,pts)=>`✅ Perpekto! +${pts} pts nakuha sa round na ito!`,feedbackPartial:(c,gained)=>`⭐ ${c}/4 tama! +${gained} pts nakuha.`,resultTitle:'Kahanga-hanga!',resultSub:'Natugma mo ang lahat ng titik!'}};
// 5 rounds × 4 pairs × 5pts = 100 pts total
const LM_TOTAL_ROUNDS=5,LM_PPR=4,LM_PPM=5,LM_MAX=LM_TOTAL_ROUNDS*LM_PPR*LM_PPM; // 100
let lm_score=0,lm_round=0,lm_matched=0,lm_pairs={},lm_submitted=false,lm_pool=[],lm_roundLetters=[],lm_selUpper=null,lm_selLower=null;

function lm_startGame(){
  lm_score=0;lm_round=0;lm_pairs={};lm_submitted=false;lm_pool=shuffle(getAlphabet());
  document.getElementById('p1Score').textContent='0';
  document.getElementById('resultOverlay').classList.remove('active');
  const c=LM_COPY[lang];
  document.getElementById('p1Title').textContent=c.title;
  document.getElementById('p1Sub').textContent=c.sub;
  document.getElementById('p1ColUpper').textContent=c.colUpper;
  document.getElementById('p1ColLower').textContent=c.colLower;
  document.getElementById('p1SubmitLbl').textContent=c.submitLbl;
  document.getElementById('p1NextLbl').textContent=c.nextLbl;
  saveStart(ACT_IDS[1]);
  lm_nextRound();
}
function lm_nextRound(){
  lm_round++;lm_matched=0;lm_pairs={};lm_submitted=false;lm_selUpper=null;lm_selLower=null;
  if(lm_pool.length<LM_PPR)lm_pool=shuffle(getAlphabet());
  lm_roundLetters=lm_pool.splice(0,LM_PPR);
  const c=LM_COPY[lang];
  document.getElementById('p1Round').textContent=lm_round;
  document.getElementById('p1RoundInfo').textContent=c.roundInfo(lm_round);
  document.getElementById('p1Bar').style.width='0%';
  document.getElementById('p1BarLbl').textContent=c.progressLbl(0,LM_PPR);
  document.getElementById('p1Feedback').textContent='';
  document.getElementById('p1Feedback').className='feedback-msg';
  document.getElementById('p1SubmitBtn').style.display='';
  document.getElementById('p1NextBtn').style.display='none';
  const lastRound=lm_round>=LM_TOTAL_ROUNDS;
  document.getElementById('p1NextLbl').textContent=lastRound?LM_COPY[lang].finishLbl:LM_COPY[lang].nextLbl;
  lm_renderRound();
}
function lm_updateProgress(){
  const c=LM_COPY[lang];
  document.getElementById('p1Bar').style.width=((lm_matched/LM_PPR)*100)+'%';
  document.getElementById('p1BarLbl').textContent=c.progressLbl(lm_matched,LM_PPR);
}
function lm_renderRound(){
  const us=shuffle([...lm_roundLetters]),ls=shuffle([...lm_roundLetters]);
  const uc=document.getElementById('p1UpperCol');uc.innerHTML='';
  const lc=document.getElementById('p1LowerCol');lc.innerHTML='';
  us.forEach((letter,i)=>{
    const color=COLORS[i%COLORS.length];
    const card=document.createElement('div');card.className='match-card';card.dataset.letter=letter;card.dataset.side='upper';card.style.setProperty('--card-color',color);card.dataset.color=color;
    card.innerHTML=`<div class="match-letter">${letter.toUpperCase()}</div><i class="fas fa-check match-check"></i>`;
    card.onclick=()=>lm_selectCard(card,'upper');uc.appendChild(card);
  });
  ls.forEach(letter=>{
    const card=document.createElement('div');card.className='match-card';card.dataset.letter=letter;card.dataset.side='lower';card.style.setProperty('--card-color','#555');
    card.innerHTML=`<div class="match-letter" style="color:#555;font-size:2.2rem;">${letter.toLowerCase()}</div><i class="fas fa-check match-check"></i>`;
    card.onclick=()=>lm_selectCard(card,'lower');lc.appendChild(card);
  });
}
function lm_selectCard(card,side){
  if(lm_submitted)return;
  if(card.classList.contains('matched')||card.classList.contains('disabled'))return;
  if(side==='upper'){
    document.querySelectorAll('#p1UpperCol .match-card.selected').forEach(c=>c.classList.remove('selected'));
    lm_selUpper=card;card.classList.add('selected');
  } else {
    document.querySelectorAll('#p1LowerCol .match-card.selected').forEach(c=>c.classList.remove('selected'));
    lm_selLower=card;card.classList.add('selected');
  }
  if(lm_selUpper&&lm_selLower) lm_pairCards();
}
function lm_pairCards(){
  const u=lm_selUpper,l=lm_selLower;
  lm_selUpper=null;lm_selLower=null;
  // If upper was already paired, unlink old lower
  const oldPairByUpper=Object.values(lm_pairs).find(p=>p.upperCard===u);
  if(oldPairByUpper){
    oldPairByUpper.lowerCard.classList.remove('matched','selected');
    delete lm_pairs[oldPairByUpper.lowerKey];
  }
  // If lower was already paired, unlink old upper
  const lKey=l.dataset.letter;
  if(lm_pairs[lKey]){
    lm_pairs[lKey].upperCard.classList.remove('matched','selected');
    delete lm_pairs[lKey];
  }
  // Link pair
  lm_pairs[lKey]={upperCard:u,lowerCard:l,upperKey:u.dataset.letter,lowerKey:lKey};
  u.classList.remove('selected');l.classList.remove('selected');
  u.classList.add('matched');l.classList.add('matched');
  lm_matched=Object.keys(lm_pairs).length;
  lm_updateProgress();
  speak(u.dataset.letter,lang);
}
function lm_submitRound(){
  if(Object.keys(lm_pairs).length<LM_PPR){
    document.getElementById('p1Feedback').textContent='⚠️ Match all 4 pairs first before submitting!';
    document.getElementById('p1Feedback').className='feedback-msg wrong';
    return;
  }
  lm_submitted=true;
  document.getElementById('p1SubmitBtn').style.display='none';
  // Evaluate
  let correctCount=0;
  lm_roundLetters.forEach(letter=>{
    const pair=lm_pairs[letter];
    if(!pair)return;
    const isCorrect=pair.upperKey===letter;
    if(isCorrect){
      correctCount++;
      pair.upperCard.classList.add('matched');pair.lowerCard.classList.add('matched');
      const color=pair.upperCard.dataset.color||'#40c057';
      pair.upperCard.style.borderColor='#40c057';pair.upperCard.style.background='#f0fdf4';
      pair.lowerCard.style.borderColor='#40c057';pair.lowerCard.style.background='#f0fdf4';
      pair.lowerCard.querySelector('.match-letter').style.color=color;
    } else {
      pair.upperCard.classList.remove('matched');pair.lowerCard.classList.remove('matched');
      pair.upperCard.classList.add('wrong');pair.lowerCard.classList.add('wrong');
      pair.upperCard.style.borderColor='#ff4444';pair.upperCard.style.background='#fff5f5';
      pair.lowerCard.style.borderColor='#ff4444';pair.lowerCard.style.background='#fff5f5';
    }
  });
  const roundGain=correctCount*LM_PPM;
  lm_score+=roundGain;
  document.getElementById('p1Score').textContent=lm_score;
  saveProgress(ACT_IDS[1],Math.min(100,Math.round((lm_score/LM_MAX)*100)));
  const c=LM_COPY[lang];
  if(correctCount===LM_PPR){
    document.getElementById('p1Feedback').textContent=c.feedbackCorrect(LM_PPR,roundGain);
    document.getElementById('p1Feedback').className='feedback-msg correct';
    confetti('#40c057');
  } else {
    document.getElementById('p1Feedback').textContent=c.feedbackPartial(correctCount,roundGain);
    document.getElementById('p1Feedback').className=roundGain>0?'feedback-msg correct':'feedback-msg wrong';
  }
  document.getElementById('p1NextBtn').style.display='';
  const lastRound=lm_round>=LM_TOTAL_ROUNDS;
  document.getElementById('p1NextLbl').textContent=lastRound?LM_COPY[lang].finishLbl:LM_COPY[lang].nextLbl;
}
function lm_proceedNext(){
  if(lm_round>=LM_TOTAL_ROUNDS){lm_showResult();}
  else{lm_nextRound();}
}
function lm_skipRound(){if(lm_round>=LM_TOTAL_ROUNDS){lm_showResult();return;}lm_nextRound();}
function lm_showResult(){
  document.getElementById('resultTitle').textContent=LM_COPY[lang].resultTitle;
  document.getElementById('resultSub').textContent=LM_COPY[lang].resultSub;
  showResult(1,lm_score,LM_MAX);
}

// ════════════════════════════════════════
// quizzes 2: SPOT THE LETTER
// ════════════════════════════════════════
const SL_COPY={en:{title:'Spot the Letter!',sub:'Find the matching letter from the choices below',prompt:'Find this letter:',submit:'Submit',next:'Next',feedbackCorrect:l=>`✅ Yes! That's ${l}!`,feedbackWrong:l=>`❌ Oops! The answer was ${l}`,resultTitle:'Great Job!',resultSub:'You finished the quizzes!'},tl:{title:'Hanapin ang Titik!',sub:'Hanapin ang tamang titik mula sa mga pagpipilian',prompt:'Hanapin ang titik na ito:',submit:'I-submit',next:'Susunod',feedbackCorrect:l=>`✅ Tama! Iyon ay ${l}!`,feedbackWrong:l=>`❌ Mali! Ang sagot ay ${l}`,resultTitle:'Magaling!',resultSub:'Natapos mo ang aktibidad!'}};
const SL_TOTAL=10,SL_MAX=SL_TOTAL*10;
let sl_pool=[],sl_correct=null,sl_answered=false,sl_score=0,sl_qIdx=0,sl_lives=3;
let sl_selectedCard=null,sl_selectedLetter=null,sl_selectedColor=null;

function sl_startGame(){
  sl_score=0;sl_qIdx=0;sl_lives=3;sl_answered=false;sl_pool=shuffle(getAlphabet());
  sl_selectedCard=null;sl_selectedLetter=null;
  document.getElementById('p2Score').textContent='0';
  document.getElementById('resultOverlay').classList.remove('active');
  document.getElementById('p2SubmitBtn').style.display='none';
  document.getElementById('p2NextBtn').style.display='none';
  document.getElementById('p2Feedback').textContent='';
  sl_updateLives();
  const c=SL_COPY[lang];
  document.getElementById('p2Title').textContent=c.title;
  document.getElementById('p2Sub').textContent=c.sub;
  document.getElementById('p2Prompt').textContent=c.prompt;
  document.getElementById('p2SubmitLbl').textContent=c.submit;
  document.getElementById('p2NextLbl').textContent=c.next;
  saveStart(ACT_IDS[2]);
  sl_loadQuestion();
}
function sl_updateLives(){document.querySelectorAll('#p2Lives .life-icon').forEach((ic,i)=>ic.classList.toggle('lost',i>=sl_lives));}
function sl_loadQuestion(){
  sl_answered=false;sl_selectedCard=null;sl_selectedLetter=null;sl_selectedColor=null;
  document.getElementById('p2SubmitBtn').style.display='none';
  document.getElementById('p2NextBtn').style.display='none';
  document.getElementById('p2Feedback').textContent='';document.getElementById('p2Feedback').className='feedback-msg';
  const alpha=getAlphabet();const words=getWords();
  if(sl_pool.length<4)sl_pool=[...shuffle(alpha),...sl_pool];
  sl_correct=sl_pool.shift();
  const color=COLORS[Math.floor(Math.random()*COLORS.length)];
  const isUpper=Math.random()>.5;
  document.getElementById('p2QNum').textContent=sl_qIdx+1;
  document.getElementById('p2Target').textContent=isUpper?sl_correct.toUpperCase():sl_correct.toLowerCase();
  document.getElementById('p2Target').style.setProperty('--q-color',color);
  document.getElementById('p2Word').textContent=words[sl_correct]||'';
  const wrong=shuffle(alpha.filter(l=>l!==sl_correct)).slice(0,3);
  const choices=shuffle([sl_correct,...wrong]);
  const grid=document.getElementById('p2Choices');grid.innerHTML='';
  choices.forEach((letter,i)=>{
    const isCorrect=letter===sl_correct;
    const cc=COLORS[i%COLORS.length];
    const card=document.createElement('div');card.className='choice-card';card.dataset.letter=letter;card.dataset.correct=isCorrect;card.style.setProperty('--c-color',cc);
    const display=isUpper?letter.toLowerCase():letter.toUpperCase();
    card.innerHTML=`<div class="choice-letter" style="color:${cc}">${display}</div>`;
    card.onclick=()=>sl_selectAnswer(card,letter,color);
    grid.appendChild(card);
  });
}
function sl_selectAnswer(card,letter,color){
  if(sl_answered)return;
  document.querySelectorAll('#p2Choices .choice-card').forEach(c=>c.classList.remove('selected'));
  card.classList.add('selected');
  sl_selectedCard=card;sl_selectedLetter=letter;sl_selectedColor=color;
  document.getElementById('p2SubmitBtn').style.display='inline-flex';
}
function sl_submitAnswer(){
  if(!sl_selectedCard||sl_answered)return;
  sl_answered=true;
  document.getElementById('p2SubmitBtn').style.display='none';
  document.querySelectorAll('#p2Choices .choice-card').forEach(c=>c.classList.add('answered'));
  const c=SL_COPY[lang];
  if(sl_selectedLetter===sl_correct){
    sl_selectedCard.classList.add('correct');sl_selectedCard.classList.remove('selected');
    sl_score+=10; saveProgress(ACT_IDS[2],Math.round((sl_score/SL_MAX)*100));document.getElementById('p2Score').textContent=sl_score;
    document.getElementById('p2Feedback').textContent=c.feedbackCorrect(sl_correct);
    document.getElementById('p2Feedback').className='feedback-msg correct';
    confetti(sl_selectedColor);speak(sl_correct,lang);
  } else {
    sl_selectedCard.classList.add('wrong');sl_selectedCard.classList.remove('selected');
    document.querySelectorAll('#p2Choices .choice-card').forEach(cd=>{if(cd.dataset.correct==='true')cd.classList.add('correct');});
    sl_lives--;sl_updateLives();
    document.getElementById('p2Feedback').textContent=c.feedbackWrong(sl_correct);
    document.getElementById('p2Feedback').className='feedback-msg wrong';
  }
  sl_qIdx++;
  document.getElementById('p2NextBtn').style.display='inline-flex';
}
function sl_nextQuestion(){
  if(sl_qIdx>=SL_TOTAL||sl_lives<=0)sl_showResult();
  else sl_loadQuestion();
}
function sl_showResult(){
  document.getElementById('resultTitle').textContent=SL_COPY[lang].resultTitle;
  document.getElementById('resultSub').textContent=SL_COPY[lang].resultSub;
  showResult(2,sl_score,SL_MAX);
}

// ════════════════════════════════════════
// quizzes 3: FILL IN THE LETTER
// ════════════════════════════════════════
const FI_WORDS_EN=[{word:'CAT',img:'https://upload.wikimedia.org/wikipedia/commons/thumb/4/4d/Cat_November_2010-1a.jpg/240px-Cat_November_2010-1a.jpg'},{word:'DOG',img:'https://upload.wikimedia.org/wikipedia/commons/thumb/2/26/YellowLabradorLooking_new.jpg/240px-YellowLabradorLooking_new.jpg'},{word:'SUN',img:'https://upload.wikimedia.org/wikipedia/commons/thumb/4/40/Sunflower_sky_backdrop.jpg/240px-Sunflower_sky_backdrop.jpg'},{word:'BEE',img:'https://upload.wikimedia.org/wikipedia/commons/thumb/c/c2/Bee_on_white_background.jpg/240px-Bee_on_white_background.jpg'},{word:'ANT',img:'https://upload.wikimedia.org/wikipedia/commons/thumb/a/a7/Camponotus_flavomarginatus_ant.jpg/240px-Camponotus_flavomarginatus_ant.jpg'},{word:'PIG',img:'https://upload.wikimedia.org/wikipedia/commons/thumb/8/8e/Pig_at_Crossness_-_geograph.org.uk_-_1399487.jpg/240px-Pig_at_Crossness_-_geograph.org.uk_-_1399487.jpg'},{word:'OWL',img:'https://upload.wikimedia.org/wikipedia/commons/thumb/a/a5/Ornate_Hawk-Eagle.jpg/240px-Ornate_Hawk-Eagle.jpg'},{word:'EGG',img:'https://upload.wikimedia.org/wikipedia/commons/thumb/3/37/African_Bush_Elephant.jpg/240px-African_Bush_Elephant.jpg'},{word:'CUP',img:'https://upload.wikimedia.org/wikipedia/commons/thumb/4/45/A_small_cup_of_coffee.JPG/240px-A_small_cup_of_coffee.JPG'},{word:'JAM',img:'https://upload.wikimedia.org/wikipedia/commons/thumb/9/9e/Humpback_whale_underwater_shot.jpg/240px-Humpback_whale_underwater_shot.jpg'},{word:'FOX',img:'https://upload.wikimedia.org/wikipedia/commons/thumb/5/56/White_shark.jpg/240px-White_shark.jpg'},{word:'HEN',img:'https://upload.wikimedia.org/wikipedia/commons/thumb/0/0a/Cow_female_black_white.jpg/240px-Cow_female_black_white.jpg'}];
const FI_WORDS_TL=[{word:'ASO',img:'https://upload.wikimedia.org/wikipedia/commons/thumb/2/26/YellowLabradorLooking_new.jpg/240px-YellowLabradorLooking_new.jpg'},{word:'PUSA',img:'https://upload.wikimedia.org/wikipedia/commons/thumb/4/4d/Cat_November_2010-1a.jpg/240px-Cat_November_2010-1a.jpg'},{word:'IBON',img:'https://upload.wikimedia.org/wikipedia/commons/thumb/a/a7/Camponotus_flavomarginatus_ant.jpg/240px-Camponotus_flavomarginatus_ant.jpg'},{word:'ISDA',img:'https://upload.wikimedia.org/wikipedia/commons/thumb/1/10/Tursiops_truncatus_01.jpg/240px-Tursiops_truncatus_01.jpg'},{word:'BAKA',img:'https://upload.wikimedia.org/wikipedia/commons/thumb/0/0a/Cow_female_black_white.jpg/240px-Cow_female_black_white.jpg'},{word:'PATO',img:'https://upload.wikimedia.org/wikipedia/commons/thumb/a/a7/Mallard-duck.jpg/240px-Mallard-duck.jpg'},{word:'NIYOG',img:'https://upload.wikimedia.org/wikipedia/commons/thumb/f/f7/Coconut_-_single.jpg/240px-Coconut_-_single.jpg'},{word:'MANGGA',img:'https://upload.wikimedia.org/wikipedia/commons/thumb/9/90/Hapus_Mango.jpg/240px-Hapus_Mango.jpg'}];
const FI_COPY={en:{title:'Fill in the Letter!',sub:'Choose the missing letter to complete the word',choicesLabel:'Pick the missing letter:',submit:'Submit',next:'Next',feedbackCorrect:w=>`✅ Correct! The word is ${w}!`,feedbackWrong:(l,w)=>`❌ It was "${l}" — the word is ${w}`,resultTitle:'Wonderful!',resultSub:'You completed all the words!'},tl:{title:'Punan ang Titik!',sub:'Piliin ang nawawalang titik para kumpleto ang salita',choicesLabel:'Piliin ang nawawalang titik:',submit:'I-submit',next:'Susunod',feedbackCorrect:w=>`✅ Tama! Ang salita ay ${w}!`,feedbackWrong:(l,w)=>`❌ Ito ay "${l}" — ang salita ay ${w}`,resultTitle:'Napakahusay!',resultSub:'Natapos mo ang lahat ng salita!'}};
const FI_TOTAL=10,FI_MAX=FI_TOTAL*10;
let fi_pool=[],fi_current=null,fi_missingIdx=0,fi_color='#339af0',fi_score=0,fi_qIdx=0;
let fi_selectedBtn=null,fi_selectedLetter=null,fi_missingLetter=null;

function fi_startGame(){
  fi_score=0;fi_qIdx=0;
  fi_selectedBtn=null;fi_selectedLetter=null;
  fi_pool=shuffle([...(lang==='en'?FI_WORDS_EN:FI_WORDS_TL)]);
  document.getElementById('p3Score').textContent='0';
  document.getElementById('p3SubmitBtn').style.display='none';
  document.getElementById('p3NextBtn').style.display='none';
  document.getElementById('p3Feedback').textContent='';
  document.getElementById('resultOverlay').classList.remove('active');
  const c=FI_COPY[lang];
  document.getElementById('p3Title').textContent=c.title;
  document.getElementById('p3Sub').textContent=c.sub;
  document.getElementById('p3ChoicesLabel').textContent=c.choicesLabel;
  document.getElementById('p3SubmitLbl').textContent=c.submit;
  document.getElementById('p3NextLbl').textContent=c.next;
  saveStart(ACT_IDS[3]);
  fi_loadQuestion();
}
function fi_loadQuestion(){
  fi_selectedBtn=null;fi_selectedLetter=null;fi_missingLetter=null;
  document.getElementById('p3Feedback').textContent='';document.getElementById('p3Feedback').className='feedback-msg';
  document.getElementById('p3SubmitBtn').style.display='none';
  document.getElementById('p3NextBtn').style.display='none';
  if(!fi_pool.length)fi_pool=shuffle([...(lang==='en'?FI_WORDS_EN:FI_WORDS_TL)]);
  fi_current=fi_pool.shift();
  fi_color=COLORS[Math.floor(Math.random()*COLORS.length)];
  const letters=fi_current.word.split('');
  fi_missingIdx=Math.floor(Math.random()*letters.length);
  fi_missingLetter=letters[fi_missingIdx];
  document.getElementById('p3QNum').textContent=fi_qIdx+1;
  document.getElementById('p3Bar').style.width=((fi_qIdx/FI_TOTAL)*100)+'%';
  document.getElementById('p3BarLbl').textContent=`${fi_qIdx} / ${FI_TOTAL}`;
  document.getElementById('p3Img').src=fi_current.img;
  document.getElementById('p3ImgWrap').style.borderColor=fi_color;
  const display=document.getElementById('p3WordDisplay');display.innerHTML='';
  letters.forEach((l,i)=>{
    const tile=document.createElement('div');
    tile.className='letter-tile'+(i===fi_missingIdx?' blank':'');
    tile.style.setProperty('--q-color',fi_color);tile.id=`fi-tile-${i}`;
    if(i!==fi_missingIdx)tile.textContent=l;
    display.appendChild(tile);
  });
  const alpha=getAlphabet().map(l=>l.charAt(0).toUpperCase());
  const wrong=shuffle(alpha.filter(l=>l!==fi_missingLetter&&l!==fi_missingLetter.toUpperCase())).slice(0,3);
  const choices=shuffle([fi_missingLetter,...wrong]);
  const row=document.getElementById('p3ChoicesRow');row.innerHTML='';
  choices.forEach((letter,i)=>{
    const btn=document.createElement('button');btn.className='choice-btn';
    const cc=COLORS[i%COLORS.length];btn.style.setProperty('--c-color',cc);btn.textContent=letter;
    btn.onclick=()=>fi_selectLetter(btn,letter);
    row.appendChild(btn);
  });
}
function fi_selectLetter(btn,letter){
  if(btn.classList.contains('disabled'))return;
  // Deselect previous
  document.querySelectorAll('#p3ChoicesRow .choice-btn').forEach(b=>b.classList.remove('selected'));
  btn.classList.add('selected');
  fi_selectedBtn=btn;fi_selectedLetter=letter;
  // Preview in blank tile
  const tile=document.getElementById(`fi-tile-${fi_missingIdx}`);
  if(tile){tile.classList.remove('blank');tile.textContent=letter;tile.classList.add('filled');}
  document.getElementById('p3SubmitBtn').style.display='inline-flex';
}
function fi_submitAnswer(){
  if(!fi_selectedBtn||!fi_selectedLetter)return;
  document.querySelectorAll('#p3ChoicesRow .choice-btn').forEach(b=>b.classList.add('disabled'));
  const tile=document.getElementById(`fi-tile-${fi_missingIdx}`);
  const c=FI_COPY[lang];
  if(fi_selectedLetter.toUpperCase()===fi_missingLetter.toUpperCase()){
    if(tile){tile.classList.remove('filled');tile.classList.add('correct');}
    fi_selectedBtn.classList.remove('selected');
    fi_score+=10; saveProgress(ACT_IDS[3],Math.round((fi_score/FI_MAX)*100));document.getElementById('p3Score').textContent=fi_score;
    document.getElementById('p3Feedback').textContent=c.feedbackCorrect(fi_current.word);
    document.getElementById('p3Feedback').className='feedback-msg correct';
    confetti(fi_color);speak(fi_current.word,lang);
  } else {
    if(tile){tile.classList.add('wrong');setTimeout(()=>{tile.textContent=fi_missingLetter;tile.classList.remove('wrong','filled');tile.classList.add('correct');},600);}
    fi_selectedBtn.classList.remove('selected');
    document.getElementById('p3Feedback').textContent=c.feedbackWrong(fi_missingLetter,fi_current.word);
    document.getElementById('p3Feedback').className='feedback-msg wrong';
  }
  fi_qIdx++;
  document.getElementById('p3Bar').style.width=((fi_qIdx/FI_TOTAL)*100)+'%';
  document.getElementById('p3BarLbl').textContent=`${fi_qIdx} / ${FI_TOTAL}`;
  document.getElementById('p3SubmitBtn').style.display='none';
  document.getElementById('p3NextBtn').style.display='inline-flex';
}
function fi_nextQuestion(){
  if(fi_qIdx>=FI_TOTAL)fi_showResult();
  else fi_loadQuestion();
}
function fi_showResult(){
  document.getElementById('resultTitle').textContent=FI_COPY[lang].resultTitle;
  document.getElementById('resultSub').textContent=FI_COPY[lang].resultSub;
  showResult(3,fi_score,FI_MAX);
}

// ════════════════════════════════════════
// quizzes 4: LETTER TRACING
// ════════════════════════════════════════
const TR_COPY={en:{title:'Letter Tracing!',sub:'Trace the letter with your finger or mouse',pickerLabel:'Choose a letter to trace',traceWordSub:'Trace the letter above!',modeUpper:'Capital',modeLower:'Small',clear:'Clear',hear:'Hear it',brush:'Brush size:',save:'Save Progress',hintStart:"Draw the letter in the box above!",hintDrawing:"Keep going! You're doing great!",hintDone:'Wonderful tracing! Try another letter!',saveOk:'✅ Progress saved!',saveMsg:'💾 Saving...'},tl:{title:'Pagsulat ng Titik!',sub:'I-trace ang titik gamit ang iyong daliri o mouse',pickerLabel:'Pumili ng titik para isulat',traceWordSub:'I-trace ang titik sa itaas!',modeUpper:'Malaki',modeLower:'Maliit',clear:'Burahin',hear:'Marinig',brush:'Laki ng brush:',save:'I-save ang Progress',hintStart:'Isulat ang titik sa kahon sa itaas!',hintDrawing:'Magpatuloy! Magaling ka!',hintDone:'Magandang pagsulat! Subukan ang ibang titik!',saveOk:'✅ Na-save na!',saveMsg:'💾 Sine-save...'}};
let tr_currentLetter='A',tr_currentColor=COLORS[0],tr_mode='upper',tr_brushSize=8,tr_isDrawing=false,tr_strokes=0,tr_starsLit=0,tr_lettersTraced=new Set();
const tr_canvas=document.getElementById('p4Canvas');
const tr_ctx=tr_canvas.getContext('2d');

function tr_buildPicker(){
  const grid=document.getElementById('p4PickerGrid');grid.innerHTML='';
  const c=TR_COPY[lang];
  document.getElementById('p4Title').textContent=c.title;
  document.getElementById('p4Sub').textContent=c.sub;
  document.getElementById('p4PickerLabel').textContent=c.pickerLabel;
  document.getElementById('p4ModeUpperLbl').textContent=c.modeUpper;
  document.getElementById('p4ModeLowerLbl').textContent=c.modeLower;
  document.getElementById('p4ClearLbl').textContent=c.clear;
  document.getElementById('p4SpeakLbl').textContent=c.hear;
  document.getElementById('p4BrushLabel').textContent=c.brush;
  document.getElementById('p4SaveLbl').textContent=c.save;
  document.getElementById('p4HintMsg').textContent=c.hintStart;
  getAlphabet().forEach((letter,i)=>{
    const color=COLORS[i%COLORS.length];
    const btn=document.createElement('div');btn.className='pick-btn'+(letter===tr_currentLetter?' active':'');
    btn.style.setProperty('--pc',color);
    btn.innerHTML=`<span class="pb-u">${letter.toUpperCase()}</span><span class="pb-l">${letter.toLowerCase()}</span>`;
    btn.onclick=()=>tr_selectLetter(letter,color);
    grid.appendChild(btn);
  });
  saveStart(ACT_IDS[4]);
}
function tr_selectLetter(letter,color){
  tr_currentLetter=letter;tr_currentColor=color;
  document.querySelectorAll('#p4PickerGrid .pick-btn').forEach((b,i)=>{b.classList.toggle('active',getAlphabet()[i]===letter);});
  const display=tr_mode==='upper'?letter.toUpperCase():letter.toLowerCase();
  document.getElementById('p4TraceLetter').textContent=display;
  document.getElementById('p4TraceLetter').style.setProperty('--tl-color',color);
  document.getElementById('p4GuideLetter').textContent=display;
  document.getElementById('p4GuideLetter').style.setProperty('--tl-color',color);
  document.getElementById('p4TraceWord').textContent=getWords()[letter]||letter;
  document.getElementById('p4TraceWordSub').textContent=TR_COPY[lang].traceWordSub;
  document.getElementById('p4CanvasWrap').style.setProperty('--tl-color',color);
  document.getElementById('p4ModeUpper').style.setProperty('--tl-color',color);
  document.getElementById('p4ModeLower').style.setProperty('--tl-color',color);
  tr_clearCanvas();tr_resetStars();tr_buildLines();
  document.getElementById('p4HintMsg').textContent=TR_COPY[lang].hintStart;
}
function tr_setMode(m){
  tr_mode=m;
  document.getElementById('p4ModeUpper').classList.toggle('active',m==='upper');
  document.getElementById('p4ModeLower').classList.toggle('active',m==='lower');
  const display=m==='upper'?tr_currentLetter.toUpperCase():tr_currentLetter.toLowerCase();
  document.getElementById('p4TraceLetter').textContent=display;
  document.getElementById('p4GuideLetter').textContent=display;
  tr_clearCanvas();tr_resetStars();
  document.getElementById('p4HintMsg').textContent=TR_COPY[lang].hintStart;
}
function tr_setBrush(size,id){
  tr_brushSize=size;
  document.querySelectorAll('.brush-btn').forEach(b=>b.classList.remove('active'));
  document.getElementById(`p4brush-${id}`).classList.add('active');
}
function tr_buildLines(){
  const svg=document.getElementById('p4LinesSvg');
  const top=60,mid=160,base=240;
  svg.innerHTML=`<line x1="30" y1="${top}" x2="600" y2="${top}" stroke="#4a90d9" stroke-width="1.4" opacity=".5"/>
    <line x1="30" y1="${mid}" x2="600" y2="${mid}" stroke="#d94040" stroke-width="1.4" opacity=".5"/>
    <line x1="30" y1="${base}" x2="600" y2="${base}" stroke="#4a90d9" stroke-width="1.4" opacity=".5"/>
    <text x="14" y="${top+4}" font-family="Nunito,sans-serif" font-weight="900" font-size="9" fill="#4a90d9" opacity=".6" text-anchor="middle">T</text>
    <text x="14" y="${mid+4}" font-family="Nunito,sans-serif" font-weight="900" font-size="9" fill="#d94040" opacity=".6" text-anchor="middle">m</text>
    <text x="14" y="${base+4}" font-family="Nunito,sans-serif" font-weight="900" font-size="9" fill="#4a90d9" opacity=".6" text-anchor="middle">B</text>`;
}
function tr_getPos(e){
  const rect=tr_canvas.getBoundingClientRect();
  const sx=tr_canvas.width/rect.width,sy=tr_canvas.height/rect.height;
  if(e.touches)return{x:(e.touches[0].clientX-rect.left)*sx,y:(e.touches[0].clientY-rect.top)*sy};
  return{x:(e.clientX-rect.left)*sx,y:(e.clientY-rect.top)*sy};
}
tr_canvas.addEventListener('mousedown',e=>{tr_isDrawing=true;const p=tr_getPos(e);tr_ctx.beginPath();tr_ctx.moveTo(p.x,p.y);tr_strokes++;tr_checkStarProgress();document.getElementById('p4HintMsg').textContent=TR_COPY[lang].hintDrawing;});
tr_canvas.addEventListener('mousemove',e=>{if(!tr_isDrawing)return;tr_draw(tr_getPos(e));});
tr_canvas.addEventListener('mouseup',()=>{tr_isDrawing=false;tr_ctx.beginPath();});
tr_canvas.addEventListener('mouseleave',()=>{tr_isDrawing=false;tr_ctx.beginPath();});
tr_canvas.addEventListener('touchstart',e=>{e.preventDefault();tr_isDrawing=true;const p=tr_getPos(e);tr_ctx.beginPath();tr_ctx.moveTo(p.x,p.y);tr_strokes++;tr_checkStarProgress();document.getElementById('p4HintMsg').textContent=TR_COPY[lang].hintDrawing;},{passive:false});
tr_canvas.addEventListener('touchmove',e=>{e.preventDefault();if(!tr_isDrawing)return;tr_draw(tr_getPos(e));},{passive:false});
tr_canvas.addEventListener('touchend',()=>{tr_isDrawing=false;tr_ctx.beginPath();});
function tr_draw(pos){
  tr_ctx.lineWidth=tr_brushSize;tr_ctx.lineCap='round';tr_ctx.lineJoin='round';tr_ctx.strokeStyle=tr_currentColor;
  tr_ctx.lineTo(pos.x,pos.y);tr_ctx.stroke();tr_ctx.beginPath();tr_ctx.moveTo(pos.x,pos.y);
}
function tr_clearCanvas(){tr_ctx.clearRect(0,0,tr_canvas.width,tr_canvas.height);tr_strokes=0;tr_isDrawing=false;tr_ctx.beginPath();}
function tr_resetStars(){tr_starsLit=0;document.querySelectorAll('#p4StarsRow .star').forEach(s=>s.classList.remove('lit'));}
function tr_checkStarProgress(){
  if(tr_strokes===1&&tr_starsLit<1){document.querySelectorAll('#p4StarsRow .star')[0]?.classList.add('lit');tr_starsLit=1;}
  if(tr_strokes===3&&tr_starsLit<2){document.querySelectorAll('#p4StarsRow .star')[1]?.classList.add('lit');tr_starsLit=2;}
  if(tr_strokes===5&&tr_starsLit<3){
    document.querySelectorAll('#p4StarsRow .star')[2]?.classList.add('lit');tr_starsLit=3;
    tr_lettersTraced.add(tr_currentLetter);
    confetti(tr_currentColor);
    document.getElementById('p4HintMsg').textContent=TR_COPY[lang].hintDone;
    tr_updateScoreInfo();
  }
}
function tr_updateScoreInfo(){
  const total=getAlphabet().length;
  const done=tr_lettersTraced.size;
  document.getElementById('p4ScoreInfo').textContent=`Letters traced: ${done} / ${total}`;
}
function tr_saveScore(){
  const total=getAlphabet().length;
  const done=tr_lettersTraced.size;
  const score=Math.round((done/total)*100);
  const el=document.getElementById('p4ScoreInfo');
  el.textContent=TR_COPY[lang].saveMsg;
  saveComplete(ACT_IDS[4],score).then(()=>{
    el.textContent=TR_COPY[lang].saveOk+` (${done}/${total} letters traced)`;
    PREV_SCORES[4]=Math.max(PREV_SCORES[4],score);
  });
}
function tr_speakLetter(){
  if(!window.speechSynthesis)return;window.speechSynthesis.cancel();
  const word=getWords()[tr_currentLetter]||tr_currentLetter;
  const uL=new SpeechSynthesisUtterance(tr_currentLetter);uL.lang='en-US';uL.rate=0.8;uL.pitch=1.3;
  const uW=new SpeechSynthesisUtterance(word);uW.lang=lang==='tl'?'fil-PH':'en-US';uW.rate=0.8;uW.pitch=1.1;
  window.speechSynthesis.speak(uL);window.speechSynthesis.speak(uW);
}
function tr_resizeCanvas(){tr_canvas.width=document.getElementById('p4CanvasWrap').clientWidth;}
window.addEventListener('resize',tr_resizeCanvas);


// ── FLUSH PROGRESS ON NAVIGATION / PAGE HIDE ──
function flushActiveTabProgress() {
  switch(activeTab) {
    case 1: if(lm_score>0) saveProgress(ACT_IDS[1],Math.min(100,Math.round((lm_score/LM_MAX)*100))); break;
    case 2: if(sl_score>0) saveProgress(ACT_IDS[2],Math.round((sl_score/SL_MAX)*100)); break;
    case 3: if(fi_score>0) saveProgress(ACT_IDS[3],Math.round((fi_score/FI_MAX)*100)); break;
    case 4: break; // tracing uses own save button
  }
}
// Save when user navigates away (back button, close tab, page refresh)
window.addEventListener('pagehide',     () => flushActiveTabProgress());
window.addEventListener('beforeunload', () => flushActiveTabProgress());
// Save when tab/app goes to background (mobile)
document.addEventListener('visibilitychange', () => {
  if(document.visibilityState === 'hidden') flushActiveTabProgress();
});

// ── INIT ──
tr_resizeCanvas();
if(activeTab===1)lm_startGame();
else if(activeTab===2)sl_startGame();
else if(activeTab===3)fi_startGame();
else if(activeTab===4){tr_buildPicker();tr_selectLetter('A',COLORS[0]);}
</script>

</div><!-- /page-wrap -->
</body>
</html>