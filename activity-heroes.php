<?php
session_start();
require_once 'database.php';
require_once 'activity_helper.php';

if (!isset($_SESSION['student_id']) || $_SESSION['role'] !== 'student') {
    header("Location: index.php");
    exit;
}

$student_id = $_SESSION['student_id'];
$lesson_id  = isset($_GET['lesson_id']) ? (int)$_GET['lesson_id'] : 7; // Heroes = 7

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

$a1 = $act_map['hero_match']   ?? ['quizzes_id'=>0,'status'=>'not_started','score'=>0];
$a2 = $act_map['who_am_i']     ?? ['quizzes_id'=>0,'status'=>'not_started','score'=>0];
$a3 = $act_map['fill_in_blank'] ?? ['quizzes_id'=>0,'status'=>'not_started','score'=>0];
$a4 = $act_map['hero_trivia']  ?? ['quizzes_id'=>0,'status'=>'not_started','score'=>0];

$active_tab = isset($_GET['tab']) ? max(1, min(4, (int)$_GET['tab'])) : 1;
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Heroes Activities — E-KINDER</title>
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
.act-tab:hover:not(.active){border-color:#f5deb3;color:#555;transform:translateY(-2px);}
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

/* ── ACT 1: HERO MATCH ── */
.round-info{font-family:'Fredoka One',cursive;font-size:.85rem;color:#bbb;text-align:center;margin-bottom:20px;text-transform:uppercase;letter-spacing:1px;}
.match-container{display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:20px;max-width:700px;margin-left:auto;margin-right:auto;}
.match-col-label{font-family:'Fredoka One',cursive;font-size:.78rem;color:#888;text-transform:uppercase;letter-spacing:1px;text-align:center;margin-bottom:12px;}
.match-col{display:flex;flex-direction:column;gap:12px;}
.name-card{background:#fff;border:3px solid #f0f0f0;border-radius:18px;padding:14px 12px;text-align:center;cursor:pointer;box-shadow:0 4px 14px rgba(0,0,0,.06);transition:all .25s cubic-bezier(.34,1.56,.64,1);}
.name-card:hover:not(.matched):not(.disabled){transform:translateY(-4px) scale(1.03);box-shadow:0 10px 26px rgba(0,0,0,.1);}
.name-card.selected{border-color:var(--cc,#1565C0);background:color-mix(in srgb,var(--cc,#1565C0) 10%,white);transform:translateY(-4px) scale(1.04);}
.name-card.matched{border-color:#40c057;background:#f0fdf4;cursor:default;animation:matchPop .4s cubic-bezier(.34,1.56,.64,1);}
.name-card.wrong{border-color:#ff4444;background:#fff5f5;animation:cardShake .4s ease;}
.hero-name-text{font-family:'Fredoka One',cursive;font-size:.95rem;color:var(--cc,#1565C0);line-height:1.2;}
.hero-role-text{font-size:.65rem;color:#bbb;font-weight:700;margin-top:3px;}
.match-check{font-size:.8rem;color:#40c057;opacity:0;transition:opacity .2s;display:block;margin-top:3px;}
.name-card.matched .match-check{opacity:1;}
.photo-card{background:#fff;border:3px solid #f0f0f0;border-radius:18px;overflow:hidden;cursor:pointer;box-shadow:0 4px 14px rgba(0,0,0,.06);transition:all .25s cubic-bezier(.34,1.56,.64,1);position:relative;}
.photo-card:hover:not(.matched):not(.disabled){transform:translateY(-4px) scale(1.03);box-shadow:0 10px 26px rgba(0,0,0,.1);}
.photo-card.selected{border-color:var(--cc,#1565C0);transform:translateY(-4px) scale(1.04);}
.photo-card.matched{border-color:#40c057;cursor:default;animation:matchPop .4s cubic-bezier(.34,1.56,.64,1);}
.photo-card.wrong{border-color:#ff4444;animation:cardShake .4s ease;}
.photo-wrap{width:100%;aspect-ratio:1/1;overflow:hidden;background:#f0f0f0;}
.photo-wrap img{width:100%;height:100%;object-fit:cover;object-position:top center;display:block;transition:transform .3s;}
.photo-card:hover:not(.matched) .photo-wrap img{transform:scale(1.07);}
.photo-check{position:absolute;top:8px;right:8px;width:26px;height:26px;border-radius:50%;background:#40c057;color:#fff;display:flex;align-items:center;justify-content:center;font-size:.75rem;opacity:0;transition:opacity .2s;}
.photo-card.matched .photo-check{opacity:1;}

/* ── ACT 2: WHO AM I ── */
.clue-card{background:#fff;border-radius:28px;padding:28px 24px;box-shadow:0 8px 36px rgba(0,0,0,.08);border:3px solid var(--qc,#4A148C);max-width:540px;margin:0 auto 24px;text-align:center;position:relative;overflow:hidden;}
.clue-card::before{content:'?';position:absolute;font-family:'Fredoka One',cursive;font-size:12rem;color:var(--qc,#4A148C);opacity:.04;top:50%;left:50%;transform:translate(-50%,-50%);pointer-events:none;}
.clue-label{font-family:'Fredoka One',cursive;font-size:.8rem;color:#bbb;text-transform:uppercase;letter-spacing:1.2px;margin-bottom:16px;}
.clue-icon{font-size:3rem;margin-bottom:12px;animation:iconBounce 2s ease-in-out infinite;}
@keyframes iconBounce{0%,100%{transform:translateY(0) rotate(-3deg)}50%{transform:translateY(-10px) rotate(3deg)}}
.clue-text{font-family:'Fredoka One',cursive;font-size:1.15rem;color:#2d2d2d;line-height:1.5;margin-bottom:8px;}
.clue-hint-row{display:flex;justify-content:center;gap:10px;flex-wrap:wrap;margin-top:12px;}
.clue-chip{display:inline-flex;align-items:center;gap:6px;padding:5px 14px;border-radius:var(--pill);background:#f8f8f8;border:1.5px solid #f0f0f0;font-size:.75rem;font-weight:800;color:#888;}
.clue-chip i{font-size:.7rem;color:var(--qc,#4A148C);}
.choices-grid-2{display:grid;grid-template-columns:repeat(2,1fr);gap:12px;max-width:540px;margin:0 auto 20px;}
.choice-card{background:#fff;border:3px solid #f0f0f0;border-radius:18px;padding:14px 12px;cursor:pointer;box-shadow:0 4px 14px rgba(0,0,0,.06);transition:all .22s cubic-bezier(.34,1.56,.64,1);animation:cardIn .4s ease both;display:flex;align-items:center;gap:12px;}
.choice-card:hover:not(.answered){transform:translateY(-4px) scale(1.02);box-shadow:0 10px 24px rgba(0,0,0,.1);border-color:var(--cc);}
.choice-card.selected{border-color:var(--cc)!important;background:color-mix(in srgb,var(--cc) 10%,white)!important;transform:translateY(-4px) scale(1.02);}
.choice-card.correct{border-color:#40c057!important;background:#d3f9d8!important;animation:popGreen .4s cubic-bezier(.34,1.56,.64,1);}
.choice-card.wrong{border-color:#ff4444!important;background:#ffe3e3!important;animation:cardShake .4s ease;}
.choice-card.answered{cursor:default;}
.choice-photo{width:52px;height:52px;border-radius:10px;overflow:hidden;flex-shrink:0;background:#f0f0f0;}
.choice-photo img{width:100%;height:100%;object-fit:cover;object-position:top center;display:block;}
.choice-info{flex:1;min-width:0;}
.choice-name{font-family:'Fredoka One',cursive;font-size:.95rem;line-height:1.2;}
.choice-role{font-size:.65rem;color:#bbb;font-weight:700;}
.choice-card:nth-child(1){animation-delay:.04s}.choice-card:nth-child(2){animation-delay:.08s}
.choice-card:nth-child(3){animation-delay:.12s}.choice-card:nth-child(4){animation-delay:.16s}

/* ── ACT 3: FILL IN BLANK ── */
.hero-display{background:#fff;border-radius:28px;padding:24px;box-shadow:0 8px 36px rgba(0,0,0,.08);border:3px solid var(--hc,#006064);max-width:520px;margin:0 auto 24px;display:flex;align-items:center;gap:20px;}
.hero-display-photo{width:90px;height:90px;border-radius:16px;overflow:hidden;flex-shrink:0;background:#f0f0f0;border:3px solid var(--hc,#006064);}
.hero-display-photo img{width:100%;height:100%;object-fit:cover;object-position:top center;display:block;}
.hero-display-info{flex:1;min-width:0;}
.hero-display-role{font-family:'Fredoka One',cursive;font-size:.85rem;margin-bottom:4px;}
.hero-display-years{font-size:.72rem;color:#bbb;font-weight:700;margin-bottom:10px;}
.name-slots{display:flex;flex-wrap:wrap;gap:6px;align-items:center;}
.name-slot{display:inline-block;min-width:28px;height:36px;border-bottom:3px solid var(--hc,#006064);font-family:'Fredoka One',cursive;font-size:1.3rem;text-align:center;line-height:36px;padding:0 3px;}
.name-slot.blank{min-width:36px;background:color-mix(in srgb,var(--hc,#006064) 8%,white);border-radius:8px 8px 0 0;}
.name-slot.revealed{animation:slotReveal .4s cubic-bezier(.34,1.56,.64,1);}
@keyframes slotReveal{0%{transform:scale(.5) rotateX(90deg);opacity:0}100%{transform:none;opacity:1}}
.name-sep{font-family:'Fredoka One',cursive;font-size:1.2rem;color:#ddd;align-self:flex-end;padding-bottom:4px;}
.word-bank-label{font-family:'Fredoka One',cursive;font-size:.78rem;color:#bbb;text-transform:uppercase;letter-spacing:1px;text-align:center;margin-bottom:12px;}
.word-bank{display:flex;flex-wrap:wrap;justify-content:center;gap:10px;max-width:520px;margin:0 auto 20px;}
.word-tile{background:#fff;border:2.5px solid var(--wc,#006064);border-radius:var(--pill);padding:10px 20px;font-family:'Fredoka One',cursive;font-size:1rem;color:var(--wc,#006064);cursor:pointer;box-shadow:0 3px 10px rgba(0,0,0,.06);transition:all .2s cubic-bezier(.34,1.56,.64,1);animation:tileIn .3s ease both;}
.word-tile:hover:not(.used):not(.correct-tile):not(.wrong-tile){transform:translateY(-3px) scale(1.05);box-shadow:0 8px 18px rgba(0,0,0,.1);}
.word-tile.selected{background:var(--wc,#006064);color:#fff;transform:translateY(-3px) scale(1.05);}
.word-tile.correct-tile{background:#d3f9d8;border-color:#40c057;color:#2f9e44;cursor:default;animation:tileCorrect .4s cubic-bezier(.34,1.56,.64,1);}
.word-tile.wrong-tile{background:#ffe3e3;border-color:#ff4444;color:#e03131;animation:tileShake .4s ease;}
.word-tile.used{opacity:.25;pointer-events:none;}
@keyframes tileIn{from{opacity:0;transform:scale(.7) translateY(10px)}to{opacity:1;transform:none}}
@keyframes tileCorrect{0%{transform:scale(.9)}60%{transform:scale(1.1)}100%{transform:scale(1)}}
@keyframes tileShake{0%,100%{transform:translateX(0)}25%{transform:translateX(-6px)}75%{transform:translateX(6px)}}

/* ── ACT 4: HERO TRIVIA ── */
.question-card{background:#fff;border-radius:28px;padding:24px;box-shadow:0 8px 36px rgba(0,0,0,.08);border:3px solid var(--qc,#E65100);max-width:560px;margin:0 auto 24px;}
.q-hero-row{display:flex;align-items:center;gap:16px;margin-bottom:16px;}
.q-hero-photo{width:72px;height:72px;border-radius:14px;overflow:hidden;flex-shrink:0;background:#f0f0f0;border:3px solid var(--qc,#E65100);}
.q-hero-photo img{width:100%;height:100%;object-fit:cover;object-position:top center;display:block;}
.q-hero-name{font-family:'Fredoka One',cursive;font-size:1.2rem;line-height:1.2;}
.q-hero-years{font-size:.72rem;color:#bbb;font-weight:700;margin-top:3px;}
.q-category{display:inline-flex;align-items:center;gap:6px;padding:4px 12px;border-radius:var(--pill);font-family:'Fredoka One',cursive;font-size:.72rem;margin-bottom:12px;}
.q-text{font-family:'Fredoka One',cursive;font-size:1.1rem;color:#2d2d2d;line-height:1.4;}
.choices-grid-trivia{display:grid;grid-template-columns:1fr 1fr;gap:12px;max-width:560px;margin:0 auto 20px;}
.choice-btn{background:#fff;border:3px solid #f0f0f0;border-radius:18px;padding:14px 12px;text-align:left;cursor:pointer;box-shadow:0 4px 14px rgba(0,0,0,.06);transition:all .22s cubic-bezier(.34,1.56,.64,1);animation:cardIn .4s ease both;display:flex;align-items:center;gap:10px;}
.choice-btn:hover:not(.answered){transform:translateY(-4px) scale(1.02);box-shadow:0 10px 24px rgba(0,0,0,.1);border-color:var(--qc);}
.choice-btn.selected{border-color:var(--qc,#E65100)!important;background:color-mix(in srgb,var(--qc,#E65100) 10%,white)!important;transform:translateY(-4px) scale(1.02);}
.choice-btn.correct{border-color:#40c057!important;background:#d3f9d8!important;animation:popGreen .4s cubic-bezier(.34,1.56,.64,1);}
.choice-btn.wrong{border-color:#ff4444!important;background:#ffe3e3!important;animation:cardShake .4s ease;}
.choice-btn.answered{cursor:default;}
.choice-letter{width:32px;height:32px;border-radius:50%;background:var(--qc,#E65100);color:#fff;font-family:'Fredoka One',cursive;font-size:.9rem;display:flex;align-items:center;justify-content:center;flex-shrink:0;}
.choice-text{font-family:'Fredoka One',cursive;font-size:.9rem;color:#2d2d2d;flex:1;line-height:1.3;}
.choice-btn:nth-child(1){animation-delay:.04s}.choice-btn:nth-child(2){animation-delay:.08s}
.choice-btn:nth-child(3){animation-delay:.12s}.choice-btn:nth-child(4){animation-delay:.16s}

/* SHARED */
@keyframes matchPop{0%{transform:scale(.9)}60%{transform:scale(1.08)}100%{transform:scale(1)}}
@keyframes cardShake{0%,100%{transform:translateX(0)}25%{transform:translateX(-6px)}75%{transform:translateX(6px)}}
@keyframes popGreen{0%{transform:scale(.95)}60%{transform:scale(1.05)}100%{transform:scale(1)}}
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
.next-quizzes-btn{width:100%;margin-top:8px;background:linear-gradient(135deg,#E65100,#B71C1C);color:#fff;border:none;border-radius:var(--pill);padding:12px 24px;font-family:'Fredoka One',cursive;font-size:.95rem;cursor:pointer;box-shadow:0 6px 18px rgba(0,0,0,.15);transition:transform .2s;display:flex;align-items:center;justify-content:center;gap:8px;}
.next-quizzes-btn:hover{transform:scale(1.04);}
.cfbit{position:fixed;pointer-events:none;z-index:1000;animation:cfFall linear forwards;}
@keyframes cfFall{0%{transform:translateY(-16px) rotate(0deg);opacity:1}100%{transform:translateY(105vh) rotate(700deg);opacity:0}}
</style>
</head>
<body>
<div class="page-wrap">

<nav class="lesson-nav">
  <a href="activities.php" class="lnav-back"><i class="fas fa-arrow-left"></i> Back</a>
  <div class="lnav-title">🦁 Heroes Activities</div>
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
      1 => ['icon'=>'fas fa-puzzle-piece',   'en'=>'Match',    'tl'=>'Pagtutugma', 'act'=>$a1],
      2 => ['icon'=>'fas fa-question-circle', 'en'=>'Who Am I', 'tl'=>'Sino Ako',   'act'=>$a2],
      3 => ['icon'=>'fas fa-pen',             'en'=>'Fill In',  'tl'=>'Punan',      'act'=>$a3],
      4 => ['icon'=>'fas fa-star',            'en'=>'Trivia',   'tl'=>'Trivia',     'act'=>$a4],
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

  <!-- ══════════════════ PANEL 1: HERO MATCH ══════════════════ -->
  <div class="quizzes-panel <?php echo $active_tab===1?'active':'';?>" id="panel1">
    <div class="page-header">
      <div class="quizzes-badge"><i class="fas fa-puzzle-piece"></i> Quizzes 1 of 4</div>
      <div class="page-title" id="p1Title">Hero Match!</div>
      <div class="page-sub" id="p1Sub">Match each hero's name to the correct photo!</div>
      <?php if($a1['score']>0):?><div class="best-score-badge">⭐ Best Score: <?php echo $a1['score'];?>%</div><?php endif;?>
    </div>
    <div class="score-bar">
      <div class="score-item"><div class="score-num" id="p1Score">0</div><div class="score-label">Score</div></div>
      <div class="score-sep"></div>
      <div class="score-item"><div class="score-num" id="p1Round">1</div><div class="score-label">Round</div></div>
      <div class="score-sep"></div>
      <div class="progress-wrap">
        <div class="progress-bar-outer"><div class="progress-bar-inner" id="p1Bar" style="width:0%;background:linear-gradient(90deg,#1565C0,#B71C1C)"></div></div>
        <div class="progress-label" id="p1BarLbl">0 / 4 matched</div>
      </div>
    </div>
    <div class="round-info" id="p1RoundInfo">Round 1 of 5 — Match 4 heroes!</div>
    <div class="match-container">
      <div><div class="match-col-label" id="p1ColName">Hero Names</div><div class="match-col" id="p1NameCol"></div></div>
      <div><div class="match-col-label" id="p1ColPhoto">Photos</div><div class="match-col" id="p1PhotoCol"></div></div>
    </div>
    <div class="feedback-msg" id="p1Feedback"></div>
    <div class="controls">
      <button class="btn-action btn-primary" id="p1SubmitBtn" onclick="hm_submitRound()"><i class="fas fa-check"></i> <span id="p1SubmitLbl">Submit Round</span></button>
      <button class="btn-action btn-secondary" id="p1NextBtn" onclick="hm_proceedNext()" style="display:none"><i class="fas fa-arrow-right"></i> <span id="p1NextLbl">Next Round</span></button>
    </div>
  </div>

  <!-- ══════════════════ PANEL 2: WHO AM I ══════════════════ -->
  <div class="quizzes-panel <?php echo $active_tab===2?'active':'';?>" id="panel2">
    <div class="page-header">
      <div class="quizzes-badge"><i class="fas fa-question-circle"></i> quizzes 2 of 4</div>
      <div class="page-title" id="p2Title">Who Am I?</div>
      <div class="page-sub" id="p2Sub">Read the clues and guess the hero!</div>
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
        <div class="progress-bar-outer"><div class="progress-bar-inner" id="p2Bar" style="width:0%;background:linear-gradient(90deg,#4A148C,#880E4F)"></div></div>
        <div class="progress-label" id="p2BarLbl">0 / 10</div>
      </div>
    </div>
    <div class="clue-card" id="p2ClueCard">
      <div class="clue-label" id="p2ClueLabel">Who Am I?</div>
      <div class="clue-icon" id="p2ClueIcon">🦁</div>
      <div class="clue-text" id="p2ClueText">I am a doctor and a writer...</div>
      <div class="clue-hint-row" id="p2ClueHints"></div>
    </div>
    <div class="choices-grid-2" id="p2Choices"></div>
    <div class="feedback-msg" id="p2Feedback"></div>
    <div class="controls">
      <button class="btn-action btn-primary" id="p2SubmitBtn" onclick="wi_submitAnswer()" style="display:none"><i class="fas fa-check"></i> <span id="p2SubmitLbl">Submit</span></button>
      <button class="btn-action btn-secondary" id="p2NextBtn" onclick="wi_nextQuestion()" style="display:none"><i class="fas fa-arrow-right"></i> <span id="p2NextLbl">Next</span></button>
    </div>
  </div>

  <!-- ══════════════════ PANEL 3: FILL IN BLANK ══════════════════ -->
  <div class="quizzes-panel <?php echo $active_tab===3?'active':'';?>" id="panel3">
    <div class="page-header">
      <div class="quizzes-badge"><i class="fas fa-pen"></i> quizzes 3 of 4</div>
      <div class="page-title" id="p3Title">Fill in the Blank!</div>
      <div class="page-sub" id="p3Sub">Complete the hero's name using the word bank!</div>
      <?php if($a3['score']>0):?><div class="best-score-badge">⭐ Best Score: <?php echo $a3['score'];?>%</div><?php endif;?>
    </div>
    <div class="score-bar">
      <div class="score-item"><div class="score-num" id="p3Score">0</div><div class="score-label">Score</div></div>
      <div class="score-sep"></div>
      <div class="score-item"><div class="score-num" id="p3QNum">1</div><div class="score-label">Question</div></div>
      <div class="score-sep"></div>
      <div class="score-item"><div class="lives-wrap" id="p3Lives"><span class="life-icon">❤️</span><span class="life-icon">❤️</span><span class="life-icon">❤️</span></div><div class="score-label">Lives</div></div>
      <div class="score-sep"></div>
      <div class="progress-wrap">
        <div class="progress-bar-outer"><div class="progress-bar-inner" id="p3Bar" style="width:0%;background:linear-gradient(90deg,#006064,#1565C0)"></div></div>
        <div class="progress-label" id="p3BarLbl">0 / 10</div>
      </div>
    </div>
    <div class="hero-display" id="p3HeroDisplay">
      <div class="hero-display-photo" id="p3HeroPhoto"><img id="p3HeroImg" src="" alt=""></div>
      <div class="hero-display-info">
        <div class="hero-display-role" id="p3HeroRole">Writer & Doctor</div>
        <div class="hero-display-years" id="p3HeroYears">1861–1896</div>
        <div class="name-slots" id="p3NameSlots"></div>
      </div>
    </div>
    <div class="word-bank-label" id="p3WbLabel">Pick the missing word:</div>
    <div class="word-bank" id="p3WordBank"></div>
    <div class="feedback-msg" id="p3Feedback"></div>
    <div class="controls">
      <button class="btn-action btn-primary" id="p3SubmitBtn" onclick="fi_submitAnswer()" style="display:none"><i class="fas fa-check"></i> <span id="p3SubmitLbl">Submit</span></button>
      <button class="btn-action btn-secondary" id="p3NextBtn" onclick="fi_nextQuestion()" style="display:none"><i class="fas fa-arrow-right"></i> <span id="p3NextLbl">Next</span></button>
    </div>
  </div>

  <!-- ══════════════════ PANEL 4: HERO TRIVIA ══════════════════ -->
  <div class="quizzes-panel <?php echo $active_tab===4?'active':'';?>" id="panel4">
    <div class="page-header">
      <div class="quizzes-badge"><i class="fas fa-star"></i> quizzes 4 of 4</div>
      <div class="page-title" id="p4Title">Hero Trivia!</div>
      <div class="page-sub" id="p4Sub">Answer questions about our national heroes!</div>
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
        <div class="progress-bar-outer"><div class="progress-bar-inner" id="p4Bar" style="width:0%;background:linear-gradient(90deg,#E65100,#B71C1C)"></div></div>
        <div class="progress-label" id="p4BarLbl">0 / 10</div>
      </div>
    </div>
    <div class="question-card" id="p4QCard">
      <div class="q-hero-row">
        <div class="q-hero-photo" id="p4HeroPhoto"><img id="p4HeroImg" src="" alt=""></div>
        <div><div class="q-hero-name" id="p4HeroName">Jose Rizal</div><div class="q-hero-years" id="p4HeroYears">1861–1896</div></div>
      </div>
      <div class="q-category" id="p4QCategory"><i class="fas fa-star"></i> Role</div>
      <div class="q-text" id="p4QText">What was Jose Rizal's role?</div>
    </div>
    <div class="choices-grid-trivia" id="p4Choices"></div>
    <div class="feedback-msg" id="p4Feedback"></div>
    <div class="controls">
      <button class="btn-action btn-primary" id="p4SubmitBtn" onclick="tr_submitAnswer()" style="display:none"><i class="fas fa-check"></i> <span id="p4SubmitLbl">Submit</span></button>
      <button class="btn-action btn-secondary" id="p4NextBtn" onclick="tr_nextQuestion()" style="display:none"><i class="fas fa-arrow-right"></i> <span id="p4NextLbl">Next</span></button>
    </div>
  </div>

</div><!-- /container -->

<!-- RESULT OVERLAY -->
<div class="result-overlay" id="resultOverlay">
  <div class="result-box">
    <div class="result-icon" id="resultIcon">🎉</div>
    <div class="result-title" id="resultTitle">Bayani!</div>
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

// ── SHARED HEROES DATA ──
const HEROES=[
  {name:'Jose Rizal',      role_en:'Writer & Doctor',          role_tl:'Manunulat at Doktor',          years:'1861–1896',color:'#1565C0',photo:'pictures/jose_rizal.png'},
  {name:'Andres Bonifacio',role_en:'Revolutionary Leader',     role_tl:'Lider ng Rebolusyon',          years:'1863–1897',color:'#B71C1C',photo:'pictures/andres_bonifacio.png'},
  {name:'Emilio Aguinaldo',role_en:'First President',          role_tl:'Unang Pangulo',                years:'1869–1964',color:'#1B5E20',photo:'pictures/emilio_aguinaldo.png'},
  {name:'Apolinario Mabini',role_en:'Political Philosopher',   role_tl:'Politikal na Pilosopo',        years:'1864–1903',color:'#4A148C',photo:'pictures/apolinario_mabini.png'},
  {name:'Melchora Aquino', role_en:'Mother of Revolution',     role_tl:'Ina ng Rebolusyon',            years:'1812–1919',color:'#880E4F',photo:'pictures/melchora_aquino.png'},
  {name:'Gregorio del Pilar',role_en:'Boy General',            role_tl:'Batang Heneral',               years:'1875–1899',color:'#E65100',photo:'pictures/gregorio_del_pilar.png'},
  {name:'Marcelo H. del Pilar',role_en:'Journalist & Propagandist',role_tl:'Mamamahayag at Propagandista',years:'1850–1896',color:'#006064',photo:'pictures/marcelo_del_pilar.png'},
  {name:'Antonio Luna',    role_en:'General & Scientist',      role_tl:'Heneral at Siyentipiko',       years:'1866–1899',color:'#3E2723',photo:'pictures/antonio_luna.png'},
];
const HEROES_CLUES=[
  {name:'Jose Rizal',color:'#1565C0',photo:'pictures/jose_rizal.png',icon:'✍️',role_en:'Writer & Doctor',role_tl:'Manunulat at Doktor',years:'1861–1896',
   clues_en:['I wrote Noli Me Tangere and El Filibusterismo.','I am a doctor and a writer from Calamba, Laguna.','I was executed at Bagumbayan (Luneta) in 1896.'],
   clues_tl:['Sumulat ako ng Noli Me Tangere at El Filibusterismo.','Ako ay isang doktor at manunulat mula sa Calamba, Laguna.','Pinaputukan ako sa Bagumbayan (Luneta) noong 1896.']},
  {name:'Andres Bonifacio',color:'#B71C1C',photo:'pictures/andres_bonifacio.png',icon:'⚔️',role_en:'Revolutionary Leader',role_tl:'Lider ng Rebolusyon',years:'1863–1897',
   clues_en:['I founded the secret society called Katipunan.','I am known as the Father of the Philippine Revolution.','I led the uprising against Spanish rule in 1896.'],
   clues_tl:['Itinatag ko ang lihim na samahan na tinatawag na Katipunan.','Kilala ako bilang Ama ng Rebolusyong Pilipino.','Pinamunuan ko ang pag-aalsa laban sa pamamahala ng Espanya noong 1896.']},
  {name:'Emilio Aguinaldo',color:'#1B5E20',photo:'pictures/emilio_aguinaldo.png',icon:'🏛️',role_en:'First President',role_tl:'Unang Pangulo',years:'1869–1964',
   clues_en:['I was the first President of the Philippine Republic.','I declared Philippine Independence on June 12, 1898.','I lived to be 94 years old, the oldest among the heroes.'],
   clues_tl:['Ako ang unang Pangulo ng Republika ng Pilipinas.','Idineklara ko ang Kalayaan ng Pilipinas noong Hunyo 12, 1898.','Nabuhay ako ng 94 taon, ang pinakamatanda sa mga bayani.']},
  {name:'Apolinario Mabini',color:'#4A148C',photo:'pictures/apolinario_mabini.png',icon:'📜',role_en:'Political Philosopher',role_tl:'Politikal na Pilosopo',years:'1864–1903',
   clues_en:['I am called the Sublime Paralytic because I was paralyzed.','I was the chief adviser and "brain" of Aguinaldo\'s government.','I wrote the True Decalogue for the Filipino people.'],
   clues_tl:['Tinatawag akong Dakilang Paralitiko dahil ako ay may kapansanan.','Ako ang pangunahing tagapayo at "utak" ng pamahalaan ni Aguinaldo.','Sinulat ko ang Tunay na Dekalogo para sa mga Pilipino.']},
  {name:'Melchora Aquino',color:'#880E4F',photo:'pictures/melchora_aquino.png',icon:'🌹',role_en:'Mother of Revolution',role_tl:'Ina ng Rebolusyon',years:'1812–1919',
   clues_en:['I am known as Tandang Sora, the oldest hero.','I sheltered and fed the Katipuneros in my home.','I am called the Mother of the Revolution.'],
   clues_tl:['Kilala ako bilang Tandang Sora, ang pinakamatandang bayani.','Pinangalagaan ko at pinakain ang mga Katipunero sa aking tahanan.','Tinatawag akong Ina ng Rebolusyon.']},
  {name:'Gregorio del Pilar',color:'#E65100',photo:'pictures/gregorio_del_pilar.png',icon:'🎖️',role_en:'Boy General',role_tl:'Batang Heneral',years:'1875–1899',
   clues_en:['I am the youngest general of the Philippine Revolution.','I am known as the Boy General or Bulakenyo.','I died heroically at the Battle of Tirad Pass in 1899.'],
   clues_tl:['Ako ang pinakabatang heneral ng Rebolusyong Pilipino.','Kilala ako bilang Batang Heneral o Bulakenyo.','Namatay akong bayani sa Labanan ng Tirad Pass noong 1899.']},
  {name:'Marcelo H. del Pilar',color:'#006064',photo:'pictures/marcelo_del_pilar.png',icon:'📰',role_en:'Journalist & Propagandist',role_tl:'Mamamahayag at Propagandista',years:'1850–1896',
   clues_en:['I edited the newspaper La Solidaridad in Spain.','I am known as Plaridel, a fierce journalist against Spain.','I fought for Philippine rights through writing and journalism.'],
   clues_tl:['In-edit ko ang pahayagang La Solidaridad sa Espanya.','Kilala ako bilang Plaridel, isang matapang na mamamahayag laban sa Espanya.','Nakipaglaban ako para sa karapatan ng mga Pilipino sa pamamagitan ng pagsulat.']},
  {name:'Antonio Luna',color:'#3E2723',photo:'pictures/antonio_luna.png',icon:'🔬',role_en:'General & Scientist',role_tl:'Heneral at Siyentipiko',years:'1866–1899',
   clues_en:['I was a brilliant general AND a scientist.','I was trained as a pharmacist and studied bacteriology in Europe.','I commanded the Philippine Army against the Americans in 1899.'],
   clues_tl:['Ako ay isang kahanga-hangang heneral AT siyentipiko.','Nag-aral ako bilang parmasyutiko at nag-aral ng bacteriology sa Europa.','Pinamunuan ko ang Hukbong Pilipino laban sa mga Amerikano noong 1899.']},
  {name:'Graciano López Jaena',color:'#F57C00',photo:'pictures/heroes/graciano_lopez.jpg',icon:'🗣️',role_en:'Journalist & Orator',role_tl:'Mamamahayag at Orador',years:'1856–1896',
   clues_en:['I founded the newspaper La Solidaridad.','I am known as the "Fighting Filipino Journalist".','I was a great orator and leader of the Propaganda Movement.'],
   clues_tl:['Itinayo ko ang pahayagang La Solidaridad.','Kilala ako bilang "Fighting Filipino Journalist".','Isa akong mahusay na orador at lider ng Kilusang Propaganda.']},
  {name:'Emilio Jacinto',color:'#512DA8',photo:'pictures/heroes/emilio_jacinto.jpg',icon:'📋',role_en:'Katipunan Leader',role_tl:'Lider ng Katipunan',years:'1875–1899',
   clues_en:['I wrote the Kartilya ng Katipunan.','I am known as the "Brains of the Katipunan".','I served as editor of the Kalayaan newspaper.'],
   clues_tl:['Sumulat ako ng Kartilya ng Katipunan.','Kilala ako bilang "Utak ng Katipunan".','Nagsilbi akong editor ng pahayagang Kalayaan.']},
  {name:'Gabriela Silang',color:'#C2185B',photo:'pictures/heroes/gabriela_silang.jpg',icon:'⚔️',role_en:'Woman Warrior',role_tl:'Babaeng Mandirigma',years:'1731–1763',
   clues_en:['I was the first female revolutionary leader in the Philippines.','I led the Ilocos revolt against the Spaniards.','I am a symbol of bravery and resistance for Filipinas.'],
   clues_tl:['Ako ang unang babaeng lider ng rebolusyon sa Pilipinas.','Pinamunuan ko ang Ilocos revolt laban sa mga Kastila.','Ako ay simbolo ng katapangan para sa mga Pilipina.']},
  {name:'Lapu-Lapu',color:'#795548',photo:'pictures/heroes/lapu_lapu.jpg',icon:'🛡️',role_en:'First Filipino Hero',role_tl:'Unang Bayani ng Pilipinas',years:'1491–1542',
   clues_en:['I defeated Ferdinand Magellan at the Battle of Mactan in 1521.','I am known as the first Filipino hero.','I was the datu (chief) of Mactan island.'],
   clues_tl:['Tinalo ko si Ferdinand Magellan sa Labanan ng Mactan noong 1521.','Kilala ako bilang unang bayani ng Pilipinas.','Ako ang datu ng pulo ng Mactan.']},
  {name:'Juan Luna',color:'#1976D2',photo:'pictures/heroes/juan_luna.png',icon:'🎨',role_en:'Famous Painter',role_tl:'Sikat na Pintor',years:'1857–1899',
   clues_en:['I painted the famous artwork "Spolarium".','I was a famous Filipino painter.','I supported the Propaganda Movement through my art.'],
   clues_tl:['Pininta ko ang sikat na obra-maestra na "Spolarium".','Ako ay isang kilalang Pilipinong pintor.','Sinuportahan ko ang Kilusang Propaganda sa pamamagitan ng aking sining.']},
];
const FILL_HEROES=[
  {name:'Jose Rizal',      parts:['Jose',' ','Rizal'],      blankIdx:0, role_en:'Writer & Doctor',         role_tl:'Manunulat at Doktor',      years:'1861–1896',color:'#1565C0',photo:'pictures/jose_rizal.png'},
  {name:'Jose Rizal',      parts:['Jose',' ','Rizal'],      blankIdx:2, role_en:'Writer & Doctor',         role_tl:'Manunulat at Doktor',      years:'1861–1896',color:'#1565C0',photo:'pictures/jose_rizal.png'},
  {name:'Andres Bonifacio',parts:['Andres',' ','Bonifacio'],blankIdx:0, role_en:'Revolutionary Leader',    role_tl:'Lider ng Rebolusyon',      years:'1863–1897',color:'#B71C1C',photo:'pictures/andres_bonifacio.png'},
  {name:'Andres Bonifacio',parts:['Andres',' ','Bonifacio'],blankIdx:2, role_en:'Revolutionary Leader',    role_tl:'Lider ng Rebolusyon',      years:'1863–1897',color:'#B71C1C',photo:'pictures/andres_bonifacio.png'},
  {name:'Emilio Aguinaldo',parts:['Emilio',' ','Aguinaldo'],blankIdx:2, role_en:'First President',         role_tl:'Unang Pangulo',            years:'1869–1964',color:'#1B5E20',photo:'pictures/emilio_aguinaldo.png'},
  {name:'Melchora Aquino', parts:['Melchora',' ','Aquino'], blankIdx:0, role_en:'Mother of Revolution',    role_tl:'Ina ng Rebolusyon',        years:'1812–1919',color:'#880E4F',photo:'pictures/melchora_aquino.png'},
  {name:'Antonio Luna',    parts:['Antonio',' ','Luna'],    blankIdx:2, role_en:'General & Scientist',     role_tl:'Heneral at Siyentipiko',   years:'1866–1899',color:'#3E2723',photo:'pictures/antonio_luna.png'},
  {name:'Apolinario Mabini',parts:['Apolinario',' ','Mabini'],blankIdx:0,role_en:'Political Philosopher',  role_tl:'Politikal na Pilosopo',    years:'1864–1903',color:'#4A148C',photo:'pictures/apolinario_mabini.png'},
  {name:'Gregorio del Pilar',parts:['Gregorio',' ','del Pilar'],blankIdx:0,role_en:'Boy General',          role_tl:'Batang Heneral',           years:'1875–1899',color:'#E65100',photo:'pictures/gregorio_del_pilar.png'},
  {name:'Marcelo H. del Pilar',parts:['Marcelo',' ','del Pilar'],blankIdx:0,role_en:'Journalist & Propagandist',role_tl:'Mamamahayag at Propagandista',years:'1850–1896',color:'#006064',photo:'pictures/marcelo_del_pilar.png'},
];
const WRONG_WORDS=['Marcos','Santos','Garcia','Reyes','Cruz','dela Torre','Gomez','Paterno','Jacinto','Gregorio','Makaraig','Ibarra'];
const TILE_COLORS=['#1565C0','#B71C1C','#1B5E20','#4A148C','#880E4F','#E65100','#006064','#3E2723'];
const TRIVIA=[
  {hi:0,cat_en:'📖 Role',cat_tl:'📖 Papel',q_en:'What was Jose Rizal known as?',q_tl:'Ano ang pagkakilala kay Jose Rizal?',ans_en:'Writer & Doctor',ans_tl:'Manunulat at Doktor',w_en:['Revolutionary Leader','First President','Boy General'],w_tl:['Lider ng Rebolusyon','Unang Pangulo','Batang Heneral']},
  {hi:1,cat_en:'⚔️ Role',cat_tl:'⚔️ Papel',q_en:'What was Andres Bonifacio known as?',q_tl:'Ano ang pagkakilala kay Andres Bonifacio?',ans_en:'Father of the Philippine Revolution',ans_tl:'Ama ng Rebolusyong Pilipino',w_en:['First President','Sublime Paralytic','Boy General'],w_tl:['Unang Pangulo','Dakilang Paralitiko','Batang Heneral']},
  {hi:2,cat_en:'📅 Lifespan',cat_tl:'📅 Panahon ng Buhay',q_en:'When was Emilio Aguinaldo born?',q_tl:'Kailan ipinanganak si Emilio Aguinaldo?',ans_en:'1869',ans_tl:'1869',w_en:['1861','1863','1875'],w_tl:['1861','1863','1875']},
  {hi:3,cat_en:'🏷️ Nickname',cat_tl:'🏷️ Palayaw',q_en:'What is Apolinario Mabini\'s famous nickname?',q_tl:'Ano ang tanyag na palayaw ni Apolinario Mabini?',ans_en:'The Sublime Paralytic',ans_tl:'Ang Dakilang Paralitiko',w_en:['The Boy General','The Great Propagandist','The Mother of Revolution'],w_tl:['Ang Batang Heneral','Ang Dakilang Propagandista','Ang Ina ng Rebolusyon']},
  {hi:4,cat_en:'🏷️ Nickname',cat_tl:'🏷️ Palayaw',q_en:'What is Melchora Aquino\'s famous nickname?',q_tl:'Ano ang tanyag na palayaw ni Melchora Aquino?',ans_en:'Tandang Sora',ans_tl:'Tandang Sora',w_en:['Bulakenyo','Plaridel','El Fili'],w_tl:['Bulakenyo','Plaridel','El Fili']},
  {hi:5,cat_en:'🎖️ Role',cat_tl:'🎖️ Papel',q_en:'Why is Gregorio del Pilar called the "Boy General"?',q_tl:'Bakit tinatawag si Gregorio del Pilar na "Batang Heneral"?',ans_en:'He was the youngest general in the Revolution',ans_tl:'Siya ang pinakabatang heneral sa Rebolusyon',w_en:['He wrote books as a boy','He was the first general','He was only 5 years old'],w_tl:['Sumulat siya ng mga libro noong bata','Siya ang unang heneral','Lima taon pa lang siya']},
  {hi:6,cat_en:'🏷️ Pen Name',cat_tl:'🏷️ Sagisag',q_en:'What was Marcelo H. del Pilar\'s pen name?',q_tl:'Ano ang sagisag ni Marcelo H. del Pilar?',ans_en:'Plaridel',ans_tl:'Plaridel',w_en:['Rizal','Bonifacio','El Fili'],w_tl:['Rizal','Bonifacio','El Fili']},
  {hi:7,cat_en:'🔬 Roles',cat_tl:'🔬 Mga Papel',q_en:'Antonio Luna was both a general AND a what?',q_tl:'Si Antonio Luna ay isang heneral AT ano pa?',ans_en:'Scientist / Pharmacist',ans_tl:'Siyentipiko / Parmasyutiko',w_en:['President','Writer','Priest'],w_tl:['Pangulo','Manunulat','Pari']},
  {hi:0,cat_en:'📅 Year',cat_tl:'📅 Taon',q_en:'In what year was Jose Rizal executed?',q_tl:'Noong anong taon pinaputukan si Jose Rizal?',ans_en:'1896',ans_tl:'1896',w_en:['1899','1897','1901'],w_tl:['1899','1897','1901']},
  {hi:1,cat_en:'🏛️ Organization',cat_tl:'🏛️ Samahan',q_en:'What secret society did Andres Bonifacio found?',q_tl:'Anong lihim na samahan ang itinatag ni Andres Bonifacio?',ans_en:'Katipunan',ans_tl:'Katipunan',w_en:['Propaganda Movement','La Solidaridad','Ilustrado'],w_tl:['Kilusang Propaganda','La Solidaridad','Ilustrado']},
];
const LETTERS=['A','B','C','D'];

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
  if(activeTab===1){
    const c1=HM_COPY[lang];
    document.getElementById('p1SubmitLbl').textContent=c1.submitLbl;
    const isLastRound=hm_round>=HM_TOTAL_ROUNDS;
    document.getElementById('p1NextLbl').textContent=isLastRound?c1.finishLbl:c1.nextLbl;
  }
  if(activeTab===1)hm_startGame();
  if(activeTab===2)wi_startGame();
  if(activeTab===3)fi_startGame();
  if(activeTab===4)tr_startGame();
}

// ── TABS ──
function switchTab(num){
  activeTab=num;
  document.querySelectorAll('.act-tab').forEach((t,i)=>t.classList.toggle('active',i+1===num));
  document.querySelectorAll('.quizzes-panel').forEach((p,i)=>p.classList.toggle('active',i+1===num));
  if(num===1)hm_startGame();
  if(num===2)wi_startGame();
  if(num===3)fi_startGame();
  if(num===4)tr_startGame();
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
  confetti('#E65100');confetti('#ffd43b');
  saveProgress(ACT_IDS[actNum],finalScore);
  saveComplete(ACT_IDS[actNum],finalScore);
  PREV_SCORES[actNum]=Math.max(PREV_SCORES[actNum],finalScore);
}
function closeResult(){document.getElementById('resultOverlay').classList.remove('active');}
function playAgain(){
  closeResult();
  if(_curActNum===1)hm_startGame();
  if(_curActNum===2)wi_startGame();
  if(_curActNum===3)fi_startGame();
  if(_curActNum===4)tr_startGame();
}
function goNextquizzes(){closeResult();switchTab(_curActNum+1);}

// ── UTILS ──
function shuffle(a){const r=[...a];for(let i=r.length-1;i>0;i--){const j=0|Math.random()*(i+1);[r[i],r[j]]=[r[j],r[i]];}return r;}
function speak(w){if(!window.speechSynthesis)return;window.speechSynthesis.cancel();const u=new SpeechSynthesisUtterance(w);u.lang='en-US';u.rate=0.8;u.pitch=1.1;window.speechSynthesis.speak(u);}
function confetti(color){const cols=[color,'#FFE66D','#FF6B6B','#A29BFE','#4ECDC4','#FD79A8'];for(let i=0;i<30;i++){const p=document.createElement('div');p.className='cfbit';p.style.cssText=`left:${Math.random()*100}vw;top:-12px;background:${cols[0|Math.random()*cols.length]};border-radius:${Math.random()>.5?'50%':'3px'};width:${6+Math.random()*8}px;height:${6+Math.random()*8}px;animation-duration:${1.2+Math.random()*1.5}s;animation-delay:${Math.random()*.4}s;`;document.body.appendChild(p);p.addEventListener('animationend',()=>p.remove());}}
function role(h){return lang==='en'?h.role_en:h.role_tl;}

// ════════════════════════════════════════
// quizzes 1: HERO MATCH
// ════════════════════════════════════════
const HM_COPY={en:{title:'Hero Match!',sub:"Match each hero's name to the correct photo!",colName:'Hero Names',colPhoto:'Photos',roundInfo:r=>`Round ${r} of 5 — Match 4 heroes!`,progressLbl:(m,t)=>`${m} / ${t} matched`,submitLbl:'Submit Round',nextLbl:'Next Round',finishLbl:'See Results',feedbackCorrect:(n,pts)=>`✅ Perfect! +${pts} pts earned this round!`,feedbackPartial:(c,gained)=>`⭐ ${c}/4 correct! +${gained} pts earned.`,resultTitle:'Bayani!',resultSub:'You matched all the heroes!'},tl:{title:'Pagtutugma ng Bayani!',sub:'Itugma ang pangalan ng bawat bayani sa tamang larawan!',colName:'Mga Pangalan',colPhoto:'Mga Larawan',roundInfo:r=>`Round ${r} ng 5 — Itugma ang 4 na bayani!`,progressLbl:(m,t)=>`${m} / ${t} natugma`,submitLbl:'I-submit ang Round',nextLbl:'Susunod na Round',finishLbl:'Tingnan ang Resulta',feedbackCorrect:(n,pts)=>`✅ Perpekto! +${pts} pts nakuha!`,feedbackPartial:(c,gained)=>`⭐ ${c}/4 tama! +${gained} pts nakuha.`,resultTitle:'Bayani!',resultSub:'Natugma mo ang lahat ng bayani!'}};
// 5 rounds × 4 pairs × 5pts = 100pts
const HM_TOTAL_ROUNDS=5,HM_PPM=5,HM_MAX=HM_TOTAL_ROUNDS*4*HM_PPM; // 100
let hm_score=0,hm_round=0,hm_matched=0,hm_pairs={},hm_submitted=false,hm_pool=[],hm_roundHeroes=[],hm_selName=null,hm_selPhoto=null;

function hm_startGame(){
  hm_score=0;hm_round=0;hm_pairs={};hm_submitted=false;hm_pool=shuffle([...HEROES]);
  document.getElementById('p1Score').textContent='0';
  document.getElementById('resultOverlay').classList.remove('active');
  const c=HM_COPY[lang];
  document.getElementById('p1Title').textContent=c.title;
  document.getElementById('p1Sub').textContent=c.sub;
  document.getElementById('p1ColName').textContent=c.colName;
  document.getElementById('p1ColPhoto').textContent=c.colPhoto;
  document.getElementById('p1SubmitLbl').textContent=c.submitLbl;
  document.getElementById('p1NextLbl').textContent=c.nextLbl;
  saveStart(ACT_IDS[1]);
  hm_nextRound();
}
function hm_nextRound(){
  hm_round++;hm_matched=0;hm_pairs={};hm_submitted=false;hm_selName=null;hm_selPhoto=null;
  if(hm_pool.length<4)hm_pool=shuffle([...HEROES]);
  hm_roundHeroes=hm_pool.splice(0,4);
  const c=HM_COPY[lang];
  document.getElementById('p1Round').textContent=hm_round;
  document.getElementById('p1RoundInfo').textContent=c.roundInfo(hm_round);
  document.getElementById('p1Bar').style.width='0%';
  document.getElementById('p1BarLbl').textContent=c.progressLbl(0,4);
  document.getElementById('p1Feedback').textContent='';document.getElementById('p1Feedback').className='feedback-msg';
  document.getElementById('p1SubmitBtn').style.display='';
  document.getElementById('p1NextBtn').style.display='none';
  const lastRound=hm_round>=HM_TOTAL_ROUNDS;
  document.getElementById('p1NextLbl').textContent=lastRound?HM_COPY[lang].finishLbl:HM_COPY[lang].nextLbl;
  hm_renderCards();
}
function hm_renderCards(){
  const ns=shuffle([...hm_roundHeroes]),ps=shuffle([...hm_roundHeroes]);
  const nc=document.getElementById('p1NameCol');nc.innerHTML='';
  const pc=document.getElementById('p1PhotoCol');pc.innerHTML='';
  ns.forEach(h=>{
    const card=document.createElement('div');card.className='name-card';card.dataset.key=h.name;card.style.setProperty('--cc',h.color);
    card.innerHTML=`<div class="hero-name-text" style="color:${h.color}">${h.name}</div><div class="hero-role-text">${role(h)}</div><i class="fas fa-check match-check"></i>`;
    card.onclick=()=>hm_selectName(card,h);nc.appendChild(card);
  });
  ps.forEach(h=>{
    const card=document.createElement('div');card.className='photo-card';card.dataset.key=h.name;card.style.setProperty('--cc',h.color);
    card.innerHTML=`<div class="photo-wrap"><img src="${h.photo}" alt="${h.name}" onerror="this.parentElement.style.background='${h.color}20'"></div><div class="photo-check"><i class="fas fa-check"></i></div>`;
    card.onclick=()=>hm_selectPhoto(card,h);pc.appendChild(card);
  });
}
function hm_selectName(card,hero){
  if(hm_submitted)return;
  if(card.classList.contains('matched'))return;
  document.querySelectorAll('#p1NameCol .name-card.selected').forEach(c=>c.classList.remove('selected'));
  hm_selName={card,hero};card.classList.add('selected');
  if(hm_selName&&hm_selPhoto)hm_pairCards();
}
function hm_selectPhoto(card,hero){
  if(hm_submitted)return;
  if(card.classList.contains('matched'))return;
  document.querySelectorAll('#p1PhotoCol .photo-card.selected').forEach(c=>c.classList.remove('selected'));
  hm_selPhoto={card,hero};card.classList.add('selected');
  if(hm_selName&&hm_selPhoto)hm_pairCards();
}
function hm_pairCards(){
  const n=hm_selName,p=hm_selPhoto;hm_selName=null;hm_selPhoto=null;
  // Unlink old photo for this name
  const oldByName=Object.values(hm_pairs).find(x=>x.nameCard===n.card);
  if(oldByName){oldByName.photoCard.classList.remove('matched','selected');delete hm_pairs[oldByName.nameKey];}
  const nameKey=n.hero.name;
  if(hm_pairs[nameKey]){hm_pairs[nameKey].photoCard.classList.remove('matched','selected');}
  hm_pairs[nameKey]={nameCard:n.card,photoCard:p.card,nameKey,photoKey:p.hero.name};
  n.card.classList.remove('selected');p.card.classList.remove('selected');
  n.card.classList.add('matched');p.card.classList.add('matched');
  hm_matched=Object.keys(hm_pairs).length;
  const c=HM_COPY[lang];
  document.getElementById('p1Bar').style.width=((hm_matched/4)*100)+'%';
  document.getElementById('p1BarLbl').textContent=c.progressLbl(hm_matched,4);
  speak(n.hero.name);
}
function hm_submitRound(){
  if(Object.keys(hm_pairs).length<4){
    document.getElementById('p1Feedback').textContent='⚠️ Match all 4 heroes first before submitting!';
    document.getElementById('p1Feedback').className='feedback-msg wrong';
    return;
  }
  hm_submitted=true;
  document.getElementById('p1SubmitBtn').style.display='none';
  let correctCount=0;
  hm_roundHeroes.forEach(h=>{
    const pair=hm_pairs[h.name];
    if(!pair)return;
    const isCorrect=pair.photoKey===h.name;
    if(isCorrect){
      correctCount++;
      pair.nameCard.style.borderColor='#40c057';pair.nameCard.style.background='#f0fdf4';
      pair.photoCard.style.borderColor='#40c057';pair.photoCard.style.background='#f0fdf4';
    } else {
      pair.nameCard.classList.remove('matched');pair.photoCard.classList.remove('matched');
      pair.nameCard.classList.add('wrong');pair.photoCard.classList.add('wrong');
      pair.nameCard.style.borderColor='#ff4444';pair.nameCard.style.background='#fff5f5';
      pair.photoCard.style.borderColor='#ff4444';pair.photoCard.style.background='#fff5f5';
    }
  });
  const roundGain=correctCount*HM_PPM;
  hm_score+=roundGain;
  document.getElementById('p1Score').textContent=hm_score;
  saveProgress(ACT_IDS[1],Math.min(100,Math.round((hm_score/HM_MAX)*100)));
  const c=HM_COPY[lang];
  if(correctCount===4){
    document.getElementById('p1Feedback').textContent=c.feedbackCorrect(4,roundGain);
    document.getElementById('p1Feedback').className='feedback-msg correct';
    confetti('#1565C0');
  } else {
    document.getElementById('p1Feedback').textContent=c.feedbackPartial(correctCount,roundGain);
    document.getElementById('p1Feedback').className=roundGain>0?'feedback-msg correct':'feedback-msg wrong';
  }
  document.getElementById('p1NextBtn').style.display='';
  const lastRound=hm_round>=HM_TOTAL_ROUNDS;
  document.getElementById('p1NextLbl').textContent=lastRound?HM_COPY[lang].finishLbl:HM_COPY[lang].nextLbl;
}
function hm_proceedNext(){
  if(hm_round>=HM_TOTAL_ROUNDS)hm_showResult();
  else hm_nextRound();
}
function hm_showResult(){
  document.getElementById('resultTitle').textContent=HM_COPY[lang].resultTitle;
  document.getElementById('resultSub').textContent=HM_COPY[lang].resultSub;
  showResult(1,hm_score,HM_MAX);
}

// ════════════════════════════════════════
// quizzes 2: WHO AM I
// ════════════════════════════════════════
const WI_COPY={en:{title:'Who Am I?',sub:'Read the clues and guess the hero!',clueLabel:'Who Am I?',submit:'Submit',next:'Next',feedbackCorrect:n=>`✅ Correct! I am ${n}!`,feedbackWrong:n=>`❌ I am ${n}! Read the clues again.`,resultTitle:'Detective!',resultSub:'You know all the heroes!'},tl:{title:'Sino Ako?',sub:'Basahin ang mga pahiwatig at hulaan ang bayani!',clueLabel:'Sino Ako?',submit:'I-submit',next:'Susunod',feedbackCorrect:n=>`✅ Tama! Ako si ${n}!`,feedbackWrong:n=>`❌ Ako si ${n}! Basahin ulit ang mga pahiwatig.`,resultTitle:'Detektibo!',resultSub:'Kilala mo ang lahat ng bayani!'}};
const WI_TOTAL=10,WI_MAX=WI_TOTAL*10;
let wi_pool=[],wi_current=null,wi_answered=false,wi_score=0,wi_qIdx=0,wi_lives=3;
let wi_selectedCard=null,wi_selectedHero=null,wi_selectedIsCorrect=false;

function wi_startGame(){
  wi_score=0;wi_qIdx=0;wi_lives=3;wi_answered=false;wi_pool=shuffle([...HEROES_CLUES]);
  wi_selectedCard=null;wi_selectedHero=null;
  document.getElementById('p2Score').textContent='0';
  document.getElementById('resultOverlay').classList.remove('active');
  wi_updateLives();
  const c=WI_COPY[lang];
  document.getElementById('p2Title').textContent=c.title;
  document.getElementById('p2Sub').textContent=c.sub;
  document.getElementById('p2SubmitLbl').textContent=c.submit;
  document.getElementById('p2NextLbl').textContent=c.next;
  saveStart(ACT_IDS[2]);
  wi_loadQuestion();
}
function wi_updateLives(){document.querySelectorAll('#p2Lives .life-icon').forEach((ic,i)=>ic.classList.toggle('lost',i>=wi_lives));}
function wi_loadQuestion(){
  wi_answered=false;wi_selectedCard=null;wi_selectedHero=null;wi_selectedIsCorrect=false;
  document.getElementById('p2SubmitBtn').style.display='none';
  document.getElementById('p2NextBtn').style.display='none';
  document.getElementById('p2Feedback').textContent='';document.getElementById('p2Feedback').className='feedback-msg';
  if(!wi_pool.length)wi_pool=shuffle([...HEROES_CLUES]);
  wi_current=wi_pool.shift();
  document.getElementById('p2QNum').textContent=wi_qIdx+1;
  document.getElementById('p2Bar').style.width=((wi_qIdx/WI_TOTAL)*100)+'%';
  document.getElementById('p2BarLbl').textContent=`${wi_qIdx} / ${WI_TOTAL}`;
  document.getElementById('p2ClueCard').style.setProperty('--qc',wi_current.color);
  document.getElementById('p2ClueIcon').textContent=wi_current.icon;
  document.getElementById('p2ClueLabel').textContent=WI_COPY[lang].clueLabel;
  const clues=lang==='en'?wi_current.clues_en:wi_current.clues_tl;
  const picked=shuffle([...clues]).slice(0,2);
  document.getElementById('p2ClueText').textContent=picked[0];
  const hintsEl=document.getElementById('p2ClueHints');hintsEl.innerHTML='';
  const chip=document.createElement('div');chip.className='clue-chip';
  chip.innerHTML=`<i class="fas fa-calendar-alt"></i>${wi_current.years}`;hintsEl.appendChild(chip);
  if(picked[1]){const c2=document.createElement('div');c2.className='clue-chip';c2.innerHTML=`<i class="fas fa-quote-left"></i>${picked[1].substring(0,38)}...`;hintsEl.appendChild(c2);}
  const wrong=shuffle(HEROES_CLUES.filter(h=>h.name!==wi_current.name)).slice(0,3);
  const choices=shuffle([wi_current,...wrong]);
  const grid=document.getElementById('p2Choices');grid.innerHTML='';
  choices.forEach((h,i)=>{
    const isCorrect=h.name===wi_current.name;
    const card=document.createElement('div');card.className='choice-card';card.style.setProperty('--cc',h.color);
    card.innerHTML=`<div class="choice-photo"><img src="${h.photo}" alt="${h.name}" onerror="this.parentElement.style.background='${h.color}22'"></div><div class="choice-info"><div class="choice-name" style="color:${h.color}">${h.name}</div><div class="choice-role">${role(h)}</div></div>`;
    card.onclick=()=>wi_selectAnswer(card,h,isCorrect);
    grid.appendChild(card);
  });
}
function wi_selectAnswer(card,hero,isCorrect){
  if(wi_answered)return;
  document.querySelectorAll('#p2Choices .choice-card').forEach(c=>c.classList.remove('selected'));
  card.classList.add('selected');
  wi_selectedCard=card;wi_selectedHero=hero;wi_selectedIsCorrect=isCorrect;
  document.getElementById('p2SubmitBtn').style.display='';
}
function wi_submitAnswer(){
  if(!wi_selectedCard||wi_answered)return;
  wi_answered=true;
  document.getElementById('p2SubmitBtn').style.display='none';
  document.querySelectorAll('#p2Choices .choice-card').forEach(c=>c.classList.add('answered'));
  const c=WI_COPY[lang];
  if(wi_selectedIsCorrect){
    wi_selectedCard.classList.add('correct');wi_selectedCard.classList.remove('selected');
    wi_score+=10; saveProgress(ACT_IDS[2],Math.round((wi_score/WI_MAX)*100));document.getElementById('p2Score').textContent=wi_score;
    document.getElementById('p2Feedback').textContent=c.feedbackCorrect(wi_current.name);
    document.getElementById('p2Feedback').className='feedback-msg correct';
    confetti(wi_current.color);wi_qIdx++;
  } else {
    wi_selectedCard.classList.add('wrong');wi_selectedCard.classList.remove('selected');
    document.querySelectorAll('#p2Choices .choice-card').forEach(cd=>{if(cd.querySelector('.choice-name')?.textContent===wi_current.name)cd.classList.add('correct');});
    wi_lives--;wi_updateLives();
    document.getElementById('p2Feedback').textContent=c.feedbackWrong(wi_current.name);
    document.getElementById('p2Feedback').className='feedback-msg wrong';
    wi_qIdx++;
  }
  document.getElementById('p2NextBtn').style.display='inline-flex';
}
function wi_nextQuestion(){
  if(wi_qIdx>=WI_TOTAL||wi_lives<=0)wi_showResult();
  else wi_loadQuestion();
}
function wi_showResult(){
  document.getElementById('resultTitle').textContent=WI_COPY[lang].resultTitle;
  document.getElementById('resultSub').textContent=WI_COPY[lang].resultSub;
  showResult(2,wi_score,WI_MAX);
}

// ════════════════════════════════════════
// quizzes 3: FILL IN THE BLANK
// ════════════════════════════════════════
const FI_COPY={en:{title:'Fill in the Blank!',sub:"Complete the hero's name using the word bank!",wbLabel:'Pick the missing word:',submit:'Submit',next:'Next',feedbackCorrect:n=>`✅ Correct! The name is ${n}!`,feedbackWrong:n=>`❌ The missing word is "${n}"!`,resultTitle:'Spelling Hero!',resultSub:'You completed all the names!'},tl:{title:'Kumpletuhin ang Pangalan!',sub:'Kumpletuhin ang pangalan ng bayani gamit ang word bank!',wbLabel:'Piliin ang nawawalang salita:',submit:'I-submit',next:'Susunod',feedbackCorrect:n=>`✅ Tama! Ang pangalan ay ${n}!`,feedbackWrong:n=>`❌ Ang nawawalang salita ay "${n}"!`,resultTitle:'Bayaning Manunulat!',resultSub:'Nakumpleto mo ang lahat ng pangalan!'}};
const FI_TOTAL=10,FI_MAX=FI_TOTAL*10;
let fi_pool=[],fi_current=null,fi_answered=false,fi_score=0,fi_qIdx=0,fi_lives=3;
let fi_selectedTile=null,fi_selectedWord=null;

function fi_startGame(){
  fi_score=0;fi_qIdx=0;fi_lives=3;fi_answered=false;fi_pool=shuffle([...FILL_HEROES]);
  fi_selectedTile=null;fi_selectedWord=null;
  document.getElementById('p3Score').textContent='0';
  document.getElementById('resultOverlay').classList.remove('active');
  fi_updateLives();
  const c=FI_COPY[lang];
  document.getElementById('p3Title').textContent=c.title;
  document.getElementById('p3Sub').textContent=c.sub;
  document.getElementById('p3WbLabel').textContent=c.wbLabel;
  document.getElementById('p3SubmitLbl').textContent=c.submit;
  document.getElementById('p3NextLbl').textContent=c.next;
  saveStart(ACT_IDS[3]);
  fi_loadQuestion();
}
function fi_updateLives(){document.querySelectorAll('#p3Lives .life-icon').forEach((ic,i)=>ic.classList.toggle('lost',i>=fi_lives));}
function fi_loadQuestion(){
  fi_answered=false;fi_selectedTile=null;fi_selectedWord=null;
  document.getElementById('p3SubmitBtn').style.display='none';
  document.getElementById('p3NextBtn').style.display='none';
  document.getElementById('p3Feedback').textContent='';document.getElementById('p3Feedback').className='feedback-msg';
  if(!fi_pool.length)fi_pool=shuffle([...FILL_HEROES]);
  fi_current=fi_pool.shift();
  document.getElementById('p3QNum').textContent=fi_qIdx+1;
  document.getElementById('p3Bar').style.width=((fi_qIdx/FI_TOTAL)*100)+'%';
  document.getElementById('p3BarLbl').textContent=`${fi_qIdx} / ${FI_TOTAL}`;
  document.getElementById('p3HeroDisplay').style.setProperty('--hc',fi_current.color);
  document.getElementById('p3HeroPhoto').style.borderColor=fi_current.color;
  document.getElementById('p3HeroImg').src=fi_current.photo;
  document.getElementById('p3HeroRole').style.color=fi_current.color;
  document.getElementById('p3HeroRole').textContent=role(fi_current);
  document.getElementById('p3HeroYears').textContent=fi_current.years;
  // Render slots
  const slotsEl=document.getElementById('p3NameSlots');slotsEl.innerHTML='';
  fi_current.parts.forEach((part,i)=>{
    if(part===' '){const sep=document.createElement('div');sep.className='name-sep';sep.textContent=' ';slotsEl.appendChild(sep);return;}
    const slot=document.createElement('div');slot.className='name-slot';slot.id=`p3slot-${i}`;
    if(i===fi_current.blankIdx){slot.classList.add('blank');slot.style.setProperty('--hc',fi_current.color);slot.textContent='';}
    else{slot.textContent=part;slot.style.color=fi_current.color;}
    slotsEl.appendChild(slot);
  });
  // Word bank
  const correctWord=fi_current.parts[fi_current.blankIdx];
  const wrongPool=shuffle(WRONG_WORDS.filter(w=>w!==correctWord)).slice(0,3);
  const allWords=shuffle([correctWord,...wrongPool]);
  const wb=document.getElementById('p3WordBank');wb.innerHTML='';
  allWords.forEach((w,i)=>{
    const tile=document.createElement('button');tile.className='word-tile';
    const tc=TILE_COLORS[i%TILE_COLORS.length];tile.style.setProperty('--wc',tc);tile.style.color=tc;tile.style.borderColor=tc;
    tile.textContent=w;tile.style.animationDelay=(i*.06)+'s';
    tile.onclick=()=>fi_selectWord(tile,w);wb.appendChild(tile);
  });
}
function fi_selectWord(tile,word){
  if(fi_answered)return;
  document.querySelectorAll('#p3WordBank .word-tile').forEach(t=>t.classList.remove('selected'));
  tile.classList.add('selected');
  fi_selectedTile=tile;fi_selectedWord=word;
  // Preview in blank slot
  const slot=document.getElementById(`p3slot-${fi_current.blankIdx}`);
  if(slot){slot.textContent=word;slot.style.color=fi_current.color;}
  document.getElementById('p3SubmitBtn').style.display='';
}
function fi_submitAnswer(){
  if(!fi_selectedTile||fi_answered)return;
  fi_answered=true;
  document.getElementById('p3SubmitBtn').style.display='none';
  document.querySelectorAll('#p3WordBank .word-tile').forEach(t=>t.classList.add('used'));
  const correctWord=fi_current.parts[fi_current.blankIdx];
  const slot=document.getElementById(`p3slot-${fi_current.blankIdx}`);
  const c=FI_COPY[lang];
  if(fi_selectedWord===correctWord){
    fi_selectedTile.classList.remove('used');fi_selectedTile.classList.add('correct-tile');
    if(slot){slot.textContent=fi_selectedWord;slot.classList.remove('blank');slot.classList.add('revealed');slot.style.color=fi_current.color;}
    fi_score+=10; saveProgress(ACT_IDS[3],Math.round((fi_score/FI_MAX)*100));document.getElementById('p3Score').textContent=fi_score;
    document.getElementById('p3Feedback').textContent=c.feedbackCorrect(fi_current.name);
    document.getElementById('p3Feedback').className='feedback-msg correct';
    confetti(fi_current.color);
    fi_qIdx++;
  } else {
    fi_selectedTile.classList.remove('used');fi_selectedTile.classList.add('wrong-tile');
    fi_lives--;fi_updateLives();
    document.getElementById('p3Feedback').textContent=c.feedbackWrong(correctWord);
    document.getElementById('p3Feedback').className='feedback-msg wrong';
    document.querySelectorAll('#p3WordBank .word-tile').forEach(t=>{if(t.textContent===correctWord){t.classList.remove('used');t.classList.add('correct-tile');}});
    if(slot){slot.textContent=correctWord;slot.classList.remove('blank');slot.classList.add('revealed');slot.style.color=fi_current.color;}
    fi_qIdx++;
  }
  document.getElementById('p3NextBtn').style.display='inline-flex';
}
function fi_nextQuestion(){
  if(fi_qIdx>=FI_TOTAL||fi_lives<=0)fi_showResult();
  else fi_loadQuestion();
}
function fi_showResult(){
  document.getElementById('resultTitle').textContent=FI_COPY[lang].resultTitle;
  document.getElementById('resultSub').textContent=FI_COPY[lang].resultSub;
  showResult(3,fi_score,FI_MAX);
}

// ════════════════════════════════════════
// quizzes 4: HERO TRIVIA
// ════════════════════════════════════════
const TR_COPY={en:{title:'Hero Trivia!',sub:'Answer questions about our national heroes!',submit:'Submit',feedbackCorrect:'✅ Correct! Well done!',feedbackWrong:a=>`❌ The answer is: ${a}`,next:'Next',resultTitle:'History Master!',resultSub:'You know your heroes!'},tl:{title:'Hero Trivia!',sub:'Sagutin ang mga tanong tungkol sa ating mga bayani!',submit:'I-submit',feedbackCorrect:'✅ Tama! Napakahusay!',feedbackWrong:a=>`❌ Ang sagot ay: ${a}`,next:'Susunod',resultTitle:'Master ng Kasaysayan!',resultSub:'Kilala mo ang iyong mga bayani!'}};
const TR_TOTAL=10,TR_MAX=TR_TOTAL*10;
let tr_pool=[],tr_current=null,tr_answered=false,tr_score=0,tr_qIdx=0,tr_lives=3;
let tr_selectedBtn=null,tr_selectedIsCorrect=false,tr_selectedAns=null,tr_selectedColor=null;

function tr_startGame(){
  tr_score=0;tr_qIdx=0;tr_lives=3;tr_answered=false;tr_pool=shuffle([...TRIVIA]);
  tr_selectedBtn=null;
  document.getElementById('p4Score').textContent='0';
  document.getElementById('resultOverlay').classList.remove('active');
  tr_updateLives();
  const c=TR_COPY[lang];
  document.getElementById('p4Title').textContent=c.title;
  document.getElementById('p4Sub').textContent=c.sub;
  document.getElementById('p4SubmitLbl').textContent=c.submit;
  document.getElementById('p4NextLbl').textContent=c.next;
  saveStart(ACT_IDS[4]);
  tr_loadQuestion();
}
function tr_updateLives(){document.querySelectorAll('#p4Lives .life-icon').forEach((ic,i)=>ic.classList.toggle('lost',i>=tr_lives));}
function tr_loadQuestion(){
  tr_answered=false;tr_selectedBtn=null;tr_selectedIsCorrect=false;tr_selectedAns=null;tr_selectedColor=null;
  document.getElementById('p4SubmitBtn').style.display='none';
  document.getElementById('p4NextBtn').style.display='none';
  document.getElementById('p4Feedback').textContent='';document.getElementById('p4Feedback').className='feedback-msg';
  if(!tr_pool.length)tr_pool=shuffle([...TRIVIA]);
  tr_current=tr_pool.shift();
  const hero=HEROES[tr_current.hi];
  document.getElementById('p4QNum').textContent=tr_qIdx+1;
  document.getElementById('p4Bar').style.width=((tr_qIdx/TR_TOTAL)*100)+'%';
  document.getElementById('p4BarLbl').textContent=`${tr_qIdx} / ${TR_TOTAL}`;
  document.getElementById('p4QCard').style.setProperty('--qc',hero.color);
  document.getElementById('p4HeroPhoto').style.borderColor=hero.color;
  document.getElementById('p4HeroImg').src=hero.photo;
  document.getElementById('p4HeroName').textContent=hero.name;document.getElementById('p4HeroName').style.color=hero.color;
  document.getElementById('p4HeroYears').textContent=hero.years;
  document.getElementById('p4QCategory').style.background=hero.color+'18';document.getElementById('p4QCategory').style.color=hero.color;
  document.getElementById('p4QCategory').innerHTML=`<i class="fas fa-star"></i> ${lang==='en'?tr_current.cat_en:tr_current.cat_tl}`;
  document.getElementById('p4QText').textContent=lang==='en'?tr_current.q_en:tr_current.q_tl;
  const ans=lang==='en'?tr_current.ans_en:tr_current.ans_tl;
  const wrongs=lang==='en'?tr_current.w_en:tr_current.w_tl;
  const choices=shuffle([ans,...wrongs]);
  const grid=document.getElementById('p4Choices');grid.innerHTML='';
  choices.forEach((ch,i)=>{
    const isCorrect=ch===ans;
    const btn=document.createElement('button');btn.className='choice-btn';
    btn.innerHTML=`<div class="choice-letter" style="background:${hero.color}">${LETTERS[i]}</div><div class="choice-text">${ch}</div>`;
    btn.onclick=()=>tr_selectAnswer(btn,ch,isCorrect,ans,hero.color);
    grid.appendChild(btn);
  });
}
function tr_selectAnswer(btn,chosen,isCorrect,correctAns,color){
  if(tr_answered)return;
  document.querySelectorAll('#p4Choices .choice-btn').forEach(b=>b.classList.remove('selected'));
  btn.classList.add('selected');
  tr_selectedBtn=btn;tr_selectedIsCorrect=isCorrect;tr_selectedAns=correctAns;tr_selectedColor=color;
  document.getElementById('p4SubmitBtn').style.display='';
}
function tr_submitAnswer(){
  if(!tr_selectedBtn||tr_answered)return;
  tr_answered=true;
  document.getElementById('p4SubmitBtn').style.display='none';
  document.querySelectorAll('#p4Choices .choice-btn').forEach(b=>b.classList.add('answered'));
  const c=TR_COPY[lang];
  if(tr_selectedIsCorrect){
    tr_selectedBtn.classList.add('correct');tr_selectedBtn.classList.remove('selected');
    tr_score+=10; saveProgress(ACT_IDS[4],Math.round((tr_score/TR_MAX)*100));document.getElementById('p4Score').textContent=tr_score;
    document.getElementById('p4Feedback').textContent=c.feedbackCorrect;
    document.getElementById('p4Feedback').className='feedback-msg correct';
    confetti(tr_selectedColor);tr_qIdx++;
  } else {
    tr_selectedBtn.classList.add('wrong');tr_selectedBtn.classList.remove('selected');
    document.querySelectorAll('#p4Choices .choice-btn').forEach(b=>{if(b.querySelector('.choice-text')?.textContent===tr_selectedAns)b.classList.add('correct');});
    tr_lives--;tr_updateLives();
    document.getElementById('p4Feedback').textContent=c.feedbackWrong(tr_selectedAns);
    document.getElementById('p4Feedback').className='feedback-msg wrong';
    tr_qIdx++;
  }
  document.getElementById('p4NextBtn').style.display='inline-flex';
}
function tr_nextQuestion(){
  if(tr_qIdx>=TR_TOTAL||tr_lives<=0)tr_showResult();
  else tr_loadQuestion();
}
function tr_showResult(){
  document.getElementById('resultTitle').textContent=TR_COPY[lang].resultTitle;
  document.getElementById('resultSub').textContent=TR_COPY[lang].resultSub;
  showResult(4,tr_score,TR_MAX);
}


// ── FLUSH PROGRESS ON NAVIGATION / PAGE HIDE ──
function flushActiveTabProgress() {
  switch(activeTab) {
    case 1: if(hm_score>0) saveProgress(ACT_IDS[1],Math.min(100,Math.round((hm_score/HM_MAX)*100))); break;
    case 2: if(wi_score>0) saveProgress(ACT_IDS[2],Math.round((wi_score/WI_MAX)*100)); break;
    case 3: if(fi_score>0) saveProgress(ACT_IDS[3],Math.round((fi_score/FI_MAX)*100)); break;
    case 4: if(tr_score>0) saveProgress(ACT_IDS[4],Math.round((tr_score/TR_MAX)*100)); break;
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
if(activeTab===1)hm_startGame();
else if(activeTab===2)wi_startGame();
else if(activeTab===3)fi_startGame();
else if(activeTab===4)tr_startGame();
</script>

</div><!-- /page-wrap -->
</body>
</html>