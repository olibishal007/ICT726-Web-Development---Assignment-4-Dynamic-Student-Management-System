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

/* Get attendance records */
$sql = "SELECT attendance.id,
               students.student_number,
               users.full_name,
               courses.course_code,
               courses.course_name,
               attendance.attendance_date,
               attendance.status
        FROM attendance
        INNER JOIN students
            ON attendance.student_id = students.id
        INNER JOIN users
            ON students.user_id = users.id
        INNER JOIN courses
            ON attendance.course_id = courses.id
        ORDER BY attendance.attendance_date DESC, attendance.id DESC";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Attendance Management - Student Management System</title>

    <link rel="stylesheet" href="../css/style.css">
</head>

<body>

<header>
    <h1>Student Management System</h1>
    <p>Attendance Management</p>
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

        <h2>Manage Attendance</h2>

        <p>
            View and manage student attendance records.
        </p>

        <p>
            <a class="btn" href="add_attendance.php">
                Record Attendance
            </a>
        </p>

    </div>

    <section class="container" style="margin-top: 20px;">

        <h2>Attendance Records</h2>

        <?php if ($result && $result->num_rows > 0): ?>

            <table>

                <caption>
                    List of student attendance records
                </caption>

                <thead>

                    <tr>
                        <th scope="col">ID</th>
                        <th scope="col">Student Number</th>
                        <th scope="col">Student Name</th>
                        <th scope="col">Course Code</th>
                        <th scope="col">Course Name</th>
                        <th scope="col">Date</th>
                        <th scope="col">Status</th>
                    </tr>

                </thead>

                <tbody>

                    <?php while ($attendance = $result->fetch_assoc()): ?>

                        <tr>

                            <td>
                                <?php echo htmlspecialchars($attendance["id"]); ?>
                            </td>

                            <td>
                                <?php echo htmlspecialchars($attendance["student_number"]); ?>
                            </td>

                            <td>
                                <?php echo htmlspecialchars($attendance["full_name"]); ?>
                            </td>

                            <td>
                                <?php echo htmlspecialchars($attendance["course_code"]); ?>
                            </td>

                            <td>
                                <?php echo htmlspecialchars($attendance["course_name"]); ?>
                            </td>

                            <td>
                                <?php echo htmlspecialchars($attendance["attendance_date"]); ?>
                            </td>

                            <td>
                                <?php echo htmlspecialchars($attendance["status"]); ?>
                            </td>

                        </tr>

                    <?php endwhile; ?>

                </tbody>

            </table>

        <?php else: ?>

            <p class="error">
                No attendance records found.
            </p>

        <?php endif; ?>

    </section>

</main>

<footer>
    <p>&copy; 2026 Student Management System</p>
</footer>

</body>

</html>