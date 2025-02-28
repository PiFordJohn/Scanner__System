<?php
session_start();
include "config.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION["user_id"];
$result = $conn->query("SELECT * FROM scan_logs WHERE user_id = $user_id ORDER BY scan_date DESC");
?>

<h2>Your Scan History</h2>
<table border="1">
    <tr>
        <th>Scan Type</th>
        <th>Result</th>
        <th>Date</th>
    </tr>
    <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?php echo $row["scan_type"]; ?></td>
            <td><?php echo htmlspecialchars($row["scanned_content"]); ?></td>
            <td><?php echo $row["scan_date"]; ?></td>
        </tr>
    <?php endwhile; ?>
</table>
