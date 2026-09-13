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

$message = "";
$message_type = "";

$course_id = $_GET["id"] ?? "";

if (!is_numeric($course_id)) {
    die("Invalid course ID.");
}

/* Get existing course information */
$sql = "SELECT id, course_code, course_name, description
        FROM courses
        WHERE id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $course_id);
$stmt->execute();

$result = $stmt->get_result();
$course = $result->fetch_assoc();

if (!$course) {
    die("Course not found.");
}

/* Update course */
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $course_code = trim($_POST["course_code"] ?? "");
    $course_name = trim($_POST["course_name"] ?? "");
    $description = trim($_POST["description"] ?? "");

    if (empty($course_code) || empty($course_name)) {

        $message = "Course code and course name are required.";
        $message_type = "error";

    } else {

        /* Check whether another course already uses this code */
        $check = $conn->prepare(
            "SELECT id FROM courses
             WHERE course_code = ?
             AND id != ?"
        );

        $check->bind_param("si", $course_code, $course_id);
        $check->execute();

        $check_result = $check->get_result();

        if ($check_result->num_rows > 0) {

            $message = "This course code is already used by another course.";
            $message_type = "error";

        } else {

            $update = $conn->prepare(
                "UPDATE courses
                 SET course_code = ?,
                     course_name = ?,
                     description = ?
                 WHERE id = ?"
            );

            $update->bind_param(
                "sssi",
                $course_code,
                $course_name,
                $description,
                $course_id
            );

            if ($update->execute()) {

                $message = "Course updated successfully.";
                $message_type = "success";

                /* Update displayed values */
                $course["course_code"] = $course_code;
                $course["course_name"] = $course_name;
                $course["description"] = $description;

            } else {

                $message = "Something went wrong. Please try again.";
                $message_type = "error";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Edit Course - Student Management System</title>

    <link rel="stylesheet" href="../css/style.css">
</head>

<body>

<header>
    <h1>Student Management System</h1>
    <p>Course Management</p>
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

        <h2>Edit Course</h2>

        <p>Update the course information below.</p>

        <?php if (!empty($message)): ?>

            <div class="<?php echo $message_type; ?>" role="alert">
                <?php echo htmlspecialchars($message); ?>
            </div>

        <?php endif; ?>

        <form method="POST" action="">

            <label for="course_code">Course Code</label>

            <input
                type="text"
                id="course_code"
                name="course_code"
                value="<?php echo htmlspecialchars($course["course_code"]); ?>"
                required
            >

            <label for="course_name">Course Name</label>

            <input
                type="text"
                id="course_name"
                name="course_name"
                value="<?php echo htmlspecialchars($course["course_name"]); ?>"
                required
            >

            <label for="description">Course Description</label>

            <textarea
                id="description"
                name="description"
                rows="6"
            ><?php echo htmlspecialchars($course["description"] ?? ""); ?></textarea>

            <br><br>

            <button type="submit">Update Course</button>

            <a class="btn" href="courses.php">Cancel</a>

        </form>

    </div>

</main>

<footer>
    <p>&copy; 2026 Student Management System</p>
</footer>

</body>
</html>