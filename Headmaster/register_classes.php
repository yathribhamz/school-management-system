<?php
session_start();
include('../Admin/connection.php');

// Hakikisha ni headmaster aliye login
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'headmaster') {
    header("Location: ../login.php");
    exit();
}

$page_title = 'Register Class';
$message = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $class_name   = trim($_POST['class_name']);
    $level        = $_POST['level'];
    $max_students = $_POST['max_students'];

    // Validation
    if (empty($class_name)) {
        $message = "Class name is required.";
    } elseif ($level < 1 || $level > 7) {
        $message = "Class level must be between 1 and 7.";
    } elseif ($max_students > 45) {
        $message = "Max students cannot exceed 45.";
    } elseif ($max_students < 10) {
        $message = "Max students must be at least 10.";
    } else {
        $stmt = $conn->prepare("INSERT INTO classes (class_name, level, max_students, headmaster_id) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("siii", $class_name, $level, $max_students, $_SESSION['user_id']);
        if ($stmt->execute()) {
            $message = "Class registered successfully.";
            $message_type = "success";
        } else {
            $message = "Error: " . $conn->error;
            $message_type = "error";
        }
    }
}

include('header.php');
include('sidebar.php');
include('topbar.php');
?>

<div class="content">
    <div class="card">
        <h3>Register New Class</h3>
        
        <?php if (!empty($message)): ?>
            <div class="<?php echo isset($message_type) && $message_type == 'success' ? 'success' : 'error'; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="" onsubmit="return validateForm()">
            <div class="input-group">
                <label for="class_name">Class Name</label>
                <input type="text" id="class_name" name="class_name" placeholder="e.g., Grade 1, Form One, Standard One" required>
                <span id="class-error" class="field-error"></span>
            </div>
            
            <div class="input-group">
                <label for="level">Class Level (1 - 7)</label>
                <input type="number" id="level" name="level" min="1" max="7" placeholder="1" required>
                <span id="level-error" class="field-error"></span>
                <div class="info-hint">Level 1 = Standard One, Level 7 = Standard Seven</div>
            </div>
            
            <div class="input-group">
                <label for="max_students">Maximum Students Per Class</label>
                <input type="number" id="max_students" name="max_students" value="40" min="10" max="45" required>
                <span id="students-error" class="field-error"></span>
                <div class="info-hint">Allowed range: 10 to 45 students</div>
            </div>
            
            <button type="submit">Register Class</button>
        </form>
    </div>
</div>

<script>
function validateForm() {
    let isValid = true;
    
    // Validate Class Name
    const className = document.getElementById("class_name").value.trim();
    const classError = document.getElementById("class-error");
    
    if (className === "") {
        classError.textContent = "Class name is required.";
        isValid = false;
    } else if (className.length < 3) {
        classError.textContent = "Class name must be at least 3 characters.";
        isValid = false;
    } else {
        classError.textContent = "";
    }
    
    // Validate Level
    const level = document.getElementById("level").value;
    const levelError = document.getElementById("level-error");
    
    if (level === "") {
        levelError.textContent = "Class level is required.";
        isValid = false;
    } else if (level < 1 || level > 7) {
        levelError.textContent = "Class level must be between 1 and 7.";
        isValid = false;
    } else {
        levelError.textContent = "";
    }
    
    // Validate Max Students
    const maxStudents = document.getElementById("max_students").value;
    const studentsError = document.getElementById("students-error");
    
    if (maxStudents === "") {
        studentsError.textContent = "Maximum students is required.";
        isValid = false;
    } else if (maxStudents < 10) {
        studentsError.textContent = "Maximum students must be at least 10.";
        isValid = false;
    } else if (maxStudents > 45) {
        studentsError.textContent = "Maximum students cannot exceed 45.";
        isValid = false;
    } else {
        studentsError.textContent = "";
    }
    
    return isValid;
}

// Real-time validation
document.getElementById("class_name").addEventListener("input", function() {
    const val = this.value.trim();
    const error = document.getElementById("class-error");
    if (val !== "" && val.length < 3) {
        error.textContent = "Minimum 3 characters";
    } else {
        error.textContent = "";
    }
});

document.getElementById("level").addEventListener("input", function() {
    const val = parseInt(this.value);
    const error = document.getElementById("level-error");
    if (this.value !== "" && (val < 1 || val > 7)) {
        error.textContent = "Level must be between 1 and 7";
    } else {
        error.textContent = "";
    }
});

document.getElementById("max_students").addEventListener("input", function() {
    const val = parseInt(this.value);
    const error = document.getElementById("students-error");
    if (this.value !== "") {
        if (val < 10) {
            error.textContent = "Minimum 10 students";
        } else if (val > 45) {
            error.textContent = "Maximum 45 students";
        } else {
            error.textContent = "";
        }
    }
});
</script>

</body>
</html>