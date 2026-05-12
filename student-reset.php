<?php
// student-reset.php
// Opened via reset link: student-reset.php?token=XXXXXXXX
// Validates token from password_resets table.
// On success → updates student_password in kinder_db → students table.

require_once __DIR__ . '/mailer.php';

$error       = '';
$success     = false;
$token       = trim($_GET['token'] ?? $_POST['token'] ?? '');
$valid       = false;
$reset_email = '';

$pdo = null;
try {
    $pdo = getDB();
} catch (PDOException $e) {
    $error = 'Database error. Please try again later.';
}

// ── Step 1: Validate the token ──
if ($pdo && $token && !$error) {
    $stmt = $pdo->prepare(
        "SELECT email, expires_at FROM password_resets
         WHERE token = :token
         LIMIT 1"
    );
    $stmt->execute([':token' => $token]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row) {
        // Use PHP time comparison to avoid timezone mismatch with MySQL
        if (strtotime($row['expires_at']) > time()) {
            $valid       = true;
            $reset_email = $row['email'];
        } else {
            $error = 'This reset link is invalid or has already expired.';
        }
    } else {
        $error = 'This reset link is invalid or has already expired.';
    }
}

// ── Step 2: Handle password reset form submission ──
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $pdo && $valid) {
    $new_password     = trim($_POST['new_password']     ?? '');
    $confirm_password = trim($_POST['confirm_password'] ?? '');

    if (!$new_password || !$confirm_password) {
        $error = 'Please fill in all fields.';
    } elseif (strlen($new_password) < 6) {
        $error = 'Password must be at least 6 characters.';
    } elseif ($new_password !== $confirm_password) {
        $error = 'Passwords do not match.';
    } else {
        try {
            $hash = password_hash($new_password, PASSWORD_DEFAULT);

            // Update student_password in students table
            $pdo->prepare(
                "UPDATE students
                 SET student_password = :hash
                 WHERE student_email = :email"
            )->execute([':hash' => $hash, ':email' => $reset_email]);

            // Delete the used token
            $pdo->prepare(
                "DELETE FROM password_resets WHERE email = :email"
            )->execute([':email' => $reset_email]);

            $success = true;

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
    <title>Reset Password — E-KINDER</title>
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
            width: 68px; height: 68px; background: #dbeafe;
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
            margin-bottom: 28px; line-height: 1.6;
        }

        /* Error alert */
        .sl-alert {
            background: #fff0f0; border: 1.5px solid #fca5a5;
            border-radius: 12px; padding: 11px 14px;
            font-size: 0.83rem; font-weight: 700; color: #dc2626;
            margin-bottom: 20px;
            display: flex; align-items: center; gap: 8px;
            animation: shakeErr 0.4s ease;
            text-align: left;
        }
        @keyframes shakeErr {
            0%,100%{transform:translateX(0)}
            25%{transform:translateX(-6px)}
            75%{transform:translateX(6px)}
        }

        /* Invalid / expired token state */
        .sl-invalid {
            background: #fff0f0; border: 1.5px solid #fca5a5;
            border-radius: 16px; padding: 28px 24px;
            text-align: center;
        }
        .sl-invalid-icon {
            font-size: 3rem; margin-bottom: 14px; display: block;
        }
        .sl-invalid-title {
            font-family: 'Fredoka One', cursive; font-size: 1.3rem;
            color: #dc2626; margin-bottom: 8px;
        }
        .sl-invalid-text {
            font-size: 0.85rem; color: #888; font-weight: 700; line-height: 1.6;
        }

        /* Success state */
        .sl-success {
            background: #f0fdf4; border: 1.5px solid #86efac;
            border-radius: 16px; padding: 28px 24px;
            text-align: center;
        }
        .sl-success-icon {
            font-size: 3rem; margin-bottom: 14px; display: block;
        }
        .sl-success-title {
            font-family: 'Fredoka One', cursive; font-size: 1.4rem;
            color: var(--green-dark); margin-bottom: 8px;
        }
        .sl-success-text {
            font-size: 0.85rem; color: #888; font-weight: 700;
            line-height: 1.6; margin-bottom: 20px;
        }

        .btn-go-login {
            width: 100%; padding: 13px;
            background: var(--green-dark); color: #fff;
            border: none; border-radius: 14px;
            font-family: 'Fredoka One', cursive; font-size: 1rem;
            cursor: pointer;
            box-shadow: 0 6px 20px rgba(13,84,7,0.28);
            display: flex; align-items: center; justify-content: center; gap: 8px;
            transition: transform 0.2s, background 0.2s;
        }
        .btn-go-login:hover { background: #1a7a0d; transform: translateY(-2px); }

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

        /* Password strength bar */
        .strength-bar-wrap {
            height: 6px; background: #f0f0f0;
            border-radius: 99px; margin-top: -8px; margin-bottom: 16px;
            overflow: hidden;
        }
        .strength-bar {
            height: 100%; width: 0%;
            border-radius: 99px;
            transition: width 0.3s, background 0.3s;
        }

        .btn-submit {
            width: 100%; padding: 14px;
            background: var(--green-dark); color: #fff;
            border: none; border-radius: 14px;
            font-family: 'Fredoka One', cursive; font-size: 1.05rem;
            cursor: pointer; margin-top: 6px;
            box-shadow: 0 6px 20px rgba(13,84,7,0.28);
            display: flex; align-items: center; justify-content: center; gap: 8px;
            transition: transform 0.2s cubic-bezier(.34,1.56,.64,1), box-shadow 0.2s, background 0.2s;
        }
        .btn-submit:hover {
            transform: translateY(-2px) scale(1.02);
            box-shadow: 0 10px 28px rgba(13,84,7,0.35);
            background: #1a7a0d;
        }
        .btn-submit:active { transform: scale(0.98); }

        .divider {
            display: flex; align-items: center; gap: 12px;
            margin: 22px 0 18px;
        }
        .divider-line { flex: 1; height: 1px; background: #f0f0f0; }
        .divider-text { font-family: 'Fredoka One', cursive; font-size: 0.75rem; color: #ddd; }

        .sl-footer-hint { font-size: 0.78rem; color: #ccc; font-weight: 700; }
        .sl-footer-hint a { color: var(--green-dark); text-decoration: none; font-weight: 800; }
        .sl-footer-hint a:hover { text-decoration: underline; }

        @media(max-width:480px) {
            .sl-modal { padding: 36px 20px 28px; border-radius: 20px; }
        }
    </style>
</head>
<body>

<div class="sl-modal">

    <?php if ($success): ?>

    <!-- ── SUCCESS STATE ── -->
    <div class="sl-success">
        <span class="sl-success-icon">🎉</span>
        <div class="sl-success-title">Password Reset!</div>
        <div class="sl-success-text">
            Your password has been updated successfully.<br>
            You can now login with your new password.
        </div>
        <button class="btn-go-login" onclick="window.parent.postMessage('openLogin','*')">
            <i class="fas fa-sign-in-alt"></i> Go to Login
        </button>
    </div>

    <?php elseif (!$valid): ?>

    <!-- ── INVALID / EXPIRED TOKEN ── -->
    <div class="sl-invalid">
        <span class="sl-invalid-icon">⛔</span>
        <div class="sl-invalid-title">Link Expired!</div>
        <div class="sl-invalid-text">
            This reset link is invalid or has already expired.<br>
            Please request a new one.
        </div>
    </div>

    <div class="divider">
        <div class="divider-line"></div>
        <div class="divider-text">OR</div>
        <div class="divider-line"></div>
    </div>
    <div class="sl-footer-hint">
        <a href="#" onclick="window.parent.postMessage('openForgot','*'); return false;">
            Request a new reset link
        </a>
    </div>

    <?php else: ?>

    <!-- ── RESET FORM ── -->
    <div class="sl-icon">🔐</div>
    <div class="sl-title">Reset Password</div>
    <div class="sl-sub">Enter your new password below.</div>

    <?php if ($error): ?>
    <div class="sl-alert">
        <i class="fas fa-exclamation-circle"></i>
        <?php echo htmlspecialchars($error); ?>
    </div>
    <?php endif; ?>

    <form method="POST" action="student-reset.php?token=<?php echo urlencode($token); ?>" autocomplete="off">
        <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">

        <label class="form-label" for="sr_password">New Password</label>
        <div class="input-wrap">
            <i class="fas fa-lock input-icon"></i>
            <input
                type="password"
                id="sr_password"
                name="new_password"
                class="form-input"
                placeholder="Enter new password"
                required
                autofocus
                oninput="checkStrength(this.value)">
            <button type="button" class="toggle-pass" onclick="togglePass('sr_password','eyeIcon1')">
                <i class="fas fa-eye" id="eyeIcon1"></i>
            </button>
        </div>
        <!-- Password strength bar -->
        <div class="strength-bar-wrap">
            <div class="strength-bar" id="strengthBar"></div>
        </div>

        <label class="form-label" for="sr_confirm">Confirm New Password</label>
        <div class="input-wrap">
            <i class="fas fa-lock input-icon"></i>
            <input
                type="password"
                id="sr_confirm"
                name="confirm_password"
                class="form-input"
                placeholder="Confirm new password"
                required>
            <button type="button" class="toggle-pass" onclick="togglePass('sr_confirm','eyeIcon2')">
                <i class="fas fa-eye" id="eyeIcon2"></i>
            </button>
        </div>

        <button type="submit" class="btn-submit">
            <i class="fas fa-shield-alt"></i> Reset Password
        </button>
    </form>

    <div class="divider">
        <div class="divider-line"></div>
        <div class="divider-text">OR</div>
        <div class="divider-line"></div>
    </div>
    <div class="sl-footer-hint">
        Remember your password?
        <a href="#" onclick="window.parent.postMessage('openLogin','*'); return false;">Back to Login</a>
    </div>

    <?php endif; ?>

</div>

<script>
function togglePass(inputId, iconId) {
    const input = document.getElementById(inputId);
    const icon  = document.getElementById(iconId);
    if (input.type === 'password') {
        input.type = 'text';
        icon.className = 'fas fa-eye-slash';
    } else {
        input.type = 'password';
        icon.className = 'fas fa-eye';
    }
}

function checkStrength(val) {
    const bar = document.getElementById('strengthBar');
    if (!bar) return;
    let score = 0;
    if (val.length >= 6)           score++;
    if (val.length >= 10)          score++;
    if (/[A-Z]/.test(val))         score++;
    if (/[0-9]/.test(val))         score++;
    if (/[^A-Za-z0-9]/.test(val))  score++;

    const levels = [
        { w: '0%',   bg: 'transparent' },
        { w: '25%',  bg: '#ef4444' },
        { w: '50%',  bg: '#f59e0b' },
        { w: '75%',  bg: '#3b82f6' },
        { w: '90%',  bg: '#22c55e' },
        { w: '100%', bg: '#16a34a' },
    ];
    bar.style.width      = levels[score].w;
    bar.style.background = levels[score].bg;
}
</script>
</body>
</html>