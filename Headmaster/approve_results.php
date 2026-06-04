<?php
session_start();
include('../Admin/connection.php');

// Hakikisha ni headmaster aliye login
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'headmaster') {
    header("Location: ../login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['class_id'])) {
        // Approve matokeo ya wanafunzi wote wa darasa husika
        $class_id = intval($_POST['class_id']);
        $sql = "UPDATE marks m
                INNER JOIN students st ON m.student_id = st.student_id
                SET m.approved_by_headmaster = 1
                WHERE st.class_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $class_id);

        if ($stmt->execute()) {
            $_SESSION['success_message'] = "All results approved successfully for class ID: $class_id";
        } else {
            $_SESSION['error_message'] = "Failed to approve all results.";
        }
        header("Location: view_results.php");
        exit();

    } elseif (isset($_POST['student_id'])) {
        // Approve matokeo ya mwanafunzi mmoja
        $student_id = intval($_POST['student_id']);
        $sql = "UPDATE marks SET approved_by_headmaster = 1 WHERE student_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $student_id);

        if ($stmt->execute()) {
            $_SESSION['success_message'] = "Results approved successfully for student ID: $student_id";
        } else {
            $_SESSION['error_message'] = "Failed to approve results.";
        }
        header("Location: view_results.php");
        exit();

    } else {
        $_SESSION['error_message'] = "Invalid request.";
        header("Location: view_results.php");
        exit();
    }
}
?>
