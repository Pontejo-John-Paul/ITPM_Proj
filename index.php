<?php
// index.php — Homepage only.
// The login modal loads student-login.php inside an <iframe>.
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-KINDER — Learn & Explore!</title>
    <link href="https://fonts.googleapis.com/css2?family=Fredoka+One&family=Nunito:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --green-dark:  #0d5407;
            --green-mid:   #1a7a0d;
            --green-light: #e8f5e2;
            --green-pale:  #f0f9eb;
            --accent:      #f59e0b;
            --accent2:     #ef4444;
            --white:       #ffffff;
            --cream:       #fafdf7;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }
        html { scroll-behavior: smooth; }
        body {
            font-family: 'Nunito', sans-serif;
            background: var(--cream);
            overflow-x: hidden;
        }

        /* ── NAVBAR ── */
        .navbar {
            position: fixed; top: 0; left: 0; right: 0; z-index: 100;
            background: rgba(255,255,255,0.92);
            backdrop-filter: blur(12px);
            border-bottom: 2px solid var(--green-light);
            padding: 0 40px;
            height: 68px;
            display: flex; align-items: center; justify-content: space-between;
            box-shadow: 0 2px 20px rgba(13,84,7,0.08);
        }
        .nav-brand {
            font-family: 'Fredoka One', cursive;
            font-size: 1.8rem;
            color: var(--green-dark);
            letter-spacing: 1px;
            display: flex; align-items: center; gap: 10px;
            text-decoration: none;
        }
        .nav-brand-dot {
            width: 10px; height: 10px;
            background: var(--accent);
            border-radius: 50%;
            animation: pulse 2s ease-in-out infinite;
        }
        @keyframes pulse { 0%,100%{transform:scale(1);opacity:1} 50%{transform:scale(1.4);opacity:0.7} }

        .nav-links { display: flex; align-items: center; gap: 6px; }
        .nav-link {
            font-family: 'Fredoka One', cursive;
            font-size: 0.92rem;
            color: #666;
            text-decoration: none;
            padding: 8px 16px;
            border-radius: 50px;
            transition: all 0.2s;
        }
        .nav-link:hover { background: var(--green-light); color: var(--green-dark); }

        .nav-btn-login {
            font-family: 'Fredoka One', cursive;
            font-size: 0.92rem;
            background: var(--green-dark);
            color: #fff;
            border: none;
            padding: 10px 22px;
            border-radius: 50px;
            cursor: pointer;
            display: inline-flex; align-items: center; gap: 7px;
            box-shadow: 0 4px 14px rgba(13,84,7,0.25);
            transition: all 0.2s cubic-bezier(.34,1.56,.64,1);
        }
        .nav-btn-login:hover { transform: translateY(-2px) scale(1.04); box-shadow: 0 8px 20px rgba(13,84,7,0.3); }

        /* ── HERO ── */
        .hero {
            min-height: 100vh;
            background: linear-gradient(160deg, #0d5407 0%, #1a7a0d 45%, #2da619 100%);
            display: flex; flex-direction: column;
            align-items: center; justify-content: center;
            padding: 100px 40px 80px;
            position: relative; overflow: hidden;
            text-align: center;
        }
        .blob {
            position: absolute; border-radius: 50%;
            background: rgba(255,255,255,0.05);
            pointer-events: none;
        }
        .blob-1 { width:500px; height:500px; top:-150px; right:-100px; }
        .blob-2 { width:300px; height:300px; bottom:-80px; left:-60px; }
        .blob-3 { width:200px; height:200px; top:40%; left:10%; animation: floatBlob 6s ease-in-out infinite; }
        .blob-4 { width:150px; height:150px; top:20%; right:15%; animation: floatBlob 8s ease-in-out infinite 1s; }
        @keyframes floatBlob { 0%,100%{transform:translateY(0) scale(1)} 50%{transform:translateY(-20px) scale(1.05)} }

        .float-emoji {
            position: absolute; font-size: 2.2rem;
            animation: floatEmoji 4s ease-in-out infinite;
            pointer-events: none;
            filter: drop-shadow(0 4px 8px rgba(0,0,0,0.15));
        }
        .fe-1 { top:15%; left:8%;   animation-delay:0s; }
        .fe-2 { top:20%; right:9%;  animation-delay:0.8s; }
        .fe-3 { bottom:22%; left:6%; animation-delay:1.4s; }
        .fe-4 { bottom:28%; right:7%; animation-delay:0.4s; }
        .fe-5 { top:50%; left:3%;   animation-delay:2s; }
        .fe-6 { top:45%; right:4%;  animation-delay:1.8s; }
        @keyframes floatEmoji { 0%,100%{transform:translateY(0) rotate(-5deg)} 50%{transform:translateY(-16px) rotate(5deg)} }

        .hero-badge {
            display: inline-flex; align-items: center; gap: 8px;
            background: rgba(255,255,255,0.15);
            border: 1.5px solid rgba(255,255,255,0.25);
            border-radius: 50px; padding: 8px 20px;
            font-family: 'Fredoka One', cursive; font-size: 0.85rem;
            color: rgba(255,255,255,0.9); margin-bottom: 24px;
            backdrop-filter: blur(8px);
            animation: fadeDown 0.6s ease both;
        }
        .hero-badge-dot { width:8px; height:8px; background:#86efac; border-radius:50%; animation: pulse 1.5s infinite; }

        .hero-title {
            font-family: 'Fredoka One', cursive;
            font-size: clamp(3rem, 8vw, 6rem);
            color: #fff; line-height: 1.05;
            margin-bottom: 6px;
            text-shadow: 0 4px 24px rgba(0,0,0,0.15);
            animation: fadeDown 0.7s ease 0.1s both;
        }
        .hero-title span {
            color: var(--accent); display: inline-block;
            animation: wiggle 3s ease-in-out infinite 1s;
        }
        @keyframes wiggle { 0%,100%{transform:rotate(-2deg)} 50%{transform:rotate(2deg)} }

        .hero-sub {
            font-size: clamp(1rem, 2.5vw, 1.25rem);
            color: rgba(255,255,255,0.78); font-weight: 700;
            max-width: 560px; margin: 0 auto 40px;
            line-height: 1.6;
            animation: fadeDown 0.7s ease 0.2s both;
        }
        .hero-btns {
            display: flex; gap: 14px; flex-wrap: wrap; justify-content: center;
            animation: fadeDown 0.7s ease 0.3s both;
        }
        .btn-hero-primary {
            font-family: 'Fredoka One', cursive; font-size: 1.1rem;
            background: var(--accent); color: #fff;
            border: none; border-radius: 50px; padding: 16px 36px;
            cursor: pointer;
            display: inline-flex; align-items: center; gap: 8px;
            box-shadow: 0 8px 28px rgba(245,158,11,0.45);
            transition: all 0.25s cubic-bezier(.34,1.56,.64,1);
        }
        .btn-hero-primary:hover { transform: translateY(-3px) scale(1.05); box-shadow: 0 14px 36px rgba(245,158,11,0.5); }

        .btn-hero-secondary {
            font-family: 'Fredoka One', cursive; font-size: 1rem;
            background: rgba(255,255,255,0.15); color: #fff;
            border: 2px solid rgba(255,255,255,0.3);
            border-radius: 50px; padding: 16px 32px;
            cursor: pointer; text-decoration: none;
            display: inline-flex; align-items: center; gap: 8px;
            backdrop-filter: blur(6px); transition: all 0.2s;
        }
        .btn-hero-secondary:hover { background: rgba(255,255,255,0.25); transform: translateY(-2px); }

        .hero-stats {
            display: flex; gap: 32px; margin-top: 56px;
            flex-wrap: wrap; justify-content: center;
            animation: fadeDown 0.7s ease 0.4s both;
        }
        .hero-stat {
            text-align: center;
            background: rgba(255,255,255,0.1);
            border: 1.5px solid rgba(255,255,255,0.15);
            border-radius: 18px; padding: 16px 28px;
            backdrop-filter: blur(6px);
        }
        .hero-stat-num { font-family: 'Fredoka One', cursive; font-size: 2rem; color: #fff; }
        .hero-stat-label { font-size: 0.75rem; color: rgba(255,255,255,0.65); font-weight: 700; margin-top: 2px; }

        .wave { display: block; margin-bottom: -2px; }

        /* ── SUBJECTS ── */
        .section-subjects { background: var(--cream); padding: 80px 40px; }
        .section-label {
            display: inline-flex; align-items: center; gap: 8px;
            font-family: 'Fredoka One', cursive; font-size: 0.8rem;
            color: var(--green-dark); background: var(--green-light);
            padding: 6px 16px; border-radius: 50px;
            margin-bottom: 12px; letter-spacing: 1px; text-transform: uppercase;
        }
        .section-title {
            font-family: 'Fredoka One', cursive;
            font-size: clamp(2rem, 4vw, 2.8rem);
            color: #1a1a2e; margin-bottom: 10px;
        }
        .section-sub { font-size: 1rem; color: #888; font-weight: 700; margin-bottom: 48px; max-width: 500px; }

        .login-notice {
            display: flex; align-items: center; gap: 14px;
            background: linear-gradient(135deg, #fff8ed, #fff3e0);
            border: 2px solid #fde68a; border-radius: 16px;
            padding: 16px 24px; margin-bottom: 40px; max-width: 700px;
        }
        .login-notice-icon { font-size: 1.8rem; flex-shrink: 0; }
        .login-notice-text { font-size: 0.88rem; font-weight: 700; color: #92400e; }
        .login-notice-text a { color: var(--green-dark); text-decoration: underline; cursor: pointer; }

        .subjects-grid {
            display: grid; grid-template-columns: repeat(4, 1fr);
            gap: 20px; max-width: 1100px;
        }
        @media(max-width:1100px){ .subjects-grid { grid-template-columns: repeat(3,1fr); } }
        @media(max-width:720px) { .subjects-grid { grid-template-columns: repeat(2,1fr); } }
        @media(max-width:480px) { .subjects-grid { grid-template-columns: 1fr 1fr; gap:12px; } }

        .subject-card {
            background: #fff; border-radius: 22px;
            padding: 28px 20px 22px; text-align: center;
            border: 2px solid #f0f0f0; position: relative;
            overflow: hidden; cursor: pointer;
            transition: transform 0.25s cubic-bezier(.34,1.56,.64,1), box-shadow 0.25s, border-color 0.2s;
            display: block;
        }
        .subject-card::before {
            content: ''; position: absolute;
            top: 0; left: 0; right: 0; height: 4px;
            border-radius: 22px 22px 0 0;
        }
        .subject-card:hover { transform: translateY(-6px) scale(1.02); box-shadow: 0 16px 40px rgba(0,0,0,0.12); }

        .sc-motorskills { --c1:#0097a7; --c2:#e0f7fa; } .sc-motorskills::before { background:#0097a7; }
        .sc-animals  { --c1:#059669; --c2:#d1fae5; } .sc-animals::before  { background:#059669; }
        .sc-body     { --c1:#dc2626; --c2:#fee2e2; } .sc-body::before     { background:#dc2626; }
        .sc-heroes   { --c1:#d97706; --c2:#fef3c7; } .sc-heroes::before   { background:#d97706; }
        .sc-letters  { --c1:#2563eb; --c2:#dbeafe; } .sc-letters::before  { background:#2563eb; }
        .sc-colors   { --c1:#db2777; --c2:#fce7f3; } .sc-colors::before   { background:#db2777; }
        .sc-shapes   { --c1:#0891b2; --c2:#cffafe; } .sc-shapes::before   { background:#0891b2; }
        .sc-numbers  { --c1:#16a34a; --c2:#dcfce7; } .sc-numbers::before  { background:#16a34a; }

        .subject-emoji-wrap {
            width: 72px; height: 72px; background: var(--c2);
            border-radius: 20px; display: flex; align-items: center;
            justify-content: center; font-size: 2.2rem;
            margin: 0 auto 16px;
            transition: transform 0.25s cubic-bezier(.34,1.56,.64,1);
        }
        .subject-card:hover .subject-emoji-wrap { transform: scale(1.12) rotate(-4deg); }
        .subject-title { font-family: 'Fredoka One', cursive; font-size: 1.1rem; color: #1a1a2e; margin-bottom: 6px; }
        .subject-desc  { font-size: 0.75rem; color: #aaa; font-weight: 700; margin-bottom: 16px; line-height: 1.4; }
        .lock-badge {
            display: inline-flex; align-items: center; gap: 5px;
            background: #f3f4f6; color: #9ca3af;
            font-family: 'Fredoka One', cursive; font-size: 0.72rem;
            padding: 5px 12px; border-radius: 50px;
        }
        .lock-badge i { font-size: 0.65rem; }
        .lock-overlay {
            position: absolute; inset: 0;
            background: rgba(255,255,255,0.55);
            backdrop-filter: blur(1px); border-radius: 22px;
            display: flex; align-items: center; justify-content: center;
            opacity: 0; transition: opacity 0.2s;
        }
        .subject-card:hover .lock-overlay { opacity: 1; }
        .lock-overlay-inner {
            background: rgba(0,0,0,0.75); color: #fff;
            font-family: 'Fredoka One', cursive; font-size: 0.85rem;
            padding: 10px 18px; border-radius: 50px;
            display: flex; align-items: center; gap: 7px;
        }

        /* ── ABOUT ── */
        .section-about {
            background: linear-gradient(160deg, #0d5407 0%, #1a7a0d 100%);
            padding: 80px 40px; position: relative; overflow: hidden;
        }
        .section-about::before {
            content: ''; position: absolute; top: -100px; right: -100px;
            width: 400px; height: 400px; border-radius: 50%;
            background: rgba(255,255,255,0.04);
        }
        .about-grid {
            display: grid; grid-template-columns: 1fr 1fr;
            gap: 60px; align-items: center;
            max-width: 1100px; margin: 0 auto;
        }
        @media(max-width:800px){ .about-grid { grid-template-columns: 1fr; gap: 40px; } }

        .about-text .section-label { background: rgba(255,255,255,0.15); color: rgba(255,255,255,0.9); }
        .about-text .section-title { color: #fff; }
        .about-text .section-sub   { color: rgba(255,255,255,0.7); max-width: 100%; margin-bottom: 28px; }

        .about-features { display: flex; flex-direction: column; gap: 14px; }
        .about-feature {
            display: flex; align-items: flex-start; gap: 14px;
            background: rgba(255,255,255,0.08);
            border: 1.5px solid rgba(255,255,255,0.12);
            border-radius: 16px; padding: 16px 18px;
            backdrop-filter: blur(4px); transition: background 0.2s;
        }
        .about-feature:hover { background: rgba(255,255,255,0.13); }
        .about-feature-icon {
            width: 40px; height: 40px; background: rgba(255,255,255,0.15);
            border-radius: 12px; display: flex; align-items: center;
            justify-content: center; font-size: 1.1rem; flex-shrink: 0;
        }
        .about-feature-title { font-family: 'Fredoka One', cursive; font-size: 0.95rem; color: #fff; margin-bottom: 3px; }
        .about-feature-desc  { font-size: 0.78rem; color: rgba(255,255,255,0.6); font-weight: 700; line-height: 1.5; }

        .about-card {
            background: rgba(255,255,255,0.1);
            border: 2px solid rgba(255,255,255,0.15);
            border-radius: 28px; padding: 36px;
            backdrop-filter: blur(10px); text-align: center;
        }
        .about-card-emoji  { font-size: 4rem; margin-bottom: 20px; display: block; }
        .about-card-title  { font-family: 'Fredoka One', cursive; font-size: 1.6rem; color: #fff; margin-bottom: 12px; }
        .about-card-text   { font-size: 0.88rem; color: rgba(255,255,255,0.7); font-weight: 700; line-height: 1.8; }
        .about-card-divider{ height:1px; background: rgba(255,255,255,0.12); margin: 20px 0; }
        .about-card-stats  { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
        .about-card-stat-num   { font-family: 'Fredoka One', cursive; font-size: 1.8rem; color: var(--accent); }
        .about-card-stat-label { font-size: 0.72rem; color: rgba(255,255,255,0.5); font-weight: 700; }

        /* ── LOCATION ── */
        .section-location { background: var(--cream); padding: 80px 40px; }
        .location-grid {
            display: grid; grid-template-columns: 1fr 1fr;
            gap: 40px; align-items: start;
            max-width: 1100px; margin: 0 auto;
        }
        @media(max-width:800px){ .location-grid { grid-template-columns: 1fr; } }

        .map-placeholder {
            background: linear-gradient(135deg, #e8f5e2, #d1fae5);
            border: 2px solid #86efac; border-radius: 24px;
            height: 320px; display: flex; flex-direction: column;
            align-items: center; justify-content: center;
            gap: 12px; position: relative; overflow: hidden;
        }
        .map-placeholder::before {
            content: ''; position: absolute;
            width: 200px; height: 200px; border-radius: 50%;
            border: 2px dashed #86efac;
            animation: spin 20s linear infinite;
        }
        .map-placeholder::after {
            content: ''; position: absolute;
            width: 120px; height: 120px; border-radius: 50%;
            border: 2px dashed #4ade80;
            animation: spin 14s linear infinite reverse;
        }
        @keyframes spin { to { transform: rotate(360deg); } }
        .map-pin   { font-size: 3rem; position: relative; z-index: 1; animation: bounce 1.5s ease-in-out infinite; }
        @keyframes bounce { 0%,100%{transform:translateY(0)} 50%{transform:translateY(-10px)} }
        .map-label { font-family: 'Fredoka One', cursive; font-size: 1rem; color: var(--green-dark); position: relative; z-index: 1; }

        .location-info { display: flex; flex-direction: column; gap: 16px; }
        .location-item {
            display: flex; gap: 16px; align-items: flex-start;
            background: #fff; border: 1.5px solid #f0f0f0;
            border-radius: 18px; padding: 20px;
            transition: box-shadow 0.2s, transform 0.2s;
        }
        .location-item:hover { box-shadow: 0 8px 24px rgba(0,0,0,0.07); transform: translateY(-2px); }
        .location-icon {
            width: 46px; height: 46px; background: var(--green-light);
            border-radius: 14px; display: flex; align-items: center;
            justify-content: center; font-size: 1.2rem;
            flex-shrink: 0; color: var(--green-dark);
        }
        .location-item-title { font-family: 'Fredoka One', cursive; font-size: 0.95rem; color: #1a1a2e; margin-bottom: 4px; }
        .location-item-text  { font-size: 0.82rem; color: #888; font-weight: 700; line-height: 1.5; }

        /* ── FOOTER ── */
        .footer {
            background: var(--green-dark); padding: 36px 40px;
            display: flex; align-items: center; justify-content: space-between;
            flex-wrap: wrap; gap: 16px;
        }
        .footer-brand { font-family: 'Fredoka One', cursive; font-size: 1.4rem; color: #fff; }
        .footer-copy  { font-size: 0.78rem; color: rgba(255,255,255,0.45); font-weight: 700; }
        .footer-links { display: flex; gap: 20px; }
        .footer-link  {
            font-family: 'Fredoka One', cursive; font-size: 0.8rem;
            color: rgba(255,255,255,0.55); text-decoration: none; transition: color 0.2s;
        }
        .footer-link:hover { color: #fff; }

        /* ── UTILITIES ── */
        .container { max-width: 1100px; margin: 0 auto; }
        @keyframes fadeDown { from{opacity:0;transform:translateY(-20px)} to{opacity:1;transform:translateY(0)} }
        .reveal { opacity: 0; transform: translateY(28px); transition: opacity 0.6s ease, transform 0.6s ease; }
        .reveal.visible { opacity: 1; transform: translateY(0); }

        /* ══════════════════════════════════════
           IFRAME MODAL OVERLAY
           The login UI is loaded from student-login.php
           inside an <iframe> — two separate files,
           same visual popup experience.
        ══════════════════════════════════════ */
        .sl-overlay {
            position: fixed; inset: 0; z-index: 9999;
            background: rgba(0,0,0,0.55);
            backdrop-filter: blur(6px);
            display: flex; align-items: center; justify-content: center;
            padding: 20px;
            opacity: 0; pointer-events: none;
            transition: opacity 0.3s ease;
        }
        .sl-overlay.open { opacity: 1; pointer-events: all; }

        .sl-iframe-wrap {
            position: relative;
            width: 100%; max-width: 460px;
            transform: scale(0.88) translateY(24px);
            transition: transform 0.35s cubic-bezier(.34,1.56,.64,1);
        }
        .sl-overlay.open .sl-iframe-wrap { transform: scale(1) translateY(0); }

        /* Close button sits outside the iframe, on the overlay wrapper */
        .sl-close {
            position: absolute; top: -14px; right: -14px; z-index: 10;
            width: 36px; height: 36px; background: #fff;
            border: none; border-radius: 50%; cursor: pointer;
            display: flex; align-items: center; justify-content: center;
            color: #9ca3af; font-size: 0.9rem;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            transition: background 0.2s, color 0.2s;
        }
        .sl-close:hover { background: #f3f4f6; color: #374151; }

        .sl-iframe {
            width: 100%;
            height: 530px;
            border: none;
            border-radius: 28px;
            display: block;
            box-shadow: 0 32px 80px rgba(0,0,0,0.25), 0 8px 24px rgba(0,0,0,0.1);
            overflow: hidden;
        }

        /* Subject lock modal */
        .modal-overlay {
            position: fixed; inset: 0; z-index: 9998;
            background: rgba(0,0,0,0.5); backdrop-filter: blur(4px);
            display: flex; align-items: center; justify-content: center;
            padding: 20px; opacity: 0; pointer-events: none;
            transition: opacity 0.3s;
        }
        .modal-overlay.open { opacity: 1; pointer-events: all; }
        .modal-box {
            background: #fff; border-radius: 28px;
            padding: 40px 36px; width: 100%; max-width: 380px;
            text-align: center;
            transform: scale(0.9) translateY(20px);
            transition: transform 0.3s cubic-bezier(.34,1.56,.64,1);
            box-shadow: 0 24px 64px rgba(0,0,0,0.2);
        }
        .modal-overlay.open .modal-box { transform: scale(1) translateY(0); }
        .modal-emoji { font-size: 3rem; margin-bottom: 14px; display: block; }
        .modal-title { font-family: 'Fredoka One', cursive; font-size: 1.6rem; color: var(--green-dark); margin-bottom: 8px; }
        .modal-text  { font-size: 0.88rem; color: #888; font-weight: 700; margin-bottom: 24px; line-height: 1.6; }
        .modal-btns  { display: flex; flex-direction: column; gap: 10px; }
        .modal-btn-primary {
            font-family: 'Fredoka One', cursive; font-size: 1rem;
            background: var(--green-dark); color: #fff;
            border: none; border-radius: 14px; padding: 13px;
            cursor: pointer; display: flex; align-items: center;
            justify-content: center; gap: 8px;
            transition: all 0.2s; box-shadow: 0 4px 16px rgba(13,84,7,0.25);
        }
        .modal-btn-primary:hover { background: var(--green-mid); transform: translateY(-2px); }
        .modal-btn-close {
            font-family: 'Fredoka One', cursive; font-size: 0.88rem;
            background: #f3f4f6; color: #888;
            border: none; border-radius: 14px; padding: 11px;
            cursor: pointer; transition: background 0.2s;
        }
        .modal-btn-close:hover { background: #e5e7eb; }

        @media(max-width:768px) {
            .navbar { padding: 0 20px; }
            .nav-links { display: none; }
            .hero { padding: 100px 24px 60px; }
            .section-subjects, .section-about, .section-location { padding: 60px 24px; }
            .footer { padding: 28px 24px; flex-direction: column; text-align: center; }
            .sl-iframe { height: 530px; border-radius: 22px; }
        }
    </style>
</head>
<body>

<!-- ── NAVBAR ── -->
<nav class="navbar">
    <a href="#" class="nav-brand">
        E-KINDER
        <div class="nav-brand-dot"></div>
    </a>
    <div class="nav-links">
        <a href="#subjects" class="nav-link">Subjects</a>
        <a href="#about"    class="nav-link">About Us</a>
        <a href="#location" class="nav-link">Location</a>
    </div>
    <button class="nav-btn-login" onclick="openLoginModal()">
        <i class="fas fa-sign-in-alt"></i> Student Login
    </button>
</nav>

<!-- ── HERO ── -->
<section class="hero">
    <div class="blob blob-1"></div>
    <div class="blob blob-2"></div>
    <div class="blob blob-3"></div>
    <div class="blob blob-4"></div>
    <div class="float-emoji fe-1">✏️</div>
    <div class="float-emoji fe-2">🐾</div>
    <div class="float-emoji fe-3">🦸</div>
    <div class="float-emoji fe-4">🔢</div>
    <div class="float-emoji fe-5">🎨</div>
    <div class="float-emoji fe-6">🫀</div>

    <div class="hero-badge">
        <div class="hero-badge-dot"></div>
        Interactive Learning for Kindergarteners
    </div>
    <h1 class="hero-title">Learn, Play &<br><span>Explore!</span></h1>
    <p class="hero-sub">A fun and interactive learning platform designed especially for kindergarten students. Discover 8 exciting subjects!</p>
    <div class="hero-btns">
        <button class="btn-hero-primary" onclick="openLoginModal()">
            <i class="fas fa-rocket"></i> Start Learning
        </button>
        <a href="#subjects" class="btn-hero-secondary">
            <i class="fas fa-book-open"></i> See Subjects
        </a>
    </div>
    <div class="hero-stats">
        <div class="hero-stat">
            <div class="hero-stat-num">8</div>
            <div class="hero-stat-label">Fun Subjects</div>
        </div>
        <div class="hero-stat">
            <div class="hero-stat-num">★</div>
            <div class="hero-stat-label">Earn Badges</div>
        </div>
        <div class="hero-stat">
            <div class="hero-stat-num">100%</div>
            <div class="hero-stat-label">Kid Friendly</div>
        </div>
    </div>
</section>

<svg class="wave" viewBox="0 0 1440 60" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none" style="display:block;background:#1a7a0d;">
    <path d="M0,40 C360,80 1080,0 1440,40 L1440,60 L0,60 Z" fill="#fafdf7"/>
</svg>

<!-- ── SUBJECTS ── -->
<section class="section-subjects" id="subjects">
    <div class="container">
        <div class="reveal">
            <div class="section-label"><i class="fas fa-book"></i> Our Lessons</div>
            <h2 class="section-title">Choose a Subject</h2>
            <p class="section-sub">Login first to unlock all 8 exciting subjects and start your learning adventure!</p>
        </div>
        <div class="login-notice reveal">
            <div class="login-notice-icon">🔐</div>
            <div class="login-notice-text">
                All subjects are locked. Please <a onclick="openLoginModal()">login to your student account</a> to start learning!
            </div>
        </div>
        <div class="subjects-grid reveal">
            <div class="subject-card locked sc-motorskills" onclick="showSubjectModal('Motor Skills')">
                <div class="subject-emoji-wrap">🙌</div>
                <div class="subject-title">Motor Skills</div>
                <div class="subject-desc">Learn to wash hands, brush teeth, and care for your body!</div>
                <div class="lock-badge"><i class="fas fa-lock"></i> Login to Unlock</div>
                <div class="lock-overlay"><div class="lock-overlay-inner"><i class="fas fa-lock"></i> Login First</div></div>
            </div>
            <div class="subject-card locked sc-animals" onclick="showSubjectModal('Animals')">
                <div class="subject-emoji-wrap">🐾</div>
                <div class="subject-title">Animals</div>
                <div class="subject-desc">Discover amazing animals and their names</div>
                <div class="lock-badge"><i class="fas fa-lock"></i> Login to Unlock</div>
                <div class="lock-overlay"><div class="lock-overlay-inner"><i class="fas fa-lock"></i> Login First</div></div>
            </div>
            <div class="subject-card locked sc-body" onclick="showSubjectModal('Human Body')">
                <div class="subject-emoji-wrap">🫀</div>
                <div class="subject-title">Human Body</div>
                <div class="subject-desc">Explore the amazing parts of your body</div>
                <div class="lock-badge"><i class="fas fa-lock"></i> Login to Unlock</div>
                <div class="lock-overlay"><div class="lock-overlay-inner"><i class="fas fa-lock"></i> Login First</div></div>
            </div>
            <div class="subject-card locked sc-heroes" onclick="showSubjectModal('National Heroes')">
                <div class="subject-emoji-wrap">🦸</div>
                <div class="subject-title">National Heroes</div>
                <div class="subject-desc">Meet the brave heroes of our nation</div>
                <div class="lock-badge"><i class="fas fa-lock"></i> Login to Unlock</div>
                <div class="lock-overlay"><div class="lock-overlay-inner"><i class="fas fa-lock"></i> Login First</div></div>
            </div>
            <div class="subject-card locked sc-letters" onclick="showSubjectModal('Letters')">
                <div class="subject-emoji-wrap">🔤</div>
                <div class="subject-title">Letters</div>
                <div class="subject-desc">Learn the alphabet A to Z with fun!</div>
                <div class="lock-badge"><i class="fas fa-lock"></i> Login to Unlock</div>
                <div class="lock-overlay"><div class="lock-overlay-inner"><i class="fas fa-lock"></i> Login First</div></div>
            </div>
            <div class="subject-card locked sc-colors" onclick="showSubjectModal('Colors')">
                <div class="subject-emoji-wrap">🎨</div>
                <div class="subject-title">Colors</div>
                <div class="subject-desc">Identify and name all the beautiful colors</div>
                <div class="lock-badge"><i class="fas fa-lock"></i> Login to Unlock</div>
                <div class="lock-overlay"><div class="lock-overlay-inner"><i class="fas fa-lock"></i> Login First</div></div>
            </div>
            <div class="subject-card locked sc-shapes" onclick="showSubjectModal('Shapes')">
                <div class="subject-emoji-wrap">
                    <svg width="36" height="36" viewBox="0 0 36 36" fill="none">
                        <rect x="4" y="4" width="28" height="28" rx="4" stroke="#0891b2" stroke-width="3" fill="none"/>
                    </svg>
                </div>
                <div class="subject-title">Shapes</div>
                <div class="subject-desc">Recognize circles, squares, triangles and more</div>
                <div class="lock-badge"><i class="fas fa-lock"></i> Login to Unlock</div>
                <div class="lock-overlay"><div class="lock-overlay-inner"><i class="fas fa-lock"></i> Login First</div></div>
            </div>
            <div class="subject-card locked sc-numbers" onclick="showSubjectModal('Numbers')">
                <div class="subject-emoji-wrap">
                    <svg width="36" height="36" viewBox="0 0 36 36" fill="none">
                        <text x="2" y="28" font-size="26" font-family="Fredoka One,cursive" fill="#16a34a">123</text>
                    </svg>
                </div>
                <div class="subject-title">Numbers</div>
                <div class="subject-desc">Count and learn numbers from 1 to 100</div>
                <div class="lock-badge"><i class="fas fa-lock"></i> Login to Unlock</div>
                <div class="lock-overlay"><div class="lock-overlay-inner"><i class="fas fa-lock"></i> Login First</div></div>
            </div>
        </div>
    </div>
</section>

<!-- ── ABOUT ── -->
<section class="section-about" id="about">
    <div class="about-grid">
        <div class="about-text reveal">
            <div class="section-label"><i class="fas fa-star"></i> About Us</div>
            <h2 class="section-title">What is E-KINDER?</h2>
            <p class="section-sub">A modern digital learning platform made for young learners to explore, discover, and grow.</p>
            <div class="about-features">
                <div class="about-feature">
                    <div class="about-feature-icon">🎮</div>
                    <div>
                        <div class="about-feature-title">Fun & Interactive</div>
                        <div class="about-feature-desc">Lessons designed with games and activities that keep kids engaged and excited to learn.</div>
                    </div>
                </div>
                <div class="about-feature">
                    <div class="about-feature-icon">📊</div>
                    <div>
                        <div class="about-feature-title">Track Progress</div>
                        <div class="about-feature-desc">Teachers can monitor each student's learning progress and performance in real time.</div>
                    </div>
                </div>
                <div class="about-feature">
                    <div class="about-feature-icon">🔒</div>
                    <div>
                        <div class="about-feature-title">Safe & Secure</div>
                        <div class="about-feature-desc">A safe digital environment designed especially for young learners with teacher supervision.</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="about-card reveal">
            <span class="about-card-emoji">🎓</span>
            <div class="about-card-title">E-KINDER</div>
            <div class="about-card-text">Our mission is to make early education exciting and accessible for every kindergarten student through technology and creativity.</div>
            <div class="about-card-divider"></div>
            <div class="about-card-stats">
                <div>
                    <div class="about-card-stat-num">8</div>
                    <div class="about-card-stat-label">Subjects</div>
                </div>
                <div>
                    <div class="about-card-stat-num">K</div>
                    <div class="about-card-stat-label">Kindergarten Level</div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ── LOCATION ── -->
<section class="section-location" id="location">
    <div class="container">
        <div class="reveal" style="margin-bottom:40px;">
            <div class="section-label"><i class="fas fa-map-marker-alt"></i> Location</div>
            <h2 class="section-title">Find Us Here</h2>
            <p class="section-sub">Visit us or get in touch with our school.</p>
        </div>
        <div class="location-grid reveal">
            <div class="map-placeholder">
                <div class="map-pin">📍</div>
                <div class="map-label">Our School Location</div>
            </div>
            <div class="location-info">
                <div class="location-item">
                    <div class="location-icon"><i class="fas fa-map-marker-alt"></i></div>
                    <div>
                        <div class="location-item-title">Address</div>
                        <div class="location-item-text">123 School Street, Barangay Sample<br>City, Province, Philippines</div>
                    </div>
                </div>
                <div class="location-item">
                    <div class="location-icon"><i class="fas fa-phone"></i></div>
                    <div>
                        <div class="location-item-title">Phone</div>
                        <div class="location-item-text">(02) 8123-4567<br>0917 123 4567</div>
                    </div>
                </div>
                <div class="location-item">
                    <div class="location-icon"><i class="fas fa-envelope"></i></div>
                    <div>
                        <div class="location-item-title">Email</div>
                        <div class="location-item-text">info@ekinder.edu.ph</div>
                    </div>
                </div>
                <div class="location-item">
                    <div class="location-icon"><i class="fas fa-clock"></i></div>
                    <div>
                        <div class="location-item-title">School Hours</div>
                        <div class="location-item-text">Monday - Friday: 7:30 AM - 4:00 PM<br>Saturday - Sunday: Closed</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ── FOOTER ── -->
<footer class="footer">
    <div class="footer-brand">E-KINDER</div>
    <div class="footer-copy">© <?php echo date('Y'); ?> E-KINDER. All rights reserved.</div>
    <div class="footer-links">
        <a href="teacher-login.php" class="footer-link">Teacher Login</a>
        <a href="#subjects"         class="footer-link">Subjects</a>
        <a href="#about"            class="footer-link">About</a>
    </div>
</footer>


<!-- ══════════════════════════════════════════
     IFRAME LOGIN MODAL
     student-login.php is a separate file
     loaded here inside an <iframe> popup
══════════════════════════════════════════ -->
<div class="sl-overlay" id="loginOverlay" onclick="overlayClickClose(event)">
    <div class="sl-iframe-wrap" id="loginModal">

        <!-- Close button is on the parent page, outside the iframe -->
        <button class="sl-close" onclick="closeLoginModal()" title="Close">
            <i class="fas fa-times"></i>
        </button>

        <!-- student-login.php loads here as a separate file -->
        <iframe
            id="loginIframe"
            class="sl-iframe"
            src=""
            data-src="student-login.php"
            title="Student Login">
        </iframe>

    </div>
</div>

<!-- ══════════════════════════════════════════
     IFRAME SIGNUP MODAL
══════════════════════════════════════════ -->
<div class="sl-overlay" id="signupOverlay" onclick="overlayClickClose2(event,'signupOverlay')">
    <div class="sl-iframe-wrap">
        <button class="sl-close" onclick="closeModal('signupOverlay')" title="Close">
            <i class="fas fa-times"></i>
        </button>
        <iframe id="signupIframe" class="sl-iframe" src="" data-src="student-signup.php" title="Student Sign Up" style="height:82vh; max-height:700px;"></iframe>
    </div>
</div>

<!-- ══════════════════════════════════════════
     IFRAME FORGOT PASSWORD MODAL
══════════════════════════════════════════ -->
<div class="sl-overlay" id="forgotOverlay" onclick="overlayClickClose2(event,'forgotOverlay')">
    <div class="sl-iframe-wrap">
        <button class="sl-close" onclick="closeModal('forgotOverlay')" title="Close">
            <i class="fas fa-times"></i>
        </button>
        <iframe id="forgotIframe" class="sl-iframe" src="" data-src="student-forgot.php" title="Forgot Password" style="height:380px;"></iframe>
    </div>
</div>

<!-- Subject lock prompt modal -->
<div class="modal-overlay" id="subjectModal" onclick="closeSubjectModalOutside(event)">
    <div class="modal-box">
        <span class="modal-emoji">🔒</span>
        <div class="modal-title">Login Required!</div>
        <div class="modal-text" id="subjectModalText">Login to access this subject.</div>
        <div class="modal-btns">
            <button class="modal-btn-primary" onclick="closeSubjectModal(); openLoginModal();">
                <i class="fas fa-sign-in-alt"></i> Go to Student Login
            </button>
            <button class="modal-btn-close" onclick="closeSubjectModal()">Maybe Later</button>
        </div>
    </div>
</div>


<script>
/* ── Scroll Reveal ── */
const observer = new IntersectionObserver((entries) => {
    entries.forEach((entry, i) => {
        if (entry.isIntersecting)
            setTimeout(() => entry.target.classList.add('visible'), i * 80);
    });
}, { threshold: 0.1 });
document.querySelectorAll('.reveal').forEach(el => observer.observe(el));

/* ── Generic modal helpers ── */
const loaded = {};

function openModal(overlayId, iframeId) {
    if (iframeId && !loaded[iframeId]) {
        const el = document.getElementById(iframeId);
        el.src = el.dataset.src;
        loaded[iframeId] = true;
    }
    document.getElementById(overlayId).classList.add('open');
    document.body.style.overflow = 'hidden';
}
function closeModal(overlayId) {
    document.getElementById(overlayId).classList.remove('open');
    document.body.style.overflow = '';
}
function overlayClickClose2(e, overlayId) {
    if (e.target === document.getElementById(overlayId)) closeModal(overlayId);
}

/* ── Shorthand openers ── */
function openLoginModal()  { openModal('loginOverlay',  'loginIframe');  }
function closeLoginModal() { closeModal('loginOverlay'); }
function overlayClickClose(e) { overlayClickClose2(e, 'loginOverlay'); }

function openSignupModal()  { openModal('signupOverlay', 'signupIframe'); }
function openForgotModal()  { openModal('forgotOverlay', 'forgotIframe'); }

/* ── Listen for postMessages from iframes ── */
window.addEventListener('message', (e) => {
    if (e.data === 'closeLoginModal')  closeModal('loginOverlay');
    if (e.data === 'openLogin')  { closeModal('signupOverlay'); closeModal('forgotOverlay'); openLoginModal(); }
    if (e.data === 'openSignup') { closeModal('loginOverlay');  openSignupModal(); }
    if (e.data === 'openForgot') { closeModal('loginOverlay');  openForgotModal(); }
});

/* ── Subject Lock Modal ── */
function showSubjectModal(subject) {
    document.getElementById('subjectModalText').textContent =
        'You need to login to access the "' + subject + '" subject.';
    document.getElementById('subjectModal').classList.add('open');
}
function closeSubjectModal() {
    document.getElementById('subjectModal').classList.remove('open');
}
function closeSubjectModalOutside(e) {
    if (e.target === document.getElementById('subjectModal')) closeSubjectModal();
}

/* ── Smooth Scroll ── */
document.querySelectorAll('a[href^="#"]').forEach(a => {
    a.addEventListener('click', e => {
        const id = a.getAttribute('href');
        if (id === '#') return;
        e.preventDefault();
        document.querySelector(id)?.scrollIntoView({ behavior: 'smooth' });
    });
});

/* ── Keyboard Escape ── */
document.addEventListener('keydown', e => {
    if (e.key === 'Escape') {
        closeLoginModal();
        closeSubjectModal();
    }
});
</script>
</body>
</html>