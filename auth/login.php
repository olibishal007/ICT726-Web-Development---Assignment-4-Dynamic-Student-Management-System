<?php
session_start();
require_once "../config/db.php";

$message = "";
$message_type = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";

    if (empty($email) || empty($password)) {

        $message = "Please enter your email and password.";
        $message_type = "error";

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $message = "Please enter a valid email address.";
        $message_type = "error";

    } else {

        $sql = "SELECT id, full_name, email, password, role
                FROM users
                WHERE email = ?";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();

        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        if ($user && password_verify($password, $user["password"])) {

            // Create a new session ID after successful login
            session_regenerate_id(true);

            $_SESSION["user_id"] = $user["id"];
            $_SESSION["full_name"] = $user["full_name"];
            $_SESSION["email"] = $user["email"];
            $_SESSION["role"] = $user["role"];

            if ($user["role"] === "admin") {

                header("Location: ../admin/dashboard.php");
                exit();

            } else {

                header("Location: ../student/dashboard.php");
                exit();
            }

        } else {

            $message = "Incorrect email or password.";
            $message_type = "error";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <meta name="description" content="Login to the Student Management System to access student or administrator services.">

    <title>Login - Student Management System</title>

    <link rel="stylesheet" href="../css/style.css">

</head>

<body>

<header>

    <h1>Student Management System</h1>
    <p>User Login</p>

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

        <h2>Login</h2>

        <p>
            Enter your email address and password to access
            your account.
        </p>

        <?php if (!empty($message)): ?>

            <div class="<?php echo $message_type; ?>" role="alert">
                <?php echo htmlspecialchars($message); ?>
            </div>

        <?php endif; ?>

        <form method="POST" action="">

            <label for="email">Email Address</label>

            <input
                type="email"
                id="email"
                name="email"
                value="<?php echo htmlspecialchars($email ?? ""); ?>"
                required
            >

            <label for="password">Password</label>

            <input
                type="password"
                id="password"
                name="password"
                required
            >

            <br><br>

            <button type="submit">Login</button>

        </form>

        <p>
            Don't have an account?
            <a href="register.php">Register here</a>.
        </p>

    </section>

</main>

<footer>

    <p>&copy; 2026 Student Management System</p>

</footer>

</body>
</html>