<?php
// activity_helper.php
// Include this on every activity PHP file.
// Requires database.php to already be included.

/**
 * Mark activity as IN PROGRESS (call when student opens/starts an activity)
 */
function quizzes_start($conn, $student_id, $quizzes_id) {
    // Only set to in_progress if not yet completed
    $stmt = $conn->prepare(
        "INSERT INTO student_activity_progress
            (student_id, quizzes_id, status, attempts, updated_at)
         VALUES (?, ?, 'in_progress', 1, NOW())
         ON DUPLICATE KEY UPDATE
            status     = IF(status = 'completed', 'completed', 'in_progress'),
            attempts   = attempts + 1,
            updated_at = NOW()"
    );
    $stmt->bind_param("ii", $student_id, $quizzes_id);
    $stmt->execute();
    $stmt->close();

    // Update student_progress table
    _update_lesson_progress($conn, $student_id, $quizzes_id);
}

/**
 * Save checkpoint (call periodically during activity so student can continue)
 * $checkpoint = question number or step where student left off
 */
function activity_checkpoint($conn, $student_id, $quizzes_id, $checkpoint) {
    $stmt = $conn->prepare(
        "INSERT INTO student_activity_progress
            (student_id, quizzes_id, status, last_checkpoint, updated_at)
         VALUES (?, ?, 'in_progress', ?, NOW())
         ON DUPLICATE KEY UPDATE
            status          = IF(status = 'completed', 'completed', 'in_progress'),
            last_checkpoint = IF(status = 'completed', last_checkpoint, ?),
            updated_at      = NOW()"
    );
    $stmt->bind_param("iiii", $student_id, $quizzes_id, $checkpoint, $checkpoint);
    $stmt->execute();
    $stmt->close();
}

/**
 * Mark activity as COMPLETED with score (call when student finishes activity)
 * $score = 0-100
 */
function quizzes_complete($conn, $student_id, $quizzes_id, $score) {
    $score = max(0, min(100, (int)$score)); // clamp 0–100
    $stmt  = $conn->prepare(
        "INSERT INTO student_activity_progress
            (student_id, quizzes_id, status, score, last_checkpoint, completed_at, updated_at)
         VALUES (?, ?, 'completed', ?, 0, NOW(), NOW())
         ON DUPLICATE KEY UPDATE
            status          = 'completed',
            score           = GREATEST(score, ?),
            last_checkpoint = 0,
            completed_at    = IFNULL(completed_at, NOW()),
            updated_at      = NOW()"
    );
    // GREATEST keeps the highest score if student redoes the activity
    $stmt->bind_param("iiii", $student_id, $quizzes_id, $score, $score);
    $stmt->execute();
    $stmt->close();

    // Update student_progress table
    _update_lesson_progress($conn, $student_id, $quizzes_id);
}

/**
 * Save partial score mid-game WITHOUT marking as completed.
 * Call this after every correct answer so progress is preserved on logout.
 * $score = current running score 0-100
 */
function quizzes_save_progress($conn, $student_id, $quizzes_id, $score, $checkpoint = 0) {
    $score = max(0, min(100, (int)$score));
    $stmt  = $conn->prepare(
        "INSERT INTO student_activity_progress
            (student_id, quizzes_id, status, score, last_checkpoint, updated_at)
         VALUES (?, ?, 'in_progress', ?, ?, NOW())
         ON DUPLICATE KEY UPDATE
            status          = IF(status = 'completed', 'completed', 'in_progress'),
            score           = IF(status = 'completed', GREATEST(score, ?), ?),
            last_checkpoint = IF(status = 'completed', last_checkpoint, ?),
            updated_at      = NOW()"
    );
    $stmt->bind_param("iiiiiii", $student_id, $quizzes_id, $score, $checkpoint, $score, $score, $checkpoint);
    $stmt->execute();
    $stmt->close();
}


 /* Returns: ['status'=>..., 'score'=>..., 'last_checkpoint'=>..., 'attempts'=>...]
 */
function quizzes_get($conn, $student_id, $quizzes_id) {
    $stmt = $conn->prepare(
        "SELECT status, score, last_checkpoint, attempts
         FROM student_activity_progress
         WHERE student_id = ? AND quizzes_id = ?"
    );
    $stmt->bind_param("ii", $student_id, $quizzes_id);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    return $result ?? [
        'status'          => 'not_started',
        'score'           => 0,
        'last_checkpoint' => 0,
        'attempts'        => 0,
    ];
}

/**
 * Internal: recompute and update student_progress (overall lesson progress)
 */
function _update_lesson_progress($conn, $student_id, $quizzes_id) {
    // Get lesson_id from quizzes table
    $stmt = $conn->prepare("SELECT lesson_id FROM quizzes WHERE quizzes_id = ?");
    $stmt->bind_param("i", $quizzes_id);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$row) return;
    $lesson_id = $row['lesson_id'];

    // Count total quizzes in this lesson
    $stmt = $conn->prepare("SELECT COUNT(*) as total FROM quizzes WHERE lesson_id = ?");
    $stmt->bind_param("i", $lesson_id);
    $stmt->execute();
    $total = $stmt->get_result()->fetch_assoc()['total'];
    $stmt->close();

    // Count completed quizzes by this student in this lesson
    $stmt = $conn->prepare(
        "SELECT COUNT(*) as completed
         FROM student_activity_progress sap
         JOIN quizzes a ON sap.quizzes_id = a.quizzes_id
         WHERE sap.student_id = ? AND a.lesson_id = ? AND sap.status = 'completed'"
    );
    $stmt->bind_param("ii", $student_id, $lesson_id);
    $stmt->execute();
    $completed = $stmt->get_result()->fetch_assoc()['completed'];
    $stmt->close();

    $percent = $total > 0 ? round(($completed / $total) * 100) : 0;

    // Upsert into student_progress
    $stmt = $conn->prepare(
        "INSERT INTO student_progress
            (student_id, lesson_id, completed, total, percent, updated_at)
         VALUES (?, ?, ?, ?, ?, NOW())
         ON DUPLICATE KEY UPDATE
            completed  = VALUES(completed),
            total      = VALUES(total),
            percent    = VALUES(percent),
            updated_at = NOW()"
    );
    $stmt->bind_param("iiiii", $student_id, $lesson_id, $completed, $total, $percent);
    $stmt->execute();
    $stmt->close();
}
?>