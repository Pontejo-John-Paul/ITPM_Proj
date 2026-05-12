<?php
session_start();
require_once 'database.php';
require_once 'activity_helper.php';

if (!isset($_SESSION['student_id']) || $_SESSION['role'] !== 'student') {
    header("Location: index.php");
    exit;
}

$student_id = $_SESSION['student_id'];
$lesson_id  = isset($_GET['lesson_id']) ? (int)$_GET['lesson_id'] : 8; // Body = 8

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
        quizzes_save_progress($conn, $student_id, $quizzes_id, $score, $checkpoint);
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

$a1 = $act_map['body_part_match']    ?? ['quizzes_id'=>0,'status'=>'not_started','score'=>0];
$a2 = $act_map['point_it_out']       ?? ['quizzes_id'=>0,'status'=>'not_started','score'=>0];
$a3 = $act_map['body_part_sorting']  ?? ['quizzes_id'=>0,'status'=>'not_started','score'=>0];
$a4 = $act_map['simon_says']         ?? ['quizzes_id'=>0,'status'=>'not_started','score'=>0];

$active_tab = isset($_GET['tab']) ? max(1, min(4, (int)$_GET['tab'])) : 1;
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Body Parts Activities — E-KINDER</title>
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
.act-tab:hover:not(.active){border-color:#ffd6d6;color:#555;transform:translateY(-2px);}
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

/* ── ACT 1: BODY MATCH ── */
.round-info{font-family:'Fredoka One',cursive;font-size:.85rem;color:#bbb;text-align:center;margin-bottom:20px;text-transform:uppercase;letter-spacing:1px;}
.match-container{ display:grid; grid-template-columns:1fr 1fr; gap:20px; margin-bottom:20px; max-width:740px; margin-left:auto; margin-right:auto;}
.match-col-label{font-family:'Fredoka One',cursive;font-size:.78rem;color:#888;text-transform:uppercase;letter-spacing:1px;text-align:center;margin-bottom:12px;}
.match-col{ display:flex; flex-direction:column; gap:12px; }
.name-card{background:#fff;border:3px solid #f0f0f0;border-radius:16px;padding:14px 12px;text-align:center;cursor:pointer;box-shadow:0 4px 14px rgba(0,0,0,.06);transition:all .25s cubic-bezier(.34,1.56,.64,1);}
.name-card:hover:not(.matched):not(.disabled){transform:translateY(-4px) scale(1.03);box-shadow:0 10px 26px rgba(0,0,0,.1);}
.name-card.selected{border-color:var(--cc,#FF6B6B);background:color-mix(in srgb,var(--cc,#FF6B6B) 10%,white);transform:translateY(-4px) scale(1.04);}
.name-card.matched{border-color:#40c057;background:#f0fdf4;cursor:default;animation:matchPop .4s cubic-bezier(.34,1.56,.64,1);}
.name-card.wrong{border-color:#ff4444;background:#fff5f5;animation:cardShake .4s ease;}
.name-text{font-family:'Fredoka One',cursive;font-size:1rem;color:var(--cc,#FF6B6B);}
.name-local{font-size:.65rem;color:#ccc;font-weight:700;margin-top:2px;}
.match-check{font-size:.8rem;color:#40c057;opacity:0;display:block;margin-top:3px;}
.name-card.matched .match-check{opacity:1;}

/* PHOTO CARDS — smaller size */
.photo-card{
  background:#fff; border:3px solid #f0f0f0; border-radius:16px; overflow:hidden; min-height:150px; display:flex; align-items:center; justify-content:center; padding:12px;}
.photo-card:hover:not(.matched):not(.disabled){transform:translateY(-4px) scale(1.03);box-shadow:0 10px 26px rgba(0,0,0,.1);}
.photo-card.selected{border-color:var(--cc,#FF6B6B);transform:translateY(-4px) scale(1.04);}
.photo-card.matched{border-color:#40c057;cursor:default;animation:matchPop .4s cubic-bezier(.34,1.56,.64,1);}
.photo-card.wrong{border-color:#ff4444;animation:cardShake .4s ease;}
/* Reduced photo wrap size */
.photo-wrap{ width:100%; aspect-ratio:3/2; max-height:100px; display:flex; align-items:center; justify-content:center; padding:16px 0; background:#fff; border-radius:12px;}
.photo-wrap img{ width:40%; max-height:200px; object-fit:contain; display:block; }
.photo-card{ position:relative; /* 🔥 para same layout */ }
.photo-card:hover:not(.matched) .photo-wrap img{transform:scale(1.07);}
.photo-check{position:absolute;top:5px;right:5px;width:20px;height:20px;border-radius:50%;background:#40c057;color:#fff;display:flex;align-items:center;justify-content:center;font-size:.6rem;opacity:0;transition:opacity .2s;}
.photo-card.matched .photo-check{opacity:1;}

/* ── ACT 2: POINT IT OUT ── */
.stage-card{background:#fff;border-radius:28px;padding:20px;box-shadow:0 8px 36px rgba(0,0,0,.08);border:2px solid #f0ece4;max-width:500px;margin:0 auto 24px;}
.stage-prompt{font-family:'Fredoka One',cursive;font-size:.9rem;color:#bbb;text-transform:uppercase;letter-spacing:1px;text-align:center;margin-bottom:14px;}
.body-pic-frame{width:100%;max-width:260px;margin:0 auto;position:relative;}
.body-pic-frame img{width:100%;display:block;border-radius:16px;}
.body-highlight{position:absolute;display:flex;flex-direction:column;align-items:center;pointer-events:none;animation:pointerBounce 1s ease-in-out infinite;}
@keyframes pointerBounce{0%,100%{transform:translateY(0)}50%{transform:translateY(-8px)}}
.highlight-arrow{font-size:1.6rem;filter:drop-shadow(0 2px 4px rgba(0,0,0,.3));}
.highlight-dot{width:14px;height:14px;border-radius:50%;background:var(--hc,#FF6B6B);border:3px solid #fff;box-shadow:0 0 0 3px var(--hc,#FF6B6B);animation:dotPulse 1s ease-in-out infinite;}
@keyframes dotPulse{0%,100%{box-shadow:0 0 0 3px var(--hc,#FF6B6B)}50%{box-shadow:0 0 0 8px color-mix(in srgb,var(--hc,#FF6B6B) 30%,transparent)}}
.choices-grid-2{display:grid;grid-template-columns:repeat(2,1fr);gap:12px;max-width:500px;margin:0 auto 20px;}
.choice-btn{background:#fff;border:3px solid #f0f0f0;border-radius:16px;padding:14px 10px;text-align:center;cursor:pointer;font-family:'Fredoka One',cursive;font-size:1.05rem;color:#555;box-shadow:0 4px 14px rgba(0,0,0,.06);transition:all .22s cubic-bezier(.34,1.56,.64,1);animation:cardIn .4s ease both;}
.choice-btn:hover:not(.answered){transform:translateY(-4px) scale(1.04);box-shadow:0 10px 24px rgba(0,0,0,.1);border-color:var(--cc);color:var(--cc);}
.choice-btn.selected{border-color:var(--cc)!important;background:color-mix(in srgb,var(--cc) 12%,white)!important;color:var(--cc)!important;transform:translateY(-4px) scale(1.04);}
.choice-btn.correct{border-color:#40c057!important;background:#d3f9d8!important;color:#2f9e44!important;animation:popGreen .4s cubic-bezier(.34,1.56,.64,1);}
.choice-btn.wrong{border-color:#ff4444!important;background:#ffe3e3!important;color:#ff4444!important;animation:cardShake .4s ease;}
.choice-btn.answered{cursor:default;}
.choice-local{font-size:.65rem;color:#ccc;font-weight:700;display:block;margin-top:2px;}
.choice-btn:nth-child(1){animation-delay:.04s}.choice-btn:nth-child(2){animation-delay:.08s}
.choice-btn:nth-child(3){animation-delay:.12s}.choice-btn:nth-child(4){animation-delay:.16s}

/* ── ACT 3: SORTING ── */
.sort-stage{background:#fff;border-radius:28px;padding:24px;text-align:center;box-shadow:0 8px 36px rgba(0,0,0,.08);border:2px solid #f0ece4;max-width:420px;margin:0 auto 20px;}
.sort-prompt{font-family:'Fredoka One',cursive;font-size:.88rem;color:#bbb;text-transform:uppercase;letter-spacing:1px;margin-bottom:14px;}
.part-photo{width:120px;height:120px;border-radius:18px;overflow:hidden;margin:0 auto 12px;border:3px solid var(--pc,#FF6B6B);box-shadow:0 6px 20px rgba(0,0,0,.1);background:#f4f4f4;animation:picFloat 2.5s ease-in-out infinite;}
.part-photo img{width:100%;height:100%;object-fit:cover;display:block;}
@keyframes picFloat{0%,100%{transform:translateY(0) rotate(-1deg)}50%{transform:translateY(-10px) rotate(1deg)}}
.part-name-big{font-family:'Fredoka One',cursive;font-size:1.8rem;color:var(--pc,#FF6B6B);margin-bottom:3px;}
.part-local{font-size:.75rem;color:#bbb;font-weight:700;}
.buckets{display:grid;grid-template-columns:repeat(4,1fr);gap:10px;max-width:580px;margin:0 auto 20px;}
@media(max-width:460px){.buckets{grid-template-columns:repeat(2,1fr);}}
.bucket{border-radius:18px;padding:14px 8px;text-align:center;cursor:pointer;border:3px solid transparent;background:#fff;box-shadow:0 4px 14px rgba(0,0,0,.06);transition:all .25s cubic-bezier(.34,1.56,.64,1);}
.bucket.head-b{border-color:#FF6B6B44;background:#fff5f5;}
.bucket.torso-b{border-color:#4D96FF44;background:#f0f5ff;}
.bucket.arms-b{border-color:#6BCB7744;background:#f0fdf4;}
.bucket.legs-b{border-color:#F9A82544;background:#fffbf0;}
.bucket:hover:not(.disabled){transform:translateY(-5px) scale(1.05);box-shadow:0 12px 28px rgba(0,0,0,.1);}
.bucket.correct-pick{border-width:4px;animation:bucketPop .4s cubic-bezier(.34,1.56,.64,1);}
.bucket.wrong-pick{animation:bucketShake .4s ease;border-color:#ff4444!important;background:#fff5f5!important;}
.bucket.disabled{pointer-events:none;}
.bucket.bucket-selected{border-width:4px;transform:translateY(-5px) scale(1.05);box-shadow:0 12px 28px rgba(0,0,0,.13);}
@keyframes bucketPop{0%{transform:scale(.95)}60%{transform:scale(1.1)}100%{transform:scale(1)}}
@keyframes bucketShake{0%,100%{transform:translateX(0)}25%{transform:translateX(-7px)}75%{transform:translateX(7px)}}
.bucket-icon{font-size:2rem;margin-bottom:6px;}
.bucket-label{font-family:'Fredoka One',cursive;font-size:.95rem;color:#2d2d2d;margin-bottom:3px;}
.bucket-parts{display:flex;flex-wrap:wrap;justify-content:center;gap:3px;min-height:22px;margin-top:6px;}
.mini-part{font-size:.65rem;font-family:'Fredoka One',cursive;color:#888;background:#f8f8f8;border-radius:var(--pill);padding:2px 7px;animation:miniIn .3s cubic-bezier(.34,1.56,.64,1);}
@keyframes miniIn{from{transform:scale(0)}to{transform:scale(1)}}

/* ── ACT 4: SIMON SAYS ── */
.simon-card{background:var(--green-dark);border-radius:28px;padding:28px 24px;text-align:center;box-shadow:0 8px 36px rgba(0,0,0,.15);max-width:520px;margin:0 auto 24px;position:relative;overflow:hidden;}
.simon-card::before{content:'';position:absolute;inset:0;background:radial-gradient(circle at 30% 30%,rgba(255,255,255,.07),transparent 60%);pointer-events:none;}
.simon-face{font-size:3.5rem;margin-bottom:10px;animation:simonBounce 1.5s ease-in-out infinite;}
@keyframes simonBounce{0%,100%{transform:scale(1) rotate(-3deg)}50%{transform:scale(1.1) rotate(3deg)}}
.simon-says-lbl{font-family:'Fredoka One',cursive;font-size:.85rem;color:rgba(255,255,255,.5);text-transform:uppercase;letter-spacing:2px;margin-bottom:8px;}
.simon-command{font-family:'Fredoka One',cursive;font-size:2.2rem;color:#FFD93D;line-height:1.2;margin-bottom:6px;min-height:56px;transition:all .3s;}
.simon-command.speaking{animation:commandPulse .4s ease-in-out;}
@keyframes commandPulse{0%{transform:scale(.95)}60%{transform:scale(1.05)}100%{transform:scale(1)}}
.simon-local{font-family:'Fredoka One',cursive;font-size:.9rem;color:rgba(255,255,255,.4);}
.simon-speak-btn{background:rgba(255,255,255,.15);border:2px solid rgba(255,255,255,.3);border-radius:var(--pill);color:#fff;font-family:'Fredoka One',cursive;font-size:.82rem;padding:7px 18px;cursor:pointer;margin-top:12px;display:inline-flex;align-items:center;gap:7px;transition:background .2s;}
.simon-speak-btn:hover{background:rgba(255,255,255,.25);}
.parts-label{font-family:'Fredoka One',cursive;font-size:.78rem;color:#bbb;text-transform:uppercase;letter-spacing:1px;text-align:center;margin-bottom:14px;}
.parts-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:10px;max-width:560px;margin:0 auto 20px;}
@media(max-width:400px){.parts-grid{grid-template-columns:repeat(3,1fr);}}
.part-tile{background:#fff;border:3px solid #f0f0f0;border-radius:16px;padding:12px 6px;text-align:center;cursor:pointer;box-shadow:0 4px 12px rgba(0,0,0,.06);transition:all .22s cubic-bezier(.34,1.56,.64,1);animation:tileIn .35s ease both;position:relative;}
.part-tile:hover:not(.answered){transform:translateY(-5px) scale(1.08);box-shadow:0 10px 22px rgba(0,0,0,.1);border-color:var(--tc);}
.part-tile.tile-selected{border-color:var(--tc)!important;background:color-mix(in srgb,var(--tc) 12%,white)!important;transform:translateY(-5px) scale(1.08);box-shadow:0 10px 22px rgba(0,0,0,.1);}
.part-tile.correct{border-color:#40c057!important;background:#d3f9d8!important;animation:tilePop .4s cubic-bezier(.34,1.56,.64,1);}
.part-tile.wrong{border-color:#ff4444!important;background:#ffe3e3!important;animation:tileShake .4s ease;}
.part-tile.answered{cursor:default;}
@keyframes tilePop{0%{transform:scale(.9)}60%{transform:scale(1.15)}100%{transform:scale(1)}}
@keyframes tileShake{0%,100%{transform:translateX(0)}25%{transform:translateX(-5px)}75%{transform:translateX(5px)}}
@keyframes tileIn{from{opacity:0;transform:scale(.7)}to{opacity:1;transform:scale(1)}}
.tile-img{width:52px;height:52px;border-radius:10px;overflow:hidden;background:#f4f4f4;margin:0 auto 6px;}
.tile-img img{width:100%;height:100%;object-fit:cover;display:block;}
.tile-name{font-family:'Fredoka One',cursive;font-size:.75rem;color:var(--tc,#555);}

/* SHARED */
@keyframes matchPop{0%{transform:scale(.9)}60%{transform:scale(1.08)}100%{transform:scale(1)}}
@keyframes cardShake{0%,100%{transform:translateX(0)}25%{transform:translateX(-6px)}75%{transform:translateX(6px)}}
@keyframes popGreen{0%{transform:scale(.95)}60%{transform:scale(1.06)}100%{transform:scale(1)}}
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
.next-quizzes-btn{width:100%;margin-top:8px;background:linear-gradient(135deg,#FF6B6B,#4D96FF);color:#fff;border:none;border-radius:var(--pill);padding:12px 24px;font-family:'Fredoka One',cursive;font-size:.95rem;cursor:pointer;box-shadow:0 6px 18px rgba(0,0,0,.15);transition:transform .2s;display:flex;align-items:center;justify-content:center;gap:8px;}
.next-quizzes-btn:hover{transform:scale(1.04);}
/* Final Exam Button */
.final-exam-btn{width:100%;margin-top:8px;background:linear-gradient(135deg,#F9A825,#FF6B6B);color:#fff;border:none;border-radius:var(--pill);padding:13px 24px;font-family:'Fredoka One',cursive;font-size:.95rem;cursor:pointer;box-shadow:0 6px 20px rgba(249,168,37,.35);transition:transform .2s;display:flex;align-items:center;justify-content:center;gap:8px;}
.final-exam-btn:hover{transform:scale(1.04);}
.cfbit{position:fixed;pointer-events:none;z-index:1000;animation:cfFall linear forwards;}
@keyframes cfFall{0%{transform:translateY(-16px) rotate(0deg);opacity:1}100%{transform:translateY(105vh) rotate(700deg);opacity:0}}
</style>
</head>
<body>
<div class="page-wrap">

<nav class="lesson-nav">
  <a href="activities.php" class="lnav-back"><i class="fas fa-arrow-left"></i> Back</a>
  <div class="lnav-title">🧍 Body Parts Activities</div>
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
      1 => ['icon'=>'fas fa-puzzle-piece', 'en'=>'Match',      'tl'=>'Pagtutugma', 'act'=>$a1],
      2 => ['icon'=>'fas fa-hand-pointer', 'en'=>'Point Out',  'tl'=>'Ituro Mo',   'act'=>$a2],
      3 => ['icon'=>'fas fa-layer-group',  'en'=>'Sorting',    'tl'=>'Pag-aayos',  'act'=>$a3],
      4 => ['icon'=>'fas fa-comment-dots', 'en'=>'Simon Says', 'tl'=>'Simon Says', 'act'=>$a4],
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

  <!-- ══════════════════ PANEL 1: BODY PART MATCH ══════════════════ -->
  <div class="quizzes-panel <?php echo $active_tab===1?'active':'';?>" id="panel1">
    <div class="page-header">
      <div class="quizzes-badge"><i class="fas fa-puzzle-piece"></i> Quizzes 1 of 4</div>
      <div class="page-title" id="p1Title">Body Part Match!</div>
      <div class="page-sub" id="p1Sub">Match each body part name to the correct picture!</div>
      <?php if($a1['score']>0):?><div class="best-score-badge">⭐ Best Score: <?php echo $a1['score'];?>%</div><?php endif;?>
    </div>
    <div class="score-bar">
      <div class="score-item"><div class="score-num" id="p1Score">0</div><div class="score-label">Score</div></div>
      <div class="score-sep"></div>
      <div class="score-item"><div class="score-num" id="p1Round">1</div><div class="score-label" id="p1RoundLbl">Round</div></div>
      <div class="score-sep"></div>
      <div class="progress-wrap">
        <div class="progress-bar-outer"><div class="progress-bar-inner" id="p1Bar" style="width:0%;background:linear-gradient(90deg,#FF6B6B,#4D96FF)"></div></div>
        <div class="progress-label" id="p1BarLbl">0 / 4 matched</div>
      </div>
    </div>
    <div class="round-info" id="p1RoundInfo">Round 1 — Match 4 body parts!</div>
    <div class="match-container">
      <div><div class="match-col-label" id="p1ColName">Body Part Names</div><div class="match-col" id="p1NameCol"></div></div>
      <div><div class="match-col-label" id="p1ColPhoto">Pictures</div><div class="match-col" id="p1PhotoCol"></div></div>
    </div>
    <div class="feedback-msg" id="p1Feedback"></div>
    <div class="controls">
      <button class="btn-action btn-primary" id="p1SubmitBtn" onclick="bm_submitRound()"><i class="fas fa-check"></i> <span id="p1SubmitLbl">Submit Round</span></button>
      <button class="btn-action btn-secondary" id="p1NextBtn" onclick="bm_proceedNext()" style="display:none"><i class="fas fa-arrow-right"></i> <span id="p1NextLbl">Next Round</span></button>
    </div>
  </div>

  <!-- ══════════════════ PANEL 2: POINT IT OUT ══════════════════ -->
  <div class="quizzes-panel <?php echo $active_tab===2?'active':'';?>" id="panel2">
    <div class="page-header">
      <div class="quizzes-badge"><i class="fas fa-hand-pointer"></i> Quizzes 2 of 4</div>
      <div class="page-title" id="p2Title">Point It Out!</div>
      <div class="page-sub" id="p2Sub">Look at the highlighted body part and pick its name!</div>
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
        <div class="progress-bar-outer"><div class="progress-bar-inner" id="p2Bar" style="width:0%;background:linear-gradient(90deg,#845EC2,#4D96FF)"></div></div>
        <div class="progress-label" id="p2BarLbl">0 / 10</div>
      </div>
    </div>
    <div class="stage-card">
      <div class="stage-prompt" id="p2Prompt">What body part is this? 👇</div>
      <div class="body-pic-frame">
        <img id="p2BodyImg" src="" alt="body part" onerror="this.style.opacity='.3'">
        <div class="body-highlight" id="p2Highlight" style="top:10%;left:50%;transform:translateX(-50%)">
          <div class="highlight-arrow">👇</div>
          <div class="highlight-dot" id="p2HighlightDot" style="--hc:#FF6B6B"></div>
        </div>
      </div>
    </div>
    <div class="choices-grid-2" id="p2Choices"></div>
    <div class="feedback-msg" id="p2Feedback"></div>
    <div class="controls" id="p2Controls">
      <button class="btn-action btn-primary" id="p2SubmitBtn" onclick="po_submitAnswer()" style="display:none"><i class="fas fa-check"></i> <span id="p2SubmitLbl">Submit</span></button>
      <button class="btn-action btn-secondary" id="p2NextBtn" onclick="po_nextQuestion()" style="display:none"><i class="fas fa-arrow-right"></i> <span id="p2NextLbl">Next</span></button>
    </div>
  </div>

  <!-- ══════════════════ PANEL 3: BODY PART SORTING ══════════════════ -->
  <div class="quizzes-panel <?php echo $active_tab===3?'active':'';?>" id="panel3">
    <div class="page-header">
      <div class="quizzes-badge"><i class="fas fa-layer-group"></i> Quizzes 3 of 4</div>
      <div class="page-title" id="p3Title">Body Part Sorting!</div>
      <div class="page-sub" id="p3Sub">Which part of the body does it belong to?</div>
      <?php if($a3['score']>0):?><div class="best-score-badge">⭐ Best Score: <?php echo $a3['score'];?>%</div><?php endif;?>
    </div>
    <div class="score-bar">
      <div class="score-item"><div class="score-num" id="p3Score">0</div><div class="score-label">Score</div></div>
      <div class="score-sep"></div>
      <div class="score-item"><div class="score-num" id="p3QNum">1</div><div class="score-label" id="p3QLbl">Part</div></div>
      <div class="score-sep"></div>
      <div class="progress-wrap">
        <div class="progress-bar-outer"><div class="progress-bar-inner" id="p3Bar" style="width:0%;background:linear-gradient(90deg,#F9A825,#FF6B6B)"></div></div>
        <div class="progress-label" id="p3BarLbl">0 / 10</div>
      </div>
    </div>
    <div class="sort-stage">
      <div class="sort-prompt" id="p3Prompt">Where does this belong?</div>
      <div class="part-photo" id="p3PartPhoto"><img id="p3PartImg" src="" alt=""></div>
      <div class="part-name-big" id="p3PartName">Head</div>
      <div class="part-local" id="p3PartLocal">Ulo</div>
    </div>
    <div class="buckets">
      <div class="bucket head-b" id="p3BHead" onclick="bs_pickBucket('head')"><div class="bucket-icon">🙂</div><div class="bucket-label" id="p3LHead">Head</div><div class="bucket-parts" id="p3SortedHead"></div></div>
      <div class="bucket torso-b" id="p3BTorso" onclick="bs_pickBucket('torso')"><div class="bucket-icon">👕</div><div class="bucket-label" id="p3LTorso">Torso</div><div class="bucket-parts" id="p3SortedTorso"></div></div>
      <div class="bucket arms-b" id="p3BArms" onclick="bs_pickBucket('arms')"><div class="bucket-icon">💪</div><div class="bucket-label" id="p3LArms">Arms</div><div class="bucket-parts" id="p3SortedArms"></div></div>
      <div class="bucket legs-b" id="p3BLegs" onclick="bs_pickBucket('legs')"><div class="bucket-icon">🦵</div><div class="bucket-label" id="p3LLegs">Legs</div><div class="bucket-parts" id="p3SortedLegs"></div></div>
    </div>
    <div class="feedback-msg" id="p3Feedback"></div>
    <div class="controls" id="p3Controls">
      <button class="btn-action btn-primary" id="p3SubmitBtn" onclick="bs_submitAnswer()" style="display:none"><i class="fas fa-check"></i> <span id="p3SubmitLbl">Submit</span></button>
      <button class="btn-action btn-secondary" id="p3NextBtn" onclick="bs_nextPart()" style="display:none"><i class="fas fa-arrow-right"></i> <span id="p3NextLbl">Next</span></button>
    </div>
  </div>

  <!-- ══════════════════ PANEL 4: SIMON SAYS ══════════════════ -->
  <div class="quizzes-panel <?php echo $active_tab===4?'active':'';?>" id="panel4">
    <div class="page-header">
      <div class="quizzes-badge"><i class="fas fa-comment-dots"></i> Quizzes 4 of 4</div>
      <div class="page-title" id="p4Title">Simon Says!</div>
      <div class="page-sub" id="p4Sub">Simon says — tap the correct body part!</div>
      <?php if($a4['score']>0):?><div class="best-score-badge">⭐ Best Score: <?php echo $a4['score'];?>%</div><?php endif;?>
    </div>
    <div class="score-bar">
      <div class="score-item"><div class="score-num" id="p4Score">0</div><div class="score-label">Score</div></div>
      <div class="score-sep"></div>
      <div class="score-item"><div class="score-num" id="p4QNum">1</div><div class="score-label">Round</div></div>
      <div class="score-sep"></div>
      <div class="score-item"><div class="lives-wrap" id="p4Lives"><span class="life-icon">❤️</span><span class="life-icon">❤️</span><span class="life-icon">❤️</span></div><div class="score-label">Lives</div></div>
      <div class="score-sep"></div>
      <div class="progress-wrap">
        <div class="progress-bar-outer"><div class="progress-bar-inner" id="p4Bar" style="width:0%;background:linear-gradient(90deg,#00C9A7,#4D96FF)"></div></div>
        <div class="progress-label" id="p4BarLbl">0 / 10</div>
      </div>
    </div>
    <div class="simon-card">
      <div class="simon-face">🤖</div>
      <div class="simon-says-lbl" id="p4SimonSays">Simon Says...</div>
      <div class="simon-command" id="p4Command">Touch your NOSE!</div>
      <div class="simon-local" id="p4CommandLocal"></div>
      <button class="simon-speak-btn" onclick="ss_sayCommand()"><i class="fas fa-volume-up"></i> <span id="p4HearBtn">Hear it again!</span></button>
    </div>
    <div class="parts-label" id="p4PartsLabel">Tap the correct body part:</div>
    <div class="parts-grid" id="p4PartsGrid"></div>
    <div class="feedback-msg" id="p4Feedback"></div>
    <div class="controls" id="p4Controls">
      <button class="btn-action btn-primary" id="p4SubmitBtn" onclick="ss_submitAnswer()" style="display:none"><i class="fas fa-check"></i> <span id="p4SubmitLbl">Submit</span></button>
      <button class="btn-action btn-secondary" id="p4NextBtn" onclick="ss_nextRound()" style="display:none"><i class="fas fa-arrow-right"></i> <span id="p4NextLbl">Next</span></button>
    </div>
    <!-- No next button — auto advances -->
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

// ── SHARED PARTS DATA ──
const PARTS_MATCH=[
  {en:'Head',tl:'Ulo',color:'#FF6B6B',img:'pictures/humanbody/head.jpg'},
  {en:'Eyebrows',tl:'Kilay',color:'#FF8E53',img:'pictures/humanbody/eyebrows.jpg'},
  {en:'Eyes',tl:'Mata',color:'#FFA500',img:'pictures/humanbody/eyes.jpg'},
  {en:'Ears',tl:'Tainga',color:'#FFD93D',img:'pictures/humanbody/ears.jpg'},
  {en:'Nose',tl:'Ilong',color:'#6BCB77',img:'pictures/humanbody/nose.jpg'},
  {en:'Mouth',tl:'Bibig',color:'#4D96FF',img:'pictures/humanbody/mouth.jpg'},
  {en:'Tongue',tl:'Dila',color:'#845EC2',img:'pictures/humanbody/tongue.jpg'},
  {en:'Neck',tl:'Leeg',color:'#FF6F91',img:'pictures/humanbody/neck.jpg'},
  {en:'Shoulders',tl:'Balikat',color:'#F9A825',img:'pictures/humanbody/shoulders.jpg'},
  {en:'Arms',tl:'Braso',color:'#00C9A7',img:'pictures/humanbody/arms.jpg'},
  {en:'Elbows',tl:'Siko',color:'#C34A36',img:'pictures/humanbody/elbows.png'},
  {en:'Hands',tl:'Kamay',color:'#FF6B6B',img:'pictures/humanbody/hands.jpg'},
  {en:'Fingers',tl:'Daliri',color:'#845EC2',img:'pictures/humanbody/fingers.jpg'},
  {en:'Stomach',tl:'Tiyan',color:'#4D96FF',img:'pictures/humanbody/stomachs.png'},
  {en:'Legs',tl:'Binti',color:'#6BCB77',img:'pictures/humanbody/legs.png'},
  {en:'Knees',tl:'Tuhod',color:'#FF8E53',img:'pictures/humanbody/knees.jpg'},
  {en:'Feet',tl:'Paa',color:'#FFD93D',img:'pictures/humanbody/feet.jpg'},
];
// Point out & Simon (no elbows/fingers)
const PARTS_PO=[
  {en:'Head',tl:'Ulo',color:'#FF6B6B',img:'pictures/humanbody/head.jpg'},
  {en:'Eyebrows',tl:'Kilay',color:'#FF8E53',img:'pictures/humanbody/eyebrows.jpg'},
  {en:'Eyes',tl:'Mata',color:'#FFA500',img:'pictures/humanbody/eyes.jpg'},
  {en:'Ears',tl:'Tainga',color:'#FFD93D',img:'pictures/humanbody/ears.jpg'},
  {en:'Nose',tl:'Ilong',color:'#6BCB77',img:'pictures/humanbody/nose.jpg'},
  {en:'Mouth',tl:'Bibig',color:'#4D96FF',img:'pictures/humanbody/mouth.jpg'},
  {en:'Tongue',tl:'Dila',color:'#845EC2',img:'pictures/humanbody/tongue.jpg'},
  {en:'Neck',tl:'Leeg',color:'#FF6F91',img:'pictures/humanbody/neck.jpg'},
  {en:'Shoulders',tl:'Balikat',color:'#F9A825',img:'pictures/humanbody/shoulders.jpg'},
  {en:'Arms',tl:'Braso',color:'#00C9A7',img:'pictures/humanbody/arms.jpg'},
  {en:'Hands',tl:'Kamay',color:'#FF6B6B',img:'pictures/humanbody/hands.jpg'},
  {en:'Stomach',tl:'Tiyan',color:'#4D96FF',img:'pictures/humanbody/stomachs.png'},
  {en:'Legs',tl:'Binti',color:'#6BCB77',img:'pictures/humanbody/legs.png'},
  {en:'Feet',tl:'Paa',color:'#FFD93D',img:'pictures/humanbody/feet.jpg'},
];
// Sorting (with zone)
const PARTS_SORT=[
  {en:'Head',tl:'Ulo',color:'#FF6B6B',img:'pictures/humanbody/head.jpg',zone:'head'},
  {en:'Eyebrows',tl:'Kilay',color:'#FF8E53',img:'pictures/humanbody/eyebrows.jpg',zone:'head'},
  {en:'Eyes',tl:'Mata',color:'#FFA500',img:'pictures/humanbody/eyes.jpg',zone:'head'},
  {en:'Ears',tl:'Tainga',color:'#FFD93D',img:'pictures/humanbody/ears.jpg',zone:'head'},
  {en:'Nose',tl:'Ilong',color:'#6BCB77',img:'pictures/humanbody/nose.jpg',zone:'head'},
  {en:'Mouth',tl:'Bibig',color:'#4D96FF',img:'pictures/humanbody/mouth.jpg',zone:'head'},
  {en:'Tongue',tl:'Dila',color:'#845EC2',img:'pictures/humanbody/tongue.jpg',zone:'head'},
  {en:'Neck',tl:'Leeg',color:'#FF6F91',img:'pictures/humanbody/neck.jpg',zone:'torso'},
  {en:'Shoulders',tl:'Balikat',color:'#F9A825',img:'pictures/humanbody/shoulders.jpg',zone:'torso'},
  {en:'Stomach',tl:'Tiyan',color:'#4D96FF',img:'pictures/humanbody/stomachs.png',zone:'torso'},
  {en:'Arms',tl:'Braso',color:'#00C9A7',img:'pictures/humanbody/arms.jpg',zone:'arms'},
  {en:'Elbows',tl:'Siko',color:'#C34A36',img:'pictures/humanbody/elbows.png',zone:'arms'},
  {en:'Hands',tl:'Kamay',color:'#FF6B6B',img:'pictures/humanbody/hands.jpg',zone:'arms'},
  {en:'Fingers',tl:'Daliri',color:'#845EC2',img:'pictures/humanbody/fingers.jpg',zone:'arms'},
  {en:'Legs',tl:'Binti',color:'#6BCB77',img:'pictures/humanbody/legs.png',zone:'legs'},
  {en:'Knees',tl:'Tuhod',color:'#FF8E53',img:'pictures/humanbody/knees.jpg',zone:'legs'},
  {en:'Feet',tl:'Paa',color:'#FFD93D',img:'pictures/humanbody/feet.jpg',zone:'legs'},
];
const ZONE_COLORS={head:'#FF6B6B',torso:'#4D96FF',arms:'#6BCB77',legs:'#F9A825'};

function n(p){return lang==='en'?p.en:p.tl;}

// ── DB SAVE ──
async function saveStart(id){try{await fetch(CURRENT_URL,{method:'POST',headers:{'Content-Type':'application/x-www-form-urlencoded'},body:`action=start&quizzes_id=${id}`});}catch(e){}}
function saveProgress(id,score,checkpoint=0){
  if(score<=0)return;
  const body=`action=progress&quizzes_id=${id}&score=${score}&checkpoint=${checkpoint}`;
  if(navigator.sendBeacon){
    const fd=new FormData();
    fd.append('action','progress');fd.append('quizzes_id',id);
    fd.append('score',score);fd.append('checkpoint',checkpoint);
    navigator.sendBeacon(CURRENT_URL,fd);
  } else {
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
  if(activeTab===1)bm_startGame();
  if(activeTab===2)po_startGame();
  if(activeTab===3)bs_startGame();
  if(activeTab===4)ss_startGame();
}

// ── TABS ──
function switchTab(num){
  activeTab=num;
  document.querySelectorAll('.act-tab').forEach((t,i)=>t.classList.toggle('active',i+1===num));
  document.querySelectorAll('.quizzes-panel').forEach((p,i)=>p.classList.toggle('active',i+1===num));
  if(num===1)bm_startGame();
  if(num===2)po_startGame();
  if(num===3)bs_startGame();
  if(num===4)ss_startGame();
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
  confetti('#FF6B6B');confetti('#4D96FF');
  saveProgress(ACT_IDS[actNum],finalScore);
  saveComplete(ACT_IDS[actNum],finalScore);
  PREV_SCORES[actNum]=Math.max(PREV_SCORES[actNum],finalScore);
}
function closeResult(){document.getElementById('resultOverlay').classList.remove('active');}
function playAgain(){
  closeResult();
  if(_curActNum===1)bm_startGame();
  if(_curActNum===2)po_startGame();
  if(_curActNum===3)bs_startGame();
  if(_curActNum===4)ss_startGame();
}
function goNextQuizzes(){closeResult();switchTab(_curActNum+1);}

// ── UTILS ──
function shuffle(a){const r=[...a];for(let i=r.length-1;i>0;i--){const j=0|Math.random()*(i+1);[r[i],r[j]]=[r[j],r[i]];}return r;}
function speak(w){if(!window.speechSynthesis)return;window.speechSynthesis.cancel();const u=new SpeechSynthesisUtterance(w);u.lang=lang==='tl'?'fil-PH':'en-US';u.rate=0.8;u.pitch=1.2;window.speechSynthesis.speak(u);}
function confetti(color){const cols=[color,'#FFE66D','#FF6B6B','#A29BFE','#4ECDC4','#FD79A8'];for(let i=0;i<30;i++){const p=document.createElement('div');p.className='cfbit';p.style.cssText=`left:${Math.random()*100}vw;top:-12px;background:${cols[0|Math.random()*cols.length]};border-radius:${Math.random()>.5?'50%':'3px'};width:${6+Math.random()*8}px;height:${6+Math.random()*8}px;animation-duration:${1.2+Math.random()*1.5}s;animation-delay:${Math.random()*.4}s;`;document.body.appendChild(p);p.addEventListener('animationend',()=>p.remove());}}

// ════════════════════════════════════════
// quizzes 1: BODY PART MATCH
// ════════════════════════════════════════
const BM_COPY={
  en:{title:'Body Part Match!',sub:'Match each body part name to the correct picture!',colName:'Body Part Names',colPhoto:'Pictures',roundInfo:r=>`Round ${r} of 5 — Match 4 body parts!`,progressLbl:(m,t)=>`${m} / ${t} matched`,submitLbl:'Submit Round',nextLbl:'Next Round',finishLbl:'See Results',feedbackCorrect:(n,pts)=>`✅ Perfect! +${pts} pts earned this round!`,feedbackPartial:(c,gained)=>`⭐ ${c}/4 correct! +${gained} pts earned.`,resultTitle:'Body Expert!',resultSub:'You matched all the body parts!'},
  tl:{title:'Pagtutugma ng Katawan!',sub:'Itugma ang bawat pangalan ng katawan sa tamang larawan!',colName:'Mga Pangalan',colPhoto:'Mga Larawan',roundInfo:r=>`Round ${r} ng 5 — Itugma ang 4 na bahagi!`,progressLbl:(m,t)=>`${m} / ${t} natugma`,submitLbl:'I-submit ang Round',nextLbl:'Susunod na Round',finishLbl:'Tingnan ang Resulta',feedbackCorrect:(n,pts)=>`✅ Perpekto! +${pts} pts nakuha sa round na ito!`,feedbackPartial:(c,gained)=>`⭐ ${c}/4 tama! +${gained} pts nakuha.`,resultTitle:'Eksperto sa Katawan!',resultSub:'Natugma mo ang lahat ng bahagi ng katawan!'}
};
const BM_TOTAL_ROUNDS=5, BM_PPM=5, BM_MAX=BM_TOTAL_ROUNDS*4*BM_PPM; // 5×4×5 = 100
let bm_score=0,bm_round=0,bm_pool=[],bm_roundParts=[],bm_selName=null,bm_selPhoto=null,bm_pairs={},bm_submitted=false;

function bm_startGame(){
  bm_score=0;bm_round=0;bm_pool=shuffle([...PARTS_MATCH]);
  document.getElementById('p1Score').textContent='0';
  document.getElementById('resultOverlay').classList.remove('active');
  const c=BM_COPY[lang];
  document.getElementById('p1Title').textContent=c.title;
  document.getElementById('p1Sub').textContent=c.sub;
  document.getElementById('p1ColName').textContent=c.colName;
  document.getElementById('p1ColPhoto').textContent=c.colPhoto;
  document.getElementById('p1SubmitLbl').textContent=c.submitLbl;
  document.getElementById('p1NextLbl').textContent=c.nextLbl;
  saveStart(ACT_IDS[1]);
  bm_nextRound();
}
function bm_nextRound(){
  bm_round++;bm_selName=null;bm_selPhoto=null;bm_pairs={};bm_submitted=false;
  if(bm_pool.length<4)bm_pool=shuffle([...PARTS_MATCH]);
  bm_roundParts=bm_pool.splice(0,4);
  const c=BM_COPY[lang];
  document.getElementById('p1Round').textContent=bm_round;
  document.getElementById('p1RoundInfo').textContent=c.roundInfo(bm_round);
  document.getElementById('p1Bar').style.width='0%';
  document.getElementById('p1BarLbl').textContent=c.progressLbl(0,4);
  document.getElementById('p1Feedback').textContent='';
  document.getElementById('p1Feedback').className='feedback-msg';
  document.getElementById('p1SubmitBtn').style.display='';
  document.getElementById('p1NextBtn').style.display='none';
  document.getElementById('p1NextLbl').textContent=bm_round>=BM_TOTAL_ROUNDS?BM_COPY[lang].finishLbl:BM_COPY[lang].nextLbl;
  bm_renderCards();
}
function bm_renderCards(){
  const ns=shuffle([...bm_roundParts]),ps=shuffle([...bm_roundParts]);
  const nc=document.getElementById('p1NameCol');nc.innerHTML='';
  const pc=document.getElementById('p1PhotoCol');pc.innerHTML='';
  ns.forEach(p=>{
    const card=document.createElement('div');card.className='name-card';card.dataset.key=p.en;card.style.setProperty('--cc',p.color);
    card.innerHTML=`<div class="name-text" style="color:${p.color}">${n(p)}</div><div class="name-local">${lang==='en'?p.tl:p.en}</div><i class="fas fa-check match-check"></i>`;
    card.onclick=()=>bm_selectName(card,p);nc.appendChild(card);
  });
  ps.forEach(p=>{
    const card=document.createElement('div');card.className='photo-card';card.dataset.key=p.en;card.style.setProperty('--cc',p.color);
    card.innerHTML=`<div class="photo-wrap"><img src="${p.img}" alt="${p.en}" onerror="this.style.display='none'"></div><div class="photo-check"><i class="fas fa-check"></i></div>`;
    card.onclick=()=>bm_selectPhoto(card,p);pc.appendChild(card);
  });
}
function bm_selectName(card,part){
  if(bm_submitted)return;
  if(card.classList.contains('matched'))return;
  document.querySelectorAll('#p1NameCol .name-card.selected').forEach(c=>c.classList.remove('selected'));
  bm_selName={card,part};card.classList.add('selected');
  if(bm_selName&&bm_selPhoto)bm_pairUp();
}
function bm_selectPhoto(card,part){
  if(bm_submitted)return;
  if(card.classList.contains('matched'))return;
  document.querySelectorAll('#p1PhotoCol .photo-card.selected').forEach(c=>c.classList.remove('selected'));
  bm_selPhoto={card,part};card.classList.add('selected');
  if(bm_selName&&bm_selPhoto)bm_pairUp();
}
function bm_pairUp(){
  const nm=bm_selName,ph=bm_selPhoto;bm_selName=null;bm_selPhoto=null;
  // Unlink previous photo for this name
  if(bm_pairs[nm.part.en]){
    const old=bm_pairs[nm.part.en].photoCard;
    old.classList.remove('matched');
    old.querySelector('.photo-check').style.opacity='0';
  }
  // Unlink if photo already linked to another name
  const existingName=Object.keys(bm_pairs).find(k=>bm_pairs[k].photoKey===ph.part.en);
  if(existingName){
    const oldNm=bm_pairs[existingName].nameCard;
    oldNm.classList.remove('matched');
    oldNm.querySelector('.match-check').style.opacity='0';
    delete bm_pairs[existingName];
  }
  bm_pairs[nm.part.en]={nameCard:nm.card,photoCard:ph.card,photoKey:ph.part.en};
  nm.card.classList.remove('selected');ph.card.classList.remove('selected');
  nm.card.classList.add('matched');ph.card.classList.add('matched');
  const paired=Object.keys(bm_pairs).length;
  const c=BM_COPY[lang];
  document.getElementById('p1Bar').style.width=((paired/4)*100)+'%';
  document.getElementById('p1BarLbl').textContent=c.progressLbl(paired,4);
}
function bm_submitRound(){
  if(Object.keys(bm_pairs).length<4){
    document.getElementById('p1Feedback').textContent='⚠️ Match all 4 pairs first before submitting!';
    document.getElementById('p1Feedback').className='feedback-msg wrong';
    return;
  }
  bm_submitted=true;
  document.getElementById('p1SubmitBtn').style.display='none';
  let correctCount=0;
  bm_roundParts.forEach(p=>{
    const pair=bm_pairs[p.en];
    if(!pair)return;
    const isCorrect=pair.photoKey===p.en;
    if(isCorrect){
      correctCount++;
      pair.nameCard.style.borderColor='#40c057';
      pair.nameCard.style.background='#f0fdf4';
      pair.photoCard.style.borderColor='#40c057';
    } else {
      pair.nameCard.classList.remove('matched');
      pair.photoCard.classList.remove('matched');
      pair.nameCard.classList.add('wrong');
      pair.photoCard.classList.add('wrong');
      pair.nameCard.style.borderColor='#ff4444';
      pair.nameCard.style.background='#fff5f5';
      pair.photoCard.style.borderColor='#ff4444';
    }
  });
  // +5 per correct only, no deduction
  const roundGain=correctCount*BM_PPM;
  bm_score=bm_score+roundGain;
  document.getElementById('p1Score').textContent=bm_score;
  saveProgress(ACT_IDS[1],Math.min(100,Math.round((bm_score/BM_MAX)*100)));
  const c=BM_COPY[lang];
  if(correctCount===4){
    document.getElementById('p1Feedback').textContent=c.feedbackCorrect(4,roundGain);
    document.getElementById('p1Feedback').className='feedback-msg correct';
    confetti('#40c057');
  } else {
    document.getElementById('p1Feedback').textContent=c.feedbackPartial(correctCount,roundGain);
    document.getElementById('p1Feedback').className=roundGain>0?'feedback-msg correct':'feedback-msg wrong';
  }
  document.getElementById('p1NextBtn').style.display='';
  const isLast=bm_round>=BM_TOTAL_ROUNDS;
  document.getElementById('p1NextLbl').textContent=isLast?BM_COPY[lang].finishLbl:BM_COPY[lang].nextLbl;
}
function bm_proceedNext(){
  if(bm_round>=BM_TOTAL_ROUNDS)bm_showResult();
  else bm_nextRound();
}
function bm_showResult(){
  document.getElementById('resultTitle').textContent=BM_COPY[lang].resultTitle;
  document.getElementById('resultSub').textContent=BM_COPY[lang].resultSub;
  showResult(1,bm_score,BM_MAX);
}

// ════════════════════════════════════════
//  2: POINT IT OUT  — AUTO NEXT
// ════════════════════════════════════════
const PO_COPY={en:{title:'Point It Out!',sub:'Look at the highlighted body part and pick its name!',prompt:'What body part is this? 👇',feedbackCorrect:nm=>`✅ Yes! That's the ${nm}!`,feedbackWrong:nm=>`❌ That's the ${nm}!`,submitLbl:'Submit',nextLbl:'Next',resultTitle:'Brilliant!',resultSub:'You know your body parts!'},tl:{title:'Ituro Mo!',sub:'Tingnan ang naka-highlight na bahagi at piliin ang pangalan nito!',prompt:'Anong bahagi ng katawan ito? 👇',feedbackCorrect:nm=>`✅ Tama! Iyan ang ${nm}!`,feedbackWrong:nm=>`❌ Iyan ang ${nm}!`,submitLbl:'I-submit',nextLbl:'Susunod',resultTitle:'Napakahusay!',resultSub:'Alam mo ang iyong mga bahagi ng katawan!'}};
const PO_TOTAL=10,PO_MAX=PO_TOTAL*10;
let po_pool=[],po_current=null,po_answered=false,po_score=0,po_qIdx=0,po_lives=3;
let po_selectedBtn=null,po_selectedPart=null,po_selectedIsCorrect=false;

function po_startGame(){
  po_score=0;po_qIdx=0;po_lives=3;po_answered=false;po_pool=shuffle([...PARTS_PO]);
  po_selectedBtn=null;po_selectedPart=null;
  document.getElementById('p2Score').textContent='0';
  document.getElementById('resultOverlay').classList.remove('active');
  po_updateLives();
  const c=PO_COPY[lang];
  document.getElementById('p2Title').textContent=c.title;
  document.getElementById('p2Sub').textContent=c.sub;
  document.getElementById('p2SubmitLbl').textContent=c.submitLbl;
  document.getElementById('p2NextLbl').textContent=c.nextLbl;
  saveStart(ACT_IDS[2]);
  po_loadQuestion();
}
function po_updateLives(){document.querySelectorAll('#p2Lives .life-icon').forEach((ic,i)=>ic.classList.toggle('lost',i>=po_lives));}
function po_loadQuestion(){
  po_answered=false;po_selectedBtn=null;po_selectedPart=null;po_selectedIsCorrect=false;
  document.getElementById('p2Feedback').textContent='';document.getElementById('p2Feedback').className='feedback-msg';
  document.getElementById('p2SubmitBtn').style.display='none';
  document.getElementById('p2NextBtn').style.display='none';
  if(!po_pool.length)po_pool=shuffle([...PARTS_PO]);
  po_current=po_pool.shift();
  document.getElementById('p2QNum').textContent=po_qIdx+1;
  document.getElementById('p2Bar').style.width=((po_qIdx/PO_TOTAL)*100)+'%';
  document.getElementById('p2BarLbl').textContent=`${po_qIdx} / ${PO_TOTAL}`;
  document.getElementById('p2Prompt').textContent=PO_COPY[lang].prompt;
  document.getElementById('p2BodyImg').src=po_current.img;
  document.getElementById('p2HighlightDot').style.setProperty('--hc',po_current.color);
  const wrong=shuffle(PARTS_PO.filter(p=>p.en!==po_current.en)).slice(0,3);
  const choices=shuffle([po_current,...wrong]);
  const grid=document.getElementById('p2Choices');grid.innerHTML='';
  choices.forEach((p,i)=>{
    const isCorrect=p.en===po_current.en;
    const btn=document.createElement('button');btn.className='choice-btn';btn.style.setProperty('--cc',p.color);
    btn.innerHTML=`${n(p)}<span class="choice-local">${lang==='en'?p.tl:p.en}</span>`;
    btn.onclick=()=>po_selectAnswer(btn,p,isCorrect);
    grid.appendChild(btn);
  });
  setTimeout(()=>speak(n(po_current)),300);
}
function po_selectAnswer(btn,part,isCorrect){
  if(po_answered)return;
  // Deselect previous
  document.querySelectorAll('#p2Choices .choice-btn').forEach(b=>b.classList.remove('selected'));
  btn.classList.add('selected');
  po_selectedBtn=btn;po_selectedPart=part;po_selectedIsCorrect=isCorrect;
  document.getElementById('p2SubmitBtn').style.display='';
}
function po_submitAnswer(){
  if(!po_selectedBtn||po_answered)return;
  po_answered=true;
  document.getElementById('p2SubmitBtn').style.display='none';
  // Disable all choices
  document.querySelectorAll('#p2Choices .choice-btn').forEach(b=>b.classList.add('answered'));
  const c=PO_COPY[lang];
  if(po_selectedIsCorrect){
    po_selectedBtn.classList.add('correct');po_selectedBtn.classList.remove('selected');
    po_score+=10;saveProgress(ACT_IDS[2],Math.round((po_score/PO_MAX)*100));
    document.getElementById('p2Score').textContent=po_score;
    document.getElementById('p2Feedback').textContent=c.feedbackCorrect(n(po_current));
    document.getElementById('p2Feedback').className='feedback-msg correct';
    confetti(po_current.color);
  } else {
    po_selectedBtn.classList.add('wrong');po_selectedBtn.classList.remove('selected');
    document.querySelectorAll('#p2Choices .choice-btn').forEach(b=>{if(b.textContent.trim().startsWith(n(po_current)))b.classList.add('correct');});
    po_lives--;po_updateLives();
    document.getElementById('p2Feedback').textContent=c.feedbackWrong(n(po_current));
    document.getElementById('p2Feedback').className='feedback-msg wrong';
  }
  po_qIdx++;
  document.getElementById('p2NextBtn').style.display='';
}
function po_nextQuestion(){
  if(po_qIdx>=PO_TOTAL||po_lives<=0){po_showResult();}
  else{po_loadQuestion();}
}
function po_showResult(){
  document.getElementById('resultTitle').textContent=PO_COPY[lang].resultTitle;
  document.getElementById('resultSub').textContent=PO_COPY[lang].resultSub;
  showResult(2,po_score,PO_MAX);
}

// ════════════════════════════════════════
// quizzes 3: BODY PART SORTING
// (Sorting is already auto-next via bs_loadPart timeout — no change needed)
// ════════════════════════════════════════
const BS_COPY={en:{title:'Body Part Sorting!',sub:'Which part of the body does it belong to?',prompt:'Where does this belong?',lHead:'Head',lTorso:'Torso',lArms:'Arms',lLegs:'Legs',feedbackCorrect:(nm,z)=>`✅ Right! ${nm} is on the ${z}!`,feedbackWrong:(nm,z)=>`❌ ${nm} belongs to the ${z}!`,submitLbl:'Submit',nextLbl:'Next',resultTitle:'Sorted!',resultSub:'You sorted all the body parts!'},tl:{title:'Pag-aayos ng Katawan!',sub:'Saan kabilang ang bahaging ito ng katawan?',prompt:'Saan ito kabilang?',lHead:'Ulo',lTorso:'Katawan',lArms:'Braso',lLegs:'Binti',feedbackCorrect:(nm,z)=>`✅ Tama! Ang ${nm} ay nasa ${z}!`,feedbackWrong:(nm,z)=>`❌ Ang ${nm} ay nasa ${z}!`,submitLbl:'I-submit',nextLbl:'Susunod',resultTitle:'Naayos Na!',resultSub:'Nayos mo ang lahat ng bahagi ng katawan!'}};
const BS_TOTAL=10,BS_MAX=BS_TOTAL*10;
let bs_pool=[],bs_current=null,bs_answered=false,bs_score=0,bs_qIdx=0;
let bs_selectedZone=null;
function bs_zoneLabel(z){const m={head:lang==='en'?'Head':'Ulo',torso:lang==='en'?'Torso':'Katawan',arms:lang==='en'?'Arms':'Braso',legs:lang==='en'?'Legs':'Binti'};return m[z]||z;}

function bs_startGame(){
  bs_score=0;bs_qIdx=0;bs_answered=false;bs_pool=shuffle([...PARTS_SORT]);
  bs_selectedZone=null;
  document.getElementById('p3Score').textContent='0';
  document.getElementById('resultOverlay').classList.remove('active');
  ['p3SortedHead','p3SortedTorso','p3SortedArms','p3SortedLegs'].forEach(id=>document.getElementById(id).innerHTML='');
  const c=BS_COPY[lang];
  document.getElementById('p3Title').textContent=c.title;
  document.getElementById('p3Sub').textContent=c.sub;
  document.getElementById('p3LHead').textContent=c.lHead;
  document.getElementById('p3LTorso').textContent=c.lTorso;
  document.getElementById('p3LArms').textContent=c.lArms;
  document.getElementById('p3LLegs').textContent=c.lLegs;
  document.getElementById('p3SubmitLbl').textContent=c.submitLbl;
  document.getElementById('p3NextLbl').textContent=c.nextLbl;
  saveStart(ACT_IDS[3]);
  bs_loadPart();
}
function bs_loadPart(){
  bs_answered=false;bs_selectedZone=null;
  document.getElementById('p3Feedback').textContent='';document.getElementById('p3Feedback').className='feedback-msg';
  document.getElementById('p3SubmitBtn').style.display='none';
  document.getElementById('p3NextBtn').style.display='none';
  ['p3BHead','p3BTorso','p3BArms','p3BLegs'].forEach(id=>document.getElementById(id).classList.remove('correct-pick','wrong-pick','disabled','bucket-selected'));
  if(!bs_pool.length){bs_showResult();return;}
  bs_current=bs_pool.shift();
  document.getElementById('p3QNum').textContent=bs_qIdx+1;
  document.getElementById('p3Bar').style.width=((bs_qIdx/BS_TOTAL)*100)+'%';
  document.getElementById('p3BarLbl').textContent=`${bs_qIdx} / ${BS_TOTAL}`;
  document.getElementById('p3Prompt').textContent=BS_COPY[lang].prompt;
  document.getElementById('p3PartPhoto').style.setProperty('--pc',bs_current.color);
  document.getElementById('p3PartPhoto').style.borderColor=bs_current.color;
  document.getElementById('p3PartImg').src=bs_current.img;
  document.getElementById('p3PartName').textContent=n(bs_current);
  document.getElementById('p3PartName').style.color=bs_current.color;
  document.getElementById('p3PartLocal').textContent=lang==='en'?bs_current.tl:bs_current.en;
  speak(n(bs_current));
}
function bs_pickBucket(zone){
  if(bs_answered)return;
  // Deselect all buckets visually
  ['p3BHead','p3BTorso','p3BArms','p3BLegs'].forEach(id=>document.getElementById(id).classList.remove('bucket-selected'));
  const bucketMap={head:'p3BHead',torso:'p3BTorso',arms:'p3BArms',legs:'p3BLegs'};
  document.getElementById(bucketMap[zone]).classList.add('bucket-selected');
  bs_selectedZone=zone;
  document.getElementById('p3SubmitBtn').style.display='';
}
function bs_submitAnswer(){
  if(!bs_selectedZone||bs_answered)return;
  bs_answered=true;
  document.getElementById('p3SubmitBtn').style.display='none';
  ['p3BHead','p3BTorso','p3BArms','p3BLegs'].forEach(id=>document.getElementById(id).classList.add('disabled'));
  const bucketMap={head:'p3BHead',torso:'p3BTorso',arms:'p3BArms',legs:'p3BLegs'};
  const sortedMap={head:'p3SortedHead',torso:'p3SortedTorso',arms:'p3SortedArms',legs:'p3SortedLegs'};
  const zone=bs_selectedZone;
  const isCorrect=zone===bs_current.zone;
  const c=BS_COPY[lang];
  if(isCorrect){
    document.getElementById(bucketMap[zone]).classList.add('correct-pick');
    bs_score+=10;saveProgress(ACT_IDS[3],Math.round((bs_score/BS_MAX)*100));document.getElementById('p3Score').textContent=bs_score;
    document.getElementById('p3Feedback').textContent=c.feedbackCorrect(n(bs_current),bs_zoneLabel(zone));
    document.getElementById('p3Feedback').className='feedback-msg correct';
    const mini=document.createElement('div');mini.className='mini-part';mini.textContent=n(bs_current);
    document.getElementById(sortedMap[zone]).appendChild(mini);
    confetti(bs_current.color||'#40c057');
  } else {
    document.getElementById(bucketMap[zone]).classList.add('wrong-pick');
    document.getElementById(bucketMap[bs_current.zone]).classList.add('correct-pick');
    document.getElementById('p3Feedback').textContent=c.feedbackWrong(n(bs_current),bs_zoneLabel(bs_current.zone));
    document.getElementById('p3Feedback').className='feedback-msg wrong';
  }
  bs_qIdx++;
  document.getElementById('p3NextBtn').style.display='';
}
function bs_nextPart(){
  if(bs_qIdx>=BS_TOTAL)bs_showResult();
  else bs_loadPart();
}
function bs_showResult(){
  document.getElementById('resultTitle').textContent=BS_COPY[lang].resultTitle;
  document.getElementById('resultSub').textContent=BS_COPY[lang].resultSub;
  showResult(3,bs_score,BS_MAX);
}

// ════════════════════════════════════════
// quizzes 4: SIMON SAYS — AUTO NEXT
// ════════════════════════════════════════
const SS_COPY={en:{title:'Simon Says!',sub:'Simon says — tap the correct body part!',simonSays:'Simon Says...',command:nm=>`Touch your ${nm.toUpperCase()}!`,partsLabel:'Tap the correct body part:',hearBtn:'Hear it again!',feedbackCorrect:nm=>`✅ Correct! You touched the ${nm}!`,feedbackWrong:nm=>`❌ That's the ${nm}! Simon said something else!`,submitLbl:'Submit',nextLbl:'Next',resultTitle:'Simon Champion!',resultSub:'You followed Simon perfectly!'},tl:{title:'Simon Says!',sub:'Sinabi ni Simon — i-tap ang tamang bahagi ng katawan!',simonSays:'Sinabi ni Simon...',command:nm=>`Hawakan ang ${nm.toUpperCase()}!`,partsLabel:'I-tap ang tamang bahagi:',hearBtn:'Pakinggan ulit!',feedbackCorrect:nm=>`✅ Tama! Hinawakan mo ang ${nm}!`,feedbackWrong:nm=>`❌ Iyon ang ${nm}! Iba ang sinabi ni Simon!`,submitLbl:'I-submit',nextLbl:'Susunod',resultTitle:'Kampeon ng Simon!',resultSub:'Perpekto mong sinunod si Simon!'}};
const SS_TOTAL=10,SS_MAX=SS_TOTAL*10;
let ss_pool=[],ss_current=null,ss_answered=false,ss_score=0,ss_qIdx=0,ss_lives=3;
let ss_selectedTile=null,ss_selectedPart=null,ss_selectedIsCorrect=false;

function ss_startGame(){
  ss_score=0;ss_qIdx=0;ss_lives=3;ss_answered=false;ss_pool=shuffle([...PARTS_PO]);
  ss_selectedTile=null;ss_selectedPart=null;
  document.getElementById('p4Score').textContent='0';
  document.getElementById('resultOverlay').classList.remove('active');
  ss_updateLives();
  const c=SS_COPY[lang];
  document.getElementById('p4Title').textContent=c.title;
  document.getElementById('p4Sub').textContent=c.sub;
  document.getElementById('p4SimonSays').textContent=c.simonSays;
  document.getElementById('p4PartsLabel').textContent=c.partsLabel;
  document.getElementById('p4HearBtn').textContent=c.hearBtn;
  document.getElementById('p4SubmitLbl').textContent=c.submitLbl;
  document.getElementById('p4NextLbl').textContent=c.nextLbl;
  saveStart(ACT_IDS[4]);
  ss_loadRound();
}
function ss_updateLives(){document.querySelectorAll('#p4Lives .life-icon').forEach((ic,i)=>ic.classList.toggle('lost',i>=ss_lives));}
function ss_loadRound(){
  ss_answered=false;ss_selectedTile=null;ss_selectedPart=null;ss_selectedIsCorrect=false;
  document.getElementById('p4Feedback').textContent='';document.getElementById('p4Feedback').className='feedback-msg';
  document.getElementById('p4SubmitBtn').style.display='none';
  document.getElementById('p4NextBtn').style.display='none';
  if(!ss_pool.length)ss_pool=shuffle([...PARTS_PO]);
  ss_current=ss_pool.shift();
  document.getElementById('p4QNum').textContent=ss_qIdx+1;
  document.getElementById('p4Bar').style.width=((ss_qIdx/SS_TOTAL)*100)+'%';
  document.getElementById('p4BarLbl').textContent=`${ss_qIdx} / ${SS_TOTAL}`;
  const cmd=SS_COPY[lang].command(n(ss_current));
  const cmdEl=document.getElementById('p4Command');cmdEl.textContent=cmd;
  cmdEl.classList.remove('speaking');void cmdEl.offsetWidth;cmdEl.classList.add('speaking');
  document.getElementById('p4CommandLocal').textContent=lang==='en'?ss_current.tl:ss_current.en;
  const others=shuffle(PARTS_PO.filter(p=>p.en!==ss_current.en)).slice(0,7);
  const tiles=shuffle([ss_current,...others]);
  const grid=document.getElementById('p4PartsGrid');grid.innerHTML='';
  tiles.forEach((p,i)=>{
    const isCorrect=p.en===ss_current.en;
    const tile=document.createElement('div');tile.className='part-tile';tile.style.setProperty('--tc',p.color);tile.style.animationDelay=(i*.04)+'s';
    tile.innerHTML=`<div class="tile-img"><img src="${p.img}" alt="${p.en}" onerror="this.style.opacity='.3'"></div><div class="tile-name" style="color:${p.color}">${n(p)}</div>`;
    tile.onclick=()=>ss_selectAnswer(tile,p,isCorrect);
    grid.appendChild(tile);
  });
  setTimeout(ss_sayCommand,500);
}
function ss_selectAnswer(tile,part,isCorrect){
  if(ss_answered)return;
  // Deselect previous tile
  document.querySelectorAll('#p4PartsGrid .part-tile').forEach(t=>t.classList.remove('tile-selected'));
  tile.classList.add('tile-selected');
  ss_selectedTile=tile;ss_selectedPart=part;ss_selectedIsCorrect=isCorrect;
  document.getElementById('p4SubmitBtn').style.display='';
}
function ss_submitAnswer(){
  if(!ss_selectedTile||ss_answered)return;
  ss_answered=true;
  document.getElementById('p4SubmitBtn').style.display='none';
  document.querySelectorAll('#p4PartsGrid .part-tile').forEach(t=>t.classList.add('answered'));
  const c=SS_COPY[lang];
  if(ss_selectedIsCorrect){
    ss_selectedTile.classList.add('correct');ss_selectedTile.classList.remove('tile-selected');
    ss_score+=10;saveProgress(ACT_IDS[4],Math.round((ss_score/SS_MAX)*100));
    document.getElementById('p4Score').textContent=ss_score;
    document.getElementById('p4Feedback').textContent=c.feedbackCorrect(n(ss_current));
    document.getElementById('p4Feedback').className='feedback-msg correct';
    confetti(ss_current.color);
  } else {
    ss_selectedTile.classList.add('wrong');ss_selectedTile.classList.remove('tile-selected');
    document.querySelectorAll('#p4PartsGrid .part-tile').forEach(t=>{if(t.querySelector('.tile-name')?.textContent===n(ss_current))t.classList.add('correct');});
    ss_lives--;ss_updateLives();
    document.getElementById('p4Feedback').textContent=c.feedbackWrong(n(ss_selectedPart));
    document.getElementById('p4Feedback').className='feedback-msg wrong';
  }
  ss_qIdx++;
  document.getElementById('p4NextBtn').style.display='';
}
function ss_nextRound(){
  if(ss_qIdx>=SS_TOTAL||ss_lives<=0){ss_showResult();}
  else{ss_loadRound();}
}
function ss_sayCommand(){
  if(!window.speechSynthesis||!ss_current)return;
  window.speechSynthesis.cancel();
  const prefix=lang==='en'?'Simon says, touch your ':'Sinabi ni Simon, hawakan ang ';
  const u=new SpeechSynthesisUtterance(prefix+n(ss_current));
  u.lang=lang==='tl'?'fil-PH':'en-US';u.rate=0.82;u.pitch=1.15;
  window.speechSynthesis.speak(u);
}
function ss_showResult(){
  document.getElementById('resultTitle').textContent=SS_COPY[lang].resultTitle;
  document.getElementById('resultSub').textContent=SS_COPY[lang].resultSub;
  showResult(4,ss_score,SS_MAX);
}

// ── FLUSH PROGRESS ON NAVIGATION / PAGE HIDE ──
function flushActiveTabProgress() {
  switch(activeTab) {
    case 1: if(bm_score>0) saveProgress(ACT_IDS[1],Math.min(100,Math.round((bm_score/BM_MAX)*100))); break;
    case 2: if(po_score>0) saveProgress(ACT_IDS[2],Math.round((po_score/PO_MAX)*100)); break;
    case 3: if(bs_score>0) saveProgress(ACT_IDS[3],Math.round((bs_score/BS_MAX)*100)); break;
    case 4: if(ss_score>0) saveProgress(ACT_IDS[4],Math.round((ss_score/SS_MAX)*100)); break;
  }
}
window.addEventListener('pagehide',     () => flushActiveTabProgress());
window.addEventListener('beforeunload', () => flushActiveTabProgress());
document.addEventListener('visibilitychange', () => {
  if(document.visibilityState === 'hidden') flushActiveTabProgress();
});

// ── INIT ──
if(activeTab===1)bm_startGame();
else if(activeTab===2)po_startGame();
else if(activeTab===3)bs_startGame();
else if(activeTab===4)ss_startGame();
</script>

</div><!-- /page-wrap -->
</body>
</html>