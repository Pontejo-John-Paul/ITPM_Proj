<?php
session_start();
require_once 'database.php';
require_once 'activity_helper.php';

if (!isset($_SESSION['student_id']) || $_SESSION['role'] !== 'student') {
    header("Location: index.php");
    exit;
}

$student_id = $_SESSION['student_id'];
$lesson_id  = isset($_GET['lesson_id']) ? (int)$_GET['lesson_id'] : 3; // Shapes = 3

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

$a1 = $act_map['shape_matching']  ?? ['quizzes_id'=>0,'status'=>'not_started','score'=>0];
$a2 = $act_map['spot_the_shape']  ?? ['quizzes_id'=>0,'status'=>'not_started','score'=>0];
$a3 = $act_map['shape_sorting']   ?? ['quizzes_id'=>0,'status'=>'not_started','score'=>0];
$a4 = $act_map['count_the_sides'] ?? ['quizzes_id'=>0,'status'=>'not_started','score'=>0];

$active_tab = isset($_GET['tab']) ? max(1, min(4, (int)$_GET['tab'])) : 1;
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Shapes Activities — E-KINDER</title>
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
.act-tab:hover:not(.active){border-color:#ffe0cc;color:#555;transform:translateY(-2px);}
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

/* ── ACT 1: SHAPE MATCHING ── */
.game-area{max-width:700px;margin:0 auto;}
.round-info{font-family:'Fredoka One',cursive;font-size:.85rem;color:#bbb;text-align:center;margin-bottom:20px;text-transform:uppercase;letter-spacing:1px;}
.match-container{display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:24px;}
.match-col-label{font-family:'Fredoka One',cursive;font-size:.78rem;color:#888;text-transform:uppercase;letter-spacing:1px;text-align:center;margin-bottom:12px;}
.match-col{display:flex;flex-direction:column;gap:12px;}
.name-card{background:#fff;border:3px solid #f0f0f0;border-radius:18px;padding:16px 14px;text-align:center;cursor:pointer;box-shadow:0 4px 14px rgba(0,0,0,.06);transition:all .25s cubic-bezier(.34,1.56,.64,1);}
.name-card:hover:not(.matched):not(.disabled){transform:translateY(-4px) scale(1.03);box-shadow:0 10px 26px rgba(0,0,0,.1);}
.name-card.selected{border-color:var(--card-color,#FF6B6B);background:color-mix(in srgb,var(--card-color,#FF6B6B) 10%,white);transform:translateY(-4px) scale(1.04);}
.name-card.matched{border-color:#40c057;background:#f0fdf4;cursor:default;animation:matchPop .4s cubic-bezier(.34,1.56,.64,1);}
.name-card.wrong{border-color:#ff4444;background:#fff5f5;animation:cardShake .4s ease;}
.name-text{font-family:'Fredoka One',cursive;font-size:1.3rem;color:var(--card-color,#555);}
.match-check-icon{color:#40c057;font-size:.9rem;opacity:0;transition:opacity .2s;display:block;margin-top:3px;}
.name-card.matched .match-check-icon{opacity:1;}
.shape-card{background:#fff;border:3px solid #f0f0f0;border-radius:18px;padding:14px 10px;text-align:center;cursor:pointer;box-shadow:0 4px 14px rgba(0,0,0,.06);transition:all .25s cubic-bezier(.34,1.56,.64,1);position:relative;}
.shape-card:hover:not(.matched):not(.disabled){transform:translateY(-4px) scale(1.03);box-shadow:0 10px 26px rgba(0,0,0,.1);}
.shape-card.selected{border-color:var(--card-color,#FF6B6B);background:color-mix(in srgb,var(--card-color,#FF6B6B) 10%,white);transform:translateY(-4px) scale(1.04);}
.shape-card.matched{border-color:#40c057;background:#f0fdf4;cursor:default;animation:matchPop .4s cubic-bezier(.34,1.56,.64,1);}
.shape-card.wrong{border-color:#ff4444;background:#fff5f5;animation:cardShake .4s ease;}
.shape-card svg{width:64px;height:64px;filter:drop-shadow(0 3px 8px rgba(0,0,0,.12));transition:transform .3s cubic-bezier(.34,1.56,.64,1);}
.shape-card:hover:not(.matched) svg{transform:rotate(-8deg) scale(1.1);}
.shape-check{position:absolute;top:8px;right:10px;color:#40c057;font-size:.9rem;opacity:0;transition:opacity .2s;}
.shape-card.matched .shape-check{opacity:1;}

/* ── ACT 2: SPOT THE SHAPE ── */
.question-card{background:#fff;border-radius:28px;padding:32px 24px;text-align:center;box-shadow:0 8px 36px rgba(0,0,0,.08);border:2px solid #f0ece4;max-width:520px;margin:0 auto 24px;}
.q-prompt{font-family:'Fredoka One',cursive;font-size:.9rem;color:#bbb;text-transform:uppercase;letter-spacing:1px;margin-bottom:16px;}
.q-name{font-family:'Fredoka One',cursive;font-size:3rem;color:var(--q-color,#FF6B6B);line-height:1;margin-bottom:8px;animation:nameBounce 2s ease-in-out infinite;}
@keyframes nameBounce{0%,100%{transform:translateY(0) rotate(-2deg)}50%{transform:translateY(-10px) rotate(2deg)}}
.q-speak-btn{background:none;border:none;font-size:1.3rem;cursor:pointer;color:var(--q-color,#FF6B6B);transition:transform .2s;margin-top:4px;}
.q-speak-btn:hover{transform:scale(1.2);}
.choices-grid-4{display:grid;grid-template-columns:repeat(4,1fr);gap:14px;max-width:580px;margin:0 auto 24px;}
@media(max-width:480px){.choices-grid-4{grid-template-columns:repeat(2,1fr);}}
.choice-card{background:#fff;border:3px solid #f0f0f0;border-radius:18px;padding:20px 10px;text-align:center;cursor:pointer;box-shadow:0 4px 14px rgba(0,0,0,.06);transition:all .25s cubic-bezier(.34,1.56,.64,1);animation:cardIn .4s ease both;}
.choice-card:hover:not(.answered){transform:translateY(-6px) scale(1.06);box-shadow:0 12px 28px rgba(0,0,0,.12);border-color:var(--cc);}
.choice-card.correct{border-color:#40c057!important;background:#d3f9d8!important;animation:popGreen .4s cubic-bezier(.34,1.56,.64,1);}
.choice-card.wrong{border-color:#ff4444!important;background:#ffe3e3!important;animation:cardShake .4s ease;}
.choice-card.answered{cursor:default;}
.choice-card svg{width:72px;height:72px;filter:drop-shadow(0 3px 8px rgba(0,0,0,.1));transition:transform .3s cubic-bezier(.34,1.56,.64,1);}
.choice-card:hover:not(.answered) svg{transform:rotate(-8deg) scale(1.1);}
.choice-card:nth-child(1){animation-delay:.04s}.choice-card:nth-child(2){animation-delay:.08s}
.choice-card:nth-child(3){animation-delay:.12s}.choice-card:nth-child(4){animation-delay:.16s}

/* ── ACT 3: SHAPE SORTING ── */
.sort-stage{background:#fff;border-radius:28px;padding:32px 24px;text-align:center;box-shadow:0 8px 36px rgba(0,0,0,.08);border:2px solid #f0ece4;max-width:520px;margin:0 auto 24px;}
.sort-prompt{font-family:'Fredoka One',cursive;font-size:.9rem;color:#bbb;text-transform:uppercase;letter-spacing:1px;margin-bottom:20px;}
.sort-shape-wrap{width:130px;height:130px;margin:0 auto 18px;filter:drop-shadow(0 8px 20px rgba(0,0,0,.15));animation:shapeBounce 2s ease-in-out infinite;}
.sort-shape-wrap svg{width:100%;height:100%;}
@keyframes shapeBounce{0%,100%{transform:translateY(0) rotate(-3deg)}50%{transform:translateY(-12px) rotate(3deg)}}
.sort-shape-name{font-family:'Fredoka One',cursive;font-size:2rem;margin-bottom:8px;}
.buckets{display:grid;grid-template-columns:1fr 1fr;gap:16px;max-width:520px;margin:0 auto 20px;}
.bucket{border-radius:22px;padding:20px 16px;text-align:center;cursor:pointer;border:3px solid #f0f0f0;background:#fff;box-shadow:0 4px 14px rgba(0,0,0,.06);transition:all .25s cubic-bezier(.34,1.56,.64,1);}
.bucket.no-corners{border-color:#4ECDC4;background:#e3fafc;}
.bucket.has-corners{border-color:#A29BFE;background:#f3f0ff;}
.bucket:hover:not(.disabled){transform:translateY(-5px) scale(1.04);box-shadow:0 12px 28px rgba(0,0,0,.1);}
.bucket.correct-pick{animation:bucketPop .4s cubic-bezier(.34,1.56,.64,1);border-width:4px;}
.bucket.wrong-pick{animation:bucketShake .4s ease;border-color:#ff4444!important;background:#fff5f5!important;}
.bucket.disabled{pointer-events:none;opacity:.6;}
@keyframes bucketPop{0%{transform:scale(.95)}60%{transform:scale(1.08)}100%{transform:scale(1)}}
@keyframes bucketShake{0%,100%{transform:translateX(0)}25%{transform:translateX(-8px)}75%{transform:translateX(8px)}}
.bucket-icon{font-size:2.4rem;margin-bottom:8px;}
.bucket-label{font-family:'Fredoka One',cursive;font-size:1.1rem;color:#2d2d2d;margin-bottom:4px;}
.bucket-desc{font-size:.72rem;font-weight:800;color:#bbb;}
.bucket-shapes{display:flex;flex-wrap:wrap;justify-content:center;gap:6px;margin-top:10px;min-height:32px;}
.sorted-shape{width:30px;height:30px;animation:sortIn .3s cubic-bezier(.34,1.56,.64,1);}
.sorted-shape svg{width:100%;height:100%;}
@keyframes sortIn{from{transform:scale(0) rotate(-20deg)}to{transform:scale(1) rotate(0)}}

/* ── ACT 4: COUNT THE SIDES ── */
.shape-stage{background:#fff;border-radius:28px;padding:32px 24px;text-align:center;box-shadow:0 8px 36px rgba(0,0,0,.08);border:2px solid #f0ece4;max-width:520px;margin:0 auto 24px;}
.stage-prompt{font-family:'Fredoka One',cursive;font-size:.9rem;color:#bbb;text-transform:uppercase;letter-spacing:1px;margin-bottom:18px;}
.shape-display{width:160px;height:160px;margin:0 auto 16px;filter:drop-shadow(0 8px 24px rgba(0,0,0,.15));}
.shape-display svg{width:100%;height:100%;}
.shape-name-display{font-family:'Fredoka One',cursive;font-size:1.8rem;margin-bottom:8px;}
.side-hint{font-size:.82rem;font-weight:800;color:#ddd;letter-spacing:.5px;}
.count-dots{display:flex;justify-content:center;gap:8px;margin:12px 0 20px;flex-wrap:wrap;}
.count-dot{width:16px;height:16px;border-radius:50%;opacity:.3;transition:opacity .3s,transform .3s;}
.count-dot.active{opacity:1;transform:scale(1.2);}
.number-choices{display:flex;justify-content:center;gap:14px;flex-wrap:wrap;max-width:480px;margin:0 auto 20px;}
.num-btn{width:80px;height:80px;border-radius:20px;background:#fff;border:3px solid #f0f0f0;font-family:'Fredoka One',cursive;font-size:2.4rem;color:var(--nb-color,#aaa);cursor:pointer;box-shadow:0 4px 14px rgba(0,0,0,.06);transition:all .2s cubic-bezier(.34,1.56,.64,1);display:flex;align-items:center;justify-content:center;}
.num-btn:hover:not(.answered){transform:translateY(-6px) scale(1.1);box-shadow:0 12px 28px rgba(0,0,0,.12);border-color:var(--nb-color);}
.num-btn.correct{background:#d3f9d8!important;border-color:#40c057!important;color:#2f9e44!important;animation:numPop .4s cubic-bezier(.34,1.56,.64,1);}
.num-btn.wrong{background:#ffe3e3!important;border-color:#ff4444!important;color:#ff4444!important;animation:numShake .4s ease;}
.num-btn.answered{cursor:default;}
@keyframes numPop{0%{transform:scale(.8)}60%{transform:scale(1.2)}100%{transform:scale(1)}}
@keyframes numShake{0%,100%{transform:translateX(0)}25%{transform:translateX(-6px)}75%{transform:translateX(6px)}}

.choice-card.selected{border-color:#4ECDC4!important;background:#e3fafc!important;transform:translateY(-6px) scale(1.06);box-shadow:0 12px 28px rgba(78,205,196,.2);}
.num-btn.selected{background:#fff9db!important;border-color:#FDCB6E!important;transform:translateY(-6px) scale(1.1);box-shadow:0 12px 28px rgba(253,203,110,.25);}
.bucket.bucket-selected{border-width:4px!important;transform:translateY(-5px) scale(1.04);box-shadow:0 12px 28px rgba(0,0,0,.12);}
.bucket.no-corners.bucket-selected{border-color:#4ECDC4!important;background:#c8f5f0!important;}
.bucket.has-corners.bucket-selected{border-color:#A29BFE!important;background:#e5e0ff!important;}

/* SHARED */
@keyframes matchPop{0%{transform:scale(.9)}60%{transform:scale(1.08)}100%{transform:scale(1)}}
@keyframes cardShake{0%,100%{transform:translateX(0)}25%{transform:translateX(-6px)}75%{transform:translateX(6px)}}
@keyframes popGreen{0%{transform:scale(.9)}60%{transform:scale(1.1)}100%{transform:scale(1)}}
@keyframes cardIn{from{opacity:0;transform:translateY(20px) scale(.9)}to{opacity:1;transform:none}}
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
.next-quizzes-btn{width:100%;margin-top:8px;background:linear-gradient(135deg,#FF6B6B,#A29BFE);color:#fff;border:none;border-radius:var(--pill);padding:12px 24px;font-family:'Fredoka One',cursive;font-size:.95rem;cursor:pointer;box-shadow:0 6px 18px rgba(0,0,0,.15);transition:transform .2s;display:flex;align-items:center;justify-content:center;gap:8px;}
.next-quizzes-btn:hover{transform:scale(1.04);}
.cfbit{position:fixed;pointer-events:none;z-index:1000;animation:cfFall linear forwards;}
@keyframes cfFall{0%{transform:translateY(-16px) rotate(0deg);opacity:1}100%{transform:translateY(105vh) rotate(700deg);opacity:0}}
</style>
</head>
<body>
<div class="page-wrap">

<nav class="lesson-nav">
  <a href="activities.php" class="lnav-back"><i class="fas fa-arrow-left"></i> Back</a>
  <div class="lnav-title">🔷 Shapes Activities</div>
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
      1 => ['icon'=>'fas fa-puzzle-piece',    'en'=>'Matching', 'tl'=>'Pagtutugma', 'act'=>$a1],
      2 => ['icon'=>'fas fa-search',           'en'=>'Spot It',  'tl'=>'Hanapin',    'act'=>$a2],
      3 => ['icon'=>'fas fa-th-large',         'en'=>'Sorting',  'tl'=>'Pag-aayos',  'act'=>$a3],
      4 => ['icon'=>'fas fa-sort-numeric-up',  'en'=>'Sides',    'tl'=>'Mga Gilid',  'act'=>$a4],
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

  <!-- ══════════════════ PANEL 1: SHAPE MATCHING ══════════════════ -->
  <div class="quizzes-panel <?php echo $active_tab===1?'active':'';?>" id="panel1">
    <div class="page-header">
      <div class="quizzes-badge"><i class="fas fa-puzzle-piece"></i> quizzes 1 of 4</div>
      <div class="page-title" id="p1Title">Shape Matching!</div>
      <div class="page-sub" id="p1Sub">Match each shape name to the correct shape!</div>
      <?php if($a1['score']>0):?><div class="best-score-badge">⭐ Best Score: <?php echo $a1['score'];?>%</div><?php endif;?>
    </div>
    <div class="score-bar">
      <div class="score-item"><div class="score-num" id="p1Score">0</div><div class="score-label">Score</div></div>
      <div class="score-sep"></div>
      <div class="score-item"><div class="score-num" id="p1Round">1</div><div class="score-label">Round</div></div>
      <div class="score-sep"></div>
      <div class="progress-wrap">
        <div class="progress-bar-outer"><div class="progress-bar-inner" id="p1Bar" style="width:0%;background:linear-gradient(90deg,#FF6B6B,#ff922b)"></div></div>
        <div class="progress-label" id="p1BarLbl">0 / 4 matched</div>
      </div>
    </div>
    <div class="game-area">
      <div class="round-info" id="p1RoundInfo">Round 1 — Match 4 pairs!</div>
      <div class="match-container">
        <div><div class="match-col-label" id="p1ColName">Shape Names</div><div class="match-col" id="p1NameCol"></div></div>
        <div><div class="match-col-label" id="p1ColShape">Shapes</div><div class="match-col" id="p1ShapeCol"></div></div>
      </div>
      <div class="feedback-msg" id="p1Feedback"></div>
      <div class="controls">
        <button class="btn-action btn-primary" id="p1SubmitBtn" onclick="sm_submitRound()"><i class="fas fa-check"></i> <span id="p1SubmitLbl">Submit Round</span></button>
        <button class="btn-action btn-secondary" id="p1NextBtn" onclick="sm_proceedNext()" style="display:none"><i class="fas fa-arrow-right"></i> <span id="p1NextLbl">Next Round</span></button>
      </div>
    </div>
  </div>

  <!-- ══════════════════ PANEL 2: SPOT THE SHAPE ══════════════════ -->
  <div class="quizzes-panel <?php echo $active_tab===2?'active':'';?>" id="panel2">
    <div class="page-header">
      <div class="quizzes-badge"><i class="fas fa-search"></i> quizzes 2 of 4</div>
      <div class="page-title" id="p2Title">Spot the Shape!</div>
      <div class="page-sub" id="p2Sub">Find the correct shape from the choices!</div>
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
        <div class="progress-bar-outer"><div class="progress-bar-inner" id="p2Bar" style="width:0%;background:linear-gradient(90deg,#4ECDC4,#00CEC9)"></div></div>
        <div class="progress-label" id="p2BarLbl">0 / 10</div>
      </div>
    </div>
    <div class="question-card">
      <div class="q-prompt" id="p2QPrompt">Find this shape:</div>
      <div class="q-name" id="p2QName">Circle</div>
      <button class="q-speak-btn" onclick="ss_speakShape()"><i class="fas fa-volume-up"></i></button>
    </div>
    <div class="feedback-msg" id="p2Feedback"></div>
    <div class="choices-grid-4" id="p2Choices"></div>
    <div class="controls">
      <button class="btn-action btn-primary" id="p2SubmitBtn" onclick="ss_submitAnswer()" style="display:none"><i class="fas fa-check"></i> <span id="p2SubmitLbl">Submit</span></button>
      <button class="btn-action btn-secondary" id="p2NextBtn" onclick="ss_nextQuestion()" style="display:none"><i class="fas fa-arrow-right"></i> <span id="p2NextLbl">Next</span></button>
    </div>
  </div>

  <!-- ══════════════════ PANEL 3: SHAPE SORTING ══════════════════ -->
  <div class="quizzes-panel <?php echo $active_tab===3?'active':'';?>" id="panel3">
    <div class="page-header">
      <div class="quizzes-badge"><i class="fas fa-th-large"></i> quizzes 3 of 4</div>
      <div class="page-title" id="p3Title">Shape Sorting!</div>
      <div class="page-sub" id="p3Sub">Does it have corners or not? Sort the shapes!</div>
      <?php if($a3['score']>0):?><div class="best-score-badge">⭐ Best Score: <?php echo $a3['score'];?>%</div><?php endif;?>
    </div>
    <div class="score-bar">
      <div class="score-item"><div class="score-num" id="p3Score">0</div><div class="score-label">Score</div></div>
      <div class="score-sep"></div>
      <div class="score-item"><div class="score-num" id="p3QNum">1</div><div class="score-label">Shape</div></div>
      <div class="score-sep"></div>
      <div class="progress-wrap">
        <div class="progress-bar-outer"><div class="progress-bar-inner" id="p3Bar" style="width:0%;background:linear-gradient(90deg,#A29BFE,#7950f2)"></div></div>
        <div class="progress-label" id="p3BarLbl">0 / 10</div>
      </div>
    </div>
    <div class="sort-stage">
      <div class="sort-prompt" id="p3SortPrompt">Where does this shape go?</div>
      <div class="sort-shape-wrap" id="p3SortShapeWrap"><svg id="p3SortSvg" viewBox="0 0 100 100"></svg></div>
      <div class="sort-shape-name" id="p3SortName">Circle</div>
    </div>
    <div class="buckets">
      <div class="bucket no-corners" id="p3BucketNo" onclick="so_pickBucket(false)">
        <div class="bucket-icon">⭕</div>
        <div class="bucket-label" id="p3NoLabel">No Corners</div>
        <div class="bucket-desc" id="p3NoDesc">Round shapes</div>
        <div class="bucket-shapes" id="p3SortedNo"></div>
      </div>
      <div class="bucket has-corners" id="p3BucketYes" onclick="so_pickBucket(true)">
        <div class="bucket-icon">🔷</div>
        <div class="bucket-label" id="p3YesLabel">Has Corners</div>
        <div class="bucket-desc" id="p3YesDesc">Shapes with corners</div>
        <div class="bucket-shapes" id="p3SortedYes"></div>
      </div>
    </div>
    <div class="feedback-msg" id="p3Feedback"></div>
    <div class="controls">
      <button class="btn-action btn-primary" id="p3SubmitBtn" onclick="so_submitAnswer()" style="display:none"><i class="fas fa-check"></i> <span id="p3SubmitLbl">Submit</span></button>
      <button class="btn-action btn-secondary" id="p3NextBtn" onclick="so_nextShape()" style="display:none"><i class="fas fa-arrow-right"></i> <span id="p3NextLbl">Next</span></button>
    </div>
  </div>

  <!-- ══════════════════ PANEL 4: COUNT THE SIDES ══════════════════ -->
  <div class="quizzes-panel <?php echo $active_tab===4?'active':'';?>" id="panel4">
    <div class="page-header">
      <div class="quizzes-badge"><i class="fas fa-sort-numeric-up"></i> quizzes 4 of 4</div>
      <div class="page-title" id="p4Title">Count the Sides!</div>
      <div class="page-sub" id="p4Sub">How many sides does this shape have?</div>
      <?php if($a4['score']>0):?><div class="best-score-badge">⭐ Best Score: <?php echo $a4['score'];?>%</div><?php endif;?>
    </div>
    <div class="score-bar">
      <div class="score-item"><div class="score-num" id="p4Score">0</div><div class="score-label">Score</div></div>
      <div class="score-sep"></div>
      <div class="score-item"><div class="score-num" id="p4QNum">1</div><div class="score-label">Shape</div></div>
      <div class="score-sep"></div>
      <div class="progress-wrap">
        <div class="progress-bar-outer"><div class="progress-bar-inner" id="p4Bar" style="width:0%;background:linear-gradient(90deg,#FDCB6E,#e8a000)"></div></div>
        <div class="progress-label" id="p4BarLbl">0 / 10</div>
      </div>
    </div>
    <div class="shape-stage">
      <div class="stage-prompt" id="p4StagePrompt">How many sides?</div>
      <div class="shape-display"><svg id="p4ShapeSvg" viewBox="0 0 100 100"></svg></div>
      <div class="shape-name-display" id="p4ShapeName">Circle</div>
      <div class="count-dots" id="p4CountDots"></div>
      <div class="side-hint" id="p4SideHint"></div>
    </div>
    <div class="number-choices" id="p4NumChoices"></div>
    <div class="feedback-msg" id="p4Feedback"></div>
    <div class="controls">
      <button class="btn-action btn-primary" id="p4SubmitBtn" onclick="cs_submitAnswer()" style="display:none"><i class="fas fa-check"></i> <span id="p4SubmitLbl">Submit</span></button>
      <button class="btn-action btn-secondary" id="p4NextBtn" onclick="cs_nextQuestion()" style="display:none"><i class="fas fa-arrow-right"></i> <span id="p4NextLbl">Next Shape</span></button>
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

// ── SHARED SHAPES DATA ──
const SHAPES_BASE=[
  {en:'Circle',   tl:'Bilog',     color:'#FF6B6B', path:'M50,10 A40,40 0 1,1 49.99,10 Z'},
  {en:'Square',   tl:'Parisukat', color:'#4ECDC4', path:'M10,10 H90 V90 H10 Z'},
  {en:'Triangle', tl:'Tatsulok',  color:'#FFE66D', path:'M50,5 L95,90 L5,90 Z'},
  {en:'Rectangle',tl:'Parihaba',  color:'#A29BFE', path:'M5,20 H95 V80 H5 Z'},
  {en:'Oval',     tl:'Itlog',     color:'#FD79A8', path:'M50,20 A40,30 0 1,1 49.99,20 Z'},
  {en:'Star',     tl:'Bituin',    color:'#FDCB6E', path:'M50,5 L61,35 L95,35 L68,57 L79,91 L50,70 L21,91 L32,57 L5,35 L39,35 Z'},
  {en:'Heart',    tl:'Puso',      color:'#E17055', path:'M50,80 C50,80 10,55 10,30 A20,20 0 0,1 50,25 A20,20 0 0,1 90,30 C90,55 50,80 50,80 Z'},
  {en:'Diamond',  tl:'Dyamante',  color:'#00CEC9', path:'M50,5 L95,50 L50,95 L5,50 Z'},
];
const SHAPES_SORT=SHAPES_BASE.map(s=>({...s,hasCorners:['Square','Triangle','Rectangle','Star','Diamond'].includes(s.en)}));
const SHAPES_SIDES=SHAPES_BASE.map(s=>{
  const sidesMap={Circle:0,Square:4,Triangle:3,Rectangle:4,Oval:0,Star:10,Heart:0,Diamond:4};
  const hintEN={Circle:'Round — no sides!',Square:'4 equal sides!',Triangle:'3 sides!',Rectangle:'4 sides!',Oval:'Round — no sides!',Star:'10 sides (5 points)!',Heart:'Curved — no straight sides!',Diamond:'4 sides!'};
  const hintTL={Circle:'Bilog — walang gilid!',Square:'4 pantay na gilid!',Triangle:'3 gilid!',Rectangle:'4 gilid!',Oval:'Bilog — walang gilid!',Star:'10 gilid (5 tulis)!',Heart:'Kurba — walang tuwid na gilid!',Diamond:'4 gilid!'};
  return {...s,sides:sidesMap[s.en],sideHintEN:hintEN[s.en],sideHintTL:hintTL[s.en]};
});
const SIDE_CHOICE_SETS={0:[0,2,3,4],3:[3,4,5,0],4:[4,3,5,6],10:[10,5,8,4]};
const COLORS=['#FF6B6B','#4ECDC4','#FFE66D','#A29BFE','#FD79A8','#FDCB6E','#E17055','#00CEC9'];

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
  if(activeTab===1)sm_startGame();
  if(activeTab===2)ss_startGame();
  if(activeTab===3)so_startGame();
  if(activeTab===4)cs_startGame();
}

// ── TABS ──
function switchTab(num){
  activeTab=num;
  document.querySelectorAll('.act-tab').forEach((t,i)=>t.classList.toggle('active',i+1===num));
  document.querySelectorAll('.quizzes-panel').forEach((p,i)=>p.classList.toggle('active',i+1===num));
  if(num===1)sm_startGame();
  if(num===2)ss_startGame();
  if(num===3)so_startGame();
  if(num===4)cs_startGame();
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
  confetti('#FF6B6B');confetti('#A29BFE');
  saveProgress(ACT_IDS[actNum],finalScore);
  saveComplete(ACT_IDS[actNum],finalScore);
  PREV_SCORES[actNum]=Math.max(PREV_SCORES[actNum],finalScore);
}
function closeResult(){document.getElementById('resultOverlay').classList.remove('active');}
function playAgain(){
  closeResult();
  if(_curActNum===1)sm_startGame();
  if(_curActNum===2)ss_startGame();
  if(_curActNum===3)so_startGame();
  if(_curActNum===4)cs_startGame();
}
function goNextquizzes(){closeResult();switchTab(_curActNum+1);}

// ── UTILS ──
function shuffle(a){const r=[...a];for(let i=r.length-1;i>0;i--){const j=0|Math.random()*(i+1);[r[i],r[j]]=[r[j],r[i]];}return r;}
function sname(s){return lang==='en'?s.en:s.tl;}
function speak(w){if(!window.speechSynthesis)return;window.speechSynthesis.cancel();const u=new SpeechSynthesisUtterance(w);u.lang=lang==='tl'?'fil-PH':'en-US';u.rate=0.8;u.pitch=1.2;window.speechSynthesis.speak(u);}
function confetti(color){const cols=[color,'#FFE66D','#FF6B6B','#A29BFE','#4ECDC4','#FD79A8'];for(let i=0;i<30;i++){const p=document.createElement('div');p.className='cfbit';p.style.cssText=`left:${Math.random()*100}vw;top:-12px;background:${cols[0|Math.random()*cols.length]};border-radius:${Math.random()>.5?'50%':'3px'};width:${6+Math.random()*8}px;height:${6+Math.random()*8}px;animation-duration:${1.2+Math.random()*1.5}s;animation-delay:${Math.random()*.4}s;`;document.body.appendChild(p);p.addEventListener('animationend',()=>p.remove());}}

// ════════════════════════════════════════
// quizzes 1: SHAPE MATCHING
// ════════════════════════════════════════
const SM_COPY={
  en:{title:'Shape Matching!',sub:'Match each shape name to the correct shape!',colName:'Shape Names',colShape:'Shapes',roundInfo:r=>`Round ${r} of 5 — Match 4 pairs!`,progressLbl:(m,t)=>`${m} / ${t} matched`,submitLbl:'Submit Round',nextLbl:'Next Round',finishLbl:'See Results',feedbackCorrect:(n,pts)=>`✅ Perfect! +${pts} pts earned this round!`,feedbackPartial:(c,gained)=>`⭐ ${c}/4 correct! +${gained} pts earned.`,resultTitle:'Amazing!',resultSub:'You matched all the shapes!'},
  tl:{title:'Pagtutugma ng Hugis!',sub:'Itugma ang bawat pangalan ng hugis sa tamang hugis!',colName:'Mga Pangalan',colShape:'Mga Hugis',roundInfo:r=>`Round ${r} ng 5 — Itugma ang 4 pares!`,progressLbl:(m,t)=>`${m} / ${t} natugma`,submitLbl:'I-submit ang Round',nextLbl:'Susunod na Round',finishLbl:'Tingnan ang Resulta',feedbackCorrect:(n,pts)=>`✅ Perpekto! +${pts} pts nakuha sa round na ito!`,feedbackPartial:(c,gained)=>`⭐ ${c}/4 tama! +${gained} pts nakuha.`,resultTitle:'Kahanga-hanga!',resultSub:'Natugma mo ang lahat ng hugis!'}
};
const SM_TOTAL_ROUNDS=5, SM_PPM=5, SM_MAX=SM_TOTAL_ROUNDS*4*SM_PPM; // 5×4×5 = 100
let sm_score=0,sm_round=0,sm_pool=[],sm_roundShapes=[],sm_selName=null,sm_selShape=null,sm_pairs={},sm_submitted=false;

function sm_startGame(){
  sm_score=0;sm_round=0;sm_pool=shuffle([...SHAPES_BASE]);
  document.getElementById('p1Score').textContent='0';
  document.getElementById('resultOverlay').classList.remove('active');
  const c=SM_COPY[lang];
  document.getElementById('p1Title').textContent=c.title;
  document.getElementById('p1Sub').textContent=c.sub;
  document.getElementById('p1ColName').textContent=c.colName;
  document.getElementById('p1ColShape').textContent=c.colShape;
  document.getElementById('p1SubmitLbl').textContent=c.submitLbl;
  document.getElementById('p1NextLbl').textContent=c.nextLbl;
  saveStart(ACT_IDS[1]);
  sm_nextRound();
}
function sm_nextRound(){
  sm_round++;sm_selName=null;sm_selShape=null;sm_pairs={};sm_submitted=false;
  if(sm_pool.length<4)sm_pool=shuffle([...SHAPES_BASE]);
  sm_roundShapes=sm_pool.splice(0,4);
  const c=SM_COPY[lang];
  document.getElementById('p1Round').textContent=sm_round;
  document.getElementById('p1RoundInfo').textContent=c.roundInfo(sm_round);
  document.getElementById('p1Bar').style.width='0%';
  document.getElementById('p1BarLbl').textContent=c.progressLbl(0,4);
  document.getElementById('p1Feedback').textContent='';
  document.getElementById('p1Feedback').className='feedback-msg';
  document.getElementById('p1SubmitBtn').style.display='';
  document.getElementById('p1NextBtn').style.display='none';
  document.getElementById('p1NextLbl').textContent=sm_round>=SM_TOTAL_ROUNDS?SM_COPY[lang].finishLbl:SM_COPY[lang].nextLbl;
  // Render cards
  const ns=shuffle([...sm_roundShapes]),ss=shuffle([...sm_roundShapes]);
  const nc=document.getElementById('p1NameCol');nc.innerHTML='';
  const sc=document.getElementById('p1ShapeCol');sc.innerHTML='';
  ns.forEach(s=>{
    const card=document.createElement('div');card.className='name-card';card.dataset.key=s.en;card.style.setProperty('--card-color',s.color);
    card.innerHTML=`<i class="fas fa-check match-check-icon"></i><div class="name-text">${sname(s)}</div>`;
    card.onclick=()=>sm_selectName(card,s);nc.appendChild(card);
  });
  ss.forEach(s=>{
    const card=document.createElement('div');card.className='shape-card';card.dataset.key=s.en;card.style.setProperty('--card-color',s.color);
    card.innerHTML=`<i class="fas fa-check shape-check"></i><svg viewBox="0 0 100 100"><path d="${s.path}" fill="${s.color}"/></svg>`;
    card.onclick=()=>sm_selectShape(card,s);sc.appendChild(card);
  });
}
function sm_selectName(card,shape){
  if(sm_submitted)return;
  if(card.classList.contains('matched'))return;
  document.querySelectorAll('#p1NameCol .name-card.selected').forEach(c=>c.classList.remove('selected'));
  sm_selName={card,shape};card.classList.add('selected');
  if(sm_selName&&sm_selShape)sm_pairUp();
}
function sm_selectShape(card,shape){
  if(sm_submitted)return;
  if(card.classList.contains('matched'))return;
  document.querySelectorAll('#p1ShapeCol .shape-card.selected').forEach(c=>c.classList.remove('selected'));
  sm_selShape={card,shape};card.classList.add('selected');
  if(sm_selName&&sm_selShape)sm_pairUp();
}
function sm_pairUp(){
  const nm=sm_selName,sh=sm_selShape;sm_selName=null;sm_selShape=null;
  // Unlink previous shape for this name
  if(sm_pairs[nm.shape.en]){
    const old=sm_pairs[nm.shape.en].shapeCard;
    old.classList.remove('matched');
  }
  // Unlink if shape already linked to another name
  const existingName=Object.keys(sm_pairs).find(k=>sm_pairs[k].shapeKey===sh.shape.en);
  if(existingName){
    const oldNm=sm_pairs[existingName].nameCard;
    oldNm.classList.remove('matched');
    delete sm_pairs[existingName];
  }
  sm_pairs[nm.shape.en]={nameCard:nm.card,shapeCard:sh.card,shapeKey:sh.shape.en};
  nm.card.classList.remove('selected');sh.card.classList.remove('selected');
  nm.card.classList.add('matched');sh.card.classList.add('matched');
  const paired=Object.keys(sm_pairs).length;
  const c=SM_COPY[lang];
  document.getElementById('p1Bar').style.width=((paired/4)*100)+'%';
  document.getElementById('p1BarLbl').textContent=c.progressLbl(paired,4);
}
function sm_submitRound(){
  if(Object.keys(sm_pairs).length<4){
    document.getElementById('p1Feedback').textContent='⚠️ Match all 4 pairs first before submitting!';
    document.getElementById('p1Feedback').className='feedback-msg wrong';
    return;
  }
  sm_submitted=true;
  document.getElementById('p1SubmitBtn').style.display='none';
  let correctCount=0;
  sm_roundShapes.forEach(s=>{
    const pair=sm_pairs[s.en];
    if(!pair)return;
    const isCorrect=pair.shapeKey===s.en;
    if(isCorrect){
      correctCount++;
      pair.nameCard.style.borderColor='#40c057';
      pair.nameCard.style.background='#f0fdf4';
      pair.shapeCard.style.borderColor='#40c057';
      pair.shapeCard.style.background='#f0fdf4';
    } else {
      pair.nameCard.classList.remove('matched');
      pair.shapeCard.classList.remove('matched');
      pair.nameCard.classList.add('wrong');
      pair.shapeCard.classList.add('wrong');
      pair.nameCard.style.borderColor='#ff4444';
      pair.nameCard.style.background='#fff5f5';
      pair.shapeCard.style.borderColor='#ff4444';
      pair.shapeCard.style.background='#fff5f5';
    }
  });
  const roundGain=correctCount*SM_PPM;
  sm_score=sm_score+roundGain;
  document.getElementById('p1Score').textContent=sm_score;
  saveProgress(ACT_IDS[1],Math.min(100,Math.round((sm_score/SM_MAX)*100)));
  const c=SM_COPY[lang];
  if(correctCount===4){
    document.getElementById('p1Feedback').textContent=c.feedbackCorrect(4,roundGain);
    document.getElementById('p1Feedback').className='feedback-msg correct';
    confetti('#40c057');
  } else {
    document.getElementById('p1Feedback').textContent=c.feedbackPartial(correctCount,roundGain);
    document.getElementById('p1Feedback').className=roundGain>0?'feedback-msg correct':'feedback-msg wrong';
  }
  document.getElementById('p1NextBtn').style.display='';
  const isLast=sm_round>=SM_TOTAL_ROUNDS;
  document.getElementById('p1NextLbl').textContent=isLast?SM_COPY[lang].finishLbl:SM_COPY[lang].nextLbl;
}
function sm_proceedNext(){
  if(sm_round>=SM_TOTAL_ROUNDS)sm_showResult();
  else sm_nextRound();
}
function sm_showResult(){
  document.getElementById('resultTitle').textContent=SM_COPY[lang].resultTitle;
  document.getElementById('resultSub').textContent=SM_COPY[lang].resultSub;
  showResult(1,sm_score,SM_MAX);
}

// ════════════════════════════════════════
// quizzes 2: SPOT THE SHAPE
// ════════════════════════════════════════
const SS_COPY={en:{title:'Spot the Shape!',sub:'Find the correct shape from the choices!',prompt:'Find this shape:',next:'Next',feedbackCorrect:n=>`✅ Yes! That's the ${n}!`,feedbackWrong:n=>`❌ Oops! That was the ${n}`,resultTitle:'Superstar!',resultSub:'You found all the shapes!'},tl:{title:'Hanapin ang Hugis!',sub:'Hanapin ang tamang hugis sa mga pagpipilian!',prompt:'Hanapin ang hugis na ito:',next:'Susunod',feedbackCorrect:n=>`✅ Oo! Iyan ang ${n}!`,feedbackWrong:n=>`❌ Mali! Iyon ay ang ${n}`,resultTitle:'Mahusay!',resultSub:'Nahanap mo ang lahat ng hugis!'}};
const SS_TOTAL=10,SS_MAX=SS_TOTAL*10;
let ss_pool=[],ss_current=null,ss_answered=false,ss_score=0,ss_qIdx=0,ss_lives=3;

function ss_startGame(){
  ss_score=0;ss_qIdx=0;ss_lives=3;ss_answered=false;ss_pool=shuffle([...SHAPES_BASE]);
  document.getElementById('p2Score').textContent='0';
  document.getElementById('resultOverlay').classList.remove('active');
  ss_updateLives();
  const c=SS_COPY[lang];
  document.getElementById('p2Title').textContent=c.title;
  document.getElementById('p2Sub').textContent=c.sub;
  document.getElementById('p2QPrompt').textContent=c.prompt;
  document.getElementById('p2NextLbl').textContent=c.next;
  saveStart(ACT_IDS[2]);
  ss_loadQuestion();
}
function ss_updateLives(){document.querySelectorAll('#p2Lives .life-icon').forEach((ic,i)=>ic.classList.toggle('lost',i>=ss_lives));}
function ss_loadQuestion(){
  ss_answered=false;ss_selectedCard=null;ss_selectedShape=null;ss_selectedIsCorrect=false;
  document.getElementById('p2SubmitBtn').style.display='none';
  document.getElementById('p2NextBtn').style.display='none';
  document.getElementById('p2Feedback').textContent='';document.getElementById('p2Feedback').className='feedback-msg';
  if(!ss_pool.length)ss_pool=shuffle([...SHAPES_BASE]);
  ss_current=ss_pool.shift();
  document.getElementById('p2QNum').textContent=ss_qIdx+1;
  document.getElementById('p2Bar').style.width=((ss_qIdx/SS_TOTAL)*100)+'%';
  document.getElementById('p2BarLbl').textContent=`${ss_qIdx} / ${SS_TOTAL}`;
  document.getElementById('p2QName').textContent=sname(ss_current);
  document.getElementById('p2QName').style.setProperty('--q-color',ss_current.color);
  document.getElementById('p2QName').style.color=ss_current.color;
  const wrong=shuffle(SHAPES_BASE.filter(s=>s.en!==ss_current.en)).slice(0,3);
  const choices=shuffle([ss_current,...wrong]);
  const grid=document.getElementById('p2Choices');grid.innerHTML='';
  choices.forEach((s,i)=>{
    const isCorrect=s.en===ss_current.en;
    const card=document.createElement('div');card.className='choice-card';card.style.setProperty('--cc',s.color);
    card.innerHTML=`<svg viewBox="0 0 100 100"><path d="${s.path}" fill="${s.color}"/></svg>`;
    card.onclick=()=>ss_checkAnswer(card,s,isCorrect);grid.appendChild(card);
  });
  setTimeout(ss_speakShape,400);
}
function ss_speakShape(){if(ss_current)speak(sname(ss_current));}
let ss_selectedCard=null,ss_selectedShape=null,ss_selectedIsCorrect=false;
function ss_checkAnswer(card,shape,isCorrect){
  // Selection only — no checking yet
  if(ss_answered)return;
  document.querySelectorAll('#p2Choices .choice-card').forEach(c=>c.classList.remove('selected'));
  card.classList.add('selected');
  ss_selectedCard=card;ss_selectedShape=shape;ss_selectedIsCorrect=isCorrect;
  document.getElementById('p2SubmitBtn').style.display='inline-flex';
}
function ss_submitAnswer(){
  if(!ss_selectedCard||ss_answered)return;
  ss_answered=true;
  document.getElementById('p2SubmitBtn').style.display='none';
  document.querySelectorAll('#p2Choices .choice-card').forEach(c=>c.classList.add('answered'));
  const c=SS_COPY[lang];
  if(ss_selectedIsCorrect){
    ss_selectedCard.classList.add('correct');ss_score+=10;saveProgress(ACT_IDS[2],Math.round((ss_score/SS_MAX)*100));document.getElementById('p2Score').textContent=ss_score;
    document.getElementById('p2Feedback').textContent=c.feedbackCorrect(sname(ss_current));
    document.getElementById('p2Feedback').className='feedback-msg correct';
    confetti(ss_current.color);speak(sname(ss_current));ss_qIdx++;
    if(ss_qIdx>=SS_TOTAL||ss_lives<=0)setTimeout(ss_showResult,900);
    else document.getElementById('p2NextBtn').style.display='inline-flex';
  } else {
    ss_selectedCard.classList.add('wrong');
    document.querySelectorAll('#p2Choices .choice-card').forEach(cd=>{if(cd.querySelector('path')?.getAttribute('d')===ss_current.path)cd.classList.add('correct');});
    ss_lives--;ss_updateLives();
    document.getElementById('p2Feedback').textContent=c.feedbackWrong(sname(ss_selectedShape));
    document.getElementById('p2Feedback').className='feedback-msg wrong';
    ss_qIdx++;if(ss_lives<=0)setTimeout(ss_showResult,1100);else document.getElementById('p2NextBtn').style.display='inline-flex';
  }
}
function ss_nextQuestion(){ss_loadQuestion();}
function ss_showResult(){
  document.getElementById('resultTitle').textContent=SS_COPY[lang].resultTitle;
  document.getElementById('resultSub').textContent=SS_COPY[lang].resultSub;
  showResult(2,ss_score,SS_MAX);
}

// ════════════════════════════════════════
// quizzes 3: SHAPE SORTING
// ════════════════════════════════════════
const SO_COPY={en:{title:'Shape Sorting!',sub:'Does it have corners or not? Sort the shapes!',prompt:'Where does this shape go?',noLabel:'No Corners',noDesc:'Round shapes',yesLabel:'Has Corners',yesDesc:'Shapes with corners',feedbackCorrect:n=>`✅ Right! ${n} has no corners!`,feedbackCorrectCorners:n=>`✅ Right! ${n} has corners!`,feedbackWrong:'❌ Oops! Look carefully!',resultTitle:'Great Sorting!',resultSub:'You sorted all the shapes!'},tl:{title:'Pag-aayos ng Hugis!',sub:'May mga sulok ba o wala? Ayusin ang mga hugis!',prompt:'Saan mapupunta ang hugis na ito?',noLabel:'Walang Sulok',noDesc:'Bilugang hugis',yesLabel:'May Sulok',yesDesc:'Mga hugis na may sulok',feedbackCorrect:n=>`✅ Tama! Walang sulok ang ${n}!`,feedbackCorrectCorners:n=>`✅ Tama! May sulok ang ${n}!`,feedbackWrong:'❌ Mali! Tingnan muli!',resultTitle:'Napakahusay!',resultSub:'Nayos mo ang lahat ng hugis!'}};
const SO_TOTAL=10,SO_MAX=SO_TOTAL*10;
let so_pool=[],so_current=null,so_answered=false,so_score=0,so_qIdx=0;

function so_startGame(){
  so_score=0;so_qIdx=0;so_answered=false;so_pool=shuffle([...SHAPES_SORT]);
  document.getElementById('p3Score').textContent='0';
  document.getElementById('resultOverlay').classList.remove('active');
  document.getElementById('p3SortedNo').innerHTML='';
  document.getElementById('p3SortedYes').innerHTML='';
  const c=SO_COPY[lang];
  document.getElementById('p3Title').textContent=c.title;
  document.getElementById('p3Sub').textContent=c.sub;
  document.getElementById('p3SortPrompt').textContent=c.prompt;
  document.getElementById('p3NoLabel').textContent=c.noLabel;
  document.getElementById('p3NoDesc').textContent=c.noDesc;
  document.getElementById('p3YesLabel').textContent=c.yesLabel;
  document.getElementById('p3YesDesc').textContent=c.yesDesc;
  saveStart(ACT_IDS[3]);
  so_loadShape();
}
function so_loadShape(){
  so_answered=false;so_selectedHasCorners=null;
  document.getElementById('p3Feedback').textContent='';document.getElementById('p3Feedback').className='feedback-msg';
  document.getElementById('p3SubmitBtn').style.display='none';
  document.getElementById('p3NextBtn').style.display='none';
  document.getElementById('p3BucketNo').classList.remove('correct-pick','wrong-pick','disabled','bucket-selected');
  document.getElementById('p3BucketYes').classList.remove('correct-pick','wrong-pick','disabled','bucket-selected');
  if(!so_pool.length)so_pool=shuffle([...SHAPES_SORT]);
  so_current=so_pool.shift();
  document.getElementById('p3QNum').textContent=so_qIdx+1;
  document.getElementById('p3Bar').style.width=((so_qIdx/SO_TOTAL)*100)+'%';
  document.getElementById('p3BarLbl').textContent=`${so_qIdx} / ${SO_TOTAL}`;
  document.getElementById('p3SortSvg').innerHTML=`<path d="${so_current.path}" fill="${so_current.color}"/>`;
  document.getElementById('p3SortName').textContent=sname(so_current);
  document.getElementById('p3SortName').style.color=so_current.color;
  setTimeout(()=>speak(sname(so_current)),300);
}
let so_selectedHasCorners=null;
function so_pickBucket(hasCorners){
  // Selection only — no checking yet
  if(so_answered)return;
  so_selectedHasCorners=hasCorners;
  document.getElementById('p3BucketNo').classList.remove('bucket-selected');
  document.getElementById('p3BucketYes').classList.remove('bucket-selected');
  const selId=hasCorners?'p3BucketYes':'p3BucketNo';
  document.getElementById(selId).classList.add('bucket-selected');
  document.getElementById('p3SubmitBtn').style.display='inline-flex';
}
function so_submitAnswer(){
  if(so_selectedHasCorners===null||so_answered)return;
  so_answered=true;
  document.getElementById('p3SubmitBtn').style.display='none';
  document.getElementById('p3BucketNo').classList.add('disabled');
  document.getElementById('p3BucketYes').classList.add('disabled');
  const hasCorners=so_selectedHasCorners;
  const isCorrect=hasCorners===so_current.hasCorners;
  const bucketId=hasCorners?'p3BucketYes':'p3BucketNo';
  const sortedId=hasCorners?'p3SortedYes':'p3SortedNo';
  const c=SO_COPY[lang];const name=sname(so_current);
  if(isCorrect){
    document.getElementById(bucketId).classList.add('correct-pick');
    so_score+=10;saveProgress(ACT_IDS[3],Math.round((so_score/SO_MAX)*100));document.getElementById('p3Score').textContent=so_score;
    const msg=so_current.hasCorners?c.feedbackCorrectCorners(name):c.feedbackCorrect(name);
    document.getElementById('p3Feedback').textContent=msg;
    document.getElementById('p3Feedback').className='feedback-msg correct';
    const mini=document.createElement('div');mini.className='sorted-shape';
    mini.innerHTML=`<svg viewBox="0 0 100 100"><path d="${so_current.path}" fill="${so_current.color}"/></svg>`;
    document.getElementById(sortedId).appendChild(mini);
    confetti(so_current.color);speak(name);
  } else {
    document.getElementById(bucketId).classList.add('wrong-pick');
    document.getElementById('p3Feedback').textContent=c.feedbackWrong;
    document.getElementById('p3Feedback').className='feedback-msg wrong';
  }
  so_qIdx++;
  if(so_qIdx>=SO_TOTAL)setTimeout(so_showResult,1000);
  else document.getElementById('p3NextBtn').style.display='inline-flex';
}
function so_nextShape(){so_loadShape();}
function so_showResult(){
  document.getElementById('resultTitle').textContent=SO_COPY[lang].resultTitle;
  document.getElementById('resultSub').textContent=SO_COPY[lang].resultSub;
  showResult(3,so_score,SO_MAX);
}

// ════════════════════════════════════════
// quizzes 4: COUNT THE SIDES
// ════════════════════════════════════════
const CS_COPY={en:{title:'Count the Sides!',sub:'How many sides does this shape have?',prompt:'How many sides?',next:'Next Shape',feedbackCorrect:(n,s)=>s===0?`✅ Right! The ${n} is round — 0 sides!`:`✅ Right! The ${n} has ${s} side${s!==1?'s':''}!`,feedbackWrong:(n,s)=>s===0?`❌ The ${n} is round — 0 sides!`:`❌ The ${n} has ${s} sides!`,resultTitle:'Great Counting!',resultSub:'You counted all the sides!'},tl:{title:'Bilangin ang mga Gilid!',sub:'Ilang gilid ang hugis na ito?',prompt:'Ilang gilid?',next:'Susunod na Hugis',feedbackCorrect:(n,s)=>s===0?`✅ Tama! Ang ${n} ay bilog — 0 gilid!`:`✅ Tama! Ang ${n} ay may ${s} gilid!`,feedbackWrong:(n,s)=>s===0?`❌ Ang ${n} ay bilog — 0 gilid!`:`❌ Ang ${n} ay may ${s} gilid!`,resultTitle:'Mahusay na Pagbibilang!',resultSub:'Nabilang mo ang lahat ng gilid!'}};
const CS_TOTAL=10,CS_MAX=CS_TOTAL*10;
let cs_pool=[],cs_current=null,cs_answered=false,cs_score=0,cs_qIdx=0,cs_dotInterval=null;

function cs_startGame(){
  cs_score=0;cs_qIdx=0;cs_answered=false;cs_pool=shuffle([...SHAPES_SIDES]);
  document.getElementById('p4Score').textContent='0';
  document.getElementById('resultOverlay').classList.remove('active');
  const c=CS_COPY[lang];
  document.getElementById('p4Title').textContent=c.title;
  document.getElementById('p4Sub').textContent=c.sub;
  document.getElementById('p4StagePrompt').textContent=c.prompt;
  document.getElementById('p4NextLbl').textContent=c.next;
  saveStart(ACT_IDS[4]);
  cs_loadQuestion();
}
function cs_loadQuestion(){
  cs_answered=false;cs_selectedBtn=null;cs_selectedIsCorrect=false;
  document.getElementById('p4SubmitBtn').style.display='none';
  document.getElementById('p4NextBtn').style.display='none';
  document.getElementById('p4Feedback').textContent='';document.getElementById('p4Feedback').className='feedback-msg';
  if(!cs_pool.length)cs_pool=shuffle([...SHAPES_SIDES]);
  cs_current=cs_pool.shift();
  const name=sname(cs_current);
  document.getElementById('p4QNum').textContent=cs_qIdx+1;
  document.getElementById('p4Bar').style.width=((cs_qIdx/CS_TOTAL)*100)+'%';
  document.getElementById('p4BarLbl').textContent=`${cs_qIdx} / ${CS_TOTAL}`;
  document.getElementById('p4ShapeSvg').innerHTML=`<path d="${cs_current.path}" fill="${cs_current.color}"/>`;
  document.getElementById('p4ShapeName').textContent=name;
  document.getElementById('p4ShapeName').style.color=cs_current.color;
  document.getElementById('p4SideHint').textContent='';
  // Build count dots
  const dotsEl=document.getElementById('p4CountDots');dotsEl.innerHTML='';
  const count=Math.min(cs_current.sides,10);
  if(count===0){dotsEl.innerHTML='<span style="font-family:\'Fredoka One\',cursive;font-size:.85rem;color:#ddd;">●●●</span>';}
  else{for(let i=0;i<count;i++){const d=document.createElement('div');d.className='count-dot';d.style.background=cs_current.color;dotsEl.appendChild(d);}
    const dots=dotsEl.querySelectorAll('.count-dot');let i=0;
    if(cs_dotInterval)clearInterval(cs_dotInterval);
    cs_dotInterval=setInterval(()=>{if(i<dots.length){dots[i].classList.add('active');i++;}else clearInterval(cs_dotInterval);},300);}
  // Build number choices
  const correctSides=cs_current.sides;
  const choiceSet=SIDE_CHOICE_SETS[correctSides]||[correctSides,correctSides+1,Math.max(0,correctSides-1),correctSides+2];
  let choices=shuffle([...new Set(choiceSet)]).slice(0,4);
  if(!choices.includes(correctSides))choices[0]=correctSides;
  const el=document.getElementById('p4NumChoices');el.innerHTML='';
  shuffle(choices).forEach((num,i)=>{
    const isCorrect=num===correctSides;
    const cc=COLORS[i];
    const btn=document.createElement('button');btn.className='num-btn';btn.style.setProperty('--nb-color',cc);btn.style.color=cc;btn.style.borderColor=cc+'44';
    btn.textContent=num;btn.onclick=()=>cs_checkAnswer(btn,num,isCorrect);el.appendChild(btn);
  });
  setTimeout(()=>speak(name),300);
}
let cs_selectedBtn=null,cs_selectedIsCorrect=false;
function cs_checkAnswer(btn,chosen,isCorrect){
  // Selection only — no checking yet
  if(cs_answered)return;
  document.querySelectorAll('#p4NumChoices .num-btn').forEach(b=>b.classList.remove('selected'));
  btn.classList.add('selected');
  cs_selectedBtn=btn;cs_selectedIsCorrect=isCorrect;
  document.getElementById('p4SubmitBtn').style.display='inline-flex';
}
function cs_submitAnswer(){
  if(!cs_selectedBtn||cs_answered)return;
  cs_answered=true;
  document.getElementById('p4SubmitBtn').style.display='none';
  document.querySelectorAll('#p4NumChoices .num-btn').forEach(b=>b.classList.add('answered'));
  const name=sname(cs_current);
  const hint=lang==='en'?cs_current.sideHintEN:cs_current.sideHintTL;
  document.getElementById('p4SideHint').textContent=hint;
  const c=CS_COPY[lang];
  if(cs_selectedIsCorrect){
    cs_selectedBtn.classList.add('correct');cs_score+=10;saveProgress(ACT_IDS[4],Math.round((cs_score/CS_MAX)*100));document.getElementById('p4Score').textContent=cs_score;
    document.getElementById('p4Feedback').textContent=c.feedbackCorrect(name,cs_current.sides);
    document.getElementById('p4Feedback').className='feedback-msg correct';
    confetti(cs_current.color);speak(String(cs_current.sides));cs_qIdx++;
    if(cs_qIdx>=CS_TOTAL)setTimeout(cs_showResult,900);
    else document.getElementById('p4NextBtn').style.display='inline-flex';
  } else {
    cs_selectedBtn.classList.add('wrong');
    document.querySelectorAll('#p4NumChoices .num-btn').forEach(b=>{if(parseInt(b.textContent)===cs_current.sides)b.classList.add('correct');});
    document.getElementById('p4Feedback').textContent=c.feedbackWrong(name,cs_current.sides);
    document.getElementById('p4Feedback').className='feedback-msg wrong';
    cs_qIdx++;if(cs_qIdx>=CS_TOTAL)setTimeout(cs_showResult,1100);else document.getElementById('p4NextBtn').style.display='inline-flex';
  }
}
function cs_nextQuestion(){if(cs_dotInterval)clearInterval(cs_dotInterval);cs_loadQuestion();}
function cs_showResult(){
  document.getElementById('resultTitle').textContent=CS_COPY[lang].resultTitle;
  document.getElementById('resultSub').textContent=CS_COPY[lang].resultSub;
  showResult(4,cs_score,CS_MAX);
}


// ── FLUSH PROGRESS ON NAVIGATION / PAGE HIDE ──
function flushActiveTabProgress() {
  switch(activeTab) {
    case 1: if(sm_score>0) saveProgress(ACT_IDS[1],Math.min(100,Math.round((sm_score/SM_MAX)*100))); break;
    case 2: if(ss_score>0) saveProgress(ACT_IDS[2],Math.round((ss_score/SS_MAX)*100)); break;
    case 3: if(so_score>0) saveProgress(ACT_IDS[3],Math.round((so_score/SO_MAX)*100)); break;
    case 4: if(cs_score>0) saveProgress(ACT_IDS[4],Math.round((cs_score/CS_MAX)*100)); break;
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
if(activeTab===1)sm_startGame();
else if(activeTab===2)ss_startGame();
else if(activeTab===3)so_startGame();
else if(activeTab===4)cs_startGame();
</script>

</div><!-- /page-wrap -->
</body>
</html>