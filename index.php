<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <meta name="description" content="Student Management System providing student services, course information, enrolments, attendance and academic support.">

    <meta name="keywords" content="student management system, student services, courses, enrolment, attendance">

    <title>Home - Student Management System</title>

    <link rel="stylesheet" href="css/style.css">
</head>

<body>

<header>
    <h1>Student Management System</h1>
    <p>Welcome to the Student Management System</p>
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

        <h2>Welcome to the Student Management System</h2>

        <img src="images/students.jfif"
             alt="Students working and learning together"
             style="width: 100%; max-width: 800px; display: block; margin: 20px auto; border-radius: 8px;">

        <p>
            The Student Management System is designed to provide
            students and administrators with an easy way to access
            and manage important student information.
        </p>

        <p>
            Students can view their courses, enrolments, attendance
            and profile information. Administrators can manage student
            records, courses, enrolments, attendance and enquiries.
        </p>

    </section>

    <section>

        <h2>What We Provide</h2>

        <div class="card-container">

            <article class="card">

                <h3>Student Services</h3>

                <p>
                    Access useful student services including course,
                    enrolment and attendance information.
                </p>

                <a class="btn" href="services.php">
                    View Services
                </a>

            </article>

            <article class="card">

                <h3>Student Portal</h3>

                <p>
                    Registered students can log in to view their
                    personal information and academic records.
                </p>

                <a class="btn" href="auth/login.php">
                    Student Login
                </a>

            </article>

            <article class="card">

                <h3>Contact Us</h3>

                <p>
                    Have a question or need assistance? Submit an
                    enquiry through our contact form.
                </p>

                <a class="btn" href="contact.php">
                    Contact Us
                </a>

            </article>

        </div>

    </section>

    <section class="container" style="margin-top: 20px;">

        <h2>Easy and Accessible</h2>

        <p>
            The website has been designed with a simple layout,
            clear navigation and responsive design so that it can
            be used on different screen sizes.
        </p>

        <p>
            The system also uses role-based access so that students
            and administrators can access the features appropriate
            to their role.
        </p>

    </section>

</main>

<footer>

    <p>&copy; 2026 Student Management System</p>

</footer>

</body>
</html>