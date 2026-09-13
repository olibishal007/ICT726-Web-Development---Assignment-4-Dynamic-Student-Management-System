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

        $user_id = $_POST["user_id"] ?? "";
        $student_number = trim($_POST["student_number"] ?? "");
        $phone = trim($_POST["phone"] ?? "");
        $date_of_birth = $_POST["date_of_birth"] ?? "";

        // Validate required fields
        if (
            empty($user_id) ||
            empty($student_number)
        ) {
            $message = "Please fill in all required fields.";
            $message_type = "error";

        } elseif (!is_numeric($user_id)) {
            $message = "Invalid student selected.";
            $message_type = "error";

        } else {

            // Check if the user already has a student profile
            $check_sql = "SELECT id FROM students WHERE user_id = ?";
            $check_stmt = $conn->prepare($check_sql);
            $check_stmt->bind_param("i", $user_id);
            $check_stmt->execute();

            $check_result = $check_stmt->get_result();

            if ($check_result->num_rows > 0) {

                $message = "This student already has a profile.";
                $message_type = "error";

            } else {

                // Check if student number already exists
                $check_sql = "SELECT id FROM students WHERE student_number = ?";
                $check_stmt = $conn->prepare($check_sql);
                $check_stmt->bind_param("s", $student_number);
                $check_stmt->execute();

                $check_result = $check_stmt->get_result();

                if ($check_result->num_rows > 0) {

                    $message = "This student number already exists.";
                    $message_type = "error";

                } else {

                    // Insert student record
                    $sql = "INSERT INTO students
                            (user_id, student_number, phone, date_of_birth)
                            VALUES (?, ?, ?, ?)";

                    $stmt = $conn->prepare($sql);

                    $stmt->bind_param(
                        "isss",
                        $user_id,
                        $student_number,
                        $phone,
                        $date_of_birth
                    );

                    if ($stmt->execute()) {

                        $message = "Student record added successfully.";
                        $message_type = "success";

                        // Clear form
                        $student_number = "";
                        $phone = "";
                        $date_of_birth = "";

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
}

// Get students who do not have a student profile yet
$sql = "SELECT users.id, users.full_name, users.email
        FROM users
        LEFT JOIN students ON users.id = students.user_id
        WHERE users.role = 'student'
        AND students.id IS NULL
        ORDER BY users.full_name";

$users_result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <meta name="description"
          content="Add a student record to the Student Management System.">

    <title>Add Student - Student Management System</title>

    <link rel="stylesheet" href="../css/style.css">

</head>

<body>

<header>

    <h1>Student Management System</h1>

    <p>Add Student Record</p>

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

        <h2>Add New Student</h2>

        <p>
            Add a student profile to the Student Management System.
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

            <label for="user_id">
                Student Account
            </label>

            <select id="user_id" name="user_id" required>

                <option value="">
                    Select Student
                </option>

                <?php if ($users_result && $users_result->num_rows > 0): ?>

                    <?php while ($user = $users_result->fetch_assoc()): ?>

                        <option value="<?php echo htmlspecialchars($user["id"]); ?>">

                            <?php
                            echo htmlspecialchars(
                                $user["full_name"] .
                                " - " .
                                $user["email"]
                            );
                            ?>

                        </option>

                    <?php endwhile; ?>

                <?php endif; ?>

            </select>

            <label for="student_number">
                Student Number
            </label>

            <input
                type="text"
                id="student_number"
                name="student_number"
                value="<?php echo htmlspecialchars($student_number ?? ""); ?>"
                required
            >

            <label for="phone">
                Phone
            </label>

            <input
                type="text"
                id="phone"
                name="phone"
                value="<?php echo htmlspecialchars($phone ?? ""); ?>"
            >

            <label for="date_of_birth">
                Date of Birth
            </label>

            <input
                type="date"
                id="date_of_birth"
                name="date_of_birth"
                value="<?php echo htmlspecialchars($date_of_birth ?? ""); ?>"
            >

            <br><br>

            <button type="submit">
                Add Student
            </button>

            <a class="btn" href="students.php">
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