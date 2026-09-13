<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <meta name="description" content="Media gallery of the Student Management System showing students, classroom, university and study activities.">

    <title>Media - Student Management System</title>

    <link rel="stylesheet" href="css/style.css">
</head>

<body>

<header>
    <h1>Student Management System</h1>
    <p>Media Gallery</p>
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

        <h2>Media Gallery</h2>

        <p>
            This page provides images related to student learning,
            university life and classroom activities.
        </p>

        <div class="card-container">

            <article class="card">
                <img src="images/students.jfif"
                     alt="Students learning together"
                     style="width: 100%; border-radius: 8px;">

                <h3>Students</h3>

                <p>
                    Students participating in learning activities
                    and working together.
                </p>
            </article>

            <article class="card">
                <img src="images/class.jfif"
                     alt="Students in a classroom"
                     style="width: 100%; border-radius: 8px;">

                <h3>Classroom Activities</h3>

                <p>
                    A classroom environment representing student
                    learning and academic activities.
                </p>
            </article>

            <article class="card">
                <img src="images/university.jfif"
                     alt="University building"
                     style="width: 100%; border-radius: 8px;">

                <h3>University</h3>

                <p>
                    The university environment where students
                    study and participate in academic activities.
                </p>
            </article>

            <article class="card">
                <img src="images/study.jfif"
                     alt="Student studying"
                     style="width: 100%; border-radius: 8px;">

                <h3>Study Activities</h3>

                <p>
                    Students using learning resources and
                    technology for their studies.
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