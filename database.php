<?php
$conn = new mysqli("localhost", "root", "", "kinder_db");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>