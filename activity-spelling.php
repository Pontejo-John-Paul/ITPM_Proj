<?php
session_start();
require_once 'database.php';
require_once 'activity_helper.php';

if (!isset($_SESSION['student_id']) || $_SESSION['role'] !== 'student') {
    header("Location: index.php");
    exit;
}

$student_id = $_SESSION['student_id'];
$lesson_id  = isset($_GET['lesson_id']) ? (int)$_GET['lesson_id'] : 2; // Spelling = 2

// ── AJAX ──
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $activity_id = (int)($_POST['activity_id'] ?? 0);
    if ($_POST['action'] === 'start') {
        activity_start($conn, $student_id, $activity_id);
        echo json_encode(['success' => true]);
        exit;
    }
    if ($_POST['action'] === 'progress') {
        $score      = (int)($_POST['score']      ?? 0);
        $checkpoint = (int)($_POST['checkpoint'] ?? 0);
        activity_save_progress($conn, $student_id, $activity_id, $score, $checkpoint);
        echo json_encode(['success' => true]);
        exit;
    }
    if ($_POST['action'] === 'complete') {
        $score = (int)($_POST['score'] ?? 0);
        activity_complete($conn, $student_id, $activity_id, $score);
        echo json_encode(['success' => true, 'score' => $score]);
        exit;
    }
}

// ── FETCH ACTIVITIES ──
$stmt = $conn->prepare("SELECT * FROM activities WHERE lesson_id = ? ORDER BY activity_id");
$stmt->bind_param("i", $lesson_id);
$stmt->execute();
$activities = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$act_map = [];
foreach ($activities as $act) {
    $prog = activity_get($conn, $student_id, $act['activity_id']);
    $act_map[$act['activity_name']] = [
        'activity_id' => $act['activity_id'],
        'status'      => $prog['status'],
        'score'       => $prog['score'],
    ];
}

$a1 = $act_map['listen_and_spell']  ?? ['activity_id'=>0,'status'=>'not_started','score'=>0];
$a2 = $act_map['word_scramble']     ?? ['activity_id'=>0,'status'=>'not_started','score'=>0];
$a3 = $act_map['rhyme_match']       ?? ['activity_id'=>0,'status'=>'not_started','score'=>0];
$a4 = $act_map['spell_the_picture'] ?? ['activity_id'=>0,'status'=>'not_started','score'=>0];

$active_tab = isset($_GET['tab']) ? max(1, min(4, (int)$_GET['tab'])) : 1;
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Spelling Activities — E-KINDER</title>
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
.activity-tabs{display:flex;gap:8px;padding:20px 0 0;overflow-x:auto;scrollbar-width:none;}
.activity-tabs::-webkit-scrollbar{display:none;}
.act-tab{flex-shrink:0;display:flex;align-items:center;gap:8px;padding:10px 18px;border-radius:var(--pill);border:2.5px solid #e8e8e8;background:#fff;font-family:'Fredoka One',cursive;font-size:.82rem;color:#aaa;cursor:pointer;transition:all .22s cubic-bezier(.34,1.56,.64,1);box-shadow:0 2px 8px rgba(0,0,0,.05);}
.act-tab:hover:not(.active){border-color:#c0e0ff;color:#555;transform:translateY(-2px);}
.act-tab.active{background:var(--green-dark);border-color:var(--green-dark);color:#fff;box-shadow:0 6px 18px rgba(26,46,26,.25);}
.tab-status{width:18px;height:18px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:.6rem;}
.act-tab.active .tab-status{background:rgba(255,255,255,.2);}
.tab-status.done{background:#40c057;color:#fff;}
.tab-status.progress{background:#fcc419;color:#fff;}
.tab-status.locked{background:#e8e8e8;color:#bbb;}
.tab-score{font-size:.7rem;opacity:.7;}

/* PANELS */
.activity-panel{display:none;}
.activity-panel.active{display:block;animation:panelIn .35s ease both;}
@keyframes panelIn{from{opacity:0;transform:translateY(14px)}to{opacity:1;transform:none}}

/* PAGE HEADER */
.page-header{text-align:center;padding:28px 0 20px;}
.activity-badge{display:inline-flex;align-items:center;gap:8px;background:#fff;border:1.5px solid #dde8dd;border-radius:var(--pill);padding:6px 18px;font-family:'Fredoka One',cursive;font-size:.78rem;color:#2a4a2a;box-shadow:0 2px 10px rgba(0,0,0,.06);margin-bottom:14px;}
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

/* GAME CARDS */
.game-card{background:#fff;border-radius:28px;padding:32px 28px;box-shadow:0 8px 36px rgba(0,0,0,.08);border:2px solid #f0ece4;max-width:560px;margin:0 auto 24px;text-align:center;}

/* ── ACT 1: LISTEN & SPELL ── */
.listen-prompt{font-family:'Fredoka One',cursive;font-size:1rem;color:#bbb;text-transform:uppercase;letter-spacing:1.5px;margin-bottom:20px;}
.listen-btn-big{width:110px;height:110px;border-radius:50%;border:none;cursor:pointer;display:flex;align-items:center;justify-content:center;margin:0 auto 20px;box-shadow:0 10px 32px rgba(51,154,240,.35);transition:transform .25s cubic-bezier(.34,1.56,.64,1),box-shadow .25s;}
.listen-btn-big:hover{transform:scale(1.1);}
.listen-btn-big.speaking{animation:speakPulse 1s ease-in-out infinite;}
@keyframes speakPulse{0%,100%{transform:scale(1)}50%{transform:scale(1.12)}}
.listen-btn-big i{font-size:2.8rem;color:#fff;}
.listen-word-hint{font-family:'Fredoka One',cursive;font-size:.88rem;color:#ddd;margin-bottom:24px;letter-spacing:2px;}
.letter-slots{display:flex;justify-content:center;gap:10px;margin-bottom:24px;flex-wrap:wrap;}
.letter-slot{width:56px;height:64px;border-radius:14px;background:#f8f8f8;border:3px solid #e8e8e8;display:flex;align-items:center;justify-content:center;font-family:'Fredoka One',cursive;font-size:1.8rem;transition:all .25s cubic-bezier(.34,1.56,.64,1);}
.letter-slot.filled{background:#e7f5ff;border-color:#339af0;}
.letter-slot.correct{background:#d3f9d8;border-color:#40c057;color:#2f9e44;animation:slotPop .35s cubic-bezier(.34,1.56,.64,1);}
.letter-slot.wrong{background:#ffe3e3;border-color:#ff4444;color:#ff4444;animation:slotShake .4s ease;}
.choices-label{font-family:'Fredoka One',cursive;font-size:.75rem;color:#bbb;text-transform:uppercase;letter-spacing:1px;margin-bottom:12px;}
.choices-row{display:flex;flex-wrap:wrap;justify-content:center;gap:10px;margin-bottom:20px;}
.choice-btn{width:56px;height:64px;border-radius:14px;background:#fff;border:3px solid #e8e8e8;font-family:'Fredoka One',cursive;font-size:1.7rem;cursor:pointer;box-shadow:0 4px 12px rgba(0,0,0,.07);transition:all .2s cubic-bezier(.34,1.56,.64,1);}
.choice-btn:hover:not(.used):not(.disabled){border-color:#339af0;background:#e7f5ff;color:#339af0;transform:translateY(-4px) scale(1.1);}
.choice-btn.used{opacity:.2;transform:scale(.9);pointer-events:none;}
.choice-btn.disabled{pointer-events:none;}

/* ── ACT 2: WORD SCRAMBLE ── */
.word-image-wrap{width:130px;height:130px;border-radius:20px;overflow:hidden;margin:0 auto 20px;border:3px solid var(--cc,#e8a000);box-shadow:0 8px 24px rgba(0,0,0,.1);}
.word-image-wrap img{width:100%;height:100%;object-fit:cover;}
.word-image-placeholder{width:100%;height:100%;display:flex;align-items:center;justify-content:center;background:#f8f8f8;font-size:3rem;}
.scramble-label{font-family:'Fredoka One',cursive;font-size:.75rem;color:#bbb;text-transform:uppercase;letter-spacing:1px;text-align:center;margin-bottom:12px;}
.scramble-row{display:flex;justify-content:center;gap:10px;flex-wrap:wrap;margin-bottom:20px;}
.scramble-tile{width:52px;height:60px;border-radius:14px;background:#fff;border:3px solid var(--cc,#e8a000);font-family:'Fredoka One',cursive;font-size:1.7rem;color:var(--cc,#e8a000);cursor:pointer;box-shadow:0 4px 14px rgba(0,0,0,.08);transition:all .2s cubic-bezier(.34,1.56,.64,1);display:flex;align-items:center;justify-content:center;animation:tileIn .4s ease both;}
.scramble-tile:hover:not(.used){transform:translateY(-6px) scale(1.12);box-shadow:0 10px 24px rgba(0,0,0,.14);}
.scramble-tile.used{opacity:.15;transform:scale(.85);pointer-events:none;}
.scramble-tile:nth-child(1){animation-delay:.04s}.scramble-tile:nth-child(2){animation-delay:.08s}
.scramble-tile:nth-child(3){animation-delay:.12s}.scramble-tile:nth-child(4){animation-delay:.16s}
.scramble-tile:nth-child(5){animation-delay:.20s}.scramble-tile:nth-child(6){animation-delay:.24s}
.scramble-tile:nth-child(7){animation-delay:.28s}

/* ── ACT 3: RHYME MATCH ── */
.prompt-card{background:#fff;border-radius:24px;padding:28px 24px;box-shadow:0 6px 24px rgba(0,0,0,.08);border:2px solid #f0ece4;text-align:center;margin-bottom:24px;}
.prompt-label{font-family:'Fredoka One',cursive;font-size:.85rem;color:#bbb;text-transform:uppercase;letter-spacing:1px;margin-bottom:14px;}
.prompt-img{width:100px;height:100px;border-radius:50%;overflow:hidden;margin:0 auto 12px;border:4px solid var(--pc,#cc5de8);box-shadow:0 6px 20px rgba(0,0,0,.1);animation:floatImg 2.5s ease-in-out infinite;}
.prompt-img img{width:100%;height:100%;object-fit:cover;}
@keyframes floatImg{0%,100%{transform:translateY(0)}50%{transform:translateY(-10px)}}
.prompt-word-disp{font-family:'Fredoka One',cursive;font-size:2.2rem;margin-bottom:6px;}
.prompt-ending{font-size:.82rem;font-weight:800;color:#bbb;letter-spacing:1px;text-transform:uppercase;}
.prompt-speak-btn{background:none;border:none;font-size:1.3rem;cursor:pointer;margin-top:8px;transition:transform .2s;}
.prompt-speak-btn:hover{transform:scale(1.2);}
.choices-label-rhyme{font-family:'Fredoka One',cursive;font-size:.78rem;color:#bbb;text-transform:uppercase;letter-spacing:1px;text-align:center;margin-bottom:14px;}
.rhyme-choices-grid{display:grid;grid-template-columns:repeat(2,1fr);gap:14px;margin-bottom:24px;}
@media(min-width:500px){.rhyme-choices-grid{grid-template-columns:repeat(4,1fr);}}
.rhyme-choice-card{background:#fff;border-radius:18px;padding:16px 8px;text-align:center;cursor:pointer;border:3px solid #f0f0f0;box-shadow:0 4px 14px rgba(0,0,0,.06);transition:all .25s cubic-bezier(.34,1.56,.64,1);animation:cardIn .4s ease both;}
.rhyme-choice-card:hover:not(.answered){transform:translateY(-6px) scale(1.05);box-shadow:0 12px 28px rgba(0,0,0,.12);border-color:var(--cc,#cc5de8);}
.rhyme-choice-card.correct{border-color:#40c057!important;background:#d3f9d8!important;animation:popGreen .4s cubic-bezier(.34,1.56,.64,1);}
.rhyme-choice-card.wrong{border-color:#ff4444!important;background:#ffe3e3!important;animation:cardShake .4s ease;}
.rhyme-choice-card.answered{cursor:default;}
.rhyme-choice-card:nth-child(1){animation-delay:.04s}.rhyme-choice-card:nth-child(2){animation-delay:.08s}
.rhyme-choice-card:nth-child(3){animation-delay:.12s}.rhyme-choice-card:nth-child(4){animation-delay:.16s}
.choice-img{width:56px;height:56px;border-radius:50%;overflow:hidden;margin:0 auto 8px;background:#f8f8f8;}
.choice-img img{width:100%;height:100%;object-fit:cover;}
.choice-word-text{font-family:'Fredoka One',cursive;font-size:1.05rem;color:#2d2d2d;}
.choice-ending-text{font-size:.65rem;font-weight:800;color:#bbb;letter-spacing:.8px;text-transform:uppercase;margin-top:2px;}

/* ── ACT 4: SPELL THE PICTURE ── */
.big-pic{width:160px;height:160px;border-radius:24px;overflow:hidden;margin:0 auto 20px;border:4px solid var(--cc,#ff6b2b);box-shadow:0 10px 32px rgba(0,0,0,.12);animation:picFloat 2.5s ease-in-out infinite;}
.big-pic img{width:100%;height:100%;object-fit:cover;}
@keyframes picFloat{0%,100%{transform:translateY(0) rotate(-1deg)}50%{transform:translateY(-12px) rotate(1deg)}}
.letter-count{display:flex;justify-content:center;gap:6px;margin-bottom:20px;}
.count-dot{width:10px;height:10px;border-radius:50%;background:color-mix(in srgb,var(--cc,#ff6b2b) 25%,white);border:2px solid var(--cc,#ff6b2b);transition:background .3s;}
.count-dot.filled{background:var(--cc,#ff6b2b);}
.kb-label{font-family:'Fredoka One',cursive;font-size:.75rem;color:#bbb;text-transform:uppercase;letter-spacing:1px;margin-bottom:12px;}
.keyboard{display:flex;flex-wrap:wrap;justify-content:center;gap:8px;margin-bottom:20px;max-width:480px;margin-left:auto;margin-right:auto;}
.kb-key{width:46px;height:52px;border-radius:12px;background:#fff;border:2.5px solid #e8e8e8;font-family:'Fredoka One',cursive;font-size:1.4rem;color:#555;cursor:pointer;box-shadow:0 3px 10px rgba(0,0,0,.06);transition:all .2s cubic-bezier(.34,1.56,.64,1);display:flex;align-items:center;justify-content:center;}
.kb-key:hover:not(.disabled){border-color:var(--cc,#ff6b2b);color:var(--cc,#ff6b2b);transform:translateY(-3px) scale(1.08);box-shadow:0 7px 18px rgba(0,0,0,.12);}
.kb-key.disabled{opacity:.35;pointer-events:none;}
.hint-btn{background:none;border:2px dashed #e8e8e8;border-radius:var(--pill);padding:7px 18px;font-family:'Fredoka One',cursive;font-size:.8rem;color:#bbb;cursor:pointer;transition:all .2s;margin-bottom:14px;}
.hint-btn:hover{border-color:var(--cc,#ff6b2b);color:var(--cc,#ff6b2b);}

/* SHARED ANSWER SLOTS */
.answer-slots{display:flex;justify-content:center;gap:8px;margin-bottom:20px;flex-wrap:wrap;}
.answer-slot{width:52px;height:60px;border-radius:14px;background:#f8f8f8;border:3px solid #e8e8e8;display:flex;align-items:center;justify-content:center;font-family:'Fredoka One',cursive;font-size:1.7rem;color:var(--cc,#e8a000);cursor:pointer;transition:all .25s cubic-bezier(.34,1.56,.64,1);}
.answer-slot.filled{background:color-mix(in srgb,var(--cc,#e8a000) 10%,white);border-color:var(--cc,#e8a000);}
.answer-slot.correct{background:#d3f9d8!important;border-color:#40c057!important;color:#2f9e44!important;animation:slotPop .4s cubic-bezier(.34,1.56,.64,1);}
.answer-slot.wrong{background:#ffe3e3!important;border-color:#ff4444!important;color:#ff4444!important;animation:slotShake .4s ease;}

/* SHARED ANIMS */
@keyframes slotPop{0%{transform:scale(.8)}60%{transform:scale(1.15)}100%{transform:scale(1)}}
@keyframes slotShake{0%,100%{transform:translateX(0)}25%{transform:translateX(-6px)}75%{transform:translateX(6px)}}
@keyframes tileIn{from{opacity:0;transform:scale(.6) rotate(-10deg)}to{opacity:1;transform:scale(1) rotate(0)}}
@keyframes popGreen{0%{transform:scale(.9)}60%{transform:scale(1.1)}100%{transform:scale(1)}}
@keyframes cardShake{0%,100%{transform:translateX(0)}25%{transform:translateX(-6px)}75%{transform:translateX(6px)}}
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
.next-activity-btn{width:100%;margin-top:8px;background:linear-gradient(135deg,#339af0,#cc5de8);color:#fff;border:none;border-radius:var(--pill);padding:12px 24px;font-family:'Fredoka One',cursive;font-size:.95rem;cursor:pointer;box-shadow:0 6px 18px rgba(0,0,0,.15);transition:transform .2s;display:flex;align-items:center;justify-content:center;gap:8px;}
.next-activity-btn:hover{transform:scale(1.04);}
.cfbit{position:fixed;pointer-events:none;z-index:1000;animation:cfFall linear forwards;}
@keyframes cfFall{0%{transform:translateY(-16px) rotate(0deg);opacity:1}100%{transform:translateY(105vh) rotate(700deg);opacity:0}}
</style>
</head>
<body>
<div class="page-wrap">

<nav class="lesson-nav">
  <a href="activities.php" class="lnav-back"><i class="fas fa-arrow-left"></i> Back</a>
  <div class="lnav-title">📝 Spelling Activities</div>
  <div class="lang-toggle">
    <button class="lang-btn active" id="btnEN" onclick="setLang('en')">🇺🇸 EN</button>
    <button class="lang-btn" id="btnTL" onclick="setLang('tl')">🇵🇭 TL</button>
  </div>
</nav>

<div class="container">

  <!-- TABS -->
  <div class="activity-tabs">
    <?php
    $tabs = [
      1 => ['icon'=>'fas fa-headphones', 'en'=>'Listen',   'tl'=>'Makinig',  'act'=>$a1],
      2 => ['icon'=>'fas fa-random',     'en'=>'Scramble', 'tl'=>'Ayusin',   'act'=>$a2],
      3 => ['icon'=>'fas fa-music',      'en'=>'Rhyme',    'tl'=>'Tugma',    'act'=>$a3],
      4 => ['icon'=>'fas fa-image',      'en'=>'Picture',  'tl'=>'Larawan',  'act'=>$a4],
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

  <!-- ══════════════════ PANEL 1: LISTEN & SPELL ══════════════════ -->
  <div class="activity-panel <?php echo $active_tab===1?'active':'';?>" id="panel1">
    <div class="page-header">
      <div class="activity-badge"><i class="fas fa-headphones"></i> Activity 1 of 4</div>
      <div class="page-title" id="p1Title">Listen &amp; Spell!</div>
      <div class="page-sub" id="p1Sub">Listen to the word, then spell it out!</div>
      <?php if($a1['score']>0):?><div class="best-score-badge">⭐ Best Score: <?php echo $a1['score'];?>%</div><?php endif;?>
    </div>
    <div class="score-bar">
      <div class="score-item"><div class="score-num" id="p1Score">0</div><div class="score-label">Score</div></div>
      <div class="score-sep"></div>
      <div class="score-item"><div class="score-num" id="p1QNum">1</div><div class="score-label">Word</div></div>
      <div class="score-sep"></div>
      <div class="score-item"><div class="lives-wrap" id="p1Lives"><span class="life-icon">❤️</span><span class="life-icon">❤️</span><span class="life-icon">❤️</span></div><div class="score-label">Lives</div></div>
      <div class="score-sep"></div>
      <div class="progress-wrap">
        <div class="progress-bar-outer"><div class="progress-bar-inner" id="p1Bar" style="width:0%;background:linear-gradient(90deg,#339af0,#1971c2)"></div></div>
        <div class="progress-label" id="p1BarLbl">0 / 10</div>
      </div>
    </div>
    <div class="game-card">
      <div class="listen-prompt" id="p1ListenPrompt">Press to hear the word:</div>
      <button class="listen-btn-big" id="p1ListenBtn" style="background:linear-gradient(135deg,#339af0,#1971c2)" onclick="ls_speakWord()">
        <i class="fas fa-volume-up"></i>
      </button>
      <div class="listen-word-hint" id="p1WordHint">? ? ?</div>
      <div class="letter-slots" id="p1LetterSlots"></div>
      <div class="choices-label" id="p1ChoicesLbl">Tap the letters in order:</div>
      <div class="choices-row" id="p1ChoicesRow"></div>
      <div class="feedback-msg" id="p1Feedback"></div>
      <div class="controls">
        <button class="btn-action btn-secondary" id="p1NextBtn" onclick="ls_nextWord()" style="display:none"><i class="fas fa-arrow-right"></i> <span id="p1NextLbl">Next Word</span></button>
      </div>
    </div>
  </div>

  <!-- ══════════════════ PANEL 2: WORD SCRAMBLE ══════════════════ -->
  <div class="activity-panel <?php echo $active_tab===2?'active':'';?>" id="panel2">
    <div class="page-header">
      <div class="activity-badge"><i class="fas fa-random"></i> Activity 2 of 4</div>
      <div class="page-title" id="p2Title">Word Scramble!</div>
      <div class="page-sub" id="p2Sub">Arrange the mixed-up letters to form the correct word!</div>
      <?php if($a2['score']>0):?><div class="best-score-badge">⭐ Best Score: <?php echo $a2['score'];?>%</div><?php endif;?>
    </div>
    <div class="score-bar">
      <div class="score-item"><div class="score-num" id="p2Score">0</div><div class="score-label">Score</div></div>
      <div class="score-sep"></div>
      <div class="score-item"><div class="score-num" id="p2QNum">1</div><div class="score-label">Word</div></div>
      <div class="score-sep"></div>
      <div class="progress-wrap">
        <div class="progress-bar-outer"><div class="progress-bar-inner" id="p2Bar" style="width:0%;background:linear-gradient(90deg,#e8a000,#f59f00)"></div></div>
        <div class="progress-label" id="p2BarLbl">0 / 10</div>
      </div>
    </div>
    <div class="game-card" id="p2Card">
      <div class="word-image-wrap" id="p2ImgWrap"><img id="p2Img" src="" alt="" onerror="this.closest('.word-image-wrap').innerHTML='<div class=\'word-image-placeholder\'>🖼️</div>'"></div>
      <div class="answer-slots" id="p2Slots"></div>
      <div class="scramble-label" id="p2ScrambleLbl">Arrange these letters:</div>
      <div class="scramble-row" id="p2ScrambleRow"></div>
      <div class="feedback-msg" id="p2Feedback"></div>
      <div class="controls">
        <button class="btn-action btn-secondary" onclick="ws_clearAnswer()"><i class="fas fa-eraser"></i> <span id="p2ClearLbl">Clear</span></button>
        <button class="btn-action btn-secondary" id="p2NextBtn" onclick="ws_nextWord()" style="display:none"><i class="fas fa-arrow-right"></i> <span id="p2NextLbl">Next</span></button>
      </div>
    </div>
  </div>

  <!-- ══════════════════ PANEL 3: RHYME MATCH ══════════════════ -->
  <div class="activity-panel <?php echo $active_tab===3?'active':'';?>" id="panel3">
    <div class="page-header">
      <div class="activity-badge"><i class="fas fa-music"></i> Activity 3 of 4</div>
      <div class="page-title" id="p3Title">Rhyme Match!</div>
      <div class="page-sub" id="p3Sub">Find the word that rhymes with the picture shown!</div>
      <?php if($a3['score']>0):?><div class="best-score-badge">⭐ Best Score: <?php echo $a3['score'];?>%</div><?php endif;?>
    </div>
    <div class="score-bar">
      <div class="score-item"><div class="score-num" id="p3Score">0</div><div class="score-label">Score</div></div>
      <div class="score-sep"></div>
      <div class="score-item"><div class="score-num" id="p3QNum">1</div><div class="score-label">Question</div></div>
      <div class="score-sep"></div>
      <div class="progress-wrap">
        <div class="progress-bar-outer"><div class="progress-bar-inner" id="p3Bar" style="width:0%;background:linear-gradient(90deg,#cc5de8,#845EC2)"></div></div>
        <div class="progress-label" id="p3BarLbl">0 / 12</div>
      </div>
    </div>
    <div class="prompt-card" id="p3PromptCard">
      <div class="prompt-label" id="p3PromptLabel">Which word rhymes with:</div>
      <div class="prompt-img" id="p3PromptImgWrap"><img id="p3PromptImg" src="" alt="" onerror="this.closest('.prompt-img').innerHTML='<div style=\'text-align:center;font-size:2.5rem;line-height:100px;\'>🖼️</div>'"></div>
      <div class="prompt-word-disp" id="p3PromptWord">CAT</div>
      <div class="prompt-ending" id="p3PromptEnding">ends in: -AT</div>
      <button class="prompt-speak-btn" id="p3SpeakBtn" onclick="rm_speakPrompt()"><i class="fas fa-volume-up"></i></button>
    </div>
    <div class="choices-label-rhyme" id="p3ChoicesLabel">Pick the rhyming word:</div>
    <div class="rhyme-choices-grid" id="p3RhymeGrid"></div>
    <div class="feedback-msg" id="p3Feedback"></div>
    <div class="controls">
      <button class="btn-action btn-secondary" id="p3NextBtn" onclick="rm_nextQuestion()" style="display:none"><i class="fas fa-arrow-right"></i> <span id="p3NextLbl">Next</span></button>
    </div>
  </div>

  <!-- ══════════════════ PANEL 4: SPELL THE PICTURE ══════════════════ -->
  <div class="activity-panel <?php echo $active_tab===4?'active':'';?>" id="panel4">
    <div class="page-header">
      <div class="activity-badge"><i class="fas fa-image"></i> Activity 4 of 4</div>
      <div class="page-title" id="p4Title">Spell the Picture!</div>
      <div class="page-sub" id="p4Sub">Look at the picture and spell the word!</div>
      <?php if($a4['score']>0):?><div class="best-score-badge">⭐ Best Score: <?php echo $a4['score'];?>%</div><?php endif;?>
    </div>
    <div class="score-bar">
      <div class="score-item"><div class="score-num" id="p4Score">0</div><div class="score-label">Score</div></div>
      <div class="score-sep"></div>
      <div class="score-item"><div class="score-num" id="p4QNum">1</div><div class="score-label">Word</div></div>
      <div class="score-sep"></div>
      <div class="progress-wrap">
        <div class="progress-bar-outer"><div class="progress-bar-inner" id="p4Bar" style="width:0%;background:linear-gradient(90deg,#ff6b2b,#ff922b)"></div></div>
        <div class="progress-label" id="p4BarLbl">0 / 12</div>
      </div>
    </div>
    <div class="game-card" id="p4Card">
      <div class="big-pic" id="p4BigPic"><img id="p4WordImg" src="" alt="" onerror="this.closest('.big-pic').innerHTML='<div style=\'display:flex;align-items:center;justify-content:center;height:100%;font-size:4rem;\'>🖼️</div>'"></div>
      <div class="letter-count" id="p4LetterCount"></div>
      <div class="answer-slots" id="p4Slots"></div>
      <div class="kb-label" id="p4KbLabel">Tap the letters to spell:</div>
      <div class="keyboard" id="p4Keyboard"></div>
      <div class="feedback-msg" id="p4Feedback"></div>
      <button class="hint-btn" id="p4HintBtn" onclick="sp_showHint()"><i class="fas fa-lightbulb"></i> <span id="p4HintBtnTxt">Hint (show first letter)</span></button>
      <div class="controls">
        <button class="btn-action btn-secondary" onclick="sp_clearAnswer()"><i class="fas fa-eraser"></i> <span id="p4ClearLbl">Clear</span></button>
        <button class="btn-action btn-secondary" id="p4NextBtn" onclick="sp_nextWord()" style="display:none"><i class="fas fa-arrow-right"></i> <span id="p4NextLbl">Next</span></button>
      </div>
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
      <button class="next-activity-btn" id="nextActBtn" style="display:none" onclick="goNextActivity()">
        <i class="fas fa-arrow-right"></i> Next Activity
      </button>
    </div>
  </div>
</div>

<script>
// ── PHP DATA ──
const ACT_IDS   = {1:<?php echo(int)$a1['activity_id'];?>,2:<?php echo(int)$a2['activity_id'];?>,3:<?php echo(int)$a3['activity_id'];?>,4:<?php echo(int)$a4['activity_id'];?>};
const PREV_SCORES={1:<?php echo(int)$a1['score'];?>,2:<?php echo(int)$a2['score'];?>,3:<?php echo(int)$a3['score'];?>,4:<?php echo(int)$a4['score'];?>};
const CURRENT_URL=window.location.href.split('?')[0];
let lang='en', activeTab=<?php echo(int)$active_tab;?>;

// ── SHARED DATA ──
const SPELL_WORDS={
  en:['cat','dog','sun','hat','fish','ball','frog','cake','moon','star','tree','duck','milk','bird','rice'],
  tl:['pusa','aso','araw','bola','isda','puno','pato','keyk','buwan','bituin','gatas','ibon','kanin','bahay','mangga']
};
const PIC_WORDS={
  en:[{word:'cat',img:'pictures/spelling/en/cat.png'},{word:'dog',img:'pictures/spelling/en/dog.png'},{word:'sun',img:'pictures/spelling/en/sun.png'},{word:'hat',img:'pictures/spelling/en/hat.png'},{word:'fish',img:'pictures/spelling/en/fish.png'},{word:'ball',img:'pictures/spelling/en/ball.png'},{word:'frog',img:'pictures/spelling/en/frog.png'},{word:'cake',img:'pictures/spelling/en/cake.png'},{word:'moon',img:'pictures/spelling/en/moon.png'},{word:'star',img:'pictures/spelling/en/star.png'},{word:'tree',img:'pictures/spelling/en/tree.png'},{word:'duck',img:'pictures/spelling/en/duck.png'},{word:'milk',img:'pictures/spelling/en/milk.png'},{word:'bird',img:'pictures/spelling/en/bird.png'},{word:'rice',img:'pictures/spelling/en/rice.png'}],
  tl:[{word:'pusa',img:'pictures/spelling/tl/pusa.png'},{word:'aso',img:'pictures/spelling/tl/aso.png'},{word:'araw',img:'pictures/spelling/tl/araw.png'},{word:'bola',img:'pictures/spelling/tl/bola.png'},{word:'isda',img:'pictures/spelling/tl/isda.png'},{word:'puno',img:'pictures/spelling/tl/puno.png'},{word:'pato',img:'pictures/spelling/tl/pato.png'},{word:'keyk',img:'pictures/spelling/tl/keyk.png'},{word:'buwan',img:'pictures/spelling/tl/buwan.png'},{word:'bituin',img:'pictures/spelling/tl/bituin.png'},{word:'gatas',img:'pictures/spelling/tl/gatas.png'},{word:'ibon',img:'pictures/spelling/tl/ibon.png'},{word:'kanin',img:'pictures/spelling/tl/kanin.png'},{word:'mangga',img:'pictures/spelling/tl/mangga.png'},{word:'saging',img:'pictures/spelling/tl/saging.png'}]
};
const RHYMES={
  en:[
    {prompt:{word:'cat',ending:'AT',img:'pictures/spelling/en/rhyme/cat.png'},correct:{word:'hat',img:'pictures/spelling/en/rhyme/hat.png'},wrong:[{word:'dog',img:'pictures/spelling/en/rhyme/dog.png'},{word:'sun',img:'pictures/spelling/en/rhyme/sun.png'},{word:'fish',img:'pictures/spelling/en/rhyme/fish.png'}]},
    {prompt:{word:'frog',ending:'OG',img:'pictures/spelling/en/rhyme/frog.png'},correct:{word:'dog',img:'pictures/spelling/en/rhyme/dog.png'},wrong:[{word:'cat',img:'pictures/spelling/en/rhyme/cat.png'},{word:'ball',img:'pictures/spelling/en/rhyme/ball.png'},{word:'cake',img:'pictures/spelling/en/rhyme/cake.png'}]},
    {prompt:{word:'sun',ending:'UN',img:'pictures/spelling/en/rhyme/sun.png'},correct:{word:'bun',img:'pictures/spelling/en/rhyme/bun.png'},wrong:[{word:'hat',img:'pictures/spelling/en/rhyme/hat.png'},{word:'book',img:'pictures/spelling/en/rhyme/book.png'},{word:'dog',img:'pictures/spelling/en/rhyme/dog.png'}]},
    {prompt:{word:'book',ending:'OOK',img:'pictures/spelling/en/rhyme/book.png'},correct:{word:'cook',img:'pictures/spelling/en/rhyme/cook.png'},wrong:[{word:'sun',img:'pictures/spelling/en/rhyme/sun.png'},{word:'cat',img:'pictures/spelling/en/rhyme/cat.png'},{word:'fall',img:'pictures/spelling/en/rhyme/fall.png'}]},
    {prompt:{word:'ball',ending:'ALL',img:'pictures/spelling/en/rhyme/ball.png'},correct:{word:'fall',img:'pictures/spelling/en/rhyme/fall.png'},wrong:[{word:'cake',img:'pictures/spelling/en/rhyme/cake.png'},{word:'frog',img:'pictures/spelling/en/rhyme/frog.png'},{word:'bun',img:'pictures/spelling/en/rhyme/bun.png'}]},
    {prompt:{word:'cake',ending:'AKE',img:'pictures/spelling/en/rhyme/cake.png'},correct:{word:'lake',img:'pictures/spelling/en/rhyme/lake.png'},wrong:[{word:'ball',img:'pictures/spelling/en/rhyme/ball.png'},{word:'book',img:'pictures/spelling/en/rhyme/book.png'},{word:'hat',img:'pictures/spelling/en/rhyme/hat.png'}]},
    {prompt:{word:'hat',ending:'AT',img:'pictures/spelling/en/rhyme/hat.png'},correct:{word:'bat',img:'pictures/spelling/en/rhyme/bat.png'},wrong:[{word:'frog',img:'pictures/spelling/en/rhyme/frog.png'},{word:'cake',img:'pictures/spelling/en/rhyme/cake.png'},{word:'cook',img:'pictures/spelling/en/rhyme/cook.png'}]},
    {prompt:{word:'look',ending:'OOK',img:'pictures/spelling/en/rhyme/look.png'},correct:{word:'hook',img:'pictures/spelling/en/rhyme/hook.png'},wrong:[{word:'sun',img:'pictures/spelling/en/rhyme/sun.png'},{word:'fall',img:'pictures/spelling/en/rhyme/fall.png'},{word:'dog',img:'pictures/spelling/en/rhyme/dog.png'}]},
    {prompt:{word:'tall',ending:'ALL',img:'pictures/spelling/en/rhyme/tall.png'},correct:{word:'call',img:'pictures/spelling/en/rhyme/call.png'},wrong:[{word:'cake',img:'pictures/spelling/en/rhyme/cake.png'},{word:'bun',img:'pictures/spelling/en/rhyme/bun.png'},{word:'cat',img:'pictures/spelling/en/rhyme/cat.png'}]},
    {prompt:{word:'bake',ending:'AKE',img:'pictures/spelling/en/rhyme/bake.png'},correct:{word:'rake',img:'pictures/spelling/en/rhyme/rake.png'},wrong:[{word:'ball',img:'pictures/spelling/en/rhyme/ball.png'},{word:'hook',img:'pictures/spelling/en/rhyme/hook.png'},{word:'frog',img:'pictures/spelling/en/rhyme/frog.png'}]},
    {prompt:{word:'run',ending:'UN',img:'pictures/spelling/en/rhyme/run.png'},correct:{word:'fun',img:'pictures/spelling/en/rhyme/fun.png'},wrong:[{word:'hat',img:'pictures/spelling/en/rhyme/hat.png'},{word:'lake',img:'pictures/spelling/en/rhyme/lake.png'},{word:'book',img:'pictures/spelling/en/rhyme/book.png'}]},
    {prompt:{word:'log',ending:'OG',img:'pictures/spelling/en/rhyme/log.png'},correct:{word:'fog',img:'pictures/spelling/en/rhyme/fog.png'},wrong:[{word:'sun',img:'pictures/spelling/en/rhyme/sun.png'},{word:'call',img:'pictures/spelling/en/rhyme/call.png'},{word:'bake',img:'pictures/spelling/en/rhyme/bake.png'}]},
  ],
  tl:[
    {prompt:{word:'basa',ending:'ASA',img:'pictures/spelling/tl/rhyme/basa.png'},correct:{word:'tasa',img:'pictures/spelling/tl/rhyme/tasa.png'},wrong:[{word:'pala',img:'pictures/spelling/tl/rhyme/pala.png'},{word:'bote',img:'pictures/spelling/tl/rhyme/bote.png'},{word:'mina',img:'pictures/spelling/tl/rhyme/mina.png'}]},
    {prompt:{word:'bala',ending:'ALA',img:'pictures/spelling/tl/rhyme/bala.png'},correct:{word:'sala',img:'pictures/spelling/tl/rhyme/sala.png'},wrong:[{word:'tasa',img:'pictures/spelling/tl/rhyme/tasa.png'},{word:'bote',img:'pictures/spelling/tl/rhyme/bote.png'},{word:'buga',img:'pictures/spelling/tl/rhyme/buga.png'}]},
    {prompt:{word:'bote',ending:'OTE',img:'pictures/spelling/tl/rhyme/bote.png'},correct:{word:'note',img:'pictures/spelling/tl/rhyme/note.png'},wrong:[{word:'basa',img:'pictures/spelling/tl/rhyme/basa.png'},{word:'tala',img:'pictures/spelling/tl/rhyme/tala.png'},{word:'hina',img:'pictures/spelling/tl/rhyme/hina.png'}]},
    {prompt:{word:'hina',ending:'INA',img:'pictures/spelling/tl/rhyme/hina.png'},correct:{word:'mina',img:'pictures/spelling/tl/rhyme/mina.png'},wrong:[{word:'note',img:'pictures/spelling/tl/rhyme/note.png'},{word:'sala',img:'pictures/spelling/tl/rhyme/sala.png'},{word:'labo',img:'pictures/spelling/tl/rhyme/labo.png'}]},
    {prompt:{word:'buga',ending:'UGA',img:'pictures/spelling/tl/rhyme/buga.png'},correct:{word:'luga',img:'pictures/spelling/tl/rhyme/luga.png'},wrong:[{word:'mina',img:'pictures/spelling/tl/rhyme/mina.png'},{word:'tasa',img:'pictures/spelling/tl/rhyme/tasa.png'},{word:'tabo',img:'pictures/spelling/tl/rhyme/tabo.png'}]},
    {prompt:{word:'labo',ending:'ABO',img:'pictures/spelling/tl/rhyme/labo.png'},correct:{word:'tabo',img:'pictures/spelling/tl/rhyme/tabo.png'},wrong:[{word:'luga',img:'pictures/spelling/tl/rhyme/luga.png'},{word:'bote',img:'pictures/spelling/tl/rhyme/bote.png'},{word:'pala',img:'pictures/spelling/tl/rhyme/pala.png'}]},
    {prompt:{word:'masa',ending:'ASA',img:'pictures/spelling/tl/rhyme/masa.png'},correct:{word:'pasa',img:'pictures/spelling/tl/rhyme/pasa.png'},wrong:[{word:'sala',img:'pictures/spelling/tl/rhyme/sala.png'},{word:'dote',img:'pictures/spelling/tl/rhyme/dote.png'},{word:'kuga',img:'pictures/spelling/tl/rhyme/kuga.png'}]},
    {prompt:{word:'tala',ending:'ALA',img:'pictures/spelling/tl/rhyme/tala.png'},correct:{word:'pala',img:'pictures/spelling/tl/rhyme/pala.png'},wrong:[{word:'pasa',img:'pictures/spelling/tl/rhyme/pasa.png'},{word:'bina',img:'pictures/spelling/tl/rhyme/bina.png'},{word:'dabo',img:'pictures/spelling/tl/rhyme/dabo.png'}]},
  ]
};
const ALPHABET_EN='ABCDEFGHIJKLMNOPQRSTUVWXYZ'.split('');
const ALPHABET_TL=['A','B','D','E','G','H','I','K','L','M','N','O','P','R','S','T','U','W','Y'];
const COLORS=['#339af0','#f06595','#e8a000','#40c057','#7950f2','#ff6b2b','#0db9c4','#e03131','#845EC2','#FF8E53','#20c997','#ffd43b','#ff922b','#cc5de8','#74c0fc'];

// ── DB SAVE ──
async function saveStart(id){try{await fetch(CURRENT_URL,{method:'POST',headers:{'Content-Type':'application/x-www-form-urlencoded'},body:`action=start&activity_id=${id}`});}catch(e){}}
function saveProgress(id,score,checkpoint=0){
  if(score<=0)return;
  const body=`action=progress&activity_id=${id}&score=${score}&checkpoint=${checkpoint}`;
  // sendBeacon works reliably even on page unload/back button
  if(navigator.sendBeacon){
    const fd=new FormData();
    fd.append('action','progress');fd.append('activity_id',id);
    fd.append('score',score);fd.append('checkpoint',checkpoint);
    navigator.sendBeacon(CURRENT_URL,fd);
  } else {
    // Fallback for older browsers
    fetch(CURRENT_URL,{method:'POST',headers:{'Content-Type':'application/x-www-form-urlencoded'},body,keepalive:true}).catch(()=>{});
  }
}
async function saveComplete(id,score){
  document.getElementById('savingMsg').textContent='💾 Saving your score...';
  try{const r=await fetch(CURRENT_URL,{method:'POST',headers:{'Content-Type':'application/x-www-form-urlencoded'},body:`action=complete&activity_id=${id}&score=${score}`});const d=await r.json();if(d.success)document.getElementById('savingMsg').textContent='✅ Score saved!';}catch(e){document.getElementById('savingMsg').textContent='';}
}

// ── LANG ──
function setLang(l){
  if(l===lang)return;lang=l;
  document.getElementById('btnEN').classList.toggle('active',l==='en');
  document.getElementById('btnTL').classList.toggle('active',l==='tl');
  document.querySelectorAll('.tab-label').forEach(el=>el.textContent=el.dataset[l]);
  if(activeTab===1)ls_startGame();
  if(activeTab===2)ws_startGame();
  if(activeTab===3)rm_startGame();
  if(activeTab===4)sp_startGame();
}

// ── TABS ──
function switchTab(num){
  activeTab=num;
  document.querySelectorAll('.act-tab').forEach((t,i)=>t.classList.toggle('active',i+1===num));
  document.querySelectorAll('.activity-panel').forEach((p,i)=>p.classList.toggle('active',i+1===num));
  if(num===1)ls_startGame();
  if(num===2)ws_startGame();
  if(num===3)rm_startGame();
  if(num===4)sp_startGame();
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
  confetti('#339af0');confetti('#ffd43b');
  saveProgress(ACT_IDS[actNum],finalScore);
  saveComplete(ACT_IDS[actNum],finalScore);
  PREV_SCORES[actNum]=Math.max(PREV_SCORES[actNum],finalScore);
}
function closeResult(){document.getElementById('resultOverlay').classList.remove('active');}
function playAgain(){
  closeResult();
  if(_curActNum===1)ls_startGame();
  if(_curActNum===2)ws_startGame();
  if(_curActNum===3)rm_startGame();
  if(_curActNum===4)sp_startGame();
}
function goNextActivity(){closeResult();switchTab(_curActNum+1);}

// ── UTILS ──
function shuffle(a){const r=[...a];for(let i=r.length-1;i>0;i--){const j=0|Math.random()*(i+1);[r[i],r[j]]=[r[j],r[i]];}return r;}
function speak(w,l){if(!window.speechSynthesis)return;window.speechSynthesis.cancel();const u=new SpeechSynthesisUtterance(w);u.lang=l==='tl'?'fil-PH':'en-US';u.rate=0.8;u.pitch=1.1;window.speechSynthesis.speak(u);}
function confetti(color){const cols=[color,'#FFE66D','#FF6B6B','#A29BFE','#4ECDC4','#FD79A8'];for(let i=0;i<30;i++){const p=document.createElement('div');p.className='cfbit';p.style.cssText=`left:${Math.random()*100}vw;top:-12px;background:${cols[0|Math.random()*cols.length]};border-radius:${Math.random()>.5?'50%':'3px'};width:${6+Math.random()*8}px;height:${6+Math.random()*8}px;animation-duration:${1.2+Math.random()*1.5}s;animation-delay:${Math.random()*.4}s;`;document.body.appendChild(p);p.addEventListener('animationend',()=>p.remove());}}

// ════════════════════════════════════════
// ACTIVITY 1: LISTEN & SPELL
// ════════════════════════════════════════
const LS_COPY={en:{title:'Listen & Spell!',sub:'Listen to the word, then spell it out!',prompt:'Press to hear the word:',choicesLbl:'Tap the letters in order:',next:'Next Word',feedbackCorrect:w=>`✅ Correct! "${w.toUpperCase()}"!`,feedbackWrong:'❌ Oops! Try again!',resultTitle:'Amazing!',resultSub:'You finished all the words!'},tl:{title:'Makinig at Mag-spell!',sub:'Pakinggan ang salita, tapos i-spell ito!',prompt:'Pindutin para marinig ang salita:',choicesLbl:'I-tap ang mga letra nang sunod-sunod:',next:'Susunod',feedbackCorrect:w=>`✅ Tama! "${w.toUpperCase()}"!`,feedbackWrong:'❌ Mali! Subukan ulit!',resultTitle:'Kahanga-hanga!',resultSub:'Natapos mo ang lahat ng salita!'}};
const LS_TOTAL=10,LS_MAX=LS_TOTAL*10;
let ls_words=[],ls_pool=[],ls_current='',ls_color='',ls_answered=false,ls_score=0,ls_qIdx=0,ls_lives=3,ls_nextSlot=0,ls_userAnswer=[];

function ls_startGame(){
  ls_score=0;ls_qIdx=0;ls_lives=3;ls_answered=false;
  ls_words=shuffle([...SPELL_WORDS[lang]]);ls_pool=[...ls_words];
  document.getElementById('p1Score').textContent='0';
  document.getElementById('resultOverlay').classList.remove('active');
  ls_updateLives();
  const c=LS_COPY[lang];
  document.getElementById('p1Title').textContent=c.title;
  document.getElementById('p1Sub').textContent=c.sub;
  document.getElementById('p1ListenPrompt').textContent=c.prompt;
  document.getElementById('p1ChoicesLbl').textContent=c.choicesLbl;
  document.getElementById('p1NextLbl').textContent=c.next;
  saveStart(ACT_IDS[1]);
  ls_loadWord();
}
function ls_updateLives(){document.querySelectorAll('#p1Lives .life-icon').forEach((ic,i)=>ic.classList.toggle('lost',i>=ls_lives));}
function ls_loadWord(){
  ls_answered=false;ls_nextSlot=0;ls_userAnswer=[];
  document.getElementById('p1NextBtn').style.display='none';
  document.getElementById('p1Feedback').textContent='';document.getElementById('p1Feedback').className='feedback-msg';
  if(!ls_pool.length)ls_pool=shuffle([...ls_words]);
  ls_current=ls_pool.shift();
  ls_color=COLORS[ls_qIdx%COLORS.length];
  document.getElementById('p1QNum').textContent=ls_qIdx+1;
  document.getElementById('p1Bar').style.width=((ls_qIdx/LS_TOTAL)*100)+'%';
  document.getElementById('p1BarLbl').textContent=`${ls_qIdx} / ${LS_TOTAL}`;
  document.getElementById('p1WordHint').textContent=ls_current.split('').map(()=>'?').join(' ');
  document.getElementById('p1ListenBtn').style.background=`linear-gradient(135deg,${ls_color},${ls_color}cc)`;
  // Build letter slots
  const slotsEl=document.getElementById('p1LetterSlots');slotsEl.innerHTML='';
  ls_current.split('').forEach((_,i)=>{const s=document.createElement('div');s.className='letter-slot';s.id=`lslot-${i}`;s.style.color=ls_color;slotsEl.appendChild(s);});
  // Build choices
  const shuffled=shuffle([...ls_current.toUpperCase().split('')]);
  const row=document.getElementById('p1ChoicesRow');row.innerHTML='';
  shuffled.forEach((l,i)=>{const btn=document.createElement('button');btn.className='choice-btn';btn.textContent=l;btn.id=`lcbtn-${i}`;btn.style.color=ls_color;btn.onclick=()=>ls_pickLetter(btn,l);row.appendChild(btn);});
  setTimeout(()=>ls_speakWord(),400);
}
function ls_speakWord(){
  const btn=document.getElementById('p1ListenBtn');btn.classList.add('speaking');
  const u=new SpeechSynthesisUtterance(ls_current);u.lang=lang==='tl'?'fil-PH':'en-US';u.rate=0.75;u.pitch=1.1;
  u.onend=()=>btn.classList.remove('speaking');u.onerror=()=>btn.classList.remove('speaking');
  if(window.speechSynthesis){window.speechSynthesis.cancel();window.speechSynthesis.speak(u);}
}
function ls_pickLetter(btn,letter){
  if(ls_answered||btn.classList.contains('used'))return;
  btn.classList.add('used');ls_userAnswer.push(letter);
  const slot=document.getElementById(`lslot-${ls_nextSlot}`);
  if(slot){slot.textContent=letter;slot.classList.add('filled');slot.style.borderColor=ls_color;slot.style.color=ls_color;}
  ls_nextSlot++;
  if(ls_nextSlot===ls_current.length)setTimeout(ls_checkAnswer,250);
}
function ls_checkAnswer(){
  ls_answered=true;
  document.querySelectorAll('#p1ChoicesRow .choice-btn').forEach(b=>b.classList.add('disabled'));
  const correct=ls_userAnswer.join('')===ls_current.toUpperCase();
  const c=LS_COPY[lang];
  if(correct){
    ls_current.split('').forEach((_,i)=>{const s=document.getElementById(`lslot-${i}`);if(s)s.classList.add('correct');});
    ls_score+=10; saveProgress(ACT_IDS[1],Math.round((ls_score/LS_MAX)*100));document.getElementById('p1Score').textContent=ls_score;
    document.getElementById('p1Feedback').textContent=c.feedbackCorrect(ls_current);
    document.getElementById('p1Feedback').className='feedback-msg correct';
    confetti(ls_color);speak(ls_current,lang);
    ls_qIdx++;
    if(ls_qIdx>=LS_TOTAL||ls_lives<=0)setTimeout(ls_showResult,900);
    else document.getElementById('p1NextBtn').style.display='inline-flex';
  } else {
    ls_current.split('').forEach((_,i)=>{const s=document.getElementById(`lslot-${i}`);if(s)s.classList.add('wrong');});
    ls_lives--;ls_updateLives();
    document.getElementById('p1Feedback').textContent=c.feedbackWrong;
    document.getElementById('p1Feedback').className='feedback-msg wrong';
    if(ls_lives<=0){setTimeout(ls_showResult,1100);return;}
    setTimeout(()=>{
      // Reset slots + choices
      document.getElementById('p1LetterSlots').querySelectorAll('.letter-slot').forEach(s=>{s.textContent='';s.classList.remove('filled','correct','wrong');});
      document.getElementById('p1ChoicesRow').querySelectorAll('.choice-btn').forEach(b=>b.classList.remove('used','disabled'));
      ls_nextSlot=0;ls_userAnswer=[];ls_answered=false;
    },900);
  }
}
function ls_nextWord(){ls_loadWord();}
function ls_showResult(){
  document.getElementById('resultTitle').textContent=LS_COPY[lang].resultTitle;
  document.getElementById('resultSub').textContent=LS_COPY[lang].resultSub;
  showResult(1,ls_score,LS_MAX);
}

// ════════════════════════════════════════
// ACTIVITY 2: WORD SCRAMBLE
// ════════════════════════════════════════
const WS_COPY={en:{title:'Word Scramble!',sub:'Arrange the mixed-up letters to form the correct word!',scrambleLbl:'Arrange these letters:',clear:'Clear',next:'Next',feedbackCorrect:w=>`✅ Correct! "${w.toUpperCase()}"!`,feedbackWrong:'❌ Not quite! Try again!',resultTitle:'Excellent!',resultSub:'You unscrambled all the words!'},tl:{title:'Ayusin ang Salita!',sub:'Ayusin ang maguluhang mga letra para mabuo ang tamang salita!',scrambleLbl:'Ayusin ang mga letrang ito:',clear:'Burahin',next:'Susunod',feedbackCorrect:w=>`✅ Tama! "${w.toUpperCase()}"!`,feedbackWrong:'❌ Hindi pa! Subukan ulit!',resultTitle:'Napakahusay!',resultSub:'Nayos mo ang lahat ng salita!'}};
const WS_TOTAL=10,WS_MAX=WS_TOTAL*10;
let ws_pool=[],ws_current=null,ws_color='',ws_answered=false,ws_score=0,ws_qIdx=0,ws_answerSlots=[],ws_tileOrder=[];

function ws_scrambleWord(w){let s=shuffle(w.toUpperCase().split(''));let t=0;while(s.join('')===w.toUpperCase()&&w.length>1&&t++<10)s=shuffle(s);return s;}
function ws_startGame(){
  ws_score=0;ws_qIdx=0;ws_answered=false;ws_pool=shuffle([...PIC_WORDS[lang]]).slice(0,12);
  document.getElementById('p2Score').textContent='0';
  document.getElementById('resultOverlay').classList.remove('active');
  const c=WS_COPY[lang];
  document.getElementById('p2Title').textContent=c.title;
  document.getElementById('p2Sub').textContent=c.sub;
  document.getElementById('p2ScrambleLbl').textContent=c.scrambleLbl;
  document.getElementById('p2ClearLbl').textContent=c.clear;
  document.getElementById('p2NextLbl').textContent=c.next;
  saveStart(ACT_IDS[2]);
  ws_loadWord();
}
function ws_loadWord(){
  ws_answered=false;ws_answerSlots=[];
  document.getElementById('p2NextBtn').style.display='none';
  document.getElementById('p2Feedback').textContent='';document.getElementById('p2Feedback').className='feedback-msg';
  if(!ws_pool.length)ws_pool=shuffle([...PIC_WORDS[lang]]).slice(0,12);
  ws_current=ws_pool.shift();
  ws_color=COLORS[ws_qIdx%COLORS.length];
  document.getElementById('p2QNum').textContent=ws_qIdx+1;
  document.getElementById('p2Bar').style.width=((ws_qIdx/WS_TOTAL)*100)+'%';
  document.getElementById('p2BarLbl').textContent=`${ws_qIdx} / ${WS_TOTAL}`;
  document.getElementById('p2Img').src=ws_current.img;
  document.getElementById('p2ImgWrap').style.setProperty('--cc',ws_color);
  document.getElementById('p2ImgWrap').style.borderColor=ws_color;
  document.getElementById('p2Card').style.setProperty('--cc',ws_color);
  // Slots
  const slotsEl=document.getElementById('p2Slots');slotsEl.innerHTML='';
  ws_current.word.split('').forEach((_,i)=>{
    const s=document.createElement('div');s.className='answer-slot';s.id=`wslot-${i}`;
    s.style.setProperty('--cc',ws_color);s.style.color=ws_color;
    s.onclick=()=>ws_removeFromSlot(i);
    slotsEl.appendChild(s);
  });
  // Scramble tiles
  ws_tileOrder=ws_scrambleWord(ws_current.word);
  const row=document.getElementById('p2ScrambleRow');row.innerHTML='';
  ws_tileOrder.forEach((l,i)=>{
    const t=document.createElement('div');t.className='scramble-tile';t.id=`wstile-${i}`;
    t.textContent=l;t.style.setProperty('--cc',ws_color);t.style.color=ws_color;t.style.borderColor=ws_color;
    t.onclick=()=>ws_pickTile(i,l);row.appendChild(t);
  });
  setTimeout(()=>speak(ws_current.word,lang),300);
}
function ws_pickTile(tileIdx,letter){
  if(ws_answered)return;
  const tile=document.getElementById(`wstile-${tileIdx}`);
  if(tile.classList.contains('used'))return;
  const slotIdx=ws_answerSlots.length;
  if(slotIdx>=ws_current.word.length)return;
  ws_answerSlots.push({tileIdx,letter});tile.classList.add('used');
  const slot=document.getElementById(`wslot-${slotIdx}`);
  if(slot){slot.textContent=letter;slot.classList.add('filled');}
  if(ws_answerSlots.length===ws_current.word.length)setTimeout(ws_checkAnswer,250);
}
function ws_removeFromSlot(slotIdx){
  if(ws_answered||slotIdx>=ws_answerSlots.length)return;
  const removed=ws_answerSlots.splice(slotIdx);
  removed.forEach(entry=>{const t=document.getElementById(`wstile-${entry.tileIdx}`);if(t)t.classList.remove('used');});
  for(let i=slotIdx;i<ws_current.word.length;i++){const s=document.getElementById(`wslot-${i}`);if(s){s.textContent='';s.classList.remove('filled');}}
}
function ws_clearAnswer(){
  if(ws_answered)return;
  ws_answerSlots=[];
  document.querySelectorAll('#p2ScrambleRow .scramble-tile').forEach(t=>t.classList.remove('used'));
  document.querySelectorAll('#p2Slots .answer-slot').forEach(s=>{s.textContent='';s.classList.remove('filled','correct','wrong');});
}
function ws_checkAnswer(){
  ws_answered=true;
  const attempt=ws_answerSlots.map(e=>e.letter).join('');
  const c=WS_COPY[lang];
  if(attempt===ws_current.word.toUpperCase()){
    ws_current.word.split('').forEach((_,i)=>{const s=document.getElementById(`wslot-${i}`);if(s)s.classList.add('correct');});
    ws_score+=10; saveProgress(ACT_IDS[2],Math.round((ws_score/WS_MAX)*100));document.getElementById('p2Score').textContent=ws_score;
    document.getElementById('p2Feedback').textContent=c.feedbackCorrect(ws_current.word);
    document.getElementById('p2Feedback').className='feedback-msg correct';
    confetti(ws_color);speak(ws_current.word,lang);
    ws_qIdx++;
    document.getElementById('p2Bar').style.width=((ws_qIdx/WS_TOTAL)*100)+'%';
    document.getElementById('p2BarLbl').textContent=`${ws_qIdx} / ${WS_TOTAL}`;
    if(ws_qIdx>=WS_TOTAL)setTimeout(ws_showResult,900);
    else document.getElementById('p2NextBtn').style.display='inline-flex';
  } else {
    ws_current.word.split('').forEach((_,i)=>{const s=document.getElementById(`wslot-${i}`);if(s)s.classList.add('wrong');});
    document.getElementById('p2Feedback').textContent=c.feedbackWrong;
    document.getElementById('p2Feedback').className='feedback-msg wrong';
    setTimeout(()=>{ws_clearAnswer();ws_answered=false;},800);
  }
}
function ws_nextWord(){ws_loadWord();}
function ws_showResult(){
  document.getElementById('resultTitle').textContent=WS_COPY[lang].resultTitle;
  document.getElementById('resultSub').textContent=WS_COPY[lang].resultSub;
  showResult(2,ws_score,WS_MAX);
}

// ════════════════════════════════════════
// ACTIVITY 3: RHYME MATCH
// ════════════════════════════════════════
const RM_COPY={en:{title:'Rhyme Match!',sub:'Find the word that rhymes with the picture shown!',promptLabel:'Which word rhymes with:',choicesLabel:'Pick the rhyming word:',next:'Next',feedbackCorrect:(a,b)=>`✅ "${a.toUpperCase()}" rhymes with "${b.toUpperCase()}"!`,feedbackWrong:"❌ Oops! They don't rhyme. Try again!",resultTitle:'Superstar!',resultSub:'You matched all the rhymes!'},tl:{title:'Rhyme Match!',sub:'Hanapin ang salitang katugma ng larawan!',promptLabel:'Alin ang katugma ng:',choicesLabel:'Piliin ang katugmang salita:',next:'Susunod',feedbackCorrect:(a,b)=>`✅ "${a.toUpperCase()}" ay katugma ng "${b.toUpperCase()}"!`,feedbackWrong:'❌ Oops! Hindi sila magkatugma. Subukan ulit!',resultTitle:'Bida-bida!',resultSub:'Natugma mo ang lahat ng rhyme!'}};
const RM_TOTAL=12,RM_MAX=RM_TOTAL*10;
let rm_pool=[],rm_current=null,rm_color='',rm_answered=false,rm_score=0,rm_qIdx=0;

function rm_startGame(){
  rm_score=0;rm_qIdx=0;rm_answered=false;rm_pool=shuffle([...RHYMES[lang]]);
  document.getElementById('p3Score').textContent='0';
  document.getElementById('resultOverlay').classList.remove('active');
  const c=RM_COPY[lang];
  document.getElementById('p3Title').textContent=c.title;
  document.getElementById('p3Sub').textContent=c.sub;
  document.getElementById('p3PromptLabel').textContent=c.promptLabel;
  document.getElementById('p3ChoicesLabel').textContent=c.choicesLabel;
  document.getElementById('p3NextLbl').textContent=c.next;
  saveStart(ACT_IDS[3]);
  rm_loadQuestion();
}
function rm_loadQuestion(){
  rm_answered=false;
  document.getElementById('p3NextBtn').style.display='none';
  document.getElementById('p3Feedback').textContent='';document.getElementById('p3Feedback').className='feedback-msg';
  if(!rm_pool.length)rm_pool=shuffle([...RHYMES[lang]]);
  rm_current=rm_pool.shift();
  rm_color=COLORS[rm_qIdx%COLORS.length];
  document.getElementById('p3QNum').textContent=rm_qIdx+1;
  document.getElementById('p3Bar').style.width=((rm_qIdx/RM_TOTAL)*100)+'%';
  document.getElementById('p3BarLbl').textContent=`${rm_qIdx} / ${RM_TOTAL}`;
  document.getElementById('p3PromptCard').style.setProperty('--pc',rm_color);
  document.getElementById('p3PromptImg').src=rm_current.prompt.img;
  document.getElementById('p3PromptWord').textContent=rm_current.prompt.word.toUpperCase();
  document.getElementById('p3PromptWord').style.color=rm_color;
  document.getElementById('p3SpeakBtn').style.color=rm_color;
  document.getElementById('p3PromptEnding').textContent=`ends in: -${rm_current.prompt.ending}`;
  // Build choices
  const choices=shuffle([rm_current.correct,...rm_current.wrong]);
  const grid=document.getElementById('p3RhymeGrid');grid.innerHTML='';
  choices.forEach((choice,i)=>{
    const isCorrect=choice.word===rm_current.correct.word;
    const cc=COLORS[(rm_qIdx+i+2)%COLORS.length];
    const card=document.createElement('div');card.className='rhyme-choice-card';card.style.setProperty('--cc',cc);
    card.innerHTML=`<div class="choice-img"><img src="${choice.img}" alt="${choice.word}" onerror="this.closest('.choice-img').innerHTML='<div style=\'text-align:center;font-size:1.5rem;line-height:56px;\'>🖼️</div>'"></div><div class="choice-word-text" style="color:${cc}">${choice.word.toUpperCase()}</div><div class="choice-ending-text" style="color:${cc}">-${choice.word.slice(-2).toUpperCase()}</div>`;
    card.onclick=()=>rm_checkAnswer(card,choice.word,isCorrect);
    grid.appendChild(card);
  });
  setTimeout(rm_speakPrompt,400);
}
function rm_speakPrompt(){if(rm_current)speak(rm_current.prompt.word,lang);}
function rm_checkAnswer(card,word,isCorrect){
  if(rm_answered)return;rm_answered=true;
  document.querySelectorAll('#p3RhymeGrid .rhyme-choice-card').forEach(c=>c.classList.add('answered'));
  const c=RM_COPY[lang];
  if(isCorrect){
    card.classList.add('correct');rm_score+=10; saveProgress(ACT_IDS[3],Math.round((rm_score/RM_MAX)*100));document.getElementById('p3Score').textContent=rm_score;
    document.getElementById('p3Feedback').textContent=c.feedbackCorrect(word,rm_current.prompt.word);
    document.getElementById('p3Feedback').className='feedback-msg correct';
    confetti(rm_color);speak(word,lang);rm_qIdx++;
    document.getElementById('p3Bar').style.width=((rm_qIdx/RM_TOTAL)*100)+'%';
    document.getElementById('p3BarLbl').textContent=`${rm_qIdx} / ${RM_TOTAL}`;
    if(rm_qIdx>=RM_TOTAL)setTimeout(rm_showResult,900);
    else document.getElementById('p3NextBtn').style.display='inline-flex';
  } else {
    card.classList.add('wrong');
    document.querySelectorAll('#p3RhymeGrid .rhyme-choice-card').forEach(cd=>{if(cd.querySelector('.choice-word-text')?.textContent===rm_current.correct.word.toUpperCase())cd.classList.add('correct');});
    document.getElementById('p3Feedback').textContent=c.feedbackWrong;
    document.getElementById('p3Feedback').className='feedback-msg wrong';
    rm_qIdx++;if(rm_qIdx>=RM_TOTAL)setTimeout(rm_showResult,1100);else document.getElementById('p3NextBtn').style.display='inline-flex';
  }
}
function rm_nextQuestion(){rm_loadQuestion();}
function rm_showResult(){
  document.getElementById('resultTitle').textContent=RM_COPY[lang].resultTitle;
  document.getElementById('resultSub').textContent=RM_COPY[lang].resultSub;
  showResult(3,rm_score,RM_MAX);
}

// ════════════════════════════════════════
// ACTIVITY 4: SPELL THE PICTURE
// ════════════════════════════════════════
const SP_COPY={en:{title:'Spell the Picture!',sub:'Look at the picture and spell the word!',kbLabel:'Tap the letters to spell:',hintTxt:'Hint (show first letter)',clear:'Clear',next:'Next',feedbackCorrect:w=>`✅ Correct! "${w.toUpperCase()}"!`,feedbackWrong:w=>`❌ Oops! The answer was "${w.toUpperCase()}"`,resultTitle:'Picture Perfect!',resultSub:'You spelled all the pictures!'},tl:{title:'I-spell ang Larawan!',sub:'Tingnan ang larawan at i-spell ang salita!',kbLabel:'I-tap ang mga letra para mag-spell:',hintTxt:'Pahiwatig (ipakita ang unang letra)',clear:'Burahin',next:'Susunod',feedbackCorrect:w=>`✅ Tama! "${w.toUpperCase()}"!`,feedbackWrong:w=>`❌ Mali! Ang sagot ay "${w.toUpperCase()}"`,resultTitle:'Perpekto!',resultSub:'Na-spell mo ang lahat ng larawan!'}};
const SP_TOTAL=12,SP_MAX=SP_TOTAL*10;
let sp_pool=[],sp_current=null,sp_color='',sp_answered=false,sp_hintUsed=false;
let sp_score=0,sp_qIdx=0,sp_userAnswer=[];

function sp_startGame(){
  sp_score=0;sp_qIdx=0;sp_answered=false;sp_pool=shuffle([...PIC_WORDS[lang]]);
  document.getElementById('p4Score').textContent='0';
  document.getElementById('resultOverlay').classList.remove('active');
  const c=SP_COPY[lang];
  document.getElementById('p4Title').textContent=c.title;
  document.getElementById('p4Sub').textContent=c.sub;
  document.getElementById('p4KbLabel').textContent=c.kbLabel;
  document.getElementById('p4HintBtnTxt').textContent=c.hintTxt;
  document.getElementById('p4ClearLbl').textContent=c.clear;
  document.getElementById('p4NextLbl').textContent=c.next;
  saveStart(ACT_IDS[4]);
  sp_loadWord();
}
function sp_loadWord(){
  sp_answered=false;sp_hintUsed=false;sp_userAnswer=[];
  document.getElementById('p4NextBtn').style.display='none';
  document.getElementById('p4Feedback').textContent='';document.getElementById('p4Feedback').className='feedback-msg';
  document.getElementById('p4HintBtn').style.display='inline-block';
  document.getElementById('p4HintBtn').style.opacity='1';document.getElementById('p4HintBtn').style.pointerEvents='auto';
  if(!sp_pool.length)sp_pool=shuffle([...PIC_WORDS[lang]]);
  sp_current=sp_pool.shift();
  sp_color=COLORS[sp_qIdx%COLORS.length];
  document.getElementById('p4QNum').textContent=sp_qIdx+1;
  document.getElementById('p4Bar').style.width=((sp_qIdx/SP_TOTAL)*100)+'%';
  document.getElementById('p4BarLbl').textContent=`${sp_qIdx} / ${SP_TOTAL}`;
  document.getElementById('p4WordImg').src=sp_current.img;
  document.getElementById('p4BigPic').style.borderColor=sp_color;
  document.getElementById('p4Card').style.setProperty('--cc',sp_color);
  document.getElementById('p4HintBtn').style.borderColor='#e8e8e8';document.getElementById('p4HintBtn').style.color='#bbb';
  // Count dots
  const dotsEl=document.getElementById('p4LetterCount');dotsEl.innerHTML='';
  sp_current.word.split('').forEach(()=>{const d=document.createElement('div');d.className='count-dot';dotsEl.appendChild(d);});
  // Answer slots
  const slotsEl=document.getElementById('p4Slots');slotsEl.innerHTML='';
  sp_current.word.split('').forEach((_,i)=>{
    const s=document.createElement('div');s.className='answer-slot';s.id=`spslot-${i}`;
    s.style.setProperty('--cc',sp_color);s.style.color=sp_color;
    s.onclick=()=>sp_removeLastLetter();
    slotsEl.appendChild(s);
  });
  // Keyboard
  const alphabet=lang==='en'?ALPHABET_EN:ALPHABET_TL;
  const kb=document.getElementById('p4Keyboard');kb.innerHTML='';
  alphabet.forEach(l=>{const k=document.createElement('button');k.className='kb-key';k.textContent=l;k.style.setProperty('--cc',sp_color);k.onclick=()=>sp_typeLetter(l);kb.appendChild(k);});
  setTimeout(()=>speak(sp_current.word,lang),300);
}
function sp_typeLetter(letter){
  if(sp_answered||sp_userAnswer.length>=sp_current.word.length)return;
  sp_userAnswer.push(letter);
  const idx=sp_userAnswer.length-1;
  const slot=document.getElementById(`spslot-${idx}`);
  if(slot){slot.textContent=letter;slot.classList.add('filled');}
  const dot=document.getElementById('p4LetterCount').children[idx];
  if(dot)dot.classList.add('filled');
  if(sp_userAnswer.length===sp_current.word.length)setTimeout(sp_checkAnswer,300);
}
function sp_removeLastLetter(){
  if(sp_answered||sp_userAnswer.length===0)return;
  const idx=sp_userAnswer.length-1;sp_userAnswer.pop();
  const slot=document.getElementById(`spslot-${idx}`);
  if(slot){slot.textContent='';slot.classList.remove('filled');}
  const dot=document.getElementById('p4LetterCount').children[idx];
  if(dot)dot.classList.remove('filled');
}
function sp_clearAnswer(){
  if(sp_answered)return;
  sp_userAnswer=[];
  document.querySelectorAll('#p4Slots .answer-slot').forEach(s=>{s.textContent='';s.classList.remove('filled','correct','wrong');});
  document.querySelectorAll('#p4LetterCount .count-dot').forEach(d=>d.classList.remove('filled'));
}
function sp_showHint(){
  if(sp_hintUsed)return;sp_hintUsed=true;
  sp_clearAnswer();
  sp_typeLetter(sp_current.word[0].toUpperCase());
  document.getElementById('p4HintBtn').style.opacity='.4';document.getElementById('p4HintBtn').style.pointerEvents='none';
}
function sp_checkAnswer(){
  sp_answered=true;
  const attempt=sp_userAnswer.join('').toLowerCase();
  const c=SP_COPY[lang];
  if(attempt===sp_current.word.toLowerCase()){
    sp_current.word.split('').forEach((_,i)=>{const s=document.getElementById(`spslot-${i}`);if(s)s.classList.add('correct');});
    const pts=sp_hintUsed?5:10;sp_score+=pts; saveProgress(ACT_IDS[4],Math.round((sp_score/SP_MAX)*100));
    document.getElementById('p4Score').textContent=sp_score;
    document.getElementById('p4Feedback').textContent=c.feedbackCorrect(sp_current.word);
    document.getElementById('p4Feedback').className='feedback-msg correct';
    confetti(sp_color);speak(sp_current.word,lang);
    document.getElementById('p4HintBtn').style.display='none';
    sp_qIdx++;
    document.getElementById('p4Bar').style.width=((sp_qIdx/SP_TOTAL)*100)+'%';
    document.getElementById('p4BarLbl').textContent=`${sp_qIdx} / ${SP_TOTAL}`;
    if(sp_qIdx>=SP_TOTAL)setTimeout(sp_showResult,900);
    else document.getElementById('p4NextBtn').style.display='inline-flex';
  } else {
    sp_current.word.split('').forEach((_,i)=>{const s=document.getElementById(`spslot-${i}`);if(s)s.classList.add('wrong');});
    document.getElementById('p4Feedback').textContent=c.feedbackWrong(sp_current.word);
    document.getElementById('p4Feedback').className='feedback-msg wrong';
    setTimeout(()=>{sp_clearAnswer();sp_answered=false;},900);
  }
}
function sp_nextWord(){sp_loadWord();}
function sp_showResult(){
  document.getElementById('resultTitle').textContent=SP_COPY[lang].resultTitle;
  document.getElementById('resultSub').textContent=SP_COPY[lang].resultSub;
  showResult(4,sp_score,SP_MAX);
}


// ── FLUSH PROGRESS ON NAVIGATION / PAGE HIDE ──
function flushActiveTabProgress() {
  switch(activeTab) {
    case 1: if(ls_score>0) saveProgress(ACT_IDS[1],Math.round((ls_score/LS_MAX)*100)); break;
    case 2: if(ws_score>0) saveProgress(ACT_IDS[2],Math.round((ws_score/WS_MAX)*100)); break;
    case 3: if(rm_score>0) saveProgress(ACT_IDS[3],Math.round((rm_score/RM_MAX)*100)); break;
    case 4: if(sp_score>0) saveProgress(ACT_IDS[4],Math.round((sp_score/SP_MAX)*100)); break;
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
if(activeTab===1)ls_startGame();
else if(activeTab===2)ws_startGame();
else if(activeTab===3)rm_startGame();
else if(activeTab===4)sp_startGame();
</script>

</div><!-- /page-wrap -->
</body>
</html>