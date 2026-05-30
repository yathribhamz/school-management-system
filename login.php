<?php
session_start();
include ('Admin/connection.php'); // $conn = new mysqli(...);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $identifier = $_POST['identifier']; // inaweza kuwa email, reg_no, au employee_no
    $password = $_POST['password'];

    // Tafuta user kulingana na role na credentials husika
    $sql = "SELECT * FROM users WHERE 
            (email = ? AND role IN ('admin','parent')) 
            OR (reg_no = ? AND role = 'student') 
            OR (employee_no = ? AND role IN ('teacher','headmaster'))";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $identifier, $identifier, $identifier);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        // Kagua password (SHA256 hash)
        if (hash('sha256', $password) === $row['password']) {
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['role'] = $row['role'];

            // Redirect kulingana na role
            switch ($row['role']) {
                case 'admin':
                    header("Location: Admin/admin_dashboard.php");
                    break;
                case 'headmaster':
                    header("Location: headmaster_dashboard.php");
                    break;
                case 'teacher':
                    header("Location: teacher_dashboard.php");
                    break;
                case 'student':
                    header("Location: student_dashboard.php");
                    break;
            }
            exit();
        } else {
            $error = "Invalid password.";
        }
    } else {
        $error = "User not found.";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>School System Login</title>
    <link rel="stylesheet" type="text/css" href="include/css/style.css">
</head>
<body>
    <div class="login-box">
    <h2>Login</h2>
    <?php if (!empty($error)) echo "<p class='error'>$error</p>"; ?>
    <form method="POST" action="">
        <input type="text" name="identifier" placeholder="Email / Reg No / Employee No" required>
        <input type="password" name="password" placeholder="Enter Password" required>
        <button type="submit">Login</button>
    </form>
    <div class="demo-note">
        <strong>System Access</strong> | Authorized personnel only
    </div>
</div>
    </div>
</body>
</html>
