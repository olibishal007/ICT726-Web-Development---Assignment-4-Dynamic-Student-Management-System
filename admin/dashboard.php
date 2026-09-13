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
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Admin Dashboard - Student Management System</title>

    <link rel="stylesheet" href="../css/style.css">

</head>

<body>

<header>

    <h1>Student Management System</h1>

    <p>Administrator Dashboard</p>

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

        <h2>
            Welcome,
            <?php echo htmlspecialchars($_SESSION["full_name"]); ?>
        </h2>

        <p>
            You are logged in as an administrator.
        </p>

    </div>


    <section>

        <h2>Management Services</h2>

        <div class="dashboard-links">


            <div class="dashboard-card">

                <h3>Student Management</h3>

                <p>
                    Add, view, edit and delete student records.
                </p>

                <a class="btn" href="students.php">
                    Manage Students
                </a>

            </div>


            <div class="dashboard-card">

                <h3>Course Management</h3>

                <p>
                    Manage available courses.
                </p>

                <a class="btn" href="courses.php">
                    Manage Courses
                </a>

            </div>


            <div class="dashboard-card">

                <h3>Enrolment Management</h3>

                <p>
                    Manage student course enrolments.
                </p>

                <a class="btn" href="enrolments.php">
                    Manage Enrolments
                </a>

            </div>


            <div class="dashboard-card">

                <h3>Attendance Management</h3>

                <p>
                    Record and manage student attendance.
                </p>

                <a class="btn" href="attendance.php">
                    Manage Attendance
                </a>

            </div>


            <div class="dashboard-card">

                <h3>Student Enquiries</h3>

                <p>
                    View and manage enquiries submitted by students.
                </p>

                <a class="btn" href="enquiries.php">
                    View Enquiries
                </a>

            </div>

        </div>

    </section>

</main>

<footer>

    <p>
        &copy; 2026 Student Management System
    </p>

</footer>

</body>

</html>