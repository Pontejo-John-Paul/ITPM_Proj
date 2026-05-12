<?php
session_start();
require_once __DIR__ . '/database.php';

// Guard — must be logged in as student
if (!isset($_SESSION['student_id']) || $_SESSION['role'] !== 'student') {
    header("Location: index.php");
    exit;
}

$student_id = $_SESSION['student_id'];
$first_name = $_SESSION['student_fname'] ?? 'Student';

// Fetch full student info from DB
$stmt = $conn->prepare(
    "SELECT student_id, student_uname, student_email,
            student_fname, student_mname, student_lname,
            student_age, student_sex, parent_number
     FROM students
     WHERE student_id = ?
     LIMIT 1"
);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result  = $stmt->get_result();
$student = $result->fetch_assoc();
$stmt->close();

// Build display values safely
$fname    = htmlspecialchars($student['student_fname']  ?? '—');
$mname    = htmlspecialchars($student['student_mname']  ?? '—');
$lname    = htmlspecialchars($student['student_lname']  ?? '—');
$uname    = htmlspecialchars($student['student_uname']  ?? '—');
$email    = htmlspecialchars($student['student_email']  ?? '—');
$age      = htmlspecialchars($student['student_age']    ?? '—');
$sex      = htmlspecialchars($student['student_sex']    ?? '—');
$parent   = htmlspecialchars($student['parent_number']  ?? '—');
$fullname = trim("$fname $mname $lname");
$initials = strtoupper(substr($fname, 0, 1) . substr($lname, 0, 1));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile — E-KINDER</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Fredoka+One&family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --green-dark:  #0d5407;
            --green-mid:   #1a7a10;
            --green-light: #e8f5e2;
            --cream:       #fffbf4;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Nunito', sans-serif;
            background: var(--cream);
            min-height: 100vh;
            overflow-x: hidden;
        }

        /* ── NAVBAR ── */
        .lesson-nav {
            padding: 0 28px; height: 64px; background: #fff;
            box-shadow: 0 4px 20px rgba(0,0,0,0.07);
            position: sticky; top: 0; z-index: 100;
            display: flex; align-items: center; justify-content: space-between;
        }
        .lnav-back {
            display: inline-flex; align-items: center; gap: 7px;
            background: var(--green-dark); color: #fff;
            font-family: 'Fredoka One', cursive; font-size: 0.9rem;
            padding: 8px 18px; border-radius: 50px; text-decoration: none;
            box-shadow: 0 4px 12px rgba(13,84,7,0.25);
            transition: transform 0.2s cubic-bezier(.34,1.56,.64,1), box-shadow 0.2s;
        }
        .lnav-back:hover { transform: scale(1.06); color: #fff; box-shadow: 0 6px 18px rgba(13,84,7,0.35); }
        .lnav-brand {
            font-family: 'Fredoka One', cursive;
            font-size: 1.5rem; color: var(--green-dark); letter-spacing: 1px;
        }

        /* ── PAGE WRAPPER ── */
        .profile-page {
            padding: 60px 0 80px;
            position: relative;
        }

        /* background blobs */
        .blob {
            position: fixed; border-radius: 50%;
            filter: blur(70px); opacity: 0.25; pointer-events: none; z-index: 0;
        }
        .blob-1 { width: 400px; height: 400px; background: #a8edba; top: -80px; left: -120px; }
        .blob-2 { width: 300px; height: 300px; background: #ffd6e7; bottom: 60px; right: -80px; }
        .blob-3 { width: 220px; height: 220px; background: #fff0a0; top: 40%; left: 60%; }

        /* ── PROFILE CARD ── */
        .profile-card {
            background: #fff;
            border-radius: 32px;
            box-shadow: 0 8px 40px rgba(0,0,0,0.09);
            overflow: visible;
            position: relative; z-index: 1;
            animation: cardUp 0.5s cubic-bezier(.34,1.56,.64,1) both;
        }
        @keyframes cardUp {
            from { opacity: 0; transform: translateY(40px) scale(0.97); }
            to   { opacity: 1; transform: translateY(0) scale(1); }
        }

        /* hero banner inside card */
        .profile-banner {
            background: linear-gradient(135deg, var(--green-dark) 0%, #1a7a10 60%, #51cf66 100%);
            height: 140px;
            position: relative;
            overflow: hidden;
            border-radius: 32px 32px 0 0;
        }
        .profile-banner::before {
            content: '🌟';
            position: absolute; font-size: 140px; opacity: 0.06;
            top: -20px; right: -10px; pointer-events: none;
        }
        .banner-dots {
            position: absolute; inset: 0;
            background-image: radial-gradient(circle, rgba(255,255,255,0.15) 1px, transparent 1px);
            background-size: 24px 24px;
        }

        /* avatar */
        .avatar-wrap {
            display: flex;
            justify-content: center;
            margin-top: -50px;
            position: relative;
            z-index: 2;
        }
        .avatar {
            width: 100px; height: 100px; border-radius: 50%;
            background: linear-gradient(135deg, #51cf66, var(--green-dark));
            border: 5px solid #fff;
            box-shadow: 0 8px 28px rgba(13,84,7,0.25);
            display: flex; align-items: center; justify-content: center;
            font-family: 'Fredoka One', cursive;
            font-size: 2.2rem; color: #fff; letter-spacing: 1px;
            animation: floatAvatar 3s ease-in-out infinite;
        }
        @keyframes floatAvatar {
            0%,100% { transform: translateY(0); }
            50%      { transform: translateY(-6px); }
        }

        /* card body */
        .profile-body {
            padding: 20px 36px 36px;
            text-align: center;
        }
        .profile-fullname {
            font-family: 'Fredoka One', cursive;
            font-size: 1.9rem; color: var(--green-dark);
            margin-bottom: 4px; line-height: 1.1;
        }
        .profile-username {
            font-size: 0.85rem; font-weight: 700; color: #bbb;
            letter-spacing: 1px; margin-bottom: 20px;
        }
        .profile-username span { color: var(--green-dark); opacity: 0.6; }

        /* role badge */
        .role-badge {
            display: inline-flex; align-items: center; gap: 6px;
            background: var(--green-light); color: var(--green-dark);
            font-family: 'Fredoka One', cursive; font-size: 0.8rem;
            padding: 5px 16px; border-radius: 50px;
            border: 2px solid #c8e6c9; margin-bottom: 32px;
        }

        /* divider */
        .section-divider {
            display: flex; align-items: center; gap: 12px; margin-bottom: 24px;
        }
        .section-divider-line {
            flex: 1; height: 2px;
            background: linear-gradient(90deg, #e0e0e0, transparent);
        }
        .section-divider-line.right { background: linear-gradient(90deg, transparent, #e0e0e0); }
        .section-divider-label {
            font-family: 'Fredoka One', cursive;
            font-size: 0.8rem; color: #ccc; letter-spacing: 2px; white-space: nowrap;
        }

        /* info rows */
        .info-grid {
            display: grid; grid-template-columns: 1fr 1fr;
            gap: 16px; text-align: left; margin-bottom: 32px;
        }
        @media (max-width: 576px) { .info-grid { grid-template-columns: 1fr; } }

        .info-item {
            background: #fafafa; border-radius: 16px;
            padding: 16px 18px;
            border: 2px solid #f0f0f0;
            transition: border-color 0.2s, box-shadow 0.2s, transform 0.2s;
            animation: itemFade 0.4s ease both;
        }
        .info-item:hover {
            border-color: #c8e6c9;
            box-shadow: 0 4px 16px rgba(13,84,7,0.08);
            transform: translateY(-2px);
        }
        .info-item:nth-child(1) { animation-delay: 0.1s; }
        .info-item:nth-child(2) { animation-delay: 0.15s; }
        .info-item:nth-child(3) { animation-delay: 0.2s; }
        .info-item:nth-child(4) { animation-delay: 0.25s; }
        .info-item:nth-child(5) { animation-delay: 0.3s; }
        @keyframes itemFade {
            from { opacity: 0; transform: translateY(12px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        .info-label {
            font-family: 'Fredoka One', cursive;
            font-size: 0.7rem; color: #bbb;
            letter-spacing: 1.5px; text-transform: uppercase;
            margin-bottom: 5px;
            display: flex; align-items: center; gap: 6px;
        }
        .info-label i { color: var(--green-dark); opacity: 0.6; font-size: 0.75rem; }
        .info-value {
            font-size: 0.95rem; font-weight: 800; color: #2d2d2d;
            word-break: break-word;
        }

        /* full-width item */
        .info-item.full { grid-column: 1 / -1; }

        /* edit button */
        .btn-edit {
            display: inline-flex; align-items: center; gap: 8px;
            background: var(--green-dark); color: #fff;
            font-family: 'Fredoka One', cursive; font-size: 1rem;
            padding: 13px 32px; border-radius: 50px; border: none;
            box-shadow: 0 6px 20px rgba(13,84,7,0.25); cursor: pointer;
            transition: transform 0.2s cubic-bezier(.34,1.56,.64,1), box-shadow 0.2s;
        }
        .btn-edit:hover {
            transform: scale(1.06); color: #fff;
            box-shadow: 0 10px 28px rgba(13,84,7,0.35);
        }

        /* ── EDIT MODAL ── */
        .edit-overlay {
            display: none; position: fixed; inset: 0;
            background: rgba(0,0,0,0.5); backdrop-filter: blur(6px);
            z-index: 999; align-items: center; justify-content: center;
            padding: 20px;
        }
        .edit-overlay.active { display: flex; }
        .edit-modal {
            background: #fff; border-radius: 28px;
            width: 100%; max-width: 500px;
            box-shadow: 0 30px 80px rgba(0,0,0,0.25);
            overflow: hidden;
            animation: modalPop 0.35s cubic-bezier(.34,1.56,.64,1) both;
            border-top: 6px solid var(--green-dark);
        }
        @keyframes modalPop {
            from { opacity: 0; transform: scale(0.85) translateY(30px); }
            to   { opacity: 1; transform: scale(1) translateY(0); }
        }
        .edit-modal-header {
            padding: 18px 24px 16px;
            display: flex; align-items: center; justify-content: space-between;
            border-bottom: 2px solid #f0f0f0;
        }
        .edit-modal-title {
            font-family: 'Fredoka One', cursive;
            font-size: 1.2rem; color: var(--green-dark);
        }
        .edit-modal-close {
            width: 32px; height: 32px; border-radius: 50%; border: none;
            background: #f0f0f0; color: #555; font-size: 0.9rem;
            cursor: pointer; display: flex; align-items: center; justify-content: center;
            transition: background 0.2s, transform 0.2s;
        }
        .edit-modal-close:hover { background: #ffe0e0; transform: rotate(90deg); }
        .edit-modal iframe {
            width: 100%; height: 520px; border: none; display: block;
        }
    </style>
</head>
<body>

<!-- ── NAVBAR ── -->
<nav class="lesson-nav">
    <a href="student-dashboard.php" class="lnav-back">
        <i class="fas fa-arrow-left"></i> Back
    </a>
    <div class="lnav-brand">E-KINDER</div>
</nav>

<!-- blobs -->
<div class="blob blob-1"></div>
<div class="blob blob-2"></div>
<div class="blob blob-3"></div>

<!-- ── PROFILE PAGE ── -->
<div class="profile-page">
    <div class="container" style="max-width: 640px;">

        <div class="profile-card">

            <!-- Banner -->
            <div class="profile-banner">
                <div class="banner-dots"></div>
            </div>

            <!-- Avatar — outside banner so it's not clipped -->
            <div class="avatar-wrap">
                <div class="avatar"><?php echo $initials; ?></div>
            </div>

            <!-- Body -->
            <div class="profile-body">

                <div class="profile-fullname"><?php echo $fullname; ?></div>
                <div class="profile-username">
                    <span>@</span><?php echo $uname; ?>
                </div>

                <div class="role-badge">
                    <i class="fas fa-graduation-cap"></i> Student
                </div>

                <!-- Section label -->
                <div class="section-divider">
                    <div class="section-divider-line"></div>
                    <div class="section-divider-label">PERSONAL INFO</div>
                    <div class="section-divider-line right"></div>
                </div>

                <!-- Info grid -->
                <div class="info-grid">

                    <div class="info-item">
                        <div class="info-label"><i class="fas fa-user"></i> First Name</div>
                        <div class="info-value"><?php echo $fname; ?></div>
                    </div>

                    <div class="info-item">
                        <div class="info-label"><i class="fas fa-user"></i> Last Name</div>
                        <div class="info-value"><?php echo $lname; ?></div>
                    </div>

                    <div class="info-item full">
                        <div class="info-label"><i class="fas fa-user"></i> Middle Name</div>
                        <div class="info-value"><?php echo $mname; ?></div>
                    </div>

                    <div class="info-item">
                        <div class="info-label"><i class="fas fa-birthday-cake"></i> Age</div>
                        <div class="info-value"><?php echo $age; ?> years old</div>
                    </div>

                    <div class="info-item">
                        <div class="info-label"><i class="fas fa-venus-mars"></i> Sex</div>
                        <div class="info-value"><?php echo $sex; ?></div>
                    </div>

                    <div class="info-item">
                        <div class="info-label"><i class="fas fa-at"></i> Username</div>
                        <div class="info-value"><?php echo $uname; ?></div>
                    </div>

                    <div class="info-item">
                        <div class="info-label"><i class="fas fa-envelope"></i> Email</div>
                        <div class="info-value"><?php echo $email; ?></div>
                    </div>

                    <div class="info-item full">
                        <div class="info-label"><i class="fas fa-phone"></i> Parent's Contact Number</div>
                        <div class="info-value"><?php echo $parent; ?></div>
                    </div>

                </div>

                <button class="btn-edit" onclick="openEditModal()">
                    <i class="fas fa-pen"></i> Edit Profile
                </button>

            </div>
        </div>

    </div>
</div>

<!-- ── EDIT MODAL ── -->
<div class="edit-overlay" id="editOverlay" onclick="closeOnBackdrop(event)">
    <div class="edit-modal">
        <div class="edit-modal-header">
            <div class="edit-modal-title"><i class="fas fa-pen"></i> Edit Profile</div>
            <button class="edit-modal-close" onclick="closeEditModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <iframe id="editIframe" src="" title="Edit Profile"></iframe>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
function openEditModal() {
    document.getElementById('editIframe').src = 'edit-profile.php';
    document.getElementById('editOverlay').classList.add('active');
    document.body.style.overflow = 'hidden';
}
function closeEditModal() {
    document.getElementById('editOverlay').classList.remove('active');
    document.getElementById('editIframe').src = '';
    document.body.style.overflow = '';
    // Reload page to reflect updated info
    location.reload();
}
function closeOnBackdrop(e) {
    if (e.target === document.getElementById('editOverlay')) closeEditModal();
}
document.addEventListener('keydown', e => { if (e.key === 'Escape') closeEditModal(); });

// Listen for message from iframe to close modal after save
window.addEventListener('message', e => {
    if (e.data === 'editSaved') closeEditModal();
});
</script>
</html>