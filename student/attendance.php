```php
<?php

session_start();
require_once "../config/db.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: ../auth/login.php");
    exit();
}

if ($_SESSION["role"] !== "student") {
    header("Location: ../admin/dashboard.php");
    exit();
}

$user_id = $_SESSION["user_id"];

/* Find the student record */
$sql = "SELECT id
        FROM students
        WHERE user_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();

$result = $stmt->get_result();
$student = $result->fetch_assoc();

if (!$student) {
    die("Student profile not found.");
}

$student_id = $student["id"];

/* Get attendance for this student */
$sql = "SELECT courses.course_code,
               courses.course_name,
               attendance.attendance_date,
               attendance.status
        FROM attendance
        INNER JOIN courses
            ON attendance.course_id = courses.id
        WHERE attendance.student_id = ?
        ORDER BY attendance.attendance_date DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $student_id);
$stmt->execute();

$attendance = $stmt->get_result();

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <meta name="description"
          content="View your attendance records in the Student Management System.">

    <title>My Attendance - Student Management System</title>

    <link rel="stylesheet" href="../css/style.css">

</head>

<body>

<header>

    <h1>Student Management System</h1>

    <p>My Attendance</p>

</header>

<nav aria-label="Student navigation">

    <a href="dashboard.php">Dashboard</a>

    <a href="courses.php">My Courses</a>

    <a href="attendance.php">My Attendance</a>

    <a href="enrolments.php">My Enrolments</a>

    <a href="../contact.php">Contact Us</a>

    <a href="../auth/logout.php">Logout</a>

</nav>

<main>

    <section class="container">

        <h2>My Attendance</h2>

        <p>
            View your attendance records for the courses you are enrolled in.
        </p>

        <?php if ($attendance->num_rows > 0): ?>

            <table>

                <caption>
                    Student attendance records
                </caption>

                <thead>

                    <tr>

                        <th scope="col">Course Code</th>

                        <th scope="col">Course Name</th>

                        <th scope="col">Date</th>

                        <th scope="col">Status</th>

                    </tr>

                </thead>

                <tbody>

                    <?php while ($record = $attendance->fetch_assoc()): ?>

                        <tr>

                            <td>
                                <?php
                                echo htmlspecialchars(
                                    $record["course_code"]
                                );
                                ?>
                            </td>

                            <td>
                                <?php
                                echo htmlspecialchars(
                                    $record["course_name"]
                                );
                                ?>
                            </td>

                            <td>
                                <?php
                                echo htmlspecialchars(
                                    $record["attendance_date"]
                                );
                                ?>
                            </td>

                            <td>
                                <?php
                                echo htmlspecialchars(
                                    $record["status"]
                                );
                                ?>
                            </td>

                        </tr>

                    <?php endwhile; ?>

                </tbody>

            </table>

        <?php else: ?>

            <p class="error">
                No attendance records are available.
            </p>

        <?php endif; ?>

        <br>

        <a class="btn" href="dashboard.php">
            Back to Student Dashboard
        </a>

    </section>

</main>

<footer>

    <p>&copy; 2026 Student Management System</p>

</footer>

</body>

</html>
```
