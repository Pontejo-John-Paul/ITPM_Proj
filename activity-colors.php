<?php
session_start();
require_once 'database.php';
require_once 'activity_helper.php';

if (!isset($_SESSION['student_id']) || $_SESSION['role'] !== 'student') {
    header("Location: index.php");
    exit;
}

$student_id = $_SESSION['student_id'];
$lesson_id  = isset($_GET['lesson_id']) ? (int)$_GET['lesson_id'] : 5; // Colors = 5

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

$a1 = $act_map['color_match']       ?? ['quizzes_id'=>0,'status'=>'not_started','score'=>0];
$a2 = $act_map['spot_the_color']    ?? ['quizzes_id'=>0,'status'=>'not_started','score'=>0];
$a3 = $act_map['color_mixing_quiz'] ?? ['quizzes_id'=>0,'status'=>'not_started','score'=>0];
$a4 = $act_map['color_hunt_quiz']   ?? ['quizzes_id'=>0,'status'=>'not_started','score'=>0];

$active_tab = isset($_GET['tab']) ? max(1, min(4, (int)$_GET['tab'])) : 1;
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Colors Activities — E-KINDER</title>
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
.act-tab:hover:not(.active){border-color:#e0d0ff;color:#555;transform:translateY(-2px);}
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

/* ── ACT 1: COLOR MATCH ── */
.game-area{max-width:700px;margin:0 auto;}
.round-info{font-family:'Fredoka One',cursive;font-size:.85rem;color:#bbb;text-align:center;margin-bottom:20px;text-transform:uppercase;letter-spacing:1px;}
.match-container{display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:20px;}
.match-col-label{font-family:'Fredoka One',cursive;font-size:.78rem;color:#888;text-transform:uppercase;letter-spacing:1px;text-align:center;margin-bottom:12px;}
.match-col{display:flex;flex-direction:column;gap:12px;}
.name-card{background:#fff;border:3px solid #f0f0f0;border-radius:18px;padding:16px 14px;text-align:center;cursor:pointer;box-shadow:0 4px 14px rgba(0,0,0,.06);transition:all .25s cubic-bezier(.34,1.56,.64,1);}
.name-card:hover:not(.matched):not(.disabled){transform:translateY(-4px) scale(1.03);box-shadow:0 10px 26px rgba(0,0,0,.1);}
.name-card.selected{border-color:var(--card-color,#a855f7);background:color-mix(in srgb,var(--card-color,#a855f7) 10%,white);transform:translateY(-4px) scale(1.04);}
.name-card.matched{border-color:#40c057;background:#f0fdf4;cursor:default;animation:matchPop .4s cubic-bezier(.34,1.56,.64,1);}
.name-card.wrong{border-color:#ff4444;background:#fff5f5;animation:cardShake .4s ease;}
.name-text{font-family:'Fredoka One',cursive;font-size:1.3rem;color:var(--card-color,#555);}
.name-local{font-size:.7rem;color:#ccc;font-weight:700;margin-top:2px;}
.match-check-icon{font-size:.85rem;color:#40c057;opacity:0;transition:opacity .2s;display:block;margin-top:4px;}
.name-card.matched .match-check-icon{opacity:1;}
.swatch-card{background:#fff;border:3px solid #f0f0f0;border-radius:18px;padding:14px 10px;text-align:center;cursor:pointer;box-shadow:0 4px 14px rgba(0,0,0,.06);transition:all .25s cubic-bezier(.34,1.56,.64,1);position:relative;}
.swatch-card:hover:not(.matched):not(.disabled){transform:translateY(-4px) scale(1.03);box-shadow:0 10px 26px rgba(0,0,0,.1);}
.swatch-card.selected{border-color:var(--card-color,#a855f7);transform:translateY(-4px) scale(1.04);}
.swatch-card.matched{border-color:#40c057;cursor:default;animation:matchPop .4s cubic-bezier(.34,1.56,.64,1);}
.swatch-card.wrong{border-color:#ff4444;animation:cardShake .4s ease;}
.color-circle{width:70px;height:70px;border-radius:50%;background:var(--swatch-color,#ccc);margin:0 auto;box-shadow:0 4px 14px rgba(0,0,0,.18);border:4px solid rgba(255,255,255,.8);transition:transform .3s cubic-bezier(.34,1.56,.64,1);}
.swatch-card:hover:not(.matched) .color-circle{transform:scale(1.12) rotate(-8deg);}
.swatch-check{position:absolute;top:8px;right:8px;width:24px;height:24px;border-radius:50%;background:#40c057;color:#fff;display:flex;align-items:center;justify-content:center;font-size:.7rem;opacity:0;transition:opacity .2s;}
.swatch-card.matched .swatch-check{opacity:1;}

/* ── ACT 2: SPOT THE COLOR ── */
.question-card{background:#fff;border-radius:28px;padding:32px 24px;text-align:center;box-shadow:0 8px 36px rgba(0,0,0,.08);border:2px solid #f0ece4;max-width:520px;margin:0 auto 24px;}
.q-prompt{font-family:'Fredoka One',cursive;font-size:.9rem;color:#bbb;text-transform:uppercase;letter-spacing:1px;margin-bottom:16px;}
.q-name{font-family:'Fredoka One',cursive;font-size:3.2rem;line-height:1;margin-bottom:10px;animation:nameBounce 2s ease-in-out infinite;}
@keyframes nameBounce{0%,100%{transform:translateY(0) rotate(-2deg)}50%{transform:translateY(-12px) rotate(2deg)}}
.q-speak-btn{background:none;border:none;font-size:1.2rem;cursor:pointer;color:var(--q-color,#a855f7);transition:transform .2s;margin-top:4px;}
.q-speak-btn:hover{transform:scale(1.2);}
.choices-grid-4{display:grid;grid-template-columns:repeat(4,1fr);gap:16px;max-width:560px;margin:0 auto 24px;}
@media(max-width:480px){.choices-grid-4{grid-template-columns:repeat(2,1fr);}}

/* ── ACT 3: COLOR MIXING ── */
.mix-card{background:#fff;border-radius:28px;padding:32px 24px;text-align:center;box-shadow:0 8px 36px rgba(0,0,0,.08);border:2px solid #f0ece4;max-width:520px;margin:0 auto 28px;}
.mix-prompt{font-family:'Fredoka One',cursive;font-size:.9rem;color:#bbb;text-transform:uppercase;letter-spacing:1px;margin-bottom:20px;}
.mix-equation{display:flex;align-items:center;justify-content:center;gap:16px;flex-wrap:wrap;margin-bottom:8px;}
.mix-circle{width:80px;height:80px;border-radius:50%;box-shadow:0 6px 20px rgba(0,0,0,.18);border:4px solid rgba(255,255,255,.8);flex-shrink:0;transition:transform .3s cubic-bezier(.34,1.56,.64,1);}
.mix-circle.pop{animation:circlePop .4s cubic-bezier(.34,1.56,.64,1);}
@keyframes circlePop{0%{transform:scale(.8)}60%{transform:scale(1.15)}100%{transform:scale(1)}}
.mix-op{font-family:'Fredoka One',cursive;font-size:1.8rem;color:#ddd;}
.mix-result-circle{width:80px;height:80px;border-radius:50%;background:#f0f0f0;box-shadow:0 6px 20px rgba(0,0,0,.1);border:4px solid #e8e8e8;flex-shrink:0;display:flex;align-items:center;justify-content:center;font-size:1.8rem;transition:all .4s;}
.mix-c1-label,.mix-c2-label{font-family:'Fredoka One',cursive;font-size:.85rem;color:#888;margin-top:6px;}
.choices-grid-4mx{display:grid;grid-template-columns:repeat(4,1fr);gap:14px;max-width:540px;margin:0 auto 20px;}
@media(max-width:480px){.choices-grid-4mx{grid-template-columns:repeat(2,1fr);}}

/* ── ACT 4: COLOR HUNT ── */
.object-card{background:#fff;border-radius:28px;padding:28px 24px;text-align:center;box-shadow:0 8px 36px rgba(0,0,0,.08);border:2px solid #f0ece4;max-width:440px;margin:0 auto 28px;}
.obj-prompt{font-family:'Fredoka One',cursive;font-size:.9rem;color:#bbb;text-transform:uppercase;letter-spacing:1px;margin-bottom:16px;}
.obj-emoji{font-size:5rem;line-height:1;margin-bottom:12px;animation:emojiFloat 2.5s ease-in-out infinite;}
@keyframes emojiFloat{0%,100%{transform:translateY(0) rotate(-3deg)}50%{transform:translateY(-14px) rotate(3deg)}}
.obj-name{font-family:'Fredoka One',cursive;font-size:1.8rem;color:#2d2d2d;margin-bottom:6px;}
.obj-question{font-size:.9rem;font-weight:700;color:#aaa;}
/* image inside object-card (Color Hunt) reuses fe-q-img style */
#p4ImgWrap .fe-q-img{margin-bottom:14px;}
#p4ImgWrap .fe-q-fallback{margin:0 auto 14px;}
.choices-grid-5{display:grid;grid-template-columns:repeat(5,1fr);gap:12px;max-width:520px;margin:0 auto 20px;}
@media(max-width:460px){.choices-grid-5{grid-template-columns:repeat(3,1fr);gap:10px;}}

/* SHARED CHOICE CARDS */
.choice-card{background:#fff;border:3px solid #f0f0f0;border-radius:18px;padding:16px 10px;text-align:center;cursor:pointer;box-shadow:0 4px 14px rgba(0,0,0,.06);transition:all .25s cubic-bezier(.34,1.56,.64,1);animation:cardIn .4s ease both;}
.choice-card.selected{border-color:var(--cc)!important;background:color-mix(in srgb,var(--cc) 12%,white)!important;transform:translateY(-4px) scale(1.06);box-shadow:0 12px 28px rgba(0,0,0,.12);}
.choice-card:hover:not(.answered){transform:translateY(-6px) scale(1.06);box-shadow:0 12px 28px rgba(0,0,0,.12);border-color:var(--cc);}
.choice-card.correct{border-color:#40c057!important;background:#d3f9d8!important;animation:popGreen .4s cubic-bezier(.34,1.56,.64,1);}
.choice-card.wrong{border-color:#ff4444!important;background:#ffe3e3!important;animation:cardShake .4s ease;}
.choice-card.answered{cursor:default;}
.choice-circle-sm{width:48px;height:48px;border-radius:50%;background:var(--cc,#ccc);margin:0 auto 6px;box-shadow:0 3px 10px rgba(0,0,0,.18);border:3px solid rgba(255,255,255,.8);}
.choice-circle-md{width:56px;height:56px;border-radius:50%;background:var(--cc,#ccc);margin:0 auto 8px;box-shadow:0 3px 10px rgba(0,0,0,.15);border:3px solid rgba(255,255,255,.8);transition:transform .3s cubic-bezier(.34,1.56,.64,1);}
.choice-circle-lg{width:68px;height:68px;border-radius:50%;background:var(--cc,#ccc);margin:0 auto 8px;box-shadow:0 4px 14px rgba(0,0,0,.18);border:3px solid rgba(255,255,255,.8);transition:transform .3s cubic-bezier(.34,1.56,.64,1);}
.choice-card:hover:not(.answered) .choice-circle-md,
.choice-card:hover:not(.answered) .choice-circle-lg{transform:scale(1.12) rotate(-8deg);}
.choice-name{font-family:'Fredoka One',cursive;font-size:.85rem;color:#555;}
.choice-name-sm{font-family:'Fredoka One',cursive;font-size:.68rem;color:#888;}

/* SHARED ANIMATIONS */
@keyframes matchPop{0%{transform:scale(.9)}60%{transform:scale(1.08)}100%{transform:scale(1)}}
@keyframes cardShake{0%,100%{transform:translateX(0)}25%{transform:translateX(-6px)}75%{transform:translateX(6px)}}
@keyframes popGreen{0%{transform:scale(.9)}60%{transform:scale(1.1)}100%{transform:scale(1)}}
@keyframes cardIn{from{opacity:0;transform:translateY(16px) scale(.9)}to{opacity:1;transform:none}}
.choice-card:nth-child(1){animation-delay:.03s}.choice-card:nth-child(2){animation-delay:.06s}
.choice-card:nth-child(3){animation-delay:.09s}.choice-card:nth-child(4){animation-delay:.12s}.choice-card:nth-child(5){animation-delay:.15s}
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
.next-quizzes-btn{width:100%;margin-top:8px;background:linear-gradient(135deg,#a855f7,#7c3aed);color:#fff;border:none;border-radius:var(--pill);padding:12px 24px;font-family:'Fredoka One',cursive;font-size:.95rem;cursor:pointer;box-shadow:0 6px 18px rgba(0,0,0,.15);transition:transform .2s;display:flex;align-items:center;justify-content:center;gap:8px;}
.next-quizzes-btn:hover{transform:scale(1.04);}
.cfbit{position:fixed;pointer-events:none;z-index:1000;animation:cfFall linear forwards;}
@keyframes cfFall{0%{transform:translateY(-16px) rotate(0deg);opacity:1}100%{transform:translateY(105vh) rotate(700deg);opacity:0}}

/* ── LOCKED TAB ── */
.act-tab.tab-locked{opacity:.55;cursor:not-allowed;}
.act-tab.tab-locked:hover{transform:none;border-color:#e8e8e8;color:#aaa;}
.locked-toast{position:fixed;bottom:28px;left:50%;transform:translateX(-50%) translateY(20px);background:#1a2e1a;color:#fff;font-family:'Fredoka One',cursive;font-size:.92rem;padding:12px 28px;border-radius:50px;box-shadow:0 8px 28px rgba(0,0,0,.25);z-index:500;opacity:0;transition:opacity .3s,transform .3s;pointer-events:none;white-space:nowrap;}
.locked-toast.show{opacity:1;transform:translateX(-50%) translateY(0);}

/* ── COLOR MATCH WITH PICTURES ── */
.pic-match-container{display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:20px;}
.pic-name-card{background:#fff;border:3px solid #f0f0f0;border-radius:18px;padding:12px 10px;text-align:center;cursor:pointer;box-shadow:0 4px 14px rgba(0,0,0,.06);transition:all .25s cubic-bezier(.34,1.56,.64,1);}
.pic-name-card:hover:not(.matched):not(.disabled){transform:translateY(-4px) scale(1.03);box-shadow:0 10px 26px rgba(0,0,0,.1);}
.pic-name-card.selected{border-color:var(--card-color,#a855f7);background:color-mix(in srgb,var(--card-color,#a855f7) 8%,white);transform:translateY(-4px) scale(1.04);}
.pic-name-card.matched{border-color:#40c057;background:#f0fdf4;cursor:default;animation:matchPop .4s cubic-bezier(.34,1.56,.64,1);}
.pic-name-card.wrong{border-color:#ff4444;background:#fff5f5;animation:cardShake .4s ease;}
.pic-name-card.paired{background:#f3e8ff;opacity:.95;}
.swatch-card.paired{background:#f3e8ff;opacity:.95;}
.pic-name-card img{width:100%;height:80px;object-fit:cover;border-radius:10px;margin-bottom:6px;display:block;}
.pic-name-card .pic-fallback{width:100%;height:80px;border-radius:10px;margin-bottom:6px;display:flex;align-items:center;justify-content:center;font-size:2.2rem;}
.pic-card-label{font-family:'Fredoka One',cursive;font-size:.85rem;color:var(--card-color,#555);}
.pic-card-check{font-size:.75rem;color:#40c057;opacity:0;transition:opacity .2s;display:block;margin-top:2px;}
.pic-name-card.matched .pic-card-check{opacity:1;}

/* ── FINAL EXAM ── */
.final-locked-wrap{text-align:center;padding:60px 20px;}
.final-lock-icon{font-size:5rem;margin-bottom:20px;animation:lockBounce 2s ease-in-out infinite;}
@keyframes lockBounce{0%,100%{transform:translateY(0) rotate(-3deg)}50%{transform:translateY(-12px) rotate(3deg)}}
.final-lock-title{font-family:'Fredoka One',cursive;font-size:2rem;color:#1a2e1a;margin-bottom:10px;}
.final-lock-sub{font-size:.95rem;color:#aaa;font-weight:700;margin-bottom:28px;line-height:1.5;}
.final-progress-checks{display:flex;justify-content:center;gap:14px;flex-wrap:wrap;margin-bottom:32px;}
.fpc-item{display:flex;align-items:center;gap:8px;background:#fff;border:2px solid #f0f0f0;border-radius:50px;padding:8px 18px;font-family:'Fredoka One',cursive;font-size:.82rem;color:#aaa;box-shadow:0 2px 8px rgba(0,0,0,.05);}
.fpc-item.done{border-color:#40c057;color:#2f9e44;background:#f0fdf4;}
.fpc-item i{font-size:.75rem;}
.fe-q-card{background:#fff;border-radius:28px;padding:28px 24px;text-align:center;box-shadow:0 8px 36px rgba(0,0,0,.08);border:2px solid #f0ece4;max-width:540px;margin:0 auto 24px;}
.fe-q-num{font-family:'Fredoka One',cursive;font-size:.78rem;color:#bbb;letter-spacing:1px;text-transform:uppercase;margin-bottom:14px;}
.fe-q-img{width:100%;max-width:220px;height:160px;object-fit:cover;border-radius:16px;margin:0 auto 14px;display:block;box-shadow:0 4px 16px rgba(0,0,0,.12);}
.fe-q-fallback{width:220px;height:160px;border-radius:16px;margin:0 auto 14px;display:flex;align-items:center;justify-content:center;font-size:4rem;background:#f8f8f8;}
.fe-q-text{font-family:'Fredoka One',cursive;font-size:1.3rem;color:#2d2d2d;margin-bottom:4px;}
.fe-q-sub{font-size:.82rem;color:#bbb;font-weight:700;}
.choices-grid-fe{display:grid;grid-template-columns:repeat(5,1fr);gap:10px;max-width:540px;margin:0 auto 20px;}
@media(max-width:480px){.choices-grid-fe{grid-template-columns:repeat(3,1fr);}}
</style>
</head>
<body>
<div class="page-wrap">

<nav class="lesson-nav">
  <a href="activities.php" class="lnav-back"><i class="fas fa-arrow-left"></i> Back</a>
  <div class="lnav-title"><i class="fas fa-palette"></i> Colors Activities</div>
  <div class="lang-toggle">
    <button class="lang-btn active" id="btnEN" onclick="setLang('en')"><i class="fas fa-globe-americas"></i> EN</button>
    <button class="lang-btn" id="btnTL" onclick="setLang('tl')"><i class="fas fa-flag"></i> TL</button>
  </div>
</nav>

<div class="container">

  <!-- TABS -->
  <div class="quizzes-tabs">
    <?php
    $tabs = [
      1 => ['icon'=>'fas fa-palette',        'en'=>'Match',      'tl'=>'Pagtutugma', 'act'=>$a1],
      2 => ['icon'=>'fas fa-search',          'en'=>'Spot It',    'tl'=>'Hanapin',   'act'=>$a2],
      3 => ['icon'=>'fas fa-flask',           'en'=>'Mixing',     'tl'=>'Paghahalo', 'act'=>$a3],
      4 => ['icon'=>'fas fa-magnifying-glass','en'=>'Hunt',       'tl'=>'Paghahanap','act'=>$a4],
    ];
    foreach ($tabs as $num => $tab):
      $status = $tab['act']['status'];
      $score  = $tab['act']['score'];
      $dot_class = match(true){
        $status==='completed' => 'done',
        $status==='in_progress' => 'progress',
        default => 'locked'
      };
      $dot_icon  = match(true){
        $status==='completed' => '<i class="fas fa-check"></i>',
        $status==='in_progress' => '<i class="fas fa-play"></i>',
        default => $num
      };
    ?>
    <div class="act-tab <?php echo $num===$active_tab?'active':'';?>" onclick="switchTab(<?php echo $num;?>)" id="tab<?php echo $num;?>">
      <i class="<?php echo $tab['icon'];?>"></i>
      <span class="tab-label" data-en="<?php echo $tab['en'];?>" data-tl="<?php echo $tab['tl'];?>"><?php echo $tab['en'];?></span>
      <span class="tab-status <?php echo $dot_class;?>"><?php echo $dot_icon;?></span>
      <?php if($status==='completed'):?><span class="tab-score"><?php echo $score;?>%</span><?php endif;?>
    </div>
    <?php endforeach;?>
  </div>

  <!-- ══════════════════ PANEL 1: COLOR MATCH ══════════════════ -->
  <div class="quizzes-panel <?php echo $active_tab===1?'active':'';?>" id="panel1">
    <div class="page-header">
      <div class="quizzes-badge"><i class="fas fa-palette"></i> Quiz 1 of 5</div>
      <div class="page-title" id="p1Title">Color Match!</div>
      <div class="page-sub" id="p1Sub">Match each color name to the correct color swatch!</div>
      <?php if($a1['score']>0):?><div class="best-score-badge"><i class="fas fa-star"></i> Best Score: <?php echo $a1['score'];?>%</div><?php endif;?>
    </div>
    <div class="score-bar">
      <div class="score-item"><div class="score-num" id="p1Score">0</div><div class="score-label">Score</div></div>
      <div class="score-sep"></div>
      <div class="score-item"><div class="score-num" id="p1Round">1</div><div class="score-label">Round</div></div>
      <div class="score-sep"></div>
      <div class="progress-wrap">
        <div class="progress-bar-outer"><div class="progress-bar-inner" id="p1Bar" style="width:0%;background:linear-gradient(90deg,#a855f7,#7c3aed)"></div></div>
        <div class="progress-label" id="p1BarLbl">0 / 4 paired</div>
      </div>
    </div>
    <div class="game-area">
      <div class="round-info" id="p1RoundInfo">Round 1 of 5 — Match 4 pairs!</div>
      <div class="match-container" id="p1MatchContainer">
        <div><div class="match-col-label" id="p1ColName">Pictures</div><div class="match-col" id="p1NameCol"></div></div>
        <div><div class="match-col-label" id="p1ColSwatch">Colors</div><div class="match-col" id="p1SwatchCol"></div></div>
      </div>
      <div class="feedback-msg" id="p1Feedback"></div>
      <div class="controls">
        <button class="btn-action btn-primary" id="p1SubmitBtn" onclick="cm_submitRound()"><i class="fas fa-check"></i> <span id="p1SubmitLbl">Submit Round</span></button>
        <button class="btn-action btn-secondary" id="p1NextBtn" onclick="cm_proceedNext()" style="display:none"><i class="fas fa-arrow-right"></i> <span id="p1NextLbl">Next Round</span></button>
      </div>
    </div>
  </div>

  <!-- ══════════════════ PANEL 2: SPOT THE COLOR ══════════════════ -->
  <div class="quizzes-panel <?php echo $active_tab===2?'active':'';?>" id="panel2">
    <div class="page-header">
      <div class="quizzes-badge"><i class="fas fa-search"></i> Quiz 2 of 5</div>
      <div class="page-title" id="p2Title">Spot the Color!</div>
      <div class="page-sub" id="p2Sub">Hear the color name, then tap the correct color!</div>
      <?php if($a2['score']>0):?><div class="best-score-badge"><i class="fas fa-star"></i> Best Score: <?php echo $a2['score'];?>%</div><?php endif;?>
    </div>
    <div class="score-bar">
      <div class="score-item"><div class="score-num" id="p2Score">0</div><div class="score-label">Score</div></div>
      <div class="score-sep"></div>
      <div class="score-item"><div class="score-num" id="p2QNum">1</div><div class="score-label">Question</div></div>
      <div class="score-sep"></div>
      <div class="score-item"><div class="lives-wrap" id="p2Lives"><span class="life-icon"><i class="fas fa-heart" style="color:#ff4d6d"></i></span><span class="life-icon"><i class="fas fa-heart" style="color:#ff4d6d"></i></span><span class="life-icon"><i class="fas fa-heart" style="color:#ff4d6d"></i></span></div><div class="score-label">Lives</div></div>
      <div class="score-sep"></div>
      <div class="progress-wrap">
        <div class="progress-bar-outer"><div class="progress-bar-inner" id="p2Bar" style="width:0%;background:linear-gradient(90deg,#FB8C00,#e65100)"></div></div>
        <div class="progress-label" id="p2BarLbl">0 / 10</div>
      </div>
    </div>
    <div class="question-card">
      <div class="q-prompt" id="p2Prompt">Find this color:</div>
      <div class="q-name" id="p2QName" style="color:#a855f7">Red</div>
      <button class="q-speak-btn" onclick="sc_speakColor()"><i class="fas fa-volume-up"></i></button>
    </div>
    <div class="feedback-msg" id="p2Feedback"></div>
    <div class="choices-grid-4" id="p2Choices"></div>
    <div class="controls">
      <button class="btn-action btn-primary" id="p2SubmitBtn" onclick="sc_submitAnswer()" style="display:none"><i class="fas fa-check"></i> <span id="p2SubmitLbl">Submit</span></button>
      <button class="btn-action btn-secondary" id="p2NextBtn" onclick="sc_nextQuestion()" style="display:none"><i class="fas fa-arrow-right"></i> <span id="p2NextLbl">Next</span></button>
    </div>
  </div>

  <!-- ══════════════════ PANEL 3: COLOR MIXING ══════════════════ -->
  <div class="quizzes-panel <?php echo $active_tab===3?'active':'';?>" id="panel3">
    <div class="page-header">
      <div class="quizzes-badge"><i class="fas fa-flask"></i> Quiz 3 of 5</div>
      <div class="page-title" id="p3Title">Color Mixing Quiz!</div>
      <div class="page-sub" id="p3Sub">What color do you get when you mix two colors?</div>
      <?php if($a3['score']>0):?><div class="best-score-badge"><i class="fas fa-star"></i> Best Score: <?php echo $a3['score'];?>%</div><?php endif;?>
    </div>
    <div class="score-bar">
      <div class="score-item"><div class="score-num" id="p3Score">0</div><div class="score-label">Score</div></div>
      <div class="score-sep"></div>
      <div class="score-item"><div class="score-num" id="p3QNum">1</div><div class="score-label">Question</div></div>
      <div class="score-sep"></div>
      <div class="progress-wrap">
        <div class="progress-bar-outer"><div class="progress-bar-inner" id="p3Bar" style="width:0%;background:linear-gradient(90deg,#f59e0b,#d97706)"></div></div>
        <div class="progress-label" id="p3BarLbl">0 / 10</div>
      </div>
    </div>
    <div class="mix-card">
      <div class="mix-prompt" id="p3MixPrompt">What color do you get?</div>
      <div class="mix-equation">
        <div><div class="mix-circle" id="p3MixC1" style="background:#E53935"></div><div class="mix-c1-label" id="p3C1Label">Red</div></div>
        <div class="mix-op">+</div>
        <div><div class="mix-circle" id="p3MixC2" style="background:#1E88E5"></div><div class="mix-c2-label" id="p3C2Label">Blue</div></div>
        <div class="mix-op">=</div>
        <div class="mix-result-circle" id="p3MixResult">?</div>
      </div>
    </div>
    <div class="choices-grid-4mx" id="p3Choices"></div>
    <div class="feedback-msg" id="p3Feedback"></div>
    <div class="controls">
      <button class="btn-action btn-primary" id="p3SubmitBtn" onclick="mx_submitAnswer()" style="display:none"><i class="fas fa-check"></i> <span id="p3SubmitLbl">Submit</span></button>
      <button class="btn-action btn-secondary" id="p3NextBtn" onclick="mx_nextQuestion()" style="display:none"><i class="fas fa-arrow-right"></i> <span id="p3NextLbl">Next</span></button>
    </div>
  </div>

  <!-- ══════════════════ PANEL 4: COLOR HUNT ══════════════════ -->
  <div class="quizzes-panel <?php echo $active_tab===4?'active':'';?>" id="panel4">
    <div class="page-header">
      <div class="quizzes-badge"><i class="fas fa-magnifying-glass"></i> Quiz 4 of 5</div>
      <div class="page-title" id="p4Title">Color Hunt Quiz!</div>
      <div class="page-sub" id="p4Sub">Look at the object — what color is it?</div>
      <?php if($a4['score']>0):?><div class="best-score-badge"><i class="fas fa-star"></i> Best Score: <?php echo $a4['score'];?>%</div><?php endif;?>
    </div>
    <div class="score-bar">
      <div class="score-item"><div class="score-num" id="p4Score">0</div><div class="score-label">Score</div></div>
      <div class="score-sep"></div>
      <div class="score-item"><div class="score-num" id="p4QNum">1</div><div class="score-label">Question</div></div>
      <div class="score-sep"></div>
      <div class="score-item"><div class="lives-wrap" id="p4Lives"><span class="life-icon"><i class="fas fa-heart" style="color:#ff4d6d"></i></span><span class="life-icon"><i class="fas fa-heart" style="color:#ff4d6d"></i></span><span class="life-icon"><i class="fas fa-heart" style="color:#ff4d6d"></i></span></div><div class="score-label">Lives</div></div>
      <div class="score-sep"></div>
      <div class="progress-wrap">
        <div class="progress-bar-outer"><div class="progress-bar-inner" id="p4Bar" style="width:0%;background:linear-gradient(90deg,#34d399,#059669)"></div></div>
        <div class="progress-label" id="p4BarLbl">0 / 10</div>
      </div>
    </div>
    <div class="object-card">
      <div class="obj-prompt" id="p4ObjPrompt">What color is this?</div>
      <div id="p4ImgWrap"></div>
      <div class="obj-name" id="p4ObjName">Apple</div>
      <div class="obj-question" id="p4ObjQuestion">What color is an apple?</div>
    </div>
    <div class="choices-grid-5" id="p4Choices"></div>
    <div class="feedback-msg" id="p4Feedback"></div>
    <div class="controls">
      <button class="btn-action btn-primary" id="p4SubmitBtn" onclick="ch_submitAnswer()" style="display:none"><i class="fas fa-check"></i> <span id="p4SubmitLbl">Submit</span></button>
      <button class="btn-action btn-secondary" id="p4NextBtn" onclick="ch_nextQuestion()" style="display:none"><i class="fas fa-arrow-right"></i> <span id="p4NextLbl">Next</span></button>
    </div>
  </div>

</div><!-- /container -->

<!-- RESULT OVERLAY -->
<div class="result-overlay" id="resultOverlay">
  <div class="result-box">
    <div class="result-icon" id="resultIcon"><i class="fas fa-star"></i></div>
    <div class="result-title" id="resultTitle">Colorful!</div>
    <div class="result-sub" id="resultSub">Great job!</div>
    <div class="result-score" id="resultScore">0%</div>
    <div class="result-best" id="resultBest"></div>
    <div class="saving-msg" id="savingMsg"></div>
    <div class="controls" style="justify-content:center;flex-direction:column;gap:8px;">
      <div style="display:flex;gap:8px;justify-content:center;">
        <button class="btn-action btn-primary" onclick="playAgain()"><i class="fas fa-redo"></i> Play Again</button>
        <button class="btn-action btn-secondary" onclick="closeResult()"><i class="fas fa-times"></i> Close</button>
      </div>
      <button class="next-quizzes-btn" id="nextActBtn" style="display:none" onclick="goNextquizzes()">
        <i class="fas fa-arrow-right"></i> Next quizzes
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

// ── SHARED COLOR DATA ──
const ALL_COLORS={
  en:[{name:'Red',hex:'#E53935'},{name:'Blue',hex:'#1E88E5'},{name:'Yellow',hex:'#FDD835'},{name:'Green',hex:'#43A047'},{name:'Orange',hex:'#FB8C00'},{name:'Purple',hex:'#8E24AA'},{name:'Pink',hex:'#E91E8C'},{name:'Brown',hex:'#6D4C41'},{name:'Black',hex:'#212121'},{name:'White',hex:'#ECEFF1'}],
  tl:[{name:'Pula',hex:'#E53935'},{name:'Asul',hex:'#1E88E5'},{name:'Dilaw',hex:'#FDD835'},{name:'Berde',hex:'#43A047'},{name:'Kahel',hex:'#FB8C00'},{name:'Lila',hex:'#8E24AA'},{name:'Rosas',hex:'#E91E8C'},{name:'Kayumanggi',hex:'#6D4C41'},{name:'Itim',hex:'#212121'},{name:'Puti',hex:'#ECEFF1'}]
};
const COLORS_MATCH={
  en:[{name:'Red',local:'(Pula)',hex:'#E53935'},{name:'Blue',local:'(Asul)',hex:'#1E88E5'},{name:'Yellow',local:'(Dilaw)',hex:'#FDD835'},{name:'Green',local:'(Berde)',hex:'#43A047'},{name:'Orange',local:'(Kahel)',hex:'#FB8C00'},{name:'Purple',local:'(Lila)',hex:'#8E24AA'},{name:'Pink',local:'(Rosas)',hex:'#E91E8C'},{name:'Brown',local:'(Kayumanggi)',hex:'#6D4C41'},{name:'Black',local:'(Itim)',hex:'#212121'},{name:'White',local:'(Puti)',hex:'#ECEFF1'}],
  tl:[{name:'Pula',local:'(Red)',hex:'#E53935'},{name:'Asul',local:'(Blue)',hex:'#1E88E5'},{name:'Dilaw',local:'(Yellow)',hex:'#FDD835'},{name:'Berde',local:'(Green)',hex:'#43A047'},{name:'Kahel',local:'(Orange)',hex:'#FB8C00'},{name:'Lila',local:'(Purple)',hex:'#8E24AA'},{name:'Rosas',local:'(Pink)',hex:'#E91E8C'},{name:'Kayumanggi',local:'(Brown)',hex:'#6D4C41'},{name:'Itim',local:'(Black)',hex:'#212121'},{name:'Puti',local:'(White)',hex:'#ECEFF1'}]
};
const QUIZ_MIX={
  en:[
    {c1:{name:'Red',hex:'#E53935'},c2:{name:'Blue',hex:'#1E88E5'},result:{name:'Purple',hex:'#8E24AA'},wrong:[{name:'Green',hex:'#43A047'},{name:'Orange',hex:'#FB8C00'},{name:'Pink',hex:'#E91E8C'}]},
    {c1:{name:'Red',hex:'#E53935'},c2:{name:'Yellow',hex:'#FDD835'},result:{name:'Orange',hex:'#FB8C00'},wrong:[{name:'Purple',hex:'#8E24AA'},{name:'Green',hex:'#43A047'},{name:'Brown',hex:'#6D4C41'}]},
    {c1:{name:'Blue',hex:'#1E88E5'},c2:{name:'Yellow',hex:'#FDD835'},result:{name:'Green',hex:'#43A047'},wrong:[{name:'Orange',hex:'#FB8C00'},{name:'Purple',hex:'#8E24AA'},{name:'Pink',hex:'#E91E8C'}]},
    {c1:{name:'Red',hex:'#E53935'},c2:{name:'White',hex:'#ECEFF1'},result:{name:'Pink',hex:'#E91E8C'},wrong:[{name:'Purple',hex:'#8E24AA'},{name:'Orange',hex:'#FB8C00'},{name:'Yellow',hex:'#FDD835'}]},
    {c1:{name:'Black',hex:'#212121'},c2:{name:'White',hex:'#ECEFF1'},result:{name:'Gray',hex:'#9E9E9E'},wrong:[{name:'Brown',hex:'#6D4C41'},{name:'Blue',hex:'#1E88E5'},{name:'Purple',hex:'#8E24AA'}]},
    {c1:{name:'Red',hex:'#E53935'},c2:{name:'Green',hex:'#43A047'},result:{name:'Brown',hex:'#6D4C41'},wrong:[{name:'Orange',hex:'#FB8C00'},{name:'Purple',hex:'#8E24AA'},{name:'Black',hex:'#212121'}]},
    {c1:{name:'Blue',hex:'#1E88E5'},c2:{name:'White',hex:'#ECEFF1'},result:{name:'Light Blue',hex:'#90CAF9'},wrong:[{name:'Green',hex:'#43A047'},{name:'Purple',hex:'#8E24AA'},{name:'Pink',hex:'#E91E8C'}]},
    {c1:{name:'Yellow',hex:'#FDD835'},c2:{name:'White',hex:'#ECEFF1'},result:{name:'Cream',hex:'#FFF9C4'},wrong:[{name:'Orange',hex:'#FB8C00'},{name:'Pink',hex:'#E91E8C'},{name:'Green',hex:'#43A047'}]},
    {c1:{name:'Yellow',hex:'#FDD835'},c2:{name:'Blue',hex:'#1E88E5'},result:{name:'Green',hex:'#43A047'},wrong:[{name:'Orange',hex:'#FB8C00'},{name:'Purple',hex:'#8E24AA'},{name:'Brown',hex:'#6D4C41'}]},
    {c1:{name:'Orange',hex:'#FB8C00'},c2:{name:'White',hex:'#ECEFF1'},result:{name:'Peach',hex:'#FFAB91'},wrong:[{name:'Pink',hex:'#E91E8C'},{name:'Yellow',hex:'#FDD835'},{name:'Cream',hex:'#FFF9C4'}]},
  ],
  tl:[
    {c1:{name:'Pula',hex:'#E53935'},c2:{name:'Asul',hex:'#1E88E5'},result:{name:'Lila',hex:'#8E24AA'},wrong:[{name:'Berde',hex:'#43A047'},{name:'Kahel',hex:'#FB8C00'},{name:'Rosas',hex:'#E91E8C'}]},
    {c1:{name:'Pula',hex:'#E53935'},c2:{name:'Dilaw',hex:'#FDD835'},result:{name:'Kahel',hex:'#FB8C00'},wrong:[{name:'Lila',hex:'#8E24AA'},{name:'Berde',hex:'#43A047'},{name:'Kayumanggi',hex:'#6D4C41'}]},
    {c1:{name:'Asul',hex:'#1E88E5'},c2:{name:'Dilaw',hex:'#FDD835'},result:{name:'Berde',hex:'#43A047'},wrong:[{name:'Kahel',hex:'#FB8C00'},{name:'Lila',hex:'#8E24AA'},{name:'Rosas',hex:'#E91E8C'}]},
    {c1:{name:'Pula',hex:'#E53935'},c2:{name:'Puti',hex:'#ECEFF1'},result:{name:'Rosas',hex:'#E91E8C'},wrong:[{name:'Lila',hex:'#8E24AA'},{name:'Kahel',hex:'#FB8C00'},{name:'Dilaw',hex:'#FDD835'}]},
    {c1:{name:'Itim',hex:'#212121'},c2:{name:'Puti',hex:'#ECEFF1'},result:{name:'Kulay-abo',hex:'#9E9E9E'},wrong:[{name:'Kayumanggi',hex:'#6D4C41'},{name:'Asul',hex:'#1E88E5'},{name:'Lila',hex:'#8E24AA'}]},
    {c1:{name:'Pula',hex:'#E53935'},c2:{name:'Berde',hex:'#43A047'},result:{name:'Kayumanggi',hex:'#6D4C41'},wrong:[{name:'Kahel',hex:'#FB8C00'},{name:'Lila',hex:'#8E24AA'},{name:'Itim',hex:'#212121'}]},
    {c1:{name:'Asul',hex:'#1E88E5'},c2:{name:'Puti',hex:'#ECEFF1'},result:{name:'Maliwanag na Asul',hex:'#90CAF9'},wrong:[{name:'Berde',hex:'#43A047'},{name:'Lila',hex:'#8E24AA'},{name:'Rosas',hex:'#E91E8C'}]},
    {c1:{name:'Dilaw',hex:'#FDD835'},c2:{name:'Puti',hex:'#ECEFF1'},result:{name:'Maliwanag na Dilaw',hex:'#FFF9C4'},wrong:[{name:'Kahel',hex:'#FB8C00'},{name:'Rosas',hex:'#E91E8C'},{name:'Berde',hex:'#43A047'}]},
    {c1:{name:'Dilaw',hex:'#FDD835'},c2:{name:'Asul',hex:'#1E88E5'},result:{name:'Berde',hex:'#43A047'},wrong:[{name:'Kahel',hex:'#FB8C00'},{name:'Lila',hex:'#8E24AA'},{name:'Kayumanggi',hex:'#6D4C41'}]},
    {c1:{name:'Kahel',hex:'#FB8C00'},c2:{name:'Puti',hex:'#ECEFF1'},result:{name:'Pampkin',hex:'#FFAB91'},wrong:[{name:'Rosas',hex:'#E91E8C'},{name:'Dilaw',hex:'#FDD835'},{name:'Maliwanag na Dilaw',hex:'#FFF9C4'}]},
  ]
};
// COLOR HUNT uses one picture per color drawn from NATURE_PICS
// Each entry: src (image path), en/tl name, question EN/TL, answer EN/TL, hex
const OBJECTS={
  en:[
    {src:'pictures/colors/red1.png',    name:'Apple',        question:'What color is an apple?',            answer:'Red',          hex:'#E53935'},
    {src:'pictures/colors/blue1.jpg',   name:'Ocean',        question:'What color is the ocean?',           answer:'Blue',         hex:'#1E88E5'},
    {src:'pictures/colors/yellow4.jpeg', name:'Lemon',        question:'What color is a lemon?',             answer:'Yellow',       hex:'#FDD835'},
    {src:'pictures/colors/green1.png',  name:'Leaf',         question:'What color is a leaf?',              answer:'Green',        hex:'#43A047'},
    {src:'pictures/colors/orange1.jpg', name:'Orange Fruit', question:'What color is an orange?',           answer:'Orange',       hex:'#FB8C00'},
    {src:'pictures/colors/purple1.jpg', name:'Grapes',       question:'What color are grapes?',             answer:'Purple',       hex:'#8E24AA'},
    {src:'pictures/colors/pink2.jpg',   name:'Flamingo',     question:'What color is a flamingo?',          answer:'Pink',         hex:'#E91E8C'},
    {src:'pictures/colors/brown1.jpg',  name:'Chocolate',    question:'What color is chocolate?',           answer:'Brown',        hex:'#6D4C41'},
    {src:'pictures/colors/black1.jpg',  name:'Night Sky',    question:'What color is the night sky?',       answer:'Black',        hex:'#212121'},
    {src:'pictures/colors/white1.jpg',  name:'Clouds',       question:'What color are clouds?',             answer:'White',        hex:'#ECEFF1'},
  ],
  tl:[
    {src:'pictures/colors/red1.png',    name:'Mansanas',     question:'Anong kulay ang mansanas?',          answer:'Pula',         hex:'#E53935'},
    {src:'pictures/colors/blue1.jpg',   name:'Karagatan',    question:'Anong kulay ang karagatan?',         answer:'Asul',         hex:'#1E88E5'},
    {src:'pictures/colors/yellow4.jpeg', name:'Limon',        question:'Anong kulay ang limon?',             answer:'Dilaw',        hex:'#FDD835'},
    {src:'pictures/colors/green1.png',  name:'Dahon',        question:'Anong kulay ang dahon?',             answer:'Berde',        hex:'#43A047'},
    {src:'pictures/colors/orange1.jpg', name:'Kahel',        question:'Anong kulay ang kahel?',             answer:'Kahel',        hex:'#FB8C00'},
    {src:'pictures/colors/purple1.jpg', name:'Ubas',         question:'Anong kulay ang ubas?',              answer:'Lila',         hex:'#8E24AA'},
    {src:'pictures/colors/pink2.jpg',   name:'Flamingo',     question:'Anong kulay ang flamingo?',          answer:'Rosas',        hex:'#E91E8C'},
    {src:'pictures/colors/brown1.jpg',  name:'Tsokolate',    question:'Anong kulay ang tsokolate?',         answer:'Kayumanggi',   hex:'#6D4C41'},
    {src:'pictures/colors/black1.jpg',  name:'Kalangitan sa Gabi', question:'Anong kulay ang kalangitan sa gabi?', answer:'Itim', hex:'#212121'},
    {src:'pictures/colors/white1.jpg',  name:'Ulap',         question:'Anong kulay ang ulap?',              answer:'Puti',         hex:'#ECEFF1'},
  ]
};

// ── NATURE PICTURES (from colors.php — used in Color Match & Final Exam) ──
// Each entry: color name (EN), image src path, label EN, label TL
const NATURE_PICS=[
  {color:'Red',   hex:'#E53935', en:'Apple',        tl:'Mansanas',   src:'pictures/colors/red1.png'},
  {color:'Red',   hex:'#E53935', en:'Rose',          tl:'Rosas',      src:'pictures/colors/red2.jpeg'},
  {color:'Red',   hex:'#E53935', en:'Strawberry',    tl:'Presa',      src:'pictures/colors/red3.jpg'},
  {color:'Red',   hex:'#E53935', en:'Fire Truck',    tl:'Trak ng Bumbero', src:'pictures/colors/red4.jpg'},
  {color:'Blue',  hex:'#1E88E5', en:'Ocean',         tl:'Karagatan',  src:'pictures/colors/blue1.jpg'},
  {color:'Blue',  hex:'#1E88E5', en:'Sky',           tl:'Langit',     src:'pictures/colors/blue2.jpg'},
  {color:'Blue',  hex:'#1E88E5', en:'Blueberry',     tl:'Blueberry',  src:'pictures/colors/blue3.jpeg'},
  {color:'Blue',  hex:'#1E88E5', en:'Butterfly',     tl:'Mariposa',   src:'pictures/colors/blue4.jpg'},
  {color:'Yellow',hex:'#FDD835', en:'Sun',           tl:'Araw',       src:'pictures/colors/yellow1.jpg'},
  {color:'Yellow',hex:'#FDD835', en:'Banana',        tl:'Saging',     src:'pictures/colors/yellow2.jpg'},
  {color:'Yellow',hex:'#FDD835', en:'Sunflower',     tl:'Sunflower',  src:'pictures/colors/yellow3.jpg'},
  {color:'Yellow',hex:'#FDD835', en:'Lemon',         tl:'Limon',      src:'pictures/colors/yellow4.jpeg'},
  {color:'Green', hex:'#43A047', en:'Leaf',          tl:'Dahon',      src:'pictures/colors/green1.png'},
  {color:'Green', hex:'#43A047', en:'Frog',          tl:'Palaka',     src:'pictures/colors/green2.jpg'},
  {color:'Green', hex:'#43A047', en:'Broccoli',      tl:'Broccoli',   src:'pictures/colors/green3.jpg'},
  {color:'Green', hex:'#43A047', en:'Tree',          tl:'Puno',       src:'pictures/colors/green4.jpg'},
  {color:'Orange',hex:'#FB8C00', en:'Orange Fruit',  tl:'Kahel',      src:'pictures/colors/orange1.jpg'},
  {color:'Orange',hex:'#FB8C00', en:'Pumpkin',       tl:'Kalabasa',   src:'pictures/colors/orange2.jpg'},
  {color:'Orange',hex:'#FB8C00', en:'Fox',           tl:'Lobo',       src:'pictures/colors/orange3.jpg'},
  {color:'Orange',hex:'#FB8C00', en:'Carrot',        tl:'Karot',      src:'pictures/colors/orange4.jpg'},
  {color:'Purple',hex:'#8E24AA', en:'Grapes',        tl:'Ubas',       src:'pictures/colors/purple1.jpg'},
  {color:'Purple',hex:'#8E24AA', en:'Lavender',      tl:'Lavender',   src:'pictures/colors/purple2.jpg'},
  {color:'Purple',hex:'#8E24AA', en:'Eggplant',      tl:'Talong',     src:'pictures/colors/purple3.jpg'},
  {color:'Purple',hex:'#8E24AA', en:'Violet Flower', tl:'Viola',      src:'pictures/colors/purple4.jpg'},
  {color:'Pink',  hex:'#E91E8C', en:'Flower',        tl:'Bulaklak',   src:'pictures/colors/pink1.jpg'},
  {color:'Pink',  hex:'#E91E8C', en:'Flamingo',      tl:'Flamingo',   src:'pictures/colors/pink2.jpg'},
  {color:'Pink',  hex:'#E91E8C', en:'Pig',           tl:'Baboy',      src:'pictures/colors/pink3.jpg'},
  {color:'Pink',  hex:'#E91E8C', en:'Cherry Blossom',tl:'Sakura',     src:'pictures/colors/pink4.jpg'},
  {color:'Brown', hex:'#6D4C41', en:'Chocolate',     tl:'Tsokolate',  src:'pictures/colors/brown1.jpg'},
  {color:'Brown', hex:'#6D4C41', en:'Bear',          tl:'Oso',        src:'pictures/colors/brown2.jpg'},
  {color:'Brown', hex:'#6D4C41', en:'Wood',          tl:'Kahoy',      src:'pictures/colors/brown3.jpg'},
  {color:'Brown', hex:'#6D4C41', en:'Coffee',        tl:'Kape',       src:'pictures/colors/brown4.jpg'},
  {color:'Black', hex:'#212121', en:'Night Sky',     tl:'Kalangitan sa Gabi', src:'pictures/colors/black1.jpg'},
  {color:'Black', hex:'#212121', en:'Crow',          tl:'Uwak',       src:'pictures/colors/black2.jpg'},
  {color:'Black', hex:'#212121', en:'Panda',         tl:'Panda',      src:'pictures/colors/black3.jpg'},
  {color:'Black', hex:'#212121', en:'Black Cat',     tl:'Pusang Itim',src:'pictures/colors/black4.jpg'},
  {color:'White', hex:'#ECEFF1', en:'Clouds',        tl:'Ulap',       src:'pictures/colors/white1.jpg'},
  {color:'White', hex:'#ECEFF1', en:'Dove',          tl:'Kalapati',   src:'pictures/colors/white2.jpg'},
  {color:'White', hex:'#ECEFF1', en:'Milk',          tl:'Gatas',      src:'pictures/colors/white3.jpg'},
  {color:'White', hex:'#ECEFF1', en:'Snow',          tl:'Niyebe',     src:'pictures/colors/white4.jpg'},
];
// Color name map EN→TL for matching game
const COLOR_TL={Red:'Pula',Blue:'Asul',Yellow:'Dilaw',Green:'Berde',Orange:'Kahel',Purple:'Lila',Pink:'Rosas',Brown:'Kayumanggi',Black:'Itim',White:'Puti'};
const COLOR_EN_FROM_TL={Pula:'Red',Asul:'Blue',Dilaw:'Yellow',Berde:'Green',Kahel:'Orange',Lila:'Purple',Rosas:'Pink',Kayumanggi:'Brown',Itim:'Black',Puti:'White'};

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
  document.getElementById('savingMsg').textContent='Saving your score...';
  try{const r=await fetch(CURRENT_URL,{method:'POST',headers:{'Content-Type':'application/x-www-form-urlencoded'},body:`action=complete&quizzes_id=${id}&score=${score}`});const d=await r.json();if(d.success)document.getElementById('savingMsg').textContent='Score saved!';}catch(e){document.getElementById('savingMsg').textContent='';}
}

// ── LANG ──
function setLang(l){
  if(l===lang)return;lang=l;
  document.getElementById('btnEN').classList.toggle('active',l==='en');
  document.getElementById('btnTL').classList.toggle('active',l==='tl');
  document.querySelectorAll('.tab-label').forEach(el=>el.textContent=el.dataset[l]);
  if(activeTab===1)cm_startGame();
  if(activeTab===2)sc_startGame();
  if(activeTab===3)mx_startGame();
  if(activeTab===4)ch_startGame();
}

// ── TABS ──
function switchTab(num){
  activeTab=num;
  document.querySelectorAll('.act-tab').forEach((t,i)=>t.classList.toggle('active',i+1===num));
  document.querySelectorAll('.quizzes-panel').forEach((p,i)=>p.classList.toggle('active',i+1===num));
  if(num===1)cm_startGame();
  if(num===2)sc_startGame();
  if(num===3)mx_startGame();
  if(num===4)ch_startGame();
  window.scrollTo({top:0,behavior:'smooth'});
}

// ── RESULT ──
let _curActNum=1;
function showResult(actNum,rawScore,maxScore){
  _curActNum=actNum;
  const finalScore=Math.round((rawScore/maxScore)*100);
  const prev=PREV_SCORES[actNum];
  document.getElementById('resultScore').textContent=finalScore+'%';
  document.getElementById('resultIcon').innerHTML=finalScore>=80?'<i class="fas fa-trophy" style="color:#f59f00"></i>':finalScore>=50?'<i class="fas fa-star" style="color:#ffd43b"></i>':'<i class="fas fa-medal" style="color:#74c0fc"></i>';
  document.getElementById('resultBest').textContent=finalScore>prev?'New best score!':prev>0?`Previous best: ${prev}%`:'';
  document.getElementById('savingMsg').textContent='';
  // Show "Next Quiz" only for quizzes 1-3
  const showNext=actNum<4;
  document.getElementById('nextActBtn').style.display=showNext?'flex':'none';
  if(showNext){
    document.getElementById('nextActBtn').innerHTML='<i class="fas fa-arrow-right"></i> Next Quiz';
  }
  document.getElementById('resultOverlay').classList.add('active');
  confetti('#a855f7');confetti('#ffd43b');
  saveProgress(ACT_IDS[actNum],finalScore);
  saveComplete(ACT_IDS[actNum],finalScore);
  PREV_SCORES[actNum]=Math.max(PREV_SCORES[actNum],finalScore);
}
function closeResult(){document.getElementById('resultOverlay').classList.remove('active');}
function playAgain(){
  closeResult();
  if(_curActNum===1)cm_startGame();
  if(_curActNum===2)sc_startGame();
  if(_curActNum===3)mx_startGame();
  if(_curActNum===4)ch_startGame();
}
function goNextquizzes(){closeResult();switchTab(_curActNum+1);}

// ── UTILS ──
function shuffle(a){const r=[...a];for(let i=r.length-1;i>0;i--){const j=0|Math.random()*(i+1);[r[i],r[j]]=[r[j],r[i]];}return r;}
function speak(w){if(!window.speechSynthesis)return;window.speechSynthesis.cancel();const u=new SpeechSynthesisUtterance(w);u.lang=lang==='tl'?'fil-PH':'en-US';u.rate=0.8;u.pitch=1.2;window.speechSynthesis.speak(u);}
function confetti(color){const cols=[color,'#FFE66D','#FF6B6B','#A29BFE','#4ECDC4','#FD79A8'];for(let i=0;i<30;i++){const p=document.createElement('div');p.className='cfbit';p.style.cssText=`left:${Math.random()*100}vw;top:-12px;background:${cols[0|Math.random()*cols.length]};border-radius:${Math.random()>.5?'50%':'3px'};width:${6+Math.random()*8}px;height:${6+Math.random()*8}px;animation-duration:${1.2+Math.random()*1.5}s;animation-delay:${Math.random()*.4}s;`;document.body.appendChild(p);p.addEventListener('animationend',()=>p.remove());}}
function getHex(name){return ALL_COLORS[lang].find(c=>c.name===name)?.hex||'#ccc';}

// ════════════════════════════════════════
// Quiz 1: COLOR MATCH (picture → color swatch) — 5 rounds × 4 pairs
// Scoring: 4 correct=20pts, 3=15pts, 2=10pts, 1=5pts, 0=0pts per round
// Total max = 5 rounds × 20pts = 100pts
// ════════════════════════════════════════
const CM_COPY={
  en:{title:'Color Match!',sub:'Match each picture to the correct color!',colName:'Pictures',colSwatch:'Colors',roundInfo:r=>`Round ${r} of 5 — Match 4 pairs!`,progressLbl:(m,t)=>`${m} / ${t} paired`,submitLbl:'Submit Round',nextLbl:'Next Round',finishLbl:'See Results',feedbackCorrect:(pts)=>`✅ Perfect! +${pts} pts this round!`,feedbackPartial:(c,pts)=>`⭐ ${c}/4 correct! +${pts} pts this round.`,feedbackNone:'❌ 0/4 correct. Keep trying!',needAll:'⚠️ Pair all 4 first before submitting!',resultTitle:'Colorful!',resultSub:'You matched all the colors!'},
  tl:{title:'Pagtutugma ng Kulay!',sub:'Itugma ang bawat larawan sa tamang kulay!',colName:'Mga Larawan',colSwatch:'Mga Kulay',roundInfo:r=>`Round ${r} ng 5 — Itugma ang 4 pares!`,progressLbl:(m,t)=>`${m} / ${t} napares`,submitLbl:'I-submit ang Round',nextLbl:'Susunod na Round',finishLbl:'Tingnan ang Resulta',feedbackCorrect:(pts)=>`✅ Perpekto! +${pts} pts sa round na ito!`,feedbackPartial:(c,pts)=>`⭐ ${c}/4 tama! +${pts} pts sa round na ito.`,feedbackNone:'❌ 0/4 tama. Subukan ulit!',needAll:'⚠️ I-pair muna ang lahat ng 4 bago i-submit!',resultTitle:'Makulay!',resultSub:'Natugma mo ang lahat ng kulay!'}
};
// 5 rounds × 4 pairs × 5pts = 100pts max
const CM_TOTAL_ROUNDS=5, CM_PPM=5, CM_MAX=CM_TOTAL_ROUNDS*4*CM_PPM; // = 100
let cm_score=0,cm_round=0,cm_pool=[],cm_roundPics=[],cm_selName=null,cm_selSwatch=null;
let cm_pairs={}; // key: picColorName → {picCard, swatchCard, swatchColorName}
let cm_submitted=false;

// Build a pool of unique-color picture entries (1 random pic per color)
function cm_buildPool(){
  const colors=['Red','Blue','Yellow','Green','Orange','Purple','Pink','Brown','Black','White'];
  return shuffle(colors).map(col=>{
    const pics=NATURE_PICS.filter(p=>p.color===col);
    return pics[Math.floor(Math.random()*pics.length)];
  });
}

function cm_startGame(){
  cm_score=0;cm_round=0;cm_pool=cm_buildPool();
  document.getElementById('p1Score').textContent='0';
  document.getElementById('resultOverlay').classList.remove('active');
  const c=CM_COPY[lang];
  document.getElementById('p1Title').textContent=c.title;
  document.getElementById('p1Sub').textContent=c.sub;
  document.getElementById('p1ColName').textContent=c.colName;
  document.getElementById('p1ColSwatch').textContent=c.colSwatch;
  document.getElementById('p1SubmitLbl').textContent=c.submitLbl;
  document.getElementById('p1NextLbl').textContent=c.nextLbl;
  saveStart(ACT_IDS[1]);
  cm_nextRound();
}

function cm_nextRound(){
  cm_round++;cm_pairs={};cm_submitted=false;cm_selName=null;cm_selSwatch=null;
  if(cm_pool.length<4)cm_pool=cm_buildPool();
  cm_roundPics=cm_pool.splice(0,4);
  const c=CM_COPY[lang];
  document.getElementById('p1Round').textContent=cm_round;
  document.getElementById('p1RoundInfo').textContent=c.roundInfo(cm_round);
  document.getElementById('p1Bar').style.width='0%';
  document.getElementById('p1BarLbl').textContent=c.progressLbl(0,4);
  document.getElementById('p1Feedback').textContent='';
  document.getElementById('p1Feedback').className='feedback-msg';
  // Show submit, hide next
  document.getElementById('p1SubmitBtn').style.display='';
  document.getElementById('p1NextBtn').style.display='none';
  const lastRound=cm_round>=CM_TOTAL_ROUNDS;
  document.getElementById('p1NextLbl').textContent=lastRound?CM_COPY[lang].finishLbl:CM_COPY[lang].nextLbl;
  cm_renderCards();
}

function cm_renderCards(){
  const ns=shuffle([...cm_roundPics]);
  const swatchData=cm_roundPics.map(p=>({name:p.color,hex:p.hex,tlName:COLOR_TL[p.color]}));
  const ss=shuffle([...swatchData]);

  const nc=document.getElementById('p1NameCol');nc.innerHTML='';
  const sc=document.getElementById('p1SwatchCol');sc.innerHTML='';

  // Left col: picture cards
  ns.forEach(p=>{
    const label=lang==='tl'?p.tl:p.en;
    const card=document.createElement('div');
    card.className='pic-name-card';
    card.dataset.key=p.color;
    card.style.setProperty('--card-color',p.hex);
    card.innerHTML=`
      <img src="${p.src}" alt="${label}" onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
      <div class="pic-fallback" style="display:none;background:${p.hex}22;">${label.charAt(0)}</div>
      <div class="pic-card-label">${label}</div>
      <i class="fas fa-check pic-card-check"></i>`;
    card.onclick=()=>cm_selectName(card,{name:p.color,hex:p.hex});
    nc.appendChild(card);
  });

  // Right col: color swatch cards
  ss.forEach(c=>{
    const card=document.createElement('div');card.className='swatch-card';card.dataset.key=c.name;card.style.setProperty('--card-color',c.hex);
    const displayName=lang==='tl'?c.tlName:c.name;
    card.innerHTML=`<div class="color-circle" style="background:${c.hex}"></div><div style="font-family:'Fredoka One',cursive;font-size:.7rem;color:${c.hex};margin-top:4px;">${displayName}</div><div class="swatch-check"><i class="fas fa-check"></i></div>`;
    card.onclick=()=>cm_selectSwatch(card,{name:c.name,hex:c.hex});
    sc.appendChild(card);
  });
}

function cm_selectName(card,color){
  if(cm_submitted||card.classList.contains('paired'))return;
  document.querySelectorAll('#p1NameCol .pic-name-card').forEach(c=>c.classList.remove('selected'));
  cm_selName={card,color};card.classList.add('selected');
  if(cm_selName&&cm_selSwatch)cm_makePair();
}

function cm_selectSwatch(card,color){
  if(cm_submitted||card.classList.contains('paired'))return;
  document.querySelectorAll('#p1SwatchCol .swatch-card').forEach(c=>c.classList.remove('selected'));
  cm_selSwatch={card,color};card.classList.add('selected');
  if(cm_selName&&cm_selSwatch)cm_makePair();
}

function cm_makePair(){
  const n=cm_selName,s=cm_selSwatch;cm_selName=null;cm_selSwatch=null;
  // Unpair any existing pair for this pic card
  const existingPair=cm_pairs[n.color.name];
  if(existingPair){
    existingPair.swatchCard.classList.remove('selected','paired');
    existingPair.swatchCard.style.borderColor='';
    existingPair.swatchCard.style.opacity='';
  }
  // Unpair any existing pair for this swatch card
  for(const key in cm_pairs){
    if(cm_pairs[key].swatchCard===s.card){
      cm_pairs[key].picCard.classList.remove('selected','paired');
      cm_pairs[key].picCard.style.borderColor='';
      cm_pairs[key].picCard.style.opacity='';
      delete cm_pairs[key];break;
    }
  }
  // Make new pair (tentative — not scored yet)
  cm_pairs[n.color.name]={picCard:n.card,swatchCard:s.card,swatchColorName:s.color.name};
  n.card.classList.remove('selected');s.card.classList.remove('selected');
  n.card.classList.add('paired');s.card.classList.add('paired');
  n.card.style.borderColor=s.color.hex;
  s.card.style.borderColor=n.color.hex;
  // Update progress bar (how many are paired, not yet scored)
  const pairedCount=Object.keys(cm_pairs).length;
  const c=CM_COPY[lang];
  document.getElementById('p1Bar').style.width=((pairedCount/4)*100)+'%';
  document.getElementById('p1BarLbl').textContent=c.progressLbl(pairedCount,4);
  document.getElementById('p1Feedback').textContent='';
  document.getElementById('p1Feedback').className='feedback-msg';
}

function cm_submitRound(){
  if(cm_submitted)return;
  const paired=Object.keys(cm_pairs).length;
  if(paired<4){
    const c=CM_COPY[lang];
    document.getElementById('p1Feedback').textContent=c.needAll;
    document.getElementById('p1Feedback').className='feedback-msg wrong';
    return;
  }
  cm_submitted=true;
  document.getElementById('p1SubmitBtn').style.display='none';
  // Evaluate pairs
  let correctCount=0;
  for(const picColorName in cm_pairs){
    const pair=cm_pairs[picColorName];
    const isCorrect=pair.swatchColorName===picColorName;
    if(isCorrect){
      correctCount++;
      pair.picCard.classList.add('matched');
      pair.swatchCard.classList.add('matched');
      pair.picCard.style.borderColor='#40c057';
      pair.swatchCard.style.borderColor='#40c057';
    } else {
      pair.picCard.classList.remove('matched');
      pair.swatchCard.classList.remove('matched');
      pair.picCard.classList.add('wrong');
      pair.swatchCard.classList.add('wrong');
      pair.picCard.style.borderColor='#ff4444';
      pair.swatchCard.style.borderColor='#ff4444';
    }
  }
  // Score per round: correct * 5pts (4=20, 3=15, 2=10, 1=5, 0=0)
  const roundGain=correctCount*CM_PPM;
  cm_score+=roundGain;
  document.getElementById('p1Score').textContent=cm_score;
  saveProgress(ACT_IDS[1],Math.min(100,Math.round((cm_score/CM_MAX)*100)));
  const c=CM_COPY[lang];
  if(correctCount===4){
    document.getElementById('p1Feedback').textContent=c.feedbackCorrect(roundGain);
    document.getElementById('p1Feedback').className='feedback-msg correct';
    confetti('#a855f7');
  } else if(correctCount>0){
    document.getElementById('p1Feedback').textContent=c.feedbackPartial(correctCount,roundGain);
    document.getElementById('p1Feedback').className='feedback-msg correct';
  } else {
    document.getElementById('p1Feedback').textContent=c.feedbackNone;
    document.getElementById('p1Feedback').className='feedback-msg wrong';
  }
  // Show Next / See Results
  document.getElementById('p1NextBtn').style.display='';
  const lastRound=cm_round>=CM_TOTAL_ROUNDS;
  document.getElementById('p1NextLbl').textContent=lastRound?CM_COPY[lang].finishLbl:CM_COPY[lang].nextLbl;
}

function cm_proceedNext(){
  if(cm_round>=CM_TOTAL_ROUNDS){cm_showResult();}
  else{cm_nextRound();}
}

function cm_showResult(){
  document.getElementById('resultTitle').textContent=CM_COPY[lang].resultTitle;
  document.getElementById('resultSub').textContent=CM_COPY[lang].resultSub;
  showResult(1,cm_score,CM_MAX);
}

// ════════════════════════════════════════
// quizzes 2: SPOT THE COLOR
// ════════════════════════════════════════
const SC_COPY={en:{title:'Spot the Color!',sub:'Hear the color name, then tap the correct color!',prompt:'Find this color:',next:'Next',feedbackCorrect:n=>`Yes! That's ${n}!`,feedbackWrong:n=>`That was ${n}!`,resultTitle:'Brilliant!',resultSub:'You found all the colors!'},tl:{title:'Hanapin ang Kulay!',sub:'Pakinggan ang pangalan ng kulay, tapos i-tap ang tamang kulay!',prompt:'Hanapin ang kulay na ito:',next:'Susunod',feedbackCorrect:n=>`Tama! Iyan ang ${n}!`,feedbackWrong:n=>`Iyon ay ${n}!`,resultTitle:'Kahanga-hanga!',resultSub:'Nahanap mo ang lahat ng kulay!'}};
const SC_TOTAL=10,SC_MAX=SC_TOTAL*10;
let sc_pool=[],sc_current=null,sc_answered=false,sc_score=0,sc_qIdx=0,sc_lives=3;

function sc_startGame(){
  sc_score=0;sc_qIdx=0;sc_lives=3;sc_answered=false;sc_pool=shuffle([...ALL_COLORS[lang]]);
  document.getElementById('p2Score').textContent='0';
  document.getElementById('resultOverlay').classList.remove('active');
  sc_updateLives();
  const c=SC_COPY[lang];
  document.getElementById('p2Title').textContent=c.title;
  document.getElementById('p2Sub').textContent=c.sub;
  document.getElementById('p2Prompt').textContent=c.prompt;
  document.getElementById('p2NextLbl').textContent=c.next;
  document.getElementById('p2SubmitLbl').textContent=c.submit||'Submit';
  saveStart(ACT_IDS[2]);
  sc_loadQuestion();
}
function sc_updateLives(){document.querySelectorAll('#p2Lives .life-icon').forEach((ic,i)=>ic.classList.toggle('lost',i>=sc_lives));}
function sc_loadQuestion(){
  sc_answered=false;
  sc_selectedCard=null;sc_selectedColor=null;sc_selectedIsCorrect=false;
  document.getElementById('p2NextBtn').style.display='none';
  document.getElementById('p2SubmitBtn').style.display='none';
  document.getElementById('p2Feedback').textContent='';document.getElementById('p2Feedback').className='feedback-msg';
  if(!sc_pool.length)sc_pool=shuffle([...ALL_COLORS[lang]]);
  sc_current=sc_pool.shift();
  document.getElementById('p2QNum').textContent=sc_qIdx+1;
  document.getElementById('p2Bar').style.width=((sc_qIdx/SC_TOTAL)*100)+'%';
  document.getElementById('p2BarLbl').textContent=`${sc_qIdx} / ${SC_TOTAL}`;
  document.getElementById('p2QName').textContent=sc_current.name;
  document.getElementById('p2QName').style.color=sc_current.hex;
  document.getElementById('p2QName').style.setProperty('--q-color',sc_current.hex);
  const wrong=shuffle(ALL_COLORS[lang].filter(c=>c.name!==sc_current.name)).slice(0,3);
  const choices=shuffle([sc_current,...wrong]);
  const grid=document.getElementById('p2Choices');grid.innerHTML='';
  choices.forEach((c,i)=>{
    const isCorrect=c.name===sc_current.name;
    const card=document.createElement('div');card.className='choice-card';card.style.setProperty('--cc',c.hex);
    card.innerHTML=`<div class="choice-circle-lg" style="background:${c.hex}"></div>`;
    card.onclick=()=>sc_selectAnswer(card,c,isCorrect);
    grid.appendChild(card);
  });
  setTimeout(sc_speakColor,400);
}
function sc_speakColor(){if(sc_current)speak(sc_current.name);}
let sc_selectedCard=null,sc_selectedColor=null,sc_selectedIsCorrect=false;
function sc_selectAnswer(card,color,isCorrect){
  if(sc_answered)return;
  document.querySelectorAll('#p2Choices .choice-card').forEach(c=>c.classList.remove('selected'));
  card.classList.add('selected');
  sc_selectedCard=card;sc_selectedColor=color;sc_selectedIsCorrect=isCorrect;
  document.getElementById('p2SubmitBtn').style.display='inline-flex';
}
function sc_submitAnswer(){
  if(sc_answered||!sc_selectedCard)return;
  sc_answered=true;
  document.getElementById('p2SubmitBtn').style.display='none';
  document.querySelectorAll('#p2Choices .choice-card').forEach(c=>c.classList.add('answered'));
  const c=SC_COPY[lang];
  if(sc_selectedIsCorrect){
    sc_selectedCard.classList.add('correct');sc_score+=10; saveProgress(ACT_IDS[2],Math.round((sc_score/SC_MAX)*100));document.getElementById('p2Score').textContent=sc_score;
    document.getElementById('p2Feedback').textContent=c.feedbackCorrect(sc_current.name);
    document.getElementById('p2Feedback').className='feedback-msg correct';
    confetti(sc_current.hex);sc_qIdx++;
    if(sc_qIdx>=SC_TOTAL||sc_lives<=0)setTimeout(sc_showResult,900);
    else document.getElementById('p2NextBtn').style.display='inline-flex';
  } else {
    sc_selectedCard.classList.add('wrong');
    document.querySelectorAll('#p2Choices .choice-card').forEach(cd=>{
      const circle=cd.querySelector('.choice-circle-lg');
      if(circle&&(circle.style.background===sc_current.hex||circle.style.backgroundColor===sc_current.hex||circle.style.background==='rgb('+[parseInt(sc_current.hex.slice(1,3),16),parseInt(sc_current.hex.slice(3,5),16),parseInt(sc_current.hex.slice(5,7),16)].join(', ')+')'))cd.classList.add('correct');
    });
    sc_lives--;sc_updateLives();
    document.getElementById('p2Feedback').textContent=c.feedbackWrong(sc_selectedColor.name);
    document.getElementById('p2Feedback').className='feedback-msg wrong';
    sc_qIdx++;
    if(sc_lives<=0)setTimeout(sc_showResult,1100);else document.getElementById('p2NextBtn').style.display='inline-flex';
  }
}
function sc_nextQuestion(){sc_loadQuestion();}
function sc_showResult(){
  document.getElementById('resultTitle').textContent=SC_COPY[lang].resultTitle;
  document.getElementById('resultSub').textContent=SC_COPY[lang].resultSub;
  showResult(2,sc_score,SC_MAX);
}

// ════════════════════════════════════════
// quizzes 3: COLOR MIXING QUIZ
// ════════════════════════════════════════
const MX_COPY={en:{title:'Color Mixing Quiz!',sub:'What color do you get when you mix two colors?',prompt:'What color do you get?',next:'Next',feedbackCorrect:n=>`Yes! ${n}!`,feedbackWrong:n=>`It makes ${n}!`,resultTitle:'Great Mixing!',resultSub:'You know your color mixes!'},tl:{title:'Color Mixing Quiz!',sub:'Anong kulay ang makukuha pag naghalo ng dalawang kulay?',prompt:'Anong kulay ang makukuha?',next:'Susunod',feedbackCorrect:n=>`Tama! ${n}!`,feedbackWrong:n=>`Nagiging ${n}!`,resultTitle:'Magaling sa Paghahalo!',resultSub:'Alam mo ang mga kulay na nahahalo!'}};
const MX_TOTAL=10, MX_PPM=10, MX_MAX=MX_TOTAL*MX_PPM; // 10 items × 10pts = 100
let mx_pool=[],mx_current=null,mx_answered=false,mx_score=0,mx_qIdx=0;

function mx_startGame(){
  mx_score=0;mx_qIdx=0;mx_answered=false;mx_pool=shuffle([...QUIZ_MIX[lang]]);
  document.getElementById('p3Score').textContent='0';
  document.getElementById('resultOverlay').classList.remove('active');
  const c=MX_COPY[lang];
  document.getElementById('p3Title').textContent=c.title;
  document.getElementById('p3Sub').textContent=c.sub;
  document.getElementById('p3MixPrompt').textContent=c.prompt;
  document.getElementById('p3NextLbl').textContent=c.next;
  document.getElementById('p3SubmitLbl').textContent=c.submit||'Submit';
  saveStart(ACT_IDS[3]);
  mx_loadQuestion();
}
function mx_loadQuestion(){
  mx_answered=false;
  mx_selectedCard=null;mx_selectedColor=null;mx_selectedIsCorrect=false;
  document.getElementById('p3NextBtn').style.display='none';
  document.getElementById('p3SubmitBtn').style.display='none';
  document.getElementById('p3Feedback').textContent='';document.getElementById('p3Feedback').className='feedback-msg';
  if(!mx_pool.length)mx_pool=shuffle([...QUIZ_MIX[lang]]);
  mx_current=mx_pool.shift();
  document.getElementById('p3QNum').textContent=mx_qIdx+1;
  document.getElementById('p3Bar').style.width=((mx_qIdx/MX_TOTAL)*100)+'%';
  document.getElementById('p3BarLbl').textContent=`${mx_qIdx} / ${MX_TOTAL}`;
  const c1=document.getElementById('p3MixC1'),c2=document.getElementById('p3MixC2');
  c1.style.background=mx_current.c1.hex;c2.style.background=mx_current.c2.hex;
  document.getElementById('p3C1Label').textContent=mx_current.c1.name;
  document.getElementById('p3C2Label').textContent=mx_current.c2.name;
  c1.classList.add('pop');c2.classList.add('pop');
  setTimeout(()=>{c1.classList.remove('pop');c2.classList.remove('pop');},500);
  const res=document.getElementById('p3MixResult');res.style.background='#f0f0f0';res.style.border='4px solid #e8e8e8';res.textContent='?';
  const choices=shuffle([mx_current.result,...mx_current.wrong]);
  const grid=document.getElementById('p3Choices');grid.innerHTML='';
  choices.forEach((c,i)=>{
    const isCorrect=c.name===mx_current.result.name;
    const card=document.createElement('div');card.className='choice-card';card.style.setProperty('--cc',c.hex);
    card.innerHTML=`<div class="choice-circle-md" style="background:${c.hex}"></div><div class="choice-name" style="color:${c.hex}">${c.name}</div>`;
    card.onclick=()=>mx_selectAnswer(card,c,isCorrect);
    grid.appendChild(card);
  });
}
let mx_selectedCard=null,mx_selectedColor=null,mx_selectedIsCorrect=false;
function mx_selectAnswer(card,color,isCorrect){
  if(mx_answered)return;
  document.querySelectorAll('#p3Choices .choice-card').forEach(c=>c.classList.remove('selected'));
  card.classList.add('selected');
  mx_selectedCard=card;mx_selectedColor=color;mx_selectedIsCorrect=isCorrect;
  document.getElementById('p3SubmitBtn').style.display='inline-flex';
}
function mx_submitAnswer(){
  if(mx_answered||!mx_selectedCard)return;
  mx_answered=true;
  document.getElementById('p3SubmitBtn').style.display='none';
  document.querySelectorAll('#p3Choices .choice-card').forEach(c=>c.classList.add('answered'));
  const res=document.getElementById('p3MixResult');
  const c=MX_COPY[lang];
  if(mx_selectedIsCorrect){
    mx_selectedCard.classList.add('correct');res.style.background=mx_current.result.hex;res.style.border=`4px solid ${mx_current.result.hex}`;res.textContent='';
    mx_score+=MX_PPM; saveProgress(ACT_IDS[3],Math.round((mx_score/MX_MAX)*100));document.getElementById('p3Score').textContent=mx_score;
    document.getElementById('p3Feedback').textContent=c.feedbackCorrect(mx_current.result.name);
    document.getElementById('p3Feedback').className='feedback-msg correct';
    confetti(mx_current.result.hex);speak(mx_current.result.name);
  } else {
    mx_selectedCard.classList.add('wrong');
    document.querySelectorAll('#p3Choices .choice-card').forEach(cd=>{if(cd.querySelector('.choice-name')?.textContent===mx_current.result.name)cd.classList.add('correct');});
    res.style.background=mx_current.result.hex;res.style.border=`4px solid ${mx_current.result.hex}`;res.textContent='';
    document.getElementById('p3Feedback').textContent=c.feedbackWrong(mx_current.result.name);
    document.getElementById('p3Feedback').className='feedback-msg wrong';
  }
  mx_qIdx++;
  if(mx_qIdx>=MX_TOTAL)setTimeout(mx_showResult,1000);
  else document.getElementById('p3NextBtn').style.display='inline-flex';
}
function mx_nextQuestion(){mx_loadQuestion();}
function mx_showResult(){
  document.getElementById('resultTitle').textContent=MX_COPY[lang].resultTitle;
  document.getElementById('resultSub').textContent=MX_COPY[lang].resultSub;
  showResult(3,mx_score,MX_MAX);
}

// ════════════════════════════════════════
// quizzes 4: COLOR HUNT QUIZ
// ════════════════════════════════════════
const CH_COPY={en:{title:'Color Hunt Quiz!',sub:'Look at the object — what color is it?',prompt:'What color is this?',next:'Next',feedbackCorrect:n=>`Yes! It's ${n}!`,feedbackWrong:n=>`It's ${n}!`,resultTitle:'Color Expert!',resultSub:'You know your colors!'},tl:{title:'Color Hunt Quiz!',sub:'Tingnan ang bagay — anong kulay ito?',prompt:'Anong kulay ito?',next:'Susunod',feedbackCorrect:n=>`Tama! ${n} ito!`,feedbackWrong:n=>`Ito ay ${n}!`,resultTitle:'Dalubhasa sa Kulay!',resultSub:'Alam mo ang iyong mga kulay!'}};
const CH_TOTAL=10,CH_MAX=CH_TOTAL*10;
let ch_pool=[],ch_current=null,ch_answered=false,ch_score=0,ch_qIdx=0,ch_lives=3;

function ch_startGame(){
  ch_score=0;ch_qIdx=0;ch_lives=3;ch_answered=false;ch_pool=shuffle([...OBJECTS[lang]]);
  document.getElementById('p4Score').textContent='0';
  document.getElementById('resultOverlay').classList.remove('active');
  ch_updateLives();
  const c=CH_COPY[lang];
  document.getElementById('p4Title').textContent=c.title;
  document.getElementById('p4Sub').textContent=c.sub;
  document.getElementById('p4ObjPrompt').textContent=c.prompt;
  document.getElementById('p4NextLbl').textContent=c.next;
  document.getElementById('p4SubmitLbl').textContent=c.submit||'Submit';
  saveStart(ACT_IDS[4]);
  ch_loadQuestion();
}
function ch_updateLives(){document.querySelectorAll('#p4Lives .life-icon').forEach((ic,i)=>ic.classList.toggle('lost',i>=ch_lives));}
function ch_loadQuestion(){
  ch_answered=false;
  ch_selectedCard=null;ch_selectedColor=null;ch_selectedIsCorrect=false;
  document.getElementById('p4NextBtn').style.display='none';
  document.getElementById('p4SubmitBtn').style.display='none';
  document.getElementById('p4Feedback').textContent='';document.getElementById('p4Feedback').className='feedback-msg';
  if(!ch_pool.length)ch_pool=shuffle([...OBJECTS[lang]]);
  ch_current=ch_pool.shift();
  document.getElementById('p4QNum').textContent=ch_qIdx+1;
  document.getElementById('p4Bar').style.width=((ch_qIdx/CH_TOTAL)*100)+'%';
  document.getElementById('p4BarLbl').textContent=`${ch_qIdx} / ${CH_TOTAL}`;
  // Show image
  const imgWrap=document.getElementById('p4ImgWrap');
  const label=ch_current.name;
  imgWrap.innerHTML=`<img class="fe-q-img" src="${ch_current.src}" alt="${label}" onerror="this.style.display='none';document.getElementById('p4FallbackEmoji').style.display='flex'"><div id="p4FallbackEmoji" class="fe-q-fallback" style="display:none"><i class=\'fas fa-palette\'></i></div>`;
  document.getElementById('p4ObjName').textContent=label;
  document.getElementById('p4ObjQuestion').textContent=ch_current.question;
  const correctColor=ALL_COLORS[lang].find(c=>c.name===ch_current.answer);
  const wrongColors=shuffle(ALL_COLORS[lang].filter(c=>c.name!==ch_current.answer)).slice(0,4);
  const choices=shuffle([correctColor,...wrongColors]);
  const grid=document.getElementById('p4Choices');grid.innerHTML='';
  choices.forEach((c,i)=>{
    const isCorrect=c.name===ch_current.answer;
    const card=document.createElement('div');card.className='choice-card';card.style.setProperty('--cc',c.hex);
    card.innerHTML=`<div class="choice-circle-sm" style="background:${c.hex}"></div><div class="choice-name-sm">${c.name}</div>`;
    card.onclick=()=>ch_selectAnswer(card,c,isCorrect);
    grid.appendChild(card);
  });
}
let ch_selectedCard=null,ch_selectedColor=null,ch_selectedIsCorrect=false;
function ch_selectAnswer(card,color,isCorrect){
  if(ch_answered)return;
  document.querySelectorAll('#p4Choices .choice-card').forEach(c=>c.classList.remove('selected'));
  card.classList.add('selected');
  ch_selectedCard=card;ch_selectedColor=color;ch_selectedIsCorrect=isCorrect;
  document.getElementById('p4SubmitBtn').style.display='inline-flex';
}
function ch_submitAnswer(){
  if(ch_answered||!ch_selectedCard)return;
  ch_answered=true;
  document.getElementById('p4SubmitBtn').style.display='none';
  document.querySelectorAll('#p4Choices .choice-card').forEach(c=>c.classList.add('answered'));
  const c=CH_COPY[lang];
  if(ch_selectedIsCorrect){
    ch_selectedCard.classList.add('correct');ch_score+=10; saveProgress(ACT_IDS[4],Math.round((ch_score/CH_MAX)*100));document.getElementById('p4Score').textContent=ch_score;
    document.getElementById('p4Feedback').textContent=c.feedbackCorrect(ch_current.answer);
    document.getElementById('p4Feedback').className='feedback-msg correct';
    confetti(getHex(ch_current.answer));speak(ch_current.answer);
    ch_qIdx++;
    if(ch_qIdx>=CH_TOTAL||ch_lives<=0)setTimeout(ch_showResult,900);
    else document.getElementById('p4NextBtn').style.display='inline-flex';
  } else {
    ch_selectedCard.classList.add('wrong');
    document.querySelectorAll('#p4Choices .choice-card').forEach(cd=>{if(cd.querySelector('.choice-name-sm')?.textContent===ch_current.answer)cd.classList.add('correct');});
    ch_lives--;ch_updateLives();
    document.getElementById('p4Feedback').textContent=c.feedbackWrong(ch_current.answer);
    document.getElementById('p4Feedback').className='feedback-msg wrong';
    ch_qIdx++;
    if(ch_lives<=0)setTimeout(ch_showResult,1100);else document.getElementById('p4NextBtn').style.display='inline-flex';
  }
}
function ch_nextQuestion(){ch_loadQuestion();}
function ch_showResult(){
  document.getElementById('resultTitle').textContent=CH_COPY[lang].resultTitle;
  document.getElementById('resultSub').textContent=CH_COPY[lang].resultSub;
  showResult(4,ch_score,CH_MAX);
}


// ── FLUSH PROGRESS ON NAVIGATION / PAGE HIDE ──
function flushActiveTabProgress() {
  switch(activeTab) {
    case 1: if(cm_score>0) saveProgress(ACT_IDS[1],Math.round((cm_score/CM_MAX)*100)); break;
    case 2: if(sc_score>0) saveProgress(ACT_IDS[2],Math.round((sc_score/SC_MAX)*100)); break;
    case 3: if(mx_score>0) saveProgress(ACT_IDS[3],Math.round((mx_score/MX_MAX)*100)); break;
    case 4: if(ch_score>0) saveProgress(ACT_IDS[4],Math.round((ch_score/CH_MAX)*100)); break;
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
if(activeTab===1)cm_startGame();
else if(activeTab===2)sc_startGame();
else if(activeTab===3)mx_startGame();
else if(activeTab===4)ch_startGame();
</script>

</div><!-- /page-wrap -->
</body>
</html>