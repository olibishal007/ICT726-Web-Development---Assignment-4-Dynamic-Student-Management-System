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

/* Get all enrolments */
$sql = "SELECT enrolments.id,
               students.student_number,
               users.full_name,
               courses.course_code,
               courses.course_name,
               enrolments.enrolment_date,
               enrolments.status
        FROM enrolments
        INNER JOIN students
            ON enrolments.student_id = students.id
        INNER JOIN users
            ON students.user_id = users.id
        INNER JOIN courses
            ON enrolments.course_id = courses.id
        ORDER BY enrolments.id DESC";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Manage Enrolments - Student Management System</title>

    <link rel="stylesheet" href="../css/style.css">
</head>

<body>

<header>
    <h1>Student Management System</h1>
    <p>Enrolment Management</p>
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

        <h2>Manage Enrolments</h2>

        <p>
            View and manage student course enrolments.
        </p>

        <p>
            <a class="btn" href="add_enrolment.php">
                Add New Enrolment
            </a>
        </p>

    </div>

    <section class="container" style="margin-top: 20px;">

        <h2>Enrolment List</h2>

        <?php if ($result && $result->num_rows > 0): ?>

            <table>

                <caption>
                    List of student course enrolments
                </caption>

                <thead>
                    <tr>
                        <th scope="col">ID</th>
                        <th scope="col">Student Number</th>
                        <th scope="col">Student Name</th>
                        <th scope="col">Course Code</th>
                        <th scope="col">Course Name</th>
                        <th scope="col">Enrolment Date</th>
                        <th scope="col">Status</th>
                        <th scope="col">Actions</th>
                    </tr>
                </thead>

                <tbody>

                    <?php while ($enrolment = $result->fetch_assoc()): ?>

                        <tr>

                            <td>
                                <?php echo htmlspecialchars($enrolment["id"]); ?>
                            </td>

                            <td>
                                <?php echo htmlspecialchars($enrolment["student_number"]); ?>
                            </td>

                            <td>
                                <?php echo htmlspecialchars($enrolment["full_name"]); ?>
                            </td>

                            <td>
                                <?php echo htmlspecialchars($enrolment["course_code"]); ?>
                            </td>

                            <td>
                                <?php echo htmlspecialchars($enrolment["course_name"]); ?>
                            </td>

                            <td>
                                <?php echo htmlspecialchars($enrolment["enrolment_date"]); ?>
                            </td>

                            <td>
                                <?php echo htmlspecialchars($enrolment["status"]); ?>
                            </td>

                            <td>

                                <a
                                    href="delete_enrolment.php?id=<?php echo $enrolment["id"]; ?>"
                                    onclick="return confirm('Are you sure you want to delete this enrolment?');"
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
                No enrolments found.
            </p>

        <?php endif; ?>

    </section>

</main>

<footer>
    <p>&copy; 2026 Student Management System</p>
</footer>

</body>

</html>