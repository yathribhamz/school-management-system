<?php
session_start();
include('../Admin/connection.php');

// Hakikisha ni student aliye login
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Tafuta student_id na class_id
$stmt = $conn->prepare("SELECT st.student_id, st.class_id, c.class_name
                        FROM students st
                        INNER JOIN classes c ON st.class_id = c.class_id
                        WHERE st.user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$student_info = $stmt->get_result()->fetch_assoc();

if (!$student_info) {
    die("No student record found.");
}

$student_id = $student_info['student_id'];
$class_id   = $student_info['class_id'];
$class_name = $student_info['class_name'];

// 1. Idadi ya wanafunzi wa darasa
$stmt2 = $conn->prepare("SELECT COUNT(*) AS total_students FROM students WHERE class_id = ?");
$stmt2->bind_param("i", $class_id);
$stmt2->execute();
$total_students = $stmt2->get_result()->fetch_assoc()['total_students'];

// 2. Pass rate ya darasa (kwa approved results pekee)
$stmt3 = $conn->prepare("SELECT 
    (SUM(CASE WHEN m.total >= 40 THEN 1 ELSE 0 END) / COUNT(*)) * 100 AS pass_rate
    FROM marks m
    INNER JOIN students st ON m.student_id = st.student_id
    WHERE st.class_id = ? AND m.approved_by_headmaster = 1");
$stmt3->bind_param("i", $class_id);
$stmt3->execute();
$pass_rate = $stmt3->get_result()->fetch_assoc()['pass_rate'];

// 3. Matokeo ya mwanafunzi (somo moja moja, approved only)
$stmt4 = $conn->prepare("SELECT s.subject_name, m.total, m.grade
                         FROM marks m
                         INNER JOIN subjects s ON m.subject_id = s.subject_id
                         WHERE m.student_id = ? AND m.approved_by_headmaster = 1");
$stmt4->bind_param("i", $student_id);
$stmt4->execute();
$student_results = $stmt4->get_result();

// 4. Jumla ya ufaulu wa mwanafunzi (approved only)
$stmt5 = $conn->prepare("SELECT SUM(total) AS grand_total, AVG(total) AS avg_total
                         FROM marks WHERE student_id = ? AND approved_by_headmaster = 1");
$stmt5->bind_param("i", $student_id);
$stmt5->execute();
$totals = $stmt5->get_result()->fetch_assoc();
$grand_total = $totals['grand_total'];
$avg_total   = $totals['avg_total'];

// 5. Nafasi darasani (approved only)
$sql_rank = "SELECT st.student_id, SUM(m.total) AS grand_total,
                    RANK() OVER (ORDER BY SUM(m.total) DESC) AS position
             FROM marks m
             INNER JOIN students st ON m.student_id = st.student_id
             WHERE st.class_id = ? AND m.approved_by_headmaster = 1
             GROUP BY st.student_id";
$stmt6 = $conn->prepare($sql_rank);
$stmt6->bind_param("i", $class_id);
$stmt6->execute();
$ranks = $stmt6->get_result();

$position = null;
while ($row = $ranks->fetch_assoc()) {
    if ($row['student_id'] == $student_id) {
        $position = $row['position'];
        break;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Student Dashboard</title>
    <style>
        table { width:100%; border-collapse:collapse; margin-top:20px; }
        table, th, td { border:1px solid #ccc; }
        th, td { padding:10px; text-align:center; }
        th { background:#f4f4f4; }
        .card { border:1px solid #ddd; padding:15px; margin:15px 0; background:#fafafa; }
    </style>
</head>
<body>
    <?php include('header.php'); ?>
    <?php include('topbar.php'); ?>
    <?php include('sidebar.php'); ?>

    <div class="content">
        <h1>Student Dashboard</h1>

        <!-- Overview ya darasa -->
        <div class="card">
            <h2>Class Overview (<?php echo $class_name; ?>)</h2>
            <p><strong>Total Students:</strong> <?php echo $total_students; ?></p>
            <p><strong>Pass Rate:</strong> <?php echo round($pass_rate,2); ?>%</p>
        </div>

        <!-- Matokeo ya mwanafunzi -->
        <div class="card">
            <h2>My Results</h2>
            <?php if ($student_results->num_rows > 0): ?>
                <table>
                    <tr>
                        <th>Subject</th>
                        <th>Total Marks</th>
                        <th>Grade</th>
                    </tr>
                    <?php while($row = $student_results->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row['subject_name']; ?></td>
                            <td><?php echo $row['total']; ?></td>
                            <td><?php echo $row['grade']; ?></td>
                        </tr>
                    <?php endwhile; ?>
                </table>
                <p><strong>Grand Total:</strong> <?php echo $grand_total; ?></p>
                <p><strong>Average Marks:</strong> <?php echo round($avg_total,2); ?></p>
                <p><strong>Class Position:</strong> <?php echo $position; ?> out of <?php echo $total_students; ?></p>
            <?php else: ?>
                <p>Results not yet approved by Headmaster.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
