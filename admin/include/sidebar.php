<!-- Sidebar -->
<div class="sidebar">
    <ul class="nav-list">
        <li>
            <a href="dashboard.php">
                <span class="icon">📖</span>
                <span class="title"><strong>BOOKS HERE</strong></span>
            </a>
        </li>
        <li>
            <a href="javascript:void(0)" class="sidebar-link" data-page="dashboard.php">
                <span class="icon">🏡</span>
                <span class="title">Dashboard</span>
            </a>
        </li>
        <li>
            <a href="javascript:void(0)" class="sidebar-link" data-page="request-books.php">
                <span class="icon">📝</span>
                <span class="title">Book Requests</span>
            </a>
        </li>

        <!-- Managements Submenu -->
        <li class="has-submenu">
            <a href="#" class="submenu-toggle">
                <span class="icon">🛠️</span>
                <span class="title">Managements ▾</span>
            </a>
            <ul class="submenu" style="list-style: none; padding-left: 25px;">
                <li><a href="javascript:void(0)"  class="sidebar-link" data-page="agent-manage.php">🕵️ | Agent Management</a></li>
                <li><a href="javascript:void(0)" class="sidebar-link" data-page="user-management.php">👥 | User Management</a></li>
                <li><a href="javascript:void(0)" class="sidebar-link" data-page="book-manage.php">📚 | Book Management</a></li>
            </ul>
        </li>

        <!-- Admin Settings Submenu -->
        <li class="has-submenu">
            <a href="#" class="submenu-toggle">
                <span class="icon">⚙️</span>
                <span class="title">Admin Settings ▾</span>
            </a>
            <ul class="submenu" style="list-style: none; padding-left: 25px;">
                <li><a href="javascript:void(0)" class="sidebar-link" data-page="profile.php">👤 | Update Profile</a></li>
                <li><a href="javascript:void(0)" class="sidebar-link" data-page="change-password.php">🔒 | Change Password</a></li>
                <li><a href="javascript:void(0)" class="sidebar-link" data-page="update-smpt.php">📝 | SMTP Settings</a></li>
            </ul>
        </li>

        <li>
            <a href="javascript:void(0)" class="sidebar-link" data-page="errors.php">
                <span class="icon">❌</span>
                <span class="title">Errors</span>
            </a>
        </li>
        <li>
            <a href="../base/logout.php">
                <span class="icon">🚪</span>
                <span class="title">Logout</span>
            </a>
        </li>
    </ul>
</div>

<!-- Sidebar Scripts -->
<script src="../includes/sidbar_script.js"></script>
