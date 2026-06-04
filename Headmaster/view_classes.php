<?php
session_start();
include('../Admin/connection.php');

// Hakikisha ni headmaster aliye login
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'headmaster') {
    header("Location: ../login.php");
    exit();
}

// Vuta madarasa yote
$sql = "SELECT c.class_id, c.class_name, c.level, c.max_students,
        COUNT(s.student_id) AS current_students
        FROM classes c
        LEFT JOIN students s ON c.class_id = s.class_id
        GROUP BY c.class_id, c.class_name, c.level, c.max_students
        ORDER BY c.level ASC";
$classes = $conn->query($sql);

?>
<!DOCTYPE html>
<html>
<head>
    <title>View Classes</title>
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
        .full {
            color:red;
            font-weight:bold;
        }
        .available {
            color:green;
            font-weight:bold;
        }
    </style>
</head>
<body>
    <?php include('header.php'); ?>
    <?php include('topbar.php'); ?>
    <?php include('sidebar.php'); ?>

    <div class="content">
        <h1>Classes Overview</h1>
        <table>
            <tr>
                <th>Class Name</th>
                <th>Level</th>
                <th>Capacity</th>
                <th>Current Students</th>
                <th>Status</th>
            </tr>
            <?php while($row = $classes->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['class_name']; ?></td>
                    <td><?php echo $row['level']; ?></td>
                    <td><?php echo $row['max_students']; ?></td>
                    <td><?php echo $row['current_students']; ?></td>
                    <td>
                        <?php if($row['current_students'] >= $row['max_students']): ?>
                            <span class="full">Class Full</span>
                        <?php else: ?>
                            <span class="available">Available</span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>
    </div>
</body>
</html>
