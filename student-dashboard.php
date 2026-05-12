<?php
session_start();
require_once 'database.php';

// Guard
if (!isset($_SESSION['student_id']) || $_SESSION['role'] !== 'student') {
    header("Location: index.php");
    exit;
}

// ── AUDIT LOG: insert login_time if not yet logged for this session ──
if (isset($_SESSION['student_id']) && !isset($_SESSION['audit_logged'])) {
    $student_id = $_SESSION['student_id'];
    $login_time = date('Y-m-d H:i:s');

    $stmt = $conn->prepare("INSERT INTO audit_logs (student_id, login_time) VALUES (?, ?)");
    $stmt->bind_param("is", $student_id, $login_time);
    $stmt->execute();

    $_SESSION['audit_log_id'] = $stmt->insert_id;
    $_SESSION['audit_logged'] = true;
    $stmt->close();
}

$first_name = $_SESSION['student_fname'] ?? 'Student';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>E-KINDER — Home</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Fredoka+One&family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">

<?php include 'navbar-styles.php'; ?>

<style>
:root {
    --green-dark: #0d5407;
    --green-mid:  #1a7a10;
    --cream:      #fffbf4;
}
* { box-sizing: border-box; margin: 0; padding: 0; }
body { font-family: 'Nunito', sans-serif; background: var(--cream); overflow-x: hidden; }

/* ── HERO ── */
.hero {
    position: relative; text-align: center;
    padding: 80px 20px 90px; overflow: hidden;
}
.hero-bg {
    position: absolute; inset: 0;
    background: linear-gradient(160deg, #e8f5e9 0%, #fffbf4 50%, #e3f2fd 100%);
    z-index: 0;
}
.blob { position: absolute; border-radius: 50%; filter: blur(60px); opacity: 0.35; pointer-events: none; }
.blob-1 { width:340px; height:340px; background:#a8edba; top:-80px; left:-100px; }
.blob-2 { width:280px; height:280px; background:#ffd6e7; top:-40px; right:-60px; }
.blob-3 { width:200px; height:200px; background:#fff0a0; bottom:-60px; left:30%; }
.hero-content { position: relative; z-index: 1; }
.hero-badge {
    display: inline-block; background: #fff; border: 2.5px solid #c8e6c9;
    border-radius: 50px; padding: 6px 18px; font-size: 0.82rem;
    font-weight: 800; color: var(--green-dark); letter-spacing: 1.5px;
    text-transform: uppercase; margin-bottom: 20px;
    box-shadow: 0 4px 14px rgba(13,84,7,0.1);
}
.hero h1 { font-family: 'Fredoka One', cursive; font-size: clamp(2.6rem, 6vw, 4.2rem); color: var(--green-dark); line-height: 1.1; margin-bottom: 16px; }
.hero h1 span { color: #e53935; }
.hero p { font-size: 1.1rem; color: #666; font-weight: 600; max-width: 520px; margin: 0 auto 32px; }
.hero-stats { display: flex; justify-content: center; gap: 40px; flex-wrap: wrap; margin-bottom: 36px; }
.hero-stat-num { font-family: 'Fredoka One', cursive; font-size: 2rem; color: var(--green-dark); line-height: 1; }
.hero-stat-label { font-size: 0.8rem; font-weight: 700; color: #aaa; text-transform: uppercase; letter-spacing: 1px; }
.float-emoji { position: absolute; animation: floatBounce 3s ease-in-out infinite; pointer-events: none; z-index: 1; user-select: none; }
.fe1 { top:12%; left:5%;    font-size:2.6rem; animation-delay:0s; }
.fe2 { top:20%; right:6%;   font-size:2rem;   animation-delay:.6s; }
.fe3 { bottom:18%; left:8%; font-size:1.8rem; animation-delay:1.2s; }
.fe4 { bottom:15%; right:5%;font-size:2.4rem; animation-delay:.9s; }
.fe5 { top:55%; left:2%;    font-size:1.6rem; animation-delay:1.8s; }
@keyframes floatBounce {
    0%,100% { transform: translateY(0) rotate(-5deg); }
    50%      { transform: translateY(-16px) rotate(5deg); }
}

/* ── INFO SECTION ── */
.info-section {
    background: linear-gradient(180deg, #fff 0%, #f0fdf4 100%);
    padding: 80px 0 90px; position: relative; overflow: hidden;
}
.info-section::before {
    content: ''; position: absolute; top: 0; left: 0; right: 0; height: 4px;
    background: linear-gradient(90deg, #51cf66, #339af0, #f06595, #fcc419, #51cf66);
    background-size: 300% 100%; animation: rainbowSlide 4s linear infinite;
}
@keyframes rainbowSlide { 0%{ background-position: 0% 0%; } 100%{ background-position: 300% 0%; } }
.info-heading { font-family: 'Fredoka One', cursive; font-size: clamp(1.8rem, 4vw, 2.8rem); color: var(--green-dark); margin-bottom: 12px; }
.info-subheading { font-size: 1rem; color: #888; font-weight: 700; margin-bottom: 48px; }

.pillars { display: grid; grid-template-columns: repeat(3,1fr); gap: 28px; margin-bottom: 72px; }
@media(max-width:768px){ .pillars { grid-template-columns: 1fr; } }
.pillar {
    background: #fff; border-radius: 24px; padding: 32px 24px; text-align: center;
    border: 2.5px solid #e9ecef; box-shadow: 0 6px 24px rgba(0,0,0,0.05);
    transition: transform 0.25s ease, box-shadow 0.25s ease;
}
.pillar:hover { transform: translateY(-6px); box-shadow: 0 16px 36px rgba(0,0,0,0.1); }
.pillar-emoji { font-size: 3rem; display: block; margin-bottom: 16px; animation: floatBounce 3.5s ease-in-out infinite; }
.pillar:nth-child(2) .pillar-emoji { animation-delay: 0.6s; }
.pillar:nth-child(3) .pillar-emoji { animation-delay: 1.2s; }
.pillar-title { font-family: 'Fredoka One', cursive; font-size: 1.25rem; color: #222; margin-bottom: 8px; }
.pillar-text  { font-size: 0.88rem; color: #777; font-weight: 600; line-height: 1.6; }

.how-section {
    background: var(--green-dark); border-radius: 32px; padding: 48px 40px;
    color: #fff; margin-bottom: 72px; position: relative; overflow: hidden;
}
.how-section::before { content: '🌟'; position: absolute; font-size: 160px; opacity: 0.04; top: -20px; right: -20px; pointer-events: none; }
.how-title { font-family: 'Fredoka One', cursive; font-size: 1.8rem; margin-bottom: 32px; color: #fff; }
.steps { display: grid; grid-template-columns: repeat(3,1fr); gap: 24px; }
@media(max-width:768px){ .steps { grid-template-columns: 1fr; } }
.step { display: flex; gap: 16px; align-items: flex-start; }
.step-num { width: 42px; height: 42px; border-radius: 50%; background: rgba(255,255,255,0.15); border: 2px solid rgba(255,255,255,0.3); display: flex; align-items: center; justify-content: center; font-family: 'Fredoka One', cursive; font-size: 1.2rem; flex-shrink: 0; color: #fff; }
.step-title { font-family: 'Fredoka One', cursive; font-size: 1.05rem; margin-bottom: 4px; color: #fff; }
.step-text  { font-size: 0.82rem; color: rgba(255,255,255,0.7); font-weight: 600; line-height: 1.5; }

.quote-block {
    background: linear-gradient(135deg, #fff9db, #fff0f6);
    border-radius: 28px; padding: 48px 40px; text-align: center;
    border: 2.5px solid #ffe066; margin-bottom: 72px;
}
.quote-mark { font-family: 'Fredoka One', cursive; font-size: 6rem; line-height: 0.5; color: #ffe066; display: block; margin-bottom: 16px; }
.quote-text { font-family: 'Fredoka One', cursive; font-size: clamp(1.1rem, 3vw, 1.5rem); color: var(--green-dark); max-width: 600px; margin: 0 auto 20px; line-height: 1.4; }
.quote-author { font-size: 0.85rem; font-weight: 700; color: #aaa; letter-spacing: 1px; text-transform: uppercase; }

.cta-banner {
    background: linear-gradient(135deg, #51cf66, #339af0);
    border-radius: 28px; padding: 52px 40px; text-align: center;
    color: #fff; position: relative; overflow: hidden;
}
.cta-banner::before { content: '🚀'; position: absolute; font-size: 180px; opacity: 0.06; bottom: -30px; right: -20px; pointer-events: none; }
.cta-title { font-family: 'Fredoka One', cursive; font-size: clamp(1.6rem, 4vw, 2.4rem); margin-bottom: 12px; color: #fff; }
.cta-sub   { font-size: 1rem; font-weight: 700; color: rgba(255,255,255,0.85); margin-bottom: 28px; }
.cta-btn {
    display: inline-block; background: #fff; color: var(--green-dark);
    font-family: 'Fredoka One', cursive; font-size: 1.05rem;
    padding: 14px 36px; border-radius: 50px;
    box-shadow: 0 8px 24px rgba(0,0,0,0.15); text-decoration: none;
    transition: transform 0.2s cubic-bezier(.34,1.56,.64,1), box-shadow 0.2s;
}
.cta-btn:hover { transform: scale(1.07); box-shadow: 0 14px 32px rgba(0,0,0,0.18); color: var(--green-dark); }

/* ── FOOTER ── */
footer { background: var(--green-dark); color: rgba(255,255,255,0.7); text-align: center; padding: 32px 20px; font-size: 0.85rem; font-weight: 700; }
footer .footer-brand { font-family: 'Fredoka One', cursive; font-size: 1.6rem; color: #fff; display: block; margin-bottom: 8px; }
footer a { color: rgba(255,255,255,0.5); text-decoration: none; margin: 0 8px; }
footer a:hover { color: #fff; }
</style>
</head>
<body>

<?php include 'navbar.php'; ?>

<!-- ── HERO ── -->
<section class="hero">
    <div class="hero-bg"></div>
    <div class="blob blob-1"></div>
    <div class="blob blob-2"></div>
    <div class="blob blob-3"></div>
    <span class="float-emoji fe1">🌟</span>
    <span class="float-emoji fe2">🎨</span>
    <span class="float-emoji fe3">📚</span>
    <span class="float-emoji fe4">🌈</span>
    <span class="float-emoji fe5">✏️</span>
    <div class="hero-content">
        <div class="hero-badge">🇵🇭 Filipino Kindergarten Platform</div>
        <h1>Learn, Play &amp;<br><span>Grow</span> Together!</h1>
        <p>A fun and interactive learning experience designed specially for Filipino kindergarteners.</p>
        <div class="hero-stats">
            <div class="hero-stat">
                <div class="hero-stat-num">8</div>
                <div class="hero-stat-label">Lessons</div>
            </div>
            <div class="hero-stat">
                <div class="hero-stat-num">100%</div>
                <div class="hero-stat-label">Free</div>
            </div>
            <div class="hero-stat">
                <div class="hero-stat-num">2</div>
                <div class="hero-stat-label">Languages</div>
            </div>
            <div class="hero-stat">
                <div class="hero-stat-num">∞</div>
                <div class="hero-stat-label">Fun</div>
            </div>
        </div>
    </div>
</section>

<!-- ── INFO SECTION ── -->
<section class="info-section">
    <div class="container">

        <div class="text-center mb-2">
            <div class="hero-badge" style="margin-bottom:16px;">ℹ️ About E-KINDER</div>
            <h2 class="info-heading">Why Kids Love E-KINDER</h2>
            <p class="info-subheading">Built with love for Filipino learners aged 5–7 years old</p>
        </div>

        <div class="pillars">
            <div class="pillar">
                <span class="pillar-emoji">🎮</span>
                <div class="pillar-title">Play-Based Learning</div>
                <p class="pillar-text">Every lesson is designed as a game — kids learn best when they're having fun. Interactive lessons keep them engaged from start to finish.</p>
            </div>
            <div class="pillar">
                <span class="pillar-emoji">🇵🇭</span>
                <div class="pillar-title">Bilingual Content</div>
                <p class="pillar-text">Supports both English and Filipino (Tagalog) so students can learn in the language they're most comfortable with — or practice both!</p>
            </div>
            <div class="pillar">
                <span class="pillar-emoji">📱</span>
                <div class="pillar-title">Works Everywhere</div>
                <p class="pillar-text">Fully responsive on tablets, phones, and computers. Learn at home, in school, or on the go — E-KINDER is always ready.</p>
            </div>
        </div>

        <div class="how-section">
            <h3 class="how-title">🚀 How It Works</h3>
            <div class="steps">
                <div class="step">
                    <div class="step-num">1</div>
                    <div class="step-body">
                        <div class="step-title">Pick a Lesson</div>
                        <p class="step-text">Choose from 8 fun lessons — Letters, Numbers, Animals, Shapes, and more. Each one is colorful and easy to navigate.</p>
                    </div>
                </div>
                <div class="step">
                    <div class="step-num">2</div>
                    <div class="step-body">
                        <div class="step-title">Watch &amp; Listen</div>
                        <p class="step-text">Animated lessons show kids how words are spelled or how concepts work — with clear audio pronunciation in English or Filipino.</p>
                    </div>
                </div>
                <div class="step">
                    <div class="step-num">3</div>
                    <div class="step-body">
                        <div class="step-title">Try It Yourself!</div>
                        <p class="step-text">Hands-on lessons let kids practice what they just learned. Instant feedback with cheerful sounds and animations celebrates every win.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="quote-block">
            <span class="quote-mark">"</span>
            <p class="quote-text">Every child is a natural learner — E-KINDER just gives them the right tools to shine! 🌟</p>
            <span class="quote-author">— The E-KINDER Team</span>
        </div>

        <div class="cta-banner">
            <h3 class="cta-title">Ready to Start Learning? 🎉</h3>
            <p class="cta-sub">Choose your first lesson and begin the adventure!</p>
            <a href="lessons.php" class="cta-btn">🚀 Go to Lessons</a>
        </div>

    </div>
</section>

<!-- ── FOOTER ── -->
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