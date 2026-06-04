<!-- topbar.php -->
<div class="topbar">
    <div>
        <strong><?php echo $_SESSION['full_name']; ?></strong> 
        (Role: <?php echo ucfirst($_SESSION['role']); ?>)
    </div>
    <div>
        <img src="admin_logo.png" alt="Admin Logo">
    </div>
</div>
