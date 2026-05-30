<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

// Zuia caching
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

include('connection.php');

// Check kama id imepitishwa
if (!isset($_GET['id'])) {
    die("Headmaster ID not provided.");
}

$id = intval($_GET['id']);

// First, check if headmaster exists
$stmt = $conn->prepare("SELECT full_name FROM users WHERE id=? AND role='headmaster'");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$headmaster = $result->fetch_assoc();

if (!$headmaster) {
    die("Headmaster not found.");
}

// Perform deletion
$stmt = $conn->prepare("DELETE FROM users WHERE id=? AND role='headmaster'");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    // Redirect with success message
    header("Location: view_headmasters.php?deleted=1&name=" . urlencode($headmaster['full_name']));
    exit();
} else {
    die("Error deleting headmaster: " . $conn->error);
}
?>