<?php
// navbar.php — include this on every page
// Requires session_start() and database.php to already be called before including this file.

$first_name   = $_SESSION['student_fname'] ?? 'Student';
$current_page = basename($_SERVER['PHP_SELF']); // e.g. "lessons.php"

$nav_links = [
    'student-dashboard.php' => 'Home',
    'lessons.php'           => 'Lessons',
    'activities.php'        => 'Quizzes',
    'badges.php'            => 'Badges',
    'about.php'             => 'About',
];
?>
<nav class="navbar navbar-expand-lg navbar-light bg-light px-4">
    <div class="container-fluid">
        <a class="navbar-brand" href="student-dashboard.php">E-KINDER</a>
        <button class="navbar-toggler border-0" type="button"
                data-bs-toggle="collapse" data-bs-target="#navbarEKinder">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse justify-content-end" id="navbarEKinder">
            <ul class="navbar-nav align-items-center gap-1">

                <?php foreach ($nav_links as $href => $label): ?>
                <li class="nav-item">
                    <a class="nav-link <?php echo $current_page === $href ? 'active' : ''; ?>"
                       href="<?php echo $href; ?>">
                        <?php echo $label; ?>
                    </a>
                </li>
                <?php endforeach; ?>

                <!-- User dropdown -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle p-0 user-dropdown-toggle"
                       href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-user-circle user-icon"></i>
                        <span class="user-name"><?php echo htmlspecialchars($first_name); ?></span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end shadow border-0"
                        style="border-radius:14px">
                        <li>
                            <a class="dropdown-item fw-bold" href="profile.php?from=<?php echo urlencode($current_page); ?>">
                                <i class="fas fa-id-card me-2 text-muted"></i>Profile
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item text-danger fw-bold" href="logout.php">
                                <i class="fas fa-right-from-bracket me-2"></i>Logout
                            </a>
                        </li>
                    </ul>
                </li>

            </ul>
        </div>
    </div>
</nav>