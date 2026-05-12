<?php
session_start();
require_once 'database.php';
require_once 'activity_helper.php';

if (!isset($_SESSION['student_id']) || $_SESSION['role'] !== 'student') {
    header("Location: index.php");
    exit;
}

$student_id = $_SESSION['student_id'];
$lesson_id  = isset($_GET['lesson_id']) ? (int)$_GET['lesson_id'] : 6; // Numbers = 6

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
$activities = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$act_map = [];
foreach ($activities as $act) {
    $prog = quizzes_get($conn, $student_id, $act['quizzes_id']);
    $act_map[$act['quizzes_name']] = [
        'quizzes_id' => $act['quizzes_id'],
        'status'      => $prog['status'],
        'score'       => $prog['score'],
    ];
}

$a1 = $act_map['number_recognition'] ?? ['quizzes_id'=>0,'status'=>'not_started','score'=>0];
$a2 = $act_map['counting_quiz']      ?? ['quizzes_id'=>0,'status'=>'not_started','score'=>0];
$a3 = $act_map['number_order']       ?? ['quizzes_id'=>0,'status'=>'not_started','score'=>0];
$a4 = $act_map['odd_or_even']        ?? ['quizzes_id'=>0,'status'=>'not_started','score'=>0];

$active_tab = isset($_GET['tab']) ? max(1, min(4, (int)$_GET['tab'])) : 1;
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Numbers Activities — E-KINDER</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Fredoka+One&family=Nunito:wght@400;600;700;800;900&display=swap" rel="stylesheet">
<style>
:root{--green-dark:#1a2e1a;--cream:#fdf8f0;--pill:999px;--odd:#845EC2;--even:#4D96FF;}
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
.act-tab:hover:not(.active){border-color:#d0e8ff;color:#555;transform:translateY(-2px);}
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
.progress-bar-inner{height:100%;border-radius:99px;transition:width .5s cubic-bezier(.34,1.56,.64,1);}
.progress-label{font-family:'Fredoka One',cursive;font-size:.75rem;color:#aaa;margin-top:4px;}

/* ── ACT 1: NUMBER RECOGNITION ── */
.question-card{background:#fff;border-radius:28px;padding:36px 28px;text-align:center;box-shadow:0 8px 36px rgba(0,0,0,.08);border:2px solid #f0ece4;max-width:520px;margin:0 auto 28px;}
.q-prompt{font-family:'Fredoka One',cursive;font-size:.9rem;color:#bbb;text-transform:uppercase;letter-spacing:1px;margin-bottom:16px;}
.q-number{font-family:'Fredoka One',cursive;font-size:7rem;line-height:1;color:var(--q-color,#4D96FF);margin-bottom:8px;animation:numBounce 2s ease-in-out infinite;display:inline-block;}
@keyframes numBounce{0%,100%{transform:translateY(0) rotate(-3deg) scale(1)}50%{transform:translateY(-14px) rotate(3deg) scale(1.05)}}
.q-speak-btn{background:none;border:none;font-size:1.2rem;cursor:pointer;color:var(--q-color,#4D96FF);transition:transform .2s;}
.q-speak-btn:hover{transform:scale(1.2);}
.choices-grid-2{display:grid;grid-template-columns:repeat(2,1fr);gap:14px;max-width:520px;margin:0 auto 24px;}
.choice-btn-nr{background:#fff;border:3px solid #f0f0f0;border-radius:18px;padding:16px 14px;text-align:center;cursor:pointer;box-shadow:0 4px 14px rgba(0,0,0,.06);transition:all .22s cubic-bezier(.34,1.56,.64,1);animation:cardIn .4s ease both;font-family:'Fredoka One',cursive;font-size:1.2rem;color:#555;}
.choice-btn-nr:hover:not(.answered){transform:translateY(-4px) scale(1.03);box-shadow:0 10px 24px rgba(0,0,0,.1);border-color:var(--q-color,#4D96FF);color:var(--q-color,#4D96FF);}
.choice-btn-nr.correct{border-color:#40c057!important;background:#d3f9d8!important;color:#2f9e44!important;animation:popGreen .4s cubic-bezier(.34,1.56,.64,1);}
.choice-btn-nr.wrong{border-color:#ff4444!important;background:#ffe3e3!important;color:#ff4444!important;animation:cardShake .4s ease;}
.choice-btn-nr.answered{cursor:default;}
.choice-btn-nr.selected{border-color:var(--q-color,#4D96FF)!important;background:color-mix(in srgb,var(--q-color,#4D96FF) 12%,white)!important;color:var(--q-color,#4D96FF)!important;transform:translateY(-2px) scale(1.02);}
.choice-btn-nr:nth-child(1){animation-delay:.04s}.choice-btn-nr:nth-child(2){animation-delay:.08s}
.choice-btn-nr:nth-child(3){animation-delay:.12s}.choice-btn-nr:nth-child(4){animation-delay:.16s}

/* ── ACT 2: COUNTING QUIZ ── */
.count-card{background:#fff;border-radius:28px;padding:28px 24px;box-shadow:0 8px 36px rgba(0,0,0,.08);border:2px solid #f0ece4;max-width:540px;margin:0 auto 24px;}
.count-prompt{font-family:'Fredoka One',cursive;font-size:.9rem;color:#bbb;text-transform:uppercase;letter-spacing:1px;text-align:center;margin-bottom:16px;}
.emoji-stage{display:flex;flex-wrap:wrap;justify-content:center;gap:8px;min-height:80px;align-items:center;padding:8px 0 16px;border-bottom:1.5px solid #f0f0f0;margin-bottom:16px;}
.count-emoji{font-size:2.2rem;line-height:1;animation:emojiPop .3s cubic-bezier(.34,1.56,.64,1) both;cursor:default;transition:transform .2s;}
.count-emoji:hover{transform:scale(1.2);}
@keyframes emojiPop{from{opacity:0;transform:scale(0) rotate(-15deg)}to{opacity:1;transform:scale(1) rotate(0)}}
.count-hint{font-family:'Fredoka One',cursive;font-size:.8rem;color:#ddd;text-align:center;margin-top:4px;letter-spacing:.5px;}
.num-choices{display:grid;grid-template-columns:repeat(4,1fr);gap:12px;max-width:480px;margin:0 auto 20px;}
@media(max-width:400px){.num-choices{grid-template-columns:repeat(2,1fr);}}
.num-btn{background:#fff;border:3px solid #f0f0f0;border-radius:16px;padding:18px 8px;text-align:center;cursor:pointer;font-family:'Fredoka One',cursive;font-size:2.2rem;color:var(--nb-color,#555);box-shadow:0 4px 14px rgba(0,0,0,.06);transition:all .22s cubic-bezier(.34,1.56,.64,1);animation:cardIn .4s ease both;}
.num-btn:hover:not(.answered){transform:translateY(-5px) scale(1.1);box-shadow:0 10px 24px rgba(0,0,0,.1);border-color:var(--nb-color);}
.num-btn.correct{border-color:#40c057!important;background:#d3f9d8!important;color:#2f9e44!important;animation:popGreen .4s cubic-bezier(.34,1.56,.64,1);}
.num-btn.wrong{border-color:#ff4444!important;background:#ffe3e3!important;color:#ff4444!important;animation:cardShake .4s ease;}
.num-btn.answered{cursor:default;}
.num-btn.selected{border-color:var(--nb-color)!important;background:color-mix(in srgb,var(--nb-color) 12%,white)!important;transform:translateY(-3px) scale(1.05);box-shadow:0 10px 24px rgba(0,0,0,.1);}
.num-btn:nth-child(1){animation-delay:.04s}.num-btn:nth-child(2){animation-delay:.08s}
.num-btn:nth-child(3){animation-delay:.12s}.num-btn:nth-child(4){animation-delay:.16s}

/* ── ACT 3: NUMBER ORDER ── */
.game-card{background:#fff;border-radius:28px;padding:28px 24px;box-shadow:0 8px 36px rgba(0,0,0,.08);border:2px solid #f0ece4;max-width:600px;margin:0 auto 24px;}
.game-prompt{font-family:'Fredoka One',cursive;font-size:.9rem;color:#bbb;text-transform:uppercase;letter-spacing:1px;text-align:center;margin-bottom:20px;}
.slots-row{display:flex;justify-content:center;align-items:center;gap:10px;margin-bottom:20px;flex-wrap:wrap;}
.order-slot{width:64px;height:72px;border-radius:16px;border:3px dashed #e0e0e0;background:#fafafa;display:flex;flex-direction:column;align-items:center;justify-content:center;cursor:pointer;transition:all .2s cubic-bezier(.34,1.56,.64,1);position:relative;}
.order-slot.has-num{border-style:solid;border-color:var(--sc,#FF8E53);background:color-mix(in srgb,var(--sc,#FF8E53) 8%,white);}
.order-slot.correct-pos{border-color:#40c057;background:#d3f9d8;}
.order-slot.wrong-pos{border-color:#ff4444;background:#ffe3e3;animation:slotShake .4s ease;}
@keyframes slotShake{0%,100%{transform:translateX(0)}25%{transform:translateX(-5px)}75%{transform:translateX(5px)}}
.slot-num{font-family:'Fredoka One',cursive;font-size:1.7rem;color:var(--sc,#FF8E53);}
.slot-pos{font-size:.55rem;font-weight:800;color:#bbb;text-transform:uppercase;letter-spacing:.5px;position:absolute;bottom:5px;}
.arrow-icon{font-size:1.2rem;color:#e0e0e0;flex-shrink:0;}
.tiles-label{font-family:'Fredoka One',cursive;font-size:.75rem;color:#bbb;text-transform:uppercase;letter-spacing:1px;text-align:center;margin-bottom:12px;}
.tiles-row{display:flex;justify-content:center;gap:10px;flex-wrap:wrap;margin-bottom:16px;}
.num-tile{width:62px;height:70px;border-radius:16px;background:#fff;border:3px solid var(--tc,#FF8E53);font-family:'Fredoka One',cursive;font-size:1.7rem;color:var(--tc,#FF8E53);cursor:pointer;box-shadow:0 4px 14px rgba(0,0,0,.08);transition:all .2s cubic-bezier(.34,1.56,.64,1);display:flex;align-items:center;justify-content:center;animation:tileIn .35s cubic-bezier(.34,1.56,.64,1) both;}
.num-tile:hover:not(.placed):not(.disabled){transform:translateY(-5px) scale(1.1);box-shadow:0 10px 22px rgba(0,0,0,.12);}
.num-tile.placed{opacity:.2;transform:scale(.85);pointer-events:none;}
@keyframes tileIn{from{opacity:0;transform:scale(.6) rotate(-10deg)}to{opacity:1;transform:scale(1) rotate(0)}}
.num-tile:nth-child(1){animation-delay:.04s}.num-tile:nth-child(2){animation-delay:.08s}
.num-tile:nth-child(3){animation-delay:.12s}.num-tile:nth-child(4){animation-delay:.16s}.num-tile:nth-child(5){animation-delay:.20s}

/* ── ACT 4: ODD OR EVEN ── */
.legend-bar{display:flex;justify-content:center;gap:24px;background:#fff;border-radius:16px;padding:14px 24px;margin-bottom:24px;box-shadow:0 3px 14px rgba(0,0,0,.05);border:1.5px solid #f0f0f0;flex-wrap:wrap;}
.legend-item{display:flex;align-items:center;gap:10px;}
.legend-dot{width:18px;height:18px;border-radius:50%;flex-shrink:0;}
.legend-text{font-family:'Fredoka One',cursive;font-size:.9rem;color:#555;}
.legend-hint{font-size:.72rem;color:#bbb;font-weight:700;}
.number-stage{background:#fff;border-radius:28px;padding:36px 28px;text-align:center;box-shadow:0 8px 36px rgba(0,0,0,.08);border:2px solid #f0ece4;max-width:500px;margin:0 auto 24px;}
.stage-prompt{font-family:'Fredoka One',cursive;font-size:.9rem;color:#bbb;text-transform:uppercase;letter-spacing:1px;margin-bottom:16px;}
.big-number{font-family:'Fredoka One',cursive;font-size:8rem;line-height:1;color:var(--n-color,#555);margin-bottom:12px;animation:numFloat 2s ease-in-out infinite;display:inline-block;}
@keyframes numFloat{0%,100%{transform:translateY(0) rotate(-2deg) scale(1)}50%{transform:translateY(-16px) rotate(2deg) scale(1.04)}}
.visual-dots{display:flex;justify-content:center;gap:6px;flex-wrap:wrap;max-width:280px;margin:0 auto 14px;}
.v-dot{width:14px;height:14px;border-radius:50%;background:var(--n-color,#555);animation:dotPop .25s cubic-bezier(.34,1.56,.64,1) both;opacity:.7;}
@keyframes dotPop{from{transform:scale(0)}to{transform:scale(1)}}
.speak-btn-small{background:none;border:none;font-size:1.1rem;cursor:pointer;color:var(--n-color,#555);transition:transform .2s;}
.speak-btn-small:hover{transform:scale(1.2);}
.oe-choices{display:grid;grid-template-columns:1fr 1fr;gap:16px;max-width:500px;margin:0 auto 20px;}
.oe-btn{border:3px solid transparent;border-radius:22px;padding:22px 14px;text-align:center;cursor:pointer;box-shadow:0 6px 20px rgba(0,0,0,.08);transition:all .25s cubic-bezier(.34,1.56,.64,1);animation:btnIn .4s ease both;}
@keyframes btnIn{from{opacity:0;transform:scale(.85)}to{opacity:1;transform:scale(1)}}
.oe-btn.odd-btn{background:#f3f0ff;border-color:#d0b8ff;color:var(--odd);}
.oe-btn.even-btn{background:#e7f5ff;border-color:#a5d0ff;color:var(--even);}
.oe-btn:hover:not(.answered){transform:translateY(-6px) scale(1.04);box-shadow:0 14px 32px rgba(0,0,0,.12);}
.oe-btn.correct.odd-btn{background:var(--odd);border-color:var(--odd);color:#fff;animation:oeCorrect .5s cubic-bezier(.34,1.56,.64,1);}
.oe-btn.correct.even-btn{background:var(--even);border-color:var(--even);color:#fff;animation:oeCorrect .5s cubic-bezier(.34,1.56,.64,1);}
.oe-btn.wrong{background:#ffe3e3!important;border-color:#ff4444!important;color:#ff4444!important;animation:oeShake .4s ease;}
.oe-btn.answered{cursor:default;}
.oe-btn.selected.odd-btn{background:#e8deff;border-color:var(--odd);transform:translateY(-4px) scale(1.04);box-shadow:0 14px 32px rgba(0,0,0,.12);}
.oe-btn.selected.even-btn{background:#d0eaff;border-color:var(--even);transform:translateY(-4px) scale(1.04);box-shadow:0 14px 32px rgba(0,0,0,.12);}
@keyframes oeCorrect{0%{transform:scale(.9)}60%{transform:scale(1.1)}100%{transform:scale(1)}}
@keyframes oeShake{0%,100%{transform:translateX(0)}25%{transform:translateX(-8px)}75%{transform:translateX(8px)}}
.oe-icon{font-size:2.5rem;margin-bottom:8px;display:block;}
.oe-label{font-family:'Fredoka One',cursive;font-size:1.6rem;display:block;margin-bottom:4px;}
.oe-hint-lbl{font-size:.75rem;font-weight:800;opacity:.7;display:block;}

/* SHARED */
@keyframes popGreen{0%{transform:scale(.9)}60%{transform:scale(1.1)}100%{transform:scale(1)}}
@keyframes cardShake{0%,100%{transform:translateX(0)}25%{transform:translateX(-6px)}75%{transform:translateX(6px)}}
@keyframes cardIn{from{opacity:0;transform:translateY(14px) scale(.95)}to{opacity:1;transform:none}}
.feedback-msg{font-family:'Fredoka One',cursive;font-size:1rem;text-align:center;margin-bottom:14px;min-height:24px;}
.feedback-msg.correct{color:#2f9e44;}
.feedback-msg.wrong{color:#e03131;}
.controls{display:flex;justify-content:center;gap:12px;flex-wrap:wrap;}
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
.next-quizzes-btn{width:100%;margin-top:8px;background:linear-gradient(135deg,#4D96FF,#845EC2);color:#fff;border:none;border-radius:var(--pill);padding:12px 24px;font-family:'Fredoka One',cursive;font-size:.95rem;cursor:pointer;box-shadow:0 6px 18px rgba(0,0,0,.15);transition:transform .2s;display:flex;align-items:center;justify-content:center;gap:8px;}
.next-quizzes-btn:hover{transform:scale(1.04);}
.cfbit{position:fixed;pointer-events:none;z-index:1000;animation:cfFall linear forwards;}
@keyframes cfFall{0%{transform:translateY(-16px) rotate(0deg);opacity:1}100%{transform:translateY(105vh) rotate(700deg);opacity:0}}
</style>
</head>
<body>
<div class="page-wrap">

<nav class="lesson-nav">
  <a href="activities.php" class="lnav-back"><i class="fas fa-arrow-left"></i> Back</a>
  <div class="lnav-title">🔢 Numbers Activities</div>
  <div class="lang-toggle">
    <button class="lang-btn active" id="btnEN" onclick="setLang('en')">🇺🇸 EN</button>
    <button class="lang-btn" id="btnTL" onclick="setLang('tl')">🇵🇭 TL</button>
  </div>
</nav>

<div class="container">

  <!-- TABS -->
  <div class="quizzes-tabs">
    <?php
    $tabs = [
      1 => ['icon'=>'fas fa-hashtag',          'en'=>'Recognition', 'tl'=>'Pagkilala',   'act'=>$a1],
      2 => ['icon'=>'fas fa-calculator',        'en'=>'Counting',    'tl'=>'Pagbibilang', 'act'=>$a2],
      3 => ['icon'=>'fas fa-sort-numeric-up',   'en'=>'Order',       'tl'=>'Ayos',        'act'=>$a3],
      4 => ['icon'=>'fas fa-divide',            'en'=>'Odd/Even',    'tl'=>'Odd/Even',    'act'=>$a4],
    ];
    foreach ($tabs as $num => $tab):
      $status = $tab['act']['status'];
      $score  = $tab['act']['score'];
      $dot_class = match($status){'completed'=>'done','in_progress'=>'progress',default=>'locked'};
      $dot_icon  = match($status){'completed'=>'<i class="fas fa-check"></i>','in_progress'=>'<i class="fas fa-play"></i>',default=>$num};
    ?>
    <div class="act-tab <?php echo $num===$active_tab?'active':'';?>" onclick="switchTab(<?php echo $num;?>)" id="tab<?php echo $num;?>">
      <i class="<?php echo $tab['icon'];?>"></i>
      <span class="tab-label" data-en="<?php echo $tab['en'];?>" data-tl="<?php echo $tab['tl'];?>"><?php echo $tab['en'];?></span>
      <span class="tab-status <?php echo $dot_class;?>"><?php echo $dot_icon;?></span>
      <?php if($status==='completed'):?><span class="tab-score"><?php echo $score;?>%</span><?php endif;?>
    </div>
    <?php endforeach;?>
  </div>

  <!-- ══════════════════ PANEL 1: NUMBER RECOGNITION ══════════════════ -->
  <div class="quizzes-panel <?php echo $active_tab===1?'active':'';?>" id="panel1">
    <div class="page-header">
      <div class="quizzes-badge"><i class="fas fa-hashtag"></i> Quizzes 1 of 4</div>
      <div class="page-title" id="p1Title">Number Recognition!</div>
      <div class="page-sub" id="p1Sub">See the number — choose the correct name!</div>
      <?php if($a1['score']>0):?><div class="best-score-badge">⭐ Best Score: <?php echo $a1['score'];?>%</div><?php endif;?>
    </div>
    <div class="score-bar">
      <div class="score-item"><div class="score-num" id="p1Score">0</div><div class="score-label">Score</div></div>
      <div class="score-sep"></div>
      <div class="score-item"><div class="score-num" id="p1QNum">1</div><div class="score-label">Question</div></div>
      <div class="score-sep"></div>
      <div class="score-item"><div class="lives-wrap" id="p1Lives"><span class="life-icon">❤️</span><span class="life-icon">❤️</span><span class="life-icon">❤️</span></div><div class="score-label">Lives</div></div>
      <div class="score-sep"></div>
      <div class="progress-wrap">
        <div class="progress-bar-outer"><div class="progress-bar-inner" id="p1Bar" style="width:0%;background:linear-gradient(90deg,#4D96FF,#845EC2)"></div></div>
        <div class="progress-label" id="p1BarLbl">0 / 10</div>
      </div>
    </div>
    <div class="question-card">
      <div class="q-prompt" id="p1QPrompt">What is this number?</div>
      <div class="q-number" id="p1QNumber">7</div><br>
      <button class="q-speak-btn" onclick="nr_speakNum()"><i class="fas fa-volume-up"></i></button>
    </div>
    <div class="choices-grid-2" id="p1Choices"></div>
    <div class="feedback-msg" id="p1Feedback"></div>
    <div class="controls">
      <button class="btn-action btn-primary" id="p1SubmitBtn" onclick="nr_submitAnswer()" style="display:none"><i class="fas fa-check"></i> <span id="p1SubmitLbl">Submit</span></button>
      <button class="btn-action btn-secondary" id="p1NextBtn" onclick="nr_nextQuestion()" style="display:none"><i class="fas fa-arrow-right"></i> <span id="p1NextLbl">Next</span></button>
    </div>
  </div>

  <!-- ══════════════════ PANEL 2: COUNTING QUIZ ══════════════════ -->
  <div class="quizzes-panel <?php echo $active_tab===2?'active':'';?>" id="panel2">
    <div class="page-header">
      <div class="quizzes-badge"><i class="fas fa-calculator"></i> Quizzes 2 of 4</div>
      <div class="page-title" id="p2Title">Counting Quiz!</div>
      <div class="page-sub" id="p2Sub">Count the objects and choose the correct number!</div>
      <?php if($a2['score']>0):?><div class="best-score-badge">⭐ Best Score: <?php echo $a2['score'];?>%</div><?php endif;?>
    </div>
    <div class="score-bar">
      <div class="score-item"><div class="score-num" id="p2Score">0</div><div class="score-label">Score</div></div>
      <div class="score-sep"></div>
      <div class="score-item"><div class="score-num" id="p2QNum">1</div><div class="score-label">Question</div></div>
      <div class="score-sep"></div>
      <div class="score-item"><div class="lives-wrap" id="p2Lives"><span class="life-icon">❤️</span><span class="life-icon">❤️</span><span class="life-icon">❤️</span></div><div class="score-label">Lives</div></div>
      <div class="score-sep"></div>
      <div class="progress-wrap">
        <div class="progress-bar-outer"><div class="progress-bar-inner" id="p2Bar" style="width:0%;background:linear-gradient(90deg,#6BCB77,#2f9e44)"></div></div>
        <div class="progress-label" id="p2BarLbl">0 / 10</div>
      </div>
    </div>
    <div class="count-card">
      <div class="count-prompt" id="p2CountPrompt">How many objects do you see?</div>
      <div class="emoji-stage" id="p2EmojiStage"></div>
      <div class="count-hint" id="p2CountHint">Count them carefully!</div>
    </div>
    <div class="num-choices" id="p2NumChoices"></div>
    <div class="feedback-msg" id="p2Feedback"></div>
    <div class="controls">
      <button class="btn-action btn-primary" id="p2SubmitBtn" onclick="cq_submitAnswer()" style="display:none"><i class="fas fa-check"></i> <span id="p2SubmitLbl">Submit</span></button>
      <button class="btn-action btn-secondary" id="p2NextBtn" onclick="cq_nextQuestion()" style="display:none"><i class="fas fa-arrow-right"></i> <span id="p2NextLbl">Next</span></button>
    </div>
  </div>

  <!-- ══════════════════ PANEL 3: NUMBER ORDER ══════════════════ -->
  <div class="quizzes-panel <?php echo $active_tab===3?'active':'';?>" id="panel3">
    <div class="page-header">
      <div class="quizzes-badge"><i class="fas fa-sort-numeric-up"></i> Quizzes 3 of 4</div>
      <div class="page-title" id="p3Title">Number Order!</div>
      <div class="page-sub" id="p3Sub">Arrange the numbers from smallest to biggest!</div>
      <?php if($a3['score']>0):?><div class="best-score-badge">⭐ Best Score: <?php echo $a3['score'];?>%</div><?php endif;?>
    </div>
    <div class="score-bar">
      <div class="score-item"><div class="score-num" id="p3Score">0</div><div class="score-label">Score</div></div>
      <div class="score-sep"></div>
      <div class="score-item"><div class="score-num" id="p3Round">1</div><div class="score-label">Round</div></div>
      <div class="score-sep"></div>
      <div class="progress-wrap">
        <div class="progress-bar-outer"><div class="progress-bar-inner" id="p3Bar" style="width:0%;background:linear-gradient(90deg,#FF8E53,#F9A825)"></div></div>
        <div class="progress-label" id="p3BarLbl">0 / 5 rounds</div>
      </div>
    </div>
    <div class="game-card">
      <div class="game-prompt" id="p3GamePrompt">Arrange from smallest ➡️ to biggest</div>
      <div class="slots-row" id="p3SlotsRow"></div>
      <div class="tiles-label" id="p3TilesLabel">Tap numbers in order:</div>
      <div class="tiles-row" id="p3TilesRow"></div>
      <div class="feedback-msg" id="p3Feedback"></div>
      <div class="controls">
        <button class="btn-action btn-secondary" onclick="no_clearSlots()"><i class="fas fa-eraser"></i> <span id="p3ClearLbl">Clear</span></button>
        <button class="btn-action btn-primary" id="p3SubmitBtn" onclick="no_submitRound()" style="display:none"><i class="fas fa-check"></i> <span id="p3SubmitLbl">Submit Round</span></button>
        <button class="btn-action btn-secondary" id="p3NextBtn" onclick="no_proceedNext()" style="display:none"><i class="fas fa-arrow-right"></i> <span id="p3NextLbl">Next Round</span></button>
      </div>
    </div>
  </div>

  <!-- ══════════════════ PANEL 4: ODD OR EVEN ══════════════════ -->
  <div class="quizzes-panel <?php echo $active_tab===4?'active':'';?>" id="panel4">
    <div class="page-header">
      <div class="quizzes-badge"><i class="fas fa-divide"></i> Quizzes 4 of 4</div>
      <div class="page-title" id="p4Title">Odd or Even?</div>
      <div class="page-sub" id="p4Sub">Is the number odd or even? Tap your answer!</div>
      <?php if($a4['score']>0):?><div class="best-score-badge">⭐ Best Score: <?php echo $a4['score'];?>%</div><?php endif;?>
    </div>
    <div class="score-bar">
      <div class="score-item"><div class="score-num" id="p4Score">0</div><div class="score-label">Score</div></div>
      <div class="score-sep"></div>
      <div class="score-item"><div class="score-num" id="p4QNum">1</div><div class="score-label">Question</div></div>
      <div class="score-sep"></div>
      <div class="score-item"><div class="lives-wrap" id="p4Lives"><span class="life-icon">❤️</span><span class="life-icon">❤️</span><span class="life-icon">❤️</span></div><div class="score-label">Lives</div></div>
      <div class="score-sep"></div>
      <div class="progress-wrap">
        <div class="progress-bar-outer"><div class="progress-bar-inner" id="p4Bar" style="width:0%;background:linear-gradient(90deg,var(--odd),var(--even))"></div></div>
        <div class="progress-label" id="p4BarLbl">0 / 10</div>
      </div>
    </div>
    <div class="legend-bar">
      <div class="legend-item">
        <div class="legend-dot" style="background:var(--odd)"></div>
        <div><div class="legend-text" id="p4LegendOdd">Odd — cannot be split equally in 2</div><div class="legend-hint">1, 3, 5, 7, 9...</div></div>
      </div>
      <div class="legend-item">
        <div class="legend-dot" style="background:var(--even)"></div>
        <div><div class="legend-text" id="p4LegendEven">Even — can be split equally in 2</div><div class="legend-hint">2, 4, 6, 8, 10...</div></div>
      </div>
    </div>
    <div class="number-stage">
      <div class="stage-prompt" id="p4StagePrompt">Is this number odd or even?</div>
      <div class="big-number" id="p4BigNumber">7</div>
      <div class="visual-dots" id="p4VisualDots"></div>
      <button class="speak-btn-small" onclick="oe_speakNum()"><i class="fas fa-volume-up"></i></button>
    </div>
    <div class="oe-choices">
      <div class="oe-btn odd-btn" id="p4OddBtn" onclick="oe_pickAnswer('odd')">
        <span class="oe-icon">🔮</span>
        <span class="oe-label" id="p4OddLabel">Odd</span>
        <span class="oe-hint-lbl" id="p4OddHint">1, 3, 5...</span>
      </div>
      <div class="oe-btn even-btn" id="p4EvenBtn" onclick="oe_pickAnswer('even')">
        <span class="oe-icon">💙</span>
        <span class="oe-label" id="p4EvenLabel">Even</span>
        <span class="oe-hint-lbl" id="p4EvenHint">2, 4, 6...</span>
      </div>
    </div>
    <div class="feedback-msg" id="p4Feedback"></div>
    <div class="controls">
      <button class="btn-action btn-primary" id="p4SubmitBtn" onclick="oe_submitAnswer()" style="display:none"><i class="fas fa-check"></i> <span id="p4SubmitLbl">Submit</span></button>
      <button class="btn-action btn-secondary" id="p4NextBtn" onclick="oe_nextQuestion()" style="display:none"><i class="fas fa-arrow-right"></i> <span id="p4NextLbl">Next</span></button>
    </div>
  </div>

</div><!-- /container -->

<!-- RESULT OVERLAY -->
<div class="result-overlay" id="resultOverlay">
  <div class="result-box">
    <div class="result-icon" id="resultIcon">🎉</div>
    <div class="result-title" id="resultTitle">Number Star!</div>
    <div class="result-sub" id="resultSub">Great job!</div>
    <div class="result-score" id="resultScore">0%</div>
    <div class="result-best" id="resultBest"></div>
    <div class="saving-msg" id="savingMsg"></div>
    <div class="controls" style="justify-content:center;flex-direction:column;gap:8px;">
      <div style="display:flex;gap:8px;justify-content:center;">
        <button class="btn-action btn-primary" onclick="playAgain()"><i class="fas fa-redo"></i> Play Again</button>
        <button class="btn-action btn-secondary" onclick="closeResult()"><i class="fas fa-times"></i> Close</button>
      </div>
      <button class="next-quizzes-btn" id="nextActBtn" style="display:none" onclick="goNextAuizzes()">
        <i class="fas fa-arrow-right"></i> Next Quiz
      </button>
    </div>
  </div>
</div>

<script>
// ── PHP DATA ──
const ACT_IDS   = {1:<?php echo(int)$a1['quizzes_id'];?>,2:<?php echo(int)$a2['quizzes_id'];?>,3:<?php echo(int)$a3['quizzes_id'];?>,4:<?php echo(int)$a4['quizzes_id'];?>};
const PREV_SCORES={1:<?php echo(int)$a1['score'];?>,2:<?php echo(int)$a2['score'];?>,3:<?php echo(int)$a3['score'];?>,4:<?php echo(int)$a4['score'];?>};
const CURRENT_URL=window.location.href.split('?')[0];
let lang='en', activeTab=<?php echo(int)$active_tab;?>;

const COLORS=['#4D96FF','#FF6B6B','#F9A825','#6BCB77','#845EC2','#FF8E53','#00ACC1','#FD79A8','#FFD93D','#A0522D'];

// ── DB SAVE ──
async function saveStart(id){try{await fetch(CURRENT_URL,{method:'POST',headers:{'Content-Type':'application/x-www-form-urlencoded'},body:`action=start&quizzes_id=${id}`});}catch(e){}}
function saveProgress(id,score,checkpoint=0){
  if(score<=0)return;
  const body=`action=progress&quizzes_id=${id}&score=${score}&checkpoint=${checkpoint}`;
  // sendBeacon works reliably even on page unload/back button
  if(navigator.sendBeacon){
    const fd=new FormData();
    fd.append('action','progress');fd.append('quizzes_id',id);
    fd.append('score',score);fd.append('checkpoint',checkpoint);
    navigator.sendBeacon(CURRENT_URL,fd);
  } else {
    // Fallback for older browsers
    fetch(CURRENT_URL,{method:'POST',headers:{'Content-Type':'application/x-www-form-urlencoded'},body,keepalive:true}).catch(()=>{});
  }
}
async function saveComplete(id,score){
  document.getElementById('savingMsg').textContent='💾 Saving your score...';
  try{const r=await fetch(CURRENT_URL,{method:'POST',headers:{'Content-Type':'application/x-www-form-urlencoded'},body:`action=complete&quizzes_id=${id}&score=${score}`});const d=await r.json();if(d.success)document.getElementById('savingMsg').textContent='✅ Score saved!';}catch(e){document.getElementById('savingMsg').textContent='';}
}

// ── LANG ──
function setLang(l){
  if(l===lang)return;lang=l;
  document.getElementById('btnEN').classList.toggle('active',l==='en');
  document.getElementById('btnTL').classList.toggle('active',l==='tl');
  document.querySelectorAll('.tab-label').forEach(el=>el.textContent=el.dataset[l]);
  if(activeTab===1)nr_startGame();
  if(activeTab===2)cq_startGame();
  if(activeTab===3)no_startGame();
  if(activeTab===4)oe_startGame();
}

// ── TABS ──
function switchTab(num){
  activeTab=num;
  document.querySelectorAll('.act-tab').forEach((t,i)=>t.classList.toggle('active',i+1===num));
  document.querySelectorAll('.quizzes-panel').forEach((p,i)=>p.classList.toggle('active',i+1===num));
  if(num===1)nr_startGame();
  if(num===2)cq_startGame();
  if(num===3)no_startGame();
  if(num===4)oe_startGame();
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
  confetti('#4D96FF');confetti('#ffd43b');
  saveProgress(ACT_IDS[actNum],finalScore);
  saveComplete(ACT_IDS[actNum],finalScore);
  PREV_SCORES[actNum]=Math.max(PREV_SCORES[actNum],finalScore);
}
function closeResult(){document.getElementById('resultOverlay').classList.remove('active');}
function playAgain(){
  closeResult();
  if(_curActNum===1)nr_startGame();
  if(_curActNum===2)cq_startGame();
  if(_curActNum===3)no_startGame();
  if(_curActNum===4)oe_startGame();
}
function goNextQuizzes(){closeResult();switchTab(_curActNum+1);}

// ── UTILS ──
function shuffle(a){const r=[...a];for(let i=r.length-1;i>0;i--){const j=0|Math.random()*(i+1);[r[i],r[j]]=[r[j],r[i]];}return r;}
function confetti(color){const cols=[color,'#FFE66D','#FF6B6B','#A29BFE','#4ECDC4','#FD79A8'];for(let i=0;i<30;i++){const p=document.createElement('div');p.className='cfbit';p.style.cssText=`left:${Math.random()*100}vw;top:-12px;background:${cols[0|Math.random()*cols.length]};border-radius:${Math.random()>.5?'50%':'3px'};width:${6+Math.random()*8}px;height:${6+Math.random()*8}px;animation-duration:${1.2+Math.random()*1.5}s;animation-delay:${Math.random()*.4}s;`;document.body.appendChild(p);p.addEventListener('animationend',()=>p.remove());}}
function speakText(w,l){if(!window.speechSynthesis)return;window.speechSynthesis.cancel();const u=new SpeechSynthesisUtterance(w);u.lang=l==='tl'?'fil-PH':'en-US';u.rate=0.8;u.pitch=1.1;window.speechSynthesis.speak(u);}

// ════════════════════════════════════════
// quizzes 1: NUMBER RECOGNITION
// ════════════════════════════════════════
const NR_EN={0:'Zero',1:'One',2:'Two',3:'Three',4:'Four',5:'Five',6:'Six',7:'Seven',8:'Eight',9:'Nine',10:'Ten',11:'Eleven',12:'Twelve',13:'Thirteen',14:'Fourteen',15:'Fifteen',16:'Sixteen',17:'Seventeen',18:'Eighteen',19:'Nineteen',20:'Twenty',21:'Twenty-one',22:'Twenty-two',23:'Twenty-three',24:'Twenty-four',25:'Twenty-five',26:'Twenty-six',27:'Twenty-seven',28:'Twenty-eight',29:'Twenty-nine',30:'Thirty',35:'Thirty-five',40:'Forty',45:'Forty-five',50:'Fifty',55:'Fifty-five',60:'Sixty',65:'Sixty-five',70:'Seventy',75:'Seventy-five',80:'Eighty',85:'Eighty-five',90:'Ninety',95:'Ninety-five',100:'One Hundred'};
const NR_TL={0:'Sero',1:'Isa',2:'Dalawa',3:'Tatlo',4:'Apat',5:'Lima',6:'Anim',7:'Pito',8:'Walo',9:'Siyam',10:'Sampu',11:'Labing-isa',12:'Labindalawa',13:'Labintatlo',14:'Labing-apat',15:'Labinlima',16:'Labing-anim',17:'Labimpito',18:'Labingwalo',19:'Labinsiyam',20:'Dalawampu',21:"Dalawampu't isa",22:"Dalawampu't dalawa",23:"Dalawampu't tatlo",24:"Dalawampu't apat",25:"Dalawampu't lima",26:"Dalawampu't anim",27:"Dalawampu't pito",28:"Dalawampu't walo",29:"Dalawampu't siyam",30:'Tatlumpu',35:"Tatlumpu't lima",40:'Apatnapu',45:"Apatnapu't lima",50:'Limampu',55:"Limampu't lima",60:'Animnapu',65:"Animnapu't lima",70:'Pitumpu',75:"Pitumpu't lima",80:'Walumpu',85:"Walumpu't lima",90:'Siyamnapu',95:"Siyamnapu't lima",100:'Isang Daan'};
const NR_POOL=Object.keys(NR_EN).map(Number);
const NR_COPY={en:{title:'Number Recognition!',sub:'See the number — choose the correct name!',prompt:'What is this number?',next:'Next',feedbackCorrect:n=>`✅ Yes! That's ${NR_EN[n]}!`,feedbackWrong:(c,n)=>`❌ That's ${NR_EN[c]}. It's ${NR_EN[n]}!`,resultTitle:'Number Star!',resultSub:'You know your numbers!'},tl:{title:'Pagkilala ng Numero!',sub:'Tingnan ang numero — piliin ang tamang pangalan!',prompt:'Ano ang numerong ito?',next:'Susunod',feedbackCorrect:n=>`✅ Tama! Iyon ay ${NR_TL[n]}!`,feedbackWrong:(c,n)=>`❌ Iyon ay ${NR_TL[c]}. Ito ay ${NR_TL[n]}!`,resultTitle:'Bituin ng Numero!',resultSub:'Alam mo ang iyong mga numero!'}};
const NR_TOTAL=10,NR_MAX=NR_TOTAL*10;
let nr_pool=[],nr_current=null,nr_color='',nr_answered=false,nr_score=0,nr_qIdx=0,nr_lives=3,nr_selected=null;

function nr_startGame(){
  nr_score=0;nr_qIdx=0;nr_lives=3;nr_answered=false;nr_selected=null;nr_pool=shuffle([...NR_POOL]);
  document.getElementById('p1Score').textContent='0';
  document.getElementById('resultOverlay').classList.remove('active');
  nr_updateLives();
  const c=NR_COPY[lang];
  document.getElementById('p1Title').textContent=c.title;
  document.getElementById('p1Sub').textContent=c.sub;
  document.getElementById('p1QPrompt').textContent=c.prompt;
  document.getElementById('p1NextLbl').textContent=c.next;
  document.getElementById('p1SubmitLbl').textContent=c.submit||'Submit';
  saveStart(ACT_IDS[1]);
  nr_loadQuestion();
}
function nr_updateLives(){document.querySelectorAll('#p1Lives .life-icon').forEach((ic,i)=>ic.classList.toggle('lost',i>=nr_lives));}
function nr_loadQuestion(){
  nr_answered=false;nr_selected=null;
  document.getElementById('p1NextBtn').style.display='none';
  document.getElementById('p1SubmitBtn').style.display='none';
  document.getElementById('p1Feedback').textContent='';document.getElementById('p1Feedback').className='feedback-msg';
  if(!nr_pool.length)nr_pool=shuffle([...NR_POOL]);
  nr_current=nr_pool.shift();
  nr_color=COLORS[nr_qIdx%COLORS.length];
  document.getElementById('p1QNum').textContent=nr_qIdx+1;
  document.getElementById('p1Bar').style.width=((nr_qIdx/NR_TOTAL)*100)+'%';
  document.getElementById('p1BarLbl').textContent=`${nr_qIdx} / ${NR_TOTAL}`;
  document.getElementById('p1QNumber').textContent=nr_current;
  document.getElementById('p1QNumber').style.color=nr_color;
  document.getElementById('p1QNumber').style.setProperty('--q-color',nr_color);
  const names=lang==='en'?NR_EN:NR_TL;
  const wrongNums=shuffle(NR_POOL.filter(n=>n!==nr_current)).slice(0,3);
  const choices=shuffle([nr_current,...wrongNums]);
  const grid=document.getElementById('p1Choices');grid.innerHTML='';
  choices.forEach((num,i)=>{
    const btn=document.createElement('button');btn.className='choice-btn-nr';
    btn.style.setProperty('--q-color',nr_color);btn.textContent=names[num]||String(num);
    btn.dataset.val=num;
    btn.onclick=()=>nr_selectChoice(btn,num);grid.appendChild(btn);
  });
  setTimeout(nr_speakNum,400);
}
function nr_selectChoice(btn,chosen){
  if(nr_answered)return;
  nr_selected=chosen;
  document.querySelectorAll('#p1Choices .choice-btn-nr').forEach(b=>b.classList.remove('selected'));
  btn.classList.add('selected');
  document.getElementById('p1SubmitBtn').style.display='inline-flex';
}
function nr_submitAnswer(){
  if(nr_answered||nr_selected===null)return;
  nr_answered=true;
  document.getElementById('p1SubmitBtn').style.display='none';
  document.querySelectorAll('#p1Choices .choice-btn-nr').forEach(b=>b.classList.add('answered'));
  const names=lang==='en'?NR_EN:NR_TL;const c=NR_COPY[lang];
  const chosenBtn=document.querySelector(`#p1Choices .choice-btn-nr.selected`);
  if(nr_selected===nr_current){
    if(chosenBtn)chosenBtn.classList.add('correct');
    nr_score+=10; saveProgress(ACT_IDS[1],Math.round((nr_score/NR_MAX)*100));document.getElementById('p1Score').textContent=nr_score;
    document.getElementById('p1Feedback').textContent=c.feedbackCorrect(nr_current);
    document.getElementById('p1Feedback').className='feedback-msg correct';
    confetti(nr_color);speakText(names[nr_current],lang);nr_qIdx++;
    if(nr_qIdx>=NR_TOTAL||nr_lives<=0)setTimeout(nr_showResult,900);
    else document.getElementById('p1NextBtn').style.display='inline-flex';
  } else {
    if(chosenBtn)chosenBtn.classList.add('wrong');
    document.querySelectorAll('#p1Choices .choice-btn-nr').forEach(b=>{if(parseInt(b.dataset.val)===nr_current)b.classList.add('correct');});
    nr_lives--;nr_updateLives();
    document.getElementById('p1Feedback').textContent=c.feedbackWrong(nr_selected,nr_current);
    document.getElementById('p1Feedback').className='feedback-msg wrong';
    nr_qIdx++;if(nr_lives<=0)setTimeout(nr_showResult,1100);else document.getElementById('p1NextBtn').style.display='inline-flex';
  }
}
function nr_nextQuestion(){nr_loadQuestion();}
function nr_showResult(){
  document.getElementById('resultTitle').textContent=NR_COPY[lang].resultTitle;
  document.getElementById('resultSub').textContent=NR_COPY[lang].resultSub;
  showResult(1,nr_score,NR_MAX);
}

// ════════════════════════════════════════
// quizzes 2: COUNTING QUIZ
// ════════════════════════════════════════
const CQ_EMOJIS=[{emoji:'🍎',en:'Apple',tl:'Mansanas'},{emoji:'⭐',en:'Star',tl:'Bituin'},{emoji:'🍭',en:'Candy',tl:'Kendi'},{emoji:'🌸',en:'Flower',tl:'Bulaklak'},{emoji:'🏀',en:'Ball',tl:'Bola'},{emoji:'🦋',en:'Butterfly',tl:'Mariposa'},{emoji:'🐶',en:'Dog',tl:'Aso'},{emoji:'🎈',en:'Balloon',tl:'Lobo'},{emoji:'🍕',en:'Pizza',tl:'Pizza'},{emoji:'🐾',en:'Paw',tl:'Paa'}];
const CQ_COPY={en:{title:'Counting Quiz!',sub:'Count the objects and choose the correct number!',prompt:'How many objects do you see?',hint:'Count them carefully!',next:'Next',feedbackCorrect:n=>`✅ Correct! There are ${n}!`,feedbackWrong:(c,n)=>`❌ There are ${n}, not ${c}!`,resultTitle:'Counting Champion!',resultSub:'You counted them all!'},tl:{title:'Counting Quiz!',sub:'Bilangin ang mga bagay at piliin ang tamang numero!',prompt:'Ilang bagay ang nakikita mo?',hint:'Bilangin nang mabuti!',next:'Susunod',feedbackCorrect:n=>`✅ Tama! May ${n} sila!`,feedbackWrong:(c,n)=>`❌ May ${n} sila, hindi ${c}!`,resultTitle:'Kampeon ng Pagbibilang!',resultSub:'Nabilang mo silang lahat!'}};
const CQ_TOTAL=10,CQ_MAX=CQ_TOTAL*10;
let cq_current=null,cq_color='',cq_answered=false,cq_score=0,cq_qIdx=0,cq_lives=3,cq_selected=null;
function cq_randInt(min,max){return Math.floor(Math.random()*(max-min+1))+min;}
function cq_pickCount(){const ranges=[{min:1,max:10,w:4},{min:11,max:20,w:3},{min:21,max:30,w:2},{min:31,max:50,w:1}];let r=Math.random()*10;for(const rg of ranges){r-=rg.w;if(r<=0)return cq_randInt(rg.min,rg.max);}return cq_randInt(1,10);}

function cq_startGame(){
  cq_score=0;cq_qIdx=0;cq_lives=3;cq_answered=false;cq_selected=null;
  document.getElementById('p2Score').textContent='0';
  document.getElementById('resultOverlay').classList.remove('active');
  cq_updateLives();
  const c=CQ_COPY[lang];
  document.getElementById('p2Title').textContent=c.title;
  document.getElementById('p2Sub').textContent=c.sub;
  document.getElementById('p2NextLbl').textContent=c.next;
  document.getElementById('p2SubmitLbl').textContent=c.submit||'Submit';
  saveStart(ACT_IDS[2]);
  cq_loadQuestion();
}
function cq_updateLives(){document.querySelectorAll('#p2Lives .life-icon').forEach((ic,i)=>ic.classList.toggle('lost',i>=cq_lives));}
function cq_loadQuestion(){
  cq_answered=false;cq_selected=null;
  document.getElementById('p2NextBtn').style.display='none';
  document.getElementById('p2SubmitBtn').style.display='none';
  document.getElementById('p2Feedback').textContent='';document.getElementById('p2Feedback').className='feedback-msg';
  cq_current=cq_pickCount();
  cq_color=COLORS[cq_qIdx%COLORS.length];
  const c=CQ_COPY[lang];
  document.getElementById('p2QNum').textContent=cq_qIdx+1;
  document.getElementById('p2Bar').style.width=((cq_qIdx/CQ_TOTAL)*100)+'%';
  document.getElementById('p2BarLbl').textContent=`${cq_qIdx} / ${CQ_TOTAL}`;
  document.getElementById('p2CountPrompt').textContent=c.prompt;
  document.getElementById('p2CountHint').textContent=c.hint;
  const eset=CQ_EMOJIS[Math.floor(Math.random()*CQ_EMOJIS.length)];
  const stage=document.getElementById('p2EmojiStage');stage.innerHTML='';
  for(let i=0;i<cq_current;i++){const s=document.createElement('span');s.className='count-emoji';s.textContent=eset.emoji;s.style.animationDelay=(i*.04)+'s';stage.appendChild(s);}
  const wrongSet=new Set();
  while(wrongSet.size<3){const w=cq_randInt(Math.max(1,cq_current-5),cq_current+5);if(w!==cq_current&&w>=1&&w<=100)wrongSet.add(w);}
  const choices=shuffle([cq_current,...wrongSet]);
  const choicesEl=document.getElementById('p2NumChoices');choicesEl.innerHTML='';
  choices.forEach((num,i)=>{
    const btn=document.createElement('button');btn.className='num-btn';
    const cc=COLORS[i%COLORS.length];btn.style.setProperty('--nb-color',cc);btn.style.color=cc;btn.textContent=num;
    btn.dataset.val=num;
    btn.onclick=()=>cq_selectChoice(btn,num);choicesEl.appendChild(btn);
  });
}
function cq_selectChoice(btn,chosen){
  if(cq_answered)return;
  cq_selected=chosen;
  document.querySelectorAll('#p2NumChoices .num-btn').forEach(b=>b.classList.remove('selected'));
  btn.classList.add('selected');
  document.getElementById('p2SubmitBtn').style.display='inline-flex';
}
function cq_submitAnswer(){
  if(cq_answered||cq_selected===null)return;
  cq_answered=true;
  document.getElementById('p2SubmitBtn').style.display='none';
  document.querySelectorAll('#p2NumChoices .num-btn').forEach(b=>b.classList.add('answered'));
  const c=CQ_COPY[lang];
  const chosenBtn=document.querySelector('#p2NumChoices .num-btn.selected');
  if(cq_selected===cq_current){
    if(chosenBtn)chosenBtn.classList.add('correct');
    cq_score+=10; saveProgress(ACT_IDS[2],Math.round((cq_score/CQ_MAX)*100));document.getElementById('p2Score').textContent=cq_score;
    document.getElementById('p2Feedback').textContent=c.feedbackCorrect(cq_current);
    document.getElementById('p2Feedback').className='feedback-msg correct';
    confetti(cq_color);speakText(String(cq_current),'en');cq_qIdx++;
    if(cq_qIdx>=CQ_TOTAL||cq_lives<=0)setTimeout(cq_showResult,900);
    else document.getElementById('p2NextBtn').style.display='inline-flex';
  } else {
    if(chosenBtn)chosenBtn.classList.add('wrong');
    document.querySelectorAll('#p2NumChoices .num-btn').forEach(b=>{if(parseInt(b.dataset.val)===cq_current)b.classList.add('correct');});
    cq_lives--;cq_updateLives();
    document.getElementById('p2Feedback').textContent=c.feedbackWrong(cq_selected,cq_current);
    document.getElementById('p2Feedback').className='feedback-msg wrong';
    cq_qIdx++;if(cq_lives<=0)setTimeout(cq_showResult,1100);else document.getElementById('p2NextBtn').style.display='inline-flex';
  }
}
function cq_nextQuestion(){cq_loadQuestion();}
function cq_showResult(){
  document.getElementById('resultTitle').textContent=CQ_COPY[lang].resultTitle;
  document.getElementById('resultSub').textContent=CQ_COPY[lang].resultSub;
  showResult(2,cq_score,CQ_MAX);
}

// ════════════════════════════════════════
// quizzes 3: NUMBER ORDER
// ════════════════════════════════════════
const NO_COLORS=['#FF8E53','#4D96FF','#6BCB77','#845EC2','#FF6B6B','#F9A825','#00ACC1','#FD79A8'];
const NO_COPY={
  en:{title:'Number Order!',sub:'Arrange the numbers from smallest to biggest!',prompt:'Arrange from smallest ➡️ to biggest',tilesLabel:'Tap numbers in order:',clear:'Clear',submitLbl:'Submit Round',nextLbl:'Next Round',finishLbl:'See Results',feedbackCorrect:(pts)=>`✅ Perfect order! +${pts} pts earned!`,feedbackPartial:(c,t,pts)=>`⭐ ${c}/${t} correct! +${pts} pts earned.`,progressLbl:(d,t)=>`${d} / ${t} rounds`,resultTitle:'In Order!',resultSub:'You sorted all the numbers!'},
  tl:{title:'Ayos ng Numero!',sub:'Ayusin ang mga numero mula pinakamaliit hanggang pinakamalaki!',prompt:'Ayusin mula pinakamaliit ➡️ hanggang pinakamalaki',tilesLabel:'I-tap ang mga numero nang sunod-sunod:',clear:'Burahin',submitLbl:'I-submit ang Round',nextLbl:'Susunod na Round',finishLbl:'Tingnan ang Resulta',feedbackCorrect:(pts)=>`✅ Perpektong pagkakaayos! +${pts} pts nakuha!`,feedbackPartial:(c,t,pts)=>`⭐ ${c}/${t} tama! +${pts} pts nakuha.`,progressLbl:(d,t)=>`${d} / ${t} rounds`,resultTitle:'Nasa Ayos Na!',resultSub:'Nayos mo ang lahat ng numero!'}
};
const NO_TOTAL_ROUNDS=5, NO_NUMS_PER_ROUND=4, NO_PPM=5; // +5 per correct pos, -5 per wrong
const NO_MAX=NO_TOTAL_ROUNDS*NO_NUMS_PER_ROUND*NO_PPM; // 5×4×5=100
let no_score=0,no_round=0,no_currentNums=[],no_sortedAnswer=[],no_userAnswer=[],no_submitted=false;
function no_randInt(min,max){return Math.floor(Math.random()*(max-min+1))+min;}
function no_generateSet(){
  const base=Math.min((no_round-1)*15,60);
  const min=Math.max(1,base);const max=Math.min(100,base+50);
  const nums=new Set();
  while(nums.size<NO_NUMS_PER_ROUND)nums.add(no_randInt(min,max));
  return [...nums];
}

function no_startGame(){
  no_score=0;no_round=0;no_submitted=false;
  document.getElementById('p3Score').textContent='0';
  document.getElementById('resultOverlay').classList.remove('active');
  const c=NO_COPY[lang];
  document.getElementById('p3Title').textContent=c.title;
  document.getElementById('p3Sub').textContent=c.sub;
  document.getElementById('p3ClearLbl').textContent=c.clear;
  document.getElementById('p3SubmitLbl').textContent=c.submitLbl;
  document.getElementById('p3NextLbl').textContent=c.nextLbl;
  saveStart(ACT_IDS[3]);
  no_nextRound();
}
function no_nextRound(){
  no_round++;no_userAnswer=[];no_submitted=false;
  document.getElementById('p3Round').textContent=no_round;
  document.getElementById('p3Bar').style.width=(((no_round-1)/NO_TOTAL_ROUNDS)*100)+'%';
  document.getElementById('p3BarLbl').textContent=NO_COPY[lang].progressLbl(no_round-1,NO_TOTAL_ROUNDS);
  document.getElementById('p3SubmitBtn').style.display='none';
  document.getElementById('p3NextBtn').style.display='none';
  document.getElementById('p3Feedback').textContent='';document.getElementById('p3Feedback').className='feedback-msg';
  document.getElementById('p3GamePrompt').textContent=NO_COPY[lang].prompt;
  document.getElementById('p3TilesLabel').textContent=NO_COPY[lang].tilesLabel;
  const isLast=no_round>=NO_TOTAL_ROUNDS;
  document.getElementById('p3NextLbl').textContent=isLast?NO_COPY[lang].finishLbl:NO_COPY[lang].nextLbl;
  no_currentNums=no_generateSet();
  no_sortedAnswer=[...no_currentNums].sort((a,b)=>a-b);
  no_renderSlots();no_renderTiles(shuffle([...no_currentNums]));
}
function no_renderSlots(){
  const row=document.getElementById('p3SlotsRow');row.innerHTML='';
  no_sortedAnswer.forEach((_,i)=>{
    if(i>0){const arr=document.createElement('span');arr.className='arrow-icon';arr.innerHTML='<i class="fas fa-chevron-right"></i>';row.appendChild(arr);}
    const slot=document.createElement('div');slot.className='order-slot';slot.id=`p3slot-${i}`;slot.style.setProperty('--sc',NO_COLORS[i]);
    const numEl=document.createElement('div');numEl.className='slot-num';numEl.style.color=NO_COLORS[i];
    const posEl=document.createElement('div');posEl.className='slot-pos';posEl.textContent=`#${i+1}`;
    slot.appendChild(numEl);slot.appendChild(posEl);slot.onclick=()=>{ if(!no_submitted) no_removeFromSlot(i); };row.appendChild(slot);
  });
}
function no_renderTiles(nums){
  const row=document.getElementById('p3TilesRow');row.innerHTML='';
  nums.forEach((n,i)=>{
    const tile=document.createElement('div');tile.className='num-tile';
    tile.style.setProperty('--tc',NO_COLORS[i]);tile.style.color=NO_COLORS[i];tile.style.borderColor=NO_COLORS[i];
    tile.textContent=n;tile.id=`p3tile-${n}`;
    tile.onclick=()=>no_placeNumber(n,tile,NO_COLORS[i]);row.appendChild(tile);
  });
}
function no_placeNumber(num,tile,color){
  if(no_submitted)return;
  if(tile.classList.contains('placed'))return;
  const nextSlot=no_userAnswer.length;if(nextSlot>=NO_NUMS_PER_ROUND)return;
  no_userAnswer.push(num);tile.classList.add('placed');
  const slot=document.getElementById(`p3slot-${nextSlot}`);
  if(slot){slot.querySelector('.slot-num').textContent=num;slot.classList.add('has-num');slot.style.setProperty('--sc',color);}
  // Show submit button once all slots filled
  if(no_userAnswer.length===NO_NUMS_PER_ROUND){
    document.getElementById('p3SubmitBtn').style.display='inline-flex';
  }
}
function no_removeFromSlot(slotIdx){
  if(no_submitted)return;
  if(slotIdx>=no_userAnswer.length)return;
  const removed=no_userAnswer.splice(slotIdx);
  removed.forEach(n=>{const t=document.getElementById(`p3tile-${n}`);if(t)t.classList.remove('placed');});
  for(let i=slotIdx;i<NO_NUMS_PER_ROUND;i++){const s=document.getElementById(`p3slot-${i}`);if(s){s.querySelector('.slot-num').textContent='';s.classList.remove('has-num','correct-pos','wrong-pos');}}
  document.getElementById('p3SubmitBtn').style.display='none';
}
function no_clearSlots(){
  if(no_submitted)return;
  no_userAnswer=[];
  document.querySelectorAll('#p3TilesRow .num-tile').forEach(t=>t.classList.remove('placed'));
  document.querySelectorAll('#p3SlotsRow .order-slot').forEach(s=>{s.querySelector('.slot-num').textContent='';s.classList.remove('has-num','correct-pos','wrong-pos');});
  document.getElementById('p3Feedback').textContent='';
  document.getElementById('p3SubmitBtn').style.display='none';
}
function no_submitRound(){
  if(no_submitted||no_userAnswer.length<NO_NUMS_PER_ROUND)return;
  no_submitted=true;
  document.getElementById('p3SubmitBtn').style.display='none';
  // Disable clear and tiles
  document.querySelectorAll('#p3TilesRow .num-tile').forEach(t=>t.style.pointerEvents='none');
  // Reveal correct/wrong per slot
  let correctCount=0;
  no_userAnswer.forEach((n,i)=>{
    const s=document.getElementById(`p3slot-${i}`);
    if(n===no_sortedAnswer[i]){
      correctCount++;
      if(s)s.classList.add('correct-pos');
    } else {
      if(s)s.classList.add('wrong-pos');
    }
  });
  // Score: +5 per correct position, no penalty for wrong
  const roundGain=correctCount*NO_PPM;
  no_score=no_score+roundGain;
  document.getElementById('p3Score').textContent=no_score;
  saveProgress(ACT_IDS[3],Math.min(100,Math.round((no_score/NO_MAX)*100)));
  document.getElementById('p3Bar').style.width=((no_round/NO_TOTAL_ROUNDS)*100)+'%';
  document.getElementById('p3BarLbl').textContent=NO_COPY[lang].progressLbl(no_round,NO_TOTAL_ROUNDS);
  const c=NO_COPY[lang];
  if(correctCount===NO_NUMS_PER_ROUND){
    document.getElementById('p3Feedback').textContent=c.feedbackCorrect(roundGain);
    document.getElementById('p3Feedback').className='feedback-msg correct';
    confetti(NO_COLORS[no_round%NO_COLORS.length]);
  } else {
    const pts=roundGain;
    document.getElementById('p3Feedback').textContent=c.feedbackPartial(correctCount,NO_NUMS_PER_ROUND,pts);
    document.getElementById('p3Feedback').className=pts>0?'feedback-msg correct':'feedback-msg wrong';
  }
  document.getElementById('p3NextBtn').style.display='inline-flex';
  const isLast=no_round>=NO_TOTAL_ROUNDS;
  document.getElementById('p3NextLbl').textContent=isLast?c.finishLbl:c.nextLbl;
}
function no_proceedNext(){
  if(no_round>=NO_TOTAL_ROUNDS)no_showResult();
  else no_nextRound();
}
function no_showResult(){
  document.getElementById('resultTitle').textContent=NO_COPY[lang].resultTitle;
  document.getElementById('resultSub').textContent=NO_COPY[lang].resultSub;
  showResult(3,no_score,NO_MAX);
}

// ════════════════════════════════════════
// quizzes 4: ODD OR EVEN
// ════════════════════════════════════════
const OE_COPY={en:{title:'Odd or Even?',sub:'Is the number odd or even? Tap your answer!',prompt:'Is this number odd or even?',oddLabel:'Odd',oddHint:'1, 3, 5...',evenLabel:'Even',evenHint:'2, 4, 6...',legendOdd:'Odd — cannot be split equally in 2',legendEven:'Even — can be split equally in 2',next:'Next',feedbackCorrectOdd:n=>`✅ Yes! ${n} is ODD!`,feedbackCorrectEven:n=>`✅ Yes! ${n} is EVEN!`,feedbackWrongOdd:n=>`❌ ${n} is ODD — it can't be split equally!`,feedbackWrongEven:n=>`❌ ${n} is EVEN — it can be split equally!`,resultTitle:'Odd-tastic!',resultSub:'You know odd and even!'},tl:{title:'Odd o Even?',sub:'Ang numero ba ay odd o even? I-tap ang sagot mo!',prompt:'Ang numerong ito ay odd o even?',oddLabel:'Odd',oddHint:'1, 3, 5...',evenLabel:'Even',evenHint:'2, 4, 6...',legendOdd:'Odd — hindi mahahati nang pantay sa 2',legendEven:'Even — mahahati nang pantay sa 2',next:'Susunod',feedbackCorrectOdd:n=>`✅ Tama! Ang ${n} ay ODD!`,feedbackCorrectEven:n=>`✅ Tama! Ang ${n} ay EVEN!`,feedbackWrongOdd:n=>`❌ Ang ${n} ay ODD — hindi mahahati nang pantay!`,feedbackWrongEven:n=>`❌ Ang ${n} ay EVEN — mahahati nang pantay!`,resultTitle:'Odd-tastic!',resultSub:'Alam mo na ang odd at even!'}};
const OE_TOTAL=10,OE_MAX=OE_TOTAL*10;
let oe_pool=[],oe_current=null,oe_answered=false,oe_score=0,oe_qIdx=0,oe_lives=3,oe_selected=null;
function oe_buildPool(){const nums=[];for(let i=0;i<60;i++)nums.push(Math.floor(Math.random()*100)+1);return nums;}

function oe_startGame(){
  oe_score=0;oe_qIdx=0;oe_lives=3;oe_answered=false;oe_selected=null;oe_pool=oe_buildPool();
  document.getElementById('p4Score').textContent='0';
  document.getElementById('resultOverlay').classList.remove('active');
  oe_updateLives();
  const c=OE_COPY[lang];
  document.getElementById('p4Title').textContent=c.title;
  document.getElementById('p4Sub').textContent=c.sub;
  document.getElementById('p4LegendOdd').textContent=c.legendOdd;
  document.getElementById('p4LegendEven').textContent=c.legendEven;
  document.getElementById('p4OddLabel').textContent=c.oddLabel;
  document.getElementById('p4OddHint').textContent=c.oddHint;
  document.getElementById('p4EvenLabel').textContent=c.evenLabel;
  document.getElementById('p4EvenHint').textContent=c.evenHint;
  document.getElementById('p4NextLbl').textContent=c.next;
  document.getElementById('p4SubmitLbl').textContent=c.submit||'Submit';
  saveStart(ACT_IDS[4]);
  oe_loadQuestion();
}
function oe_updateLives(){document.querySelectorAll('#p4Lives .life-icon').forEach((ic,i)=>ic.classList.toggle('lost',i>=oe_lives));}
function oe_loadQuestion(){
  oe_answered=false;oe_selected=null;
  document.getElementById('p4NextBtn').style.display='none';
  document.getElementById('p4SubmitBtn').style.display='none';
  document.getElementById('p4Feedback').textContent='';document.getElementById('p4Feedback').className='feedback-msg';
  document.getElementById('p4OddBtn').className='oe-btn odd-btn';
  document.getElementById('p4EvenBtn').className='oe-btn even-btn';
  if(!oe_pool.length)oe_pool=oe_buildPool();
  oe_current=oe_pool.shift();
  const isEven=oe_current%2===0;
  const color=isEven?'#4D96FF':'#845EC2';
  document.getElementById('p4QNum').textContent=oe_qIdx+1;
  document.getElementById('p4Bar').style.width=((oe_qIdx/OE_TOTAL)*100)+'%';
  document.getElementById('p4BarLbl').textContent=`${oe_qIdx} / ${OE_TOTAL}`;
  document.getElementById('p4StagePrompt').textContent=OE_COPY[lang].prompt;
  document.getElementById('p4BigNumber').textContent=oe_current;
  document.getElementById('p4BigNumber').style.color=color;
  document.getElementById('p4BigNumber').style.setProperty('--n-color',color);
  const dotCount=Math.min(oe_current,20);
  const dotsEl=document.getElementById('p4VisualDots');dotsEl.innerHTML='';
  for(let i=0;i<dotCount;i++){const d=document.createElement('div');d.className='v-dot';d.style.background=color;d.style.animationDelay=(i*.03)+'s';dotsEl.appendChild(d);}
  if(oe_current>20){const more=document.createElement('span');more.style.cssText=`font-family:'Fredoka One',cursive;font-size:.75rem;color:#bbb;margin-top:4px;`;more.textContent=`+${oe_current-20} more`;dotsEl.appendChild(more);}
  setTimeout(oe_speakNum,400);
}
function oe_speakNum(){if(oe_current!==null)speakText(String(oe_current),'en');}
function oe_pickAnswer(type){
  if(oe_answered)return;
  oe_selected=type;
  document.getElementById('p4OddBtn').className='oe-btn odd-btn';
  document.getElementById('p4EvenBtn').className='oe-btn even-btn';
  document.getElementById(type==='odd'?'p4OddBtn':'p4EvenBtn').classList.add('selected');
  document.getElementById('p4SubmitBtn').style.display='inline-flex';
}
function oe_submitAnswer(){
  if(oe_answered||oe_selected===null)return;
  oe_answered=true;
  document.getElementById('p4SubmitBtn').style.display='none';
  document.getElementById('p4OddBtn').classList.add('answered');
  document.getElementById('p4EvenBtn').classList.add('answered');
  const isEven=oe_current%2===0;
  const isCorrect=(oe_selected==='even'&&isEven)||(oe_selected==='odd'&&!isEven);
  const c=OE_COPY[lang];
  if(isCorrect){
    document.getElementById(oe_selected==='odd'?'p4OddBtn':'p4EvenBtn').classList.add('correct');
    oe_score+=10; saveProgress(ACT_IDS[4],Math.round((oe_score/OE_MAX)*100));document.getElementById('p4Score').textContent=oe_score;
    const msg=isEven?c.feedbackCorrectEven(oe_current):c.feedbackCorrectOdd(oe_current);
    document.getElementById('p4Feedback').textContent=msg;
    document.getElementById('p4Feedback').className='feedback-msg correct';
    confetti(isEven?'#4D96FF':'#845EC2');oe_qIdx++;
    if(oe_qIdx>=OE_TOTAL||oe_lives<=0)setTimeout(oe_showResult,900);
    else document.getElementById('p4NextBtn').style.display='inline-flex';
  } else {
    document.getElementById(oe_selected==='odd'?'p4OddBtn':'p4EvenBtn').classList.add('wrong');
    document.getElementById(oe_selected==='odd'?'p4EvenBtn':'p4OddBtn').classList.add('correct');
    oe_lives--;oe_updateLives();
    const msg=isEven?c.feedbackWrongEven(oe_current):c.feedbackWrongOdd(oe_current);
    document.getElementById('p4Feedback').textContent=msg;
    document.getElementById('p4Feedback').className='feedback-msg wrong';
    oe_qIdx++;if(oe_lives<=0)setTimeout(oe_showResult,1100);else document.getElementById('p4NextBtn').style.display='inline-flex';
  }
}
function oe_nextQuestion(){oe_loadQuestion();}
function oe_showResult(){
  document.getElementById('resultTitle').textContent=OE_COPY[lang].resultTitle;
  document.getElementById('resultSub').textContent=OE_COPY[lang].resultSub;
  showResult(4,oe_score,OE_MAX);
}


// ── FLUSH PROGRESS ON NAVIGATION / PAGE HIDE ──
function flushActiveTabProgress() {
  switch(activeTab) {
    case 1: if(nr_score>0) saveProgress(ACT_IDS[1],Math.round((nr_score/NR_MAX)*100)); break;
    case 2: if(cq_score>0) saveProgress(ACT_IDS[2],Math.round((cq_score/CQ_MAX)*100)); break;
    case 3: if(no_score>0) saveProgress(ACT_IDS[3],Math.min(100,Math.round((no_score/NO_MAX)*100))); break;
    case 4: if(oe_score>0) saveProgress(ACT_IDS[4],Math.round((oe_score/OE_MAX)*100)); break;
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
if(activeTab===1)nr_startGame();
else if(activeTab===2)cq_startGame();
else if(activeTab===3)no_startGame();
else if(activeTab===4)oe_startGame();
</script>

</div><!-- /page-wrap -->
</body>
</html>