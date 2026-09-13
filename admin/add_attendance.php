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

/* Record attendance */
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $student_id = $_POST["student_id"] ?? "";
    $course_id = $_POST["course_id"] ?? "";
    $attendance_date = $_POST["attendance_date"] ?? "";
    $status = $_POST["status"] ?? "";

    if (
        empty($student_id) ||
        empty($course_id) ||
        empty($attendance_date) ||
        empty($status)
    ) {
        $message = "Please fill in all fields.";
        $message_type = "error";
    } else {

        /* Check for duplicate attendance */
        $check = $conn->prepare(
            "SELECT id
             FROM attendance
             WHERE student_id = ?
             AND course_id = ?
             AND attendance_date = ?"
        );

        $check->bind_param(
            "iis",
            $student_id,
            $course_id,
            $attendance_date
        );

        $check->execute();

        $check_result = $check->get_result();

        if ($check_result->num_rows > 0) {

            $message = "Attendance has already been recorded for this student, course and date.";
            $message_type = "error";

        } else {

            /* Insert attendance */
            $sql = "INSERT INTO attendance
                    (student_id, course_id, attendance_date, status)
                    VALUES (?, ?, ?, ?)";

            $stmt = $conn->prepare($sql);

            $stmt->bind_param(
                "iiss",
                $student_id,
                $course_id,
                $attendance_date,
                $status
            );

            if ($stmt->execute()) {

                $message = "Attendance recorded successfully.";
                $message_type = "success";

            } else {

                $message = "Something went wrong. Please try again.";
                $message_type = "error";
            }
        }
    }
}

/* Get students */
$students_sql = "SELECT students.id,
                        students.student_number,
                        users.full_name
                 FROM students
                 INNER JOIN users
                    ON students.user_id = users.id
                 ORDER BY users.full_name ASC";

$students_result = $conn->query($students_sql);

/* Get courses */
$courses_sql = "SELECT id,
                       course_code,
                       course_name
                FROM courses
                ORDER BY course_code ASC";

$courses_result = $conn->query($courses_sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Record Attendance - Student Management System</title>

    <link rel="stylesheet" href="../css/style.css">

</head>

<body>

<header>

    <h1>Student Management System</h1>

    <p>Attendance Management</p>

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

        <h2>Record Attendance</h2>

        <p>
            Enter the attendance information below.
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

            <label for="student_id">
                Student
            </label>

            <select
                id="student_id"
                name="student_id"
                required
            >

                <option value="">
                    Select a student
                </option>

                <?php if ($students_result && $students_result->num_rows > 0): ?>

                    <?php while ($student = $students_result->fetch_assoc()): ?>

                        <option
                            value="<?php echo htmlspecialchars($student["id"]); ?>"
                            <?php
                            if (
                                isset($_POST["student_id"]) &&
                                $_POST["student_id"] == $student["id"]
                            ) {
                                echo "selected";
                            }
                            ?>
                        >

                            <?php
                            echo htmlspecialchars(
                                $student["student_number"] .
                                " - " .
                                $student["full_name"]
                            );
                            ?>

                        </option>

                    <?php endwhile; ?>

                <?php endif; ?>

            </select>


            <label for="course_id">
                Course
            </label>

            <select
                id="course_id"
                name="course_id"
                required
            >

                <option value="">
                    Select a course
                </option>

                <?php if ($courses_result && $courses_result->num_rows > 0): ?>

                    <?php while ($course = $courses_result->fetch_assoc()): ?>

                        <option
                            value="<?php echo htmlspecialchars($course["id"]); ?>"
                            <?php
                            if (
                                isset($_POST["course_id"]) &&
                                $_POST["course_id"] == $course["id"]
                            ) {
                                echo "selected";
                            }
                            ?>
                        >

                            <?php
                            echo htmlspecialchars(
                                $course["course_code"] .
                                " - " .
                                $course["course_name"]
                            );
                            ?>

                        </option>

                    <?php endwhile; ?>

                <?php endif; ?>

            </select>


            <label for="attendance_date">
                Attendance Date
            </label>

            <input
                type="date"
                id="attendance_date"
                name="attendance_date"
                value="<?php echo htmlspecialchars($_POST["attendance_date"] ?? date("Y-m-d")); ?>"
                required
            >


            <label for="status">
                Attendance Status
            </label>

            <select
                id="status"
                name="status"
                required
            >

                <option value="">
                    Select attendance status
                </option>

                <option
                    value="Present"
                    <?php
                    if (($_POST["status"] ?? "") === "Present") {
                        echo "selected";
                    }
                    ?>
                >
                    Present
                </option>

                <option
                    value="Absent"
                    <?php
                    if (($_POST["status"] ?? "") === "Absent") {
                        echo "selected";
                    }
                    ?>
                >
                    Absent
                </option>

                <option
                    value="Late"
                    <?php
                    if (($_POST["status"] ?? "") === "Late") {
                        echo "selected";
                    }
                    ?>
                >
                    Late
                </option>

            </select>

            <br><br>

            <button type="submit">
                Record Attendance
            </button>

            <a
                class="btn"
                href="attendance.php"
            >
                Cancel
            </a>

        </form>

    </div>

</main>

<footer>

    <p>&copy; 2026 Student Management System</p>

</footer>

</body>

</html>