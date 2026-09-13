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

/* Get all enquiries */
$sql = "SELECT id, name, email, subject, created_at
        FROM enquiries
        ORDER BY id DESC";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Student Enquiries - Student Management System</title>

    <link rel="stylesheet" href="../css/style.css">
</head>

<body>

<header>
    <h1>Student Management System</h1>
    <p>Student Enquiries</p>
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

        <h2>Student Enquiries</h2>

        <p>
            View enquiries submitted through the Contact Us form.
        </p>

    </div>

    <section class="container" style="margin-top: 20px;">

        <h2>Enquiry List</h2>

        <?php if ($result && $result->num_rows > 0): ?>

            <table>

                <caption>
                    List of submitted student enquiries
                </caption>

                <thead>
                    <tr>
                        <th scope="col">ID</th>
                        <th scope="col">Name</th>
                        <th scope="col">Email</th>
                        <th scope="col">Subject</th>
                        <th scope="col">Date</th>
                        <th scope="col">Action</th>
                    </tr>
                </thead>

                <tbody>

                    <?php while ($enquiry = $result->fetch_assoc()): ?>

                        <tr>

                            <td>
                                <?php echo htmlspecialchars($enquiry["id"]); ?>
                            </td>

                            <td>
                                <?php echo htmlspecialchars($enquiry["name"]); ?>
                            </td>

                            <td>
                                <?php echo htmlspecialchars($enquiry["email"]); ?>
                            </td>

                            <td>
                                <?php echo htmlspecialchars($enquiry["subject"]); ?>
                            </td>

                            <td>
                                <?php echo htmlspecialchars($enquiry["created_at"]); ?>
                            </td>

                            <td>
                                <a
                                    class="btn"
                                    href="view_enquiry.php?id=<?php echo $enquiry["id"]; ?>"
                                >
                                    View
                                </a>
                            </td>

                        </tr>

                    <?php endwhile; ?>

                </tbody>

            </table>

        <?php else: ?>

            <p class="error">
                No enquiries found.
            </p>

        <?php endif; ?>

    </section>

</main>

<footer>
    <p>&copy; 2026 Student Management System</p>
</footer>

</body>

</html>