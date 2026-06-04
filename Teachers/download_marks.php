<?php
session_start();
include('../Admin/connection.php');

// Hakikisha ni teacher aliye login
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'teacher') {
    header("Location: ../login.php");
    exit();
}

$teacher_id = $_SESSION['teacher_id'];
$subject_id = (int)$_GET['subject_id'];

header('Content-Type: text/csv');
header('Content-Disposition: attachment;filename="marks_subject_'.$subject_id.'.csv"');

$output = fopen("php://output", "w");

// Headers
fputcsv($output, ['reg_no','test1','test2','groupwork1','groupwork2','exam']);

// Vuta marks za somo husika
$sql = "SELECT u.reg_no, m.test1, m.test2, m.groupwork1, m.groupwork2, m.exam
        FROM marks m
        INNER JOIN students st ON m.student_id = st.student_id
        INNER JOIN users u ON st.user_id = u.id
        WHERE m.subject_id = ? AND m.teacher_id = ? AND m.locked = 0";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $subject_id, $teacher_id);
$stmt->execute();
$res = $stmt->get_result();

while($row = $res->fetch_assoc()) {
    fputcsv($output, $row);
}

fclose($output);
exit();
