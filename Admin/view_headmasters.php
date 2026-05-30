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

$page_title = 'View Headmasters';

// Fetch all headmasters
$result = $conn->query("SELECT id, full_name, email, employee_no, created_at FROM users WHERE role='headmaster'");

include('header.php');
include('sidebar.php');
include('topbar.php');
?>

<div class="content">
    <div class="card">
        <h3>Registered Headmasters</h3>
        
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Employee No</th>
                    <th>Registered At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = $result->fetch_assoc()) { ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['full_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['email']); ?></td>
                        <td><?php echo htmlspecialchars($row['employee_no']); ?></td>
                        <td><?php echo htmlspecialchars($row['created_at']); ?></td>
                        <td>
                            <a class="btn edit" href="edit_headmaster.php?id=<?php echo $row['id']; ?>">Edit</a>
                            <a class="btn delete" href="delete_headmaster.php?id=<?php echo $row['id']; ?>" onclick="return confirm('Are you sure you want to delete this headmaster?');">Delete</a>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>