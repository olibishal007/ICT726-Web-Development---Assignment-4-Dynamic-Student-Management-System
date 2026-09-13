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

$sql = "SELECT users.full_name,
               users.email,
               students.student_number,
               students.phone,
               students.date_of_birth
        FROM users
        LEFT JOIN students ON users.id = students.user_id
        WHERE users.id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();

$result = $stmt->get_result();
$student = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <meta name="description"
          content="Student dashboard for viewing personal information, courses, enrolments and attendance.">

    <title>Student Dashboard - Student Management System</title>

    <link rel="stylesheet" href="../css/style.css">

</head>

<body>

<header>

    <h1>Student Management System</h1>

    <p>Student Dashboard</p>

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

    <!-- Welcome Section -->
    <section class="container">

        <h2>
            Welcome,
            <?php echo htmlspecialchars($_SESSION["full_name"]); ?>
        </h2>

        <p>
            Welcome to your student dashboard. You can view your profile,
            courses, enrolments and attendance information here.
        </p>

    </section>


    <!-- Student Profile -->
    <section class="container" style="margin-top: 20px;">

        <h2>My Profile</h2>

        <?php if ($student): ?>

            <p>
                <strong>Student Number:</strong>
                <?php echo htmlspecialchars($student["student_number"] ?? "Not available"); ?>
            </p>

            <p>
                <strong>Name:</strong>
                <?php echo htmlspecialchars($student["full_name"]); ?>
            </p>

            <p>
                <strong>Email:</strong>
                <?php echo htmlspecialchars($student["email"]); ?>
            </p>

            <p>
                <strong>Phone:</strong>
                <?php echo htmlspecialchars($student["phone"] ?? "Not available"); ?>
            </p>

            <p>
                <strong>Date of Birth:</strong>
                <?php echo htmlspecialchars($student["date_of_birth"] ?? "Not available"); ?>
            </p>

            <!-- NEW BUTTON -->
            <p>
                <a href="edit_profile.php" class="btn">
                    Edit Personal Information
                </a>
            </p>

        <?php else: ?>

            <p class="error">
                Student profile information is not available.
            </p>

        <?php endif; ?>

    </section>


    <!-- Student Services -->
    <section>

        <h2>Student Services</h2>

        <div class="dashboard-links">

            <!-- My Courses -->
            <div class="dashboard-card">

                <h3>My Courses</h3>

                <p>
                    View the courses you are currently studying.
                </p>

                <a class="btn" href="courses.php">
                    View Courses
                </a>

            </div>


            <!-- My Attendance -->
            <div class="dashboard-card">

                <h3>My Attendance</h3>

                <p>
                    View your attendance records.
                </p>

                <a class="btn" href="attendance.php">
                    View Attendance
                </a>

            </div>


            <!-- My Enrolments -->
            <div class="dashboard-card">

                <h3>My Enrolments</h3>

                <p>
                    View your current course enrolments.
                </p>

                <a class="btn" href="enrolments.php">
                    View Enrolments
                </a>

            </div>


            <!-- Contact Us -->
            <div class="dashboard-card">

                <h3>Contact Us</h3>

                <p>
                    Submit an enquiry to the Student Management System.
                </p>

                <a class="btn" href="../contact.php">
                    Contact Us
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
```
