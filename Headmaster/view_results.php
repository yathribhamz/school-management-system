<?php
session_start();
include('../Admin/connection.php');

// Hakikisha ni headmaster aliye login
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'headmaster') {
    header("Location: ../login.php");
    exit();
}

// Vuta madarasa yote kwa dropdown
$classes = $conn->query("SELECT class_id, class_name, level FROM classes ORDER BY level ASC");

// Angalia kama Headmaster amechagua class
$selected_class = isset($_POST['class_id']) ? $_POST['class_id'] : null;

$results = null;
$class_avg = null;

if ($selected_class) {
    // Vuta matokeo ya wanafunzi wa darasa husika kwa kila somo
    $sql = "SELECT u.full_name, c.class_name, s.subject_name,
                   m.student_id, m.test1, m.test2, m.groupwork1, m.groupwork2, m.exam,
                   m.total, m.grade, m.locked, m.approved_by_headmaster
            FROM marks m
            INNER JOIN students st ON m.student_id = st.student_id
            INNER JOIN users u ON st.user_id = u.id
            INNER JOIN classes c ON st.class_id = c.class_id
            INNER JOIN subjects s ON m.subject_id = s.subject_id
            WHERE st.class_id = ?
            ORDER BY s.subject_name, m.total DESC";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $selected_class);
    $stmt->execute();
    $results = $stmt->get_result();

    // Hesabu wastani wa darasa husika
    $class_avg_sql = "SELECT AVG(m.total) AS class_avg
                      FROM marks m
                      INNER JOIN students st ON m.student_id = st.student_id
                      WHERE st.class_id = ?";
    $stmt2 = $conn->prepare($class_avg_sql);
    $stmt2->bind_param("i", $selected_class);
    $stmt2->execute();
    $class_avg = $stmt2->get_result()->fetch_assoc()['class_avg'];
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>View Results</title>
    <link rel="stylesheet" href="/include/css/style.css">
    <style>
        table { width:100%; border-collapse:collapse; margin-top:20px; }
        table, th, td { border:1px solid #ccc; }
        th, td { padding:10px; text-align:center; }
        th { background:#f4f4f4; }
        .approve-btn { background:green; color:white; padding:5px 10px; border:none; cursor:pointer; margin:2px; }
        .unlock-btn { background:orange; color:white; padding:5px 10px; border:none; cursor:pointer; margin:2px; }
    </style>
</head>
<body>
    <?php include('header.php'); ?>
    <?php include('topbar.php'); ?>
    <?php include('sidebar.php'); ?>

    <div class="content">
        <h1>Students Results Overview</h1>

        <!-- Dropdown ya kuchagua class -->
        <form method="POST" action="">
            <label>Select Class:</label>
            <select name="class_id" required>
                <option value="">-- Select Class --</option>
                <?php while($row = $classes->fetch_assoc()): ?>
                    <option value="<?php echo $row['class_id']; ?>" 
                        <?php if($selected_class == $row['class_id']) echo "selected"; ?>>
                        <?php echo $row['class_name'] . " (Level " . $row['level'] . ")"; ?>
                    </option>
                <?php endwhile; ?>
            </select>
            <button type="submit">View Results</button>
        </form>

        <?php if ($selected_class && $results && $results->num_rows > 0): ?>
            <p><strong>Class Average:</strong> <?php echo round($class_avg,2); ?></p>

            <!-- Button ya kuapprove matokeo yote ya darasa -->
            <form method="POST" action="approve_results.php">
                <input type="hidden" name="class_id" value="<?php echo $selected_class; ?>">
                <button type="submit" class="approve-btn">Approve All Results for Class</button>
            </form>

            <table>
                <tr>
                    <th>Student Name</th>
                    <th>Class</th>
                    <th>Subject</th>
                    <th>Total Marks</th>
                    <th>Grade</th>
                    <th>Position</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
                <?php 
                $current_subject = null;
                $position = 1;
                while($row = $results->fetch_assoc()): 
                    // Reset position per subject
                    if ($current_subject !== $row['subject_name']) {
                        $current_subject = $row['subject_name'];
                        $position = 1;
                        echo "<tr><td colspan='8' style='background:#ddd; font-weight:bold;'>Subject: ".$current_subject."</td></tr>";
                    }
                ?>
                    <tr>
                        <td><?php echo $row['full_name']; ?></td>
                        <td><?php echo $row['class_name']; ?></td>
                        <td><?php echo $row['subject_name']; ?></td>
                        <td><?php echo $row['total']; ?></td>
                        <td><?php echo $row['grade']; ?></td>
                        <td><?php echo $position++; ?></td>
                        <td>
                            <?php 
                                if ($row['approved_by_headmaster'] == 1) {
                                    echo "<span style='color:green;'>Approved</span>";
                                } else {
                                    echo "<span style='color:red;'>Pending</span>";
                                }
                            ?>
                        </td>
                        <td>
                            <!-- Headmaster anaweza kufungua marks -->
                            <form method="POST" action="unlock_marks.php" style="display:inline;">
                                <input type="hidden" name="student_id" value="<?php echo $row['student_id']; ?>">
                                <button type="submit" class="unlock-btn">Unlock for Teacher</button>
                            </form>

                            <!-- Headmaster anaweza approve matokeo ya mwanafunzi mmoja -->
                            <form method="POST" action="approve_results.php" style="display:inline;">
                                <input type="hidden" name="student_id" value="<?php echo $row['student_id']; ?>">
                                <button type="submit" class="approve-btn">Approve for Parents</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </table>
        <?php elseif ($selected_class): ?>
            <p>No results found for this class.</p>
        <?php endif; ?>
    </div>
</body>
</html>
