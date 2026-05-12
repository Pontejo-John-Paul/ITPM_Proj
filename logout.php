<?php
session_start();
require_once 'database.php';

// ── If confirmed, do the actual logout ──
if (isset($_GET['confirm']) && $_GET['confirm'] === 'yes') {

    if (isset($_SESSION['audit_log_id'])) {
        $log_id      = $_SESSION['audit_log_id'];
        $logout_time = date('Y-m-d H:i:s');

        $stmt = $conn->prepare("UPDATE audit_logs SET logout_time = ? WHERE log_id = ?");
        $stmt->bind_param("si", $logout_time, $log_id);
        $stmt->execute();
        $stmt->close();
    }

    session_unset();
    session_destroy();
    header("Location: index.php");
    exit;
}

// ── Otherwise show confirmation page ──
$first_name = $_SESSION['student_fname'] ?? 'Student';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logout — E-KINDER</title>
    <link href="https://fonts.googleapis.com/css2?family=Fredoka+One&family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <style>
        :root { --green-dark: #0d5407; --green-light: #e8f5e2; }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Nunito', sans-serif;
            background: #fffbf4;
            min-height: 100vh;
            display: flex; align-items: center; justify-content: center;
            padding: 20px;
            background-image:
                radial-gradient(circle at 15% 20%, rgba(13,84,7,0.07) 0%, transparent 40%),
                radial-gradient(circle at 85% 80%, rgba(255,107,107,0.07) 0%, transparent 40%);
        }

        .confirm-card {
            background: #fff; border-radius: 32px;
            padding: 48px 40px 40px;
            text-align: center; max-width: 400px; width: 100%;
            box-shadow: 0 12px 50px rgba(0,0,0,0.10);
            border-top: 6px solid var(--green-dark);
            animation: popIn 0.4s cubic-bezier(.34,1.56,.64,1) both;
        }
        @keyframes popIn {
            from { opacity: 0; transform: scale(0.85) translateY(24px); }
            to   { opacity: 1; transform: scale(1) translateY(0); }
        }

        .logout-emoji {
            font-size: 4rem; display: block; margin-bottom: 16px;
            animation: wave 1.2s ease-in-out infinite;
        }
        @keyframes wave {
            0%,100% { transform: rotate(0deg); }
            25%      { transform: rotate(-15deg); }
            75%      { transform: rotate(15deg); }
        }

        .confirm-title {
            font-family: 'Fredoka One', cursive;
            font-size: 1.7rem; color: var(--green-dark);
            margin-bottom: 8px;
        }
        .confirm-name {
            font-family: 'Fredoka One', cursive;
            font-size: 1rem; color: #aaa; margin-bottom: 12px;
        }
        .confirm-name span { color: var(--green-dark); }
        .confirm-text {
            font-size: 0.88rem; font-weight: 700; color: #bbb;
            line-height: 1.6; margin-bottom: 32px;
        }

        .btn-group { display: flex; gap: 12px; }
        .btn {
            flex: 1; padding: 13px; border-radius: 50px; border: none;
            font-family: 'Fredoka One', cursive; font-size: 1rem;
            cursor: pointer; text-decoration: none;
            display: flex; align-items: center; justify-content: center; gap: 8px;
            transition: transform 0.2s cubic-bezier(.34,1.56,.64,1), box-shadow 0.2s;
        }
        .btn-cancel {
            background: #f0f0f0; color: #666;
        }
        .btn-cancel:hover { background: #e0e0e0; transform: scale(1.04); color: #444; }

        .btn-logout {
            background: #e53935; color: #fff;
            box-shadow: 0 6px 20px rgba(229,57,53,0.3);
        }
        .btn-logout:hover { transform: scale(1.04); box-shadow: 0 10px 28px rgba(229,57,53,0.4); color: #fff; }

        /* loading overlay */
        .loading-overlay {
            display: none; position: fixed; inset: 0;
            background: rgba(255,255,255,0.92); backdrop-filter: blur(4px);
            z-index: 999; flex-direction: column;
            align-items: center; justify-content: center; gap: 20px;
        }
        .loading-overlay.active { display: flex; }
        .loading-child-wrap { position: relative; width: 260px; height: 40px; }
        .loading-track {
            width: 100%; height: 12px; background: #e8f5e2;
            border-radius: 50px; overflow: hidden;
            box-shadow: inset 0 2px 6px rgba(0,0,0,0.08);
            position: absolute; bottom: 0;
        }
        .loading-bar {
            height: 100%; width: 0%;
            background: linear-gradient(90deg, #51cf66, var(--green-dark));
            border-radius: 50px; transition: width 0.12s linear;
        }
        .loading-child {
            position: absolute; bottom: 10px;
            font-size: 1.6rem; left: 0%;
            transition: left 0.12s linear;
            display: inline-block; transform: scaleX(-1);
            filter: drop-shadow(0 2px 4px rgba(0,0,0,0.15));
        }
        .loading-label {
            font-family: 'Fredoka One', cursive;
            font-size: 1rem; color: var(--green-dark);
            letter-spacing: 0.5px; transition: opacity 0.2s;
        }
    </style>
</head>
<body>

<div class="confirm-card">
    <span class="logout-emoji">👋</span>
    <div class="confirm-title">Leaving so soon?</div>
    <div class="confirm-name">Bye, <span><?php echo htmlspecialchars($first_name); ?>!</span></div>
    <div class="confirm-text">Are you sure you want to logout?<br>Your progress is saved — come back anytime! 🌟</div>

    <div class="btn-group">
        <a href="student-dashboard.php" class="btn btn-cancel">
            <i class="fas fa-arrow-left"></i> Stay
        </a>
        <button class="btn btn-logout" onclick="doLogout()">
            <i class="fas fa-sign-out-alt"></i> Yes, Logout
        </button>
    </div>
</div>

<!-- Loading overlay -->
<div class="loading-overlay" id="loadingOverlay">
    <div class="loading-child-wrap">
        <div class="loading-child" id="loadingChild">🏃</div>
        <div class="loading-track">
            <div class="loading-bar" id="loadingBar"></div>
        </div>
    </div>
    <div class="loading-label" id="loadingLabel">Logging you out...</div>
</div>

<script>
function doLogout() {
    const overlay  = document.getElementById('loadingOverlay');
    const bar      = document.getElementById('loadingBar');
    const child    = document.getElementById('loadingChild');
    const label    = document.getElementById('loadingLabel');
    const messages = ['Logging you out...', 'Saving your session...', 'See you next time! 👋'];

    overlay.classList.add('active');

    let progress = 0;
    let msgIdx   = 0;

    const interval = setInterval(() => {
        const step = progress < 30 ? 2 : progress < 80 ? 4 : 1;
        progress   = Math.min(progress + step, 95);

        bar.style.width  = progress + '%';
        child.style.left = Math.max(0, progress - 8) + '%';

        const newMsg = Math.floor(progress / 35);
        if (newMsg !== msgIdx && newMsg < messages.length) {
            msgIdx = newMsg;
            label.style.opacity = '0';
            setTimeout(() => {
                label.textContent   = messages[msgIdx];
                label.style.opacity = '1';
            }, 200);
        }

        if (progress >= 95) {
            clearInterval(interval);
            window.location.href = 'index.php?confirm=yes';
        }
    }, 60);
}
</script>
</body>
</html>