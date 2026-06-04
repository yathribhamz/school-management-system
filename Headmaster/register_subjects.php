<?php
session_start();
include('../Admin/connection.php');

// Hakikisha ni headmaster aliye login
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'headmaster') {
    header("Location: ../login.php");
    exit();
}

$page_title = 'Register Subject';
$message = "";
$message_type = "";

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $subject_name = trim($_POST['subject_name']);
    $class_id     = $_POST['class_id'];

    // Validation
    if (empty($subject_name)) {
        $message = "Subject name is required.";
        $message_type = "error";
    } elseif (empty($class_id)) {
        $message = "Please select a class level.";
        $message_type = "error";
    } elseif (strlen($subject_name) < 3) {
        $message = "Subject name must be at least 3 characters.";
        $message_type = "error";
    } else {
        // Check if subject already exists for this class
        $check_sql = "SELECT subject_id FROM subjects WHERE subject_name = ? AND class_id = ? AND headmaster_id = ?";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param("sii", $subject_name, $class_id, $_SESSION['user_id']);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();
        
        if ($check_result->num_rows > 0) {
            $message = "This subject is already registered for the selected class.";
            $message_type = "error";
        } else {
            $stmt = $conn->prepare("INSERT INTO subjects (subject_name, class_id, headmaster_id) VALUES (?, ?, ?)");
            $stmt->bind_param("sii", $subject_name, $class_id, $_SESSION['user_id']);
            if ($stmt->execute()) {
                $message = "Subject registered successfully.";
                $message_type = "success";
            } else {
                $message = "Error: " . $conn->error;
                $message_type = "error";
            }
            $stmt->close();
        }
        $check_stmt->close();
    }
}

$classes = $conn->query("SELECT class_id, class_name, level FROM classes ORDER BY level ASC");

include('header.php');
include('sidebar.php');
include('topbar.php');
?>

<div class="content">
    <div class="card">
        <h3>Register New Subject</h3>
        
        <?php if (!empty($message)): ?>
            <div class="<?php echo $message_type == 'success' ? 'success' : 'error'; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="" onsubmit="return validateForm()">
            <div class="input-group">
                <label for="subject_name">Subject Name</label>
                <input type="text" id="subject_name" name="subject_name" placeholder="e.g., Mathematics, English, Science" required>
                <span id="subject-error" class="field-error"></span>
                <div class="info-hint">Enter the full subject name (minimum 3 characters)</div>
            </div>
            
            <div class="input-group">
                <label for="class_id">Select Class Level</label>
                <select id="class_id" name="class_id" required>
                    <option value="">-- Select Class --</option>
                    <?php while($row = $classes->fetch_assoc()): ?>
                        <option value="<?php echo $row['class_id']; ?>">
                            <?php echo htmlspecialchars($row['class_name']) . " (Level " . $row['level'] . ")"; ?>
                        </option>
                    <?php endwhile; ?>
                </select>
                <span id="class-error" class="field-error"></span>
                <div class="info-hint">Choose the class level for this subject</div>
            </div>
            
            <button type="submit">Register Subject</button>
        </form>
    </div>
</div>

<script>
function validateForm() {
    let isValid = true;
    
    // Validate Subject Name
    const subjectName = document.getElementById("subject_name").value.trim();
    const subjectError = document.getElementById("subject-error");
    
    if (subjectName === "") {
        subjectError.textContent = "Subject name is required.";
        isValid = false;
    } else if (subjectName.length < 3) {
        subjectError.textContent = "Subject name must be at least 3 characters.";
        isValid = false;
    } else if (subjectName.length > 100) {
        subjectError.textContent = "Subject name is too long (maximum 100 characters).";
        isValid = false;
    } else {
        subjectError.textContent = "";
    }
    
    // Validate Class Selection
    const classId = document.getElementById("class_id").value;
    const classError = document.getElementById("class-error");
    
    if (classId === "") {
        classError.textContent = "Please select a class level.";
        isValid = false;
    } else {
        classError.textContent = "";
    }
    
    return isValid;
}

// Real-time validation for subject name
document.getElementById("subject_name").addEventListener("input", function() {
    const val = this.value.trim();
    const error = document.getElementById("subject-error");
    
    if (val === "") {
        error.textContent = "Subject name is required.";
    } else if (val.length < 3) {
        error.textContent = "Minimum 3 characters required.";
    } else if (val.length > 100) {
        error.textContent = "Maximum 100 characters allowed.";
    } else {
        error.textContent = "";
    }
});

// Real-time validation for class selection
document.getElementById("class_id").addEventListener("change", function() {
    const val = this.value;
    const error = document.getElementById("class-error");
    
    if (val === "") {
        error.textContent = "Please select a class level.";
    } else {
        error.textContent = "";
    }
});

// Prevent form resubmission on page refresh
if (window.history.replaceState) {
    window.history.replaceState(null, null, window.location.href);
}
</script>

</body>
</html>