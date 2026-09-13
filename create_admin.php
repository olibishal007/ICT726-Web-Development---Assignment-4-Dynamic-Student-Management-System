<?php

require_once "config/db.php";

$full_name = "System Administrator";
$email = "admin@student.com";
$password = "Admin123";

$hashed_password = password_hash($password, PASSWORD_DEFAULT);

$stmt = $conn->prepare(
    "INSERT INTO users (full_name, email, password, role)
     VALUES (?, ?, ?, 'admin')"
);

$stmt->bind_param(
    "sss",
    $full_name,
    $email,
    $hashed_password
);

if ($stmt->execute()) {
    echo "Admin account created successfully.";
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();

?>