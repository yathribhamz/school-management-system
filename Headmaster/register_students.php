<?php
session_start();
include('../Admin/connection.php');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'headmaster') {
    header("Location: ../login.php");
    exit();
}

$page_title = 'Register Student';
$message = "";
$message_type = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $full_name = trim($_POST['full_name']);
    $sex       = $_POST['sex'];
    $email     = trim($_POST['email']);
    $dob       = $_POST['dob'];
    $class_id  = $_POST['class_id'];
    $reg_no    = trim($_POST['reg_no']);
    $password  = $_POST['password'];
    
    // Validation
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
        // Check if email already exists
        $check_email = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $check_email->bind_param("s", $email);
        $check_email->execute();
        $check_email->store_result();
        if ($check_email->num_rows > 0) {
            $errors[] = "Email address already registered. Please use a different email.";
        }
        $check_email->close();
    }
    
    // 4. Validate date of birth
    if (empty($dob)) {
        $errors[] = "Date of birth is required.";
    } else {
        $age = date_diff(date_create($dob), date_create('today'))->y;
        if ($age < 5 || $age > 20) {
            $errors[] = "Student age must be between 5 and 20 years.";
        }
    }
    
    // 5. Validate class selection
    if (empty($class_id)) {
        $errors[] = "Please select a class.";
    }
    
    // 6. Validate registration number
    if (empty($reg_no)) {
        $errors[] = "Registration number is required.";
    } elseif (strlen($reg_no) < 3) {
        $errors[] = "Registration number must be at least 3 characters.";
    } else {
        // Check if registration number already exists
        $check_reg = $conn->prepare("SELECT id FROM users WHERE reg_no = ?");
        $check_reg->bind_param("s", $reg_no);
        $check_reg->execute();
        $check_reg->store_result();
        if ($check_reg->num_rows > 0) {
            $errors[] = "Registration number already exists. Please use a different number.";
        }
        $check_reg->close();
    }
    
    // 7. Validate password
    if (empty($password)) {
        $errors[] = "Password is required.";
    } elseif (strlen($password) < 4) {
        $errors[] = "Password must be at least 4 characters.";
    }
    
    // 8. Check class capacity if no errors so far
    if (empty($errors) && !empty($class_id)) {
        $check = $conn->prepare("SELECT COUNT(*) AS total FROM students WHERE class_id = ?");
        $check->bind_param("i", $class_id);
        $check->execute();
        $result = $check->get_result()->fetch_assoc();
        $current_students = $result['total'];
        
        $max = $conn->prepare("SELECT max_students FROM classes WHERE class_id = ?");
        $max->bind_param("i", $class_id);
        $max->execute();
        $max_result = $max->get_result();
        if ($max_row = $max_result->fetch_assoc()) {
            $max_students = $max_row['max_students'];
            
            if ($current_students >= $max_students) {
                $errors[] = "Class is full (Maximum $max_students students). Cannot register more students.";
            }
        }
    }
    
    // If no errors, proceed with registration
    if (empty($errors)) {
        $hashed_password = hash('sha256', $password);
        
        // Step 1: Insert into users table
        $stmt1 = $conn->prepare("INSERT INTO users (full_name, sex, email, date_of_birth, role, reg_no, password) VALUES (?, ?, ?, ?, 'student', ?, ?)");
        $stmt1->bind_param("ssssss", $full_name, $sex, $email, $dob, $reg_no, $hashed_password);
        
        if ($stmt1->execute()) {
            $user_id = $stmt1->insert_id;
            
            // Step 2: Insert into students table
            $stmt2 = $conn->prepare("INSERT INTO students (user_id, class_id, headmaster_id) VALUES (?, ?, ?)");
            $stmt2->bind_param("iii", $user_id, $class_id, $_SESSION['user_id']);
            
            if ($stmt2->execute()) {
                $message = "Student registered successfully.";
                $message_type = "success";
            } else {
                $message = "Error inserting into students: " . $conn->error;
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

include('header.php');
include('sidebar.php');
include('topbar.php');
?>

<div class="content">
    <div class="card">
        <h3>Register New Student</h3>
        
        <?php if (!empty($message)): ?>
            <div class="<?php echo $message_type == 'success' ? 'success' : 'error'; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="" onsubmit="return validateForm()">
            <div class="input-group">
                <label for="full_name">Full Name</label>
                <input type="text" id="full_name" name="full_name" placeholder="Enter student's full name" required>
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
                <input type="email" id="email" name="email" placeholder="student@example.com" required>
                <span id="email-error" class="field-error"></span>
                <div class="info-hint">Valid email address required for communication</div>
            </div>
            
            <div class="input-group">
                <label for="dob">Date of Birth</label>
                <input type="date" id="dob" name="dob" required>
                <span id="dob-error" class="field-error"></span>
                <div class="info-hint">Student must be between 5 and 20 years old</div>
            </div>
            
            <div class="input-group">
                <label for="class_id">Select Class</label>
                <select id="class_id" name="class_id" required>
                    <option value="">-- Select Class --</option>
                    <?php while($row = $classes->fetch_assoc()): ?>
                        <option value="<?php echo $row['class_id']; ?>" data-max="<?php echo $row['max_students']; ?>">
                            <?php echo htmlspecialchars($row['class_name']) . " (Level " . $row['level'] . " - Max: " . $row['max_students'] . " students)"; ?>
                        </option>
                    <?php endwhile; ?>
                </select>
                <span id="class-error" class="field-error"></span>
                <div class="info-hint" id="class-capacity-info"></div>
            </div>
            
            <div class="input-group">
                <label for="reg_no">Registration Number</label>
                <input type="text" id="reg_no" name="reg_no" placeholder="e.g., S001, 2024-001" required>
                <span id="reg-error" class="field-error"></span>
                <div class="info-hint">Unique registration number for identification</div>
            </div>
            
            <div class="input-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Create a secure password" required>
                <span id="password-error" class="field-error"></span>
                <div class="info-hint">Minimum 4 characters</div>
            </div>
            
            <button type="submit">Register Student</button>
        </form>
        
    </div>
</div>

<script>
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
        if (age < 5 || age > 20) {
            dobError.textContent = "Student must be between 5 and 20 years old.";
            isValid = false;
        } else {
            dobError.textContent = "";
        }
    }
    
    // Validate Class
    const classId = document.getElementById("class_id").value;
    const classError = document.getElementById("class-error");
    if (classId === "") {
        classError.textContent = "Please select a class.";
        isValid = false;
    } else {
        classError.textContent = "";
    }
    
    // Validate Registration Number
    const regNo = document.getElementById("reg_no").value.trim();
    const regError = document.getElementById("reg-error");
    if (regNo === "") {
        regError.textContent = "Registration number is required.";
        isValid = false;
    } else if (regNo.length < 3) {
        regError.textContent = "Registration number must be at least 3 characters.";
        isValid = false;
    } else {
        regError.textContent = "";
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

document.getElementById("reg_no").addEventListener("input", function() {
    const val = this.value.trim();
    const error = document.getElementById("reg-error");
    if (val !== "" && val.length < 3) {
        error.textContent = "Minimum 3 characters";
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
        if (age < 5 || age > 20) {
            error.textContent = "Age must be between 5 and 20";
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