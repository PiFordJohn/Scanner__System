<?php
include "config.php"; // Database connection

if (isset($_GET["id"])) {
    $scan_id = intval($_GET["id"]);
    $stmt = $conn->prepare("DELETE FROM scan_logs WHERE id = ?");
    $stmt->bind_param("i", $scan_id);
    if ($stmt->execute()) {
        echo "<script>alert('✅ Scan log deleted successfully!'); window.location.href='admin_dashboard.php';</script>";
    } else {
        echo "<script>alert('❌ Error deleting scan log.'); window.location.href='admin_dashboard.php';</script>";
    }
}

// Clear all logs
if (isset($_GET["all"])) {
    $conn->query("DELETE FROM scan_logs");
    echo "<script>alert('✅ All scan logs deleted!'); window.location.href='admin_dashboard.php';</script>";
}
?>
