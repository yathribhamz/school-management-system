<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}


header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

include('connection.php');

$page_title = 'Edit Headmaster';


if (!isset($_GET['id'])) {
    die("Headmaster ID not provided.");
}
$id = intval($_GET['id']);

// Fetch headmaster details
$stmt = $conn->prepare("SELECT * FROM users WHERE id=? AND role='headmaster'");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$headmaster = $result->fetch_assoc();

if (!$headmaster) {
    die("Headmaster not found.");
}


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $employee_no = trim($_POST['employee_no']);
    $dob = $_POST['dob'];
    $nida = trim($_POST['nida']);
    
    $error = "";
    $success = "";

    if (empty($name) || empty($email) || empty($employee_no) || empty($dob) || empty($nida)) {
        $error = "All fields are required.";
    }
    
    // Validation 2: Email format check
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email address.";
    }
    
    // Validation 3: Employee Number format check
    else {
        $empRegex = '/^(H|HM)-[0-9]+(-[0-9]+)?$/';
        if (!preg_match($empRegex, $employee_no)) {
            $error = "Invalid Employee Number format. Use: H-12345 or HM-2024-001 or HM-12345";
        }
    }
    
    // Validation 4: NIDA format check (20 digits only)
    if (empty($error)) {
        if (!preg_match('/^[0-9]+$/', $nida)) {
            $error = "NIDA number must contain only digits (0-9).";
        } elseif (strlen($nida) !== 20) {
            $error = "NIDA number must be exactly 20 digits.";
        }
    }
    
    // Validation 5: Check duplicate email (excluding current headmaster)
    if (empty($error)) {
        $check_email_sql = "SELECT id FROM users WHERE email = ? AND id != ?";
        $check_email_stmt = $conn->prepare($check_email_sql);
        $check_email_stmt->bind_param("si", $email, $id);
        $check_email_stmt->execute();
        $check_email_result = $check_email_stmt->get_result();
        
        if ($check_email_result->num_rows > 0) {
            $error = "Email address already registered to another user. Please use a different email.";
        }
        $check_email_stmt->close();
    }
    
    // Validation 6: Check duplicate employee number (excluding current headmaster)
    if (empty($error)) {
        $check_emp_sql = "SELECT id FROM users WHERE employee_no = ? AND id != ?";
        $check_emp_stmt = $conn->prepare($check_emp_sql);
        $check_emp_stmt->bind_param("si", $employee_no, $id);
        $check_emp_stmt->execute();
        $check_emp_result = $check_emp_stmt->get_result();
        
        if ($check_emp_result->num_rows > 0) {
            $error = "Employee Number already exists for another user. Please use a different Employee Number.";
        }
        $check_emp_stmt->close();
    }
    
    // Validation 7: Check duplicate NIDA (excluding current headmaster)
    if (empty($error)) {
        $check_nida_sql = "SELECT id FROM users WHERE nida = ? AND id != ?";
        $check_nida_stmt = $conn->prepare($check_nida_sql);
        $check_nida_stmt->bind_param("si", $nida, $id);
        $check_nida_stmt->execute();
        $check_nida_result = $check_nida_stmt->get_result();
        
        if ($check_nida_result->num_rows > 0) {
            $error = "NIDA number already registered to another user. Please use a different NIDA.";
        }
        $check_nida_stmt->close();
    }
    
    // If all validations pass, proceed with update
    if (empty($error)) {
        $sql = "UPDATE users SET full_name=?, email=?, employee_no=?, date_of_birth=?, nida=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssi", $name, $email, $employee_no, $dob, $nida, $id);

        if ($stmt->execute()) {
            $success = "Headmaster updated successfully!";
            // Refresh data after update
            $stmt = $conn->prepare("SELECT * FROM users WHERE id=? AND role='headmaster'");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            $headmaster = $result->fetch_assoc();
        } else {
            $error = "Error updating headmaster: " . $conn->error;
        }
        $stmt->close();
    }
}

include('header.php');
include('sidebar.php');
include('topbar.php');
?>

<div class="content">
    <div class="card">
        <h3>Edit Headmaster Information</h3>
        
        <?php if (!empty($success)) echo "<div class='success'>$success</div>"; ?>
        <?php if (!empty($error)) echo "<div class='error'>$error</div>"; ?>
        
        <form method="POST" action="" onsubmit="return validateForm()">
            <input type="text" id="full_name" name="full_name" value="<?php echo htmlspecialchars($headmaster['full_name']); ?>" placeholder="Full Name" required>
            
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($headmaster['email']); ?>" placeholder="Email Address" required>
            <span id="email-error" class="field-error"></span>
            
            <input type="text" id="employee_no" name="employee_no" value="<?php echo htmlspecialchars($headmaster['employee_no']); ?>" placeholder="Employee Number (Format: H-12345 or HM-2024-001)" required>
            <span id="emp-error" class="field-error"></span>
            
            <input type="date" id="dob" name="dob" value="<?php echo $headmaster['date_of_birth']; ?>" required>
            
            <input type="text" id="nida" name="nida" value="<?php echo htmlspecialchars($headmaster['nida']); ?>" placeholder="NIDA Number (20 digits)" required>
            <span id="nida-error" class="field-error"></span>
            
            <button type="submit">Update Headmaster</button>
        </form>
        
    </div>
</div>

<style>

.field-error {
    display: block;
    color: #d14545;
    font-size: 0.7rem;
    margin-top: -5px;
    margin-bottom: 8px;
    padding-left: 12px;
}


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


.back-link {
    display: inline-block;
    margin-top: 20px;
    color: #004080;
    text-decoration: none;
    font-weight: 500;
    font-size: 0.85rem;
    transition: all 0.2s;
}

.back-link:hover {
    text-decoration: underline;
    transform: translateX(-2px);
    color: #003366;
}
</style>

<script>
function validateForm() {
    let isValid = true;
    
    
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