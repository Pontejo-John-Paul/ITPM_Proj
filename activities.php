<?php
session_start();
require_once 'database.php';

// Guard
if (!isset($_SESSION['student_id']) || $_SESSION['role'] !== 'student') {
    header("Location: index.php");
    exit;
}

$student_id = $_SESSION['student_id'];

// ── FETCH ALL LESSONS WITH THEIR quizzes + STUDENT PROGRESS ──
$lessons_query = $conn->query("SELECT * FROM lessons ORDER BY lesson_id");
$lessons = $lessons_query->fetch_all(MYSQLI_ASSOC);

$data = [];

foreach ($lessons as $lesson) {
    $lid   = $lesson['lesson_id'];
    $lname = $lesson['lesson_name'];

    // Get all quizzes for this lesson
    $act_stmt = $conn->prepare("SELECT * FROM quizzes WHERE lesson_id = ? ORDER BY quizzes_id");
    $act_stmt->bind_param("i", $lid);
    $act_stmt->execute();
    $quizzes = $act_stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $act_stmt->close();

    $total     = count($quizzes);
    $completed = 0;
    $quizzes_rows = [];

    foreach ($quizzes as $act) {
        $aid = $act['quizzes_id'];

        // Get student progress for this quizzes
        $prog_stmt = $conn->prepare(
            "SELECT status, score FROM student_activity_progress
             WHERE student_id = ? AND quizzes_id = ?"
        );
        $prog_stmt->bind_param("ii", $student_id, $aid);
        $prog_stmt->execute();
        $prog = $prog_stmt->get_result()->fetch_assoc();
        $prog_stmt->close();

        $status = $prog['status'] ?? 'not_started';
        $score  = $prog['score']  ?? 0;

        if ($status === 'completed') $completed++;

        $quizzes_rows[] = [
            'quizzes_id'   => $aid,
            'quizzes_name' => $act['quizzes_name'],
            'status'        => $status,
            'score'         => $score,
        ];
    }

// check if may exam na
$exam_stmt = $conn->prepare("
SELECT score FROM final_exam_result 
WHERE student_id=? AND lesson_id=?
");
$exam_stmt->bind_param("ii", $student_id, $lid);
$exam_stmt->execute();
$exam_result = $exam_stmt->get_result()->fetch_assoc();

$exam_done = $exam_result ? 1 : 0;
$exam_score = $exam_result['score'] ?? 0;

// NEW COMPUTATION: 4 quizzes = 70% (17.5% each), exam = 30%
$quiz_pct  = $total > 0 ? ($completed / $total) * 70 : 0;
$exam_pct  = $exam_done ? 30 : 0;
$completion_pct = round($quiz_pct + $exam_pct);

    // Average score across all activities (only those with scores)
    $score_sum   = array_sum(array_column($quizzes_rows, 'score'));
    $avg_score   = $total > 0 ? round($score_sum / $total) : 0;

    $data[] = [
        'lesson_id'      => $lid,
        'lesson_name'    => $lname,
        'total'          => $total,
        'completed'      => $completed,
        'percent'        => $completion_pct,
        'avg_score'      => $avg_score,
        'quizzes'     => $quizzes_rows,
        'final_score' => $exam_score,
        'final_done'  => $exam_done,
    ];
}

// ── LESSON DISPLAY CONFIG ──
$lesson_config = [
    'letters'  => ['icon'=>'🔤', 'label'=>'Alphabet',   'color'=>'ac1', 'prefix'=>'activity-letters',  'title'=>'Letters',         'combined'=>true,  'combined_file'=>'activity-letters.php'],
    'motorskills' => ['icon'=>'🙌', 'label'=>'Hygiene',    'color'=>'ac2', 'prefix'=>'activity-motorskills', 'title'=>'Motor Skills',    'combined'=>true,  'combined_file'=>'activity-motorskills.php'],
    'shapes'   => ['icon'=>'🔷', 'label'=>'Geometry',   'color'=>'ac3', 'prefix'=>'activity-shapes',   'title'=>'Shapes',          'combined'=>true,  'combined_file'=>'activity-shapes.php'],
    'animals'  => ['icon'=>'🐾', 'label'=>'Science',    'color'=>'ac4', 'prefix'=>'activity-animals',  'title'=>'Animals',         'combined'=>true,  'combined_file'=>'activity-animals.php'],
    'colors'   => ['icon'=>'🎨', 'label'=>'Art',         'color'=>'ac5', 'prefix'=>'activity-colors',   'title'=>'Colors',          'combined'=>true,  'combined_file'=>'activity-colors.php'],
    'numbers'  => ['icon'=>'🔢', 'label'=>'Math',        'color'=>'ac6', 'prefix'=>'activity-numbers',  'title'=>'Numbers',         'combined'=>true,  'combined_file'=>'activity-numbers.php'],
    'heroes'   => ['icon'=>'🦁', 'label'=>'Filipino',    'color'=>'ac7', 'prefix'=>'activity-heroes',   'title'=>'National Heroes', 'combined'=>true,  'combined_file'=>'activity-heroes.php'],
    'body'     => ['icon'=>'🧍', 'label'=>'Health',      'color'=>'ac8', 'prefix'=>'activity-body',     'title'=>'Body Parts',      'combined'=>true,  'combined_file'=>'activity-body.php'],
];

// Tab number per quizzes_name for combined lessons
$combined_tab_map = [
    // Letters
    'letter_matching'    => 1,
    'spot_the_letter'    => 2,
    'fill_in_the_letter' => 3,
    'letter_tracing'     => 4,
    // Motor Skills
    'handwashing_steps'  => 1,
    'hygiene_match'      => 2,
    'daily_routine'      => 3,
    'body_care_quiz'     => 4,
    // Shapes
    'shape_matching'     => 1,
    'spot_the_shape'     => 2,
    'shape_sorting'      => 3,
    'count_the_sides'    => 4,
    // Animals
    'animal_name_match'  => 1,
    'spot_the_animal'    => 2,
    'animal_sorting'     => 3,
    'animal_quiz'        => 4,
    // Numbers
    'number_recognition' => 1,
    'counting_quiz'      => 2,
    'number_order'       => 3,
    'odd_or_even'        => 4,
    // Heroes
    'hero_match'    => 1,
    'who_am_i'      => 2,
    'fill_in_blank' => 3,
    'hero_trivia'   => 4,
    // Colors
    'color_match'       => 1,
    'spot_the_color'    => 2,
    'color_mixing_quiz' => 3,
    'color_hunt_quiz'   => 4,
    // Body
    'body_part_match'    => 1,
    'point_it_out'       => 2,
    'body_part_sorting'  => 3,
    'simon_says'         => 4,
];

// ── ACTIVITY DISPLAY NAMES + FILE SLUGS ──
$quizzes_display = [
    'letter_matching'    => ['Letter Matching',    'matching'],
    'spot_the_letter'    => ['Spot the Letter',    'spot'],
    'fill_in_the_letter' => ['Fill in the Letter', 'fill'],
    'letter_tracing'     => ['Letter Tracing',     'tracing'],
    'handwashing_steps'  => ['Handwashing Steps',   'handwash'],
    'hygiene_match'      => ['Hygiene Match',        'match'],
    'daily_routine'      => ['Daily Routine',        'routine'],
    'body_care_quiz'     => ['Body Care Quiz',       'quiz'],
    'shape_matching'     => ['Shape Matching',      'matching'],
    'spot_the_shape'     => ['Spot the Shape',      'spot'],
    'shape_sorting'      => ['Shape Sorting',       'sorting'],
    'count_the_sides'    => ['Count the Sides',     'count'],
    'animal_name_match'  => ['Animal Name Match',   'match'],
    'spot_the_animal'    => ['Spot the Animal',     'spot'],
    'animal_sorting'     => ['Animal Sorting',      'sorting'],
    'animal_quiz'        => ['Animal Quiz',         'quiz'],
    'color_match'        => ['Color Match',         'match'],
    'spot_the_color'     => ['Spot the Color',      'spot'],
    'color_mixing_quiz'  => ['Color Mixing Quiz',   'mixing'],
    'color_hunt_quiz'    => ['Color Hunt Quiz',     'hunt'],
    'number_recognition' => ['Number Recognition',  'recognition'],
    'counting_quiz'      => ['Counting Quiz',        'counting'],
    'number_order'       => ['Number Order',         'order'],
    'odd_or_even'        => ['Odd or Even',          'oddeven'],
    'hero_match'         => ['Hero Match',           'match'],
    'who_am_i'           => ['Who Am I?',            'whoami'],
    'fill_in_the_blank'  => ['Fill in the Blank',    'fillin'],
    'hero_trivia'        => ['Hero Trivia',          'trivia'],
    'body_part_match'    => ['Body Part Match',      'match'],
    'point_it_out'       => ['Point It Out',         'pointout'],
    'body_part_sorting'  => ['Body Part Sorting',    'sorting'],
    'simon_says'         => ['Simon Says',           'simonsays'],
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>E-KINDER — Quizzes</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Fredoka+One&family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">

<?php include 'navbar-styles.php'; ?>

<style>
:root { --green-dark:#0d5407; --green-mid:#1a7a10; --cream:#fffbf4; }
* { box-sizing:border-box; margin:0; padding:0; }
body { font-family:'Nunito',sans-serif; background:var(--cream); overflow-x:hidden; }

.page-header { background:linear-gradient(160deg,#fff4e6 0%,#fffbf4 55%,#e3f2fd 100%); padding:60px 20px 70px; text-align:center; position:relative; overflow:hidden; }
.page-header::after { content:''; position:absolute; bottom:0; left:0; right:0; height:4px; background:linear-gradient(90deg,#ff922b,#fcc419,#51cf66,#339af0,#ff922b); background-size:300% 100%; animation:rainbowSlide 4s linear infinite; }
@keyframes rainbowSlide { 0%{background-position:0% 0%;} 100%{background-position:300% 0%;} }
.blob { position:absolute; border-radius:50%; filter:blur(60px); opacity:0.28; pointer-events:none; }
.blob-1 { width:260px; height:260px; background:#ffd6a0; top:-60px; left:-70px; }
.blob-2 { width:200px; height:200px; background:#a8edba; top:-30px; right:-50px; }
.page-badge { display:inline-block; background:#fff; border:2.5px solid #ffe0b2; border-radius:50px; padding:6px 18px; font-size:0.82rem; font-weight:800; color:#e65100; letter-spacing:1.5px; text-transform:uppercase; margin-bottom:18px; box-shadow:0 4px 14px rgba(230,81,0,0.12); position:relative; z-index:1; }
.page-header h1 { font-family:'Fredoka One',cursive; font-size:clamp(2rem,5vw,3.2rem); color:#333; margin-bottom:12px; position:relative; z-index:1; }
.page-header h1 span { color:#ff922b; }
.page-header p { font-size:1rem; color:#777; font-weight:600; max-width:480px; margin:0 auto; position:relative; z-index:1; }
.float-emoji { position:absolute; animation:floatBounce 3s ease-in-out infinite; pointer-events:none; z-index:1; user-select:none; }
.fe1{top:14%;left:6%;font-size:2.2rem;animation-delay:0s;} .fe2{top:22%;right:7%;font-size:1.8rem;animation-delay:.7s;}
.fe3{bottom:20%;left:9%;font-size:1.6rem;animation-delay:1.3s;} .fe4{bottom:16%;right:6%;font-size:2rem;animation-delay:1s;}
@keyframes floatBounce{0%,100%{transform:translateY(0) rotate(-5deg);}50%{transform:translateY(-14px) rotate(5deg);}}

.section-label{display:flex;align-items:center;gap:12px;margin-bottom:36px;}
.section-label-line{flex:1;height:2px;background:linear-gradient(90deg,#e0e0e0,transparent);}
.section-label-line.right{background:linear-gradient(90deg,transparent,#e0e0e0);}
.section-label-text{font-family:'Fredoka One',cursive;font-size:1.05rem;color:#bbb;letter-spacing:2px;white-space:nowrap;}

.quizzes-section { padding:72px 0 90px; }
.quizzes-card { background:#fff; border-radius:24px; padding:0; box-shadow:0 6px 24px rgba(0,0,0,0.07); border:2.5px solid #f0f0f0; transition:transform 0.25s cubic-bezier(.34,1.56,.64,1),box-shadow 0.25s; animation:cardIn 0.5s ease both; position:relative; overflow:hidden; }
.quizzes-card::before { content:''; position:absolute; top:0; left:0; right:0; height:5px; background:var(--ac-color,#ccc); border-radius:24px 24px 0 0; z-index:1; }
.quizzes-card:hover { transform:translateY(-5px); box-shadow:0 18px 40px rgba(0,0,0,0.11); }
@keyframes cardIn{from{opacity:0;transform:translateY(28px);}to{opacity:1;transform:translateY(0);}}
.quizzes-card:nth-child(1){animation-delay:.04s;} .quizzes-card:nth-child(2){animation-delay:.08s;} .quizzes-card:nth-child(3){animation-delay:.12s;}
.quizzes-card:nth-child(4){animation-delay:.16s;} .quizzes-card:nth-child(5){animation-delay:.20s;} .quizzes-card:nth-child(6){animation-delay:.24s;}
.quizzes-card:nth-child(7){animation-delay:.28s;} .quizzes-card:nth-child(8){animation-delay:.32s;}

.ac-head { padding:20px 20px 14px; display:flex; align-items:center; gap:14px; background:var(--ac-bg,#f9f9f9); border-bottom:2px solid #f0f0f0; }
.ac-icon { width:50px; height:50px; border-radius:50%; background:#fff; display:flex; align-items:center; justify-content:center; font-size:1.5rem; flex-shrink:0; box-shadow:0 4px 12px rgba(0,0,0,0.1); transition:transform 0.3s cubic-bezier(.34,1.56,.64,1); }
.quizzes-card:hover .ac-icon { transform:scale(1.15) rotate(-8deg); }
.ac-meta { flex:1; min-width:0; }
.ac-title { font-family:'Fredoka One',cursive; font-size:1.05rem; color:#222; margin-bottom:3px; }
.ac-tag { display:inline-block; background:rgba(255,255,255,0.7); color:var(--ac-tag-color,#888); font-size:0.66rem; font-weight:800; letter-spacing:0.5px; text-transform:uppercase; padding:2px 9px; border-radius:50px; border:1.5px solid rgba(0,0,0,0.06); }
.ac-percent { font-family:'Fredoka One',cursive; font-size:1.4rem; color:var(--ac-color,#ccc); line-height:1; text-align:right; }
.ac-percent small { font-size:0.65rem; font-family:'Nunito',sans-serif; color:#bbb; font-weight:700; display:block; }

.ac-progress-wrap { padding:12px 20px 0; }
.ac-progress-row { display:flex; justify-content:space-between; font-size:0.72rem; font-weight:800; color:#bbb; margin-bottom:5px; }
.ac-progress-row span:last-child { color:var(--ac-color); }
.ac-bar-track { height:10px; border-radius:50px; background:#f0f0f0; position:relative; overflow:visible; margin-bottom:12px; }
.ac-bar-fill { height:100%; border-radius:50px; background:linear-gradient(90deg,var(--ac-color),var(--ac-color-light,#ccc)); position:relative; transition:width 1.2s cubic-bezier(.22,1,.36,1); box-shadow:0 2px 8px rgba(0,0,0,0.13); }
.ac-bar-fill::after { content:''; position:absolute; right:-2px; top:50%; transform:translateY(-50%); width:16px; height:16px; border-radius:50%; background:var(--ac-color); border:3px solid #fff; box-shadow:0 2px 6px rgba(0,0,0,0.2); }

.ac-row { display:flex; align-items:center; gap:8px; padding:10px 20px 4px; border-bottom:1px solid #f5f5f5; transition:background 0.15s; flex-wrap:wrap; }
.ac-row:last-child { border-bottom:none; }
.ac-row:hover { background:#fafafa; }
.ac-row-top { display:flex; align-items:center; gap:8px; width:100%; }
.ac-status-dot { width:22px; height:22px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:0.6rem; flex-shrink:0; }
.st-completed   { background:var(--ac-color); color:#fff; }
.st-in_progress { background:#fff8e1; color:#f59f00; border:2px solid #fcc419; }
.st-not_started { background:#f5f5f5; color:#ccc; border:2px solid #e8e8e8; }
.ac-row-name { flex:1; font-size:0.8rem; font-weight:700; color:#777; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
.ac-row-name.done-name { color:#333; }
.ac-row-score { font-size:0.72rem; font-weight:800; color:var(--ac-color); min-width:44px; text-align:right; }
.ac-row-score.no-score { color:#ddd; }

/* Per-quizzes mini progress bar */
.ac-quizzes { padding:2px 0 14px; }
.ac-mini-bar-wrap { width:100%; padding:0 30px 8px 30px; }
.ac-mini-bar-track { height:5px; background:#f0f0f0; border-radius:99px; overflow:hidden; }
.ac-mini-bar-fill { height:100%; border-radius:99px; background:linear-gradient(90deg,var(--ac-color),var(--ac-color-light,#ccc)); transition:width 1.4s cubic-bezier(.22,1,.36,1); }

.ac-btn { flex-shrink:0; font-family:'Fredoka One',cursive; font-size:0.7rem; padding:5px 11px; border-radius:50px; border:none; cursor:pointer; text-decoration:none; display:inline-flex; align-items:center; gap:4px; transition:transform 0.18s cubic-bezier(.34,1.56,.64,1),box-shadow 0.18s; white-space:nowrap; }
.ac-btn:hover { transform:scale(1.09); box-shadow:0 4px 12px rgba(0,0,0,0.15); text-decoration:none; }
.btn-start    { background:var(--ac-color); color:#fff; }
.btn-continue { background:#fff8e1; color:#e67700; border:1.5px solid #fcc419; }
.btn-redo     { background:#f0fdf4; color:var(--ac-color); border:1.5px solid var(--ac-color); }

/* Avg score badge on card header */
.ac-avg-score { font-family:'Fredoka One',cursive; font-size:.72rem; color:var(--ac-tag-color); background:var(--ac-bg); border:1.5px solid var(--ac-color); border-radius:var(--pill,999px); padding:3px 10px; white-space:nowrap; margin-top:4px; display:inline-block; }

.ac1{--ac-color:#f06595;--ac-color-light:#faa2c1;--ac-bg:#fff0f6;--ac-tag-color:#c2255c;}
.ac2{--ac-color:#339af0;--ac-color-light:#74c0fc;--ac-bg:#e7f5ff;--ac-tag-color:#1971c2;}
.ac3{--ac-color:#fcc419;--ac-color-light:#ffe066;--ac-bg:#fff9db;--ac-tag-color:#e67700;}
.ac4{--ac-color:#51cf66;--ac-color-light:#8ce99a;--ac-bg:#ebfbee;--ac-tag-color:#2f9e44;}
.ac5{--ac-color:#9775fa;--ac-color-light:#c5b5ff;--ac-bg:#f3f0ff;--ac-tag-color:#6741d9;}
.ac6{--ac-color:#ff922b;--ac-color-light:#ffc078;--ac-bg:#fff4e6;--ac-tag-color:#d9480f;}
.ac7{--ac-color:#22b8cf;--ac-color-light:#66d9e8;--ac-bg:#e3fafc;--ac-tag-color:#0c8599;}
.ac8{--ac-color:#ff6b6b;--ac-color-light:#ffa8a8;--ac-bg:#fff5f5;--ac-tag-color:#c92a2a;}

footer { background:var(--green-dark); color:rgba(255,255,255,0.7); text-align:center; padding:32px 20px; font-size:0.85rem; font-weight:700; }
footer .footer-brand { font-family:'Fredoka One',cursive; font-size:1.6rem; color:#fff; display:block; margin-bottom:8px; }
footer a { color:rgba(255,255,255,0.5); text-decoration:none; margin:0 8px; }
footer a:hover { color:#fff; }
</style>
</head>
<body>

<?php include 'navbar.php'; ?>

<!-- PAGE HEADER -->
<section class="page-header">
  <div class="blob blob-1"></div>
  <div class="blob blob-2"></div>
  <span class="float-emoji fe1">🎯</span>
  <span class="float-emoji fe2">⚡</span>
  <span class="float-emoji fe3">📊</span>
  <span class="float-emoji fe4">🌟</span>
  <div class="page-badge">📈 Track Your Progress</div>
  <h1>My <span>Quizzes</span></h1>
  <p>See how far you've come in each lesson. Keep going — you're doing great!</p>
</section>

<!-- quizzes CARDS -->
<section class="quizzes-section">
  <div class="container">
    <div class="section-label">
      <div class="section-label-line"></div>
      <span class="section-label-text">✏️ LESSON PROGRESS</span>
      <div class="section-label-line right"></div>
    </div>

    <div class="row g-4">

    <?php foreach ($data as $lesson):
        $lname  = $lesson['lesson_name'];
        $cfg    = $lesson_config[$lname] ?? ['icon'=>'📖','label'=>'Lesson','color'=>'ac1','prefix'=>'quizzes','title'=>ucfirst($lname)];
        $final_unlocked = ($lesson['completed'] == $lesson['total']);
        $final_taken = false; // pwede mo pa to i-connect sa database later
        $final_score = $lesson['final_score'];
        $final_done  = $lesson['final_done'];
    ?>
      <div class="col-md-6 col-xl-4">
        <div class="quizzes-card <?php echo $cfg['color']; ?>">

          <div class="ac-head">
            <div class="ac-icon"><?php echo $cfg['icon']; ?></div>
            <div class="ac-meta">
              <div class="ac-title"><?php echo $cfg['title']; ?></div>
              <span class="ac-tag"><?php echo $cfg['label']; ?></span>
              <?php if($lesson['avg_score'] > 0): ?>
              <span class="ac-avg-score">⭐ Avg: <?php echo $lesson['avg_score']; ?>%</span>
              <?php endif; ?>
            </div>
            <div class="ac-percent">
              <?php echo $lesson['percent']; ?><small>% done</small>
            </div>
          </div>

          <div class="ac-progress-wrap">
            <div class="ac-progress-row">
              <span>Overall Progress</span>
              <span><?php echo $lesson['completed']; ?> / <?php echo $lesson['total']; ?> completed</span>
            </div>
            <div class="ac-bar-track">
             <div class="ac-bar-fill" 
                style="width:0%" 
                data-width="<?php echo $lesson['percent']; ?>%">
            </div>
            </div>
          </div>

          <div class="ac-quizzes">

            <?php foreach ($lesson['quizzes'] as $act):
                $aname   = $act['quizzes_name'];
                $disp    = $quizzes_display[$aname] ?? [ucfirst(str_replace('_',' ',$aname)), 'quizzes'];
                $label   = $disp[0];
                $status  = $act['status'];
                $score   = $act['score'];

                // Build URL
                if (!empty($cfg['combined'])) {
                    $tab = $combined_tab_map[$aname] ?? 1;
                    $url = $cfg['combined_file'] . '?lesson_id=' . $lesson['lesson_id'] . '&tab=' . $tab;
                } else {
                    $slug = $disp[1];
                    $url  = $cfg['prefix'] . '-' . $slug . '.php?quizzes_id=' . $act['quizzes_id'];
                }

                $dot_class = 'st-' . $status;
                $dot_icon  = match($status) {
                    'completed'   => '<i class="fas fa-check"></i>',
                    'in_progress' => '<i class="fas fa-play"></i>',
                    default       => '<i class="fas fa-lock"></i>',
                };
                $btn = match($status) {
                    'completed'   => "<a href=\"$url\" class=\"ac-btn btn-redo\">↩ Redo</a>",
                    'in_progress' => "<a href=\"$url\" class=\"ac-btn btn-continue\">▶ Continue</a>",
                    default       => "<a href=\"$url\" class=\"ac-btn btn-start\">▶ Start</a>",
                };
                $score_html = $score > 0
                    ? "<span class=\"ac-row-score\">{$score}%</span>"
                    : "<span class=\"ac-row-score no-score\">—</span>";
                $name_class = $status === 'completed' ? 'done-name' : '';
            ?>
            <div class="ac-row">
              <div class="ac-row-top">
                <div class="ac-status-dot <?php echo $dot_class; ?>"><?php echo $dot_icon; ?></div>
                <span class="ac-row-name <?php echo $name_class; ?>"><?php echo $label; ?></span>
                <?php echo $score_html; ?>
                <?php echo $btn; ?>
              </div>
              <?php if($score > 0): ?>
              <div class="ac-mini-bar-wrap">
                <div class="ac-mini-bar-track">
                  <div class="ac-mini-bar-fill" style="width:0%" data-width="<?php echo $score; ?>%"></div>
                </div>
              </div>
              <?php endif; ?>
            </div>
            <?php endforeach; ?>

<!-- FINAL EXAM -->
<div class="ac-row">
  <div class="ac-row-top">
    
    <?php 
    $final_unlocked = ($lesson['completed'] == $lesson['total']);
    $exam_file = '/ITPM_project/' . $lesson['lesson_name'] . '_exam.php';
    ?>

    <div class="ac-status-dot <?php echo $final_unlocked ? 'st-in_progress' : 'st-not_started'; ?>">
      <?php echo $final_unlocked ? '<i class="fas fa-star"></i>' : '<i class="fas fa-lock"></i>'; ?>
    </div>

    <span class="ac-row-name">Final Exam</span>

    <?php if($final_score > 0): ?>
      <span class="ac-row-score"> <?php echo round(($final_score / 40) * 100); ?>%</span>
    <?php else: ?>
      <span class="ac-row-score no-score">—</span>
    <?php endif; ?>

    <?php if($final_done): ?>
      <button class="ac-btn btn-start" disabled style="opacity:.6; cursor:not-allowed;">
        ✅ Taken Already
      </button>

    <?php elseif($final_unlocked): ?>
      <a href="<?php echo $exam_file; ?>" class="ac-btn btn-start">
        🎓 Take Final Exam
      </a>

    <?php else: ?>
      <button class="ac-btn btn-start" disabled style="opacity:.5; cursor:not-allowed;">
        🔒 Locked
      </button>
    <?php endif; ?>

  </div>

  <!-- FINAL EXAM PROGRESS BAR -->
  <div class="ac-mini-bar-wrap">
  <div class="ac-mini-bar-track">
    <div class="ac-mini-bar-fill" 
         style="width:0%" 
         data-width="<?php echo round(($final_score / 40) * 100); ?>%">
    </div>
  </div>
</div>

</div>
          </div>

        </div>
      </div>
    <?php endforeach; ?>

    </div>
  </div>
</section>

<!-- FOOTER -->
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
  <p style="margin-top:16px;font-size:0.75rem;opacity:0.5;">© 2025 E-KINDER. Made with ❤️ for young Filipino learners.</p>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    // Animate main lesson bars
    document.querySelectorAll('.ac-bar-fill').forEach((bar, i) => {
        const w = bar.dataset.width; // 🔥 eto dapat!
        bar.style.width = '0%';
        setTimeout(() => { bar.style.width = w; }, 300 + i * 80);
    });
    // Animate per-quizzes mini bars
    document.querySelectorAll('.ac-mini-bar-fill').forEach((bar, i) => {
        const w = bar.dataset.width;
        bar.style.width = '0%';
        setTimeout(() => { bar.style.width = w; }, 400 + i * 60);
    });
});
</script>
</body>
</html>