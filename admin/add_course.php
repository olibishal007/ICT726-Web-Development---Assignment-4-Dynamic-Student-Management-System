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

// Create CSRF token
if (empty($_SESSION["csrf_token"])) {
    $_SESSION["csrf_token"] = bin2hex(random_bytes(32));
}

$message = "";
$message_type = "";

$course_code = "";
$course_name = "";
$description = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Check CSRF token
    $csrf_token = $_POST["csrf_token"] ?? "";

    if (
        empty($csrf_token) ||
        !hash_equals($_SESSION["csrf_token"], $csrf_token)
    ) {
        $message = "Invalid form submission. Please try again.";
        $message_type = "error";

    } else {

        $course_code = trim($_POST["course_code"] ?? "");
        $course_name = trim($_POST["course_name"] ?? "");
        $description = trim($_POST["description"] ?? "");

        // Validate required fields
        if (empty($course_code) || empty($course_name)) {

            $message = "Please fill in all required fields.";
            $message_type = "error";

        } else {

            // Check if course code already exists
            $check_sql = "SELECT id FROM courses WHERE course_code = ?";

            $check_stmt = $conn->prepare($check_sql);
            $check_stmt->bind_param("s", $course_code);
            $check_stmt->execute();

            $check_result = $check_stmt->get_result();

            if ($check_result->num_rows > 0) {

                $message = "This course code already exists.";
                $message_type = "error";

            } else {

                // Insert course
                $sql = "INSERT INTO courses
                        (course_code, course_name, description)
                        VALUES (?, ?, ?)";

                $stmt = $conn->prepare($sql);

                $stmt->bind_param(
                    "sss",
                    $course_code,
                    $course_name,
                    $description
                );

                if ($stmt->execute()) {

                    $message = "Course added successfully.";
                    $message_type = "success";

                    // Clear form
                    $course_code = "";
                    $course_name = "";
                    $description = "";

                    // Generate a new CSRF token
                    $_SESSION["csrf_token"] = bin2hex(
                        random_bytes(32)
                    );

                } else {

                    $message = "Something went wrong. Please try again.";
                    $message_type = "error";
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <meta name="description"
          content="Add a course to the Student Management System.">

    <title>Add Course - Student Management System</title>

    <link rel="stylesheet" href="../css/style.css">

</head>

<body>

<header>

    <h1>Student Management System</h1>

    <p>Add Course</p>

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

    <section class="container">

        <h2>Add New Course</h2>

        <p>
            Enter the course details below to add a new course.
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

            <!-- CSRF protection -->
            <input
                type="hidden"
                name="csrf_token"
                value="<?php echo htmlspecialchars($_SESSION["csrf_token"]); ?>"
            >

            <label for="course_code">
                Course Code
            </label>

            <input
                type="text"
                id="course_code"
                name="course_code"
                value="<?php echo htmlspecialchars($course_code); ?>"
                required
            >

            <label for="course_name">
                Course Name
            </label>

            <input
                type="text"
                id="course_name"
                name="course_name"
                value="<?php echo htmlspecialchars($course_name); ?>"
                required
            >

            <label for="description">
                Description
            </label>

            <textarea
                id="description"
                name="description"
                rows="6"
            ><?php echo htmlspecialchars($description); ?></textarea>

            <br><br>

            <button type="submit">
                Add Course
            </button>

            <a class="btn" href="courses.php">
                Cancel
            </a>

        </form>

    </section>

</main>

<footer>

    <p>&copy; 2026 Student Management System</p>

</footer>

</body>

</html>