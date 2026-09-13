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

$enquiry_id = $_GET["id"] ?? "";

if (!is_numeric($enquiry_id)) {
    header("Location: enquiries.php");
    exit();
}

/* Get enquiry details */
$sql = "SELECT id, name, email, subject, message, created_at
        FROM enquiries
        WHERE id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $enquiry_id);
$stmt->execute();

$result = $stmt->get_result();
$enquiry = $result->fetch_assoc();

if (!$enquiry) {
    die("Enquiry not found.");
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>View Enquiry - Student Management System</title>

    <link rel="stylesheet" href="../css/style.css">

</head>

<body>

<header>

    <h1>Student Management System</h1>

    <p>Student Enquiries</p>

</header>

<nav aria-label="Main navigation">

    <a href="dashboard.php">Dashboard</a>
    <a href="students.php">Students</a>
    <a href="courses.php">Courses</a>
    <a href="enrolments.php">Enrolments</a>
    <a href="attendance.php">Attendance</a>
    <a href="enquiries.php">Enquiries</a>
    <a href="../auth/logout.php">Logout</a>

</nav>

<main>

    <div class="container">

        <h2>View Enquiry</h2>

        <p>
            Full details of the submitted enquiry are shown below.
        </p>

        <p>
            <strong>Enquiry ID:</strong>
            <?php echo htmlspecialchars($enquiry["id"]); ?>
        </p>

        <p>
            <strong>Name:</strong>
            <?php echo htmlspecialchars($enquiry["name"]); ?>
        </p>

        <p>
            <strong>Email:</strong>
            <?php echo htmlspecialchars($enquiry["email"]); ?>
        </p>

        <p>
            <strong>Subject:</strong>
            <?php echo htmlspecialchars($enquiry["subject"]); ?>
        </p>

        <p>
            <strong>Date Submitted:</strong>
            <?php echo htmlspecialchars($enquiry["created_at"]); ?>
        </p>

        <h3>Message</h3>

        <div class="card">

            <p>
                <?php
                echo nl2br(
                    htmlspecialchars($enquiry["message"])
                );
                ?>
            </p>

        </div>

        <br>

        <a
            class="btn"
            href="enquiries.php"
        >
            Back to Enquiries
        </a>

    </div>

</main>

<footer>

    <p>&copy; 2026 Student Management System</p>

</footer>

</body>

</html>