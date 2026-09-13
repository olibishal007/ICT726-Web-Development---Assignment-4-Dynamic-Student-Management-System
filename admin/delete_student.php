<?php
session_start();
require_once "../config/db.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: ../auth/login.php");
    exit();
}

if ($_SESSION["role"] !== "admin") {
    header("Location: ../student/dashboard.php");
    exit();
}

if (!isset($_GET["id"]) || !is_numeric($_GET["id"])) {
    header("Location: students.php");
    exit();
}

$student_id = $_GET["id"];

/* Delete the student */
$stmt = $conn->prepare(
    "DELETE FROM students WHERE id = ?"
);

$stmt->bind_param("i", $student_id);

if ($stmt->execute()) {
    header("Location: students.php?message=Student+deleted+successfully");
    exit();
} else {
    echo "Failed to delete student.";
}

$stmt->close();
?>