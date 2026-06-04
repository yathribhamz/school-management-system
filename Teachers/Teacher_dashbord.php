<?php
session_start();
include('../Admin/connection.php');

// Hakikisha ni teacher aliye login
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'teacher') {
    header("Location: ../login.php");
    exit();
}

// Hakikisha teacher_id ipo kwenye session
// Wakati wa login, hakikisha umehifadhi $_SESSION['teacher_id'] kutoka teachers table
if (!isset($_SESSION['teacher_id'])) {
    die("Teacher ID haipo kwenye session. Tafadhali hakikisha umehifadhi wakati wa login.");
}
$teacher_id = $_SESSION['teacher_id'];

// 1. Idadi ya wanafunzi waliopangiwa mwalimu huyu
$sql_students = "SELECT COUNT(DISTINCT st.student_id) AS total_students
                 FROM teacher_subjects ts
                 INNER JOIN students st ON ts.class_id = st.class_id
                 WHERE ts.teacher_id = ?";
$stmt = $conn->prepare($sql_students);
$stmt->bind_param("i", $teacher_id);
$stmt->execute();
$total_students = $stmt->get_result()->fetch_assoc()['total_students'];

// 2. Idadi ya madarasa yote kwenye shule
$sql_classes = "SELECT COUNT(*) AS total_classes FROM classes";
$total_classes = $conn->query($sql_classes)->fetch_assoc()['total_classes'];

// 3. Asilimia ya ufaulu kwenye somo husika (pass = total_marks >= 50)
$sql_pass = "SELECT (COUNT(CASE WHEN (m.test1 + m.test2 + m.groupwork1 + m.groupwork2 + m.exam) >= 50 THEN 1 END) / COUNT(*)) * 100 AS pass_rate
             FROM marks m
             INNER JOIN students st ON m.student_id = st.student_id
             INNER JOIN teacher_subjects ts ON m.subject_id = ts.subject_id AND st.class_id = ts.class_id
             WHERE ts.teacher_id = ?";
$stmt2 = $conn->prepare($sql_pass);
$stmt2->bind_param("i", $teacher_id);
$stmt2->execute();
$pass_rate = $stmt2->get_result()->fetch_assoc()['pass_rate'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Teacher Dashboard</title>
    <link rel="stylesheet" href="dashboard.css">
</head>
<body>
    <?php include('header.php'); ?>
    <?php include('topbar.php'); ?>
    <?php include('sidebar.php'); ?>

    <div class="content">
        <h1>Teacher Dashboard</h1>
        <div class="cards">
            <div class="card blue">
                <h2>Wanafunzi Wangu</h2>
                <p><?php echo $total_students; ?></p>
            </div>
            <div class="card black">
                <h2>Madarasa ya Shule</h2>
                <p><?php echo $total_classes; ?></p>
            </div>
            <div class="card blue">
                <h2>Ufaulu wa Somo Langu</h2>
                <p><?php echo round($pass_rate,2); ?>%</p>
            </div>
        </div>
    </div>
</body>
</html>
