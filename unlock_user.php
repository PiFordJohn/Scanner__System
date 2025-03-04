<?php
include "config.php"; // Database connection

if (isset($_GET["id"])) {
    $user_id = intval($_GET["id"]);
    $stmt = $conn->prepare("UPDATE users SET account_locked = 0, failed_attempts = 0 WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    if ($stmt->execute()) {
        echo "<script>alert('✅ User account unlocked successfully!'); window.location.href='admin_dashboard.php';</script>";
    } else {
        echo "<script>alert('❌ Error unlocking user.'); window.location.href='admin_dashboard.php';</script>";
    }
}
?>
