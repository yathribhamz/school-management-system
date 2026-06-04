<?php
session_start();
include('../Admin/connection.php');

// Hakikisha ni teacher aliye login
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'teacher') {
    header("Location: ../login.php");
    exit();
}

$teacher_id = $_SESSION['teacher_id']; 
$upload_message = '';
$message_type = '';

// Tafuta subject_id ya mwalimu huyu
$stmt_sub = $conn->prepare("SELECT subject_id FROM teacher_subjects WHERE teacher_id = ?");
$stmt_sub->bind_param("i", $teacher_id);
$stmt_sub->execute();
$res_sub = $stmt_sub->get_result();
$subject_row = $res_sub->fetch_assoc();
$subject_id = $subject_row ? (int)$subject_row['subject_id'] : 0;

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['marks_file'])) {
    $file = $_FILES['marks_file']['tmp_name'];
    $filename = $_FILES['marks_file']['name'];
    $file_ext = pathinfo($filename, PATHINFO_EXTENSION);

    if (strtolower($file_ext) !== 'csv') {
        $upload_message = "Please upload a valid CSV file only!";
        $message_type = 'error';
    } elseif (($handle = fopen($file, "r")) !== FALSE) {
        $success_count = 0;
        $error_count = 0;
        $errors = [];

        // Skip header row
        $header = fgetcsv($handle);

        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
            if (count($data) < 6) {
                $error_count++;
                $errors[] = "Invalid row format for registration number: " . ($data[0] ?? 'unknown');
                continue;
            }

            $reg_no     = trim($data[0]);
            $test1      = (float)$data[1];
            $test2      = (float)$data[2];
            $groupwork1 = (float)$data[3];
            $groupwork2 = (float)$data[4];
            $exam       = (float)$data[5];

            // Validate marks range
            if ($test1 < 0 || $test1 > 20 || $test2 < 0 || $test2 > 20 ||
                $groupwork1 < 0 || $groupwork1 > 10 || $groupwork2 < 0 || $groupwork2 > 10 ||
                $exam < 0 || $exam > 40) {
                $error_count++;
                $errors[] = "Invalid marks range for student: $reg_no";
                continue;
            }

            $total = $test1 + $test2 + $groupwork1 + $groupwork2 + $exam;

            // Grade calculation
            if ($total >= 80) $grade = "A";
            elseif ($total >= 65) $grade = "B";
            elseif ($total >= 50) $grade = "C";
            elseif ($total >= 40) $grade = "D";
            else $grade = "F";

            // Tafuta student_id kulingana na reg_no
            $stmt = $conn->prepare("SELECT st.student_id 
                                    FROM students st
                                    INNER JOIN users u ON st.user_id = u.id
                                    WHERE u.reg_no = ?");
            $stmt->bind_param("s", $reg_no);
            $stmt->execute();
            $res = $stmt->get_result();

            if ($row = $res->fetch_assoc()) {
                $student_id = $row['student_id'];

                // Check if marks already exist
                $check_stmt = $conn->prepare("SELECT mark_id FROM marks WHERE student_id = ? AND subject_id = ? AND teacher_id = ?");
                $check_stmt->bind_param("iii", $student_id, $subject_id, $teacher_id);
                $check_stmt->execute();
                $check_result = $check_stmt->get_result();

                if ($check_result->num_rows > 0) {
                    // Update existing marks
                    $sql_update = "UPDATE marks 
                                   SET test1 = ?, test2 = ?, groupwork1 = ?, groupwork2 = ?, exam = ?, total = ?, grade = ?, locked = 1 
                                   WHERE student_id = ? AND subject_id = ? AND teacher_id = ?";
                    $stmt2 = $conn->prepare($sql_update);
                    $stmt2->bind_param("iiiiiiisii", 
                        $test1, $test2, $groupwork1, $groupwork2, $exam, $total, $grade, 
                        $student_id, $subject_id, $teacher_id
                    );
                    $stmt2->execute();
                    $success_count++;
                } else {
                    // Insert new marks
                    $sql_insert = "INSERT INTO marks (student_id, subject_id, teacher_id, test1, test2, groupwork1, groupwork2, exam, total, grade, locked) 
                                   VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1)";
                    $stmt2 = $conn->prepare($sql_insert);
                    $stmt2->bind_param("iiiiiiiiis", 
                        $student_id, $subject_id, $teacher_id, 
                        $test1, $test2, $groupwork1, $groupwork2, $exam, $total, $grade
                    );
                    $stmt2->execute();
                    $success_count++;
                }
            } else {
                $error_count++;
                $errors[] = "Student not found with registration number: $reg_no";
            }
        }
        fclose($handle);

        $upload_message = "Uploaded $success_count record(s). Failed $error_count.";
        $message_type = $success_count > 0 ? 'success' : 'error';

        if (!empty($errors)) {
            $_SESSION['upload_errors'] = $errors;
        }
    }
}

$upload_errors = $_SESSION['upload_errors'] ?? [];
unset($_SESSION['upload_errors']);

include('header.php');
include('topbar.php');
include('sidebar.php');
?>

<div class="content">
    <div class="page-header">
        <h1>Upload Student Marks</h1>
        <p>Upload marks from CSV file - marks will be locked after upload to prevent modification</p>
    </div>

    <?php if ($upload_message): ?>
        <div class="message <?php echo $message_type; ?>">
            <span><?php echo $upload_message; ?></span>
        </div>
    <?php endif; ?>

    <?php if (!empty($upload_errors)): ?>
        <div class="error-list">
            <h4>Detailed Errors (<?php echo count($upload_errors); ?> issues):</h4>
            <ul>
                <?php foreach ($upload_errors as $error): ?>
                    <li><?php echo htmlspecialchars($error); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <div class="card">
        <h3>
            <span>CSV File</span>
            Select File to Upload
        </h3>
        
        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label>Excel/CSV File <span class="required">*</span></label>
                <div class="file-input-wrapper">
                    <input type="file" name="marks_file" accept=".csv" required>
                </div>
            </div>
            
            <button type="submit" class="btn-primary">
                Upload and Lock Marks
            </button>
        </form>

        <div class="info-note">
            <h4>CSV File Format Requirements:</h4>
            <ul>
                <li><strong>File must be .csv format</strong></li>
                <li><strong>First row should be headers</strong></li>
                <li><strong>Columns order:</strong> Registration Number, Test1, Test2, Group Work1, Group Work2, Exam</li>
            </ul>
        </div>
    </div>
</div>

<script>
document.querySelector('input[type="file"]')?.addEventListener('change', function(e) {
    var fileName = e.target.files[0]?.name;
    if (fileName) {
        console.log('Selected file:', fileName);
    }
});
</script>
</body>
</html>
