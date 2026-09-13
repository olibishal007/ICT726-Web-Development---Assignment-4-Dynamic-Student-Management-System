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


/* Drop a course */
if ($_SERVER["REQUEST_METHOD"] === "POST"
    && isset($_POST["drop_enrolment_id"])) {

    $enrolment_id = (int) $_POST["drop_enrolment_id"];

    if ($enrolment_id <= 0) {

        $error = "Invalid enrolment.";

    } else {

        /*
         * Only allow the logged-in student to drop
         * their own enrolment.
         */
        $sql = "UPDATE enrolments
                SET status = 'Dropped'
                WHERE id = ?
                AND student_id = ?
                AND status = 'Active'";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param(
            "ii",
            $enrolment_id,
            $student_id
        );

        if ($stmt->execute()) {

            if ($stmt->affected_rows > 0) {

                $success = "The course has been dropped successfully.";

            } else {

                $error = "Unable to drop this course.";
            }

        } else {

            $error = "Unable to update the enrolment.";
        }
    }
}


/* Get enrolments for this student */
$sql = "SELECT enrolments.id AS enrolment_id,
               courses.course_code,
               courses.course_name,
               enrolments.enrolment_date,
               enrolments.status
        FROM enrolments
        INNER JOIN courses
            ON enrolments.course_id = courses.id
        WHERE enrolments.student_id = ?
        ORDER BY enrolments.enrolment_date DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $student_id);
$stmt->execute();

$enrolments = $stmt->get_result();

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <meta name="description"
          content="View and manage your course enrolments in the Student Management System.">

    <title>My Enrolments - Student Management System</title>

    <link rel="stylesheet" href="../css/style.css">

</head>

<body>

<header>

    <h1>Student Management System</h1>

    <p>My Enrolments</p>

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

        <h2>My Enrolments</h2>

        <p>
            View and manage your current and previous course enrolments.
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


        <?php if ($enrolments->num_rows > 0): ?>

            <table>

                <caption>
                    Course enrolment records associated with your student account
                </caption>

                <thead>

                    <tr>

                        <th scope="col">Course Code</th>

                        <th scope="col">Course Name</th>

                        <th scope="col">Enrolment Date</th>

                        <th scope="col">Status</th>

                        <th scope="col">Action</th>

                    </tr>

                </thead>


                <tbody>

                    <?php while ($enrolment = $enrolments->fetch_assoc()): ?>

                        <tr>

                            <td>
                                <?php
                                echo htmlspecialchars(
                                    $enrolment["course_code"]
                                );
                                ?>
                            </td>


                            <td>
                                <?php
                                echo htmlspecialchars(
                                    $enrolment["course_name"]
                                );
                                ?>
                            </td>


                            <td>
                                <?php
                                echo htmlspecialchars(
                                    $enrolment["enrolment_date"]
                                );
                                ?>
                            </td>


                            <td>
                                <?php
                                echo htmlspecialchars(
                                    $enrolment["status"]
                                );
                                ?>
                            </td>


                            <td>

                                <?php if ($enrolment["status"] === "Active"): ?>

                                    <form method="POST"
                                          action="enrolments.php">

                                        <input
                                            type="hidden"
                                            name="drop_enrolment_id"
                                            value="<?php echo (int) $enrolment["enrolment_id"]; ?>"
                                        >

                                        <button type="submit">
                                            Drop Course
                                        </button>

                                    </form>

                                <?php else: ?>

                                    <span>
                                        No action
                                    </span>

                                <?php endif; ?>

                            </td>

                        </tr>

                    <?php endwhile; ?>

                </tbody>

            </table>

        <?php else: ?>

            <p class="error">
                You do not have any course enrolments at the moment.
            </p>

        <?php endif; ?>


        <p>

            <a class="btn" href="courses.php">
                Browse Available Courses
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
