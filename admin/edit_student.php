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

$id = $_GET["id"] ?? "";

if (!is_numeric($id)) {
    header("Location: students.php");
    exit();
}

$id = (int)$id;

$message = "";
$message_type = "";

/* Get existing student */
$sql = "SELECT students.id,
               students.user_id,
               students.student_number,
               students.phone,
               students.date_of_birth,
               users.full_name,
               users.email
        FROM students
        INNER JOIN users
            ON students.user_id = users.id
        WHERE students.id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();

$result = $stmt->get_result();
$student = $result->fetch_assoc();

if (!$student) {
    header("Location: students.php");
    exit();
}

/* Update student */
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $student_number = trim($_POST["student_number"] ?? "");
    $phone = trim($_POST["phone"] ?? "");
    $date_of_birth = $_POST["date_of_birth"] ?? "";

    if (empty($student_number)) {

        $message = "Student number is required.";
        $message_type = "error";

    } else {

        /* Check duplicate student number */
        $check = $conn->prepare(
            "SELECT id
             FROM students
             WHERE student_number = ?
             AND id != ?"
        );

        $check->bind_param(
            "si",
            $student_number,
            $id
        );

        $check->execute();

        $check_result = $check->get_result();

        if ($check_result->num_rows > 0) {

            $message = "This student number is already in use.";
            $message_type = "error";

        } else {

            $update = $conn->prepare(
                "UPDATE students
                 SET student_number = ?,
                     phone = ?,
                     date_of_birth = ?
                 WHERE id = ?"
            );

            $update->bind_param(
                "sssi",
                $student_number,
                $phone,
                $date_of_birth,
                $id
            );

            if ($update->execute()) {

                $message = "Student details updated successfully.";
                $message_type = "success";

                /* Update displayed values */
                $student["student_number"] = $student_number;
                $student["phone"] = $phone;
                $student["date_of_birth"] = $date_of_birth;

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

    <title>Edit Student - Student Management System</title>

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

        <h2>Edit Student</h2>

        <p>
            Update the student's information below.
        </p>


        <?php if (!empty($message)): ?>

            <div class="<?php echo $message_type; ?>" role="alert">

                <?php echo htmlspecialchars($message); ?>

            </div>

        <?php endif; ?>


        <div class="card">

            <h3>Student Account</h3>

            <p>
                <strong>Name:</strong>
                <?php echo htmlspecialchars($student["full_name"]); ?>
            </p>

            <p>
                <strong>Email:</strong>
                <?php echo htmlspecialchars($student["email"]); ?>
            </p>

        </div>


        <form method="POST" action="">

            <label for="student_number">
                Student Number
            </label>

            <input
                type="text"
                id="student_number"
                name="student_number"
                value="<?php echo htmlspecialchars($student["student_number"]); ?>"
                required
            >


            <label for="phone">
                Phone Number
            </label>

            <input
                type="text"
                id="phone"
                name="phone"
                value="<?php echo htmlspecialchars($student["phone"] ?? ""); ?>"
            >


            <label for="date_of_birth">
                Date of Birth
            </label>

            <input
                type="date"
                id="date_of_birth"
                name="date_of_birth"
                value="<?php echo htmlspecialchars($student["date_of_birth"] ?? ""); ?>"
            >


            <br><br>

            <button type="submit">
                Update Student
            </button>

            <a class="btn" href="students.php">
                Cancel
            </a>

        </form>

    </div>

</main>


<footer>

    <p>
        &copy; 2026 Student Management System
    </p>

</footer>

</body>

</html>