<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <meta
        name="description"
        content="Learn about the Student Management System and how it supports students and administrators."
    >

    <title>About Us - Student Management System</title>

    <link rel="stylesheet" href="css/style.css">

</head>

<body>

<header>

    <h1>Student Management System</h1>

    <p>About Us</p>

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

        <h2>About the Student Management System</h2>

        <p>
            The Student Management System is a web-based system
            designed to make student information and services
            easier to manage and access.
        </p>

        <p>
            The system provides different features for students
            and administrators. Students can access their profile,
            courses, enrolments and attendance information.
            Administrators can manage student records, courses,
            enrolments, attendance and student enquiries.
        </p>

    </section>


    <section class="card-container">

        <article class="card">

            <h2>For Students</h2>

            <p>
                Students can log in to view their personal
                information, courses, enrolments and attendance
                records.
            </p>

        </article>


        <article class="card">

            <h2>For Administrators</h2>

            <p>
                Administrators can manage student records,
                courses, enrolments, attendance and enquiries
                through the administrator dashboard.
            </p>

        </article>


        <article class="card">

            <h2>Our Purpose</h2>

            <p>
                The purpose of the system is to provide a simple,
                organised and user-friendly way to manage
                student-related information and services.
            </p>

        </article>

    </section>

</main>

<footer>

    <p>&copy; 2026 Student Management System</p>

</footer>

</body>

</html>