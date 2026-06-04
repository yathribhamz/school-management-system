<?php
session_start();
include('../Admin/connection.php');

// Hakikisha ni headmaster aliye login
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'headmaster') {
    header("Location: ../login.php");
    exit();
}

$sql = "SELECT u.full_name, u.sex, u.email, u.date_of_birth, u.reg_no,
               c.class_name, c.level
        FROM students s
        INNER JOIN users u ON s.user_id = u.id
        INNER JOIN classes c ON s.class_id = c.class_id
        ORDER BY c.level ASC, u.full_name ASC";
$students = $conn->query($sql);

?>
<!DOCTYPE html>
<html>
<head>
    <title>View Students</title>
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
        <h1>Students Overview</h1>
        <table>
            <tr>
                <th>Full Name</th>
                <th>Sex</th>
                <th>Email</th>
                <th>Date of Birth</th>
                <th>Registration No</th>
                <th>Class</th>
                <th>Level</th>
            </tr>
            <?php while($row = $students->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['full_name']; ?></td>
                    <td><?php echo $row['sex']; ?></td>
                    <td><?php echo $row['email']; ?></td>
                    <td><?php echo $row['date_of_birth']; ?></td>
                    <td><?php echo $row['reg_no']; ?></td>
                    <td><?php echo $row['class_name']; ?></td>
                    <td><?php echo $row['level']; ?></td>
                </tr>
            <?php endwhile; ?>
        </table>
    </div>
</body>
</html>
