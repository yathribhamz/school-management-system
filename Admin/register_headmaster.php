<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

// Zuia caching
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

include('connection.php');

$page_title = 'Register Headmaster';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $employee_no = trim($_POST['employee_no']);
    $nida = trim($_POST['nida']);
    $dob = $_POST['dob'];
    $password = hash('sha256', $_POST['password']);
    
    // Kwanza check kama email tayari ipo kwenye database
    $check_email_sql = "SELECT id FROM users WHERE email = ?";
    $check_email_stmt = $conn->prepare($check_email_sql);
    $check_email_stmt->bind_param("s", $email);
    $check_email_stmt->execute();
    $check_email_result = $check_email_stmt->get_result();
    
    // Check kama employee number tayari ipo
    $check_emp_sql = "SELECT id FROM users WHERE employee_no = ?";
    $check_emp_stmt = $conn->prepare($check_emp_sql);
    $check_emp_stmt->bind_param("s", $employee_no);
    $check_emp_stmt->execute();
    $check_emp_result = $check_emp_stmt->get_result();
    
    if ($check_email_result->num_rows > 0) {
        // Email tayari ipo
        $duplicate_error = "Email address already registered. Please use a different email.";
    } elseif ($check_emp_result->num_rows > 0) {
        // Employee number tayari ipo
        $duplicate_emp_error = "Employee Number already exists. Please use a different Employee Number.";
    } else {
        // Email haipo, endelea na kuingiza data
        $sql = "INSERT INTO users (full_name, sex, email, date_of_birth, role, employee_no, nida, password) 
                VALUES (?, 'Male', ?, ?, 'headmaster', ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssss", $name, $email, $dob, $employee_no, $nida, $password);

        if ($stmt->execute()) {
            $success = "Headmaster registered successfully!";
        } else {
            $error = "Error: " . $conn->error;
        }
        $stmt->close();
    }
    $check_email_stmt->close();
    $check_emp_stmt->close();
}

include('header.php');
include('sidebar.php');
include('topbar.php');
?>

<div class="content">
    <div class="card">
        <h3>Register Headmaster</h3>
        
        <?php 
        if (!empty($success)) echo "<div class='success'>$success</div>";
        if (!empty($error)) echo "<div class='error'>$error</div>";
        if (!empty($duplicate_error)) echo "<div class='error'>$duplicate_error</div>";
        if (!empty($duplicate_emp_error)) echo "<div class='error'>$duplicate_emp_error</div>";
        ?>
        
        <form method="POST" action="" onsubmit="return validateForm()">
            <input type="text" name="full_name" placeholder="Full Name" required>
            
            <input type="email" id="email" name="email" placeholder="Email" required>
            <span id="email-error" class="field-error"></span>
            
            <input type="text" id="employee_no" name="employee_no" placeholder="Employee No (Format: H-12345 or HM-2024-001)" required>
            <span id="emp-error" class="field-error"></span>
            
            <input type="text" id="nida" name="nida" placeholder="NIDA (20 digits)" required>
            <span id="nida-error" class="field-error"></span>
            
            <input type="date" name="dob" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Register Headmaster</button>
        </form>
        
    </div>
</div>

<style>
/* Field error styling */
.field-error {
    display: block;
    color: #d14545;
    font-size: 0.7rem;
    margin-top: -5px;
    margin-bottom: 8px;
    padding-left: 12px;
}

/* Info note styling */
.info-note {
    margin-top: 20px;
    padding: 15px 20px;
    background: #f0f7fe;
    border-radius: 12px;
    border-left: 4px solid #004080;
    font-size: 0.8rem;
}

.info-note strong {
    color: #004080;
    display: block;
    margin-bottom: 8px;
}

.info-note ul {
    margin-left: 20px;
    color: #2c5a7a;
}

.info-note li {
    margin: 5px 0;
}
</style>

<script>
function validateForm() {
    let isValid = true;
    
    // Validate Email format
    const emailInput = document.getElementById("email").value.trim();
    const emailError = document.getElementById("email-error");
    const emailRegex = /^[^\s@]+@([^\s@]+\.)+[^\s@]+$/;

    if (!emailRegex.test(emailInput)) {
        emailError.textContent = "Please enter a valid email address.";
        isValid = false;
    } else {
        emailError.textContent = "";
    }
    
    // Validate Employee Number
    const empInput = document.getElementById("employee_no").value.trim();
    const empError = document.getElementById("emp-error");
    // Format: H-12345 au HM-2024-001 au HM-12345
    const empRegex = /^(H|HM)-[0-9]+(-[0-9]+)?$/;
    
    if (empInput === "") {
        empError.textContent = "Employee Number is required.";
        isValid = false;
    } else if (!empRegex.test(empInput)) {
        empError.textContent = "Invalid format. Use: H-12345 or HM-2024-001 or HM-12345";
        isValid = false;
    } else if (empInput.length < 4) {
        empError.textContent = "Employee Number is too short.";
        isValid = false;
    } else {
        empError.textContent = "";
    }
    
    // Validate NIDA
    const nidaInput = document.getElementById("nida").value.trim();
    const nidaError = document.getElementById("nida-error");
    const numberRegex = /^[0-9]+$/;

    if (nidaInput === "") {
        nidaError.textContent = "NIDA number is required.";
        isValid = false;
    } else if (!numberRegex.test(nidaInput)) {
        nidaError.textContent = "NIDA number must contain only digits (0-9).";
        isValid = false;
    } else if (nidaInput.length !== 20) {
        nidaError.textContent = "NIDA number must be exactly 20 digits.";
        isValid = false;
    } else {
        nidaError.textContent = "";
    }
    
    return isValid;
}

// Real-time validation for better UX
document.getElementById("employee_no").addEventListener("input", function() {
    const empInput = this.value.trim();
    const empError = document.getElementById("emp-error");
    const empRegex = /^(H|HM)-[0-9]+(-[0-9]+)?$/;
    
    if (empInput !== "" && !empRegex.test(empInput)) {
        empError.textContent = "Invalid format. Use: H-12345 or HM-2024-001";
    } else {
        empError.textContent = "";
    }
});

document.getElementById("nida").addEventListener("input", function() {
    const nidaInput = this.value.trim();
    const nidaError = document.getElementById("nida-error");
    const numberRegex = /^[0-9]+$/;
    
    if (nidaInput !== "" && !numberRegex.test(nidaInput)) {
        nidaError.textContent = "Use only digits (0-9)";
    } else if (nidaInput !== "" && nidaInput.length !== 20) {
        nidaError.textContent = "Need exactly 20 digits (currently " + nidaInput.length + ")";
    } else {
        nidaError.textContent = "";
    }
});

document.getElementById("email").addEventListener("input", function() {
    const emailInput = this.value.trim();
    const emailError = document.getElementById("email-error");
    const emailRegex = /^[^\s@]+@([^\s@]+\.)+[^\s@]+$/;
    
    if (emailInput !== "" && !emailRegex.test(emailInput)) {
        emailError.textContent = "Invalid email format";
    } else {
        emailError.textContent = "";
    }
});
</script>

</body>
</html>