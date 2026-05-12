<?php
// student-login.php
// Loaded inside an iframe on index.php.
// Database: kinder_db → students table
// Columns:  student_id, student_uname, student_email, student_password,
//           student_fname, student_mname, student_lname

session_start();
require_once __DIR__ . '/mailer.php';

$error         = '';
$login_success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login    = trim($_POST['login']    ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($login === '' || $password === '') {
        $error = 'Please fill in all fields.';
    } else {
        try {
            $pdo = getDB();

            $stmt = $pdo->prepare(
                "SELECT student_id, student_uname, student_email,
                        student_password, student_fname, student_lname
                 FROM students
                 WHERE student_uname = :login OR student_email = :login
                 LIMIT 1"
            );
            $stmt->execute([':login' => $login]);
            $student = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$student || !password_verify($password, $student['student_password'])) {
                $error = 'Invalid username/email or password. Please try again.';
            } else {
                // ── Login success — set session ──
                $_SESSION['student_id']   = $student['student_id'];
                $_SESSION['student_name'] = $student['student_fname'] . ' ' . $student['student_lname'];
                $_SESSION['student_user'] = $student['student_uname'];
                $_SESSION['role']         = 'student';
                session_regenerate_id(true);

                // Show loading overlay first, redirect via JS after delay
                $login_success = true;
            }
        } catch (PDOException $e) {
            $error = 'Database error. Please try again later.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Login — E-KINDER</title>
    <link href="https://fonts.googleapis.com/css2?family=Fredoka+One&family=Nunito:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --green-dark:  #0d5407;
            --green-mid:   #1a7a0d;
            --green-light: #e8f5e2;
            --accent:      #f59e0b;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        html, body {
            margin: 0; padding: 0;
            height: 100%;
            overflow: hidden;
            background: #fff;
        }
        body {
            font-family: 'Nunito', sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .sl-modal {
            background: #fff;
            border-radius: 28px;
            padding: 36px 36px 28px;
            width: 100%;
            text-align: center;
        }

        .sl-icon {
            width: 68px; height: 68px; background: var(--green-light);
            border-radius: 20px; display: flex; align-items: center;
            justify-content: center; font-size: 1.8rem;
            margin: 0 auto 18px;
        }
        .sl-title {
            font-family: 'Fredoka One', cursive; font-size: 1.75rem;
            color: var(--green-dark); margin-bottom: 6px;
        }
        .sl-sub {
            font-size: 0.85rem; color: #aaa; font-weight: 700;
            margin-bottom: 28px;
        }

        .sl-alert {
            background: #fff0f0; border: 1.5px solid #fca5a5;
            border-radius: 12px; padding: 11px 14px;
            font-size: 0.83rem; font-weight: 700; color: #dc2626;
            margin-bottom: 20px;
            display: flex; align-items: center; gap: 8px;
            animation: shakeErr 0.4s ease; text-align: left;
        }
        @keyframes shakeErr {
            0%,100%{transform:translateX(0)}
            25%{transform:translateX(-6px)}
            75%{transform:translateX(6px)}
        }

        .form-label {
            font-family: 'Fredoka One', cursive; font-size: 0.82rem;
            color: #555; display: block; margin-bottom: 6px; text-align: left;
        }
        .input-wrap { position: relative; margin-bottom: 16px; }
        .input-icon {
            position: absolute; left: 14px; top: 50%;
            transform: translateY(-50%);
            color: #d1d5db; font-size: 0.9rem; pointer-events: none;
            transition: color 0.2s;
        }
        .input-wrap:focus-within .input-icon { color: var(--green-dark); }
        .form-input {
            width: 100%; padding: 13px 16px 13px 42px;
            border: 2px solid #e8e8e8; border-radius: 14px;
            font-family: 'Nunito', sans-serif; font-size: 0.95rem;
            font-weight: 700; color: #1a1a2e; background: #fafafa;
            outline: none;
            transition: border-color 0.2s, box-shadow 0.2s, background 0.2s;
        }
        .form-input:focus {
            border-color: var(--green-dark); background: #fff;
            box-shadow: 0 0 0 4px rgba(13,84,7,0.08);
        }
        .form-input::placeholder { color: #c4c4c4; font-weight: 600; }

        .toggle-pass {
            position: absolute; right: 13px; top: 50%;
            transform: translateY(-50%);
            background: none; border: none; color: #d1d5db;
            cursor: pointer; font-size: 0.88rem; padding: 4px;
            transition: color 0.2s;
        }
        .toggle-pass:hover { color: var(--green-dark); }

        .btn-login {
            width: 100%; padding: 14px;
            background: var(--green-dark); color: #fff;
            border: none; border-radius: 14px;
            font-family: 'Fredoka One', cursive; font-size: 1.05rem;
            cursor: pointer; margin-top: 6px;
            box-shadow: 0 6px 20px rgba(13,84,7,0.28);
            display: flex; align-items: center; justify-content: center; gap: 8px;
            transition: transform 0.2s cubic-bezier(.34,1.56,.64,1), box-shadow 0.2s, background 0.2s;
        }
        .btn-login:hover {
            transform: translateY(-2px) scale(1.02);
            box-shadow: 0 10px 28px rgba(13,84,7,0.35);
            background: #1a7a0d;
        }
        .btn-login:active { transform: scale(0.98); }

        .forgot-link { text-align: right; margin-top: 10px; }
        .forgot-link a {
            font-family: 'Fredoka One', cursive;
            font-size: 0.78rem; color: #aaa;
            text-decoration: none; transition: color 0.2s;
        }
        .forgot-link a:hover { color: var(--green-dark); }

        .divider { display: flex; align-items: center; gap: 12px; margin: 22px 0 18px; }
        .divider-line { flex: 1; height: 1px; background: #f0f0f0; }
        .divider-text { font-family: 'Fredoka One', cursive; font-size: 0.75rem; color: #ddd; }

        .sl-footer-hint { font-size: 0.78rem; color: #ccc; font-weight: 700; }
        .sl-footer-hint a { color: var(--green-dark); text-decoration: none; font-weight: 800; }
        .sl-footer-hint a:hover { text-decoration: underline; }

        /* ══════════════════════════════════════
           RUNNING EMOJI + PROGRESS BAR LOADING
        ══════════════════════════════════════ */
        .loading-overlay {
            position: fixed; inset: 0; z-index: 9999;
            background: rgba(255,255,255,0.97);
            display: flex; flex-direction: column;
            align-items: center; justify-content: center;
            gap: 28px;
            opacity: 0; pointer-events: none;
            transition: opacity 0.3s ease;
        }
        .loading-overlay.show { opacity: 1; pointer-events: all; }

        .loading-title {
            font-family: 'Fredoka One', cursive;
            font-size: 1.4rem; color: var(--green-dark);
        }

        .run-track-wrap { width: 260px; position: relative; }

        .run-emoji {
            position: absolute; top: -32px; left: 0%;
            font-size: 1.8rem;
            transform: translateX(-50%) scaleX(-1); /* flip to face right */
            transition: left 4s linear;
            z-index: 2; line-height: 1;
            animation: runBounce 0.35s ease-in-out infinite;
        }
        .run-emoji.go { left: 100%; }

        /* Bouncing run effect */
        @keyframes runBounce {
            0%, 100% { top: -32px; }
            50%       { top: -40px; }
        }

        .run-flag {
            position: absolute; top: -28px; right: -4px;
            font-size: 1.6rem; line-height: 1; z-index: 2;
        }

        .run-track {
            width: 100%; height: 14px;
            background: #e8f5e2; border-radius: 99px;
            overflow: hidden; position: relative;
        }
        .run-fill {
            height: 100%; width: 0%;
            background: linear-gradient(90deg, #0d5407, #2da619);
            border-radius: 99px; transition: width 4s linear;
        }
        .run-fill.go { width: 100%; }

        .run-track::after {
            content: ''; position: absolute;
            top: 50%; left: 0; right: 0; height: 2px;
            transform: translateY(-50%);
            background: repeating-linear-gradient(
                90deg,
                rgba(255,255,255,0.5) 0px, rgba(255,255,255,0.5) 10px,
                transparent 10px, transparent 20px
            );
        }

        .loading-sub { font-size: 0.85rem; color: #aaa; font-weight: 700; margin-top: -12px; }

        @media(max-width:480px) {
            .sl-modal { padding: 36px 20px 28px; border-radius: 20px; }
        }
    </style>
</head>
<body>

<!-- ══ RUNNING EMOJI LOADING OVERLAY ══ -->
<div class="loading-overlay" id="loadingOverlay">
    <div class="run-track-wrap">
        <div class="run-emoji" id="runEmoji">🏃</div>
        <div class="run-flag">🏁</div>
        <div class="run-track">
            <div class="run-fill" id="runFill"></div>
        </div>
    </div>
    <div class="loading-sub" id="loadingSub">Logging you in...</div>
</div>

<div class="sl-modal">

    <div class="sl-title">Student Login</div>
    <div class="sl-sub">Welcome back! Enter your account details.</div>

    <?php if ($error): ?>
    <div class="sl-alert">
        <i class="fas fa-exclamation-circle"></i>
        <?php echo htmlspecialchars($error); ?>
    </div>
    <?php endif; ?>

    <form method="POST" action="student-login.php" autocomplete="off">

        <label class="form-label" for="sl_login">Username or Email</label>
        <div class="input-wrap">
            <i class="fas fa-user input-icon"></i>
            <input type="text" id="sl_login" name="login" class="form-input"
                placeholder="Enter username or email"
                value="<?php echo htmlspecialchars($_POST['login'] ?? ''); ?>"
                required autofocus>
        </div>

        <label class="form-label" for="sl_password">Password</label>
        <div class="input-wrap">
            <i class="fas fa-lock input-icon"></i>
            <input type="password" id="sl_password" name="password" class="form-input"
                placeholder="Enter your password" required>
            <button type="button" class="toggle-pass" onclick="togglePass()">
                <i class="fas fa-eye" id="eyeIcon"></i>
            </button>
        </div>

        <button type="submit" class="btn-login">
            <i class="fas fa-sign-in-alt"></i> Login
        </button>
    </form>

    <div class="forgot-link">
        <a href="#" onclick="window.parent.postMessage('openForgot','*'); return false;">
            Forgot password?
        </a>
    </div>

    <div class="divider">
        <div class="divider-line"></div>
        <div class="divider-text">OR</div>
        <div class="divider-line"></div>
    </div>
    <div class="sl-footer-hint">
        No account yet?
        <a href="#" onclick="window.parent.postMessage('openSignup','*'); return false;">Sign up here</a>
        &nbsp;·&nbsp;
    </div>

</div>

<script>
function togglePass() {
    const input = document.getElementById('sl_password');
    const icon  = document.getElementById('eyeIcon');
    if (input.type === 'password') {
        input.type = 'text';
        icon.className = 'fas fa-eye-slash';
    } else {
        input.type = 'password';
        icon.className = 'fas fa-eye';
    }
}

<?php if ($login_success): ?>
// ── Login success — show running emoji loading then redirect ──
window.addEventListener('DOMContentLoaded', function () {
    const overlay  = document.getElementById('loadingOverlay');
    const runFill  = document.getElementById('runFill');
    const runEmoji = document.getElementById('runEmoji');
    const sub      = document.getElementById('loadingSub');

    overlay.classList.add('show');

    requestAnimationFrame(() => {
        requestAnimationFrame(() => {
            runFill.classList.add('go');
            runEmoji.classList.add('go');
        });
    });

    setTimeout(() => { sub.textContent = 'Almost there! 💨'; }, 2000);
    setTimeout(() => { sub.textContent = 'Ready! 🎉'; }, 3800);

    // Redirect to dashboard after 4 seconds
    setTimeout(() => {
        window.parent.location.href = 'student-dashboard.php';
    }, 4000);
});
<?php endif; ?>
</script>
</body>
</html>