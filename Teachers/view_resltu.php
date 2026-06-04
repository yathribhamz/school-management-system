<?php
session_start();
include('../Admin/connection.php');

// Hakikisha ni teacher aliye login
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'teacher') {
    header("Location: ../login.php");
    exit();
}

$teacher_id = $_SESSION['teacher_id'];

// Vuta matokeo ya wanafunzi wa mwalimu huyu
$sql = "SELECT u.reg_no, u.full_name, c.class_name, s.subject_name, s.subject_id,
               m.test1, m.test2, m.groupwork1, m.groupwork2, m.exam, m.total, m.grade, m.locked
        FROM marks m
        INNER JOIN students st ON m.student_id = st.student_id
        INNER JOIN users u ON st.user_id = u.id
        INNER JOIN classes c ON st.class_id = c.class_id
        INNER JOIN subjects s ON m.subject_id = s.subject_id
        WHERE m.teacher_id = ?
        ORDER BY s.subject_name, u.full_name ASC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $teacher_id);
$stmt->execute();
$results = $stmt->get_result();
?>
<!DOCTYPE html>
<html>
<head>
    <title>My Students Results</title>
    <style>
        table { width:100%; border-collapse:collapse; margin-top:20px; }
        table, th, td { border:1px solid #ccc; }
        th, td { padding:10px; text-align:center; }
        th { background:#f4f4f4; }
        .locked { color:red; font-weight:bold; }
        .unlocked { color:green; font-weight:bold; }
        .btn { padding:5px 10px; text-decoration:none; border-radius:4px; }
        .btn-primary { background:#007bff; color:white; }
    </style>
</head>
<body>
    <?php include('header.php'); ?>
    <?php include('topbar.php'); ?>
    <?php include('sidebar.php'); ?>

    <div class="content">
        <h1>My Students Results</h1>
        <table>
            <tr>
                <th>Reg No</th>
                <th>Student Name</th>
                <th>Class</th>
                <th>Subject</th>
                <th>Test1</th>
                <th>Test2</th>
                <th>Groupwork1</th>
                <th>Groupwork2</th>
                <th>Exam</th>
                <th>Total</th>
                <th>Grade</th>
                <th>Status / Action</th>
            </tr>
            <?php while($row = $results->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['reg_no']; ?></td>
                    <td><?php echo $row['full_name']; ?></td>
                    <td><?php echo $row['class_name']; ?></td>
                    <td><?php echo $row['subject_name']; ?></td>
                    <td><?php echo $row['test1']; ?></td>
                    <td><?php echo $row['test2']; ?></td>
                    <td><?php echo $row['groupwork1']; ?></td>
                    <td><?php echo $row['groupwork2']; ?></td>
                    <td><?php echo $row['exam']; ?></td>
                    <td><?php echo $row['total']; ?></td>
                    <td><?php echo $row['grade']; ?></td>
                    <td>
                        <?php if ($row['locked'] == 0): ?>
                            <a href="download_marks.php?subject_id=<?php echo $row['subject_id']; ?>" class="btn btn-primary">Download CSV</a>
                        <?php else: ?>
                            <span class="locked">Locked</span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>
    </div>
</body>
</html>
