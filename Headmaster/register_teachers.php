<?php
session_start();
include('../Admin/connection.php');

// Hakikisha ni headmaster aliye login
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'headmaster') {
    header("Location: ../login.php");
    exit();
}

$page_title = 'Register Teacher';
$message = "";
$message_type = "";

// Handle registration
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['register_teacher'])) {
    $full_name   = trim($_POST['full_name']);
    $sex         = $_POST['sex'];
    $email       = trim($_POST['email']);
    $dob         = $_POST['dob'];
    $employee_no = trim($_POST['employee_no']);
    $nida        = trim($_POST['nida']);
    $password    = $_POST['password'];
    $levels      = $_POST['levels'] ?? array();
    $subjects    = $_POST['subjects'] ?? array();
    
    $errors = array();
    
    // 1. Validate full name
    if (empty($full_name)) {
        $errors[] = "Full name is required.";
    } elseif (strlen($full_name) < 3) {
        $errors[] = "Full name must be at least 3 characters.";
    }
    
    // 2. Validate sex
    if (empty($sex)) {
        $errors[] = "Please select gender.";
    }
    
    // 3. Validate email
    if (empty($email)) {
        $errors[] = "Email address is required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Please enter a valid email address.";
    } else {
        $check_email = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $check_email->bind_param("s", $email);
        $check_email->execute();
        $check_email->store_result();
        if ($check_email->num_rows > 0) {
            $errors[] = "Email address already registered.";
        }
        $check_email->close();
    }
    
    // 4. Validate date of birth
    if (empty($dob)) {
        $errors[] = "Date of birth is required.";
    } else {
        $age = date_diff(date_create($dob), date_create('today'))->y;
        if ($age < 21 || $age > 65) {
            $errors[] = "Teacher age must be between 21 and 65 years.";
        }
    }
    
    // 5. Validate employee number
    if (empty($employee_no)) {
        $errors[] = "Employee number is required.";
    } elseif (!preg_match('/^T-[0-9]+$/', $employee_no)) {
        $errors[] = "Employee number must start with 'T-' followed by numbers (e.g., T-001, T-1234).";
    } else {
        $check_emp = $conn->prepare("SELECT id FROM users WHERE employee_no = ?");
        $check_emp->bind_param("s", $employee_no);
        $check_emp->execute();
        $check_emp->store_result();
        if ($check_emp->num_rows > 0) {
            $errors[] = "Employee number already exists.";
        }
        $check_emp->close();
    }
    
    // 6. Validate NIDA
    if (empty($nida)) {
        $errors[] = "NIDA number is required.";
    } elseif (!preg_match('/^[0-9]+$/', $nida)) {
        $errors[] = "NIDA number must contain only digits.";
    } elseif (strlen($nida) !== 20) {
        $errors[] = "NIDA number must be exactly 20 digits.";
    } else {
        $check_nida = $conn->prepare("SELECT id FROM users WHERE nida = ?");
        $check_nida->bind_param("s", $nida);
        $check_nida->execute();
        $check_nida->store_result();
        if ($check_nida->num_rows > 0) {
            $errors[] = "NIDA number already registered.";
        }
        $check_nida->close();
    }
    
    // 7. Validate password
    if (empty($password)) {
        $errors[] = "Password is required.";
    } elseif (strlen($password) < 4) {
        $errors[] = "Password must be at least 4 characters.";
    }
    
    // 8. Validate levels selection
    if (empty($levels)) {
        $errors[] = "Please select at least one class level.";
    }
    
    // 9. Validate subjects selection
    if (empty($subjects)) {
        $errors[] = "Please select at least one subject.";
    }
    
    // If no errors, proceed with registration
    if (empty($errors)) {
        $hashed_password = hash('sha256', $password);
        
        // Step 1: Insert teacher into users table
        $stmt1 = $conn->prepare("INSERT INTO users (full_name, sex, email, date_of_birth, role, employee_no, nida, password) VALUES (?, ?, ?, ?, 'teacher', ?, ?, ?)");
        $stmt1->bind_param("sssssss", $full_name, $sex, $email, $dob, $employee_no, $nida, $hashed_password);
        
        if ($stmt1->execute()) {
            $user_id = $stmt1->insert_id;
            
            // Step 2: Insert into teachers table
            $stmt2 = $conn->prepare("INSERT INTO teachers (user_id, headmaster_id) VALUES (?, ?)");
            $stmt2->bind_param("ii", $user_id, $_SESSION['user_id']);
            
            if ($stmt2->execute()) {
                $teacher_id = $stmt2->insert_id;
                
                // Step 3: Link teacher to subjects & levels
                $success_count = 0;
                if (!empty($levels) && !empty($subjects)) {
                    foreach ($levels as $class_id) {
                        foreach ($subjects as $subject_id) {
                            $stmt3 = $conn->prepare("INSERT INTO teacher_subjects (teacher_id, subject_id, class_id) VALUES (?, ?, ?)");
                            $stmt3->bind_param("iii", $teacher_id, $subject_id, $class_id);
                            if ($stmt3->execute()) {
                                $success_count++;
                            }
                            $stmt3->close();
                        }
                    }
                }
                $message = "Teacher registered successfully with " . $success_count . " subject assignments.";
                $message_type = "success";
            } else {
                $message = "Error inserting into teachers: " . $conn->error;
                $message_type = "error";
            }
            $stmt2->close();
        } else {
            $message = "Error inserting into users: " . $conn->error;
            $message_type = "error";
        }
        $stmt1->close();
    } else {
        $message = implode("<br>", $errors);
        $message_type = "error";
    }
}

$classes = $conn->query("SELECT class_id, class_name, level, max_students FROM classes ORDER BY level ASC");

// Handle AJAX request for subjects filtering
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['levels']) && !isset($_POST['register_teacher'])) {
    $levels = $_POST['levels'];
    $placeholders = implode(',', array_fill(0, count($levels), '?'));
    $sql = "SELECT subject_id, subject_name, class_id 
            FROM subjects 
            WHERE class_id IN ($placeholders) 
            ORDER BY subject_name ASC";
    $stmt = $conn->prepare($sql);
    $types = str_repeat('i', count($levels));
    $stmt->bind_param($types, ...$levels);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $subjects_list = array();
    while($row = $result->fetch_assoc()) {
        $subjects_list[] = $row;
    }
    
    if (empty($subjects_list)) {
        echo '<option value="">No subjects available for selected levels</option>';
    } else {
        foreach($subjects_list as $row) {
            echo "<option value='{$row['subject_id']}'>{$row['subject_name']} (Class {$row['class_id']})</option>";
        }
    }
    exit();
}


include('header.php');
include('sidebar.php');
include('topbar.php');
?>

<div class="content">
    <div class="card">
        <h3>Register New Teacher</h3>
        
        <?php if (!empty($message)): ?>
            <div class="<?php echo $message_type == 'success' ? 'success' : 'error'; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="" onsubmit="return validateForm()">
            <div class="input-group">
                <label for="full_name">Full Name</label>
                <input type="text" id="full_name" name="full_name" placeholder="Enter teacher's full name" required>
                <span id="name-error" class="field-error"></span>
            </div>
            
            <div class="input-group">
                <label for="sex">Gender</label>
                <select id="sex" name="sex" required>
                    <option value="">-- Select Gender --</option>
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                </select>
                <span id="sex-error" class="field-error"></span>
            </div>
            
            <div class="input-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" placeholder="teacher@school.com" required>
                <span id="email-error" class="field-error"></span>
                <div class="info-hint">Valid email address required for communication</div>
            </div>
            
            <div class="input-group">
                <label for="dob">Date of Birth</label>
                <input type="date" id="dob" name="dob" required>
                <span id="dob-error" class="field-error"></span>
                <div class="info-hint">Teacher must be between 21 and 65 years old</div>
            </div>
            
            <div class="input-group">
                <label for="employee_no">Employee Number</label>
                <input type="text" id="employee_no" name="employee_no" placeholder="T-001, T-1234" required>
                <span id="emp-error" class="field-error"></span>
                <div class="info-hint">Format: T- followed by numbers (e.g., T-001, T-1234)</div>
            </div>
            
            <div class="input-group">
                <label for="nida">NIDA Number</label>
                <input type="text" id="nida" name="nida" placeholder="20 digit NIDA number" required>
                <span id="nida-error" class="field-error"></span>
                <div class="info-hint">Exactly 20 digits, numbers only</div>
            </div>
            
            <div class="input-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Create a secure password" required>
                <span id="password-error" class="field-error"></span>
                <div class="info-hint">Minimum 4 characters</div>
            </div>
           <div class="input-group">
                <label for="levels">Class Levels (Multi-select)</label>
                <select id="levels" name="levels[]" class="select2" multiple required>
                <?php while($row = $classes->fetch_assoc()): ?>
                <option value="<?php echo $row['class_id']; ?>">
                <?php echo htmlspecialchars($row['class_name']) . " (Level " . $row['level'] . ")"; ?>
                </option>
                <?php endwhile; ?>
                </select>
                 <span id="levels-error" class="field-error"></span>
                <div class="info-hint">Select one or more class levels this teacher will teach</div>
            </div>

<div class="input-group">
    <label for="subjects">Subjects (Multi-select)</label>
    <select id="subjects" name="subjects[]" class="select2" multiple required>
        <option value="">-- Select Class Levels First --</option>
    </select>
    <span id="subjects-error" class="field-error"></span>
    <div class="info-hint">Select one or more subjects. Subjects appear after selecting levels</div>
</div>

            
            <button type="submit" name="register_teacher">Register Teacher</button>
        </form>
    </div>
</div>

<!-- Select2 CSS & JS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/js/select2.min.js"></script>

<script>
$(document).ready(function() {
    $('.select2').select2({
        placeholder: "Select options",
        allowClear: true,
        width: '100%'
    });
    
    // On change of levels, fetch subjects via AJAX
    $('#levels').on('change', function() {
        var selectedLevels = $(this).val();
        var subjectsSelect = $('#subjects');
        
        if(selectedLevels && selectedLevels.length > 0){
            $.ajax({
                url: window.location.href,
                type: 'POST',
                data: {levels: selectedLevels},
                success: function(data){
                    if(data.trim() === '' || data.includes('No subjects available')) {
                        subjectsSelect.html('<option value="">No subjects available for selected levels</option>');
                    } else {
                        subjectsSelect.html(data);
                    }
                    subjectsSelect.trigger('change');
                },
                error: function() {
                    subjectsSelect.html('<option value="">Error loading subjects</option>');
                }
            });
        } else {
            subjectsSelect.html('<option value="">-- Select Class Levels First --</option>');
            subjectsSelect.trigger('change');
        }
    });
});

function validateForm() {
    let isValid = true;
    
    // Validate Full Name
    const fullName = document.getElementById("full_name").value.trim();
    const nameError = document.getElementById("name-error");
    if (fullName === "") {
        nameError.textContent = "Full name is required.";
        isValid = false;
    } else if (fullName.length < 3) {
        nameError.textContent = "Full name must be at least 3 characters.";
        isValid = false;
    } else {
        nameError.textContent = "";
    }
    
    // Validate Gender
    const sex = document.getElementById("sex").value;
    const sexError = document.getElementById("sex-error");
    if (sex === "") {
        sexError.textContent = "Please select gender.";
        isValid = false;
    } else {
        sexError.textContent = "";
    }
    
    // Validate Email
    const email = document.getElementById("email").value.trim();
    const emailError = document.getElementById("email-error");
    const emailRegex = /^[^\s@]+@([^\s@]+\.)+[^\s@]+$/;
    if (email === "") {
        emailError.textContent = "Email address is required.";
        isValid = false;
    } else if (!emailRegex.test(email)) {
        emailError.textContent = "Please enter a valid email address.";
        isValid = false;
    } else {
        emailError.textContent = "";
    }
    
    // Validate Date of Birth
    const dob = document.getElementById("dob").value;
    const dobError = document.getElementById("dob-error");
    if (dob === "") {
        dobError.textContent = "Date of birth is required.";
        isValid = false;
    } else {
        const birthDate = new Date(dob);
        const today = new Date();
        let age = today.getFullYear() - birthDate.getFullYear();
        const monthDiff = today.getMonth() - birthDate.getMonth();
        if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDate.getDate())) {
            age--;
        }
        if (age < 21 || age > 65) {
            dobError.textContent = "Teacher must be between 21 and 65 years old.";
            isValid = false;
        } else {
            dobError.textContent = "";
        }
    }
    
    // Validate Employee Number
    const empNo = document.getElementById("employee_no").value.trim();
    const empError = document.getElementById("emp-error");
    const empRegex = /^T-[0-9]+$/;
    if (empNo === "") {
        empError.textContent = "Employee number is required.";
        isValid = false;
    } else if (!empRegex.test(empNo)) {
        empError.textContent = "Format must be T-001, T-1234";
        isValid = false;
    } else {
        empError.textContent = "";
    }
    
    // Validate NIDA
    const nida = document.getElementById("nida").value.trim();
    const nidaError = document.getElementById("nida-error");
    const numberRegex = /^[0-9]+$/;
    if (nida === "") {
        nidaError.textContent = "NIDA number is required.";
        isValid = false;
    } else if (!numberRegex.test(nida)) {
        nidaError.textContent = "NIDA must contain only digits.";
        isValid = false;
    } else if (nida.length !== 20) {
        nidaError.textContent = "NIDA must be exactly 20 digits.";
        isValid = false;
    } else {
        nidaError.textContent = "";
    }
    
    // Validate Password
    const password = document.getElementById("password").value;
    const passwordError = document.getElementById("password-error");
    if (password === "") {
        passwordError.textContent = "Password is required.";
        isValid = false;
    } else if (password.length < 4) {
        passwordError.textContent = "Password must be at least 4 characters.";
        isValid = false;
    } else {
        passwordError.textContent = "";
    }
    
    // Validate Levels
    const levels = document.getElementById("levels").value;
    const levelsError = document.getElementById("levels-error");
    if (!levels || levels.length === 0) {
        levelsError.textContent = "Please select at least one class level.";
        isValid = false;
    } else {
        levelsError.textContent = "";
    }
    
    // Validate Subjects
    const subjects = document.getElementById("subjects").value;
    const subjectsError = document.getElementById("subjects-error");
    if (!subjects || subjects.length === 0) {
        subjectsError.textContent = "Please select at least one subject.";
        isValid = false;
    } else {
        subjectsError.textContent = "";
    }
    
    return isValid;
}

// Real-time validations
document.getElementById("full_name").addEventListener("input", function() {
    const val = this.value.trim();
    const error = document.getElementById("name-error");
    if (val !== "" && val.length < 3) {
        error.textContent = "Minimum 3 characters";
    } else {
        error.textContent = "";
    }
});

document.getElementById("email").addEventListener("input", function() {
    const val = this.value.trim();
    const error = document.getElementById("email-error");
    const emailRegex = /^[^\s@]+@([^\s@]+\.)+[^\s@]+$/;
    if (val !== "" && !emailRegex.test(val)) {
        error.textContent = "Invalid email format";
    } else {
        error.textContent = "";
    }
});

document.getElementById("employee_no").addEventListener("input", function() {
    const val = this.value.trim();
    const error = document.getElementById("emp-error");
    const empRegex = /^T-[0-9]+$/;
    if (val !== "" && !empRegex.test(val)) {
        error.textContent = "Use format: T-001, T-1234";
    } else {
        error.textContent = "";
    }
});

document.getElementById("nida").addEventListener("input", function() {
    const val = this.value.trim();
    const error = document.getElementById("nida-error");
    const numberRegex = /^[0-9]+$/;
    if (val !== "" && !numberRegex.test(val)) {
        error.textContent = "Digits only";
    } else if (val !== "" && val.length !== 20) {
        error.textContent = "Need exactly 20 digits (currently " + val.length + ")";
    } else {
        error.textContent = "";
    }
});

document.getElementById("password").addEventListener("input", function() {
    const val = this.value;
    const error = document.getElementById("password-error");
    if (val !== "" && val.length < 4) {
        error.textContent = "Minimum 4 characters";
    } else {
        error.textContent = "";
    }
});

document.getElementById("dob").addEventListener("change", function() {
    const val = this.value;
    const error = document.getElementById("dob-error");
    if (val !== "") {
        const birthDate = new Date(val);
        const today = new Date();
        let age = today.getFullYear() - birthDate.getFullYear();
        const monthDiff = today.getMonth() - birthDate.getMonth();
        if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDate.getDate())) {
            age--;
        }
        if (age < 21 || age > 65) {
            error.textContent = "Age must be between 21 and 65";
        } else {
            error.textContent = "";
        }
    }
});

// Prevent form resubmission on page refresh
if (window.history.replaceState) {
    window.history.replaceState(null, null, window.location.href);
}
</script>

</body>
</html>