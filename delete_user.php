<?php
session_start();
include "config.php";

// Ensure only admin can delete users
if (!isset($_SESSION["admin_id"])) {
    header("Location: admin_login.php");
    exit;
}

// Check if user ID is provided
if (isset($_GET["id"])) {
    $user_id = intval($_GET["id"]);

    // Delete user from the database
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);

    if ($stmt->execute()) {
        $_SESSION["message"] = "User deleted successfully.";
    } else {
        $_SESSION["message"] = "Error deleting user.";
    }
    
    $stmt->close();
} else {
    $_SESSION["message"] = "Invalid request.";
}

// Redirect back to admin dashboard
header("Location: admin_dashboard.php");
exit;
?>
