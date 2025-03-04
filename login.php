<?php
session_start();
include "config.php"; 

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $login_input = trim($_POST["login_input"]); 
    $password = trim($_POST["password"]);

    if (!empty($login_input) && !empty($password)) {
        
        // Check if the user exists
        $stmt = $conn->prepare("SELECT id, username, email, password, failed_attempts, last_attempt_time, account_locked FROM users WHERE username = ? OR email = ?");
        $stmt->bind_param("ss", $login_input, $login_input);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows == 1) {
            $user = $result->fetch_assoc();
            $user_id = $user["id"];
            $failed_attempts = $user["failed_attempts"];
            $last_attempt_time = $user["last_attempt_time"];
            $account_locked = $user["account_locked"];

            // Check if the account is locked
            if ($account_locked == 1) {
                $error = "Your account is locked due to multiple failed login attempts. Please contact admin.";
            } else {
                // Verify password
                if (password_verify($password, $user["password"])) {
                    // Reset failed attempts on successful login
                    $reset_stmt = $conn->prepare("UPDATE users SET failed_attempts = 0, last_attempt_time = NULL WHERE id = ?");
                    $reset_stmt->bind_param("i", $user_id);
                    $reset_stmt->execute();
                    $reset_stmt->close();

                    // Set session variables
                    $_SESSION["user_id"] = $user["id"];
                    $_SESSION["username"] = $user["username"];
                    $_SESSION["email"] = $user["email"];

                    // Redirect to dashboard
                    header("Location: dashboard.php");
                    exit;
                } else {
                    // Increment failed attempts
                    $failed_attempts++;
                    $update_stmt = $conn->prepare("UPDATE users SET failed_attempts = ?, last_attempt_time = NOW() WHERE id = ?");
                    $update_stmt->bind_param("ii", $failed_attempts, $user_id);
                    $update_stmt->execute();
                    $update_stmt->close();

                    // Lock account if failed attempts reach 3
                    if ($failed_attempts >= 3) {
                        $lock_stmt = $conn->prepare("UPDATE users SET account_locked = 1 WHERE id = ?");
                        $lock_stmt->bind_param("i", $user_id);
                        $lock_stmt->execute();
                        $lock_stmt->close();
                        $error = "Your account has been locked due to multiple failed login attempts. Please contact admin.";
                    } else {
                        $error = "Invalid password. Attempts remaining: " . (3 - $failed_attempts);
                    }
                }
            }
        } else {
            $error = "User not found.";
        }
        $stmt->close();
    } else {
        $error = "Please enter your username or email and password.";
    }
}

$conn->close();
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

<div class="container d-flex justify-content-center align-items-center" style="height: 100vh;">
    <div class="card p-4 shadow" style="width: 400px;">
        <h3 class="text-center">Login</h3>
        
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-3">
                <label for="login_input" class="form-label">Username or Email</label>
                <input type="text" class="form-control" id="login_input" name="login_input" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Login</button>
        </form>

        <div class="text-center mt-3">
            <a href="register.php">Create an account</a> | 
            <a href="forgot_password.php">Forgot password?</a>
        </div>

        <!-- Back to Home Button -->
        <div class="text-center mt-3">
            <a href="index.php" class="btn btn-secondary w-100">Back to Home</a>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
