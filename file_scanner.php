<?php
session_start();
include "config.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["file"])) {
    $user_id = $_SESSION["user_id"];
    $filename = $_FILES["file"]["name"];
    $filetype = pathinfo($filename, PATHINFO_EXTENSION);
    $allowedTypes = ["txt", "log", "json"];

    if (!in_array($filetype, $allowedTypes)) {
        $result = "File type not allowed!";
        $alertClass = "alert-danger";
    } else {
        $content = file_get_contents($_FILES["file"]["tmp_name"]);
        $blacklist = ["malware", "virus", "attack", "hacker"];
        $detected = [];

        foreach ($blacklist as $word) {
            if (stripos($content, $word) !== false) {
                $detected[] = $word;
            }
        }

        if (empty($detected)) {
            $result = "File is clean.";
            $alertClass = "alert-success";
        } else {
            $result = "Malicious content detected: " . implode(", ", $detected);
            $alertClass = "alert-danger";
        }

        // Store in database
        $stmt = $conn->prepare("INSERT INTO scan_logs (user_id, scan_type, scanned_content) VALUES (?, 'file', ?)");
        $stmt->bind_param("is", $user_id, $result);
        $stmt->execute();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>File Scanner - Scanner System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container">
        <a class="navbar-brand" href="#">Scanner System</a>
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
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow p-4">
                <h2 class="text-center">File Scanner</h2>
                <p class="text-muted text-center">Upload a file (.txt, .log, .json) for scanning.</p>

                <?php if (isset($result)): ?>
                    <div class="alert <?php echo $alertClass; ?>"><?php echo $result; ?></div>
                <?php endif; ?>

                <form method="post" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label class="form-label">Select File</label>
                        <input type="file" name="file" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-success w-100">Scan File</button>
                </form>

                <a href="dashboard.php" class="btn btn-secondary w-100 mt-3">Back to Dashboard</a>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
