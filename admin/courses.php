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

$sql = "SELECT id, course_code, course_name, description, created_at
        FROM courses
        ORDER BY id DESC";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Manage Courses - Student Management System</title>

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

        <h2>Manage Courses</h2>

        <p>
            View and manage the courses available in the system.
        </p>

        <p>
            <a class="btn" href="add_course.php">
                Add New Course
            </a>
        </p>

    </div>

    <section class="container" style="margin-top: 20px;">

        <h2>Course List</h2>

        <?php if ($result && $result->num_rows > 0): ?>

            <table>

                <caption>
                    List of available courses
                </caption>

                <thead>

                    <tr>
                        <th scope="col">ID</th>
                        <th scope="col">Course Code</th>
                        <th scope="col">Course Name</th>
                        <th scope="col">Description</th>
                        <th scope="col">Created Date</th>
                        <th scope="col">Actions</th>
                    </tr>

                </thead>

                <tbody>

                    <?php while ($course = $result->fetch_assoc()): ?>

                        <tr>

                            <td>
                                <?php echo htmlspecialchars($course["id"]); ?>
                            </td>

                            <td>
                                <?php echo htmlspecialchars($course["course_code"]); ?>
                            </td>

                            <td>
                                <?php echo htmlspecialchars($course["course_name"]); ?>
                            </td>

                            <td>
                                <?php echo htmlspecialchars($course["description"] ?? ""); ?>
                            </td>

                            <td>
                                <?php echo htmlspecialchars($course["created_at"]); ?>
                            </td>

                            <td>

                                <a
                                    class="btn"
                                    href="edit_course.php?id=<?php echo $course["id"]; ?>"
                                >
                                    Edit
                                </a>

                                <br><br>

                                <a
                                    href="delete_course.php?id=<?php echo $course["id"]; ?>"
                                    onclick="return confirm('Are you sure you want to delete this course?');"
                                >
                                    Delete
                                </a>

                            </td>

                        </tr>

                    <?php endwhile; ?>

                </tbody>

            </table>

        <?php else: ?>

            <p class="error">
                No courses found.
            </p>

        <?php endif; ?>

    </section>

</main>

<footer>

    <p>
        &copy; 2026 Student Management System
    </p>

</footer>

</body>

</html>