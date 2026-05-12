<?php
$bodyParts = [
    "Head"      => ["#FF6B6B", "Ulo",      "pictures/humanbody/head.jpg",      "pictures/humanbody/head.jpg"],
    "Eyebrows"  => ["#FF8E53", "Kilay",    "pictures/humanbody/eyebrows.jpg",  "pictures/humanbody/eyebrows.jpg"],
    "Eyes"      => ["#FFA500", "Mata",     "pictures/humanbody/eyes.jpg",      "pictures/humanbody/eyes.jpg"],
    "Ears"      => ["#FFD93D", "Tainga",   "pictures/humanbody/ears.jpg",      "pictures/humanbody/ears.jpg"],
    "Nose"      => ["#6BCB77", "Ilong",    "pictures/humanbody/nose.jpg",      "pictures/humanbody/nose.jpg"],
    "Mouth"     => ["#4D96FF", "Bibig",    "pictures/humanbody/mouth.jpg",     "pictures/humanbody/mouth.jpg"],
    "Tongue"    => ["#845EC2", "Dila",     "pictures/humanbody/tongue.jpg",    "pictures/humanbody/tongue.jpg"],
    "Neck"      => ["#FF6F91", "Leeg",     "pictures/humanbody/neck.jpg",      "pictures/humanbody/neck.jpg"],
    "Shoulders" => ["#F9A825", "Balikat",  "pictures/humanbody/shoulders.jpg", "pictures/humanbody/shoulders.jpg"],
    "Arms"      => ["#00C9A7", "Braso",    "pictures/humanbody/arms.jpg",      "pictures/humanbody/arms.jpg"],
    "Elbows"    => ["#C34A36", "Siko",     "pictures/humanbody/elbows.png",    "pictures/humanbody/elbows.png"],
    "Hands"     => ["#FF6B6B", "Kamay",    "pictures/humanbody/hands.jpg",     "pictures/humanbody/hands.jpg"],
    "Fingers"   => ["#845EC2", "Daliri",   "pictures/humanbody/fingers.jpg",   "pictures/humanbody/fingers.jpg"],
    "Stomach"   => ["#4D96FF", "Tiyan",    "pictures/humanbody/stomachs.png",   "pictures/humanbody/stomachs.png"],
    "Legs"      => ["#6BCB77", "Binti",    "pictures/humanbody/legs.png",      "pictures/humanbody/legs.png"],
    "Knees"     => ["#FF8E53", "Tuhod",    "pictures/humanbody/knees.jpg",     "pictures/humanbody/knees.jpg"],
    "Feet"      => ["#FFD93D", "Paa",      "pictures/humanbody/feet.jpg",      "pictures/humanbody/feet.jpg"],
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Human Body - E-KINDER</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Fredoka+One&family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --green-dark: #0d5407;
            --song: #7c3aed;
            --song2: #a855f7;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Nunito', sans-serif;
            background-color: #fff9f0;
            background-image:
                radial-gradient(circle at 10% 15%, rgba(255,107,107,0.10) 0%, transparent 40%),
                radial-gradient(circle at 88% 70%, rgba(77,150,255,0.10) 0%, transparent 40%),
                radial-gradient(circle at 50% 95%, rgba(107,203,119,0.08) 0%, transparent 40%);
            min-height: 100vh; overflow-x: hidden;
        }

        /* ── NAVBAR ── */
        .lesson-nav {
            padding: 0 28px; height: 64px; background: #fff;
            box-shadow: 0 4px 20px rgba(0,0,0,0.07);
            position: sticky; top: 0; z-index: 100;
            display: flex; align-items: center; justify-content: space-between; gap: 16px;
        }
        .lnav-back {
            display: inline-flex; align-items: center; gap: 7px;
            background: var(--green-dark); color: #fff;
            font-family: 'Fredoka One', cursive; font-size: 0.9rem;
            padding: 8px 18px; border-radius: 50px; text-decoration: none;
            box-shadow: 0 4px 12px rgba(13,84,7,0.25);
            transition: transform 0.2s cubic-bezier(.34,1.56,.64,1);
        }
        .lnav-back:hover { transform: scale(1.06); color: #fff; }
        .lang-toggle {
            display: inline-flex; align-items: center;
            background: #f0f0f0; border-radius: 50px; padding: 4px;
            border: 2px solid #e0e0e0;
        }
        .lang-btn {
            font-family: 'Fredoka One', cursive; font-size: 0.82rem;
            padding: 6px 16px; border-radius: 50px; border: none; cursor: pointer;
            background: transparent; color: #aaa;
            transition: background 0.2s, color 0.2s, transform 0.15s;
            display: flex; align-items: center; gap: 5px;
        }
        .lang-btn.active { background: var(--green-dark); color: #fff; box-shadow: 0 3px 10px rgba(13,84,7,0.25); transform: scale(1.04); }

        /* ── PAGE HEADER ── */
        .page-header { text-align: center; padding: 36px 0 24px; }
        .page-header h1 { font-family: 'Fredoka One', cursive; font-size: clamp(2rem,5vw,3rem); color: var(--green-dark); }
        .lang-badge {
            display: inline-flex; align-items: center; gap: 6px;
            margin-top: 10px; padding: 5px 16px; border-radius: 50px;
            font-family: 'Fredoka One', cursive; font-size: 0.82rem;
            background: #fff; border: 2px solid #c8e6c9; color: var(--green-dark);
            box-shadow: 0 2px 8px rgba(13,84,7,0.08); transition: all 0.3s;
        }
        .lang-badge.tl-mode { background: #fff3e0; border-color: #ffcc80; color: #e65100; }

        /* ── SECTION HEADER CARD ── */
        .section-header-card {
            display: flex; align-items: center; gap: 16px;
            background: #fff; border-radius: 20px; padding: 18px 24px;
            box-shadow: 0 4px 18px rgba(0,0,0,.06); margin-bottom: 28px;
        }
        .sec-icon-box {
            width: 56px; height: 56px; border-radius: 16px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.4rem; flex-shrink: 0; color: #fff;
            box-shadow: 0 6px 16px rgba(0,0,0,.12);
        }
        .sec-title-group { flex: 1; min-width: 0; }
        .sec-title  { font-family: 'Fredoka One', cursive; font-size: 1.5rem; color: #1a1a2e; line-height: 1.15; margin: 0; }
        .sec-subtitle { font-size: .875rem; color: #aaa; font-weight: 700; margin: 3px 0 0; }
        .sec-line { flex: 1; height: 2px; background: linear-gradient(90deg, #e0e0e0 0%, transparent 100%); border-radius: 99px; margin-left: 8px; min-width: 40px; }

        /* ── BODY GRID ── */
        .body-grid {
            display: grid; grid-template-columns: repeat(5,1fr);
            gap: 24px; padding: 0 0 16px;
            transition: opacity 0.2s, transform 0.2s;
        }
        @media(max-width:992px){ .body-grid { grid-template-columns: repeat(3,1fr); } }
        @media(max-width:600px){ .body-grid { grid-template-columns: repeat(2,1fr); } }

        /* ── BODY CARD ── */
        .body-card {
            background: #fff; border-radius: 20px; padding: 20px 16px 16px;
            text-align: center; cursor: pointer; position: relative; overflow: hidden;
            box-shadow: 0 4px 16px rgba(0,0,0,0.08);
            border: 3px solid var(--card-color);
            transition: transform 0.25s cubic-bezier(.34,1.56,.64,1), box-shadow 0.25s;
            animation: cardEntrance 0.5s ease both;
            display: flex; flex-direction: column; align-items: center;
        }
        .body-card:hover { transform: translateY(-8px) scale(1.03); box-shadow: 0 18px 36px rgba(0,0,0,0.13); }
        .body-card:active { transform: scale(0.96); }
        .body-card::after {
            content: ''; position: absolute; top: -60%; left: -60%;
            width: 60%; height: 200%;
            background: linear-gradient(105deg, transparent 40%, rgba(255,255,255,0.5) 50%, transparent 60%);
            transition: left 0.5s ease; pointer-events: none;
        }
        .body-card:hover::after { left: 130%; }
        .body-part-name { font-family: 'Fredoka One', cursive; font-size: 1.6rem; color: var(--card-color); margin-bottom: 2px; line-height: 1.1; }
        .body-part-local { font-size: 0.7rem; font-weight: 800; letter-spacing: 1.5px; color: #bbb; text-transform: uppercase; margin-bottom: 12px; }
        .body-card-divider { width: 60%; height: 2px; border-radius: 2px; background: color-mix(in srgb, var(--card-color) 30%, #eee); margin: 0 auto 14px; }
        .body-img-wrap {
            width: 100%; border-radius: 12px; background: #f4f4f4;
            overflow: hidden; aspect-ratio: 1 / 1;
            transition: transform 0.3s cubic-bezier(.34,1.56,.64,1);
        }
        .body-card:hover .body-img-wrap { transform: scale(1.03); }
        .body-img-wrap img { width: 100%; height: 100%; object-fit: cover; display: block; }
        .body-card.speaking { animation: speakPulse 0.7s ease-in-out infinite alternate !important; }
        @keyframes speakPulse {
            from { box-shadow: 0 0 0 0 color-mix(in srgb, var(--card-color) 50%, transparent); }
            to   { box-shadow: 0 0 0 12px color-mix(in srgb, var(--card-color) 0%, transparent); }
        }
        @keyframes cardEntrance { from{opacity:0;transform:translateY(30px) scale(0.9);} to{opacity:1;transform:translateY(0) scale(1);} }
        <?php foreach(range(1,17) as $i): ?>
        .body-card:nth-child(<?=$i?>){animation-delay:<?=$i*0.04?>s;}
        <?php endforeach; ?>

        /* ── SENSES GRID ── */
        .senses-grid {
            display: grid; grid-template-columns: repeat(5,1fr);
            gap: 16px; margin-bottom: 16px;
        }
        @media(max-width:768px){ .senses-grid { grid-template-columns: repeat(3,1fr); } }
        @media(max-width:480px){ .senses-grid { grid-template-columns: repeat(2,1fr); } }

        /* ── SENSE CARD ── */
        .sense-card {
            background: #fff; border-radius: 20px; padding: 0 0 16px;
            text-align: center; border: 2px solid transparent;
            box-shadow: 0 4px 16px rgba(0,0,0,0.06);
            transition: transform 0.25s cubic-bezier(.34,1.56,.64,1), box-shadow 0.25s, border-color 0.2s;
            cursor: pointer; animation: cardEntrance 0.5s ease both;
            overflow: hidden; display: flex; flex-direction: column; align-items: center;
        }
        .sense-card:hover { transform: translateY(-8px) scale(1.03); box-shadow: 0 16px 32px rgba(0,0,0,0.12); border-color: var(--sc); }
        .sense-card-bar { width: 100%; height: 5px; background: var(--sc); flex-shrink: 0; margin-bottom: 14px; }
        .sense-img-wrap {
            width: 80px; height: 80px; border-radius: 50%;
            overflow: hidden; margin: 0 auto 12px;
            border: 3px solid var(--sc);
            box-shadow: 0 4px 14px rgba(0,0,0,0.1);
            transition: transform 0.3s cubic-bezier(.34,1.56,.64,1);
            flex-shrink: 0;
        }
        .sense-card:hover .sense-img-wrap { transform: scale(1.12) rotate(-5deg); }
        .sense-img-wrap img { width: 100%; height: 100%; object-fit: cover; display: block; }
        .sense-name  { font-family: 'Fredoka One', cursive; font-size: 1.05rem; color: var(--sc); margin-bottom: 3px; padding: 0 8px; }
        .sense-organ { font-size: 0.75rem; font-weight: 800; color: #bbb; margin-bottom: 6px; }
        .sense-desc  { font-size: 0.72rem; font-weight: 700; color: #aaa; line-height: 1.4; padding: 0 10px; }

        /* ── VIDEO SECTION ── */
        .song-section { padding: 0 0 80px; }
        .song-video-card { background: #fff; border-radius: 24px; overflow: hidden; box-shadow: 0 12px 44px rgba(124,58,237,.14); border: 2px solid rgba(124,58,237,.1); }
        .song-vtabs { display: flex; gap: 8px; padding: 16px 18px 0; flex-wrap: wrap; }
        .svtab { font-family: 'Fredoka One', cursive; font-size: .8rem; padding: 7px 16px; border-radius: 50px; border: 2px solid #e8e0f0; background: #fff; color: #9880c0; cursor: pointer; transition: all .2s; display: flex; align-items: center; gap: 6px; }
        .svtab:hover { border-color: var(--song2); color: var(--song); }
        .svtab.active { background: linear-gradient(135deg, var(--song), var(--song2)); color: #fff; border-color: transparent; box-shadow: 0 4px 14px rgba(124,58,237,.28); }
        .song-yt-wrap { padding: 16px 18px 18px; }
        .song-yt-box { width: 100%; aspect-ratio: 16/9; border-radius: 14px; overflow: hidden; background: #0a0a0a; position: relative; box-shadow: 0 6px 24px rgba(0,0,0,.18); }
        .song-yt-box iframe { width: 100%; height: 100%; border: none; display: none; }
        .song-yt-placeholder { position: absolute; inset: 0; background: linear-gradient(135deg, #2d0b6e, #5b21b6); display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 14px; cursor: pointer; }
        .song-yt-placeholder.gone { display: none; }
        .song-play-btn { width: 68px; height: 68px; border-radius: 50%; background: #fff; display: flex; align-items: center; justify-content: center; font-size: 1.6rem; color: var(--song); box-shadow: 0 8px 24px rgba(124,58,237,.4); transition: transform .2s cubic-bezier(.34,1.56,.64,1); }
        .song-yt-placeholder:hover .song-play-btn { transform: scale(1.15); }
        .song-ph-title { font-family: 'Fredoka One', cursive; font-size: 1.15rem; color: #fff; text-align: center; padding: 0 20px; }
        .song-ph-sub { font-size: .75rem; font-weight: 800; color: rgba(255,255,255,.6); }

        /* ── CONFETTI ── */
        .cp { position: fixed; pointer-events: none; z-index: 1000; animation: cfFall linear forwards; }
        @keyframes cfFall { 0%{transform:translateY(-20px) rotate(0deg);opacity:1} 100%{transform:translateY(100vh) rotate(720deg);opacity:0} }
    </style>
</head>
<body>

<!-- ═══ NAV ═══ -->
<nav class="lesson-nav">
    <a href="lessons.php" class="lnav-back"><i class="fas fa-arrow-left"></i> Back</a>
    <div class="lang-toggle">
        <button class="lang-btn active" id="btnEN" onclick="setLang('en')">🇺🇸 EN</button>
        <button class="lang-btn"        id="btnTL" onclick="setLang('tl')">🇵🇭 TL</button>
    </div>
</nav>

<div class="container">

    <!-- ═══ PAGE HEADER ═══ -->
    <div class="page-header">
        <h1 id="pageTitle">Learn the Human Body!</h1>
        <div class="lang-badge" id="langBadge">🇺🇸 English — 17 Body Parts</div>
    </div>

    <!-- ═══ SECTION 1: BODY PARTS GRID ═══ -->
    <div class="section-header-card">
        <div class="sec-icon-box" style="background:linear-gradient(135deg,#ff6b6b,#ff8e53);">
            <i class="fas fa-person"></i>
        </div>
        <div class="sec-title-group">
            <div class="sec-title" id="secPartsTitle">Body Parts</div>
            <div class="sec-subtitle" id="secPartsSub">Tap any card to hear the name!</div>
        </div>
        <div class="sec-line"></div>
    </div>

    <div class="body-grid" id="bodyGrid">
        <?php foreach($bodyParts as $part => $data):
            [$color, $tl_name, $img_en, $img_tl] = $data;
        ?>
        <div class="body-card"
             style="--card-color:<?=$color?>"
             data-part-en="<?=addslashes($part)?>"
             data-part-tl="<?=addslashes($tl_name)?>"
             data-color="<?=$color?>"
             data-img-en="<?=$img_en?>"
             data-img-tl="<?=$img_tl?>"
             onclick="speakCard(this)">
            <div class="body-part-name"><?=$part?></div>
            <div class="body-part-local"><?=$tl_name?></div>
            <div class="body-card-divider"></div>
            <div class="body-img-wrap">
                <img src="<?=$img_en?>" alt="<?=$part?>"
                     onerror="this.parentElement.style.background='#f0f0f0';this.style.display='none'">
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- ═══ SECTION 2: FIVE SENSES ═══ -->
    <div style="margin-top:48px; margin-bottom:16px;">
        <div class="section-header-card">
            <div class="sec-icon-box" style="background:linear-gradient(135deg,#20c997,#12b886);">
                <i class="fas fa-star"></i>
            </div>
            <div class="sec-title-group">
                <div class="sec-title" id="secSensesTitle">The Five Senses!</div>
                <div class="sec-subtitle" id="secSensesSub">How do we feel the world around us?</div>
            </div>
            <div class="sec-line"></div>
        </div>
        <div class="senses-grid" id="sensesGrid"></div>
    </div>

    <!-- ═══ SECTION 3: VIDEO SONGS ═══ -->
    <div style="margin-top:48px;">
        <div class="song-section">
            <div class="section-header-card">
                <div class="sec-icon-box" style="background:linear-gradient(135deg,#5b21b6,#7c3aed);">
                    <i class="fas fa-headphones"></i>
                </div>
                <div class="sec-title-group">
                    <div class="sec-title" id="bodyVideoTitle">Watch &amp; Sing!</div>
                    <div class="sec-subtitle" id="bodyVideoSub">Learn body parts through fun songs — tap to play!</div>
                </div>
                <div class="sec-line"></div>
            </div>
            <div class="song-video-card">
                <div class="song-vtabs" id="bodyVTabs"></div>
                <div class="song-yt-wrap">
                    <div class="song-yt-box">
                        <div class="song-yt-placeholder" id="bodyYtPlaceholder" onclick="loadBodyVideo()">
                            <div class="song-play-btn"><i class="fas fa-play"></i></div>
                            <div class="song-ph-title" id="bodyPhTitle">Head Shoulders Knees &amp; Toes</div>
                            <div class="song-ph-sub" id="bodyPhSub">Tap to play the video</div>
                        </div>
                        <iframe id="bodyYtFrame" src=""
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                            allowfullscreen></iframe>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div><!-- /.container -->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
let currentLang  = 'en';
let speakingCard = null;
let bodyVideoIdx = 0;

const UI = {
    en: {
        pageTitle:      'Learn the Human Body!',
        badge:          '🇺🇸 English — 17 Body Parts',
        secPartsTitle:  'Body Parts',
        secPartsSub:    'Tap any card to hear the name!',
        secSensesTitle: 'The Five Senses!',
        secSensesSub:   'How do we feel the world around us?',
        videoTitle:     'Watch & Sing!',
        videoSub:       'Learn body parts through fun songs — tap to play!',
        videoPhSub:     'Tap to play the video',
        speechLang:     'en-US',
    },
    tl: {
        pageTitle:      'Alamin ang Katawang Pantao!',
        badge:          '🇵🇭 Filipino — 17 Bahagi ng Katawan',
        secPartsTitle:  'Mga Bahagi ng Katawan',
        secPartsSub:    'I-tap ang card para marinig ang pangalan!',
        secSensesTitle: 'Ang Limang Pandama!',
        secSensesSub:   'Paano natin naramdaman ang mundo sa paligid natin?',
        videoTitle:     'Manood at Kumanta!',
        videoSub:       'Matuto tungkol sa katawan sa pamamagitan ng masayang kanta — i-tap para i-play!',
        videoPhSub:     'I-tap para i-play ang video',
        speechLang:     'fil-PH',
    }
};

/* ── SENSES — all using pictures from pictures/ folder ── */
const SENSES_DATA = {
    en: [
        { sense:'Sight',    organ:'Eyes',   img:'pictures/humanbody/eyes.jpg',   desc:'Seeing colors, shapes, and things around us!',         color:'#FFA500' },
        { sense:'Hearing',  organ:'Ears',   img:'pictures/humanbody/ears.jpg', desc:'Listening to music, voices, and sounds!',              color:'#FFD93D' },
        { sense:'Smell',    organ:'Nose',   img:'pictures/humanbody/nose.jpg',   desc:'Smelling flowers, food, and fresh air!',               color:'#6BCB77' },
        { sense:'Taste',    organ:'Tongue', img:'pictures/humanbody/tongue.jpg',   desc:'Tasting sweet, sour, salty, and bitter things!',       color:'#4D96FF' },
        { sense:'Touch',    organ:'Skin',   img:'pictures/humanbody/fingers.jpg',   desc:'Feeling soft, rough, hot, and cold things!',           color:'#FF6B6B' },
    ],
    tl: [
        { sense:'Paningin',  organ:'Mata',   img:'pictures/humanbody/eyes.jpg',   desc:'Nakikita natin ang mga kulay, hugis, at bagay!',        color:'#FFA500' },
        { sense:'Pandinig',  organ:'Tainga', img:'pictures/humanbody/ears.jpg', desc:'Nakikinig tayo ng musika, boses, at tunog!',            color:'#FFD93D' },
        { sense:'Pang-amoy', organ:'Ilong',  img:'pictures/humanbody/nose.jpg',   desc:'Naamoy natin ang mga bulaklak at pagkain!',            color:'#6BCB77' },
        { sense:'Panlasa',   organ:'Dila',   img:'pictures/humanbody/tongue.jpg',   desc:'Nararamdaman natin ang matamis, maasim, maalat!',      color:'#4D96FF' },
        { sense:'Pakiramdam',organ:'Balat',  img:'pictures/humanbody/fingers.jpg',   desc:'Nararamdaman natin ang malambot, malamig, at mainit!', color:'#FF6B6B' },
    ]
};

/* ── VIDEOS — 2 EN, 2 TL ── */
const BODY_VIDEOS = {
    en: [
        { label:'Head Shoulders', icon:'fa-music',  ytId:'h4eueDYPTIg', title:'Head Shoulders Knees & Toes — CoComelon' },
        { label:'Body Song',      icon:'fa-person', ytId:'yPF0K4Lmogi', title:'My Body — Kids Body Parts Song' },
    ],
    tl: [
        { label:'Ulo Balikat', icon:'fa-music',  ytId:'WmGeRIxMPgA', title:'Ulo Balikat Tuhod Paa — Filipino Version' },
        { label:'Katawan Ko',  icon:'fa-person', ytId:'yPF0K4Lmogi', title:'Katawan Ko — Bahagi ng Katawan' },
    ]
};

function setLang(lang) {
    if (lang === currentLang) return;
    currentLang = lang;
    document.getElementById('btnEN').classList.toggle('active', lang==='en');
    document.getElementById('btnTL').classList.toggle('active', lang==='tl');
    const u = UI[lang];
    document.getElementById('pageTitle').textContent      = u.pageTitle;
    document.getElementById('langBadge').textContent      = u.badge;
    document.getElementById('langBadge').classList.toggle('tl-mode', lang==='tl');
    document.getElementById('secPartsTitle').textContent  = u.secPartsTitle;
    document.getElementById('secPartsSub').textContent    = u.secPartsSub;
    document.getElementById('secSensesTitle').textContent = u.secSensesTitle;
    document.getElementById('secSensesSub').textContent   = u.secSensesSub;
    document.getElementById('bodyVideoTitle').textContent = u.videoTitle;
    document.getElementById('bodyVideoSub').textContent   = u.videoSub;
    document.getElementById('bodyPhSub').textContent      = u.videoPhSub;

    const grid = document.getElementById('bodyGrid');
    grid.style.opacity = '0'; grid.style.transform = 'scale(0.97)';
    setTimeout(() => {
        document.querySelectorAll('.body-card').forEach(card => {
            card.querySelector('.body-part-name').textContent  = lang==='en' ? card.dataset.partEn : card.dataset.partTl;
            card.querySelector('.body-part-local').textContent = lang==='en' ? card.dataset.partTl : card.dataset.partEn;
            card.querySelector('img').src = lang==='en' ? card.dataset.imgEn : card.dataset.imgTl;
        });
        grid.style.opacity = '1'; grid.style.transform = 'scale(1)';
        grid.style.transition = 'opacity 0.25s, transform 0.25s';
    }, 200);

    window.speechSynthesis && window.speechSynthesis.cancel();
    buildSenses(lang);
    document.getElementById('bodyYtFrame').src = '';
    document.getElementById('bodyYtFrame').style.display = 'none';
    document.getElementById('bodyYtPlaceholder').classList.remove('gone');
    initBodyVideo();
}

function speakCard(card) {
    const name  = currentLang==='en' ? card.dataset.partEn : card.dataset.partTl;
    const color = card.dataset.color;
    window.speechSynthesis && window.speechSynthesis.cancel();
    if (speakingCard) speakingCard.classList.remove('speaking');
    speakingCard = card;
    card.classList.add('speaking');
    launchConfetti(color);
    if (!window.speechSynthesis) { card.classList.remove('speaking'); return; }
    const utt = new SpeechSynthesisUtterance(name);
    utt.lang = UI[currentLang].speechLang; utt.rate = 0.85; utt.pitch = 1.2; utt.volume = 1;
    utt.onend = utt.onerror = () => card.classList.remove('speaking');
    window.speechSynthesis.speak(utt);
}

function buildSenses(lang) {
    const grid = document.getElementById('sensesGrid');
    grid.innerHTML = '';
    SENSES_DATA[lang].forEach((item, i) => {
        const card = document.createElement('div');
        card.className = 'sense-card';
        card.style.cssText = `--sc:${item.color};animation-delay:${i*0.08}s`;
        card.innerHTML = `
            <div class="sense-card-bar"></div>
            <div class="sense-img-wrap">
                <img src="${item.img}" alt="${item.sense}"
                     onerror="this.parentElement.style.background='#f0f0f0';this.style.display='none'">
            </div>
            <div class="sense-name">${item.sense}</div>
            <div class="sense-organ">${item.organ}</div>
            <div class="sense-desc">${item.desc}</div>`;
        card.onclick = () => { speakText(item.sense); launchConfetti(item.color); };
        grid.appendChild(card);
    });
}

function buildBodyTabs() {
    const el = document.getElementById('bodyVTabs');
    el.innerHTML = '';
    BODY_VIDEOS[currentLang].forEach((v, i) => {
        const btn = document.createElement('button');
        btn.className = 'svtab' + (i===bodyVideoIdx ? ' active' : '');
        btn.innerHTML = `<i class="fas ${v.icon}"></i> ${v.label}`;
        btn.onclick = () => selectBodyVideo(i);
        el.appendChild(btn);
    });
}

function selectBodyVideo(i) {
    bodyVideoIdx = i;
    document.querySelectorAll('#bodyVTabs .svtab').forEach((b,idx) => b.classList.toggle('active', idx===i));
    document.getElementById('bodyPhTitle').textContent = BODY_VIDEOS[currentLang][i].title;
    document.getElementById('bodyYtPlaceholder').classList.remove('gone');
    const f = document.getElementById('bodyYtFrame');
    f.src = ''; f.style.display = 'none';
}

function loadBodyVideo() {
    const v = BODY_VIDEOS[currentLang][bodyVideoIdx];
    const f = document.getElementById('bodyYtFrame');
    f.src = `https://www.youtube.com/embed/${v.ytId}?autoplay=1&rel=0&modestbranding=1`;
    f.style.display = 'block';
    document.getElementById('bodyYtPlaceholder').classList.add('gone');
}

function initBodyVideo() {
    bodyVideoIdx = 0;
    buildBodyTabs();
    document.getElementById('bodyPhTitle').textContent = BODY_VIDEOS[currentLang][0].title;
    document.getElementById('bodyYtPlaceholder').classList.remove('gone');
    const f = document.getElementById('bodyYtFrame');
    f.src = ''; f.style.display = 'none';
}

function speakText(text) {
    if (!window.speechSynthesis) return;
    window.speechSynthesis.cancel();
    const utt = new SpeechSynthesisUtterance(text);
    utt.lang = UI[currentLang].speechLang; utt.rate = 0.85; utt.pitch = 1.1;
    window.speechSynthesis.speak(utt);
}

function launchConfetti(color) {
    const cols = [color,'#FFE66D','#FF6B6B','#A29BFE','#4ECDC4','#FD79A8'];
    for (let i=0; i<30; i++) {
        const p = document.createElement('div'); p.className = 'cp';
        p.style.cssText = `left:${Math.random()*100}vw;top:-12px;background:${cols[Math.floor(Math.random()*cols.length)]};border-radius:${Math.random()>.5?'50%':'2px'};width:${6+Math.random()*8}px;height:${6+Math.random()*8}px;animation-duration:${1.2+Math.random()*1.5}s;animation-delay:${Math.random()*.4}s;`;
        document.body.appendChild(p);
        p.addEventListener('animationend', () => p.remove());
    }
}

buildSenses('en');
initBodyVideo();
</script>
<!-- ── TAKE QUIZ BUTTON ── -->
<style>
.quiz-btn-wrap {
    display: flex;
    justify-content: center;
    padding: 32px 16px 56px;
}
.take-quiz-btn {
    display: inline-flex;
    align-items: center;
    gap: 12px;
    background: linear-gradient(135deg, #0d5407 0%, #1a8c0a 100%);
    color: #fff;
    font-family: 'Fredoka One', cursive;
    font-size: 1.35rem;
    padding: 18px 48px;
    border-radius: 60px;
    text-decoration: none;
    box-shadow: 0 8px 28px rgba(13,84,7,0.35);
    transition: transform 0.25s cubic-bezier(.34,1.56,.64,1), box-shadow 0.25s;
    letter-spacing: 0.5px;
    border: 3px solid rgba(255,255,255,0.2);
}
.take-quiz-btn:hover {
    transform: translateY(-5px) scale(1.05);
    box-shadow: 0 16px 40px rgba(13,84,7,0.4);
    color: #fff;
}
.take-quiz-btn:active { transform: scale(0.97); }
.take-quiz-btn .quiz-icon { font-size: 1.5rem; }
</style>

<div class="quiz-btn-wrap">
    <a href="activities.php" class="take-quiz-btn">
        Take Quiz
    </a>
</div>

</body>
</html>