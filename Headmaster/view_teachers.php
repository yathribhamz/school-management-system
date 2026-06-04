<?php
session_start();
include('../Admin/connection.php');

// Hakikisha ni headmaster aliye login
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'headmaster') {
    header("Location: ../login.php");
    exit();
}

// Vuta walimu wote pamoja na user info
$sql = "SELECT t.teacher_id, u.full_name, u.sex, u.email, u.date_of_birth, u.employee_no, u.nida
        FROM teachers t
        INNER JOIN users u ON t.user_id = u.id
        ORDER BY u.full_name ASC";
$teachers = $conn->query($sql);

// Function ya kuvuta subjects na levels za mwalimu husika
function getTeacherSubjects($conn, $teacher_id) {
    $sql = "SELECT s.subject_name, c.class_name, c.level
            FROM teacher_subjects ts
            INNER JOIN subjects s ON ts.subject_id = s.subject_id
            INNER JOIN classes c ON ts.class_id = c.class_id
            WHERE ts.teacher_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $teacher_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = [];
    while($row = $result->fetch_assoc()){
        $data[] = $row['subject_name'] . " (Level " . $row['level'] . " - " . $row['class_name'] . ")";
    }
    return implode(", ", $data);
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>View Teachers</title>
    <link rel="stylesheet" href="/include/css/style.css">
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top:20px;
        }
        table, th, td {
            border:1px solid #ccc;
        }
        th, td {
            padding:10px;
            text-align:center;
        }
        th {
            background:#f4f4f4;
        }
    </style>
</head>
<body>
    <?php include('header.php'); ?>
    <?php include('topbar.php'); ?>
    <?php include('sidebar.php'); ?>

    <div class="content">
        <h1>Teachers Overview</h1>
        <table>
            <tr>
                <th>Full Name</th>
                <th>Sex</th>
                <th>Email</th>
                <th>Date of Birth</th>
                <th>Employee No</th>
                <th>NIDA</th>
                <th>Subjects & Levels</th>
            </tr>
            <?php while($row = $teachers->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['full_name']; ?></td>
                    <td><?php echo $row['sex']; ?></td>
                    <td><?php echo $row['email']; ?></td>
                    <td><?php echo $row['date_of_birth']; ?></td>
                    <td><?php echo $row['employee_no']; ?></td>
                    <td><?php echo $row['nida']; ?></td>
                    <td><?php echo getTeacherSubjects($conn, $row['teacher_id']); ?></td>
                </tr>
            <?php endwhile; ?>
        </table>
    </div>
</body>
</html>
