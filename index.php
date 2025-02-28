<?php
session_start();
if (isset($_SESSION["user_id"])) {
    header("Location: dashboard.php"); // Redirect logged-in users
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Scanner System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container">
        <a class="navbar-brand" href="#">Scanner System</a>
    </div>
</nav>

<div class="container d-flex justify-content-center align-items-center vh-100">
    <div class="text-center">
        <h1 class="text-primary mb-4">Welcome to Scanner System</h1>
        <p class="lead">Securely scan text and files for malicious content.</p>
        <a href="login.php" class="btn btn-primary btn-lg">Login</a>
        <a href="register.php" class="btn btn-outline-primary btn-lg">Register</a>
        <br><br>
        <a href="admin_login.php" class="btn btn-danger">Admin Login</a>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
