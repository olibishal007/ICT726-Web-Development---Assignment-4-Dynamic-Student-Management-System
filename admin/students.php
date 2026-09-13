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

$sql = "SELECT students.id,
               students.student_number,
               students.phone,
               students.date_of_birth,
               users.full_name,
               users.email
        FROM students
        INNER JOIN users
            ON students.user_id = users.id
        ORDER BY students.id DESC";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Manage Students - Student Management System</title>

    <link rel="stylesheet" href="../css/style.css">

</head>

<body>

<header>

    <h1>Student Management System</h1>

    <p>Student Management</p>

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

        <h2>Manage Students</h2>

        <p>
            View and manage student records in the system.
        </p>

        <p>
            <a class="btn" href="add_student.php">
                Add New Student
            </a>
        </p>

    </div>


    <section class="container" style="margin-top: 20px;">

        <h2>Student List</h2>

        <?php if ($result && $result->num_rows > 0): ?>

            <table>

                <caption>
                    List of registered student records
                </caption>

                <thead>

                    <tr>

                        <th scope="col">ID</th>

                        <th scope="col">Student Number</th>

                        <th scope="col">Name</th>

                        <th scope="col">Email</th>

                        <th scope="col">Phone</th>

                        <th scope="col">Date of Birth</th>

                        <th scope="col">Actions</th>

                    </tr>

                </thead>

                <tbody>

                    <?php while ($student = $result->fetch_assoc()): ?>

                        <tr>

                            <td>
                                <?php
                                echo htmlspecialchars($student["id"]);
                                ?>
                            </td>

                            <td>
                                <?php
                                echo htmlspecialchars($student["student_number"]);
                                ?>
                            </td>

                            <td>
                                <?php
                                echo htmlspecialchars($student["full_name"]);
                                ?>
                            </td>

                            <td>
                                <?php
                                echo htmlspecialchars($student["email"]);
                                ?>
                            </td>

                            <td>
                                <?php
                                echo htmlspecialchars(
                                    $student["phone"] ?? "Not available"
                                );
                                ?>
                            </td>

                            <td>
                                <?php
                                echo htmlspecialchars(
                                    $student["date_of_birth"] ?? "Not available"
                                );
                                ?>
                            </td>

                            <td>

                                <a
                                    class="btn"
                                    href="edit_student.php?id=<?php echo $student["id"]; ?>"
                                >
                                    Edit
                                </a>

                                <br><br>

                                <a
                                    href="delete_student.php?id=<?php echo $student["id"]; ?>"
                                    onclick="return confirm('Are you sure you want to delete this student?');"
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
                No students found.
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