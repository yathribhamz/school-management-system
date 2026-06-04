<?php
session_start();
include('../Admin/connection.php');

// Hakikisha ni headmaster aliye login
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'headmaster') {
    header("Location: ../login.php");
    exit();
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['subject_id'])) {
    $subject_id = (int)$_POST['subject_id'];

    $sql_unlock = "UPDATE marks SET locked = 0 WHERE subject_id = ?";
    $stmt = $conn->prepare($sql_unlock);
    $stmt->bind_param("i", $subject_id);

    if ($stmt->execute()) {
        $message = "Marks for subject unlocked successfully!";
    } else {
        $message = "Error unlocking marks.";
    }
}

// Vuta masomo yote
$sql_subjects = "SELECT s.subject_id, s.subject_name, c.class_name
                 FROM subjects s
                 INNER JOIN teacher_subjects ts ON s.subject_id = ts.subject_id
                 INNER JOIN classes c ON ts.class_id = c.class_id
                 ORDER BY c.class_name, s.subject_name";
$subjects = $conn->query($sql_subjects);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Unlock Marks</title>
    <style>
        table { width:100%; border-collapse:collapse; margin-top:20px; }
        table, th, td { border:1px solid #ccc; }
        th, td { padding:10px; text-align:center; }
        th { background:#f4f4f4; }
        .unlock-btn { background:orange; color:white; padding:5px 10px; border:none; cursor:pointer; }
    </style>
</head>
<body>
    <?php include('header.php'); ?>
    <?php include('topbar.php'); ?>
    <?php include('sidebar.php'); ?>

    <div class="content">
        <h1>Unlock Subject Marks</h1>
        <?php if (!empty($message)) echo "<p style='color:green;'>$message</p>"; ?>

        <table>
            <tr>
                <th>Class</th>
                <th>Subject</th>
                <th>Action</th>
            </tr>
            <?php while($row = $subjects->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['class_name']; ?></td>
                    <td><?php echo $row['subject_name']; ?></td>
                    <td>
                        <form method="POST" action="">
                            <input type="hidden" name="subject_id" value="<?php echo $row['subject_id']; ?>">
                            <button type="submit" class="unlock-btn">Unlock Marks</button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>
    </div>
</body>
</html>
