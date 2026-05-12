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
$lesson_id  = isset($_GET['lesson_id']) ? (int)$_GET['lesson_id'] : 4; // Animals = 4

// ── HANDLE AJAX (start / complete per quizzes) ──
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

// ── FETCH ACTIVITIES FOR THIS LESSON ──
$stmt = $conn->prepare("SELECT * FROM quizzes WHERE lesson_id = ? ORDER BY quizzes_id");
$stmt->bind_param("i", $lesson_id);
$stmt->execute();
$activities = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Build quizzes map: quizzes_name => { quizzes_id, status, score }
$act_map = [];
foreach ($activities as $act) {
    $prog = quizzes_get($conn, $student_id, $act['quizzes_id']);
    $act_map[$act['quizzes_name']] = [
        'quizzes_id' => $act['quizzes_id'],
        'status'      => $prog['status'],
        'score'       => $prog['score'],
    ];
}

// Shortcut variables for each quizzes
$a1 = $act_map['animal_name_match'] ?? ['quizzes_id'=>0,'status'=>'not_started','score'=>0];
$a2 = $act_map['spot_the_animal']   ?? ['quizzes_id'=>0,'status'=>'not_started','score'=>0];
$a3 = $act_map['animal_sorting']    ?? ['quizzes_id'=>0,'status'=>'not_started','score'=>0];
$a4 = $act_map['animal_quiz']       ?? ['quizzes_id'=>0,'status'=>'not_started','score'=>0];

// Active tab from URL, default to first incomplete or 1
$active_tab = isset($_GET['tab']) ? (int)$_GET['tab'] : 1;
if ($active_tab < 1 || $active_tab > 4) $active_tab = 1;
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Animals Activities — E-KINDER</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Fredoka+One&family=Nunito:wght@400;600;700;800;900&display=swap" rel="stylesheet">
<style>
:root{--green-dark:#1a2e1a;--cream:#fdf8f0;--pill:999px;--ac-color:#51cf66;}
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0;}
body{font-family:'Nunito',sans-serif;background: #d5fcdb;;min-height:100vh;overflow-x:hidden;}
body::before{content:'';position:fixed;inset:0;pointer-events:none;z-index:0;background-image:radial-gradient(circle,rgba(0,0,0,.035) 1.2px,transparent 1.2px);background-size:26px 26px;}
.page-wrap{position:relative;z-index:1;padding-bottom:80px;}

/* NAV */
.lesson-nav{height:64px;padding:0 28px;background:rgba(255,255,255,.94);backdrop-filter:blur(16px);border-bottom:1px solid rgba(255, 0, 0, 0.07);box-shadow:0 2px 18px rgba(0,0,0,.06);position:sticky;top:0;z-index:200;display:flex;align-items:center;gap:14px;}
.lnav-back{display:inline-flex;align-items:center;gap:8px;background:#2e7d32;color:#fff;font-family:'Fredoka One',cursive;font-size:.88rem;padding:8px 20px;border-radius:var(--pill);text-decoration:none;box-shadow:0 4px 14px rgba(0,0,0,.22);transition:transform .22s cubic-bezier(.34,1.56,.64,1);flex-shrink:0;}
.lnav-back:hover{transform:scale(1.06);color:#fff;}
.lnav-title{font-family:'Fredoka One',cursive;font-size:1.1rem;color: #2e7d32;;flex:1;text-align:center;}
.lang-toggle{display:inline-flex;align-items:center;background:#f0f0f0;border-radius:var(--pill);padding:4px;border:1.5px solid #e0e0e0;}
.lang-btn{font-family:'Fredoka One',cursive;font-size:.8rem;padding:6px 18px;border-radius:var(--pill);border:none;cursor:pointer;background:transparent;color:#aaa;transition:background .2s,color .2s;}
.lang-btn.active{background: #2e7d32;color:#fff;box-shadow:0 3px 10px rgba(0,0,0,.22);}

/* quizzes TABS */
.quizzes-tabs{display:flex;gap:8px;padding:20px 0 0;overflow-x:auto;scrollbar-width:none;}
.quizzes-tabs::-webkit-scrollbar{display:none;}
.act-tab{flex-shrink:0;display:flex;align-items:center;gap:8px;padding:10px 18px;border-radius:var(--pill);border:2.5px solid #e8e8e8;background:#fff;font-family:'Fredoka One',cursive;font-size:.82rem;color:#aaa;cursor:pointer;transition:all .22s cubic-bezier(.34,1.56,.64,1);box-shadow:0 2px 8px rgba(218, 30, 30, 0.05);}
.act-tab:hover:not(.active){border-color:#c8e6c9;color:#555;transform:translateY(-2px);}
.act-tab.active{background: #2e7d32;;border-color: #2e7d32;;color:#fff;box-shadow:0 6px 18px rgba(26,46,26,.25);}
.act-tab .tab-status{width:18px;height:18px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:.6rem;}
.act-tab.active .tab-status{background:rgba(255,255,255,.2);}
.tab-status.done{background:#40c057;color:#fff;}
.tab-status.progress{background:#fcc419;color:#fff;}
.tab-status.locked{background:#e8e8e8;color:#bbb;}
.act-tab .tab-score{font-size:.7rem;opacity:.7;}

/* quizzes PANELS */
.quizzes-panel{display:none;}
.quizzes-panel.active{display:block;animation:panelIn .35s ease both;}
@keyframes panelIn{from{opacity:0;transform:translateY(14px)}to{opacity:1;transform:none}}

/* PAGE HEADER */
.page-header{text-align:center;padding:28px 0 20px;}
.quizzes-badge{display:inline-flex;align-items:center;gap:8px;background:#fff;border:1.5px solid #dde8dd;border-radius:var(--pill);padding:6px 18px;font-family:'Fredoka One',cursive;font-size:.78rem;color:#2e7d32;;box-shadow:0 2px 10px rgba(0,0,0,.06);margin-bottom:14px;}
.page-title{font-family:'Fredoka One',cursive;font-size:clamp(1.8rem,4vw,2.6rem);color:#2e7d32;;margin-bottom:6px;}
.page-sub{font-size:.92rem;color: #80b683;;font-weight:700;}
.best-score-badge{display:inline-flex;align-items:center;gap:6px;background:#fff9db;border:1.5px solid #ffe066;border-radius:var(--pill);padding:5px 14px;font-family:'Fredoka One',cursive;font-size:.78rem;color:#e67700;margin-top:6px;}

/* SCORE BAR */
.score-bar{display:flex;align-items:center;justify-content:center;gap:24px;background:#fff;border-radius:20px;padding:16px 28px;margin-bottom:28px;box-shadow:0 4px 18px rgba(0,0,0,.06);border:1.5px solid #eee;flex-wrap:wrap;}
.score-item{text-align:center;}
.score-num{font-family:'Fredoka One',cursive;font-size:1.8rem;color: #2e7d32;;line-height:1;}
.score-label{font-size:.7rem;font-weight:800;color:#bbb;text-transform:uppercase;letter-spacing:.8px;}
.score-sep{width:1.5px;height:40px;background:#f0f0f0;}
.lives-wrap{display:flex;gap:6px;}
.life-icon{font-size:1.2rem;transition:opacity .3s;}
.life-icon.lost{opacity:.2;filter:grayscale(1);}
.progress-wrap{flex:1;min-width:160px;}
.progress-bar-outer{height:10px;background:#f0f0f0;border-radius:99px;overflow:hidden;}
.progress-bar-inner{height:100%;border-radius:99px;transition:width .5s cubic-bezier(.34,1.56,.64,1);}
.progress-label{font-family:'Fredoka One',cursive;font-size:.75rem;color:#aaa;margin-top:4px;}

/* ── quizzes 1: MATCH ── */
.game-area{max-width:740px;margin:0 auto;}
.round-info{font-family:'Fredoka One',cursive;font-size:.85rem;color:#bbb;text-align:center;margin-bottom:20px;text-transform:uppercase;letter-spacing:1px;}
.match-container{display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:20px;}
.match-col-label{font-family:'Fredoka One',cursive;font-size:.78rem;color:#888;text-transform:uppercase;letter-spacing:1px;text-align:center;margin-bottom:12px;}
.match-col{display:flex;flex-direction:column;gap:12px;}
.name-card{background:#fff;border:3px solid #f0f0f0;border-radius:18px;padding:15px 14px;text-align:center;cursor:pointer;box-shadow:0 4px 14px rgba(0,0,0,.06);transition:all .25s cubic-bezier(.34,1.56,.64,1);}
.name-card:hover:not(.matched):not(.disabled){transform:translateY(-4px) scale(1.03);box-shadow:0 10px 26px rgba(0,0,0,.1);}
.name-card.selected{border-color:var(--card-color,#A0522D);background:color-mix(in srgb,var(--card-color,#A0522D) 10%,white);transform:translateY(-4px) scale(1.04);}
.name-card.matched{border-color:#40c057;background:#f0fdf4;cursor:default;animation:matchPop .4s cubic-bezier(.34,1.56,.64,1);}
.name-card.wrong{border-color:#ff4444;background:#fff5f5;animation:cardShake .4s ease;}
.name-emoji{font-size:1.6rem;margin-bottom:4px;}
.name-text{font-family:'Fredoka One',cursive;font-size:1.1rem;color:var(--card-color,#555);}
.match-check{font-size:.85rem;color:#40c057;opacity:0;transition:opacity .2s;display:block;margin-top:2px;}
.name-card.matched .match-check{opacity:1;}
.photo-card{background:#fff;border:3px solid #f0f0f0;border-radius:18px;overflow:hidden;cursor:pointer;box-shadow:0 4px 14px rgba(0,0,0,.06);transition:all .25s cubic-bezier(.34,1.56,.64,1);position:relative;}
.photo-card:hover:not(.matched):not(.disabled){transform:translateY(-4px) scale(1.03);box-shadow:0 10px 26px rgba(0,0,0,.1);}
.photo-card.selected{border-color:var(--card-color,#A0522D);transform:translateY(-4px) scale(1.04);}
.photo-card.matched{border-color:#40c057;cursor:default;animation:matchPop .4s cubic-bezier(.34,1.56,.64,1);}
.photo-card.wrong{border-color:#ff4444;animation:cardShake .4s ease;}
.photo-wrap{ width:100%; aspect-ratio:3/2; overflow:hidden; max-height:120px; display:flex; align-items:center; justify-content:center; padding:10px 0;}
.photo-wrap img{width:50%;height:100%;object-fit:cover;display:block;transition:transform .3s;}
.photo-card:hover:not(.matched) .photo-wrap img{transform:scale(1.08);}
.photo-check{position:absolute;top:8px;right:8px;width:26px;height:26px;border-radius:50%;background:#40c057;color:#fff;display:flex;align-items:center;justify-content:center;font-size:.75rem;opacity:0;transition:opacity .2s;}
.photo-card.matched .photo-check{opacity:1;}

/* ── quizzes 2: SPOT ── */
.question-card{background:#fff;border-radius:28px;padding:32px 24px;text-align:center;box-shadow:0 8px 36px rgba(0,0,0,.08);border:2px solid #f0ece4;max-width:520px;margin:0 auto 24px;}
.q-prompt{font-family:'Fredoka One',cursive;font-size:.9rem;color:#bbb;text-transform:uppercase;letter-spacing:1px;margin-bottom:16px;}
.q-emoji{font-size:4.5rem;line-height:1;margin-bottom:8px;animation:emojiBounce 2s ease-in-out infinite;}
@keyframes emojiBounce{0%,100%{transform:translateY(0) rotate(-3deg)}50%{transform:translateY(-12px) rotate(3deg)}}
.q-name{font-family:'Fredoka One',cursive;font-size:2.4rem;color:#1E88E5;margin-bottom:6px;}
.q-desc{font-size:.82rem;color:#ccc;font-weight:700;margin-bottom:10px;}
.q-speak-btn{background:none;border:none;font-size:1.2rem;cursor:pointer;color:#1E88E5;transition:transform .2s;}
.q-speak-btn:hover{transform:scale(1.2);}
.choices-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:14px;max-width:580px;margin:0 auto 20px;}
@media(max-width:480px){.choices-grid{grid-template-columns:repeat(2,1fr);}}
.choice-card{background:#fff;border:3px solid #f0f0f0;border-radius:18px;overflow:hidden;cursor:pointer;box-shadow:0 4px 14px rgba(0,0,0,.06);transition:all .25s cubic-bezier(.34,1.56,.64,1);position:relative;}
.choice-card:hover:not(.answered){transform:translateY(-6px) scale(1.05);box-shadow:0 12px 28px rgba(0,0,0,.12);}
.choice-card.correct{border-color:#40c057!important;animation:popGreen .4s cubic-bezier(.34,1.56,.64,1);}
.choice-card.wrong{border-color:#ff4444!important;animation:cardShake .4s ease;}
.choice-card.answered{cursor:default;}
.choice-card.correct::after{content:'✅';position:absolute;top:6px;right:8px;font-size:1rem;}
.choice-card.wrong::after{content:'❌';position:absolute;top:6px;right:8px;font-size:1rem;}
.choice-photo{width:100%;aspect-ratio:1/1;overflow:hidden;background:#f8f8f8;}
.choice-photo img{width:100%;height:100%;object-fit:cover;display:block;transition:transform .3s;}
.choice-card:hover:not(.answered) .choice-photo img{transform:scale(1.08);}
.choice-label{font-family:'Fredoka One',cursive;font-size:.75rem;text-align:center;padding:6px 4px;color:#888;}

/* ── quizzes 3: SORTING ── */
.sort-stage{background:#fff;border-radius:28px;padding:28px 24px;text-align:center;box-shadow:0 8px 36px rgba(0,0,0,.08);border:2px solid #f0ece4;max-width:480px;margin:0 auto 24px;}
.stage-prompt{font-family:'Fredoka One',cursive;font-size:.9rem;color: #2e7d32;text-transform:uppercase;letter-spacing:1px;margin-bottom:16px;}
.animal-pic{width:130px;height:130px;border-radius:20px;overflow:hidden;margin:0 auto 14px;border:3px solid #A0522D;box-shadow:0 8px 24px rgba(0,0,0,.1);animation:picFloat 2.5s ease-in-out infinite;}
.animal-pic img{width:100%;height:100%;object-fit:cover;display:block;}
@keyframes picFloat{0%,100%{transform:translateY(0) rotate(-1deg)}50%{transform:translateY(-12px) rotate(1deg)}}
.animal-name-big{font-family:'Fredoka One',cursive;font-size:2rem;color:#A0522D;margin-bottom:4px;}
.animal-emoji-big{font-size:1.8rem;}
.buckets{display:grid;grid-template-columns:repeat(3,1fr);gap:12px;max-width:560px;margin:0 auto 20px;}
@media(max-width:400px){.buckets{grid-template-columns:1fr;}}
.bucket{border-radius:20px;padding:18px 12px;text-align:center;cursor:pointer;border:3px solid transparent;background:#fff;box-shadow:0 4px 14px rgba(0,0,0,.06);transition:all .25s cubic-bezier(.34,1.56,.64,1);}
.bucket.land-bucket{border-color:#d7b89a;background:#fdf5ef;}
.bucket.air-bucket{border-color:#90caf9;background:#e3f2fd;}
.bucket.water-bucket{border-color:#80deea;background:#e0f7fa;}
.bucket:hover:not(.disabled){transform:translateY(-5px) scale(1.05);box-shadow:0 12px 28px rgba(0,0,0,.1);}
.bucket.correct-pick{animation:bucketPop .4s cubic-bezier(.34,1.56,.64,1);border-width:4px;}
.bucket.wrong-pick{animation:bucketShake .4s ease;border-color:#ff4444!important;background:#fff5f5!important;}
.bucket.disabled{pointer-events:none;opacity:.65;}
@keyframes bucketPop{0%{transform:scale(.95)}60%{transform:scale(1.1)}100%{transform:scale(1)}}
@keyframes bucketShake{0%,100%{transform:translateX(0)}25%{transform:translateX(-8px)}75%{transform:translateX(8px)}}
.bucket-icon{font-size:2rem;margin-bottom:6px;}
.bucket-label{font-family:'Fredoka One',cursive;font-size:1rem;color:#2d2d2d;margin-bottom:4px;}
.bucket-desc{font-size:.68rem;font-weight:800;color:#bbb;margin-bottom:8px;}
.bucket-animals{display:flex;flex-wrap:wrap;justify-content:center;gap:4px;min-height:28px;}
.mini-animal{font-size:1.2rem;animation:miniIn .3s cubic-bezier(.34,1.56,.64,1);}
@keyframes miniIn{from{transform:scale(0) rotate(-20deg)}to{transform:scale(1) rotate(0)}}

/* ── quizzes 4: QUIZ ── */
.quiz-question-card{background:#fff;border-radius:28px;padding:28px 24px;box-shadow:0 8px 36px rgba(0,0,0,.08);border:2px solid #f0ece4;max-width:560px;margin:0 auto 24px;}
.q-animal-pic{width:120px;height:120px;border-radius:18px;overflow:hidden;margin:0 auto 16px;border:3px solid #51cf66;box-shadow:0 8px 22px rgba(0,0,0,.1);}
.q-animal-pic img{width:100%;height:100%;object-fit:cover;display:block;}
.q-text{font-family:'Fredoka One',cursive;font-size:1.3rem;color:#2d2d2d;text-align:center;margin-bottom:6px;line-height:1.3;}
.q-badge-pill{display:inline-flex;align-items:center;gap:6px;padding:4px 14px;border-radius:var(--pill);font-family:'Fredoka One',cursive;font-size:.75rem;background:#f8f8f8;color:#888;margin-bottom:16px;}
.quiz-choices{display:grid;grid-template-columns:1fr 1fr;gap:12px;max-width:520px;margin:0 auto 20px;}
.choice-btn{background:#fff;border:3px solid #f0f0f0;border-radius:18px;padding:14px 16px;text-align:left;cursor:pointer;display:flex;align-items:center;gap:12px;box-shadow:0 4px 14px rgba(0,0,0,.06);transition:all .22s cubic-bezier(.34,1.56,.64,1);}
.choice-btn:hover:not(.answered){transform:translateY(-4px) scale(1.02);box-shadow:0 10px 26px rgba(0,0,0,.1);}
.choice-btn.correct{border-color:#40c057!important;background:#d3f9d8!important;animation:popGreen .4s cubic-bezier(.34,1.56,.64,1);}
.choice-btn.wrong{border-color:#ff4444!important;background:#ffe3e3!important;animation:cardShake .4s ease;}
.choice-btn.answered{cursor:default;}
.choice-letter{width:34px;height:34px;border-radius:50%;background:#51cf66;color:#fff;font-family:'Fredoka One',cursive;font-size:1rem;display:flex;align-items:center;justify-content:center;flex-shrink:0;}
.choice-text{font-family:'Fredoka One',cursive;font-size:.95rem;color:#2d2d2d;flex:1;}

.choice-card.selected{border-color:#51cf66!important;background:#f0fdf4;transform:translateY(-6px) scale(1.05);box-shadow:0 12px 28px rgba(81,207,102,.2);}
.choice-btn.selected{border-color:#51cf66!important;background:#f0fdf4!important;}
.bucket.bucket-selected{border-width:4px;box-shadow:0 12px 28px rgba(0,0,0,.12);transform:translateY(-5px) scale(1.05);}
.bucket.land-bucket.bucket-selected{border-color:#A0522D;background:#fde8d8;}
.bucket.air-bucket.bucket-selected{border-color:#1E88E5;background:#dbeeff;}
.bucket.water-bucket.bucket-selected{border-color:#00ACC1;background:#d0f4f8;}

/* SHARED */
@keyframes matchPop{0%{transform:scale(.9)}60%{transform:scale(1.08)}100%{transform:scale(1)}}
@keyframes cardShake{0%,100%{transform:translateX(0)}25%{transform:translateX(-6px)}75%{transform:translateX(6px)}}
@keyframes popGreen{0%{transform:scale(.9)}60%{transform:scale(1.1)}100%{transform:scale(1)}}
.feedback-msg{font-family:'Fredoka One',cursive;font-size:1rem;text-align:center;margin-bottom:14px;min-height:24px;}
.feedback-msg.correct{color:#2f9e44;}
.feedback-msg.wrong{color:#e03131;}
.controls{display:flex;justify-content:center;gap:12px;flex-wrap:wrap;}
.btn-action{font-family:'Fredoka One',cursive;font-size:.95rem;padding:12px 28px;border-radius:var(--pill);border:none;cursor:pointer;display:inline-flex;align-items:center;gap:8px;box-shadow:0 6px 18px rgba(0,0,0,.14);transition:transform .2s;}
.btn-action:hover{transform:scale(1.06);}
.btn-primary{background: #2e7d32;color:#fff;}
.btn-secondary{background:#f0f2f0;color: #2e7d32;box-shadow:0 2px 8px rgba(0,0,0,.07); border: 2px solid #2e7d32;}

/* RESULT OVERLAY */
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
.next-quizzes-btn{width:100%;margin-top:8px;background:linear-gradient(135deg,#51cf66,#2f9e44);color:#fff;border:none;border-radius:var(--pill);padding:12px 24px;font-family:'Fredoka One',cursive;font-size:.95rem;cursor:pointer;box-shadow:0 6px 18px rgba(0,0,0,.15);transition:transform .2s;display:flex;align-items:center;justify-content:center;gap:8px;}
.next-quizzes-btn:hover{transform:scale(1.04);}

/* CONFETTI */
.cfbit{position:fixed;pointer-events:none;z-index:1000;animation:cfFall linear forwards;}
@keyframes cfFall{0%{transform:translateY(-16px) rotate(0deg);opacity:1}100%{transform:translateY(105vh) rotate(700deg);opacity:0}}
</style>
</head>
<body>
<div class="page-wrap">

<!-- NAV -->
<nav class="lesson-nav">
  <a href="activities.php" class="lnav-back"><i class="fas fa-arrow-left"></i> Back</a>
  <div class="lnav-title">🐾 Animals Activities</div>
  <div class="lang-toggle">
    <button class="lang-btn active" id="btnEN" onclick="setLang('en')">🇺🇸 EN</button>
    <button class="lang-btn" id="btnTL" onclick="setLang('tl')">🇵🇭 TL</button>
  </div>
</nav>

<div class="container">

  <!-- quizzes TABS -->
  <div class="quizzes-tabs" id="quizzesTabs">
    <?php
    $tabs = [
        1 => ['icon'=>'fas fa-puzzle-piece', 'label_en'=>'Name Match',  'label_tl'=>'Pagtutugma', 'act'=>$a1],
        2 => ['icon'=>'fas fa-search',        'label_en'=>'Spot It',    'label_tl'=>'Hanapin',    'act'=>$a2],
        3 => ['icon'=>'fas fa-layer-group',   'label_en'=>'Sorting',    'label_tl'=>'Pag-aayos',  'act'=>$a3],
        4 => ['icon'=>'fas fa-star',          'label_en'=>'Quiz',       'label_tl'=>'Quiz',        'act'=>$a4],
    ];
    foreach ($tabs as $num => $tab):
        $status = $tab['act']['status'];
        $score  = $tab['act']['score'];
        $dot_class = match($status) {
            'completed'   => 'done',
            'in_progress' => 'progress',
            default       => 'locked',
        };
        $dot_icon = match($status) {
            'completed'   => '<i class="fas fa-check"></i>',
            'in_progress' => '<i class="fas fa-play"></i>',
            default       => $num,
        };
        $is_active = $num === $active_tab ? 'active' : '';
    ?>
    <div class="act-tab <?php echo $is_active; ?>" onclick="switchTab(<?php echo $num; ?>)" id="tab<?php echo $num; ?>">
      <i class="<?php echo $tab['icon']; ?>"></i>
      <span class="tab-label" data-en="<?php echo $tab['label_en']; ?>" data-tl="<?php echo $tab['label_tl']; ?>"><?php echo $tab['label_en']; ?></span>
      <span class="tab-status <?php echo $dot_class; ?>"><?php echo $dot_icon; ?></span>
      <?php if ($status === 'completed'): ?>
      <span class="tab-score"><?php echo $score; ?>%</span>
      <?php endif; ?>
    </div>
    <?php endforeach; ?>
  </div>

  <!-- ══════════════════════════════════════════ -->
  <!-- PANEL 1: ANIMAL NAME MATCH                 -->
  <!-- ══════════════════════════════════════════ -->
  <div class="quizzes-panel <?php echo $active_tab === 1 ? 'active' : ''; ?>" id="panel1">
    <div class="page-header">
      <div class="quizzes-badge"><i class="fas fa-puzzle-piece"></i> <span>Quizzes 1 of 4</span></div>
      <div class="page-title" id="p1Title">Animal Name Match!</div>
      <div class="page-sub" id="p1Sub">Match each animal name to the correct picture!</div>
      <?php if ($a1['score'] > 0): ?>
      <div class="best-score-badge">⭐ Best Score: <?php echo $a1['score']; ?>%</div>
      <?php endif; ?>
    </div>
    <div class="score-bar">
      <div class="score-item"><div class="score-num" id="p1Score">0</div><div class="score-label">Score</div></div>
      <div class="score-sep"></div>
      <div class="score-item"><div class="score-num" id="p1Round">1</div><div class="score-label">Round</div></div>
      <div class="score-sep"></div>
      <div class="progress-wrap">
        <div class="progress-bar-outer"><div class="progress-bar-inner" id="p1Bar" style="width:0%;background:linear-gradient(90deg,#A0522D,#d4845a)"></div></div>
        <div class="progress-label" id="p1BarLbl">0 / 5 matched</div>
      </div>
    </div>
    <div class="game-area">
      <div class="round-info" id="p1RoundInfo">Round 1 — Match 4 pairs!</div>
      <div class="match-container">
        <div><div class="match-col-label" id="p1ColName">Animal Names</div><div class="match-col" id="p1NameCol"></div></div>
        <div><div class="match-col-label" id="p1ColPhoto">Animal Photos</div><div class="match-col" id="p1PhotoCol"></div></div>
      </div>
      <div class="feedback-msg" id="p1Feedback"></div>
      <div class="controls">
        <button class="btn-action btn-primary" id="p1SubmitBtn" onclick="m_submitRound()"><i class="fas fa-check"></i> <span id="p1SubmitLbl">Submit Round</span></button>
        <button class="btn-action btn-secondary" id="p1NextBtn" onclick="m_proceedNext()" style="display:none"><i class="fas fa-arrow-right"></i> <span id="p1NextLbl">Next Round</span></button>
      </div>
    </div>
  </div>

  <!-- ══════════════════════════════════════════ -->
  <!-- PANEL 2: SPOT THE ANIMAL                   -->
  <!-- ══════════════════════════════════════════ -->
  <div class="quizzes-panel <?php echo $active_tab === 2 ? 'active' : ''; ?>" id="panel2">
    <div class="page-header">
      <div class="quizzes-badge"><i class="fas fa-search"></i> <span>Quizzes 2 of 4</span></div>
      <div class="page-title" id="p2Title">Spot the Animal!</div>
      <div class="page-sub" id="p2Sub">Read the name and find the correct animal!</div>
      <?php if ($a2['score'] > 0): ?>
      <div class="best-score-badge">⭐ Best Score: <?php echo $a2['score']; ?>%</div>
      <?php endif; ?>
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
      <div class="score-sep"></div>
      <div class="progress-wrap">
        <div class="progress-bar-outer"><div class="progress-bar-inner" id="p2Bar" style="width:0%;background:linear-gradient(90deg,#1E88E5,#0d47a1)"></div></div>
        <div class="progress-label" id="p2BarLbl">0 / 10</div>
      </div>
    </div>
    <div class="question-card">
      <div class="q-prompt" id="p2Prompt">Find this animal:</div>
      <div class="q-emoji" id="p2QEmoji">🐕</div>
      <div class="q-name" id="p2QName">Dog</div>
      <div class="q-desc" id="p2QDesc"></div>
      <button class="q-speak-btn" onclick="s_speakAnimal()"><i class="fas fa-volume-up"></i></button>
    </div>
    <div class="feedback-msg" id="p2Feedback"></div>
    <div class="choices-grid" id="p2Choices"></div>
    <div class="controls">
      <button class="btn-action btn-primary" id="p2SubmitBtn" onclick="s_submitAnswer()" style="display:none"><i class="fas fa-check"></i> <span id="p2SubmitLbl">Submit</span></button>
      <button class="btn-action btn-secondary" id="p2NextBtn" onclick="s_nextQuestion()" style="display:none">
        <i class="fas fa-arrow-right"></i> <span id="p2NextLbl">Next</span>
      </button>
    </div>
  </div>

  <!-- ══════════════════════════════════════════ -->
  <!-- PANEL 3: ANIMAL SORTING                    -->
  <!-- ══════════════════════════════════════════ -->
  <div class="quizzes-panel <?php echo $active_tab === 3 ? 'active' : ''; ?>" id="panel3">
    <div class="page-header">
      <div class="quizzes-badge"><i class="fas fa-layer-group"></i> <span>Quizzes 3 of 4</span></div>
      <div class="page-title" id="p3Title">Animal Sorting!</div>
      <div class="page-sub" id="p3Sub">Where does this animal live? Sort them!</div>
      <?php if ($a3['score'] > 0): ?>
      <div class="best-score-badge">⭐ Best Score: <?php echo $a3['score']; ?>%</div>
      <?php endif; ?>
    </div>
    <div class="score-bar">
      <div class="score-item"><div class="score-num" id="p3Score">0</div><div class="score-label">Score</div></div>
      <div class="score-sep"></div>
      <div class="score-item"><div class="score-num" id="p3QNum">1</div><div class="score-label">Animal</div></div>
      <div class="score-sep"></div>
      <div class="progress-wrap">
        <div class="progress-bar-outer"><div class="progress-bar-inner" id="p3Bar" style="width:0%;background:linear-gradient(90deg,#00ACC1,#006064)"></div></div>
        <div class="progress-label" id="p3BarLbl">0 / 12</div>
      </div>
    </div>
    <div class="sort-stage">
      <div class="stage-prompt" id="p3Prompt">Where does this animal live?</div>
      <div class="animal-pic" id="p3AnimalPic">
        <img id="p3AnimalImg" src="" alt="">
      </div>
      <div class="animal-name-big" id="p3AnimalName">Dog</div>
      <div class="animal-emoji-big" id="p3AnimalEmoji">🐕</div>
    </div>
    <div class="buckets">
      <div class="bucket land-bucket" id="p3BucketLand" onclick="so_pickBucket('land')">
        <div class="bucket-icon">🌿</div>
        <div class="bucket-label" id="p3LandLbl">Land</div>
        <div class="bucket-desc" id="p3LandDesc">Ground animals</div>
        <div class="bucket-animals" id="p3SortedLand"></div>
      </div>
      <div class="bucket air-bucket" id="p3BucketAir" onclick="so_pickBucket('air')">
        <div class="bucket-icon">☁️</div>
        <div class="bucket-label" id="p3AirLbl">Air</div>
        <div class="bucket-desc" id="p3AirDesc">Flying animals</div>
        <div class="bucket-animals" id="p3SortedAir"></div>
      </div>
      <div class="bucket water-bucket" id="p3BucketWater" onclick="so_pickBucket('water')">
        <div class="bucket-icon">🌊</div>
        <div class="bucket-label" id="p3WaterLbl">Water</div>
        <div class="bucket-desc" id="p3WaterDesc">Ocean/water animals</div>
        <div class="bucket-animals" id="p3SortedWater"></div>
      </div>
    </div>
    <div class="feedback-msg" id="p3Feedback"></div>
    <div class="controls">
      <button class="btn-action btn-primary" id="p3SubmitBtn" onclick="so_submitAnswer()" style="display:none"><i class="fas fa-check"></i> <span id="p3SubmitLbl">Submit</span></button>
      <button class="btn-action btn-secondary" id="p3NextBtn" onclick="so_nextAnimal()" style="display:none"><i class="fas fa-arrow-right"></i> <span id="p3NextLbl">Next</span></button>
    </div>
  </div>

  <!-- ══════════════════════════════════════════ -->
  <!-- PANEL 4: ANIMAL QUIZ                       -->
  <!-- ══════════════════════════════════════════ -->
  <div class="quizzes-panel <?php echo $active_tab === 4 ? 'active' : ''; ?>" id="panel4">
    <div class="page-header">
      <div class="quizzes-badge"><i class="fas fa-star"></i> <span>quizzes 4 of 4</span></div>
      <div class="page-title" id="p4Title">Animal Quiz!</div>
      <div class="page-sub" id="p4Sub">Answer the questions about animals!</div>
      <?php if ($a4['score'] > 0): ?>
      <div class="best-score-badge">⭐ Best Score: <?php echo $a4['score']; ?>%</div>
      <?php endif; ?>
    </div>
    <div class="score-bar">
      <div class="score-item"><div class="score-num" id="p4Score">0</div><div class="score-label">Score</div></div>
      <div class="score-sep"></div>
      <div class="score-item"><div class="score-num" id="p4QNum">1</div><div class="score-label">Question</div></div>
      <div class="score-sep"></div>
      <div class="score-item">
        <div class="lives-wrap" id="p4Lives"><span class="life-icon">❤️</span><span class="life-icon">❤️</span><span class="life-icon">❤️</span></div>
        <div class="score-label">Lives</div>
      </div>
      <div class="score-sep"></div>
      <div class="progress-wrap">
        <div class="progress-bar-outer"><div class="progress-bar-inner" id="p4Bar" style="width:0%;background:linear-gradient(90deg,#51cf66,#2f9e44)"></div></div>
        <div class="progress-label" id="p4BarLbl">0 / 10</div>
      </div>
    </div>
    <div class="quiz-question-card" id="p4QCard">
      <div class="q-animal-pic" id="p4AnimalPic"><img id="p4AnimalImg" src="" alt=""></div>
      <div class="q-text" id="p4QText">What animal is this?</div>
      <div style="text-align:center"><div class="q-badge-pill" id="p4QBadge"> Animal Question</div></div>
    </div>
    <div class="quiz-choices" id="p4Choices"></div>
    <div class="feedback-msg" id="p4Feedback"></div>
    <div class="controls">
      <button class="btn-action btn-primary" id="p4SubmitBtn" onclick="q_submitAnswer()" style="display:none"><i class="fas fa-check"></i> <span id="p4SubmitLbl">Submit</span></button>
      <button class="btn-action btn-secondary" id="p4NextBtn" onclick="q_nextQuestion()" style="display:none">
        <i class="fas fa-arrow-right"></i> <span id="p4NextLbl">Next</span>
      </button>
    </div>
  </div>

</div><!-- /container -->

<!-- RESULT OVERLAY (shared) -->
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
        <button class="btn-action btn-primary" id="playAgainBtn" onclick="playAgain()"><i class="fas fa-redo"></i> Play Again</button>
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
const ACT_IDS = {
  1: <?php echo (int)$a1['quizzes_id']; ?>,
  2: <?php echo (int)$a2['quizzes_id']; ?>,
  3: <?php echo (int)$a3['quizzes_id']; ?>,
  4: <?php echo (int)$a4['quizzes_id']; ?>
};
const PREV_SCORES = {
  1: <?php echo (int)$a1['score']; ?>,
  2: <?php echo (int)$a2['score']; ?>,
  3: <?php echo (int)$a3['score']; ?>,
  4: <?php echo (int)$a4['score']; ?>
};

// ── GLOBAL STATE ──
let lang = 'en';
let activeTab = <?php echo (int)$active_tab; ?>;
let currentQuizzesId = ACT_IDS[activeTab];
const CURRENT_URL = window.location.href.split('?')[0];

// ── SAVE TO DB ──
async function saveStart(actId) {
  try {
    await fetch(CURRENT_URL, {
      method:'POST',
      headers:{'Content-Type':'application/x-www-form-urlencoded'},
      body:`action=start&quizzes_id=${actId}`
    });
  } catch(e){}
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
async function saveComplete(actId, score) {
  document.getElementById('savingMsg').textContent = '💾 Saving your score...';
  try {
    const res = await fetch(CURRENT_URL, {
      method:'POST',
      headers:{'Content-Type':'application/x-www-form-urlencoded'},
      body:`action=complete&quizzes_id=${actId}&score=${score}`
    });
    const data = await res.json();
    if(data.success) document.getElementById('savingMsg').textContent = '✅ Score saved!';
  } catch(e) {
    document.getElementById('savingMsg').textContent = '';
  }
}

// ── TAB SWITCHING ──
function switchTab(num) {
  activeTab = num;
  currentQuizzesId = ACT_IDS[num];
  document.querySelectorAll('.act-tab').forEach((t,i) => t.classList.toggle('active', i+1 === num));
  document.querySelectorAll('.quizzes-panel').forEach((p,i) => p.classList.toggle('active', i+1 === num));
  // Start the game for that tab
  if(num===1) m_startGame();
  if(num===2) s_startGame();
  if(num===3) so_startGame();
  if(num===4) q_startGame();
  window.scrollTo({top:0,behavior:'smooth'});
}

// ── LANG ──
function setLang(l) {
  if(l===lang) return;
  lang=l;
  document.getElementById('btnEN').classList.toggle('active', l==='en');
  document.getElementById('btnTL').classList.toggle('active', l==='tl');
  // Update tab labels
  document.querySelectorAll('.tab-label').forEach(el => {
    el.textContent = el.dataset[l];
  });
  applyAllUI();
  // Restart current game
  if(activeTab===1) m_startGame();
  if(activeTab===2) s_startGame();
  if(activeTab===3) so_startGame();
  if(activeTab===4) q_startGame();
}

function applyAllUI() {
  // Refresh Quiz 1 button labels on lang switch
  const c1=M_COPY[lang];
  document.getElementById('p1SubmitLbl').textContent=c1.submitLbl;
  const isLastRound=m_round>=M_TOTAL_ROUNDS;
  document.getElementById('p1NextLbl').textContent=isLastRound?c1.finishLbl:c1.nextLbl;
  // Refresh submit/next labels for panels 2, 3, 4
  const c2=S_COPY[lang];
  document.getElementById('p2SubmitLbl').textContent=c2.submit;
  document.getElementById('p2NextLbl').textContent=c2.next;
  const c3=SO_COPY[lang];
  document.getElementById('p3SubmitLbl').textContent=c3.submit;
  document.getElementById('p3NextLbl').textContent=c3.next;
  const c4=Q_COPY[lang];
  document.getElementById('p4SubmitLbl').textContent=c4.submit;
  document.getElementById('p4NextLbl').textContent=c4.next;
}

// ── RESULT OVERLAY ──
let _currentActNum = 1;
function showResult(actNum, rawScore, maxScore) {
  _currentActNum = actNum;
  const finalScore = Math.round((rawScore / maxScore) * 100);
  const prevScore  = PREV_SCORES[actNum];
  const isBest     = finalScore > prevScore;

  document.getElementById('resultScore').textContent = finalScore + '%';
  document.getElementById('resultIcon').textContent  = finalScore >= 80 ? '🏆' : finalScore >= 50 ? '🎉' : '⭐';
  document.getElementById('resultBest').textContent  = isBest ? '🌟 New best score!' : (prevScore > 0 ? `Previous best: ${prevScore}%` : '');
  document.getElementById('savingMsg').textContent   = '';

  // Show "Next quizzes" button if not last
  const nextActBtn = document.getElementById('nextActBtn');
  nextActBtn.style.display = actNum < 4 ? 'flex' : 'none';

  document.getElementById('resultOverlay').classList.add('active');
  confetti('#51cf66'); confetti('#ffd43b');

  saveProgress(ACT_IDS[actNum], finalScore);
  saveComplete(ACT_IDS[actNum], finalScore);
  // Update prev score in memory
  PREV_SCORES[actNum] = Math.max(PREV_SCORES[actNum], finalScore);
}

function closeResult() {
  document.getElementById('resultOverlay').classList.remove('active');
}
function playAgain() {
  closeResult();
  if(_currentActNum===1) m_startGame();
  if(_currentActNum===2) s_startGame();
  if(_currentActNum===3) so_startGame();
  if(_currentActNum===4) q_startGame();
}
function goNextQuizzes() {
  closeResult();
  switchTab(_currentActNum + 1);
}

// ── SHARED UTILS ──
function shuffle(a){const r=[...a];for(let i=r.length-1;i>0;i--){const j=0|Math.random()*(i+1);[r[i],r[j]]=[r[j],r[i]];}return r;}
function speak(w,l='en-US'){if(!window.speechSynthesis)return;window.speechSynthesis.cancel();const u=new SpeechSynthesisUtterance(w);u.lang=l==='tl'?'fil-PH':'en-US';u.rate=0.8;u.pitch=1.2;window.speechSynthesis.speak(u);}
function confetti(color){const cols=[color,'#FFE66D','#FF6B6B','#A29BFE','#4ECDC4','#FD79A8'];for(let i=0;i<30;i++){const p=document.createElement('div');p.className='cfbit';p.style.cssText=`left:${Math.random()*100}vw;top:-12px;background:${cols[0|Math.random()*cols.length]};border-radius:${Math.random()>.5?'50%':'3px'};width:${6+Math.random()*8}px;height:${6+Math.random()*8}px;animation-duration:${1.2+Math.random()*1.5}s;animation-delay:${Math.random()*.4}s;`;document.body.appendChild(p);p.addEventListener('animationend',()=>p.remove());}}

const CAT_COLORS={Land:'#A0522D',Air:'#1E88E5',Water:'#00ACC1',Lupa:'#A0522D',Hangin:'#1E88E5',Tubig:'#00ACC1',land:'#A0522D',air:'#1E88E5',water:'#00ACC1'};

// ════════════════════════════════════════
// quizzes 1: ANIMAL NAME MATCH
// ════════════════════════════════════════
const M_ANIMALS={
  en:[
  {name:'Dog',emoji:'🐕',cat:'Land',img:'pictures/animals/dogi.jpg'},
  {name:'Cat',emoji:'🐈',cat:'Land',img:'pictures/animals/cat.jpg'},
  {name:'Eagle',emoji:'🦅',cat:'Air',img:'pictures/animals/eagle.jpg'},
  {name:'Parrot',emoji:'🦜',cat:'Air',img:'pictures/animals/parrot.jpg'},
  {name:'Fish',emoji:'🐠',cat:'Water',img:'pictures/animals/fishh.jpg'},
  {name:'Dolphin',emoji:'🐬',cat:'Water',img:'pictures/animals/dolphin.jpg'},
  {name:'Horse',emoji:'🐴',cat:'Land',img:'pictures/animals/horse.jpg'},
  {name:'Owl',emoji:'🦉',cat:'Air',img:'pictures/animals/owl.jpg'},
  {name:'Shark',emoji:'🦈',cat:'Water',img:'pictures/animals/sharky.jpg'},
  {name:'Rabbit',emoji:'🐰',cat:'Land',img:'pictures/animals/rabbit.jpg'},
  {name:'Flamingo',emoji:'🦩',cat:'Air',img:'pictures/animals/flamingo.jpg'},
  {name:'Turtle',emoji:'🐢',cat:'Water',img:'pictures/animals/turtle.jpg'}
  ],

  tl:[
  {name:'Aso',emoji:'🐕',cat:'Lupa',img:'pictures/animals/dogi.jpg'},
  {name:'Pusa',emoji:'🐈',cat:'Lupa',img:'pictures/animals/cat.jpg'},
  {name:'Agila',emoji:'🦅',cat:'Hangin',img:'pictures/animals/eagle.jpg'},
  {name:'Loro',emoji:'🦜',cat:'Hangin',img:'pictures/animals/parrot.jpg'},
  {name:'Isda',emoji:'🐠',cat:'Tubig',img:'pictures/animals/fishh.jpg'},
  {name:'Lumba-lumba',emoji:'🐬',cat:'Tubig',img:'pictures/animals/dolphin.jpg'},
  {name:'Kabayo',emoji:'🐴',cat:'Lupa',img:'pictures/animals/horse.jpg'},
  {name:'Kuwago',emoji:'🦉',cat:'Hangin',img:'pictures/animals/owl.jpg'},
  {name:'Pating',emoji:'🦈',cat:'Tubig',img:'pictures/animals/sharky.jpg'},
  {name:'Kuneho',emoji:'🐰',cat:'Lupa',img:'pictures/animals/rabbit.jpg'},
  {name:'Flamingo',emoji:'🦩',cat:'Hangin',img:'pictures/animals/flamingo.jpg'},
  {name:'Pagong',emoji:'🐢',cat:'Tubig',img:'pictures/animals/turtle.jpg'}
  ]
};
const M_COPY={en:{title:'Animal Name Match!',sub:'Match each animal name to the correct picture!',colName:'Animal Names',colPhoto:'Animal Photos',roundInfo:r=>`Round ${r} of 5 — Match 4 pairs!`,progressLbl:(m,t)=>`${m} / ${t} matched`,submitLbl:'Submit Round',nextLbl:'Next Round',finishLbl:'See Results',feedbackCorrect:(n,pts)=>`✅ Perfect! +${pts} pts earned this round!`,feedbackPartial:(c,gained)=>`⭐ ${c}/4 correct! +${gained} pts earned.`,resultTitle:'Amazing!',resultSub:'You matched all the animals!'},tl:{title:'Pagtutugma ng Hayop!',sub:'Itugma ang bawat pangalan ng hayop sa tamang larawan!',colName:'Mga Pangalan',colPhoto:'Mga Larawan',roundInfo:r=>`Round ${r} ng 5 — Itugma ang 4 pares!`,progressLbl:(m,t)=>`${m} / ${t} natugma`,submitLbl:'I-submit ang Round',nextLbl:'Susunod na Round',finishLbl:'Tingnan ang Resulta',feedbackCorrect:(n,pts)=>`✅ Perpekto! +${pts} pts nakuha sa round na ito!`,feedbackPartial:(c,gained)=>`⭐ ${c}/4 tama! +${gained} pts nakuha.`,resultTitle:'Kahanga-hanga!',resultSub:'Natugma mo ang lahat ng hayop!'}};
const M_TOTAL_ROUNDS=5, M_PPM=5, M_MAX=M_TOTAL_ROUNDS*4*M_PPM; // 5 rounds × 4 pairs × 5pts = 100
let m_score=0,m_round=0,m_pool=[],m_roundAnimals=[],m_selName=null,m_selPhoto=null,m_pairs={},m_submitted=false;

function m_startGame(){
  m_score=0;m_round=0;m_pool=shuffle([...M_ANIMALS[lang]]);
  document.getElementById('p1Score').textContent='0';
  document.getElementById('resultOverlay').classList.remove('active');
  const c=M_COPY[lang];
  document.getElementById('p1Title').textContent=c.title;
  document.getElementById('p1Sub').textContent=c.sub;
  document.getElementById('p1ColName').textContent=c.colName;
  document.getElementById('p1ColPhoto').textContent=c.colPhoto;
  document.getElementById('p1SubmitLbl').textContent=c.submitLbl;
  document.getElementById('p1NextLbl').textContent=c.nextLbl;
  saveStart(ACT_IDS[1]);
  m_nextRound();
}
function m_nextRound(){
  m_round++;m_selName=null;m_selPhoto=null;m_pairs={};m_submitted=false;
  if(m_pool.length<4)m_pool=shuffle([...M_ANIMALS[lang]]);
  m_roundAnimals=m_pool.splice(0,4);
  const c=M_COPY[lang];
  document.getElementById('p1Round').textContent=m_round;
  document.getElementById('p1RoundInfo').textContent=c.roundInfo(m_round);
  document.getElementById('p1Bar').style.width='0%';
  document.getElementById('p1BarLbl').textContent=c.progressLbl(0,4);
  document.getElementById('p1Feedback').textContent='';
  document.getElementById('p1Feedback').className='feedback-msg';
  // Show submit, hide next
  document.getElementById('p1SubmitBtn').style.display='';
  document.getElementById('p1NextBtn').style.display='none';
  const lastRound=m_round>=M_TOTAL_ROUNDS;
  document.getElementById('p1NextLbl').textContent=lastRound?M_COPY[lang].finishLbl:M_COPY[lang].nextLbl;
  m_renderCards();
}
function m_renderCards(){
  const ns=shuffle([...m_roundAnimals]),ps=shuffle([...m_roundAnimals]);
  const nc=document.getElementById('p1NameCol');nc.innerHTML='';
  const pc=document.getElementById('p1PhotoCol');pc.innerHTML='';
  ns.forEach(a=>{
    const color=CAT_COLORS[a.cat]||'#888';
    const card=document.createElement('div');card.className='name-card';card.dataset.key=a.name;card.style.setProperty('--card-color',color);
    card.innerHTML=`<div class="name-emoji">${a.emoji}</div><div class="name-text">${a.name}</div><i class="fas fa-check match-check"></i>`;
    card.onclick=()=>m_selectName(card,a);nc.appendChild(card);
  });
  ps.forEach(a=>{
    const card=document.createElement('div');card.className='photo-card';card.dataset.key=a.name;
    card.innerHTML=`<div class="photo-wrap"><img src="${a.img}" alt="${a.name}" onerror="this.parentElement.innerHTML='<div style=\\'display:flex;align-items:center;justify-content:center;height:100%;font-size:3rem;background:#f8f8f8\\'>${a.emoji}</div>'"></div><div class="photo-check"><i class="fas fa-check"></i></div>`;
    card.onclick=()=>m_selectPhoto(card,a);pc.appendChild(card);
  });
}
function m_selectName(card,animal){
  if(m_submitted)return;
  if(card.classList.contains('matched'))return;
  document.querySelectorAll('#p1NameCol .name-card.selected').forEach(c=>c.classList.remove('selected'));
  m_selName={card,animal};card.classList.add('selected');
  if(m_selName&&m_selPhoto)m_pairUp();
}
function m_selectPhoto(card,animal){
  if(m_submitted)return;
  if(card.classList.contains('matched'))return;
  document.querySelectorAll('#p1PhotoCol .photo-card.selected').forEach(c=>c.classList.remove('selected'));
  m_selPhoto={card,animal};card.classList.add('selected');
  if(m_selName&&m_selPhoto)m_pairUp();
}
function m_pairUp(){
  // Just visually "link" them — no scoring yet
  const n=m_selName,p=m_selPhoto;m_selName=null;m_selPhoto=null;
  // If name already paired, unlink old photo
  if(m_pairs[n.animal.name]){
    const oldPhotoCard=m_pairs[n.animal.name].photoCard;
    oldPhotoCard.classList.remove('matched');
    oldPhotoCard.querySelector('.photo-check').style.opacity='0';
  }
  // If photo already paired to another name, unlink it
  const existingName=Object.keys(m_pairs).find(k=>m_pairs[k].photoKey===p.animal.name);
  if(existingName){
    const oldNameCard=m_pairs[existingName].nameCard;
    oldNameCard.classList.remove('matched');
    oldNameCard.querySelector('.match-check').style.opacity='0';
    delete m_pairs[existingName];
  }
  // Store pair
  m_pairs[n.animal.name]={nameCard:n.card,photoCard:p.card,photoKey:p.animal.name};
  n.card.classList.remove('selected');p.card.classList.remove('selected');
  n.card.classList.add('matched');p.card.classList.add('matched');
  // Update bar to show how many paired (not yet scored)
  const paired=Object.keys(m_pairs).length;
  const c=M_COPY[lang];
  document.getElementById('p1Bar').style.width=((paired/4)*100)+'%';
  document.getElementById('p1BarLbl').textContent=c.progressLbl(paired,4);
}
function m_submitRound(){
  if(Object.keys(m_pairs).length<4){
    document.getElementById('p1Feedback').textContent='⚠️ Match all 4 pairs first before submitting!';
    document.getElementById('p1Feedback').className='feedback-msg wrong';
    return;
  }
  m_submitted=true;
  document.getElementById('p1SubmitBtn').style.display='none';
  // Evaluate each pair
  let correctCount=0;
  m_roundAnimals.forEach(a=>{
    const pair=m_pairs[a.name];
    if(!pair)return;
    const isCorrect=pair.photoKey===a.name;
    if(isCorrect){
      correctCount++;
      pair.nameCard.classList.add('matched');
      pair.photoCard.classList.add('matched');
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
  // Score: +5 per correct, −5 per wrong
  const roundGain = correctCount * M_PPM;  // +5 per correct pair only
  m_score = m_score + roundGain;            // no deduction for wrong pairs
  document.getElementById('p1Score').textContent = m_score;
  saveProgress(ACT_IDS[1], Math.min(100, Math.round((m_score / M_MAX) * 100)));
  const c = M_COPY[lang];
  if(correctCount === 4){
    document.getElementById('p1Feedback').textContent = c.feedbackCorrect(4, roundGain);
    document.getElementById('p1Feedback').className = 'feedback-msg correct';
    confetti('#40c057');
  } else {
    document.getElementById('p1Feedback').textContent = c.feedbackPartial(correctCount, roundGain);
    document.getElementById('p1Feedback').className = roundGain > 0 ? 'feedback-msg correct' : 'feedback-msg wrong';
  }
  // Show Next Round / See Results button
  document.getElementById('p1NextBtn').style.display='';
  const lastRound=m_round>=M_TOTAL_ROUNDS;
  document.getElementById('p1NextLbl').textContent=lastRound?M_COPY[lang].finishLbl:M_COPY[lang].nextLbl;
}
function m_proceedNext(){
  if(m_round>=M_TOTAL_ROUNDS){m_showResult();}
  else{m_nextRound();}
}
function m_showResult(){
  document.getElementById('resultTitle').textContent=M_COPY[lang].resultTitle;
  document.getElementById('resultSub').textContent=M_COPY[lang].resultSub;
  showResult(1, m_score, M_MAX);
}

// ════════════════════════════════════════
// quizzes 2: SPOT THE ANIMAL
// ════════════════════════════════════════
const S_ANIMALS={
  en:[
  {name:'Dog',emoji:'🐕',cat:'Land',img:'pictures/animals/dogi.jpg',desc:'Loyal and friendly!'},
  {name:'Cat',emoji:'🐈',cat:'Land',img:'pictures/animals/cat.jpg',desc:'Independent and curious!'},
  {name:'Horse',emoji:'🐴',cat:'Land',img:'pictures/animals/horse.jpg',desc:'Strong and fast!'},
  {name:'Pig',emoji:'🐷',cat:'Land',img:'pictures/animals/pig.jpg',desc:'Smart and playful!'},
  {name:'Rabbit',emoji:'🐰',cat:'Land',img:'pictures/animals/rabbit.jpg',desc:'Fluffy and quick!'},
  {name:'Eagle',emoji:'🦅',cat:'Air',img:'pictures/animals/eagle.jpg',desc:'Mighty and sharp-eyed!'},
  {name:'Parrot',emoji:'🦜',cat:'Air',img:'pictures/animals/parrot.jpg',desc:'Colorful and clever!'},
  {name:'Owl',emoji:'🦉',cat:'Air',img:'pictures/animals/owl.jpg',desc:'Wise and silent!'},
  {name:'Fish',emoji:'🐠',cat:'Water',img:'pictures/animals/fishh.jpg',desc:'Graceful and colorful!'},
  {name:'Dolphin',emoji:'🐬',cat:'Water',img:'pictures/animals/dolphin.jpg',desc:'Playful and smart!'},
  {name:'Shark',emoji:'🦈',cat:'Water',img:'pictures/animals/sharky.jpg',desc:'Powerful and fast!'},
  {name:'Turtle',emoji:'🐢',cat:'Water',img:'pictures/animals/turtle.jpg',desc:'Slow and steady!'}],

  tl:[
  {name:'Aso',emoji:'🐕',cat:'Lupa',img:'pictures/animals/dogi.jpg',desc:'Tapat at magiliw!'},
  {name:'Pusa',emoji:'🐈',cat:'Lupa',img:'pictures/animals/cat.jpg',desc:'Malaya at mausisa!'},
  {name:'Kabayo',emoji:'🐴',cat:'Lupa',img:'pictures/animals/horse.jpg',desc:'Malakas at mabilis!'},
  {name:'Baboy',emoji:'🐷',cat:'Lupa',img:'pictures/animals/pig.jpg',desc:'Matalino at masayahin!'},
  {name:'Kuneho',emoji:'🐰',cat:'Lupa',img:'pictures/animals/rabbit.jpg',desc:'Malambot at maliksi!'},
  {name:'Agila',emoji:'🦅',cat:'Hangin',img:'pictures/animals/eagle.jpg',desc:'Makapangyarihan!'},
  {name:'Loro',emoji:'🦜',cat:'Hangin',img:'pictures/animals/parrot.jpg',desc:'Makulay at matalino!'},
  {name:'Kuwago',emoji:'🦉',cat:'Hangin',img:'pictures/animals/owl.jpg',desc:'Matalino at tahimik!'},
  {name:'Isda',emoji:'🐠',cat:'Tubig',img:'pictures/animals/fishh.jpg',desc:'Maganda at makulay!'},
  {name:'Lumba-lumba',emoji:'🐬',cat:'Tubig',img:'pictures/animals/dolphin.jpg',desc:'Masayahin at matalino!'},
  {name:'Pating',emoji:'🦈',cat:'Tubig',img:'pictures/animals/sharky.jpg',desc:'Malakas at mabilis!'},
  {name:'Pagong',emoji:'🐢',cat:'Tubig',img:'pictures/animals/turtle.jpg',desc:'Mabagal ngunit matatag!'}]
};
const S_COPY={en:{title:'Spot the Animal!',sub:'Read the name and find the correct animal!',prompt:'Find this animal:',submit:'Submit',next:'Next',feedbackCorrect:n=>`✅ Yes! That's the ${n}!`,feedbackWrong:n=>`❌ That's the ${n}! Keep going!`,resultTitle:'Superstar!',resultSub:'You found all the animals!'},tl:{title:'Hanapin ang Hayop!',sub:'Basahin ang pangalan at hanapin ang tamang hayop!',prompt:'Hanapin ang hayop na ito:',submit:'I-submit',next:'Susunod',feedbackCorrect:n=>`✅ Tama! Iyan ang ${n}!`,feedbackWrong:n=>`❌ Iyan ang ${n}! Magpatuloy!`,resultTitle:'Mahusay!',resultSub:'Nahanap mo ang lahat ng hayop!'}};
const S_TOTAL=10,S_MAX=S_TOTAL*10;
let s_pool=[],s_current=null,s_answered=false,s_score=0,s_qIdx=0,s_lives=3,s_selectedCard=null,s_selectedAnimal=null,s_selectedIsCorrect=false;

function s_startGame(){
  s_score=0;s_qIdx=0;s_lives=3;s_answered=false;s_selectedCard=null;s_pool=shuffle([...S_ANIMALS[lang]]);
  document.getElementById('p2Score').textContent='0';
  document.getElementById('resultOverlay').classList.remove('active');
  s_updateLives();
  const c=S_COPY[lang];
  document.getElementById('p2Title').textContent=c.title;
  document.getElementById('p2Sub').textContent=c.sub;
  document.getElementById('p2Prompt').textContent=c.prompt;
  document.getElementById('p2SubmitLbl').textContent=c.submit;
  document.getElementById('p2NextLbl').textContent=c.next;
  saveStart(ACT_IDS[2]);
  s_loadQuestion();
}
function s_updateLives(){document.querySelectorAll('#p2Lives .life-icon').forEach((ic,i)=>ic.classList.toggle('lost',i>=s_lives));}
function s_loadQuestion(){
  s_answered=false;s_selectedCard=null;s_selectedAnimal=null;s_selectedIsCorrect=false;
  document.getElementById('p2SubmitBtn').style.display='none';
  document.getElementById('p2NextBtn').style.display='none';
  document.getElementById('p2Feedback').textContent='';document.getElementById('p2Feedback').className='feedback-msg';
  if(!s_pool.length)s_pool=shuffle([...S_ANIMALS[lang]]);
  s_current=s_pool.shift();
  const color=CAT_COLORS[s_current.cat]||'#1E88E5';
  document.getElementById('p2QNum').textContent=s_qIdx+1;
  document.getElementById('p2Bar').style.width=((s_qIdx/S_TOTAL)*100)+'%';
  document.getElementById('p2BarLbl').textContent=`${s_qIdx} / ${S_TOTAL}`;
  document.getElementById('p2QEmoji').textContent=s_current.emoji;
  document.getElementById('p2QName').textContent=s_current.name;
  document.getElementById('p2QName').style.color=color;
  document.getElementById('p2QDesc').textContent=s_current.desc;
  const wrong=shuffle(S_ANIMALS[lang].filter(a=>a.name!==s_current.name)).slice(0,3);
  const choices=shuffle([s_current,...wrong]);
  const grid=document.getElementById('p2Choices');grid.innerHTML='';
  choices.forEach(a=>{
    const isCorrect=a.name===s_current.name;
    const card=document.createElement('div');card.className='choice-card';
    card.innerHTML=`<div class="choice-photo"><img src="${a.img}" alt="${a.name}" onerror="this.parentElement.innerHTML='<div style=\\'display:flex;align-items:center;justify-content:center;height:100%;font-size:3rem;background:#f8f8f8\\'>${a.emoji}</div>'"></div><div class="choice-label">${a.name}</div>`;
    card.onclick=()=>s_selectCard(card,a,isCorrect);
    grid.appendChild(card);
  });
  setTimeout(s_speakAnimal,400);
}
function s_speakAnimal(){if(s_current)speak(s_current.name,lang);}
function s_selectCard(card,animal,isCorrect){
  if(s_answered)return;
  // Deselect previous
  document.querySelectorAll('#p2Choices .choice-card').forEach(c=>c.classList.remove('selected'));
  card.classList.add('selected');
  s_selectedCard=card;s_selectedAnimal=animal;s_selectedIsCorrect=isCorrect;
  // Show submit button
  document.getElementById('p2SubmitBtn').style.display='';
}
function s_submitAnswer(){
  if(!s_selectedCard||s_answered)return;
  s_answered=true;
  document.getElementById('p2SubmitBtn').style.display='none';
  document.querySelectorAll('#p2Choices .choice-card').forEach(c=>c.classList.add('answered'));
  const c=S_COPY[lang];
  if(s_selectedIsCorrect){
    s_selectedCard.classList.add('correct');s_score+=10;saveProgress(ACT_IDS[2],Math.round((s_score/S_MAX)*100));document.getElementById('p2Score').textContent=s_score;
    document.getElementById('p2Feedback').textContent=c.feedbackCorrect(s_current.name);
    document.getElementById('p2Feedback').className='feedback-msg correct';
    confetti(CAT_COLORS[s_current.cat]||'#1E88E5');speak(s_current.name,lang);
  } else {
    s_selectedCard.classList.add('wrong');
    document.querySelectorAll('#p2Choices .choice-card').forEach(c=>{if(c.querySelector('.choice-label')?.textContent===s_current.name)c.classList.add('correct');});
    s_lives--;s_updateLives();
    document.getElementById('p2Feedback').textContent=c.feedbackWrong(s_selectedAnimal.name);
    document.getElementById('p2Feedback').className='feedback-msg wrong';
  }
  s_qIdx++;
  if(s_qIdx>=S_TOTAL||s_lives<=0){
    setTimeout(s_showResult,1200);
  } else {
    document.getElementById('p2NextBtn').style.display='';
  }
}
function s_nextQuestion(){s_loadQuestion();}
function s_showResult(){
  document.getElementById('resultTitle').textContent=S_COPY[lang].resultTitle;
  document.getElementById('resultSub').textContent=S_COPY[lang].resultSub;
  showResult(2, s_score, S_MAX);
}

// ════════════════════════════════════════
// quizzes 3: ANIMAL SORTING
// ════════════════════════════════════════
const SO_ANIMALS={
  en:[
  {name:'Dog',cat:'land',img:'pictures/animals/dogi.jpg'},
  {name:'Cat',cat:'land',img:'pictures/animals/cat.jpg'},
  {name:'Horse',cat:'land',img:'pictures/animals/horse.jpg'},
  {name:'Pig',cat:'land',img:'pictures/animals/pig.jpg'},
  {name:'Rabbit',cat:'land',img:'pictures/animals/rabbit.jpg'},
  {name:'Eagle',cat:'air',img:'pictures/animals/eagle.jpg'},
  {name:'Parrot',cat:'air',img:'pictures/animals/parrot.jpg'},
  {name:'Owl', cat:'air',img:'pictures/animals/owl.jpg'},
  {name:'Bat',cat:'air',img:'pictures/animals/bat.jpg'},
  {name:'Fish',cat:'water',img:'pictures/animals/fishh.jpg'},
  {name:'Dolphin',cat:'water',img:'pictures/animals/dolphin.jpg'},
  {name:'Turtle',cat:'water',img:'pictures/animals/turtle.jpg'}],

  tl:[
  {name:'Aso',cat:'land',img:'pictures/animals/dogi.jpg'},
  {name:'Pusa',cat:'land',img:'pictures/animals/cat.jpg'},
  {name:'Kabayo',cat:'land',img:'pictures/animals/horse.jpg'},
  {name:'Baboy',cat:'land',img:'pictures/animals/pig.jpg'},
  {name:'Kuneho',cat:'land',img:'pictures/animals/rabbit.jpg'},
  {name:'Agila',cat:'air',img:'pictures/animals/eagle.jpg'},
  {name:'Loro',cat:'air',img:'pictures/animals/parrot.jpg'},
  {name:'Kuwago',cat:'air',img:'pictures/animals/owl.jpg'},
  {name:'Paniki',cat:'air',img:'pictures/animals/bat.jpg'},
  {name:'Isda',cat:'water',img:'pictures/animals/fishh.jpg'},
  {name:'Lumba-lumba',cat:'water',img:'pictures/animals/dolphin.jpg'},
  {name:'Pagong',cat:'water',img:'pictures/animals/turtle.jpg'}]
};

const SO_COPY={en:{title:'Animal Sorting!',sub:'Where does this animal live? Sort them!',prompt:'Where does this animal live?',land:'Land',landDesc:'Ground animals',air:'Air',airDesc:'Flying animals',water:'Water',waterDesc:'Ocean/water animals',submit:'Submit',next:'Next',feedbackCorrect:(n,c)=>`✅ Right! ${n} lives on ${c}!`,feedbackWrong:(n,c)=>`❌ ${n} actually lives on ${c}!`,resultTitle:'Great Sorting!',resultSub:'You sorted all the animals!'},tl:{title:'Pag-aayos ng Hayop!',sub:'Saan nakatira ang hayop na ito? Ayusin sila!',prompt:'Saan nakatira ang hayop na ito?',land:'Lupa',landDesc:'Mga hayop sa lupa',air:'Hangin',airDesc:'Mga hayop na lumilipad',water:'Tubig',waterDesc:'Mga hayop sa tubig',submit:'I-submit',next:'Susunod',feedbackCorrect:(n,c)=>`✅ Tama! Ang ${n} ay nakatira sa ${c}!`,feedbackWrong:(n,c)=>`❌ Ang ${n} ay nakatira sa ${c}!`,resultTitle:'Napakahusay!',resultSub:'Nayos mo ang lahat ng hayop!'}};
const SO_TOTAL=10,SO_MAX=SO_TOTAL*10;
let so_pool=[],so_current=null,so_answered=false,so_score=0,so_qIdx=0,so_selectedType=null;

function so_startGame(){
  so_score=0;so_qIdx=0;so_answered=false;so_selectedType=null;so_pool=shuffle([...SO_ANIMALS[lang]]);
  document.getElementById('p3Score').textContent='0';
  document.getElementById('resultOverlay').classList.remove('active');
  ['p3SortedLand','p3SortedAir','p3SortedWater'].forEach(id=>document.getElementById(id).innerHTML='');
  const c=SO_COPY[lang];
  document.getElementById('p3Title').textContent=c.title;
  document.getElementById('p3Sub').textContent=c.sub;
  document.getElementById('p3Prompt').textContent=c.prompt;
  document.getElementById('p3LandLbl').textContent=c.land;
  document.getElementById('p3LandDesc').textContent=c.landDesc;
  document.getElementById('p3AirLbl').textContent=c.air;
  document.getElementById('p3AirDesc').textContent=c.airDesc;
  document.getElementById('p3WaterLbl').textContent=c.water;
  document.getElementById('p3WaterDesc').textContent=c.waterDesc;
  document.getElementById('p3SubmitLbl').textContent=c.submit;
  document.getElementById('p3NextLbl').textContent=c.next;
  saveStart(ACT_IDS[3]);
  so_loadAnimal();
}
function so_loadAnimal(){
  so_answered=false;so_selectedType=null;
  document.getElementById('p3Feedback').textContent='';document.getElementById('p3Feedback').className='feedback-msg';
  document.getElementById('p3SubmitBtn').style.display='none';
  document.getElementById('p3NextBtn').style.display='none';
  const buckets=['p3BucketLand','p3BucketAir','p3BucketWater'];
  buckets.forEach(id=>{
    const el=document.getElementById(id);
    el.classList.remove('correct-pick','wrong-pick','disabled','bucket-selected');
  });
  if(!so_pool.length)so_pool=shuffle([...SO_ANIMALS[lang]]);
  so_current=so_pool.shift();
  const color=CAT_COLORS[so_current.cat];
  document.getElementById('p3QNum').textContent=so_qIdx+1;
  document.getElementById('p3Bar').style.width=((so_qIdx/SO_TOTAL)*100)+'%';
  document.getElementById('p3BarLbl').textContent=`${so_qIdx} / ${SO_TOTAL}`;
  document.getElementById('p3AnimalPic').style.borderColor=color;
  document.getElementById('p3AnimalImg').src=so_current.img;
  document.getElementById('p3AnimalImg').alt=so_current.name;
  document.getElementById('p3AnimalName').textContent=so_current.name;
  document.getElementById('p3AnimalName').style.color=color;
  document.getElementById('p3AnimalEmoji').textContent=so_current.emoji;
  speak(so_current.name,lang);
}
function so_pickBucket(type){
  if(so_answered)return;
  // Visual selection — highlight chosen bucket, allow changing
  so_selectedType=type;
  const bucketMap={land:'p3BucketLand',air:'p3BucketAir',water:'p3BucketWater'};
  Object.values(bucketMap).forEach(id=>document.getElementById(id).classList.remove('bucket-selected'));
  document.getElementById(bucketMap[type]).classList.add('bucket-selected');
  // Show submit
  document.getElementById('p3SubmitBtn').style.display='';
}
function so_submitAnswer(){
  if(!so_selectedType||so_answered)return;
  so_answered=true;
  document.getElementById('p3SubmitBtn').style.display='none';
  const bucketMap={land:'p3BucketLand',air:'p3BucketAir',water:'p3BucketWater'};
  const sortedMap={land:'p3SortedLand',air:'p3SortedAir',water:'p3SortedWater'};
  // Disable all buckets
  Object.values(bucketMap).forEach(id=>document.getElementById(id).classList.add('disabled'));
  const isCorrect=so_selectedType===so_current.cat;
  const c=SO_COPY[lang];
  const catName={land:c.land,air:c.air,water:c.water};
  if(isCorrect){
    document.getElementById(bucketMap[so_selectedType]).classList.add('correct-pick');
    so_score+=10;saveProgress(ACT_IDS[3],Math.round((so_score/SO_MAX)*100));document.getElementById('p3Score').textContent=so_score;
    document.getElementById('p3Feedback').textContent=c.feedbackCorrect(so_current.name,catName[so_selectedType]);
    document.getElementById('p3Feedback').className='feedback-msg correct';
    const mini=document.createElement('span');mini.className='mini-animal';mini.textContent=so_current.emoji;
    document.getElementById(sortedMap[so_selectedType]).appendChild(mini);
    confetti(CAT_COLORS[so_current.cat]);
  } else {
    document.getElementById(bucketMap[so_selectedType]).classList.add('wrong-pick');
    document.getElementById(bucketMap[so_current.cat]).classList.add('correct-pick');
    document.getElementById('p3Feedback').textContent=c.feedbackWrong(so_current.name,catName[so_current.cat]);
    document.getElementById('p3Feedback').className='feedback-msg wrong';
  }
  so_qIdx++;
  if(so_qIdx>=SO_TOTAL){
    setTimeout(so_showResult,1000);
  } else {
    document.getElementById('p3NextBtn').style.display='';
  }
}
function so_nextAnimal(){so_loadAnimal();}
function so_showResult(){
  document.getElementById('resultTitle').textContent=SO_COPY[lang].resultTitle;
  document.getElementById('resultSub').textContent=SO_COPY[lang].resultSub;
  showResult(3, so_score, SO_MAX);
}

// ════════════════════════════════════════
// quizzes 4: ANIMAL QUIZ
// ════════════════════════════════════════
const Q_ANIMALS={
  en:[{name:'Dog',emoji:'🐕',cat:'Land',img:'pictures/animals/dogi.jpg',desc:'Loyal and friendly pet!'},
  {name:'Cat',emoji:'🐈',cat:'Land',img:'pictures/animals/cat.jpg',desc:'Independent and curious!'},
  {name:'Horse',emoji:'🐴',cat:'Land',img:'pictures/animals/horse.jpg',desc:'Strong and fast land animal!'},
  {name:'Pig',emoji:'🐷',cat:'Land',img:'pictures/animals/pig.jpg',desc:'Smart animal with a curly tail!'},
  {name:'Rabbit',emoji:'🐰',cat:'Land',img:'pictures/animals/rabbit.jpg',desc:'Fluffy animal that hops!'},
  {name:'Chicken',emoji:'🐓',cat:'Land',img:'pictures/animals/chicken.jpg',desc:'Lays the eggs we eat!'},
  {name:'Goat',emoji:'🐐',cat:'Land',img:'pictures/animals/goat.jpg',desc:'Can climb almost anything!'},
  {name:'Eagle',emoji:'🦅',cat:'Air',img:'pictures/animals/eagle.jpg',desc:'Mighty bird with sharp eyes!'},
  {name:'Parrot',emoji:'🦜',cat:'Air',img:'pictures/animals/parrot.jpg',desc:'Colorful bird that can talk!'},
  {name:'Owl',emoji:'🦉',cat:'Air',img:'pictures/animals/owl.jpg',desc:'Hunts at night with big eyes!'},
  {name:'Flamingo',emoji:'🦩',cat:'Air',img:'pictures/animals/flamingo.jpg',desc:'Pink bird that stands on one leg!'},
  {name:'Bat',emoji:'🦇',cat:'Air',img:'pictures/animals/bat.jpg',desc:'Flies at night using sound!'},
  {name:'Fish',emoji:'🐠',cat:'Water',img:'pictures/animals/fishh.jpg',desc:'Colorful creature of the sea!'},
  {name:'Dolphin',emoji:'🐬',cat:'Water',img:'pictures/animals/dolphin.jpg',desc:'Playful and very smart!'},
  {name:'Shark',emoji:'🦈',cat:'Water',img:'pictures/animals/sharky.jpg',desc:'Great hunter of the ocean!'},
  {name:'Turtle',emoji:'🐢',cat:'Water',img:'pictures/animals/turtle.jpg',desc:'Carries its home on its back!'}],

  tl:[{name:'Aso',emoji:'🐕',cat:'Lupa',img:'pictures/animals/dogi.jpg',desc:'Tapat na alagang hayop!'},
  {name:'Pusa',emoji:'🐈',cat:'Lupa',img:'pictures/animals/cat.jpg',desc:'Malaya at mausisa!'},
  {name:'Kabayo',emoji:'🐴',cat:'Lupa',img:'pictures/animals/horse.jpg',desc:'Malakas at mabilis na hayop!'},
  {name:'Baboy',emoji:'🐷',cat:'Lupa',img:'pictures/animals/pig.jpg',desc:'Matalinong hayop!'},
  {name:'Kuneho',emoji:'🐰',cat:'Lupa',img:'pictures/animals/rabbit.jpg',desc:'Malambot na hayop na bumabrincas!'},
  {name:'Manok',emoji:'🐓',cat:'Lupa',img:'pictures/animals/chicken.jpg',desc:'Naglalagay ng itlog!'},
  {name:'Kambing',emoji:'🐐',cat:'Lupa',img:'pictures/animals/goat.jpg',desc:'Kayang akyatin ang halos lahat!'},
  {name:'Agila',emoji:'🦅',cat:'Hangin',img:'pictures/animals/eagle.jpg',desc:'Makapangyarihang ibon!'},
  {name:'Loro',emoji:'🦜',cat:'Hangin',img:'pictures/animals/parrot.jpg',desc:'Makulay na ibong nakakapagsalita!'},
  {name:'Kuwago',emoji:'🦉',cat:'Hangin',img:'pictures/animals/owl.jpg',desc:'Nangangaso sa gabi!'},
  {name:'Flamingo',emoji:'🦩',cat:'Hangin',img:'pictures/animals/flamingo.jpg',desc:'Rosas na ibong nakatayo sa isang paa!'},
  {name:'Paniki',emoji:'🦇',cat:'Hangin',img:'pictures/animals/bat.jpg',desc:'Lumilipad sa gabi!'},
  {name:'Isda',emoji:'🐠',cat:'Tubig',img:'pictures/animals/fishh.jpg',desc:'Makulay na nilalang ng dagat!'},
  {name:'Lumba-lumba',emoji:'🐬',cat:'Tubig',img:'pictures/animals/dolphin.jpg',desc:'Masayahin at napakatalino!'},
  {name:'Pating',emoji:'🦈',cat:'Tubig',img:'pictures/animals/sharky.jpg',desc:'Dakilang mangangaso!'},
  {name:'Pagong',emoji:'🐢',cat:'Tubig',img:'pictures/animals/turtle.jpg',desc:'Dinadala ang tahanan sa likod!'}]
};
const Q_COPY={en:{title:'Animal Quiz!',sub:'Answer the questions about animals!',feedbackCorrect:'✅ Correct! Well done!',feedbackWrong:a=>`❌ The answer was ${a}`,submit:'Submit',next:'Next',resultTitle:'Amazing!',resultSub:'You finished the Animal Quiz!'},tl:{title:'Animal Quiz!',sub:'Sagutin ang mga tanong tungkol sa mga hayop!',feedbackCorrect:'✅ Tama! Napakahusay!',feedbackWrong:a=>`❌ Ang sagot ay ${a}`,submit:'I-submit',next:'Susunod',resultTitle:'Kahanga-hanga!',resultSub:'Natapos mo ang Animal Quiz!'}};
const Q_TOTAL=10,Q_MAX=Q_TOTAL*10,Q_LETTERS=['A','B','C','D'];
let q_pool=[],q_current=null,q_answered=false,q_score=0,q_qIdx=0,q_lives=3,q_selectedBtn=null,q_selectedIsCorrect=false,q_correctKey=null;

function q_buildPool(){
  const animals=Q_ANIMALS[lang];
  return shuffle([...animals]).slice(0,Q_TOTAL).map((a,i)=>{
    const type=i%3;
    if(type===0)return{img:a.img,emoji:a.emoji,color:CAT_COLORS[a.cat]||'#51cf66',question:()=>lang==='en'?'What animal is this?':'Anong hayop ito?',badge:()=>a.emoji+' '+a.name,correct:a.name,choices:()=>{const wrong=shuffle(animals.filter(x=>x.name!==a.name)).slice(0,3);return shuffle([a,...wrong]).map(x=>({text:x.name+' '+x.emoji,key:x.name}));}};
    if(type===1){const cats={Land:{en:'Land 🌿',tl:'Lupa 🌿'},Air:{en:'Air ☁️',tl:'Hangin ☁️'},Water:{en:'Water 🌊',tl:'Tubig 🌊'}};return{img:a.img,emoji:a.emoji,color:CAT_COLORS[a.cat]||'#51cf66',question:()=>lang==='en'?`Where does the ${a.name} live?`:`Saan nakatira ang ${a.name}?`,badge:()=>a.emoji+' '+a.name,correct:()=>cats[a.cat]?.[lang]||a.cat,choices:()=>shuffle(Object.keys(cats)).map(k=>({text:cats[k][lang],key:cats[k][lang]}))};};
    return{img:a.img,emoji:a.emoji,color:CAT_COLORS[a.cat]||'#51cf66',question:()=>lang==='en'?`"${a.desc}" — what animal is this?`:`"${a.desc}" — anong hayop ito?`,badge:()=>lang==='en'?'Read and pick!':'Basahin at pumili!',correct:a.name,choices:()=>{const wrong=shuffle(animals.filter(x=>x.name!==a.name)).slice(0,3);return shuffle([a,...wrong]).map(x=>({text:x.name+' '+x.emoji,key:x.name}));}};
  });
}
function q_startGame(){
  q_score=0;q_qIdx=0;q_lives=3;q_answered=false;q_selectedBtn=null;q_pool=q_buildPool();
  document.getElementById('p4Score').textContent='0';
  document.getElementById('resultOverlay').classList.remove('active');
  q_updateLives();
  const c=Q_COPY[lang];
  document.getElementById('p4Title').textContent=c.title;
  document.getElementById('p4Sub').textContent=c.sub;
  document.getElementById('p4SubmitLbl').textContent=c.submit;
  document.getElementById('p4NextLbl').textContent=c.next;
  saveStart(ACT_IDS[4]);
  q_loadQuestion();
}
function q_updateLives(){document.querySelectorAll('#p4Lives .life-icon').forEach((ic,i)=>ic.classList.toggle('lost',i>=q_lives));}
function q_loadQuestion(){
  q_answered=false;q_selectedBtn=null;q_selectedIsCorrect=false;q_correctKey=null;
  document.getElementById('p4SubmitBtn').style.display='none';
  document.getElementById('p4NextBtn').style.display='none';
  document.getElementById('p4Feedback').textContent='';document.getElementById('p4Feedback').className='feedback-msg';
  if(!q_pool.length)q_pool=q_buildPool();
  q_current=q_pool.shift();
  const color=q_current.color;
  document.getElementById('p4QNum').textContent=q_qIdx+1;
  document.getElementById('p4Bar').style.width=((q_qIdx/Q_TOTAL)*100)+'%';
  document.getElementById('p4BarLbl').textContent=`${q_qIdx} / ${Q_TOTAL}`;
  document.getElementById('p4AnimalPic').style.borderColor=color;
  document.getElementById('p4AnimalImg').src=q_current.img;
  document.getElementById('p4AnimalImg').onerror=function(){this.parentElement.innerHTML=`<div style="display:flex;align-items:center;justify-content:center;height:100%;font-size:3rem;background:#f8f8f8">${q_current.emoji}</div>`;};
  document.getElementById('p4QText').textContent=typeof q_current.question==='function'?q_current.question():q_current.question;
  document.getElementById('p4QBadge').textContent = 'Animal Question';
  const choices=typeof q_current.choices==='function'?q_current.choices():q_current.choices;
  q_correctKey=typeof q_current.correct==='function'?q_current.correct():q_current.correct;
  const grid=document.getElementById('p4Choices');grid.innerHTML='';
  choices.forEach((ch,i)=>{
    const isCorrect=ch.key===q_correctKey;
    const btn=document.createElement('button');btn.className='choice-btn';
    btn.innerHTML=`<div class="choice-letter" style="background:${color}">${Q_LETTERS[i]}</div><div class="choice-text">${ch.text}</div>`;
    btn.onclick=()=>q_selectAnswer(btn,ch.key,isCorrect);
    grid.appendChild(btn);
  });
}
function q_selectAnswer(btn,chosenKey,isCorrect){
  if(q_answered)return;
  document.querySelectorAll('#p4Choices .choice-btn').forEach(b=>b.classList.remove('selected'));
  btn.classList.add('selected');
  q_selectedBtn=btn;q_selectedIsCorrect=isCorrect;
  document.getElementById('p4SubmitBtn').style.display='';
}
function q_submitAnswer(){
  if(!q_selectedBtn||q_answered)return;
  q_answered=true;
  document.getElementById('p4SubmitBtn').style.display='none';
  document.querySelectorAll('#p4Choices .choice-btn').forEach(b=>b.classList.add('answered'));
  const c=Q_COPY[lang];
  if(q_selectedIsCorrect){
    q_selectedBtn.classList.add('correct');q_score+=10;saveProgress(ACT_IDS[4],Math.round((q_score/Q_MAX)*100));document.getElementById('p4Score').textContent=q_score;
    document.getElementById('p4Feedback').textContent=c.feedbackCorrect;
    document.getElementById('p4Feedback').className='feedback-msg correct';
    confetti(q_current.color);speak(q_correctKey,lang);
  } else {
    q_selectedBtn.classList.add('wrong');
    document.querySelectorAll('#p4Choices .choice-btn').forEach(b=>{const t=b.querySelector('.choice-text');if(t&&t.textContent.startsWith(q_correctKey))b.classList.add('correct');});
    q_lives--;q_updateLives();
    document.getElementById('p4Feedback').textContent=c.feedbackWrong(q_correctKey);
    document.getElementById('p4Feedback').className='feedback-msg wrong';
  }
  q_qIdx++;
  if(q_qIdx>=Q_TOTAL||q_lives<=0){setTimeout(q_showResult,1100);}
  else{document.getElementById('p4NextBtn').style.display='';}
}
function q_nextQuestion(){q_loadQuestion();}
function q_showResult(){
  document.getElementById('resultTitle').textContent=Q_COPY[lang].resultTitle;
  document.getElementById('resultSub').textContent=Q_COPY[lang].resultSub;
  showResult(4, q_score, Q_MAX);
}


// ── FLUSH PROGRESS ON NAVIGATION / PAGE HIDE ──
function flushActiveTabProgress() {
  switch(activeTab) {
    case 1: if(m_score>0) saveProgress(ACT_IDS[1],Math.min(100,Math.round((m_score/M_MAX)*100))); break;
    case 2: if(s_score>0) saveProgress(ACT_IDS[2],Math.round((s_score/S_MAX)*100)); break;
    case 3: if(so_score>0) saveProgress(ACT_IDS[3],Math.round((so_score/SO_MAX)*100)); break;
    case 4: if(q_score>0) saveProgress(ACT_IDS[4],Math.round((q_score/Q_MAX)*100)); break;
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
// Start only the active tab's game on page load
if(activeTab===1) m_startGame();
else if(activeTab===2) s_startGame();
else if(activeTab===3) so_startGame();
else if(activeTab===4) q_startGame();
</script>

</div><!-- /page-wrap -->
</body>
</html>