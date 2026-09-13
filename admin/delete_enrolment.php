<?php
session_start();
require_once "../config/db.php";

/* Check login */
if (!isset($_SESSION["user_id"])) {
    header("Location: ../auth/login.php");
    exit();
}

/* Only admin can access */
if ($_SESSION["role"] !== "admin") {
    header("Location: ../student/dashboard.php");
    exit();
}

/* Check enrolment ID */
if (!isset($_GET["id"]) || !is_numeric($_GET["id"])) {
    header("Location: enrolments.php");
    exit();
}

$enrolment_id = $_GET["id"];

/* Delete enrolment */
$stmt = $conn->prepare(
    "DELETE FROM enrolments WHERE id = ?"
);

$stmt->bind_param("i", $enrolment_id);

if ($stmt->execute()) {
    header("Location: enrolments.php?message=Enrolment+deleted+successfully");
    exit();
} else {
    echo "Failed to delete enrolment.";
}

$stmt->close();
?>