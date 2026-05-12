<?php
session_start();
require_once __DIR__ . '/database.php';

// Guard
if (!isset($_SESSION['student_id']) || $_SESSION['role'] !== 'student') {
    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>E-KINDER — Lessons</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Fredoka+One&family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">

<?php include 'navbar-styles.php'; ?>

<style>
:root {
    --green-dark: #0d5407;
    --green-mid:  #1a7a10;
    --cream:      #f7f9f4;
}
* { box-sizing: border-box; margin: 0; padding: 0; }
body { font-family: 'Nunito', sans-serif; background: var(--cream); overflow-x: hidden; }

/* ── HERO ── */
.hero {
    background: linear-gradient(145deg, #0d5407 0%, #1e8a12 50%, #2da617 100%);
    padding: 72px 20px 90px;
    text-align: center;
    position: relative;
    overflow: hidden;
}
.hero::before {
    content: '';
    position: absolute; inset: 0;
    background:
        radial-gradient(circle at 15% 50%, rgba(255,255,255,0.07) 0%, transparent 50%),
        radial-gradient(circle at 85% 20%, rgba(255,255,255,0.05) 0%, transparent 40%);
}
.hero-dots {
    position: absolute; inset: 0; pointer-events: none;
    background-image: radial-gradient(circle, rgba(255,255,255,0.12) 1px, transparent 1px);
    background-size: 32px 32px;
    animation: dotsDrift 20s linear infinite;
}
@keyframes dotsDrift { from{background-position:0 0} to{background-position:32px 32px} }

.hero-badge {
    display: inline-flex; align-items: center; gap: 8px;
    background: rgba(255,255,255,0.15);
    border: 1.5px solid rgba(255,255,255,0.25);
    border-radius: 50px; padding: 7px 20px;
    font-size: 0.78rem; font-weight: 800; color: rgba(255,255,255,0.9);
    letter-spacing: 2px; text-transform: uppercase;
    margin-bottom: 22px; position: relative; z-index: 1;
    backdrop-filter: blur(4px);
}
.hero h1 {
    font-family: 'Fredoka One', cursive;
    font-size: clamp(2.2rem, 5vw, 3.6rem);
    color: #fff; margin-bottom: 14px;
    position: relative; z-index: 1;
    text-shadow: 0 4px 24px rgba(0,0,0,0.2);
}
.hero h1 em { color: #ffd43b; font-style: normal; }
.hero p {
    font-size: 1rem; color: rgba(255,255,255,0.75); font-weight: 600;
    max-width: 420px; margin: 0 auto;
    position: relative; z-index: 1;
}
.hero-stats {
    display: flex; justify-content: center; gap: 32px;
    margin-top: 36px; position: relative; z-index: 1; flex-wrap: wrap;
}
.hero-stat {
    background: rgba(255,255,255,0.12);
    border: 1px solid rgba(255,255,255,0.2);
    border-radius: 16px; padding: 14px 24px;
    backdrop-filter: blur(8px); text-align: center;
}
.hero-stat-num { font-family: 'Fredoka One', cursive; font-size: 1.8rem; color: #ffd43b; line-height: 1; }
.hero-stat-label { font-size: 0.72rem; color: rgba(255,255,255,0.7); font-weight: 700; text-transform: uppercase; letter-spacing: 1px; margin-top: 3px; }

.hero-wave { position: absolute; bottom: -1px; left: 0; right: 0; line-height: 0; }
.hero-wave svg { display: block; width: 100%; }

/* ── SECTION ── */
.lessons-section { padding: 64px 0 90px; }
.section-head { display: flex; align-items: center; gap: 14px; margin-bottom: 40px; }
.section-head-line { flex: 1; height: 1.5px; background: #e8e8e8; }
.section-head-text {
    font-family: 'Fredoka One', cursive;
    font-size: 0.85rem; color: #ccc; letter-spacing: 2.5px;
    white-space: nowrap; text-transform: uppercase;
}

/* ── CARDS GRID ── */
.cards-grid {
    display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px;
}
@media(max-width:1100px){ .cards-grid { grid-template-columns: repeat(3,1fr); } }
@media(max-width:768px) { .cards-grid { grid-template-columns: repeat(2,1fr); } }
@media(max-width:480px) { .cards-grid { grid-template-columns: 1fr; } }

/* ── LESSON CARD ── */
.lesson-card {
    background: #fff; border-radius: 22px; padding: 0;
    cursor: pointer; text-decoration: none;
    display: flex; flex-direction: column;
    overflow: hidden; border: 2px solid #f0f0f0;
    box-shadow: 0 4px 18px rgba(0,0,0,0.06);
    transition: transform 0.25s cubic-bezier(.34,1.56,.64,1), box-shadow 0.25s, border-color 0.2s;
    animation: cardIn 0.5s ease both;
}
.lesson-card:hover {
    transform: translateY(-8px) scale(1.02);
    box-shadow: 0 20px 48px rgba(0,0,0,0.12);
    border-color: var(--accent); text-decoration: none;
}
.lesson-card:active { transform: scale(0.97); }
.card-strip { height: 6px; background: var(--accent); flex-shrink: 0; }
.card-icon-area { padding: 28px 20px 20px; display: flex; align-items: flex-start; gap: 16px; }
.card-icon-box {
    width: 56px; height: 56px; border-radius: 16px; flex-shrink: 0;
    background: var(--icon-bg);
    display: flex; align-items: center; justify-content: center;
    font-size: 1.4rem; color: var(--accent);
    box-shadow: 0 4px 14px var(--icon-shadow);
    transition: transform 0.3s cubic-bezier(.34,1.56,.64,1);
}
.lesson-card:hover .card-icon-box { transform: scale(1.12) rotate(-6deg); }
.card-text { flex: 1; }
.card-title { font-family: 'Fredoka One', cursive; font-size: 1.15rem; color: #1a1a1a; margin-bottom: 4px; }
.card-desc  { font-size: 0.78rem; color: #999; font-weight: 600; line-height: 1.5; }
.card-footer-strip {
    margin-top: auto; padding: 12px 20px; border-top: 1.5px solid #f5f5f5;
    display: flex; align-items: center; justify-content: space-between;
}
.card-tag {
    font-size: 0.68rem; font-weight: 800; color: var(--accent);
    text-transform: uppercase; letter-spacing: 1px;
    background: var(--icon-bg); padding: 3px 10px; border-radius: 20px;
}
.card-arrow {
    width: 28px; height: 28px; border-radius: 50%;
    background: var(--icon-bg); border: none; cursor: pointer;
    display: flex; align-items: center; justify-content: center;
    color: var(--accent); font-size: 0.7rem;
    transition: background 0.2s, transform 0.2s;
}
.lesson-card:hover .card-arrow { background: var(--accent); color: #fff; transform: translateX(2px); }

@keyframes cardIn {
    from { opacity: 0; transform: translateY(30px) scale(0.92); }
    to   { opacity: 1; transform: translateY(0) scale(1); }
}
.lesson-card:nth-child(1){ animation-delay:.04s }
.lesson-card:nth-child(2){ animation-delay:.08s }
.lesson-card:nth-child(3){ animation-delay:.12s }
.lesson-card:nth-child(4){ animation-delay:.16s }
.lesson-card:nth-child(5){ animation-delay:.20s }
.lesson-card:nth-child(6){ animation-delay:.24s }
.lesson-card:nth-child(7){ animation-delay:.28s }
.lesson-card:nth-child(8){ animation-delay:.32s }

/* ── COLOR THEMES ── */
.c1{ --accent:#f06595; --icon-bg:#fff0f6; --icon-shadow:rgba(240,101,149,0.2); }
.c2{ --accent:#339af0; --icon-bg:#e7f5ff; --icon-shadow:rgba(51,154,240,0.2); }
.c3{ --accent:#e8a000; --icon-bg:#fff9db; --icon-shadow:rgba(232,160,0,0.2); }
.c4{ --accent:#40c057; --icon-bg:#ebfbee; --icon-shadow:rgba(64,192,87,0.2); }
.c5{ --accent:#7950f2; --icon-bg:#f3f0ff; --icon-shadow:rgba(121,80,242,0.2); }
.c6{ --accent:#ff6b2b; --icon-bg:#fff4e6; --icon-shadow:rgba(255,107,43,0.2); }
.c7{ --accent:#0db9c4; --icon-bg:#e3fafc; --icon-shadow:rgba(13,185,196,0.2); }
.c8{ --accent:#e03131; --icon-bg:#fff5f5; --icon-shadow:rgba(224,49,49,0.2); }

/* ── FOOTER ── */
footer { background: var(--green-dark); color: rgba(255,255,255,0.65); padding: 44px 20px 32px; }
.footer-inner { max-width: 900px; margin: 0 auto; text-align: center; }
.footer-brand { font-family: 'Fredoka One', cursive; font-size: 2rem; color: #fff; display: block; margin-bottom: 6px; }
.footer-tagline { font-size: 0.85rem; font-weight: 600; margin-bottom: 24px; }
.footer-links { display: flex; justify-content: center; flex-wrap: wrap; gap: 4px; margin-bottom: 24px; }
.footer-links a { color: rgba(255,255,255,0.5); text-decoration: none; font-size: 0.82rem; font-weight: 700; padding: 5px 14px; border-radius: 20px; transition: all 0.2s; }
.footer-links a:hover { color: #fff; background: rgba(255,255,255,0.1); }
.footer-copy { font-size: 0.72rem; opacity: 0.4; font-weight: 700; }
</style>
</head>
<body>

<?php include 'navbar.php'; ?>

<!-- HERO -->
<section class="hero">
    <div class="hero-dots"></div>
    <div style="position:relative;z-index:1">
        <div class="hero-badge"><i class="fas fa-graduation-cap"></i> Choose Your Lesson</div>
        <h1>Start <em>Learning</em> Today!</h1>
        <p>Pick any lesson below to begin your adventure. Each one is packed with fun!</p>
        <div class="hero-stats">
            <div class="hero-stat">
                <div class="hero-stat-num">8</div>
                <div class="hero-stat-label">Lessons</div>
            </div>
            <div class="hero-stat">
                <div class="hero-stat-num">EN</div>
                <div class="hero-stat-label">+ Filipino</div>
            </div>
            <div class="hero-stat">
                <div class="hero-stat-num"><i class="fas fa-star" style="font-size:1.2rem"></i></div>
                <div class="hero-stat-label">Interactive</div>
            </div>
        </div>
    </div>
    <div class="hero-wave">
        <svg viewBox="0 0 1440 60" fill="none" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none">
            <path d="M0,30 C240,60 480,0 720,30 C960,60 1200,0 1440,30 L1440,60 L0,60 Z" fill="#f7f9f4"/>
        </svg>
    </div>
</section>

<!-- LESSONS -->
<section class="lessons-section">
    <div class="container">
        <div class="section-head">
            <div class="section-head-line"></div>
            <span class="section-head-text"><i class="fas fa-book-open me-2"></i>All Lessons</span>
            <div class="section-head-line"></div>
        </div>

        <div class="cards-grid">
            <a href="letters.php" class="lesson-card c1">
                <div class="card-strip"></div>
                <div class="card-icon-area">
                    <div class="card-icon-box"><i class="fas fa-font"></i></div>
                    <div class="card-text">
                        <div class="card-title">Letters</div>
                        <div class="card-desc">Learn the alphabet from A to Z with fun lessons.</div>
                    </div>
                </div>
                <div class="card-footer-strip">
                    <span class="card-tag">Alphabet</span>
                    <div class="card-arrow"><i class="fas fa-chevron-right"></i></div>
                </div>
            </a>

            <a href="shapes.php" class="lesson-card c2">
                <div class="card-strip"></div>
                <div class="card-icon-area">
                    <div class="card-icon-box"><i class="fas fa-shapes"></i></div>
                    <div class="card-text">
                        <div class="card-title">Shapes</div>
                        <div class="card-desc">Identify and learn different geometric shapes.</div>
                    </div>
                </div>
                <div class="card-footer-strip">
                    <span class="card-tag">Geometry</span>
                    <div class="card-arrow"><i class="fas fa-chevron-right"></i></div>
                </div>
            </a>

            <a href="animals.php" class="lesson-card c3">
                <div class="card-strip"></div>
                <div class="card-icon-area">
                    <div class="card-icon-box"><i class="fas fa-paw"></i></div>
                    <div class="card-text">
                        <div class="card-title">Animals</div>
                        <div class="card-desc">Discover animals and the sounds they make.</div>
                    </div>
                </div>
                <div class="card-footer-strip">
                    <span class="card-tag">Science</span>
                    <div class="card-arrow"><i class="fas fa-chevron-right"></i></div>
                </div>
            </a>

            <a href="colors.php" class="lesson-card c4">
                <div class="card-strip"></div>
                <div class="card-icon-area">
                    <div class="card-icon-box"><i class="fas fa-palette"></i></div>
                    <div class="card-text">
                        <div class="card-title">Colors</div>
                        <div class="card-desc">Learn to recognize and name different colors.</div>
                    </div>
                </div>
                <div class="card-footer-strip">
                    <span class="card-tag">Art</span>
                    <div class="card-arrow"><i class="fas fa-chevron-right"></i></div>
                </div>
            </a>

            <a href="numbers.php" class="lesson-card c5">
                <div class="card-strip"></div>
                <div class="card-icon-area">
                    <div class="card-icon-box"><i class="fas fa-calculator"></i></div>
                    <div class="card-text">
                        <div class="card-title">Numbers</div>
                        <div class="card-desc">Practice counting and number recognition.</div>
                    </div>
                </div>
                <div class="card-footer-strip">
                    <span class="card-tag">Math</span>
                    <div class="card-arrow"><i class="fas fa-chevron-right"></i></div>
                </div>
            </a>

            <a href="heroes.php" class="lesson-card c6">
                <div class="card-strip"></div>
                <div class="card-icon-area">
                    <div class="card-icon-box"><i class="fas fa-medal"></i></div>
                    <div class="card-text">
                        <div class="card-title">National Heroes</div>
                        <div class="card-desc">Meet brave Filipino heroes who shaped our country!</div>
                    </div>
                </div>
                <div class="card-footer-strip">
                    <span class="card-tag">Filipino</span>
                    <div class="card-arrow"><i class="fas fa-chevron-right"></i></div>
                </div>
            </a>

            <a href="humanbody.php" class="lesson-card c7">
                <div class="card-strip"></div>
                <div class="card-icon-area">
                    <div class="card-icon-box"><i class="fas fa-person"></i></div>
                    <div class="card-text">
                        <div class="card-title">Body Parts</div>
                        <div class="card-desc">Identify and learn the basic parts of the body.</div>
                    </div>
                </div>
                <div class="card-footer-strip">
                    <span class="card-tag">Health</span>
                    <div class="card-arrow"><i class="fas fa-chevron-right"></i></div>
                </div>
            </a>

            <a href="motorskills.php" class="lesson-card c8">
                <div class="card-strip"></div>
                <div class="card-icon-area">
                    <div class="card-icon-box"><i class="fas fa-hands"></i></div>
                    <div class="card-text">
                        <div class="card-title">Motor Skills</div>
                        <div class="card-desc">Learn to wash hands, brush teeth, and care for your body!</div>
                    </div>
                </div>
                <div class="card-footer-strip">
                    <span class="card-tag">Hygiene</span>
                    <div class="card-arrow"><i class="fas fa-chevron-right"></i></div>
                </div>
            </a>
        </div>
    </div>
</section>

<!-- FOOTER -->
<footer>
    <div class="footer-inner">
        <span class="footer-brand">E-KINDER</span>
        <p class="footer-tagline">An interactive learning platform for Filipino kindergarteners 🇵🇭</p>
        <div class="footer-links">
            <a href="student-dashboard.php">Home</a>
            <a href="lessons.php">Lessons</a>
            <a href="activities.php">Quizz</a>
            <a href="badges.php">Badges</a>
            <a href="about.php">About</a>
        </div>
        <p class="footer-copy">© 2025 E-KINDER. Made with ❤️ for young Filipino learners.</p>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>