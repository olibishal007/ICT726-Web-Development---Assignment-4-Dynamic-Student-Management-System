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

$success = "";
$error = "";

/* Find the student record */
$sql = "SELECT id
        FROM students
        WHERE user_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();

$result = $stmt->get_result();
$student = $result->fetch_assoc();

if (!$student) {
    die("Student profile not found.");
}

$student_id = $student["id"];


/* Enrol in a course */
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["enrol_course_id"])) {

    $course_id = (int) $_POST["enrol_course_id"];

    if ($course_id <= 0) {

        $error = "Please select a valid course.";

    } else {

        /* Check that the course exists */
        $sql = "SELECT id
                FROM courses
                WHERE id = ?";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $course_id);
        $stmt->execute();

        $course_result = $stmt->get_result();

        if ($course_result->num_rows === 0) {

            $error = "The selected course does not exist.";

        } else {

            /* Check if the student is already enrolled */
            $sql = "SELECT id, status
                    FROM enrolments
                    WHERE student_id = ?
                    AND course_id = ?";

            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ii", $student_id, $course_id);
            $stmt->execute();

            $existing = $stmt->get_result();
            $existing_enrolment = $existing->fetch_assoc();

            if ($existing_enrolment) {

                if ($existing_enrolment["status"] === "Dropped") {

                    /* Re-activate a previous dropped enrolment */
                    $sql = "UPDATE enrolments
                            SET status = 'Active',
                                enrolment_date = CURDATE()
                            WHERE id = ?
                            AND student_id = ?";

                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param(
                        "ii",
                        $existing_enrolment["id"],
                        $student_id
                    );

                    if ($stmt->execute()) {
                        $success = "You have successfully enrolled in the course again.";
                    } else {
                        $error = "Unable to enrol in the course.";
                    }

                } else {

                    $error = "You are already enrolled in this course.";
                }

            } else {

                /* Create a new enrolment */
                $sql = "INSERT INTO enrolments
                        (student_id, course_id, enrolment_date, status)
                        VALUES (?, ?, CURDATE(), 'Active')";

                $stmt = $conn->prepare($sql);
                $stmt->bind_param(
                    "ii",
                    $student_id,
                    $course_id
                );

                if ($stmt->execute()) {
                    $success = "You have successfully enrolled in the course.";
                } else {
                    $error = "Unable to enrol in the course.";
                }
            }
        }
    }
}


/* Get available courses */
$sql = "SELECT courses.id,
               courses.course_code,
               courses.course_name,
               courses.description
        FROM courses
        WHERE courses.id NOT IN (
            SELECT enrolments.course_id
            FROM enrolments
            WHERE enrolments.student_id = ?
            AND enrolments.status = 'Active'
        )
        ORDER BY courses.course_code";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $student_id);
$stmt->execute();

$available_courses = $stmt->get_result();

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <meta name="description"
          content="View and enrol in available courses in the Student Management System.">

    <title>My Courses - Student Management System</title>

    <link rel="stylesheet" href="../css/style.css">

</head>

<body>

<header>

    <h1>Student Management System</h1>

    <p>My Courses</p>

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

    <section class="container">

        <h2>My Courses</h2>

        <p>
            View available courses and enrol in a course you would like to study.
        </p>


        <?php if ($success): ?>

            <p class="success">
                <?php echo htmlspecialchars($success); ?>
            </p>

        <?php endif; ?>


        <?php if ($error): ?>

            <p class="error">
                <?php echo htmlspecialchars($error); ?>
            </p>

        <?php endif; ?>


        <h2>Available Courses</h2>

        <?php if ($available_courses->num_rows > 0): ?>

            <table>

                <caption>
                    Courses available for enrolment
                </caption>

                <thead>

                    <tr>

                        <th scope="col">Course Code</th>

                        <th scope="col">Course Name</th>

                        <th scope="col">Description</th>

                        <th scope="col">Action</th>

                    </tr>

                </thead>

                <tbody>

                    <?php while ($course = $available_courses->fetch_assoc()): ?>

                        <tr>

                            <td>
                                <?php
                                echo htmlspecialchars(
                                    $course["course_code"]
                                );
                                ?>
                            </td>

                            <td>
                                <?php
                                echo htmlspecialchars(
                                    $course["course_name"]
                                );
                                ?>
                            </td>

                            <td>
                                <?php
                                echo htmlspecialchars(
                                    $course["description"]
                                );
                                ?>
                            </td>

                            <td>

                                <form method="POST"
                                      action="courses.php">

                                    <input
                                        type="hidden"
                                        name="enrol_course_id"
                                        value="<?php echo (int) $course["id"]; ?>"
                                    >

                                    <button type="submit">
                                        Enrol
                                    </button>

                                </form>

                            </td>

                        </tr>

                    <?php endwhile; ?>

                </tbody>

            </table>

        <?php else: ?>

            <p class="success">
                You are currently enrolled in all available courses.
            </p>

        <?php endif; ?>


        <p>

            <a class="btn" href="enrolments.php">
                View My Enrolments
            </a>

        </p>

        <p>

            <a class="btn" href="dashboard.php">
                Back to Student Dashboard
            </a>

        </p>

    </section>

</main>

<footer>

    <p>&copy; 2026 Student Management System</p>

</footer>

</body>

</html>
```
