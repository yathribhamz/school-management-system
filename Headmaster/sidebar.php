<!-- sidebar.php -->
<div class="sidebar">
    <h2>Admin Panel</h2>
    <a href="headmaster_dashboard.php">Home Dashboard</a>
    <!-- Registration Dropdown -->
    <button class="dropdown-btn">Registration</button>
    <div class="dropdown-container">
        <a href="register_classes.php">Register Classes</a>
        <a href="register_subjects.php">Register Subjects</a>
        <a href="register_students.php">Register Students</a>
        <a href="register_teachers.php">Register Teachers</a>
    </div>

    <!-- View Dropdown -->
    <button class="dropdown-btn">View</button>
    <div class="dropdown-container">
        <a href="view_classes.php">View Classes</a>
        <a href="view_students.php">View Students</a>
        <a href="view_teachers.php">View Teachers</a>
        <a href="view_results.php">View Results</a>
    </div>

    <!-- Other Links -->
    <a href="../Admin/logout.php">Logout</a>
</div>

<script>
// Dropdown toggle functionality
document.addEventListener('DOMContentLoaded', function() {
    var dropdown = document.getElementsByClassName("dropdown-btn");
    
    for (var i = 0; i < dropdown.length; i++) {
        dropdown[i].addEventListener("click", function() {
            this.classList.toggle("active");
            var container = this.nextElementSibling;
            
            if (container.style.display === "block") {
                container.style.display = "none";
            } else {
                container.style.display = "block";
            }
        });
    }
    
    // Keep dropdown open based on current page
    var currentPage = window.location.pathname.split('/').pop();
    var allDropdownLinks = document.querySelectorAll('.dropdown-container a');
    
    allDropdownLinks.forEach(function(link) {
        if (link.getAttribute('href') === currentPage) {
            var parentContainer = link.closest('.dropdown-container');
            if (parentContainer) {
                parentContainer.style.display = "block";
                var parentBtn = parentContainer.previousElementSibling;
                if (parentBtn && parentBtn.classList.contains('dropdown-btn')) {
                    parentBtn.classList.add('active');
                }
            }
        }
    });
});
</script>