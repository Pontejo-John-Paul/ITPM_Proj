<?php
// student-forgot.php
// Loaded inside an iframe on index.php.
// Database: kinder_db → students table
// Looks up student_email, generates reset token, saves to password_resets table,
// and sends reset link via PHPMailer.
//
// Required SQL — run once in your DB:
// CREATE TABLE password_resets (
//     id         INT AUTO_INCREMENT PRIMARY KEY,
//     email      VARCHAR(255) NOT NULL,
//     token      VARCHAR(255) NOT NULL,
//     expires_at DATETIME     NOT NULL,
//     INDEX (email),
//     INDEX (token)
// );

require_once __DIR__ . '/mailer.php';

$error   = '';
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');

    if ($email === '') {
        $error = 'Please enter your email address.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } else {
        try {
            $pdo = getDB();

            // Look up student by student_email — also get name for the email
            $stmt = $pdo->prepare(
                "SELECT student_id, student_fname, student_lname
                 FROM students
                 WHERE student_email = :email
                 LIMIT 1"
            );
            $stmt->execute([':email' => $email]);
            $student = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($student) {
                // Generate secure reset token
                $token      = bin2hex(random_bytes(32));
                $expires    = date('Y-m-d H:i:s', strtotime('+24 hours'));
                $reset_link = SITE_URL . '/student-reset.php?token=' . $token;
                $full_name  = $student['student_fname'] . ' ' . $student['student_lname'];

                // Remove any existing tokens for this email, then save new one
                $pdo->prepare("DELETE FROM password_resets WHERE email = :email")
                    ->execute([':email' => $email]);

                $pdo->prepare(
                    "INSERT INTO password_resets (email, token, expires_at)
                     VALUES (:email, :token, :expires)"
                )->execute([':email' => $email, ':token' => $token, ':expires' => $expires]);

                // Send reset email via PHPMailer
                $subject = '🔑 Reset Your E-KINDER Password';
                $body    = mailTemplate_PasswordReset($full_name, $reset_link);
                sendMail($email, $full_name, $subject, $body);
            }

            // Always show success — never reveal if email is registered (security)
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
    <title>Forgot Password — E-KINDER</title>
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
            width: 68px; height: 68px; background: #fef3c7;
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
            font-size: 0.85rem; color: #888; font-weight: 700; line-height: 1.6;
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

        .btn-submit {
            width: 100%; padding: 14px;
            background: var(--accent); color: #fff;
            border: none; border-radius: 14px;
            font-family: 'Fredoka One', cursive; font-size: 1.05rem;
            cursor: pointer; margin-top: 6px;
            box-shadow: 0 6px 20px rgba(245,158,11,0.35);
            display: flex; align-items: center; justify-content: center; gap: 8px;
            transition: transform 0.2s cubic-bezier(.34,1.56,.64,1), box-shadow 0.2s, filter 0.2s;
        }
        .btn-submit:hover {
            transform: translateY(-2px) scale(1.02);
            box-shadow: 0 10px 28px rgba(245,158,11,0.45);
            filter: brightness(1.05);
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
        <span class="sl-success-icon">📬</span>
        <div class="sl-success-title">Check Your Email!</div>
        <div class="sl-success-text">
            If your email is registered, we've sent a password reset link.<br>
            Please check your inbox (and spam folder).<br><br>
            The link will expire in <strong>1 hour</strong>.
        </div>
    </div>

    <div class="divider">
        <div class="divider-line"></div>
        <div class="divider-text">OR</div>
        <div class="divider-line"></div>
    </div>
    <div class="sl-footer-hint">
        Remember your password?
        <a href="#" onclick="window.parent.postMessage('openLogin','*'); return false;">Back to Login</a>
    </div>

    <?php else: ?>

    <!-- ── FORGOT FORM ── -->
    <div class="sl-icon">🔑</div>
    <div class="sl-title">Forgot Password?</div>
    <div class="sl-sub">Enter your registered email and we'll send you a reset link.</div>

    <?php if ($error): ?>
    <div class="sl-alert">
        <i class="fas fa-exclamation-circle"></i>
        <?php echo htmlspecialchars($error); ?>
    </div>
    <?php endif; ?>

    <form method="POST" action="student-forgot.php" autocomplete="off">

        <label class="form-label" for="sf_email">Email Address</label>
        <div class="input-wrap">
            <i class="fas fa-envelope input-icon"></i>
            <input
                type="email"
                id="sf_email"
                name="email"
                class="form-input"
                placeholder="Enter your registered email"
                value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>"
                required
                autofocus>
        </div>

        <button type="submit" class="btn-submit">
            <i class="fas fa-paper-plane"></i> Send Reset Link
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

</body>
</html>