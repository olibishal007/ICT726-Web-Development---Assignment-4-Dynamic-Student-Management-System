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
    header("Location: enquiries.php");
    exit();
}

$enquiry_id = $_GET["id"];

$stmt = $conn->prepare(
    "DELETE FROM enquiries WHERE id = ?"
);

$stmt->bind_param("i", $enquiry_id);

if ($stmt->execute()) {
    header("Location: enquiries.php?message=Enquiry+deleted+successfully");
    exit();
} else {
    echo "Failed to delete enquiry.";
}

$stmt->close();
?>