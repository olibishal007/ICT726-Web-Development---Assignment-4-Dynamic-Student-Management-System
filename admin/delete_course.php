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
    header("Location: courses.php");
    exit();
}

$course_id = $_GET["id"];

/* Delete the course */
$stmt = $conn->prepare(
    "DELETE FROM courses WHERE id = ?"
);

$stmt->bind_param("i", $course_id);

if ($stmt->execute()) {
    header("Location: courses.php?message=Course+deleted+successfully");
    exit();
} else {
    echo "Failed to delete course.";
}

$stmt->close();
?>