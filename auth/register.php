<?php
session_start();
require_once "../config/db.php";

$message = "";
$message_type = "";

// Create a CSRF token if one does not already exist
if (empty($_SESSION["csrf_token"])) {
    $_SESSION["csrf_token"] = bin2hex(random_bytes(32));
}

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

        $full_name = trim($_POST["full_name"] ?? "");
        $email = trim($_POST["email"] ?? "");
        $password = $_POST["password"] ?? "";
        $confirm_password = $_POST["confirm_password"] ?? "";

        // Check required fields
        if (
            empty($full_name) ||
            empty($email) ||
            empty($password) ||
            empty($confirm_password)
        ) {

            $message = "Please fill in all fields.";
            $message_type = "error";

        // Validate name
        } elseif (strlen($full_name) < 2) {

            $message = "Please enter a valid full name.";
            $message_type = "error";

        // Validate email
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

            $message = "Please enter a valid email address.";
            $message_type = "error";

        // Validate password length
        } elseif (strlen($password) < 8) {

            $message = "Password must be at least 8 characters long.";
            $message_type = "error";

        // Check password confirmation
        } elseif ($password !== $confirm_password) {

            $message = "Passwords do not match.";
            $message_type = "error";

        } else {

            // Check whether the email already exists
            $sql = "SELECT id FROM users WHERE email = ?";

            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $email);
            $stmt->execute();

            $result = $stmt->get_result();

            if ($result->num_rows > 0) {

                $message = "An account with this email already exists.";
                $message_type = "error";

            } else {

                // Securely hash the password
                $hashed_password = password_hash(
                    $password,
                    PASSWORD_DEFAULT
                );

                // New registrations are students by default
                $role = "student";

                $sql = "INSERT INTO users
                        (full_name, email, password, role)
                        VALUES (?, ?, ?, ?)";

                $stmt = $conn->prepare($sql);

                $stmt->bind_param(
                    "ssss",
                    $full_name,
                    $email,
                    $hashed_password,
                    $role
                );

                if ($stmt->execute()) {

                    $message = "Registration successful. You can now log in.";
                    $message_type = "success";

                    // Clear form fields after successful registration
                    $full_name = "";
                    $email = "";

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
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <meta name="description"
          content="Register for the Student Management System to access student services and academic information.">

    <title>Register - Student Management System</title>

    <link rel="stylesheet" href="../css/style.css">

</head>

<body>

<header>

    <h1>Student Management System</h1>

    <p>Create a Student Account</p>

</header>

<nav aria-label="Main navigation">

    <a href="../index.php">Home</a>

    <a href="../about.php">About Us</a>

    <a href="../services.php">Student Services</a>

    <a href="../media.php">Media</a>

    <a href="../contact.php">Contact Us</a>

    <a href="login.php">Login</a>

    <a href="register.php">Register</a>

</nav>

<main>

    <section class="container">

        <h2>Register</h2>

        <p>
            Create a student account by completing
            the form below.
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

            <!-- CSRF protection token -->
            <input
                type="hidden"
                name="csrf_token"
                value="<?php echo htmlspecialchars($_SESSION["csrf_token"]); ?>"
            >

            <label for="full_name">
                Full Name
            </label>

            <input
                type="text"
                id="full_name"
                name="full_name"
                value="<?php echo htmlspecialchars($full_name ?? ""); ?>"
                required
            >

            <label for="email">
                Email Address
            </label>

            <input
                type="email"
                id="email"
                name="email"
                value="<?php echo htmlspecialchars($email ?? ""); ?>"
                required
            >

            <label for="password">
                Password
            </label>

            <input
                type="password"
                id="password"
                name="password"
                minlength="8"
                required
            >

            <label for="confirm_password">
                Confirm Password
            </label>

            <input
                type="password"
                id="confirm_password"
                name="confirm_password"
                minlength="8"
                required
            >

            <br><br>

            <button type="submit">
                Register
            </button>

        </form>

        <p>
            Already have an account?
            <a href="login.php">Login here</a>.
        </p>

    </section>

</main>

<footer>

    <p>&copy; 2026 Student Management System</p>

</footer>

</body>

</html>