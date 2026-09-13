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
    header("Location: attendance.php");
    exit();
}

$attendance_id = $_GET["id"];

$stmt = $conn->prepare(
    "DELETE FROM attendance WHERE id = ?"
);

$stmt->bind_param("i", $attendance_id);

if ($stmt->execute()) {
    header("Location: attendance.php?message=Attendance+deleted+successfully");
    exit();
} else {
    echo "Failed to delete attendance.";
}

$stmt->close();
?>