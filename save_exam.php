<?php
session_start();
require_once 'database.php';

if (!isset($_SESSION['student_id'])) exit;

$student_id = $_SESSION['student_id'];
$score      = $_POST['score'] ?? 0;
$lesson_id  = $_POST['lesson_id'] ?? 0;

// 🔥 SAVE SA final_exam_result (universal)
$stmt = $conn->prepare("
INSERT INTO final_exam_result (lesson_id, student_id, score, date_time_completed)
VALUES (?, ?, ?, NOW())
");
$stmt->bind_param("iii", $lesson_id, $student_id, $score);
$stmt->execute();

echo "ok";