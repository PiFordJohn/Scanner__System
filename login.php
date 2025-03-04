<?php
session_start();
include "config.php"; // Database connection file

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);

    // Fetch user data including failed attempts and lock status
    $stmt = $conn->prepare("SELECT id, username, password, failed_attempts, account_locked FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $username, $hashed_password, $failed_attempts, $account_locked);
        $stmt->fetch();

        if ($account_locked) {
            $error = "❌ Your account is locked due to multiple failed login attempts. Contact the admin.";
        } elseif (password_verify($password, $hashed_password)) {
            // Reset failed attempts on successful login
            $conn->query("UPDATE users SET failed_attempts = 0 WHERE id = $id");
            $_SESSION["user_id"] = $id;
            $_SESSION["username"] = $username;
            header("Location: dashboard.php");
            exit;
        } else {
            // Increment failed attempts
            $failed_attempts++;
            if ($failed_attempts >= 3) {
                // Lock account if 3 failed attempts
                $conn->query("UPDATE users SET account_locked = 1 WHERE id = $id");
                $error = "❌ Too many failed attempts! Your account has been locked.";
            } else {
                // Update failed attempts count
                $conn->query("UPDATE users SET failed_attempts = $failed_attempts WHERE id = $id");
                $error = "❌ Invalid password! Attempt $failed_attempts/3";
            }
        }
    } else {
        $error = "❌ User not found!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - Scanner System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card shadow p-4">
                <h2 class="text-center">Login</h2>

                <?php if (isset($error)): ?>
                    <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>

                <form method="post">
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" placeholder="Enter your email" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" placeholder="Enter your password" required>
                    </div>
                    <button type="submit" class="btn btn-success w-100">Login</button>
                </form>

                <p class="mt-3 text-center">Don't have an account? <a href="register.php">Register</a></p>
                <p class="text-center"><a href="forgot_password.php">Forgot Password?</a></p>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
