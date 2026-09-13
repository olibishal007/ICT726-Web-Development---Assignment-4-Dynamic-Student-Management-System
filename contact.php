<?php
session_start();
require_once "config/db.php";

$message = "";
$message_type = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $name = trim($_POST["name"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $subject = trim($_POST["subject"] ?? "");
    $user_message = trim($_POST["message"] ?? "");

    if (
        empty($name) ||
        empty($email) ||
        empty($subject) ||
        empty($user_message)
    ) {
        $message = "Please fill in all fields.";
        $message_type = "error";

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $message = "Please enter a valid email address.";
        $message_type = "error";

    } else {

        $sql = "INSERT INTO enquiries
                (name, email, subject, message)
                VALUES (?, ?, ?, ?)";

        $stmt = $conn->prepare($sql);

        $stmt->bind_param(
            "ssss",
            $name,
            $email,
            $subject,
            $user_message
        );

        if ($stmt->execute()) {

            $message = "Thank you. Your enquiry has been submitted successfully.";
            $message_type = "success";

            $name = "";
            $email = "";
            $subject = "";
            $user_message = "";

        } else {

            $message = "Something went wrong. Please try again.";
            $message_type = "error";
        }
    }
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

    <meta
        name="description"
        content="Contact the Student Management System for student-related enquiries and support."
    >

    <title>Contact Us - Student Management System</title>

    <link rel="stylesheet" href="css/style.css">

</head>

<body>

<header>

    <h1>Student Management System</h1>

    <p>Contact Us</p>

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

    <div class="container">

        <h2>Contact Us</h2>

        <p>
            If you have a question or need assistance,
            please complete the form below.
        </p>

        <?php if (!empty($message)): ?>

            <div
                class="<?php echo $message_type; ?>"
                role="alert"
            >
                <?php echo htmlspecialchars($message); ?>
            </div>

        <?php endif; ?>

        <form method="POST" action="">

            <label for="name">
                Name
            </label>

            <input
                type="text"
                id="name"
                name="name"
                value="<?php echo htmlspecialchars($name ?? ""); ?>"
                required
            >

            <label for="email">
                Email
            </label>

            <input
                type="email"
                id="email"
                name="email"
                value="<?php echo htmlspecialchars($email ?? ""); ?>"
                required
            >

            <label for="subject">
                Subject
            </label>

            <input
                type="text"
                id="subject"
                name="subject"
                value="<?php echo htmlspecialchars($subject ?? ""); ?>"
                required
            >

            <label for="message">
                Message
            </label>

            <textarea
                id="message"
                name="message"
                rows="7"
                required
            ><?php echo htmlspecialchars($user_message ?? ""); ?></textarea>

            <br><br>

            <button type="submit">
                Submit Enquiry
            </button>

        </form>

    </div>
    <hr>

<h2>Privacy Notice</h2>

<p>
    The Student Management System collects information such as
    names, email addresses and student details to provide
    student-related services.
</p>

<p>
    Information stored in the system is used only for
    educational and student management purposes. Access to
    student information is restricted according to user roles.
</p>

<p>
    Passwords are securely stored using password hashing.
    Users should not submit unnecessary sensitive or private
    information through the contact form.
</p>

<p>
    By using this website, users should ensure that the
    information they provide is accurate and appropriate.
</p>

</main>

<footer>

    <p>&copy; 2026 Student Management System</p>

</footer>

</body>

</html>