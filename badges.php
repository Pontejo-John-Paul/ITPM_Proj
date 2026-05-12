<?php
session_start();
require_once 'database.php';

if (!isset($_SESSION['student_id']) || $_SESSION['role'] !== 'student') {
    header("Location: index.php");
    exit;
}

$student_id = $_SESSION['student_id'];

// ── BADGE CONFIG ──
$badge_config = [
    'letters'     => ['title'=>'Alphabet Ace',   'emoji'=>'🔤', 'color'=>'bc1', 'subtitle'=>'Letters Lesson'],
    'motorskills' => ['title'=>'Hygiene Hero',   'emoji'=>'🙌', 'color'=>'bc2', 'subtitle'=>'Motor Skills Lesson'],
    'shapes'      => ['title'=>'Shape Wizard',   'emoji'=>'🔷', 'color'=>'bc3', 'subtitle'=>'Shapes Lesson'],
    'animals'     => ['title'=>'Animal Expert',  'emoji'=>'🐾', 'color'=>'bc4', 'subtitle'=>'Animals Lesson'],
    'colors'      => ['title'=>'Color Champion', 'emoji'=>'🎨', 'color'=>'bc5', 'subtitle'=>'Colors Lesson'],
    'numbers'     => ['title'=>'Number Ninja',   'emoji'=>'🔢', 'color'=>'bc6', 'subtitle'=>'Numbers Lesson'],
    'heroes'      => ['title'=>'History Hero',   'emoji'=>'🏅', 'color'=>'bc7', 'subtitle'=>'National Heroes'],
    'body'        => ['title'=>'Body Boss',      'emoji'=>'🧍', 'color'=>'bc8', 'subtitle'=>'Body Parts Lesson'],
];

// ── AUTO-AWARD BADGES ──
foreach ($badge_config as $lesson_name => $cfg) {

    // Get lesson_id
    $stmt = $conn->prepare("SELECT lesson_id FROM lessons WHERE lesson_name = ?");
    $stmt->bind_param("s", $lesson_name);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    if (!$row) continue;

    $lesson_id = $row['lesson_id'];

    // 🔥 CHECK FINAL EXAM (ETO ANG BAGONG LOGIC)
    $stmt = $conn->prepare(
        "SELECT COUNT(*) as exam_done 
         FROM final_exam_result 
         WHERE student_id = ? AND lesson_id = ?"
    );
    $stmt->bind_param("ii", $student_id, $lesson_id);
    $stmt->execute();
    $exam_done = $stmt->get_result()->fetch_assoc()['exam_done'];
    $stmt->close();

    // ❗ UNLOCK BADGE ONLY IF FINAL EXAM IS DONE
    if ($exam_done > 0) {
        $badge_name = $cfg['title'];

        $stmt = $conn->prepare(
            "INSERT IGNORE INTO students_badge_table (student_id, badge_name)
             VALUES (?, ?)"
        );
        $stmt->bind_param("is", $student_id, $badge_name);
        $stmt->execute();
        $stmt->close();
    }
}

// ── FETCH EARNED BADGES ──
$stmt = $conn->prepare(
    "SELECT badge_name, date_completed FROM students_badge_table WHERE student_id = ?"
);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$earned_rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$earned_badges = [];
foreach ($earned_rows as $row) {
    $earned_badges[$row['badge_name']] = $row['date_completed'];
}

// ── FETCH LESSON PROGRESS FOR LOCKED BADGES ──
$lesson_progress = [];

foreach ($badge_config as $lesson_name => $cfg) {

    $stmt = $conn->prepare("SELECT lesson_id FROM lessons WHERE lesson_name = ?");
    $stmt->bind_param("s", $lesson_name);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$row) {
        $lesson_progress[$lesson_name] = 0;
        continue;
    }

    $lesson_id = $row['lesson_id'];

    // Count quizzes
    $stmt = $conn->prepare("SELECT COUNT(*) as total FROM quizzes WHERE lesson_id = ?");
    $stmt->bind_param("i", $lesson_id);
    $stmt->execute();
    $total = $stmt->get_result()->fetch_assoc()['total'];
    $stmt->close();

    // Count completed
    $stmt = $conn->prepare(
        "SELECT COUNT(*) as completed
         FROM student_activity_progress sap
         JOIN quizzes q ON sap.quizzes_id = q.quizzes_id
         WHERE sap.student_id = ? AND q.lesson_id = ? AND sap.status = 'completed'"
    );
    $stmt->bind_param("ii", $student_id, $lesson_id);
    $stmt->execute();
    $completed = $stmt->get_result()->fetch_assoc()['completed'];
    $stmt->close();

    // Check final exam status
    $stmt = $conn->prepare(
        "SELECT COUNT(*) as exam_done 
         FROM final_exam_result 
         WHERE student_id = ? AND lesson_id = ?"
    );
    $stmt->bind_param("ii", $student_id, $lesson_id);
    $stmt->execute();
    $exam_done = $stmt->get_result()->fetch_assoc()['exam_done'];
    $stmt->close();

    // 🔥 PROGRESS LOGIC
    if ($exam_done > 0) {
        $lesson_progress[$lesson_name] = 100;
    } else {
        $lesson_progress[$lesson_name] = $total > 0 
            ? round(($completed / $total) * 80) // hanggang 80% lang pag wala pang final exam
            : 0;
    }
}

$total_badges  = count($badge_config);
$earned_count  = count($earned_badges);
$locked_count  = $total_badges - $earned_count;
$completion_pct = round(($earned_count / $total_badges) * 100);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>E-KINDER — Badges</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Fredoka+One&family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">

<?php include 'navbar-styles.php'; ?>

<style>
:root { --green-dark: #0d5407; --green-mid: #1a7a10; --cream: #fffbf4; }
* { box-sizing: border-box; margin: 0; padding: 0; }
body { font-family: 'Nunito', sans-serif; background: var(--cream); overflow-x: hidden; }
.page-header { background: linear-gradient(160deg, #fff9db 0%, #fffbf4 55%, #f3f0ff 100%); padding: 60px 20px 70px; text-align: center; position: relative; overflow: hidden; }
.page-header::after { content: ''; position: absolute; bottom:0; left:0; right:0; height:4px; background: linear-gradient(90deg,#fcc419,#ff922b,#f06595,#9775fa,#fcc419); background-size: 300% 100%; animation: rainbowSlide 4s linear infinite; }
@keyframes rainbowSlide { 0%{background-position:0% 0%;} 100%{background-position:300% 0%;} }
.blob { position:absolute; border-radius:50%; filter:blur(60px); opacity:0.3; pointer-events:none; }
.blob-1 { width:260px; height:260px; background:#ffe066; top:-60px; left:-70px; }
.blob-2 { width:200px; height:200px; background:#d0bfff; top:-30px; right:-50px; }
.page-badge { display:inline-block; background:#fff; border:2.5px solid #ffe066; border-radius:50px; padding:6px 18px; font-size:0.82rem; font-weight:800; color:#e67700; letter-spacing:1.5px; text-transform:uppercase; margin-bottom:18px; box-shadow:0 4px 14px rgba(230,119,0,0.12); position:relative; z-index:1; }
.page-header h1 { font-family:'Fredoka One',cursive; font-size:clamp(2rem,5vw,3.2rem); color:#333; margin-bottom:12px; position:relative; z-index:1; }
.page-header h1 span { color:#fcc419; -webkit-text-stroke: 1.5px #e67700; }
.page-header p { font-size:1rem; color:#777; font-weight:600; max-width:480px; margin:0 auto; position:relative; z-index:1; }
.float-emoji { position:absolute; animation:floatBounce 3s ease-in-out infinite; pointer-events:none; z-index:1; user-select:none; }
.fe1{top:14%;left:6%;font-size:2.4rem;animation-delay:0s;} .fe2{top:22%;right:7%;font-size:2rem;animation-delay:.7s;}
.fe3{bottom:20%;left:9%;font-size:1.8rem;animation-delay:1.3s;} .fe4{bottom:16%;right:6%;font-size:2.2rem;animation-delay:1s;}
@keyframes floatBounce{0%,100%{transform:translateY(0) rotate(-5deg);}50%{transform:translateY(-14px) rotate(5deg);}}
.summary-bar { background: #fff; border-radius: 20px; padding: 24px 32px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 20px; box-shadow: 0 6px 24px rgba(0,0,0,0.07); border: 2.5px solid #f0f0f0; margin-bottom: 48px; }
.summary-stat { text-align: center; }
.summary-num { font-family: 'Fredoka One', cursive; font-size: 2.2rem; line-height: 1; color: var(--green-dark); }
.summary-label { font-size: 0.78rem; font-weight: 800; color: #aaa; text-transform: uppercase; letter-spacing: 1px; }
.summary-divider { width: 1px; height: 48px; background: #e9ecef; }
@media(max-width:576px){ .summary-divider{ display:none; } .summary-bar{ justify-content:center; } }
.section-label{display:flex;align-items:center;gap:12px;margin-bottom:36px;}
.section-label-line{flex:1;height:2px;background:linear-gradient(90deg,#e0e0e0,transparent);}
.section-label-line.right{background:linear-gradient(90deg,transparent,#e0e0e0);}
.section-label-text{font-family:'Fredoka One',cursive;font-size:1.05rem;color:#bbb;letter-spacing:2px;white-space:nowrap;}
.badges-section { padding: 72px 0 90px; }
.badge-card { border-radius: 28px; padding: 32px 20px 28px; text-align: center; border: 3px solid transparent; box-shadow: 0 6px 24px rgba(0,0,0,0.07); position: relative; overflow: hidden; transition: transform 0.28s cubic-bezier(.34,1.56,.64,1), box-shadow 0.28s; animation: cardIn 0.55s ease both; }
.badge-card::before { content: ''; position: absolute; top:0; left:0; right:0; height:6px; background: var(--bc-color, #ccc); border-radius: 28px 28px 0 0; }
.badge-card::after { content: ''; position: absolute; top:-60%; left:-70%; width: 55%; height: 200%; background: linear-gradient(105deg,transparent 40%,rgba(255,255,255,0.55) 50%,transparent 60%); transition: left 0.5s ease; pointer-events: none; }
.badge-card:hover::after { left:130%; }
.badge-card.earned { border-color: var(--bc-color); box-shadow: 0 8px 30px rgba(0,0,0,0.1), 0 0 0 4px var(--bc-glow, rgba(0,0,0,0.05)); }
.badge-card.earned:hover { transform: translateY(-10px) scale(1.04); box-shadow: 0 22px 48px rgba(0,0,0,0.13), 0 0 0 4px var(--bc-glow); }
.badge-card.locked { background: #f9f9f9 !important; opacity: 0.65; cursor: not-allowed; filter: grayscale(0.6); }
.badge-card.locked:hover { transform: none; box-shadow: 0 6px 24px rgba(0,0,0,0.07); }
@keyframes cardIn{from{opacity:0;transform:translateY(36px) scale(0.88);}to{opacity:1;transform:translateY(0) scale(1);}}
.badge-ring { width: 100px; height: 100px; border-radius: 50%; margin: 0 auto 16px; position: relative; display: flex; align-items: center; justify-content: center; }
.badge-card.earned .badge-ring { background: var(--bc-bg); box-shadow: 0 0 0 5px var(--bc-color), 0 8px 24px rgba(0,0,0,0.12); animation: badgePulse 2.5s ease-in-out infinite; }
.badge-card.locked .badge-ring { background: #eee; box-shadow: 0 0 0 4px #ddd; }
@keyframes badgePulse { 0%,100%{ box-shadow: 0 0 0 5px var(--bc-color), 0 8px 24px rgba(0,0,0,0.1); } 50%{ box-shadow: 0 0 0 8px var(--bc-glow), 0 12px 30px rgba(0,0,0,0.15); } }
.badge-emoji { font-size: 3rem; line-height: 1; }
.badge-card.locked .badge-emoji { font-size: 2rem; opacity: 0.4; }
.badge-crown { position: absolute; top: -8px; right: -4px; font-size: 1.3rem; animation: crownBounce 2s ease-in-out infinite; }
@keyframes crownBounce { 0%,100%{transform:rotate(-10deg) scale(1);} 50%{transform:rotate(10deg) scale(1.2);} }
.badge-title { font-family: 'Fredoka One', cursive; font-size: 1.1rem; color: #222; margin-bottom: 4px; }
.badge-card.locked .badge-title { color: #bbb; }
.badge-subtitle { font-size: 0.78rem; font-weight: 700; color: #aaa; margin-bottom: 12px; }
.badge-pill { display: inline-block; border-radius: 50px; padding: 5px 16px; font-family: 'Fredoka One', cursive; font-size: 0.82rem; }
.badge-card.earned .badge-pill { background: var(--bc-color); color: #fff; box-shadow: 0 4px 12px rgba(0,0,0,0.15); }
.badge-card.locked .badge-pill { background: #e9ecef; color: #bbb; }
.badge-date { display: block; margin-top: 8px; font-size: 0.72rem; font-weight: 700; color: #ccc; letter-spacing: 0.5px; }
.bc1{background:#fff0f6;--bc-color:#f06595;--bc-glow:rgba(240,101,149,0.25);--bc-bg:#fce4ec;}
.bc2{background:#e7f5ff;--bc-color:#339af0;--bc-glow:rgba(51,154,240,0.25);--bc-bg:#e3f2fd;}
.bc3{background:#fff9db;--bc-color:#fcc419;--bc-glow:rgba(252,196,25,0.3);--bc-bg:#fff8e1;}
.bc4{background:#ebfbee;--bc-color:#51cf66;--bc-glow:rgba(81,207,102,0.25);--bc-bg:#e8f5e9;}
.bc5{background:#f3f0ff;--bc-color:#9775fa;--bc-glow:rgba(151,117,250,0.25);--bc-bg:#ede7f6;}
.bc6{background:#fff4e6;--bc-color:#ff922b;--bc-glow:rgba(255,146,43,0.25);--bc-bg:#fff3e0;}
.bc7{background:#e3fafc;--bc-color:#22b8cf;--bc-glow:rgba(34,184,207,0.25);--bc-bg:#e0f7fa;}
.bc8{background:#fff5f5;--bc-color:#ff6b6b;--bc-glow:rgba(255,107,107,0.25);--bc-bg:#ffebee;}
.locked-note { text-align: center; margin-top: 48px; background: linear-gradient(135deg, #fff9db, #f3f0ff); border-radius: 24px; padding: 36px 24px; border: 2.5px dashed #ffe066; }
.locked-note p { font-family: 'Fredoka One', cursive; font-size: 1.1rem; color: #888; margin: 0; }
footer { background: var(--green-dark); color: rgba(255,255,255,0.7); text-align: center; padding: 32px 20px; font-size: 0.85rem; font-weight: 700; }
footer .footer-brand { font-family: 'Fredoka One', cursive; font-size: 1.6rem; color: #fff; display: block; margin-bottom: 8px; }
footer a { color: rgba(255,255,255,0.5); text-decoration: none; margin: 0 8px; }
footer a:hover { color: #fff; }
</style>
</head>
<body>

<?php include 'navbar.php'; ?>

<section class="page-header">
    <div class="blob blob-1"></div>
    <div class="blob blob-2"></div>
    <span class="float-emoji fe1">🏆</span>
    <span class="float-emoji fe2">⭐</span>
    <span class="float-emoji fe3">🎖️</span>
    <span class="float-emoji fe4">✨</span>
    <div class="page-badge">🏅 Your Achievements</div>
    <h1>My <span>Badges</span></h1>
    <p>Complete all activities in a lesson to earn your badge. Keep it up, superstar!</p>
</section>

<section class="badges-section">
    <div class="container">

        <!-- Summary bar -->
        <div class="summary-bar">
            <div class="summary-stat">
                <div class="summary-num"><?php echo $earned_count; ?></div>
                <div class="summary-label">Badges Earned</div>
            </div>
            <div class="summary-divider"></div>
            <div class="summary-stat">
                <div class="summary-num"><?php echo $locked_count; ?></div>
                <div class="summary-label">Still Locked</div>
            </div>
            <div class="summary-divider"></div>
            <div class="summary-stat">
                <div class="summary-num"><?php echo $total_badges; ?></div>
                <div class="summary-label">Total Badges</div>
            </div>
            <div class="summary-divider"></div>
            <div class="summary-stat">
                <div class="summary-num" style="color:#fcc419;"><?php echo $completion_pct; ?>%</div>
                <div class="summary-label">Completion</div>
            </div>
        </div>

        <!-- EARNED BADGES -->
        <?php if ($earned_count > 0): ?>
        <div class="section-label">
            <div class="section-label-line"></div>
            <span class="section-label-text">🏅 EARNED BADGES</span>
            <div class="section-label-line right"></div>
        </div>
        <div class="row g-4 mb-5">
            <?php foreach ($badge_config as $lesson_name => $cfg):
                if (!isset($earned_badges[$cfg['title']])) continue;
                $date_earned = date('F j, Y', strtotime($earned_badges[$cfg['title']]));
            ?>
            <div class="col-6 col-md-4 col-lg-3">
                <div class="badge-card <?php echo $cfg['color']; ?> earned">
                    <div class="badge-ring">
                        <span class="badge-emoji"><?php echo $cfg['emoji']; ?></span>
                        <span class="badge-crown">👑</span>
                    </div>
                    <div class="badge-title"><?php echo $cfg['title']; ?></div>
                    <div class="badge-subtitle"><?php echo $cfg['subtitle']; ?></div>
                    <span class="badge-pill">✅ Completed!</span>
                    <span class="badge-date">Earned: <?php echo $date_earned; ?></span>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <!-- LOCKED BADGES -->
        <div class="section-label">
            <div class="section-label-line"></div>
            <span class="section-label-text">🔒 LOCKED BADGES</span>
            <div class="section-label-line right"></div>
        </div>
        <div class="row g-4">
            <?php foreach ($badge_config as $lesson_name => $cfg):
                if (isset($earned_badges[$cfg['title']])) continue;
                $pct = $lesson_progress[$lesson_name] ?? 0;
                $pill_text = $pct === 0 ? '🔒 Not started' : "🔒 {$pct}% done";
            ?>
            <div class="col-6 col-md-4 col-lg-3">
                <div class="badge-card <?php echo $cfg['color']; ?> locked">
                    <div class="badge-ring"><span class="badge-emoji"><?php echo $cfg['emoji']; ?></span></div>
                    <div class="badge-title"><?php echo $cfg['title']; ?></div>
                    <div class="badge-subtitle"><?php echo $cfg['subtitle']; ?></div>
                    <span class="badge-pill"><?php echo $pill_text; ?></span>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <?php if ($locked_count > 0): ?>
        <div class="locked-note">
            <p>🌟 Complete all activities in a lesson to unlock its badge! You've got this! 💪</p>
        </div>
        <?php endif; ?>

    </div>
</section>

<footer>
    <span class="footer-brand">E-KINDER</span>
    <p>An interactive learning platform for Filipino kindergarteners 🇵🇭</p>
    <div style="margin-top:14px;">
        <a href="student-dashboard.php">Home</a>
        <a href="lessons.php">Lessons</a>
        <a href="activities.php">Quizz</a>
        <a href="badges.php">Badges</a>
        <a href="about.php">About</a>
    </div>
    <p style="margin-top:16px; font-size:0.75rem; opacity:0.5;">© 2025 E-KINDER. Made with ❤️ for young Filipino learners.</p>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>