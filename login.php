<?php
session_start();
include ('Admin/connection.php'); // $conn = new mysqli(...);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $identifier = $_POST['identifier']; // inaweza kuwa email, reg_no, au employee_no
    $password = $_POST['password'];

    // Tafuta user kulingana na role na credentials husika
    $sql = "SELECT * FROM users WHERE 
            (email = ? AND role = 'admin') 
            OR (reg_no = ? AND role = 'student') 
            OR (employee_no = ? AND role IN ('teacher','headmaster'))";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $identifier, $identifier, $identifier);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        // Kagua password (SHA256 hash)
        if (hash('sha256', $password) === $row['password']) {
            // Hifadhi session variables
            $_SESSION['user_id']   = $row['id'];
            $_SESSION['role']      = $row['role'];
            $_SESSION['full_name'] = $row['full_name']; // <-- hii ndio jina halisi

            // Redirect kulingana na role
            switch ($row['role']) {
                case 'admin':
                    header("Location: Admin/admin_dashboard.php");
                    break;
                case 'headmaster':
                    header("Location: Headmaster/headmaster_dashboard.php");
                    break;
                case 'teacher':
                     $sql_teacher = "SELECT teacher_id FROM teachers WHERE user_id = ?";
                        $stmt_teacher = $conn->prepare($sql_teacher);
                        $stmt_teacher->bind_param("i", $row['id']);
                        $stmt_teacher->execute();
                        $res_teacher = $stmt_teacher->get_result();
                        if ($res_teacher->num_rows > 0) {
                         $teacher = $res_teacher->fetch_assoc();
                         $_SESSION['teacher_id'] = $teacher['teacher_id']; // 🔑 sasa teacher_id ipo
                            }
                    header("Location: Teachers/Teacher_dashbord.php");
                    break;
               case 'student':
        // Hapa student aende kwenye dashboard yake mwenyewe
                        $sql_student = "SELECT student_id FROM students WHERE user_id = ?";
                        $stmt_student = $conn->prepare($sql_student);
                        $stmt_student->bind_param("i", $row['id']);
                        $stmt_student->execute();
                        $res_student = $stmt_student->get_result();
                        if ($res_student->num_rows > 0) {
                            $student = $res_student->fetch_assoc();
                            $_SESSION['student_id'] = $student['student_id']; // 🔑 sasa student_id ipo
                        }
                        header("Location: Students/student_dashboard.php");
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
</body>
</html>
