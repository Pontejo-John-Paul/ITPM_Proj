<?php
$colors = [
    "Red"    => ["Bright and bold — like apples and fire trucks!", "#E53935", "Primary",   "(Pula)"],
    "Blue"   => ["Calm and cool — like the sky and the ocean!",    "#1E88E5", "Primary",   "(Asul)"],
    "Yellow" => ["Sunny and happy — like the sun and bananas!",    "#FDD835", "Primary",   "(Dilaw)"],
    "Green"  => ["Fresh and lively — like grass and leaves!",      "#43A047", "Secondary", "(Berde)"],
    "Orange" => ["Warm and yummy — like oranges and pumpkins!",    "#FB8C00", "Secondary", "(Kahel)"],
    "Purple" => ["Magical and royal — like grapes and lavender!",  "#8E24AA", "Secondary", "(Lila)"],
    "Pink"   => ["Sweet and soft — like flowers and cotton candy!","#E91E8C", "Tertiary",  "(Rosas)"],
    "Brown"  => ["Earthy and cozy — like chocolate and wood!",     "#6D4C41", "Tertiary",  "(Kayumanggi)"],
    "Black"  => ["Dark and strong — like the night sky!",          "#212121", "Tertiary",  "(Itim)"],
    "White"  => ["Clean and bright — like clouds and snow!",       "#ECEFF1", "Tertiary",  "(Puti)"],
];

$colors_tl = [
    "Pula"        => ["Maliwanag at matapang — tulad ng mga mansanas at fire trucks!", "#E53935", "Pangunahin",  "(Red)"],
    "Asul"        => ["Tahimik at malamig — tulad ng langit at karagatan!",            "#1E88E5", "Pangunahin",  "(Blue)"],
    "Dilaw"       => ["Maliwanag at masaya — tulad ng araw at saging!",                "#FDD835", "Pangunahin",  "(Yellow)"],
    "Berde"       => ["Sariwa at masigla — tulad ng damo at mga dahon!",               "#43A047", "Pangalawa",   "(Green)"],
    "Kahel"       => ["Mainit at masarap — tulad ng mga kahel at kalabasa!",           "#FB8C00", "Pangalawa",   "(Orange)"],
    "Lila"        => ["Mahiwagang at marangya — tulad ng ubas at lavender!",           "#8E24AA", "Pangalawa",   "(Purple)"],
    "Rosas"       => ["Matamis at malambot — tulad ng mga bulaklak at cotton candy!",  "#E91E8C", "Pangatlo",    "(Pink)"],
    "Kayumanggi"  => ["Malalim at parang tahanan — tulad ng tsokolate at kahoy!",      "#6D4C41", "Pangatlo",    "(Brown)"],
    "Itim"        => ["Madilim at malakas — tulad ng kalangitan sa gabi!",             "#212121", "Pangatlo",    "(Black)"],
    "Puti"        => ["Malinis at maliwanag — tulad ng mga ulap at niyebe!",           "#ECEFF1", "Pangatlo",    "(White)"],
];

/*
 * NATURE PICTURES DATA
 * -------------------------------------------------------
 * To add your own photos, replace the "src" value with:
 *   - A relative path:  "images/red-apple.jpg"
 *   - An absolute URL:  "/assets/nature/apple.jpg"
 * Leave src as "" to keep the gray placeholder shown.
 * en_label = name shown in English mode
 * tl_label = name shown in Filipino/Tagalog mode
 * -------------------------------------------------------
 */
$nature_pics = [
    "Red" => [
        ["src"=>"pictures/colors/red1.jpg",    "en_label"=>"Apple",        "tl_label"=>"Mansanas"],
        ["src"=>"pictures/colors/red2.jpg",    "en_label"=>"Rose",          "tl_label"=>"Rosas"],
        ["src"=>"pictures/colors/red3.jpg",    "en_label"=>"Strawberry",    "tl_label"=>"Presa"],
        ["src"=>"pictures/colors/red4.jpg",    "en_label"=>"Fire Truck",    "tl_label"=>"Trak ng Bumbero"],
    ],
    "Blue" => [
        ["src"=>"pictures/colors/blue1.jpg",   "en_label"=>"Ocean",         "tl_label"=>"Karagatan"],
        ["src"=>"pictures/colors/blue2.jpg",   "en_label"=>"Sky",           "tl_label"=>"Langit"],
        ["src"=>"pictures/colors/blue3.jpg",   "en_label"=>"Blueberry",     "tl_label"=>"Blueberry"],
        ["src"=>"pictures/colors/blue4.jpg",   "en_label"=>"Butterfly",     "tl_label"=>"Mariposa"],
    ],
    "Yellow" => [
        ["src"=>"pictures/colors/yellow1.jpg", "en_label"=>"Sun",           "tl_label"=>"Araw"],
        ["src"=>"pictures/colors/yellow2.jpg", "en_label"=>"Banana",        "tl_label"=>"Saging"],
        ["src"=>"pictures/colors/yellow3.jpg", "en_label"=>"Sunflower",     "tl_label"=>"Sunflower"],
        ["src"=>"pictures/colors/yellow4.jpg", "en_label"=>"Lemon",         "tl_label"=>"Limon"],
    ],
    "Green" => [
        ["src"=>"pictures/colors/green1.jpg",  "en_label"=>"Leaf",          "tl_label"=>"Dahon"],
        ["src"=>"pictures/colors/green2.jpg",  "en_label"=>"Frog",          "tl_label"=>"Palaka"],
        ["src"=>"pictures/colors/green3.jpg",  "en_label"=>"Broccoli",      "tl_label"=>"Broccoli"],
        ["src"=>"pictures/colors/green4.jpg",  "en_label"=>"Tree",          "tl_label"=>"Puno"],
    ],
    "Orange" => [
        ["src"=>"pictures/colors/orange1.jpg", "en_label"=>"Orange Fruit",  "tl_label"=>"Kahel"],
        ["src"=>"pictures/colors/orange2.jpg", "en_label"=>"Pumpkin",       "tl_label"=>"Kalabasa"],
        ["src"=>"pictures/colors/orange3.jpg", "en_label"=>"Fox",           "tl_label"=>"Lobo"],
        ["src"=>"pictures/colors/orange4.jpg", "en_label"=>"Carrot",        "tl_label"=>"Karot"],
    ],
    "Purple" => [
        ["src"=>"pictures/colors/purple1.jpg", "en_label"=>"Grapes",        "tl_label"=>"Ubas"],
        ["src"=>"pictures/colors/purple2.jpg", "en_label"=>"Lavender",      "tl_label"=>"Lavender"],
        ["src"=>"pictures/colors/purple3.jpg", "en_label"=>"Eggplant",      "tl_label"=>"Talong"],
        ["src"=>"pictures/colors/purple4.jpg", "en_label"=>"Violet Flower", "tl_label"=>"Viola"],
    ],
    "Pink" => [
        ["src"=>"pictures/colors/pink1.jpg",   "en_label"=>"Hair Tie",        "tl_label"=>"Hair Tie"],
        ["src"=>"pictures/colors/pink2.jpg",   "en_label"=>"Flamingo",      "tl_label"=>"Flamingo"],
        ["src"=>"pictures/colors/pink3.jpg",   "en_label"=>"Pig",           "tl_label"=>"Baboy"],
        ["src"=>"pictures/colors/pink4.jpg",   "en_label"=>"Cherry Blossom","tl_label"=>"Sakura"],
    ],
    "Brown" => [
        ["src"=>"pictures/colors/brown1.jpg",  "en_label"=>"Chocolate",     "tl_label"=>"Tsokolate"],
        ["src"=>"pictures/colors/brown2.jpg",  "en_label"=>"Bear",          "tl_label"=>"Oso"],
        ["src"=>"pictures/colors/brown3.jpg",  "en_label"=>"Wood",          "tl_label"=>"Kahoy"],
        ["src"=>"pictures/colors/brown4.jpg",  "en_label"=>"Coffee",        "tl_label"=>"Kape"],
    ],
    "Black" => [
        ["src"=>"pictures/colors/black1.jpg",  "en_label"=>"Night Sky",     "tl_label"=>"Kalangitan sa Gabi"],
        ["src"=>"pictures/colors/black2.jpg",  "en_label"=>"Crow",          "tl_label"=>"Uwak"],
        ["src"=>"pictures/colors/black3.jpg",  "en_label"=>"Charcoal",         "tl_label"=>"Charcoal"],
        ["src"=>"pictures/colors/black4.jpg",  "en_label"=>"Cat",     "tl_label"=>"Pusa"],
    ],
    "White" => [
        ["src"=>"pictures/colors/white1.jpg",  "en_label"=>"Clouds",        "tl_label"=>"Ulap"],
        ["src"=>"pictures/colors/white2.jpg",  "en_label"=>"Dove",          "tl_label"=>"Kalapati"],
        ["src"=>"pictures/colors/white3.jpg",  "en_label"=>"Milk",          "tl_label"=>"Gatas"],
        ["src"=>"pictures/colors/white4.jpg",  "en_label"=>"Snow",          "tl_label"=>"Niyebe"],
    ],
];

$color_hex = [
    "Red"=>"#E53935","Blue"=>"#1E88E5","Yellow"=>"#FDD835",
    "Green"=>"#43A047","Orange"=>"#FB8C00","Purple"=>"#8E24AA",
    "Pink"=>"#E91E8C","Brown"=>"#6D4C41","Black"=>"#212121","White"=>"#ECEFF1",
];
$color_tl_name = [
    "Red"=>"Pula","Blue"=>"Asul","Yellow"=>"Dilaw","Green"=>"Berde",
    "Orange"=>"Kahel","Purple"=>"Lila","Pink"=>"Rosas","Brown"=>"Kayumanggi",
    "Black"=>"Itim","White"=>"Puti",
];
$light_colors = ["Yellow","White","Pink"];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Colors - E-KINDER</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Fredoka+One&family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root { --green-dark:#0d5407; --song:#7c3aed; --song2:#a855f7; }
        * { box-sizing:border-box; margin:0; padding:0; }
        body {
            font-family:'Nunito',sans-serif; background-color:#fff9f0;
            background-image:
                radial-gradient(circle at 15% 20%,rgba(78,205,196,.12) 0%,transparent 40%),
                radial-gradient(circle at 85% 70%,rgba(255,107,107,.12) 0%,transparent 40%),
                radial-gradient(circle at 50% 90%,rgba(162,155,254,.1) 0%,transparent 40%);
            min-height:100vh; overflow-x:hidden;
        }

        /* ── NAVBAR ── */
        .lesson-nav { padding:0 28px; height:64px; background:#fff; box-shadow:0 4px 20px rgba(0,0,0,.07); position:sticky; top:0; z-index:100; display:flex; align-items:center; justify-content:space-between; gap:16px; }
        .lnav-back { display:inline-flex; align-items:center; gap:7px; background:var(--green-dark); color:#fff; font-family:'Fredoka One',cursive; font-size:.9rem; padding:8px 18px; border-radius:50px; text-decoration:none; box-shadow:0 4px 12px rgba(13,84,7,.25); transition:transform .2s; flex-shrink:0; }
        .lnav-back:hover { transform:scale(1.06); color:#fff; }
        .lang-toggle { display:inline-flex; align-items:center; background:#f0f0f0; border-radius:50px; padding:4px; border:2px solid #e0e0e0; flex-shrink:0; }
        .lang-btn { font-family:'Fredoka One',cursive; font-size:.82rem; padding:6px 16px; border-radius:50px; border:none; cursor:pointer; background:transparent; color:#aaa; transition:background .2s,color .2s,transform .15s; display:flex; align-items:center; gap:5px; }
        .lang-btn.active { background:var(--green-dark); color:#fff; box-shadow:0 3px 10px rgba(13,84,7,.25); transform:scale(1.04); }

        /* ── PAGE HEADER ── */
        .page-header { text-align:center; padding:36px 0 20px; }
        .page-header h1 { font-family:'Fredoka One',cursive; font-size:clamp(2rem,5vw,3rem); color:var(--green-dark); letter-spacing:1px; }
        .lang-badge { display:inline-flex; align-items:center; gap:6px; margin-top:10px; padding:5px 16px; border-radius:50px; font-family:'Fredoka One',cursive; font-size:.82rem; background:#fff; border:2px solid #c8e6c9; color:var(--green-dark); box-shadow:0 2px 8px rgba(13,84,7,.08); transition:all .3s; }
        .lang-badge.tl-mode { background:#fff3e0; border-color:#ffcc80; color:#e65100; }

        /* ── FILTER BUTTONS ── */
        .filters { display:flex; justify-content:center; flex-wrap:wrap; gap:10px; margin-bottom:28px; }
        .filter-btn { font-family:'Fredoka One',cursive; font-size:.95rem; padding:9px 24px; border-radius:50px; border:2px solid #e0e0e0; background:#fff; color:#aaa; cursor:pointer; transition:all .2s cubic-bezier(.34,1.56,.64,1); }
        .filter-btn:hover { border-color:#ccc; color:#666; transform:translateY(-2px); }
        .filter-btn.active { background:var(--green-dark); border-color:var(--green-dark); color:#fff; box-shadow:0 4px 14px rgba(13,84,7,.3); transform:translateY(-2px); }

        /* ── LESSON GUIDE CARD ── */
        .lesson-guide { display:flex; align-items:center; gap:16px; background:#fff; border-radius:18px; padding:16px 24px; margin-bottom:28px; box-shadow:0 2px 16px rgba(0,0,0,.06); border:1.5px solid #f0f0f0; }
        .guide-icon { width:52px; height:52px; border-radius:14px; display:flex; align-items:center; justify-content:center; font-size:1.4rem; flex-shrink:0; box-shadow:0 4px 12px rgba(0,0,0,.15); }
        .guide-text-title { font-family:'Fredoka One',cursive; font-size:1.1rem; color:#1a1a1a; margin-bottom:2px; }
        .guide-text-sub   { font-size:.82rem; color:#aaa; font-weight:600; }

        /* ── COLORS GRID ── */
        .colors-grid { display:grid; grid-template-columns:repeat(5,1fr); gap:28px; padding:0 0 20px; transition:opacity .25s,transform .25s; }
        @media(max-width:992px){ .colors-grid { grid-template-columns:repeat(3,1fr); } }
        @media(max-width:600px){ .colors-grid { grid-template-columns:repeat(2,1fr); } }

        /* ── COLOR CARD ── */
        .color-card { background:#fff; border-radius:28px; padding:22px 16px 18px; text-align:center; cursor:pointer; position:relative; overflow:hidden; border:3px solid transparent; box-shadow:0 6px 20px rgba(0,0,0,.07); transition:transform .25s cubic-bezier(.34,1.56,.64,1),box-shadow .25s; animation:cardEntrance .5s ease both; }
        .color-card:hover { transform:translateY(-10px) scale(1.03); box-shadow:0 20px 40px rgba(0,0,0,.13); }
        .color-card:active { transform:scale(.96); }
        .color-card::before { content:''; position:absolute; top:0; left:0; right:0; height:6px; background:var(--card-color); border-radius:28px 28px 0 0; }
        .color-card::after  { content:''; position:absolute; top:-60%; left:-60%; width:60%; height:200%; background:linear-gradient(105deg,transparent 40%,rgba(255,255,255,.5) 50%,transparent 60%); transition:left .5s; pointer-events:none; }
        .color-card:hover::after { left:130%; }
        .color-card.hidden  { display:none; }
        .color-card.playing { animation:soundPulse .4s ease; }
        @keyframes soundPulse  { 0%{transform:scale(1)} 50%{transform:scale(1.07)} 100%{transform:scale(1)} }
        @keyframes cardEntrance{ from{opacity:0;transform:translateY(30px) scale(.9)} to{opacity:1;transform:none} }
        .color-circle-wrap { width:100px; height:100px; margin:0 auto 14px; position:relative; display:flex; align-items:center; justify-content:center; }
        .color-circle { width:80px; height:80px; border-radius:50%; background:var(--card-color); box-shadow:0 8px 20px rgba(0,0,0,.18); transition:transform .3s cubic-bezier(.34,1.56,.64,1); border:4px solid rgba(255,255,255,.6); }
        .color-card:hover .color-circle { transform:scale(1.15) rotate(-10deg); }
        .color-circle-wrap::after { content:''; position:absolute; inset:0; border-radius:50%; border:3px dashed var(--card-color); opacity:0; transition:opacity .3s; animation:spinRing 4s linear infinite; }
        .color-card:hover .color-circle-wrap::after { opacity:.5; }
        @keyframes spinRing { to{transform:rotate(360deg)} }
        .color-name  { font-family:'Fredoka One',cursive; font-size:1.3rem; color:#2d2d2d; margin-bottom:3px; }
        .speak-icon  { font-size:.75em; opacity:.45; margin-left:4px; display:inline-block; transition:opacity .2s,transform .2s; }
        .color-card:hover .speak-icon { opacity:.85; }
        .color-card.speaking .speak-icon { opacity:1; animation:speakerPulse .6s ease infinite alternate; }
        @keyframes speakerPulse { from{transform:scale(1)} to{transform:scale(1.3)} }
        .color-local { font-size:.82rem; color:#999; font-weight:700; }
        .color-badge { display:inline-block; margin-top:8px; font-size:.72rem; font-weight:800; padding:3px 12px; border-radius:50px; background:var(--card-color); color:#fff; letter-spacing:.5px; text-shadow:0 1px 2px rgba(0,0,0,.2); }
        .color-badge.dark-text { color:#555; text-shadow:none; }

        /* ── TTS NOTICE ── */
        .tts-notice { display:flex; align-items:center; gap:10px; background:#f0fdf4; border:1.5px solid #bbf7d0; border-radius:14px; padding:10px 18px; margin-bottom:20px; font-size:.82rem; color:#166534; font-weight:700; }
        .tts-notice i { font-size:1rem; color:#16a34a; }

        /* ── LESSON SECTIONS ── */
        .lesson-section { background:#fff; border-radius:24px; padding:28px 28px 32px; box-shadow:0 4px 24px rgba(0,0,0,.06); border:1.5px solid #f0f0f0; margin-bottom:32px; }

        /* ── PRIMARY COLORS ── */
        .primary-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:16px; }
        @media(max-width:576px){ .primary-grid { grid-template-columns:1fr; } }
        .primary-card  { border-radius:20px; padding:24px 16px; text-align:center; border:2px solid transparent; transition:transform .2s; }
        .primary-card:hover { transform:translateY(-4px); }
        .primary-circle{ width:72px; height:72px; border-radius:50%; margin:0 auto 14px; border:4px solid rgba(255,255,255,.6); box-shadow:0 8px 24px rgba(0,0,0,.15); }
        .primary-name  { font-family:'Fredoka One',cursive; font-size:1.2rem; margin-bottom:2px; }
        .primary-local { font-size:.8rem; font-weight:700; margin-bottom:8px; }
        .primary-desc  { font-size:.8rem; font-weight:600; line-height:1.4; }
        .primary-badge { display:inline-block; margin-top:10px; font-size:.72rem; font-weight:800; padding:4px 14px; border-radius:50px; }
        .primary-note  { margin-top:20px; padding:14px 18px; border-radius:14px; background:#f8f8f8; font-size:.85rem; color:#555; font-weight:700; text-align:center; border-left:4px solid var(--green-dark); }

        /* ── SECONDARY COLORS MIXING ── */
        .mix-lesson-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:14px; }
        @media(max-width:576px){ .mix-lesson-grid { grid-template-columns:1fr; } }
        .mix-lesson-card { border-radius:18px; padding:18px 12px; text-align:center; border:2px solid #f0f0f0; transition:transform .2s; }
        .mix-lesson-card:hover { transform:translateY(-4px); }
        .mix-circles   { display:flex; align-items:center; justify-content:center; gap:6px; margin-bottom:12px; flex-wrap:wrap; }
        .mix-dot       { width:34px; height:34px; border-radius:50%; border:3px solid rgba(255,255,255,.6); box-shadow:0 4px 10px rgba(0,0,0,.15); flex-shrink:0; }
        .mix-result-dot{ width:44px; height:44px; border-radius:50%; border:3px solid rgba(255,255,255,.6); box-shadow:0 6px 16px rgba(0,0,0,.2); flex-shrink:0; }
        .mix-op        { font-size:1.1rem; color:#bbb; font-weight:900; flex-shrink:0; }
        .mix-name      { font-family:'Fredoka One',cursive; font-size:1.1rem; margin-bottom:2px; }
        .mix-local     { font-size:.78rem; font-weight:700; color:#999; margin-bottom:4px; }
        .mix-formula   { font-size:.75rem; color:#bbb; font-weight:700; }

        /* ── COLORS IN NATURE ── */
        .nature-color-block { margin-bottom:32px; }
        .nature-color-block:last-child { margin-bottom:0; }
        .nature-color-header { display:flex; align-items:center; gap:14px; margin-bottom:16px; padding-bottom:14px; border-bottom:2px dashed #f0f0f0; }
        .nature-color-swatch { width:48px; height:48px; border-radius:50%; flex-shrink:0; border:4px solid rgba(255,255,255,.7); box-shadow:0 6px 18px rgba(0,0,0,.15); }
        .nature-color-name-en{ font-family:'Fredoka One',cursive; font-size:1.4rem; line-height:1; }
        .nature-color-name-tl{ font-size:.82rem; font-weight:700; opacity:.7; margin-top:2px; }

        /* 4-picture grid per color */
        .nature-pics-grid { display:grid; grid-template-columns:repeat(4,1fr); gap:14px; }
        @media(max-width:768px){ .nature-pics-grid { grid-template-columns:repeat(2,1fr); } }

        .nature-pic-card { border-radius:18px; overflow:hidden; border:2px solid #f0f0f0; box-shadow:0 4px 14px rgba(0,0,0,.06); transition:transform .2s,box-shadow .2s; background:#fff; }
        .nature-pic-card:hover { transform:translateY(-5px); box-shadow:0 10px 28px rgba(0,0,0,.12); }

        .nature-pic-img-wrap { width:100%; aspect-ratio:1/1; overflow:hidden; position:relative; background:#f5f5f5; }
        .nature-pic-img-wrap img { width:100%; height:100%; object-fit:cover; display:block; transition:transform .3s; }
        .nature-pic-card:hover .nature-pic-img-wrap img { transform:scale(1.06); }

        /* placeholder when src is empty */
        .nature-pic-placeholder { position:absolute; inset:0; display:flex; flex-direction:column; align-items:center; justify-content:center; gap:8px; background:#fafafa; }
        .ph-icon  { width:52px; height:52px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:1.3rem; opacity:.5; }
        .ph-text  { font-size:.7rem; font-weight:800; color:#bbb; text-align:center; padding:0 8px; line-height:1.3; }

        .nature-pic-label { padding:10px 10px 12px; text-align:center; }
        .pic-name { font-family:'Fredoka One',cursive; font-size:1rem; line-height:1.2; }

        /* ── WARM / COOL ── */
        .wc-grid  { display:grid; grid-template-columns:1fr 1fr; gap:14px; }
        @media(max-width:480px){ .wc-grid { grid-template-columns:1fr; } }
        .wc-card  { border-radius:18px; padding:20px 16px; border:2px solid #f0f0f0; }
        .wc-title { font-family:'Fredoka One',cursive; font-size:1.1rem; margin-bottom:12px; }
        .wc-dots  { display:flex; gap:8px; flex-wrap:wrap; margin-bottom:12px; }
        .wc-dot   { width:38px; height:38px; border-radius:50%; border:3px solid rgba(255,255,255,.6); box-shadow:0 4px 12px rgba(0,0,0,.12); }
        .wc-desc  { font-size:.82rem; font-weight:600; line-height:1.5; }
        .wc-examples { margin-top:8px; font-size:.78rem; color:#999; font-weight:700; }

        /* ── PH FLAG ── */
        .flag-wrap     { display:flex; flex-direction:column; align-items:center; gap:20px; }
        .flag-svg-wrap { width:100%; max-width:480px; }
        .flag-hotspots { display:flex; flex-wrap:wrap; justify-content:center; gap:12px; margin-top:8px; }
        .flag-chip { display:flex; align-items:center; gap:8px; padding:8px 16px; border-radius:50px; background:#fff; border:2px solid #e0e0e0; font-family:'Fredoka One',cursive; font-size:.88rem; cursor:pointer; transition:all .2s; color:#555; }
        .flag-chip:hover { transform:translateY(-2px); box-shadow:0 4px 14px rgba(0,0,0,.1); }
        .flag-chip .fc-dot { width:16px; height:16px; border-radius:50%; flex-shrink:0; }
        #flagInfo { margin-top:14px; padding:14px 18px; background:#f8f8f8; border-radius:12px; font-size:.85rem; color:#555; font-weight:700; text-align:center; min-height:44px; width:100%; max-width:480px; }

        /* ── MIXING LAB ── */
        .mix-lab { display:flex; align-items:center; justify-content:center; gap:16px; flex-wrap:wrap; }
        .mix-picker-wrap { text-align:center; }
        .mix-label { font-family:'Fredoka One',cursive; font-size:.85rem; color:#888; margin-bottom:10px; }
        .mix-swatches  { display:flex; gap:8px; flex-wrap:wrap; justify-content:center; max-width:220px; }
        .mix-swatch    { width:40px; height:40px; border-radius:50%; cursor:pointer; border:3px solid transparent; transition:transform .2s cubic-bezier(.34,1.56,.64,1),border-color .2s; box-shadow:0 3px 8px rgba(0,0,0,.15); }
        .mix-swatch:hover  { transform:scale(1.15); }
        .mix-swatch.active { border-color:#1a1a1a; transform:scale(1.2); }
        .mix-plus,.mix-equals { font-size:1.6rem; color:#ccc; font-weight:900; flex-shrink:0; }
        .mix-result-wrap   { text-align:center; }
        .mix-result-circle { width:80px; height:80px; border-radius:50%; margin:0 auto 10px; background:#f0f0f0; border:4px solid #e0e0e0; transition:background .4s,box-shadow .4s; box-shadow:0 6px 20px rgba(0,0,0,.1); }
        .mix-result-circle.has-color { box-shadow:0 8px 28px rgba(0,0,0,.2); }
        .mix-result-name { font-family:'Fredoka One',cursive; font-size:1.1rem; color:#555; transition:color .3s; }

        /* ── VIDEO SECTION ── */
        .song-section { padding:0 0 80px; }
        .section-header-card { display:flex; align-items:center; gap:16px; background:#fff; border-radius:20px; padding:18px 24px; box-shadow:0 4px 18px rgba(0,0,0,.06); margin-bottom:28px; }
        .sec-icon-box  { width:56px; height:56px; border-radius:16px; display:flex; align-items:center; justify-content:center; font-size:1.4rem; flex-shrink:0; color:#fff; box-shadow:0 6px 16px rgba(0,0,0,.12); }
        .sec-title     { font-family:'Fredoka One',cursive; font-size:1.5rem; color:#1a1a2e; line-height:1.15; margin:0; }
        .sec-subtitle  { font-size:.875rem; color:#aaa; font-weight:700; margin:3px 0 0; }
        .sec-line      { flex:1; height:2px; background:linear-gradient(90deg,#e0e0e0 0%,transparent 100%); border-radius:99px; margin-left:8px; min-width:40px; }
        .song-video-card { background:#fff; border-radius:24px; overflow:hidden; box-shadow:0 12px 44px rgba(124,58,237,.14); border:2px solid rgba(124,58,237,.1); }
        .song-vtabs    { display:flex; gap:8px; padding:16px 18px 0; flex-wrap:wrap; }
        .svtab         { font-family:'Fredoka One',cursive; font-size:.8rem; padding:7px 16px; border-radius:50px; border:2px solid #e8e0f0; background:#fff; color:#9880c0; cursor:pointer; transition:all .2s; display:flex; align-items:center; gap:6px; }
        .svtab:hover   { border-color:var(--song2); color:var(--song); }
        .svtab.active  { background:linear-gradient(135deg,var(--song),var(--song2)); color:#fff; border-color:transparent; box-shadow:0 4px 14px rgba(124,58,237,.28); }
        .song-yt-wrap  { padding:16px 18px 18px; }
        .song-yt-box   { width:100%; aspect-ratio:16/9; border-radius:14px; overflow:hidden; background:#0a0a0a; position:relative; box-shadow:0 6px 24px rgba(0,0,0,.18); }
        .song-yt-box iframe { width:100%; height:100%; border:none; display:none; }
        .song-yt-placeholder { position:absolute; inset:0; background:linear-gradient(135deg,#2d0b6e,#5b21b6); display:flex; flex-direction:column; align-items:center; justify-content:center; gap:14px; cursor:pointer; }
        .song-yt-placeholder.gone { display:none; }
        .song-play-btn { width:68px; height:68px; border-radius:50%; background:#fff; display:flex; align-items:center; justify-content:center; font-size:1.6rem; color:var(--song); box-shadow:0 8px 24px rgba(124,58,237,.4); transition:transform .2s cubic-bezier(.34,1.56,.64,1); }
        .song-yt-placeholder:hover .song-play-btn { transform:scale(1.15); }
        .song-ph-title { font-family:'Fredoka One',cursive; font-size:1.15rem; color:#fff; text-align:center; padding:0 20px; }
        .song-ph-sub   { font-size:.75rem; font-weight:800; color:rgba(255,255,255,.6); }

        /* ── CONFETTI ── */
        .cp { position:fixed; pointer-events:none; z-index:1000; animation:cfFall linear forwards; }
        @keyframes cfFall { 0%{transform:translateY(-20px) rotate(0deg);opacity:1} 100%{transform:translateY(100vh) rotate(720deg);opacity:0} }
    </style>
</head>
<body>

<!-- ═══ NAV ═══ -->
<nav class="lesson-nav">
    <a href="lessons.php" class="lnav-back"><i class="fas fa-arrow-left"></i> Back</a>
    <div style="flex:1"></div>
    <div class="lang-toggle">
        <button class="lang-btn active" id="btnEN" onclick="setLang('en')"><i class="fas fa-globe-americas"></i> EN</button>
        <button class="lang-btn"        id="btnTL" onclick="setLang('tl')"><i class="fas fa-flag"></i> TL</button>
    </div>
</nav>

<div class="container">

    <!-- ═══ PAGE HEADER ═══ -->
    <div class="page-header">
        <h1 id="pageTitle">Learn the Colors!</h1>
        <div class="lang-badge" id="langBadge"><i class="fas fa-globe-americas"></i> English — 10 Colors</div>
    </div>

    <!-- ═══ TTS NOTICE ═══ -->
    <div class="tts-notice">
        <i class="fas fa-volume-high"></i>
        <span id="ttsNoticeText">Tap any color card to hear its name spoken aloud!</span>
    </div>

    <!-- ═══ FILTER BUTTONS ═══ -->
    <div class="filters">
        <button class="filter-btn active" data-key="All"       onclick="filterColors('All',this)"><i class="fas fa-palette"></i> <span id="tabAll">All</span></button>
        <button class="filter-btn"        data-key="Primary"   onclick="filterColors('Primary',this)"><i class="fas fa-star"></i> <span id="tabPrimary">Primary</span></button>
        <button class="filter-btn"        data-key="Secondary" onclick="filterColors('Secondary',this)"><i class="fas fa-circle-half-stroke"></i> <span id="tabSecondary">Secondary</span></button>
        <button class="filter-btn"        data-key="Tertiary"  onclick="filterColors('Tertiary',this)"><i class="fas fa-droplet"></i> <span id="tabTertiary">Tertiary</span></button>
    </div>

    <!-- ═══ LESSON GUIDE ═══ -->
    <div class="lesson-guide">
        <div class="guide-icon" style="background:linear-gradient(135deg,#a855f7,#7c3aed)"><i class="fas fa-palette" style="color:#fff;font-size:1.3rem"></i></div>
        <div>
            <div class="guide-text-title" id="guideTitle">Learn the Colors!</div>
            <div class="guide-text-sub"   id="guideSub">Tap any color card to hear its name!</div>
        </div>
    </div>

    <!-- ═══ COLORS GRID ═══ -->
    <div class="colors-grid" id="colorsGrid"></div>

    <!-- ═══ SECTION 1 — PRIMARY COLORS ═══ -->
    <div class="lesson-section">
        <div class="lesson-guide" style="margin-bottom:24px;box-shadow:none;border:none;padding:0">
            <div class="guide-icon" style="background:linear-gradient(135deg,#f59e0b,#ef4444)"><i class="fas fa-star" style="color:#fff;font-size:1.3rem"></i></div>
            <div>
                <div class="guide-text-title" id="primaryTitle">Primary Colors</div>
                <div class="guide-text-sub"   id="primarySub">These 3 colors cannot be made by mixing other colors!</div>
            </div>
        </div>
        <div class="primary-grid">
            <div class="primary-card" style="background:#fff5f5;">
                <div class="primary-circle" style="background:#E53935;"></div>
                <div class="primary-name"  style="color:#C62828;" id="pc-red-name">Red</div>
                <div class="primary-local" style="color:#E53935;" id="pc-red-local">(Pula)</div>
                <div class="primary-desc"  style="color:#888;"    id="pc-red-desc">Bold and bright — like apples and fire trucks!</div>
                <span class="primary-badge" style="background:#FFCDD2;color:#B71C1C;" id="pc-red-badge">Primary Color</span>
            </div>
            <div class="primary-card" style="background:#fffde7;">
                <div class="primary-circle" style="background:#FDD835;"></div>
                <div class="primary-name"  style="color:#F57F17;" id="pc-yellow-name">Yellow</div>
                <div class="primary-local" style="color:#F9A825;" id="pc-yellow-local">(Dilaw)</div>
                <div class="primary-desc"  style="color:#888;"    id="pc-yellow-desc">Sunny and happy — like the sun and bananas!</div>
                <span class="primary-badge" style="background:#FFF9C4;color:#F57F17;" id="pc-yellow-badge">Primary Color</span>
            </div>
            <div class="primary-card" style="background:#e3f2fd;">
                <div class="primary-circle" style="background:#1E88E5;"></div>
                <div class="primary-name"  style="color:#1565C0;" id="pc-blue-name">Blue</div>
                <div class="primary-local" style="color:#1E88E5;" id="pc-blue-local">(Asul)</div>
                <div class="primary-desc"  style="color:#888;"    id="pc-blue-desc">Calm and cool — like the sky and the ocean!</div>
                <span class="primary-badge" style="background:#BBDEFB;color:#0D47A1;" id="pc-blue-badge">Primary Color</span>
            </div>
        </div>
        <div class="primary-note" id="primaryNote">
            These are called <strong>Primary Colors</strong> because they are the <em>parent colors</em> — you cannot make them by mixing other colors. All other colors come from these three!
        </div>
    </div>

    <!-- ═══ SECTION 2 — SECONDARY COLORS ═══ -->
    <div class="lesson-section">
        <div class="lesson-guide" style="margin-bottom:24px;box-shadow:none;border:none;padding:0">
            <div class="guide-icon" style="background:linear-gradient(135deg,#f87171,#f59e0b)"><i class="fas fa-flask" style="color:#fff;font-size:1.3rem"></i></div>
            <div>
                <div class="guide-text-title" id="mixLessonTitle">Secondary Colors — Let's Mix!</div>
                <div class="guide-text-sub"   id="mixLessonSub">Mix 2 primary colors together to make a secondary color!</div>
            </div>
        </div>
        <div class="mix-lesson-grid">
            <div class="mix-lesson-card" style="background:#fff8e1;">
                <div class="mix-circles">
                    <div class="mix-dot" style="background:#E53935;"></div>
                    <span class="mix-op">+</span>
                    <div class="mix-dot" style="background:#FDD835;"></div>
                    <span class="mix-op">=</span>
                    <div class="mix-result-dot" style="background:#FB8C00;"></div>
                </div>
                <div class="mix-name"    style="color:#E65100;" id="mix-orange-name">Orange</div>
                <div class="mix-local"   id="mix-orange-local">Kahel</div>
                <div class="mix-formula" id="mix-orange-formula">Red + Yellow</div>
            </div>
            <div class="mix-lesson-card" style="background:#e8f5e9;">
                <div class="mix-circles">
                    <div class="mix-dot" style="background:#FDD835;"></div>
                    <span class="mix-op">+</span>
                    <div class="mix-dot" style="background:#1E88E5;"></div>
                    <span class="mix-op">=</span>
                    <div class="mix-result-dot" style="background:#43A047;"></div>
                </div>
                <div class="mix-name"    style="color:#2E7D32;" id="mix-green-name">Green</div>
                <div class="mix-local"   id="mix-green-local">Berde</div>
                <div class="mix-formula" id="mix-green-formula">Yellow + Blue</div>
            </div>
            <div class="mix-lesson-card" style="background:#f3e5f5;">
                <div class="mix-circles">
                    <div class="mix-dot" style="background:#1E88E5;"></div>
                    <span class="mix-op">+</span>
                    <div class="mix-dot" style="background:#E53935;"></div>
                    <span class="mix-op">=</span>
                    <div class="mix-result-dot" style="background:#8E24AA;"></div>
                </div>
                <div class="mix-name"    style="color:#6A1B9A;" id="mix-purple-name">Purple</div>
                <div class="mix-local"   id="mix-purple-local">Lila</div>
                <div class="mix-formula" id="mix-purple-formula">Blue + Red</div>
            </div>
        </div>
        <div class="primary-note" style="margin-top:20px;" id="mixNote">
            Orange, Green, and Purple are called <strong>Secondary Colors</strong> — they are made by mixing two primary colors together!
        </div>
    </div>

    <!-- ═══ SECTION 3 — COLORS IN NATURE (PICTURE CARDS) ═══ -->
    <div class="lesson-section">
        <div class="lesson-guide" style="margin-bottom:28px;box-shadow:none;border:none;padding:0">
            <div class="guide-icon" style="background:linear-gradient(135deg,#34d399,#059669)"><i class="fas fa-images" style="color:#fff;font-size:1.3rem"></i></div>
            <div>
                <div class="guide-text-title" id="natureTitle">Colors in the Environment</div>
                <div class="guide-text-sub"   id="natureSub">Look at real pictures — colors are everywhere around us!</div>
            </div>
        </div>

        <!-- ╔══════════════════════════════════════════════════════════╗ -->
        <!-- ║  COLORS IN NATURE — HARD-CODED PICTURE CARDS            ║ -->
        <!-- ║  Para palitan ang picture, baguhin lang ang src=""       ║ -->
        <!-- ║  sa loob ng <img> tag ng card na gusto mong palitan.     ║ -->
        <!-- ╚══════════════════════════════════════════════════════════╝ -->

        <!-- ═══ RED ═══ -->
        <div class="nature-color-block">
            <div class="nature-color-header">
                <div class="nature-color-swatch" style="background:#E53935;"></div>
                <div>
                    <div class="nature-color-name-en" style="color:#E53935;">Red</div>
                    <div class="nature-color-name-tl"  style="color:#E53935;">Pula</div>
                </div>
            </div>
            <div class="nature-pics-grid">
                <div class="nature-pic-card">
                    <div class="nature-pic-img-wrap">
                        <img src="pictures/colors/red1.png" alt="Apple" loading="lazy">
                    </div>
                    <div class="nature-pic-label" style="background:#E5393511;border-top:2px solid #E5393522;">
                        <div class="pic-name en-only" style="color:#E53935;">Apple</div>
                        <div class="pic-name tl-only" style="color:#E53935;display:none;">Mansanas</div>
                    </div>
                </div>
                <div class="nature-pic-card">
                    <div class="nature-pic-img-wrap">
                        <img src="pictures/colors/red2.jpeg" alt="Rose" loading="lazy">
                    </div>
                    <div class="nature-pic-label" style="background:#E5393511;border-top:2px solid #E5393522;">
                        <div class="pic-name en-only" style="color:#E53935;">Rose</div>
                        <div class="pic-name tl-only" style="color:#E53935;display:none;">Rosas</div>
                    </div>
                </div>
                <div class="nature-pic-card">
                    <div class="nature-pic-img-wrap">
                        <img src="pictures/colors/red3.jpg" alt="Strawberry" loading="lazy">
                    </div>
                    <div class="nature-pic-label" style="background:#E5393511;border-top:2px solid #E5393522;">
                        <div class="pic-name en-only" style="color:#E53935;">Strawberry</div>
                        <div class="pic-name tl-only" style="color:#E53935;display:none;">Presa</div>
                    </div>
                </div>
                <div class="nature-pic-card">
                    <div class="nature-pic-img-wrap">
                        <img src="pictures/colors/red4.jpg" alt="Fire Truck" loading="lazy">
                    </div>
                    <div class="nature-pic-label" style="background:#E5393511;border-top:2px solid #E5393522;">
                        <div class="pic-name en-only" style="color:#E53935;">Fire Truck</div>
                        <div class="pic-name tl-only" style="color:#E53935;display:none;">Trak ng Bumbero</div>
                    </div>
                </div>
            </div>
        </div>
        <hr style="border:none;border-top:2px dashed #f0f0f0;margin:28px 0;">

        <!-- ═══ BLUE ═══ -->
        <div class="nature-color-block">
            <div class="nature-color-header">
                <div class="nature-color-swatch" style="background:#1E88E5;"></div>
                <div>
                    <div class="nature-color-name-en" style="color:#1E88E5;">Blue</div>
                    <div class="nature-color-name-tl"  style="color:#1E88E5;">Asul</div>
                </div>
            </div>
            <div class="nature-pics-grid">
                <div class="nature-pic-card">
                    <div class="nature-pic-img-wrap">
                        <img src="pictures/colors/blue1.jpg" alt="Ocean" loading="lazy">
                    </div>
                    <div class="nature-pic-label" style="background:#1E88E511;border-top:2px solid #1E88E522;">
                        <div class="pic-name en-only" style="color:#1E88E5;">Ocean</div>
                        <div class="pic-name tl-only" style="color:#1E88E5;display:none;">Karagatan</div>
                    </div>
                </div>
                <div class="nature-pic-card">
                    <div class="nature-pic-img-wrap">
                        <img src="pictures/colors/blue2.jpg" alt="Sky" loading="lazy">
                    </div>
                    <div class="nature-pic-label" style="background:#1E88E511;border-top:2px solid #1E88E522;">
                        <div class="pic-name en-only" style="color:#1E88E5;">Sky</div>
                        <div class="pic-name tl-only" style="color:#1E88E5;display:none;">Langit</div>
                    </div>
                </div>
                <div class="nature-pic-card">
                    <div class="nature-pic-img-wrap">
                        <img src="pictures/colors/blue3.jpeg" alt="Blueberry" loading="lazy">
                    </div>
                    <div class="nature-pic-label" style="background:#1E88E511;border-top:2px solid #1E88E522;">
                        <div class="pic-name en-only" style="color:#1E88E5;">Blueberry</div>
                        <div class="pic-name tl-only" style="color:#1E88E5;display:none;">Blueberry</div>
                    </div>
                </div>
                <div class="nature-pic-card">
                    <div class="nature-pic-img-wrap">
                        <img src="pictures/colors/blue4.jpg" alt="Butterfly" loading="lazy">
                    </div>
                    <div class="nature-pic-label" style="background:#1E88E511;border-top:2px solid #1E88E522;">
                        <div class="pic-name en-only" style="color:#1E88E5;">Butterfly</div>
                        <div class="pic-name tl-only" style="color:#1E88E5;display:none;">Mariposa</div>
                    </div>
                </div>
            </div>
        </div>
        <hr style="border:none;border-top:2px dashed #f0f0f0;margin:28px 0;">

        <!-- ═══ YELLOW ═══ -->
        <div class="nature-color-block">
            <div class="nature-color-header">
                <div class="nature-color-swatch" style="background:#FDD835;"></div>
                <div>
                    <div class="nature-color-name-en" style="color:#888;">Yellow</div>
                    <div class="nature-color-name-tl"  style="color:#888;">Dilaw</div>
                </div>
            </div>
            <div class="nature-pics-grid">
                <div class="nature-pic-card">
                    <div class="nature-pic-img-wrap">
                        <img src="pictures/colors/yellow1.jpg" alt="Sun" loading="lazy">
                    </div>
                    <div class="nature-pic-label" style="background:#FDD83511;border-top:2px solid #FDD83522;">
                        <div class="pic-name en-only" style="color:#888;">Sun</div>
                        <div class="pic-name tl-only" style="color:#888;display:none;">Araw</div>
                    </div>
                </div>
                <div class="nature-pic-card">
                    <div class="nature-pic-img-wrap">
                        <img src="pictures/colors/yellow2.jpg" alt="Banana" loading="lazy">
                    </div>
                    <div class="nature-pic-label" style="background:#FDD83511;border-top:2px solid #FDD83522;">
                        <div class="pic-name en-only" style="color:#888;">Banana</div>
                        <div class="pic-name tl-only" style="color:#888;display:none;">Saging</div>
                    </div>
                </div>
                <div class="nature-pic-card">
                    <div class="nature-pic-img-wrap">
                        <img src="pictures/colors/yellow3.jpg" alt="Sunflower" loading="lazy">
                    </div>
                    <div class="nature-pic-label" style="background:#FDD83511;border-top:2px solid #FDD83522;">
                        <div class="pic-name en-only" style="color:#888;">Sunflower</div>
                        <div class="pic-name tl-only" style="color:#888;display:none;">Sunflower</div>
                    </div>
                </div>
                <div class="nature-pic-card">
                    <div class="nature-pic-img-wrap">
                        <img src="pictures/colors/yellow4.jpeg" alt="Lemon" loading="lazy">
                    </div>
                    <div class="nature-pic-label" style="background:#FDD83511;border-top:2px solid #FDD83522;">
                        <div class="pic-name en-only" style="color:#888;">Lemon</div>
                        <div class="pic-name tl-only" style="color:#888;display:none;">Limon</div>
                    </div>
                </div>
            </div>
        </div>
        <hr style="border:none;border-top:2px dashed #f0f0f0;margin:28px 0;">

        <!-- ═══ GREEN ═══ -->
        <div class="nature-color-block">
            <div class="nature-color-header">
                <div class="nature-color-swatch" style="background:#43A047;"></div>
                <div>
                    <div class="nature-color-name-en" style="color:#43A047;">Green</div>
                    <div class="nature-color-name-tl"  style="color:#43A047;">Berde</div>
                </div>
            </div>
            <div class="nature-pics-grid">
                <div class="nature-pic-card">
                    <div class="nature-pic-img-wrap">
                        <img src="pictures/colors/green1.png" alt="Leaf" loading="lazy">
                    </div>
                    <div class="nature-pic-label" style="background:#43A04711;border-top:2px solid #43A04722;">
                        <div class="pic-name en-only" style="color:#43A047;">Leaf</div>
                        <div class="pic-name tl-only" style="color:#43A047;display:none;">Dahon</div>
                    </div>
                </div>
                <div class="nature-pic-card">
                    <div class="nature-pic-img-wrap">
                        <img src="pictures/colors/green2.jpg" alt="Frog" loading="lazy">
                    </div>
                    <div class="nature-pic-label" style="background:#43A04711;border-top:2px solid #43A04722;">
                        <div class="pic-name en-only" style="color:#43A047;">Frog</div>
                        <div class="pic-name tl-only" style="color:#43A047;display:none;">Palaka</div>
                    </div>
                </div>
                <div class="nature-pic-card">
                    <div class="nature-pic-img-wrap">
                        <img src="pictures/colors/green3.jpg" alt="Broccoli" loading="lazy">
                    </div>
                    <div class="nature-pic-label" style="background:#43A04711;border-top:2px solid #43A04722;">
                        <div class="pic-name en-only" style="color:#43A047;">Broccoli</div>
                        <div class="pic-name tl-only" style="color:#43A047;display:none;">Broccoli</div>
                    </div>
                </div>
                <div class="nature-pic-card">
                    <div class="nature-pic-img-wrap">
                        <img src="pictures/colors/green4.jpg" alt="Tree" loading="lazy">
                    </div>
                    <div class="nature-pic-label" style="background:#43A04711;border-top:2px solid #43A04722;">
                        <div class="pic-name en-only" style="color:#43A047;">Tree</div>
                        <div class="pic-name tl-only" style="color:#43A047;display:none;">Puno</div>
                    </div>
                </div>
            </div>
        </div>
        <hr style="border:none;border-top:2px dashed #f0f0f0;margin:28px 0;">

        <!-- ═══ ORANGE ═══ -->
        <div class="nature-color-block">
            <div class="nature-color-header">
                <div class="nature-color-swatch" style="background:#FB8C00;"></div>
                <div>
                    <div class="nature-color-name-en" style="color:#FB8C00;">Orange</div>
                    <div class="nature-color-name-tl"  style="color:#FB8C00;">Kahel</div>
                </div>
            </div>
            <div class="nature-pics-grid">
                <div class="nature-pic-card">
                    <div class="nature-pic-img-wrap">
                        <img src="pictures/colors/orange1.jpg" alt="Orange Fruit" loading="lazy">
                    </div>
                    <div class="nature-pic-label" style="background:#FB8C0011;border-top:2px solid #FB8C0022;">
                        <div class="pic-name en-only" style="color:#FB8C00;">Orange Fruit</div>
                        <div class="pic-name tl-only" style="color:#FB8C00;display:none;">Kahel</div>
                    </div>
                </div>
                <div class="nature-pic-card">
                    <div class="nature-pic-img-wrap">
                        <img src="pictures/colors/orange2.jpg" alt="Pumpkin" loading="lazy">
                    </div>
                    <div class="nature-pic-label" style="background:#FB8C0011;border-top:2px solid #FB8C0022;">
                        <div class="pic-name en-only" style="color:#FB8C00;">Pumpkin</div>
                        <div class="pic-name tl-only" style="color:#FB8C00;display:none;">Kalabasa</div>
                    </div>
                </div>
                <div class="nature-pic-card">
                    <div class="nature-pic-img-wrap">
                        <img src="pictures/colors/orange3.jpg" alt="Fox" loading="lazy">
                    </div>
                    <div class="nature-pic-label" style="background:#FB8C0011;border-top:2px solid #FB8C0022;">
                        <div class="pic-name en-only" style="color:#FB8C00;">Fox</div>
                        <div class="pic-name tl-only" style="color:#FB8C00;display:none;">Lobo</div>
                    </div>
                </div>
                <div class="nature-pic-card">
                    <div class="nature-pic-img-wrap">
                        <img src="pictures/colors/orange4.jpg" alt="Carrot" loading="lazy">
                    </div>
                    <div class="nature-pic-label" style="background:#FB8C0011;border-top:2px solid #FB8C0022;">
                        <div class="pic-name en-only" style="color:#FB8C00;">Carrot</div>
                        <div class="pic-name tl-only" style="color:#FB8C00;display:none;">Karot</div>
                    </div>
                </div>
            </div>
        </div>
        <hr style="border:none;border-top:2px dashed #f0f0f0;margin:28px 0;">

        <!-- ═══ PURPLE ═══ -->
        <div class="nature-color-block">
            <div class="nature-color-header">
                <div class="nature-color-swatch" style="background:#8E24AA;"></div>
                <div>
                    <div class="nature-color-name-en" style="color:#8E24AA;">Purple</div>
                    <div class="nature-color-name-tl"  style="color:#8E24AA;">Lila</div>
                </div>
            </div>
            <div class="nature-pics-grid">
                <div class="nature-pic-card">
                    <div class="nature-pic-img-wrap">
                        <img src="pictures/colors/purple1.jpg" alt="Grapes" loading="lazy">
                    </div>
                    <div class="nature-pic-label" style="background:#8E24AA11;border-top:2px solid #8E24AA22;">
                        <div class="pic-name en-only" style="color:#8E24AA;">Grapes</div>
                        <div class="pic-name tl-only" style="color:#8E24AA;display:none;">Ubas</div>
                    </div>
                </div>
                <div class="nature-pic-card">
                    <div class="nature-pic-img-wrap">
                        <img src="pictures/colors/purple2.jpg" alt="Lavender" loading="lazy">
                    </div>
                    <div class="nature-pic-label" style="background:#8E24AA11;border-top:2px solid #8E24AA22;">
                        <div class="pic-name en-only" style="color:#8E24AA;">Lavender</div>
                        <div class="pic-name tl-only" style="color:#8E24AA;display:none;">Lavender</div>
                    </div>
                </div>
                <div class="nature-pic-card">
                    <div class="nature-pic-img-wrap">
                        <img src="pictures/colors/purple3.jpg" alt="Eggplant" loading="lazy">
                    </div>
                    <div class="nature-pic-label" style="background:#8E24AA11;border-top:2px solid #8E24AA22;">
                        <div class="pic-name en-only" style="color:#8E24AA;">Eggplant</div>
                        <div class="pic-name tl-only" style="color:#8E24AA;display:none;">Talong</div>
                    </div>
                </div>
                <div class="nature-pic-card">
                    <div class="nature-pic-img-wrap">
                        <img src="pictures/colors/purple4.jpg" alt="Violet Flower" loading="lazy">
                    </div>
                    <div class="nature-pic-label" style="background:#8E24AA11;border-top:2px solid #8E24AA22;">
                        <div class="pic-name en-only" style="color:#8E24AA;">Shoes</div>
                        <div class="pic-name tl-only" style="color:#8E24AA;display:none;">Sapatos</div>
                    </div>
                </div>
            </div>
        </div>
        <hr style="border:none;border-top:2px dashed #f0f0f0;margin:28px 0;">

        <!-- ═══ PINK ═══ -->
        <div class="nature-color-block">
            <div class="nature-color-header">
                <div class="nature-color-swatch" style="background:#E91E8C;"></div>
                <div>
                    <div class="nature-color-name-en" style="color:#888;">Pink</div>
                    <div class="nature-color-name-tl"  style="color:#888;">Rosas</div>
                </div>
            </div>
            <div class="nature-pics-grid">
                <div class="nature-pic-card">
                    <div class="nature-pic-img-wrap">
                        <img src="pictures/colors/pink1.jpg" alt="Hair Tie" loading="lazy">
                    </div>
                    <div class="nature-pic-label" style="background:#E91E8C11;border-top:2px solid #E91E8C22;">
                        <div class="pic-name en-only" style="color:#888;">Hair Tie</div>
                        <div class="pic-name tl-only" style="color:#888;display:none;">Pamuyod</div>
                    </div>
                </div>
                <div class="nature-pic-card">
                    <div class="nature-pic-img-wrap">
                        <img src="pictures/colors/pink2.jpg" alt="Flamingo" loading="lazy">
                    </div>
                    <div class="nature-pic-label" style="background:#E91E8C11;border-top:2px solid #E91E8C22;">
                        <div class="pic-name en-only" style="color:#888;">Flamingo</div>
                        <div class="pic-name tl-only" style="color:#888;display:none;">Flamingo</div>
                    </div>
                </div>
                <div class="nature-pic-card">
                    <div class="nature-pic-img-wrap">
                        <img src="pictures/colors/pink3.jpg" alt="Pig" loading="lazy">
                    </div>
                    <div class="nature-pic-label" style="background:#E91E8C11;border-top:2px solid #E91E8C22;">
                        <div class="pic-name en-only" style="color:#888;">Pig</div>
                        <div class="pic-name tl-only" style="color:#888;display:none;">Baboy</div>
                    </div>
                </div>
                <div class="nature-pic-card">
                    <div class="nature-pic-img-wrap">
                        <img src="pictures/colors/pink4.jpg" alt="Cherry Blossom" loading="lazy">
                    </div>
                    <div class="nature-pic-label" style="background:#E91E8C11;border-top:2px solid #E91E8C22;">
                        <div class="pic-name en-only" style="color:#888;">Cherry Blossom</div>
                        <div class="pic-name tl-only" style="color:#888;display:none;">Sakura</div>
                    </div>
                </div>
            </div>
        </div>
        <hr style="border:none;border-top:2px dashed #f0f0f0;margin:28px 0;">

        <!-- ═══ BROWN ═══ -->
        <div class="nature-color-block">
            <div class="nature-color-header">
                <div class="nature-color-swatch" style="background:#6D4C41;"></div>
                <div>
                    <div class="nature-color-name-en" style="color:#6D4C41;">Brown</div>
                    <div class="nature-color-name-tl"  style="color:#6D4C41;">Kayumanggi</div>
                </div>
            </div>
            <div class="nature-pics-grid">
                <div class="nature-pic-card">
                    <div class="nature-pic-img-wrap">
                        <img src="pictures/colors/brown1.jpg" alt="Chocolate" loading="lazy">
                    </div>
                    <div class="nature-pic-label" style="background:#6D4C4111;border-top:2px solid #6D4C4122;">
                        <div class="pic-name en-only" style="color:#6D4C41;">Chocolate</div>
                        <div class="pic-name tl-only" style="color:#6D4C41;display:none;">Tsokolate</div>
                    </div>
                </div>
                <div class="nature-pic-card">
                    <div class="nature-pic-img-wrap">
                        <img src="pictures/colors/brown2.jpg" alt="Bear" loading="lazy">
                    </div>
                    <div class="nature-pic-label" style="background:#6D4C4111;border-top:2px solid #6D4C4122;">
                        <div class="pic-name en-only" style="color:#6D4C41;">Bear</div>
                        <div class="pic-name tl-only" style="color:#6D4C41;display:none;">Oso</div>
                    </div>
                </div>
                <div class="nature-pic-card">
                    <div class="nature-pic-img-wrap">
                        <img src="pictures/colors/brown3.jpg" alt="Wood" loading="lazy">
                    </div>
                    <div class="nature-pic-label" style="background:#6D4C4111;border-top:2px solid #6D4C4122;">
                        <div class="pic-name en-only" style="color:#6D4C41;">Wood</div>
                        <div class="pic-name tl-only" style="color:#6D4C41;display:none;">Kahoy</div>
                    </div>
                </div>
                <div class="nature-pic-card">
                    <div class="nature-pic-img-wrap">
                        <img src="pictures/colors/brown4.jpg" alt="Coffee" loading="lazy">
                    </div>
                    <div class="nature-pic-label" style="background:#6D4C4111;border-top:2px solid #6D4C4122;">
                        <div class="pic-name en-only" style="color:#6D4C41;">Coffee</div>
                        <div class="pic-name tl-only" style="color:#6D4C41;display:none;">Kape</div>
                    </div>
                </div>
            </div>
        </div>
        <hr style="border:none;border-top:2px dashed #f0f0f0;margin:28px 0;">

        <!-- ═══ BLACK ═══ -->
        <div class="nature-color-block">
            <div class="nature-color-header">
                <div class="nature-color-swatch" style="background:#212121;"></div>
                <div>
                    <div class="nature-color-name-en" style="color:#212121;">Black</div>
                    <div class="nature-color-name-tl"  style="color:#212121;">Itim</div>
                </div>
            </div>
            <div class="nature-pics-grid">
                <div class="nature-pic-card">
                    <div class="nature-pic-img-wrap">
                        <img src="pictures/colors/black1.jpg" alt="Night Sky" loading="lazy">
                    </div>
                    <div class="nature-pic-label" style="background:#21212111;border-top:2px solid #21212122;">
                        <div class="pic-name en-only" style="color:#212121;">Night Sky</div>
                        <div class="pic-name tl-only" style="color:#212121;display:none;">Kalangitan sa Gabi</div>
                    </div>
                </div>
                <div class="nature-pic-card">
                    <div class="nature-pic-img-wrap">
                        <img src="pictures/colors/black2.jpg" alt="Crow" loading="lazy">
                    </div>
                    <div class="nature-pic-label" style="background:#21212111;border-top:2px solid #21212122;">
                        <div class="pic-name en-only" style="color:#212121;">Crow</div>
                        <div class="pic-name tl-only" style="color:#212121;display:none;">Uwak</div>
                    </div>
                </div>
                <div class="nature-pic-card">
                    <div class="nature-pic-img-wrap">
                        <img src="pictures/colors/black3.jpg" alt="Charcoal" loading="lazy">
                    </div>
                    <div class="nature-pic-label" style="background:#21212111;border-top:2px solid #21212122;">
                        <div class="pic-name en-only" style="color:#212121;">Charcoal</div>
                        <div class="pic-name tl-only" style="color:#212121;display:none;">Uling</div>
                    </div>
                </div>
                <div class="nature-pic-card">
                    <div class="nature-pic-img-wrap">
                        <img src="pictures/colors/black4.jpg" alt="Black Cat" loading="lazy">
                    </div>
                    <div class="nature-pic-label" style="background:#21212111;border-top:2px solid #21212122;">
                        <div class="pic-name en-only" style="color:#212121;">Cat</div>
                        <div class="pic-name tl-only" style="color:#212121;display:none;">Pusa</div>
                    </div>
                </div>
            </div>
        </div>
        <hr style="border:none;border-top:2px dashed #f0f0f0;margin:28px 0;">

        <!-- ═══ WHITE ═══ -->
        <div class="nature-color-block">
            <div class="nature-color-header">
                <div class="nature-color-swatch" style="background:#ECEFF1;border:3px solid #ddd;"></div>
                <div>
                    <div class="nature-color-name-en" style="color:#888;">White</div>
                    <div class="nature-color-name-tl"  style="color:#888;">Puti</div>
                </div>
            </div>
            <div class="nature-pics-grid">
                <div class="nature-pic-card">
                    <div class="nature-pic-img-wrap">
                        <img src="pictures/colors/white1.jpg" alt="Clouds" loading="lazy">
                    </div>
                    <div class="nature-pic-label" style="background:#ECEFF111;border-top:2px solid #ECEFF122;">
                        <div class="pic-name en-only" style="color:#888;">Clouds</div>
                        <div class="pic-name tl-only" style="color:#888;display:none;">Ulap</div>
                    </div>
                </div>
                <div class="nature-pic-card">
                    <div class="nature-pic-img-wrap">
                        <img src="pictures/colors/white2.jpg" alt="Dove" loading="lazy">
                    </div>
                    <div class="nature-pic-label" style="background:#ECEFF111;border-top:2px solid #ECEFF122;">
                        <div class="pic-name en-only" style="color:#888;">Dove</div>
                        <div class="pic-name tl-only" style="color:#888;display:none;">Kalapati</div>
                    </div>
                </div>
                <div class="nature-pic-card">
                    <div class="nature-pic-img-wrap">
                        <img src="pictures/colors/white3.jpg" alt="Milk" loading="lazy">
                    </div>
                    <div class="nature-pic-label" style="background:#ECEFF111;border-top:2px solid #ECEFF122;">
                        <div class="pic-name en-only" style="color:#888;">Milk</div>
                        <div class="pic-name tl-only" style="color:#888;display:none;">Gatas</div>
                    </div>
                </div>
                <div class="nature-pic-card">
                    <div class="nature-pic-img-wrap">
                        <img src="pictures/colors/white4.jpg" alt="Snow" loading="lazy">
                    </div>
                    <div class="nature-pic-label" style="background:#ECEFF111;border-top:2px solid #ECEFF122;">
                        <div class="pic-name en-only" style="color:#888;">Snow</div>
                        <div class="pic-name tl-only" style="color:#888;display:none;">Niyebe</div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- ═══ SECTION 4 — WARM & COOL ═══ -->
    <div class="lesson-section">
        <div class="lesson-guide" style="margin-bottom:24px;box-shadow:none;border:none;padding:0">
            <div class="guide-icon" style="background:linear-gradient(135deg,#fb923c,#60a5fa)"><i class="fas fa-temperature-half" style="color:#fff;font-size:1.3rem"></i></div>
            <div>
                <div class="guide-text-title" id="wcTitle">Warm and Cool Colors</div>
                <div class="guide-text-sub"   id="wcSub">Some colors feel warm, some feel cool!</div>
            </div>
        </div>
        <div class="wc-grid">
            <div class="wc-card" style="background:#fff8f5;">
                <div class="wc-title" style="color:#BF360C;" id="wcWarmTitle"><i class="fas fa-sun" style="color:#e65100"></i> Warm Colors — Mainit na Kulay</div>
                <div class="wc-dots">
                    <div class="wc-dot" style="background:#E53935;"></div>
                    <div class="wc-dot" style="background:#FB8C00;"></div>
                    <div class="wc-dot" style="background:#FDD835;"></div>
                    <div class="wc-dot" style="background:#E91E8C;"></div>
                </div>
                <div class="wc-desc" style="color:#888;" id="wcWarmDesc">These colors make us think of the <strong>sun, fire, and warmth</strong>. They feel energetic and happy!</div>
                <div class="wc-examples" id="wcWarmEx">Red · Orange · Yellow · Pink</div>
            </div>
            <div class="wc-card" style="background:#f5f9ff;">
                <div class="wc-title" style="color:#1565C0;" id="wcCoolTitle"><i class="fas fa-snowflake" style="color:#1565c0"></i> Cool Colors — Malamig na Kulay</div>
                <div class="wc-dots">
                    <div class="wc-dot" style="background:#1E88E5;"></div>
                    <div class="wc-dot" style="background:#43A047;"></div>
                    <div class="wc-dot" style="background:#8E24AA;"></div>
                </div>
                <div class="wc-desc" style="color:#888;" id="wcCoolDesc">These colors make us think of the <strong>ocean, sky, and shade</strong>. They feel calm and peaceful!</div>
                <div class="wc-examples" id="wcCoolEx">Blue · Green · Purple</div>
            </div>
        </div>
    </div>

    <!-- ═══ SECTION 5 — PH FLAG ═══ -->
    <div class="lesson-section">
        <div class="lesson-guide" style="margin-bottom:20px;box-shadow:none;border:none;padding:0">
            <div class="guide-icon" style="background:linear-gradient(135deg,#1e88e5,#e53935)"><i class="fas fa-flag" style="color:#fff;font-size:1.3rem"></i></div>
            <div>
                <div class="guide-text-title" id="flagTitle">Colors in the Philippine Flag</div>
                <div class="guide-text-sub"   id="flagSub">The Philippine flag uses the 3 primary colors — tap each part!</div>
            </div>
        </div>
        <div class="flag-wrap">
            <div class="flag-svg-wrap">
                <svg width="100%" viewBox="0 0 480 240" style="border-radius:10px;box-shadow:0 6px 24px rgba(0,0,0,.12);display:block;">
                    <rect x="0" y="0"   width="480" height="120" fill="#1E88E5" style="cursor:pointer" onclick="showFlagInfo('blue')" />
                    <rect x="0" y="120" width="480" height="120" fill="#E53935" style="cursor:pointer" onclick="showFlagInfo('red')" />
                    <polygon points="0,0 200,120 0,240" fill="#FFFFFF" style="cursor:pointer" onclick="showFlagInfo('white')" />
                    <g style="cursor:pointer" onclick="showFlagInfo('yellow')">
                        <circle cx="80" cy="120" r="28" fill="#FDD835"/>
                        <line x1="80" y1="84"  x2="80" y2="74"  stroke="#FDD835" stroke-width="5" stroke-linecap="round"/>
                        <line x1="80" y1="156" x2="80" y2="166" stroke="#FDD835" stroke-width="5" stroke-linecap="round"/>
                        <line x1="44"  y1="120" x2="34"  y2="120" stroke="#FDD835" stroke-width="5" stroke-linecap="round"/>
                        <line x1="116" y1="120" x2="126" y2="120" stroke="#FDD835" stroke-width="5" stroke-linecap="round"/>
                        <line x1="54.5" y1="94.5"  x2="47.4" y2="87.4"  stroke="#FDD835" stroke-width="4" stroke-linecap="round"/>
                        <line x1="105.5" y1="145.5" x2="112.6" y2="152.6" stroke="#FDD835" stroke-width="4" stroke-linecap="round"/>
                        <line x1="54.5" y1="145.5" x2="47.4" y2="152.6" stroke="#FDD835" stroke-width="4" stroke-linecap="round"/>
                        <line x1="105.5" y1="94.5"  x2="112.6" y2="87.4"  stroke="#FDD835" stroke-width="4" stroke-linecap="round"/>
                    </g>
                    <g fill="#FDD835" font-size="24" style="cursor:pointer" onclick="showFlagInfo('yellow')">
                        <text x="18"  y="46"  text-anchor="middle">★</text>
                        <text x="18"  y="204" text-anchor="middle">★</text>
                        <text x="168" y="128" text-anchor="middle">★</text>
                    </g>
                    <text x="360" y="24" text-anchor="middle" font-family="Nunito,sans-serif" font-size="11" font-weight="700" fill="rgba(255,255,255,.7)">Tap any part!</text>
                </svg>
            </div>
            <div class="flag-hotspots">
                <div class="flag-chip" onclick="showFlagInfo('blue')"><div class="fc-dot" style="background:#1E88E5"></div><span id="fcBlue">Blue — Asul</span></div>
                <div class="flag-chip" onclick="showFlagInfo('red')"><div class="fc-dot" style="background:#E53935"></div><span id="fcRed">Red — Pula</span></div>
                <div class="flag-chip" onclick="showFlagInfo('white')"><div class="fc-dot" style="background:#ddd;border:1px solid #ccc"></div><span id="fcWhite">White — Puti</span></div>
                <div class="flag-chip" onclick="showFlagInfo('yellow')"><div class="fc-dot" style="background:#FDD835"></div><span id="fcYellow">Yellow — Dilaw</span></div>
            </div>
            <div id="flagInfo">Tap a part of the flag to learn about it!</div>
        </div>
    </div>

    <!-- ═══ SECTION 6 — COLOR MIXING LAB ═══ -->
    <div class="lesson-section">
        <div class="lesson-guide" style="margin-bottom:24px;box-shadow:none;border:none;padding:0">
            <div class="guide-icon" style="background:linear-gradient(135deg,#f87171,#f59e0b)"><i class="fas fa-flask" style="color:#fff;font-size:1.3rem"></i></div>
            <div>
                <div class="guide-text-title" id="mixTitle">Color Mixing Lab</div>
                <div class="guide-text-sub"   id="mixSub">Pick two colors and see what they make!</div>
            </div>
        </div>
        <div class="mix-lab">
            <div class="mix-picker-wrap">
                <div class="mix-label" id="mixLabel1">Pick Color 1</div>
                <div class="mix-swatches" id="swatches1"></div>
            </div>
            <div class="mix-plus"><i class="fas fa-plus"></i></div>
            <div class="mix-picker-wrap">
                <div class="mix-label" id="mixLabel2">Pick Color 2</div>
                <div class="mix-swatches" id="swatches2"></div>
            </div>
            <div class="mix-equals"><i class="fas fa-equals"></i></div>
            <div class="mix-result-wrap">
                <div class="mix-result-circle" id="mixResult"></div>
                <div class="mix-result-name"   id="mixResultName">?</div>
            </div>
        </div>
    </div>

    <!-- ═══ SECTION 7 — VIDEO ═══ -->
    <div class="song-section">
        <div class="section-header-card">
            <div class="sec-icon-box" style="background:linear-gradient(135deg,#5b21b6,#7c3aed);">
                <i class="fas fa-headphones"></i>
            </div>
            <div style="flex:1;min-width:0;">
                <div class="sec-title"    id="colorSongTitle">Watch Color Songs!</div>
                <div class="sec-subtitle" id="colorSongSub">Sing and learn about colors — tap a video to play!</div>
            </div>
            <div class="sec-line"></div>
        </div>
        <div class="song-video-card">
            <div class="song-vtabs" id="colorVTabs"></div>
            <div class="song-yt-wrap">
                <div class="song-yt-box">
                    <div class="song-yt-placeholder" id="colorYtPlaceholder" onclick="loadColorVideo()">
                        <div class="song-play-btn"><i class="fas fa-play"></i></div>
                        <div class="song-ph-title" id="colorPhTitle">Colors Song for Kids</div>
                        <div class="song-ph-sub"   id="colorPhSub">Tap to play the video</div>
                    </div>
                    <iframe id="colorYtFrame" src=""
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                        allowfullscreen></iframe>
                </div>
            </div>
        </div>
    </div>

</div><!-- /.container -->
<?php include 'take-quiz.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
/* ═══════════════════════════════════════════
   DATA
═══════════════════════════════════════════ */
const DATA = {
    en: <?php
        $out=[];
        foreach($colors as $name=>$d)
            $out[]=['name'=>$name,'desc'=>$d[0],'color'=>$d[1],'category'=>$d[2],'local'=>$d[3],'isLight'=>in_array($name,['Yellow','White','Pink'])];
        echo json_encode($out,JSON_UNESCAPED_UNICODE);
    ?>,
    tl: <?php
        $out2=[];
        foreach($colors_tl as $name=>$d)
            $out2[]=['name'=>$name,'desc'=>$d[0],'color'=>$d[1],'category'=>$d[2],'local'=>$d[3],'isLight'=>in_array($name,['Dilaw','Puti','Rosas'])];
        echo json_encode($out2,JSON_UNESCAPED_UNICODE);
    ?>
};

const CAT_EN={All:'All',Primary:'Primary',Secondary:'Secondary',Tertiary:'Tertiary'};
const CAT_TL={All:'Lahat',Primary:'Pangunahin',Secondary:'Pangalawa',Tertiary:'Pangatlo'};

const UI={
    en:{
        title:'Learn the Colors!',badge:'English — 10 Colors',
        all:'All',primary:'Primary',secondary:'Secondary',tertiary:'Tertiary',
        guideTitle:'Learn the Colors!',guideSub:'Tap any color card to hear its name!',
        ttsNotice:'Tap any color card to hear its name spoken aloud!',
        primaryTitle:'Primary Colors',primarySub:'These 3 colors cannot be made by mixing other colors!',
        primaryNote:'These are called <strong>Primary Colors</strong> because they are the <em>parent colors</em> — you cannot make them by mixing other colors. All other colors come from these three!',
        pcRedName:'Red',pcRedLocal:'(Pula)',pcRedDesc:'Bold and bright — like apples and fire trucks!',pcRedBadge:'Primary Color',
        pcYellowName:'Yellow',pcYellowLocal:'(Dilaw)',pcYellowDesc:'Sunny and happy — like the sun and bananas!',pcYellowBadge:'Primary Color',
        pcBlueName:'Blue',pcBlueLocal:'(Asul)',pcBlueDesc:'Calm and cool — like the sky and the ocean!',pcBlueBadge:'Primary Color',
        mixLessonTitle:"Secondary Colors — Let's Mix!",mixLessonSub:'Mix 2 primary colors together to make a secondary color!',
        mixNote:'Orange, Green, and Purple are called <strong>Secondary Colors</strong> — they are made by mixing two primary colors together!',
        mixOrangeName:'Orange',mixOrangeLocal:'Kahel',mixOrangeFormula:'Red + Yellow',
        mixGreenName:'Green',mixGreenLocal:'Berde',mixGreenFormula:'Yellow + Blue',
        mixPurpleName:'Purple',mixPurpleLocal:'Lila',mixPurpleFormula:'Blue + Red',
        natureTitle:'Colors in the Environment',natureSub:'Look at real pictures — colors are everywhere around us!',
        wcTitle:'Warm and Cool Colors',wcSub:'Some colors feel warm, some feel cool!',
        wcWarmTitle:'<i class="fas fa-sun" style="color:#e65100"></i> Warm Colors — Mainit na Kulay',
        wcWarmDesc:'These colors make us think of the <strong>sun, fire, and warmth</strong>. They feel energetic and happy!',
        wcWarmEx:'Red · Orange · Yellow · Pink',
        wcCoolTitle:'<i class="fas fa-snowflake" style="color:#1565c0"></i> Cool Colors — Malamig na Kulay',
        wcCoolDesc:'These colors make us think of the <strong>ocean, sky, and shade</strong>. They feel calm and peaceful!',
        wcCoolEx:'Blue · Green · Purple',
        flagTitle:'Colors in the Philippine Flag',flagSub:'The Philippine flag uses the 3 primary colors — tap each part!',
        fcBlue:'Blue — Asul',fcRed:'Red — Pula',fcWhite:'White — Puti',fcYellow:'Yellow — Dilaw',
        mixTitle:'Color Mixing Lab',mixSub:'Pick two colors and see what they make!',
        mixLabel1:'Pick Color 1',mixLabel2:'Pick Color 2',
        songTitle:'Watch Color Songs!',songSub:'Sing and learn about colors — tap a video to play!',
        songPhSub:'Tap to play the video',
    },
    tl:{
        title:'Alamin ang mga Kulay!',badge:'Filipino — 10 Kulay',
        all:'Lahat',primary:'Pangunahin',secondary:'Pangalawa',tertiary:'Pangatlo',
        guideTitle:'Alamin ang mga Kulay!',guideSub:'I-tap ang kulay para marinig ang pangalan nito!',
        ttsNotice:'I-tap ang anumang kulay para marinig ang tamang bigkas ng pangalan nito!',
        primaryTitle:'Mga Pangunahing Kulay',primarySub:'Ang 3 kulay na ito ay hindi magagawa sa pamamagitan ng paghahalo!',
        primaryNote:'Ang mga ito ay tinatawag na <strong>Pangunahing Kulay</strong> dahil sila ang <em>nanay na kulay</em> — hindi mo sila magagawa sa pamamagitan ng paghahalo ng ibang kulay. Lahat ng ibang kulay ay nagmumula sa tatlong ito!',
        pcRedName:'Pula',pcRedLocal:'(Red)',pcRedDesc:'Matapang at maliwanag — tulad ng mga mansanas at fire trucks!',pcRedBadge:'Pangunahing Kulay',
        pcYellowName:'Dilaw',pcYellowLocal:'(Yellow)',pcYellowDesc:'Masaya at maliwanag — tulad ng araw at saging!',pcYellowBadge:'Pangunahing Kulay',
        pcBlueName:'Asul',pcBlueLocal:'(Blue)',pcBlueDesc:'Tahimik at malamig — tulad ng langit at karagatan!',pcBlueBadge:'Pangunahing Kulay',
        mixLessonTitle:'Pangalawang Kulay — Haluin Natin!',mixLessonSub:'Ihalo ang 2 pangunahing kulay para makagawa ng pangalawang kulay!',
        mixNote:'Ang Kahel, Berde, at Lila ay tinatawag na <strong>Pangalawang Kulay</strong> — ginawa ang mga ito sa pamamagitan ng paghahalo ng dalawang pangunahing kulay!',
        mixOrangeName:'Kahel',mixOrangeLocal:'Orange',mixOrangeFormula:'Pula + Dilaw',
        mixGreenName:'Berde',mixGreenLocal:'Green',mixGreenFormula:'Dilaw + Asul',
        mixPurpleName:'Lila',mixPurpleLocal:'Purple',mixPurpleFormula:'Asul + Pula',
        natureTitle:'Mga Kulay sa Kapaligiran',natureSub:'Tingnan ang mga tunay na larawan — may mga kulay sa lahat ng dako!',
        wcTitle:'Mainit at Malamig na Kulay',wcSub:'May mga kulay na mainit, may mga malamig!',
        wcWarmTitle:'<i class="fas fa-sun" style="color:#e65100"></i> Mainit na Kulay — Warm Colors',
        wcWarmDesc:'Ang mga kulay na ito ay nagpapaalala sa atin ng <strong>araw, apoy, at init</strong>. Pakiramdam ay masigla at masaya!',
        wcWarmEx:'Pula · Kahel · Dilaw · Rosas',
        wcCoolTitle:'<i class="fas fa-snowflake" style="color:#1565c0"></i> Malamig na Kulay — Cool Colors',
        wcCoolDesc:'Ang mga kulay na ito ay nagpapaalala sa atin ng <strong>karagatan, langit, at lilim</strong>. Pakiramdam ay tahimik at mapayapa!',
        wcCoolEx:'Asul · Berde · Lila',
        flagTitle:'Mga Kulay ng Bandila ng Pilipinas',flagSub:'Gumagamit ang bandilang Pilipino ng 3 pangunahing kulay — i-tap ang bawat bahagi!',
        fcBlue:'Asul — Blue',fcRed:'Pula — Red',fcWhite:'Puti — White',fcYellow:'Dilaw — Yellow',
        mixTitle:'Laboratoryo ng Paghahalo ng Kulay',mixSub:'Pumili ng dalawang kulay at tingnan kung ano ang magiging resulta!',
        mixLabel1:'Pumili ng Kulay 1',mixLabel2:'Pumili ng Kulay 2',
        songTitle:'Panoorin ang mga Awit ng Kulay!',songSub:'Kumanta at matuto tungkol sa mga kulay — i-tap para i-play!',
        songPhSub:'I-tap para i-play ang video',
    }
};

let currentLang='en', activeFilter='All';

/* ── VIDEOS ── */
const COLOR_VIDEOS={
    en:[
        {label:'Colors Song',   icon:'fa-palette',ytId:'ybt4c8Q0ybY',title:'Colors Song for Kids — CoComelon'},
        {label:'Rainbow Colors',icon:'fa-rainbow',ytId:'OoRwSZGEBZo',title:'Rainbow Colors Song — Pinkfong'},
        {label:'Color Mixing',  icon:'fa-flask',  ytId:'yu44JRTIxSQ',title:'Color Mixing for Kids'},
    ],
    tl:[
        {label:'Awit ng Kulay', icon:'fa-palette',ytId:'ybt4c8Q0ybY',title:'Colors Song para sa mga Bata'},
        {label:'Bahaghari',     icon:'fa-rainbow',ytId:'OoRwSZGEBZo',title:'Rainbow Colors Song'},
        {label:'Paghahalo',     icon:'fa-flask',  ytId:'yu44JRTIxSQ',title:'Paghahalo ng Kulay para sa mga Bata'},
    ]
};
let colorVideoIdx=0;

function buildColorTabs(){
    const el=document.getElementById('colorVTabs'); el.innerHTML='';
    COLOR_VIDEOS[currentLang].forEach((v,i)=>{
        const btn=document.createElement('button');
        btn.className='svtab'+(i===colorVideoIdx?' active':'');
        btn.innerHTML=`<i class="fas ${v.icon}"></i> ${v.label}`;
        btn.onclick=()=>selectColorVideo(i);
        el.appendChild(btn);
    });
}
function selectColorVideo(i){
    colorVideoIdx=i;
    document.querySelectorAll('#colorVTabs .svtab').forEach((b,idx)=>b.classList.toggle('active',idx===i));
    document.getElementById('colorPhTitle').textContent=COLOR_VIDEOS[currentLang][i].title;
    document.getElementById('colorYtPlaceholder').classList.remove('gone');
    const f=document.getElementById('colorYtFrame'); f.src=''; f.style.display='none';
}
function loadColorVideo(){
    const v=COLOR_VIDEOS[currentLang][colorVideoIdx];
    const f=document.getElementById('colorYtFrame');
    f.src=`https://www.youtube.com/embed/${v.ytId}?autoplay=1&rel=0&modestbranding=1`;
    f.style.display='block';
    document.getElementById('colorYtPlaceholder').classList.add('gone');
}
function initColorSong(){
    colorVideoIdx=0; buildColorTabs();
    document.getElementById('colorPhTitle').textContent=COLOR_VIDEOS[currentLang][0].title;
    document.getElementById('colorYtPlaceholder').classList.remove('gone');
    const f=document.getElementById('colorYtFrame'); f.src=''; f.style.display='none';
}

/* ── TTS ── */
let ttsVoices=[];
function loadVoices(){ ttsVoices=window.speechSynthesis?window.speechSynthesis.getVoices():[]; }
function getBestVoice(lang){
    if(!ttsVoices.length) loadVoices();
    if(lang==='tl')
        return ttsVoices.find(v=>v.lang==='fil-PH')||ttsVoices.find(v=>v.lang==='tl-PH')||ttsVoices.find(v=>v.lang.startsWith('fil'))||ttsVoices.find(v=>v.lang==='en-US')||ttsVoices.find(v=>v.lang.startsWith('en'))||null;
    return ttsVoices.find(v=>v.lang==='en-US')||ttsVoices.find(v=>v.lang==='en-GB')||ttsVoices.find(v=>v.lang.startsWith('en'))||null;
}
function speakText(text,lang,card){
    if(!window.speechSynthesis) return;
    window.speechSynthesis.cancel();
    const utt=new SpeechSynthesisUtterance(text);
    utt.rate=0.78; utt.pitch=1.15; utt.volume=1;
    const voice=getBestVoice(lang);
    if(voice){utt.voice=voice;utt.lang=voice.lang;}
    else{utt.lang=lang==='tl'?'fil-PH':'en-US';}
    if(card){
        card.classList.add('speaking');
        utt.onend=()=>card.classList.remove('speaking');
        utt.onerror=()=>card.classList.remove('speaking');
    }
    window.speechSynthesis.speak(utt);
}

/* ── GRID ── */
function renderGrid(lang){
    const grid=document.getElementById('colorsGrid');
    grid.style.opacity='0'; grid.style.transform='scale(.96)';
    setTimeout(()=>{
        grid.innerHTML='';
        DATA[lang].forEach((c,i)=>{
            const card=document.createElement('div');
            card.className='color-card';
            card.dataset.category=c.category;
            card.style.cssText=`--card-color:${c.color};animation-delay:${(i*.03).toFixed(2)}s`;
            card.onclick=()=>{
                card.classList.remove('playing'); void card.offsetWidth; card.classList.add('playing');
                card.addEventListener('animationend',()=>card.classList.remove('playing'),{once:true});
                confetti(c.color); speakText(c.name,lang,card);
            };
            card.innerHTML=`
                <div class="color-circle-wrap"><div class="color-circle"></div></div>
                <div class="color-name">${c.name} <span class="speak-icon"><i class="fas fa-volume-up"></i></span></div>
                <div class="color-local">${c.local}</div>
                <div class="color-badge${c.isLight?' dark-text':''}">${c.category}</div>`;
            grid.appendChild(card);
        });
        applyFilter(activeFilter);
        grid.style.opacity='1'; grid.style.transform='scale(1)';
        grid.style.transition='opacity .25s,transform .25s';
    },220);
}

/* ── NATURE LABELS ── */
function updateNatureLabels(lang){
    document.querySelectorAll('.en-only').forEach(el=>el.style.display=lang==='en'?'':'none');
    document.querySelectorAll('.tl-only').forEach(el=>el.style.display=lang==='tl'?'':'none');
}

/* ── FILTER ── */
function applyFilter(key){
    document.querySelectorAll('.color-card').forEach(card=>{
        const c=card.dataset.category;
        card.classList.toggle('hidden',!(key==='All'||c===CAT_EN[key]||c===CAT_TL[key]));
    });
}
function filterColors(key,btn){
    activeFilter=key; applyFilter(key);
    document.querySelectorAll('.filter-btn').forEach(b=>b.classList.remove('active'));
    btn.classList.add('active');
    buildSwatches(key);
}

/* ── LANG SWITCH ── */
function setLang(lang){
    if(lang===currentLang) return;
    currentLang=lang; activeFilter='All';
    document.getElementById('btnEN').classList.toggle('active',lang==='en');
    document.getElementById('btnTL').classList.toggle('active',lang==='tl');
    const u=UI[lang];
    const ids={
        pageTitle:'title',langBadge:'badge',ttsNoticeText:'ttsNotice',
        tabAll:'all',tabPrimary:'primary',tabSecondary:'secondary',tabTertiary:'tertiary',
        guideTitle:'guideTitle',guideSub:'guideSub',
        primaryTitle:'primaryTitle',primarySub:'primarySub',
        'pc-red-name':'pcRedName','pc-red-local':'pcRedLocal','pc-red-desc':'pcRedDesc','pc-red-badge':'pcRedBadge',
        'pc-yellow-name':'pcYellowName','pc-yellow-local':'pcYellowLocal','pc-yellow-desc':'pcYellowDesc','pc-yellow-badge':'pcYellowBadge',
        'pc-blue-name':'pcBlueName','pc-blue-local':'pcBlueLocal','pc-blue-desc':'pcBlueDesc','pc-blue-badge':'pcBlueBadge',
        mixLessonTitle:'mixLessonTitle',mixLessonSub:'mixLessonSub',
        'mix-orange-name':'mixOrangeName','mix-orange-local':'mixOrangeLocal','mix-orange-formula':'mixOrangeFormula',
        'mix-green-name':'mixGreenName','mix-green-local':'mixGreenLocal','mix-green-formula':'mixGreenFormula',
        'mix-purple-name':'mixPurpleName','mix-purple-local':'mixPurpleLocal','mix-purple-formula':'mixPurpleFormula',
        natureTitle:'natureTitle',natureSub:'natureSub',
        wcTitle:'wcTitle',wcSub:'wcSub',
        // wcWarmTitle and wcCoolTitle handled via innerHTML below
        wcWarmEx:'wcWarmEx',
        wcCoolEx:'wcCoolEx',
        flagTitle:'flagTitle',flagSub:'flagSub',
        fcBlue:'fcBlue',fcRed:'fcRed',fcWhite:'fcWhite',fcYellow:'fcYellow',
        mixTitle:'mixTitle',mixSub:'mixSub',mixLabel1:'mixLabel1',mixLabel2:'mixLabel2',
        colorSongTitle:'songTitle',colorSongSub:'songSub',colorPhSub:'songPhSub',
    };
    Object.entries(ids).forEach(([id,key])=>{
        const el=document.getElementById(id);
        if(el) el.textContent=u[key]||'';
    });
    // innerHTML fields
    ['primaryNote','mixNote','wcWarmDesc','wcCoolDesc','wcWarmTitle','wcCoolTitle'].forEach(id=>{
        const k=id==='primaryNote'?'primaryNote':id==='mixNote'?'mixNote':id==='wcWarmDesc'?'wcWarmDesc':id==='wcCoolDesc'?'wcCoolDesc':id==='wcWarmTitle'?'wcWarmTitle':'wcCoolTitle';
        const el=document.getElementById(id); if(el) el.innerHTML=u[k]||'';
    });
    document.getElementById('langBadge').classList.toggle('tl-mode',lang==='tl');
    document.getElementById('flagInfo').textContent=lang==='tl'?'I-tap ang bahagi ng bandila para matuto!':'Tap a part of the flag to learn about it!';
    document.querySelectorAll('.filter-btn').forEach(b=>b.classList.remove('active'));
    document.querySelector('.filter-btn[data-key="All"]').classList.add('active');
    document.getElementById('colorYtFrame').src='';
    document.getElementById('colorYtFrame').style.display='none';
    document.getElementById('colorYtPlaceholder').classList.remove('gone');
    initColorSong();
    renderGrid(lang);
    updateNatureLabels(lang);
    buildSwatches('All');
    if(mixSel1||mixSel2) updateMixResult();
}

/* ── CONFETTI ── */
function confetti(color){
    const cols=[color,'#FFE66D','#FF6B6B','#A29BFE','#4ECDC4','#FD79A8'];
    for(let i=0;i<25;i++){
        const p=document.createElement('div'); p.className='cp';
        p.style.cssText=`left:${Math.random()*100}vw;top:-10px;background:${cols[Math.floor(Math.random()*cols.length)]};border-radius:${Math.random()>.5?'50%':'2px'};width:${5+Math.random()*7}px;height:${5+Math.random()*7}px;animation-duration:${1+Math.random()*1.2}s;animation-delay:${Math.random()*.3}s;`;
        document.body.appendChild(p);
        p.addEventListener('animationend',()=>p.remove());
    }
}

/* ── FLAG ── */
const FLAG_INFO={
    blue:{
        en:{title:'Blue — Asul',text:'The blue stripe stands for <strong>peace, truth, and justice</strong>. Blue is a primary color! It means the Filipino people love peace.',examples:'Peace · Justice · Truth'},
        tl:{title:'Asul — Blue',text:'Ang asul na guhit ay kumakatawan sa <strong>kapayapaan, katotohanan, at katarungan</strong>. Ang asul ay pangunahing kulay!',examples:'Kapayapaan · Katarungan · Katotohanan'},
    },
    red:{
        en:{title:'Red — Pula',text:'The red stripe stands for <strong>courage and bravery</strong>. Red is a primary color! It means Filipinos are brave.',examples:'Bravery · Courage · Strength'},
        tl:{title:'Pula — Red',text:'Ang pulang guhit ay kumakatawan sa <strong>katapangan at lakas-loob</strong>. Ang pula ay pangunahing kulay!',examples:'Katapangan · Lakas-loob · Lakas'},
    },
    white:{
        en:{title:'White — Puti',text:'The white triangle stands for <strong>equality and brotherhood</strong>. All Filipinos are equal and are brothers and sisters!',examples:'Equality · Brotherhood · Unity'},
        tl:{title:'Puti — White',text:'Ang puting tatsulok ay kumakatawan sa <strong>pagkakapantay-pantay at pagkakaisa</strong>.',examples:'Pagkakapantay-pantay · Pagkakapatid · Pagkakaisa'},
    },
    yellow:{
        en:{title:'Yellow — Dilaw (Sun & Stars)',text:'The <strong>golden sun</strong> has 8 rays (for the first 8 provinces that fought for freedom) and 3 stars (Luzon, Visayas, Mindanao).',examples:'Sun (8 rays) · Luzon · Visayas · Mindanao'},
        tl:{title:'Dilaw — Yellow (Araw at Bituin)',text:'Ang <strong>gintong araw</strong> ay may 8 sinag (para sa unang 8 lalawigan) at 3 bituin (Luzon, Visayas, at Mindanao).',examples:'Araw (8 sinag) · Luzon · Visayas · Mindanao'},
    },
};
function showFlagInfo(part){
    const info=FLAG_INFO[part][currentLang];
    const el=document.getElementById('flagInfo');
    const clrs={blue:'#1E88E5',red:'#E53935',white:'#bbb',yellow:'#F9A825'};
    el.style.cssText=`border-left:4px solid ${clrs[part]};background:${clrs[part]}15;border-radius:12px;padding:14px 18px;`;
    el.innerHTML=`<div style="font-family:'Fredoka One',cursive;font-size:1rem;color:${clrs[part]};margin-bottom:6px">${info.title}</div>
        <div style="font-size:.85rem;color:#555;font-weight:700;margin-bottom:6px">${info.text}</div>
        <div style="font-size:.8rem;color:#888;font-weight:600">${info.examples}</div>`;
}

/* ── MIXING LAB ── */
const PRIMARIES=[
    {name:'Red',hex:'#E53935'},{name:'Blue',hex:'#1E88E5'},{name:'Yellow',hex:'#FDD835'},
    {name:'Green',hex:'#43A047'},{name:'Orange',hex:'#FB8C00'},{name:'Purple',hex:'#8E24AA'},
    {name:'Pink',hex:'#E91E8C'},{name:'Brown',hex:'#6D4C41'},{name:'Black',hex:'#212121'},{name:'White',hex:'#ECEFF1'},
];
const MIX_RESULTS={
    'Red+Blue':'Purple','Blue+Red':'Purple','Red+Yellow':'Orange','Yellow+Red':'Orange',
    'Blue+Yellow':'Green','Yellow+Blue':'Green','Red+White':'Pink','White+Red':'Pink',
    'Red+Black':'Dark Red','Black+Red':'Dark Red','Blue+White':'Light Blue','White+Blue':'Light Blue',
    'Yellow+White':'Light Yellow','White+Yellow':'Light Yellow','Red+Green':'Brown','Green+Red':'Brown',
    'Blue+Orange':'Brown','Orange+Blue':'Brown','Yellow+Purple':'Brown','Purple+Yellow':'Brown',
    'Red+Orange':'Red-Orange','Orange+Red':'Red-Orange','Blue+Purple':'Blue-Purple','Purple+Blue':'Blue-Purple',
    'Yellow+Green':'Yellow-Green','Green+Yellow':'Yellow-Green','Red+Purple':'Magenta','Purple+Red':'Magenta',
    'Black+White':'Gray','White+Black':'Gray',
};
const MIX_COLORS={
    'Purple':'#8E24AA','Orange':'#FB8C00','Green':'#43A047','Pink':'#E91E8C',
    'Dark Red':'#7f0000','Light Blue':'#90CAF9','Light Yellow':'#FFF9C4',
    'Brown':'#6D4C41','Red-Orange':'#FF5722','Blue-Purple':'#5C35CC',
    'Yellow-Green':'#8BC34A','Magenta':'#D500F9','Gray':'#9E9E9E',
};
let mixSel1=null,mixSel2=null;

function getFilteredPrimaries(key){
    if(key==='All') return PRIMARIES;
    return PRIMARIES.filter(p=>{const m=DATA['en'].find(d=>d.name===p.name);return m&&m.category===CAT_EN[key];});
}
function buildSwatches(key){
    key=key||'All';
    const filtered=getFilteredPrimaries(key);
    ['swatches1','swatches2'].forEach((id,si)=>{
        const wrap=document.getElementById(id); wrap.innerHTML='';
        filtered.forEach(p=>{
            const d=document.createElement('div');
            d.className='mix-swatch'; d.style.background=p.hex; d.title=p.name;
            d.onclick=()=>selectSwatch(si+1,p,d,id);
            wrap.appendChild(d);
        });
    });
    mixSel1=null; mixSel2=null;
    const res=document.getElementById('mixResult'),nm=document.getElementById('mixResultName');
    res.style.background='#f0f0f0'; res.classList.remove('has-color'); nm.textContent='?'; nm.style.color='#555';
}
function selectSwatch(slot,p,el,wrId){
    document.querySelectorAll(`#${wrId} .mix-swatch`).forEach(s=>s.classList.remove('active'));
    el.classList.add('active');
    if(slot===1) mixSel1=p; else mixSel2=p;
    updateMixResult();
}
function updateMixResult(){
    const res=document.getElementById('mixResult'),nm=document.getElementById('mixResultName');
    if(!mixSel1||!mixSel2){res.style.background='#f0f0f0';nm.textContent='?';res.classList.remove('has-color');return;}
    if(mixSel1.name===mixSel2.name){res.style.background=mixSel1.hex;nm.textContent=mixSel1.name;nm.style.color=mixSel1.hex;res.classList.add('has-color');return;}
    const key=`${mixSel1.name}+${mixSel2.name}`;
    const rName=MIX_RESULTS[key]||'A new color!';
    const rHex=MIX_COLORS[rName]||blendHex(mixSel1.hex,mixSel2.hex);
    res.style.background=rHex;
    nm.textContent=currentLang==='tl'?translateMixResult(rName):rName;
    nm.style.color=rHex; res.classList.add('has-color'); confetti(rHex);
}
function blendHex(a,b){
    const r1=parseInt(a.slice(1,3),16),g1=parseInt(a.slice(3,5),16),b1=parseInt(a.slice(5,7),16);
    const r2=parseInt(b.slice(1,3),16),g2=parseInt(b.slice(3,5),16),b2=parseInt(b.slice(5,7),16);
    return '#'+[Math.round((r1+r2)/2),Math.round((g1+g2)/2),Math.round((b1+b2)/2)].map(v=>v.toString(16).padStart(2,'0')).join('');
}
function translateMixResult(name){
    const t={'Purple':'Lila','Orange':'Kahel','Green':'Berde','Pink':'Rosas','Brown':'Kayumanggi','Gray':'Kulay-abo','Dark Red':'Maitim na Pula','Light Blue':'Maliwanag na Asul','Light Yellow':'Maliwanag na Dilaw','Red-Orange':'Pula-Kahel','Blue-Purple':'Asul-Lila','Yellow-Green':'Dilaw-Berde','Magenta':'Magenta','A new color!':'Bagong kulay!'};
    return t[name]||name;
}

/* ── INIT ── */
if(window.speechSynthesis){
    window.speechSynthesis.getVoices();
    window.speechSynthesis.onvoiceschanged=loadVoices;
}
renderGrid('en');
updateNatureLabels('en');
buildSwatches('All');
initColorSong();
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