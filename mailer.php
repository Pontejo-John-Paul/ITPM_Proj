<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Manual PHPMailer include — matches your folder structure:
// ITPM_project/phpmailer/src/
require_once __DIR__ . '/phpmailer/src/Exception.php';
require_once __DIR__ . '/phpmailer/src/PHPMailer.php';
require_once __DIR__ . '/phpmailer/src/SMTP.php';

// ── Gmail SMTP Credentials ──
define('MAIL_FROM',     'Your_email');
define('MAIL_FROM_NAME','E-KINDER');
define('MAIL_PASSWORD', 'google_app_password');

// ── Site base URL — must match your actual project folder ──
define('SITE_URL', 'http://localhost/ITPM_PROJECT');

// ── Database config ──
define('DB_HOST', 'localhost');
define('DB_NAME', 'kinder_db');
define('DB_USER', 'root');
define('DB_PASS', '');

/**
 * Get a shared PDO connection to kinder_db.
 */
function getDB(): PDO {
    return new PDO(
        'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
        DB_USER,
        DB_PASS,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
}

/**
 * Send an HTML email via Gmail SMTP.
 * Returns true on success, or an error string on failure.
 */
function sendMail(string $to_email, string $to_name, string $subject, string $body_html): true|string
{
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = MAIL_FROM;
        $mail->Password   = MAIL_PASSWORD;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        // ── Fix for XAMPP localhost SSL certificate verify failed ──
        // This is safe for local development environments.
        $mail->SMTPOptions = [
            'ssl' => [
                'verify_peer'       => false,
                'verify_peer_name'  => false,
                'allow_self_signed' => true,
            ]
        ];

        $mail->setFrom(MAIL_FROM, MAIL_FROM_NAME);
        $mail->addAddress($to_email, $to_name);
        $mail->addReplyTo(MAIL_FROM, MAIL_FROM_NAME);

        $mail->isHTML(true);
        $mail->CharSet = 'UTF-8';
        $mail->Subject = $subject;
        $mail->Body    = $body_html;
        $mail->AltBody = strip_tags(str_replace(['<br>', '<br/>', '</p>'], "\n", $body_html));

        $mail->send();
        return true;

    } catch (Exception $e) {
        return $mail->ErrorInfo;
    }
}


// ══════════════════════════════════════════════
//  EMAIL TEMPLATES
// ══════════════════════════════════════════════

/**
 * Welcome email sent after successful signup.
 */
function mailTemplate_Welcome(string $full_name, string $username): string
{
    return '
<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1"></head>
<body style="margin:0;padding:0;background:#f0f9eb;font-family:Arial,sans-serif;">
<table width="100%" cellpadding="0" cellspacing="0" style="background:#f0f9eb;padding:40px 0;">
  <tr><td align="center">
    <table width="560" cellpadding="0" cellspacing="0"
           style="background:#fff;border-radius:24px;overflow:hidden;box-shadow:0 8px 32px rgba(0,0,0,0.08);">
      <tr>
        <td style="background:linear-gradient(135deg,#0d5407,#2da619);padding:40px;text-align:center;">
          <div style="font-size:3rem;margin-bottom:10px;">🎒</div>
          <h1 style="font-size:2rem;color:#fff;margin:0;letter-spacing:1px;">E-KINDER</h1>
          <p style="color:rgba(255,255,255,0.75);margin:6px 0 0;font-size:0.9rem;">Learn, Play & Explore!</p>
        </td>
      </tr>
      <tr>
        <td style="padding:36px 40px 28px;">
          <h2 style="color:#0d5407;font-size:1.5rem;margin:0 0 12px;">
            Welcome, ' . htmlspecialchars($full_name) . '! 🌟
          </h2>
          <p style="color:#555;font-size:0.95rem;line-height:1.7;margin:0 0 20px;">
            Your E-KINDER student account has been successfully created.
            You\'re all set to start your learning adventure!
          </p>
          <table width="100%" cellpadding="0" cellspacing="0"
                 style="background:#f0f9eb;border:1.5px solid #86efac;border-radius:14px;margin-bottom:24px;">
            <tr><td style="padding:20px 24px;">
              <p style="margin:0 0 10px;color:#888;font-size:0.78rem;font-weight:bold;
                         text-transform:uppercase;letter-spacing:1px;">Your Account Details</p>
              <table cellpadding="4">
                <tr>
                  <td style="color:#555;font-size:0.88rem;width:100px;">👤 Name</td>
                  <td style="color:#1a1a2e;font-size:0.88rem;font-weight:bold;">' . htmlspecialchars($full_name) . '</td>
                </tr>
                <tr>
                  <td style="color:#555;font-size:0.88rem;">🏷️ Username</td>
                  <td style="color:#1a1a2e;font-size:0.88rem;font-weight:bold;">' . htmlspecialchars($username) . '</td>
                </tr>
              </table>
            </td></tr>
          </table>
          <p style="color:#555;font-size:0.9rem;font-weight:bold;margin:0 0 8px;">Here\'s what you can explore:</p>
          <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:28px;">
            <tr><td style="padding:5px 0;font-size:0.87rem;color:#555;">✏️ &nbsp;Spelling — Learn to spell common words</td></tr>
            <tr><td style="padding:5px 0;font-size:0.87rem;color:#555;">🐾 &nbsp;Animals — Discover amazing animals</td></tr>
            <tr><td style="padding:5px 0;font-size:0.87rem;color:#555;">🔤 &nbsp;Letters — Learn the alphabet A to Z</td></tr>
            <tr><td style="padding:5px 0;font-size:0.87rem;color:#555;">🔢 &nbsp;Numbers — Count from 1 to 100</td></tr>
            <tr><td style="padding:5px 0;font-size:0.87rem;color:#555;">🎨 &nbsp;Colors, Shapes, Human Body & More!</td></tr>
          </table>
          <table width="100%" cellpadding="0" cellspacing="0">
            <tr><td align="center">
              <a href="' . SITE_URL . '"
                 style="display:inline-block;background:#0d5407;color:#fff;text-decoration:none;
                        padding:14px 40px;border-radius:50px;font-size:1rem;font-weight:bold;
                        box-shadow:0 6px 20px rgba(13,84,7,0.3);">
                🚀 Start Learning Now
              </a>
            </td></tr>
          </table>
        </td>
      </tr>
      <tr>
        <td style="background:#f9fafb;border-top:1px solid #f0f0f0;padding:20px 40px;text-align:center;">
          <p style="color:#bbb;font-size:0.78rem;margin:0;">
            © ' . date('Y') . ' E-KINDER. All rights reserved.<br>
            If you did not create this account, please ignore this email.
          </p>
        </td>
      </tr>
    </table>
  </td></tr>
</table>
</body>
</html>';
}


/**
 * Password reset email with reset link.
 */
function mailTemplate_PasswordReset(string $full_name, string $reset_link): string
{
    return '
<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1"></head>
<body style="margin:0;padding:0;background:#f0f9eb;font-family:Arial,sans-serif;">
<table width="100%" cellpadding="0" cellspacing="0" style="background:#f0f9eb;padding:40px 0;">
  <tr><td align="center">
    <table width="560" cellpadding="0" cellspacing="0"
           style="background:#fff;border-radius:24px;overflow:hidden;box-shadow:0 8px 32px rgba(0,0,0,0.08);">
      <tr>
        <td style="background:linear-gradient(135deg,#0d5407,#2da619);padding:40px;text-align:center;">
          <div style="font-size:3rem;margin-bottom:10px;">🔑</div>
          <h1 style="font-size:2rem;color:#fff;margin:0;letter-spacing:1px;">E-KINDER</h1>
          <p style="color:rgba(255,255,255,0.75);margin:6px 0 0;font-size:0.9rem;">Password Reset Request</p>
        </td>
      </tr>
      <tr>
        <td style="padding:36px 40px 28px;">
          <h2 style="color:#0d5407;font-size:1.4rem;margin:0 0 12px;">
            Hi, ' . htmlspecialchars($full_name) . '! 👋
          </h2>
          <p style="color:#555;font-size:0.95rem;line-height:1.7;margin:0 0 24px;">
            We received a request to reset your E-KINDER password.
            Click the button below to create a new password.
            This link will expire in <strong>24 hours</strong>.
          </p>
          <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:24px;">
            <tr><td align="center">
              <a href="' . htmlspecialchars($reset_link) . '"
                 style="display:inline-block;background:#f59e0b;color:#fff;text-decoration:none;
                        padding:16px 44px;border-radius:50px;font-size:1rem;font-weight:bold;
                        box-shadow:0 6px 20px rgba(245,158,11,0.4);">
                🔐 Reset My Password
              </a>
            </td></tr>
          </table>
          <table width="100%" cellpadding="0" cellspacing="0"
                 style="background:#fafafa;border:1.5px solid #e8e8e8;border-radius:12px;margin-bottom:24px;">
            <tr><td style="padding:16px 20px;">
              <p style="margin:0 0 6px;color:#888;font-size:0.78rem;font-weight:bold;
                         text-transform:uppercase;letter-spacing:1px;">Or copy this link:</p>
              <p style="margin:0;color:#0d5407;font-size:0.78rem;word-break:break-all;">
                ' . htmlspecialchars($reset_link) . '
              </p>
            </td></tr>
          </table>
          <p style="color:#aaa;font-size:0.82rem;line-height:1.6;margin:0;">
            ⚠️ If you did not request a password reset, you can safely ignore this email.
          </p>
        </td>
      </tr>
      <tr>
        <td style="background:#f9fafb;border-top:1px solid #f0f0f0;padding:20px 40px;text-align:center;">
          <p style="color:#bbb;font-size:0.78rem;margin:0;">
            © ' . date('Y') . ' E-KINDER. All rights reserved.<br>
            This link expires in 24 hours for your security.
          </p>
        </td>
      </tr>
    </table>
  </td></tr>
</table>
</body>
</html>';
}
