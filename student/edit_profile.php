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

/* Get current student information */
$sql = "SELECT users.full_name,
               users.email,
               students.student_number,
               students.phone,
               students.date_of_birth
        FROM users
        LEFT JOIN students ON users.id = students.user_id
        WHERE users.id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();

$result = $stmt->get_result();
$student = $result->fetch_assoc();

if (!$student) {
    die("Student profile not found.");
}

/* Update profile */
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $full_name = trim($_POST["full_name"]);
    $email = trim($_POST["email"]);
    $phone = trim($_POST["phone"]);
    $date_of_birth = $_POST["date_of_birth"];

    if ($full_name === "" || $email === "") {
        $error = "Name and email are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email address.";
    } else {

        /* Check if email is already used by another user */
        $sql = "SELECT id
                FROM users
                WHERE email = ? AND id != ?";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $email, $user_id);
        $stmt->execute();

        $email_result = $stmt->get_result();

        if ($email_result->num_rows > 0) {

            $error = "This email address is already in use.";

        } else {

            /* Update users table */
            $sql = "UPDATE users
                    SET full_name = ?, email = ?
                    WHERE id = ?";

            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssi", $full_name, $email, $user_id);

            if ($stmt->execute()) {

                /* Update students table */
                $sql = "UPDATE students
                        SET phone = ?, date_of_birth = ?
                        WHERE user_id = ?";

                $stmt = $conn->prepare($sql);
                $stmt->bind_param(
                    "ssi",
                    $phone,
                    $date_of_birth,
                    $user_id
                );

                if ($stmt->execute()) {

                    $_SESSION["full_name"] = $full_name;

                    $success = "Your personal information has been updated successfully.";

                    /* Refresh displayed information */
                    $student["full_name"] = $full_name;
                    $student["email"] = $email;
                    $student["phone"] = $phone;
                    $student["date_of_birth"] = $date_of_birth;

                } else {

                    $error = "Unable to update student information.";
                }

            } else {

                $error = "Unable to update your information.";
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

    <meta name="description"
          content="Update personal information in the Student Management System.">

    <title>Edit Personal Information - Student Management System</title>

    <link rel="stylesheet" href="../css/style.css">

</head>

<body>

<header>

    <h1>Student Management System</h1>

    <p>Edit Personal Information</p>

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

        <h2>Edit Personal Information</h2>

        <p>
            Update your personal information below.
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

        <form method="POST" action="edit_profile.php">

            <label for="student_number">
                Student Number
            </label>

            <input
                type="text"
                id="student_number"
                value="<?php echo htmlspecialchars($student["student_number"] ?? ""); ?>"
                readonly
            >

            <label for="full_name">
                Full Name
            </label>

            <input
                type="text"
                id="full_name"
                name="full_name"
                value="<?php echo htmlspecialchars($student["full_name"] ?? ""); ?>"
                required
            >

            <label for="email">
                Email
            </label>

            <input
                type="email"
                id="email"
                name="email"
                value="<?php echo htmlspecialchars($student["email"] ?? ""); ?>"
                required
            >

            <label for="phone">
                Phone
            </label>

            <input
                type="tel"
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

            <p>
                <button type="submit">
                    Save Changes
                </button>
            </p>

        </form>

        <p>
            <a href="dashboard.php" class="btn">
                Back to Student Dashboard
            </a>
        </p>

    </section>

</main>

<footer>

    <p>
        &copy; 2026 Student Management System
    </p>

</footer>

</body>

</html>
```
