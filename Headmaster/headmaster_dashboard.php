<?php
session_start();
include('../Admin/connection.php');

// Hakikisha ni headmaster aliye login
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'headmaster') {
    header("Location: ../login.php");
    exit();
}

// Students count
$students = $conn->query("SELECT COUNT(*) AS total_students FROM students")->fetch_assoc()['total_students'];

// Classes count
$classes = $conn->query("SELECT COUNT(*) AS total_classes FROM classes")->fetch_assoc()['total_classes'];

// Teachers count
$teachers = $conn->query("SELECT COUNT(*) AS total_teachers FROM teachers")->fetch_assoc()['total_teachers'];

// Pass rate (A–C)
$pass_rate = $conn->query("
    SELECT (COUNT(CASE WHEN grade IN ('A','B','C') THEN 1 END) / COUNT(*)) * 100 AS pass_rate 
    FROM marks
")->fetch_assoc()['pass_rate'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Headmaster Dashboard</title>
    <link rel="stylesheet" href="include/css/style.css">
</head>
<body>
    <!-- Include Topbar -->
    <?php include('topbar.php'); ?>

    <!-- Include Sidebar -->
    <?php include('sidebar.php'); ?>

    <!-- Include Contentbar (ikiwa unayo) -->
    <?php include('header.php'); ?>

    <!-- Main Content -->
    <div class="content">
        <h1>Headmaster Dashboard</h1>
        <div class="cards">
            <div class="card blue">
                <h2>Students</h2>
                <p><?php echo $students; ?></p>
            </div>
            <div class="card black">
                <h2>Classes</h2>
                <p><?php echo $classes; ?></p>
            </div>
            <div class="card blue">
                <h2>Teachers</h2>
                <p><?php echo $teachers; ?></p>
            </div>
            <div class="card black">
                <h2>School Performance</h2>
                <p><?php echo round($pass_rate,2); ?>%</p>
            </div>
        </div>
    </div>
</body>
</html>
