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
$lesson_id  = isset($_GET['lesson_id']) ? (int)$_GET['lesson_id'] : 9;

// ── HANDLE AJAX (QUIZZES VERSION) ──
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $quizzes_id = (int)($_POST['quizzes_id'] ?? 0);

    if ($_POST['action'] === 'start') {
        quizzes_start($conn, $student_id, $quizzes_id);
        echo json_encode(['success' => true]);
        exit;
    }

    if ($_POST['action'] === 'progress') {
        $score      = (int)($_POST['score'] ?? 0);
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

// ── FETCH QUIZZES ──
$stmt = $conn->prepare("SELECT * FROM quizzes WHERE lesson_id = ? ORDER BY quizzes_id");
$stmt->bind_param("i", $lesson_id);
$stmt->execute();
$activities = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// ── BUILD MAP ──
$act_map = [];
foreach ($activities as $act) {
    $prog = quizzes_get($conn, $student_id, $act['quizzes_id']);
    $act_map[$act['quizzes_name']] = [
        'quizzes_id' => $act['quizzes_id'],
        'status'     => $prog['status'],
        'score'      => $prog['score'],
    ];
}

// ── SHORTCUTS ──
$a1 = $act_map['handwashing_steps'] ?? ['quizzes_id'=>0,'status'=>'not_started','score'=>0];
$a2 = $act_map['hygiene_match'] ?? ['quizzes_id'=>0,'status'=>'not_started','score'=>0];
$a3 = $act_map['daily_routine'] ?? ['quizzes_id'=>0,'status'=>'not_started','score'=>0];
$a4 = $act_map['body_care_quiz'] ?? ['quizzes_id'=>0,'status'=>'not_started','score'=>0];

$active_tab = isset($_GET['tab']) ? (int)$_GET['tab'] : 1;
if ($active_tab < 1 || $active_tab > 4) $active_tab = 1;
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Motor Skills Activities — E-KINDER</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Fredoka+One&family=Nunito:wght@400;600;700;800;900&display=swap" rel="stylesheet">
<style>
:root{--teal:#0097a7;--teal-dark:#006978;--teal-light:#e0f7fa;--cream:#f0fdff;--pill:999px;--ac-color:#00acc1;}
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0;}
body{font-family:'Nunito',sans-serif;background:var(--cream);min-height:100vh;overflow-x:hidden;}
body::before{content:'';position:fixed;inset:0;pointer-events:none;z-index:0;background-image:radial-gradient(circle,rgba(0,151,167,.04) 1.2px,transparent 1.2px);background-size:26px 26px;}
.page-wrap{position:relative;z-index:1;padding-bottom:80px;}

/* NAV */
.lesson-nav{height:64px;padding:0 28px;background:rgba(255,255,255,.94);backdrop-filter:blur(16px);border-bottom:1px solid rgba(0,0,0,.07);box-shadow:0 2px 18px rgba(0,0,0,.06);position:sticky;top:0;z-index:200;display:flex;align-items:center;gap:14px;}
.lnav-back{display:inline-flex;align-items:center;gap:8px;background:var(--teal-dark);color:#fff;font-family:'Fredoka One',cursive;font-size:.88rem;padding:8px 20px;border-radius:var(--pill);text-decoration:none;box-shadow:0 4px 14px rgba(0,0,0,.22);transition:transform .22s cubic-bezier(.34,1.56,.64,1);flex-shrink:0;}
.lnav-back:hover{transform:scale(1.06);color:#fff;}
.lnav-title{font-family:'Fredoka One',cursive;font-size:1.1rem;color:var(--teal-dark);flex:1;text-align:center;}
.lang-toggle{display:inline-flex;align-items:center;background:#f0f0f0;border-radius:var(--pill);padding:4px;border:1.5px solid #e0e0e0;}
.lang-btn{font-family:'Fredoka One',cursive;font-size:.8rem;padding:6px 18px;border-radius:var(--pill);border:none;cursor:pointer;background:transparent;color:#aaa;transition:background .2s,color .2s;}
.lang-btn.active{background:var(--teal-dark);color:#fff;box-shadow:0 3px 10px rgba(0,0,0,.22);}

/* TABS */
.activity-tabs{display:flex;gap:8px;padding:20px 0 0;overflow-x:auto;scrollbar-width:none;}
.activity-tabs::-webkit-scrollbar{display:none;}
.act-tab{flex-shrink:0;display:flex;align-items:center;gap:8px;padding:10px 18px;border-radius:var(--pill);border:2.5px solid #e8e8e8;background:#fff;font-family:'Fredoka One',cursive;font-size:.82rem;color:#aaa;cursor:pointer;transition:all .22s cubic-bezier(.34,1.56,.64,1);box-shadow:0 2px 8px rgba(0,0,0,.05);}
.act-tab:hover:not(.active){border-color:#b2ebf2;color:#555;transform:translateY(-2px);}
.act-tab.active{background:var(--teal-dark);border-color:var(--teal-dark);color:#fff;box-shadow:0 6px 18px rgba(0,105,120,.28);}
.act-tab .tab-status{width:18px;height:18px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:.6rem;}
.act-tab.active .tab-status{background:rgba(255,255,255,.2);}
.tab-status.done{background:#40c057;color:#fff;}
.tab-status.progress{background:#fcc419;color:#fff;}
.tab-status.locked{background:#e8e8e8;color:#bbb;}
.act-tab .tab-score{font-size:.7rem;opacity:.7;}

/* PANELS */
.activity-panel{display:none;}
.activity-panel.active{display:block;animation:panelIn .35s ease both;}
@keyframes panelIn{from{opacity:0;transform:translateY(14px)}to{opacity:1;transform:none}}

/* PAGE HEADER */
.page-header{text-align:center;padding:28px 0 20px;}
.activity-badge{display:inline-flex;align-items:center;gap:8px;background:#fff;border:1.5px solid #b2ebf2;border-radius:var(--pill);padding:6px 18px;font-family:'Fredoka One',cursive;font-size:.78rem;color:var(--teal-dark);box-shadow:0 2px 10px rgba(0,0,0,.06);margin-bottom:14px;}
.page-title{font-family:'Fredoka One',cursive;font-size:clamp(1.8rem,4vw,2.6rem);color:var(--teal-dark);margin-bottom:6px;}
.page-sub{font-size:.92rem;color:#a0a8b0;font-weight:700;}
.best-score-badge{display:inline-flex;align-items:center;gap:6px;background:#fff9db;border:1.5px solid #ffe066;border-radius:var(--pill);padding:5px 14px;font-family:'Fredoka One',cursive;font-size:.78rem;color:#e67700;margin-top:6px;}

/* SCORE BAR */
.score-bar{display:flex;align-items:center;justify-content:center;gap:24px;background:#fff;border-radius:20px;padding:16px 28px;margin-bottom:28px;box-shadow:0 4px 18px rgba(0,0,0,.06);border:1.5px solid #eee;flex-wrap:wrap;}
.score-item{text-align:center;}
.score-num{font-family:'Fredoka One',cursive;font-size:1.8rem;color:var(--teal-dark);line-height:1;}
.score-label{font-size:.7rem;font-weight:800;color:#bbb;text-transform:uppercase;letter-spacing:.8px;}
.score-sep{width:1.5px;height:40px;background:#f0f0f0;}
.lives-wrap{display:flex;gap:6px;}
.life-icon{font-size:1.2rem;transition:opacity .3s;}
.life-icon.lost{opacity:.2;filter:grayscale(1);}
.progress-wrap{flex:1;min-width:160px;}
.progress-bar-outer{height:10px;background:#f0f0f0;border-radius:99px;overflow:hidden;}
.progress-bar-inner{height:100%;border-radius:99px;transition:width .5s cubic-bezier(.34,1.56,.64,1);}
.progress-label{font-family:'Fredoka One',cursive;font-size:.75rem;color:#aaa;margin-top:4px;}

/* STEP CARDS (Activity 1 - Step-by-Step Activities) */
.game-area{max-width:900px;margin:0 auto;}
.steps-prompt{font-family:'Fredoka One',cursive;font-size:1.1rem;color:var(--teal-dark);text-align:center;margin-bottom:16px;}
.round-activity-label{text-align:center;font-family:'Fredoka One',cursive;font-size:1.3rem;color:var(--teal-dark);margin-bottom:18px;display:flex;align-items:center;justify-content:center;gap:10px;}
.round-activity-icon{font-size:2rem;}

/* DROP ZONE */
.step-slots{display:flex;gap:14px;flex-wrap:wrap;justify-content:center;margin-bottom:24px;min-height:170px;border:3px dashed #b2ebf2;border-radius:24px;padding:16px;background:#f0fdff;transition:border-color .2s;}
.step-slots.drag-over{border-color:var(--teal);background:#e0f7fa;}
.step-slot{width:148px;height:165px;border-radius:20px;border:3px dashed #80deea;background:#fff;display:flex;flex-direction:column;align-items:center;justify-content:center;font-size:1.8rem;position:relative;transition:all .2s cubic-bezier(.34,1.56,.64,1);cursor:default;}
.step-slot .slot-num{position:absolute;top:-12px;left:-12px;width:28px;height:28px;border-radius:50%;background:var(--teal);color:#fff;font-family:'Fredoka One',cursive;font-size:.85rem;display:flex;align-items:center;justify-content:center;box-shadow:0 2px 8px rgba(0,151,167,.3);}
.step-slot.filled{border-color:var(--teal);background:var(--teal-light);}
.step-slot.correct-slot{border-color:#40c057;background:#f0fdf4;animation:slotPop .4s cubic-bezier(.34,1.56,.64,1);}
.step-slot.wrong-slot{border-color:#ff4444;background:#fff5f5;animation:cardShake .4s ease;}
.step-slot.drag-over-slot{border-color:var(--teal);background:#b2ebf2;transform:scale(1.04);}
.slot-remove-btn{position:absolute;top:4px;right:4px;width:22px;height:22px;border-radius:50%;background:#ff4444;color:#fff;border:none;font-size:.7rem;cursor:pointer;display:flex;align-items:center;justify-content:center;opacity:0;transition:opacity .2s;z-index:2;}
.step-slot.filled:hover .slot-remove-btn{opacity:1;}
@keyframes slotPop{0%{transform:scale(.9)}60%{transform:scale(1.1)}100%{transform:scale(1)}}
@keyframes cardShake{0%,100%{transform:translateX(0)}25%{transform:translateX(-8px)}75%{transform:translateX(8px)}}

/* DRAGGABLE CHOICES */
.step-choices{display:flex;gap:14px;flex-wrap:wrap;justify-content:center;margin-bottom:20px;}
.step-choice-card{width:148px;height:165px;border-radius:20px;border:3px solid #e0f7fa;background:#fff;display:flex;flex-direction:column;align-items:center;justify-content:center;cursor:grab;font-size:1.8rem;box-shadow:0 6px 18px rgba(0,0,0,.08);transition:all .25s cubic-bezier(.34,1.56,.64,1);user-select:none;}
.step-choice-card:active{cursor:grabbing;}
.step-choice-card:hover:not(.used){transform:translateY(-6px) scale(1.06);border-color:var(--teal);box-shadow:0 12px 28px rgba(0,151,167,.18);}
.step-choice-card.used{opacity:.28;pointer-events:none;}
.step-choice-card.dragging{opacity:.5;transform:scale(1.08);border-color:var(--teal);}
.step-choice-label{font-family:'Fredoka One',cursive;font-size:.72rem;color:#555;margin-top:6px;text-align:center;padding:0 6px;line-height:1.3;}

/* SUBMIT BUTTON */
.btn-submit-steps{display:inline-flex;align-items:center;gap:8px;padding:13px 34px;border-radius:var(--pill);border:none;font-family:'Fredoka One',cursive;font-size:1rem;cursor:pointer;transition:all .22s cubic-bezier(.34,1.56,.64,1);box-shadow:0 4px 14px rgba(0,0,0,.12);background:linear-gradient(135deg,#00acc1,#006978);color:#fff;}
.btn-submit-steps:disabled{background:#ccc;cursor:not-allowed;box-shadow:none;transform:none;}
.btn-submit-steps:not(:disabled):hover{transform:scale(1.06);box-shadow:0 8px 22px rgba(0,105,120,.3);}

/* ROUND DOTS */
.round-dots{display:flex;gap:8px;justify-content:center;margin-bottom:16px;flex-wrap:wrap;}
.round-dot{width:28px;height:28px;border-radius:50%;border:2px solid #b2ebf2;background:#f0fdff;display:flex;align-items:center;justify-content:center;font-family:'Fredoka One',cursive;font-size:.7rem;color:#aaa;transition:all .3s;}
.round-dot.rd-active{background:var(--teal);border-color:var(--teal-dark);color:#fff;transform:scale(1.15);}
.round-dot.rd-done{background:#40c057;border-color:#2e7d32;color:#fff;}
.round-dot.rd-skipped{background:#fcc419;border-color:#e67700;color:#fff;}

/* MATCH COLUMNS (Activity 2 - Column A vs Column B) */
.match-columns-wrapper{display:grid;grid-template-columns:1fr auto 1fr;gap:16px;max-width:720px;margin:0 auto 20px;align-items:start;}
.match-col-header{font-family:'Fredoka One',cursive;font-size:1.1rem;color:#fff;text-align:center;padding:10px 20px;border-radius:50px;margin-bottom:14px;}
.match-col-a .match-col-header{background:linear-gradient(135deg,#00acc1,#006978);}
.match-col-b .match-col-header{background:linear-gradient(135deg,#f06292,#c2185b);}
.match-col-divider{display:flex;flex-direction:column;align-items:center;justify-content:center;gap:10px;padding-top:56px;}
.match-col-divider-line{width:3px;flex:1;background:linear-gradient(180deg,#b2ebf2,#f8bbd0);border-radius:4px;min-height:200px;}
.match-col-divider-icon{font-size:1.6rem;}
.match-col{display:flex;flex-direction:column;gap:12px;}
.match-card{background:#fff;border:3px solid #e0f7fa;border-radius:18px;padding:14px 12px;text-align:center;cursor:pointer;box-shadow:0 4px 14px rgba(0,0,0,.06);transition:all .25s cubic-bezier(.34,1.56,.64,1);display:flex;flex-direction:column;align-items:center;justify-content:center;gap:8px;min-height:110px;}
.match-col-b .match-card{border-color:#fce4ec;}
.match-card:hover:not(.matched):not(.disabled){transform:translateY(-4px) scale(1.03);}
.match-col-a .match-card:hover:not(.matched):not(.disabled){border-color:var(--teal);box-shadow:0 8px 20px rgba(0,151,167,.18);}
.match-col-b .match-card:hover:not(.matched):not(.disabled){border-color:#f06292;box-shadow:0 8px 20px rgba(240,98,146,.18);}
.match-card.selected{transform:translateY(-4px) scale(1.04);}
.match-col-a .match-card.selected{border-color:var(--teal);background:var(--teal-light);box-shadow:0 8px 20px rgba(0,151,167,.2);}
.match-col-b .match-card.selected{border-color:#f06292;background:#fce4ec;box-shadow:0 8px 20px rgba(240,98,146,.2);}
.match-card.matched{border-color:#40c057;background:#f0fdf4;cursor:default;animation:slotPop .4s cubic-bezier(.34,1.56,.64,1);}
.match-card.wrong{animation:cardShake .4s ease;}
.match-col-a .match-card.wrong{border-color:#ff4444;background:#fff5f5;}
.match-col-b .match-card.wrong{border-color:#ff4444;background:#fff5f5;}
.match-card-img{width:90px;height:90px;object-fit:cover;border-radius:12px;box-shadow:0 2px 8px rgba(0,0,0,.1);}
.match-emoji{font-size:2.8rem;}
.match-label{font-family:'Fredoka One',cursive;font-size:.85rem;color:#555;line-height:1.3;}
.match-card.matched .match-label{color:#2e7d32;}
/* Selected choice highlight for Parts 3 & 4 */
.choice-btn.selected-choice{border-color:#00acc1!important;background:#e0f7fa!important;transform:translateY(-2px) scale(1.02);}
/* Larger match cards for Part 2 */
.hm-col-a-card,.hm-col-b-card{min-height:130px;padding:18px 14px;}
@media(max-width:560px){
  .match-columns-wrapper{grid-template-columns:1fr auto 1fr;gap:8px;}
  .match-card-img{width:70px;height:70px;}
  .match-col-divider-line{min-height:150px;}
}

/* ROUTINE DRAG (Activity 3 - Daily Routine) */
.routine-question{background:#fff;border-radius:28px;padding:28px 24px;text-align:center;box-shadow:0 8px 36px rgba(0,0,0,.08);border:2px solid #e0f7fa;max-width:500px;margin:0 auto 24px;}
.routine-emoji-big{font-size:5rem;line-height:1;margin-bottom:12px;animation:emojiBounce 2s ease-in-out infinite;}
@keyframes emojiBounce{0%,100%{transform:translateY(0) rotate(-3deg)}50%{transform:translateY(-12px) rotate(3deg)}}
.routine-question-text{font-family:'Fredoka One',cursive;font-size:1.3rem;color:var(--teal-dark);margin-bottom:6px;line-height:1.3;}
.routine-sub{font-size:.82rem;color:#ccc;font-weight:700;margin-bottom:16px;}
.routine-choices{display:grid;grid-template-columns:1fr 1fr;gap:12px;max-width:480px;margin:0 auto 20px;}
.routine-btn{background:#fff;border:3px solid #e0f7fa;border-radius:18px;padding:14px 16px;text-align:center;cursor:pointer;font-family:'Fredoka One',cursive;font-size:.95rem;color:var(--teal-dark);box-shadow:0 4px 14px rgba(0,0,0,.06);transition:all .22s cubic-bezier(.34,1.56,.64,1);}
.routine-btn:hover:not(.answered){transform:translateY(-3px) scale(1.03);border-color:var(--teal);}
.routine-btn.correct{border-color:#40c057!important;background:#f0fdf4;color:#2e7d32;}
.routine-btn.wrong{border-color:#ff4444!important;background:#fff5f5;color:#c62828;animation:cardShake .4s ease;}
.routine-btn.answered{cursor:default;}

/* QUIZ (Activity 4 - Body Care Quiz) */
.quiz-question-card{background:#fff;border-radius:28px;padding:28px 24px;box-shadow:0 8px 36px rgba(0,0,0,.08);border:2px solid #e0f7fa;max-width:560px;margin:0 auto 24px;}
.q-icon-big{font-size:4rem;text-align:center;display:block;margin-bottom:12px;animation:emojiBounce 2.5s ease-in-out infinite;}
.q-text{font-family:'Fredoka One',cursive;font-size:1.3rem;color:var(--teal-dark);text-align:center;margin-bottom:6px;line-height:1.3;}
.q-badge-pill{display:inline-flex;align-items:center;gap:6px;padding:4px 14px;border-radius:var(--pill);font-family:'Fredoka One',cursive;font-size:.75rem;background:#e0f7fa;color:var(--teal-dark);margin-bottom:16px;}
.quiz-choices{display:grid;grid-template-columns:1fr 1fr;gap:12px;max-width:520px;margin:0 auto 20px;}
.choice-btn{background:#fff;border:3px solid #e0f7fa;border-radius:18px;padding:14px 16px;text-align:left;cursor:pointer;display:flex;align-items:center;gap:12px;box-shadow:0 4px 14px rgba(0,0,0,.06);transition:all .22s cubic-bezier(.34,1.56,.64,1);}
.choice-btn:hover:not(.answered){transform:translateY(-3px) scale(1.02);border-color:var(--teal);}
.choice-btn.correct{border-color:#40c057!important;background:#f0fdf4;}
.choice-btn.wrong{border-color:#ff4444!important;background:#fff5f5;animation:cardShake .4s ease;}
.choice-btn.answered{cursor:default;}
.choice-letter{width:32px;height:32px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-family:'Fredoka One',cursive;font-size:.9rem;color:#fff;flex-shrink:0;}
.choice-text{font-family:'Fredoka One',cursive;font-size:.95rem;color:#333;line-height:1.3;}

/* SHARED */
.feedback-msg{text-align:center;font-family:'Fredoka One',cursive;font-size:1rem;padding:12px 20px;border-radius:14px;margin-bottom:16px;min-height:44px;}
.feedback-msg.correct{background:#f0fdf4;color:#2e7d32;border:1.5px solid #a5d6a7;}
.feedback-msg.wrong{background:#fff5f5;color:#c62828;border:1.5px solid #ef9a9a;}
.controls{text-align:center;margin-top:8px;}
.btn-action{display:inline-flex;align-items:center;gap:8px;padding:12px 28px;border-radius:var(--pill);border:none;font-family:'Fredoka One',cursive;font-size:.92rem;cursor:pointer;transition:all .22s cubic-bezier(.34,1.56,.64,1);box-shadow:0 4px 14px rgba(0,0,0,.12);}
.btn-primary-teal{background:var(--teal-dark);color:#fff;}
.btn-primary-teal:hover{transform:scale(1.05);box-shadow:0 8px 22px rgba(0,105,120,.3);}
.btn-secondary{background:#fff;color:var(--teal-dark);border:2px solid var(--teal);}
.btn-secondary:hover{transform:scale(1.04);background:var(--teal-light);}

/* RESULT OVERLAY */
.result-overlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,.6);backdrop-filter:blur(6px);z-index:999;align-items:center;justify-content:center;}
.result-overlay.active{display:flex;animation:fadeIn .3s ease;}
@keyframes fadeIn{from{opacity:0}to{opacity:1}}
.result-card{background:#fff;border-radius:32px;padding:48px 36px;text-align:center;max-width:400px;width:90%;box-shadow:0 24px 60px rgba(0,0,0,.2);animation:popIn .5s cubic-bezier(.34,1.56,.64,1);}
@keyframes popIn{from{opacity:0;transform:scale(.8)}to{opacity:1;transform:scale(1)}}
.result-trophy{font-size:5rem;margin-bottom:16px;display:block;animation:emojiBounce 2s ease-in-out infinite;}
.result-title{font-family:'Fredoka One',cursive;font-size:2rem;color:var(--teal-dark);margin-bottom:8px;}
.result-sub{font-size:.95rem;color:#aaa;font-weight:700;margin-bottom:16px;}
.result-score{font-family:'Fredoka One',cursive;font-size:2.4rem;color:var(--teal);margin-bottom:24px;}
.result-btns{display:flex;gap:12px;justify-content:center;flex-wrap:wrap;}
.btn-retry{background:#fff;color:var(--teal-dark);border:3px solid var(--teal);border-radius:var(--pill);padding:12px 28px;font-family:'Fredoka One',cursive;font-size:.95rem;cursor:pointer;transition:all .2s cubic-bezier(.34,1.56,.64,1);}
.btn-retry:hover{background:var(--teal-light);transform:scale(1.05);}
.btn-next-act{background:var(--teal-dark);color:#fff;border:none;border-radius:var(--pill);padding:12px 28px;font-family:'Fredoka One',cursive;font-size:.95rem;cursor:pointer;box-shadow:0 6px 18px rgba(0,105,120,.28);transition:all .2s cubic-bezier(.34,1.56,.64,1);}
.btn-next-act:hover{transform:scale(1.05);}

/* CONFETTI */
.confetti-piece{position:fixed;width:10px;height:10px;border-radius:2px;pointer-events:none;z-index:9999;animation:confettiFall 1.2s ease forwards;}
@keyframes confettiFall{0%{opacity:1;transform:translateY(-20px) rotate(0deg)}100%{opacity:0;transform:translateY(100vh) rotate(720deg)}}
</style>
</head>
<body>
<div class="page-wrap">

<!-- NAV -->
<nav class="lesson-nav">
  <a href="activities.php" class="lnav-back"><i class="fas fa-arrow-left"></i> Back</a>
  <div class="lnav-title"><i class="fas fa-hands"></i> Motor Skills Activities</div>
  <div class="lang-toggle">
    <button class="lang-btn active" id="btnEN" onclick="setLang('en')">EN</button>
    <button class="lang-btn"        id="btnTL" onclick="setLang('tl')">FIL</button>
  </div>
</nav>

<div class="container py-4">

  <!-- TABS -->
  <div class="activity-tabs">
    <?php
    $tabs = [
      1 => ['icon'=>'fas fa-list-ol',       'label'=>'Step-by-Step',      'act'=>$a1],
      2 => ['icon'=>'fas fa-hand-holding-heart', 'label'=>'Hygiene Match',     'act'=>$a2],
      3 => ['icon'=>'fas fa-sun',         'label'=>'Daily Routine',     'act'=>$a3],
      4 => ['icon'=>'fas fa-shield-alt',  'label'=>'Body Care Quiz',    'act'=>$a4],
    ];
    foreach ($tabs as $n => $t):
      $s = $t['act']['status'];
      $sc = $t['act']['score'];
      $statusClass = $s === 'completed' ? 'done' : ($s === 'in_progress' ? 'progress' : 'locked');
      $statusIcon  = $s === 'completed' ? '<i class="fas fa-check"></i>' : ($s === 'in_progress' ? '<i class="fas fa-ellipsis"></i>' : '<i class="fas fa-lock"></i>');
    ?>
    <div class="act-tab <?php echo $active_tab === $n ? 'active' : ''; ?>" onclick="switchTab(<?php echo $n; ?>)">
      <i class="<?php echo $t['icon']; ?>"></i>
      <?php echo $t['label']; ?>
      <div class="tab-status <?php echo $statusClass; ?>"><?php echo $statusIcon; ?></div>
      <?php if ($sc > 0): ?><span class="tab-score"><?php echo $sc; ?>%</span><?php endif; ?>
    </div>
    <?php endforeach; ?>
  </div>

  <!-- ══════════════════════════════════════════ -->
  <!-- PANEL 1: STEP-BY-STEP ACTIVITIES           -->
  <!-- ══════════════════════════════════════════ -->
  <div class="activity-panel <?php echo $active_tab === 1 ? 'active' : ''; ?>" id="panel1">
    <div class="page-header">
      <div class="activity-badge"><i class="fas fa-list-ol"></i> <span>Activity 1 of 4</span></div>
      <div class="page-title" id="p1Title">Step-by-Step Activities!</div>
      <div class="page-sub" id="p1Sub">Drag and drop the steps in the correct order!</div>
      <?php if ($a1['score'] > 0): ?>
      <div class="best-score-badge"><i class="fas fa-star"></i> Best Score: <?php echo $a1['score']; ?>%</div>
      <?php endif; ?>
    </div>
    <div class="score-bar">
      <div class="score-item"><div class="score-num" id="p1Score">0</div><div class="score-label">Score</div></div>
      <div class="score-sep"></div>
      <div class="score-item">
        <div class="score-num"><span id="p1RoundCur">1</span><span style="font-size:1rem;color:#ccc"> / 5</span></div>
        <div class="score-label">Round</div>
      </div>
      <div class="score-sep"></div>
      <div class="progress-wrap">
        <div class="progress-bar-outer"><div class="progress-bar-inner" id="p1Bar" style="width:0%;background:linear-gradient(90deg,#00acc1,#006978)"></div></div>
        <div class="progress-label" id="p1BarLbl">Round 1 of 6</div>
      </div>
    </div>
    <div class="game-area">
      <!-- Round dots -->
      <div class="round-dots" id="p1RoundDots"></div>
      <!-- Current activity label -->
      <div class="round-activity-label" id="p1ActivityLabel">
        <span class="round-activity-icon" id="p1ActivityIcon">🧼</span>
        <span id="p1ActivityName">Handwashing</span>
      </div>
      <div class="steps-prompt" id="p1Prompt">Drag the steps into the correct order!</div>
      <!-- Drop zone -->
      <div class="step-slots" id="p1Slots"></div>
      <!-- Draggable cards -->
      <div class="step-choices" id="p1Choices"></div>
      <div class="feedback-msg" id="p1Feedback"></div>
      <div class="controls" style="display:flex;gap:12px;justify-content:center;flex-wrap:wrap;">
        <button class="btn-submit-steps" id="p1SubmitBtn" onclick="hw_submit()" disabled>
          <i class="fas fa-check-circle"></i> <span id="p1SubmitLbl">Submit Answer</span>
        </button>
        <button class="btn-action btn-secondary" id="p1NextBtn" onclick="hw_nextRound()" style="display:none">
          <i class="fas fa-forward"></i> <span id="p1NextLbl">Next Round</span>
        </button>
      </div>
    </div>
  </div>

  <!-- ══════════════════════════════════════════ -->
  <!-- PANEL 2: HYGIENE MATCH                     -->
  <!-- ══════════════════════════════════════════ -->
  <div class="activity-panel <?php echo $active_tab === 2 ? 'active' : ''; ?>" id="panel2">
    <div class="page-header">
      <div class="activity-badge"><i class="fas fa-heart"></i> <span>Activity 2 of 4</span></div>
      <div class="page-title" id="p2Title">Hygiene Match!</div>
      <div class="page-sub" id="p2Sub">Match each activity name to its step image and description!</div>
      <?php if ($a2['score'] > 0): ?>
      <div class="best-score-badge"><i class="fas fa-star"></i> Best Score: <?php echo $a2['score']; ?>%</div>
      <?php endif; ?>
    </div>
    <div class="score-bar">
      <div class="score-item"><div class="score-num" id="p2Score">0</div><div class="score-label">Score</div></div>
      <div class="score-sep"></div>
      <div class="score-item">
        <div class="score-num"><span id="p2RoundCur">1</span><span style="font-size:1rem;color:#ccc"> / 5</span></div>
        <div class="score-label">Round</div>
      </div>
      <div class="score-sep"></div>
      <div class="progress-wrap">
        <div class="progress-bar-outer"><div class="progress-bar-inner" id="p2Bar" style="width:0%;background:linear-gradient(90deg,#f06292,#c2185b)"></div></div>
        <div class="progress-label" id="p2BarLbl">Round 1 of 4</div>
      </div>
    </div>
    <div class="game-area">
      <!-- Round dots -->
      <div class="round-dots" id="p2RoundDots"></div>
      <div class="steps-prompt" id="p2Prompt">Tap a card from Column A, then one from Column B to pair them. Match all 6 pairs, then click Submit!</div>
      <div class="match-columns-wrapper" style="max-width:800px;">
        <div class="match-col match-col-a">
          <div class="match-col-header">Column A — Activity</div>
          <div id="p2ColA"></div>
        </div>
        <div class="match-col-divider">
          <div class="match-col-divider-line"></div>
          <div class="match-col-divider-icon"><i class="fas fa-link" style="font-size:1.4rem;color:#80deea"></i></div>
          <div class="match-col-divider-line"></div>
        </div>
        <div class="match-col match-col-b">
          <div class="match-col-header">Column B — Step</div>
          <div id="p2ColB"></div>
        </div>
      </div>
      <!-- Pair status display -->
      <div id="p2PendingDisplay" style="display:none;text-align:center;margin:10px 0 14px;font-family:'Fredoka One',cursive;font-size:.88rem;color:#006978;background:#e0f7fa;border:1.5px solid #b2ebf2;border-radius:14px;padding:10px 18px;transition:all .2s;"></div>
      <div class="feedback-msg" id="p2Feedback"></div>
      <div class="controls" style="display:flex;gap:12px;justify-content:center;flex-wrap:wrap;">
        <button class="btn-submit-steps" id="p2SubmitBtn" onclick="hm_submit()" disabled>
          <i class="fas fa-check-circle"></i> <span id="p2SubmitLbl">Submit All Matches</span>
        </button>
        <button class="btn-action btn-secondary" id="p2NextBtn" onclick="hm_nextRound()" style="display:none">
          <i class="fas fa-forward"></i> <span id="p2NextLbl">Next Round</span>
        </button>
      </div>
    </div>
  </div>

  <!-- ══════════════════════════════════════════ -->
  <!-- PANEL 3: DAILY ROUTINE                     -->
  <!-- ══════════════════════════════════════════ -->
  <div class="activity-panel <?php echo $active_tab === 3 ? 'active' : ''; ?>" id="panel3">
    <div class="page-header">
      <div class="activity-badge"><i class="fas fa-sun"></i> <span>Activity 3 of 4</span></div>
      <div class="page-title" id="p3Title">Daily Routine!</div>
      <div class="page-sub" id="p3Sub">Choose the correct answer for each situation!</div>
      <?php if ($a3['score'] > 0): ?>
      <div class="best-score-badge"><i class="fas fa-star"></i> Best Score: <?php echo $a3['score']; ?>%</div>
      <?php endif; ?>
    </div>
    <div class="score-bar">
      <div class="score-item"><div class="score-num" id="p3Score">0</div><div class="score-label">Score</div></div>
      <div class="score-sep"></div>
      <div class="score-item"><div class="score-num" id="p3QNum">1</div><div class="score-label">Question</div></div>
      <div class="score-sep"></div>
      <div class="progress-wrap">
        <div class="progress-bar-outer"><div class="progress-bar-inner" id="p3Bar" style="width:0%;background:linear-gradient(90deg,#fcc419,#f59f00)"></div></div>
        <div class="progress-label" id="p3BarLbl">0 / 10</div>
      </div>
    </div>
    <div class="routine-question">
      <div class="routine-emoji-big" id="p3Emoji"><i class="fas fa-sun-horizon" style="font-size:5rem;color:#fcc419"></i></div>
      <div class="routine-question-text" id="p3QText">Loading...</div>
      <div class="routine-sub" id="p3Sub2">What should you do?</div>
    </div>
    <div class="quiz-choices" id="p3Choices"></div>
    <div class="feedback-msg" id="p3Feedback"></div>
    <div class="controls" style="display:flex;gap:12px;justify-content:center;flex-wrap:wrap;">
      <button class="btn-submit-steps" id="p3SubmitBtn" onclick="dr_submit()" disabled>
        <i class="fas fa-check-circle"></i> <span id="p3SubmitLbl">Submit Answer</span>
      </button>
      <button class="btn-action btn-secondary" id="p3NextBtn" onclick="dr_nextQuestion()" style="display:none">
        <i class="fas fa-arrow-right"></i> <span id="p3NextLbl">Next</span>
      </button>
    </div>
  </div>

  <!-- ══════════════════════════════════════════ -->
  <!-- PANEL 4: BODY CARE QUIZ                    -->
  <!-- ══════════════════════════════════════════ -->
  <div class="activity-panel <?php echo $active_tab === 4 ? 'active' : ''; ?>" id="panel4">
    <div class="page-header">
      <div class="activity-badge"><i class="fas fa-shield-alt"></i> <span>Activity 4 of 4</span></div>
      <div class="page-title" id="p4Title">Body Care Quiz!</div>
      <div class="page-sub" id="p4Sub">Answer the questions about taking care of your body!</div>
      <?php if ($a4['score'] > 0): ?>
      <div class="best-score-badge"><i class="fas fa-star"></i> Best Score: <?php echo $a4['score']; ?>%</div>
      <?php endif; ?>
    </div>
    <div class="score-bar">
      <div class="score-item"><div class="score-num" id="p4Score">0</div><div class="score-label">Score</div></div>
      <div class="score-sep"></div>
      <div class="score-item"><div class="score-num" id="p4QNum">1</div><div class="score-label">Question</div></div>
      <div class="score-sep"></div>
      <div class="progress-wrap">
        <div class="progress-bar-outer"><div class="progress-bar-inner" id="p4Bar" style="width:0%;background:linear-gradient(90deg,#7c3aed,#4c1d95)"></div></div>
        <div class="progress-label" id="p4BarLbl">0 / 10</div>
      </div>
    </div>
    <div class="quiz-question-card">
      <span class="q-icon-big" id="p4Emoji"><i class="fas fa-soap" style="color:#00acc1"></i></span>
      <div class="q-text" id="p4QText">Loading...</div>
      <div style="text-align:center"><span class="q-badge-pill" id="p4QBadge"><i class="fas fa-hands"></i> Motor Skills</span></div>
    </div>
    <div class="quiz-choices" id="p4Choices"></div>
    <div class="feedback-msg" id="p4Feedback"></div>
    <div class="controls" style="display:flex;gap:12px;justify-content:center;flex-wrap:wrap;">
      <button class="btn-submit-steps" id="p4SubmitBtn" onclick="bc_submit()" disabled>
        <i class="fas fa-check-circle"></i> <span id="p4SubmitLbl">Submit Answer</span>
      </button>
      <button class="btn-action btn-secondary" id="p4NextBtn" onclick="bc_nextQuestion()" style="display:none">
        <i class="fas fa-arrow-right"></i> <span id="p4NextLbl">Next</span>
      </button>
    </div>
  </div>

</div><!-- /container -->

<!-- RESULT OVERLAY -->
<div class="result-overlay" id="resultOverlay">
  <div class="result-card">
    <span class="result-trophy" id="resultTrophy"><i class="fas fa-trophy" style="color:#fcc419"></i></span>
    <div class="result-title" id="resultTitle">Amazing!</div>
    <div class="result-sub"   id="resultSub">You finished the activity!</div>
    <div class="result-score" id="resultScore"></div>
    <div class="result-btns">
      <button class="btn-retry"    id="retryBtn"   onclick="retryActivity()"><i class="fas fa-rotate-left"></i> Try Again</button>
      <button class="btn-next-act" id="nextActBtn" onclick="goNextActivity()"><i class="fas fa-arrow-right"></i> Next Activity</button>
    </div>
  </div>
</div>

</div><!-- /page-wrap -->

<script>
// ── GLOBALS ──
const ACT_IDS = {
  1: <?php echo $a1['quizzes_id']; ?>,
  2: <?php echo $a2['quizzes_id']; ?>,
  3: <?php echo $a3['quizzes_id']; ?>,
  4: <?php echo $a4['quizzes_id']; ?>
};
let activeTab = <?php echo $active_tab; ?>;
let lang = 'en';

// ── LANG ──
function setLang(l) {
  lang = l;
  document.getElementById('btnEN').classList.toggle('active', l==='en');
  document.getElementById('btnTL').classList.toggle('active', l==='tl');
  if(activeTab===1){ document.getElementById('p1ActivityName').textContent=hw_currentActivity().name; document.getElementById('p1ActivityIcon').textContent=hw_currentActivity().icon; hw_render(); }
  if(activeTab===2 && hm_started) hm_renderRound();
  if(activeTab===3) dr_render();
  if(activeTab===4) bc_render();
}

// ── TAB SWITCHING ──
function switchTab(n) {
  document.querySelectorAll('.activity-panel').forEach(p => p.classList.remove('active'));
  document.querySelectorAll('.act-tab').forEach(t => t.classList.remove('active'));
  document.getElementById('panel'+n).classList.add('active');
  document.querySelectorAll('.act-tab')[n-1].classList.add('active');
  activeTab = n;
  if(n===1 && hw_round===0) hw_startGame();
  if(n===2 && !hm_started)  hm_startGame();
  if(n===3 && dr_qIdx===0)  dr_startGame();
  if(n===4 && bc_qIdx===0)  bc_startGame();
  history.replaceState(null,'','?tab='+n);
}

// ── AJAX HELPERS ──
function saveStart(aid) {
  if(!aid) return;
  fetch('activity-motorskills.php',{method:'POST',headers:{'Content-Type':'application/x-www-form-urlencoded'},body:`action=start&quizzes_id=${aid}`});
}
function saveProgress(aid, score, checkpoint=0) {
  if(!aid) return;
  fetch('activity-motorskills.php',{method:'POST',headers:{'Content-Type':'application/x-www-form-urlencoded'},body:`action=progress&quizzes_id=${aid}&score=${score}&checkpoint=${checkpoint}`});
}
function saveComplete(aid, score) {
  return fetch('activity-motorskills.php',{method:'POST',headers:{'Content-Type':'application/x-www-form-urlencoded'},body:`action=complete&quizzes_id=${aid}&score=${score}`});
}

// ── CONFETTI ──
function confetti(color='#00acc1') {
  const colors=[color,'#fcc419','#40c057','#f06292','#74c0fc'];
  for(let i=0;i<18;i++){
    const el=document.createElement('div');
    el.className='confetti-piece';
    el.style.cssText=`left:${Math.random()*100}vw;top:0;background:${colors[Math.floor(Math.random()*colors.length)]};width:${6+Math.random()*8}px;height:${6+Math.random()*8}px;animation-delay:${Math.random()*.4}s;animation-duration:${.9+Math.random()*.6}s;border-radius:${Math.random()>0.5?'50%':'2px'}`;
    document.body.appendChild(el);
    setTimeout(()=>el.remove(),1600);
  }
}

// ── RESULT OVERLAY ──
function showResult(tabNum, score, maxScore) {
  const pct = Math.round((score/maxScore)*100);
  const clamped = Math.min(100, pct);
  saveComplete(ACT_IDS[tabNum], clamped);

  let trophy='<i class="fas fa-medal" style="color:#cd7f32"></i>', title='Good Try!', sub='Keep practicing!';
  if(pct>=90){trophy='<i class="fas fa-trophy" style="color:#fcc419"></i>';title=lang==='en'?'Amazing!':'Kamangha-mangha!';sub=lang==='en'?'You got a perfect score!':'Perpekto ang iyong sagot!';}
  else if(pct>=70){trophy='<i class="fas fa-trophy" style="color:#c0c0c0"></i>';title=lang==='en'?'Excellent!':'Napakahusay!';sub=lang==='en'?'Great job!':'Magaling ka!';}
  else if(pct>=50){trophy='<i class="fas fa-award" style="color:#a8a8a8"></i>';title=lang==='en'?'Good Job!':'Magaling!';sub=lang==='en'?'Almost there!':'Halos na!';}

  document.getElementById('resultTrophy').innerHTML=trophy;
  document.getElementById('resultTitle').textContent=title;
  document.getElementById('resultSub').textContent=sub;
  document.getElementById('resultScore').textContent=`${clamped}%`;
  document.getElementById('nextActBtn').innerHTML = tabNum<4 ? (lang==='en'?'<i class="fas fa-arrow-right"></i> Next Activity':'<i class="fas fa-arrow-right"></i> Susunod') : (lang==='en'?'<i class="fas fa-check-circle"></i> All Done!':'<i class="fas fa-check-circle"></i> Tapos na!');
  document.getElementById('resultOverlay').classList.add('active');
  confetti('#00acc1');
}
function retryActivity(){
  document.getElementById('resultOverlay').classList.remove('active');
  if(activeTab===1){ hw_score=0; hw_round=0; hw_roundDone=Array(HW_TOTAL_ROUNDS).fill(null); hw_startGame(); }
  if(activeTab===2){ hm_score=0; hm_round=0; hm_roundDone=[]; hm_started=false; hm_startGame(); }
  if(activeTab===3) dr_startGame();
  if(activeTab===4) bc_startGame();
}
function goNextActivity(){
  document.getElementById('resultOverlay').classList.remove('active');
  if(activeTab<4) switchTab(activeTab+1);
}

// ════════════════════════════════════════════════
// ACTIVITY 1 — STEP-BY-STEP ACTIVITIES (6 rounds)
// Each round = one activity from motorskills.php
// Drag-and-drop ordering, submit button, 10pts/step
// ════════════════════════════════════════════════
const STEP_ACTIVITIES = {
  en: [
    {
      name:'Handwashing', icon:'🧼', color:'#00acc1',
      steps:[
        {label:'Turn on the Tap',         img:'pictures/motorskills/handwash_step1.jpg'},
        {label:'Apply Soap',              img:'pictures/motorskills/handwash_step2.jpg'},
        {label:'Scrub for 20 Seconds',    img:'pictures/motorskills/handwash_step3.jpg'},
        {label:'Rinse Thoroughly',        img:'pictures/motorskills/handwash_step4.jpg'},
        {label:'Dry Your Hands',          img:'pictures/motorskills/handwash_step5.jpg'},
      ]
    },
    {
      name:'Tooth Brushing', icon:'🦷', color:'#2e7d32',
      steps:[
        {label:'Get Toothbrush & Paste',  img:'pictures/motorskills/brush_step1.jpg'},
        {label:'Brush Front Teeth',       img:'pictures/motorskills/brush_step2.png'},
        {label:'Brush Inside & Back',     img:'pictures/motorskills/brush_step3.jpg'},
        {label:'Brush Your Tongue',       img:'pictures/motorskills/brush_step4.jpg'},
        {label:'Rinse & Spit',            img:'pictures/motorskills/brush_step5.jpg'},
      ]
    },
    {
      name:'Proper Bathing', icon:'🛁', color:'#1565c0',
      steps:[
        {label:'Prepare Supplies',        img:'pictures/motorskills/bath_step1.jpg'},
        {label:'Wet Your Whole Body',     img:'pictures/motorskills/bath_step2.jpg'},
        {label:'Soap Your Whole Body',    img:'pictures/motorskills/bath_step3.png'},
        {label:'Wash Your Hair',          img:'pictures/motorskills/bath_step4.jpg'},
        {label:'Rinse & Dry Off',         img:'pictures/motorskills/bath_step5.jpg'},
      ]
    },
    {
      name:'Hair Combing', icon:'💇', color:'#c2185b',
      steps:[
        {label:'Use Your Own Comb',       img:'pictures/motorskills/comb_step1.jpg'},
        {label:'Start at the Tips',       img:'pictures/motorskills/comb_step2.jpg'},
        {label:'Comb Carefully',          img:'pictures/motorskills/comb_step3.jpg'},
        {label:'Comb Every Day',          img:'pictures/motorskills/comb_step4.jpg'},
      ]
    },
    {
      name:'Nail Trimming', icon:'✂️', color:'#e65100',
      steps:[
        {label:"Ask Parent for Help",     img:'pictures/motorskills/nails_step1.jpg'},
        {label:'Use the Right Tool',      img:'pictures/motorskills/nails_step2.jpg'},
        {label:'Cut Straight Across',     img:'pictures/motorskills/nails_step3.jpg'},
        {label:'Clean Under the Nails',   img:'pictures/motorskills/nails_step4.jpg'},
        {label:'Make It a Weekly Habit',  img:'pictures/motorskills/nails_step5.jpg'},
      ]
    },
    {
      name:'Getting Dressed', icon:'👕', color:'#4527a0',
      steps:[
        {label:'Choose Clean Clothes',    img:'pictures/motorskills/clothes_step1.jpg'},
        {label:'Put It on the Right Way', img:'pictures/motorskills/clothes_step2.jpg'},
        {label:'Underwear First',         img:'pictures/motorskills/clothes_step3.jpg'},
        {label:'Use Buttons & Zippers',   img:'pictures/motorskills/clothes_step4.jpg'},
        {label:'Check in the Mirror',     img:'pictures/motorskills/clothes_step5.jpg'},
      ]
    },
  ],
  tl: [
    {
      name:'Paghuhugas ng Kamay', icon:'🧼', color:'#00acc1',
      steps:[
        {label:'Buksan ang Gripo',             img:'pictures/motorskills/handwash_step1.jpg'},
        {label:'Mag-sabon',                    img:'pictures/motorskills/handwash_step2.jpg'},
        {label:'Kuskusin ng 20 Segundo',       img:'pictures/motorskills/handwash_step3.jpg'},
        {label:'Banlawan nang Mabuti',         img:'pictures/motorskills/handwash_step4.jpg'},
        {label:'Patuyuin ang Kamay',           img:'pictures/motorskills/handwash_step5.jpg'},
      ]
    },
    {
      name:'Pagsisipilyo ng Ngipin', icon:'🦷', color:'#2e7d32',
      steps:[
        {label:'Kumuha ng Sipilyo at Toothpaste', img:'pictures/motorskills/brush_step1.jpg'},
        {label:'Sipilyo ang Harap ng Ngipin',     img:'pictures/motorskills/brush_step2.png'},
        {label:'Sipilyo ang Loob at Likod',       img:'pictures/motorskills/brush_step3.jpg'},
        {label:'Sipilyo ang Dila',                img:'pictures/motorskills/brush_step4.jpg'},
        {label:'Banlawan at Dura',                img:'pictures/motorskills/brush_step5.jpg'},
      ]
    },
    {
      name:'Tamang Paliligo', icon:'🛁', color:'#1565c0',
      steps:[
        {label:'Ihanda ang Lahat',         img:'pictures/motorskills/bath_step1.jpg'},
        {label:'Basain ang Buong Katawan', img:'pictures/motorskills/bath_step2.jpg'},
        {label:'Sabunin ang Katawan',      img:'pictures/motorskills/bath_step3.png'},
        {label:'Hugasan ang Buhok',        img:'pictures/motorskills/bath_step4.jpg'},
        {label:'Banlawan at Patuyuin',     img:'pictures/motorskills/bath_step5.jpg'},
      ]
    },
    {
      name:'Pagsusuklay', icon:'💇', color:'#c2185b',
      steps:[
        {label:'Gamitin ang Sariling Suklay', img:'pictures/motorskills/comb_step1.jpg'},
        {label:'Magsimula sa Dulo',           img:'pictures/motorskills/comb_step2.jpg'},
        {label:'Mag-suklay nang Maingat',     img:'pictures/motorskills/comb_step3.jpg'},
        {label:'Suklayin Araw-Araw',          img:'pictures/motorskills/comb_step4.jpg'},
      ]
    },
    {
      name:'Paggupit ng Kuko', icon:'✂️', color:'#e65100',
      steps:[
        {label:'Humingi ng Tulong sa Magulang', img:'pictures/motorskills/nails_step1.jpg'},
        {label:'Gamitin ang Tamang Kagamitan',  img:'pictures/motorskills/nails_step2.jpg'},
        {label:'Gupitin nang Tuwid',            img:'pictures/motorskills/nails_step3.jpg'},
        {label:'Linisin ang Ilalim ng Kuko',    img:'pictures/motorskills/nails_step4.jpg'},
        {label:'Gawing Lingguhang Gawi',        img:'pictures/motorskills/nails_step5.jpg'},
      ]
    },
    {
      name:'Pagbibihis ng Damit', icon:'👕', color:'#4527a0',
      steps:[
        {label:'Pumili ng Malinis na Damit',   img:'pictures/motorskills/clothes_step1.jpg'},
        {label:'Isuot sa Tamang Direksyon',    img:'pictures/motorskills/clothes_step2.jpg'},
        {label:'Panloob na Damit Muna',        img:'pictures/motorskills/clothes_step3.jpg'},
        {label:'Gamitin ang Butones at Zipper',img:'pictures/motorskills/clothes_step4.jpg'},
        {label:'Tingnan sa Salamin',           img:'pictures/motorskills/clothes_step5.jpg'},
      ]
    },
  ]
};

const HW_TOTAL_ROUNDS = 5;
let hw_score=0, hw_round=0;
let hw_placed=[];      // array of step indices placed in slots (null = empty)
let hw_shuffled=[];    // shuffled step indices shown as choices
let hw_roundDone=[];   // tracks which rounds are done (true/false/skipped)
let hw_dragSrcIdx=null; // index in hw_shuffled being dragged
let hw_dragFromSlot=null; // slot index if dragging from a slot

function hw_currentActivity(){
  return STEP_ACTIVITIES[lang][(hw_round-1)];
}

function hw_startGame(){
  hw_score=0; hw_round=0;
  hw_roundDone=Array(HW_TOTAL_ROUNDS).fill(null);
  document.getElementById('p1Score').textContent='0';
  document.getElementById('p1Feedback').textContent='';
  document.getElementById('p1Feedback').className='feedback-msg';
  document.getElementById('p1NextBtn').style.display='none';
  document.getElementById('p1SubmitBtn').disabled=true;
  saveStart(ACT_IDS[1]);
  hw_buildRoundDots();
  hw_nextRound();
}

function hw_buildRoundDots(){
  const el=document.getElementById('p1RoundDots');
  el.innerHTML='';
  for(let i=0;i<HW_TOTAL_ROUNDS;i++){
    const d=document.createElement('div');
    d.className='round-dot'+(i===hw_round-1?' rd-active':'');
    d.id='rdot_'+i;
    d.textContent=i+1;
    el.appendChild(d);
  }
}

function hw_updateRoundDots(){
  for(let i=0;i<HW_TOTAL_ROUNDS;i++){
    const d=document.getElementById('rdot_'+i);
    if(!d) continue;
    d.className='round-dot';
    if(hw_roundDone[i]===true) d.classList.add('rd-done');
    else if(hw_roundDone[i]==='skipped') d.classList.add('rd-skipped');
    else if(i===hw_round-1) d.classList.add('rd-active');
  }
}

function hw_nextRound(){
  hw_round++;
  if(hw_round>HW_TOTAL_ROUNDS){ hw_showResult(); return; }
  const act=hw_currentActivity();
  hw_placed=Array(act.steps.length).fill(null);
  hw_shuffled=[...Array(act.steps.length).keys()].sort(()=>Math.random()-.5);

  document.getElementById('p1RoundCur').textContent=hw_round;
  document.getElementById('p1BarLbl').textContent='Round '+hw_round+' of '+HW_TOTAL_ROUNDS;
  document.getElementById('p1Bar').style.width=((hw_round-1)/HW_TOTAL_ROUNDS*100)+'%';
  document.getElementById('p1Feedback').textContent='';
  document.getElementById('p1Feedback').className='feedback-msg';
  document.getElementById('p1NextBtn').style.display='none';
  document.getElementById('p1SubmitBtn').style.display='inline-flex';
  document.getElementById('p1SubmitBtn').disabled=true;
  document.getElementById('p1ActivityIcon').textContent=act.icon;
  document.getElementById('p1ActivityName').textContent=act.name;
  document.getElementById('p1Prompt').textContent=
    lang==='en'?'Drag and drop the steps in the correct order!':'I-drag at i-drop ang mga hakbang sa tamang pagkakasunod!';

  hw_buildRoundDots();
  hw_updateRoundDots();
  hw_render();
  // Set zone-level drag listeners once per round (on fresh element)
  const slotsZone=document.getElementById('p1Slots');
  slotsZone.addEventListener('dragover',e=>{e.preventDefault();slotsZone.classList.add('drag-over');});
  slotsZone.addEventListener('dragleave',e=>{if(!slotsZone.contains(e.relatedTarget)) slotsZone.classList.remove('drag-over');});
  slotsZone.addEventListener('drop',e=>{
    e.preventDefault(); slotsZone.classList.remove('drag-over');
    if(hw_dragSrcIdx!==null){
      const firstEmpty=hw_placed.indexOf(null);
      if(firstEmpty!==-1){ hw_placed[firstEmpty]=hw_dragSrcIdx; hw_dragSrcIdx=null; hw_render(); hw_checkSubmitReady(); }
    }
  });
}

function hw_render(){
  const act=hw_currentActivity();
  const steps=act.steps;

  // ── DROP ZONE ──
  const slotsEl=document.getElementById('p1Slots');
  slotsEl.innerHTML='';
  steps.forEach((s,pos)=>{
    const slot=document.createElement('div');
    const placedIdx=hw_placed[pos];
    slot.className='step-slot'+(placedIdx!==null?' filled':'');
    slot.id='slot_'+pos;

    const numEl=document.createElement('div');
    numEl.className='slot-num'; numEl.textContent=pos+1;
    slot.appendChild(numEl);

    if(placedIdx!==null){
      const ps=steps[placedIdx];
      const im=document.createElement('img');
      im.src=ps.img; im.alt=ps.label;
      im.style.cssText='width:100px;height:100px;object-fit:cover;border-radius:12px;pointer-events:none;';
      im.onerror=()=>{im.style.display='none';};
      slot.appendChild(im);
      const lb=document.createElement('div');
      lb.style.cssText='font-family:Fredoka One,cursive;font-size:.55rem;color:#006978;text-align:center;margin-top:3px;padding:0 4px;line-height:1.3;pointer-events:none;';
      lb.textContent=ps.label; slot.appendChild(lb);

      // Remove button
      const rm=document.createElement('button');
      rm.className='slot-remove-btn'; rm.innerHTML='✕';
      rm.onclick=(e)=>{e.stopPropagation();hw_removeFromSlot(pos);};
      slot.appendChild(rm);

      // Drag FROM slot
      slot.draggable=true;
      slot.addEventListener('dragstart',e=>{
        hw_dragFromSlot=pos; hw_dragSrcIdx=null;
        e.dataTransfer.setData('text/plain','fromslot');
        slot.style.opacity='.5';
      });
      slot.addEventListener('dragend',()=>{ slot.style.opacity=''; hw_dragFromSlot=null; });
    } else {
      // Empty slot placeholder
      const ph=document.createElement('div');
      ph.style.cssText='color:#b2ebf2;font-size:2rem;';
      ph.textContent='?'; slot.appendChild(ph);
    }

    // Drop targets
    slot.addEventListener('dragover',e=>{e.preventDefault();slot.classList.add('drag-over-slot');});
    slot.addEventListener('dragleave',()=>slot.classList.remove('drag-over-slot'));
    slot.addEventListener('drop',e=>{
      e.preventDefault();
      slot.classList.remove('drag-over-slot');
      if(hw_dragSrcIdx!==null){
        // From choices area
        const si=hw_dragSrcIdx;
        if(hw_placed[pos]!==null){
          // Swap: put existing back (find which choice card)
          hw_placed[pos]=si;
        } else {
          hw_placed[pos]=si;
        }
        hw_dragSrcIdx=null;
      } else if(hw_dragFromSlot!==null && hw_dragFromSlot!==pos){
        // From another slot — swap
        const tmp=hw_placed[pos];
        hw_placed[pos]=hw_placed[hw_dragFromSlot];
        hw_placed[hw_dragFromSlot]=tmp;
        hw_dragFromSlot=null;
      }
      hw_render();
      hw_checkSubmitReady();
    });

    slotsEl.appendChild(slot);
  });

  // Zone-level drop listeners are set once in hw_nextRound to avoid stacking

  // ── CHOICES ──
  const choicesEl=document.getElementById('p1Choices');
  choicesEl.innerHTML='';
  const usedSet=new Set(hw_placed.filter(v=>v!==null));

  hw_shuffled.forEach(si=>{
    const s=steps[si];
    const card=document.createElement('div');
    card.className='step-choice-card'+(usedSet.has(si)?' used':'');
    card.draggable=!usedSet.has(si);
    card.id='choice_'+si;

    const im=document.createElement('img');
    im.src=s.img; im.alt=s.label;
    im.style.cssText='width:100px;height:100px;object-fit:cover;border-radius:12px;pointer-events:none;';
    im.onerror=()=>{im.style.display='none';};
    card.appendChild(im);

    const lb=document.createElement('div');
    lb.className='step-choice-label'; lb.textContent=s.label; card.appendChild(lb);

    // Drag
    card.addEventListener('dragstart',e=>{
      hw_dragSrcIdx=si; hw_dragFromSlot=null;
      e.dataTransfer.setData('text/plain','fromchoice');
      card.classList.add('dragging');
    });
    card.addEventListener('dragend',()=>{card.classList.remove('dragging');hw_dragSrcIdx=null;});

    // Touch / click fallback (tap to place in first empty slot)
    card.addEventListener('click',()=>{
      if(usedSet.has(si)) return;
      const firstEmpty=hw_placed.indexOf(null);
      if(firstEmpty!==-1){ hw_placed[firstEmpty]=si; hw_render(); hw_checkSubmitReady(); }
    });

    choicesEl.appendChild(card);
  });
}

function hw_removeFromSlot(pos){
  hw_placed[pos]=null;
  hw_render();
  hw_checkSubmitReady();
}

function hw_checkSubmitReady(){
  const allFilled=hw_placed.every(v=>v!==null);
  document.getElementById('p1SubmitBtn').disabled=!allFilled;
}

function hw_submit(){
  const act=hw_currentActivity();
  const steps=act.steps;
  const correctCount=hw_placed.filter((si,pos)=>si===pos).length;
  const stepsCount=act.steps.length;
  // Always 20pts per round: 5 steps × 4pts = 20, or 4 steps × 5pts = 20
  const ptsPerStep = stepsCount === 4 ? 5 : 4;
  const roundScore=correctCount*ptsPerStep;
  hw_score+=roundScore;
  document.getElementById('p1Score').textContent=hw_score;
  const hwMax=100; // always 5 rounds × 20pts = 100
  saveProgress(ACT_IDS[1], Math.min(100, Math.round((hw_score/hwMax)*100)));

  // Highlight slots
  const slotsEl=document.getElementById('p1Slots');
  hw_placed.forEach((si,pos)=>{
    const slot=document.getElementById('slot_'+pos);
    if(!slot) return;
    slot.classList.remove('filled');
    if(si===pos){ slot.classList.add('correct-slot'); }
    else { slot.classList.add('wrong-slot'); }
  });

  if(correctCount===steps.length){
    document.getElementById('p1Feedback').textContent=lang==='en'?'🎉 Perfect! All steps correct! +'+roundScore+' pts':'🎉 Perpekto! Lahat tama! +'+roundScore+' pts';
    document.getElementById('p1Feedback').className='feedback-msg correct';
    hw_roundDone[hw_round-1]=true;
    confetti('#00acc1');
  } else {
    document.getElementById('p1Feedback').textContent=lang==='en'?correctCount+' of '+steps.length+' correct. +'+roundScore+' pts — Keep going!':correctCount+' sa '+steps.length+' ang tama. +'+roundScore+' pts — Patuloy lang!';
    document.getElementById('p1Feedback').className='feedback-msg wrong';
    hw_roundDone[hw_round-1]='skipped';
  }

  hw_updateRoundDots();
  document.getElementById('p1SubmitBtn').style.display='none';
  if(hw_round<HW_TOTAL_ROUNDS){
    document.getElementById('p1NextBtn').style.display='inline-flex';
    document.getElementById('p1NextLbl').textContent=lang==='en'?'Next Round':'Susunod na Round';
  } else {
    setTimeout(()=>hw_showResult(),1400);
  }
}

function hw_showResult(){
  showResult(1,hw_score,100); // 5 rounds × 20pts = 100 max
}

// ════════════════════════════════════════════════
// ACTIVITY 2 — HYGIENE MATCH (Multi-Round)
// 4 rounds × 6 pairs = derived from STEP_ACTIVITIES
// Column A: Activity name | Column B: Step image + description
// Submit-based validation (no auto-checking)
// 10 pts per correct pair
// ════════════════════════════════════════════════

// Build HM_ROUNDS: 5 rounds × 4 pairs each
// Each round picks 4 activities, one step per activity
function buildHMRounds(){
  const acts=STEP_ACTIVITIES[lang];
  const rounds=[];
  // Use first 4 activities for rounds 1-4, mix for round 5
  const actGroups=[
    [0,1,2,3],   // round 1: acts 0-3, step 0
    [0,1,2,3],   // round 2: acts 0-3, step 1
    [4,5,0,1],   // round 3: acts 4-5 + 0-1, step 2
    [2,3,4,5],   // round 4: acts 2-5, step 3
    [0,2,4,1],   // round 5: mix
  ];
  for(let r=0;r<5;r++){
    const pairs=[];
    actGroups[r].forEach((ai,pi)=>{
      const act=acts[ai];
      const stepIdx=Math.min(r, act.steps.length-1);
      const step=act.steps[stepIdx];
      pairs.push({
        activityName: act.name,
        activityIcon: act.icon,
        stepImg: step.img,
        stepLabel: step.label,
        pairId: pi   // 0-3 within this round
      });
    });
    rounds.push(pairs);
  }
  return rounds;
}

const HM_TOTAL_ROUNDS=5;
const HM_PAIRS_PER_ROUND=4;
const HM_PPM=5; // +5 pts per correct pair, no deduction
const HM_MAX=HM_TOTAL_ROUNDS*HM_PAIRS_PER_ROUND*HM_PPM; // 5×4×5=100

// Pair colors: each match number gets a unique border+badge color
const HM_PAIR_COLORS=[
  {border:'#2196f3', bg:'#e3f2fd', text:'#0d47a1', name:'blue'},
  {border:'#4caf50', bg:'#e8f5e9', text:'#1b5e20', name:'green'},
  {border:'#ff9800', bg:'#fff3e0', text:'#e65100', name:'orange'},
  {border:'#e91e63', bg:'#fce4ec', text:'#880e4f', name:'pink'},
  {border:'#9c27b0', bg:'#f3e5f5', text:'#4a148c', name:'purple'},
  {border:'#009688', bg:'#e0f2f1', text:'#004d40', name:'teal'},
];

let hm_started=false, hm_score=0, hm_round=0;
let hm_roundDone=[];
let hm_currentPairs=[];
// hm_userMatches: { aPairId: bPairId }  — confirmed pairings
let hm_userMatches={};
// hm_matchOrder: [aPairId, ...] in the order they were paired → determines color/number
let hm_matchOrder=[];
// Pending selection: one from each column
let hm_selA=null; // aPairId currently highlighted
let hm_selB=null; // bPairId currently highlighted
// Shuffled display orders
let hm_orderA=[], hm_orderB=[];
// Locked after submit
let hm_submitted=false;

function hm_startGame(){
  hm_started=true; hm_score=0; hm_round=0;
  hm_roundDone=Array(HM_TOTAL_ROUNDS).fill(null);
  document.getElementById('p2Score').textContent='0';
  document.getElementById('p2Bar').style.width='0%';
  document.getElementById('p2BarLbl').textContent='Round 1 of '+HM_TOTAL_ROUNDS;
  document.getElementById('p2Feedback').textContent='';
  document.getElementById('p2Feedback').className='feedback-msg';
  document.getElementById('p2SubmitBtn').style.display='inline-flex';
  document.getElementById('p2SubmitBtn').disabled=true;
  document.getElementById('p2NextBtn').style.display='none';
  saveStart(ACT_IDS[2]);
  hm_buildRoundDots();
  hm_nextRound();
}

function hm_buildRoundDots(){
  const el=document.getElementById('p2RoundDots');
  el.innerHTML='';
  for(let i=0;i<HM_TOTAL_ROUNDS;i++){
    const d=document.createElement('div');
    d.className='round-dot'+(i===hm_round-1?' rd-active':'');
    d.id='hm_rdot_'+i;
    d.textContent=i+1;
    el.appendChild(d);
  }
}

function hm_updateRoundDots(){
  for(let i=0;i<HM_TOTAL_ROUNDS;i++){
    const d=document.getElementById('hm_rdot_'+i);
    if(!d) continue;
    d.className='round-dot';
    if(hm_roundDone[i]===true) d.classList.add('rd-done');
    else if(hm_roundDone[i]==='skipped') d.classList.add('rd-skipped');
    else if(i===hm_round-1) d.classList.add('rd-active');
  }
}

function hm_nextRound(){
  hm_round++;
  if(hm_round>HM_TOTAL_ROUNDS){ hm_showResult(); return; }
  hm_selA=null; hm_selB=null;
  hm_userMatches={}; hm_matchOrder=[];
  hm_submitted=false;
  const allRounds=buildHMRounds();
  hm_currentPairs=allRounds[hm_round-1];
  hm_orderA=[...Array(HM_PAIRS_PER_ROUND).keys()].sort(()=>Math.random()-.5);
  hm_orderB=[...Array(HM_PAIRS_PER_ROUND).keys()].sort(()=>Math.random()-.5);

  document.getElementById('p2RoundCur').textContent=hm_round;
  document.getElementById('p2BarLbl').textContent='Round '+hm_round+' of '+HM_TOTAL_ROUNDS;
  document.getElementById('p2Bar').style.width=((hm_round-1)/HM_TOTAL_ROUNDS*100)+'%';
  document.getElementById('p2Feedback').textContent='';
  document.getElementById('p2Feedback').className='feedback-msg';
  document.getElementById('p2SubmitBtn').style.display='inline-flex';
  document.getElementById('p2SubmitBtn').disabled=true;
  document.getElementById('p2NextBtn').style.display='none';
  document.getElementById('p2PendingDisplay').style.display='none';
  document.getElementById('p2Prompt').textContent=lang==='en'
    ?'Tap a card from Column A, then tap a card from Column B to pair them. Match all 4 pairs, then click Submit!'
    :'I-tap ang isang card mula Column A, tapos isa mula Column B para ipares. I-match ang lahat ng 4, tapos i-click ang Submit!';

  hm_buildRoundDots();
  hm_updateRoundDots();
  hm_renderRound();
}

/* ── Core render: rebuilds both columns with color+number badges ── */
function hm_renderRound(){
  const colAEl=document.getElementById('p2ColA');
  const colBEl=document.getElementById('p2ColB');
  colAEl.innerHTML=''; colBEl.innerHTML='';

  /* helper: get pair info for a given aPairId */
  function getPairInfo(aPairId){
    const matchIdx=hm_matchOrder.indexOf(aPairId);
    if(matchIdx===-1) return null;
    return { num: matchIdx+1, color: HM_PAIR_COLORS[matchIdx % HM_PAIR_COLORS.length] };
  }

  /* helper: get pair info for a given bPairId */
  function getPairInfoB(bPairId){
    const aPairId=Object.entries(hm_userMatches).find(([a,b])=>parseInt(b)===bPairId)?.[0];
    if(aPairId===undefined) return null;
    return getPairInfo(parseInt(aPairId));
  }

  /* build number badge element */
  function makeBadge(num, color){
    const badge=document.createElement('div');
    badge.style.cssText=`
      position:absolute; top:-10px; right:-10px;
      width:26px; height:26px; border-radius:50%;
      background:${color.border}; color:#fff;
      font-family:'Fredoka One',cursive; font-size:.82rem;
      display:flex; align-items:center; justify-content:center;
      box-shadow:0 2px 8px rgba(0,0,0,.22); z-index:3;
      border:2px solid #fff; line-height:1;
    `;
    badge.textContent=num;
    return badge;
  }

  /* ── COLUMN A ── */
  hm_orderA.forEach(pairIdx=>{
    const pair=hm_currentPairs[pairIdx];
    const info=getPairInfo(pairIdx);
    const isPaired=info!==null;
    const isSelA=hm_selA===pairIdx;

    const el=document.createElement('div');
    el.className='match-card hm-col-a-card';
    el.id='hm_a_'+pairIdx;
    el.dataset.pairId=pairIdx;
    el.style.position='relative';

    /* border coloring */
    if(isPaired){
      el.style.borderColor=info.color.border;
      el.style.background=info.color.bg;
      el.style.boxShadow=`0 4px 16px ${info.color.border}44`;
    } else if(isSelA){
      el.style.borderColor='#f06292';
      el.style.background='#fce4ec';
      el.style.boxShadow='0 4px 16px rgba(240,98,146,.25)';
    }

    /* number badge */
    if(isPaired) el.appendChild(makeBadge(info.num, info.color));

    /* icon */
    const iconEl=document.createElement('div');
    iconEl.style.cssText='font-size:2.6rem;line-height:1;margin-bottom:6px;';
    iconEl.textContent=pair.activityIcon;
    el.appendChild(iconEl);

    /* label */
    const lb=document.createElement('div');
    lb.className='match-label';
    lb.style.cssText='font-family:Fredoka One,cursive;font-size:.95rem;color:#006978;font-weight:700;';
    lb.textContent=pair.activityName;
    el.appendChild(lb);

    /* click: if locked after submit, ignore */
    el.onclick=()=>{ if(!hm_submitted) hm_clickA(pairIdx); };
    colAEl.appendChild(el);
  });

  /* ── COLUMN B ── */
  hm_orderB.forEach(bPairIdx=>{
    const pair=hm_currentPairs[bPairIdx];
    const info=getPairInfoB(bPairIdx);
    const isPaired=info!==null;
    const isSelB=hm_selB===bPairIdx;

    const el=document.createElement('div');
    el.className='match-card hm-col-b-card';
    el.id='hm_b_'+bPairIdx;
    el.dataset.pairId=bPairIdx;
    el.style.position='relative';
    el.style.minHeight='155px';

    /* border coloring */
    if(isPaired){
      el.style.borderColor=info.color.border;
      el.style.background=info.color.bg;
      el.style.boxShadow=`0 4px 16px ${info.color.border}44`;
    } else if(isSelB){
      el.style.borderColor='#f06292';
      el.style.background='#fce4ec';
      el.style.boxShadow='0 4px 16px rgba(240,98,146,.25)';
    }

    /* number badge */
    if(isPaired) el.appendChild(makeBadge(info.num, info.color));

    /* image */
    if(pair.stepImg){
      const im=document.createElement('img');
      im.src=pair.stepImg; im.alt=pair.stepLabel;
      im.style.cssText='width:100px;height:100px;object-fit:cover;border-radius:14px;box-shadow:0 3px 12px rgba(0,0,0,.14);margin-bottom:8px;display:block;margin-left:auto;margin-right:auto;';
      im.onerror=()=>{ im.style.display='none'; };
      el.appendChild(im);
    }

    /* label */
    const lb=document.createElement('div');
    lb.className='match-label';
    lb.style.cssText='font-family:Fredoka One,cursive;font-size:.82rem;color:#555;padding:0 6px;line-height:1.3;';
    lb.textContent=pair.stepLabel;
    el.appendChild(lb);

    el.onclick=()=>{ if(!hm_submitted) hm_clickB(bPairIdx); };
    colBEl.appendChild(el);
  });

  /* update submit button */
  const allPaired=Object.keys(hm_userMatches).length>=HM_PAIRS_PER_ROUND;
  document.getElementById('p2SubmitBtn').disabled=!allPaired || hm_submitted;

  /* update pair count display */
  hm_updatePendingDisplay();
}

/* ── Click handler for Column A ── */
function hm_clickA(pairIdx){
  const alreadyPaired=hm_userMatches[pairIdx]!==undefined;
  if(alreadyPaired){
    /* clicking a paired A card → unmatch it so both cards become free */
    const bPairId=hm_userMatches[pairIdx];
    hm_matchOrder=hm_matchOrder.filter(id=>id!==pairIdx);
    delete hm_userMatches[pairIdx];
    /* if this was also the pending B selection, clear it */
    if(hm_selB===bPairId) hm_selB=null;
    hm_selA=pairIdx; /* auto-select this A for convenience */
    hm_renderRound();
    return;
  }
  /* toggle selection */
  hm_selA=(hm_selA===pairIdx) ? null : pairIdx;
  /* if both A and B are now selected, auto-pair them */
  if(hm_selA!==null && hm_selB!==null) hm_autoPair();
  else hm_renderRound();
}

/* ── Click handler for Column B ── */
function hm_clickB(bPairIdx){
  /* check if this B is already part of a pair */
  const existingA=Object.entries(hm_userMatches).find(([a,b])=>parseInt(b)===bPairIdx);
  if(existingA){
    /* clicking a paired B card → unmatch it */
    const aPairId=parseInt(existingA[0]);
    hm_matchOrder=hm_matchOrder.filter(id=>id!==aPairId);
    delete hm_userMatches[aPairId];
    if(hm_selA===aPairId) hm_selA=null;
    hm_selB=bPairIdx; /* auto-select this B for convenience */
    hm_renderRound();
    return;
  }
  hm_selB=(hm_selB===bPairIdx) ? null : bPairIdx;
  if(hm_selA!==null && hm_selB!==null) hm_autoPair();
  else hm_renderRound();
}

/* ── Auto-pair when both A and B are selected ── */
function hm_autoPair(){
  const a=hm_selA, b=hm_selB;
  hm_selA=null; hm_selB=null;
  /* record the pair */
  hm_userMatches[a]=b;
  if(!hm_matchOrder.includes(a)) hm_matchOrder.push(a);
  hm_renderRound();
}

/* ── Status display below columns ── */
function hm_updatePendingDisplay(){
  const disp=document.getElementById('p2PendingDisplay');
  const total=Object.keys(hm_userMatches).length;
  const needed=HM_PAIRS_PER_ROUND-total;

  if(hm_selA!==null || hm_selB!==null){
    /* show which side is waiting */
    let txt='';
    if(hm_selA!==null && hm_selB===null)
      txt='✅ '+(lang==='en'?'Selected from Column A — now tap a card from Column B!':'Napili sa Column A — i-tap na ang isang card sa Column B!');
    else if(hm_selB!==null && hm_selA===null)
      txt='✅ '+(lang==='en'?'Selected from Column B — now tap a card from Column A!':'Napili sa Column B — i-tap na ang isang card sa Column A!');
    disp.textContent=txt;
    disp.style.display='block';
  } else if(needed>0){
    disp.textContent=(lang==='en'
      ?'📌 '+total+' of '+HM_PAIRS_PER_ROUND+' pairs matched — '+needed+' more to go!'
      :'📌 '+total+' sa '+HM_PAIRS_PER_ROUND+' na pairs ang natugma — '+needed+' pa!');
    disp.style.display='block';
  } else {
    disp.textContent=(lang==='en'
      ?'🎉 All '+HM_PAIRS_PER_ROUND+' pairs matched! Click Submit Answer to check!'
      :'🎉 Lahat ng '+HM_PAIRS_PER_ROUND+' pairs ay natugma na! I-click ang Submit Answer!');
    disp.style.display='block';
  }
}

/* ── SUBMIT: evaluate all pairs at once (one attempt per round) ── */
function hm_submit(){
  if(hm_submitted) return;
  if(Object.keys(hm_userMatches).length<HM_PAIRS_PER_ROUND) return;
  hm_submitted=true;

  let correct=0;
  for(const [aPairId, bPairId] of Object.entries(hm_userMatches)){
    if(parseInt(aPairId)===parseInt(bPairId)) correct++;
  }
  const roundScore=correct*HM_PPM; // +5 per correct only, no deduction
  hm_score+=roundScore;
  document.getElementById('p2Score').textContent=hm_score;
  document.getElementById('p2Bar').style.width=(hm_round/HM_TOTAL_ROUNDS*100)+'%';
  saveProgress(ACT_IDS[2], Math.min(100,Math.round((hm_score/HM_MAX)*100)));

  /* show correct/wrong visual on each card pair */
  hm_matchOrder.forEach((aPairId,idx)=>{
    const bPairId=hm_userMatches[aPairId];
    const isCorrect=parseInt(aPairId)===parseInt(bPairId);
    const elA=document.getElementById('hm_a_'+aPairId);
    const elB=document.getElementById('hm_b_'+bPairId);
    const indicator=isCorrect?'✅':'❌';
    const overlayStyle=isCorrect
      ?'border-color:#40c057!important;background:#f0fdf4!important;'
      :'border-color:#ff4444!important;background:#fff5f5!important;';
    [elA,elB].forEach(el=>{
      if(!el) return;
      el.style.cssText+=(isCorrect
        ?';border-color:#40c057;background:#f0fdf4;box-shadow:0 4px 16px rgba(64,192,87,.3);'
        :';border-color:#ff4444;background:#fff5f5;box-shadow:0 4px 16px rgba(255,68,68,.25);');
      /* add result indicator */
      const ind=document.createElement('div');
      ind.style.cssText='font-size:1.3rem;margin-top:5px;';
      ind.textContent=indicator;
      el.appendChild(ind);
    });
  });

  /* feedback + buttons */
  if(correct===HM_PAIRS_PER_ROUND){
    document.getElementById('p2Feedback').textContent=lang==='en'?'🎉 Perfect! All '+HM_PAIRS_PER_ROUND+' pairs correct! +'+roundScore+' pts':'🎉 Perpekto! Lahat ng '+HM_PAIRS_PER_ROUND+' pairs tama! +'+roundScore+' pts';
    document.getElementById('p2Feedback').className='feedback-msg correct';
    hm_roundDone[hm_round-1]=true;
    confetti('#f06292');
  } else {
    document.getElementById('p2Feedback').textContent=lang==='en'?correct+'/'+HM_PAIRS_PER_ROUND+' correct! +'+roundScore+' pts earned.':correct+'/'+HM_PAIRS_PER_ROUND+' tama! +'+roundScore+' pts nakuha.';
    document.getElementById('p2Feedback').className=roundScore>0?'feedback-msg correct':'feedback-msg wrong';
    hm_roundDone[hm_round-1]='skipped';
  }

  /* hide pending display */
  document.getElementById('p2PendingDisplay').style.display='none';
  document.getElementById('p2SubmitBtn').style.display='none';
  hm_updateRoundDots();

  if(hm_round<HM_TOTAL_ROUNDS){
    document.getElementById('p2NextBtn').style.display='inline-flex';
    document.getElementById('p2NextLbl').textContent=lang==='en'?'Next Round':'Susunod na Round';
  } else {
    setTimeout(()=>hm_showResult(),1600);
  }
}

function hm_showResult(){
  showResult(2, hm_score, HM_MAX);
}

// ════════════════════════════════════════════════
// ACTIVITY 3 — DAILY ROUTINE
// ════════════════════════════════════════════════
// ════════════════════════════════════════════════
// ACTIVITY 3 — DAILY ROUTINE
// Questions aligned to all 6 lessons in motorskills.php
// ════════════════════════════════════════════════
const DR_QUESTIONS = {
  en:[
    // Lesson 1: Handwashing
    {emoji:'🧼',q:'You are about to eat lunch. What must you do first?',choices:['🎮 Play games','🧼 Wash your hands','📺 Watch TV','😴 Take a nap'],correct:1, img:'pictures/motorskills/handwash_step3.jpg'},
    {emoji:'🚽',q:'You just used the bathroom. What is the NEXT thing to do?',choices:['🏃 Run outside','🧼 Wash your hands','🍕 Eat pizza','📱 Use your phone'],correct:1, img:'pictures/motorskills/handwash_step4.jpg'},
    // Lesson 2: Tooth Brushing
    {emoji:'🌅',q:'You just woke up in the morning. What should you do?',choices:['🎮 Play games','📺 Watch TV','🪥 Brush your teeth','😴 Sleep more'],correct:2, img:'pictures/motorskills/brush_step1.jpg'},
    {emoji:'🌙',q:'It is bedtime. What should you do before sleeping?',choices:['🎮 Play video games','🍫 Eat candy','🪥 Brush your teeth','📺 Watch TV'],correct:2, img:'pictures/motorskills/brush_step5.jpg'},
    // Lesson 3: Bathing
    {emoji:'🏫',q:'You just came home from school feeling sweaty. What should you do?',choices:['😴 Sleep right away','🎮 Play games','🛁 Take a bath','🍔 Eat first'],correct:2, img:'pictures/motorskills/bath_step2.jpg'},
    {emoji:'🛁',q:'When bathing, what do you use to wash your hair?',choices:['Sabon/Soap','Shampoo','Only water','Toothpaste'],correct:1, img:'pictures/motorskills/bath_step4.jpg'},
    // Lesson 4: Hair Combing
    {emoji:'💆',q:'Your hair is tangled in the morning. Where should you START combing?',choices:['At the top (roots)','At the middle','At the tips/ends first','Anywhere is fine'],correct:2, img:'pictures/motorskills/comb_step2.jpg'},
    // Lesson 5: Nail Trimming
    {emoji:'✂️',q:'Your nails are getting very long. What should you do?',choices:['🎀 Decorate them','✂️ Trim them short and clean','💅 Paint them','🙈 Ignore them'],correct:1, img:'pictures/motorskills/nails_step3.jpg'},
    // Lesson 6: Getting Dressed
    {emoji:'👕',q:'You are getting dressed. What do you put on FIRST?',choices:['Shoes','Jacket','Underwear/undershirt','School bag'],correct:2, img:'pictures/motorskills/clothes_step3.jpg'},
    {emoji:'🏫',q:'You are going to school. What should you wear?',choices:['😴 Pajamas','🧹 Dirty clothes','👕 Clean uniform','🎃 Halloween costume'],correct:2, img:'pictures/motorskills/clothes_step1.jpg'},
  ],
  tl:[
    // Lesson 1: Handwashing
    {emoji:'🧼',q:'Malapit ka nang kumain. Ano ang dapat mong gawin muna?',choices:['🎮 Maglaro','🧼 Maghugas ng kamay','📺 Manood ng TV','😴 Matulog muna'],correct:1, img:'pictures/motorskills/handwash_step3.jpg'},
    {emoji:'🚽',q:'Kagamit mo lang ng banyo. Ano ang susunod mong gagawin?',choices:['🏃 Tumakbo labas','🧼 Maghugas ng kamay','🍕 Kumain ng pizza','📱 Gumamit ng cellphone'],correct:1, img:'pictures/motorskills/handwash_step4.jpg'},
    // Lesson 2: Tooth Brushing
    {emoji:'🌅',q:'Kagagising mo lang sa umaga. Ano ang dapat mong gawin?',choices:['🎮 Maglaro','📺 Manood ng TV','🪥 Magsipilyo ng ngipin','😴 Matulog pa'],correct:2, img:'pictures/motorskills/brush_step1.jpg'},
    {emoji:'🌙',q:'Oras na para matulog. Ano ang dapat mong gawin bago matulog?',choices:['🎮 Maglaro','🍫 Kumain ng kendi','🪥 Magsipilyo ng ngipin','📺 Manood ng TV'],correct:2, img:'pictures/motorskills/brush_step5.jpg'},
    // Lesson 3: Bathing
    {emoji:'🏫',q:'Nanggaling ka sa paaralan at pawis na pawis ka. Ano ang dapat gawin?',choices:['😴 Matulog agad','🎮 Maglaro','🛁 Maligo','🍔 Kumain muna'],correct:2, img:'pictures/motorskills/bath_step2.jpg'},
    {emoji:'🛁',q:'Habang naliligo, ano ang ginagamit para hugasan ang buhok?',choices:['Sabon','Shampoo','Tubig lang','Toothpaste'],correct:1, img:'pictures/motorskills/bath_step4.jpg'},
    // Lesson 4: Hair Combing
    {emoji:'💆',q:'Gusot ang iyong buhok sa umaga. Saan dapat SIMULAN ang pagsusuklay?',choices:['Sa itaas (ugat)','Sa gitna','Sa dulo/tips muna','Kahit saan'],correct:2, img:'pictures/motorskills/comb_step2.jpg'},
    // Lesson 5: Nail Trimming
    {emoji:'✂️',q:'Napakahabab na ng iyong mga kuko. Ano ang dapat mong gawin?',choices:['🎀 Lagyan ng dekorasyon','✂️ Gupitin at linisin','💅 Pinturahan','🙈 Huwag pansinin'],correct:1, img:'pictures/motorskills/nails_step3.jpg'},
    // Lesson 6: Getting Dressed
    {emoji:'👕',q:'Nagbibihis ka na. Ano ang UNANG isinusuot?',choices:['Sapatos','Jacket','Panloob na damit/sando','School bag'],correct:2, img:'pictures/motorskills/clothes_step3.jpg'},
    {emoji:'🏫',q:'Pupunta ka sa paaralan. Ano ang dapat mong isuot?',choices:['😴 Pajama','🧹 Maruming damit','👕 Malinis na uniporme','🎃 Halloween costume'],correct:2, img:'pictures/motorskills/clothes_step1.jpg'},
  ]
};
const DR_TOTAL=10, DR_MAX=DR_TOTAL*10;
let dr_score=0, dr_qIdx=0, dr_answered=false, dr_pool=[], dr_selectedChoice=null;

function dr_startGame(){
  dr_score=0; dr_qIdx=0; dr_answered=false; dr_selectedChoice=null;
  dr_pool=[...Array(DR_TOTAL).keys()].sort(()=>Math.random()-.5);
  document.getElementById('p3Score').textContent='0';
  document.getElementById('p3Bar').style.width='0%';
  document.getElementById('p3BarLbl').textContent='0 / '+DR_TOTAL;
  document.getElementById('p3Feedback').textContent='';
  document.getElementById('p3Feedback').className='feedback-msg';
  document.getElementById('p3SubmitBtn').style.display='inline-flex';
  document.getElementById('p3SubmitBtn').disabled=true;
  document.getElementById('p3NextBtn').style.display='none';
  saveStart(ACT_IDS[3]);
  dr_render();
}
function dr_render(){
  const q=DR_QUESTIONS[lang][dr_pool[dr_qIdx]];
  dr_answered=false; dr_selectedChoice=null;
  // Show image or emoji
  const emojiEl=document.getElementById('p3Emoji');
  if(q.img){
    emojiEl.innerHTML='';
    const im=document.createElement('img');
    im.src=q.img; im.alt=q.q;
    im.style.cssText='width:140px;height:140px;object-fit:cover;border-radius:18px;box-shadow:0 4px 16px rgba(0,0,0,.12);';
    im.onerror=()=>{ emojiEl.innerHTML=''; emojiEl.textContent=q.emoji; };
    emojiEl.appendChild(im);
  } else {
    emojiEl.innerHTML='';
    emojiEl.textContent=q.emoji;
  }
  document.getElementById('p3QText').textContent=q.q;
  document.getElementById('p3Sub2').textContent=lang==='en'?'What should you do?':'Ano ang dapat gawin?';
  document.getElementById('p3QNum').textContent=dr_qIdx+1;
  document.getElementById('p3BarLbl').textContent=dr_qIdx+' / '+DR_TOTAL;
  document.getElementById('p3Bar').style.width=(dr_qIdx/DR_TOTAL*100)+'%';
  document.getElementById('p3Feedback').textContent='';
  document.getElementById('p3Feedback').className='feedback-msg';
  document.getElementById('p3SubmitBtn').style.display='inline-flex';
  document.getElementById('p3SubmitBtn').disabled=true;
  document.getElementById('p3NextBtn').style.display='none';
  const choicesEl=document.getElementById('p3Choices');
  choicesEl.innerHTML='';
  const letters=['A','B','C','D'];
  const colors=['#00acc1','#f06292','#fcc419','#66bb6a'];
  q.choices.forEach((c,i)=>{
    const btn=document.createElement('button');
    btn.className='choice-btn';
    btn.dataset.idx=i;
    btn.innerHTML=`<div class="choice-letter" style="background:${colors[i]}">${letters[i]}</div><div class="choice-text">${c}</div>`;
    btn.onclick=()=>dr_selectChoice(btn, i);
    choicesEl.appendChild(btn);
  });
}
function dr_selectChoice(btn, idx){
  if(dr_answered) return;
  // Deselect all
  document.querySelectorAll('#p3Choices .choice-btn').forEach(b=>{
    b.classList.remove('selected-choice');
    b.style.borderColor='';
    b.style.background='';
  });
  // Select this one
  btn.classList.add('selected-choice');
  btn.style.borderColor='#00acc1';
  btn.style.background='#e0f7fa';
  dr_selectedChoice=idx;
  document.getElementById('p3SubmitBtn').disabled=false;
}
function dr_submit(){
  if(dr_answered || dr_selectedChoice===null) return;
  dr_answered=true;
  const q=DR_QUESTIONS[lang][dr_pool[dr_qIdx]];
  const isCorrect=dr_selectedChoice===q.correct;
  document.querySelectorAll('#p3Choices .choice-btn').forEach(b=>b.classList.add('answered'));
  const allBtns=document.querySelectorAll('#p3Choices .choice-btn');
  // Highlight correct and wrong
  allBtns.forEach(b=>{
    const idx=parseInt(b.dataset.idx);
    if(idx===q.correct){ b.classList.add('correct'); b.style.borderColor=''; b.style.background=''; }
    else if(idx===dr_selectedChoice && !isCorrect){ b.classList.add('wrong'); b.style.borderColor=''; b.style.background=''; }
    else { b.style.borderColor=''; b.style.background=''; }
  });
  if(isCorrect){
    dr_score+=10;
    document.getElementById('p3Score').textContent=dr_score;
    document.getElementById('p3Feedback').textContent=lang==='en'?'✅ Correct! Great thinking!':'✅ Tama! Napakahusay!';
    document.getElementById('p3Feedback').className='feedback-msg correct';
    saveProgress(ACT_IDS[3], Math.round((dr_score/DR_MAX)*100));
    confetti('#fcc419');
  } else {
    document.getElementById('p3Feedback').textContent=lang==='en'?`❌ The answer was: ${q.choices[q.correct]}`:`❌ Ang sagot ay: ${q.choices[q.correct]}`;
    document.getElementById('p3Feedback').className='feedback-msg wrong';
  }
  dr_qIdx++;
  document.getElementById('p3SubmitBtn').style.display='none';
  if(dr_qIdx>=DR_TOTAL) setTimeout(()=>showResult(3,dr_score,DR_MAX),1000);
  else document.getElementById('p3NextBtn').style.display='inline-flex';
}
function dr_nextQuestion(){ dr_render(); }

// ════════════════════════════════════════════════
// ACTIVITY 4 — BODY CARE QUIZ
// ════════════════════════════════════════════════
const BC_QUESTIONS = {
  en:[
    // Lesson 1: Handwashing
    {emoji:'🧼', img:'pictures/motorskills/handwash_step3.jpg', q:'How long should you SCRUB your hands when washing?',choices:['5 seconds','10 seconds','20 seconds','1 minute'],correct:2},
    {emoji:'🚿', img:'pictures/motorskills/handwash_step4.jpg', q:'After scrubbing, what is the NEXT step in handwashing?',choices:['Dry your hands','Rinse off the soap','Apply more soap','Turn off the tap'],correct:1},
    // Lesson 2: Tooth Brushing
    {emoji:'🪥', img:'pictures/motorskills/brush_step1.jpg',   q:'How many times a day should you brush your teeth?',choices:['Once a week','Once a day','Twice a day','Never'],correct:2},
    {emoji:'👅', img:'pictures/motorskills/brush_step4.jpg',   q:'Why do you also brush your tongue?',choices:['To make it pink','To remove bacteria causing bad breath','Because it tastes good','To whiten teeth'],correct:1},
    // Lesson 3: Bathing
    {emoji:'🛁', img:'pictures/motorskills/bath_step1.jpg',    q:'Before you bathe, what must you prepare first?',choices:['Your toys','Soap, shampoo, and a clean towel','Your school bag','A glass of juice'],correct:1},
    {emoji:'💆', img:'pictures/motorskills/bath_step4.jpg',    q:'When washing your hair during a bath, what do you use?',choices:['Soap only','Shampoo','Toothpaste','Body lotion'],correct:1},
    // Lesson 4: Hair Combing
    {emoji:'🪮', img:'pictures/motorskills/comb_step2.jpg',    q:'Where should you START combing to avoid pain?',choices:['At the roots (top)','At the middle','At the tips/ends first','It does not matter'],correct:2},
    {emoji:'💇', img:'pictures/motorskills/comb_step1.jpg',    q:'Why should you use YOUR OWN comb and not borrow?',choices:['It looks nicer','To avoid spreading lice and infections','It combs better','To save money'],correct:1},
    // Lesson 5: Nail Trimming
    {emoji:'✂️', img:'pictures/motorskills/nails_step3.jpg',   q:'What is the correct way to cut your nails?',choices:['As short as possible','Straight across, level with fingertip','Cut the sides deeply','Cut only one hand'],correct:1},
    {emoji:'🧹', img:'pictures/motorskills/nails_step4.jpg',   q:'After trimming nails, what should you do next?',choices:['Paint them right away','Clean under the nails with a brush','Leave them as they are','Put lotion on them'],correct:1},
    // Lesson 6: Getting Dressed
    {emoji:'👕', img:'pictures/motorskills/clothes_step3.jpg', q:'When getting dressed, what do you wear FIRST?',choices:['Shoes','Underwear / undershirt','Jacket','Socks'],correct:1},
    {emoji:'🪞', img:'pictures/motorskills/clothes_step5.jpg', q:'After getting dressed, what is the LAST thing to do?',choices:['Go back to sleep','Check yourself in the mirror to look neat','Eat breakfast first','Put on more clothes'],correct:1},
  ],
  tl:[
    // Lesson 1: Handwashing
    {emoji:'🧼', img:'pictures/motorskills/handwash_step3.jpg', q:'Gaano katagal dapat KUSKUSIN ang kamay habang naghuhugas?',choices:['5 segundo','10 segundo','20 segundo','1 minuto'],correct:2},
    {emoji:'🚿', img:'pictures/motorskills/handwash_step4.jpg', q:'Pagkatapos kuskusin, ano ang SUSUNOD na hakbang sa paghuhugas ng kamay?',choices:['Patuyuin ang kamay','Banlawan ang sabon','Dagdagan ng sabon','Isara ang gripo'],correct:1},
    // Lesson 2: Tooth Brushing
    {emoji:'🪥', img:'pictures/motorskills/brush_step1.jpg',   q:'Ilang beses sa isang araw dapat magsipilyo ng ngipin?',choices:['Isang beses sa isang linggo','Isang beses sa isang araw','Dalawang beses sa isang araw','Hindi na kailangan'],correct:2},
    {emoji:'👅', img:'pictures/motorskills/brush_step4.jpg',   q:'Bakit kailangan ding sipilyo ang dila?',choices:['Para maging pink ito','Para maalis ang bakterya na nagdudulot ng masamang amoy ng hininga','Kasi masarap','Para palakasin ang ngipin'],correct:1},
    // Lesson 3: Bathing
    {emoji:'🛁', img:'pictures/motorskills/bath_step1.jpg',    q:'Bago ka maligo, ano ang UNANG dapat ihanda?',choices:['Ang iyong mga laruan','Sabon, shampoo, at malinis na tuwalya','Ang iyong school bag','Isang baso ng juice'],correct:1},
    {emoji:'💆', img:'pictures/motorskills/bath_step4.jpg',    q:'Habang naliligo, ano ang ginagamit para hugasan ang buhok?',choices:['Sabon lang','Shampoo','Toothpaste','Body lotion'],correct:1},
    // Lesson 4: Hair Combing
    {emoji:'🪮', img:'pictures/motorskills/comb_step2.jpg',    q:'Saan dapat SIMULAN ang pagsusuklay para hindi masakit?',choices:['Sa ugat/itaas','Sa gitna','Sa dulo/tips muna','Hindi mahalaga'],correct:2},
    {emoji:'💇', img:'pictures/motorskills/comb_step1.jpg',    q:'Bakit dapat gumamit ng SARILING suklay at hindi mamahiram?',choices:['Para maganda','Para maiwasan ang pagkalat ng kuto at impeksyon','Mas magaling suklayin','Para makatipid'],correct:1},
    // Lesson 5: Nail Trimming
    {emoji:'✂️', img:'pictures/motorskills/nails_step3.jpg',   q:'Ano ang tamang paraan ng paggupit ng kuko?',choices:['Gupitin nang napaka-ikli','Tuwid, kasing haba ng dulo ng daliri','Gupitin ang gilid nang malalim','Gupitin ang isang kamay lang'],correct:1},
    {emoji:'🧹', img:'pictures/motorskills/nails_step4.jpg',   q:'Pagkatapos gupitin ang kuko, ano ang susunod na dapat gawin?',choices:['Pinturahan agad','Linisin ang ilalim ng kuko gamit ang maliit na brush','Hayaan na lang','Lagyan ng lotion'],correct:1},
    // Lesson 6: Getting Dressed
    {emoji:'👕', img:'pictures/motorskills/clothes_step3.jpg', q:'Habang nagbibihis, ano ang UNANG isinusuot?',choices:['Sapatos','Panloob na damit/sando','Jacket','Medyas'],correct:1},
    {emoji:'🪞', img:'pictures/motorskills/clothes_step5.jpg', q:'Pagkatapos magbihis, ano ang HULING dapat gawin?',choices:['Bumalik matulog','Tingnan ang sarili sa salamin para tiyaking maayos','Kumain muna ng almusal','Magsuot pa ng isa pang damit'],correct:1},
  ]
};
const BC_TOTAL=10, BC_MAX=BC_TOTAL*10;
let bc_score=0, bc_qIdx=0, bc_answered=false, bc_pool=[], bc_selectedChoice=null;

function bc_startGame(){
  bc_score=0; bc_qIdx=0; bc_answered=false; bc_selectedChoice=null;
  bc_pool=[...Array(BC_TOTAL).keys()].sort(()=>Math.random()-.5);
  document.getElementById('p4Score').textContent='0';
  document.getElementById('p4Bar').style.width='0%';
  document.getElementById('p4BarLbl').textContent='0 / '+BC_TOTAL;
  document.getElementById('p4Feedback').textContent='';
  document.getElementById('p4Feedback').className='feedback-msg';
  document.getElementById('p4SubmitBtn').style.display='inline-flex';
  document.getElementById('p4SubmitBtn').disabled=true;
  document.getElementById('p4NextBtn').style.display='none';
  saveStart(ACT_IDS[4]);
  bc_render();
}
function bc_render(){
  const q=BC_QUESTIONS[lang][bc_pool[bc_qIdx]];
  bc_answered=false; bc_selectedChoice=null;
  // Show image or emoji
  const emojiEl=document.getElementById('p4Emoji');
  if(q.img){
    emojiEl.innerHTML='';
    emojiEl.style.display='block';
    const im=document.createElement('img');
    im.src=q.img; im.alt=q.q;
    im.style.cssText='width:140px;height:140px;object-fit:cover;border-radius:18px;box-shadow:0 4px 16px rgba(0,0,0,.12);';
    im.onerror=()=>{ emojiEl.innerHTML=''; emojiEl.textContent=q.emoji; emojiEl.style.fontSize='4rem'; };
    emojiEl.appendChild(im);
  } else {
    emojiEl.textContent=q.emoji;
    emojiEl.style.fontSize='4rem';
  }
  document.getElementById('p4QText').textContent=q.q;
  document.getElementById('p4QNum').textContent=bc_qIdx+1;
  document.getElementById('p4BarLbl').textContent=bc_qIdx+' / '+BC_TOTAL;
  document.getElementById('p4Bar').style.width=(bc_qIdx/BC_TOTAL*100)+'%';
  document.getElementById('p4Feedback').textContent='';
  document.getElementById('p4Feedback').className='feedback-msg';
  document.getElementById('p4SubmitBtn').style.display='inline-flex';
  document.getElementById('p4SubmitBtn').disabled=true;
  document.getElementById('p4NextBtn').style.display='none';
  const choicesEl=document.getElementById('p4Choices');
  choicesEl.innerHTML='';
  const letters=['A','B','C','D'];
  const colors=['#00acc1','#f06292','#fcc419','#66bb6a'];
  q.choices.forEach((c,i)=>{
    const btn=document.createElement('button');
    btn.className='choice-btn';
    btn.dataset.idx=i;
    btn.innerHTML=`<div class="choice-letter" style="background:${colors[i]}">${letters[i]}</div><div class="choice-text">${c}</div>`;
    btn.onclick=()=>bc_selectChoice(btn,i);
    choicesEl.appendChild(btn);
  });
}
function bc_selectChoice(btn, idx){
  if(bc_answered) return;
  // Deselect all
  document.querySelectorAll('#p4Choices .choice-btn').forEach(b=>{
    b.classList.remove('selected-choice');
    b.style.borderColor='';
    b.style.background='';
  });
  // Select this
  btn.classList.add('selected-choice');
  btn.style.borderColor='#7c3aed';
  btn.style.background='#ede9fe';
  bc_selectedChoice=idx;
  document.getElementById('p4SubmitBtn').disabled=false;
}
function bc_submit(){
  if(bc_answered || bc_selectedChoice===null) return;
  bc_answered=true;
  const q=BC_QUESTIONS[lang][bc_pool[bc_qIdx]];
  const isCorrect=bc_selectedChoice===q.correct;
  document.querySelectorAll('#p4Choices .choice-btn').forEach(b=>b.classList.add('answered'));
  const allBtns=document.querySelectorAll('#p4Choices .choice-btn');
  allBtns.forEach(b=>{
    const idx=parseInt(b.dataset.idx);
    if(idx===q.correct){ b.classList.add('correct'); b.style.borderColor=''; b.style.background=''; }
    else if(idx===bc_selectedChoice && !isCorrect){ b.classList.add('wrong'); b.style.borderColor=''; b.style.background=''; }
    else { b.style.borderColor=''; b.style.background=''; }
  });
  if(isCorrect){
    bc_score+=10;
    document.getElementById('p4Score').textContent=bc_score;
    document.getElementById('p4Feedback').textContent=lang==='en'?'✅ Correct! Great job!':'✅ Tama! Napakahusay!';
    document.getElementById('p4Feedback').className='feedback-msg correct';
    saveProgress(ACT_IDS[4], Math.round((bc_score/BC_MAX)*100));
    confetti('#00acc1');
  } else {
    document.getElementById('p4Feedback').textContent=lang==='en'?`❌ The answer was: ${q.choices[q.correct]}`:`❌ Ang sagot ay: ${q.choices[q.correct]}`;
    document.getElementById('p4Feedback').className='feedback-msg wrong';
  }
  bc_qIdx++;
  document.getElementById('p4SubmitBtn').style.display='none';
  if(bc_qIdx>=BC_TOTAL) setTimeout(()=>showResult(4,bc_score,BC_MAX),1000);
  else document.getElementById('p4NextBtn').style.display='inline-flex';
}
function bc_nextQuestion(){ bc_render(); }

// ── FLUSH ON NAVIGATE AWAY ──
function flushActiveTabProgress(){
  const sa=STEP_ACTIVITIES[lang]; const hwMax=sa.reduce((s,a)=>s+a.steps.length*10,0);
  switch(activeTab){
    case 1: if(hw_score>0) saveProgress(ACT_IDS[1],Math.min(100,Math.round((hw_score/100)*100))); break;
    case 2: if(hm_score>0) saveProgress(ACT_IDS[2],Math.round((hm_score/HM_MAX)*100)); break;
    case 3: if(dr_score>0) saveProgress(ACT_IDS[3],Math.round((dr_score/DR_MAX)*100)); break;
    case 4: if(bc_score>0) saveProgress(ACT_IDS[4],Math.round((bc_score/BC_MAX)*100)); break;
  }
}
window.addEventListener('pagehide',     ()=>flushActiveTabProgress());
window.addEventListener('beforeunload', ()=>flushActiveTabProgress());
document.addEventListener('visibilitychange',()=>{ if(document.visibilityState==='hidden') flushActiveTabProgress(); });

// ── INIT ──
if(activeTab===1) hw_startGame();
else if(activeTab===2) hm_startGame();
else if(activeTab===3) dr_startGame();
else if(activeTab===4) bc_startGame();
</script>
</body>
</html>