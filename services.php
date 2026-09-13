<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <meta name="description" content="Student services including course information, enrolments, attendance and academic support.">

    <title>Student Services - Student Management System</title>

    <link rel="stylesheet" href="css/style.css">
</head>

<body>

<header>
    <h1>Student Management System</h1>
    <p>Student Services</p>
</header>

<nav aria-label="Main navigation">
    <a href="index.php">Home</a>
    <a href="about.php">About Us</a>
    <a href="services.php">Student Services</a>
    <a href="media.php">Media</a>
    <a href="contact.php">Contact Us</a>

    <?php if (isset($_SESSION["user_id"])): ?>

        <?php if ($_SESSION["role"] === "admin"): ?>
            <a href="admin/dashboard.php">Dashboard</a>
        <?php else: ?>
            <a href="student/dashboard.php">Dashboard</a>
        <?php endif; ?>

        <a href="auth/logout.php">Logout</a>

    <?php else: ?>

        <a href="auth/login.php">Login</a>

    <?php endif; ?>
</nav>

<main>

    <section class="container">
        <h2>Student Services</h2>

        <p>
            The Student Management System provides several services
            to help students access and manage important information.
        </p>

        <div class="card-container">

            <article class="card">
                <h3>Course Information</h3>

                <p>
                    Students can view information about available courses,
                    including course codes, course names and descriptions.
                </p>
            </article>

            <article class="card">
                <h3>Enrolment Information</h3>

                <p>
                    Students can view their course enrolments, enrolment
                    dates and current enrolment status.
                </p>
            </article>

            <article class="card">
                <h3>Attendance Records</h3>

                <p>
                    Students can view their attendance records for
                    the courses they are enrolled in.
                </p>
            </article>

            <article class="card">
                <h3>Student Profile</h3>

                <p>
                    Students can access their basic profile information,
                    including their student number and contact details.
                </p>
            </article>

            <article class="card">
                <h3>Student Enquiries</h3>

                <p>
                    Students can submit questions or requests through
                    the Contact Us form.
                </p>
            </article>

            <article class="card">
                <h3>Administrative Management</h3>

                <p>
                    Authorised administrators can manage student records,
                    courses, enrolments, attendance and enquiries.
                </p>
            </article>

        </div>
    </section>

</main>

<footer>
    <p>&copy; 2026 Student Management System</p>
</footer>

</body>
</html>