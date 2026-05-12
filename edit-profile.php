<?php
session_start();
require_once __DIR__ . '/database.php';

// Guard
if (!isset($_SESSION['student_id']) || $_SESSION['role'] !== 'student') {
    exit('Unauthorized');
}

$student_id = $_SESSION['student_id'];
$success    = false;
$error      = '';

// Where to go back after save
$from = $_GET['from'] ?? $_POST['from'] ?? 'student-dashboard.php';
$allowed = ['student-dashboard.php','lessons.php','activities.php','badges.php','about.php'];
if (!in_array($from, $allowed)) $from = 'student-dashboard.php';

// ── HANDLE FORM SUBMIT ──
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fname  = trim($_POST['student_fname']   ?? '');
    $mname  = trim($_POST['student_mname']   ?? '');
    $lname  = trim($_POST['student_lname']   ?? '');
    $uname  = trim($_POST['student_uname']   ?? '');
    $email  = trim($_POST['student_email']   ?? '');
    $age    = intval($_POST['student_age']   ?? 0);
    $sex    = trim($_POST['student_sex']     ?? '');
    $parent = trim($_POST['parent_number']   ?? '');

    if (!$fname || !$lname || !$uname || !$email) {
        $error = 'Please fill in all required fields.';
    } else {
        // Check if username or email is taken by another student
        $chk = $conn->prepare(
            "SELECT student_id FROM students
             WHERE (student_uname = ? OR student_email = ?)
             AND student_id != ? LIMIT 1"
        );
        $chk->bind_param("ssi", $uname, $email, $student_id);
        $chk->execute();
        $chk->store_result();

        if ($chk->num_rows > 0) {
            $error = 'Username or email is already taken by another account.';
        } else {
            $stmt = $conn->prepare(
                "UPDATE students SET
                    student_fname   = ?,
                    student_mname   = ?,
                    student_lname   = ?,
                    student_uname   = ?,
                    student_email   = ?,
                    student_age     = ?,
                    student_sex     = ?,
                    parent_number   = ?
                 WHERE student_id = ?"
            );
            $stmt->bind_param(
                "sssssissi",
                $fname, $mname, $lname,
                $uname, $email, $age,
                $sex, $parent, $student_id
            );
            $stmt->execute();
            $stmt->close();

            // Update session name
            $_SESSION['student_fname'] = $fname;
            $_SESSION['student_name']  = $fname . ' ' . $lname;

            $success = true;
        }
        $chk->close();
    }
}

// ── FETCH CURRENT DATA ──
$stmt = $conn->prepare(
    "SELECT student_fname, student_mname, student_lname,
            student_uname, student_email,
            student_age, student_sex, parent_number
     FROM students WHERE student_id = ? LIMIT 1"
);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result  = $stmt->get_result();
$student = $result->fetch_assoc();
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
    <link href="https://fonts.googleapis.com/css2?family=Fredoka+One&family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <style>
        :root { --green-dark: #0d5407; --green-light: #e8f5e2; }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        html, body {
            font-family: 'Nunito', sans-serif;
            background: #fff; height: 100%;
            overflow-y: auto;
        }
        body { padding: 24px 28px 28px; }

        .alert {
            border-radius: 12px; padding: 11px 16px;
            font-size: 0.85rem; font-weight: 700;
            margin-bottom: 18px;
            display: flex; align-items: center; gap: 8px;
        }
        .alert-success { background: #f0fdf4; border: 1.5px solid #86efac; color: #166534; }
        .alert-error   { background: #fff0f0; border: 1.5px solid #fca5a5; color: #dc2626;
                         animation: shake 0.4s ease; }
        @keyframes shake {
            0%,100%{transform:translateX(0)} 25%{transform:translateX(-6px)} 75%{transform:translateX(6px)}
        }

        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-bottom: 14px; }
        .form-row.full { grid-template-columns: 1fr; }
        .form-group { display: flex; flex-direction: column; gap: 5px; }
        .form-label {
            font-family: 'Fredoka One', cursive; font-size: 0.75rem;
            color: #888; letter-spacing: 1px; text-transform: uppercase;
        }
        .form-label span { color: #e53935; }
        .input-wrap { position: relative; }
        .input-icon {
            position: absolute; left: 13px; top: 50%;
            transform: translateY(-50%);
            color: #d1d5db; font-size: 0.85rem; pointer-events: none;
            transition: color 0.2s;
        }
        .input-wrap:focus-within .input-icon { color: var(--green-dark); }
        .form-input, .form-select {
            width: 100%; padding: 11px 14px 11px 38px;
            border: 2px solid #e8e8e8; border-radius: 12px;
            font-family: 'Nunito', sans-serif; font-size: 0.9rem;
            font-weight: 700; color: #1a1a2e; background: #fafafa;
            outline: none;
            transition: border-color 0.2s, box-shadow 0.2s;
        }
        .form-select { padding-left: 38px; cursor: pointer; }
        .form-input:focus, .form-select:focus {
            border-color: var(--green-dark); background: #fff;
            box-shadow: 0 0 0 4px rgba(13,84,7,0.08);
        }
        .form-input::placeholder { color: #c4c4c4; font-weight: 600; }

        .btn-save {
            width: 100%; padding: 13px;
            background: var(--green-dark); color: #fff;
            border: none; border-radius: 14px;
            font-family: 'Fredoka One', cursive; font-size: 1rem;
            cursor: pointer; margin-top: 6px;
            box-shadow: 0 6px 20px rgba(13,84,7,0.25);
            display: flex; align-items: center; justify-content: center; gap: 8px;
            transition: transform 0.2s cubic-bezier(.34,1.56,.64,1), box-shadow 0.2s;
        }
        .btn-save:hover {
            transform: translateY(-2px) scale(1.02);
            box-shadow: 0 10px 28px rgba(13,84,7,0.35);
        }
        .btn-save:active { transform: scale(0.98); }

        /* ── CONFIRMATION DIALOG ── */
        .confirm-overlay {
            display: none; position: fixed; inset: 0;
            background: rgba(0,0,0,0.45); backdrop-filter: blur(4px);
            z-index: 999; align-items: center; justify-content: center; padding: 20px;
        }
        .confirm-overlay.active { display: flex; }
        .confirm-box {
            background: #fff; border-radius: 24px; padding: 32px 28px 24px;
            text-align: center; max-width: 340px; width: 100%;
            box-shadow: 0 20px 60px rgba(0,0,0,0.2);
            border-top: 5px solid var(--green-dark);
            animation: popIn 0.3s cubic-bezier(.34,1.56,.64,1) both;
        }
        @keyframes popIn {
            from { opacity: 0; transform: scale(0.8) translateY(20px); }
            to   { opacity: 1; transform: scale(1) translateY(0); }
        }
        .confirm-emoji { font-size: 3rem; margin-bottom: 12px; display: block;
            animation: wobble 0.6s ease; }
        @keyframes wobble {
            0%,100%{transform:rotate(0)} 25%{transform:rotate(-10deg)} 75%{transform:rotate(10deg)}
        }
        .confirm-title {
            font-family: 'Fredoka One', cursive; font-size: 1.25rem;
            color: var(--green-dark); margin-bottom: 8px;
        }
        .confirm-text {
            font-size: 0.85rem; font-weight: 700; color: #aaa;
            margin-bottom: 24px; line-height: 1.5;
        }
        .confirm-btns { display: flex; gap: 10px; }
        .confirm-btn {
            flex: 1; padding: 11px; border-radius: 50px; border: none;
            font-family: 'Fredoka One', cursive; font-size: 0.95rem; cursor: pointer;
            transition: transform 0.2s cubic-bezier(.34,1.56,.64,1), box-shadow 0.2s;
        }
        .confirm-btn.yes {
            background: var(--green-dark); color: #fff;
            box-shadow: 0 4px 14px rgba(13,84,7,0.25);
        }
        .confirm-btn.yes:hover { transform: scale(1.05); box-shadow: 0 6px 20px rgba(13,84,7,0.35); }
        .confirm-btn.no {
            background: #f0f0f0; color: #888;
        }
        .confirm-btn.no:hover { transform: scale(1.05); background: #ffe0e0; color: #e53935; }

        /* ── LOADING BAR ── */
        .loading-overlay {
            display: none; position: fixed; inset: 0;
            background: rgba(255,255,255,0.92); backdrop-filter: blur(4px);
            z-index: 1000; flex-direction: column;
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
            border-radius: 50px;
            transition: width 0.12s linear;
        }
        .loading-child {
            position: absolute; bottom: 10px;
            font-size: 1.6rem;
            transition: left 0.12s linear;
            left: 0%;
            filter: drop-shadow(0 2px 4px rgba(0,0,0,0.15));
            display: inline-block;
            transform: scaleX(-1);
        }
        .loading-label {
            font-family: 'Fredoka One', cursive;
            font-size: 1rem; color: var(--green-dark); letter-spacing: 0.5px;
            transition: opacity 0.2s;
        }
    </style>
</head>
<body>

<?php if ($success): ?>
    <div class="alert alert-success">
        <i class="fas fa-check-circle"></i> Profile updated successfully!
    </div>
    <script>
        // Tell parent to close modal after short delay
        setTimeout(() => window.parent.postMessage('editSaved', '*'), 1200);
    </script>
<?php endif; ?>

<?php if ($error): ?>
    <div class="alert alert-error">
        <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?>
    </div>
<?php endif; ?>

<form method="POST" action="edit-profile.php?from=<?php echo urlencode($from); ?>" id="editForm">
<input type="hidden" name="from" value="<?php echo htmlspecialchars($from); ?>">

    <div class="form-row">
        <div class="form-group">
            <label class="form-label">First Name <span>*</span></label>
            <div class="input-wrap">
                <i class="fas fa-user input-icon"></i>
                <input type="text" name="student_fname" class="form-input"
                       value="<?php echo htmlspecialchars($student['student_fname'] ?? ''); ?>"
                       placeholder="First name" required>
            </div>
        </div>
        <div class="form-group">
            <label class="form-label">Last Name <span>*</span></label>
            <div class="input-wrap">
                <i class="fas fa-user input-icon"></i>
                <input type="text" name="student_lname" class="form-input"
                       value="<?php echo htmlspecialchars($student['student_lname'] ?? ''); ?>"
                       placeholder="Last name" required>
            </div>
        </div>
    </div>

    <div class="form-row full">
        <div class="form-group">
            <label class="form-label">Middle Name</label>
            <div class="input-wrap">
                <i class="fas fa-user input-icon"></i>
                <input type="text" name="student_mname" class="form-input"
                       value="<?php echo htmlspecialchars($student['student_mname'] ?? ''); ?>"
                       placeholder="Middle name">
            </div>
        </div>
    </div>

    <div class="form-row">
        <div class="form-group">
            <label class="form-label">Age</label>
            <div class="input-wrap">
                <i class="fas fa-birthday-cake input-icon"></i>
                <input type="number" name="student_age" class="form-input"
                       value="<?php echo htmlspecialchars($student['student_age'] ?? ''); ?>"
                       placeholder="Age" min="1" max="20">
            </div>
        </div>
        <div class="form-group">
            <label class="form-label">Sex</label>
            <div class="input-wrap">
                <i class="fas fa-venus-mars input-icon"></i>
                <select name="student_sex" class="form-select">
                    <option value="">Select...</option>
                    <option value="Male"   <?php echo ($student['student_sex'] ?? '') === 'Male'   ? 'selected' : ''; ?>>Male</option>
                    <option value="Female" <?php echo ($student['student_sex'] ?? '') === 'Female' ? 'selected' : ''; ?>>Female</option>
                </select>
            </div>
        </div>
    </div>

    <div class="form-row">
        <div class="form-group">
            <label class="form-label">Username <span>*</span></label>
            <div class="input-wrap">
                <i class="fas fa-at input-icon"></i>
                <input type="text" name="student_uname" class="form-input"
                       value="<?php echo htmlspecialchars($student['student_uname'] ?? ''); ?>"
                       placeholder="Username" required>
            </div>
        </div>
        <div class="form-group">
            <label class="form-label">Email <span>*</span></label>
            <div class="input-wrap">
                <i class="fas fa-envelope input-icon"></i>
                <input type="email" name="student_email" class="form-input"
                       value="<?php echo htmlspecialchars($student['student_email'] ?? ''); ?>"
                       placeholder="Email" required>
            </div>
        </div>
    </div>

    <div class="form-row full">
        <div class="form-group">
            <label class="form-label">Parent's Contact Number</label>
            <div class="input-wrap">
                <i class="fas fa-phone input-icon"></i>
                <input type="text" name="parent_number" class="form-input"
                       value="<?php echo htmlspecialchars($student['parent_number'] ?? ''); ?>"
                       placeholder="e.g. 09XX XXX XXXX">
            </div>
        </div>
    </div>

    <button type="button" class="btn-save" onclick="showConfirm()">
        <i class="fas fa-save"></i> Save Changes
    </button>

</form>

<!-- ── CONFIRMATION DIALOG ── -->
<div class="confirm-overlay" id="confirmOverlay">
    <div class="confirm-box">
        <span class="confirm-emoji">🤔</span>
        <div class="confirm-title">Save Changes?</div>
        <div class="confirm-text">Are you sure you want to update your profile information?</div>
        <div class="confirm-btns">
            <button class="confirm-btn no" onclick="hideConfirm()">
                ✖ Cancel
            </button>
            <button class="confirm-btn yes" onclick="doSave()">
                ✔ Yes, Save!
            </button>
        </div>
    </div>
</div>

<!-- ── LOADING OVERLAY ── -->
<div class="loading-overlay" id="loadingOverlay">
    <div class="loading-child-wrap">
        <div class="loading-child" id="loadingChild">🏃</div>
        <div class="loading-track">
            <div class="loading-bar" id="loadingBar"></div>
        </div>
    </div>
    <div class="loading-label" id="loadingLabel">Saving your changes...</div>
</div>

<script>
function showConfirm() {
    document.getElementById('confirmOverlay').classList.add('active');
}
function hideConfirm() {
    document.getElementById('confirmOverlay').classList.remove('active');
}
function doSave() {
    hideConfirm();
    startLoading();
}

function startLoading() {
    const overlay  = document.getElementById('loadingOverlay');
    const bar      = document.getElementById('loadingBar');
    const child    = document.getElementById('loadingChild');
    const label    = document.getElementById('loadingLabel');
    const messages = ['Saving your changes...', 'Almost there...', 'Just a moment! 🌟'];

    overlay.classList.add('active');

    let progress = 0;
    let msgIdx   = 0;

    const interval = setInterval(() => {
        // Slow start, fast middle, slow end
        const step = progress < 30 ? 2 : progress < 80 ? 4 : 1;
        progress = Math.min(progress + step, 95);

        bar.style.width    = progress + '%';
        child.style.left   = Math.max(0, progress - 8) + '%';

        // Cycle messages
        const newMsg = Math.floor(progress / 35);
        if (newMsg !== msgIdx && newMsg < messages.length) {
            msgIdx = newMsg;
            label.style.opacity = '0';
            setTimeout(() => {
                label.textContent  = messages[msgIdx];
                label.style.opacity = '1';
            }, 200);
        }

        if (progress >= 95) {
            clearInterval(interval);
            // Now actually submit the form
            document.getElementById('editForm').submit();
        }
    }, 60);
}
</script>
</html>