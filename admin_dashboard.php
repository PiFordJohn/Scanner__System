<?php
session_start();
if (!isset($_SESSION["admin_id"])) {
    header("Location: admin_login.php");
    exit;
}

include "config.php";

// Fetch scan logs with user details
$result = $conn->query("
    SELECT scan_logs.*, users.username, users.email
    FROM scan_logs
    LEFT JOIN users ON scan_logs.user_id = users.id
    ORDER BY scan_logs.scan_date DESC
");

// Fetch locked users
$locked_users = $conn->query("SELECT id, username, email FROM users WHERE account_locked = 1");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Dashboard - Scanner System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container">
        <a class="navbar-brand" href="#">Admin Dashboard</a>
        <div class="collapse navbar-collapse">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link btn btn-danger text-white" href="logout.php">Logout</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="container mt-5">
    <h2 class="text-center">All Scan Logs</h2>
    <div class="card shadow p-4 mt-3">
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead class="table-dark">
                    <tr>
                        <th>User</th>
                        <th>Scan Type</th>
                        <th>Result</th>
                        <th>Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                  <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td>
                            <?php 
                                echo isset($row["username"]) ? htmlspecialchars($row["username"]) : "Unknown User"; 
                                echo " (" . (isset($row["email"]) ? htmlspecialchars($row["email"]) : "No Email") . ")"; 
                            ?>
                        </td>
                        <td><?php echo ucfirst(htmlspecialchars($row["scan_type"])); ?></td>
                        <td><?php echo htmlspecialchars($row["scanned_content"]); ?></td>
                        <td><?php echo htmlspecialchars($row["scan_date"]); ?></td>
                        <td>
                            <a href="delete_scan.php?id=<?php echo htmlspecialchars($row['id']); ?>" class="btn btn-danger btn-sm">Delete</a>
                        </td>
                    </tr>
                  <?php endwhile; ?>
                </tbody>
            </table>
        </div>
        <div class="text-center mt-3">
            <a href="delete_scan.php?all=true" class="btn btn-warning">Clear All Logs</a>
        </div>
    </div>

    <!-- Locked Accounts Section -->
    <h2 class="text-center mt-5">Locked User Accounts</h2>
    <div class="card shadow p-4 mt-3">
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead class="table-danger">
                    <tr>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                  <?php while ($user = $locked_users->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($user["username"]); ?></td>
                        <td><?php echo htmlspecialchars($user["email"]); ?></td>
                        <td>
                            <a href="unlock_user.php?id=<?php echo htmlspecialchars($user['id']); ?>" class="btn btn-success btn-sm">Unlock</a>
                        </td>
                    </tr>
                  <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
