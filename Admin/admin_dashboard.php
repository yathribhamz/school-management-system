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

$page_title = 'Admin Dashboard';

// Count Headmasters
$headmasters = $conn->query("SELECT COUNT(*) AS total FROM users WHERE role='headmaster'")->fetch_assoc()['total'];

// Count Students
$students = $conn->query("SELECT COUNT(*) AS total FROM users WHERE role='student'")->fetch_assoc()['total'];

// Average Performance (marks table)
$performance = $conn->query("SELECT AVG(total) AS avg_score FROM marks")->fetch_assoc()['avg_score'];

include('header.php');
include('sidebar.php');
include('topbar.php');
?>

<div class="content">
    <div class="card">
        <h3>Total Headmasters</h3>
        <p style="font-size: 2rem; font-weight: 700; color: #004080;"><?php echo $headmasters; ?></p>
    </div>
    
    <div class="card">
        <h3>Total Students</h3>
        <p style="font-size: 2rem; font-weight: 700; color: #004080;"><?php echo $students; ?></p>
    </div>
    
    <div class="card">
        <h3>School Average Performance</h3>
        <p style="font-size: 2rem; font-weight: 700; color: #004080;"><?php echo round($performance, 2); ?>%</p>
    </div>
</div>

</body>
</html>